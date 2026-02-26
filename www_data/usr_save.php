<?php
mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');

include("config.php");

$conn = new mysqli($db_server, $db_user,$db_pass,$db_name,$db_serverport);
mysqli_set_charset($conn,'utf8');

$usr_id = $_POST['usr_id'];
$usr_name = $_POST['usr_name'];
$usr_lastname = $_POST['usr_lastname'];
$usr_email = $_POST['usr_email'];
$usr_email = strtolower($usr_email);

$usr_name = $conn->escape_string($usr_name);
$usr_lastname = $conn->escape_string($usr_lastname);
$usr_email = $conn->escape_string($usr_email);

if($usr_id !=0){
    $sql = "UPDATE " . $table_pre . "usr SET usr_name = '". $usr_name ."', 
    usr_lastname = '" . $usr_lastname ."', 
    usr_email = '" .$usr_email ."' WHERE usr_id=" . $usr_id;
}else{
$usr_pass = hash('sha256','123456789');
    $sql = "INSERT INTO " . $table_pre . "usr(usr_name,usr_lastname,usr_email,usr_pass)
    Values('". $usr_name ."','" . $usr_lastname ."', '" .$usr_email ."','".$usr_pass."')";
}
$result = $conn->query($sql);
header('Location: index.php');
?>