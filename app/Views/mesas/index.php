<?php // BLOQUE 100% DESARROLLADO CON IA (/docs/diseño-de-mesas) ?>
<?= $this->extend('plantilla') ?>

<?= $this->section('titulo') ?>Mapa de mesas<?= $this->endSection() ?>

<?= $this->section('contenido') ?>
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <h1 class="h4 mb-0">Mapa de mesas</h1>
    <div class="d-flex flex-wrap gap-2">
        <span class="badge rounded-pill text-bg-success fs-6 fw-normal" id="resumen-libres">– libres</span>
        <span class="badge rounded-pill text-bg-danger fs-6 fw-normal" id="resumen-ocupadas">– ocupadas</span>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body p-2">
        <div class="plano-contenedor" id="plano"></div>
    </div>
    <div class="card-footer bg-white d-flex flex-wrap gap-3 small text-secondary">
        <span><span class="leyenda-punto leyenda-libre"></span>Libre</span>
        <span><span class="leyenda-punto leyenda-ocupada"></span>Ocupada</span>
        <span class="ms-auto">Tocá una mesa para abrirla o ver su visita.</span>
    </div>
</div>

<div class="modal fade" id="modal-mesa" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title h5" id="mesa-titulo">Mesa</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <dl class="row mb-0" id="mesa-datos"></dl>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-success" id="boton-abrir">Abrir mesa</button>
                <a class="btn btn-dark" id="boton-visita" href="#">Ver visita</a>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= recurso('js/plano.js') ?>"></script>
<script src="<?= recurso('js/mesas.js') ?>"></script>
<?= $this->endSection() ?>
<?php // FIN BLOQUE 100% DESARROLLADO CON IA (/docs/diseño-de-mesas) ?>
