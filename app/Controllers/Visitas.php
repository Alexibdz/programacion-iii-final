<?php

namespace App\Controllers;

class Visitas extends BaseController
{
    public function ver(int $id): string
    {
        return view('visitas/ver', ['visitaId' => $id]);
    }

    public function recibo(int $id): string
    {
        return view('visitas/recibo', ['visitaId' => $id]);
    }
}
