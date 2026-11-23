// Pantalla de visita de mesa

$(function () {
    const visitaId = $('#visita').data('id');
    let visita = null;

    // Textos y colores de estados
    const estadosOrden = {
        pendiente: ['Pendiente', 'secondary'],
        en_preparacion: ['En preparación', 'warning'],
        terminada: ['Terminada', 'success'],
        cancelada: ['Cancelada', 'danger'],
    };
    const estadosVisita = {
        abierta: ['Abierta', 'success'],
        cerrada: ['Cerrada', 'secondary'],
        cancelada: ['Cancelada', 'danger'],
    };

    function cargarVisita() {
        return $.getJSON(URL_BASE + 'api/visitas/' + visitaId).done(function (datos) {
            visita = datos;
            dibujarVisita();
        });
    }

    function dibujarVisita() {
        const [texto, color] = estadosVisita[visita.estado];
        const abierta = visita.estado === 'abierta';
        let horario = 'Abierta a las ' + formatearHora(visita.creado_en);

        if (visita.cerrada_en) {
            horario += ' · cerrada a las ' + formatearHora(visita.cerrada_en);
        }

        $('#visita-mesa').text(visita.mesa_numero);
        $('#visita-estado').attr('class', 'badge align-middle text-bg-' + color).text(texto);
        $('#visita-horario').text(horario);
        $('#visita-total').text(formatearPrecio(abierta ? visita.total_actual : visita.total));
        $('#boton-nueva-orden, #boton-cerrar-visita').toggleClass('d-none', !abierta);

        // Sin órdenes válidas el botón cancela la visita
        $('#boton-cerrar-visita')
            .text(tieneConsumo() ? 'Cerrar visita' : 'Cancelar visita')
            .toggleClass('btn-outline-success', tieneConsumo())
            .toggleClass('btn-outline-danger', !tieneConsumo());
        $('#boton-recibo').toggleClass('d-none', visita.estado !== 'cerrada').attr('href', URL_BASE + 'visitas/' + visitaId + '/recibo');

        dibujarOrdenes(abierta);
    }

    function dibujarOrdenes(abierta) {
        const $lista = $('#lista-ordenes').empty();

        if (visita.ordenes.length === 0) {
            $lista.append('<p class="text-secondary">Todavía no hay órdenes en esta visita.</p>');
            return;
        }

        visita.ordenes.forEach(function (orden) {
            const [texto, color] = estadosOrden[orden.estado];
            const $tarjeta = $('<div class="card shadow-sm mb-3">').toggleClass('opacity-50', orden.estado === 'cancelada');
            const $items = $('<ul class="list-group list-group-flush">');

            orden.items.forEach(function (item) {
                const $detalle = $('<div>').append($('<span>').text(item.cantidad + ' × ' + item.producto_nombre));

                if (item.observaciones) {
                    $detalle.append($('<div class="small text-secondary">').text(item.observaciones));
                }

                $items.append(
                    $('<li class="list-group-item d-flex justify-content-between gap-2">').append(
                        $detalle,
                        $('<span class="text-nowrap">').text(formatearPrecio(item.subtotal)),
                    ),
                );
            });

            $tarjeta.append(
                $('<div class="card-header d-flex flex-wrap align-items-center gap-2">').append(
                    $('<strong>').text('Orden ' + orden.id),
                    $('<span class="small text-secondary">').text(formatearHora(orden.creado_en)),
                    $('<span class="badge text-bg-' + color + '">').text(texto),
                    $('<span class="ms-auto fw-semibold">').text(formatearPrecio(orden.total)),
                ),
                $items,
            );

            if (orden.observaciones) {
                $tarjeta.append($('<div class="card-body py-2 small text-secondary">').text('Observaciones: ' + orden.observaciones));
            }

            // Cancelación solo antes de que cocina la empiece
            if (abierta && orden.estado === 'pendiente') {
                $tarjeta.append($('<div class="card-footer text-end">').append(crearBotonAccion('Cancelar orden', 'btn-outline-danger', 'cancelada', orden.id)));
            }

            $lista.append($tarjeta);
        });
    }

    // Cancelación de una orden mal cargada
    $('#lista-ordenes').on('click', 'button', function () {
        const id = $(this).data('id');

        if (!confirm('¿Cancelar esta orden?')) {
            return;
        }

        enviarJson('PUT', 'api/ordenes/' + id + '/estado', { estado: 'cancelada' }).done(function () {
            mostrarAlerta('Orden cancelada.');
            cargarVisita();
        });
    });

    // Hay al menos una orden no cancelada
    function tieneConsumo() {
        return visita.ordenes.some(orden => orden.estado !== 'cancelada');
    }

    $('#boton-cerrar-visita').on('click', function () {
        const pregunta = tieneConsumo()
            ? '¿Cerrar la visita? Se genera el recibo y la mesa queda libre.'
            : '¿Cancelar la visita? No hay consumos y la mesa queda libre.';

        if (!confirm(pregunta)) {
            return;
        }

        enviarJson('POST', 'api/visitas/' + visitaId + '/cerrar').done(function (respuesta) {
            // Con consumo va directo al recibo
            if (respuesta.estado === 'cerrada') {
                window.location.href = URL_BASE + 'visitas/' + visitaId + '/recibo?imprimir=1';
                return;
            }

            mostrarAlerta('Visita cancelada.', 'info');
            cargarVisita();
        });
    });

    // Actualización automática mientras la mesa está abierta
    setInterval(function () {
        if (visita?.estado === 'abierta') {
            cargarVisita();
        }
    }, 10000);

    // Aviso al volver de cargar una orden
    if (new URLSearchParams(location.search).get('orden') === 'enviada') {
        mostrarAlerta('Orden enviada.');
        history.replaceState(null, '', location.pathname);
    }

    cargarVisita();
});
