<?php
include("head.php");
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
              <h3 class="text-indigo" id="drawer_title">New Drawer</h3>
            </section>
            <section class="col-md-6 text-end"><a href="index.php" class="btn btn-primary"><i class="fa-regular fa-circle-chevron-left"></i>&nbsp;Back</a></section>
          </article>
        </section>
        <section class="card-body">
        <form action="drawer_save.php" method="post" enctype="multipart/form-data">
        <input id="drawer_owner" name="drawer_owner" type="hidden" value="<?= $usuarioId ?>">
        <input id="drawer_id_status" name="drawer_id_status" type="hidden" value="0">
          <article class="row">
            <section class="col-md-4 text-center">
              <img src="images/drawers/default.png" id="drawer_image" class="img-fluid rounded-4 border border-3">
            </section>
            <section class="col-md-8">
              <article class="row mb-3">
                <section class="col">
                  <div class="form-floating">
                    <input type='text' class='form-control' id='drawer_name' name='drawer_name' value='' placeholder='drawer_name' title='drawer_name'>
                    <label class="text-indigo " for="drawer_name">Drawer Name</label>
                  </div>
                </section>
              </article>
              <article class="row mb-3">
                <section class="col">
                  <div class="form-floating">
                    <input type='text' class='form-control' id='drawer_location' name='drawer_location' value='' placeholder='drawer_location' title='drawer_location'>
                    <label class="text-indigo " for="drawer_location">Location</label>
                  </div>
                </section>
              </article>
              <article class="row mb-3">
                <section class="col">
                <div class="form-floating">
                    <textarea id='drawer_descriptinon' class='form-control' name='drawer_descriptinon' rows='5' cols='10' placeholder='drawer_descriptinon' title='drawer_descriptinon'></textarea>
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
                </section>
                <section class="col-md-6 text-end p-3">
                  <button class="btn btn-success"><i class="fa-regular fa-floppy-disk"></i>&nbsp;Save</button>
                </section>
              </article>
            </section>
          </article>
        </form>
        </section>
      </article>
    </section>
    <section class="col-md-1"></section>
  </article>
</main>
<!-- /Container -->

<?php include("footer.php"); ?>
<script>
  categoryList('drawer_category')
</script>