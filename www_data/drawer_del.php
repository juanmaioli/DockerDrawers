<?php
mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');

include("config.php");

$conn = get_db_connection();
$drawer_id = $_GET['id'];

$sql = "UPDATE drawers_drawer SET drawer_delete = 1 WHERE drawer_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $drawer_id);
$stmt->execute();
$stmt->close();
$conn->close();

header('Location: index.php');
?>