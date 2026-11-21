// Funciones comunes

// Aviso bootstrap
function mostrarAlerta(mensaje, tipo = 'success') {
    const $alerta = $(`
        <div class="alert alert-${tipo} alert-dismissible fade show shadow-sm" role="alert">
            <span></span>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `);

    // Texto sin HTML
    $alerta.find('span').text(mensaje);
    $('#contenedor-alertas').append($alerta);

    setTimeout(() => $alerta.alert('close'), 4000);
}

// Petición JSON a la API
function enviarJson(metodo, ruta, datos = null) {
    return $.ajax({
        url: URL_BASE + ruta,
        method: metodo,
        contentType: 'application/json',
        data: datos ? JSON.stringify(datos) : undefined,
        dataType: 'json',
    });
}

// Botón de acción de tabla
function crearBotonAccion(texto, clase, accion, id) {
    return $('<button type="button" class="btn btn-sm ms-1">')
        .addClass(clase)
        .text(texto)
        .attr('data-accion', accion)
        .attr('data-id', id);
}

// Formato de precio
const formatoPesos = new Intl.NumberFormat('es-AR', { style: 'currency', currency: 'ARS' });

function formatearPrecio(valor) {
    return formatoPesos.format(Number(valor));
}

// Manejo de errores AJAX
$(document).ajaxError(function (evento, respuesta) {
    const mensajes = respuesta.responseJSON?.messages;

    const mensaje = mensajes
        ? Object.values(mensajes).join(' ')
        : respuesta.responseJSON?.message ?? 'No se pudo conectar con el servidor.';

    mostrarAlerta(mensaje, 'danger');
});
