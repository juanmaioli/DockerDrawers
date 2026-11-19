<?php
$logo_arch = 'images/minilogo.png';
if(file_exists("config.php"))
{include("config.php");} else {
  // header( "Location: install.php" );
}
require_auth();
validate_csrf();

include("func_img.php");

$usr_id = (int)($_POST['image_usr_id'] ?? 0);
$session_uid = (int)$_SESSION['usuario_id'];
$is_admin = ((int)($_SESSION['right'] ?? 0) === 1);

if ($usr_id <= 0 || ($usr_id !== $session_uid && !$is_admin)) {
    header('HTTP/1.1 403 Forbidden');
    die("Acceso denegado.");
}
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
