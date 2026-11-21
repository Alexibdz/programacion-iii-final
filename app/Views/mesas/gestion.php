<?php // BLOQUE 100% DESARROLLADO CON IA (/docs/diseño-de-mesas) ?>
<?= $this->extend('plantilla') ?>

<?= $this->section('titulo') ?>Gestión de mesas<?= $this->endSection() ?>

<?= $this->section('contenido') ?>
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <h1 class="h4 mb-0">Gestión de mesas</h1>
    <button type="button" class="btn btn-dark" id="boton-agregar">Agregar mesa</button>
</div>

<div class="row g-3">
    <div class="col-xl-9">
        <div class="card shadow-sm">
            <div class="card-body p-2">
                <div class="plano-contenedor" id="plano"></div>
            </div>
            <div class="card-footer bg-white small text-secondary">
                Arrastrá una mesa para moverla, se guarda sola al soltarla. Tocala para editarla.
            </div>
        </div>
    </div>

    <div class="col-xl-3">
        <div class="card shadow-sm mb-3">
            <div class="card-header bg-white fw-semibold" id="panel-titulo">Mesa seleccionada</div>
            <div class="card-body">
                <p class="text-secondary small mb-0" id="panel-vacio">Elegí una mesa del plano o agregá una nueva.</p>

                <form class="d-none" id="formulario-mesa">
                    <div class="mb-3">
                        <label for="mesa-numero" class="form-label">Número</label>
                        <input type="number" class="form-control" id="mesa-numero" min="1" max="999" required>
                    </div>
                    <div class="mb-3">
                        <label for="mesa-capacidad" class="form-label">Capacidad</label>
                        <input type="number" class="form-control" id="mesa-capacidad" min="1" max="12" required>
                    </div>
                    <div class="mb-3">
                        <label for="mesa-forma" class="form-label">Forma</label>
                        <select class="form-select" id="mesa-forma">
                            <option value="cuadrada">Cuadrada</option>
                            <option value="redonda">Redonda</option>
                            <option value="rectangular">Rectangular</option>
                        </select>
                    </div>
                    <p class="small text-danger d-none" id="aviso-ocupada">La mesa está ocupada, no se puede dar de baja.</p>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-dark flex-grow-1">Guardar</button>
                        <button type="button" class="btn btn-outline-danger" id="boton-baja">Dar de baja</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header bg-white fw-semibold">Dadas de baja</div>
            <ul class="list-group list-group-flush" id="lista-bajas"></ul>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= recurso('js/plano.js') ?>"></script>
<script src="<?= recurso('js/mesas_gestion.js') ?>"></script>
<?= $this->endSection() ?>
<?php // FIN BLOQUE 100% DESARROLLADO CON IA (/docs/diseño-de-mesas) ?>
