<?php
include("head.php");
$drawerId = $_GET['id'];
?>

<!-- Container -->
<main class="container-fluid">
  <article class="row">
    <section class="col-md-1"></section>
    <section class="col-md-10">
      <article class="card" id="drawer_card">
        <section class="card-header">
          <article class="row">
            <section class="col-md-6 text-start">
              <h3 class="text-indigo" id="drawer_title">Drawers</h3>
            </section>
            <section class="col-md-6 text-end"><a href="index.php" class="btn btn-primary"><i class="fa-regular fa-circle-chevron-left"></i>&nbsp;Back</a></section>
          </article>
        </section>
        <section class="card-body">
          <article class="row">
            <section class="col-md-4 text-center">
              <a href="#drawer_image_full" data-bs-toggle="modal">
                <img src="" id="drawer_image" class="img-fluid rounded-4 border border-3">
              </a>
              <div class="mt-3">
                <form action="drawer_img.php" method="post" enctype="multipart/form-data">
                  <input id="drawer_id" name="drawer_id" type="hidden" value="<?= $drawerId ?>">
                  <label for="file-upload" class="custom-file-upload btn btn-indigo m-2">
                  <i class="fa-regular fa-cloud-upload-alt"></i>&nbsp;Change Image</label>
                  <input id="file-upload" name="file-upload" type="file" accept=".jpeg, .jpg" onChange="this.form.submit()">
                </form>
              </div>
            </section>
            <section class="col-md-8">
            <form action="drawer_save.php" method="post" enctype="multipart/form-data">
              <article class="row mb-3">
                <section class="col">
                <input id="drawer_id_status" name="drawer_id_status" type="hidden" value="<?= $drawerId ?>">
                <input id="drawer_owner" name="drawer_owner" type="hidden" value="<?= $usuarioId ?>">
                  <div class="form-floating">
                    <input type='text' class='form-control' id='drawer_name' name='drawer_name' value='drawer_name' placeholder='drawer_name' title='drawer_name'>
                    <label class="text-indigo " for="drawer_name">Drawer Name</label>
                  </div>
                </section>
              </article>
              <article class="row mb-3">
                <section class="col">
                  <div class="form-floating">
                    <input type='text' class='form-control' id='drawer_location' name='drawer_location' value='drawer_location' placeholder='drawer_location' title='drawer_location'>
                    <label class="text-indigo " for="drawer_location">Location</label>
                  </div>
                </section>
              </article>
              <article class="row mb-3">
                <section class="col">
                <div class="form-floating">
                    <textarea id='drawer_descriptinon' class='form-control' name='drawer_descriptinon' rows='5' cols='10' placeholder='drawer_descriptinon' title='drawer_descriptinon'>drawer_descriptinon</textarea>
                    <label class="text-indigo " for="drawer_descriptinon">Description</label>
                  </div>
                </section>
              </article>
              <article class="row mb-3">
                <section class="col">
                <div class="form-floating">
                  <select name='drawer_category' id='drawer_category' class='form-control'>
                  </select>
                  <label class="text-indigo " for="drawer_category">Category</label>
                </div>
                </section>
              </article>
              <article class="row mb-3">
                <section class="col-md-6 text-start p-3">
                  <a href="drawer_del.php?id=<?= $drawerId ?>" class="btn btn-danger"><i class="fa-regular fa-trash-can"></i>&nbsp;Delete Drawer</a>
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
  <article class="row mt-4">
    <section class="col-md-1"></section>
    <section class="col-md-10">
      <article class="card" id="drawer_card_items">
        <section class="card-header">
          <article class="row">
            <section class="col-md-6 text-start">
              <h3 class="text-indigo">Items in the Drawer</h3>
            </section>
            <section class="col-md-6 text-end"><a href="item_new.php?did=<?=$drawerId?>" class="btn btn-indigo"><i class="fa-regular fa-circle-plus"></i>&nbsp;Add Item</a></section>
          </article>
        </section>
        <section class="card-body">
        <table id="drawer_item_table" class="table table-sm table-hover" style="width:100%">
          <thead class="small">
            <th></th>
            <th>Name</th>
            <th>Category</th>
            <th>Description</th>
            <th>Amount</th>
            <th>Price U$S</th>
            <th>Total Items Price U$S</th>
            <th>View</th>
            <th>Delete</th>
          </thead>
          <tbody class="small">
          </tbody>
        </table>
        </section>
      </article>
    </section>
    <section class="col-md-1"></section>
  </article>
</main>
<!-- /Container -->
<!-- Modal Full Image-->
<div class="modal fade" id="drawer_image_full" tabindex="-1" aria-labelledby="drawer_image_full_Label" aria-hidden="true">
  <div class="modal-dialog  modal-fullscreen">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5 text-indigo" id="drawer_image_full_Label"></h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body text-center">
        <img src="" id="drawer_image_full_src" class="img-fluid rounded-4 border border-3">
      </div>
    </div>
  </div>
</div>

<?php include("footer.php"); ?>
<script>
  // categoryList('drawer_category')
  drawerView(<?= $drawerId ?>)
  drawerItems(<?= $drawerId ?>,<?= $usuarioId ?>)
</script>