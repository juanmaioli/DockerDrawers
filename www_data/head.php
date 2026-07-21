<?php
include("config.php");
session_start();

$conn = get_db_connection();

/**
 * Reconstruct session from "Remember Me" cookie if needed
 */
if (empty($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    if (isset($_COOKIE[$site_cookie])) {
        $parts = explode(":", $_COOKIE[$site_cookie]);
        if (count($parts) === 2) {
            $cookie_hash = $parts[0];
            $cookie_id = (int)$parts[1];

            $sql = "SELECT * FROM " . $table_pre . "usr WHERE usr_id = ? AND usr_delete = 0";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $cookie_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 1) {
                $row = $result->fetch_assoc();
                // Validate hash
                if (hash('sha256', $row["usr_email"]) === $cookie_hash) {
                    $_SESSION["usuario_id"] = $row["usr_id"];
                    $_SESSION["usuario"] = $row["usr_email"];
                    $_SESSION["avatar"] = $row["usr_image"];
                    $_SESSION["right"] = $row["usr_right"];
                    $_SESSION["loggedin"] = true;
                }
            }
            $stmt->close();
        }
    }
}

// Redirect to login if still not logged in
if (empty($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header('Location: login.php');
    exit();
}

// Session is active, set variables for the app
$usuarioId = $_SESSION["usuario_id"];
$usuarioMail = $_SESSION["usuario"];
$usr_image_session = $_SESSION["avatar"];
$usr_right = $_SESSION["right"];

// CSRF Protection
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];

mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');

/**
 * Global HTML Escaping Function (Anti-XSS)
 */
function h($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

//Date to work
$dateShow = new DateTime(date("Y-m-d H:i:s"));
$dateForm = $dateShow->format('Y-m-d');
$dateShow = $dateShow->format('Y-m-d H:i:s');

// Initialize variables to avoid warnings
$usr_id = $usuarioId; 
$usr_name = '';
$usr_lastname = '';
$usr_email = $usuarioMail;
$usr_image = $usr_image_session;
$usr_token = '';

// Refresh user data from DB to ensure it's up to date (e.g. if profile was edited)
if ($usuarioMail) {
    $sql = "SELECT * FROM " . $table_pre . "usr WHERE usr_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $usuarioId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $usr_id = $row["usr_id"];
        $usr_name = $row["usr_name"];
        $usr_lastname = $row["usr_lastname"];
        $usr_email = $row["usr_email"];
        $usr_image = $row["usr_image"];
        $usr_pass = $row["usr_pass"];
        $usr_token = $row["usr_token"];
        $usr_right = $row["usr_right"];
    }
    $stmt->close();
}
$res_cfg = $conn->query("SELECT cfg_value FROM drawers_config WHERE cfg_key = 'dolar_venta'");
$row_cfg = $res_cfg->fetch_assoc();
$dolar_venta_global = (float)($row_cfg['cfg_value'] ?? 1000.00);

$conn->close();

if ($usr_right == 1) {
    //Admin Menu
    $menu_admin = "<a href='admin.php' class='dropdown-item d-flex align-items-center py-2 px-3 rounded-3'><i class='fa-solid fa-user-shield fa-fw text-white me-2 fs-5'></i><span>Admin</span></a>";
} else {
    $menu_admin = "<a href='#' class='dropdown-item d-flex align-items-center py-2 px-3 rounded-3 opacity-50'><i class='fa-solid fa-user-shield fa-fw text-white-50 me-2 fs-5'></i><span>No Admin</span></a>";
}
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

  <script>
    window.usuarioId = <?= $usuarioId ?>;
    window.csrfToken = "<?= $csrf_token ?>";
    window.valorDolar = <?= $dolar_venta_global ?>;
  </script>
</head>

<body>
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
          <a class="nav-link dropdown-toggle btn-menu-custom d-flex align-items-center gap-2 px-3 py-2 rounded-pill" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="fa-solid fa-bars-staggered text-white"></i>
            <span class="fw-semibold text-white">Menú</span>
          </a>
          <ul class="dropdown-menu dropdown-menu-custom shadow-lg border-0 rounded-4 p-2 mt-2" aria-labelledby="navbarDropdown">
            <li class="px-2 py-2 border-bottom border-white border-opacity-10 mb-2">
              <form action='usr_edit.php' method='post' id='userProfileForm' class='m-0'>
                <input type='hidden' name='id' value="<?= $usr_id ?>">
                <a class="d-flex align-items-center text-white text-decoration-none p-2 rounded-3 btn-user-profile" href='#' onclick='document.getElementById("userProfileForm").submit();' title="Editar Perfil">
                  <img class="profile-img1 border border-2 border-primary me-2" src="<?= $usr_image ?>" style="width: 38px; height: 38px; object-fit: cover;">
                  <div class="d-flex flex-column text-truncate ms-1">
                    <span class="fw-bold lh-1 text-white"><?= h($usr_name . " " . $usr_lastname) ?></span>
                    <small class="text-white-50 fs-7 mt-1"><?= h($usr_email) ?></small>
                  </div>
                </a>
              </form>
            </li>
            <li><a class="dropdown-item d-flex align-items-center py-2 px-3 rounded-3" href="index.php"><i class="fa-solid fa-boxes-stacked fa-fw text-white me-2 fs-5"></i><span>Drawers</span></a></li>
            <li><a class="dropdown-item d-flex align-items-center py-2 px-3 rounded-3" href="items.php"><i class="fa-solid fa-box-archive fa-fw text-white me-2 fs-5"></i><span>Ítems</span></a></li>
            <li><a class="dropdown-item d-flex align-items-center py-2 px-3 rounded-3" href="categories.php"><i class="fa-solid fa-tags fa-fw text-white me-2 fs-5"></i><span>Categorías</span></a></li>
            <li><a class="dropdown-item d-flex align-items-center py-2 px-3 rounded-3" href="inches_mm.php"><i class="fa-solid fa-ruler-combined fa-fw text-white me-2 fs-5"></i><span>Inches a MM</span></a></li>
            <li><a class="dropdown-item d-flex align-items-center py-2 px-3 rounded-3" href="favs.php"><i class="fa-solid fa-bookmark fa-fw text-white me-2 fs-5"></i><span>Marcadores</span></a></li>
            <li><a class="dropdown-item d-flex align-items-center py-2 px-3 rounded-3" href="compras.php"><i class="fa-solid fa-cart-shopping fa-fw text-white me-2 fs-5"></i><span>Compras</span></a></li>
            <li><a class="dropdown-item d-flex align-items-center py-2 px-3 rounded-3" href="favoritos_ml.php"><i class="fa-solid fa-star fa-fw text-white me-2 fs-5"></i><span>Favoritos ML</span></a></li>
            <li>
              <hr class="dropdown-divider my-2">
            </li>
            <li><?= $menu_admin ?></li>
            <li><a class="dropdown-item d-flex align-items-center py-2 px-3 rounded-3" href="logout.php"><i class="fa-solid fa-right-from-bracket fa-fw text-white me-2 fs-5"></i><span>Salir</span></a></li>
          </ul>
        </li>
      </ul>
      <h2 class="me-auto "><a class="text-white text-decoration-none" href="index.php">Drawers App</a></h2>
      <ul class="navbar-nav mx-auto d-none d-lg-block" style="width: 30%;">
        <li class="nav-item position-relative">
          <div class="input-group">
            <span class="input-group-text bg-transparent border-0 text-white"><i class="fas fa-search"></i></span>
            <input type="text" class="form-control bg-dark text-white border-secondary rounded-pill" id="globalSearchInput" placeholder="Buscar ítems o cajones..." onkeyup="handleSearchKeyUp(event)" autocomplete="off">
          </div>
          <div id="autocomplete-results" class="list-group position-absolute w-100 shadow-lg d-none" style="z-index: 2000; top: 100%;"></div>
        </li>
      </ul>
      <ul class="navbar-nav ml-auto align-items-center">
        <li class="nav-item me-2" title="Cambiar tema claro/oscuro">
          <button type="button" class="btn btn-link nav-link text-white border-0 p-0" id="btn-theme" onclick="toggleTheme()">
            <i class="fa-regular fa-sun fa-fw fs-5"></i>
          </button>
        </li>
      </ul>
    </div>
    </div>
  </nav>
  <!-- /Navigation -->
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const dropdowns = document.querySelectorAll('.nav-item.dropdown');
      dropdowns.forEach(dropdown => {
        let timeoutId;
        const menu = dropdown.querySelector('.dropdown-menu-custom');
        const btn = dropdown.querySelector('.dropdown-toggle');
        if (!menu || !btn) return;

        dropdown.addEventListener('mouseenter', () => {
          clearTimeout(timeoutId);
          menu.classList.add('show');
          btn.classList.add('show');
          btn.setAttribute('aria-expanded', 'true');
        });

        dropdown.addEventListener('mouseleave', () => {
          timeoutId = setTimeout(() => {
            menu.classList.remove('show');
            btn.classList.remove('show');
            btn.setAttribute('aria-expanded', 'false');
          }, 350); // Tolerancia de 350ms para mantenerlo abierto cómodamente
        });
      });
    });
  </script>
  <div class="separador"></div>
