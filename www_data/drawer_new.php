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
              <h3 class="" id="drawer_title">Nuevo Cajón</h3>
            </section>
            <section class="col-md-6 text-end"><a href="index.php" class="btn btn-primary"><i class="fa-regular fa-circle-chevron-left"></i>&nbsp;Volver</a></section>
          </article>
        </section>
        <section class="card-body">
        <form action="drawer_save.php" method="post" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
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
                    <input type='text' class='form-control' id='drawer_name' name='drawer_name' value='' placeholder='Nombre del Cajón' title='Nombre del Cajón'>
                    <label class="" for="drawer_name">Nombre del Cajón</label>
                  </div>
                </section>
              </article>
              <article class="row mb-3">
                <section class="col">
                  <div class="form-floating">
                    <input type='text' class='form-control' id='drawer_location' name='drawer_location' value='' placeholder='Ubicación' title='Ubicación'>
                    <label class="" for="drawer_location">Ubicación</label>
                  </div>
                </section>
              </article>
              <article class="row mb-3">
                <section class="col">
                <div class="form-floating">
                    <textarea id='drawer_description' class='form-control' name='drawer_description' rows='5' cols='10' placeholder='Descripción' title='Descripción'></textarea>
                    <label class="" for="drawer_description">Descripción</label>
                  </div>
                </section>
              </article>
              <article class="row mb-3">
                <section class="col">
                <div class="form-floating">
                  <select name='drawer_category' id='drawer_category' class='form-control'>
                  </select>
                  <label class="" for="drawer_category">Categoría</label>
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