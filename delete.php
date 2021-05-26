<?php
require_once('dbconnection.php');
$connect = $dbconn;
$tablename=$_SESSION['tableName'];
$pkey=$_SESSION['table_primary_key'];

if(isset($_POST["id"]))
{
 $query = "DELETE FROM $tablename WHERE $pkey = '".$_POST["id"]."'";
 if(mysqli_query($connect, $query))
 {
  echo 'Data Deleted';
 }
}
?>