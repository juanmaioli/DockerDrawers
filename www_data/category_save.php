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

$category_id_status = $_POST["category_id_status"];
$category_name = $_POST["category_name"];
$category_color = $_POST["category_color"];

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