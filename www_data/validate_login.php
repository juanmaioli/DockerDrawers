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

$ip = $_SERVER['REMOTE_ADDR'];
$dateShow = new DateTime(date("Y-m-d H:i:s"));
$dateShow = $dateShow->format('Y-m-d H:i:s');

if(empty($usr_email) || empty($usr_passwd)){
header("Location: index.php");
exit();
}
$usrExiste = "";

$conn = get_db_connection();

$sql = "SELECT usr_id, usr_email, usr_pass, usr_right, usr_image FROM " . $table_pre . "usr WHERE usr_email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $usr_email);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows > 0 )
{
    $row = $result->fetch_assoc();
    $db_hashed_password = $row["usr_pass"];

    if (password_verify($usr_passwd, $db_hashed_password)) {
        $usr_email = $row["usr_email"];
        $usr_id = $row["usr_id"];
        $usr_image = $row["usr_image"];
        $usr_right = $row["usr_right"];

        session_start();
        $_SESSION["usuario_id"] = $usr_id;
        $_SESSION["usuario"] = $usr_email;
        $_SESSION["avatar"] = $usr_image;
        $_SESSION["right"] = $usr_right;
        $_SESSION["loggedin"] = true;

        if ($www_https == "on") {
            setcookie($site_cookie, hash('sha256', $usr_email )  . ":".$usr_id, time()+60*60*24*$usr_remember, '/',  $www_host   , true, true);
        } else {
            setcookie($site_cookie, hash('sha256', $usr_email )  . ":".$usr_id, time()+60*60*24*$usr_remember, '/',  $www_host  , false, true);
        }

        $sql_sess = "INSERT INTO " . $table_pre . "session(sess_usr, sess_ip, sess_date, sess_action) VALUES(?, ?, ?, 1)";
        $stmt_sess = $conn->prepare($sql_sess);
        $stmt_sess->bind_param("iss", $usr_id, $ip, $dateShow);
        $stmt_sess->execute();
        $stmt_sess->close();

        header('Location: index.php');
        exit();
    } else {
        header('Location: login.php?id=1');
        exit();
    }
}
else
{
    header('Location: login.php?id=1');
    exit();
}

$stmt->close();
$conn->close();

?>