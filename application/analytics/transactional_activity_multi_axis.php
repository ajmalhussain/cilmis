<!DOCTYPE html>
<?php
include("../includes/classes/Configuration.inc.php");
//Including db file
Login();
include(APP_PATH . "includes/classes/db.php");
//Including header file
include(PUBLIC_PATH . "html/header.php");

include(PUBLIC_PATH . "FusionCharts/Code/PHP/includes/FusionCharts.php");

$stock_in = $stock_out = $stock_on_hand = $dates_arr = array();

    $qry_in="
        SELECT
            tbl_stock_master.TranDate,
            tbl_stock_master.WHIDFrom,
            tbl_stock_master.WHIDTo,
            tbl_stock_detail.Qty,
            tbl_stock_detail.adjustmentType,
            sysuser_tab.usrlogin_id,
            sysuser_tab.UserID,
            tbl_trans_type.trans_type,
            tbl_trans_type.trans_nature
            FROM
            tbl_stock_master
            INNER JOIN tbl_stock_detail ON tbl_stock_master.PkStockID = tbl_stock_detail.fkStockID
            INNER JOIN stock_batch ON tbl_stock_detail.BatchID = stock_batch.batch_id
            INNER JOIN sysuser_tab ON tbl_stock_master.CreatedBy = sysuser_tab.UserID
            INNER JOIN tbl_trans_type ON tbl_stock_detail.adjustmentType = tbl_trans_type.trans_id
            WHERE
            sysuser_tab.sysusr_type = 38 AND
            sysuser_tab.UserID = 10044 AND
            tbl_stock_master.WHIDTo = 72656 AND
            tbl_trans_type.trans_nature = '+'
ORDER BY tbl_stock_master.TranDate
"; 
        $qryRes_wh = mysql_query($qry_in); 
        while ($row = mysql_fetch_assoc($qryRes_wh)) {
            $tr_date = date('Y-m-d',strtotime($row['TranDate']));
            $dates_arr[$tr_date]=$tr_date;
            @$stock_in[$tr_date] += $row['Qty']; 
            
           
        }
        $qry_out = "SELECT
tbl_stock_master.TranDate,
tbl_stock_master.WHIDFrom,
tbl_stock_master.WHIDTo,
tbl_stock_detail.Qty,
tbl_stock_detail.adjustmentType,
sysuser_tab.usrlogin_id,
sysuser_tab.UserID,
tbl_trans_type.trans_type,
tbl_trans_type.trans_nature,
tbl_trans_type.is_adjustment
FROM
tbl_stock_master
INNER JOIN tbl_stock_detail ON tbl_stock_master.PkStockID = tbl_stock_detail.fkStockID
INNER JOIN stock_batch ON tbl_stock_detail.BatchID = stock_batch.batch_id
INNER JOIN sysuser_tab ON tbl_stock_master.CreatedBy = sysuser_tab.UserID
INNER JOIN tbl_trans_type ON tbl_stock_detail.adjustmentType = tbl_trans_type.trans_id
WHERE
sysuser_tab.sysusr_type = 38 AND
sysuser_tab.UserID = 10044 AND
tbl_stock_master.WHIDFrom = 72656 AND
tbl_trans_type.trans_nature = '-'
ORDER BY tbl_stock_master.TranDate
";

//        print_r($qry_soh);
//        exit;
        $qryRes = mysql_query($qry_out);
        $num_rows = mysql_num_rows($qryRes);
        while ($row = mysql_fetch_assoc($qryRes)) {
            $tr_date = date('Y-m-d',strtotime($row['TranDate']));
            $dates_arr[$tr_date]=$tr_date;
            @$stock_out[$tr_date] += abs($row['Qty']); 
        }
        
        
        $soh_val = $last_val = 0;
        foreach($dates_arr as $k=>$date){
            
            $tr_date = date('Y-m-d',strtotime($date));
            @$soh_val += $stock_in[$tr_date];
            @$soh_val -= $stock_out[$tr_date];
            
            @$stock_on_hand[$tr_date] = $soh_val; 
            
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
<body class="page-header-fixed page-quick-sidebar-over-content">
    <div class="page-container">
        <?php
        include PUBLIC_PATH . "html/top.php";
        include PUBLIC_PATH . "html/top_im.php";
        ?>

        <div class="page-content-wrapper" >
            <div class="page-content">

                <div class="row" style="margin-left:0px;margin-right: 0px;" id="graph_row_1">
                     <div id="canvas-holder" style="width:90%">
                                    <canvas id="chart-area"></canvas>
                            </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php
    include PUBLIC_PATH . "/html/footer.php";
    ?>
    <script src="<?= PUBLIC_URL ?>js/bootstrap-multiselect.js"></script>

    <script language="Javascript" src="<?php echo PUBLIC_URL; ?>FusionCharts/Charts/FusionCharts.js"></script>
    <script language="Javascript" src="<?php echo PUBLIC_URL; ?>FusionCharts/themes/fusioncharts.theme.zune.js"></script>
    <script type="text/javascript" src="../../public/assets/global/plugins/select2/select2.min.js"></script>
    <script>
		var MONTHS = [
                    <?php
                        foreach($dates_arr as $k=>$date){
                            echo "'".$date."',";
                        }
                    ?>
                ];
		var config = {
			type: 'line',
			data: {
				labels: [
                                    
                                     <?php
                                            foreach($dates_arr as $k=>$date){
                                                echo "'".$date."',";
                                            }
                                        ?>
                                ],
				datasets: [{
					label: 'Inward Stock',
					backgroundColor: window.chartColors.red,
					borderColor: window.chartColors.red,
					data: [
                                           <?php
                                                foreach($dates_arr as $k=>$date){
                                                    echo "'".@$stock_in[$date]."',";
                                                }
                                            ?>
					],
					fill: false,
				yAxisID: 'y-axis-1',
				}, {
					label: 'Outward Stock',
					fill: false,
					backgroundColor: window.chartColors.blue,
					borderColor: window.chartColors.blue,
					data: [
                                           <?php
                                                foreach($dates_arr as $k=>$date){
                                                    echo "'".@$stock_out[$date]."',";
                                                }
                                            ?>
					],
				yAxisID: 'y-axis-2',
				}, {
					label: 'Stock On Hand',
					fill: false,
					backgroundColor: window.chartColors.yellow,
					borderColor: window.chartColors.yellow,
					data: [
                                           <?php
                                                foreach($dates_arr as $k=>$date){
                                                    echo "'".@$stock_on_hand[$date]."',";
                                                }
                                            ?>
					],
				yAxisID: 'y-axis-3',
				}]
			},
			options: {
				responsive: true,
				title: {
					display: true,
					text: 'Transactional Activity'
				},
				tooltips: {
					mode: 'index',
					intersect: false,
				},
				hover: {
					mode: 'nearest',
					intersect: true
				},
				scales: {
					xAxes: [{
						display: true,
						scaleLabel: {
							display: true,
							labelString: 'Date'
						}
					}],
//					yAxes: [{
//						display: true,
//                                                //type: 'logarithmic',
//						scaleLabel: {
//							display: true,
//							labelString: 'Unit Items'
//						}
//					}]

						yAxes: [{
							type: 'linear', // only linear but allow scale type registration. This allows extensions to exist solely for log scale for instance
							display: true,
							position: 'left',
							id: 'y-axis-1',
						},{
							type: 'linear', // only linear but allow scale type registration. This allows extensions to exist solely for log scale for instance
							display: true,
							position: 'left',
							id: 'y-axis-2',
						}, {
							type: 'linear', // only linear but allow scale type registration. This allows extensions to exist solely for log scale for instance
							display: true,
							position: 'right',
							id: 'y-axis-3',

							// grid line settings
							gridLines: {
								drawOnChartArea: false, // only want the grid lines for one axis to show up
							},
						}]
				}
			}
		};

		window.onload = function() {
			var ctx = document.getElementById('chart-area').getContext('2d');
			window.myLine = new Chart(ctx, config);
		};
	</script>
     
        </body>
</html>
