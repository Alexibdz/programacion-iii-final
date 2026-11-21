<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

// Control de sesión, cambio de credenciales y permisos por rol
class Acceso implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $sesion = session();
        $ruta   = $request->getPath();
        $esApi  = str_starts_with($ruta, 'api/');

        // Sin sesión
        if (! $sesion->has('usuario_id')) {
            return $esApi
                ? $this->rechazar(401, 'La sesión expiró, volvé a ingresar.')
                : redirect()->to('login');
        }

        // Credenciales iniciales pendientes de cambio
        if ($sesion->get('debe_cambiar_credenciales') && ! str_starts_with($ruta, 'cambiar-credenciales')) {
            return $esApi
                ? $this->rechazar(403, 'Primero tenés que cambiar las credenciales iniciales.')
                : redirect()->to('cambiar-credenciales');
        }

        // Rol sin permiso
        if ($arguments !== null && ! in_array($sesion->get('rol'), $arguments, true)) {
            return $esApi
                ? $this->rechazar(403, 'No tenés permiso para esta acción.')
                : redirect()->to(paginaInicio($sesion->get('rol')));
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }

    // Respuesta JSON de error para la API
    private function rechazar(int $estado, string $mensaje): ResponseInterface
    {
        return service('response')
            ->setStatusCode($estado)
            ->setJSON(['status' => $estado, 'messages' => ['error' => $mensaje]]);
    }
}
