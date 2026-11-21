// BLOQUE 100% DESARROLLADO CON IA (/docs/diseño-de-mesas)

// Editor del plano de mesas

$(function () {
    const svg = crearPlano(document.getElementById('plano'));
    const capa = svg.querySelector('.capa-mesas');
    let mesas = [];
    let seleccionadaId = null;
    let arrastre = null;

    svg.classList.add('plano-editable');

    const activas = () => mesas.filter(mesa => mesa.eliminado_en === null);
    const buscarMesa = id => mesas.find(mesa => Number(mesa.id) === Number(id));

    function cargarMesas(seleccionar = seleccionadaId) {
        return $.getJSON(URL_BASE + 'api/mesas', { bajas: 1 }).done(function (datos) {
            mesas = datos.map(mesa => ({ ...mesa, pos_x: Number(mesa.pos_x), pos_y: Number(mesa.pos_y) }));
            dibujarBajas();
            seleccionarMesa(buscarMesa(seleccionar)?.eliminado_en === null ? seleccionar : null);
        });
    }

    // Mesa dibujada en el plano
    function crearMesaEditable(mesa) {
        const clases = [];

        if (Number(mesa.id) === Number(seleccionadaId)) {
            clases.push('seleccionada');
        }

        if (mesa.visita_id !== null) {
            clases.push('con-visita');
        }

        return crearMesaSvg(mesa, {
            clase: clases.join(' '),
            detalle: mesa.visita_id !== null ? 'ocupada' : mesa.capacidad + ' p.',
        });
    }

    function dibujarPlano() {
        capa.replaceChildren(...activas().map(crearMesaEditable));
    }

    function reemplazarMesa(mesa) {
        capa.querySelector(`.mesa[data-id="${mesa.id}"]`)?.replaceWith(crearMesaEditable(mesa));
    }

    function dibujarBajas() {
        const $lista = $('#lista-bajas').empty();
        const bajas = mesas.filter(mesa => mesa.eliminado_en !== null);

        if (bajas.length === 0) {
            $lista.append('<li class="list-group-item small text-secondary">No hay mesas dadas de baja.</li>');
            return;
        }

        bajas.forEach(function (mesa) {
            $lista.append(
                $('<li class="list-group-item d-flex justify-content-between align-items-center">').append(
                    $('<span>').text('Mesa ' + mesa.numero + ' · ' + mesa.capacidad + ' p.'),
                    crearBotonAccion('Restaurar', 'btn-outline-success', 'restaurar', mesa.id),
                ),
            );
        });
    }

    // Selección y panel lateral
    function seleccionarMesa(id) {
        const mesa = buscarMesa(id);
        seleccionadaId = mesa ? mesa.id : null;
        dibujarPlano();

        $('#panel-vacio').toggleClass('d-none', mesa !== undefined);
        $('#formulario-mesa').toggleClass('d-none', mesa === undefined);
        $('#panel-titulo').text(mesa ? 'Mesa ' + mesa.numero : 'Mesa seleccionada');

        if (mesa) {
            $('#mesa-numero').val(mesa.numero);
            $('#mesa-capacidad').val(mesa.capacidad);
            $('#mesa-forma').val(mesa.forma);
            $('#aviso-ocupada').toggleClass('d-none', mesa.visita_id === null);
            $('#boton-baja').prop('disabled', mesa.visita_id !== null);
        }
    }

    // Valores del formulario aplicados a la mesa
    function mesaDelFormulario() {
        const mesa = buscarMesa(seleccionadaId);
        const capacidad = Math.min(12, Math.max(1, Number($('#mesa-capacidad').val()) || 1));
        const temporal = {
            ...mesa,
            numero: $('#mesa-numero').val() || mesa.numero,
            capacidad,
            forma: $('#mesa-forma').val(),
        };
        const posicion = limitarPosicion(temporal, mesa.pos_x, mesa.pos_y);

        return { ...temporal, pos_x: posicion.x, pos_y: posicion.y };
    }

    $('#mesa-numero, #mesa-capacidad, #mesa-forma').on('input change', function () {
        if (seleccionadaId !== null) {
            reemplazarMesa(mesaDelFormulario());
        }
    });

    $('#formulario-mesa').on('submit', function (evento) {
        evento.preventDefault();

        const mesa = mesaDelFormulario();
        const datos = {
            numero: Number($('#mesa-numero').val()),
            capacidad: mesa.capacidad,
            forma: mesa.forma,
            pos_x: mesa.pos_x,
            pos_y: mesa.pos_y,
        };

        enviarJson('PUT', 'api/mesas/' + mesa.id, datos).done(function () {
            mostrarAlerta('Mesa actualizada.');
            cargarMesas();
        });
    });

    $('#boton-baja').on('click', function () {
        const mesa = buscarMesa(seleccionadaId);

        if (!confirm('¿Dar de baja la mesa ' + mesa.numero + '?')) {
            return;
        }

        enviarJson('DELETE', 'api/mesas/' + mesa.id).done(function () {
            mostrarAlerta('Mesa dada de baja.');
            cargarMesas(null);
        });
    });

    $('#lista-bajas').on('click', 'button', function () {
        const id = $(this).data('id');

        enviarJson('POST', 'api/mesas/' + id + '/restaurar').done(function () {
            mostrarAlerta('Mesa restaurada.');
            cargarMesas(id);
        });
    });

    // Primer lugar libre del salón
    function posicionLibre(nueva) {
        for (let y = 90; y <= 560; y += 40) {
            for (let x = 90; x <= 720; x += 40) {
                const libre = activas().every(mesa => Math.hypot(mesa.pos_x - x, mesa.pos_y - y) > 130);
                const enEntrada = x < 200 && y > 500;

                if (libre && !enEntrada) {
                    return limitarPosicion(nueva, x, y);
                }
            }
        }

        return limitarPosicion(nueva, 400, 320);
    }

    $('#boton-agregar').on('click', function () {
        const numero = mesas.reduce((maximo, mesa) => Math.max(maximo, Number(mesa.numero)), 0) + 1;
        const nueva = { numero, capacidad: 4, forma: 'cuadrada' };
        const posicion = posicionLibre(nueva);

        enviarJson('POST', 'api/mesas', { ...nueva, pos_x: posicion.x, pos_y: posicion.y }).done(function (creada) {
            mostrarAlerta('Mesa ' + numero + ' agregada, ubicala en el plano.');
            cargarMesas(creada.id);
        });
    });

    // Posición dentro del plano
    function puntoEnPlano(evento) {
        const punto = svg.createSVGPoint();
        punto.x = evento.clientX;
        punto.y = evento.clientY;
        return punto.matrixTransform(svg.getScreenCTM().inverse());
    }

    function guardarPosicion(mesa) {
        enviarJson('PUT', 'api/mesas/' + mesa.id, { pos_x: mesa.pos_x, pos_y: mesa.pos_y })
            .fail(() => cargarMesas());
    }

    // Arrastre de mesas
    svg.addEventListener('pointerdown', function (evento) {
        const grupo = evento.target.closest('.mesa');

        if (!grupo) {
            seleccionarMesa(null);
            return;
        }

        const mesa = buscarMesa(grupo.dataset.id);
        const punto = puntoEnPlano(evento);

        arrastre = {
            mesa,
            grupo,
            inicio: punto,
            desfaseX: punto.x - mesa.pos_x,
            desfaseY: punto.y - mesa.pos_y,
            x: mesa.pos_x,
            y: mesa.pos_y,
            movida: false,
        };

        svg.setPointerCapture(evento.pointerId);
    });

    svg.addEventListener('pointermove', function (evento) {
        if (!arrastre) {
            return;
        }

        const punto = puntoEnPlano(evento);

        if (!arrastre.movida && Math.hypot(punto.x - arrastre.inicio.x, punto.y - arrastre.inicio.y) < 4) {
            return;
        }

        const posicion = limitarPosicion(
            arrastre.mesa,
            Math.round((punto.x - arrastre.desfaseX) / 5) * 5,
            Math.round((punto.y - arrastre.desfaseY) / 5) * 5,
        );

        arrastre.movida = true;
        arrastre.x = posicion.x;
        arrastre.y = posicion.y;
        arrastre.grupo.classList.add('arrastrando');
        arrastre.grupo.setAttribute('transform', `translate(${posicion.x} ${posicion.y})`);
    });

    svg.addEventListener('pointerup', function () {
        if (!arrastre) {
            return;
        }

        const { mesa, movida, x, y } = arrastre;
        arrastre = null;

        if (movida) {
            mesa.pos_x = x;
            mesa.pos_y = y;
            guardarPosicion(mesa);
        }

        seleccionarMesa(mesa.id);
    });

    svg.addEventListener('pointercancel', function () {
        arrastre = null;
        dibujarPlano();
    });

    // Movimiento con teclado
    let esperaTeclado = null;

    svg.addEventListener('keydown', function (evento) {
        const grupo = evento.target.closest('.mesa');
        const flechas = { ArrowUp: [0, -10], ArrowDown: [0, 10], ArrowLeft: [-10, 0], ArrowRight: [10, 0] };

        if (!grupo) {
            return;
        }

        const mesa = buscarMesa(grupo.dataset.id);

        if (evento.key === 'Enter' || evento.key === ' ') {
            evento.preventDefault();
            seleccionarMesa(mesa.id);
            svg.querySelector(`.mesa[data-id="${mesa.id}"]`).focus();
            return;
        }

        if (!flechas[evento.key]) {
            return;
        }

        evento.preventDefault();
        const [dx, dy] = flechas[evento.key];
        const posicion = limitarPosicion(mesa, mesa.pos_x + dx, mesa.pos_y + dy);
        mesa.pos_x = posicion.x;
        mesa.pos_y = posicion.y;
        grupo.setAttribute('transform', `translate(${posicion.x} ${posicion.y})`);

        clearTimeout(esperaTeclado);
        esperaTeclado = setTimeout(() => guardarPosicion(mesa), 400);
    });

    cargarMesas();
});

// FIN BLOQUE 100% DESARROLLADO CON IA (/docs/diseño-de-mesas)
