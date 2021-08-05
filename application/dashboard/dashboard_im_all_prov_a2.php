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
$from_date = date('Y-m', strtotime($from_date));
$to_date = (!empty($_REQUEST['to_date']) ? $_REQUEST['to_date'] : '');
$to_date = date('Y-m', strtotime($to_date));
$product = (!empty($_REQUEST['product']) ? $_REQUEST['product'] : '');
$search_type = isset($_REQUEST['search_type']) ? $_REQUEST['search_type'] : '';
$district=(!empty($_REQUEST['district']) ? $_REQUEST['district'] : '');
$prod_filter='';
if($search_type==1){
    $prod_filter="AND itminfo_tab.generic_name LIKE '%$product%'";
}
 else {
    $prod_filter="AND itminfo_tab.itm_id = $product";
}

if ($province == 1) {
    $queryItem = "SELECT DISTINCT
                                                            tbl_warehouse.wh_name,
                                                            tbl_warehouse.wh_id
                                                            FROM
                                                            tbl_warehouse
                                                            INNER JOIN project_locations ON tbl_warehouse.dist_id = project_locations.location_id
                                                            WHERE
                                                            tbl_warehouse.dist_id IN(".implode($district,',').") AND
                                                            tbl_warehouse.is_allowed_im = 1 AND
                                                            tbl_warehouse.stkid IN (7, 74, 145, 276,951)
                                                            ORDER by wh_name
                                                                     ";
//    print_r($queryItem);exit;
    //Result
    $rsprov = mysql_query($queryItem) or die();
    while ($rowItem = mysql_fetch_array($rsprov)) {
        $wh_id[$rowItem['wh_id']] = $rowItem['wh_id'];
    }
} else if ($province == 2) {

    $queryItem = "SELECT DISTINCT
                                                            tbl_warehouse.wh_name,
                                                            tbl_warehouse.wh_id
                                                            FROM
                                                            tbl_warehouse
                                                            INNER JOIN project_locations ON tbl_warehouse.dist_id = project_locations.location_id
                                                            WHERE
                                                            tbl_warehouse.dist_id IN(".implode($district,',').") AND
                                                            tbl_warehouse.is_allowed_im = 1 AND
                                                           tbl_warehouse.stkid IN (7, 74, 145, 276,951)
                                                            ORDER by wh_name
                                                                     ";
//    print_r($queryItem);exit;
    //Result
    $rsprov = mysql_query($queryItem) or die();
    while ($rowItem = mysql_fetch_array($rsprov)) {
        $wh_id[$rowItem['wh_id']] = $rowItem['wh_id'];
    }
} else if ($province == 3) {

    $queryItem = "SELECT DISTINCT
                                                            tbl_warehouse.wh_name,
                                                            tbl_warehouse.wh_id
                                                            FROM
                                                            tbl_warehouse
                                                            INNER JOIN project_locations ON tbl_warehouse.dist_id = project_locations.location_id
                                                            WHERE
                                                             tbl_warehouse.dist_id IN(".implode($district,',').") AND
                                                            tbl_warehouse.is_allowed_im = 1 AND
                                                           tbl_warehouse.stkid IN (7, 74, 145, 276,951)
                                                            ORDER by wh_name
                                                                     ";
//    print_r($queryItem);exit;
    //Result
    $rsprov = mysql_query($queryItem) or die();
    while ($rowItem = mysql_fetch_array($rsprov)) {
        $wh_id[$rowItem['wh_id']] = $rowItem['wh_id'];
    }
}
$avg_consumption_date = date('Y-m', strtotime("-3 month", strtotime($from_without_date)));
//$from_date .= '-01';
$from_date_last_day = date('Y-m-t', strtotime($from_date));
//print_r($wh_id);exit;
?>
<div class="widget widget-tabs" style="border:0px;">
    <div class="widget-body" id="a2" style='background-color: white;'>
        <ul class="list-inline panel-actions" style='float: right;' >
            <li><a   id="panel-fullscreen_a2" role="button" title="Toggle fullscreen"><i class="glyphicon glyphicon-resize-full"></i></a></li>
        </ul>
        <h3 style="text-align: center;"><?php echo "District Stores Month wise Receiving Trend " ?></h3>
                <h3 style="text-align: center;"><?php echo "(Drilldown) " ?></h3>

<?php
$stock_arr = $wh_arr = array();
$count = 1;
$counter = 0;



$qry_soh = "SELECT
	ABS(SUM(tbl_stock_detail.Qty)) AS receive,
	DATE_FORMAT(
		tbl_stock_master.TranDate,
		'%Y-%m'
	) AS t_date,
	stock_batch.item_id,
        tbl_warehouse.wh_id,
        tbl_warehouse.wh_name,
	itminfo_tab.itm_name
FROM
	tbl_stock_detail
INNER JOIN tbl_stock_master ON tbl_stock_detail.fkStockID = tbl_stock_master.PkStockID
-- INNER JOIN tbl_warehouse ON tbl_stock_master.WHIDFrom = tbl_warehouse.wh_id
INNER JOIN stock_batch ON tbl_stock_detail.BatchID = stock_batch.batch_id
INNER JOIN itminfo_tab ON stock_batch.item_id = itminfo_tab.itm_id
INNER JOIN tbl_warehouse ON stock_batch.wh_id = tbl_warehouse.wh_id
WHERE
	tbl_warehouse.wh_id IN(" . implode(",", $wh_id) . ")
AND tbl_stock_master.TranTypeID = 1
AND tbl_warehouse.stkid IN (7, 74, 145, 276,951)
$prod_filter
AND (
	DATE_FORMAT(
		tbl_stock_master.TranDate,
		'%Y-%m'
	) >= '$from_date'
	AND DATE_FORMAT(
		tbl_stock_master.TranDate,
		'%Y-%m'
	) <= '$to_date'
)
GROUP BY
	DATE_FORMAT(
		tbl_stock_master.TranDate,
		'%Y-%m'
	),
	stock_batch.wh_id";

//print_r($qry_soh);
//exit;
$qryRes = mysql_query($qry_soh);
$num_rows = mysql_num_rows($qryRes);
while ($row = mysql_fetch_assoc($qryRes)) {

    $stock_arr[$row['wh_id']][$row['t_date']] = $row['receive'];
    $reporting_array[$row['t_date']] = $row['t_date'];
    $wh_name[$row['wh_id']] = $row['wh_name'];
//        $wh_arr[$row['wh_id']] = $row['wh_name'];
}
//print_r($stock_arr);exit;
//        foreach ($item_arr as $key => $id) {
//            if (($stock_arr[$id]['soh'] ) == 0 || $stock_arr[$id]['soh'] == null) {
//                $so += $stock_arr[$id]['soh'];
//            }
//        }

$xmlstore = '<chart caption=" " allowSelection="0" PYAxisName="Receiving"  axisOnLeft="0"  subcaption="" captionfontsize="14" placeValuesInside="0" subcaptionfontsize="14" basefontcolor="#333333" basefont="Helvetica Neue,Arial" subcaptionfontbold="0" xaxisname="Months" yaxisname="Consumption" showvalues="1"  bgcolor="#ffffff" showborder="0" showshadow="0" showalternatehgridcolor="0" showcanvasborder="0" showxaxisline="1" xaxislinethickness="1" xaxislinecolor="#999999" canvasbgcolor="#ffffff" legendborderalpha="0" legendshadow="0" divlinealpha="100" divlinecolor="#999999" divlinethickness="1" divlinedashed="1" divlinedashlen="1" theme="zune">';
$xmlstore .= ' <categories>';
foreach ($reporting_array as $key => $value) {
    $xmlstore .= ' <category label="' . date('M-Y', strtotime($key)) . '" />';
}

$xmlstore .= ' </categories>';


foreach ($wh_name as $key => $value) {
    $xmlstore .= ' <dataset renderas="line"  parentYAxis="P"    seriesname="' . $value . '">';
    foreach ($reporting_array as $date => $val) {
        if(!isset($stock_arr[$key][$date]))
        {
            $xmlstore .= '    <set  value="0"  />';
        }
        else{
            $xmlstore .= '    <set  value="' . $stock_arr[$key][$date] . '" link="P-detailsWin,width=900,height=600,toolbar=no,scrollbars=yes, resizable=no-dashboard_im_all_prov_a2_table.php?prov_sel='.$province.'&product='.$product.'&wh_id='.$key.'&wh_name='.$value.'&from_date='.$from_date.'&to_date='.$to_date.'&search_type='.$search_type.'&district='.implode($district,',').'" />';
        }
       
    }
    $xmlstore .= '  </dataset>';
}
$xmlstore .= ' </chart>';
FC_SetRenderer('javascript');
echo renderChart(PUBLIC_URL . "FusionCharts/Charts/MSCombiDY2D.swf", "", $xmlstore, 'r_chart', '100%', 300, false, false);
?>

    </div>
</div>

<script>
    $(document).ready(function () {
        //Toggle fullscreen
        $("#panel-fullscreen_a2").click(function (e) {
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