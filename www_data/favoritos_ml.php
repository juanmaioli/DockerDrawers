<?php
ob_start();
include("head.php");
?>
<?php
$conn = get_db_connection();

$error_msg    = "";
$config_ready = ($ml_client_id !== 'TU_CLIENT_ID' && $ml_client_secret !== 'TU_CLIENT_SECRET');
$bookmarks    = [];
$connected    = false;

// 1. Desconectar cuenta
if (isset($_GET['action']) && $_GET['action'] === 'disconnect') {
    $stmt = $conn->prepare("DELETE FROM drawers_ml_auth WHERE usr_id = ?");
    $stmt->bind_param("i", $usuarioId);
    $stmt->execute();
    $stmt->close();
    $conn->close();
    header("Location: favoritos_ml.php");
    exit();
}

// 2. Leer token guardado por compras.php
$auth = null;
$stmt = $conn->prepare("SELECT * FROM drawers_ml_auth WHERE usr_id = ?");
$stmt->bind_param("i", $usuarioId);
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows > 0) {
    $auth = $res->fetch_assoc();
}
$stmt->close();

if ($auth && $config_ready) {
    $access_token  = $auth['access_token'];
    $refresh_token = $auth['refresh_token'];
    $ml_user_id    = $auth['ml_user_id'];

    // 3. Refrescar token si expiró
    if (time() >= strtotime($auth['expires_at'])) {
        $ch = curl_init("https://api.mercadolibre.com/oauth/token");
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => http_build_query([
                "grant_type"    => "refresh_token",
                "client_id"     => $ml_client_id,
                "client_secret" => $ml_client_secret,
                "refresh_token" => $refresh_token,
            ]),
            CURLOPT_HTTPHEADER => ['Content-Type: application/x-www-form-urlencoded'],
        ]);
        $r = json_decode(curl_exec($ch), true);
        curl_close($ch);

        if (isset($r['access_token'])) {
            $access_token  = $r['access_token'];
            $refresh_token = $r['refresh_token'];
            $expires_at    = date("Y-m-d H:i:s", time() + $r['expires_in']);
            $stmt_upd = $conn->prepare("UPDATE drawers_ml_auth SET access_token=?, refresh_token=?, expires_at=? WHERE usr_id=?");
            $stmt_upd->bind_param("sssi", $access_token, $refresh_token, $expires_at, $usuarioId);
            $stmt_upd->execute();
            $stmt_upd->close();
            $connected = true;
        } else {
            $stmt_del = $conn->prepare("DELETE FROM drawers_ml_auth WHERE usr_id = ?");
            $stmt_del->bind_param("i", $usuarioId);
            $stmt_del->execute();
            $stmt_del->close();
            $error_msg = "La sesión de Mercado Libre expiró. Conectate nuevamente desde Compras.";
        }
    } else {
        $connected = true;
    }

    // 4. Obtener los bookmarks del usuario desduplicados
    if ($connected) {
        $ch = curl_init("https://api.mercadolibre.com/users/me/bookmarks");
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => ["Authorization: Bearer {$access_token}", "Accept: application/json"],
            CURLOPT_TIMEOUT        => 15,
        ]);
        $response_body = curl_exec($ch);
        curl_close($ch);

        $page = json_decode($response_body, true);

        if (is_array($page)) {
            $items = isset($page['results']) && is_array($page['results']) ? $page['results'] : $page;
            if (is_array($items)) {
                $seen = [];
                foreach ($items as $bk) {
                    if (is_array($bk) && !empty($bk['item_id'])) {
                        $id = $bk['item_id'];
                        if (!isset($seen[$id])) {
                            $seen[$id] = true;
                            $bookmarks[] = $bk;
                        }
                    }
                }
            }
        }
        
        // 5. Comparar con la tabla drawers_fav e insertar los que NO estén cargados en la base de datos
        if (!empty($bookmarks)) {
            // Asegurar que las columnas existan en la tabla MariaDB
            $conn->query("ALTER TABLE drawers_fav ADD COLUMN IF NOT EXISTS fav_full VARCHAR(2) NOT NULL DEFAULT 'no'");
            $conn->query("ALTER TABLE drawers_fav ADD COLUMN IF NOT EXISTS fav_internacional VARCHAR(2) NOT NULL DEFAULT 'no'");

            $res_existing = $conn->query("SELECT fav_mla FROM drawers_fav WHERE fav_mla IS NOT NULL AND fav_mla != ''");
            $existing_mlas = [];
            if ($res_existing) {
                while ($row_ex = $res_existing->fetch_assoc()) {
                    $existing_mlas[$row_ex['fav_mla']] = true;
                }
            }

            $stmt_ins = $conn->prepare("INSERT INTO drawers_fav (fav_mla, fav_link, fav_date, fav_title, fav_img, fav_price, fav_desc, fav_full, fav_internacional, fav_delete) VALUES (?, ?, ?, '', '', NULL, '', 'no', 'no', 0)");

            foreach ($bookmarks as $bk) {
                $mla_id = $bk['item_id'];
                if (!isset($existing_mlas[$mla_id])) {
                    $link = "https://articulo.mercadolibre.com.ar/" . str_replace('MLA', 'MLA-', $mla_id);
                    $date = '';
                    if (!empty($bk['bookmarked_date'])) {
                        $dt = new DateTime($bk['bookmarked_date']);
                        $date = $dt->format('Y-m-d H:i:s');
                    }
                    $stmt_ins->bind_param("sss", $mla_id, $link, $date);
                    $stmt_ins->execute();
                    $existing_mlas[$mla_id] = true;
                }
            }
            $stmt_ins->close();
        }
    }
}

$conn->close();

// Pasar marcadores a JS como JSON (ID, Fecha, Enlace directo, Full e Internacional)
$bookmarks_json = json_encode(array_map(function($bk) {
    $iid  = $bk['item_id'];
    $link = "https://articulo.mercadolibre.com.ar/" . str_replace('MLA', 'MLA-', $iid);
    $date = '';
    if (!empty($bk['bookmarked_date'])) {
        $dt   = new DateTime($bk['bookmarked_date']);
        $date = $dt->format('Y/m/d');
    }
    return [
        'id'            => $iid,
        'link'          => $link,
        'date'          => $date,
        'full'          => 'no',
        'internacional' => 'no'
    ];
}, $bookmarks), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
?>

<!-- Container -->
<main class="container-fluid">
  <article class="row ms-2 me-2 mb-3">
    <section class="col">
      <article class="card shadow-indigo-sm">
        <section class="card-header">
          <article class="row align-items-center">
            <section class="col-md-6 text-start">
              <h3 class="mb-0">
                <img class="border border-lemon mb-1 rounded-circle" src="images/ml.svg" alt="ML" width="40px">
                Favoritos de Mercado Libre
                <?php if ($connected && !empty($bookmarks)): ?>
                  <span class="badge bg-indigo ms-2 fs-6" id="fav-badge"><?= count($bookmarks) ?></span>
                <?php endif; ?>
              </h3>
            </section>
            <section class="col-md-6 text-end">
              <?php if ($connected): ?>
                <a href="favoritos_ml.php?action=disconnect" class="btn btn-danger btn-sm">
                  <i class="fa-solid fa-link-slash me-1"></i>Desconectar Cuenta
                </a>
              <?php endif; ?>
            </section>
          </article>
        </section>
        <section class="card-body">

          <?php if (!empty($error_msg)): ?>
            <div class="alert alert-danger"><i class="fa-solid fa-triangle-exclamation me-2"></i><?= h($error_msg) ?></div>
          <?php endif; ?>

          <?php if (!$config_ready): ?>
            <div class="alert alert-warning"><i class="fa-solid fa-circle-info me-2"></i>
              <strong>Configuración Requerida:</strong> Configurá tus credenciales en <code>config.php</code>.
            </div>

          <?php elseif (!$connected): ?>
            <div class="text-center py-5">
              <img class="border border-lemon mb-3 rounded-circle" src="images/ml.svg" alt="ML" width="90px">
              <h4>Vinculá tu cuenta de Mercado Libre</h4>
              <p class="text-muted mb-4">Para ver tus favoritos, primero conectá tu cuenta desde la sección de Compras.</p>
              <a href="compras.php" class="btn btn-indigo btn-lg shadow-indigo-sm">
                <i class="fa-solid fa-plug me-2"></i>Ir a Compras para Conectar
              </a>
            </div>

          <?php elseif (empty($bookmarks)): ?>
            <div class="alert alert-info"><i class="fa-solid fa-star me-2"></i>No tenés artículos marcados como favoritos todavía.</div>

          <?php else: ?>

            <table id="favoritosTable" class="table table-sm table-hover" style="width:100%">
              <thead class="small">
                <tr>
                  <th style="width:60px;">Foto</th>
                  <th>ID del Artículo</th>
                  <th class="text-center" style="width:70px;">Full</th>
                  <th class="text-center" style="width:100px;">Internacional</th>
                  <th class="text-end">Precio</th>
                  <th class="text-center">Fecha de Marcado</th>
                  <th class="text-center" style="width:110px;">Enlace directo</th>
                </tr>
              </thead>
              <tbody class="small" id="favoritosBody"></tbody>
            </table>
          <?php endif; ?>

        </section>
      </article>
<?php include("footer.php"); ?>

<script>
(function () {
  const bookmarks = <?= $bookmarks_json ?>;
  if (!bookmarks || !bookmarks.length) return;

  const tbody = document.getElementById('favoritosBody');

  // Renderizar las filas con las columnas 'Full' e 'Internacional' por defecto en 'no'
  bookmarks.forEach(bk => {
    const tr = document.createElement('tr');
    tr.dataset.itemId = bk.id;
    tr.innerHTML = `
      <td class="text-center td-foto">
        <a href="${bk.link}" target="_blank">
          <img src="images/ml.svg" class="rounded border border-lemon" width="40" height="40" style="object-fit:contain" alt="ML">
        </a>
      </td>
      <td class="td-titulo fw-bold">
        <a href="${bk.link}" target="_blank" class="fw-bold text-decoration-none">${bk.id}</a>
      </td>
      <td class="text-center"><span class="badge bg-secondary">no</span></td>
      <td class="text-center"><span class="badge bg-secondary">no</span></td>
      <td class="text-end fw-bold td-precio"></td>
      <td class="text-center">${bk.date}</td>
      <td class="text-center">
        <a href="${bk.link}" target="_blank" class="btn btn-outline-warning btn-sm" title="Ver en Mercado Libre">
          <i class="fa-solid fa-arrow-up-right-from-square me-1"></i>Ver en ML
        </a>
      </td>`;
    tbody.appendChild(tr);
  });

  $('#favoritosTable').DataTable({
    destroy: true,
    deferRender: true,
    stateSave: true,
    stateDuration: 120,
    pageLength: 25,
    order: [[5, 'desc']],
    paging: true,
    responsive: true,
    dom: 'Bfrtip',
    orderCellsTop: true,
    columnDefs: [{ orderable: false, targets: [0, 6] }],
    buttons: [
      {extend:'copy',  className:'btn btn-darkblue', text:'<i class="fa-regular fa-copy"></i> Copiar'},
      {extend:'excel', className:'btn btn-green',    text:'<i class="fa-regular fa-file-excel"></i> Excel'},
      {extend:'pdf',   className:'btn btn-danger',   text:'<i class="fa-regular fa-file-pdf"></i> PDF', orientation:'landscape', pageSize:'A4'},
      {extend:'print', className:'btn btn-indigo',   text:'<i class="fa-regular fa-print"></i> Imprimir'}
    ]
  });
})();
</script>
