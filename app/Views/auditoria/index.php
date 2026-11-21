<?= $this->extend('plantilla') ?>

<?= $this->section('titulo') ?>Auditoría<?= $this->endSection() ?>

<?= $this->section('estilos') ?>
<link rel="stylesheet" href="<?= base_url('css/vendor/datatables-2.1.8.bootstrap5.min.css') ?>">
<?= $this->endSection() ?>

<?= $this->section('contenido') ?>
<h1 class="h4 mb-3">Auditoría</h1>

<div class="card shadow-sm">
    <div class="card-body">
        <table id="tabla-auditoria" class="table table-sm table-hover align-middle w-100">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Usuario</th>
                    <th>Tabla</th>
                    <th>Registro</th>
                    <th>Acción</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('js/vendor/datatables-2.1.8.min.js') ?>"></script>
<script src="<?= base_url('js/vendor/datatables-2.1.8.bootstrap5.min.js') ?>"></script>
<script src="<?= recurso('js/auditoria.js') ?>"></script>
<?= $this->endSection() ?>
