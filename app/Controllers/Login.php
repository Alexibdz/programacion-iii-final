<?php

namespace App\Controllers;

use App\Models\UsuarioModelo;
use CodeIgniter\API\ResponseTrait;

class Login extends BaseController
{
    use ResponseTrait;

    public function index()
    {
        if (session()->has('usuario_id')) {
            return redirect()->to(paginaInicio(session('rol')));
        }

        return view('login/index');
    }

    // Ingreso
    public function ingresar()
    {
        $datos    = $this->request->getJSON(true) ?? [];
        $usuario  = trim($datos['usuario'] ?? '');
        $password = $datos['password'] ?? '';

        if ($usuario === '' || $password === '') {
            return $this->failValidationErrors(['usuario' => 'Ingresá usuario y contraseña.']);
        }

        $registro = (new UsuarioModelo())->where('usuario', $usuario)->first();

        if ($registro === null || ! password_verify($password, $registro['password_hash'])) {
            return $this->failUnauthorized('Usuario o contraseña incorrectos.');
        }

        // Nuevo id de sesión para evitar robo de sesión
        session()->regenerate(true);

        $debeCambiar = $password === CONTRASENA_INICIAL;

        session()->set([
            'usuario_id'                => (int) $registro['id'],
            'nombre'                    => $registro['nombre'],
            'rol'                       => $registro['rol'],
            'debe_cambiar_credenciales' => $debeCambiar,
        ]);

        return $this->respond([
            'redireccion' => base_url($debeCambiar ? 'cambiar-credenciales' : paginaInicio($registro['rol'])),
        ]);
    }

    public function cambiarCredenciales(): string
    {
        return view('login/cambiar_credenciales');
    }

    // Guardado de usuario y contraseña nuevos
    public function guardarCredenciales()
    {
        $id          = (int) session('usuario_id');
        $datos       = $this->request->getJSON(true) ?? [];
        $datos['id'] = $id;

        $reglas = [
            'id'                    => 'required|is_natural_no_zero',
            'usuario'               => 'required|min_length[3]|max_length[40]|is_unique[usuarios.usuario,id,{id}]',
            'password'              => 'required|min_length[6]',
            'password_confirmacion' => 'required|matches[password]',
        ];

        $mensajes = [
            'usuario' => [
                'required'   => 'El usuario es obligatorio.',
                'min_length' => 'El usuario debe tener al menos 3 caracteres.',
                'max_length' => 'El usuario no puede superar los 40 caracteres.',
                'is_unique'  => 'Ese usuario ya existe.',
            ],
            'password' => [
                'required'   => 'La contraseña es obligatoria.',
                'min_length' => 'La contraseña debe tener al menos 6 caracteres.',
            ],
            'password_confirmacion' => [
                'required' => 'Repetí la contraseña.',
                'matches'  => 'Las contraseñas no coinciden.',
            ],
        ];

        if (! $this->validateData($datos, $reglas, $mensajes)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        if ($datos['password'] === CONTRASENA_INICIAL) {
            return $this->failValidationErrors(['password' => 'La nueva contraseña no puede ser la inicial.']);
        }

        (new UsuarioModelo())->update($id, [
            'usuario'       => trim($datos['usuario']),
            'password_hash' => password_hash($datos['password'], PASSWORD_DEFAULT),
        ]);

        session()->set('debe_cambiar_credenciales', false);

        return $this->respond(['redireccion' => base_url(paginaInicio(session('rol')))]);
    }

    // Cierre de sesión
    public function salir()
    {
        session()->destroy();

        return redirect()->to('login');
    }
}
