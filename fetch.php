<?php require_once('dbconnection.php');
if(isset($_POST['logoutUser']) && !empty($_POST['logoutUser']))
{
    session_destroy();
}
if(isset($_POST['tableName']) && !empty($_POST['tableName'])){
    $_SESSION['tableName']=$_POST['tableName'];
    return;
}
//fetch.php

$connect = $dbconn;
$tablename=$_SESSION['tableName'];
$fetchqry="SELECT `COLUMN_NAME`,COLUMN_KEY FROM `INFORMATION_SCHEMA`.`COLUMNS` WHERE `TABLE_SCHEMA`='$dbname' AND `TABLE_NAME`='$tablename' ";//AND `COLUMN_KEY` <> 'PRI'
$columns=[];
if ($qryresult = mysqli_query($connect,$fetchqry))
{
    while ($row = mysqli_fetch_assoc($qryresult)) {
        if($row['COLUMN_KEY']!='PRI')
            $columns[]=$row['COLUMN_NAME'];
        else
            $_SESSION['table_primary_key']=$row['COLUMN_NAME'];
    }
    mysqli_free_result($qryresult);
    
}
$pkey=$_SESSION['table_primary_key'];
//$columns = array('first_name', 'last_name');

$query = "SELECT * FROM  $tablename ";

/*if(isset($_POST["search"]["value"]))
{
 $query .= '
 WHERE first_name LIKE "%'.$_POST["search"]["value"].'%" 
 OR last_name LIKE "%'.$_POST["search"]["value"].'%" 
 ';
}*/

if(isset($_POST["order"]))
{
 $query .= 'ORDER BY '.$columns[$_POST['order']['0']['column']].' '.$_POST['order']['0']['dir'].' 
 ';
}
else
{
 $query .= 'ORDER BY '.$pkey.' DESC ';
}

$query1 = '';

if(isset($_POST["length"]) && $_POST["length"] != -1)
{
 $query1 = 'LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
}
//echo $query;exit;
$listresult=mysqli_query($connect, $query);
//print_r($listresult);exit;
$number_filter_row = mysqli_num_rows($listresult);

$result = mysqli_query($connect, $query . $query1);

$data = array();

while($row = mysqli_fetch_array($result))
{
 $sub_array = array();
 foreach($columns as $val)
 {
     if($_SESSION['role_name']!='admin'){
        $sub_array[$val]=$row[$val];
    }
    else{
        $sub_array[$val] = '<div contenteditable class="update" data-id="'.$row[$pkey].'" data-column="'.$val.'">' . $row[$val] . '</div>';
    }
 }
 if($_SESSION['role_name']=='admin')
    $sub_array['Actions'] = '<button type="button" name="delete" class="btn btn-danger btn-xs delete" id="'.$row[$pkey].'">Delete</button>';
/*else
    $sub_array['Actions'] = '<button type="button" name="delete" class="btn btn-danger btn-xs delete" id="'.$row["Idbilet"].'">Delete</button>';*/
 $data[] = $sub_array;
}

function get_all_data($connect)
{
    global $tablename;
 $query = "SELECT * FROM $tablename ";
 $result = mysqli_query($connect, $query);
 return mysqli_num_rows($result);
}
if($_SESSION['role_name']=='admin')
    $columns[]='Actions';
$output = array(
"columns" => $columns,
 "draw"    => isset($_POST["draw"])?intval($_POST["draw"]):0,
 "recordsTotal"  =>  get_all_data($connect),
 "recordsFiltered" => $number_filter_row,
 "data"    => $data
);

echo json_encode($output);

?>
