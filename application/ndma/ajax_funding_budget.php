<?php
include("../includes/classes/AllClasses.php");
$id = $_REQUEST['hidden_id'];
$amount=$_REQUEST['amount'];
$date=$_REQUEST['add_date'];
$date= date("Y-m-d",strtotime($date)); 
$type=$_REQUEST['amount_type'];
if($type=='negative'){
    $amount='-'.$amount;
}
$description=$_REQUEST['description'];
 $qry="INSERT INTO funding_source_budget (funding_source_id,amount,date,description,amount_type) VALUES($id,'$amount','$date','$description','$type')";
 mysql_query($qry);
 
?>