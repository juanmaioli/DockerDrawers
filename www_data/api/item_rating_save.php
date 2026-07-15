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

$item_id = isset($_POST['item_id']) ? (int)$_POST['item_id'] : 0;
$rating  = isset($_POST['item_rating']) ? (int)$_POST['item_rating'] : 0;

if ($item_id <= 0 || $rating < 0 || $rating > 5) {
    echo json_encode(['ok' => false, 'error' => 'Parámetros inválidos']);
    exit();
}

// Actualizar calificación verificando propiedad del ítem
$sql = "UPDATE drawers_items SET item_rating = ? WHERE item_id = ? AND item_owner = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iii", $rating, $item_id, $usuarioId);
$stmt->execute();
$stmt->close();
$conn->close();

echo json_encode(['ok' => true, 'item_rating' => $rating]);
