<?php

namespace App\Controllers;

use App\Models\OrdenModelo;
use CodeIgniter\API\ResponseTrait;

class Cocina extends BaseController
{
    use ResponseTrait;

    public function index(): string
    {
        return view('cocina/index');
    }

    // Órdenes pendientes y en preparación
    public function cola()
    {
        return $this->respond((new OrdenModelo())->colaDeCocina());
    }
}
