<?php
mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');

include("config.php");
require_auth();
validate_csrf();

$conn = get_db_connection();

$usr_id = (int)($_POST['usr_id_pass'] ?? 0);
$session_uid = (int)$_SESSION['usuario_id'];
$is_admin = ((int)($_SESSION['right'] ?? 0) === 1);

if ($usr_id <= 0 || ($usr_id !== $session_uid && !$is_admin)) {
    header('HTTP/1.1 403 Forbidden');
    die("Acceso denegado.");
}

$usr_pass_confirm = $_POST['usr_pass_confirm'] ?? '';
if (empty($usr_pass_confirm)) {
    header('Location: index.php');
    exit();
}

$hashed_pass = password_hash($usr_pass_confirm, PASSWORD_DEFAULT);

$sql = "UPDATE " . $table_pre . "usr SET usr_pass = ? WHERE usr_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $hashed_pass, $usr_id);
$stmt->execute();
$stmt->close();
$conn->close();

header('Location: index.php');
?>