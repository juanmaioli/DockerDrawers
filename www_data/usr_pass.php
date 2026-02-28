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

$usr_id = $_POST['usr_id_pass'];
$usr_pass_confirm = $_POST['usr_pass_confirm'];

$hashed_pass = password_hash($usr_pass_confirm, PASSWORD_DEFAULT);

$sql = "UPDATE " . $table_pre . "usr SET usr_pass = ? WHERE usr_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $hashed_pass, $usr_id);
$stmt->execute();
$stmt->close();
$conn->close();

header('Location: index.php');
?>