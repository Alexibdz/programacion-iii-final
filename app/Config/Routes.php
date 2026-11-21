<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Home::index');

// Acceso
$routes->get('login', 'Login::index');
$routes->get('cambiar-credenciales', 'Login::cambiarCredenciales');

// Cocina
$routes->get('cocina', 'Cocina::index');

// Mozo y administración
$routes->get('mesas', 'Mesas::index');
$routes->get('mesas/gestion', 'Mesas::gestion');
$routes->get('visitas/(:num)', 'Visitas::ver/$1');
$routes->get('visitas/(:num)/recibo', 'Visitas::recibo/$1');
$routes->get('productos', 'Productos::index');
$routes->get('categorias', 'Categorias::index');
$routes->get('usuarios', 'Usuarios::index');
$routes->get('historial', 'Historial::index');
$routes->get('auditoria', 'Auditoria::index');
