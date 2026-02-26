<?php
include("head.php");
$itemId= $_GET['id'];
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
              <h3 class="text-indigo" id="item_title">Drawers</h3>
            </section>
            <section class="col-md-6 text-end"><a href="drawer_view.php?id=<?=$drawerId?>" class="btn btn-primary"><i class="fa-regular fa-circle-chevron-left"></i>&nbsp;Back</a></section>
          </article>
        </section>
        <section class="card-body">
          <article class="row">
            <section class="col-md-4 text-center">
              <a href="#item_image_full" data-bs-toggle="modal">
                <img src="" id="item_image" class="img-fluid rounded-4 border border-3">
              </a>
              <article class="row mt-3">
                <section class="col">
                <form action="item_img.php" method="post" enctype="multipart/form-data">
                  <input id="item_id" name="item_id" type="hidden" value="<?= $itemId?>">
                  <input id="item_drawer_img" name="item_drawer_img" type="hidden" value="<?= $drawerId?>">
                  <label for="file-upload" class="custom-file-upload btn btn-indigo">
                  <i class="fa-regular fa-cloud-upload-alt"></i>&nbsp;Change Image</label>
                  <input id="file-upload" name="file-upload" type="file" accept=".jpeg, .jpg" onChange="this.form.submit()">
                </form>
                </section>
                <section class="col">
                  <a id="searchImage" href='https://www.google.com/search?q= &source=lnms&tbm=isch' class='btn btn-primary' target='_blank'>
                  <i class="fa-regular fa-search"></i>&nbsp;Search Image</a>
                </section>
                <section class="col">
                  <a id="searchML" href='https://www.google.com/search?q= &source=lnms&tbm=isch' class='btn btn-yellow' target='_blank'>
                  <i class="fa-brands fa-shopify"></i>&nbsp;MercadoLibre</a>
                </section>
                <!-- <section class="col">
                  <a id="searchPdf" href='https://www.google.com/search?q= &source=lnms&tbm=isch' class='btn btn-primary m-2' target='_blank'>
                  <i class="fa-regular fa-search"></i>&nbsp;Search Pdf</a>
                </section> -->
              </article>
            </section>
            <section class="col-md-8">
            <form action="item_save.php" method="post" enctype="multipart/form-data">
              <article class="row mb-3">
                <section class="col">
                  <input id="item_id_status" name="item_id_status" type="hidden" value="<?= $itemId?>">
                  <input id="item_owner" name="item_owner" type="hidden" value="<?= $usuarioId ?>">
                  <label class="text-indigo mb-2" for="item_name">Item Name</label>
                  <input type='text' class='form-control' id='item_name' name='item_name' value='' placeholder='item_name' title='Name'>
                </section>
                <section class="col-2">
                  <label class="text-indigo mb-2" for="item_amount">Amount</label>
                  <input type='number' class='form-control text-end' id='item_amount' name='item_amount' value='0' placeholder='item_amount' title='Amount'>
                </section>
                <section class="col-2">
                  <label class="text-indigo mb-2" for="item_price">Price U$S</label>
                  <input type='number' class='form-control text-end' id='item_price' name='item_price' value='item_price' placeholder='item_price' title='Price' min="0" value="0" step="0.01">
                </section>
              </article>
              <article class="row mb-3">
                <section class="col">
                  <label class="text-indigo mb-2" for="item_brand">Brand</label>
                  <section class="input-group">
                    <select name='item_brand' id='item_brand' class='form-control' title='Brand'>
                    </select>
                    <span class="input-group-text bg-night rounded-end-2" title="Add Brand">
                      <a href="#" class="btn btn-night btn-sm" data-bs-toggle="modal" data-bs-target="#addItemToList"><i class="fa-regular fa-plus-circle"></i></a>
                    </span>
                  </section>
                </section>
                <section class="col">
                  <label class="text-indigo mb-2" for="item_model">Model</label>
                  <input type='text' class='form-control' id='item_model' name='item_model' value='' placeholder='Model' title='Model'>
                </section>
              </article>

              <article class="row mb-3">
                <section class="col-12">
                  <label class="text-indigo mb-2" for="item_descriptinon">Description</label>
                  <textarea id='item_descriptinon' class='form-control' name='item_descriptinon' rows='2' placeholder='Description' title='Description'>Description</textarea>
                </section>
              </article>
              <article class="row mb-3">
                <section class="col-6">
                  <label class="text-indigo mb-2" for="item_category">Category</label>
                  <select name='item_category' id='item_category' class='form-control' title='Category'>
                  </select>
                </section>
                <section class="col-6">
                  <label class="text-indigo mb-2" for="item_drawer">Actual Drawer > Move to</label>
                  <select name='item_drawer' id='item_drawer' class='form-control  mb-3' title='Actual Drawer'>
                  </select>
                </section>
              </article>
              <article class="row mb-3">
                <section class="col-md-6 text-start p-3">
                  <a href="item_del.php?id=<?= $itemId?>" class="btn btn-danger"><i class="fa-regular fa-trash-can"></i>&nbsp;Delete Item</a>
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
  <div class="modal-dialog  modal-lg">
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
<!-- End Modal Full Image-->
  <!-- INICIO  Modal AddItemALista -->
  <div class="modal fade" id="addItemToList">
    <div class="modal-dialog ">
      <div class="modal-content">
        <div class="modal-header alert-indigo">
          <h5 class="modal-title" id="addItemToListTitle">Add New Item to Brand List</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <article class="row">
            <section class="col">
              <div class="form-group">
                <label for="newItemName" id="labelNewItem">New Item</label>
                <input type="text" class="form-control" id="newItemName" name="newItemName" value="" required >
              </div>
            </section>
          </article>
        </div>
        <div class="modal-footer">
          <button id="addItemToListModalCerrar" class="btn btn-outline-secondary" data-bs-dismiss="modal" title="Close Window">
            <i class="fa-regular fa-circle-xmark"></i>
          </button>
          <button  id="addItemToListModalGuardar" class="btn btn-outline-success" title="Save" onclick="addItemAList()" data-bs-dismiss="modal">
            <i class="fa-regular fa-floppy-disk"></i>
          </button>
        </div>
      </div>
    </div>
  </div>
  <!-- FIN  Modal AddItemALista -->

<?php include("footer.php"); ?>
<script>
  // categoryList('item_category')
  // drawerListSelect('item_drawer',<?= $usuarioId ?>)
  itemView(<?= $itemId?>,<?= $usuarioId ?>)
  // fillSelectBrand('item_brand')
  $(document).ready(function() {
    $('#item_brand').select2({theme: 'bootstrap-5'})
    $('#item_drawer').select2({theme: 'bootstrap-5' })
    $('#item_category').select2({theme: 'bootstrap-5'})
    // $('#empleadoBaja').select2({theme: 'bootstrap-5',dropdownParent: $('#nuevaSolicitudBajaModal') })
    // $('#empleadoAusencia').select2({theme: 'bootstrap-5',dropdownParent: $('#nuevaAusenciaModal') })
    // $('#obraAusencia').select2({theme: 'bootstrap-5',dropdownParent: $('#nuevaAusenciaModal') })
    // $('#empleadoAdicional').select2({theme: 'bootstrap-5',dropdownParent: $('#nuevoAdicionalesModal') })
    // $('#obraAdicional').select2({theme: 'bootstrap-5',dropdownParent: $('#nuevoAdicionalesModal') })
    // $('#empleadoFichada').select2({theme: 'bootstrap-5',dropdownParent: $('#nuevaFichadaModal') })
    // $('#obraFichada').select2({theme: 'bootstrap-5',dropdownParent: $('#nuevaFichadaModal') })
  })
</script>