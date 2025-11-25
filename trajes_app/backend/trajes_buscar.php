<?php
header('Content-Type: application/json; charset=utf-8');
require 'conexion.php';

$q = isset($_GET['q']) ? trim($_GET['q']) : '';

$trajes = [];

if ($q !== '') {
    $sql = "SELECT id, descripcion, estado, cantidadRentas 
            FROM trajes
            WHERE descripcion LIKE ? OR estado LIKE ?";
    $stmt = $conn->prepare($sql);
    $like = "%$q%";
    $stmt->bind_param("ss", $like, $like);
    $stmt->execute();
    $res = $stmt->get_result();
} else {
    $sql = "SELECT id, descripcion, estado, cantidadRentas FROM trajes";
    $res = $conn->query($sql);
}

if ($res) {
    while ($row = $res->fetch_assoc()) {
        $trajes[] = $row;
    }
    echo json_encode([
        'ok' => true,
        'trajes' => $trajes
    ]);
} else {
    echo json_encode([
        'ok' => false,
        'error' => $conn->error
    ]);
}

$conn->close();
