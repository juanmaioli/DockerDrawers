<?php
mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');

include("config.php");
validate_csrf();
$conn = get_db_connection();

$newItem = $_POST['newItem'] ?? '';

// Fallback por si se envía como JSON en el cuerpo de la solicitud
if (empty($newItem) && $_SERVER['REQUEST_METHOD'] === 'POST') {
  $data = file_get_contents("php://input");
  $jsonData = json_decode($data, true);
  $newItem = $jsonData['newItem'] ?? '';
}

if ($newItem) {
    $newItem = ucwords(strtolower($newItem));
    $sql = "INSERT INTO drawers_brand (brand_name) VALUES(?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $newItem);
    $stmt->execute();
    $stmt->close();

    $sql = "SELECT max(brand_id) as newItemID FROM drawers_brand";
    $result = $conn->query($sql);
    $rawdata = array();
    if ($row = $result->fetch_assoc()) {
        $rawdata[] = $row;
    }
    echo json_encode($rawdata, JSON_UNESCAPED_UNICODE);
}

$conn->close();
?>