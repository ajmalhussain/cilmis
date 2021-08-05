<?php
include("../includes/classes/AllClasses.php");
if (isset($_REQUEST['id']) && !empty($_REQUEST['id'])) {
    $id = $_REQUEST['id'];
    $objStockDetail->deleteReceive($id);
    if (!empty($_REQUEST['p']) && $_REQUEST['p'] == 'stock') {
		$_SESSION['success'] = 2;
        redirect("bar_stock_receive.php");
        exit;
    }
	$_SESSION['success'] = 2;
    redirect("bar_receive.php");
    exit;
}
if(isset($_POST['detailId']) && !empty($_POST['detailId'])){
	$detailId = $_POST['detailId'];
	$batchId = $_POST['batchId'];
	// Delete Issue Entry
	$objStockDetail->deleteReceive($detailId);
}