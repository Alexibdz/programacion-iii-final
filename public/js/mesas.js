// BLOQUE 100% DESARROLLADO CON IA (/docs/diseño-de-mesas)

// Mapa de mesas del mozo

$(function () {
    const modal = new bootstrap.Modal('#modal-mesa');
    const svg = crearPlano(document.getElementById('plano'));
    const capa = svg.querySelector('.capa-mesas');
    let mesas = [];
    let mesaElegida = null;

    function cargarMesas() {
        $.getJSON(URL_BASE + 'api/mesas').done(function (datos) {
            mesas = datos;
            dibujarMesas();
        });
    }

    // Minutos desde la apertura
    function minutosAbierta(mesa) {
        const desde = new Date(mesa.visita_desde.replace(' ', 'T'));
        return Math.max(0, Math.round((Date.now() - desde) / 60000));
    }

    function dibujarMesas() {
        capa.replaceChildren();

        mesas.forEach(function (mesa) {
            const ocupada = mesa.visita_id !== null;

            capa.appendChild(crearMesaSvg(mesa, {
                clase: ocupada ? 'estado-ocupada' : 'estado-libre',
                detalle: ocupada ? minutosAbierta(mesa) + ' min' : mesa.capacidad + ' p.',
            }));
        });

        const ocupadas = mesas.filter(mesa => mesa.visita_id !== null).length;
        $('#resumen-libres').text((mesas.length - ocupadas) + ' libres');
        $('#resumen-ocupadas').text(ocupadas + ' ocupadas');
    }

    // Detalle de la mesa
    function mostrarMesa(mesa) {
        const ocupada = mesa.visita_id !== null;
        const datos = [
            ['Estado', ocupada ? 'Ocupada' : 'Libre'],
            ['Capacidad', mesa.capacidad + ' personas'],
            ['Forma', FORMAS_MESA[mesa.forma]],
        ];

        if (ocupada) {
            datos.push(['Abierta hace', minutosAbierta(mesa) + ' min']);
            datos.push(['Total', formatearPrecio(mesa.visita_total)]);
        }

        const $lista = $('#mesa-datos').empty();
        datos.forEach(function ([titulo, valor]) {
            $lista.append(
                $('<dt class="col-6 fw-normal text-secondary">').text(titulo),
                $('<dd class="col-6 mb-1">').text(valor),
            );
        });

        mesaElegida = mesa;
        $('#mesa-titulo').text('Mesa ' + mesa.numero);
        $('#boton-abrir').toggleClass('d-none', ocupada);
        $('#boton-visita').toggleClass('d-none', !ocupada).attr('href', URL_BASE + 'visitas/' + mesa.visita_id);
        modal.show();
    }

    // Click o teclado sobre una mesa
    $(svg).on('click keydown', '.mesa', function (evento) {
        if (evento.type === 'keydown' && evento.key !== 'Enter' && evento.key !== ' ') {
            return;
        }

        evento.preventDefault();
        const id = Number(this.dataset.id);
        mostrarMesa(mesas.find(mesa => Number(mesa.id) === id));
    });

    $('#boton-abrir').on('click', function () {
        enviarJson('POST', 'api/mesas/' + mesaElegida.id + '/abrir').done(function (respuesta) {
            window.location.href = URL_BASE + 'visitas/' + respuesta.visita_id;
        });
    });

    // Actualización periódica
    cargarMesas();
    setInterval(cargarMesas, 20000);
});

// FIN BLOQUE 100% DESARROLLADO CON IA (/docs/diseño-de-mesas)
