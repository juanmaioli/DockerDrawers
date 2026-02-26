<?php
$logo_arch = 'images/drawers/minilogo.png';
if(file_exists("config.php"))
{include("config.php");} else {
  // header( "Location: install.php" );
}
include("func_img.php");

$nombre = $_FILES['file-upload']['name'];
$drawer_id = $_POST['drawer_id'];
$nuevo_nombre = hash('sha256', $nombre ).".jpg";
$nuevo_nombre_full = hash('sha256', $nombre )."_full.jpg";
$ruta = "images/drawers/" . $nuevo_nombre;
$ruta_full = "images/drawers/" . $nuevo_nombre_full;

// echo $nombre . "<br>";
// echo $nuevo_nombre . "<br>";
// echo $nuevo_nombre_full  . "<br>";
// echo $drawer_id . "<br>";
// echo $ruta . "<br>";
// echo $ruta_full . "<br>";

$resultado = @move_uploaded_file($_FILES["file-upload"]["tmp_name"], $ruta);

// echo $resultado;

if (!empty($resultado)){
    copy($ruta, $ruta_full);
    mb_internal_encoding('UTF-8');
    mb_http_output('UTF-8');
    $conn = new mysqli($db_server, $db_user,$db_pass,$db_name,$db_serverport);
    mysqli_set_charset($conn,'utf8');
    $sql = "UPDATE drawers_drawer set drawer_image = '" . $nuevo_nombre . "', drawer_image_full = '". $nuevo_nombre_full ."'  WHERE drawer_id=" . $drawer_id;
    // echo $sql;
    $result = $conn->query($sql);
    $conn->close();
    crop_image_square($ruta);
    resize_image($ruta,500,500);
    add_logo_image($ruta,$logo_arch , $ruta);
}
header('Location: drawer_view.php?id='.$drawer_id);
?>
