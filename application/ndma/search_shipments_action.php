<?php
//echo '<pre>';print_r($_REQUEST);exit;
include("../includes/classes/AllClasses.php");
$strSql = "SELECT
            itminfo_tab.itm_id,
            itminfo_tab.itm_name,
            itminfo_tab.itm_type,
            shipments.*
            FROM
            itminfo_tab
            INNER JOIN shipments ON shipments.item_id = itminfo_tab.itm_id
            WHERE
            shipments.pk_id  = ".$_REQUEST['shipment_id'] ;
//query result
$rsSql = mysql_query($strSql) or die("Error GetProduct data");
$prev_data = array();
if (mysql_num_rows($rsSql) > 0) {
    $prev_data = mysql_fetch_assoc($rsSql);
}

if(!empty($_REQUEST['action']) && $_REQUEST['action'] == 'Edit')
{
    $qty = str_replace(',','',$_REQUEST['qty']);
    $sql = "UPDATE shipments SET ";
    $sql .= " shipment_date = '".$_REQUEST['receive_date']."', ";
    $sql .= " reference_number = '".$_REQUEST['refrence_number']."', ";
    //$sql .= " stk_id = '".$_REQUEST['receive_from']."', ";
    $sql .= " shipment_quantity = '".$qty."', ";
    
    if(!empty($_REQUEST['unit_price'])){
        $sql .= " unit_price = '".$_REQUEST['unit_price']."', ";
    }
    
    $sql .= " `status` = '".$_REQUEST['status']."' ";
    $sql .= "WHERE pk_id=" . $_REQUEST['shipment_id'];
   //echo $sql;exit;
    mysql_query($sql);
    
}
elseif(!empty($_REQUEST['action']) && $_REQUEST['action'] == 'cancel')
{
    $sql = "UPDATE shipments SET status = 'Cancelled'  WHERE pk_id=" . $_REQUEST['shipment_id'];
    mysql_query($sql);
}
header("location:search_purchase_order.php");
exit;
?>