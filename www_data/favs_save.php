<?php
mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');

include("config.php");

$conn = new mysqli($db_server, $db_user,$db_pass,$db_name,$db_serverport);
mysqli_set_charset($conn,'utf8');

$dateShow = new DateTime(date("Y-m-d H:i:s"));
$dateShow = $dateShow->format('Y-m-d H:i:s');

// Recibir datos JSON desde la solicitud POST
$jsonData = file_get_contents("php://input");

// Decodificar el JSON a un array de PHP
$data = json_decode($jsonData, true);
// $newArticles = array();
// $oldArticles = array();
// Verificar si la decodificación fue exitosa
if ($data !== null) {
  foreach ($data as $producto) {
    // Acceder a los datos de cada producto
    $titulo = $producto['titulo'];
    $link = $producto['link'];
    $precio = $producto['precio'];
    $imagen = $producto['imagen'];
    $mlaID = $producto['mlaID'];
    // array_push($newArticles,$mlaID);
    $sql = "SELECT count(*) AS total FROM drawers_fav WHERE fav_mla = '$mlaID'";
    $result = $conn->query($sql);
    if (mysqli_num_rows($result) == true) {
      while($row = $result->fetch_assoc())
      {
        $existe = $row["total"];
      }
    }
    if($existe == 0){
      $sql = "INSERT INTO drawers_fav (fav_title, fav_link, fav_img, fav_price,fav_mla,fav_desc,fav_date) VALUES('$titulo', '$link', '$imagen', $precio,'$mlaID','$titulo','$dateShow')";
    }else{
      $sql = "UPDATE drawers_fav SET fav_desc = '$titulo',fav_link = '$link', fav_img = '$imagen', fav_price = $precio, fav_date = '$dateShow' WHERE fav_mla = '$mlaID'";
      // $sql = "UPDATE drawers_fav SET fav_link = '$link', fav_img = '$imagen', fav_price = $precio WHERE fav_mla = '$mlaID'";
    }
    $result = $conn->query($sql);
  }
}
// $newArticlesList = implode("','", $newArticles) . "'";
// $sql = "SELECT * FROM drawers_fav WHERE fav_mla NOT IN ($newArticlesList)";
// $result = $conn->query($sql);
// if (mysqli_num_rows($result) == true) {
//   while($row = $result->fetch_assoc())
//   {
//     $mlaID = $row["fav_mla"];
//     array_push($oldArticles,$mlaID);
//   }
// }

$response = array(
  'status' => 'ok',
  'message' => 'Datos recibidos correctamente',
  'data' => $sql
);

// Enviar la respuesta en formato JSON
header('Content-Type: application/json');
echo json_encode($response);
$conn->close();
?>

