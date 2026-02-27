<?php
include("head.php");
$query = $_GET['q'] ?? '';
?>

<!-- Container -->
<main class="container-fluid">
  <article class="row ms-2 me-2">
    <section class="col">
      <article class="card shadow-indigo-sm">
        <section class="card-header">
          <article class="row">
            <section class="col-md-6 text-start">
              <h3 class="text-indigo">Search Results for: "<span id="searchQueryText"><?= htmlspecialchars($query) ?></span>"</h3>
            </section>
            <section class="col-md-6 text-end">
                <a href="index.php" class="btn btn-primary"><i class="fa-regular fa-circle-chevron-left"></i>&nbsp;Back</a>
            </section>
          </article>
        </section>
        <section class="card-body">
          <div id="searchResults" class="row">
              <!-- Results will be loaded here via AJAX -->
              <div class="text-center p-5">
                  <div class="spinner-border text-indigo" role="status">
                      <span class="visually-hidden">Loading...</span>
                  </div>
              </div>
          </div>
        </section>
      </article>
    </section>
  </article>
</main>
<!-- /Container -->

<?php include("footer.php"); ?>

<script>
  $(document).ready(function() {
      const query = "<?= addslashes($query) ?>";
      if (query) {
          executeGlobalSearch(query, <?= $usuarioId ?>);
      } else {
          document.getElementById('searchResults').innerHTML = '<div class="col text-center mt-5"><h3>Please enter a search term</h3></div>';
      }
  });
</script>
