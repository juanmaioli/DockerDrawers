<?php
mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');

include("config.php");

$conn = new mysqli($db_server, $db_user,$db_pass,$db_name,$db_serverport);
mysqli_set_charset($conn,'utf8');

$usr_id = $_POST['id'];


$sql = "UPDATE " . $table_pre . "usr SET usr_delete = '1', usr_pass='2339103de47b3d3fbe513f297af02635684e8bd301404ef46f6100e65f519215' WHERE usr_id=" . $usr_id;

$result = $conn->query($sql);
header('Location: index.php');

?>