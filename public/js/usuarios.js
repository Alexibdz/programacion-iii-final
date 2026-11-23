// Pantalla de usuarios

$(function () {
    const modal = new bootstrap.Modal('#modal-usuario');
    let usuarios = [];

    // Nombres y colores de roles
    const roles = {
        principal: ['Principal', 'dark'],
        administrador: ['Administrador', 'primary'],
        mozo: ['Mozo', 'success'],
        cocina: ['Cocina', 'warning'],
    };

    function cargarUsuarios() {
        const bajas = $('#mostrar-bajas').is(':checked') ? 1 : 0;

        $.getJSON(URL_BASE + 'api/usuarios', { bajas }).done(function (datos) {
            usuarios = datos;
            dibujarTabla();
        });
    }

    function dibujarTabla() {
        const $cuerpo = $('#tabla-usuarios').empty();

        usuarios.forEach(function (usuario) {
            const dadoDeBaja = usuario.eliminado_en !== null;
            const [texto, color] = roles[usuario.rol];
            const $acciones = $('<td class="text-end text-nowrap">');

            if (dadoDeBaja) {
                $acciones.append(crearBotonAccion('Restaurar', 'btn-outline-success', 'restaurar', usuario.id));
            } else {
                $acciones.append(crearBotonAccion('Editar', 'btn-outline-secondary', 'editar', usuario.id));

                if (usuario.rol !== 'principal') {
                    $acciones.append(crearBotonAccion('Dar de baja', 'btn-outline-danger', 'baja', usuario.id));
                }
            }

            $cuerpo.append(
                $('<tr>').toggleClass('text-secondary', dadoDeBaja).append(
                    $('<td>').text(usuario.nombre),
                    $('<td>').text(usuario.usuario),
                    $('<td>').append($('<span class="badge text-bg-' + color + '">').text(texto)),
                    $acciones,
                ),
            );
        });
    }

    // Formulario de alta y edición
    function abrirFormulario(usuario = null) {
        const esPrincipal = usuario?.rol === 'principal';

        $('#usuario-id').val(usuario ? usuario.id : '');
        $('#usuario-nombre').val(usuario ? usuario.nombre : '');
        $('#usuario-usuario').val(usuario ? usuario.usuario : '');
        $('#usuario-password').val('').prop('required', !usuario);
        $('#aviso-password').toggleClass('d-none', !usuario);

        // El principal muestra su rol sin poder cambiarlo
        $('#usuario-rol option[value=principal]').remove();

        if (esPrincipal) {
            $('#usuario-rol').append('<option value="principal">Principal</option>');
        }

        $('#usuario-rol').val(usuario ? usuario.rol : 'mozo').prop('disabled', esPrincipal);
        $('#aviso-principal').toggleClass('d-none', !esPrincipal);
        $('#titulo-modal').text(usuario ? 'Editar usuario' : 'Nuevo usuario');
        modal.show();
    }

    $('#boton-nuevo').on('click', () => abrirFormulario());
    $('#mostrar-bajas').on('change', cargarUsuarios);

    // Botones de la tabla
    $('#tabla-usuarios').on('click', 'button', function () {
        const id = Number($(this).data('id'));
        const accion = $(this).data('accion');

        if (accion === 'editar') {
            abrirFormulario(usuarios.find(usuario => Number(usuario.id) === id));
        }

        if (accion === 'baja' && confirm('¿Dar de baja este usuario? No va a poder ingresar al sistema.')) {
            enviarJson('DELETE', 'api/usuarios/' + id).done(function () {
                mostrarAlerta('Usuario dado de baja.');
                cargarUsuarios();
            });
        }

        if (accion === 'restaurar') {
            enviarJson('POST', 'api/usuarios/' + id + '/restaurar').done(function () {
                mostrarAlerta('Usuario restaurado.');
                cargarUsuarios();
            });
        }
    });

    $('#formulario-usuario').on('submit', function (evento) {
        evento.preventDefault();

        const id = $('#usuario-id').val();
        const datos = {
            nombre: $('#usuario-nombre').val().trim(),
            usuario: $('#usuario-usuario').val().trim(),
            rol: $('#usuario-rol').val(),
            password: $('#usuario-password').val(),
        };

        const peticion = id
            ? enviarJson('PUT', 'api/usuarios/' + id, datos)
            : enviarJson('POST', 'api/usuarios', datos);

        peticion.done(function () {
            modal.hide();
            mostrarAlerta(id ? 'Usuario actualizado.' : 'Usuario creado.');
            cargarUsuarios();
        });
    });

    cargarUsuarios();
});
