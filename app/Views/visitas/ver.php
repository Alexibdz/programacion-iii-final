<?= $this->extend('plantilla') ?>

<?= $this->section('titulo') ?>Visita de mesa<?= $this->endSection() ?>

<?= $this->section('contenido') ?>
<div id="visita" data-id="<?= esc($visitaId) ?>">
    <a href="<?= base_url($volverRuta) ?>" class="btn btn-outline-dark btn-lg mb-3">← <?= esc($volverTexto) ?></a>

    <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-3">
        <div>
            <h1 class="h4 mb-1">Mesa <span id="visita-mesa"></span> <span id="visita-estado" class="badge align-middle"></span></h1>
            <div class="text-secondary small" id="visita-horario"></div>
        </div>
        <div class="text-end">
            <div class="small text-secondary">Total</div>
            <div class="fs-4 fw-semibold" id="visita-total"></div>
        </div>
    </div>

    <div class="d-flex flex-wrap gap-2 mb-3">
        <a class="btn btn-dark" id="boton-nueva-orden" href="<?= base_url('visitas/' . $visitaId . '/nueva') ?>">Nueva orden</a>
        <button type="button" class="btn btn-outline-success" id="boton-cerrar-visita">Cerrar visita</button>
        <a class="btn btn-outline-secondary d-none" id="boton-recibo" target="_blank">Ver recibo</a>
    </div>

    <div id="lista-ordenes"></div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= recurso('js/visita.js') ?>"></script>
<?= $this->endSection() ?>
