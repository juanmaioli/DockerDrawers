<?php
$logo_arch = 'images/item/minilogo.png';
if(file_exists("config.php"))
{include("config.php");} else {
  // header( "Location: install.php" );
}
require_auth();
validate_csrf();

include("func_img.php");

$item_id = (int)($_POST['item_id'] ?? 0);
$item_drawer = (int)($_POST['item_drawer_img'] ?? 0);
$session_uid = (int)$_SESSION['usuario_id'];
$logo_arch = 'images/item/minilogo.png';
$destination_dir = "images/item/";

$nuevo_nombre = process_image_upload('file-upload', $destination_dir, "item_" . $item_id, 500, 500, true, $logo_arch);

if ($nuevo_nombre) {
    $conn = get_db_connection();

    $sql = "UPDATE drawers_items SET item_image = ? WHERE item_id = ? AND item_owner = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sii", $nuevo_nombre, $item_id, $session_uid);
    $stmt->execute();
    $stmt->close();
    $conn->close();
}

header('Location: item_view.php?id='.$item_id.'&did='. $item_drawer);
?>
