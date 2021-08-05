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


if (isset($_REQUEST['receive_date']) && !empty($_REQUEST['receive_date'])) {
    //get receive date
    $receive_date = $_REQUEST['receive_date'];
}
//check refrence number
if (isset($_REQUEST['refrence_number']) && !empty($_REQUEST['refrence_number'])) {
    //get receive ref
    $refrence_number = $_REQUEST['refrence_number'];
}
//check receive from
if (isset($_REQUEST['receive_from']) && !empty($_REQUEST['receive_from'])) {
    //get receive from
    $receive_from = $_REQUEST['receive_from'];
}
//check receive from
if (isset($_REQUEST['procured_by']) && !empty($_REQUEST['procured_by'])) {
    //get receive from
    $procured_by = $_REQUEST['procured_by'];
}
//check product
if (isset($_REQUEST['product']) && !empty($_REQUEST['product'])) {
    //get product
    $product = $_REQUEST['product'];
}
//check manufacturer
if (isset($_REQUEST['manufacturer']) && !empty($_REQUEST['manufacturer'])) {
    //get manufacturer
    $manufacturer = $_REQUEST['manufacturer'];
}

//check qty
if (isset($_REQUEST['qty']) && !empty($_REQUEST['qty'])) {
    //get qty
    $qty = str_replace(',', '', $_REQUEST['qty']);
}
//check status
if (isset($_REQUEST['status']) && !empty($_REQUEST['status'])) {
    //get status
    $status = $_REQUEST['status'];
}

if (isset($_REQUEST['system_po']) && !empty($_REQUEST['system_po'])) {
    //get status
    $system_po = $_REQUEST['system_po'];
}

if (isset($_REQUEST['unit_price']) && !empty($_REQUEST['unit_price'])) {
    //get status
    $unit_price = $_REQUEST['unit_price'];
}

if (isset($_REQUEST['po_date']) && !empty($_REQUEST['po_date'])) {
    //get status
    $po_date = $_REQUEST['po_date'];
}


$objshipments->reference_number = $refrence_number;
$objshipments->item_id = $product;
$objshipments->manufacturer = $manufacturer;
//$dataArr = explode(' ', $receive_date);
//$time = date('H:i:s', strtotime($dataArr[1] . $dataArr[2]));
//transaction date

$objshipments->shipment_date = dateToDbFormat($receive_date);
$objshipments->shipment_quantity = $qty;
$objshipments->stk_id = $receive_from;
$objshipments->procured_by = $procured_by;
$objshipments->status = $status;
$objshipments->created_date = date("Y-m-d");
$objshipments->created_by = $_SESSION['user_id'];
$objshipments->modified_by = $_SESSION['user_id'];
$objshipments->wh_id = $_SESSION['user_warehouse'];
$objshipments->po_number = $system_po;
$objshipments->unit_price = $unit_price;
$objshipments->po_date = dateToDbFormat($po_date);

$shipments = $objshipments->save();

$strSql = "SELECT
                itminfo_tab.itm_id,
                itminfo_tab.itm_name,
                itminfo_tab.itm_type
            FROM
                itminfo_tab
            WHERE
                itminfo_tab.itm_id  = " . $product;
//query result
$rsSql = mysql_query($strSql) or die("Error GetProduct data");
$prod_data = array();
if (mysql_num_rows($rsSql) > 0) {
    $prod_data = mysql_fetch_assoc($rsSql);
}

$res = $objstakeholderitem->get_manufacturer_by_id($manufacturer);
$manuf_data = mysql_fetch_assoc($res);

$objloc->PkLocID = $procured_by;
$loc_name = $objloc->get_location_name();

$_SESSION["success"] = 1;
header("location:add_purchase_order.php");
exit;
?>