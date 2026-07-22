<?php
mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');
header('Content-Type: application/json; charset=utf-8');

include('../config.php');
session_start();

// Verificar sesión activa
if (empty($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    http_response_code(401);
    echo json_encode(['ok' => false, 'error' => 'No autorizado']);
    exit();
}

$usuarioId = $_SESSION['usuario_id'];
validate_csrf();

$conn = get_db_connection();

$ml_order_id = $_POST['ml_order_id'] ?? '';
$ml_item_id  = $_POST['ml_item_id']  ?? '';
$checked     = isset($_POST['checked']) ? (int)$_POST['checked'] : 1;

if (empty($ml_order_id) || empty($ml_item_id)) {
    echo json_encode(['ok' => false, 'error' => 'Parámetros inválidos']);
    exit();
}

// 1. Actualizar en drawers_compras
$sql_cmp = "UPDATE drawers_compras SET cmp_checked = ? WHERE usr_id = ? AND ml_order_id = ? AND ml_item_id = ?";
$stmt_cmp = $conn->prepare($sql_cmp);
$stmt_cmp->bind_param("iiss", $checked, $usuarioId, $ml_order_id, $ml_item_id);
$stmt_cmp->execute();
$stmt_cmp->close();

// 2. Mantener actualización en drawers_compras_check por compatibilidad
$sql = "INSERT INTO drawers_compras_check (usr_id, ml_order_id, ml_item_id, checked)
        VALUES (?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE checked = VALUES(checked)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("issi", $usuarioId, $ml_order_id, $ml_item_id, $checked);
$stmt->execute();
$stmt->close();
$conn->close();

echo json_encode(['ok' => true, 'checked' => $checked]);
