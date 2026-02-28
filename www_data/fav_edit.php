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

$bookmarkID = $_POST["bookmarkID"];
$bookmarkTitle = $_POST["bookmarkTitle"];

$sql_Update = "UPDATE drawers_fav SET fav_title = ? WHERE fav_mla = ?";
$stmt = $conn->prepare($sql_Update);
$stmt->bind_param("ss", $bookmarkTitle, $bookmarkID);
$stmt->execute();
$stmt->close();
$conn->close();

header('Location: favs.php');
?>