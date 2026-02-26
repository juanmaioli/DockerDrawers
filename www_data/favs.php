<?php
include("head.php");
?>

<!-- Container -->
<main class="container-fluid">
  <article class="row ms-2 me-2">
    <section class="col">
      <article class="card shadow-indigo-sm">
        <section class="card-header">
          <article class="row">
            <section class="col-md-5 text-start">
              <h3 class="text-indigo" id="totalPriceBookmark">Mercado Libre Bookmarks</h3>
            </section>
            <section class="col-md-4 text-end">
              <input class="form-check-input text-purple" type="checkbox" value="" id="checkDeleted" onchange="bookmarksTable()" checked>
              <label class="form-check-label text-purple" for="checkDeleted">Hide Deleted Items</label>
            </section>
            <section class="col-md-3 text-end"><a href="favs_new.php" class="btn btn-indigo"><i class="fa-regular fa-circle-plus"></i>&nbsp;New List of Bookmarks</a></section>
            <!-- <section class="col-md-3 text-end"><a href="favs_new.php" class="btn btn-indigo"><i class="fa-regular fa-circle-plus"></i>&nbsp;New List of Bookmarks</a> <a href="javascript:void(0)" class="btn btn-danger ms-1" onclick="clearBookmark()"><i class="fa-regular fa-broom-wide"></i> Clear</a></section> -->
          </article>
        </section>
        <section class="card-body" id="bookmarksList">
          <table id="bookmarksListTable" class="table table-sm table-hover" style="width:100%">
          <thead class="small">
            <th></th>
            <th>Title</th>
            <th>Description</th>
            <th>Price</th>
            <th>Ml Id</th>
            <th>Update</th>
            <th>Action</th>
          </thead>
          <tbody class="small">
          </tbody>
        </table>
        </section>
      </article>
    </section>
  </article>
</main>
<!-- Modal Full Image-->
<div class="modal fade" id="item_image_full" tabindex="-1" aria-labelledby="item_image_full_Label" aria-hidden="true">
  <div class="modal-dialog  modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5 text-indigo" id="item_image_full_Label"></h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body text-center">
        <img src="" id="item_image_full_src" class="rounded-4 border border-3">
      </div>
    </div>
  </div>
</div>
<!-- Fin Modal Full Image-->
<!-- Modal Bookmark-->
<div class="modal fade" id="bookmark_modal_full" tabindex="-1" aria-labelledby="bookmark_modal_full_Label" aria-hidden="true">
  <div class="modal-dialog  modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5 text-indigo" id="bookmark_modal_full_Label"></h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body text-center">
        <form action="fav_edit.php" name = "bookmarkEdit" id = "bookmarkEdit" method="post" enctype="multipart/form-data">
        <input type='hidden' name='bookmarkID' id='bookmarkID' value=''>
          <article class="row">
            <section class="col-md-8">
            <div class="form-floating">
              <input type='text' class='form-control' id='bookmarkTitle' name='bookmarkTitle' value='bookmarkTitle' placeholder='bookmarkTitle' title='bookmarkTitle'>
              <label class="text-indigo " for="bookmarkTitle">Bookmark</label>
            </div>
            </section>
            <section class="col-md-4"><button class="btn btn-success"><i class="fa-regular fa-floppy-disk"></i></button></section>
          </article>
        </form>
      </div>
    </div>
  </div>
</div>
<!-- Fin Modal Bookmark-->
<!-- /Container -->
<?php include("footer.php"); ?>
<script>
  priceBookmark()
  bookmarksTable()
</script>