<?php

namespace App\Controllers;

class Categorias extends BaseController
{
    public function index(): string
    {
        return view('categorias/index');
    }
}
