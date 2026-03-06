<?php
mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');

include("config.php");
validate_csrf();
$conn = get_db_connection();

$dateShow = new DateTime(date("Y-m-d H:i:s"));
$dateShow = $dateShow->format('Y-m-d H:i:s');

// Recibir datos JSON desde la solicitud POST
$jsonData = file_get_contents("php://input");

// Decodificar el JSON a un array de PHP
$data = json_decode($jsonData, true);

// Verificar si la decodificación fue exitosa
if ($data !== null) {
  foreach ($data as $producto) {
    // Acceder a los datos de cada producto
    $titulo = $producto['titulo'];
    $link = $producto['link'];
    $precio = $producto['precio'];
    $imagen = $producto['imagen'];
    $mlaID = $producto['mlaID'];

    $sql_check = "SELECT count(*) AS total FROM drawers_fav WHERE fav_mla = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("s", $mlaID);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();
    $row_check = $result_check->fetch_assoc();
    $existe = $row_check["total"];
    $stmt_check->close();

    if($existe == 0){
      $sql = "INSERT INTO drawers_fav (fav_title, fav_link, fav_img, fav_price, fav_mla, fav_desc, fav_date) VALUES(?, ?, ?, ?, ?, ?, ?)";
      $stmt = $conn->prepare($sql);
      $stmt->bind_param("sssdsss", $titulo, $link, $imagen, $precio, $mlaID, $titulo, $dateShow);
    }else{
      $sql = "UPDATE drawers_fav SET fav_desc = ?, fav_link = ?, fav_img = ?, fav_price = ?, fav_date = ? WHERE fav_mla = ?";
      $stmt = $conn->prepare($sql);
      $stmt->bind_param("sss dss", $titulo, $link, $imagen, $precio, $dateShow, $mlaID);
    }
    $stmt->execute();
    $stmt->close();
  }
}

$response = array(
  'status' => 'ok',
  'message' => 'Datos recibidos correctamente'
);

// Enviar la respuesta en formato JSON
header('Content-Type: application/json');
echo json_encode($response);
$conn->close();
?>