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
$id=$_REQUEST['id'];
$objPurchaseOrderProductDetails->pk_id = $id;
if($objPurchaseOrderProductDetails->delete()){
    echo 'Deleted';
}
else{
    echo 'Some Issue Occured';
}
?>
 