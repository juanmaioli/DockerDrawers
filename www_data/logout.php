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
  $conn = get_db_connection();
  $sql = "INSERT INTO " . $table_pre . "session(sess_usr, sess_ip, sess_date, sess_action) VALUES(?, ?, ?, 2)";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("iss", $usuarioId, $ip, $dateShow);
  $stmt->execute();
  $stmt->close();
  $conn->close();
  unset ($_SESSION["usuario"]);
  unset ($_SESSION["loggedin"]);
  unset ($_SESSION["usuario_id"]);
  session_destroy();
  unset($_COOKIE[$site_cookie]);
  header('Location: login.php');
?>
