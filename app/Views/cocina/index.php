<?= $this->extend('plantilla') ?>

<?= $this->section('titulo') ?>Pedidos de cocina<?= $this->endSection() ?>

<?= $this->section('contenido') ?>
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <h1 class="h4 mb-0">Pedidos de cocina</h1>
    <span class="small text-secondary" id="cocina-actualizado"></span>
</div>

<div class="row row-cols-1 row-cols-lg-2 row-cols-xxl-3 g-3" id="cola-cocina"></div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= recurso('js/cocina.js') ?>"></script>
<?= $this->endSection() ?>
