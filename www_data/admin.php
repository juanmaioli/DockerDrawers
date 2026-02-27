<?php 
include("head.php");

// Verificación dinámica de permisos (Admin = 1)
if ($usr_right != 1) {
    header('Location: index.php');
    exit();
}

mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');
$conn = get_db_connection();

$sql = "SELECT * FROM " . $table_pre . "usr WHERE usr_delete = 0 ORDER BY usr_lastname ASC";
$result = $conn->query($sql);

if (mysqli_num_rows($result) == true) {
    $tabla_usr = "<table class='table'>" ;
    $tabla_usr .= "<thead><tr class='text-center'><th Colspan=2>Usuario</th><th>Email</th><th>Editar</th><th>Borrar</th></tr></thead><tbody>";
  while($row = $result->fetch_assoc())
    {
      $usr_id = $row["usr_id"];
      $usr_name = $row["usr_name"];
      $usr_lastname = $row["usr_lastname"];
      $usr_email = $row["usr_email"];
      $usr_image = $row["usr_image"];
      $usr_pass = $row["usr_pass"];
      $usr_token = $row["usr_token"];
      $usr_timezone = $row["usr_timezone"];
      $usr_right = $row["usr_right"];
      $usr_image = "<img class='rounded-circle border border-primary' src=". $usr_image . " width=50px  alt=''>";
      $tabla_usr .="<tr><td class='text-center'>" . $usr_image . "</td>
      <td>" . $usr_lastname . " " . $usr_name  . "</td>
      <td>" . $usr_email . "</td>
      <td class='text-center'>
      
        <form action='usr_edit.php' method='post'>
        <input type='hidden' name='id' id='id' value='". $usr_id."'>
        <button type='submit' id='btnGuardar' class='btn btn-outline-primary btn-sm'><i class='fas fa-edit'></i></button>
        </form></td><td class='text-center'>
        <form action='usr_del.php' method='post'>
        <input type='hidden' name='id' id='id' value='". $usr_id."'>
        <button type='submit' id='btnGuardar' class='btn btn-outline-danger btn-sm'><i class='fas fa-trash'></i></button>
        </form></td></tr>";
    }
}
$tabla_usr .= "</tbody></table>";

$conn->close();
?>
<!-- Container -->
<div class="container-fluid">
    <div class="row">
        <div class="col-md-1"></div>
        <div class="col-md-10">
            <span class="float-right m-2"><a href="usr_add.php" class='btn btn-outline-primary btn-lg'><i class='far fa-plus-square'></i>&nbsp;Agregar Usuario</a></span>
            <?=$tabla_usr?>
        </div> 
        <div class="col-md-1"></div>
    </div>
</div>
<!-- /Container -->
<?php include("footer.php"); ?>