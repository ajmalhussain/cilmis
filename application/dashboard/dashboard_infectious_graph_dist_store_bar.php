<?php
include("../includes/classes/Configuration.inc.php");
Login();
//include db
include(APP_PATH . "includes/classes/db.php");
//include functions
include APP_PATH . "includes/classes/functions.php";

//include FusionCharts
include(PUBLIC_PATH . "FusionCharts/Code/PHP/includes/FusionCharts.php");


$where_hf = '';
$province = (!empty($_REQUEST['prov_sel']) ? $_REQUEST['prov_sel'] : '');
$from_date = (!empty($_REQUEST['from_date']) ? $_REQUEST['from_date'] : '');
$from_date = date('Y-m-d', strtotime($from_date));
$from_without_date = date('Y-m', strtotime($from_date));
$reporting_start_month=date('Y-m-d', strtotime("-12 month", strtotime($from_without_date . '-01')));
$dist_id = 93;
$avg_consumption_date = date('Y-m-d', strtotime("-3 month", strtotime($from_without_date . '-01')));
//$from_date .= '-01';
$from_date_last_day = date('Y-m-t', strtotime($from_date));
//print_r($endDateLastDay);exit;
?>
<div class="widget widget-tabs" style="border:0px;">
    <div class="widget-body" id="a2" style='background-color: white;'>
        <ul class="list-inline panel-actions" style='float: right;' >
            <li><a   id="panel-fullscreen_a1" role="button" title="Toggle fullscreen"><i class="glyphicon glyphicon-resize-full"></i></a></li>
        </ul>
        <h3 style="text-align: center;">District Stores Comparison</h3>
        <?php
        $stock_arr = array();
        $dist_arr_name = array(14 => 'Charsada', 77 => 'Lakki Marwat', 93 => 'Swat', 149 => 'Mohmmand Agency');
        $count = 1;
        $counter = 0;
        $qry_soh = "SELECT
	itminfo_tab.itm_id AS item_id,
	SUM(tbl_stock_detail.Qty) AS soh,
	itminfo_tab.itm_name,
	tbl_warehouse.wh_id,
	tbl_warehouse.prov_id,
	tbl_warehouse.stkid,
	tbl_warehouse.dist_id,
	tbl_warehouse.wh_name
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
AND tbl_warehouse.dist_id IN(14,77,93,149)
AND stakeholder.lvl = 3
GROUP BY
	itminfo_tab.itm_id";

//        print_r($qry_soh);
//        exit;
        $qryRes = mysql_query($qry_soh);
        $num_rows = mysql_num_rows($qryRes);
        while ($row = mysql_fetch_assoc($qryRes)) {
            $stock_arr[$row['dist_id']][$row['item_id']]['soh'] = $row['soh'];
            $dist_array[$row['dist_id']][$row['item_id']] = $row['item_id'];
            $num_row_arr[$row['dist_id']]+=1;
        }
        $qry_amc = "SELECT
              itminfo_tab.itm_id AS item_id,
			(ROUND(
				SUM(ABS(tbl_stock_detail.Qty)) / 3
			)) AS amc,
                        tbl_warehouse.dist_id
FROM
tbl_stock_master
INNER JOIN tbl_stock_detail ON tbl_stock_master.PkStockID = tbl_stock_detail.fkStockID
INNER JOIN stock_batch ON stock_batch.batch_id = tbl_stock_detail.BatchID
INNER JOIN itminfo_tab ON stock_batch.item_id = itminfo_tab.itm_id
INNER JOIN tbl_warehouse ON stock_batch.wh_id = tbl_warehouse.wh_id
INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
WHERE
tbl_stock_master.TranTypeID = 2 AND
DATE_FORMAT(tbl_stock_master.TranDate, '%Y-%m-%d') >= '$avg_consumption_date' AND DATE_FORMAT(tbl_stock_master.TranDate, '%Y-%m-%d') < '$from_without_date-01' AND
stock_batch.item_id = itminfo_tab.itm_id 
AND tbl_warehouse.dist_id IN(14,77,93,149)
    AND stakeholder.lvl = 3
GROUP BY
	itminfo_tab.itm_id
";

//        print_r($qry_amc);
//        exit;
        $qryRes = mysql_query($qry_amc);
//        $num_rows = mysql_num_rows($qryRes);
        while ($row = mysql_fetch_assoc($qryRes)) {
            $stock_arr[$row['dist_id']][$row['item_id']]['amc'] = $row['amc'];
        }
        
        foreach ($dist_array as $dist_id => $item_array) {
            foreach ($item_array as $key => $id) {
                if (($stock_arr[$dist_id][$key]['soh'] / $stock_arr[$dist_id][$key]['amc']) == 0 || $stock_arr[$dist_id][$key]['soh'] / $stock_arr[$dist_id][$key]['amc'] == null) {
                    $so[$dist_id]++;
                } else if ($stock_arr[$dist_id][$key]['soh'] / $stock_arr[$dist_id][$key]['amc'] > 0) {
                    $sat[$dist_id]++;
                }
            }
        }
        $us = $os = 0;
//        echo '<pre>';
//        print_r($sat);
//        exit;
        $xmlstore = "<chart  theme='zune' yAxisMaxValue='100' labelDisplay='auto' numberSuffix='%' showValues='1' >";
	
        $xmlstore .= "<categories>";
        foreach ($dist_arr_name as $dist_id => $dist_name) {
            $xmlstore .= "<category label='$dist_name' />";
        }
//        print_r($xmlstore);exit;
        $xmlstore .= "</categories>";
        $xmlstore .= "<dataset seriesName='Stock Out'>";
        foreach ($dist_arr_name as $dist_id => $name) {
            $xmlstore .= '<set value="' . round(( $so[$dist_id] / $num_row_arr[$dist_id]) * 100, 2) . '" color="#FF0000" />';
        }
        $xmlstore .= "</dataset>";
        $xmlstore .= "<dataset seriesName='Satisfactory'>";
        foreach ($dist_arr_name as $dist_id => $name) {
            $xmlstore .= '<set value="' . round(($sat[$dist_id] / $num_row_arr[$dist_id]) * 100, 2) . '" color="#31B404"/>';
        }
        $xmlstore .= "</dataset>"; 

        $xmlstore .= ' </chart>';
//        print_r($xmlstore);
//        exit;
        FC_SetRenderer('javascript');
        echo renderChart(PUBLIC_URL . "FusionCharts/Charts/MSColumn2D.swf", "", $xmlstore, 'dist_im_bar', '100%', 300, false, false);
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