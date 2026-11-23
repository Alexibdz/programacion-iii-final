<?php

namespace App\Models;

// Usuarios del sistema
class UsuarioModelo extends ModeloBase
{
    protected $table         = 'usuarios';
    protected $allowedFields = ['nombre', 'usuario', 'password_hash', 'rol'];

    // Sin fechas de alta y modificación
    protected $useTimestamps = false;

    protected $validationRules = [
        'id'      => 'permit_empty|is_natural_no_zero',
        'nombre'  => 'required|max_length[60]',
        'usuario' => 'required|min_length[3]|max_length[40]|is_unique[usuarios.usuario,id,{id}]',
        'rol'     => 'required|in_list[principal,administrador,mozo,cocina]',
    ];

    protected $validationMessages = [
        'nombre' => [
            'required'   => 'El nombre es obligatorio.',
            'max_length' => 'El nombre no puede superar los 60 caracteres.',
        ],
        'usuario' => [
            'required'   => 'El usuario es obligatorio.',
            'min_length' => 'El usuario debe tener al menos 3 caracteres.',
            'max_length' => 'El usuario no puede superar los 40 caracteres.',
            'is_unique'  => 'Ese usuario ya existe, revisá también los dados de baja.',
        ],
        'rol' => [
            'required' => 'Elegí un rol.',
            'in_list'  => 'El rol no es válido.',
        ],
    ];

    // Usuarios sin el hash de la contraseña
    public function listarSinContrasena(bool $incluirBajas = false): array
    {
        $consulta = $this->select('id, nombre, usuario, rol, eliminado_en');

        if ($incluirBajas) {
            $consulta->withDeleted();
        }

        return $consulta->orderBy('nombre')->findAll();
    }
}
