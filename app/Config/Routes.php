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
});

// Mozo y administración
$routes->group('', ['filter' => 'acceso:principal,administrador,mozo'], static function ($routes) {
    $routes->get('mesas', 'Mesas::index');
    $routes->get('mesas/gestion', 'Mesas::gestion');
    $routes->get('visitas/(:num)', 'Visitas::ver/$1');
    $routes->get('visitas/(:num)/recibo', 'Visitas::recibo/$1');
    $routes->get('productos', 'Productos::index');
    $routes->get('categorias', 'Categorias::index');
    $routes->get('historial', 'Historial::index');
    $routes->get('auditoria', 'Auditoria::index');
});

// Usuarios
$routes->group('', ['filter' => 'acceso:principal'], static function ($routes) {
    $routes->get('usuarios', 'Usuarios::index');
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

    // BLOQUE 100% DESARROLLADO CON IA (/docs/diseño-de-mesas)
    $routes->get('mesas', 'Mesas::listar');
    $routes->post('mesas', 'Mesas::crear');
    $routes->put('mesas/(:num)', 'Mesas::actualizar/$1');
    $routes->delete('mesas/(:num)', 'Mesas::eliminar/$1');
    $routes->post('mesas/(:num)/restaurar', 'Mesas::restaurar/$1');
    $routes->post('mesas/(:num)/abrir', 'Mesas::abrir/$1');
    // FIN BLOQUE 100% DESARROLLADO CON IA (/docs/diseño-de-mesas)
});
