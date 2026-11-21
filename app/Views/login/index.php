<?= $this->extend('plantilla_acceso') ?>

<?= $this->section('titulo') ?>Ingresar<?= $this->endSection() ?>

<?= $this->section('contenido') ?>
<div class="card shadow-sm">
    <div class="card-body p-4">
        <h1 class="h4 text-center mb-4">Gestión de Órdenes</h1>
        <form id="formulario-login">
            <div class="mb-3">
                <label for="usuario" class="form-label">Usuario</label>
                <input type="text" class="form-control" id="usuario" name="usuario" required autofocus>
            </div>
            <div class="mb-4">
                <label for="password" class="form-label">Contraseña</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-dark w-100">Ingresar</button>
        </form>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= recurso('js/login.js') ?>"></script>
<?= $this->endSection() ?>
