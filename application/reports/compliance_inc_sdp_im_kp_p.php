<?php
$chart_id = 'compliance_sdp';
$where_clause = "   ";
$where_clause .= " AND tbl_warehouse.prov_id =$sel_prov ";
$where_clause .= " AND tbl_warehouse.stkid ='".$_SESSION['user_stakeholder1']."' ";
//get total number of facilities in province
$qry_1 = "  
            SELECT
                tbl_warehouse.dist_id,
                COUNT( DISTINCT tbl_warehouse.wh_id ) AS totalWH,
                tbl_warehouse.reporting_start_month
            FROM
                    tbl_warehouse ";
$qry_1 .= "
            INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
            INNER JOIN stakeholder_item ON tbl_warehouse.stkid = stakeholder_item.stkid
            WHERE
                stakeholder.lvl = 7
                
            $where_clause
            GROUP BY
                tbl_warehouse.dist_id,
                tbl_warehouse.reporting_start_month
                ";
//echo $qry_1;exit;
$res_1 = mysql_query($qry_1);
$total_sdps = array();
while ($row_1 = mysql_fetch_array($res_1)) {
    $total_sdps[$row_1['dist_id']][$row_1['reporting_start_month']] = $row_1['totalWH'];

    if (!isset($total_sdps['all'][$row_1['reporting_start_month']]))
        $total_sdps['all'][$row_1['reporting_start_month']] = 0;
    $total_sdps['all'][$row_1['reporting_start_month']] += $row_1['totalWH'];
}
//echo'<pre>'; print_r($total_sdps);exit;
//counting the disabled facilities 
$disabled_qry = "
                    SELECT

                        COUNT(DISTINCT warehouse_status_history.warehouse_id) as cnt,
                        tbl_warehouse.dist_id,
                        warehouse_status_history.reporting_month
                    FROM
                            warehouse_status_history
                    INNER JOIN tbl_warehouse ON warehouse_status_history.warehouse_id = tbl_warehouse.wh_id
                    INNER JOIN stakeholder_item ON tbl_warehouse.stkid = stakeholder_item.stkid
                     ";
$disabled_qry .= "
            
                    
                    INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
                    WHERE
                            warehouse_status_history.reporting_month BETWEEN '" . $from_date . "' and '" . $to_date . "'
                            AND warehouse_status_history.`status` = 0
                            /*AND tbl_warehouse.hf_type_id NOT IN (5, 2, 3, 9, 6, 7, 8, 12, 10, 11)*/
                            AND stakeholder.lvl=7
                            $where_clause
                    GROUP BY
                            tbl_warehouse.dist_id,
                            warehouse_status_history.reporting_month
            ";
//echo $disabled_qry;exit;
$res_d = mysql_query($disabled_qry);
$disabled_count = array();
while ($row_d = mysql_fetch_array($res_d)) {
    $disabled_count[$row_d['dist_id']][$row_d['reporting_month']] = $row_d['cnt'];
    if (empty($disabled_count['all'][$row_d['reporting_month']]))
        $disabled_count['all'][$row_d['reporting_month']] = 0;
    $disabled_count['all'][$row_d['reporting_month']] += $row_d['cnt'];
}
//echo '<pre>';print_r($disabled_count);exit;      
//making list of items , to display list incase no data entry is found

$w_clause = "";
if (!empty($stk))
    $w_clause .= " stakeholder_item.stkid in (" . $stk . ")  ";
if (empty($itm)) {
    $w_clause .= " stakeholder_item.stkid = " . $_SESSION['user_stakeholder'];
} else if (!empty($itm))
    $w_clause .= " AND itminfo_tab.itm_id in (" . $itm . ")  ";

$qry_1 = "  SELECT
                        itminfo_tab.itmrec_id,
                        itminfo_tab.itm_name,
                        itminfo_tab.itm_id
                    FROM
                        itminfo_tab
                        INNER JOIN stakeholder_item ON stakeholder_item.stk_item = itminfo_tab.itm_id
                    WHERE 
                        $w_clause

                    ORDER BY
                        itminfo_tab.frmindex ASC
                ";
//echo $qry_1;
//exit;
$res_1 = mysql_query($qry_1);
$itm_arr = array();
while ($row_1 = mysql_fetch_array($res_1)) {
    $itm_arr[$row_1['itm_id']] = $row_1['itm_name'];
}


//query for getting reported facilities
//$q_reporting = "SELECT
//                                        tbl_warehouse.stkid,
//                                        COUNT(
//                                                DISTINCT tbl_warehouse.wh_id
//                                        ) AS reportedWH,
//
//                                        tbl_warehouse.dist_id,
//                                        tbl_locations.LocName,tbl_hf_data.reporting_date,
//                                        COUNT(tbl_hf_data.received_balance) AS received_balance,
//                                        COUNT(tbl_hf_data.issue_balance) AS issue_balance
//                                FROM
//                                        tbl_warehouse
//                                        
//                     ";
//$q_reporting .= "
//            
//                    
//                                
//                                INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
//                                INNER JOIN tbl_hf_data ON tbl_warehouse.wh_id = tbl_hf_data.warehouse_id
//                                INNER JOIN tbl_locations ON tbl_warehouse.dist_id = tbl_locations.PkLocID
//                                WHERE
//                                     stakeholder.lvl = 7
//                                     /*AND tbl_warehouse.hf_type_id NOT IN (5, 2, 3, 9, 6, 7, 8, 12, 10, 11)*/
//                                     AND tbl_warehouse.reporting_start_month <=tbl_hf_data.reporting_date
//                                AND tbl_hf_data.reporting_date BETWEEN '" . $from_date . "' and '" . $to_date . "'
//                                AND tbl_warehouse.dist_id IN (14,149,77,93)
//                                $where_clause
//                                GROUP BY
//                                        tbl_warehouse.dist_id,
//                                        tbl_hf_data.reporting_date
//                                ORDER BY LocName ";
//echo $q_reporting;exit;


//                Query For Received 

                    $query1 = "SELECT
	A.*, B.*
FROM
	(
		SELECT
			tbl_wh_data.wh_id,
			tbl_warehouse.stkid,
			tbl_warehouse.dist_id,
			mainStk.stkname,
			tbl_warehouse.wh_name AS LocName,
			count(
				DISTINCT tbl_warehouse.dist_id
			) AS total_districts
		FROM
			tbl_warehouse
		INNER JOIN tbl_wh_data ON tbl_warehouse.wh_id = tbl_wh_data.wh_id
		INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
		INNER JOIN tbl_locations ON tbl_warehouse.dist_id = tbl_locations.PkLocID
		INNER JOIN stakeholder AS mainStk ON tbl_warehouse.stkid = mainStk.stkid
		WHERE
			mainStk.stk_type_id = 0
		AND mainStk.lvl = 1
		AND mainStk.is_reporting = 1
		$where_clause
		AND tbl_warehouse.is_active = 1
		AND tbl_warehouse.wh_id IN (3912,25,356)
		GROUP BY
			tbl_warehouse.wh_id,
			tbl_warehouse.stkid
	) A
RIGHT JOIN (SELECT
	DATE_FORMAT(tbl_stock_master.TranDate,'%Y-%m-01') AS TranDate,
	tbl_stock_master.PkStockID,
	COUNT(tbl_stock_master.TranNo) AS Total,
	tbl_stock_master.TranRef,
	tbl_warehouse.wh_name,
	stock_batch.wh_id,
	itminfo_tab.itm_name,
	itminfo_tab.generic_name,
	stock_batch.batch_no,
	tbl_stock_detail.Qty,
	stakeholder_item.unit_price * tbl_stock_detail.Qty AS price_of_qty,
	tbl_itemunits.UnitType,
	tbl_stock_detail.PkDetailID,
	stock_batch.batch_id AS BatchID,
	stock_batch.batch_expiry,
	stakeholder.stkname,
	stakeholder_item.unit_price,
	IFNULL(
		stakeholder_item.quantity_per_pack,
		itminfo_tab.qty_carton
	) qty_carton,
	itminfo_tab.field_color,
	tbl_stock_master.CreatedBy,
	tbl_stock_master.CreatedOn,
	tbl_stock_master.source_type,
	tbl_stock_master.shipment_mode,
	tbl_stock_master.attachment_name,
	tbl_stock_master. EVENT
FROM
	tbl_stock_master
INNER JOIN tbl_stock_detail ON tbl_stock_master.PkStockID = tbl_stock_detail.fkStockID
INNER JOIN tbl_warehouse ON tbl_stock_master.WHIDFrom = tbl_warehouse.wh_id
INNER JOIN stock_batch ON tbl_stock_detail.BatchID = stock_batch.batch_id
INNER JOIN itminfo_tab ON stock_batch.item_id = itminfo_tab.itm_id
LEFT JOIN tbl_itemunits ON itminfo_tab.item_unit_id = tbl_itemunits.pkUnitID
LEFT JOIN stakeholder_item ON stock_batch.manufacturer = stakeholder_item.stk_id
LEFT JOIN stakeholder ON stakeholder_item.stkid = stakeholder.stkid
WHERE
	DATE_FORMAT(
		tbl_stock_master.TranDate,
		'%Y-%m-%d'
	) BETWEEN '" . $from_date . "' and '" . $to_date . "'
AND tbl_stock_master.TranTypeID = 1
AND stock_batch.wh_id IN (3912,25,356)
AND tbl_stock_detail.temp = 0
GROUP BY
stock_batch.wh_id,
MONTH(tbl_stock_master.TranDate)
ORDER BY
stock_batch.wh_id,
tbl_stock_master.TranDate
) B ON A.wh_id = B.wh_id";

                       
           $res_reporting1 = mysql_query($query1);
$rec1 = array();
//$prov_arr['all']='Aggregated';
while ($row = mysql_fetch_assoc($res_reporting1)) {
    $rec1[$row['dist_id']][$row['TranDate']] = $row['Total'];
}         
                     
                    
$query2 = "SELECT
	A.*, B.*
FROM
	(
		SELECT
			tbl_wh_data.wh_id,
			tbl_warehouse.stkid,
			tbl_warehouse.dist_id,
			mainStk.stkname,
			tbl_warehouse.wh_name AS LocName,
			count(
				DISTINCT tbl_warehouse.dist_id
			) AS total_districts
		FROM
			tbl_warehouse
		INNER JOIN tbl_wh_data ON tbl_warehouse.wh_id = tbl_wh_data.wh_id
		INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
		INNER JOIN tbl_locations ON tbl_warehouse.dist_id = tbl_locations.PkLocID
		INNER JOIN stakeholder AS mainStk ON tbl_warehouse.stkid = mainStk.stkid
		WHERE
			mainStk.stk_type_id = 0
		AND mainStk.lvl = 1
		AND mainStk.is_reporting = 1
		$where_clause
		AND tbl_warehouse.is_active = 1
		AND tbl_warehouse.wh_id IN (3912,25,356)
		GROUP BY
			tbl_warehouse.wh_id,
			tbl_warehouse.stkid
	) A
RIGHT JOIN (SELECT
	DATE_FORMAT(tbl_stock_master.TranDate,'%Y-%m-01') AS TranDate,
	tbl_stock_master.PkStockID,
	COUNT(tbl_stock_master.TranNo) AS Total,
	tbl_stock_master.TranRef,
	tbl_warehouse.wh_name,
	stock_batch.wh_id,
	itminfo_tab.itm_name,
	itminfo_tab.generic_name,
	stock_batch.batch_no,
	tbl_stock_detail.Qty,
	stakeholder_item.unit_price * tbl_stock_detail.Qty AS price_of_qty,
	tbl_itemunits.UnitType,
	tbl_stock_detail.PkDetailID,
	stock_batch.batch_id AS BatchID,
	stock_batch.batch_expiry,
	stakeholder.stkname,
	stakeholder_item.unit_price,
	IFNULL(
		stakeholder_item.quantity_per_pack,
		itminfo_tab.qty_carton
	) qty_carton,
	itminfo_tab.field_color,
	tbl_stock_master.CreatedBy,
	tbl_stock_master.CreatedOn,
	tbl_stock_master.source_type,
	tbl_stock_master.shipment_mode,
	tbl_stock_master.attachment_name,
	tbl_stock_master. EVENT
FROM
	tbl_stock_master
INNER JOIN tbl_stock_detail ON tbl_stock_master.PkStockID = tbl_stock_detail.fkStockID
INNER JOIN tbl_warehouse ON tbl_stock_master.WHIDFrom = tbl_warehouse.wh_id
INNER JOIN stock_batch ON tbl_stock_detail.BatchID = stock_batch.batch_id
INNER JOIN itminfo_tab ON stock_batch.item_id = itminfo_tab.itm_id
LEFT JOIN tbl_itemunits ON itminfo_tab.item_unit_id = tbl_itemunits.pkUnitID
LEFT JOIN stakeholder_item ON stock_batch.manufacturer = stakeholder_item.stk_id
LEFT JOIN stakeholder ON stakeholder_item.stkid = stakeholder.stkid
WHERE
	DATE_FORMAT(
		tbl_stock_master.TranDate,
		'%Y-%m-%d'
	) BETWEEN '" . $from_date . "' and '" . $to_date . "'
AND tbl_stock_master.TranTypeID = 2
AND stock_batch.wh_id IN (3912,25,356)
AND tbl_stock_detail.temp = 0
GROUP BY
stock_batch.wh_id,
MONTH(tbl_stock_master.TranDate)
ORDER BY
stock_batch.wh_id,
tbl_stock_master.TranDate
) B ON A.wh_id = B.wh_id";                    



           $res_reporting2 = mysql_query($query2);
$rec2 = array();
//$prov_arr['all']='Aggregated';
while ($row = mysql_fetch_assoc($res_reporting2)) {
    $rec2[$row['dist_id']][$row['TranDate']] = $row['Total'];
}       


$query3 = "SELECT
	A.*, B.*
FROM
	(
		SELECT
			tbl_wh_data.wh_id,
			tbl_warehouse.stkid,
			tbl_warehouse.dist_id,
			mainStk.stkname,
			tbl_warehouse.wh_name AS LocName,
			count(
				DISTINCT tbl_warehouse.dist_id
			) AS total_districts
		FROM
			tbl_warehouse
		INNER JOIN tbl_wh_data ON tbl_warehouse.wh_id = tbl_wh_data.wh_id
		INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
		INNER JOIN tbl_locations ON tbl_warehouse.dist_id = tbl_locations.PkLocID
		INNER JOIN stakeholder AS mainStk ON tbl_warehouse.stkid = mainStk.stkid
		WHERE
			mainStk.stk_type_id = 0
		AND mainStk.lvl = 1
		AND mainStk.is_reporting = 1
		$where_clause
		AND tbl_warehouse.is_active = 1
		AND tbl_warehouse.wh_id IN (72709,72710,72711)
		GROUP BY
			tbl_warehouse.wh_id,
			tbl_warehouse.stkid
	) A
RIGHT JOIN (SELECT
	DATE_FORMAT(tbl_stock_master.TranDate,'%Y-%m-01') AS TranDate,
	tbl_stock_master.PkStockID,
	COUNT(tbl_stock_master.TranNo) AS Total,
	tbl_stock_master.TranRef,
	tbl_warehouse.wh_name,
	stock_batch.wh_id,
	itminfo_tab.itm_name,
	itminfo_tab.generic_name,
	stock_batch.batch_no,
	tbl_stock_detail.Qty,
	stakeholder_item.unit_price * tbl_stock_detail.Qty AS price_of_qty,
	tbl_itemunits.UnitType,
	tbl_stock_detail.PkDetailID,
	stock_batch.batch_id AS BatchID,
	stock_batch.batch_expiry,
	stakeholder.stkname,
	stakeholder_item.unit_price,
	IFNULL(
		stakeholder_item.quantity_per_pack,
		itminfo_tab.qty_carton
	) qty_carton,
	itminfo_tab.field_color,
	tbl_stock_master.CreatedBy,
	tbl_stock_master.CreatedOn,
	tbl_stock_master.source_type,
	tbl_stock_master.shipment_mode,
	tbl_stock_master.attachment_name,
	tbl_stock_master. EVENT
FROM
	tbl_stock_master
INNER JOIN tbl_stock_detail ON tbl_stock_master.PkStockID = tbl_stock_detail.fkStockID
INNER JOIN tbl_warehouse ON tbl_stock_master.WHIDFrom = tbl_warehouse.wh_id
INNER JOIN stock_batch ON tbl_stock_detail.BatchID = stock_batch.batch_id
INNER JOIN itminfo_tab ON stock_batch.item_id = itminfo_tab.itm_id
LEFT JOIN tbl_itemunits ON itminfo_tab.item_unit_id = tbl_itemunits.pkUnitID
LEFT JOIN stakeholder_item ON stock_batch.manufacturer = stakeholder_item.stk_id
LEFT JOIN stakeholder ON stakeholder_item.stkid = stakeholder.stkid
WHERE
	DATE_FORMAT(
		tbl_stock_master.TranDate,
		'%Y-%m-%d'
	) BETWEEN '" . $from_date . "' and '" . $to_date . "'
AND tbl_stock_master.TranTypeID = 1
AND stock_batch.wh_id IN (72709,72710,72711)
AND tbl_stock_detail.temp = 0
GROUP BY
stock_batch.wh_id,
MONTH(tbl_stock_master.TranDate)
ORDER BY
stock_batch.wh_id,
tbl_stock_master.TranDate
) B ON A.wh_id = B.wh_id";


           $res_reporting3 = mysql_query($query3);
$rec3 = array();
//$prov_arr['all']='Aggregated';
while ($row = mysql_fetch_assoc($res_reporting3)) {
    $rec3[$row['dist_id']][$row['TranDate']] = $row['Total'];
}       

$query4 = "SELECT
	A.*, B.*
FROM
	(
		SELECT
			tbl_wh_data.wh_id,
			tbl_warehouse.stkid,
			tbl_warehouse.dist_id,
			mainStk.stkname,
			tbl_warehouse.wh_name AS LocName,
			count(
				DISTINCT tbl_warehouse.dist_id
			) AS total_districts
		FROM
			tbl_warehouse
		INNER JOIN tbl_wh_data ON tbl_warehouse.wh_id = tbl_wh_data.wh_id
		INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
		INNER JOIN tbl_locations ON tbl_warehouse.dist_id = tbl_locations.PkLocID
		INNER JOIN stakeholder AS mainStk ON tbl_warehouse.stkid = mainStk.stkid
		WHERE
			mainStk.stk_type_id = 0
		AND mainStk.lvl = 1
		AND mainStk.is_reporting = 1
		$where_clause
		AND tbl_warehouse.is_active = 1
		AND tbl_warehouse.wh_id IN (72709, 72710, 72711)
		GROUP BY
			tbl_warehouse.wh_id,
			tbl_warehouse.stkid
	) A
RIGHT JOIN (
	SELECT
		DATE_FORMAT(tbl_stock_master.TranDate,'%Y-%m-01') AS TranDate,
		tbl_stock_master.PkStockID,
		COUNT(tbl_stock_master.TranNo) AS Total,
		tbl_stock_master.TranRef,
		tbl_warehouse.wh_name,
		stock_batch.wh_id,
		itminfo_tab.itm_name,
		itminfo_tab.generic_name,
		stock_batch.batch_no,
		tbl_stock_detail.Qty,
		stakeholder_item.unit_price * tbl_stock_detail.Qty AS price_of_qty,
		tbl_itemunits.UnitType,
		tbl_stock_detail.PkDetailID,
		stock_batch.batch_id AS BatchID,
		stock_batch.batch_expiry,
		stakeholder.stkname,
		stakeholder_item.unit_price,
		IFNULL(
			stakeholder_item.quantity_per_pack,
			itminfo_tab.qty_carton
		) qty_carton,
		itminfo_tab.field_color,
		tbl_stock_master.CreatedBy,
		tbl_stock_master.CreatedOn,
		tbl_stock_master.source_type,
		tbl_stock_master.shipment_mode,
		tbl_stock_master.attachment_name,
		tbl_stock_master. EVENT
	FROM
		tbl_stock_master
	INNER JOIN tbl_stock_detail ON tbl_stock_master.PkStockID = tbl_stock_detail.fkStockID
	INNER JOIN tbl_warehouse ON tbl_stock_master.WHIDFrom = tbl_warehouse.wh_id
	INNER JOIN stock_batch ON tbl_stock_detail.BatchID = stock_batch.batch_id
	INNER JOIN itminfo_tab ON stock_batch.item_id = itminfo_tab.itm_id
	LEFT JOIN tbl_itemunits ON itminfo_tab.item_unit_id = tbl_itemunits.pkUnitID
	LEFT JOIN stakeholder_item ON stock_batch.manufacturer = stakeholder_item.stk_id
	LEFT JOIN stakeholder ON stakeholder_item.stkid = stakeholder.stkid
	WHERE
		DATE_FORMAT(
			tbl_stock_master.TranDate,
			'%Y-%m-%d'
		) BETWEEN '" . $from_date . "' and '" . $to_date . "'
	AND tbl_stock_master.TranTypeID = 2
	AND stock_batch.wh_id IN (72709, 72710, 72711)
	AND tbl_stock_detail.temp = 0
	GROUP BY
		stock_batch.wh_id,
		MONTH (tbl_stock_master.TranDate)
	ORDER BY
		stock_batch.wh_id,
tbl_stock_master.TranDate
) B ON A.wh_id = B.wh_id";


           $res_reporting4 = mysql_query($query4);
$rec4 = array();
//$prov_arr['all']='Aggregated';
while ($row = mysql_fetch_assoc($res_reporting4)) {
    $rec4[$row['dist_id']][$row['TranDate']] = $row['Total'];
}       



$q_reporting = "SELECT
	count(DISTINCT tbl_wh_data.wh_id) AS reported,
	tbl_wh_data.RptDate,
	tbl_warehouse.stkid,
	tbl_warehouse.wh_id,
tbl_warehouse.dist_id,
tbl_warehouse.wh_name AS LocName,
COUNT(tbl_wh_data.wh_received) AS received_balance,
COUNT(tbl_wh_data.wh_issue_up) AS issue_balance
FROM
	tbl_wh_data
INNER JOIN tbl_warehouse ON tbl_wh_data.wh_id = tbl_warehouse.wh_id
INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
WHERE
	stakeholder.lvl IN (3, 7)
AND tbl_wh_data.RptDate BETWEEN '" . $from_date . "' and '" . $to_date . "'
AND tbl_warehouse.wh_id IN (
	72710,
	72709,
	72711
)
$where_clause
GROUP BY
	tbl_warehouse.wh_id,
	tbl_warehouse.stkid,
	tbl_wh_data.RptDate";
//echo $q_reporting;exit;
$res_reporting1 = mysql_query($q_reporting);
$dist_arr121 = $received_arr121 = $issued_arr121 = $montharray121 = array();
//$prov_arr['all']='Aggregated';
while ($row = mysql_fetch_assoc($res_reporting1)) {
    $dist_arr121[$row['dist_id']] = $row['LocName'];
    $montharray121[$row['RptDate']] = $row['RptDate'];
    $received_arr121[$row['dist_id']][$row['RptDate']] = $row['received_balance'];
    $issued_arr121[$row['dist_id']][$row['RptDate']] = $row['issue_balance'];
}






$q_reporting = "SELECT
	A.*, B.*
FROM
	(
		SELECT
	tbl_wh_data.wh_id,
	tbl_warehouse.stkid,
tbl_warehouse.dist_id,
	mainStk.stkname,
	tbl_warehouse.wh_name AS LocName,
	count(
		DISTINCT tbl_warehouse.dist_id
	) AS total_districts
FROM
	tbl_warehouse
INNER JOIN tbl_wh_data ON tbl_warehouse.wh_id = tbl_wh_data.wh_id
INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
INNER JOIN tbl_locations ON tbl_warehouse.dist_id = tbl_locations.PkLocID
INNER JOIN stakeholder AS mainStk ON tbl_warehouse.stkid = mainStk.stkid
WHERE
	mainStk.stk_type_id = 0
AND mainStk.lvl = 1
AND mainStk.is_reporting = 1
$where_clause
AND tbl_warehouse.is_active = 1
AND tbl_warehouse.wh_id IN (
		25,
	3912,
	356
)
GROUP BY
	tbl_warehouse.wh_id,
	tbl_warehouse.stkid
	) A
LEFT JOIN (
SELECT
	tbl_warehouse.stkid,
tbl_warehouse.wh_id,

	COUNT(
		DISTINCT tbl_warehouse.wh_id
	) AS reportedWH,
	tbl_warehouse.dist_id,
	tbl_locations.LocName As A,
	tbl_hf_data.reporting_date,
	COUNT(
		tbl_hf_data.received_balance
	) AS received_balance,
	COUNT(tbl_hf_data.issue_balance) AS issue_balance
FROM
	tbl_warehouse
INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
INNER JOIN tbl_hf_data ON tbl_warehouse.wh_id = tbl_hf_data.warehouse_id
INNER JOIN tbl_locations ON tbl_warehouse.dist_id = tbl_locations.PkLocID
WHERE
	stakeholder.lvl = 7 /*AND tbl_warehouse.hf_type_id NOT IN (5, 2, 3, 9, 6, 7, 8, 12, 10, 11)*/
AND tbl_warehouse.reporting_start_month <= tbl_hf_data.reporting_date
AND tbl_hf_data.reporting_date BETWEEN '" . $from_date . "' and '" . $to_date . "'
$where_clause
GROUP BY
	tbl_warehouse.dist_id,
	tbl_hf_data.reporting_date
ORDER BY
	LocName
) B ON A.dist_id = B.dist_id";


//echo $q_reporting;exit;





$res_reporting = mysql_query($q_reporting);
$reporting_wh_arr = $dist_arr = $received_arr = $montharray1 = $issued_arr = array();
$total_reporting_wh = 0;
//$prov_arr['all']='Aggregated';
while ($row = mysql_fetch_assoc($res_reporting)) {
    $dist_arr[$row['dist_id']] = $row['LocName'];
    $montharray1[$row['reporting_date']] = $row['reporting_date'];
    $received_arr[$row['dist_id']][$row['reporting_date']] = $row['received_balance'];
    $issued_arr[$row['dist_id']][$row['reporting_date']] = $row['issue_balance'];
    if (empty($reporting_wh_arr['all'][$row['reporting_date']]))
        $reporting_wh_arr['all'][$row['reporting_date']] = 0;
    if (empty($reporting_wh_arr[$row['dist_id']][$row['reporting_date']]))
        $reporting_wh_arr[$row['dist_id']][$row['reporting_date']] = 0;

    $reporting_wh_arr[$row['dist_id']][$row['reporting_date']] += $row['reportedWH'];
    $reporting_wh_arr['all'][$row['reporting_date']] += $row['reportedWH'];

    $total_reporting_wh += $row['reportedWH'];
}

$rep_rate = $to_be_reported_arr = array();
foreach ($dist_arr as $dist_id => $prov_data) {

    foreach ($timearray as $k => $v) {
        $this_t_sdp = 0;
        foreach ($total_sdps[$dist_id] as $mn => $t_sdp) {
            if ($mn <= $v)
                $this_t_sdp += $t_sdp;
        }
        //$master_total = $total_sdps[$dist_id];
        $master_total = $this_t_sdp;
        $disabled_fac = (isset($disabled_count[$dist_id][$v]) ? $disabled_count[$dist_id][$v] : 0);
        $to_be_reported = $master_total - $disabled_fac;

        //overriding the 'to be reported' value to the reported , in case of greater value
        if (isset($reporting_wh_arr[$dist_id][$v]) && isset($disabled_count[$dist_id][$v]) && $to_be_reported < $reporting_wh_arr[$dist_id][$v])
            $to_be_reported = $reporting_wh_arr[$dist_id][$v];

        $to_be_reported_arr[$dist_id][$v] = $to_be_reported;

        $val = (isset($prov_data[$v]) ? $prov_data[$v] : 0);

        if ($to_be_reported > 0 && isset($reporting_wh_arr[$dist_id][$v]))
            $r_r = ($reporting_wh_arr[$dist_id][$v] * 100) / $to_be_reported;
        else
            $r_r = 0;

        $rep_rate[$dist_id][$v] = $r_r;
    }
}
//echo'<pre>';print_r($rec1);
//print_r($received_arr121);exit;
include('sub_dist_reports.php')
?>


<div class="row">
    <div class="col-md-12">
<!--        <table width="100%" cellpadding="0" cellspacing="0" id="myTable" class="table table-bordered table-condensed">
            <tr>
                <td align="center" width="95%"   ><h3 class="text-info">Stock Received & Issue Report From Date ( <?= $from_date ?> ) To Date ( <?= $to_date ?> )</h3></td>
                <td align="center" width="5%"   >
                    <img title="Click here to export data to PDF file" style="cursor:pointer;" src="<?php echo PUBLIC_URL ?>images/pdf-32.png" onClick="mygrid.toPDF('<?php echo PUBLIC_URL ?>dhtmlxGrid/dhtmlxGrid/grid2pdf/server/generate.php');"/>
                    <img title="Click here to export data to Excel file" style="cursor:pointer;" src="<?php echo PUBLIC_URL ?>images/excel-32.png" onClick="mygrid.toExcel('<?php echo PUBLIC_URL ?>dhtmlxGrid/dhtmlxGrid/grid2excel/server/generate.php');"/>
                </td>
            </tr>
        </table>-->

        <div style="overflow-x:auto;">
            <table width="100%" cellpadding="0" cellspacing="0" id="compliance_Report_SDP" class="table table-bordered table-condensed">
                <tr class=" bg-blue-madison">
                    <td rowspan="2">District</td>
<?php
foreach ($timearray as $k => $month) {
    echo '<td colspan="3" align="center">' . date('M-Y', strtotime($month)) . '</td>';
    echo '<td style="display:none;"></td>';
}
?>

                </tr>
                <tr class=" bg-blue-madison">

<?php
foreach ($timearray as $k => $month) {
    echo '<td>HFs Consumption<br>(R / T)</td>';
    echo '<td>Received</td>';
    echo '<td>Issue</td>';
}
?>
                </tr>
                    <?php
                    foreach ($dist_arr as $dist_id => $dist_name) {
                        if ($dist_id == 'all')
                            continue;
                        echo ' <tr>
                                            <td>' . $dist_name . '</td>';
                        foreach ($timearray as $k => $month) {
                            $perc = 0;
                            if (!empty($rep_rate[$dist_id][$month]))
                                $perc = $rep_rate[$dist_id][$month];

                            $clr = 'green';
                            if ($perc < 85)
                                $clr = 'orange';
                            if ($perc < 50)
                                $clr = 'red';

                            echo '<td  align="center"><span style="font-size:11px;vertical-align:top;">' . (!empty($reporting_wh_arr[$dist_id][$month]) ? $reporting_wh_arr[$dist_id][$month] : 0) . '</span>';
                            ;
                            echo '<span style="font-size:18px;">/</span>';
                            echo '<span style="font-size:11px;padding-top:30px">' . $to_be_reported_arr[$dist_id][$month] . '</span></td>';
                            echo '<td align="right">' . (!empty($rec1[$dist_id][$month]) ? $rec1[$dist_id][$month] : 0). '</td>';
                            echo '<td align="right">' . (!empty($rec2[$dist_id][$month]) ? $rec2[$dist_id][$month] : 0). '</td>';
                        }
                        echo ' </tr>';
                    }
                    ?>
                
                <?php
                    foreach ($dist_arr121 as $dist_id => $dist_name) {
                        if ($dist_id == 'all')
                            continue;
                        echo ' <tr>
                                            <td>' . $dist_name . '</td>';
                        foreach ($timearray as $k => $month) {
//                            $perc = 0;
//                            if (!empty($rep_rate[$dist_id][$month]))
//                                $perc = $rep_rate[$dist_id][$month];
//
//                            $clr = 'green';
//                            if ($perc < 85)
//                                $clr = 'orange';
//                            if ($perc < 50)
//                                $clr = 'red';

                            echo '<td style="background: gray;" align="center"><span style="font-size:11px;vertical-align:top;">-</td>';
                            echo '<td align="right">' . (!empty($rec3[$dist_id][$month]) ? $rec3[$dist_id][$month] : 0). '</td>';
                            echo '<td align="right">' . (!empty($rec4[$dist_id][$month]) ? $rec4[$dist_id][$month] : 0). '</td>';
                        }
                        echo ' </tr>';
                    }
                    ?>
                
            </table>

        </div>
<?php
        $xmlstore = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
        $xmlstore .= "<rows>";
        foreach ($dist_arr as $dist_id => $dist_name) {
            if ($dist_id == 'all')
                continue;
            $xmlstore .= "<row>";
            $xmlstore .= "<cell>" . $dist_name . "</cell>";
            foreach ($timearray as $k => $month) {

                $xmlstore .= "<cell>" . (!empty($received_arr[$dist_id][$month]) ? $received_arr[$dist_id][$month] : 0) . "</cell>";
                $xmlstore .= "<cell>" . (!empty($issued_arr[$dist_id][$month]) ? $issued_arr[$dist_id][$month] : 0) . "</cell>";
            }
            $xmlstore .= "</row>";
        }
        foreach ($dist_arr121 as $dist_id => $dist_name) {
            if ($dist_id == 'all')
                continue;
            $xmlstore .= "<row>";
            $xmlstore .= "<cell>" . $dist_name . "</cell>";
            foreach ($timearray as $k => $month) {

                $xmlstore .= "<cell>" . (!empty($received_arr121[$dist_id][$month]) ? $received_arr121[$dist_id][$month] : 0) . "</cell>";
                $xmlstore .= "<cell>" . (!empty($issued_arr121[$dist_id][$month]) ? $issued_arr121[$dist_id][$month] : 0) . "</cell>";
            }
            $xmlstore .= "</row>";
        }

        $xmlstore .= "</rows>";
        ?>
        <div id="mygrid_container" style="width:100%; height:1100px;"  ></div>  
    </div>
</div>
<?php
$cspan = $header = $width = $ro = $align = $stkName = $locName = '';
$header .= "<span title='District'>Name</span>";
foreach ($timearray as $k => $month) {
    $header .= ",<span title='" . date('M Y', strtotime($month)) . "'>" . date('M Y', strtotime($month)) . "</span>";
    $cspan .= ",#cspan";
}
?>
<script>
    var mygrid;
    function doInitGrid() {
        mygrid = new dhtmlXGridObject('mygrid_container');
        mygrid.selMultiRows = true;
        mygrid.setImagePath("<?php echo PUBLIC_URL; ?>dhtmlxGrid/dhtmlxGrid/codebase/imgs/");
        mygrid.setHeader("<div style='text-align:center;'>Compliance Report - SDPs- </div><?= $cspan ?>");
        mygrid.attachHeader("<?= $header ?>");
        mygrid.attachFooter("<div style='font-size: 10px;'>Note: This report is based on data as on <br> </div><?php echo $cspan; ?>");

        mygrid.setColAlign("left,right,right,right,right,right,right,right,right,right,right,right,right");
        mygrid.setInitWidths("*,80,80,80,80,80,80,80,80,80,80,80,80");
        mygrid.setColTypes("ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro");
        mygrid.enableRowsHover(true, 'onMouseOver'); // `onMouseOver` is the css cla ss name.
        mygrid.setSkin("light");
        mygrid.init();
        mygrid.clearAll();
        mygrid.loadXMLString('<?php echo $xmlstore; ?>');
    }

</script>