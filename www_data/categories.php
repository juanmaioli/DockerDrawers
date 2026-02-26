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
            <section class="col-md-3 text-start">
              <h3 class="text-indigo">Categories</h3>
            </section>
            <section class="col-md-6 text-end"></section>
            <section class="col-md-3 text-end"><a href="category_new.php" class="btn btn-indigo"><i class="fa-regular fa-circle-plus"></i>&nbsp;Add Category</a></section>
          </article>
        </section>
        <section class="card-body" id="categoriesList">
          <table id="categoriesListTable" class="table table-sm table-hover" style="width:100%">
          <thead class="small">
            <th>Name</th>
            <th>Color</th>
            <th>Drawers per Category</th>
            <th>Items per Category</th>
            <th>Drawer Price</th>
            <th>Actions</th>
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
<?php include("footer.php"); ?>
<script>
categoriesTable()
</script>