<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($this->renderSection('titulo')) ?> | Gestión de Órdenes</title>
    <link rel="stylesheet" href="<?= base_url('css/vendor/bootstrap-5.3.3.min.css') ?>">
    <link rel="stylesheet" href="<?= recurso('css/estilos.css') ?>">
</head>
<body class="d-flex align-items-center min-vh-100">

<main class="container contenedor-acceso">
    <div id="contenedor-alertas"></div>
    <?= $this->renderSection('contenido') ?>
</main>

<script>
    const URL_BASE = '<?= base_url('/') ?>';
</script>
<script src="<?= base_url('js/vendor/jquery-3.7.1.min.js') ?>"></script>
<script src="<?= base_url('js/vendor/bootstrap-5.3.3.bundle.min.js') ?>"></script>
<script src="<?= recurso('js/app.js') ?>"></script>
<?= $this->renderSection('scripts') ?>
</body>
</html>
