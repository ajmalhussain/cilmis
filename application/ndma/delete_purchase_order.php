<?php

include("../includes/classes/AllClasses.php");
//echo '<pre>';
//print_r($_REQUEST);
//echo '</pre>';
//exit;
if (isset($_REQUEST['detail_id']) && !empty($_REQUEST['detail_id'])) {
    //get receive date
    $detail_id = $_REQUEST['detail_id'];
    
    $objPurchaseOrderProductDetails->pk_id = $detail_id;
    $objPurchaseOrderProductDetails->delete();


}
//exit;
$_SESSION["success"] = 2;
header("location: search_purchase_order.php");
