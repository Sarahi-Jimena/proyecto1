# Tienda Sara · Punto de venta

Sistema web de punto de venta e inventario hecho con **PHP + MySQL/MariaDB + JavaScript**.

## Funciones
- **Vender**: catálogo con búsqueda y categorías, carrito, cobro y ticket imprimible.
- **Productos**: alta y baja de productos con foto, costo, precio y stock.
- **Ventas del día**: total vendido y listado de transacciones.
- El precio y el stock se validan en el servidor y el inventario se descuenta en una transacción.

## Instalación local (XAMPP)
1. Copia la carpeta del proyecto en `htdocs/tienda-sara`.
2. Inicia **Apache** y **MySQL** en XAMPP.
3. En phpMyAdmin, importa `tiendita.sql` (crea la base `tienda_sara` con datos de ejemplo).
4. Abre <http://localhost/tienda-sara/>.

Si tu MySQL tiene otra contraseña, crea `conexion.local.php` (no se sube a GitHub):

```php
<?php
$usuario = 'mi_usuario';
$contrasena = 'mi_contraseña';
```

## Estructura
| Archivo | Para qué sirve |
| --- | --- |
| `index.php`, `script.js` | Punto de venta |
| `procesar_venta.php` | Registra la venta y descuenta stock |
| `admin_productos.php` | Inventario |
| `ventas_dia.php` | Reporte diario |
| `conexion.php` | Conexión a la base de datos |
| `tiendita.sql` | Estructura y datos de ejemplo |
| `estilos.css` | Diseño |

## Pendiente
Todavía no hay inicio de sesión: las ventas se registran con el usuario administrador (`id = 1`). Consulta `plan_de_trabajo.md`.
