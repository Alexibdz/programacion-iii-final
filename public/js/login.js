// Pantalla de ingreso

$(function () {
    $('#formulario-login').on('submit', function (evento) {
        evento.preventDefault();

        const datos = {
            usuario: $('#usuario').val().trim(),
            password: $('#password').val(),
        };

        enviarJson('POST', 'login', datos).done(function (respuesta) {
            window.location.href = respuesta.redireccion;
        });
    });
});
