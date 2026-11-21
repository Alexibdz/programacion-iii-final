<?php

namespace App\Controllers;

use App\Models\AuditoriaModelo;
use CodeIgniter\API\ResponseTrait;

class Auditoria extends BaseController
{
    use ResponseTrait;

    public function index(): string
    {
        return view('auditoria/index');
    }

    // Listado de movimientos
    public function listar()
    {
        return $this->respond((new AuditoriaModelo())->listarConUsuario());
    }
}
