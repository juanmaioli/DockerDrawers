<?php
//unset($_COOKIE['reloginID']);
if (empty($_GET['id'])) {
  $id = 0;
  $msg = "";
} else {
  $id = $_GET["id"];
  $msg = "<h6 class='text-center border border-danger text-danger m-1 p-2 rounded'>Usuario o Clave Incorrecto</h6>";
}
?>
<html>

<head>
  <meta http-equiv="content-type" content="text/html;charset=UTF-8">
  <title>Drawers App</title>
  <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0">

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

  <link rel="stylesheet" href="css/bootstrap.min.css">
  <link rel="stylesheet" href="css/all.min.css">
  <style>
    html, body {
      height: 100%;
    }
    body {
      background-image: url("http://pikapp.com.ar/rnd_img/index.php?id=anime");
      background-position: center center;
      background-repeat: no-repeat;
      background-attachment: fixed;
      background-size: cover;
      background-color: #a31b0f;
    }

    fieldset {
      background-color: rgba(0, 0, 0, 0.8);
      border-right: 1px solid #053481 !important;
      padding: 1.4em 1.4em 1.4em 1.4em !important;
      height: 100%;
      display: flex;
      flex-direction: column;
      justify-content: center;
    }
  </style>
</head>

<body>
  <div class="container-fluid h-100 p-0">
    <div class="row h-100 m-0">
      <div class="col-md-3 h-100 p-0">
        <fieldset>
        <form ACTION="validate_login.php" name="form1" method="POST">
          <article class="row mb-3">
            <section class="col text-center">
            <img class="img-fluid" src="images/logo.png" width="" alt="">
            </section>
          </article>
          <article class="row mb-3">
            <section class="col">
              <input name="usr_email" type="text" id="usr_email" class="form-control mt-3" placeholder="Email" required autofocus>
            </section>
          </article>
          <article class="row mb-3">
            <section class="col">
            <div class="input-group mb-3">
              <input type="password" class="form-control" id="usr_passwd" name="usr_passwd" placeholder="Contrase&ntilde;a" required>
              <span class="input-group-text" id="basic-addon2"><a href='javascript:void(0);' onclick='passClear()'><i class="far fa-eye-slash" id="eye"></i></a></span>
            </div>
            </section>
          </article>
          <article class="row mb-3">
            <section class="col">
            <div class="form-group form-check m-2">
              <label class="form-check-label text-primary small">
                <input class="form-check-input" type="checkbox" id="usr_remember" name="usr_remember" value="sip"> Recordarme en este equipo
              </label>
            </div>
            </section>
          </article>
          <article class="row mb-3">
            <section class="col text-center">
            <input name="formSubmit" type="hidden" id="formSubmit" value="yes">
            <button class="btn btn-primary btn-block mt-3" type="submit">Ingresar</button>
            </section>
          </article>
          </form>
          <?= $msg ?>
        </fieldset>
      </div>
    </div>
  </div>
  <script>
    function passClear() {
      var password = document.getElementById('usr_passwd');
      var eye = document.getElementById('eye');
      if (password.getAttribute('type') === 'password') {
        password.setAttribute('type', 'text');
        eye.classList.remove("fa-eye-slash");
        eye.classList.add("fa-eye");
      } else {
        password.setAttribute('type', 'password');
        eye.classList.remove("fa-eye");
        eye.classList.add("fa-eye-slash");
      }
    }
  </script>
</body>

</html>