<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($this->renderSection('titulo')) ?> | Gestión de Órdenes</title>
    <link rel="stylesheet" href="<?= base_url('css/vendor/bootstrap-5.3.3.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('css/estilos.css') ?>">
</head>
<body>

<nav class="navbar navbar-expand-md navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="<?= base_url('/') ?>">Gestión de Órdenes</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menu-principal">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="menu-principal">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link" href="<?= base_url('mesas') ?>">Mesas</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= base_url('cocina') ?>">Cocina</a></li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">Menú</a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="<?= base_url('productos') ?>">Productos</a></li>
                        <li><a class="dropdown-item" href="<?= base_url('categorias') ?>">Categorías</a></li>
                    </ul>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">Administración</a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="<?= base_url('mesas/gestion') ?>">Mesas</a></li>
                        <li><a class="dropdown-item" href="<?= base_url('usuarios') ?>">Usuarios</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="<?= base_url('historial') ?>">Historial</a></li>
                        <li><a class="dropdown-item" href="<?= base_url('auditoria') ?>">Auditoría</a></li>
                    </ul>
                </li>
            </ul>
            <a class="btn btn-outline-light btn-sm" href="<?= base_url('login') ?>">Salir</a>
        </div>
    </div>
</nav>

<main class="container py-4">
    <div id="contenedor-alertas"></div>
    <?= $this->renderSection('contenido') ?>
</main>

<script>
    const URL_BASE = '<?= base_url('/') ?>';
</script>
<script src="<?= base_url('js/vendor/jquery-3.7.1.min.js') ?>"></script>
<script src="<?= base_url('js/vendor/bootstrap-5.3.3.bundle.min.js') ?>"></script>
<script src="<?= base_url('js/app.js') ?>"></script>
<?= $this->renderSection('scripts') ?>
</body>
</html>
