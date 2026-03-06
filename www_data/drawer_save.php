<?php
mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');

include("config.php");
validate_csrf();
$conn = get_db_connection();

$drawer_id_status = $_POST["drawer_id_status"];
$drawer_owner = $_POST["drawer_owner"];
$drawer_name = $_POST["drawer_name"];
$drawer_location = $_POST["drawer_location"];
$drawer_description = $_POST["drawer_description"] ?? $_POST["drawer_descriptinon"] ?? "";
$drawer_category = $_POST["drawer_category"];

$drawer_name = ucwords(strtolower($drawer_name));
$drawer_location = ucwords(strtolower($drawer_location));
$drawer_description  = ucwords(strtolower($drawer_description));

if($drawer_id_status == 0){
  $sql_Add = "INSERT INTO drawers_drawer (drawer_name, drawer_location, drawer_description, drawer_category, drawer_owner) VALUES(?, ?, ?, ?, ?)";
  $stmt = $conn->prepare($sql_Add);
  $stmt->bind_param("sssii", $drawer_name, $drawer_location, $drawer_description, $drawer_category, $drawer_owner);
  $stmt->execute();
  $drawer_id = $conn->insert_id;
  $stmt->close();
  header('Location: drawer_view.php?id='.$drawer_id);

}else{
  $sql_Update = "UPDATE drawers_drawer SET drawer_name=?, drawer_location = ?, drawer_description = ?, drawer_category = ? WHERE drawer_id = ?";
  $stmt = $conn->prepare($sql_Update);
  $stmt->bind_param("sssii", $drawer_name, $drawer_location, $drawer_description, $drawer_category, $drawer_id_status);
  $stmt->execute();
  $stmt->close();
  header('Location: drawer_view.php?id='.$drawer_id_status);
}
$conn->close();
?>