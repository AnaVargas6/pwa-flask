<?php
header('Content-Type: application/json; charset=utf-8');
require_once 'conexion.php';

// Solo mientras desarrollas (para ver errores de PHP en el navegador)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Solo POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'ok' => false,
        'error' => 'Método no permitido (usa POST)'
    ]);
    exit;
}

// Leer datos
$id             = isset($_POST['id']) ? intval($_POST['id']) : 0;
$descripcion    = isset($_POST['descripcion']) ? trim($_POST['descripcion']) : '';
$estado         = isset($_POST['estado']) ? trim($_POST['estado']) : '';
$cantidadRentas = isset($_POST['cantidadRentas']) ? intval($_POST['cantidadRentas']) : 0;

// Validar
if ($id <= 0 || $descripcion === '' || $estado === '') {
    echo json_encode([
        'ok' => false,
        'error' => 'Datos incompletos o ID inválido'
    ]);
    exit;
}

// OJO: aquí usamos exactamente el nombre de tu columna: cantidadRentas
$sql = "UPDATE trajes
        SET descripcion = ?,
            estado = ?,
            cantidadRentas = ?
        WHERE id = ?";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode([
        'ok' => false,
        'error' => 'Error al preparar la consulta: ' . $conn->error
    ]);
    exit;
}

$stmt->bind_param("ssii", $descripcion, $estado, $cantidadRentas, $id);

if ($stmt->execute()) {
    echo json_encode(['ok' => true]);
} else {
    echo json_encode([
        'ok' => false,
        'error' => 'Error al ejecutar el UPDATE: ' . $stmt->error
    ]);
}

$stmt->close();
$conn->close();
