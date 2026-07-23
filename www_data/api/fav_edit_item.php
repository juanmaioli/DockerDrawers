<?php
session_start();
include_once("../config.php");

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

if (empty($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'No autenticado']);
    exit();
}

$mla_id = $_POST['id'] ?? '';
$mla_id = preg_replace('/[^A-Z0-9]/', '', strtoupper($mla_id));

if (empty($mla_id)) {
    echo json_encode(['status' => 'error', 'message' => 'ID de artículo inválido']);
    exit();
}

$title         = trim($_POST['title'] ?? '');
$price         = (isset($_POST['price']) && $_POST['price'] !== '') ? floatval($_POST['price']) : null;
$full          = (isset($_POST['full']) && $_POST['full'] === 'si') ? 'si' : 'no';
$internacional = (isset($_POST['internacional']) && $_POST['internacional'] === 'si') ? 'si' : 'no';
$img           = trim($_POST['img'] ?? '');

$conn = get_db_connection();

// Asegurar que las columnas existan en la tabla MariaDB
$conn->query("ALTER TABLE drawers_fav ADD COLUMN IF NOT EXISTS fav_full VARCHAR(2) NOT NULL DEFAULT 'no'");
$conn->query("ALTER TABLE drawers_fav ADD COLUMN IF NOT EXISTS fav_internacional VARCHAR(2) NOT NULL DEFAULT 'no'");

$stmt_check = $conn->prepare("SELECT count(*) AS total FROM drawers_fav WHERE fav_mla = ?");
$stmt_check->bind_param("s", $mla_id);
$stmt_check->execute();
$row_check = $stmt_check->get_result()->fetch_assoc();
$stmt_check->close();

$permalink = "https://articulo.mercadolibre.com.ar/MLA-" . str_replace('MLA', '', $mla_id);

if (!empty($row_check['total'])) {
    $stmt = $conn->prepare("UPDATE drawers_fav SET fav_title = ?, fav_price = ?, fav_full = ?, fav_internacional = ?, fav_img = ? WHERE fav_mla = ?");
    $stmt->bind_param("sdssss", $title, $price, $full, $internacional, $img, $mla_id);
    $stmt->execute();
    $stmt->close();
} else {
    $stmt = $conn->prepare("INSERT INTO drawers_fav (fav_mla, fav_link, fav_date, fav_title, fav_img, fav_price, fav_full, fav_internacional, fav_delete) VALUES (?, ?, NOW(), ?, ?, ?, ?, ?, 0)");
    $stmt->bind_param("ssssdss", $mla_id, $permalink, $title, $img, $price, $full, $internacional);
    $stmt->execute();
    $stmt->close();
}

$conn->close();

echo json_encode([
    'status' => 'ok',
    'data'   => [
        'id'            => $mla_id,
        'title'         => $title,
        'price'         => $price,
        'full'          => $full,
        'internacional' => $internacional,
        'img'           => $img,
    ]
]);
