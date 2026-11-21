<?php
// BLOQUE 100% DESARROLLADO CON IA (/docs/diseño-de-mesas)

// Secciones del menú lateral
$menu = [
    'Salón' => [
        'mesas'  => 'Mapa de mesas',
        'cocina' => 'Cocina',
    ],
    'Menú' => [
        'productos'  => 'Productos',
        'categorias' => 'Categorías',
    ],
    'Administración' => [
        'mesas/gestion' => 'Gestión de mesas',
        'usuarios'      => 'Usuarios',
        'historial'     => 'Historial',
        'auditoria'     => 'Auditoría',
    ],
];

// FIN BLOQUE 100% DESARROLLADO CON IA (/docs/diseño-de-mesas)
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($this->renderSection('titulo')) ?> | Gestión de Órdenes</title>
    <link rel="stylesheet" href="<?= base_url('css/vendor/bootstrap-5.3.3.min.css') ?>">
    <link rel="stylesheet" href="<?= recurso('css/estilos.css') ?>">
</head>
<body>

<?php // BLOQUE 100% DESARROLLADO CON IA (/docs/diseño-de-mesas) ?>
<div class="d-flex min-vh-100">
    <aside class="barra-lateral d-flex flex-column p-3 text-bg-dark">
        <a class="fs-5 fw-semibold text-white text-decoration-none px-2 mb-2" href="<?= base_url('/') ?>">Gestión de Órdenes</a>

        <nav class="menu-lateral flex-grow-1">
            <?php foreach ($menu as $titulo => $enlaces): ?>
                <div class="menu-titulo"><?= esc($titulo) ?></div>
                <ul class="nav flex-column">
                    <?php foreach ($enlaces as $ruta => $texto): ?>
                        <?php $activo = url_is($ruta) || ($ruta === 'mesas' && url_is('visitas*')) ?>
                        <li class="nav-item">
                            <a class="nav-link<?= $activo ? ' active' : '' ?>" href="<?= base_url($ruta) ?>"<?= $activo ? ' aria-current="page"' : '' ?>><?= esc($texto) ?></a>
                        </li>
                    <?php endforeach ?>
                </ul>
            <?php endforeach ?>
        </nav>

        <a class="btn btn-outline-light btn-sm mt-4" href="<?= base_url('login') ?>">Salir</a>
    </aside>

    <main class="flex-grow-1 min-w-0 p-4">
        <div id="contenedor-alertas"></div>
        <?= $this->renderSection('contenido') ?>
    </main>
</div>
<?php // FIN BLOQUE 100% DESARROLLADO CON IA (/docs/diseño-de-mesas) ?>

<script>
    const URL_BASE = '<?= base_url('/') ?>';
</script>
<script src="<?= base_url('js/vendor/jquery-3.7.1.min.js') ?>"></script>
<script src="<?= base_url('js/vendor/bootstrap-5.3.3.bundle.min.js') ?>"></script>
<script src="<?= recurso('js/app.js') ?>"></script>
<?= $this->renderSection('scripts') ?>
</body>
</html>
