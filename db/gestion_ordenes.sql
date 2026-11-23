-- Zona horaria de Argentina
SET time_zone = '-03:00';

CREATE DATABASE IF NOT EXISTS gestion_ordenes
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE gestion_ordenes;

-- Usuarios del sistema
CREATE TABLE usuarios (
  id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nombre          VARCHAR(60)  NOT NULL,
  usuario         VARCHAR(40)  NOT NULL UNIQUE,
  password_hash   VARCHAR(255) NOT NULL,
  rol             ENUM('principal','administrador','mozo','cocina') NOT NULL,
  eliminado_en    DATETIME NULL
) ENGINE=InnoDB;

-- Categorías del menú
CREATE TABLE categorias (
  id               INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nombre           VARCHAR(50) NOT NULL UNIQUE,
  requiere_cocina  BOOLEAN NOT NULL DEFAULT TRUE,
  eliminado_en     DATETIME NULL
) ENGINE=InnoDB;

-- Productos del menú
CREATE TABLE productos (
  id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  categoria_id    INT UNSIGNED NOT NULL,
  nombre          VARCHAR(80)   NOT NULL,
  precio          DECIMAL(10,2) NOT NULL CHECK (precio > 0),
  creado_en       DATETIME NULL,
  actualizado_en  DATETIME NULL,
  eliminado_en    DATETIME NULL,
  CONSTRAINT fk_productos_categoria FOREIGN KEY (categoria_id) REFERENCES categorias(id)
) ENGINE=InnoDB;

-- Mesas del salón
CREATE TABLE mesas (
  id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  numero          SMALLINT UNSIGNED NOT NULL UNIQUE,
  -- BLOQUE 100% DESARROLLADO CON IA (/docs/diseño-de-mesas)
  capacidad       TINYINT UNSIGNED NOT NULL DEFAULT 4 CHECK (capacidad BETWEEN 1 AND 12),
  forma           ENUM('cuadrada','redonda','rectangular') NOT NULL DEFAULT 'cuadrada',
  pos_x           SMALLINT UNSIGNED NOT NULL DEFAULT 400,
  pos_y           SMALLINT UNSIGNED NOT NULL DEFAULT 320,
  -- FIN BLOQUE 100% DESARROLLADO CON IA (/docs/diseño-de-mesas)
  creado_en       DATETIME NULL,
  actualizado_en  DATETIME NULL,
  eliminado_en    DATETIME NULL
) ENGINE=InnoDB;

-- Visitas de un cliente a una mesa
CREATE TABLE mesa_visita (
  id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  mesa_id         INT UNSIGNED NOT NULL,
  usuario_id      INT UNSIGNED NOT NULL,
  estado          ENUM('abierta','cerrada','cancelada') NOT NULL DEFAULT 'abierta',
  total           DECIMAL(10,2) NOT NULL DEFAULT 0,
  cerrada_en      DATETIME NULL,
  creado_en       DATETIME NULL,
  actualizado_en  DATETIME NULL,
  eliminado_en    DATETIME NULL,
  CONSTRAINT fk_visita_mesa    FOREIGN KEY (mesa_id)    REFERENCES mesas(id),
  CONSTRAINT fk_visita_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id),
  INDEX idx_visita_mesa_estado (mesa_id, estado)
) ENGINE=InnoDB;

-- Pedidos de cada visita
CREATE TABLE ordenes (
  id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  mesa_visita_id  INT UNSIGNED NOT NULL,
  estado          ENUM('pendiente','en_preparacion','terminada','cancelada') NOT NULL DEFAULT 'pendiente',
  observaciones   VARCHAR(255) NULL,
  creado_en       DATETIME NULL,
  actualizado_en  DATETIME NULL,
  eliminado_en    DATETIME NULL,
  CONSTRAINT fk_ordenes_visita FOREIGN KEY (mesa_visita_id) REFERENCES mesa_visita(id) ON DELETE CASCADE,
  INDEX idx_ordenes_estado (estado, creado_en)
) ENGINE=InnoDB;

-- Detalle de cada orden
CREATE TABLE orden_detalle (
  id               INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  orden_id         INT UNSIGNED NOT NULL,
  producto_id      INT UNSIGNED NOT NULL,
  producto_nombre  VARCHAR(80)   NOT NULL,
  requiere_cocina  BOOLEAN       NOT NULL,
  precio_unitario  DECIMAL(10,2) NOT NULL,
  cantidad         SMALLINT UNSIGNED NOT NULL CHECK (cantidad > 0),
  subtotal         DECIMAL(10,2) NOT NULL,
  observaciones    VARCHAR(255) NULL,
  creado_en        DATETIME NULL,
  CONSTRAINT fk_detalle_orden FOREIGN KEY (orden_id) REFERENCES ordenes(id) ON DELETE CASCADE,
  INDEX idx_detalle_producto (producto_id)
) ENGINE=InnoDB;

-- Historial de cambios de cada visita
CREATE TABLE historial_mesa_visita (
  id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  mesa_visita_id  INT UNSIGNED NOT NULL,
  mesa_id         INT UNSIGNED NOT NULL,
  total           DECIMAL(10,2) NOT NULL,
  cerrada_en      DATETIME NULL,
  accion          ENUM('ALTA','MODIFICACION','BAJA','RESTAURACION') NOT NULL,
  usuario_id      INT UNSIGNED NULL,
  registrado_en   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_historial_visita FOREIGN KEY (mesa_visita_id) REFERENCES mesa_visita(id) ON DELETE CASCADE,
  INDEX idx_historial_visita (mesa_visita_id)
) ENGINE=InnoDB;

-- Historial de cambios de cada orden
CREATE TABLE historial_ordenes (
  id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  orden_id        INT UNSIGNED NOT NULL,
  mesa_visita_id  INT UNSIGNED NOT NULL,
  observaciones   VARCHAR(255) NULL,
  accion          ENUM('ALTA','MODIFICACION','BAJA','RESTAURACION') NOT NULL,
  usuario_id      INT UNSIGNED NULL,
  registrado_en   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_historial_orden FOREIGN KEY (orden_id) REFERENCES ordenes(id) ON DELETE CASCADE,
  INDEX idx_historial_orden (orden_id)
) ENGINE=InnoDB;

-- Auditoría de todo el sistema
CREATE TABLE auditoria (
  id             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  tabla          VARCHAR(40)  NOT NULL,
  registro_id    INT UNSIGNED NOT NULL,
  accion         ENUM('ALTA','MODIFICACION','BAJA','RESTAURACION') NOT NULL,
  usuario_id     INT UNSIGNED NULL,
  registrado_en  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_auditoria_registro (tabla, registro_id)
) ENGINE=InnoDB;

-- Usuario inicial admin / admin123
INSERT INTO usuarios (nombre, usuario, password_hash, rol) VALUES
  ('Administrador', 'admin', '$2y$10$tRz9bdSaodtOKnNWPZ.Qz.Be08fMZJAkKjZYMRHLRD8iRkKdYcGLG', 'principal');

INSERT INTO categorias (nombre, requiere_cocina) VALUES
  ('Entradas', TRUE), ('Platos principales', TRUE), ('Bebidas', FALSE), ('Postres', TRUE);

INSERT INTO productos (categoria_id, nombre, precio, creado_en) VALUES
  (1, 'Empanadas',                      12000, NOW()),
  (1, 'Provoleta',                       6500, NOW()),
  (2, 'Milanesa napolitana',            14000, NOW()),
  (2, 'Bife de chorizo',                18000, NOW()),
  (3, 'Agua mineral',                    2000, NOW()),
  (3, 'Gaseosa',                         2500, NOW()),
  (4, 'Flan casero',                     4500, NOW());

-- Menú de ejemplo
INSERT INTO productos (categoria_id, nombre, precio, creado_en) VALUES
  (1, 'Rabas',                          11500, NOW()),
  (1, 'Papas fritas',                    6000, NOW()),
  (1, 'Papas con cheddar',               8500, NOW()),
  (1, 'Tabla de fiambres',              15000, NOW()),
  (1, 'Bastones de muzzarella',          8000, NOW()),
  (1, 'Langostinos rebozados',          14500, NOW()),
  (1, 'Croquetas de jamón',              7500, NOW()),
  (1, 'Ensalada caprese',                7000, NOW()),
  (1, 'Chorizo a la parrilla',           5500, NOW()),
  (1, 'Morcilla a la parrilla',          5000, NOW()),
  (2, 'Milanesa a caballo',             15500, NOW()),
  (2, 'Suprema de pollo',               13000, NOW()),
  (2, 'Ojo de bife',                    21000, NOW()),
  (2, 'Vacío a la parrilla',            19000, NOW()),
  (2, 'Pollo al horno',                 12500, NOW()),
  (2, 'Ravioles de ricota',             11500, NOW()),
  (2, 'Sorrentinos de jamón y queso',   12000, NOW()),
  (2, 'Ñoquis de papa',                 10500, NOW()),
  (2, 'Hamburguesa completa',           12000, NOW()),
  (2, 'Risotto de hongos',              13500, NOW()),
  (3, 'Agua con gas',                    2000, NOW()),
  (3, 'Gaseosa grande',                  5000, NOW()),
  (3, 'Jugo de naranja',                 4000, NOW()),
  (3, 'Limonada',                        4500, NOW()),
  (3, 'Cerveza tirada',                  5500, NOW()),
  (3, 'Cerveza en lata',                 4000, NOW()),
  (3, 'Vino tinto Malbec',              14000, NOW()),
  (3, 'Vino blanco Torrontés',          13000, NOW()),
  (3, 'Copa de vino',                    4500, NOW()),
  (3, 'Fernet con cola',                 6000, NOW()),
  (4, 'Tiramisú',                        6500, NOW()),
  (4, 'Helado',                          5500, NOW()),
  (4, 'Panqueques con dulce de leche',   6000, NOW()),
  (4, 'Brownie con helado',              6500, NOW()),
  (4, 'Volcán de chocolate',             7500, NOW()),
  (4, 'Cheesecake',                      6800, NOW()),
  (4, 'Ensalada de frutas',              4500, NOW()),
  (4, 'Queso y dulce',                   4000, NOW()),
  (4, 'Mousse de chocolate',             5000, NOW()),
  (4, 'Budín de pan',                    5000, NOW());

-- BLOQUE 100% DESARROLLADO CON IA (/docs/diseño-de-mesas)
-- Salón de ejemplo
INSERT INTO mesas (numero, capacidad, forma, pos_x, pos_y, creado_en) VALUES
  (1,  4, 'cuadrada',    120, 110, NOW()),
  (2,  4, 'cuadrada',    290, 110, NOW()),
  (3,  4, 'cuadrada',    460, 110, NOW()),
  (4,  4, 'cuadrada',    630, 110, NOW()),
  (5,  6, 'redonda',     180, 310, NOW()),
  (6,  4, 'redonda',     400, 310, NOW()),
  (7,  4, 'redonda',     610, 310, NOW()),
  (8,  8, 'rectangular', 380, 510, NOW()),
  (9,  2, 'cuadrada',    580, 510, NOW()),
  (10, 2, 'cuadrada',    690, 510, NOW());
-- FIN BLOQUE 100% DESARROLLADO CON IA (/docs/diseño-de-mesas)
