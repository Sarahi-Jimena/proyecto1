-- Base de datos de "Tienda Sara"
-- Importar en phpMyAdmin o con: mysql -u root < tiendita.sql
CREATE DATABASE IF NOT EXISTS `tienda_sara` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `tienda_sara`;

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS `detalle_ventas`, `ventas`, `productos`, `categorias`, `usuarios`;
SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE `usuarios` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nombre` VARCHAR(100) NOT NULL,
  `gmail` VARCHAR(100) NOT NULL UNIQUE,
  `contrasena` VARCHAR(255) NOT NULL,
  `tipo` ENUM('admin', 'vendedor') NOT NULL DEFAULT 'vendedor',
  `fecha_creacion` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `categorias` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nombre` VARCHAR(50) NOT NULL UNIQUE,
  `descripcion` VARCHAR(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `productos` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `codigo_barras` VARCHAR(50) UNIQUE DEFAULT NULL,
  `producto` VARCHAR(100) NOT NULL,
  `id_categoria` INT NOT NULL,
  `costo` DECIMAL(10,2) NOT NULL,
  `precio` DECIMAL(10,2) NOT NULL,
  `cantidad` INT NOT NULL DEFAULT 0,
  `imagen` VARCHAR(255) DEFAULT 'default.svg',
  FOREIGN KEY (`id_categoria`) REFERENCES `categorias`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `ventas` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `id_usuario` INT NOT NULL,
  `total` DECIMAL(10,2) NOT NULL,
  `fecha_venta` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`id_usuario`) REFERENCES `usuarios`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `detalle_ventas` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `id_venta` INT NOT NULL,
  `id_producto` INT NOT NULL,
  `cantidad` INT NOT NULL,
  `precio_unitario` DECIMAL(10,2) NOT NULL,
  `subtotal` DECIMAL(10,2) NOT NULL,
  FOREIGN KEY (`id_venta`) REFERENCES `ventas`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`id_producto`) REFERENCES `productos`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Contraseña de ambos usuarios de ejemplo: "password" (hash bcrypt). Cámbiala antes de usar un login real.
INSERT INTO `usuarios` (`id`, `nombre`, `gmail`, `contrasena`, `tipo`) VALUES
(1, 'Sara (Administradora)', 'sara@tiendasara.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'),
(2, 'Vendedor de turno', 'ventas@tiendasara.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'vendedor');

INSERT INTO `categorias` (`id`, `nombre`, `descripcion`) VALUES
(1, 'Lácteos y Huevo', 'Leche, queso, crema, yogurt y huevo'),
(2, 'Abarrotes', 'Arroz, frijol, pasta, aceite y azúcar'),
(3, 'Enlatados y Salsas', 'Atún, chiles, sopas y salsas'),
(4, 'Botanas y Dulces', 'Papas, galletas, chocolates y dulces'),
(5, 'Bebidas', 'Refrescos, jugos, agua y energizantes'),
(6, 'Limpieza y Hogar', 'Detergente, cloro, papel y jabón');

INSERT INTO `productos` (`id`, `codigo_barras`, `producto`, `id_categoria`, `costo`, `precio`, `cantidad`, `imagen`) VALUES
(1,  '75020001', 'Leche entera 1 L',          1, 22.00, 28.00, 24, 'default.svg'),
(2,  '75020002', 'Queso fresco 250 g',        1, 38.00, 52.00, 12, 'default.svg'),
(3,  '75020003', 'Yogurt natural 900 g',      1, 30.00, 40.00, 14, 'default.svg'),
(4,  '75020004', 'Huevo blanco 12 pzas',      1, 42.00, 54.00, 20, 'default.svg'),
(5,  '75020005', 'Frijol negro 900 g',        2, 30.00, 40.00, 25, 'default.svg'),
(6,  '75020006', 'Arroz 900 g',               2, 22.00, 29.00, 30, 'default.svg'),
(7,  '75020007', 'Pasta spaghetti 200 g',     2,  7.00, 11.00, 45, 'default.svg'),
(8,  '75020008', 'Aceite vegetal 900 ml',     2, 34.00, 44.00, 18, 'default.svg'),
(9,  '75020009', 'Atún en agua 140 g',        3, 16.00, 22.00, 50, 'default.svg'),
(10, '75020010', 'Chiles jalapeños en lata',  3, 12.00, 18.00, 30, 'default.svg'),
(11, '75020011', 'Salsa roja botella 190 g',  3,  9.00, 14.00, 35, 'default.svg'),
(12, '75020012', 'Sopa instantánea',          3,  9.00, 15.00, 60, 'default.svg'),
(13, '75020013', 'Papas fritas 120 g',        4, 25.00, 36.00, 18, 'default.svg'),
(14, '75020014', 'Galletas de vainilla',      4, 12.00, 18.00, 28, 'default.svg'),
(15, '75020015', 'Chocolate de leche',        4, 11.00, 17.00, 45, 'default.svg'),
(16, '75020016', 'Cacahuates con chile',      4, 10.00, 16.00, 32, 'default.svg'),
(17, '75020017', 'Refresco de cola 600 ml',   5, 14.00, 20.00, 48, 'default.svg'),
(18, '75020018', 'Jugo de naranja 1 L',       5, 21.00, 30.00, 20, 'default.svg'),
(19, '75020019', 'Agua natural 1.5 L',        5,  9.00, 15.00, 60, 'default.svg'),
(20, '75020020', 'Bebida energizante 473 ml', 5, 24.00, 36.00, 15, 'default.svg'),
(21, '75020021', 'Detergente en polvo 1 kg',  6, 32.00, 45.00, 16, 'default.svg'),
(22, '75020022', 'Cloro 1 L',                 6, 12.00, 18.00, 22, 'default.svg'),
(23, '75020023', 'Papel higiénico 4 rollos',  6, 24.00, 34.00, 26, 'default.svg'),
(24, '75020024', 'Jabón de barra',            6,  9.00, 14.00, 40, 'default.svg');
