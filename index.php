<?php
require_once __DIR__ . '/conexion.php';

$categorias = $conexion->query("SELECT * FROM categorias ORDER BY nombre ASC")->fetch_all(MYSQLI_ASSOC);

$sql = "SELECT p.*, c.nombre AS categoria_nombre
        FROM productos p
        JOIN categorias c ON p.id_categoria = c.id
        ORDER BY p.producto ASC";
$productos = $conexion->query($sql)->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tienda Sara | Punto de Venta</title>
    <link rel="stylesheet" href="estilos.css">
    <script src="script.js" defer></script>
</head>
<body>
    <header class="site-header">
        <div class="header-content">
            <a href="index.php" class="brand">
                <span class="brand-badge">S</span>
                <span>Tienda Sara</span>
            </a>
            <nav class="nav-links">
                <a href="index.php" class="nav-btn active">Vender</a>
                <a href="admin_productos.php" class="nav-btn">Productos</a>
                <a href="ventas_dia.php" class="nav-btn">Ventas del día</a>
            </nav>
        </div>
    </header>

    <main class="main-layout">
        <section class="catalog-section">
            <div class="section-top">
                <h1 class="page-title">¿Qué se lleva hoy?</h1>
                <input type="search" id="search-box" class="search-input" placeholder="Buscar producto" aria-label="Buscar producto">
            </div>

            <div class="category-bar">
                <button class="cat-chip active" data-cat="all">Todos (<?php echo count($productos); ?>)</button>
                <?php foreach ($categorias as $cat): ?>
                    <button class="cat-chip" data-cat="<?php echo htmlspecialchars($cat['nombre']); ?>">
                        <?php echo htmlspecialchars($cat['nombre']); ?>
                    </button>
                <?php endforeach; ?>
            </div>

            <div class="products-grid" id="products-container">
                <?php foreach ($productos as $prod): ?>
                    <?php
                        $agotado = (int)$prod['cantidad'] < 1;
                        $datos = [
                            'id' => (int)$prod['id'],
                            'producto' => $prod['producto'],
                            'precio' => (float)$prod['precio'],
                            'cantidad' => (int)$prod['cantidad'],
                        ];
                    ?>
                    <div class="product-card<?php echo $agotado ? ' agotado' : ''; ?>"
                         data-category="<?php echo htmlspecialchars($prod['categoria_nombre']); ?>"
                         data-name="<?php echo htmlspecialchars(mb_strtolower($prod['producto'])); ?>">
                        <div>
                            <img src="<?php echo htmlspecialchars(ruta_imagen($prod['imagen'])); ?>"
                                 alt="<?php echo htmlspecialchars($prod['producto']); ?>"
                                 class="product-thumb" loading="lazy"
                                 onerror="this.onerror=null; this.src='img/default.svg';">
                            <h3 class="product-title"><?php echo htmlspecialchars($prod['producto']); ?></h3>
                            <div class="product-price">$<?php echo number_format($prod['precio'], 2); ?></div>
                            <div class="product-stock<?php echo $prod['cantidad'] <= 5 ? ' bajo' : ''; ?>">
                                <?php echo $agotado ? 'Agotado' : 'Quedan ' . (int)$prod['cantidad']; ?>
                            </div>
                        </div>
                        <button class="btn-add" <?php echo $agotado ? 'disabled' : ''; ?>
                                data-producto="<?php echo htmlspecialchars(json_encode($datos), ENT_QUOTES); ?>">
                            <?php echo $agotado ? 'Agotado' : 'Agregar'; ?>
                        </button>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <section class="cart-section">
            <h2 class="cart-title">Orden de venta</h2>
            <div class="cart-items" id="cart-items">
                <p class="cart-empty">Aún no hay productos en la orden.</p>
            </div>
            <div class="cart-total">
                <span>Total</span>
                <span id="cart-total-amount">$0.00</span>
            </div>
            <button class="btn-checkout" id="btn-checkout">Cobrar e imprimir ticket</button>
        </section>
    </main>
</body>
</html>
