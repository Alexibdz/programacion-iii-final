<?php

namespace App\Controllers;

use App\Models\ProductoModelo;
use CodeIgniter\API\ResponseTrait;

class Productos extends BaseController
{
    use ResponseTrait;

    private ProductoModelo $productos;

    public function __construct()
    {
        $this->productos = new ProductoModelo();
    }

    public function index(): string
    {
        return view('productos/index');
    }

    // Listado de productos
    public function listar()
    {
        $categoriaId = $this->request->getGet('categoria');

        return $this->respond($this->productos->listarConCategoria(
            $this->request->getGet('bajas') === '1',
            $categoriaId ? (int) $categoriaId : null,
        ));
    }

    public function crear()
    {
        $datos = $this->request->getJSON(true) ?? [];
        $id    = $this->productos->insert($datos);

        if ($id === false) {
            return $this->failValidationErrors($this->productos->errors());
        }

        return $this->respondCreated($this->productos->find($id));
    }

    public function actualizar(int $id)
    {
        if ($this->productos->find($id) === null) {
            return $this->failNotFound('El producto no existe.');
        }

        $datos = $this->request->getJSON(true) ?? [];

        if (! $this->productos->update($id, $datos)) {
            return $this->failValidationErrors($this->productos->errors());
        }

        return $this->respond($this->productos->find($id));
    }

    public function eliminar(int $id)
    {
        if ($this->productos->find($id) === null) {
            return $this->failNotFound('El producto no existe.');
        }

        $this->productos->delete($id);

        return $this->respondDeleted(['id' => $id]);
    }

    public function restaurar(int $id)
    {
        if (! $this->productos->restaurar($id)) {
            return $this->failNotFound('El producto no existe o no está dado de baja.');
        }

        return $this->respond($this->productos->find($id));
    }
}
