<?php
mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');

include("config.php");

$conn = new mysqli($db_server, $db_user,$db_pass,$db_name,$db_serverport);
mysqli_set_charset($conn,'utf8');

$category_id_status = $_POST["category_id_status"];
$category_name = $_POST["category_name"];
$category_color = $_POST["category_color"];

$category_id_status = $conn->escape_string($category_id_status);
$category_name = $conn->escape_string($category_name);
$category_color = $conn->escape_string($category_color);

$category_name = ucwords(strtolower($category_name));

if($category_id_status == 0){
  $sql_Add = "INSERT INTO drawers_category (category_name, category_color) VALUES (?, ?)";
  $stmt = $conn->prepare($sql_Add);
  $stmt->bind_param("ss", $category_name, $category_color);
  $stmt->execute();
  $category_id = $conn->insert_id;
  $stmt->close();
  header('Location: category_view.php?id='.$category_id);

}else{
  $sql_Update = "UPDATE drawers_category SET category_name = ?, category_color = ? WHERE category_id = ?";
  $stmt = $conn->prepare($sql_Update);
  $stmt->bind_param("ssi", $category_name, $category_color, $category_id_status);
  $stmt->execute();
  $stmt->close();
  header('Location: category_view.php?id='.$category_id_status);
}
$conn->close();
?>