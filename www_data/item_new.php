<?php
include("head.php");
$drawerId = isset($_GET['did']) ? (int)$_GET['did'] : 0;

$pre_name = $_GET['name'] ?? '';
$pre_amount = isset($_GET['amount']) ? (int)$_GET['amount'] : 0;
$pre_desc = $_GET['desc'] ?? '';
$pre_price = isset($_GET['price']) ? (float)$_GET['price'] : 0.0;

// Cargar cotización del dólar blue venta
$conn_cfg = get_db_connection();
$res_cfg = $conn_cfg->query("SELECT cfg_value FROM drawers_config WHERE cfg_key = 'dolar_venta'");
$row_cfg = $res_cfg->fetch_assoc();
$dolar_venta = (float)($row_cfg['cfg_value'] ?? 1000.00);
$conn_cfg->close();

$pre_price_usd = 0.0;
$pre_price_ars = 0.0;
if ($pre_price > 0) {
    // Si viene de compras, se asume que viene en ARS.
    $pre_price_ars = $pre_price;
    $pre_price_usd = round($pre_price_ars / $dolar_venta, 2);
}
?>

<!-- Container -->
<main class="container-fluid">
  <article class="row">
    <section class="col-md-1"></section>
    <section class="col-md-10">
      <article class="card" id="item_card">
        <section class="card-header">
          <article class="row">
            <section class="col-md-6 text-start">
              <h3 class="" id="item_title">Nuevo Ítem</h3>
            </section>
            <section class="col-md-6 text-end"><a href="drawer_view.php?id=<?=$drawerId?>" class="btn btn-primary"><i class="fa-regular fa-circle-chevron-left"></i>&nbsp;Volver</a></section>
          </article>
        </section>
        <section class="card-body">
          <article class="row">
            <section class="col-md-4 text-center">
              <img src="images/item/default.png" id="item_image" class="img-fluid rounded-4 border border-3">
            </section>
            <section class="col-md-8">
            <form action="item_save.php" method="post" enctype="multipart/form-data">
              <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
              <article class="row mb-3">
                <section class="col">
                <input id="item_id_status" name="item_id_status" type="hidden" value="0">
                <input id="item_brand" name="item_brand" type="hidden" value="1">
                <input id="item_model" name="item_model" type="hidden" value="No Model">

                <input id="item_owner" name="item_owner" type="hidden" value="<?= $usuarioId ?>">
                  <div class="form-floating">
                    <input type='text' class='form-control' id='item_name' name='item_name' value='<?= h($pre_name) ?>' placeholder='Nombre del ítem' title='Nombre del ítem'>
                    <label class="" for="item_name">Nombre del Ítem</label>
                  </div>
                </section>
              </article>
              <article class="row mb-3">
                <section class="col">
                  <div class="form-floating">
                    <input type='number' class='form-control' id='item_amount' name='item_amount' value='<?= h($pre_amount) ?>' placeholder='Cantidad' title='Cantidad'>
                    <label class="" for="item_amount">Cantidad</label>
                  </div>
                </section>
              </article>
              <article class="row mb-3">
                <section class="col-md-6 mb-3 mb-md-0">
                  <div class="form-floating">
                    <input type='number' step='0.01' class='form-control' id='item_price_ars' value='<?= $pre_price_ars > 0 ? number_format($pre_price_ars, 2, '.', '') : "" ?>' placeholder='Precio en Pesos (ARS)' title='Precio en Pesos (ARS)'>
                    <label class="" for="item_price_ars">Precio (ARS)</label>
                  </div>
                </section>
                <section class="col-md-6">
                  <div class="form-floating">
                    <input type='number' step='0.01' class='form-control' id='item_price' name='item_price' value='<?= $pre_price_usd > 0 ? number_format($pre_price_usd, 2, '.', '') : "" ?>' placeholder='Precio en Dólares (USD)' title='Precio en Dólares (USD)'>
                    <label class="" for="item_price">Precio (USD) - Se guarda</label>
                  </div>
                </section>
              </article>
              <article class="row mb-3">
                <section class="col">
                <div class="form-floating">
                    <textarea id='item_description' class='form-control' name='item_description' rows='5' cols='10' placeholder='Descripción' title='Descripción'><?= h($pre_desc) ?></textarea>
                    <label class="" for="item_description">Descripción</label>
                  </div>
                </section>
              </article>
              <article class="row mb-3">
                <section class="col">
                <div class="form-floating">
                  <select name='item_category' id='item_category' class='form-control'>
                  </select>
                  <label class="" for="item_category">Categoría</label>
                </div>
                </section>
              </article>
              <article class="row mb-3">
                <section class="col">
                <div class="form-floating">
                  <select name='item_drawer' id='item_drawer' class='form-control'>
                  </select>
                  <label class="" for="item_drawer">Cajón Actual</label>
                </div>
                </section>
              </article>
              <article class="row mb-3">
                <section class="col-md-6 text-start p-3">
                </section>
                <section class="col-md-6 text-end p-3">
                  <button class="btn btn-success"><i class="fa-regular fa-floppy-disk"></i>&nbsp;Guardar</button>
                </section>
              </article>
            </form>
            </section>
          </article>
        </section>
      </article>
    </section>
    <section class="col-md-1"></section>
  </article>
</main>
<!-- /Container -->
<!-- Modal Full Image-->
<div class="modal fade" id="item_image_full" tabindex="-1" aria-labelledby="item_image_full_Label" aria-hidden="true">
  <div class="modal-dialog  modal-fullscreen">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="item_image_full_Label"></h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body text-center">
        <img src="" id="item_image_full_src" class="img-fluid rounded-4 border border-3">
      </div>
    </div>
  </div>
</div>

<?php include("footer.php"); ?>
<script>
  categoryList('item_category')
  drawerListSelect('item_drawer',<?= $usuarioId ?>)

  const valorDolar = <?= $dolar_venta ?>;

  $(document).ready(function() {
    // Escuchar cambios en pesos (ARS) para calcular dólares (USD)
    $('#item_price_ars').on('input', function() {
      let ars = parseFloat($(this).val());
      if (!isNaN(ars) && ars >= 0) {
        let usd = (ars / valorDolar).toFixed(2);
        $('#item_price').val(usd);
      } else {
        $('#item_price').val('');
      }
    });

    // Escuchar cambios en dólares (USD) para calcular pesos (ARS)
    $('#item_price').on('input', function() {
      let usd = parseFloat($(this).val());
      if (!isNaN(usd) && usd >= 0) {
        let ars = (usd * valorDolar).toFixed(2);
        $('#item_price_ars').val(ars);
      } else {
        $('#item_price_ars').val('');
      }
    });
  });
</script>