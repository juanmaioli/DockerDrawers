<?php
ob_start();
include("head.php");
?>
<style>
  html, body {
    max-width: 100%;
    overflow-x: hidden;
  }
  .table-responsive {
    overflow-x: hidden !important;
  }
  #comprasTable th, #comprasTable td {
    white-space: normal !important;
    word-wrap: break-word;
    word-break: break-word;
  }
</style>
<?php
$conn = get_db_connection();

$error_msg = "";
$success_msg = "";

// Verificar si las credenciales están configuradas
$config_ready = ($ml_client_id !== 'TU_CLIENT_ID' && $ml_client_secret !== 'TU_CLIENT_SECRET');

// 1. Desconectar Cuenta
if (isset($_GET['action']) && $_GET['action'] === 'disconnect') {
    $stmt = $conn->prepare("DELETE FROM drawers_ml_auth WHERE usr_id = ?");
    $stmt->bind_param("i", $usuarioId);
    $stmt->execute();
    $stmt->close();
    header("Location: compras.php");
    exit();
}

// 2. Intercambio de código OAuth
if ($config_ready && isset($_GET['code'])) {
    $code = $_GET['code'];
    
    $url = "https://api.mercadolibre.com/oauth/token";
    $params = [
        "grant_type" => "authorization_code",
        "client_id" => $ml_client_id,
        "client_secret" => $ml_client_secret,
        "code" => $code,
        "redirect_uri" => $ml_redirect_uri
    ];
    
    $response = ml_curl_post($url, $params);
    
    if (isset($response['access_token'])) {
        $access_token = $response['access_token'];
        $refresh_token = $response['refresh_token'];
        $expires_in = $response['expires_in'];
        $ml_user_id = $response['user_id'];
        $expires_at = date("Y-m-d H:i:s", time() + $expires_in);
        
        // Limpiar tokens anteriores del mismo usuario
        $stmt_del = $conn->prepare("DELETE FROM drawers_ml_auth WHERE usr_id = ?");
        $stmt_del->bind_param("i", $usuarioId);
        $stmt_del->execute();
        $stmt_del->close();
        
        // Guardar nuevos tokens
        $stmt_ins = $conn->prepare("INSERT INTO drawers_ml_auth (usr_id, access_token, refresh_token, expires_at, ml_user_id) VALUES (?, ?, ?, ?, ?)");
        $stmt_ins->bind_param("issss", $usuarioId, $access_token, $refresh_token, $expires_at, $ml_user_id);
        $stmt_ins->execute();
        $stmt_ins->close();
        
        header("Location: compras.php");
        exit();
    } else {
        $error_msg = "Error al autenticar: " . ($response['message'] ?? 'Respuesta inválida de Mercado Libre.');
    }
}

// 3. Consultar tokens actuales del usuario
$auth = null;
$stmt = $conn->prepare("SELECT * FROM drawers_ml_auth WHERE usr_id = ?");
$stmt->bind_param("i", $usuarioId);
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows > 0) {
    $auth = $res->fetch_assoc();
}
$stmt->close();

$connected = false;
$orders = [];
$items_details = [];

// 4. Validar expiración y refrescar token si es necesario
if ($auth && $config_ready) {
    $access_token = $auth['access_token'];
    $refresh_token = $auth['refresh_token'];
    $expires_at = strtotime($auth['expires_at']);
    $ml_user_id = $auth['ml_user_id'];
    
    if (time() >= $expires_at) {
        $url = "https://api.mercadolibre.com/oauth/token";
        $params = [
            "grant_type" => "refresh_token",
            "client_id" => $ml_client_id,
            "client_secret" => $ml_client_secret,
            "refresh_token" => $refresh_token
        ];
        $response = ml_curl_post($url, $params);
        
        if (isset($response['access_token'])) {
            $access_token = $response['access_token'];
            $refresh_token = $response['refresh_token'];
            $expires_in = $response['expires_in'];
            $expires_at_str = date("Y-m-d H:i:s", time() + $expires_in);
            
            $stmt_upd = $conn->prepare("UPDATE drawers_ml_auth SET access_token = ?, refresh_token = ?, expires_at = ? WHERE usr_id = ?");
            $stmt_upd->bind_param("sssi", $access_token, $refresh_token, $expires_at_str, $usuarioId);
            $stmt_upd->execute();
            $stmt_upd->close();
            
            $connected = true;
        } else {
            // Eliminar registro inválido
            $stmt_del = $conn->prepare("DELETE FROM drawers_ml_auth WHERE usr_id = ?");
            $stmt_del->bind_param("i", $usuarioId);
            $stmt_del->execute();
            $stmt_del->close();
            $auth = null;
            $error_msg = "La sesión de Mercado Libre expiró. Por favor, conectate nuevamente.";
        }
    } else {
        $connected = true;
    }
    
    // 5. Obtener compras
    if ($connected) {
        $offset = 0;
        $limit = 50;
        $has_more = true;
        
        while ($has_more) {
            $orders_res = ml_curl_get("https://api.mercadolibre.com/orders/search?buyer=$ml_user_id&limit=$limit&offset=$offset", $access_token);
            
            if (isset($orders_res['results']) && !empty($orders_res['results'])) {
                $orders = array_merge($orders, $orders_res['results']);
                $offset += $limit;
                
                $total = $orders_res['paging']['total'] ?? 0;
                if ($offset >= $total) {
                    $has_more = false;
                }
            } else {
                if ($offset === 0 && isset($orders_res['message'])) {
                    $error_msg = "No se pudieron obtener las compras: " . $orders_res['message'];
                }
                $has_more = false;
            }
        }
    }
}

$conn->close();

// Auxiliares Curl
function ml_curl_post($url, $params) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/x-www-form-urlencoded',
        'Accept: application/json'
    ]);
    $response = curl_exec($ch);
    curl_close($ch);
    return json_decode($response, true);
}

function ml_curl_get($url, $access_token) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer $access_token",
        'Accept: application/json'
    ]);
    $response = curl_exec($ch);
    curl_close($ch);
    return json_decode($response, true);
}
?>

<!-- Container -->
<main class="container-fluid">
  <article class="row ms-2 me-2 mb-3">
    <section class="col">
      <article class="card shadow-indigo-sm">
        <section class="card-header">
          <article class="row align-items-center">
            <section class="col-md-6 text-start">
              <h3 class="mb-0">  <img class="border border-lemon mb-3 rounded-circle" src="images/ml.svg" alt="" width="40px"> Shopping Mercado Libre</h3>
            </section>
            <section class="col-md-6 text-end">
              <?php if ($connected): ?>
                <a href="compras.php?action=disconnect" class="btn btn-danger btn-sm"><i class="fa-solid fa-link-slash me-1"></i>Desconectar Cuenta</a>
              <?php endif; ?>
            </section>
          </article>
        </section>
        <section class="card-body">
          <?php if (!empty($error_msg)): ?>
            <div class="alert alert-danger" role="alert">
              <i class="fa-solid fa-triangle-exclamation me-2"></i><?= h($error_msg) ?>
            </div>
          <?php endif; ?>

          <?php if (!$config_ready): ?>
            <div class="alert alert-warning" role="alert">
              <i class="fa-solid fa-circle-info me-2"></i>
              <strong>Configuración Requerida:</strong> Por favor, configurá tus credenciales de Mercado Libre (<code>$ml_client_id</code> y <code>$ml_client_secret</code>) en el archivo <code>config.php</code> antes de continuar.
            </div>
          <?php elseif (!$connected): ?>
            <div class="text-center py-5">
              <img class="border border-lemon mb-3 rounded-circle" src="images/ml.svg" alt="" width="90px">
              <h4>Vinculá tu cuenta de Mercado Libre</h4>
              <p class="text-muted mb-4">Conectate para visualizar el listado completo de tus compras en tiempo real.</p>
              <a href="https://auth.mercadolibre.com.ar/authorization?response_type=code&client_id=<?= urlencode($ml_client_id) ?>&redirect_uri=<?= urlencode($ml_redirect_uri) ?>" class="btn btn-indigo btn-lg shadow-indigo-sm">
                <i class="fa-solid fa-plug me-2"></i>Conectar con Mercado Libre
              </a>
            </div>
          <?php else: ?>
              <table id="comprasTable" class="table table-sm table-hover" style="width:100%">
                <thead class="small">
                  <tr>
                    <th style="width: 60px;">Photo</th>
                    <th>Product</th>
                    <th>Date</th>
                    <th class="text-center">Qty.</th>
                    <th class="text-end">Unit Price</th>
                    <th class="text-end">Total</th>
                    <th class="text-center">Status</th>
                    <th class="text-center" style="width: 50px;">Action</th>
                  </tr>
                </thead>
                <tbody class="small">
                  <?php foreach ($orders as $order): ?>
                    <?php
                      $date = new DateTime($order['date_created']);
                      $date_formatted = $date->format('Y/m/d');
                      foreach ($order['order_items'] as $oi):
                        $item_id = $oi['item']['id'] ?? '';
                        $item_link_id = $item_id;
                        if (strlen($item_id) > 3) {
                            $item_link_id = substr($item_id, 0, 3) . '-' . substr($item_id, 3);
                        }
                        $item_title = $oi['item']['title'] ?? 'Producto sin título';
                        $quantity = $oi['quantity'] ?? 1;
                        $price = $oi['unit_price'] ?? 0;
                        $total = $quantity * $price;
                    ?>
                      <tr>
                        <td class="text-center">
                          <a href="https://articulo.mercadolibre.com.ar/<?= urlencode($item_link_id) ?>" target="_blank" title="View  Mercado Libre">
                          <img class="border border-lemon mb-3 rounded-circle" src="images/ml.svg" alt="" width="90px">
                          </a>
                        </td>
                        <td>
                          <a href="https://articulo.mercadolibre.com.ar/<?= urlencode($item_link_id) ?>" target="_blank" class="fw-bold text-decoration-none">
                            <?= h($item_title) ?>
                          </a>
                          <br>
                          <small class="text-muted"><?= h($item_id) ?></small>
                        </td>
                        <td><?= $date_formatted ?></td>
                        <td class="text-center"><?= $quantity ?></td>
                        <td class="text-end">$<?= number_format($price, 2, ',', '.') ?></td>
                        <td class="text-end fw-bold">$<?= number_format($total, 2, ',', '.') ?></td>
                        <td class="text-center">
                          <?php 
                            $status = $order['status'] ?? 'unknown';
                            $badge_class = 'bg-secondary';
                            $status_text = $status;
                            
                            switch ($status) {
                                case 'paid':
                                    $badge_class = 'bg-success-subtle text-success';
                                    $status_text = 'Pagado';
                                    break;
                                case 'cancelled':
                                    $badge_class = 'bg-danger-subtle text-danger';
                                    $status_text = 'Cancelado';
                                    break;
                                case 'shipped':
                                    $badge_class = 'bg-primary-subtle text-primary';
                                    $status_text = 'Enviado';
                                    break;
                            }
                          ?>
                          <span class="badge <?= $badge_class ?>"><?= $status_text ?></span>
                        </td>
                        <td>
                          <a href="item_new.php?did=0&name=<?= urlencode($item_title) ?>&amount=<?= urlencode($quantity) ?>&price=<?= urlencode($price) ?>&desc=<?= urlencode($item_title . ' - Compra de Mercado Libre (' . $item_id . ')') ?>" class="btn btn-outline-success w-100" title="Agregar al Inventario">
                            <i class="fa-solid fa-plus"></i>
                          </a>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  <?php endforeach; ?>
                </tbody>
              </table>
          <?php endif; ?>

        </section>
      </article>
    </section>
  </article>
</main>
<!-- /Container -->

<?php include("footer.php"); ?>

<script>
  $(document).ready(function() {
    if ($('#comprasTable').length) {
      $('#comprasTable').DataTable({
        destroy: true,
        deferRender: true,
        stateSave: true,
        stateDuration: 120,
        pageLength: 20,
        order: [[2, 'desc']], // Ordenar por fecha desc por defecto
        paging: true,
        responsive: true,
        dom: 'Bfrtip',
        orderCellsTop: true,
        buttons: [
          {extend:'copy',className: 'btn btn-darkblue',text:'<i class="fa-regular fa-copy"></i> Copy' },
          {extend: 'excel',className: 'btn btn-green',text:'<i class="fa-regular fa-file-excel"></i> Excel'},
          {extend:'pdf',className: 'btn btn-danger',text:'<i class="fa-regular fa-file-pdf"></i> Pdf',orientation: 'landscape',pageSize: 'A4'},
          {extend:'print',className: 'btn btn-indigo',text:'<i class="fa-regular fa-print"></i> Print'}
        ]
      });
    }
  });
</script>
