<?php

namespace App\Controllers;

class Cocina extends BaseController
{
    public function index(): string
    {
        return view('cocina/index');
    }
}
