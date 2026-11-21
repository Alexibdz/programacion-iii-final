<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Recibo <?= esc($visitaId) ?></title>
    <style>
        /* Ticket para impresora de 80 mm */
        @page { size: 80mm auto; margin: 0; }
        body { width: 72mm; margin: 0 auto; padding: 4mm 0; font: 12px monospace; }
        .centrado { text-align: center; }
        .separador { border-top: 1px dashed #000; margin: 6px 0; }
        @media print { .no-imprimir { display: none; } }
    </style>
</head>
<body>
    <div class="centrado"><strong>GESTIÓN DE ÓRDENES</strong></div>
    <div class="centrado">Recibo N° <?= esc($visitaId) ?></div>
    <div class="separador"></div>
    <button class="no-imprimir" onclick="window.print()">Imprimir</button>
</body>
</html>
