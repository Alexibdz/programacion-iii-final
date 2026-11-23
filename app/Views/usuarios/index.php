<?= $this->extend('plantilla') ?>

<?= $this->section('titulo') ?>Usuarios<?= $this->endSection() ?>

<?= $this->section('contenido') ?>
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <h1 class="h4 mb-0">Usuarios</h1>
    <div class="d-flex align-items-center gap-3">
        <div class="form-check form-switch mb-0">
            <input class="form-check-input" type="checkbox" id="mostrar-bajas">
            <label class="form-check-label" for="mostrar-bajas">Ver dados de baja</label>
        </div>
        <button type="button" class="btn btn-dark" id="boton-nuevo">Nuevo usuario</button>
    </div>
</div>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Nombre</th>
                    <th>Usuario</th>
                    <th>Rol</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody id="tabla-usuarios"></tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="modal-usuario" tabindex="-1">
    <div class="modal-dialog">
        <form class="modal-content" id="formulario-usuario">
            <div class="modal-header">
                <h2 class="modal-title h5" id="titulo-modal">Nuevo usuario</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="usuario-id">
                <div class="mb-3">
                    <label for="usuario-nombre" class="form-label">Nombre</label>
                    <input type="text" class="form-control" id="usuario-nombre" maxlength="60" required>
                </div>
                <div class="mb-3">
                    <label for="usuario-usuario" class="form-label">Usuario</label>
                    <input type="text" class="form-control" id="usuario-usuario" minlength="3" maxlength="40" required>
                </div>
                <div class="mb-3">
                    <label for="usuario-rol" class="form-label">Rol</label>
                    <select class="form-select" id="usuario-rol" required>
                        <option value="administrador">Administrador</option>
                        <option value="mozo">Mozo</option>
                        <option value="cocina">Cocina</option>
                    </select>
                    <div class="form-text d-none" id="aviso-principal">El rol del usuario principal no se puede cambiar.</div>
                </div>
                <div>
                    <label for="usuario-password" class="form-label">Contraseña</label>
                    <input type="password" class="form-control" id="usuario-password" minlength="6" autocomplete="new-password">
                    <div class="form-text d-none" id="aviso-password">Dejala vacía para no cambiarla.</div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-dark">Guardar</button>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= recurso('js/usuarios.js') ?>"></script>
<?= $this->endSection() ?>
