<?php
ob_start();

//  include 'db_connection.php';
include("../includes/classes/Configuration.inc.php");
include(APP_PATH . "includes/classes/db.php");
//echo '<pre>';print_r($_POST);
$province = $_REQUEST['prov_sel'];
$stk = $_REQUEST['stakeholder'];
$year=$_REQUEST['year'];
$planned=$_REQUEST['planned'];
$expenditure=$_REQUEST['expenditure'];
$sql  = "INSERT INTO fp_procurement_details(prov_id,stk_id,fiscal_year,planned,expenditure) VALUES($province,$stk,'$year','$planned','$expenditure')";
//print_r($sql);exit;
$ress = mysql_query($sql);  
?>
 