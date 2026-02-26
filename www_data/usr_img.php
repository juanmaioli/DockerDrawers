<?php
$logo_arch = 'images/minilogo.png';
if(file_exists("config.php"))
{include("config.php");} else {
  // header( "Location: install.php" );
}
include("func_img.php");

$nombre = $_FILES['file-upload']['name'];
$usr_id = $_POST['image_usr_id'];
$nombrer = "images/usr/" . $usr_id . "-" . strtolower($nombre);

$ruta = $nombrer ;// $_FILES['imagen']['name'];
$resultado = @move_uploaded_file($_FILES["file-upload"]["tmp_name"], $ruta);

if (!empty($resultado)){
    mb_internal_encoding('UTF-8');
    mb_http_output('UTF-8');
    $conn = new mysqli($db_server, $db_user,$db_pass,$db_name,$db_serverport);
    mysqli_set_charset($conn,'utf8');
    $sql = "UPDATE " . $table_pre . "usr set usr_image = 'images/usr/" . $usr_id . "-" . strtolower($nombre) . "' WHERE usr_id=" . $usr_id;

    $result = $conn->query($sql);
    $conn->close();

    crop_image_square($nombrer);
    resize_image($nombrer,750,750);
    // add_logo_image($nombrer,$logo_arch , $nombrer);
}
header('Location: index.php');
?>
