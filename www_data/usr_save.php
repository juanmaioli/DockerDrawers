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

$usr_id = $_POST['usr_id'];
$usr_name = $_POST['usr_name'];
$usr_lastname = $_POST['usr_lastname'];
$usr_email = $_POST['usr_email'];
$usr_email = strtolower($usr_email);

if($usr_id != 0){
    $sql = "UPDATE " . $table_pre . "usr SET usr_name = ?, usr_lastname = ?, usr_email = ? WHERE usr_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $usr_name, $usr_lastname, $usr_email, $usr_id);
    $stmt->execute();
    $stmt->close();
}else{
    $usr_pass = password_hash('123456789', PASSWORD_DEFAULT);
    $sql = "INSERT INTO " . $table_pre . "usr(usr_name, usr_lastname, usr_email, usr_pass) VALUES(?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $usr_name, $usr_lastname, $usr_email, $usr_pass);
    $stmt->execute();
    $stmt->close();
}
$conn->close();
header('Location: index.php');
?>