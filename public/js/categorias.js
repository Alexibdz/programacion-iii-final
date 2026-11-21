// Pantalla de categorías

$(function () {
    const modal = new bootstrap.Modal('#modal-categoria');
    let categorias = [];

    function cargarCategorias() {
        const bajas = $('#mostrar-bajas').is(':checked') ? 1 : 0;

        $.getJSON(URL_BASE + 'api/categorias', { bajas }).done(function (datos) {
            categorias = datos;
            dibujarTabla();
        });
    }

    function dibujarTabla() {
        const $cuerpo = $('#tabla-categorias').empty();

        if (categorias.length === 0) {
            $cuerpo.append('<tr><td colspan="3" class="text-center text-secondary py-4">No hay categorías cargadas.</td></tr>');
            return;
        }

        categorias.forEach(function (categoria) {
            const dadaDeBaja = categoria.eliminado_en !== null;
            const $fila = $('<tr>').toggleClass('text-secondary', dadaDeBaja);
            const $acciones = $('<td class="text-end">');

            if (dadaDeBaja) {
                $acciones.append(crearBotonAccion('Restaurar', 'btn-outline-success', 'restaurar', categoria.id));
            } else {
                $acciones.append(
                    crearBotonAccion('Editar', 'btn-outline-secondary', 'editar', categoria.id),
                    crearBotonAccion('Dar de baja', 'btn-outline-danger', 'baja', categoria.id),
                );
            }

            $fila.append(
                $('<td>').text(categoria.nombre),
                $('<td>').text(categoria.requiere_cocina === '1' ? 'Sí' : 'No'),
                $acciones,
            );
            $cuerpo.append($fila);
        });
    }

    // Formulario de alta y edición
    function abrirFormulario(categoria = null) {
        $('#categoria-id').val(categoria ? categoria.id : '');
        $('#categoria-nombre').val(categoria ? categoria.nombre : '');
        $('#categoria-cocina').prop('checked', categoria ? categoria.requiere_cocina === '1' : true);
        $('#titulo-modal').text(categoria ? 'Editar categoría' : 'Nueva categoría');
        modal.show();
    }

    $('#boton-nueva').on('click', () => abrirFormulario());
    $('#mostrar-bajas').on('change', cargarCategorias);

    // Botones de la tabla
    $('#tabla-categorias').on('click', 'button', function () {
        const id = Number($(this).data('id'));
        const accion = $(this).data('accion');

        if (accion === 'editar') {
            abrirFormulario(categorias.find(categoria => Number(categoria.id) === id));
        }

        if (accion === 'baja' && confirm('¿Dar de baja esta categoría?')) {
            enviarJson('DELETE', 'api/categorias/' + id).done(function () {
                mostrarAlerta('Categoría dada de baja.');
                cargarCategorias();
            });
        }

        if (accion === 'restaurar') {
            enviarJson('POST', 'api/categorias/' + id + '/restaurar').done(function () {
                mostrarAlerta('Categoría restaurada.');
                cargarCategorias();
            });
        }
    });

    $('#formulario-categoria').on('submit', function (evento) {
        evento.preventDefault();

        const id = $('#categoria-id').val();
        const datos = {
            nombre: $('#categoria-nombre').val().trim(),
            requiere_cocina: $('#categoria-cocina').is(':checked') ? 1 : 0,
        };

        const peticion = id
            ? enviarJson('PUT', 'api/categorias/' + id, datos)
            : enviarJson('POST', 'api/categorias', datos);

        peticion.done(function () {
            modal.hide();
            mostrarAlerta(id ? 'Categoría actualizada.' : 'Categoría creada.');
            cargarCategorias();
        });
    });

    cargarCategorias();
});
