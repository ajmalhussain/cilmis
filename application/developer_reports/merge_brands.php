<?php
echo '<pre>';
print_r($_REQUEST);
//exit;

include("../includes/classes/AllClasses.php");
if(!empty($_REQUEST['merge_into'])){
    $brands = implode(',',$_REQUEST['merge_these']);
    $merge_into = $_REQUEST['merge_into'];
    $qry = " UPDATE stock_batch SET manufacturer =  '".$merge_into."' WHERE manufacturer IN (".$brands.") ;";
    mysql_query($qry);
    
    
    $qry3 = " SELECT count(stock_batch.batch_id) as cnt FROM stock_batch  WHERE  manufacturer  IN (".$brands.") AND manufacturer <> '".$merge_into."'; ";
    $res =mysql_query($qry3);
    $row = mysql_fetch_assoc($res);

    if($row['cnt']==0){
        $qry2 = " DELETE FROM stakeholder_item WHERE stk_id IN (".$brands.") AND stk_id <> '".$merge_into."'; ";
        mysql_query($qry2);
    }
}
elseif(!empty($_REQUEST['delete_this'])){

     $qry3 = " SELECT count(stock_batch.batch_id) as cnt FROM stock_batch  WHERE stock_batch.item_id = '".$_REQUEST['itm_id']."' AND manufacturer  =  '".$_REQUEST['delete_this']."' ; ";
     $res =mysql_query($qry3);
    $row = mysql_fetch_assoc($res);
    
    if($row['cnt']==0){
    
        $qry4 = " DELETE FROM stakeholder_item WHERE stk_id  =  '".$_REQUEST['delete_this']."' and stk_item = '".$_REQUEST['itm_id']."'; ";
        mysql_query($qry4);
    }
}
elseif(!empty($_REQUEST['split_prod'])){
    //ins itminfo_tab
    //update stakeholder_item
    //batches
    
    foreach($_REQUEST['split_these'] as $k => $old_brand){
        $new_name = $_REQUEST['new_name'][$old_brand];
        $old_itm = $_REQUEST['itm_id'];
        
        $qryget = "SELECT * from itminfo_tab WHERE itm_id = '".$old_itm."'; ";
        $res = mysql_query($qryget);
        $row = mysql_fetch_assoc($res);
        $qry5 = "INSERT INTO itminfo_tab SET itm_name = '".$new_name."' , generic_name='".$row['generic_name']."' 
            , old_prod_id = '".$old_itm."', dis_code= '".$_REQUEST['dis_code'][$old_brand]."'
            , method_type= '".$row['method_type']."', itm_type='".$row['itm_type']."', itm_category='".$row['itm_category']."'
            , item_unit_id='".$row['item_unit_id']."', itm_status='1' ; ";
        mysql_query($qry5);
        $new_itm = mysql_insert_id();
        
        $qry6 = " UPDATE stakeholder_item SET stk_item =  '".$new_itm."' WHERE stk_id = '".$old_brand."' AND stk_item = '".$old_itm."' ;";
        mysql_query($qry6);
        $qry7 = " UPDATE stock_batch SET item_id =  '".$new_itm."' WHERE manufacturer = '".$old_brand."' AND item_id = '".$old_itm."'  ;";
        mysql_query($qry7);
        $qry8 = " INSERT INTO stakeholder_item SET stk_item =  '".$new_itm."',stkid='7' ;";
        mysql_query($qry8);
        $qry9 = " UPDATE itminfo_tab SET itmrec_id =  'IT-".$new_itm."' WHERE itm_id = '".$new_itm."'  ;";
        mysql_query($qry9);
//        exit;
    }
}

redirect('multi_manuf_prods_2.php?itm_id='.$_REQUEST['itm_id']);

?>
