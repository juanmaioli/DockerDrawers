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
  <article class="row ms-2 me-2">
    <section class="col">
          <article class="row">
            <section class="col-md-4 p-4">
              <article class="card bg-indigo card-200">
                <section class="card-body text-center" id="statisticsPrice"></section>
              </article>
            </section>
            <section class="col-md-4 p-4">
            <article class="card bg-indigo card-200">
                <section class="card-body" id="statisticsCategoryPrice"></section>
              </article>
            </section>
            <section class="col-md-4 p-4">
            <article class="card bg-indigo card-200">
                <section class="card-body" id="statisticsCategoryTotal"></section>
              </article>
            </section>
          </article>
    </section>
  </article>
  <article class="row ms-2 me-2">
    <section class="col">
      <article class="card shadow-indigo-sm">
        <section class="card-header">
          <article class="row">
            <section class="col-md-3 text-start">
              <h3 class="text-indigo">Drawers</h3>
              <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" role="switch" id="switchTableCard" onchange="changeView()">
                <label class="form-check-label" id="switchTableCardLabel" for="switchTableCard" onclick="document.getElementById('switchTableCard').toogle">Change view to cards</label>
              </div>
            </section>
            <section class="col-md-5 text-end"></section>
            <section class="col-md-2 text-end"><a href="item_new.php?did=0" class="btn btn-indigo"><i class="fa-regular fa-circle-plus"></i>&nbsp;Add Item</a></section>
            <section class="col-md-2 text-end"><a href="drawer_new.php" class="btn btn-indigo"><i class="fa-regular fa-circle-plus"></i>&nbsp;Add Drawer</a></section>
          </article>
        </section>
        <section class="card-body" id="drawersList">
        </section>
      </article>
    </section>
  </article>
</main>
<!-- /Container -->
<?php include("footer.php"); ?>
<script>
  const switchTableCard = document.querySelector('#switchTableCard')
  const switchTableCardLabel = document.querySelector('#switchTableCardLabel')

  // Load preference from localStorage
  const currentView = localStorage.getItem('drawers_view') || 'table'
  
  if (currentView === 'cards') {
    switchTableCard.checked = true
    switchTableCardLabel.innerHTML = 'Change view to table'
    drawersListCards(<?= $usuarioId ?>,<?=$categoryId?>)
  } else {
    switchTableCard.checked = false
    switchTableCardLabel.innerHTML = 'Change view to cards'
    drawersListTable(<?= $usuarioId ?>,<?=$categoryId?>)
  }

  getStatistics(<?= $usuarioId ?>, 5)

  async function changeView() {
    if (switchTableCard.checked) {
      switchTableCardLabel.innerHTML = 'Change view to table'
      localStorage.setItem('drawers_view', 'cards')
      drawersListCards(<?= $usuarioId ?>,<?=$categoryId?>)
    } else {
      switchTableCardLabel.innerHTML = 'Change view to cards'
      localStorage.setItem('drawers_view', 'table')
      drawersListTable(<?= $usuarioId ?>,<?=$categoryId?>)
    }
  }
</script>
