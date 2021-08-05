<?php
include("../includes/classes/AllClasses.php");

if(!empty($_REQUEST['id'])){
    $batch = $_REQUEST['id'];
    $qry_one = " SELECT AdjustQty2('".$batch."') as new_qty FROM DUAL ";
    $r = mysql_query($qry_one);
    $row = mysql_fetch_assoc($r);
   
    //echo 'batch qty fixed. New Qty :'.$row['new_qty'];
    
    
}
else{
    //echo 'Invalid request';
}

$id = $_REQUEST['id'];
$red = $_REQUEST['redirect'];
header('Location: '.APP_URL.$red.'.php?id='.$id);

?>