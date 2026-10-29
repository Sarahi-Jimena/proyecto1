<?php
require_once __DIR__ . '/conexion.php';

$mensaje = '';
$esError = false;

// ---- Eliminar producto ----
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['eliminar_producto'])) {
    try {
        $id = (int)$_POST['id'];
        $stmt = $conexion->prepare("DELETE FROM productos WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $mensaje = 'Producto eliminado.';
    } catch (mysqli_sql_exception $e) {
        $esError = true;
        $mensaje = 'No se puede eliminar: el producto ya tiene ventas registradas. Déjalo en 0 de stock.';
    }
}

// ---- Agregar producto ----
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['guardar_producto'])) {
    $producto = trim($_POST['producto']);
    $id_categoria = (int)$_POST['id_categoria'];
    $costo = max(0, (float)$_POST['costo']);
    $precio = max(0, (float)$_POST['precio']);
    $cantidad = max(0, (int)$_POST['cantidad']);
    $nombreImagen = 'default.svg';

    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif', 'jfif'], true)) {
            $nombreImagen = time() . '_' . uniqid() . '.' . $ext;
            $directorioImg = __DIR__ . '/img/';
            if (!is_dir($directorioImg)) {
                mkdir($directorioImg, 0755, true);
            }
            move_uploaded_file($_FILES['imagen']['tmp_name'], $directorioImg . $nombreImagen);
        } else {
            $esError = true;
            $mensaje = 'Formato de imagen no permitido; se guardó sin imagen.';
        }
    }

    $stmt = $conexion->prepare("INSERT INTO productos (producto, id_categoria, costo, precio, cantidad, imagen) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("siddis", $producto, $id_categoria, $costo, $precio, $cantidad, $nombreImagen);

    if ($stmt->execute()) {
        $mensaje = $esError ? $mensaje : 'Producto agregado.';
    } else {
        $esError = true;
        $mensaje = 'No se pudo agregar el producto.';
    }
}

$productos = $conexion->query(
    "SELECT p.*, c.nombre AS categoria_nombre
     FROM productos p JOIN categorias c ON p.id_categoria = c.id
     ORDER BY p.id DESC"
)->fetch_all(MYSQLI_ASSOC);
$categorias = $conexion->query("SELECT * FROM categorias ORDER BY nombre ASC")->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tienda Sara | Productos</title>
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
                <a href="admin_productos.php" class="nav-btn active">Productos</a>
                <a href="ventas_dia.php" class="nav-btn">Ventas del día</a>
            </nav>
        </div>
    </header>

    <main class="main-layout">
        <section class="catalog-section">
            <h1 class="page-title" style="margin-bottom:20px;">Inventario</h1>

            <div class="table-frame">
                <table>
                    <thead>
                        <tr><th>Foto</th><th>Producto</th><th>Categoría</th><th>Costo</th><th>Precio</th><th>Stock</th><th></th></tr>
                    </thead>
                    <tbody>
                        <?php if (count($productos) === 0): ?>
                            <tr><td colspan="7" class="tabla-vacia">No hay productos. Agrega el primero con el formulario.</td></tr>
                        <?php endif; ?>
                        <?php foreach ($productos as $p): ?>
                            <tr>
                                <td><img class="mini-thumb" src="<?php echo htmlspecialchars(ruta_imagen($p['imagen'])); ?>" alt="" onerror="this.onerror=null; this.src='img/default.svg';"></td>
                                <td><strong><?php echo htmlspecialchars($p['producto']); ?></strong></td>
                                <td><?php echo htmlspecialchars($p['categoria_nombre']); ?></td>
                                <td>$<?php echo number_format($p['costo'], 2); ?></td>
                                <td class="precio">$<?php echo number_format($p['precio'], 2); ?></td>
                                <td><?php echo (int)$p['cantidad']; ?> pzas</td>
                                <td>
                                    <form method="POST" onsubmit="return confirm('¿Eliminar este producto?');">
                                        <input type="hidden" name="id" value="<?php echo (int)$p['id']; ?>">
                                        <button type="submit" name="eliminar_producto" class="btn-borrar">Eliminar</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <section class="cart-section">
            <h2 class="cart-title">Nuevo producto</h2>

            <?php if ($mensaje): ?>
                <p class="aviso<?php echo $esError ? ' error' : ''; ?>"><?php echo htmlspecialchars($mensaje); ?></p>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data" class="form-stack">
                <label><span>Nombre</span>
                    <input type="text" name="producto" required maxlength="100">
                </label>
                <label><span>Categoría</span>
                    <select name="id_categoria" required>
                        <?php foreach ($categorias as $cat): ?>
                            <option value="<?php echo (int)$cat['id']; ?>"><?php echo htmlspecialchars($cat['nombre']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <div class="form-row">
                    <label><span>Costo</span><input type="number" step="0.01" min="0" name="costo" required></label>
                    <label><span>Precio de venta</span><input type="number" step="0.01" min="0" name="precio" required></label>
                </div>
                <label><span>Piezas en inventario</span>
                    <input type="number" min="0" name="cantidad" required>
                </label>
                <label><span>Foto (opcional)</span>
                    <input type="file" name="imagen" accept="image/*">
                </label>
                <button type="submit" name="guardar_producto" class="btn-checkout">Guardar producto</button>
            </form>
        </section>
    </main>
</body>
</html>
