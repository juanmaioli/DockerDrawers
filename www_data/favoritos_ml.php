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
            // 6. Consultar los datos procesados en MariaDB drawers_fav para fusionar con la vista
            $db_favs = [];
            $res_db = $conn->query("SELECT fav_mla, fav_title, fav_img, fav_price, fav_full, fav_internacional FROM drawers_fav");
            if ($res_db) {
                while ($row = $res_db->fetch_assoc()) {
                    $db_favs[$row['fav_mla']] = $row;
                }
            }
        }
    }
}

$conn->close();

// Pasar marcadores a JS como JSON (ID, Fecha, Título, Foto, Precio, Full e Internacional)
$bookmarks_json = json_encode(array_map(function($bk) use ($db_favs) {
    $iid  = $bk['item_id'];
    $link = "https://articulo.mercadolibre.com.ar/" . str_replace('MLA', 'MLA-', $iid);
    $date = '';
    if (!empty($bk['bookmarked_date'])) {
        $dt   = new DateTime($bk['bookmarked_date']);
        $date = $dt->format('Y/m/d');
    }
    $saved = $db_favs[$iid] ?? [];
    return [
        'id'            => $iid,
        'link'          => $link,
        'date'          => $date,
        'title'         => !empty($saved['fav_title']) ? $saved['fav_title'] : $iid,
        'img'           => !empty($saved['fav_img']) ? $saved['fav_img'] : 'images/ml.svg',
        'price'         => (isset($saved['fav_price']) && $saved['fav_price'] !== null) ? floatval($saved['fav_price']) : null,
        'full'          => $saved['fav_full'] ?? 'no',
        'internacional' => $saved['fav_internacional'] ?? 'no'
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
                <?php if (!empty($bookmarks)): ?>
                  <button class="btn btn-indigo btn-sm me-2 shadow-indigo-sm" id="btnScrapearTodos" onclick="scrapeAllFavs()">
                    <i class="fa-solid fa-cloud-arrow-down me-1"></i>Scrapear Todos
                  </button>
                <?php endif; ?>
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

            <table id="favoritosTable" class="table table-sm table-hover align-middle" style="width:100%">
              <thead class="small">
                <tr>
                  <th style="width:100px;" class="text-center">Foto</th>
                  <th>Artículo</th>
                  <th class="text-center" style="width:70px;">Full</th>
                  <th class="text-center" style="width:110px;">Internacional</th>
                  <th class="text-end">Precio</th>
                  <th class="text-center">Fecha</th>
                  <th class="text-center" style="width:180px;">Acciones</th>
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

  // Renderizar las filas con datos previos o valores por defecto
  bookmarks.forEach(bk => {
    const tr = document.createElement('tr');
    tr.dataset.itemId = bk.id;

    const fullHtml = bk.full === 'si' 
      ? '<span class="text-success fst-italic fw-bold"><i class="fa-solid fa-bolt"></i>FULL</span>' 
      : '';

    const intHtml = bk.internacional === 'si' 
      ? '<span class="text-danger fst-italic fw-bold"><i class="fa-solid fa-plane" style="transform: rotate(-45deg); display: inline-block;"></i>INTER.</span>' 
      : '';

    const priceText = (bk.price !== null && bk.price !== undefined) 
      ? '$ ' + Number(bk.price).toLocaleString('es-AR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) 
      : '-';

    tr.innerHTML = `
      <td class="text-center td-foto">
        <a href="${bk.link}" target="_blank">
          <img src="${bk.img}" class="border border-lemon rounded-circle" width="90" height="90" style="object-fit:cover;" alt="Foto">
        </a>
      </td>
      <td class="td-titulo fw-bold">
        <a href="${bk.link}" target="_blank" class="text-decoration-none text-body">${bk.title}</a>
        <br><small class="text-muted fw-normal">${bk.id}</small>
      </td>
      <td class="text-center td-full">${fullHtml}</td>
      <td class="text-center td-internacional">${intHtml}</td>
      <td class="text-end fw-bold td-precio">${priceText}</td>
      <td class="text-center">${bk.date}</td>
      <td class="text-center">
        <button class="btn btn-outline-indigo btn-sm btn-scrape me-1" onclick="scrapeItem('${bk.id}', this)" title="Scrapear y guardar en drawers_fav">
          <i class="fa-solid fa-arrows-rotate me-1"></i>Scrapear
        </button>
        <a href="${bk.link}" target="_blank" class="btn btn-outline-warning btn-sm" title="Ver en Mercado Libre">
          <i class="fa-solid fa-arrow-up-right-from-square"></i>
        </a>
      </td>`;
    tbody.appendChild(tr);
  });

  window.scrapeItem = function (itemId, btn) {
    const $btn = $(btn);
    const originalHtml = $btn.html();
    $btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i>');

    $.getJSON('api/scrape_fav_item.php', { id: itemId })
      .done(function (res) {
        if (res.status === 'ok') {
          const d = res.data;
          const tr = document.querySelector(`tr[data-item-id="${itemId}"]`);
          if (tr) {
            if (d.img) {
              tr.querySelector('.td-foto img').src = d.img;
            }
            if (d.title) {
              const titleEl = tr.querySelector('.td-titulo a');
              if (titleEl) titleEl.textContent = d.title;
            }
            const fullTd = tr.querySelector('.td-full');
            if (fullTd) {
              fullTd.innerHTML = d.full === 'si'
                ? '<span class="text-success fst-italic fw-bold"><i class="fa-solid fa-bolt"></i>FULL</span>'
                : '';
            }
            const intTd = tr.querySelector('.td-internacional');
            if (intTd) {
              intTd.innerHTML = d.internacional === 'si'
                ? '<span class="text-danger fst-italic fw-bold"><i class="fa-solid fa-plane" style="transform: rotate(-45deg); display: inline-block;"></i>INTER.</span>'
                : '';
            }
            const priceTd = tr.querySelector('.td-precio');
            if (priceTd && d.price !== null) {
              priceTd.textContent = '$ ' + Number(d.price).toLocaleString('es-AR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            }
          }
          $btn.removeClass('btn-outline-indigo').addClass('btn-success').html('<i class="fa-solid fa-check me-1"></i>Guardado');
          setTimeout(() => {
            $btn.removeClass('btn-success').addClass('btn-outline-indigo').html(originalHtml).prop('disabled', false);
          }, 2000);
        } else {
          alert('Error al scrapear: ' + (res.message || 'Ocurrió un problema'));
          $btn.html(originalHtml).prop('disabled', false);
        }
      })
      .fail(function () {
        alert('Error de red al consultar el servidor.');
        $btn.html(originalHtml).prop('disabled', false);
      });
  };

  window.scrapeAllFavs = function () {
    const buttons = document.querySelectorAll('.btn-scrape');
    if (!buttons.length) return;
    const $mainBtn = $('#btnScrapearTodos');
    $mainBtn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin me-1"></i>Scrapeando...');

    let index = 0;
    function processNext() {
      if (index >= bookmarks.length) {
        $mainBtn.removeClass('btn-indigo').addClass('btn-success').html('<i class="fa-solid fa-check me-1"></i>Completado');
        setTimeout(() => {
          $mainBtn.removeClass('btn-success').addClass('btn-indigo').html('<i class="fa-solid fa-cloud-arrow-down me-1"></i>Scrapear Todos').prop('disabled', false);
        }, 3000);
        return;
      }
      const item = bookmarks[index];
      const btn = document.querySelector(`tr[data-item-id="${item.id}"] .btn-scrape`);
      index++;

      if (btn) {
        const $btn = $(btn);
        $btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i>');
        $.getJSON('api/scrape_fav_item.php', { id: item.id })
          .done(function (res) {
            if (res.status === 'ok') {
              const d = res.data;
              const tr = document.querySelector(`tr[data-item-id="${item.id}"]`);
              if (tr) {
                if (d.img) tr.querySelector('.td-foto img').src = d.img;
                if (d.title) tr.querySelector('.td-titulo a').textContent = d.title;
                const fullTd = tr.querySelector('.td-full');
                if (fullTd) {
                  fullTd.innerHTML = d.full === 'si'
                    ? '<span class="text-success fst-italic fw-bold"><i class="fa-solid fa-bolt"></i>FULL</span>'
                    : '';
                }
                const intTd = tr.querySelector('.td-internacional');
                if (intTd) {
                  intTd.innerHTML = d.internacional === 'si'
                    ? '<span class="text-danger fst-italic fw-bold"><i class="fa-solid fa-plane" style="transform: rotate(-45deg); display: inline-block;"></i>INTER.</span>'
                    : '';
                }
                const priceTd = tr.querySelector('.td-precio');
                if (priceTd && d.price !== null) {
                  priceTd.textContent = '$ ' + Number(d.price).toLocaleString('es-AR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                }
              }
              $btn.removeClass('btn-outline-indigo').addClass('btn-success').html('<i class="fa-solid fa-check"></i>');
            }
            setTimeout(processNext, 600);
          })
          .fail(function () {
            setTimeout(processNext, 600);
          });
      } else {
        processNext();
      }
    }
    processNext();
  };

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

