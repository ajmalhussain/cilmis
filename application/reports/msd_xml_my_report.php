<?php
//echo '<pre>';print_r($_REQUEST);exit;
$refCases = $CS_Done = $ante_natal = $post_natal = $ailment_children = $ailment_adults = $general_ailment = '';
//print_r($_SESSION);exit;
$qry_wh = "

(
SELECT
tbl_warehouse.wh_id,
tbl_warehouse.wh_name,
tbl_warehouse.dist_id,
tbl_warehouse.prov_id
FROM
tbl_warehouse
INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
WHERE
tbl_warehouse.prov_id = ".$province." AND
stakeholder.lvl = 2 AND tbl_warehouse.is_allowed_im = 1 AND
tbl_warehouse.stkid IN (7,145)
 ) 
UNION 

(
SELECT
	tbl_warehouse.wh_id,
	tbl_warehouse.wh_name,
	tbl_warehouse.dist_id,
	tbl_warehouse.prov_id
FROM
	tbl_warehouse
INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
WHERE
tbl_warehouse.prov_id = 1 AND
stakeholder.lvl = 3 AND
tbl_warehouse.is_allowed_im = 1 AND
tbl_warehouse.stkid = 2

)       

";
$qryRes_wh = mysql_query($qry_wh);
while ($row = mysql_fetch_assoc($qryRes_wh)) {
    $wh_array[$row['wh_id']] = $row['wh_id'];
}
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
tbl_stock_master.temp = 0 
";
if(isset($_REQUEST['wh_id']) && $_REQUEST['wh_id']>0){
    $query_xmlw .= "AND stock_batch.wh_id = " .$_REQUEST['wh_id']. " ";
}else{
    $query_xmlw .= "AND stock_batch.wh_id IN (" . implode($wh_array, ',') . ") ";
}
if($_REQUEST['grp_by'] == 'generic_name')
{
    $query_xmlw .= " GROUP BY itminfo_tab.generic_name ";
}else{
    $query_xmlw .= " GROUP BY itminfo_tab.itm_id ";
}

$query_xmlw .= "
ORDER BY
wh_name ASC,
itm_name ASC
";
//    print_r($query_xmlw);exit;


$colspan = '';
$header = '';
$header1 = '';
$header2 = '';
$width = '';
$colAlign = '';
$colType = '';
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
        //xml
        $xmlstore .= "<row>";
        $xmlstore .= "<cell>" . $row_xmlw['wh_name'] . "</cell>";
        
        if($_REQUEST['grp_by'] != 'generic_name')
        {
        $xmlstore .= "<cell>" . $row_xmlw['itm_name'] . "</cell>";
        }
        
        $generic = preg_replace( "/\r|\n/", "", $row_xmlw['generic_name'] );
        $generic = str_replace("-"," ",$generic);
        $generic = str_replace("'"," ",$generic);
        $generic = str_replace('"'," ",$generic);
        $generic = str_replace('>'," ",$generic);
        $generic = str_replace('<'," ",$generic);
        $generic = str_replace(';'," ",$generic);
        $generic = str_replace(':'," ",$generic);
        $generic = str_replace('>'," ",$generic);
        $generic = str_replace('<'," ",$generic);
        $generic = str_replace('!'," ",$generic);
        
        $xmlstore .= "<cell><![CDATA[" . $generic . "]]></cell>";
        //wh_obl_a
        //$xmlstore .= "<cell>" . $row_xmlw['batch_no'] . "</cell>";

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

        $xmlstore .= "<cell>" . $lastModified . "</cell>";

        $xmlstore .= "</row>";
    }
}
$xmlstore .= "</rows>";
//print_r($xmlstore);exit;
?>