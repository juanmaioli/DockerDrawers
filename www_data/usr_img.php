<?php
$logo_arch = 'images/minilogo.png';
if(file_exists("config.php"))
{include("config.php");} else {
  // header( "Location: install.php" );
}
include("func_img.php");

$usr_id = $_POST['image_usr_id'];
$destination_dir = "images/usr/";

$nuevo_nombre = process_image_upload('file-upload', $destination_dir, "usr_" . $usr_id, 750, 750, false);

if ($nuevo_nombre) {
    $conn = get_db_connection();

    $sql = "UPDATE " . $table_pre . "usr SET usr_image = ? WHERE usr_id = ?";
    $stmt = $conn->prepare($sql);
    $full_path = "images/usr/" . $nuevo_nombre;
    $stmt->bind_param("si", $full_path, $usr_id);
    $stmt->execute();
    $stmt->close();
    $conn->close();
}

header('Location: index.php');
?>
