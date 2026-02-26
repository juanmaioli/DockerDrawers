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
          <article class="row">
            <section class="col-md-6 text-start">
              <h3 class="text-indigo" id="item_title">Items</h3>
            </section>
            <section class="col-md-6 text-end"><a href="item_new.php?did=0" class="btn btn-indigo"><i class="fa-regular fa-circle-plus"></i>&nbsp;Add Item</a></section>
          </article>
        </section>
        <section class="card-body">
          <table id="item_all_table" class="table table-sm table-hover" style="width:100%">
            <thead class="small">
              <th></th>
              <th>Name</th>
              <th>Brand</th>
              <th>Model</th>
              <th>Category</th>
              <th>Drawer</th>
              <th>Description</th>
              <th>Amount</th>
              <th>Price U$S</th>
              <th>Delete</th>
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
        <h1 class="modal-title fs-5 text-indigo" id="item_image_full_Label"></h1>
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
  itemsAll(<?= $usuarioId ?>,<?=$categoryId?>)
</script>