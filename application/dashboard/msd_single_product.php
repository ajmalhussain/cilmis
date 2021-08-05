<!DOCTYPE html>
<?php
include("../includes/classes/Configuration.inc.php");
//Including db file
Login();
include(APP_PATH . "includes/classes/db.php");
//Including header file
include(PUBLIC_PATH . "html/header.php");

include(PUBLIC_PATH . "FusionCharts/Code/PHP/includes/FusionCharts.php");
$product = (!empty($_REQUEST['product']) ? $_REQUEST['product'] : '');
$to_date = (!empty($_REQUEST['to_date']) ? $_REQUEST['to_date'] : '');
//echo '<pre>';print_r($_REQUEST);exit;

if(!empty($product)){
    $qry_wh="( SELECT
tbl_warehouse.wh_id,
tbl_warehouse.wh_name,
tbl_warehouse.dist_id,
tbl_warehouse.prov_id
FROM
tbl_warehouse
INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
WHERE
tbl_warehouse.prov_id = 1 AND
stakeholder.lvl = 2 AND tbl_warehouse.is_allowed_im = 1 AND
tbl_warehouse.stkid IN (7,145) ) 
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
            $wh_array[$row['wh_id']]=$row['wh_id'];
            $wh_name_array[$row['wh_id']]=$row['wh_name'];
           
        }
        $qry_soh = "SELECT
itminfo_tab.itm_id,
Sum(tbl_stock_detail.Qty) AS soh,
tbl_warehouse.wh_name,
tbl_warehouse.wh_id,
itminfo_tab.itm_name,
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
stock_batch.wh_id IN (".implode($wh_array,',').") AND
itminfo_tab.itm_id = $product
GROUP BY
	itminfo_tab.itm_id,wh_id 
ORDER BY
itm_name ASC";

//        print_r($qry_soh);
//        exit;
        $qryRes = mysql_query($qry_soh);
        $num_rows = mysql_num_rows($qryRes);
        while ($row = mysql_fetch_assoc($qryRes)) {
            $stock_arr[$row['wh_id']]['soh'] = $row['soh']; 
            
        }
}

?>

<script src="<?php echo PUBLIC_URL;?>assets/chart.min.js"></script>
	<script src="<?php echo PUBLIC_URL;?>assets/utils.js"></script>
	<style>
		canvas {
			-moz-user-select: none;
			-webkit-user-select: none;
			-ms-user-select: none;
		}
	</style>
        <link rel="stylesheet" type="text/css" href="../../public/assets/global/plugins/select2/select2.css"/>
</head>
<?php
if (isset($_REQUEST['to_date'])) {
    $to_date = $_REQUEST['to_date'];
}
?>
<body class="page-header-fixed page-quick-sidebar-over-content">
    <!--<div class="pageLoader"></div>-->
    <!-- BEGIN HEADER -->


    <div class="page-container">
        <?php
//Including top file
        include PUBLIC_PATH . "html/top.php";
//Including top_im file
        include PUBLIC_PATH . "html/top_im.php";
        ?>

        <div class="page-content-wrapper" >
            <div class="page-content">

                <div class="row">
                    <div class="col-md-12" >
                        <div class="widget" data-toggle="" >
                            <div class="widget-head" >
                                <h3 class="heading">Filter by</h3>
                            </div>
                            <div class="widget-body collapse in">

                                <form name="frm" id="frm" action="" method="POST" >
                                    <table width="100%">
                                        <tbody>
                                            <tr>
                                                 
                                                <td class="col-md-2 ">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label class="control-label">Stock As On:</label>
                                                            <div class="form-group">
                                                                <input type="text" name="to_date" id="to_date"  class="form-control input-sm" value="<?php
                                                                if (isset($_REQUEST['to_date'])) {
                                                                    echo date('Y-m-d', strtotime($to_date));
                                                                } else {

                                                                    echo date('Y-m-d');
                                                                }
                                                                ?>"  readonly="true">
                                                            </div>
                                                        </div>
                                                    </div>

                                                </td>
                                                <td class="col-md-2">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label class="control-label">Product</label>
                                                            <br>
                                                            <select   name="product" id="product" class="input-large select2me">
                                                                <option value="">Select</option>
                                                                <?php
                                                                //fetching only the relevant products
                                                                 $qry = "
                                                                    SELECT
                                                                            distinct itminfo_tab.itm_id,
                                                                            itminfo_tab.itm_name,
                                                                            itminfo_tab.generic_name
                                                                    FROM
                                                                    itminfo_tab
                                                                    INNER JOIN stock_batch ON itminfo_tab.itm_id = stock_batch.item_id
                                                                    INNER JOIN tbl_warehouse ON stock_batch.wh_id = tbl_warehouse.wh_id
                                                                    INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
                                                                    WHERE tbl_warehouse.prov_id = 1 AND
                                                                    stakeholder.lvl = 2 AND tbl_warehouse.is_allowed_im = 1 AND
                                                                    tbl_warehouse.stkid IN (7,145)
                                                                    ORDER BY
                                                                            itminfo_tab.itm_name ASC
 


                                                                    ";
                                                                $dist_res = mysql_query($qry);
                                                                $pr_name = '';
                                                                while ($row = mysql_fetch_array($dist_res)) {
                                                                    $sel = '';
                                                                    
                                                                    if(!empty($product) && $product == $row['itm_id']){
                                                                        $sel = ' selected ';
                                                                        $pr_name = $row['itm_name'];
//                                                                        echo $pr_name.''.$row['itm_id'];exit;
                                                                    }
                                                                    ?>

                                                                    <option <?=$sel?> value="  <?php echo $row['itm_id']; ?>"><?php echo $row['itm_name'].' ['.$row['generic_name'].']'; ?></option>
                                                                    <?php
                                                                }
                                                                ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </td>

                                                <td class="col-md-2">

                                                    <!--<label class="control-label">&nbsp;</label>-->
                                                    <input name="submit_btn" class="btn btn-succes" id="submit_btn" style="margin-top:8%" value="Go" type="submit">
                                                </td>
                                                <td class="col-md-2"></td>
                                                <td class="col-md-2"></td>
                                            </tr>
                                        </tbody>
                                    </table>

                                </form>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="row" style="margin-left:0px;margin-right: 0px;" id="graph_row_1">
                     <div id="canvas-holder" style="width:90%">
                                    <canvas id="chart-area"></canvas>
                            </div>


                </div>

            </div>
        </div>
    </div>
    

    	
    <?php
//Including footer file
    include PUBLIC_PATH . "/html/footer.php";
    ?>
    <script src="<?= PUBLIC_URL ?>js/bootstrap-multiselect.js"></script>

    <script language="Javascript" src="<?php echo PUBLIC_URL; ?>FusionCharts/Charts/FusionCharts.js"></script>
    <script language="Javascript" src="<?php echo PUBLIC_URL; ?>FusionCharts/themes/fusioncharts.theme.zune.js"></script>
    <script type="text/javascript" src="../../public/assets/global/plugins/select2/select2.min.js"></script>
    <script type="text/javascript">
                            
                                $(function () {
                                     
                                     $('#to_date').datepicker({
                                        dateFormat: "yy-mm-dd",
                                        constrainInput: false,
                                        changeMonth: true,
                                        changeYear: true,
                                        maxDate: ''
                                    });
                                });


    </script>
<?php
if(!empty($product))
{
?>
	<script>
		var randomScalingFactor = function() {
			return Math.round(Math.random() * 100);
		};

		var chartColors = window.chartColors;
		var color = Chart.helpers.color;
		var config = {
			data: {
				datasets: [{
					data: [
						 <?php
                                                 foreach ($wh_name_array as $wh_id => $wh_name) {
                                                    echo   @$stock_arr[$wh_id]['soh'] .',';
                                                }
                                                 ?>
					],
					backgroundColor: [
						color(chartColors.red).alpha(0.5).rgbString(),
						color(chartColors.orange).alpha(0.5).rgbString(),
						color(chartColors.yellow).alpha(0.5).rgbString(),
						color(chartColors.green).alpha(0.5).rgbString(),
						color(chartColors.blue).alpha(0.5).rgbString(),
					],
					label: 'My dataset' // for legend
				}],
				labels: [
					<?php
                                        foreach ($wh_name_array as $wh_id => $wh_name) {
                                                echo "'".$wh_name."',";
                                            }
                                        ?>
				]
			},
			options: {
				responsive: true,
				legend: {
					position: 'right',
				},
				title: {
					display: true,
					text: 'Stock Status of <?php echo $pr_name;?> at MSD Stores'
				},
				scale: {
					ticks: {
						beginAtZero: true
					},
					reverse: false
				},
				animation: {
					animateRotate: false,
					animateScale: true
				}
			}
		};

		window.onload = function() {
			var ctx = document.getElementById('chart-area');
			window.myPolarArea = Chart.PolarArea(ctx, config);
		};
                

		
	</script>
        <?php
}
        ?>
        </body>
</html>
