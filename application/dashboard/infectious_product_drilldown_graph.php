<?php
include("../includes/classes/Configuration.inc.php");
//Login();
//include db
include(APP_PATH . "includes/classes/db.php");
//include functions
include APP_PATH . "includes/classes/functions.php";

//include FusionCharts
include(PUBLIC_PATH . "FusionCharts/Code/PHP/includes/FusionCharts.php");

$stk_name = '';

$where_hf = '';
$itm = (!empty($_REQUEST['product']) ? $_REQUEST['product'] : '');
$dist_id = (!empty($_REQUEST['dist_id']) ? $_REQUEST['dist_id'] : '');
$hf = (!empty($_REQUEST['hf']) ? $_REQUEST['hf'] : '');
$status = (!empty($_REQUEST['status']) ? $_REQUEST['status'] : '');
$type = (!empty($_REQUEST['type']) ? $_REQUEST['type'] : '');
$from_date = (!empty($_REQUEST['from_date']) ? $_REQUEST['from_date'] : '');
$from_date = date('Y-m-d', strtotime($from_date));
$from_without_date = date('Y-m', strtotime($from_date));
$avg_consumption_date = date('Y-m-d', strtotime("-3 month", strtotime($from_without_date . '-01')));
//$from_date .= '-01';
$from_date_last_day = date('Y-m-t', strtotime($from_date));
$reporting_start_month = date('Y-m-d', strtotime("-12 month", strtotime($from_without_date . '-01')));
//print_r($reporting_start_month);exit;
$from_date_last_day = date('Y-m-t', strtotime($from_date));
$dist_dhq_type_filter = '';
$reporting_arr = $item_arr = $stock_arr = Array();
if (empty($_REQUEST['product'])) {
    $itm_filter = '';
} else if (!empty($_REQUEST['product'])) {
    $itm_filter = " AND itminfo_tab.itm_id=$itm";
}
//print_r($type);exit;
if ($type == 'dist') {
    $qry_soh = "SELECT
	itminfo_tab.itm_id AS item_id,
	SUM(tbl_stock_detail.Qty) AS soh,
	itminfo_tab.itm_name,
	tbl_warehouse.wh_id,
	tbl_warehouse.prov_id,
	tbl_warehouse.stkid,
	tbl_warehouse.dist_id,
	tbl_warehouse.wh_name,
        DATE_FORMAT(
	tbl_stock_master.TranDate,
	'%Y-%m-%d'
) as TranDate
FROM
	itminfo_tab
INNER JOIN stock_batch ON itminfo_tab.itm_id = stock_batch.item_id
INNER JOIN tbl_stock_detail ON stock_batch.batch_id = tbl_stock_detail.BatchID
INNER JOIN tbl_stock_master ON tbl_stock_detail.fkStockID = tbl_stock_master.PkStockID
INNER JOIN tbl_trans_type ON tbl_stock_master.TranTypeID = tbl_trans_type.trans_id
INNER JOIN tbl_warehouse ON stock_batch.wh_id = tbl_warehouse.wh_id
INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
WHERE
DATE_FORMAT(tbl_stock_master.TranDate, '%Y-%m-%d') <=  '$from_date'
    AND    DATE_FORMAT(tbl_stock_master.TranDate, '%Y-%m-%d') >=  '$reporting_start_month'
AND tbl_stock_master.temp = 0
    AND tbl_warehouse.dist_id = $dist_id 
AND stakeholder.lvl = 3
$itm_filter
GROUP BY
	tbl_stock_master.TranDate";

//        print_r($qry_soh);
//        exit;
    $qryRes_soh = mysql_query($qry_soh);
    $num_rows = mysql_num_rows($qryRes_soh);
//    print_r(mysql_fetch_assoc($qryRes));exit;
    while ($row = mysql_fetch_assoc($qryRes_soh)) {
        $stock_arr[$row['TranDate']]['soh'] = $row['soh'];
        $item_arr[$row['item_id']] = $row['itm_name'];
        $reporting_arr[$row['TranDate']] = $row['TranDate'];
//       print_r('stock_array is this'.$stock_arr);exit;
    }
//    print_r('testing if');exit;
//    
    $qry_amc = "SELECT
              itminfo_tab.itm_id AS item_id,
              DATE_FORMAT(
	tbl_stock_master.TranDate,
	'%Y-%m-%d'
) as TranDate,
              itminfo_tab.itm_name,
			(ROUND(
				SUM(ABS(tbl_stock_detail.Qty)) / 3
			)) AS amc
FROM
tbl_stock_master
INNER JOIN tbl_stock_detail ON tbl_stock_master.PkStockID = tbl_stock_detail.fkStockID
INNER JOIN stock_batch ON stock_batch.batch_id = tbl_stock_detail.BatchID
INNER JOIN itminfo_tab ON stock_batch.item_id = itminfo_tab.itm_id
INNER JOIN tbl_warehouse ON stock_batch.wh_id = tbl_warehouse.wh_id
INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
WHERE
tbl_stock_master.TranTypeID = 2 AND
tbl_stock_master.TranDate >= '$avg_consumption_date' AND tbl_stock_master.TranDate < '$from_without_date-01' AND
stock_batch.item_id = itminfo_tab.itm_id 
    AND tbl_warehouse.dist_id = $dist_id  
    AND stakeholder.lvl = 3
    $itm_filter
GROUP BY
	tbl_stock_master.TranDate
";

//        print_r($qry_amc);
//        exit;
    $qryRes_amc = mysql_query($qry_amc);
//    print_r('result is this ');
//    $test=mysql_fetch_assoc($qryRes_amc);
//     print_r($test);exit;
    while ($row = mysql_fetch_assoc($qryRes_amc)) {
        $stock_arr['amc'] += $row['amc'];
    }
    foreach ($reporting_arr as $key => $id) {
        if($status=='so'){
        if (($stock_arr[$id]['soh'] / $stock_arr['amc']) == 0 || $stock_arr[$id]['soh'] / $stock_arr['amc'] == null) {
            $stock_arr[$id]['mos'] = 0;
        } 
        }
        else if($status=='sat'){
         if ($stock_arr[$id]['soh'] / $stock_arr['amc'] > 0) {
            $stock_arr[$id]['mos'] = ($stock_arr[$id]['soh'] / $stock_arr['amc']);
        }
        }
//        print_r($stock_arr);
    }
} else if ($type == 'dhq') {
    $qry_soh = "SELECT
	itminfo_tab.itm_id AS item_id,
	SUM(tbl_stock_detail.Qty) AS soh,
	itminfo_tab.itm_name,
	tbl_warehouse.wh_id,
	tbl_warehouse.prov_id,
	tbl_warehouse.stkid,
	tbl_warehouse.dist_id,
	tbl_warehouse.wh_name,DATE_FORMAT(
	tbl_stock_master.TranDate,
	'%Y-%m-%d'
) as TranDate
FROM
	itminfo_tab
INNER JOIN stock_batch ON itminfo_tab.itm_id = stock_batch.item_id
INNER JOIN tbl_stock_detail ON stock_batch.batch_id = tbl_stock_detail.BatchID
INNER JOIN tbl_stock_master ON tbl_stock_detail.fkStockID = tbl_stock_master.PkStockID
INNER JOIN tbl_trans_type ON tbl_stock_master.TranTypeID = tbl_trans_type.trans_id
INNER JOIN tbl_warehouse ON stock_batch.wh_id = tbl_warehouse.wh_id
WHERE
DATE_FORMAT(tbl_stock_master.TranDate, '%Y-%m-%d') <=  '$from_date'
    AND    DATE_FORMAT(tbl_stock_master.TranDate, '%Y-%m-%d') >=  '$reporting_start_month'
AND tbl_stock_master.temp = 0
    AND tbl_warehouse.dist_id = $dist_id  
    AND wh_type_id=21
   $itm_filter
GROUP BY
	tbl_stock_master.TranDate";

//        print_r($qry);
//        exit;
    $qryRes_soh = mysql_query($qry_soh);
    $num_rows = mysql_num_rows($qryRes_soh);
    while ($row = mysql_fetch_assoc($qryRes_soh)) {
        $stock_arr[$row['TranDate']]['soh'] = $row['soh'];
        $item_arr[$row['item_id']] = $row['itm_name'];
        $reporting_arr[$row['TranDate']] = $row['TranDate'];
    }
    $qry_amc = "SELECT
              itminfo_tab.itm_id AS item_id,
              DATE_FORMAT(
	tbl_stock_master.TranDate,
	'%Y-%m-%d'
) as TranDate,
			(ROUND(
				SUM(ABS(tbl_stock_detail.Qty)) / 3
			)) AS amc
FROM
tbl_stock_master
INNER JOIN tbl_stock_detail ON tbl_stock_master.PkStockID = tbl_stock_detail.fkStockID
INNER JOIN stock_batch ON stock_batch.batch_id = tbl_stock_detail.BatchID
INNER JOIN itminfo_tab ON stock_batch.item_id = itminfo_tab.itm_id
INNER JOIN tbl_warehouse ON stock_batch.wh_id = tbl_warehouse.wh_id
INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
WHERE
tbl_stock_master.TranTypeID = 2 AND
tbl_stock_master.TranDate >= '$avg_consumption_date' AND tbl_stock_master.TranDate < '$from_without_date-01' AND
stock_batch.item_id = itminfo_tab.itm_id 
    AND tbl_warehouse.dist_id = $dist_id  
    AND wh_type_id=21
    $itm_filter
GROUP BY
	tbl_stock_master.TranDate
";

//        print_r($qry_amc);
//        exit;
    $qryRes_amc = mysql_query($qry_amc);
//        $num_rows = mysql_num_rows($qryRes);
    while ($row = mysql_fetch_assoc($qryRes_amc)) {
        $stock_arr['amc'] += $row['amc'];
    }
    foreach ($reporting_arr as $key => $id) {
        if($status=='so'){
        if (($stock_arr[$id]['soh'] / $stock_arr['amc']) == 0 || $stock_arr[$id]['soh'] / $stock_arr['amc'] == null) {
            $stock_arr[$id]['mos'] = 0;
        } 
        }
        else if($status=='sat'){
         if ($stock_arr[$id]['soh'] / $stock_arr['amc'] > 0) {
            $stock_arr[$id]['mos'] = ($stock_arr[$id]['soh'] / $stock_arr['amc']);
        }
        }
    }
    $us = $os = 0;
}
//print_r($stk);exit;
?>
<div class="widget widget-tabs">
    <div class="widget-body" id="a2" style='background-color: white;'>
        <ul class="list-inline panel-actions" style='float: right;' >
            <li><a   id="panel-fullscreen_a1" role="button" title="Toggle fullscreen"><i class="glyphicon glyphicon-resize-full"></i></a></li>
        </ul>

        <h3 style="text-align: center;">Product Drill down</h3>
        <?php
        $xmlstore = '<chart caption=" " allowSelection="0"    subcaption="" captionfontsize="14" placeValuesInside="0" subcaptionfontsize="14" basefontcolor="#333333" basefont="Helvetica Neue,Arial" subcaptionfontbold="0" xaxisname="Months" yaxisname="Consumption" showvalues="1" palettecolors="#0075c2,#1aaf5d,#AF1AA5,#AF711A,#D93636" bgcolor="#ffffff" showborder="0" showshadow="0" showalternatehgridcolor="0" showcanvasborder="0" showxaxisline="1" xaxislinethickness="1" xaxislinecolor="#999999" canvasbgcolor="#ffffff" legendborderalpha="0" legendshadow="0" divlinealpha="100" divlinecolor="#999999" divlinethickness="1" divlinedashed="1" divlinedashlen="1" >';

        foreach ($reporting_arr as $key => $value) {
            $xmlstore .= '    <set label="' . $key . '" value="' . number_format($stock_arr[$value]['mos'], 2) . '"  />';
        }

        $xmlstore .= ' </chart>';
        FC_SetRenderer('javascript');
        echo renderChart(PUBLIC_URL . "FusionCharts/Charts/Line.swf", "", $xmlstore, 'c_chart', '100%', 300, false, false);
        ?>

    </div>
</div>

<script>
    $(document).ready(function () {
        //Toggle fullscreen
        $("#panel-fullscreen_a1").click(function (e) {
            e.preventDefault();
//        console.log('into js');
            var $this = $(this);

            if ($this.children('i').hasClass('glyphicon-resize-full'))
            {
                $this.children('i').removeClass('glyphicon-resize-full');
                $this.children('i').addClass('glyphicon-resize-small');
            } else if ($this.children('i').hasClass('glyphicon-resize-small'))
            {
                $this.children('i').removeClass('glyphicon-resize-small');
                $this.children('i').addClass('glyphicon-resize-full');
            }
            $(this).closest('div').toggleClass('panel-fullscreen');
        });
    });


</script>