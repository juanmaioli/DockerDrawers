<?php

mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');

include("config.php");

$conn = get_db_connection();
$item_id = $_GET['id'];

$sql = "UPDATE drawers_items SET item_delete = 1 WHERE item_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $item_id);
$stmt->execute();
$stmt->close();
$conn->close();

header('Location: index.php');

?>