<?php

include("../includes/classes/AllClasses.php");
//print_r($_REQUEST);exit;
$objPurchaseOrderDetails->po_id = $_REQUEST['master_id'];
$objPurchaseOrderDetails->delete();
$objPurchaseOrderDetails->delivered = 0;
if (isset($_REQUEST['ddate']) && count($_REQUEST['ddate']) > 0) {
    for ($i = 1; $i <= 6; $i++) {
        if (!empty($_REQUEST['ddate'][$i])) {
            $objPurchaseOrderDetails->po_id = $_REQUEST['master_id'];
            $objPurchaseOrderDetails->delivery_date = dateToDbFormat($_REQUEST['ddate'][$i]);
            $objPurchaseOrderDetails->total_unit = $_REQUEST['dunit'][$i];
            $objPurchaseOrderDetails->delivered = $_REQUEST['ddelivered'][$i];
            $objPurchaseOrderDetails->balance = $_REQUEST['dbalance'][$i];
            $objPurchaseOrderDetails->warehouse_id = $_REQUEST['dwarehouse'][$i];
            $objPurchaseOrderDetails->save();
        }
    }
} 
//header("location:search_purchase_order.php");
//exit;
?>