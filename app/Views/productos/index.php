<?= $this->extend('plantilla') ?>

<?= $this->section('titulo') ?>Productos<?= $this->endSection() ?>

<?= $this->section('contenido') ?>
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <h1 class="h4 mb-0">Productos</h1>
    <div class="d-flex flex-wrap align-items-center gap-3">
        <select class="form-select w-auto" id="filtro-categoria">
            <option value="">Todas las categorías</option>
        </select>
        <div class="form-check form-switch mb-0">
            <input class="form-check-input" type="checkbox" id="mostrar-bajas">
            <label class="form-check-label" for="mostrar-bajas">Ver dados de baja</label>
        </div>
        <button type="button" class="btn btn-dark" id="boton-nuevo">Nuevo producto</button>
    </div>
</div>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Nombre</th>
                    <th>Categoría</th>
                    <th>Descripción</th>
                    <th class="text-end">Precio</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody id="tabla-productos"></tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="modal-producto" tabindex="-1">
    <div class="modal-dialog">
        <form class="modal-content" id="formulario-producto">
            <div class="modal-header">
                <h2 class="modal-title h5" id="titulo-modal">Nuevo producto</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="producto-id">
                <div class="mb-3">
                    <label for="producto-categoria" class="form-label">Categoría</label>
                    <select class="form-select" id="producto-categoria" required></select>
                </div>
                <div class="mb-3">
                    <label for="producto-nombre" class="form-label">Nombre</label>
                    <input type="text" class="form-control" id="producto-nombre" maxlength="30" required>
                </div>
                <div class="mb-3">
                    <label for="producto-descripcion" class="form-label">Descripción <span class="text-secondary">opcional</span></label>
                    <input type="text" class="form-control" id="producto-descripcion" maxlength="100">
                </div>
                <div>
                    <label for="producto-precio" class="form-label">Precio</label>
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="number" class="form-control" id="producto-precio" min="0.01" step="0.01" required>
                    </div>
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
<script src="<?= recurso('js/productos.js') ?>"></script>
<?= $this->endSection() ?>
