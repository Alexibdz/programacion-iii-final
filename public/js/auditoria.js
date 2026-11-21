// Pantalla de auditoría

$(function () {
    // Colores por acción
    const coloresAccion = {
        ALTA: 'success',
        MODIFICACION: 'primary',
        BAJA: 'danger',
        RESTAURACION: 'warning',
    };

    // Fecha como hora d-m-a, el orden sigue usando el valor original
    function formatearFechaHora(fechaHora, tipo) {
        if (tipo !== 'display' && tipo !== 'filter') {
            return fechaHora;
        }

        const [fecha, hora] = fechaHora.split(' ');
        const [anio, mes, dia] = fecha.split('-');

        return `${hora} ${dia}-${mes}-${anio}`;
    }

    $('#tabla-auditoria').DataTable({
        ajax: { url: URL_BASE + 'api/auditoria', dataSrc: '' },
        order: [[0, 'desc']],
        pageLength: 25,
        language: { url: URL_BASE + 'js/vendor/datatables-2.1.8.es-ES.json' },
        columns: [
            { data: 'registrado_en', render: formatearFechaHora },
            { data: 'usuario', defaultContent: 'Sin usuario', render: DataTable.render.text() },
            { data: 'tabla', render: DataTable.render.text() },
            { data: 'registro_id' },
            {
                data: 'accion',
                render: accion => `<span class="badge text-bg-${coloresAccion[accion] ?? 'secondary'}">${accion}</span>`,
            },
        ],
    });
});
