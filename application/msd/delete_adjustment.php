<?php
//echo '<pre>';print_r($_REQUEST);exit;
include("../includes/classes/AllClasses.php");

//Delete records after stock is issued
if(isset($_POST['detailId'])){
	$detailId = $_POST['detailId'];
	// Delete Issue Entry
	$resp = $objStockDetail->delete_adjustment($detailId);
        echo $resp;
}