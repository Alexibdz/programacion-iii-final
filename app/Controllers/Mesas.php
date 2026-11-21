<?php

namespace App\Controllers;

class Mesas extends BaseController
{
    public function index(): string
    {
        return view('mesas/index');
    }

    public function gestion(): string
    {
        return view('mesas/gestion');
    }
}
