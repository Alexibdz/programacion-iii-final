<?php

namespace App\Controllers;

use App\Models\UsuarioModelo;
use CodeIgniter\API\ResponseTrait;

class Usuarios extends BaseController
{
    use ResponseTrait;

    // Roles que se pueden asignar desde esta pantalla
    private const ROLES_ASIGNABLES = ['administrador', 'mozo', 'cocina'];

    private UsuarioModelo $usuarios;

    public function __construct()
    {
        $this->usuarios = new UsuarioModelo();
    }

    public function index(): string
    {
        return view('usuarios/index');
    }

    // Listado de usuarios
    public function listar()
    {
        return $this->respond($this->usuarios->listarSinContrasena($this->request->getGet('bajas') === '1'));
    }

    public function crear()
    {
        $datos = $this->request->getJSON(true) ?? [];

        if (! in_array($datos['rol'] ?? '', self::ROLES_ASIGNABLES, true)) {
            return $this->failValidationErrors(['rol' => 'Elegí administrador, mozo o cocina.']);
        }

        $error = $this->validarContrasena($datos['password'] ?? '', true);

        if ($error !== null) {
            return $this->failValidationErrors(['password' => $error]);
        }

        $id = $this->usuarios->insert([
            'nombre'        => trim($datos['nombre'] ?? ''),
            'usuario'       => trim($datos['usuario'] ?? ''),
            'rol'           => $datos['rol'],
            'password_hash' => password_hash($datos['password'], PASSWORD_DEFAULT),
        ]);

        if ($id === false) {
            return $this->failValidationErrors($this->usuarios->errors());
        }

        return $this->respondCreated(['id' => $id]);
    }

    public function actualizar(int $id)
    {
        $usuario = $this->usuarios->find($id);

        if ($usuario === null) {
            return $this->failNotFound('El usuario no existe.');
        }

        $datos  = $this->request->getJSON(true) ?? [];
        $cambio = [
            'id'      => $id,
            'nombre'  => trim($datos['nombre'] ?? ''),
            'usuario' => trim($datos['usuario'] ?? ''),
            'rol'     => $datos['rol'] ?? '',
        ];

        // El principal conserva su rol
        if ($usuario['rol'] === 'principal') {
            $cambio['rol'] = 'principal';
        } elseif (! in_array($cambio['rol'], self::ROLES_ASIGNABLES, true)) {
            return $this->failValidationErrors(['rol' => 'Elegí administrador, mozo o cocina.']);
        }

        // Contraseña opcional al editar
        if (($datos['password'] ?? '') !== '') {
            $error = $this->validarContrasena($datos['password'], false);

            if ($error !== null) {
                return $this->failValidationErrors(['password' => $error]);
            }

            $cambio['password_hash'] = password_hash($datos['password'], PASSWORD_DEFAULT);
        }

        if (! $this->usuarios->update($id, $cambio)) {
            return $this->failValidationErrors($this->usuarios->errors());
        }

        return $this->respond(['id' => $id]);
    }

    public function eliminar(int $id)
    {
        $usuario = $this->usuarios->find($id);

        if ($usuario === null) {
            return $this->failNotFound('El usuario no existe.');
        }

        if ($usuario['rol'] === 'principal') {
            return $this->fail('El usuario principal no se puede dar de baja.', 409);
        }

        $this->usuarios->delete($id);

        return $this->respondDeleted(['id' => $id]);
    }

    public function restaurar(int $id)
    {
        if (! $this->usuarios->restaurar($id)) {
            return $this->failNotFound('El usuario no existe o no está dado de baja.');
        }

        return $this->respond(['id' => $id]);
    }

    // Reglas de contraseña
    private function validarContrasena(string $password, bool $obligatoria): ?string
    {
        if ($password === '') {
            return $obligatoria ? 'La contraseña es obligatoria.' : null;
        }

        if (mb_strlen($password) < 6) {
            return 'La contraseña debe tener al menos 6 caracteres.';
        }

        if ($password === CONTRASENA_INICIAL) {
            return 'La contraseña no puede ser la inicial.';
        }

        return null;
    }
}
