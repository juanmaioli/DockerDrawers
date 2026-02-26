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


echo "category_name: $category_name <br>";
echo "category_color: $category_color <br>";
echo "category_id_status: $category_id_status <br>";

$sql_Update = "UPDATE drawers_category SET category_name = '$category_name', category_color = '$category_color' WHERE category_id = $category_id_status";
// echo $sql_Update . "<br>";
$sql_Add = "INSERT INTO drawers_category (category_name, category_color) VALUES ('$category_name', '$category_color')";
// echo $sql_Add . "<br>";


if($category_id_status== 0){
  $result = $conn->query($sql_Add);
  $sql_last = "SELECT max(category_id) as category_id FROM drawers_category";
  $result = $conn->query($sql_last);
  if (mysqli_num_rows($result) == true) {
      while($row = $result->fetch_assoc())
      {
      $category_id = $row["category_id"];
      }
  }
  header('Location: category_view.php?id='.$category_id);

}else{
  $result = $conn->query($sql_Update);
  echo 'Location: category_view.php?id='.$category_id_status;
  sleep(1);
  header('Location: category_view.php?id='.$category_id_status);
}
?>