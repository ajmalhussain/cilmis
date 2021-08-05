<?php
//NOTE: this service is for dhis2 FP Products.
// Supposed to only send FP products of the requested facode and month

// for CORS following two headers are mandatory , later we can fix the Origin to the server address only 
header('Access-Control-Allow-Origin: *');  
header('Access-Control-Allow-Headers: x-requested-with');  
header('Content-Type: application/json');

include("../../includes/classes/Configuration.inc.php");
include("../../includes/classes/db.php");

$display_data = $fetched  = array();
$token 		  = 'w5ur38DXe9';

@$month       = $_REQUEST['month'];
@$facode      = $_REQUEST['facode'];

//following is for testing only
if(!empty($_REQUEST['dev_test']) && $_REQUEST['dev_test'] == 'true')
{
	//$month       = '201812';
	$facode      = '173066';
	// 173066 is RHC DINA
	$display_data['testing']='yes';
}


$m_exp = str_split($month);
$hf_month = $m_exp[0].$m_exp[1].$m_exp[2].$m_exp[3].'-'.$m_exp[4].$m_exp[5].'-01';

$v_count = $d_count = 0 ;

//setting empty values
$items_list = array(2,9,7,1,5,8,13,14,32,31);
foreach($items_list as $itm){
	$fetched[$itm]['rcv']='';
	$fetched[$itm]['cb']='';
	$fetched[$itm]['iss']='';
}
 
 
if(!empty($_REQUEST['token']) && $_REQUEST['token']  == $token ){
	if(!empty($month) && $month !=''  && $hf_month<=date('Y-m-d') && !empty($facode) && $facode != '')
	{
			$qry_dist= "
				SELECT
					tbl_warehouse.dhis_code,
					tbl_warehouse.wh_id,
					tbl_warehouse.wh_name,
					tbl_hf_data.reporting_date,
					tbl_hf_data.item_id,
					tbl_hf_data.received_balance,
					tbl_hf_data.issue_balance,
					tbl_hf_data.closing_balance
					FROM
					tbl_hf_data
					INNER JOIN tbl_warehouse ON tbl_hf_data.warehouse_id = tbl_warehouse.wh_id
					WHERE
					tbl_hf_data.reporting_date = '".$hf_month."' AND
					tbl_hf_data.item_id IN (2, 9, 1, 7, 5, 8, 13, 14, 32, 31) AND
					tbl_warehouse.stkid = 7 AND
					tbl_warehouse.dhis_code = ".$facode."

			 ";

			//echo $qry_dist;exit;
			
			$Res3 =mysql_query($qry_dist);
			while($row = mysql_fetch_assoc($Res3))
			{
				$fetched[$row['item_id']]['rcv']=$row['received_balance'];
				$fetched[$row['item_id']]['iss']=$row['issue_balance'];
				$fetched[$row['item_id']]['cb']=$row['closing_balance'];
			}
			$msg='ok';
			$display_data['data']=$fetched;
	}
	else{
		$msg='Invalid Request';
	}
}
else{
	$msg='Invalid Token';
}
$display_data['msg']=$msg;
//$display_data['records_returned_vouchers']=count($fetched);
//$display_data['records_returned_details']=$d_count;


echo json_encode($display_data);