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


@$userid = $_REQUEST['userid'];
@$month = $_REQUEST['month'];
@$itm_id = $_REQUEST['itm_id'];
@$issuance = $_REQUEST['issuance'];
@$adj_p = $_REQUEST['adj_p'];
@$adj_n = $_REQUEST['adj_n'];


$fetched_data = array();
if (!empty($_REQUEST['token']) && $_REQUEST['token'] == $token) {
    if (!empty($userid) && $userid != '' && !empty($month) && $month != '' && !empty($itm_id) && $itm_id != '' && !empty($issuance) && $issuance != '' && !empty($adj_p) && $adj_p != '' && !empty($adj_n) && $adj_n != '') {
        $qry_save_data = "INSERT INTO "
                                . "tbl_hf_data "
                        . "SET "
                                 . "item_id=$itm_id,issue_balance=$issuance,adjustment_positive=$adj_p, adjustment_negative=$adj_n";
//        echo $qry_save_data;exit;
        $Res3 = mysql_query($qry_save_data);

        if ($Res3) {

                $fetched_data['msg'] = 'data inserted sucessfully';

//				print_r($fetched_data);exit;
//             echo "<pre>";
//             print_r($fetched_data);exit;
            $resp_code = '1';
            $msg = 'ok';
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