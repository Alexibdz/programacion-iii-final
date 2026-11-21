// Pantalla de productos

$(function () {
    const modal = new bootstrap.Modal('#modal-producto');
    let productos = [];
    let hayCategorias = false;

    // Carga de categorías
    function cargarCategorias() {
        return $.getJSON(URL_BASE + 'api/categorias').done(function (categorias) {
            const $filtro = $('#filtro-categoria');
            const $formulario = $('#producto-categoria').empty();

            hayCategorias = categorias.length > 0;

            if (!hayCategorias) {
                mostrarAlerta('No existen categorías activas.', 'danger');
            }

            categorias.forEach(function (categoria) {
                $filtro.append($('<option>').val(categoria.id).text(categoria.nombre));
                $formulario.append($('<option>').val(categoria.id).text(categoria.nombre));
            });
        });
    }

    function cargarProductos() {
        const parametros = {
            categoria: $('#filtro-categoria').val(),
            bajas: $('#mostrar-bajas').is(':checked') ? 1 : 0,
        };

        $.getJSON(URL_BASE + 'api/productos', parametros).done(function (datos) {
            productos = datos;
            dibujarTabla();
        });
    }

    function dibujarTabla() {
        const $cuerpo = $('#tabla-productos').empty();

        if (productos.length === 0) {
            $cuerpo.append('<tr><td colspan="5" class="text-center text-secondary py-4">No hay productos para mostrar.</td></tr>');
            return;
        }

        productos.forEach(function (producto) {
            const dadoDeBaja = producto.eliminado_en !== null;
            const $fila = $('<tr>').toggleClass('text-secondary', dadoDeBaja);
            const $acciones = $('<td class="text-end text-nowrap">');

            if (dadoDeBaja) {
                $acciones.append(crearBotonAccion('Restaurar', 'btn-outline-success', 'restaurar', producto.id));
            } else {
                $acciones.append(
                    crearBotonAccion('Editar', 'btn-outline-secondary', 'editar', producto.id),
                    crearBotonAccion('Dar de baja', 'btn-outline-danger', 'baja', producto.id),
                );
            }

            $fila.append(
                $('<td>').text(producto.nombre),
                $('<td>').text(producto.categoria),
                $('<td class="text-secondary">').text(producto.descripcion ?? ''),
                $('<td class="text-end">').text(formatearPrecio(producto.precio)),
                $acciones,
            );
            $cuerpo.append($fila);
        });
    }

    // Formulario de alta y edición
    function abrirFormulario(producto = null) {
        if (!hayCategorias) {
            mostrarAlerta('No existen categorías activas.', 'danger');
            return;
        }

        $('#producto-id').val(producto ? producto.id : '');
        $('#producto-categoria').val(producto ? producto.categoria_id : $('#filtro-categoria').val() || $('#producto-categoria option:first').val());
        $('#producto-nombre').val(producto ? producto.nombre : '');
        $('#producto-descripcion').val(producto ? producto.descripcion : '');
        $('#producto-precio').val(producto ? producto.precio : '');
        $('#titulo-modal').text(producto ? 'Editar producto' : 'Nuevo producto');
        modal.show();
    }

    $('#boton-nuevo').on('click', () => abrirFormulario());
    $('#filtro-categoria, #mostrar-bajas').on('change', cargarProductos);

    // Botones de la tabla
    $('#tabla-productos').on('click', 'button', function () {
        const id = Number($(this).data('id'));
        const accion = $(this).data('accion');

        if (accion === 'editar') {
            abrirFormulario(productos.find(producto => Number(producto.id) === id));
        }

        if (accion === 'baja' && confirm('¿Dar de baja este producto?')) {
            enviarJson('DELETE', 'api/productos/' + id).done(function () {
                mostrarAlerta('Producto dado de baja.');
                cargarProductos();
            });
        }

        if (accion === 'restaurar') {
            enviarJson('POST', 'api/productos/' + id + '/restaurar').done(function () {
                mostrarAlerta('Producto restaurado.');
                cargarProductos();
            });
        }
    });

    $('#formulario-producto').on('submit', function (evento) {
        evento.preventDefault();

        const id = $('#producto-id').val();
        const datos = {
            categoria_id: Number($('#producto-categoria').val()),
            nombre: $('#producto-nombre').val().trim(),
            descripcion: $('#producto-descripcion').val().trim(),
            precio: Number($('#producto-precio').val()),
        };

        const peticion = id
            ? enviarJson('PUT', 'api/productos/' + id, datos)
            : enviarJson('POST', 'api/productos', datos);

        peticion.done(function () {
            modal.hide();
            mostrarAlerta(id ? 'Producto actualizado.' : 'Producto creado.');
            cargarProductos();
        });
    });

    cargarCategorias().done(cargarProductos);
});
