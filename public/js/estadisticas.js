// Pantalla de estadísticas

$(function () {
    const hoy = new Date().toLocaleDateString('sv-SE');

    // Fechas según el período elegido
    function periodoElegido() {
        const modo = $('input[name=modo]:checked').val();
        const desde = $('#fecha-desde').val() || hoy;

        if (modo === 'hoy') {
            return { desde: hoy, hasta: hoy };
        }

        if (modo === 'dia') {
            return { desde, hasta: desde };
        }

        return { desde, hasta: $('#fecha-hasta').val() || desde };
    }

    // Campos visibles según el período
    function mostrarCampos() {
        const modo = $('input[name=modo]:checked').val();

        $('#campo-desde').toggleClass('d-none', modo === 'hoy');
        $('#campo-hasta').toggleClass('d-none', modo !== 'rango');
        $('#etiqueta-desde').text(modo === 'rango' ? 'Desde' : 'Día');
    }

    function cargarResumen() {
        const periodo = periodoElegido();

        $.getJSON(URL_BASE + 'api/estadisticas', periodo).done(function (resumen) {
            $('#resumen-vendido').text(formatearPrecio(resumen.vendido));
            $('#resumen-cobradas').text(resumen.cobradas);
            $('#resumen-promedio').text(formatearPrecio(resumen.promedio));
        });
    }

    $('input[name=modo]').on('change', function () {
        mostrarCampos();
        cargarResumen();
    });

    $('#fecha-desde, #fecha-hasta').on('change', cargarResumen);

    $('#fecha-desde, #fecha-hasta').val(hoy).attr('max', hoy);
    mostrarCampos();
    cargarResumen();
});
