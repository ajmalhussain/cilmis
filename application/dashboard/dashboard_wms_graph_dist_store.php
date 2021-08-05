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
$prod_filter = '';
if ($search_type == 1) {
    $prod_filter = " AND itminfo_tab.generic_name LIKE '%$product%'";
} else {
    $prod_filter = " AND itminfo_tab.itm_id = $product";
}

if ($province == 3) {
    $dist_filter_c = " AND tbl_warehouse.dist_id IN(149,14,77,93)"; 
    $dist_name_filter = " AND tbl_locations.PkLocID IN(149,14,77,93)";
}  
if ($province == 3) {
    $queryItem = "SELECT DISTINCT
                                                            tbl_warehouse.wh_name,
                                                            tbl_warehouse.wh_id
                                                            FROM
                                                            tbl_warehouse
                                                             WHERE 
                                                            tbl_warehouse.is_allowed_im = 1 $dist_filter_c AND
                                                            tbl_warehouse.stkid =7
                                                            ORDER by wh_name
                                                                     ";
//    print_r($queryItem);exit;
    //Result
    $rsprov = mysql_query($queryItem) or die();
    while ($rowItem = mysql_fetch_array($rsprov)) {
        $wh_id[$rowItem['wh_id']] = $rowItem['wh_id'];
    }
} 
 
//print_r($wh_id);exit;
?>
<div class="widget widget-tabs">
    <div class="widget-body" id="a2" style='background-color: white;'>

<?php
$stock_arr = $prod_array = $prov_array = $prod_id = $stk_array = array();
$count = 1;
$counter = 0;

if ($search_type == 1) {
    $qry_products = "select itminfo_tab.itm_id from itminfo_tab where itminfo_tab.generic_name LIKE '%$product%'";
    $qryRes = mysql_query($qry_products);
    while ($row = mysql_fetch_assoc($qryRes)) {
        $product_arr[$row['itm_id']] = $row['itm_id'];
    }
}

//  print_r($stock_arr);exit;
$qry_soh = "SELECT
        ABS(SUM(tbl_stock_detail.Qty)) AS soh,
        DATE_FORMAT(
                        tbl_stock_master.TranDate,
                        '%Y-%m'
                ) AS t_date,
        stock_batch.item_id,
        itminfo_tab.itm_name,
        tbl_warehouse.wh_id,
        tbl_warehouse.wh_name,
        tbl_warehouse.prov_id,
        tbl_warehouse.dist_id
        FROM
        tbl_stock_detail
        INNER JOIN tbl_stock_master ON tbl_stock_detail.fkStockID = tbl_stock_master.PkStockID
        INNER JOIN stock_batch ON tbl_stock_detail.BatchID = stock_batch.batch_id
        INNER JOIN itminfo_tab ON stock_batch.item_id = itminfo_tab.itm_id
        INNER JOIN tbl_warehouse ON stock_batch.wh_id = tbl_warehouse.wh_id
        WHERE
                tbl_warehouse.wh_id IN(" . implode(",", $wh_id) . ")
        AND tbl_warehouse.stkid =7
        $prod_filter
        AND (
        DATE_FORMAT(
                        tbl_stock_master.TranDate,
                        '%Y-%m-%d'
                ) >= '$from_date'
        AND
                 DATE_FORMAT(
                        tbl_stock_master.TranDate,
                        '%Y-%m-%d'
                ) <= '$to_date'
        )
        GROUP BY
	DATE_FORMAT(
		tbl_stock_master.TranDate,
		'%Y-%m'
	),
	stock_batch.wh_id";

//        print_r($qry_soh);
//        exit;
$qryRes = mysql_query($qry_soh);
$num_rows = mysql_num_rows($qryRes);
while ($row = mysql_fetch_assoc($qryRes)) {
    $soh[$row['wh_id']]+=$row['soh'];
    $wh_name[$row['wh_id']]=$row['wh_name'];
    $min_soh=min($soh);
    $max_soh=max($soh);
}
//        print_r($max_soh);exit;
$qry_consumption = "SELECT
	ABS(SUM(tbl_stock_detail.Qty)) AS consumption,
	DATE_FORMAT(
		tbl_stock_master.TranDate,
		'%Y-%m'
	) AS t_date,
	stock_batch.item_id,
        stock_batch.wh_id,
        tbl_warehouse.wh_name,
	itminfo_tab.itm_name
FROM
	tbl_stock_detail
INNER JOIN tbl_stock_master ON tbl_stock_detail.fkStockID = tbl_stock_master.PkStockID
INNER JOIN tbl_warehouse ON tbl_stock_master.WHIDFrom = tbl_warehouse.wh_id
INNER JOIN stock_batch ON tbl_stock_detail.BatchID = stock_batch.batch_id
INNER JOIN itminfo_tab ON stock_batch.item_id = itminfo_tab.itm_id
WHERE
	tbl_warehouse.wh_id IN(" . implode(",", $wh_id) . ")
AND tbl_stock_master.TranTypeID = 2
AND tbl_warehouse.stkid = 7
 $prod_filter
AND (
	DATE_FORMAT(
		tbl_stock_master.TranDate,
		'%Y-%m-%d'
	) >= '$from_date'
	AND DATE_FORMAT(
		tbl_stock_master.TranDate,
		'%Y-%m-%d'
	) <= '$to_date'
)
GROUP BY
	DATE_FORMAT(
		tbl_stock_master.TranDate,
		'%Y-%m'
	),
	stock_batch.wh_id";

//print_r($qry_consumption);
//exit;
$qryRes = mysql_query($qry_consumption);
$num_rows = mysql_num_rows($qryRes);
$i = 0;
while ($row = mysql_fetch_assoc($qryRes)) {
    $consumption[$row['wh_id']]+=$row['consumption'];
    $wh_name[$row['wh_id']]=$row['wh_name'];
    $min_consumption=min($consumption);
    $max_consumption=max($consumption);
}
//        print_r( $stock_arr);exit;
?>
        <div class="col-md-12">
            <div class="col-md-3" id="prov_sufficiency">
                <div class="dashboard-stat red-pink" style="height:130px;box-shadow: 0 0 3px 0 #999;background-image: linear-gradient(to bottom right, #0087ff, #575748);">
<!--                    <div class="visual" style="margin-left:70%;"><i class="fa fa-bar-chart-o" style="margin-left:-10px;"></i></div>-->
                    <div class="desc" style="font-size:24px;float:left;padding-left:20%;"><b>Maximum SoH</b></div>
                    <div class="" >
                        <div class="center" id="general_av_dist" style="font-size:16px;padding-top: 22%;color:white;">
                            <?php 
                            foreach ($soh as $wh_id => $value) {
                                if($value==$max_soh){
                                    echo $wh_name[$wh_id];
                                }
                            }
                            ?>
                        </div>
                        
                    </div>


                </div>
            </div>
            <div class="col-md-3" id="prov_sufficiency">
                <div class="dashboard-stat red-pink" style="height:130px;box-shadow: 0 0 3px 0 #999;background-image: linear-gradient(to bottom right, #00ffb7, #8dabd9);">
                    <!--<div class="visual" style="margin-left:70%;"><i class="fa fa-bar-chart-o" style="margin-left:-10px;"></i></div>-->
                    <div class="desc" style="font-size:24px;float:left;padding-left:20%;"><b>Minimum SoH</b></div>
                    <div class="" >
                        <div class="center" id="general_av_dist" style="font-size:16px;padding-top: 22%;color:white;">
                            <?php 
                            foreach ($soh as $wh_id => $value) {
                                if($value==$min_soh){
                                    echo $wh_name[$wh_id];
                                }
                            }
                            ?>
                        </div>
                        
                    </div>


                </div>
            </div>
            <div class="col-md-3">
                <div class="dashboard-stat red-pink" style="height:130px;box-shadow: 0 0 3px 0 #999;background-image: linear-gradient(to bottom right, #ff00d7, #575748);">
                    <!--<div class="visual" style="margin-left:70%;"><i class="fa fa-bar-chart-o" style="margin-left:-10px;"></i></div>-->
                    <div class="desc" style="font-size:24px;float:left;padding-left:12%;"><b>Max. Consumption/<br>Issue</b></div>
                    <div class="" >
                        <div class="center" id="general_av_dist" style="font-size:16px;padding-top: 22%;color:white;">
                            <?php 
                            foreach ($consumption as $wh_id => $value) {
                                if($value==$max_consumption){
                                    echo $wh_name[$wh_id];
                                }
                            }
                            ?>
                        </div>
                        
                    </div>


                </div>
            </div>
            <div class="col-md-3">
                <div class="dashboard-stat red-pink" style="height:130px;background-image: linear-gradient(to bottom right, #fff3b3, #575748);box-shadow: 0 0 3px 0 #999;">
                    <!--<div class="visual" style="margin-left:70%;"><i class="fa fa-bar-chart-o" style="margin-left:-10px;"></i></div>-->
                    <div class="desc" style="font-size:24px;float:left;padding-left:12%;"><b>Min. Consumption/<br>Issue</b></div>
                    <div class="" >
                        <div class="center" id="general_av_dist" style="font-size:16px;padding-top: 22%;color:white;">
                            <?php 
                            foreach ($consumption as $wh_id => $value) {
                                if($value==$min_consumption){
                                    echo $wh_name[$wh_id];
                                }
                            }
                            ?>
                        </div>
                        
                    </div>


                </div>
            </div>

        </div>
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