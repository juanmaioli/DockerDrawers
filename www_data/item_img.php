<?php
$logo_arch = 'images/item/minilogo.png';
if(file_exists("config.php"))
{include("config.php");} else {
  // header( "Location: install.php" );
}
include("func_img.php");

$nombre = $_FILES['file-upload']['name'];
$item_id = $_POST['item_id'];
$item_drawer = $_POST['item_drawer_img'];
$nuevo_nombre = hash('sha256', $nombre ).".jpg";
$ruta = "images/item/" . $nuevo_nombre;

// echo $nombre . "<br>";
// echo $nuevo_nombre . "<br>";
// echo $nuevo_nombre_full  . "<br>";
// echo $item_id . "<br>";
// echo $ruta . "<br>";
// echo $ruta_full . "<br>";

$resultado = @move_uploaded_file($_FILES["file-upload"]["tmp_name"], $ruta);

// echo $resultado;

if (!empty($resultado)){
    mb_internal_encoding('UTF-8');
    mb_http_output('UTF-8');
    $conn = new mysqli($db_server, $db_user,$db_pass,$db_name,$db_serverport);
    mysqli_set_charset($conn,'utf8');
    $sql = "UPDATE drawers_items SET item_image = '$nuevo_nombre' WHERE item_id=" . $item_id;
    echo $sql;
    $result = $conn->query($sql);
    $conn->close();
    crop_image_square($ruta);
    resize_image($ruta,500,500);
    add_logo_image($ruta,$logo_arch , $ruta);
}
header('Location: item_view.php?id='.$item_id.'&did='. $item_drawer);
?>
