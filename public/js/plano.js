// BLOQUE 100% DESARROLLADO CON IA (/docs/diseño-de-mesas)

// Plano 2D del salón

const PLANO = {
    ancho: 1000,
    alto: 640,
    salon: { x1: 25, y1: 25, x2: 765, y2: 615 },
};

const NS_SVG = 'http://www.w3.org/2000/svg';

// Elemento SVG
function crearSvg(etiqueta, atributos = {}, padre = null) {
    const elemento = document.createElementNS(NS_SVG, etiqueta);

    Object.entries(atributos).forEach(([clave, valor]) => elemento.setAttribute(clave, valor));

    if (padre) {
        padre.appendChild(elemento);
    }

    return elemento;
}

// Texto SVG sin HTML
function crearTextoSvg(texto, atributos, padre) {
    const elemento = crearSvg('text', atributos, padre);
    elemento.textContent = texto;
    return elemento;
}

// Plano vacío con ambientes
function crearPlano(contenedor) {
    const svg = crearSvg('svg', {
        viewBox: `0 0 ${PLANO.ancho} ${PLANO.alto}`,
        class: 'plano',
        role: 'img',
        'aria-label': 'Plano del salón',
    });

    dibujarAmbientes(svg);
    crearSvg('g', { class: 'capa-mesas' }, svg);
    contenedor.replaceChildren(svg);

    return svg;
}

// Pisos, paredes y mobiliario fijo
function dibujarAmbientes(svg) {
    const defs = crearSvg('defs', {}, svg);

    const madera = crearSvg('pattern', { id: 'piso-madera', width: 120, height: 24, patternUnits: 'userSpaceOnUse' }, defs);
    crearSvg('rect', { width: 120, height: 24, fill: '#f6efe4' }, madera);
    crearSvg('path', { d: 'M0 24 H120 M60 0 V12 M0 12 H120 M20 12 V24 M100 12 V24', stroke: '#ebdfcc', 'stroke-width': 1 }, madera);

    const baldosa = crearSvg('pattern', { id: 'piso-baldosa', width: 20, height: 20, patternUnits: 'userSpaceOnUse' }, defs);
    crearSvg('rect', { width: 20, height: 20, fill: '#eef1f4' }, baldosa);
    crearSvg('path', { d: 'M20 0 V20 H0', stroke: '#d9dee3', 'stroke-width': 1, fill: 'none' }, baldosa);

    const fondo = crearSvg('g', { class: 'plano-fondo' }, svg);

    // Pisos
    crearSvg('rect', { x: 15, y: 15, width: 970, height: 610, fill: 'url(#piso-madera)' }, fondo);
    crearSvg('rect', { x: 785, y: 15, width: 200, height: 185, fill: 'url(#piso-baldosa)' }, fondo);
    crearSvg('rect', { x: 836, y: 200, width: 149, height: 320, fill: '#ece5da' }, fondo);
    crearSvg('rect', { x: 850, y: 520, width: 135, height: 105, fill: 'url(#piso-baldosa)' }, fondo);

    // Cocina
    crearSvg('rect', { x: 800, y: 30, width: 170, height: 34, rx: 3, class: 'plano-mueble' }, fondo);
    crearSvg('rect', { x: 935, y: 75, width: 35, height: 100, rx: 3, class: 'plano-mueble' }, fondo);
    crearTextoSvg('COCINA', { x: 870, y: 125, class: 'plano-etiqueta' }, fondo);

    // Barra con banquetas
    crearSvg('rect', { x: 800, y: 250, width: 36, height: 220, rx: 6, class: 'plano-barra' }, fondo);
    crearTextoSvg('BARRA', { x: 818, y: 360, class: 'plano-etiqueta plano-etiqueta-clara', transform: 'rotate(-90 818 360)' }, fondo);

    for (let i = 0; i < 5; i++) {
        crearSvg('circle', { cx: 781, cy: 272 + i * 44, r: 10, class: 'plano-banqueta' }, fondo);
    }

    crearSvg('rect', { x: 880, y: 290, width: 70, height: 40, rx: 4, class: 'plano-mueble' }, fondo);
    crearTextoSvg('CAJA', { x: 915, y: 310, class: 'plano-etiqueta' }, fondo);

    // Baños
    crearTextoSvg('BAÑOS', { x: 918, y: 575, class: 'plano-etiqueta' }, fondo);

    // Paredes
    const paredes = crearSvg('g', { class: 'plano-pared' }, svg);
    crearSvg('path', { d: 'M170 625 H985 V15 H15 V625 H70' }, paredes);
    crearSvg('path', { d: 'M785 15 V200 H860 M920 200 H985', class: 'plano-pared-interna' }, paredes);
    crearSvg('path', { d: 'M850 625 V585 M850 545 V520 H985', class: 'plano-pared-interna' }, paredes);

    // Ventanas
    const ventanas = crearSvg('g', { class: 'plano-ventana' }, svg);
    [[60, 220], [280, 440], [500, 660]].forEach(([desde, hasta]) => {
        crearSvg('line', { x1: desde, y1: 15, x2: hasta, y2: 15 }, ventanas);
    });
    [[110, 250], [330, 470]].forEach(([desde, hasta]) => {
        crearSvg('line', { x1: 15, y1: desde, x2: 15, y2: hasta }, ventanas);
    });

    // Entrada
    crearSvg('path', { d: 'M70 625 V545 A80 80 0 0 1 150 625', class: 'plano-puerta' }, svg);
    crearTextoSvg('ENTRADA', { x: 110, y: 528, class: 'plano-etiqueta' }, svg);

    // Plantas
    [[42, 42], [742, 42], [215, 598]].forEach(([x, y]) => {
        crearSvg('circle', { cx: x, cy: y, r: 15, class: 'plano-planta' }, svg);
        crearSvg('circle', { cx: x, cy: y, r: 7, class: 'plano-planta-centro' }, svg);
    });
}

// Sillas por lado
function ladosSillas(mesa) {
    const capacidad = Number(mesa.capacidad);
    const lados = { arriba: 0, abajo: 0, izquierda: 0, derecha: 0 };

    if (mesa.forma === 'rectangular') {
        const cabeceras = capacidad >= 6 ? 1 : 0;
        lados.izquierda = cabeceras;
        lados.derecha = cabeceras;
        lados.arriba = Math.ceil((capacidad - cabeceras * 2) / 2);
        lados.abajo = Math.floor((capacidad - cabeceras * 2) / 2);
        return lados;
    }

    const orden = ['arriba', 'abajo', 'izquierda', 'derecha'];

    for (let i = 0; i < capacidad; i++) {
        lados[orden[i % 4]]++;
    }

    return lados;
}

// Tamaño de la mesa
function medidasMesa(mesa) {
    const capacidad = Number(mesa.capacidad);

    if (mesa.forma === 'redonda') {
        const radio = Math.max(28, 14 + capacidad * 4.5);
        return { ancho: radio * 2, alto: radio * 2, radio };
    }

    const lados = ladosSillas(mesa);
    let ancho = Math.max(mesa.forma === 'rectangular' ? 110 : 60, Math.max(lados.arriba, lados.abajo) * 40 + 20);
    let alto = Math.max(60, Math.max(lados.izquierda, lados.derecha) * 40 + 20);

    if (mesa.forma === 'cuadrada') {
        ancho = alto = Math.max(ancho, alto);
    }

    return { ancho, alto, radio: 0 };
}

// Silla individual
function dibujarSilla(grupo, x, y, angulo) {
    crearSvg('rect', {
        x: -14,
        y: -7,
        width: 28,
        height: 14,
        rx: 5,
        class: 'mesa-silla',
        transform: `translate(${x} ${y}) rotate(${angulo})`,
    }, grupo);
}

// Sillas alrededor de la mesa
function dibujarSillas(grupo, mesa, medidas) {
    const separacion = 13;

    if (mesa.forma === 'redonda') {
        const capacidad = Number(mesa.capacidad);
        const distancia = medidas.radio + separacion;

        for (let i = 0; i < capacidad; i++) {
            const angulo = (360 / capacidad) * i - 90;
            const radianes = angulo * Math.PI / 180;
            dibujarSilla(grupo, Math.cos(radianes) * distancia, Math.sin(radianes) * distancia, angulo + 90);
        }

        return;
    }

    const lados = ladosSillas(mesa);
    const medioAncho = medidas.ancho / 2;
    const medioAlto = medidas.alto / 2;

    // Reparto parejo sobre un lado
    const posiciones = (cantidad, largo) =>
        Array.from({ length: cantidad }, (_, i) => -largo / 2 + (largo / cantidad) * (i + 0.5));

    posiciones(lados.arriba, medidas.ancho).forEach(x => dibujarSilla(grupo, x, -medioAlto - separacion, 0));
    posiciones(lados.abajo, medidas.ancho).forEach(x => dibujarSilla(grupo, x, medioAlto + separacion, 0));
    posiciones(lados.izquierda, medidas.alto).forEach(y => dibujarSilla(grupo, -medioAncho - separacion, y, 90));
    posiciones(lados.derecha, medidas.alto).forEach(y => dibujarSilla(grupo, medioAncho + separacion, y, 90));
}

// Mesa completa
function crearMesaSvg(mesa, opciones = {}) {
    const medidas = medidasMesa(mesa);
    const grupo = crearSvg('g', {
        class: 'mesa ' + (opciones.clase ?? ''),
        transform: `translate(${mesa.pos_x} ${mesa.pos_y})`,
        'data-id': mesa.id,
        tabindex: 0,
        role: 'button',
        'aria-label': `Mesa ${mesa.numero}, ${mesa.capacidad} personas`,
    });

    dibujarSillas(grupo, mesa, medidas);

    if (mesa.forma === 'redonda') {
        crearSvg('circle', { r: medidas.radio, class: 'mesa-tabla' }, grupo);
    } else {
        crearSvg('rect', {
            x: -medidas.ancho / 2,
            y: -medidas.alto / 2,
            width: medidas.ancho,
            height: medidas.alto,
            rx: 6,
            class: 'mesa-tabla',
        }, grupo);
    }

    crearTextoSvg(String(mesa.numero), { y: opciones.detalle ? -6 : 0, class: 'mesa-numero' }, grupo);

    if (opciones.detalle) {
        crearTextoSvg(opciones.detalle, { y: 13, class: 'mesa-detalle' }, grupo);
    }

    return grupo;
}

// Límites para mover una mesa
function limitarPosicion(mesa, x, y) {
    const medidas = medidasMesa(mesa);
    const margenX = medidas.ancho / 2 + 28;
    const margenY = medidas.alto / 2 + 28;

    return {
        x: Math.round(Math.min(Math.max(x, PLANO.salon.x1 + margenX), PLANO.salon.x2 - margenX)),
        y: Math.round(Math.min(Math.max(y, PLANO.salon.y1 + margenY), PLANO.salon.y2 - margenY)),
    };
}

// Etiquetas de forma
const FORMAS_MESA = {
    cuadrada: 'Cuadrada',
    redonda: 'Redonda',
    rectangular: 'Rectangular',
};

// FIN BLOQUE 100% DESARROLLADO CON IA (/docs/diseño-de-mesas)
