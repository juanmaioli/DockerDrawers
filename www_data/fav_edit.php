<?php
mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');

include("config.php");

$conn = new mysqli($db_server, $db_user,$db_pass,$db_name,$db_serverport);
mysqli_set_charset($conn,'utf8');

$bookmarkID = $_POST["bookmarkID"];
$bookmarkTitle = $_POST["bookmarkTitle"];

$bookmarkTitle = $conn->escape_string($bookmarkTitle);
$bookmarkID  = $conn->escape_string($bookmarkID  );

// echo "bookmarkTitle: " . $bookmarkTitle . '<br>';
// echo "bookmarkID: " . $bookmarkID . '<br>';


$sql_Update = "UPDATE drawers_fav SET fav_title='$bookmarkTitle' WHERE fav_mla = '$bookmarkID'";
// echo $sql_Update;

  $result = $conn->query($sql_Update);
  header('Location: favs.php');
?>