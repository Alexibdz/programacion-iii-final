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
  descripcion     VARCHAR(255)  NULL,
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

-- Órdenes: cada pedido dentro de una visita
CREATE TABLE ordenes (
  id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  mesa_visita_id  INT UNSIGNED NOT NULL,
  estado          ENUM('pendiente','en_preparacion','lista','entregada','cancelada')
                  NOT NULL DEFAULT 'pendiente',
  observaciones   VARCHAR(255) NULL,
  creado_en       DATETIME NULL,
  actualizado_en  DATETIME NULL,
  eliminado_en    DATETIME NULL,
  CONSTRAINT fk_ordenes_visita FOREIGN KEY (mesa_visita_id) REFERENCES mesa_visita(id) ON DELETE CASCADE,
  INDEX idx_ordenes_estado (estado, creado_en)
) ENGINE=InnoDB;

-- Productos pedidos en cada orden, copiados al momento de pedir
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

-- Usuario inicial: admin, contraseña admin123
INSERT INTO usuarios (nombre, usuario, password_hash, rol) VALUES
  ('Administrador', 'admin', '$2y$10$tRz9bdSaodtOKnNWPZ.Qz.Be08fMZJAkKjZYMRHLRD8iRkKdYcGLG', 'principal');

INSERT INTO categorias (nombre, requiere_cocina) VALUES
  ('Entradas', TRUE), ('Platos principales', TRUE), ('Bebidas', FALSE), ('Postres', TRUE);

INSERT INTO productos (categoria_id, nombre, descripcion, precio, creado_en) VALUES
  (1, 'Empanadas',           'Docena, carne o jamón y queso', 12000, NOW()),
  (1, 'Provoleta',           'A la parrilla con orégano',      6500, NOW()),
  (2, 'Milanesa napolitana', 'Con papas fritas',              14000, NOW()),
  (2, 'Bife de chorizo',     'Con guarnición a elección',     18000, NOW()),
  (3, 'Agua mineral',        '500 ml',                         2000, NOW()),
  (3, 'Gaseosa',             '500 ml',                         2500, NOW()),
  (4, 'Flan casero',         'Con dulce de leche',             4500, NOW());

INSERT INTO mesas (numero, creado_en) VALUES
  (1, NOW()), (2, NOW()), (3, NOW()), (4, NOW()),
  (5, NOW()), (6, NOW()), (7, NOW()), (8, NOW());
