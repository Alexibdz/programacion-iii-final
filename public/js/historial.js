// Pantalla de historial de visitas

$(function () {
    // Fecha como hora d-m-a, el orden sigue usando el valor original
    function formatearFechaHora(fechaHora, tipo) {
        if (!fechaHora || (tipo !== 'display' && tipo !== 'filter')) {
            return fechaHora;
        }

        const [fecha, hora] = fechaHora.split(' ');
        const [anio, mes, dia] = fecha.split('-');

        return `${hora.substring(0, 5)} ${dia}-${mes}-${anio}`;
    }

    $('#tabla-historial').DataTable({
        ajax: { url: URL_BASE + 'api/historial', dataSrc: '' },
        order: [[0, 'desc']],
        pageLength: 25,
        language: { url: URL_BASE + 'js/vendor/datatables-2.1.8.es-ES.json' },
        columns: [
            { data: 'cerrada_en', render: formatearFechaHora },
            { data: 'id' },
            { data: 'mesa_numero' },
            { data: 'usuario', defaultContent: '-', render: DataTable.render.text() },
            {
                data: 'estado',
                render: estado => estado === 'cerrada'
                    ? '<span class="badge text-bg-secondary">Cobrada</span>'
                    : '<span class="badge text-bg-danger">Sin consumo</span>',
            },
            {
                data: 'total',
                className: 'text-end',
                render: (total, tipo) => (tipo === 'display' ? formatearPrecio(total) : Number(total)),
            },
            {
                data: null,
                orderable: false,
                className: 'text-end text-nowrap',
                render: function (visita) {
                    const detalle = `<a class="btn btn-sm btn-outline-secondary" href="${URL_BASE}visitas/${visita.id}?desde=historial">Detalle</a>`;
                    const recibo = `<a class="btn btn-sm btn-outline-dark ms-1" href="${URL_BASE}visitas/${visita.id}/recibo?reimpresion=1" target="_blank">Ver recibo</a>`;

                    return visita.estado === 'cerrada' ? detalle + recibo : detalle;
                },
            },
        ],
    });
});
