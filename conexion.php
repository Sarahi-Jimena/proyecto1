<?php
// Configuración de la base de datos (ajusta si tu servidor es distinto)
$host = 'localhost';
$usuario = 'root';
$contrasena = '';
$baseDatos = 'tienda_sara';

// Si existe conexion.local.php, sobrescribe los datos de arriba (no se sube a GitHub)
if (file_exists(__DIR__ . '/conexion.local.php')) {
    require __DIR__ . '/conexion.local.php';
}

date_default_timezone_set('America/Mexico_City');
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conexion = new mysqli($host, $usuario, $contrasena, $baseDatos);
    $conexion->set_charset('utf8mb4');
    $conexion->query("SET time_zone = '-06:00'");
} catch (mysqli_sql_exception $error) {
    error_log('Error de conexión a MySQL: ' . $error->getMessage());
    http_response_code(500);
    exit('Error de conexión con la base de datos. Verifica la configuración en conexion.php.');
}

/** Devuelve la ruta de la imagen de un producto, con respaldo si no existe. */
function ruta_imagen(string $nombre): string
{
    $nombre = trim($nombre);
    if ($nombre === '' || !file_exists(__DIR__ . '/img/' . $nombre)) {
        return 'img/default.svg';
    }
    return 'img/' . $nombre;
}
