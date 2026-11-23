<?php

namespace App\Controllers;

use App\Models\VisitaModelo;
use CodeIgniter\API\ResponseTrait;

class Historial extends BaseController
{
    use ResponseTrait;

    public function index(): string
    {
        return view('historial/index');
    }

    // Visitas cerradas y canceladas
    public function listar()
    {
        $visitas = (new VisitaModelo())
            ->select('mesa_visita.id, mesa_visita.estado, mesa_visita.total, mesa_visita.creado_en, mesa_visita.cerrada_en, mesas.numero AS mesa_numero, usuarios.nombre AS usuario')
            ->join('mesas', 'mesas.id = mesa_visita.mesa_id')
            ->join('usuarios', 'usuarios.id = mesa_visita.usuario_id', 'left')
            ->where('mesa_visita.estado !=', 'abierta')
            ->orderBy('mesa_visita.cerrada_en', 'DESC')
            ->findAll();

        return $this->respond($visitas);
    }
}
