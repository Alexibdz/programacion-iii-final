<?php

namespace App\Models;

use RuntimeException;

// Pedidos de cada visita
class OrdenModelo extends ModeloBase
{
    protected $table         = 'ordenes';
    protected $allowedFields = ['mesa_visita_id', 'estado', 'observaciones'];

    // Auditoría e historial
    protected $afterInsert = ['auditarAlta', 'historialAlta'];
    protected $afterUpdate = ['auditarModificacion', 'historialModificacion'];

    // Órdenes de una visita con sus productos
    public function listarDeVisita(int $visitaId): array
    {
        $ordenes = $this->where('mesa_visita_id', $visitaId)->orderBy('id')->findAll();

        return $this->agregarItems($ordenes, false);
    }

    // Cola de cocina de la más vieja a la más nueva, solo con platos
    public function colaDeCocina(): array
    {
        $ordenes = $this->select('ordenes.*, mesas.numero AS mesa_numero')
            ->join('mesa_visita', 'mesa_visita.id = ordenes.mesa_visita_id')
            ->join('mesas', 'mesas.id = mesa_visita.mesa_id')
            ->whereIn('ordenes.estado', ['pendiente', 'en_preparacion'])
            ->where('mesa_visita.estado', 'abierta')
            ->orderBy('ordenes.creado_en')
            ->orderBy('ordenes.id')
            ->findAll();

        return $this->agregarItems($ordenes, true);
    }

    // Productos de cada orden y su total
    private function agregarItems(array $ordenes, bool $soloCocina): array
    {
        if ($ordenes === []) {
            return [];
        }

        $consulta = $this->db->table('orden_detalle')->whereIn('orden_id', array_column($ordenes, 'id'));

        if ($soloCocina) {
            $consulta->where('requiere_cocina', 1);
        }

        $items = $consulta->orderBy('id')->get()->getResultArray();

        foreach ($ordenes as &$orden) {
            $orden['items'] = array_values(array_filter($items, static fn ($item) => $item['orden_id'] === $orden['id']));
            $orden['total'] = array_sum(array_column($orden['items'], 'subtotal'));
        }
        unset($orden);

        return $ordenes;
    }

    // Alta de una orden con la copia de cada producto
    public function crearConDetalle(int $visitaId, array $items, ?string $observaciones): int
    {
        $pasaPorCocina = in_array('1', array_column(array_column($items, 'producto'), 'requiere_cocina'), true);

        $this->db->transStart();

        $ordenId = (int) $this->insert([
            'mesa_visita_id' => $visitaId,
            'estado'         => $pasaPorCocina ? 'pendiente' : 'terminada',
            'observaciones'  => $observaciones,
        ]);

        $filas = array_map(static fn ($item) => [
            'orden_id'        => $ordenId,
            'producto_id'     => $item['producto']['id'],
            'producto_nombre' => $item['producto']['nombre'],
            'requiere_cocina' => $item['producto']['requiere_cocina'],
            'precio_unitario' => $item['producto']['precio'],
            'cantidad'        => $item['cantidad'],
            'subtotal'        => $item['producto']['precio'] * $item['cantidad'],
            'observaciones'   => $item['observaciones'],
            'creado_en'       => date('Y-m-d H:i:s'),
        ], $items);

        $this->db->table('orden_detalle')->insertBatch($filas);

        $this->db->transComplete();

        if (! $this->db->transStatus()) {
            throw new RuntimeException('No se pudo guardar la orden.');
        }

        return $ordenId;
    }

    protected function historialAlta(array $evento): array
    {
        if ($evento['result']) {
            $this->registrarHistorial((int) $evento['id'], 'ALTA');
        }

        return $evento;
    }

    protected function historialModificacion(array $evento): array
    {
        if ($evento['result']) {
            foreach ($evento['id'] ?? [] as $id) {
                $this->registrarHistorial((int) $id, 'MODIFICACION');
            }
        }

        return $evento;
    }

    // Copia de la orden en el historial
    private function registrarHistorial(int $id, string $accion): void
    {
        $orden = $this->withDeleted()->find($id);

        $this->db->table('historial_ordenes')->insert([
            'orden_id'       => $id,
            'mesa_visita_id' => $orden['mesa_visita_id'],
            'observaciones'  => $orden['observaciones'],
            'accion'         => $accion,
            'usuario_id'     => session('usuario_id'),
        ]);
    }
}
