<?php
mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');

include("config.php");

$conn = new mysqli($db_server, $db_user,$db_pass,$db_name,$db_serverport);
mysqli_set_charset($conn,'utf8');

$usr_id = $_POST['usr_id_pass'];
$usr_pass_confirm = $_POST['usr_pass_confirm'];

$usr_pass_confirm = $conn->escape_string($usr_pass_confirm);
$usr_pass_confirm =  hash('sha256', $usr_pass_confirm );

$sql = "UPDATE " . $table_pre . "usr SET usr_pass = '". $usr_pass_confirm ."' WHERE usr_id=" . $usr_id;

$result = $conn->query($sql);
header('Location: index.php');

?>