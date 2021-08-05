<?php
include("../includes/classes/Configuration.inc.php");
//Login();
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
//$to_date = (!empty($_REQUEST['to_date']) ? $_REQUEST['to_date'] : '');
//$to_date = date('Y-m-d', strtotime($to_date));
$product = (!empty($_REQUEST['product']) ? $_REQUEST['product'] : '');  
$product_array=implode($product,','); 
 
?>
<div class="widget widget-tabs" style="border:0px;">
    <div class="widget-body" id="a1" style='background-color: white;'>
        <ul class="list-inline panel-actions" style='float: right;' >
            <li><a   id="panel-fullscreen_a1" role="button" title="Toggle fullscreen"><i class="glyphicon glyphicon-resize-full"></i></a></li>
        </ul>
        <h3 style="text-align: center;"><?php echo $caption?></h3>
        <?php
        $stock_arr =$wh_arr= array();
        $count = 1;
        $counter = 0;



        $qry_soh = "SELECT
	itminfo_tab.itm_id AS item_id,	 
	Sum(tbl_stock_detail.Qty) AS soh,
	itminfo_tab.itm_name,
	tbl_warehouse.wh_id,
	tbl_warehouse.prov_id,
	tbl_warehouse.stkid,
	tbl_warehouse.dist_id,
	tbl_warehouse.wh_name,
	stock_batch.batch_no,
        itminfo_tab.generic_name
FROM
	itminfo_tab
INNER JOIN stock_batch ON itminfo_tab.itm_id = stock_batch.item_id
INNER JOIN tbl_stock_detail ON stock_batch.batch_id = tbl_stock_detail.BatchID
INNER JOIN tbl_stock_master ON tbl_stock_detail.fkStockID = tbl_stock_master.PkStockID
INNER JOIN tbl_trans_type ON tbl_stock_master.TranTypeID = tbl_trans_type.trans_id
INNER JOIN tbl_warehouse ON stock_batch.wh_id = tbl_warehouse.wh_id
INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
WHERE
	(
		DATE_FORMAT(
			tbl_stock_master.TranDate,
			'%Y-%m-%d'
		) <= '$from_date-31'
		 

	)
 
AND tbl_stock_master.temp = 0
AND tbl_warehouse.prov_id = $province
AND (stakeholder.lvl = 3 OR stakeholder.lvl=7 )
AND itminfo_tab.itm_id IN($product_array) 
AND tbl_warehouse.stkid IN(7,74,145,276)
GROUP BY
	itm_id,
wh_id";

//print_r($qry_soh);
//exit;
$qryRes = mysql_query($qry_soh);
$num_rows = mysql_num_rows($qryRes);
while ($row = mysql_fetch_assoc($qryRes)) { 
        $stock_arr[$row['item_id']]['soh'] += $row['soh'];
    $itm_arr[$row['item_id']] = $row['itm_name'];
}
$qry = "SELECT
Sum(ABS(tbl_stock_detail.Qty)) AS Issue,
stock_batch.item_id,
tbl_warehouse.wh_id,
itminfo_tab.itm_name,
DATE_FORMAT(
		tbl_stock_master.TranDate,
		'%Y-%m'
	)
FROM
	tbl_stock_detail
INNER JOIN tbl_stock_master ON tbl_stock_detail.fkStockID = tbl_stock_master.PkStockID
INNER JOIN tbl_warehouse ON tbl_stock_master.WHIDFrom = tbl_warehouse.wh_id
INNER JOIN stock_batch ON tbl_stock_detail.BatchID = stock_batch.batch_id
INNER JOIN itminfo_tab ON stock_batch.item_id = itminfo_tab.itm_id
INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
WHERE
DATE_FORMAT(
						tbl_stock_master.TranDate,
						'%Y-%m-%d'
					) >= '$from_date-01'
					AND DATE_FORMAT(
						tbl_stock_master.TranDate,
						'%Y-%m-%d'
					) <= '$from_date-31'
AND itminfo_tab.itm_id IN($product_array) 
AND tbl_warehouse.prov_id = $province AND
tbl_stock_master.TranTypeID = 2 
AND (stakeholder.lvl = 3 OR stakeholder.lvl=7 )
AND tbl_warehouse.stkid IN(7,74,145,276)
GROUP BY

	itm_id,
tbl_warehouse.wh_id


";


//        print_r($qry);exit;
$dist_res = mysqli_query($connc, $qry);
while ($row = $dist_res->fetch_assoc()) {
     $stock_arr[$row['item_id']]['consumption'] += $row['Issue'];
}

$qry_avg_consumption="SELECT
	ROUND(AVG(A.consumption)) AS AMC,
	A.item_id,
	A.wh_id
FROM
	(
		SELECT
			ABS(SUM(tbl_stock_detail.Qty)) AS consumption,
			stock_batch.item_id,
			tbl_warehouse.wh_id
		FROM
			tbl_stock_detail
		INNER JOIN tbl_stock_master ON tbl_stock_detail.fkStockID = tbl_stock_master.PkStockID
		INNER JOIN tbl_warehouse ON tbl_stock_master.WHIDFrom = tbl_warehouse.wh_id
		INNER JOIN stock_batch ON tbl_stock_detail.BatchID = stock_batch.batch_id
                INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
		WHERE
			tbl_warehouse.prov_id = $province
		AND tbl_warehouse.stkid IN (7, 74, 145, 276)
		AND tbl_stock_master.TranTypeID = 2
                AND (stakeholder.lvl = 3 OR stakeholder.lvl=7 )
		AND stock_batch.item_id IN ($product_array)
		AND ABS(tbl_stock_detail.Qty) > 0
		AND DATE_FORMAT(
			tbl_stock_master.TranDate,
			'%Y-%m'
		) <= '2019-11'
		GROUP BY
			DATE_FORMAT(
				tbl_stock_master.TranDate,
				'%Y-%m'
			),
			stock_batch.item_id,
			tbl_warehouse.wh_id
		LIMIT 12
	) A
GROUP BY
A.item_id,
	A.wh_id
";

//print_r($qry_avg_consumption);exit;
$dist_res = mysqli_query($connc, $qry_avg_consumption);
while ($row = $dist_res->fetch_assoc()) {
     $stock_arr[$row['item_id']]['avg_consumption'] += $row['AMC'];
}
foreach ($itm_arr as $key => $value) {
    $stock_arr[$key]['mos']=($stock_arr[$key]['soh']/$stock_arr[$key]['avg_consumption']);
}
//echo '<pre>';
//print_r($stock_arr);exit;
       $xmlstore = '<chart caption=" " theme="zune" allowSelection="0" PYAxisName="Stock status"  axisOnLeft="0"  subcaption="" captionfontsize="14" placeValuesInside="0" subcaptionfontsize="14" basefontcolor="#333333" basefont="Helvetica Neue,Arial" subcaptionfontbold="0"  yaxisname="Months of Stock" showvalues="1"  bgcolor="#ffffff" showborder="0" showshadow="0" showalternatehgridcolor="0" showcanvasborder="0" showxaxisline="1" xaxislinethickness="1" xaxislinecolor="#999999" canvasbgcolor="#ffffff" legendborderalpha="0" legendshadow="0" divlinealpha="100" divlinecolor="#999999" divlinethickness="1" divlinedashed="1" divlinedashlen="1" >';
        $xmlstore .= ' <categories>';
        foreach ($itm_arr as $key => $value) {
            $xmlstore .= ' <category label="' . $value . '" />';
        }

        $xmlstore .= ' </categories>';

 
         
//        $xmlstore .= ' <dataset   seriesname="SOH">';
//        foreach ($itm_arr as $id => $name) {
//            $xmlstore .= '    <set  value="' . $stock_arr[$id]['soh'] . '"  />';
//        }
//        $xmlstore .= '  </dataset>';
        $xmlstore .= ' <dataset   seriesname="Months of Stock">';
       foreach ($itm_arr as $id => $name) {
            $xmlstore .= '    <set  value="' . number_format($stock_arr[$id]['mos'],2) . '"  />';
        }
        $xmlstore .= '  </dataset>';


        $xmlstore .= ' </chart>';
//        print_r($xmlstore);
//        exit;
        FC_SetRenderer('javascript');
        echo renderChart(PUBLIC_URL . "FusionCharts/Charts/MSColumn2D.swf", "", $xmlstore, 'prov_im_a1', '100%', 300, false, false);
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