<?php

// for CORS following two headers are mandatory , later we can fix the Origin to the server address only 
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: x-requested-with');
header('Content-Type: application/json');

include("../../includes/classes/Configuration.inc.php");
include("../../includes/classes/db.php");
include("include.php");
//token is in include.php

$display_data = $fetched = array();


@$wh_id = $_REQUEST['wh_id'];


$fetched_data = array();
if (!empty($_REQUEST['token']) && $_REQUEST['token'] == $token) {
    if (!empty($wh_id) && $wh_id != '') {
         $qry_product = "SELECT DISTINCT
                                                    itminfo_tab.itm_id,
                                                    itminfo_tab.itm_name
                                                FROM 
                                                    itminfo_tab
                                                    LEFT JOIN stock_batch ON itminfo_tab.itm_id = stock_batch.item_id
                                                WHERE
                                                    stock_batch.wh_id = '" . $wh_id . "' 
                                                ORDER BY
                                                    itminfo_tab.itm_name ASC  ";
            //echo $qry_product;exit;
            $prod_array = array();
            $t_array=array();
            $qryProdRes = mysql_query($qry_product);
            if (mysql_num_rows($qryProdRes) > 0) {
                while ($row1 = mysql_fetch_array($qryProdRes)) {
                   
                    $t_array['itm_id'] = $row1['itm_id'];
                    $t_array['itm_name'] = $row1['itm_name'];
                    $prod_array[] = $t_array;
                }
                 $fetched_data['products_list'] = $prod_array;
                $resp_code = '1';
                $msg = 'ok';
                $display_data['data'] = $fetched_data;
            }
            else{
                
                $resp_code = '5';
                $msg = 'No data found';
                $display_data['data'] = $fetched_data;
            }
            
           
        
    } else {
        $resp_code = '3';
        $msg = 'Invalid Request Parameters';
    }
} else {
    $resp_code = '4';
    $msg = 'Invalid Token';
}
$display_data['msg'] = $msg;
$display_data['response_code'] = $resp_code;

echo json_encode($display_data);

//------ Response Code : --------------------------------
//----1 = success, 
//----2 = wrong credentials, 
//----3 = Invalid Request Parameters , 
//----4 = Invalid Security Token
//----5 = No Data Found
//-------------------------------------------------------