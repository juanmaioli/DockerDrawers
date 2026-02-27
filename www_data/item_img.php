<?php
$logo_arch = 'images/item/minilogo.png';
if(file_exists("config.php"))
{include("config.php");} else {
  // header( "Location: install.php" );
}
include("func_img.php");

$item_id = $_POST['item_id'];
$item_drawer = $_POST['item_drawer_img'];
$logo_arch = 'images/item/minilogo.png';
$destination_dir = "images/item/";

$nuevo_nombre = process_image_upload('file-upload', $destination_dir, "item_" . $item_id, 500, 500, true, $logo_arch);

if ($nuevo_nombre) {
    $conn = new mysqli($db_server, $db_user, $db_pass, $db_name, $db_serverport);
    mysqli_set_charset($conn, 'utf8');

    $sql = "UPDATE drawers_items SET item_image = ? WHERE item_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $nuevo_nombre, $item_id);
    $stmt->execute();
    $stmt->close();
    $conn->close();
}

header('Location: item_view.php?id='.$item_id.'&did='. $item_drawer);
?>
