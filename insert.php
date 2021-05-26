<?php
require_once('dbconnection.php');
$connect = $dbconn;
$tablename=$_SESSION['tableName'];
//$connect = mysqli_connect("localhost", "root", "", "testing");
$query = "INSERT INTO $tablename(";
$i=0;
$valstr='';
foreach($_POST as $key=>$val)
{
    if($i<(count($_POST)-1))
    {
        $query.=$key.",";
        $valstr.="'".$val."',";
    }
    else
    {
        $query.=$key;
        $valstr.="'".$val."'";
    }
    $i++;
}
$query.=") VALUES (".$valstr.")";

//echo $query;exit;
//print_r($query);exit;
// $first_name = mysqli_real_escape_string($connect, $_POST["first_name"]);
 //$last_name = mysqli_real_escape_string($connect, $_POST["last_name"]);
 //$query = "INSERT INTO user(first_name, last_name) VALUES('$first_name', '$last_name')";
 if(mysqli_query($connect, $query))
 {
  echo 'Data Inserted';
 }

?>