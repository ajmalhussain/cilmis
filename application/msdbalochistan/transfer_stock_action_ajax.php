<?php
//echo '<pre>';print_r($_REQUEST);exit;
include("../includes/classes/AllClasses.php");

$strDo = "Add";
$nstkId = 0;
$remarks = '';
if (isset($_REQUEST['loc_id']) && !empty($_REQUEST['loc_id'])) {
    $locId = $_REQUEST['loc_id'];
}
if (isset($_REQUEST['item_id']) && !empty($_REQUEST['item_id'])) {
    $item_id = $_REQUEST['item_id'];
}

if (isset($_REQUEST['qty_carton']) && !empty($_REQUEST['qty_carton'])) {
    $carton_qty = $_REQUEST['qty_carton'];
}

if (isset($_REQUEST['stock_detail_id']) && !empty($_REQUEST['stock_detail_id'])) {
    $stock_detail = $_REQUEST['stock_detail_id'];
}
if (isset($_REQUEST['batch_id']) && !empty($_REQUEST['batch_id'])) {
    $batch_id = $_REQUEST['batch_id'];
}


if (isset($_REQUEST['transfer_qty']) && !empty($_REQUEST['transfer_qty'])) {
    $quantity = $_REQUEST['transfer_qty'];
}
if (isset($_REQUEST['transfer_to']) && !empty($_REQUEST['transfer_to'])) {
    $transfer_to = $_REQUEST['transfer_to'];
}
$err='';
$t_qty = 0;
foreach($quantity as $k => $qty){
    
    if(!empty($qty) && $qty > 0){
        $t_qty+=$qty;
        if(empty($transfer_to[$k])){
            $err = 'Please select transfer locations against all quantities.';
        }
    }
    if($t_qty<=0) $err ='No quantities entered for transfer.';
}
$resp = array();
if(empty($err) || $err ==''){
   
        $placement_transaction = '90';
        $placement_transaction_to = '89';
        $created_date = date('Y-m-d H:i:s');
        $created_by = $_SESSION['user_id'];
        $is_placed = "-1";
        
        foreach($quantity as $k => $qty){
            
            if(!empty($qty) && $qty > 0){
                if(!empty($transfer_to[$k])){
                        $quantityActual = $qty;
                        $transferFromQuery = "insert into placements set placement_location_id=" . $locId . ",quantity='-" . $quantityActual . "',is_placed='" . $is_placed . "',stock_batch_id=" . $batch_id . ",stock_detail_id=" . $stock_detail . ", placement_transaction_type_id=" . $placement_transaction . ",created_date='" . $created_date . "',created_by=" . $created_by . "";
                        $transferRes = mysql_query($transferFromQuery) or $err='Some err in transfer';
                        if ($transferRes) {
                            $transferFromQuery = "insert into placements set placement_location_id=" . $transfer_to[$k] . ",quantity='" . $quantityActual . "',is_placed='" . $is_placed . "',stock_batch_id=" . $batch_id . ",stock_detail_id=" . $stock_detail . ", placement_transaction_type_id=" . $placement_transaction . ",created_date='" . $created_date . "',created_by=" . $created_by . "";
                            $transferToRes = mysql_query($transferFromQuery) or $err='Some error in transfer';
                        }
                }else $err = 'Error';
            }else $err = 'Error';
        }
        
        
        
        
       

        
        $resp['resp_type'] = 'success'; 
        $resp['msg'] = ' Quantities transferred.'; 
}
else
{
        $resp['resp_type'] = 'error'; 
        $resp['msg'] = $err; 
}
header('Content-Type: application/json');
$j = json_encode($resp);
echo $j;