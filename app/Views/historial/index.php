<?= $this->extend('plantilla') ?>

<?= $this->section('titulo') ?>Historial<?= $this->endSection() ?>

<?= $this->section('estilos') ?>
<link rel="stylesheet" href="<?= base_url('css/vendor/datatables-2.1.8.bootstrap5.min.css') ?>">
<?= $this->endSection() ?>

<?= $this->section('contenido') ?>
<h1 class="h4 mb-3">Historial de visitas</h1>

<div class="card shadow-sm">
    <div class="card-body">
        <table id="tabla-historial" class="table table-sm table-hover align-middle w-100">
            <thead>
                <tr>
                    <th>Cierre</th>
                    <th>Recibo</th>
                    <th>Mesa</th>
                    <th>Atendió</th>
                    <th>Estado</th>
                    <th class="text-end">Total</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('js/vendor/datatables-2.1.8.min.js') ?>"></script>
<script src="<?= base_url('js/vendor/datatables-2.1.8.bootstrap5.min.js') ?>"></script>
<script src="<?= recurso('js/historial.js') ?>"></script>
<?= $this->endSection() ?>
