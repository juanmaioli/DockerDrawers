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

// Calcular la suma de precios de los artículos favoritos que tengan precio
$total_price_sum = 0;
if (!empty($bookmarks)) {
    foreach ($bookmarks as $bk) {
        $saved = $db_favs[$bk['item_id']] ?? [];
        if (isset($saved['fav_price']) && $saved['fav_price'] !== null) {
            $total_price_sum += floatval($saved['fav_price']);
        }
    }
}
$formatted_total_price = '$ ' . number_format($total_price_sum, 2, ',', '.');

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
              <?php if ($connected && !empty($bookmarks)): ?>
                <h3 class="fst-italic text-muted mt-1 mb-0 fs-5" id="fav-total-price">(<?= $formatted_total_price ?>)</h3>
              <?php endif; ?>
            </section>
            <section class="col-md-6 text-end">
              <?php if ($connected): ?>
                <?php if (!empty($bookmarks)): ?>
                  <div class="d-inline-flex align-items-center me-2 border border-secondary border-opacity-25 rounded-3 px-2 py-1 bg-body-tertiary">
                    <div class="form-check form-switch mb-0 me-3" title="Filtrar por envíos FULL">
                      <input class="form-check-input" type="checkbox" id="switchFiltrarFull" role="switch">
                      <label class="form-check-label small fw-bold text-success" for="switchFiltrarFull">
                        <i class="fa-solid fa-bolt me-1"></i>Solo FULL
                      </label>
                    </div>
                    <div class="form-check form-switch mb-0" title="Filtrar por compras INTERNACIONALES">
                      <input class="form-check-input" type="checkbox" id="switchFiltrarInter" role="switch">
                      <label class="form-check-label small fw-bold text-danger fst-italic" for="switchFiltrarInter">
                        <i class="fa-solid fa-plane" style="transform: rotate(-45deg); display: inline-block;"></i>Solo INTER.
                      </label>
                    </div>
                  </div>
                  <button class="btn btn-lightpink btn-sm me-2 shadow-sm" id="btnScrapearTodos" onclick="scrapeAllFavs()">
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
    </section>
  </article>
</main>

<!-- Modal Editar Favorito -->
<div class="modal fade" id="editFavModal" tabindex="-1" aria-labelledby="editFavModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-indigo text-white">
        <h5 class="modal-title fs-5" id="editFavModalLabel">
          <i class="fa-solid fa-pen-to-square me-2"></i>Editar Artículo Favorito
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="formEditFav" onsubmit="saveFavItem(event)">
        <div class="modal-body">
          <input type="hidden" id="edit_fav_id" name="id">
          
          <div class="row g-3 align-items-stretch">
            <div class="col-md-5 text-center d-flex align-items-center justify-content-center">
              <a id="edit_fav_img_link" href="#" target="_blank" class="w-100 h-100" title="Ver en Mercado Libre">
                <img id="edit_fav_img_preview" src="images/ml.svg" class="border border-lemon rounded-4 shadow-sm img-fluid w-100" style="object-fit: cover; width: 100%; height: 100%; min-height: 230px; max-height: 280px; aspect-ratio: 1/1;" alt="Vista previa">
              </a>
            </div>
            <div class="col-md-7 d-flex flex-column justify-content-center">
              <div class="mb-2">
                <label for="edit_fav_title" class="form-label fw-bold small mb-1">Título del Artículo</label>
                <input type="text" class="form-control form-control-sm" id="edit_fav_title" name="title" required placeholder="Nombre del artículo">
              </div>

              <div class="mb-2">
                <label for="edit_fav_price" class="form-label fw-bold small mb-1">Precio ($)</label>
                <input type="number" step="0.01" min="0" class="form-control form-control-sm" id="edit_fav_price" name="price" placeholder="0.00">
              </div>

              <div class="mb-3">
                <label for="edit_fav_img" class="form-label fw-bold small mb-1">URL de Imagen</label>
                <input type="url" class="form-control form-control-sm" id="edit_fav_img" name="img" placeholder="https://...">
              </div>

              <div class="row g-2 pt-2 border-top">
                <div class="col-6">
                  <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="edit_fav_full" name="full" value="si" role="switch">
                    <label class="form-check-label fw-bold text-success small" for="edit_fav_full">
                      <i class="fa-solid fa-bolt me-1"></i>Envío FULL
                    </label>
                  </div>
                </div>
                <div class="col-6">
                  <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="edit_fav_internacional" name="internacional" value="si" role="switch">
                    <label class="form-check-label fw-bold text-danger fst-italic small" for="edit_fav_internacional">
                      <i class="fa-solid fa-plane me-1" style="transform: rotate(-45deg); display: inline-block;"></i>Internacional
                    </label>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <a id="edit_fav_search_link" href="#" target="_blank" class="btn btn-outline-info btn-sm me-1" title="Buscar en Mercado Libre">
            <i class="fa-solid fa-magnifying-glass me-1"></i>Buscar en ML
          </a>
          <a id="edit_fav_link" href="#" target="_blank" class="btn btn-outline-warning btn-sm me-auto" title="Ver en Mercado Libre">
            <i class="fa-solid fa-arrow-up-right-from-square me-1"></i>Ver en ML
          </a>
          <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-indigo btn-sm shadow-indigo-sm" id="btnSaveFav">
            <i class="fa-solid fa-floppy-disk me-1"></i>Guardar Cambios
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

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
        <button class="btn btn-outline-lightpink btn-sm btn-scrape me-1" onclick="scrapeItem('${bk.id}', this)" title="Scrapear y guardar en drawers_fav">
          <i class="fa-solid fa-arrows-rotate"></i>
        </button>
        <button class="btn btn-outline-primary btn-sm me-1" onclick="openEditFavModal('${bk.id}')" title="Editar artículo">
          <i class="fa-solid fa-pen-to-square"></i>
        </button>
        <a href="https://listado.mercadolibre.com.ar/${encodeURIComponent(bk.title)}" target="_blank" class="btn btn-outline-info btn-sm me-1" title="Buscar en Mercado Libre">
          <i class="fa-solid fa-magnifying-glass"></i>
        </a>
        <a href="${bk.link}" target="_blank" class="btn btn-outline-warning btn-sm" title="Ver en Mercado Libre">
          <i class="fa-solid fa-arrow-up-right-from-square"></i>
        </a>
      </td>`;
    tbody.appendChild(tr);
  });

  function updateFavTotalPrice() {
    let sum = 0;
    bookmarks.forEach(b => {
      if (b.price !== null && b.price !== undefined && !isNaN(b.price)) {
        sum += parseFloat(b.price);
      }
    });
    const formatted = '$ ' + sum.toLocaleString('es-AR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    const totalEl = document.getElementById('fav-total-price');
    if (totalEl) {
      totalEl.textContent = `(${formatted})`;
    }
  }

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
              const targetBk = bookmarks.find(b => b.id === itemId);
              if (targetBk) targetBk.price = parseFloat(d.price);
              updateFavTotalPrice();
            }
          }
          $btn.removeClass('btn-outline-lightpink').addClass('btn-success').html('<i class="fa-solid fa-check"></i>');
          setTimeout(() => {
            $btn.removeClass('btn-success').addClass('btn-outline-lightpink').html(originalHtml).prop('disabled', false);
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
        $mainBtn.removeClass('btn-lightpink').addClass('btn-success').html('<i class="fa-solid fa-check me-1"></i>Completado');
        setTimeout(() => {
          $mainBtn.removeClass('btn-success').addClass('btn-lightpink').html('<i class="fa-solid fa-cloud-arrow-down me-1"></i>Scrapear Todos').prop('disabled', false);
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
                  const targetBk = bookmarks.find(b => b.id === item.id);
                  if (targetBk) targetBk.price = parseFloat(d.price);
                  updateFavTotalPrice();
                }
              }
              $btn.removeClass('btn-outline-lightpink').addClass('btn-success').html('<i class="fa-solid fa-check"></i>');
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

  window.openEditFavModal = function (itemId) {
    const item = bookmarks.find(b => b.id === itemId);
    if (!item) return;

    document.getElementById('edit_fav_id').value = item.id;
    document.getElementById('edit_fav_title').value = item.title || '';
    document.getElementById('edit_fav_price').value = (item.price !== null && item.price !== undefined) ? item.price : '';
    document.getElementById('edit_fav_img').value = item.img || '';
    document.getElementById('edit_fav_full').checked = (item.full === 'si');
    document.getElementById('edit_fav_internacional').checked = (item.internacional === 'si');

    const linkEl = document.getElementById('edit_fav_link');
    if (linkEl) {
      linkEl.href = item.link || '#';
    }
    const imgLinkEl = document.getElementById('edit_fav_img_link');
    if (imgLinkEl) {
      imgLinkEl.href = item.link || '#';
    }
    const searchLinkEl = document.getElementById('edit_fav_search_link');
    if (searchLinkEl) {
      searchLinkEl.href = 'https://listado.mercadolibre.com.ar/' + encodeURIComponent(item.title || '');
    }

    const previewImg = document.getElementById('edit_fav_img_preview');
    if (previewImg) {
      previewImg.src = item.img || 'images/ml.svg';
    }

    const editModal = new bootstrap.Modal(document.getElementById('editFavModal'));
    editModal.show();
  };

  const titleInput = document.getElementById('edit_fav_title');
  if (titleInput) {
    titleInput.addEventListener('input', function () {
      const searchLinkEl = document.getElementById('edit_fav_search_link');
      if (searchLinkEl) {
        searchLinkEl.href = 'https://listado.mercadolibre.com.ar/' + encodeURIComponent(this.value.trim());
      }
    });
  }

  const imgInput = document.getElementById('edit_fav_img');
  if (imgInput) {
    imgInput.addEventListener('input', function () {
      const previewImg = document.getElementById('edit_fav_img_preview');
      if (previewImg) {
        previewImg.src = this.value.trim() || 'images/ml.svg';
      }
    });
  }

  window.saveFavItem = function (e) {
    e.preventDefault();
    const $btn = $('#btnSaveFav');
    const originalHtml = $btn.html();
    $btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin me-1"></i>Guardando...');

    const itemId = document.getElementById('edit_fav_id').value;
    const title = document.getElementById('edit_fav_title').value;
    const priceVal = document.getElementById('edit_fav_price').value;
    const price = priceVal !== '' ? parseFloat(priceVal) : null;
    const img = document.getElementById('edit_fav_img').value;
    const full = document.getElementById('edit_fav_full').checked ? 'si' : 'no';
    const internacional = document.getElementById('edit_fav_internacional').checked ? 'si' : 'no';

    $.post('api/fav_edit_item.php', {
      id: itemId,
      title: title,
      price: priceVal,
      img: img,
      full: full,
      internacional: internacional
    })
    .done(function (res) {
      if (res.status === 'ok') {
        const targetBk = bookmarks.find(b => b.id === itemId);
        if (targetBk) {
          targetBk.title = title;
          targetBk.price = price;
          targetBk.img = img || 'images/ml.svg';
          targetBk.full = full;
          targetBk.internacional = internacional;
        }

        const tr = document.querySelector(`tr[data-item-id="${itemId}"]`);
        if (tr) {
          if (img) tr.querySelector('.td-foto img').src = img;
          const titleEl = tr.querySelector('.td-titulo a');
          if (titleEl) titleEl.textContent = title;

          const fullTd = tr.querySelector('.td-full');
          if (fullTd) {
            fullTd.innerHTML = full === 'si'
              ? '<span class="text-success fst-italic fw-bold"><i class="fa-solid fa-bolt"></i>FULL</span>'
              : '';
          }
          const intTd = tr.querySelector('.td-internacional');
          if (intTd) {
            intTd.innerHTML = internacional === 'si'
              ? '<span class="text-danger fst-italic fw-bold"><i class="fa-solid fa-plane" style="transform: rotate(-45deg); display: inline-block;"></i>INTER.</span>'
              : '';
          }
          const priceTd = tr.querySelector('.td-precio');
          if (priceTd) {
            priceTd.textContent = (price !== null && !isNaN(price))
              ? '$ ' + Number(price).toLocaleString('es-AR', { minimumFractionDigits: 2, maximumFractionDigits: 2 })
              : '-';
          }
        }

        updateFavTotalPrice();

        if (typeof table !== 'undefined' && table) {
          table.rows().invalidate();
        }

        const modalEl = document.getElementById('editFavModal');
        const modalInstance = bootstrap.Modal.getInstance(modalEl);
        if (modalInstance) {
          modalInstance.hide();
        }
      } else {
        alert('Error al guardar: ' + (res.message || 'Ocurrió un problema'));
      }
    })
    .fail(function () {
      alert('Error de red al guardar los cambios.');
    })
    .always(function () {
      $btn.html(originalHtml).prop('disabled', false);
    });
  };

  $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
    if (settings.nTable.id !== 'favoritosTable') return true;
    const soloFull = $('#switchFiltrarFull').is(':checked');
    const soloInter = $('#switchFiltrarInter').is(':checked');

    if (soloFull) {
      const fullCell = data[2] || '';
      if (fullCell.indexOf('FULL') === -1) return false;
    }
    if (soloInter) {
      const interCell = data[3] || '';
      if (interCell.indexOf('INTER.') === -1) return false;
    }
    return true;
  });

  const table = $('#favoritosTable').DataTable({
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

  $('#switchFiltrarFull, #switchFiltrarInter').on('change', function () {
    localStorage.setItem('drawers_fav_switch_full', $('#switchFiltrarFull').is(':checked') ? '1' : '0');
    localStorage.setItem('drawers_fav_switch_inter', $('#switchFiltrarInter').is(':checked') ? '1' : '0');
    table.draw();
  });

  if (localStorage.getItem('drawers_fav_switch_full') === '1') {
    $('#switchFiltrarFull').prop('checked', true);
  }
  if (localStorage.getItem('drawers_fav_switch_inter') === '1') {
    $('#switchFiltrarInter').prop('checked', true);
  }
  if (localStorage.getItem('drawers_fav_switch_full') === '1' || localStorage.getItem('drawers_fav_switch_inter') === '1') {
    table.draw();
  }
})();
</script>

