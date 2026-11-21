<?= $this->extend('plantilla_acceso') ?>

<?= $this->section('titulo') ?>Cambiar credenciales<?= $this->endSection() ?>

<?= $this->section('contenido') ?>
<div class="card shadow-sm">
    <div class="card-body p-4">
        <h1 class="h5 mb-2">Cambiar usuario y contraseña</h1>
        <p class="text-secondary small mb-4">Estás usando las credenciales iniciales. Antes de continuar tenés que reemplazarlas.</p>
        <form id="formulario-credenciales">
            <div class="mb-3">
                <label for="usuario" class="form-label">Nuevo usuario</label>
                <input type="text" class="form-control" id="usuario" name="usuario" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Nueva contraseña</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <div class="mb-4">
                <label for="password_confirmacion" class="form-label">Repetir contraseña</label>
                <input type="password" class="form-control" id="password_confirmacion" name="password_confirmacion" required>
            </div>
            <button type="submit" class="btn btn-dark w-100">Guardar y continuar</button>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
