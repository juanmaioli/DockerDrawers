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

$success_msg = "";
$error_msg = "";

// 1. Guardar Dólar Manualmente (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'save_dolar') {
    validate_csrf();
    $dolar_venta = (float)$_POST['dolar_venta'];
    $stmt = $conn->prepare("UPDATE drawers_config SET cfg_value = ? WHERE cfg_key = 'dolar_venta'");
    $stmt->bind_param("s", $dolar_venta);
    if ($stmt->execute()) {
        $success_msg = "Valor del dólar guardado correctamente.";
    } else {
        $error_msg = "Error al guardar el valor del dólar.";
    }
    $stmt->close();
}

// 2. Sincronizar Dólar desde DolarAPI (GET)
if (isset($_GET['action']) && $_GET['action'] === 'sync_dolar') {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://dolarapi.com/v1/dolares/blue");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
    $res = curl_exec($ch);
    curl_close($ch);
    
    if ($res) {
        $data = json_decode($res, true);
        if (isset($data['venta'])) {
            $dolar_venta = (float)$data['venta'];
            $stmt = $conn->prepare("UPDATE drawers_config SET cfg_value = ? WHERE cfg_key = 'dolar_venta'");
            $stmt->bind_param("s", $dolar_venta);
            if ($stmt->execute()) {
                $success_msg = "Dólar sincronizado correctamente: $" . number_format($dolar_venta, 2, ',', '.');
            } else {
                $error_msg = "Error al actualizar el dólar en la base de datos.";
            }
            $stmt->close();
        } else {
            $error_msg = "La API no devolvió el valor de venta.";
        }
    } else {
        $error_msg = "No se pudo conectar con DolarAPI.";
    }
}

// 3. Consultar Dólar Actual
$res_cfg = $conn->query("SELECT cfg_value FROM drawers_config WHERE cfg_key = 'dolar_venta'");
$row_cfg = $res_cfg->fetch_assoc();
$dolar_venta_actual = $row_cfg['cfg_value'] ?? '1000.00';

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

            <!-- Panel de Administración del Dólar Blue -->
            <article class="card mt-4 shadow-indigo-sm">
                <section class="card-header">
                    <h4 class="mb-0 text-indigo"><i class="fa-solid fa-dollar-sign me-2"></i>Cotización del Dólar Blue</h4>
                </section>
                <section class="card-body">
                    <?php if (!empty($success_msg)): ?>
                        <div class="alert alert-success"><i class="fa-solid fa-circle-check me-2"></i><?= h($success_msg) ?></div>
                    <?php endif; ?>
                    <?php if (!empty($error_msg)): ?>
                        <div class="alert alert-danger"><i class="fa-solid fa-triangle-exclamation me-2"></i><?= h($error_msg) ?></div>
                    <?php endif; ?>
                    
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <p class="fs-5 mb-0">Valor actual en sistema: <strong class="text-success">$<?= number_format((float)$dolar_venta_actual, 2, ',', '.') ?></strong></p>
                            <small class="text-muted">Utilizado para conversiones y cálculos de inventario.</small>
                        </div>
                        <div class="col-md-6 text-end">
                            <a href="admin.php?action=sync_dolar" class="btn btn-indigo"><i class="fa-solid fa-rotate me-1"></i>Sincronizar desde DolarAPI</a>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <form action="admin.php" method="post" class="row g-3 align-items-center mt-2">
                        <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                        <input type="hidden" name="action" value="save_dolar">
                        <div class="col-auto">
                            <label for="dolar_venta" class="col-form-label fw-bold text-indigo">Actualizar valor venta manualmente:</label>
                        </div>
                        <div class="col-auto">
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="0.01" class="form-control" id="dolar_venta" name="dolar_venta" value="<?= h($dolar_venta_actual) ?>" required>
                            </div>
                        </div>
                        <div class="col-auto">
                            <button type="submit" class="btn btn-success"><i class="fa-solid fa-floppy-disk me-1"></i>Guardar</button>
                        </div>
                    </form>
                </section>
            </article>
        </div> 
        <div class="col-md-1"></div>
    </div>
</div>
<!-- /Container -->
<?php include("footer.php"); ?>