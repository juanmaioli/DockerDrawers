<?php
mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');

include("config.php");

$conn = new mysqli($db_server, $db_user,$db_pass,$db_name,$db_serverport);
mysqli_set_charset($conn,'utf8');

// Verificar si se ha enviado una solicitud POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Leer el cuerpo de la solicitud
  $data = file_get_contents("php://input");

  // Decodificar los datos JSON
  $jsonData = json_decode($data, true);

  // Acceder al valor enviado desde JavaScript
  $newItem = $jsonData['newItem'];

  // Hacer algo con $newItem, por ejemplo, guardarlo en una base de datos
  // O devolver una respuesta al cliente
  // echo "El nuevo item es: " . $newItem;
} else {
  // Manejar la solicitud de otro método (GET, PUT, DELETE, etc.)
  // Devolver un mensaje de error o hacer algo más según sea necesario
  // echo "Solicitud no válida";
}

$newItem =  ucwords($newItem);
$sql = "INSERT INTO drawers_brand (brand_name) VALUES('$newItem')";
$result = $conn->query($sql);
$sql = "SELECT max(brand_id) as newItemID FROM drawers_brand";

if (strlen($sql) > 5){
  $result = $conn->query($sql);
  $dataCount = mysqli_num_rows($result);
  $rawdata = array();
$i=0;
  while($row = mysqli_fetch_assoc($result))
  {
    $rawdata[$i] = $row;
    $i++;
  }
echo json_encode($rawdata,JSON_UNESCAPED_UNICODE);
$conn->close();
}