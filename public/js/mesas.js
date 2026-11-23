// BLOQUE 100% DESARROLLADO CON IA (/docs/diseño-de-mesas)

// Mapa de mesas del mozo

$(function () {
    const svg = crearPlano(document.getElementById('plano'));
    const capa = svg.querySelector('.capa-mesas');
    let mesas = [];

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

    // Click o teclado sobre una mesa: ocupada va a su visita, libre a la mesa
    $(svg).on('click keydown', '.mesa', function (evento) {
        if (evento.type === 'keydown' && evento.key !== 'Enter' && evento.key !== ' ') {
            return;
        }

        evento.preventDefault();
        const id = Number(this.dataset.id);
        const mesa = mesas.find(item => Number(item.id) === id);

        window.location.href = URL_BASE + (mesa.visita_id !== null ? 'visitas/' + mesa.visita_id : 'mesas/' + mesa.id);
    });

    // Actualización periódica
    cargarMesas();
    setInterval(cargarMesas, 20000);
});

// FIN BLOQUE 100% DESARROLLADO CON IA (/docs/diseño-de-mesas)
