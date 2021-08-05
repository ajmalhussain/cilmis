<?php
//NOTE: this service is for mims.
// Supposed to only send MSD vouchers, issued from provincial stores to district stores.

include("../../includes/classes/Configuration.inc.php");
include("../../includes/classes/db.php");

@$date      = $_REQUEST['date'];
@$wh_from      = $_REQUEST['wh_from'];
@$wh_to      = $_REQUEST['wh_to'];
$token 		= 'Z4v5XwDXe9';

$display_data = $fetched  = array();
$v_count = $d_count = 0 ;


if(!empty($_REQUEST['token']) && $_REQUEST['token']  == $token ){
	if(!empty($date) && $date !='' && $date >= '2018-01-01' && $date<=date('Y-m-d'))
	{
            
            $where_clause = "";
            if(!empty($wh_from)){
                $where_clause .= " AND tbl_stock_master.WHIDFrom = '".$wh_from."' ";
            }
            $where_clause = "";
            if(!empty($wh_to)){
                $where_clause .= " AND tbl_stock_master.WHIDTo = '".$wh_to."' ";
            }
            
			$qry_dist= "
				SELECT
					tbl_stock_master.PkStockID,
					tbl_stock_master.TranDate,
					tbl_stock_master.TranNo,
tbl_stock_master.TranRef,
					tbl_stock_master.WHIDFrom AS from_wh,
					frm.wh_name AS from_wh_name,
					tbl_stock_master.WHIDTo AS to_wh,
					to_wh.wh_name AS to_wh_name,
					tbl_stock_detail.PkDetailID,
					tbl_stock_detail.BatchID,
					tbl_stock_detail.Qty,
					stock_batch.batch_no,
					stock_batch.item_id,
					itminfo_tab.itm_name,
tbl_itemunits.UnitType,
stock_batch.batch_expiry,
stock_batch.manufacturer as manufacturer_id,
s2.stkname as manufacturer_name,
stock_batch.funding_source,
tbl_warehouse.wh_name as funding_source_name
				FROM
					tbl_stock_master
					INNER JOIN tbl_warehouse AS frm ON tbl_stock_master.WHIDFrom = frm.wh_id
					INNER JOIN stakeholder ON frm.stkofficeid = stakeholder.stkid
					INNER JOIN tbl_warehouse AS to_wh ON tbl_stock_master.WHIDTo = to_wh.wh_id
					INNER JOIN tbl_stock_detail ON tbl_stock_master.PkStockID = tbl_stock_detail.fkStockID
					INNER JOIN stock_batch ON tbl_stock_detail.BatchID = stock_batch.batch_id
					INNER JOIN itminfo_tab ON stock_batch.item_id = itminfo_tab.itm_id
LEFT JOIN tbl_itemunits ON itminfo_tab.item_unit_id = tbl_itemunits.pkUnitID
INNER JOIN stakeholder_item ON stock_batch.manufacturer = stakeholder_item.stk_id
INNER JOIN stakeholder AS s2 ON stakeholder_item.stkid = s2.stkid
INNER JOIN tbl_warehouse ON stock_batch.funding_source = tbl_warehouse.wh_id
				WHERE
					tbl_stock_master.CreatedOn = '".$date."' AND
					frm.prov_id = 1 AND
					stakeholder.lvl = 2 AND
					frm.is_allowed_im = 1 AND
					frm.stkid IN (7, 145) AND
					tbl_stock_master.TranTypeID = 2 AND
					tbl_stock_master.temp = 0
                                        ".$where_clause."
			 ";

			//echo $qry_dist;
			
			
			$Res3 =mysql_query($qry_dist);
			while($row_dist = mysql_fetch_assoc($Res3))
			{
				$fetched[$row_dist['PkStockID']]['voucher_info']['issue_no']=$row_dist['TranNo'];
				$fetched[$row_dist['PkStockID']]['voucher_info']['voucher_id']=$row_dist['PkStockID'];
				$fetched[$row_dist['PkStockID']]['voucher_info']['from_wh']=$row_dist['from_wh'];
				$fetched[$row_dist['PkStockID']]['voucher_info']['from_wh_name']=$row_dist['from_wh_name'];
				$fetched[$row_dist['PkStockID']]['voucher_info']['to_wh']=$row_dist['to_wh'];
				$fetched[$row_dist['PkStockID']]['voucher_info']['to_wh_name']=$row_dist['to_wh_name'];
				$fetched[$row_dist['PkStockID']]['voucher_info']['TranRef']=$row_dist['TranRef'];
				
				$det_arr=array();
				$det_arr['item_id'] = $row_dist['item_id']; 
				$det_arr['item_name'] = $row_dist['itm_name']; 
				$det_arr['unit'] = $row_dist['UnitType']; 
				$det_arr['batch_primary_id'] = $row_dist['BatchID']; 
				$det_arr['batch_display_no'] = $row_dist['batch_no']; 
				$det_arr['batch_expiry'] = $row_dist['batch_expiry']; 
				$det_arr['manufacturer_id'] = $row_dist['manufacturer_id']; 
				$det_arr['manufacturer_name'] = $row_dist['manufacturer_name']; 
				$det_arr['funding_source'] = $row_dist['funding_source']; 
				$det_arr['funding_source_name'] = $row_dist['funding_source_name']; 
				$det_arr['qty'] = abs($row_dist['Qty']); 
				$fetched[$row_dist['PkStockID']]['issuance_details'][$row_dist['PkDetailID']]=$det_arr;
				
				$d_count++;
			}
			$msg='ok';
	}
	else{
		$msg='Invalid Request';
	}
}
else{
	$msg='Invalid Token';
}
$display_data['msg']=$msg;
$display_data['records_returned_vouchers']=count($fetched);
$display_data['records_returned_details']=$d_count;
$display_data['data']=$fetched;

header('Content-Type: application/json');
echo json_encode($display_data);