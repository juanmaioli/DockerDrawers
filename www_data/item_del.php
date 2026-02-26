<?php

mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');

include("config.php");

$conn = new mysqli($db_server, $db_user,$db_pass,$db_name,$db_serverport);
mysqli_set_charset($conn,'utf8');
$item_id = $_GET['id'];

$sql = "UPDATE drawers_items SET item_delete = 1  WHERE item_id = " . $item_id;

$result = $conn->query($sql);
$conn->close();

header('Location: index.php');

?>