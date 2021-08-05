<?php
echo 'Add where clauses in query before running this file';
exit;
ini_set('max_execution_time', 0);

//Including files
include("../includes/classes/Configuration.inc.php");
include(APP_PATH . "includes/classes/db.php");
include(PUBLIC_PATH . "html/header.php");
include(PUBLIC_PATH . "/FusionCharts/Code/PHP/includes/FusionCharts.php");

$caption = 'what';
$downloadFileName = $caption . ' - ' . date('Y-m-d H:i:s');
//chart_id
$chart_id = 'b2';

$qry = "SELECT
	itm_id,
	SUM(opening_balance) as total,itm_name
FROM
	tbl_hf_data
INNER JOIN itminfo_tab ON tbl_hf_data.item_id = itminfo_tab.itm_id
INNER JOIN tbl_warehouse ON tbl_hf_data.warehouse_id = tbl_warehouse.wh_id
where tbl_warehouse.dist_id = 14
and tbl_warehouse.stkid = 7
GROUP BY itm_id";
$qryRes = mysql_query($qry);
?>
<body class="page-header-fixed page-quick-sidebar-over-content">
    <div class="page-container">
        <?php
//Including top file
        include PUBLIC_PATH . "html/top.php";
//Including top_im file
        include PUBLIC_PATH . "html/top_im.php";
        ?>

        <div class="page-content-wrapper">
            <div class="page-content">
                <script language="Javascript" src="<?php echo PUBLIC_URL; ?>FusionCharts/Charts/FusionCharts.js"></script>
                <script language="Javascript" src="<?php echo PUBLIC_URL; ?>FusionCharts/themes/fusioncharts.theme.fint.js"></script>

                <div class="page-container">

                    <div class="widget widget-tabs">    
                        <div class="widget-body">
                            <a href="javascript:exportChart('<?php echo $chart_id; ?>', '<?php echo $downloadFileName; ?>')" style="float:right;"><img class="export_excel" src="<?php echo PUBLIC_URL; ?>images/excel-16.png" alt="Export" /></a>
                            <?php
                            $xmlstore = '<chart caption="Bar Chart" yaxismaxvalue="100" subcaption="" xaxisname="Products" exportEnabled="1"  yaxisname="Percentage"  palettecolors="#26C281"  numberprefix="" theme="fint">';
//$xmlstore .= ' <dataset>';

                            while ($row = mysql_fetch_assoc($qryRes)) {
//                                $perc = 0;

                                $xmlstore .= '     <set label="' . $row['itm_name'] . '"     value="' . $row['total'] . '"/>';
                            }

//$xmlstore .= '  </dataset>';
                            $xmlstore .= ' </chart>';

                            FC_SetRenderer('javascript');
                            echo renderChart(PUBLIC_URL . "FusionCharts/Charts/Bar2D.swf", "", $xmlstore, '1', '100%', 300, false, false);
                            ?>
                        </div>
                    </div>
                    <?php
                    $qry = "SELECT
	itm_id,
	SUM(opening_balance) as total,itm_name
FROM
	tbl_hf_data
INNER JOIN itminfo_tab ON tbl_hf_data.item_id = itminfo_tab.itm_id
INNER JOIN tbl_warehouse ON tbl_hf_data.warehouse_id = tbl_warehouse.wh_id
where tbl_warehouse.dist_id = 14
and tbl_warehouse.stkid = 7
GROUP BY itm_id";
                    $qryRes = mysql_query($qry);
                    ?>

                    <div class="widget widget-tabs">    
                        <div class="widget-body">
                            <a href="javascript:exportChart('<?php echo $chart_id; ?>', '<?php echo $downloadFileName; ?>')" style="float:right;"><img class="export_excel" src="<?php echo PUBLIC_URL; ?>images/excel-16.png" alt="Export" /></a>
                            <?php
                            $xmlstore = '<chart caption="Column Chart" yaxismaxvalue="100"  subcaption="" xaxisname="Products" exportEnabled="1"  yaxisname="Percentage"  palettecolors="#26C281"  numberprefix="" theme="fint">';
//$xmlstore .= ' <dataset>';
                            while ($row = mysql_fetch_assoc($qryRes)) {
//                                $perc = 0;

                                $xmlstore .= '     <set label="' . $row['itm_name'] . '" value="' . $row['total'] . '"/>';
                            }
//$xmlstore .= '  </dataset>';
                            $xmlstore .= ' </chart>';

                            FC_SetRenderer('javascript');
                            echo renderChart(PUBLIC_URL . "FusionCharts/Charts/Column2D.swf", "", $xmlstore, '2', '100%', 300, false, false);
                            ?>
                        </div>
                    </div>



                    <?php
                    $qry = "SELECT
	itm_id,
	SUM(opening_balance) as total,itm_name
FROM
	tbl_hf_data
INNER JOIN itminfo_tab ON tbl_hf_data.item_id = itminfo_tab.itm_id
INNER JOIN tbl_warehouse ON tbl_hf_data.warehouse_id = tbl_warehouse.wh_id
where tbl_warehouse.dist_id = 14
and tbl_warehouse.stkid = 7
GROUP BY itm_id";
                    $qryRes = mysql_query($qry);
                    ?>


                    <div class="widget widget-tabs">    
                        <div class="widget-body">
                            <a href="javascript:exportChart('<?php echo $chart_id; ?>', '<?php echo $downloadFileName; ?>')" style="float:right;"><img class="export_excel" src="<?php echo PUBLIC_URL; ?>images/excel-16.png" alt="Export" /></a>
                            <?php
                            $xmlstore = '<chart caption="Line Chart" yaxismaxvalue="100"  subcaption="" xaxisname="Products" exportEnabled="1"  yaxisname="Percentage"  palettecolors="#26C281"  numberprefix="" theme="fint">';
//$xmlstore .= ' <dataset>';
                            while ($row = mysql_fetch_assoc($qryRes)) {
//                                $perc = 0;

                                $xmlstore .= '     <set label="' . $row['itm_name'] . '" value="' . $row['total'] . '"/>';
                            }
//$xmlstore .= '  </dataset>';
//$xmlstore .= '  </dataset>';
                            $xmlstore .= ' </chart>';

                            FC_SetRenderer('javascript');
                            echo renderChart(PUBLIC_URL . "FusionCharts/Charts/Line.swf", "", $xmlstore, '3', '100%', 300, false, false);
                            ?>
                        </div>
                    </div>





                    <?php
                    $qry = "SELECT
	itm_id,
	SUM(opening_balance) as total,itm_name
FROM
	tbl_hf_data
INNER JOIN itminfo_tab ON tbl_hf_data.item_id = itminfo_tab.itm_id
INNER JOIN tbl_warehouse ON tbl_hf_data.warehouse_id = tbl_warehouse.wh_id
where tbl_warehouse.dist_id = 14
and tbl_warehouse.stkid = 7
GROUP BY itm_id";
                    $qryRes = mysql_query($qry);
                    ?>




                    <div class="widget widget-tabs">    
                        <div class="widget-body">
                            <a href="javascript:exportChart('<?php echo $chart_id; ?>', '<?php echo $downloadFileName; ?>')" style="float:right;"><img class="export_excel" src="<?php echo PUBLIC_URL; ?>images/excel-16.png" alt="Export" /></a>
                            <?php
                            $xmlstore = '<chart caption="Pie Chart" yaxismaxvalue="100"  subcaption="" xaxisname="Products" exportEnabled="1"  yaxisname="Percentage"  palettecolors="#26C281"  numberprefix="" theme="fint">';
//$xmlstore .= ' <dataset>';
                            while ($row = mysql_fetch_assoc($qryRes)) {
//                                $perc = 0;

                                $xmlstore .= '     <set label="' . $row['itm_name'] . '" value="' . $row['total'] . '"/>';
                            }
//$xmlstore .= '  </dataset>';
//$xmlstore .= '  </dataset>';
                            $xmlstore .= ' </chart>';

                            FC_SetRenderer('javascript');
                            echo renderChart(PUBLIC_URL . "FusionCharts/Charts/Pie2D.swf", "", $xmlstore, '4', '100%', 300, false, false);
                            ?>
                        </div>
                    </div>



                    <?php
                    $qry = "SELECT
	itm_id,
	SUM(opening_balance) as total,itm_name
FROM
	tbl_hf_data
INNER JOIN itminfo_tab ON tbl_hf_data.item_id = itminfo_tab.itm_id
INNER JOIN tbl_warehouse ON tbl_hf_data.warehouse_id = tbl_warehouse.wh_id
where tbl_warehouse.dist_id = 14
and tbl_warehouse.stkid = 7
GROUP BY itm_id";
                    $qryRes = mysql_query($qry);
                    ?>



                    <div class="widget widget-tabs">    
                        <div class="widget-body">
                            <a href="javascript:exportChart('<?php echo $chart_id; ?>', '<?php echo $downloadFileName; ?>')" style="float:right;"><img class="export_excel" src="<?php echo PUBLIC_URL; ?>images/excel-16.png" alt="Export" /></a>
                            <?php
                            $xmlstore = '<chart caption="Doughnut Chart" yaxismaxvalue="100"  subcaption="" xaxisname="Products" exportEnabled="1"  yaxisname="Percentage"  palettecolors="#26C281"  numberprefix="" theme="fint">';
//$xmlstore .= ' <dataset>';
                            while ($row = mysql_fetch_assoc($qryRes)) {
//                                $perc = 0;

                                $xmlstore .= '     <set label="' . $row['itm_name'] . '" value="' . $row['total'] . '"/>';
                            }
//$xmlstore .= '  </dataset>';
//$xmlstore .= '  </dataset>';
                            $xmlstore .= ' </chart>';

                            FC_SetRenderer('javascript');
                            echo renderChart(PUBLIC_URL . "FusionCharts/Charts/Doughnut2D.swf", "", $xmlstore, '5', '100%', 300, false, false);
                            ?>
                        </div>
                    </div>

                   <?php
                    $info = array();
                    $info1 = [];
                    $qry1 = "SELECT
	itm_id,
	opening_balance,itm_name,item_id,reporting_date
FROM
	tbl_hf_data
INNER JOIN itminfo_tab ON tbl_hf_data.item_id = itminfo_tab.itm_id
INNER JOIN tbl_warehouse ON tbl_hf_data.warehouse_id = tbl_warehouse.wh_id
where tbl_warehouse.dist_id = 14
and tbl_warehouse.stkid = 7
ORDER BY tbl_hf_data.item_id ASC";
                    $qryRes = mysql_query($qry1);
                    while ($row = mysql_fetch_assoc($qryRes)) {
                        $info[$row['itm_id']] = $row['itm_name'];

                        $info1[$row['reporting_date']][$row['item_id']] = $row['opening_balance'];
                    }
                    //echo '<pre>';print_r($info1);exit;
                    ?>


                    <div class="widget widget-tabs">    
                        <div class="widget-body">
                            <a href="javascript:exportChart('<?php echo $chart_id; ?>', '<?php echo $downloadFileName; ?>')" style="float:right;"><img class="export_excel" src="<?php echo PUBLIC_URL; ?>images/excel-16.png" alt="Export" /></a>
                            <?php
                            $xmlstore = '<chart caption="StackedColumn2D Chart" yaxismaxvalue="100"  subcaption="" xaxisname="Products" exportEnabled="1"  yaxisname="Percentage"   numberprefix="" theme="fusion">';

                            $xmlstore .= '  <categories>';
                            foreach ($info as $id => $name) {
                                $xmlstore .= '  <category label="' . $name . '" />';
                            }
                            $xmlstore .= ' </categories>';
                            
                            
                             foreach ($info1 as $date => $arr) {
                                $xmlstore .= '  <dataset seriesname="' . $date . '">';
                                foreach ($arr as $id1 => $openingbalance) {
                                    $xmlstore .= '  <set value="' . $openingbalance . '" />';
                                }
                                $xmlstore .= '  </dataset>';
                            }
                            
                            

                            $xmlstore .= ' </chart>';

                            FC_SetRenderer('javascript');
                            echo renderChart(PUBLIC_URL . "FusionCharts/Charts/StackedColumn2D.swf", "", $xmlstore, '6', '100%', 300, false, false);
                            ?>
                        </div>
                    </div>

                    <?php
                    $info = array();
                    $info1 = [];
                    $qry1 = "SELECT
	itm_id,
	opening_balance,itm_name,item_id,reporting_date
FROM
	tbl_hf_data
INNER JOIN itminfo_tab ON tbl_hf_data.item_id = itminfo_tab.itm_id
INNER JOIN tbl_warehouse ON tbl_hf_data.warehouse_id = tbl_warehouse.wh_id
where tbl_warehouse.dist_id = 14
and tbl_warehouse.stkid = 7
ORDER BY tbl_hf_data.item_id ASC";
                    $qryRes = mysql_query($qry1);
                    while ($row = mysql_fetch_assoc($qryRes)) {
                        $info[$row['itm_id']] = $row['itm_name'];

                        $info1[$row['reporting_date']][$row['item_id']] = $row['opening_balance'];
                    }
                    //echo '<pre>';print_r($info1);exit;
                    ?>



                    <div class="widget widget-tabs">    
                        <div class="widget-body">
                            <a href="javascript:exportChart('<?php echo $chart_id; ?>', '<?php echo $downloadFileName; ?>')" style="float:right;"><img class="export_excel" src="<?php echo PUBLIC_URL; ?>images/excel-16.png" alt="Export" /></a>
                            <?php
                            $xmlstore = '<chart caption="MSBar2D Chart" yaxismaxvalue="100"  subcaption="" xaxisname="Products" exportEnabled="1"  yaxisname="Percentage"      numberprefix="" theme="fint">';

                            $xmlstore .= '  <categories>';
                            foreach ($info as $id => $name) {
                                $xmlstore .= '  <category label="' . $name . '" />';
                            }
                            $xmlstore .= '  </categories>';

                            foreach ($info1 as $date => $arr) {
                                $xmlstore .= '  <dataset seriesname="' . $date . '">';
                                foreach ($arr as $id1 => $openingbalance) {
                                    $xmlstore .= '  <set value="' . $openingbalance . '" />';
                                }
                                $xmlstore .= '  </dataset>';
                            }

                            $xmlstore .= '  <trendlines>';
                            $xmlstore .= '<line startvalue="12250" color="#5D62B5" displayvalue="Previous{br}Average" valueonright="1" thickness="1" showbelow="1" tooltext="Previous year quarterly target  : $13.5K" />';
                            $xmlstore .= '<line startvalue="25950" color="#29C3BE" displayvalue="Current{br}Average" valueonright="1" thickness="1" showbelow="1" tooltext="Current year quarterly target  : $23K" />';
                            $xmlstore .= '     </trendlines>';

                            $xmlstore .= ' </chart>';

                            FC_SetRenderer('javascript');
                            echo renderChart(PUBLIC_URL . "FusionCharts/Charts/MSColumn2D.swf", "", $xmlstore, '7', '100%', 300, false, false);
                            ?>
                        </div>
                    </div>



                    <?php
                    $qry = "SELECT
	itm_id,
	SUM(opening_balance) as total,itm_name
FROM
	tbl_hf_data
INNER JOIN itminfo_tab ON tbl_hf_data.item_id = itminfo_tab.itm_id
INNER JOIN tbl_warehouse ON tbl_hf_data.warehouse_id = tbl_warehouse.wh_id
where tbl_warehouse.dist_id = 14
and tbl_warehouse.stkid = 7
GROUP BY itm_id";
                    $qryRes = mysql_query($qry);
                    ?>




                    <div class="widget widget-tabs">    
                        <div class="widget-body">
                            <a href="javascript:exportChart('<?php echo $chart_id; ?>', '<?php echo $downloadFileName; ?>')" style="float:right;"><img class="export_excel" src="<?php echo PUBLIC_URL; ?>images/excel-16.png" alt="Export" /></a>
                                <?php
                                $xmlstore = '<chart caption="Area Chart" yaxismaxvalue="100"  subcaption="" xaxisname="Products" exportEnabled="1"  yaxisname="Percentage"  palettecolors="#90EE90"  numberprefix="" theme="fint">';
//$xmlstore .= ' <dataset>';
                                while ($row = mysql_fetch_assoc($qryRes)) {
//                                $perc = 0;

                                    $xmlstore .= '     <set label="' . $row['itm_name'] . '" value="' . $row['total'] . '"/>';
                                }
//$xmlstore .= '  </dataset>';
                                $xmlstore .= ' </chart>';

                                FC_SetRenderer('javascript');
                                echo renderChart(PUBLIC_URL . "FusionCharts/Charts/Area2D.swf", "", $xmlstore, '8', '100%', 300, false, false);
                                ?>
                        </div>
                    </div>




                </div>
            </div>
        </div>
    </div>
    <?php
//Including footer file
    include PUBLIC_PATH . "/html/footer.php";
    ?>
</body>



