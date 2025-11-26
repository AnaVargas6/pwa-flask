<?php
header('Content-Type: application/json; charset=utf-8');
require_once 'conexion.php';

// Aceptamos ID por GET o POST, como gustes
$id = 0;

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
} elseif (isset($_POST['id'])) {
    $id = intval($_POST['id']);
}

if ($id <= 0) {
    echo json_encode([
        'ok' => false,
        'error' => 'ID inválido'
    ]);
    exit;
}

// Preparar DELETE
$sql = "DELETE FROM trajes WHERE id = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode([
        'ok' => false,
        'error' => 'Error al preparar la consulta: ' . $conn->error
    ]);
    exit;
}

$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        echo json_encode(['ok' => true]);
    } else {
        echo json_encode([
            'ok' => false,
            'error' => 'No se encontró el registro a eliminar'
        ]);
    }
} else {
    echo json_encode([
        'ok' => false,
        'error' => 'Error al ejecutar el DELETE: ' . $stmt->error
    ]);
}

$stmt->close();
$conn->close();
