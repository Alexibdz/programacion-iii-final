<?php

namespace App\Controllers;

class Login extends BaseController
{
    public function index(): string
    {
        return view('login/index');
    }

    public function cambiarCredenciales(): string
    {
        return view('login/cambiar_credenciales');
    }
}
