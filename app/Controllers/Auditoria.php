<?php

namespace App\Controllers;

class Auditoria extends BaseController
{
    public function index(): string
    {
        return view('auditoria/index');
    }
}
