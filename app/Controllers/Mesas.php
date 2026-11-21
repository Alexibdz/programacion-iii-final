<?php

// BLOQUE 100% DESARROLLADO CON IA (/docs/diseño-de-mesas)

namespace App\Controllers;

use App\Models\MesaModelo;
use App\Models\VisitaModelo;
use CodeIgniter\API\ResponseTrait;

class Mesas extends BaseController
{
    use ResponseTrait;

    private MesaModelo $mesas;

    public function __construct()
    {
        $this->mesas = new MesaModelo();
    }

    public function index(): string
    {
        return view('mesas/index');
    }

    public function gestion(): string
    {
        return view('mesas/gestion');
    }

    // Listado de mesas con estado
    public function listar()
    {
        return $this->respond($this->mesas->listarConEstado($this->request->getGet('bajas') === '1'));
    }

    public function crear()
    {
        $datos = $this->request->getJSON(true) ?? [];
        $id    = $this->mesas->insert($datos);

        if ($id === false) {
            return $this->failValidationErrors($this->mesas->errors());
        }

        return $this->respondCreated($this->mesas->find($id));
    }

    public function actualizar(int $id)
    {
        if ($this->mesas->find($id) === null) {
            return $this->failNotFound('La mesa no existe.');
        }

        // Id para la regla de número único
        $datos       = $this->request->getJSON(true) ?? [];
        $datos['id'] = $id;

        if (! $this->mesas->update($id, $datos)) {
            return $this->failValidationErrors($this->mesas->errors());
        }

        return $this->respond($this->mesas->find($id));
    }

    public function eliminar(int $id)
    {
        if ($this->mesas->find($id) === null) {
            return $this->failNotFound('La mesa no existe.');
        }

        if ($this->mesas->visitaAbierta($id) !== null) {
            return $this->fail('No se puede dar de baja: la mesa está ocupada.', 409);
        }

        $this->mesas->delete($id);

        return $this->respondDeleted(['id' => $id]);
    }

    public function restaurar(int $id)
    {
        if (! $this->mesas->restaurar($id)) {
            return $this->failNotFound('La mesa no existe o no está dada de baja.');
        }

        return $this->respond($this->mesas->find($id));
    }

    // Apertura de una visita
    public function abrir(int $id)
    {
        if ($this->mesas->find($id) === null) {
            return $this->failNotFound('La mesa no existe.');
        }

        $abierta = $this->mesas->visitaAbierta($id);

        if ($abierta !== null) {
            return $this->fail('La mesa ya está ocupada.', 409);
        }

        $usuarioId = session('usuario_id');

        if ($usuarioId === null) {
            return $this->failUnauthorized('Iniciá sesión para abrir una mesa.');
        }

        $visitaId = (new VisitaModelo())->abrir($id, (int) $usuarioId);

        return $this->respondCreated(['visita_id' => $visitaId]);
    }
}

// FIN BLOQUE 100% DESARROLLADO CON IA (/docs/diseño-de-mesas)
