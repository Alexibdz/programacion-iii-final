<?php

namespace App\Controllers;

use App\Models\OrdenModelo;
use App\Models\VisitaModelo;
use CodeIgniter\API\ResponseTrait;

class Ordenes extends BaseController
{
    use ResponseTrait;

    // Cambios de estado permitidos
    private const TRANSICIONES = [
        'pendiente'      => ['en_preparacion', 'cancelada'],
        'en_preparacion' => ['terminada', 'pendiente'],
    ];

    // Roles que pueden pasar a cada estado
    private const ROLES = [
        'pendiente'      => ['cocina', 'principal', 'administrador'],
        'en_preparacion' => ['cocina', 'principal', 'administrador'],
        'terminada'      => ['cocina', 'principal', 'administrador'],
        'cancelada'      => ['principal', 'administrador', 'mozo'],
    ];

    // Nombres de los estados para los mensajes
    private const NOMBRES = [
        'pendiente'      => 'pendiente',
        'en_preparacion' => 'en preparación',
        'terminada'      => 'terminada',
        'cancelada'      => 'cancelada',
    ];

    // Cambio de estado de una orden
    public function cambiarEstado(int $id)
    {
        $ordenes = new OrdenModelo();
        $orden   = $ordenes->find($id);

        if ($orden === null) {
            return $this->failNotFound('La orden no existe.');
        }

        $nuevo = ($this->request->getJSON(true) ?? [])['estado'] ?? '';

        if (! in_array($nuevo, self::TRANSICIONES[$orden['estado']] ?? [], true)) {
            return $this->fail('Una orden ' . self::NOMBRES[$orden['estado']] . ' no puede pasar a ' . (self::NOMBRES[$nuevo] ?? 'ese estado') . '.', 409);
        }

        if (! in_array(session('rol'), self::ROLES[$nuevo], true)) {
            return $this->failForbidden('No tenés permiso para este cambio.');
        }

        $visita = (new VisitaModelo())->find($orden['mesa_visita_id']);

        if ($visita === null || $visita['estado'] !== 'abierta') {
            return $this->fail('La visita ya está cerrada.', 409);
        }

        $ordenes->update($id, ['estado' => $nuevo]);

        return $this->respond($ordenes->find($id));
    }
}
