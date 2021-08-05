<?php
include("../includes/classes/AllClasses.php");
$json_array = array();
if (isset($_POST['manufacturer_id']) && isset($_POST['batch_no'])) {
    $manuf_id = $_POST['manufacturer_id'];
    
    $batch_no = $_POST['batch_no'];
    $wh_id = $_SESSION['user_warehouse']; 
    //select query
    //gets
    $strSql = "SELECT
                    stock_batch.batch_no,
                    stock_batch.batch_id,
                    stock_batch.batch_expiry,
                    stock_batch.item_id,
                    stock_batch.qty as Qty,
                    stock_batch.funding_source,
                    tbl_warehouse.wh_name as funding_source_name,
                    stakeholder_item.brand_name,
                    stakeholder.stkname as manuf_name,
                    itminfo_tab.itm_name,
                    itminfo_tab.generic_name,
                    stock_batch.`status`
                FROM
                    stock_batch
                INNER JOIN tbl_warehouse ON stock_batch.funding_source = tbl_warehouse.wh_id
                INNER JOIN itminfo_tab ON stock_batch.item_id = itminfo_tab.itm_id
                INNER JOIN stakeholder_item ON stock_batch.manufacturer = stakeholder_item.stk_id
                INNER JOIN stakeholder ON stakeholder_item.stkid = stakeholder.stkid
                WHERE
                    stock_batch.batch_no ='$batch_no'
                    AND stock_batch.`status` = 'Running' 
                    AND stock_batch.wh_id = $wh_id  
                    AND stock_batch.manufacturer = $manuf_id
                GROUP BY
                        stock_batch.batch_no";
//    $json_array['sql']=$strSql;
//query result
//    echo $strSql;exit;
    $rsSql = mysql_query($strSql) or die("Error");
    if (mysql_num_rows($rsSql) > 0) {
       while ($row = mysql_fetch_object($rsSql)) {
            $batch = array(
                'batch_id' => $row->batch_id,
                'batch_no' => $row->batch_no,
                'batch_expiry' => $row->batch_expiry,
                'available' => $row->Qty,
                'funding_source_id' => $row->funding_source,
                'funding_source_name' => $row->funding_source_name,
                'manuf_name' => $row->manuf_name,
                'item_name'=>$row->itm_name,
                'generic_name'=>$row->generic_name,
                'item_id'=>$row->item_id
            );
        }
   
//    print_r($batchinfo);exit;
        $batch_data= '' . $batch['batch_no']. '<input type="hidden" id="batch" name="batch" value="' . $batch['batch_id'] . '">';
        $json_array['batch']=$batch_data;
        $available_data =$batch['available'];
        $json_array['available'] = $available_data;
        $expiry_data =$batch['batch_expiry'];
        $json_array['batch_expiry'] = $expiry_data; 
        $json_array['manuf_name'] = $batch['manuf_name']; 
        $funding_source_id_data =$batch['funding_source_id'];
        $json_array['funding_source_id'] = $funding_source_id_data; 
        $funding_source_name_data =$batch['funding_source_name'];
        $json_array['funding_source_name'] = $funding_source_name_data; 
        
        $product_data = '' . $batch['item_name'] . ((!empty($batch['generic_name']) && $batch['generic_name'] != 'NULL') ? "[" . $batch['generic_name'] . "]" : "") . ''
            . '<input type="hidden" id="product" name="product" value="' . $batch['item_id'] . '">';
        $json_array['product'] = $product_data; 
        $json_array['msg']='Product Details Found'; 
        $json_array['err']='no'; 
         
     }
     else{
            $json_array['msg']='No batch / product info found. '; 
            $json_array['err']='yes'; 
        }
}
//print_r($json_array);exit;
echo json_encode($json_array);
?>