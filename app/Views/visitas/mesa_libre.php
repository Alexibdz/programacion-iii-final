<?= $this->extend('plantilla') ?>

<?= $this->section('titulo') ?>Mesa <?= esc($mesa['numero']) ?><?= $this->endSection() ?>

<?= $this->section('contenido') ?>
<a href="<?= base_url('mesas') ?>" class="btn btn-outline-dark btn-lg mb-3">← Mapa de mesas</a>

<div class="card shadow-sm tarjeta-mesa-libre" id="mesa" data-id="<?= esc($mesa['id']) ?>">
    <div class="card-body p-4 text-center">
        <h1 class="h3 mb-2">Mesa <?= esc($mesa['numero']) ?> <span class="badge text-bg-success align-middle fs-6">Libre</span></h1>
        <p class="text-secondary mb-4">Capacidad para <?= esc($mesa['capacidad']) ?> personas</p>
        <button type="button" class="btn btn-success btn-lg w-100" id="boton-abrir-visita">Abrir visita</button>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= recurso('js/mesa_libre.js') ?>"></script>
<?= $this->endSection() ?>
