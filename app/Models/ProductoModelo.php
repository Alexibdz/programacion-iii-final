<?php

namespace App\Models;

// Productos del menú
class ProductoModelo extends ModeloBase
{
    protected $table         = 'productos';
    protected $allowedFields = ['categoria_id', 'nombre', 'precio'];

    protected $validationRules = [
        'categoria_id' => 'required|is_natural_no_zero|is_not_unique[categorias.id]',
        'nombre'       => 'required|max_length[30]',
        'precio'       => 'required|decimal|greater_than[0]',
    ];

    protected $validationMessages = [
        'categoria_id' => [
            'required'           => 'Elegí una categoría.',
            'is_natural_no_zero' => 'La categoría no es válida.',
            'is_not_unique'      => 'La categoría elegida no existe.',
        ],
        'nombre' => [
            'required'   => 'El nombre es obligatorio.',
            'max_length' => 'El nombre no puede superar los 30 caracteres.',
        ],
        'precio' => [
            'required'     => 'El precio es obligatorio.',
            'decimal'      => 'El precio debe ser un número.',
            'greater_than' => 'El precio debe ser mayor a cero.',
        ],
    ];

    // Productos con su categoría
    public function listarConCategoria(bool $incluirBajas = false, ?int $categoriaId = null): array
    {
        $consulta = $this->select('productos.*, categorias.nombre AS categoria')
            ->join('categorias', 'categorias.id = productos.categoria_id');

        if ($incluirBajas) {
            $consulta->withDeleted();
        }

        if ($categoriaId !== null) {
            $consulta->where('productos.categoria_id', $categoriaId);
        }

        return $consulta->orderBy('categorias.nombre')->orderBy('productos.nombre')->findAll();
    }
}
