// Funciones comunes a todas las pantallas del sistema

/**
 * Muestra un aviso de Bootstrap en la esquina de la pantalla.
 * tipo: success, danger, warning o info
 */
function mostrarAlerta(mensaje, tipo = 'success') {
    const $alerta = $(`
        <div class="alert alert-${tipo} alert-dismissible fade show shadow-sm" role="alert">
            <span></span>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `);

    // text evita que el mensaje se interprete como HTML
    $alerta.find('span').text(mensaje);
    $('#contenedor-alertas').append($alerta);

    setTimeout(() => $alerta.alert('close'), 4000);
}

/**
 * Manejo centralizado de errores AJAX:
 * cualquier petición que falle muestra el mensaje que devolvió el servidor.
 */
$(document).ajaxError(function (evento, respuesta) {
    const mensaje = respuesta.responseJSON?.messages?.error
        ?? respuesta.responseJSON?.message
        ?? 'No se pudo conectar con el servidor.';

    mostrarAlerta(mensaje, 'danger');
});
