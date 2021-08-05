<?php

include("../includes/classes/AllClasses.php");

$strDo = "Add";
$nstkId = 0;
//remarks
$remarks = '';
//initialize 
$prod_date = $unit_price = '';
//echo '<pre>';print_r($_REQUEST);
//exit;
//check receive date
$_SESSION["success"] = 0;

if (isset($_REQUEST['id']) && !empty($_REQUEST['id'])) {
    //get receive date
    $id = $_REQUEST['id'];
    $strSql = "DELETE FROM shipments WHERE pk_id = $id";
    mysql_query($strSql) or die("Error Shipments Delete");
    $_SESSION["success"] = 1;
}


header("location:add_purchase_order.php");
exit;
?>