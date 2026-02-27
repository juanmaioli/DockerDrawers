<?php
mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');

include("config.php");

$conn = get_db_connection();

// Verificar si se ha enviado una solicitud POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Leer el cuerpo de la solicitud
  $data = file_get_contents("php://input");

  // Decodificar los datos JSON
  $jsonData = json_decode($data, true);

  // Acceder al valor enviado desde JavaScript
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