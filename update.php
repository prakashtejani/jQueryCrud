<?php
require_once('dbconnection.php');
$connect = $dbconn;
$tablename=$_SESSION['tableName'];
$pkey=$_SESSION['table_primary_key'];
if(isset($_POST["id"]))
{
 $value = mysqli_real_escape_string($connect, $_POST["value"]);
 $query = "UPDATE $tablename SET ".$_POST["column_name"]."='".$value."' WHERE $pkey = '".$_POST["id"]."'";
 //echo $query;exit;
 if(mysqli_query($connect, $query))
 {
  echo 'Data Updated';
 }
}
?>