<?php

namespace App\Controllers;

class Home extends BaseController
{
    // Redirección a la pantalla de inicio del rol
    public function index()
    {
        if (! session()->has('usuario_id')) {
            return redirect()->to('login');
        }

        return redirect()->to(paginaInicio(session('rol')));
    }
}
