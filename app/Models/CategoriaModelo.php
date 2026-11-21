<?php

namespace App\Models;

// Categorías del menú
class CategoriaModelo extends ModeloBase
{
    protected $table         = 'categorias';
    protected $allowedFields = ['nombre', 'requiere_cocina'];

    // Sin fechas de alta y modificación
    protected $useTimestamps = false;

    protected $validationRules = [
        'id'              => 'permit_empty|is_natural_no_zero',
        'nombre'          => 'required|max_length[50]|is_unique[categorias.nombre,id,{id}]',
        'requiere_cocina' => 'required|in_list[0,1]',
    ];

    protected $validationMessages = [
        'nombre' => [
            'required'   => 'El nombre es obligatorio.',
            'max_length' => 'El nombre no puede superar los 50 caracteres.',
            'is_unique'  => 'Ya existe una categoría con ese nombre.',
        ],
        'requiere_cocina' => [
            'required' => 'Indicá si la categoría pasa por cocina.',
            'in_list'  => 'El valor de cocina no es válido.',
        ],
    ];
}
