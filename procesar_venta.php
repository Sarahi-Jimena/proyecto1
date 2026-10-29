<?php
require_once __DIR__ . '/conexion.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

if (!$data || empty($data['items']) || !is_array($data['items'])) {
    echo json_encode(['success' => false, 'message' => 'Carrito vacío']);
    exit;
}

$id_usuario = 1; // Usuario administrador por defecto (aún no hay login)

$conexion->begin_transaction();

try {
    $stmtProd  = $conexion->prepare("SELECT producto, precio, cantidad FROM productos WHERE id = ? FOR UPDATE");
    $stmtStock = $conexion->prepare("UPDATE productos SET cantidad = cantidad - ? WHERE id = ?");

    $lineas = [];
    $total = 0.0;

    foreach ($data['items'] as $item) {
        $id_producto = (int)($item['id'] ?? 0);
        $cant = (int)($item['qty'] ?? 0);
        if ($id_producto < 1 || $cant < 1) {
            throw new Exception('Datos de producto inválidos');
        }

        // El precio y el stock se leen de la base de datos, no del navegador
        $stmtProd->bind_param("i", $id_producto);
        $stmtProd->execute();
        $prod = $stmtProd->get_result()->fetch_assoc();

        if (!$prod) {
            throw new Exception('El producto #' . $id_producto . ' no existe');
        }
        if ($prod['cantidad'] < $cant) {
            throw new Exception('Stock insuficiente de "' . $prod['producto'] . '" (quedan ' . $prod['cantidad'] . ')');
        }

        $precio = (float)$prod['precio'];
        $subtotal = $precio * $cant;
        $total += $subtotal;
        $lineas[] = [$id_producto, $cant, $precio, $subtotal];
    }

    $stmt = $conexion->prepare("INSERT INTO ventas (id_usuario, total) VALUES (?, ?)");
    $stmt->bind_param("id", $id_usuario, $total);
    $stmt->execute();
    $id_venta = $conexion->insert_id;

    $stmtDetalle = $conexion->prepare("INSERT INTO detalle_ventas (id_venta, id_producto, cantidad, precio_unitario, subtotal) VALUES (?, ?, ?, ?, ?)");

    foreach ($lineas as [$id_producto, $cant, $precio, $subtotal]) {
        $stmtDetalle->bind_param("iiidd", $id_venta, $id_producto, $cant, $precio, $subtotal);
        $stmtDetalle->execute();

        $stmtStock->bind_param("ii", $cant, $id_producto);
        $stmtStock->execute();
    }

    $conexion->commit();
    echo json_encode(['success' => true, 'id_venta' => $id_venta, 'total' => $total]);
} catch (Exception $e) {
    $conexion->rollback();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
