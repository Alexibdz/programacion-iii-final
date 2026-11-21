<?= $this->extend('plantilla') ?>

<?= $this->section('titulo') ?>Categorías<?= $this->endSection() ?>

<?= $this->section('contenido') ?>
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <h1 class="h4 mb-0">Categorías</h1>
    <div class="d-flex align-items-center gap-3">
        <div class="form-check form-switch mb-0">
            <input class="form-check-input" type="checkbox" id="mostrar-bajas">
            <label class="form-check-label" for="mostrar-bajas">Ver dadas de baja</label>
        </div>
        <button type="button" class="btn btn-dark" id="boton-nueva">Nueva categoría</button>
    </div>
</div>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Nombre</th>
                    <th>Pasa por cocina</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody id="tabla-categorias"></tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="modal-categoria" tabindex="-1">
    <div class="modal-dialog">
        <form class="modal-content" id="formulario-categoria">
            <div class="modal-header">
                <h2 class="modal-title h5" id="titulo-modal">Nueva categoría</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="categoria-id">
                <div class="mb-3">
                    <label for="categoria-nombre" class="form-label">Nombre</label>
                    <input type="text" class="form-control" id="categoria-nombre" maxlength="50" required>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="categoria-cocina">
                    <label class="form-check-label" for="categoria-cocina">Pasa por cocina</label>
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
<script src="<?= recurso('js/categorias.js') ?>"></script>
<?= $this->endSection() ?>
