<?php

mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');

include("config.php");

$conn = new mysqli($db_server, $db_user,$db_pass,$db_name,$db_serverport);
mysqli_set_charset($conn,'utf8');
$drawer_id = $_GET['id'];

$sql = "UPDATE drawers_drawer SET drawer_delete = 1  WHERE drawer_id = " . $drawer_id;

$result = $conn->query($sql);
$conn->close();

header('Location: index.php');

?>