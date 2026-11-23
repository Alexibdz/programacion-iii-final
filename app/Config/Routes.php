<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Home::index');

// Acceso
$routes->get('login', 'Login::index');
$routes->post('login', 'Login::ingresar');
$routes->get('salir', 'Login::salir');

// Cambio de credenciales iniciales
$routes->group('', ['filter' => 'acceso'], static function ($routes) {
    $routes->get('cambiar-credenciales', 'Login::cambiarCredenciales');
    $routes->post('cambiar-credenciales', 'Login::guardarCredenciales');
});

// Cocina
$routes->group('', ['filter' => 'acceso:cocina,principal,administrador'], static function ($routes) {
    $routes->get('cocina', 'Cocina::index');
    $routes->get('api/cocina', 'Cocina::cola');
});

// Mozo y administración
$routes->group('', ['filter' => 'acceso:principal,administrador,mozo'], static function ($routes) {
    $routes->get('mesas', 'Mesas::index');
    $routes->get('mesas/gestion', 'Mesas::gestion');
    $routes->get('mesas/(:num)', 'Visitas::mesa/$1');
    $routes->get('visitas/(:num)', 'Visitas::ver/$1');
    $routes->get('visitas/(:num)/nueva', 'Visitas::nuevaOrden/$1');
    $routes->get('visitas/(:num)/recibo', 'Visitas::recibo/$1');
    $routes->get('productos', 'Productos::index');
    $routes->get('categorias', 'Categorias::index');
    $routes->get('historial', 'Historial::index');
    $routes->get('auditoria', 'Auditoria::index');
    $routes->get('estadisticas', 'Estadisticas::index');
});

// Usuarios
$routes->group('', ['filter' => 'acceso:principal'], static function ($routes) {
    $routes->get('usuarios', 'Usuarios::index');
    $routes->get('api/usuarios', 'Usuarios::listar');
    $routes->post('api/usuarios', 'Usuarios::crear');
    $routes->put('api/usuarios/(:num)', 'Usuarios::actualizar/$1');
    $routes->delete('api/usuarios/(:num)', 'Usuarios::eliminar/$1');
    $routes->post('api/usuarios/(:num)/restaurar', 'Usuarios::restaurar/$1');
});

// API JSON
$routes->group('api', ['filter' => 'acceso:principal,administrador,mozo'], static function ($routes) {
    $routes->get('categorias', 'Categorias::listar');
    $routes->post('categorias', 'Categorias::crear');
    $routes->put('categorias/(:num)', 'Categorias::actualizar/$1');
    $routes->delete('categorias/(:num)', 'Categorias::eliminar/$1');
    $routes->post('categorias/(:num)/restaurar', 'Categorias::restaurar/$1');

    $routes->get('productos', 'Productos::listar');
    $routes->post('productos', 'Productos::crear');
    $routes->put('productos/(:num)', 'Productos::actualizar/$1');
    $routes->delete('productos/(:num)', 'Productos::eliminar/$1');
    $routes->post('productos/(:num)/restaurar', 'Productos::restaurar/$1');

    $routes->get('auditoria', 'Auditoria::listar');
    $routes->get('historial', 'Historial::listar');
    $routes->get('estadisticas', 'Estadisticas::resumen');

    $routes->get('visitas/(:num)', 'Visitas::detalle/$1');
    $routes->post('visitas/(:num)/ordenes', 'Visitas::crearOrden/$1');
    $routes->post('visitas/(:num)/cerrar', 'Visitas::cerrar/$1');

    // BLOQUE 100% DESARROLLADO CON IA (/docs/diseño-de-mesas)
    $routes->get('mesas', 'Mesas::listar');
    $routes->post('mesas', 'Mesas::crear');
    $routes->put('mesas/(:num)', 'Mesas::actualizar/$1');
    $routes->delete('mesas/(:num)', 'Mesas::eliminar/$1');
    $routes->post('mesas/(:num)/restaurar', 'Mesas::restaurar/$1');
    $routes->post('mesas/(:num)/abrir', 'Mesas::abrir/$1');
    // FIN BLOQUE 100% DESARROLLADO CON IA (/docs/diseño-de-mesas)
});

// Estados de órdenes, compartido entre mozo y cocina
$routes->group('api', ['filter' => 'acceso:principal,administrador,mozo,cocina'], static function ($routes) {
    $routes->put('ordenes/(:num)/estado', 'Ordenes::cambiarEstado/$1');
});
