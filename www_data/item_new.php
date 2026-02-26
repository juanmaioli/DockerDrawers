<?php
include("head.php");
$drawerId = $_GET['did'];
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
              <h3 class="text-indigo" id="item_title">New Item</h3>
            </section>
            <section class="col-md-6 text-end"><a href="drawer_view.php?id=<?=$drawerId?>" class="btn btn-primary"><i class="fa-regular fa-circle-chevron-left"></i>&nbsp;Back</a></section>
          </article>
        </section>
        <section class="card-body">
          <article class="row">
            <section class="col-md-4 text-center">
              <img src="images/item/default.png" id="item_image" class="img-fluid rounded-4 border border-3">
            </section>
            <section class="col-md-8">
            <form action="item_save.php" method="post" enctype="multipart/form-data">
              <article class="row mb-3">
                <section class="col">
                <input id="item_id_status" name="item_id_status" type="hidden" value="0">
                <input id="item_brand" name="item_brand" type="hidden" value="0">
                <input id="item_model" name="item_model" type="hidden" value="No Model">

                <input id="item_owner" name="item_owner" type="hidden" value="<?= $usuarioId ?>">
                  <div class="form-floating">
                    <input type='text' class='form-control' id='item_name' name='item_name' value='' placeholder='item_name' title='item_name'>
                    <label class="text-indigo " for="item_name">Item Name</label>
                  </div>
                </section>
              </article>
              <article class="row mb-3">
                <section class="col">
                  <div class="form-floating">
                    <input type='number' class='form-control' id='item_amount' name='item_amount' value='0' placeholder='item_amount' title='item_amount'>
                    <label class="text-indigo " for="item_amount">Amount</label>
                  </div>
                </section>
              </article>
              <article class="row mb-3">
                <section class="col">
                <div class="form-floating">
                    <textarea id='item_descriptinon' class='form-control' name='item_descriptinon' rows='5' cols='10' placeholder='item_descriptinon' title='item_descriptinon'></textarea>
                    <label class="text-indigo " for="item_descriptinon">Description</label>
                  </div>
                </section>
              </article>
              <article class="row mb-3">
                <section class="col">
                <div class="form-floating">
                  <select name='item_category' id='item_category' class='form-control'>
                  </select>
                  <label class="text-indigo " for="item_category">Category</label>
                </div>
                </section>
              </article>
              <article class="row mb-3">
                <section class="col">
                <div class="form-floating">
                  <select name='item_drawer' id='item_drawer' class='form-control'>
                  </select>
                  <label class="text-indigo " for="item_drawer">Actual Drawer</label>
                </div>
                </section>
              </article>
              <article class="row mb-3">
                <section class="col-md-6 text-start p-3">
                </section>
                <section class="col-md-6 text-end p-3">
                  <button class="btn btn-success"><i class="fa-regular fa-floppy-disk"></i>&nbsp;Save</button>
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
  categoryList('item_category')
  drawerListSelect('item_drawer',<?= $usuarioId ?>)
</script>