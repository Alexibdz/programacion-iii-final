<?php
// Formato de montos y fechas del ticket
$pesos = static fn ($valor) => '$ ' . number_format((float) $valor, 2, ',', '.');
$fecha = static fn ($fechaHora) => $fechaHora ? date('H:i d-m-Y', strtotime($fechaHora)) : '';
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Recibo <?= esc($visita['id']) ?></title>
    <link rel="stylesheet" href="<?= recurso('css/recibo.css') ?>">
</head>
<body>
    <div class="centrado"><strong>GESTIÓN DE ÓRDENES</strong></div>
    <div class="centrado">Recibo N° <?= esc($visita['id']) ?></div>
    <?php if ($reimpresion): ?>
        <div class="centrado"><strong>*** REIMPRESIÓN ***</strong></div>
    <?php endif ?>

    <div class="separador"></div>
    <div class="fila"><span>Mesa</span><span><?= esc($visita['mesa_numero']) ?></span></div>
    <div class="fila"><span>Apertura</span><span><?= esc($fecha($visita['creado_en'])) ?></span></div>
    <div class="fila"><span>Cierre</span><span><?= esc($fecha($visita['cerrada_en'])) ?></span></div>
    <div class="fila"><span>Atendió</span><span><?= esc($visita['usuario'] ?? '-') ?></span></div>
    <div class="separador"></div>

    <?php foreach ($lineas as $linea): ?>
        <div class="fila"><span><?= esc($linea['cantidad']) ?> × <?= esc($linea['nombre']) ?></span><span><?= esc($pesos($linea['subtotal'])) ?></span></div>
        <?php if ($linea['cantidad'] > 1): ?>
            <div class="detalle"><?= esc($pesos($linea['precio'])) ?> c/u</div>
        <?php endif ?>
    <?php endforeach ?>

    <div class="separador"></div>
    <div class="fila total"><span>TOTAL</span><span><?= esc($pesos($visita['total'])) ?></span></div>
    <div class="separador"></div>
    <div class="centrado">¡Gracias por su visita!</div>

    <div class="acciones no-imprimir">
        <button type="button" id="boton-imprimir"><?= $reimpresion ? 'Reimprimir' : 'Imprimir' ?></button>
        <a href="<?= base_url('mesas') ?>">Mapa de mesas</a>
        <a href="<?= base_url('historial') ?>">Historial</a>
    </div>
    <script src="<?= recurso('js/recibo.js') ?>"></script>
</body>
</html>
