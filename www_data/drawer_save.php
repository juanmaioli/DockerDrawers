<?php
mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');

include("config.php");

$conn = new mysqli($db_server, $db_user,$db_pass,$db_name,$db_serverport);
mysqli_set_charset($conn,'utf8');

$drawer_id_status = $_POST["drawer_id_status"];
$drawer_owner = $_POST["drawer_owner"];
$drawer_name = $_POST["drawer_name"];
$drawer_location = $_POST["drawer_location"];
$drawer_descriptinon = $_POST["drawer_descriptinon"];
$drawer_category = $_POST["drawer_category"];


$drawer_id_status = $conn->escape_string($drawer_id_status );
$drawer_owner = $conn->escape_string($drawer_owner );
$drawer_name = $conn->escape_string($drawer_name);
$drawer_location = $conn->escape_string($drawer_location);
$drawer_descriptinon = $conn->escape_string($drawer_descriptinon);
$drawer_category = $conn->escape_string($drawer_category);

$drawer_name = ucwords(strtolower($drawer_name));
$drawer_location = ucwords(strtolower($drawer_location));
$drawer_descriptinon  = ucwords(strtolower($drawer_descriptinon));

// echo "drawer_id_status: " . $drawer_id_status . '<br>';
// echo "drawer_name: " . $drawer_name . '<br>';
// echo "drawer_location: " . $drawer_location . '<br>';
// echo "drawer_descriptinon: " . $drawer_descriptinon . '<br>';
// echo "drawer_category: " . $drawer_category . '<br>';

$sql_Update = "UPDATE drawers_drawer SET drawer_name='$drawer_name', drawer_location = '$drawer_location', drawer_descriptinon = '$drawer_descriptinon',drawer_category = $drawer_category WHERE drawer_id = " . $drawer_id_status;
// echo $sql_Update;
$sql_Add = "INSERT INTO drawers_drawer (drawer_name, drawer_location, drawer_descriptinon,drawer_category,drawer_owner)
VALUES('$drawer_name', '$drawer_location', '$drawer_descriptinon',$drawer_category,$drawer_owner)";
// echo $sql_Add;


if($drawer_id_status == 0){
  $result = $conn->query($sql_Add);
  $sql_last = "SELECT max(drawer_id) as drawer_id FROM drawers_drawer";
  $result = $conn->query($sql_last);
  if (mysqli_num_rows($result) == true) {
      while($row = $result->fetch_assoc())
      {
      $drawer_id = $row["drawer_id"];
      }
  }
  header('Location: drawer_view.php?id='.$drawer_id);

}else{
  $result = $conn->query($sql_Update);
  header('Location: drawer_view.php?id='.$drawer_id_status);
}
?>