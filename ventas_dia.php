<?php
require_once __DIR__ . '/conexion.php';

$resTotal = $conexion->query(
    "SELECT SUM(total) AS total_dia, COUNT(id) AS total_ventas
     FROM ventas WHERE DATE(fecha_venta) = CURDATE()"
)->fetch_assoc();
$totalDia = $resTotal['total_dia'] ?? 0;
$totalVentas = $resTotal['total_ventas'] ?? 0;

$ventas = $conexion->query(
    "SELECT v.*, u.nombre AS vendedor
     FROM ventas v
     JOIN usuarios u ON v.id_usuario = u.id
     WHERE DATE(v.fecha_venta) = CURDATE()
     ORDER BY v.fecha_venta DESC"
)->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tienda Sara | Ventas del día</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>
    <header class="site-header">
        <div class="header-content">
            <a href="index.php" class="brand">
                <span class="brand-badge">S</span>
                <span>Tienda Sara</span>
            </a>
            <nav class="nav-links">
                <a href="index.php" class="nav-btn">Vender</a>
                <a href="admin_productos.php" class="nav-btn">Productos</a>
                <a href="ventas_dia.php" class="nav-btn active">Ventas del día</a>
            </nav>
        </div>
    </header>

    <main class="main-layout solo-una-columna">
        <section class="catalog-section">
            <h1 class="page-title" style="margin-bottom:20px;">Ventas de hoy</h1>

            <div class="stats">
                <div class="stat dinero">
                    <div class="stat-label">Total vendido</div>
                    <div class="stat-num">$<?php echo number_format($totalDia, 2); ?></div>
                </div>
                <div class="stat conteo">
                    <div class="stat-label">Ventas realizadas</div>
                    <div class="stat-num"><?php echo (int)$totalVentas; ?></div>
                </div>
            </div>

            <div class="table-frame">
                <table>
                    <thead>
                        <tr><th>Venta</th><th>Hora</th><th>Vendedor</th><th>Monto</th></tr>
                    </thead>
                    <tbody>
                        <?php if (count($ventas) === 0): ?>
                            <tr><td colspan="4" class="tabla-vacia">Todavía no hay ventas hoy. Cobra la primera desde Vender.</td></tr>
                        <?php else: ?>
                            <?php foreach ($ventas as $v): ?>
                                <tr>
                                    <td>#<?php echo (int)$v['id']; ?></td>
                                    <td><?php echo date('H:i:s', strtotime($v['fecha_venta'])); ?></td>
                                    <td><?php echo htmlspecialchars($v['vendedor']); ?></td>
                                    <td class="precio">$<?php echo number_format($v['total'], 2); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>
</body>
</html>
