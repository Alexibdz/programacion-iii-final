<?php

namespace App\Controllers;

use App\Models\MesaModelo;
use App\Models\OrdenModelo;
use App\Models\ProductoModelo;
use App\Models\VisitaModelo;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\Exceptions\PageNotFoundException;

class Visitas extends BaseController
{
    use ResponseTrait;

    private VisitaModelo $visitas;
    private OrdenModelo $ordenes;

    public function __construct()
    {
        $this->visitas = new VisitaModelo();
        $this->ordenes = new OrdenModelo();
    }

    // Página de una mesa: si está ocupada va a su visita, si no permite abrirla
    public function mesa(int $mesaId)
    {
        $mesas = new MesaModelo();
        $mesa  = $mesas->find($mesaId);

        if ($mesa === null) {
            throw PageNotFoundException::forPageNotFound('La mesa no existe.');
        }

        $abierta = $mesas->visitaAbierta($mesaId);

        if ($abierta !== null) {
            return redirect()->to('visitas/' . $abierta['id']);
        }

        return view('visitas/mesa_libre', ['mesa' => $mesa]);
    }

    public function ver(int $id): string
    {
        if ($this->buscarVisita($id) === null) {
            throw PageNotFoundException::forPageNotFound('La visita no existe.');
        }

        // El botón de volver lleva a la pantalla de origen
        $desdeHistorial = $this->request->getGet('desde') === 'historial';

        return view('visitas/ver', [
            'visitaId'    => $id,
            'volverRuta'  => $desdeHistorial ? 'historial' : 'mesas',
            'volverTexto' => $desdeHistorial ? 'Volver al historial' : 'Mapa de mesas',
        ]);
    }

    // Página para armar una orden nueva
    public function nuevaOrden(int $id)
    {
        $visita = $this->buscarVisita($id);

        if ($visita === null) {
            throw PageNotFoundException::forPageNotFound('La visita no existe.');
        }

        if ($visita['estado'] !== 'abierta') {
            return redirect()->to('visitas/' . $id);
        }

        return view('visitas/nueva_orden', ['visita' => $visita]);
    }

    // Recibo de 80 mm de una visita cerrada
    public function recibo(int $id)
    {
        $visita = $this->buscarVisita($id);

        if ($visita === null) {
            throw PageNotFoundException::forPageNotFound('La visita no existe.');
        }

        if ($visita['estado'] !== 'cerrada') {
            return redirect()->to('visitas/' . $id);
        }

        // Productos de las órdenes no canceladas, sumando los repetidos
        $lineas = [];

        foreach ($this->ordenes->listarDeVisita($id) as $orden) {
            if ($orden['estado'] === 'cancelada') {
                continue;
            }

            foreach ($orden['items'] as $item) {
                $clave = $item['producto_nombre'] . '|' . $item['precio_unitario'];

                $lineas[$clave] ??= ['nombre' => $item['producto_nombre'], 'precio' => (float) $item['precio_unitario'], 'cantidad' => 0, 'subtotal' => 0];
                $lineas[$clave]['cantidad'] += (int) $item['cantidad'];
                $lineas[$clave]['subtotal'] += (float) $item['subtotal'];
            }
        }

        return view('visitas/recibo', [
            'visita'      => $visita,
            'lineas'      => array_values($lineas),
            'reimpresion' => $this->request->getGet('reimpresion') === '1',
        ]);
    }

    // Visita con sus órdenes y total actual
    public function detalle(int $id)
    {
        $visita = $this->buscarVisita($id);

        if ($visita === null) {
            return $this->failNotFound('La visita no existe.');
        }

        $visita['ordenes']      = $this->ordenes->listarDeVisita($id);
        $visita['total_actual'] = $this->sumarTotal($visita['ordenes'], ['cancelada']);

        return $this->respond($visita);
    }

    // Alta de una orden en la visita
    public function crearOrden(int $id)
    {
        $visita = $this->buscarVisita($id);

        if ($visita === null) {
            return $this->failNotFound('La visita no existe.');
        }

        if ($visita['estado'] !== 'abierta') {
            return $this->fail('La visita ya está cerrada.', 409);
        }

        $datos         = $this->request->getJSON(true) ?? [];
        $pedidos       = $datos['items'] ?? [];
        $observaciones = trim($datos['observaciones'] ?? '');

        if (! is_array($pedidos) || $pedidos === []) {
            return $this->failValidationErrors(['items' => 'Agregá al menos un producto.']);
        }

        if (mb_strlen($observaciones) > 255) {
            return $this->failValidationErrors(['observaciones' => 'Las observaciones no pueden superar los 255 caracteres.']);
        }

        foreach ($pedidos as $pedido) {
            $cantidad = $pedido['cantidad'] ?? 0;

            if (! is_int($pedido['producto_id'] ?? null) || ! is_int($cantidad) || $cantidad < 1 || $cantidad > 99) {
                return $this->failValidationErrors(['items' => 'Cada producto necesita una cantidad entre 1 y 99.']);
            }
        }

        // Productos activos con su paso por cocina
        $productos = (new ProductoModelo())
            ->select('productos.id, productos.nombre, productos.precio, categorias.requiere_cocina')
            ->join('categorias', 'categorias.id = productos.categoria_id')
            ->whereIn('productos.id', array_column($pedidos, 'producto_id'))
            ->findAll();

        $productos = array_column($productos, null, 'id');
        $items     = [];

        foreach ($pedidos as $pedido) {
            if (! isset($productos[$pedido['producto_id']])) {
                return $this->failValidationErrors(['items' => 'Algún producto ya no está disponible en el menú.']);
            }

            $items[] = [
                'producto'      => $productos[$pedido['producto_id']],
                'cantidad'      => $pedido['cantidad'],
                'observaciones' => trim($pedido['observaciones'] ?? '') ?: null,
            ];
        }

        $ordenId = $this->ordenes->crearConDetalle($id, $items, $observaciones ?: null);

        return $this->respondCreated(['id' => $ordenId]);
    }

    // Generación de la cuenta y cierre de la visita
    public function cerrar(int $id)
    {
        $visita = $this->buscarVisita($id);

        if ($visita === null) {
            return $this->failNotFound('La visita no existe.');
        }

        if ($visita['estado'] !== 'abierta') {
            return $this->fail('La visita ya está cerrada.', 409);
        }

        $ordenes     = $this->ordenes->listarDeVisita($id);
        $sinTerminar = array_filter($ordenes, static fn ($orden) => in_array($orden['estado'], ['pendiente', 'en_preparacion'], true));

        if ($sinTerminar !== []) {
            return $this->fail('Hay órdenes sin terminar. Esperá a que cocina las termine o cancelá las pendientes antes de cerrar la visita.', 409);
        }

        // Sin órdenes válidas la visita se cancela en vez de cobrarse
        $validas = array_filter($ordenes, static fn ($orden) => $orden['estado'] !== 'cancelada');
        $total   = $this->sumarTotal($ordenes, ['cancelada']);
        $estado  = $validas === [] ? 'cancelada' : 'cerrada';
        $cierre = date('Y-m-d H:i:s');

        $this->visitas->update($id, ['estado' => $estado, 'total' => $total, 'cerrada_en' => $cierre]);

        db_connect()->table('historial_mesa_visita')->insert([
            'mesa_visita_id' => $id,
            'mesa_id'        => $visita['mesa_id'],
            'total'          => $total,
            'cerrada_en'     => $cierre,
            'accion'         => 'MODIFICACION',
            'usuario_id'     => session('usuario_id'),
        ]);

        return $this->respond(['estado' => $estado, 'total' => $total]);
    }

    // Visita con el número de mesa
    private function buscarVisita(int $id): ?array
    {
        return $this->visitas
            ->select('mesa_visita.*, mesas.numero AS mesa_numero, usuarios.nombre AS usuario')
            ->join('mesas', 'mesas.id = mesa_visita.mesa_id')
            ->join('usuarios', 'usuarios.id = mesa_visita.usuario_id', 'left')
            ->find($id);
    }

    // Suma de órdenes excluyendo estados
    private function sumarTotal(array $ordenes, array $estadosExcluidos): float
    {
        $validas = array_filter($ordenes, static fn ($orden) => ! in_array($orden['estado'], $estadosExcluidos, true));

        return (float) array_sum(array_column($validas, 'total'));
    }
}
