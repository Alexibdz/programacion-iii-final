# Gestión de Órdenes de Restaurante

Sistema web para gestionar las mesas y órdenes de un restaurante.
Conecta la estación de mozos con la estación de cocina y emite el recibo de cada cuenta.


> 🚧 Proyecto para el Coloquio Final Integrador de Programación III.

## Créditos

| | |
|---|---|
| **Autor** | Alexis Angel Rios Artaza |
| **Asignatura** | Programación III |
| **Carrera** | Técnico Superior en Análisis y Desarrollo de Software |
| **Institución** | Instituto Gaspar Benavento |
| **Año** | 2026 |

## Tecnologías

| Capa | Tecnología |
|---|---|
| Frontend | HTML, CSS, JavaScript, jQuery, Bootstrap|
| Backend | PHP con CodeIgniter|
| Base de datos | MySQL |
| Comunicación | API JSON consumida por AJAX |

## Funcionalidades

- ABM de menú, categorías, mesas y usuarios.
- Mapa de mesas con su estado: libre u ocupada.
- Cuentas por mesa con múltiples órdenes.
- Cola de cocina: recibe solo los productos que requieren preparación.
- Generación de cuenta con recibo para impresora de 80 mm.
- Roles de usuario: principal, administrador, mozo y cocina.
- Baja lógica de registros, historial de órdenes y auditoría de todo el sistema.

## Instalación

Requiere XAMPP con PHP 8.2 o superior y Composer.

```bash
git clone https://github.com/Alexibdz/programacion-iii-final.git
cd programacion-iii-final
composer install
```

1. Importar `db/gestion_ordenes.sql` en phpMyAdmin.
2. Copiar el archivo `env` como `.env` y completar los datos de conexión a la base.
3. Ejecutar `php spark serve` y abrir <http://localhost:8080>.


## Licencia

Distribuido bajo la licencia **MIT**. Ver el archivo [LICENSE](LICENSE).

CodeIgniter 4, jQuery y Bootstrap se distribuyen bajo sus propias licencias MIT.
