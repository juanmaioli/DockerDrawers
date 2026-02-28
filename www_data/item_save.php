<?php
mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');

include("config.php");
session_start();

// CSRF Verification
if (empty($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    die("CSRF token validation failed.");
}

$conn = get_db_connection();

$item_id_status = $_POST["item_id_status"];
$item_owner = $_POST["item_owner"];
$item_name = $_POST["item_name"];
$item_amount = $_POST["item_amount"];
$item_description = $_POST["item_description"] ?? $_POST["item_descriptinon"] ?? "";
$item_category = $_POST["item_category"];
$item_drawer = $_POST["item_drawer"];
$item_brand = $_POST["item_brand"];
$item_model = $_POST["item_model"];

if(empty($_POST['item_price'])){
  $item_price = 0;
}else{
  $item_price =$_POST["item_price"];
}

$item_name = ucwords(strtolower($item_name));
$item_description  = ucwords(strtolower($item_description));

if($item_id_status == 0){
  $sql_Add = "INSERT INTO drawers_items (item_name, item_amount, item_description, item_category, item_owner, item_drawer, item_price, item_brand, item_model) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?)";
  $stmt = $conn->prepare($sql_Add);
  $stmt->bind_param("sisiiidis", $item_name, $item_amount, $item_description, $item_category, $item_owner, $item_drawer, $item_price, $item_brand, $item_model);
  $stmt->execute();
  $item_id = $conn->insert_id;
  $stmt->close();
  header('Location: item_view.php?id='.$item_id.'&did='. $item_drawer);

}else{
  $sql_Update = "UPDATE drawers_items SET item_name=?, item_amount = ?, item_description = ?, item_category = ?, item_drawer = ?, item_price = ?, item_brand = ?, item_model = ? WHERE item_id = ?";
  $stmt = $conn->prepare($sql_Update);
  $stmt->bind_param("sisiiidsi", $item_name, $item_amount, $item_description, $item_category, $item_drawer, $item_price, $item_brand, $item_model, $item_id_status);
  $stmt->execute();
  $stmt->close();
  header('Location: item_view.php?id='.$item_id_status.'&did='. $item_drawer);
}
$conn->close();
?>