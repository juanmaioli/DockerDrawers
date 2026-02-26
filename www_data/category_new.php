<?php
include("head.php");
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
              <h3 class="text-indigo" id="item_title">Category</h3>
            </section>
            <section class="col-md-6 text-end"><a href="categories.php" class="btn btn-primary"><i class="fa-regular fa-circle-chevron-left"></i>&nbsp;Back</a></section>
          </article>
        </section>
        <section class="card-body">
        <form action="category_save.php" method="post" enctype="multipart/form-data">
          <article class="row">
            <section class="col-md-6">
            <input id="category_id_status" name="category_id_status" type="hidden" value="0">
              <div class="form-floating">
                <input type='text' class='form-control' id='category_name' name='category_name' value='' placeholder='category_name' title='category_name'>
                <label class="text-indigo " for="category_name">Category Name</label>
              </div>
            </section>
            <section class="col-md-6">
            <div class="form-floating">
              <select name='category_color' id='category_color' class='form-control'>
                <option value="cyan" class="text-cyan">cyan</option>
                <option value="danger" class="text-danger">danger</option>
                <option value="darkblue" class="text-darkblue">darkblue</option>
                <option value="darkmagenta" class="text-darkmagenta">darkmagenta</option>
                <option value="green" class="text-green">green</option>
                <option value="indigo" class="text-indigo">indigo</option>
                <option value="info" class="text-info">info</option>
                <option value="lemon" class="text-lemon">lemon</option>
                <option value="lightpink" class="text-lightpink">lightpink</option>
                <option value="magenta" class="text-magenta">magenta</option>
                <option value="night" class="text-night">night</option>
                <option value="orange" class="text-orange">orange</option>
                <option value="pink" class="text-pink">pink</option>
                <option value="primary" class="text-primary">primary</option>
                <option value="purple" class="text-purple">purple</option>
                <option value="success" class="text-success">success</option>
                <option value="teal" class="text-teal">teal</option>
                <option value="warning" class="text-warning">warning</option>
                <option value="yellow" class="text-yellow">yellow</option>
              </select>
              <label class="text-indigo " for="category_color">Categoy Color</label>
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
<?php include("footer.php"); ?>
