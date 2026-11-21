# Diseño de mesas

Exportación de la conversación con Claude Code del 21/11/2026 sobre las pantallas de Mesas, el plano 2D del restaurante y el menú lateral.

Se incluyen los mensajes y las respuestas completas. Las acciones sobre archivos y comandos se resumen en una línea; sus salidas no se incluyen.

---

## Usuario

MESAS tanto mesas y mesas gestion quiero que lo hagas vos.

necesito un diseño 2d del restaurante (calquiera) de ejemplo y que se pueda agregar o sacar

## Claude

Voy a revisar el proyecto para seguir el mismo patrón que Categorías y Productos.

<details><summary>Acciones (6)</summary>

- Comando: Lista la estructura del proyecto y lee los archivos clave
- Lee `C:/Users/alexi/.claude/projects/c--Users-alexi-Desktop-PROGRAMACION-FINAL/memory/comentarios-sin-parentesis.md`
- Comando: Lee los archivos de mesas y la vista y JS de categorías
- Comando: Lee el esquema de la base y la plantilla
- Comando: Lee visitas, login, productos, CSS y configuración
- Comando: Lee el modelo de productos y los patrones de JS

</details>

Tengo el patrón claro. Empiezo por la base: la tabla `mesas` necesita forma, capacidad y posición en el plano.

<details><summary>Acciones (1)</summary>

- Comando: Agrega al SQL las columnas del plano y el salón de ejemplo

</details>

Ahora los modelos de mesa y visita.

<details><summary>Acciones (5)</summary>

- Crea o reescribe `app/Models/MesaModelo.php`
- Crea o reescribe `app/Models/VisitaModelo.php`
- Crea o reescribe `app/Controllers/Mesas.php`
- Edita `app/Config/Routes.php`
- Comando: Alinea los mensajes y revisa la sintaxis PHP

</details>

Ahora el plano 2D compartido entre las dos pantallas. Uso SVG con un `viewBox` fijo de 1000×640, así las posiciones guardadas valen igual en cualquier tamaño de pantalla.

<details><summary>Acciones (1)</summary>

- Crea o reescribe `public/js/plano.js`

</details>

Pantalla del mozo: mapa con estado en vivo.

<details><summary>Acciones (1)</summary>

- Crea o reescribe `app/Views/mesas/index.php`

</details>

_[El usuario interrumpió la tarea]_

---

## Usuario

el menu pasa a estar como un navbar izquierdo

## Claude

Paso el menú a una barra lateral izquierda. En celular se abre desde un botón (offcanvas), y desde tablet en adelante queda fija a la izquierda.

<details><summary>Acciones (2)</summary>

- Comando: Revisa la plantilla de acceso y los helpers cargados
- Crea o reescribe `app/Views/plantilla.php`

</details>

Dejé una línea sobrante en el bucle; la limpio y hago que las páginas de visitas marquen "Mapa de mesas" como activo.

<details><summary>Acciones (2)</summary>

- Edita `app/Views/plantilla.php`
- Comando: Agrega los estilos de la barra lateral y revisa la plantilla

</details>

Hice el cambio de menú: ahora es una barra lateral izquierda. No lo probé en el navegador, solo verifiqué que el PHP no tenga errores de sintaxis.

**Cómo queda** ([plantilla.php](app/Views/plantilla.php))
- **Desde tablet en adelante:** barra oscura fija a la izquierda, de 240px de ancho. Queda quieta cuando bajás por la página y "Salir" está abajo.
- **En celular:** arriba aparece una barra con el botón ☰, que abre el menú desde la izquierda.
- **Secciones:** las páginas del menú se definen en un array al principio de la plantilla, en tres grupos:
  - **Salón:** Mapa de mesas, Cocina
  - **Menú:** Productos, Categorías
  - **Administración:** Gestión de mesas, Usuarios, Historial, Auditoría
- **Página actual:** el enlace de la página en la que estás aparece resaltado. Las páginas de visitas marcan "Mapa de mesas".
- **Nombre del enlace:** el "Mesas" de Administración ahora se llama "Gestión de mesas", para no confundirlo con el mapa.
- **Estilos:** van al final de [estilos.css](public/css/estilos.css). El contenido ahora usa todo el ancho disponible, cosa que le sirve al plano.

**Cómo dejé Mesas**
- **Listo:**
  - El SQL, con forma, capacidad y posición de cada mesa, y un salón de ejemplo de 10 mesas.
  - [MesaModelo.php](app/Models/MesaModelo.php), [VisitaModelo.php](app/Models/VisitaModelo.php), el controlador [Mesas.php](app/Controllers/Mesas.php) y las rutas de la API.
  - [plano.js](public/js/plano.js): dibuja el restaurante en 2D con cocina, barra, caja, baños, entrada, ventanas y plantas, y cada mesa con sus sillas según forma y capacidad.
- **Falta:**
  - Las vistas `mesas/index.php` y `mesas/gestion.php`.
  - `mesas.js`, el mapa del mozo con mesas libres y ocupadas.
  - `mesas_gestion.js`, el editor para agregar, arrastrar, editar, dar de baja y restaurar mesas.
  - Los estilos del plano.

Para ver las mesas de ejemplo vas a tener que volver a importar `db/gestion_ordenes.sql`, porque la tabla `mesas` tiene columnas nuevas.

¿Sigo con las vistas de Mesas? Si rechazaste la vista del mapa por algo puntual del diseño, decime qué cambio.

---

## Usuario

segui con la vista y corre la db

## Claude

Sigo. Primero busco cómo está configurada la base para poder importarla.

<details><summary>Acciones (1)</summary>

- Comando: Busca la configuración de la base y el cliente de MySQL

</details>

XAMPP tiene MySQL. Antes de reimportar miro qué hay en la base actual, porque el script crea las tablas desde cero y hay que borrar la base existente.

<details><summary>Acciones (2)</summary>

- Comando: Revisa el contenido actual de la base
- Comando: Modifica la tabla mesas y carga el salón de ejemplo

</details>

Base actualizada. Ahora las vistas, empezando por el mapa del mozo.

<details><summary>Acciones (2)</summary>

- Crea o reescribe `app/Views/mesas/index.php`
- Crea o reescribe `public/js/mesas.js`

</details>

Ahora la pantalla de gestión, con el editor del plano.

<details><summary>Acciones (2)</summary>

- Crea o reescribe `app/Views/mesas/gestion.php`
- Crea o reescribe `public/js/mesas_gestion.js`

</details>

Ahora los estilos del plano.

<details><summary>Acciones (1)</summary>

- Comando: Agrega los estilos del plano y revisa la sintaxis JS

</details>

Pruebo la API contra la base real. Busco si hay un servidor corriendo.

<details><summary>Acciones (2)</summary>

- Comando: Revisa la URL base y los servidores activos
- Comando: Consulta la API de mesas a través de Apache

</details>

La API responde. Pruebo el ciclo completo: crear, mover, número repetido, baja, restaurar y abrir mesa.

<details><summary>Acciones (1)</summary>

- Comando: Prueba los endpoints de la API de mesas

</details>

Todo bien. Falta probar que una mesa ocupada aparezca como tal y que no se pueda dar de baja. Abro una visita de prueba directo en la base.

<details><summary>Acciones (2)</summary>

- Comando: Prueba una mesa ocupada y limpia los datos de prueba
- Comando: Revisa la zona horaria de la app

</details>

La app está en UTC y el sistema es para Argentina: los recibos y el historial también mostrarían la hora corrida. La paso a `America/Argentina/Buenos_Aires`.

<details><summary>Acciones (1)</summary>

- Comando: Pasa la zona horaria a Argentina y compara relojes

</details>

Relojes alineados. Verifico que las dos páginas carguen y saco capturas con Edge en modo headless para ver cómo queda el plano.

<details><summary>Acciones (6)</summary>

- Comando: Verifica que las páginas carguen y saca capturas
- Lee `scratchpad/mesas.png`
- Lee `scratchpad/mesas_gestion.png`
- Lee `scratchpad/movil.png`
- Edita `public/js/plano.js`
- Comando: Busca herramientas de automatización de navegador

</details>

Hay navegadores de Playwright instalados pero no el paquete. Instalo `playwright-core` en el scratchpad, fuera del proyecto, y simulo el uso real: arrastrar, editar, agregar, dar de baja y restaurar.

<details><summary>Acciones (3)</summary>

- Comando: Instala playwright-core en la carpeta temporal
- Crea o reescribe `scratchpad/pw/prueba.js`
- Comando: Corre la prueba de uso en el navegador

</details>

---

## Usuario

necesito que achiques el mapa de las mesa que esten sobre el viewport sin scroll

_[captura de pantalla adjunta del mapa de mesas]_

## Claude

<details><summary>Acciones (5)</summary>

- Comando: Revisa las filas de auditoría creadas por las pruebas
- Comando: Revierte solo los cambios hechos por las pruebas
- Edita `public/css/estilos.css`
- Crea o reescribe `scratchpad/pw/alto.js`
- Comando: Mide si el mapa entra en varias resoluciones

</details>

El mapa entra completo sin scroll en las cuatro resoluciones. Reviso las capturas: la de tu resolución y las de celular.

<details><summary>Acciones (4)</summary>

- Lee `scratchpad/alto_1920_mesas.png`
- Lee `scratchpad/gestion_edicion.png`
- Lee `scratchpad/movil_modal.png`
- Lee `scratchpad/movil_menu.png`

</details>

Todo se ve bien. Un detalle en el menú de celular: "Salir" queda chico y pegado a la lista, porque el cuerpo del offcanvas no es flex en mobile. Lo corrijo.

<details><summary>Acciones (1)</summary>

- Edita `app/Views/plantilla.php`

</details>

Las dos pantallas de mesas ya funcionan, la base está actualizada y el mapa entra completo en pantalla sin scroll. Lo probé en el navegador con Edge automatizado.

**Base de datos**
- No la reimporté. Tenía datos tuyos y el script borra todo, así que agregué las columnas nuevas a la tabla `mesas` con `ALTER`: capacidad, forma y posición en el plano.
- Cargué el salón de ejemplo con 10 mesas. Tus categorías y productos quedaron intactos.
- [gestion_ordenes.sql](db/gestion_ordenes.sql) también quedó actualizado, para cuando la importes desde cero.

**Mapa de mesas** (`/mesas`, para el mozo)
- Plano 2D del restaurante: salón, cocina, barra con banquetas, caja, baños, entrada, ventanas y plantas.
- Mesas en verde si están libres y en rojo si están ocupadas, con los minutos que llevan abiertas. Arriba hay un contador de libres y ocupadas, y la pantalla se actualiza cada 20 segundos.
- Al tocar una mesa se abre su detalle con "Abrir mesa" o "Ver visita".

**Gestión de mesas** (`/mesas/gestion`)
- **Agregar mesa:** la crea con el número siguiente y en un lugar libre del salón.
- **Mover:** arrastrás la mesa y la posición se guarda sola al soltarla. También se mueve con las flechas del teclado.
- **Editar:** al tocar una mesa, el panel derecho permite cambiar número, capacidad (de 1 a 12) y forma. El plano muestra el cambio en vivo antes de guardar.
- **Baja y restaurar:** la baja está bloqueada si la mesa está ocupada. Las dadas de baja aparecen en una lista con botón "Restaurar".

**Tamaño del mapa**
- El plano entra completo en pantalla sin scroll. Lo comprobé en 1920×919 (tu resolución), 1440×900, 1366×657 y 1024×700.
- En pantallas muy anchas queda centrado con márgenes blancos a los costados, porque mantiene la proporción del salón.
- En celular le dejé un ancho mínimo con scroll horizontal; si no, las mesas quedan demasiado chicas para tocarlas.

**Cosas que cambié o que tenés que saber**
- **"Abrir mesa" todavía no anda:** responde "Iniciá sesión para abrir una mesa" porque el login no está hecho. Va a funcionar cuando el login guarde `usuario_id` en la sesión.
- **Zona horaria:** la pasé de UTC a Argentina en `app/Config/App.php`. Estaba 3 horas corrida respecto de MySQL, y eso afectaba los minutos de las mesas ocupadas y los horarios de recibos e historial.
- **Datos de prueba:** borré solo lo que crearon mis pruebas. Las mesas que vos moviste mientras probabas (5, 6, 7, 8 y 10) quedaron donde las dejaste.
- **Menú en celular:** "Salir" ahora queda abajo del menú, como en escritorio.

Los archivos nuevos son [VisitaModelo.php](app/Models/VisitaModelo.php), [plano.js](public/js/plano.js), [mesas.js](public/js/mesas.js) y [mesas_gestion.js](public/js/mesas_gestion.js).


