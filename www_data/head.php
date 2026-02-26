<?php
include("config.php");
session_start();
if ($_SESSION["loggedin"] == false) {
  header('Location: login.php');
  exit();
}

if (isset($_COOKIE[$site_cookie])) {
  $datos = $_COOKIE[$site_cookie];
  $datosCuenta = explode(":", $datos);
  $usuarioId = $datosCuenta[1];
} else {
  header("Location: login.php");
  exit();
}

//User Data
$usuarioMail = $_SESSION["usuario"];
$usr_image = $_SESSION["avatar"];
$usr_right = $_SESSION["right"];
if ($usr_right == 1) {
  //Admin Menu
  $menu_admin = "<a href='admin.php' class='dropdown-item text-white'><i class='fas fa-user-shield text-warning fa-lg'></i>&nbsp;Admin</a>";
} else {
  $menu_admin = "<a href='#' class='dropdown-item text-white'><i class='fas fa-user-shield text-warning fa-lg'></i>&nbsp;No Admin</a>";
}


$conn = new mysqli($db_server, $db_user, $db_pass, $db_name, $db_serverport);
$acentos = $conn->query("SET NAMES 'utf8'");
mysqli_set_charset($conn, 'utf8');
mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');
//Date to work
$dateShow = new DateTime(date("Y-m-d H:i:s"));
$dateForm = $dateShow->format('Y-m-d');
$dateShow = $dateShow->format('Y-m-d H:i:s');
//Usuario
$sql = "SELECT * FROM " . $table_pre . "usr where usr_email = '" . $usuarioMail . "'";
$result = $conn->query($sql);

if (mysqli_num_rows($result) == true) {
  while ($row = $result->fetch_assoc()) {
    $usr_id = $row["usr_id"];
    $usr_name = $row["usr_name"];
    $usr_lastname = $row["usr_lastname"];
    $usr_email = $row["usr_email"];
    $usr_image = $row["usr_image"];
    $usr_pass = $row["usr_pass"];
    $usr_token = $row["usr_token"];
    $usr_right = $row["usr_right"];
  }
}
$conn->close();
?>
<html lang="es">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="Juan Maioli">
  <meta name="author" content="https://github.com/juanmaioli">
  <title>Drawers App</title>
  <!-- Google Fonts -->
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Lato:wght@300&family=Montserrat&family=Roboto&display=swap');
  </style>
  <!-- Bootstrap core CSS -->
  <link rel="stylesheet" href="css/bootstrap.min.css?version=5.3.0">
  <!-- Bootstrap Extension Colors  -->
  <link rel="stylesheet" href="css/bootstrap-color-extension.css?version=1.6.0">
  <!-- fontawesome.com -->
  <link rel="stylesheet" href="css/all.min.css?version=6.4.0">
  <!-- Custom styles for this template -->
  <link rel="stylesheet" href="css/style.css?version=1.1">
  <!-- DataTables CSS -->
  <link rel="stylesheet" type="text/css" href="js/dataTables/dataTables.bootstrap5.min.css">
  <link rel="stylesheet" type="text/css" href="js/dataTables/responsive.bootstrap5.min.css">
  <link rel="stylesheet" type="text/css" href="js/dataTables/buttons.bootstrap5.min.css">
  <link rel="stylesheet" type="text/css" href="js/dataTables/searchPanes.bootstrap5.min.css">
  <link rel="stylesheet" type="text/css" href="js/dataTables/select.bootstrap5.min.css">
  <link rel="stylesheet" type="text/css" href="js/dataTables/select.bootstrap5.min.css">
  <link rel="stylesheet" type="text/css" href="js/dataTables/rowReorder.bootstrap5.min.css">
  <link rel="stylesheet" type="text/css" href="js/dataTables/rowGroup.bootstrap5.min.css">
  <!-- Select2 Css -->
  <link rel="stylesheet" href="js/select2/select2.min.css">
  <link rel="stylesheet" href="js/select2/select2-bootstrap-5-theme.min.css">
  <!-- Favicon for this template -->
  <link rel="apple-touch-icon" sizes="57x57" href="images/apple-icon-57x57.png">
  <link rel="apple-touch-icon" sizes="60x60" href="images/apple-icon-60x60.png">
  <link rel="apple-touch-icon" sizes="72x72" href="images/apple-icon-72x72.png">
  <link rel="apple-touch-icon" sizes="76x76" href="images/apple-icon-76x76.png">
  <link rel="apple-touch-icon" sizes="114x114" href="images/apple-icon-114x114.png">
  <link rel="apple-touch-icon" sizes="120x120" href="images/apple-icon-120x120.png">
  <link rel="apple-touch-icon" sizes="144x144" href="images/apple-icon-144x144.png">
  <link rel="apple-touch-icon" sizes="152x152" href="images/apple-icon-152x152.png">
  <link rel="apple-touch-icon" sizes="180x180" href="images/apple-icon-180x180.png">
  <link rel="icon" type="image/png" sizes="192x192" href="images/android-icon-192x192.png">
  <link rel="icon" type="image/png" sizes="32x32" href="images/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="96x96" href="images/favicon-96x96.png">
  <link rel="icon" type="image/png" sizes="16x16" href="images/favicon-16x16.png">
  <link rel="manifest" href="images/manifest.json">
  <meta name="msapplication-TileColor" content="#ffffff">
  <meta name="msapplication-TileImage" content="images/ms-icon-144x144.png">
  <meta name="theme-color" content="#ffffff">
</head>

<body>
  <!-- Inicio Toggle modo dark -->
  <nav class="dropdown position-fixed bottom-0 end-0 mb-5 me-3 bd-mode-toggle">
    <button class="btn btn-indigo py-2 dropdown-toggle d-flex align-items-center" id="btn-theme" type="button" data-bs-toggle="dropdown"><i class="fa-regular fa-circle-half-stroke fa-fw"></i></button>
    <ul class="dropdown-menu dropdown-menu-end shadow">
      <li>
        <button type="button" class="dropdown-item d-flex align-items-center text-white" onclick="changeTheme('light')"><i class="fa-regular fa-sun fa-fw"></i>&nbsp;Claro</button>
      </li>
      <li>
        <button type="button" class="dropdown-item d-flex align-items-center text-white" onclick="changeTheme('dark')"><i class="fa-regular fa-moon-stars fa-fw"></i>&nbsp;Oscuro</button>
      </li>
      <li>
        <button type="button" class="dropdown-item d-flex align-items-center text-white" onclick="changeTheme('auto')"><i class="fa-regular fa-circle-half-stroke fa-fw"></i>&nbsp;Auto</button>
      </li>
    </ul>
  </nav>
  <!-- Fin Toggle modo dark -->
  <!-- Logo -->
  <div class="d-none d-lg-block" style="width:25px;height:75px;position:fixed;left:20px;bottom:25px;z-index:10000">
    <a class="navbar-brand" href="index.php">
      <img class="profile-img2" src="images/logo.svg" alt="Logo">
    </a>
  </div>
  <!-- /Logo -->
  <!-- Navigation -->
  <nav class="navbar navbar-expand-md navbar-dark fixed-top"">
    <div class=" container-fluid">
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav me-auto">
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false"><i class='fas fa-ellipsis-v text-secondary fa-lg'></i>&nbsp;Menú</a>
          <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
            <li><a class="dropdown-item text-white" href="index.php">Drawers</a></li>
            <li><a class="dropdown-item text-white" href="items.php">Items</a></li>
            <li><a class="dropdown-item text-white" href="categories.php">Categories</a></li>
            <li><a class="dropdown-item text-white" href="inches_mm.php">Inches to MM</a></li>
            <li><a class="dropdown-item text-white" href="favs.php">Bookmarks</a></li>
            <li>
              <hr class="dropdown-divider">
            </li>
            <li><?= $menu_admin ?></li>
          </ul>
        </li>
      </ul>
      <h2 class="me-auto "><a class="text-white text-decoration-none" href="index.php">Drawers App</a></h2>
      <ul class="navbar-nav ml-auto">
        <li class="nav-item d-none d-lg-block">
          <a class="nav-link" href='#'><img class="profile-img1 border border-primary" src="<?= $usr_image ?>"></a>
        </li>
        <li class="nav-item">
          <form action='usr_edit.php' method='post'>
            <input type='hidden' name='id' id='id' value="<?= $usr_id ?>">
            <a class="nav-link text-white" href='#' onclick='this.parentNode.submit();'><?= $usr_name . " " . $usr_lastname ?></a>
          </form>
        </li>
        <li class="nav-item" title="Cerrar Sesion">
          <a class="nav-link text-white" href="logout.php"><i class="fas fa-sign-out-alt text-danger fa-lg"></i>Salir</a>
        </li>
      </ul>
    </div>
    </div>
  </nav>
  <!-- /Navigation -->
  <div class="separador"></div>


