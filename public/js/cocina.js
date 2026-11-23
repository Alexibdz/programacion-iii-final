// Pantalla de cocina

$(function () {
    // Botones según el estado de la orden
    const acciones = {
        pendiente: [['Empezar', 'btn-warning', 'en_preparacion']],
        en_preparacion: [
            ['Volver a pendiente', 'btn-outline-secondary', 'pendiente'],
            ['Terminar', 'btn-success', 'terminada'],
        ],
    };

    const estados = {
        pendiente: ['Pendiente', 'secondary'],
        en_preparacion: ['En preparación', 'warning'],
    };

    function cargarCola() {
        $.getJSON(URL_BASE + 'api/cocina').done(function (ordenes) {
            dibujarCola(ordenes);
            $('#cocina-actualizado').text('Actualizado a las ' + new Date().toLocaleTimeString('es-AR'));
        });
    }

    // Minutos de espera desde que entró la orden
    function minutosDeEspera(fechaHora) {
        return Math.max(0, Math.floor((Date.now() - new Date(fechaHora.replace(' ', 'T'))) / 60000));
    }

    function dibujarCola(ordenes) {
        const $cola = $('#cola-cocina').empty();

        if (ordenes.length === 0) {
            $cola.append('<div class="col-12"><p class="text-secondary">No hay órdenes en cola.</p></div>');
            return;
        }

        // Agrupación por mesa respetando el orden de llegada
        const mesas = new Map();

        ordenes.forEach(function (orden) {
            if (!mesas.has(orden.mesa_numero)) {
                mesas.set(orden.mesa_numero, []);
            }

            mesas.get(orden.mesa_numero).push(orden);
        });

        mesas.forEach(function (ordenesDeMesa, numero) {
            const $cuerpo = $('<div class="list-group list-group-flush">');

            ordenesDeMesa.forEach(orden => $cuerpo.append(dibujarOrden(orden)));

            $cola.append(
                $('<div class="col">').append(
                    $('<div class="card shadow-sm h-100">').append(
                        $('<div class="card-header d-flex justify-content-between align-items-center">').append(
                            $('<span class="fs-5 fw-semibold">').text('Mesa ' + numero),
                            $('<span class="small text-secondary">').text(ordenesDeMesa.length + (ordenesDeMesa.length === 1 ? ' orden' : ' órdenes')),
                        ),
                        $cuerpo,
                    ),
                ),
            );
        });
    }

    function dibujarOrden(orden) {
        const [texto, color] = estados[orden.estado];
        const espera = minutosDeEspera(orden.creado_en);
        const $items = $('<ul class="list-unstyled mb-2">');

        orden.items.forEach(function (item) {
            const $item = $('<li>').append($('<strong>').text(item.cantidad + ' × '), document.createTextNode(item.producto_nombre));

            if (item.observaciones) {
                $item.append($('<div class="small text-secondary ms-3">').text(item.observaciones));
            }

            $items.append($item);
        });

        const $orden = $('<div class="list-group-item py-3">').append(
            $('<div class="d-flex flex-wrap align-items-center gap-2 mb-2">').append(
                $('<strong>').text('Orden ' + orden.id),
                $('<span class="small text-secondary">').text(formatearHora(orden.creado_en) + ' · hace ' + espera + ' min'),
                $('<span class="badge text-bg-' + color + ' ms-auto">').text(texto),
            ),
            $items,
        );

        if (orden.observaciones) {
            $orden.append($('<div class="alert alert-light border py-1 px-2 small mb-2">').text('Observaciones: ' + orden.observaciones));
        }

        const $botones = $('<div class="d-flex justify-content-end gap-1">');

        acciones[orden.estado].forEach(function ([etiqueta, clase, estado]) {
            $botones.append(crearBotonAccion(etiqueta, clase, estado, orden.id));
        });

        return $orden.append($botones);
    }

    // Cambio de estado desde cocina
    $('#cola-cocina').on('click', 'button', function () {
        const id = $(this).data('id');
        const estado = $(this).data('accion');

        $(this).prop('disabled', true);

        enviarJson('PUT', 'api/ordenes/' + id + '/estado', { estado }).always(cargarCola);
    });

    // Actualización automática
    setInterval(cargarCola, 5000);
    cargarCola();
});
