// Cambio de credenciales iniciales

$(function () {
    $('#formulario-credenciales').on('submit', function (evento) {
        evento.preventDefault();

        const datos = {
            usuario: $('#usuario').val().trim(),
            password: $('#password').val(),
            password_confirmacion: $('#password_confirmacion').val(),
        };

        if (datos.password !== datos.password_confirmacion) {
            mostrarAlerta('Las contraseñas no coinciden.', 'danger');
            return;
        }

        enviarJson('POST', 'cambiar-credenciales', datos).done(function (respuesta) {
            window.location.href = respuesta.redireccion;
        });
    });
});
