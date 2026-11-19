<?php
mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');

include("config.php");
require_admin();
validate_csrf();

$conn = get_db_connection();

$usr_id = (int)($_POST['id'] ?? 0);
if ($usr_id > 0 && $usr_id !== (int)$_SESSION['usuario_id']) {
    // Marcar como borrado y resetear contraseña y token por seguridad
    $sql = "UPDATE " . $table_pre . "usr SET usr_delete = 1, usr_pass = 'deleted', usr_token = NULL WHERE usr_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $usr_id);
    $stmt->execute();
    $stmt->close();
}
$conn->close();

header('Location: admin.php');
?>