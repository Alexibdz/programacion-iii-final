<?= $this->extend('plantilla') ?>

<?= $this->section('titulo') ?>Estadísticas<?= $this->endSection() ?>

<?= $this->section('contenido') ?>
<h1 class="h4 mb-3">Estadísticas</h1>

<div class="card shadow-sm mb-3">
    <div class="card-body d-flex flex-wrap align-items-end gap-3">
        <div>
            <div class="form-label">Período</div>
            <div class="btn-group" role="group" id="modo-periodo">
                <input type="radio" class="btn-check" name="modo" id="modo-hoy" value="hoy" checked>
                <label class="btn btn-outline-dark" for="modo-hoy">Hoy</label>
                <input type="radio" class="btn-check" name="modo" id="modo-dia" value="dia">
                <label class="btn btn-outline-dark" for="modo-dia">Día específico</label>
                <input type="radio" class="btn-check" name="modo" id="modo-rango" value="rango">
                <label class="btn btn-outline-dark" for="modo-rango">Rango</label>
            </div>
        </div>
        <div class="d-none" id="campo-desde">
            <label for="fecha-desde" class="form-label" id="etiqueta-desde">Día</label>
            <input type="date" class="form-control" id="fecha-desde">
        </div>
        <div class="d-none" id="campo-hasta">
            <label for="fecha-hasta" class="form-label">Hasta</label>
            <input type="date" class="form-control" id="fecha-hasta">
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-12 col-md-4">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <div class="small text-secondary">Vendido</div>
                <div class="fs-3 fw-semibold" id="resumen-vendido"></div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <div class="small text-secondary">Mesas cobradas</div>
                <div class="fs-3 fw-semibold" id="resumen-cobradas"></div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <div class="small text-secondary">Ticket promedio</div>
                <div class="fs-3 fw-semibold" id="resumen-promedio"></div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= recurso('js/estadisticas.js') ?>"></script>
<?= $this->endSection() ?>
