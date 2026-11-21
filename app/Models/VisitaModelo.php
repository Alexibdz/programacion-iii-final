<?php

// BLOQUE 100% DESARROLLADO CON IA (/docs/diseño-de-mesas)

namespace App\Models;

// Visitas de clientes a las mesas
class VisitaModelo extends ModeloBase
{
    protected $table         = 'mesa_visita';
    protected $allowedFields = ['mesa_id', 'usuario_id', 'estado', 'total', 'cerrada_en'];

    // Historial de la visita
    protected $afterInsert = ['auditarAlta', 'historialAlta'];

    // Apertura de una mesa
    public function abrir(int $mesaId, int $usuarioId): int
    {
        return (int) $this->insert([
            'mesa_id'    => $mesaId,
            'usuario_id' => $usuarioId,
            'estado'     => 'abierta',
            'total'      => 0,
        ]);
    }

    protected function historialAlta(array $evento): array
    {
        if ($evento['result']) {
            $this->db->table('historial_mesa_visita')->insert([
                'mesa_visita_id' => $evento['id'],
                'mesa_id'        => $evento['data']['mesa_id'],
                'total'          => $evento['data']['total'] ?? 0,
                'accion'         => 'ALTA',
                'usuario_id'     => session('usuario_id'),
            ]);
        }

        return $evento;
    }
}

// FIN BLOQUE 100% DESARROLLADO CON IA (/docs/diseño-de-mesas)
