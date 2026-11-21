<?php

namespace App\Models;

// Usuarios del sistema
class UsuarioModelo extends ModeloBase
{
    protected $table         = 'usuarios';
    protected $allowedFields = ['nombre', 'usuario', 'password_hash', 'rol'];

    // Sin fechas de alta y modificación
    protected $useTimestamps = false;
}
