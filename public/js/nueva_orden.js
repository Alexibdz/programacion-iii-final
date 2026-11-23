// Pantalla para armar una orden nueva

$(function () {
    const visitaId = $('#nueva-orden').data('visita');
    let productos = [];
    let pedido = new Map();

    function cargarProductos() {
        $.getJSON(URL_BASE + 'api/productos').done(function (datos) {
            productos = datos;

            if (productos.length === 0) {
                mostrarAlerta('No hay productos cargados en el menú.', 'danger');
            }

            dibujarCatalogo();
            dibujarPedido();
        });
    }

    // Texto sin tildes ni mayúsculas para comparar
    function normalizar(texto) {
        return (texto ?? '').normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase();
    }

    // Productos que coinciden con todas las palabras buscadas
    function productosFiltrados() {
        const palabras = normalizar($('#buscar-producto').val()).split(' ').filter(Boolean);

        return productos.filter(function (producto) {
            const texto = normalizar(producto.nombre + ' ' + producto.categoria);
            return palabras.every(palabra => texto.includes(palabra));
        });
    }

    // Grilla de tarjetas agrupadas por categoría
    function dibujarCatalogo() {
        const $catalogo = $('#catalogo').empty();
        let categoriaActual = null;
        let $grupo = null;

        productosFiltrados().forEach(function (producto) {
            if (producto.categoria !== categoriaActual) {
                categoriaActual = producto.categoria;
                $catalogo.append($('<div class="small fw-semibold text-secondary mt-3 mb-1">').text(categoriaActual));
                $grupo = $('<div class="row row-cols-2 row-cols-md-3 row-cols-xl-4 g-2">').appendTo($catalogo);
            }

            $grupo.append(
                $('<div class="col">').append(
                    $('<button type="button" class="tarjeta-producto btn btn-light border w-100 h-100 text-start">')
                        .attr('data-id', producto.id)
                        .append(
                            $('<span class="tarjeta-producto-nombre">').text(producto.nombre),
                            $('<span class="small text-secondary">').text(formatearPrecio(producto.precio)),
                            $('<span class="tarjeta-producto-cantidad badge rounded-pill text-bg-dark d-none">'),
                        ),
                ),
            );
        });

        if ($catalogo.children().length === 0) {
            $catalogo.append('<p class="text-secondary small mt-2">Sin resultados.</p>');
        }

        marcarCantidades();
    }

    // Cantidad ya pedida en cada tarjeta
    function marcarCantidades() {
        $('#catalogo .tarjeta-producto').each(function () {
            const cantidad = pedido.get(Number($(this).data('id'))) ?? 0;
            $(this).toggleClass('seleccionada', cantidad > 0);
            $(this).find('.tarjeta-producto-cantidad').text(cantidad).toggleClass('d-none', cantidad === 0);
        });
    }

    function agregarAlPedido(id) {
        pedido.set(id, Math.min((pedido.get(id) ?? 0) + 1, 99));
        dibujarPedido();
    }

    // Pedido en armado
    function dibujarPedido() {
        const $pedido = $('#pedido').empty();
        let total = 0;

        if (pedido.size === 0) {
            $pedido.append('<p class="text-secondary small mb-0">Elegí productos del menú.</p>');
        }

        pedido.forEach(function (cantidad, id) {
            const producto = productos.find(item => Number(item.id) === id);
            const subtotal = cantidad * Number(producto.precio);
            total += subtotal;

            $pedido.append(
                $('<div class="d-flex align-items-center gap-2 mb-2">').append(
                    $('<div class="flex-grow-1 small">').text(producto.nombre),
                    $('<div class="btn-group btn-group-sm">').append(
                        $('<button type="button" class="btn btn-outline-secondary">').attr({ 'data-id': id, 'data-cambio': -1 }).text('−'),
                        $('<span class="btn btn-outline-secondary disabled">').text(cantidad),
                        $('<button type="button" class="btn btn-outline-secondary">').attr({ 'data-id': id, 'data-cambio': 1 }).text('+'),
                    ),
                    $('<span class="small text-nowrap">').text(formatearPrecio(subtotal)),
                ),
            );
        });

        $('#pedido-total').text(formatearPrecio(total));
        marcarCantidades();
    }

    $('#catalogo').on('click', 'button', function () {
        agregarAlPedido(Number($(this).data('id')));
    });

    $('#pedido').on('click', 'button', function () {
        const id = Number($(this).data('id'));
        const cantidad = (pedido.get(id) ?? 0) + Number($(this).data('cambio'));

        if (cantidad <= 0) {
            pedido.delete(id);
        } else {
            pedido.set(id, Math.min(cantidad, 99));
        }

        dibujarPedido();
    });

    $('#buscar-producto').on('input', dibujarCatalogo);

    // Enter agrega el primer resultado y limpia la búsqueda
    $('#buscar-producto').on('keydown', function (evento) {
        if (evento.key !== 'Enter') {
            return;
        }

        evento.preventDefault();
        const primero = productosFiltrados()[0];

        if (primero && $(this).val().trim() !== '') {
            agregarAlPedido(Number(primero.id));
            $(this).val('');
            dibujarCatalogo();
        }
    });

    $('#formulario-orden').on('submit', function (evento) {
        evento.preventDefault();

        if (pedido.size === 0) {
            mostrarAlerta('Agregá al menos un producto.', 'danger');
            return;
        }

        const datos = {
            observaciones: $('#orden-observaciones').val().trim(),
            items: [...pedido].map(([producto_id, cantidad]) => ({ producto_id, cantidad })),
        };

        const $boton = $(this).find('button[type=submit]').prop('disabled', true);

        enviarJson('POST', 'api/visitas/' + visitaId + '/ordenes', datos)
            .done(function () {
                window.location.href = URL_BASE + 'visitas/' + visitaId + '?orden=enviada';
            })
            .fail(() => $boton.prop('disabled', false));
    });

    $('#buscar-producto').trigger('focus');
    cargarProductos();
});
