<?php
include("config.php");
$ip = $_SERVER['REMOTE_ADDR'];
$dateShow = new DateTime(date("Y-m-d H:i:s"));
$dateShow = $dateShow->format('Y-m-d H:i:s');
session_start();

$usuarioId = 0;
if (isset($_COOKIE[$site_cookie])) {
    $datos = $_COOKIE[$site_cookie];
    $datosCuenta = explode(":", $datos);
    $usuarioId = (int)$datosCuenta[1];
} elseif (isset($_SESSION["usuario_id"])) {
    $usuarioId = $_SESSION["usuario_id"];
}

$conn = get_db_connection();
$sql = "INSERT INTO " . $table_pre . "session(sess_usr, sess_ip, sess_date, sess_action) VALUES(?, ?, ?, 2)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iss", $usuarioId, $ip, $dateShow);
$stmt->execute();
$stmt->close();
$conn->close();

// Clear session
$_SESSION = array();
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}
session_destroy();

// Clear "Remember Me" cookie properly
setcookie($site_cookie, '', time() - 3600, '/', $www_host);

header('Location: login.php');
exit();
?>
