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
$usr_email = $conn->escape_string($usr_email);

$sql = "SELECT usr_id, usr_email, usr_pass, usr_right, usr_image FROM " . $table_pre . "usr WHERE usr_email = '" . $usr_email ."' and usr_pass = '" . $usr_passwd ."'";
$result = $conn->query($sql);
$usrExiste = mysqli_num_rows($result);
if($usrExiste == true )
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
    $sql = "INSERT INTO " . $table_pre . "session(sess_usr,sess_ip,sess_date,sess_action) values('". $usr_id ."','" . $ip . "','" . $dateShow ."',1)";
    $result = $conn->query($sql);
  	header('Location: index.php');
}
else
{
  header('Location: login.php?id=1');
}

$conn->close();

?>