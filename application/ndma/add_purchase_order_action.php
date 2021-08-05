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

if (isset($_REQUEST['currency']) && !empty($_REQUEST['currency'])) {
    //get manufacturer
    $currency = $_REQUEST['currency'];
}

//check qty
if (isset($_REQUEST['qty']) && !empty($_REQUEST['qty'])) {
    //get qty
    $qty = str_replace(',', '', $_REQUEST['qty']);
}
//check status
$objPurchaseOrder->po_accept_date = $objPurchaseOrder->po_cancelled_date = $objPurchaseOrder->po_delete_date = "1970-01-01";
if (isset($_REQUEST['postatus']) && !empty($_REQUEST['postatus'])) {
    //get status
    $status = $_REQUEST['postatus'];

    switch ($status) {
        case 'Active':
            $objPurchaseOrder->po_accept_date = dateToDbFormat($_REQUEST['status_date']);
            break;
        case 'Canceled':
            $objPurchaseOrder->po_cancelled_date = dateToDbFormat($_REQUEST['status_date']);
            break;
        case 'InActive':
            $objPurchaseOrder->po_delete_date = dateToDbFormat($_REQUEST['status_date']);
            break;
    }
}
if(isset($_REQUEST['completed'])){
    $status="Completed";
}
if(isset($_REQUEST['remarks'])){
    $remarks=$_REQUEST['remarks'];
}
//print_r($status);exit;
if (isset($_REQUEST['system_po']) && !empty($_REQUEST['system_po'])) {
    //get status
    $system_po = $_REQUEST['system_po'];
}

if (isset($_REQUEST['unit_price']) && !empty($_REQUEST['unit_price'])) {
    //get status
    $unit_price = $_REQUEST['unit_price'];
}else $unit_price =0;
    
$dollar_rate = 1;
if (isset($_REQUEST['drate']) && !empty($_REQUEST['drate'])) {
    //get status
    $dollar_rate = $_REQUEST['drate'];
}else $dollar_rate =0;
if (isset($_REQUEST['contact_no']) && !empty($_REQUEST['contact_no'])) {
    //get status
    $contact_no = $_REQUEST['contact_no'];
}
if (isset($_REQUEST['signing_date']) && !empty($_REQUEST['signing_date'])) {
    //get status
    $signing_date = $_REQUEST['signing_date'];
}
if (isset($_REQUEST['receive_from']) && !empty($_REQUEST['receive_from'])) {
    //get status
    $funding_source = $_REQUEST['receive_from'];
}
if (isset($_REQUEST['adv_payment_release']) && !empty($_REQUEST['adv_payment_release'])) {
    //get status
    $adv_payment_release = $_REQUEST['adv_payment_release'];
}else{
    $adv_payment_release = 0;
}
    
if (isset($_REQUEST['contract_delivery_date']) && !empty($_REQUEST['contract_delivery_date'])) {
    //get status
    $contract_delivery_date = $_REQUEST['contract_delivery_date'];
}
if (isset($_REQUEST['po_accept_date']) && !empty($_REQUEST['po_accept_date'])) {
    //get status
    $po_accept_date = $_REQUEST['po_accept_date'];
}

if (isset($_REQUEST['po_date']) && !empty($_REQUEST['po_date'])) {
    //get status
    $po_date = $_REQUEST['po_date'];
}


if (isset($_REQUEST['local_foreign']) && !empty($_REQUEST['local_foreign'])) {
    $local_foreign = $_REQUEST['local_foreign'];
}
if (isset($_REQUEST['country']) && !empty($_REQUEST['country'])) {
    $country = $_REQUEST['country'];
}else $country=0;
if (isset($_REQUEST['sub_cat']) && !empty($_REQUEST['sub_cat'])) {
    $sub_cat = $_REQUEST['sub_cat'];
}else $sub_cat='';
if (isset($_REQUEST['tender_no']) && !empty($_REQUEST['tender_no'])) {
    $tender_no = $_REQUEST['tender_no'];
}

$isupdate = 'No';
if (isset($_REQUEST['po_id']) && !empty($_REQUEST['po_id'])) {
    //get status
    $po_id = $_REQUEST['po_id'];
    $objPurchaseOrder->pk_id = $po_id;

    $isupdate = 'Yes';
}



$objPurchaseOrder->reference_number = $refrence_number;
$objPurchaseOrder->item_id = $product;
$objPurchaseOrder->manufacturer = $manufacturer;
//$dataArr = explode(' ', $receive_date);
//$time = date('H:i:s', strtotime($dataArr[1] . $dataArr[2]));
//transaction date

$objPurchaseOrder->shipment_quantity = $qty;
$objPurchaseOrder->stk_id = $receive_from;
$objPurchaseOrder->procured_by = 0;
$objPurchaseOrder->status = $status;
$objPurchaseOrder->created_date = date("Y-m-d");
$objPurchaseOrder->created_by = $_SESSION['user_id'];
$objPurchaseOrder->modified_by = $_SESSION['user_id'];
$objPurchaseOrder->wh_id = $_SESSION['user_warehouse'];
$objPurchaseOrder->po_number = $system_po;
$objPurchaseOrder->unit_price = $unit_price;
$objPurchaseOrder->po_date = dateToDbFormat($po_date);

$objPurchaseOrder->dollar_rate = $dollar_rate;
$objPurchaseOrder->contact_no = $contact_no;
$objPurchaseOrder->signing_date = dateToDbFormat($signing_date);
$objPurchaseOrder->funding_source = $funding_source;
$objPurchaseOrder->adv_payment_release = $adv_payment_release;
$objPurchaseOrder->contract_delivery_date = dateToDbFormat($contract_delivery_date);
$objPurchaseOrder->currency = $currency;
$objPurchaseOrder->local_foreign = $local_foreign;
$objPurchaseOrder->country = $country;
$objPurchaseOrder->sub_cat = $sub_cat;
$objPurchaseOrder->tender_no = $tender_no;
$objPurchaseOrder->remarks = $remarks;

if(!isset($objPurchaseOrder->country) || empty($objPurchaseOrder->country)) $objPurchaseOrder->country = 130; //default value of pakistan for local purchase

$po_id_add = $objPurchaseOrder->save();

$objPurchaseOrderDetails->po_id = ($isupdate == 'Yes' ? $po_id : $po_id_add );
$objPurchaseOrderDetails->delete();

$objPurchaseOrderDetails->delivered = 0;
if (isset($_REQUEST['ddate']) && count($_REQUEST['ddate']) > 0) {
    for ($i = 1; $i <= 6; $i++) {
        if (!empty($_REQUEST['ddate'][$i])) {
            $objPurchaseOrderDetails->po_id = ($isupdate == 'Yes' ? $po_id : $po_id_add );
            $objPurchaseOrderDetails->delivery_date = dateToDbFormat($_REQUEST['ddate'][$i]);
            $objPurchaseOrderDetails->total_unit = $_REQUEST['dunit'][$i];
            $objPurchaseOrderDetails->delivered = $_REQUEST['ddelivered'][$i];
            $objPurchaseOrderDetails->balance = $_REQUEST['dbalance'][$i];
            $objPurchaseOrderDetails->save();
        }
    }
}

$_SESSION["success"] = 1;
if(isset($_REQUEST['save_exit'])){
    header("location:search_purchase_order.php");
}elseif(isset($_REQUEST['save_stay'])){
    header("location:add_purchase_order.php?DO=Update&po_id=".$_REQUEST['po_id']);
}
exit;
?>