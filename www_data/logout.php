<?php
include("config.php");
$ip = $_SERVER['REMOTE_ADDR'];
$dateShow = new DateTime(date("Y-m-d H:i:s"));
$dateShow = $dateShow->format('Y-m-d H:i:s');
session_start();
  if( isset( $_COOKIE[$site_cookie])) {
    $datos = $_COOKIE[$site_cookie ];
    $datosCuenta = explode(":", $datos);
    $usuarioId = $datosCuenta[1];
  }
  $conn = new mysqli($db_server, $db_user,$db_pass,$db_name,$db_serverport);
  $sql = "INSERT INTO " . $table_pre . "session(sess_usr,sess_ip,sess_date,sess_action) values('". $usuarioId ."','" . $ip . "','" . $dateShow ."',2)";
  $result = $conn->query($sql);
  unset ($_SESSION["usuario"]);
  unset ($_SESSION["loggedin"]);
  unset ($_SESSION["usuario_id"]);
  session_destroy();
  unset($_COOKIE[$site_cookie]);
  header('Location: login.php');
?>
