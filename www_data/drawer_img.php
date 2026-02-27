<?php
$logo_arch = 'images/drawers/minilogo.png';
if(file_exists("config.php"))
{include("config.php");} else {
  // header( "Location: install.php" );
}
include("func_img.php");

$drawer_id = $_POST['drawer_id'];
$logo_arch = 'images/drawers/minilogo.png';
$destination_dir = "images/drawers/";

$nuevo_nombre = process_image_upload('file-upload', $destination_dir, "drawer_" . $drawer_id, 500, 500, true, $logo_arch, true);

if ($nuevo_nombre) {
    $nuevo_nombre_full = str_replace(".jpg", "_full.jpg", $nuevo_nombre);
    $conn = get_db_connection();

    $sql = "UPDATE drawers_drawer SET drawer_image = ?, drawer_image_full = ? WHERE drawer_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $nuevo_nombre, $nuevo_nombre_full, $drawer_id);
    $stmt->execute();
    $stmt->close();
    $conn->close();
}

header('Location: drawer_view.php?id='.$drawer_id);
?>
