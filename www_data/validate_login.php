<?php
include("config.php");

$usr_email	= $_POST['usr_email'];
$usr_passwd = $_POST['usr_passwd'];

if(empty($_POST['usr_remember']))
{
  $usr_remember = 1;
}else{
  $usr_remember = 1000;
}

echo $usr_remember;
$usr_passwd =  hash('sha256', $usr_passwd );
$ip = $_SERVER['REMOTE_ADDR'];
$dateShow = new DateTime(date("Y-m-d H:i:s"));
$dateShow = $dateShow->format('Y-m-d H:i:s');

if(empty($usr_email) || empty($usr_passwd)){
header("Location: index.php");
exit();
}
$usrExiste = "";

$conn = new mysqli($db_server, $db_user,$db_pass,$db_name,$db_serverport);

$sql = "SELECT usr_id, usr_email, usr_pass, usr_right, usr_image FROM " . $table_pre . "usr WHERE usr_email = ? AND usr_pass = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $usr_email, $usr_passwd);
$stmt->execute();
$result = $stmt->get_result();
$usrExiste = $result->num_rows;

if($usrExiste > 0 )
{
  	while($row = $result->fetch_assoc())
  	{
  		$usr_email = $row["usr_email"];
      $usr_id = $row["usr_id"];
      $usr_image = $row["usr_image"];
      $usr_right = $row["usr_right"];
    }

    session_start();
    $_SESSION["usuario_id"] = $usr_id;
    $_SESSION["usuario"] = $usr_email;
    $_SESSION["avatar"] = $usr_image;
    $_SESSION["right"] = $usr_right;
    $_SESSION["loggedin"] = true;

    if ($www_https == "on") {
      echo "con https";
      setcookie($site_cookie, hash('sha256', $usr_email )  . ":".$usr_id, time()+60*60*24*$usr_remember, '/',  $www_host   , true, true);
    }else {
      echo "sin https";
      setcookie($site_cookie, hash('sha256', $usr_email )  . ":".$usr_id, time()+60*60*24*$usr_remember, '/',  $www_host  , false, true);
    }
    $sql_sess = "INSERT INTO " . $table_pre . "session(sess_usr,sess_ip,sess_date,sess_action) values(?, ?, ?, 1)";
    $stmt_sess = $conn->prepare($sql_sess);
    $stmt_sess->bind_param("iss", $usr_id, $ip, $dateShow);
    $stmt_sess->execute();

  	header('Location: index.php');
}
else
{
  header('Location: login.php?id=1');
}

$stmt->close();
$conn->close();

?>