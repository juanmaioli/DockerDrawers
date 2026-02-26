<?php
include("head.php");
$conn = new mysqli($db_server, $db_user, $db_pass, $db_name, $db_serverport);
$acentos = $conn->query("SET NAMES 'utf8'");
mysqli_set_charset($conn, 'utf8');
mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');

$sql = "SELECT inches_mm_id,inches_mm_mm,inches_mm_inches,inches_mm_ml_inches,inches_mm_tool FROM drawers_inches_mm ORDER BY inches_mm_mm";
$result = $conn->query($sql);
$table_inches = '';
if (mysqli_num_rows($result) == true) {
  while ($row = $result->fetch_assoc()) {
    $inches_mm_id = $row["inches_mm_id"];
    $inches_mm_mm = $row["inches_mm_mm"];
    $inches_mm_inches = $row["inches_mm_inches"];
    $inches_mm_ml_inches = $row["inches_mm_ml_inches"];
    $inches_mm_tool = $row["inches_mm_tool"];
    if ($inches_mm_tool == 1) {
      $inches_mm_tool = '<i class="fa-regular fa-wrench text-success"></i>&nbsp;Yes';
    }
    else {
      $inches_mm_tool = '<i class="fa-regular fa-circle-xmark text-danger"></i>&nbsp;No';
    }

    $table_inches .= "<tr>
    <td>$inches_mm_inches</td>
    <td>$inches_mm_mm</td>
    <td>$inches_mm_ml_inches</td>
    <td>$inches_mm_tool</td>
    </tr>";
  }
}
$conn->close();
?>

<!-- Container -->
<main class="container-fluid">
<article class="row ms-2 me-2 mb-3">
  <section class="col">
    <article class="card shadow-indigo-sm">
      <section class="card-header">
        <article class="row">
          <section class="col-md-3 text-start">
            <h3 class="text-indigo">Convert MM to Inches</h3>
          </section>
          <section class="col-md-9 text-end"></section>
        </article>
      </section>
      <section class="card-body">
        <article class="row">
          <section class="col-md-1"></section>
          <section class="col-md-2">
          <div class="input-group mb-3">
            <input type="text" class="form-control" placeholder="Millimeters" aria-label="Millimeters" id="millimetersIn" name="millimetersIn" onkeyup="mmToFractionInches(this.value)" value="0">
            <span class="input-group-text bg-indigo text-white">mm</span>
          </div>
          </section>
          <section class="col-md-1"></section>
          <section class="col-md-3"><h4 id="thousandthsOut">Thousandths of an Inch: 0"</h4></section>
          <section class="col-md-1"></section>
          <section class="col-md-3"><h4 id="fractionOut">Inches Fraction: 0</h4></section>
          <section class="col-md-1"></section>
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
            <h3 class="text-indigo">Table Inches to MM</h3>
          </section>
          <section class="col-md-9 text-end"></section>
        </article>
      </section>
      <section class="card-body">
        <table id="inchesListTable" class="table table-sm table-hover">
          <thead class="small">
            <th>Inches</th>
            <th>Mm</th>
            <th>Thousandths Of An Inch</th>
            <th>Tools</th>
          </thead>
          <tbody class="small"><?= $table_inches ?></tbody>
        </table>
      </section>
    </article>
  </section>
</article>
</main>
<!-- /Container -->
<?php include("footer.php"); ?>
<script>
  let table;
  $(document).ready(function() {
    table = $('#inchesListTable').DataTable({
      destroy: true,
      deferRender: false,
      stateSave: false,
      stateDuration: 120,
      pageLength: 50,
      order: [],
      paging: true,
      responsive: true,
      dom: 'Bfrtip',
      orderCellsTop: true,
      buttons: ['copy', 'excel',
        {
          extend: 'pdf',
          orientation: 'portrait',
          pageSize: 'A4'
        },
        'print'
      ]
    })
  })
</script>
