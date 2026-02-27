<?php
mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');

include("config.php");

$conn = get_db_connection();

$usr_id = $_POST['id'];

// Marcar como borrado y resetear contraseña por seguridad
$sql = "UPDATE " . $table_pre . "usr SET usr_delete = 1, usr_pass = 'deleted' WHERE usr_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usr_id);
$stmt->execute();
$stmt->close();
$conn->close();

header('Location: index.php');
?>