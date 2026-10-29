# Plan de trabajo: Sistema Punto de Venta "Tienda Sara"

## 1. Resumen
**Tienda Sara** es un sistema web de punto de venta (POS) e inventario para pequeños negocios: ventas rápidas, descuento automático de stock, administración de productos y reporte de ventas del día.

## 2. Fases

### Fase 1: Base de datos (completada)
- [x] Modelo entidad-relación.
- [x] Tablas `usuarios`, `categorias`, `productos`, `ventas` y `detalle_ventas`.
- [x] 24 productos de ejemplo en 6 categorías.

### Fase 2: Punto de venta (completada)
- [x] Búsqueda y filtro por categoría.
- [x] Carrito con control de cantidades y validación de stock.
- [x] Descuento de inventario dentro de una transacción.
- [x] Precios y stock verificados en el servidor.
- [x] Ticket imprimible.
- [x] Reporte de ventas del día (`ventas_dia.php`).

### Fase 3: Administración (completada)
- [x] Alta y baja de productos con foto.
- [x] Nuevo diseño visual (violeta tinta y lima).

### Fase 4: Siguientes pasos
- [ ] Inicio de sesión por rol (admin / vendedor).
- [ ] Editar productos y reabastecer stock.
- [ ] Reporte por rango de fechas y producto más vendido.

## 3. Cotización estimada

| Concepto | Descripción | MXN |
| :--- | :--- | ---: |
| Desarrollo del sistema POS | Ventas, inventario, administración y ticket | $6,500 |
| Dominio (.com / .com.mx) | Registro por 1 año | $350 |
| Hosting PHP/MySQL | Plan esencial por 1 año | $1,200 |
| Instalación y capacitación | Configuración y entrenamiento del personal | $1,000 |
| **Total** | **Llave en mano** | **$9,050** |
