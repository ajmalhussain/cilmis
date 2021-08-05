<?php
/**
 * ajaxbatch
 * @package im
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
//Including AllClasses file
include("../includes/classes/AllClasses.php"); 
if (isset($_REQUEST['manufacturer']) && !empty($_REQUEST['manufacturer'])) {
    //get manufacturer
    $manufacturer = $_REQUEST['manufacturer'];
}

//check qty
if (isset($_REQUEST['qty']) && !empty($_REQUEST['qty'])) {
    //get qty
    $qty = str_replace(',', '', $_REQUEST['qty']);
}

if (isset($_REQUEST['unit_price']) && !empty($_REQUEST['unit_price'])) {
    //get status
    $unit_price = $_REQUEST['unit_price'];
}else $unit_price =0;
//if (isset($_REQUEST['receive_from']) && !empty($_REQUEST['receive_from'])) {
//    //get status
//    $funding_source = $_REQUEST['receive_from'];
//}
if (isset($_REQUEST['product']) && !empty($_REQUEST['product'])) {
    //get product
    $product = $_REQUEST['product'];
} 
if(isset($_REQUEST['po_master_id'])){
    $po_master_id=$_REQUEST['po_master_id'];
}
if(isset($_REQUEST['edit_field'])&&!(empty($_REQUEST['edit_field']))){
    $objPurchaseOrderProductDetails->pk_id=$_REQUEST['edit_field'];
}
$objPurchaseOrderProductDetails->item_id = $product;
$objPurchaseOrderProductDetails->manufacturer_id = $manufacturer;
$objPurchaseOrderProductDetails->shipment_quantity = $qty; 
//$objPurchaseOrderProductDetails->funding_source = $funding_source;
$objPurchaseOrderProductDetails->unit_price = $unit_price;
$objPurchaseOrderProductDetails->po_master_id=$po_master_id;
$objPurchaseOrderProductDetails->save();
?>