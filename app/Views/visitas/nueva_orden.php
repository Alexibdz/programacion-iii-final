<?= $this->extend('plantilla') ?>

<?= $this->section('titulo') ?>Nueva orden · Mesa <?= esc($visita['mesa_numero']) ?><?= $this->endSection() ?>

<?= $this->section('contenido') ?>
<div id="nueva-orden" data-visita="<?= esc($visita['id']) ?>">
    <a href="<?= base_url('visitas/' . $visita['id']) ?>" class="btn btn-outline-dark btn-lg mb-3">← Volver a la mesa</a>

    <h1 class="h4 mb-3">Nueva orden · Mesa <?= esc($visita['mesa_numero']) ?></h1>

    <div class="row g-4">
        <div class="col-lg-8">
            <input type="search" class="form-control form-control-lg mb-2" id="buscar-producto" placeholder="Buscar producto · Enter agrega el primero" autocomplete="off">
            <div id="catalogo"></div>
        </div>

        <div class="col-lg-4">
            <form class="card shadow-sm panel-pedido" id="formulario-orden">
                <div class="card-body">
                    <h2 class="h6">Pedido</h2>
                    <div id="pedido" class="mb-3"></div>
                    <label for="orden-observaciones" class="form-label small">Observaciones</label>
                    <textarea class="form-control" id="orden-observaciones" rows="2" maxlength="255"></textarea>
                </div>
                <div class="card-footer bg-white">
                    <div class="d-flex justify-content-between fw-semibold fs-5 mb-2">
                        <span>Total</span>
                        <span id="pedido-total"></span>
                    </div>
                    <button type="submit" class="btn btn-dark btn-lg w-100">Confirmar orden</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= recurso('js/nueva_orden.js') ?>"></script>
<?= $this->endSection() ?>
