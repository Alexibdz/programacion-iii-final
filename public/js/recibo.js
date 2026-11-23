// Impresión del recibo

document.getElementById('boton-imprimir').addEventListener('click', () => window.print());

// Impresión automática al llegar desde el cierre de la visita
if (new URLSearchParams(location.search).get('imprimir') === '1') {
    window.addEventListener('load', () => window.print());
}
