// Apertura de una visita desde la mesa libre

$(function () {
    const mesaId = $('#mesa').data('id');

    $('#boton-abrir-visita').on('click', function () {
        $(this).prop('disabled', true);

        enviarJson('POST', 'api/mesas/' + mesaId + '/abrir')
            .done(function (respuesta) {
                window.location.href = URL_BASE + 'visitas/' + respuesta.visita_id;
            })
            .fail(() => $(this).prop('disabled', false));
    });
});
