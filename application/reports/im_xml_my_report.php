<?php

/**
 * xml_my_report
 * @package reports
 * 
 * @author     Ajmal Hussain 
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
// Check Level of the warehouse
//Level query
//gets stakeholder.lvl and hf_type_id
$lvlQry = "SELECT
			stakeholder.lvl,
			tbl_warehouse.hf_type_id
		FROM
			tbl_warehouse
		INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
		WHERE
			tbl_warehouse.wh_id = " . $wh_id . " ";
//Query result
$lvlQryRes = mysql_fetch_array(mysql_query($lvlQry));
$lvl = $lvlQryRes['lvl'];
//print_r($lvl);exit;
$type = $lvlQryRes['hf_type_id'];
//Initializing variables
$refCases = $CS_Done = $ante_natal = $post_natal = $ailment_children = $ailment_adults = $general_ailment = '';
//print_r($_SESSION);exit;
if ($lvl <= 4 && $lvl != 3) {
    //This query gets
    //w_id
    //itm_name
    //itm_category  
    //wh_obl_a
    //wh_received
    //wh_issue_up
    //wh_adja
    //wh_adjb
    //wh_cbl_a
    //last_update
    $query_xmlw = "SELECT
						tbl_wh_data.w_id,
						itminfo_tab.itm_name,
						itminfo_tab.itm_category,
						tbl_wh_data.wh_obl_a,
						tbl_wh_data.wh_received,
						tbl_wh_data.wh_issue_up,
						tbl_wh_data.wh_adja,
						tbl_wh_data.wh_adjb,
						tbl_wh_data.wh_cbl_a,
						tbl_wh_data.last_update
					FROM  tbl_wh_data 
					INNER JOIN itminfo_tab ON tbl_wh_data.item_id=itminfo_tab.itmrec_id 
					INNER JOIN tbl_warehouse ON tbl_wh_data.wh_id=tbl_warehouse.wh_id 
					WHERE $where 
					ORDER BY frmindex";
//        print_r($query_xmlw);exit;
    $colspan = '';
    $header = '';
    $header1 = '';
    $header2 = '';
    $width = '';
    $colAlign = '';
    $colType = '';
} else if ($lvl == 3) {
    //This query gets
    //w_id
    //itm_name
    //itm_category  
    //wh_obl_a
    //wh_received
    //wh_issue_up
    //wh_adja
    //wh_adjb
    //wh_cbl_a
    //last_update
    $query_xmlw = "SELECT
itminfo_tab.itm_id,
Sum(IF (
					DATE_FORMAT(
						tbl_stock_master.TranDate,
						'%Y-%m-%d'
					) < '$from_date',
					tbl_stock_detail.Qty,
					0
				)) AS wh_obl_a,
Sum(IF (
					DATE_FORMAT(
						tbl_stock_master.TranDate,
						'%Y-%m-%d'
					) >= '$from_date'
					AND DATE_FORMAT(
						tbl_stock_master.TranDate,
						'%Y-%m-%d'
					) <= '$to_date'
					AND tbl_stock_master.TranTypeID = 1,
					tbl_stock_detail.Qty,
					0
				)) AS wh_received,
Sum(IF (
					DATE_FORMAT(
						tbl_stock_master.TranDate,
						'%Y-%m-%d'
					) >= '$from_date'
					AND DATE_FORMAT(
						tbl_stock_master.TranDate,
						'%Y-%m-%d'
					) <= '$to_date'
					AND tbl_stock_master.TranTypeID = 2,
					ABS(tbl_stock_detail.Qty),
					0
				)) AS wh_issue_up,
Sum(IF (
					DATE_FORMAT(
						tbl_stock_master.TranDate,
						'%Y-%m-%d'
					) >= '$from_date'
					AND DATE_FORMAT(
						tbl_stock_master.TranDate,
						'%Y-%m-%d'
					) <= '$to_date'
					AND tbl_stock_master.TranTypeID > 2
					AND tbl_trans_type.trans_nature = '+',
					tbl_stock_detail.Qty,
					0
				)) AS wh_adja,
ABS(
				SUM(

					IF (
						DATE_FORMAT(
							tbl_stock_master.TranDate,
							'%Y-%m-%d'
						) >= '$from_date'
						AND DATE_FORMAT(
							tbl_stock_master.TranDate,
							'%Y-%m-%d'
						) <= '$to_date'
						AND tbl_stock_master.TranTypeID > 2
						AND tbl_trans_type.trans_nature = '-',
						tbl_stock_detail.Qty,
						0
					)
				)
			) AS wh_adjb,
Sum(tbl_stock_detail.Qty) AS wh_cbl_a,
tbl_warehouse.wh_name,
itminfo_tab.itm_name,
itminfo_tab.generic_name,
stock_batch.batch_no,
tbl_stock_master.TranDate AS last_update
FROM
itminfo_tab
INNER JOIN stock_batch ON itminfo_tab.itm_id = stock_batch.item_id
INNER JOIN tbl_stock_detail ON stock_batch.batch_id = tbl_stock_detail.BatchID
INNER JOIN tbl_stock_master ON tbl_stock_detail.fkStockID = tbl_stock_master.PkStockID
INNER JOIN tbl_trans_type ON tbl_stock_master.TranTypeID = tbl_trans_type.trans_id
INNER JOIN tbl_warehouse ON stock_batch.wh_id = tbl_warehouse.wh_id
WHERE
DATE_FORMAT(
				tbl_stock_master.TranDate,
				'%Y-%m-%d'
			) <= '$to_date' AND
((tbl_stock_master.TranTypeID = 2) OR
(tbl_stock_master.TranTypeID = 1) OR
(tbl_stock_master.TranTypeID > 2)) AND
tbl_stock_master.temp = 0 AND
stock_batch.wh_id = $wh_id
GROUP BY
			itminfo_tab.itm_id,batch_no

ORDER BY
itm_name ASC
";
//    print_r($query_xmlw);
//    exit;
    $colspan = '';
    $header = '';
    $header1 = '';
    $header2 = '';
    $width = '';
    $colAlign = '';
    $colType = '';
} else if ($lvl == 7) {
    //Tghis query gets
    //w_id
    //itm_name
    //wh_obl_a
    //wh_received
    //wh_issue_up
    //wh_adja
    //wh_adjb
    //wh_cbl_a
    //last_update

    $query_xmlw = "SELECT
						tbl_hf_data.pk_id AS w_id,
						itminfo_tab.itm_name,
                                                itminfo_tab.generic_name,
						itminfo_tab.itm_category,
						tbl_hf_data.opening_balance AS wh_obl_a,
						tbl_hf_data.received_balance AS wh_received,
						tbl_hf_data.issue_balance AS wh_issue_up,
						tbl_hf_data.adjustment_positive AS wh_adja,
						tbl_hf_data.adjustment_negative AS wh_adjb,
						tbl_hf_data.closing_balance AS wh_cbl_a,
						tbl_hf_data.last_update,
						tbl_hf_data.new,
						tbl_hf_data.old
					FROM
						tbl_hf_data
					INNER JOIN itminfo_tab ON tbl_hf_data.item_id = itminfo_tab.itm_id
					WHERE
						tbl_hf_data.warehouse_id = " . $wh_id . "
					AND (tbl_hf_data.reporting_date BETWEEN '$from_date_hf-01' AND '$to_date_hf-01')
					ORDER BY
						itminfo_tab.itm_name ASC";
//    print_r($query_xmlw);exit;
    $colspan = ',#cspan,#cspan';
    $header = ",<div style='text-align:center;'>Clients</div>,#cspan";
    $header1 = ",<div style='text-align:center;'>New</div>,<div style='text-align:center;'>Old</div>";
    $header2 = ',,';
    $width = ',60,60';
    $colAlign = ',right,right';
    $colType = ',ro,ro';

    // Get surgery Cases
    $csQry = "SELECT
				SUM(tbl_hf_data_reffered_by.static + tbl_hf_data_reffered_by.camp) AS CS_Done,
				(tbl_hf_mother_care.pre_natal_new + tbl_hf_mother_care.pre_natal_old) AS ante_natal,
				(tbl_hf_mother_care.post_natal_new + tbl_hf_mother_care.post_natal_old) AS post_natal,
				tbl_hf_mother_care.ailment_children,
				tbl_hf_mother_care.ailment_adults,
				tbl_hf_mother_care.general_ailment
			FROM
				tbl_hf_data
			INNER JOIN tbl_hf_data_reffered_by ON tbl_hf_data.pk_id = tbl_hf_data_reffered_by.hf_data_id
			INNER JOIN tbl_hf_mother_care ON tbl_hf_data.warehouse_id = tbl_hf_mother_care.warehouse_id
			AND tbl_hf_data.reporting_date = tbl_hf_mother_care.reporting_date
			WHERE
				tbl_hf_data.warehouse_id = " . $wh_id . "
			AND YEAR (tbl_hf_data.reporting_date) = " . $sel_year . "
			AND MONTH (tbl_hf_data.reporting_date) = " . $sel_month . " ";
    //query result
    $csRows = mysql_query($csQry);
    $row = mysql_fetch_array($csRows);
    $CS_Done = !empty($row['CS_Done']) ? $row['CS_Done'] : 0;
    $ante_natal = !empty($row['ante_natal']) ? $row['ante_natal'] : 0;
    $post_natal = !empty($row['post_natal']) ? $row['post_natal'] : 0;
    $ailment_children = !empty($row['ailment_children']) ? $row['ailment_children'] : 0;
    $ailment_adults = !empty($row['ailment_adults']) ? $row['ailment_adults'] : 0;
    $general_ailment = !empty($row['general_ailment']) ? $row['general_ailment'] : 0;
}
$result_xmlw = mysql_query($query_xmlw);
//xml
$xmlstore = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
$xmlstore .= "<rows>";
$numOfRows = mysql_num_rows($result_xmlw);
$_SESSION['numOfRows'] = $numOfRows;
if ($numOfRows > 0) {
    while ($row_xmlw = mysql_fetch_array($result_xmlw)) {
        $lastModified = (is_null($row_xmlw['last_update'])) ? '' : date('d/m/Y h:i A', strtotime($row_xmlw['last_update']));
//        print_r($lastModified);exit;
        //Checking itm_category
        if ($row_xmlw['itm_category'] == 2) {
            $refCases += $row_xmlw['wh_issue_up'];
        }
        //Checking itm_category
        //xml
        $xmlstore .= "<row>";
        $xmlstore .= "<cell>" . $row_xmlw['itm_name'] . "</cell>";
        $xmlstore .= "<cell>" . $row_xmlw['generic_name'] . "</cell>";
        //wh_obl_a
        if ($lvl == 3) {
            $xmlstore .= "<cell>" . $row_xmlw['batch_no'] . "</cell>";
        } else {
            $xmlstore .= "<cell></cell>";
        }
        $xmlstore .= "<cell>" . number_format($row_xmlw['wh_obl_a']) . "</cell>";
        //wh_received
        $xmlstore .= "<cell>" . number_format($row_xmlw['wh_received']) . "</cell>";
        //wh_issue_up
        $xmlstore .= "<cell>" . number_format($row_xmlw['wh_issue_up']) . "</cell>";
        //wh_adja
        $xmlstore .= "<cell>" . number_format($row_xmlw['wh_adja']) . "</cell>";
        //wh_adjb
        $xmlstore .= "<cell>" . number_format($row_xmlw['wh_adjb']) . "</cell>";
        //wh_cbl_a
        $xmlstore .= "<cell>" . number_format($row_xmlw['wh_cbl_a']) . "</cell>";
        //Checking level
        if ($lvl == 7) {
            $xmlstore .= "<cell>" . number_format($row_xmlw['new']) . "</cell>";
            $xmlstore .= "<cell>" . number_format($row_xmlw['old']) . "</cell>";
        }
        $xmlstore .= "<cell>" . $lastModified . "</cell>";

        $xmlstore .= "</row>";
    }
}
$xmlstore .= "</rows>";
//print_r($xmlstore);exit;
//Checking province
if ($province == 3) {
    $xmlstore1 = "Surgery Cases(Reffered): $refCases  Surgery Cases(Performed): $CS_Done Ante-natal: $ante_natal Post-natal: $post_natal Children: $ailment_children Adults: $ailment_adults";
} else {
    $xmlstore1 = "Surgery Cases(Reffered): $refCases  Surgery Cases(Performed): $CS_Done Ante-natal: $ante_natal Post-natal: $post_natal Children: " . ($ailment_children + $ailment_adults) . " General Ailment: $general_ailment";
}
?>