<?php
include("head.php");

if(empty($_GET['id']))
{
  $categoryId = 0;

}else{
  $categoryId =$_GET["id"];
}
?>

<!-- Container -->
<main class="container-fluid">
  <article class="row  ms-2 me-2">
    <section class="col">
      <article class="card" id="item_card">
        <section class="card-header">
          <article class="row align-items-center">
            <section class="col-md-6 text-start d-flex align-items-center gap-2">
              <h3 class="mb-0" id="item_title">Ítems</h3>
              <span id="totalRegaladoBadge" class="badge bg-danger d-none fs-6">
                <i class="fa-solid fa-gift me-1"></i>Regalado: U$S <span id="totalRegaladoMonto">0.00</span>
              </span>
            </section>
            <section class="col-md-6 text-end d-flex align-items-center justify-content-end gap-3">
              <div class="form-check form-switch mb-0" title="Mostrar ítems activos / borrados">
                <input class="form-check-input" type="checkbox" id="switchBorrados" role="switch" onchange="reloadItemsTable()">
                <label class="form-check-label small fw-bold text-danger" for="switchBorrados">Borrados</label>
              </div>
              <a href="item_new.php?did=0" class="btn btn-indigo"><i class="fa-regular fa-circle-plus"></i>&nbsp;Agregar Ítem</a>
            </section>
          </article>
        </section>
        <section class="card-body">
          <table id="item_all_table" class="table table-sm table-hover" style="width:100%">
            <thead class="small">
              <th></th>
              <th>Nombre</th>
              <th>Marca</th>
              <th>Modelo</th>
              <th>Categoría</th>
              <th>Cajón</th>
              <th>Descripción</th>
              <th>Cantidad</th>
              <th>Precio U$S</th>
              <th>Estado / Acciones</th>
            </thead>
            <tbody class="small">
            </tbody>
          </table>
        </section>
      </article>
    </section>
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
  function reloadItemsTable() {
    const isBorrados = $('#switchBorrados').is(':checked');
    const filter = isBorrados ? '1' : '0';
    itemsAll(<?= (int)$usuarioId ?>, <?= (int)$categoryId ?>, filter);
  }

  $(document).ready(function() {
    reloadItemsTable();
  });
</script>