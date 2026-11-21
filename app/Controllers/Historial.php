<?php

namespace App\Controllers;

class Historial extends BaseController
{
    public function index(): string
    {
        return view('historial/index');
    }
}
