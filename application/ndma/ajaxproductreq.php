<?php

/**
 * ajaxproductcost
 * @package im
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */

//Including required files
include("../includes/classes/AllClasses.php");

//for product category
if(isset($_POST['product']) && !empty($_POST['product'])){
	$product = $_POST['product'];
	$cat = $objManageItem->GetProductReq($product);
	
	if($cat != false){
		echo number_format($cat);
	}
}
?>