<?php
mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');

include("config.php");
validate_csrf(); // Now only works via POST

$conn = get_db_connection();
$item_id = (int)$_POST['id'];

$reason_id = 1;
if (isset($_POST['reason_id']) && (int)$_POST['reason_id'] > 0) {
    $reason_id = (int)$_POST['reason_id'];
} elseif (isset($_POST['item_delete']) && (int)$_POST['item_delete'] > 0) {
    $reason_id = (int)$_POST['item_delete'];
}

$sql = "UPDATE drawers_items SET item_delete = ? WHERE item_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $reason_id, $item_id);
$stmt->execute();
$stmt->close();
$conn->close();

header('Location: index.php');
?>