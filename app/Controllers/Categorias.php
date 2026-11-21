<?php

namespace App\Controllers;

use App\Models\CategoriaModelo;
use App\Models\ProductoModelo;
use CodeIgniter\API\ResponseTrait;

class Categorias extends BaseController
{
    use ResponseTrait;

    private CategoriaModelo $categorias;

    public function __construct()
    {
        $this->categorias = new CategoriaModelo();
    }

    public function index(): string
    {
        return view('categorias/index');
    }

    // Listado de categorías
    public function listar()
    {
        $modelo = $this->request->getGet('bajas') === '1'
            ? $this->categorias->withDeleted()
            : $this->categorias;

        return $this->respond($modelo->orderBy('nombre')->findAll());
    }

    public function crear()
    {
        $datos = $this->request->getJSON(true) ?? [];
        $id    = $this->categorias->insert($datos);

        if ($id === false) {
            return $this->failValidationErrors($this->categorias->errors());
        }

        return $this->respondCreated($this->categorias->find($id));
    }

    public function actualizar(int $id)
    {
        if ($this->categorias->find($id) === null) {
            return $this->failNotFound('La categoría no existe.');
        }

        // Id para la regla de nombre único
        $datos       = $this->request->getJSON(true) ?? [];
        $datos['id'] = $id;

        if (! $this->categorias->update($id, $datos)) {
            return $this->failValidationErrors($this->categorias->errors());
        }

        return $this->respond($this->categorias->find($id));
    }

    public function eliminar(int $id)
    {
        if ($this->categorias->find($id) === null) {
            return $this->failNotFound('La categoría no existe.');
        }

        $productosActivos = (new ProductoModelo())->where('categoria_id', $id)->countAllResults();

        if ($productosActivos > 0) {
            return $this->fail('No se puede dar de baja: la categoría tiene productos activos.', 409);
        }

        $this->categorias->delete($id);

        return $this->respondDeleted(['id' => $id]);
    }

    public function restaurar(int $id)
    {
        if (! $this->categorias->restaurar($id)) {
            return $this->failNotFound('La categoría no existe o no está dada de baja.');
        }

        return $this->respond($this->categorias->find($id));
    }
}
