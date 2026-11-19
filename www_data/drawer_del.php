<?php
mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');

include("config.php");
require_auth();
validate_csrf(); // Now only works via POST

$conn = get_db_connection();
$drawer_id = (int)($_POST['id'] ?? 0);
$session_uid = (int)$_SESSION['usuario_id'];

$sql = "UPDATE drawers_drawer SET drawer_delete = 1 WHERE drawer_id = ? AND drawer_owner = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $drawer_id, $session_uid);
$stmt->execute();
$stmt->close();
$conn->close();

header('Location: index.php');
?>