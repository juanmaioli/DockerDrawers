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
              <h3 class="">Categorías</h3>
            </section>
            <section class="col-md-6 text-end"></section>
            <section class="col-md-3 text-end"><a href="category_new.php" class="btn btn-indigo"><i class="fa-regular fa-circle-plus"></i>&nbsp;Agregar Categoría</a></section>
          </article>
        </section>
        <section class="card-body" id="categoriesList">
          <table id="categoriesListTable" class="table table-sm table-hover" style="width:100%">
          <thead class="small">
            <th>Nombre</th>
            <th>Color</th>
            <th>Cajones por Categoría</th>
            <th>Ítems por Categoría</th>
            <th>Precio Total</th>
            <th>Acciones</th>
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