<?php
mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');

include("config.php");
require_auth();
validate_csrf(); // Now only works via POST

$conn = get_db_connection();
$item_id = (int)($_POST['id'] ?? 0);
$session_uid = (int)$_SESSION['usuario_id'];

$reason_id = 1;
if (isset($_POST['reason_id'])) {
    $reason_id = (int)$_POST['reason_id'];
} elseif (isset($_POST['item_delete'])) {
    $reason_id = (int)$_POST['item_delete'];
}

$sql = "UPDATE drawers_items SET item_delete = ? WHERE item_id = ? AND item_owner = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iii", $reason_id, $item_id, $session_uid);
$stmt->execute();
$stmt->close();
$conn->close();

header('Location: index.php');
?>