<?php
mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');

include("config.php");

$conn = new mysqli($db_server, $db_user,$db_pass,$db_name,$db_serverport);
mysqli_set_charset($conn,'utf8');

$item_id_status = $_POST["item_id_status"];
$item_owner = $_POST["item_owner"];
$item_name = $_POST["item_name"];
$item_amount = $_POST["item_amount"];
$item_descriptinon = $_POST["item_descriptinon"];
$item_category = $_POST["item_category"];
$item_drawer = $_POST["item_drawer"];
$item_brand = $_POST["item_brand"];
$item_model = $_POST["item_model"];
$item_drawer = $_POST["item_drawer"];

if(empty($_POST['item_price'])){
  $item_price = 0;
}else{
  $item_price =$_POST["item_price"];
}

$item_id_status = $conn->escape_string($item_id_status );
$item_owner = $conn->escape_string($item_owner );
$item_name = $conn->escape_string($item_name);
$item_amount = $conn->escape_string($item_amount);
$item_descriptinon = $conn->escape_string($item_descriptinon);
$item_category = $conn->escape_string($item_category);
$item_drawer = $conn->escape_string($item_drawer);

$item_name = ucwords(strtolower($item_name));
$item_descriptinon  = ucwords(strtolower($item_descriptinon));

// echo "item_id_status: " . $item_id_status . '<br>';
// echo "item_name: " . $item_name . '<br>';
// echo "item_amount: " . $item_amount . '<br>';
// echo "item_descriptinon: " . $item_descriptinon . '<br>';
// echo "item_category: " . $item_category . '<br>';
// echo "item_drawer: " . $item_drawer . '<br>';

$sql_Update = "UPDATE drawers_items SET item_name='$item_name', item_amount = '$item_amount', item_descrption = '$item_descriptinon',item_category = '$item_category', item_drawer = '$item_drawer',item_price = $item_price, item_brand=$item_brand, item_model='$item_model' WHERE item_id = " . $item_id_status;
// echo $sql_Update;
$sql_Add = "INSERT INTO drawers_items (item_name, item_amount, item_descrption,item_category,item_owner,item_drawer)
VALUES('$item_name', '$item_amount', '$item_descriptinon',$item_category,$item_owner,$item_drawer)";
// echo $sql_Add;


if($item_id_status == 0){
  $result = $conn->query($sql_Add);
  $sql_last = "SELECT max(item_id) as item_id FROM drawers_items";
  $result = $conn->query($sql_last);
  if (mysqli_num_rows($result) == true) {
      while($row = $result->fetch_assoc())
      {
      $item_id = $row["item_id"];
      }
  }
  header('Location: item_view.php?id='.$item_id.'&did='. $item_drawer);

}else{
  $result = $conn->query($sql_Update);
  echo 'Location: item_view.php?id='.$item_id_status.'&did='. $item_drawer;
  sleep(1);
  header('Location: item_view.php?id='.$item_id_status.'&did='. $item_drawer);
}
?>