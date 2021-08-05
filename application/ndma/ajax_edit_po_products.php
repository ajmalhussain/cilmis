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
$product=$objPurchaseOrderProductDetails->find_by_id($id);
while ($row = mysql_fetch_array($product)) {
    $prod_arr['item_id']=$row['item_id'];
    $prod_arr['manuf_id']=$row['manufacturer_id'];
    $prod_arr['funding_source']=$row['funding_source'];
     $prod_arr['qty']=$row['shipment_quantity'];
     $prod_arr['unit_price']=number_format($row['unit_price'],2,'.','');
     $prod_arr['amount']=($row['shipment_quantity'] * $row['unit_price']);
     ///hereeeeee
    $itm_req =  $objManageItem->GetProductReq($prod_arr['item_id']);
     if(!empty($itm_req) && $itm_req != false){
        $prod_arr['item_req'] = $itm_req;
     }else{
        $prod_arr['item_req'] = '';
     }
}
echo json_encode($prod_arr);
?>
 