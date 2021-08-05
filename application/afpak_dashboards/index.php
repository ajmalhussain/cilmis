<?php
ini_set('max_execution_time', 0);

//Including files
include("../includes/classes/Configuration.inc.php");
include(APP_PATH . "includes/classes/db.php");
include(PUBLIC_PATH . "html/header.php");
include(PUBLIC_PATH . "/FusionCharts/Code/PHP/includes/FusionCharts.php");
include("../includes/classes/AllClasses.php");

$caption = 'what';
$downloadFileName = $caption . ' - ' . date('Y-m-d H:i:s');
//chart_id
$chart_id = 'b2';

//include AllClasses
//report id
$rptId = 'afpak_1';
//user province id
//$userProvId = $_SESSION['user_province1'];
//if submitted
if (isset($_POST['submit'])) {
    //get from date
    $selYear  = isset($_POST['year_sel']) ? mysql_real_escape_string($_POST['year_sel']) : '';
    $selMonth = isset($_POST['month_sel']) ? mysql_real_escape_string($_POST['month_sel']) : '';
    
    
    $fromDate = $selYear.'-'.$selMonth;
    $date_last_12_mon = ($selYear-1).'-'.$selMonth;
    //get to date
//    $toDate = isset($_POST['to_datse']) ? mysql_real_escape_string($_POST['to_date']) : '';
    //get selected province
    $selProv = mysql_real_escape_string($_POST['prov_sel']);

    //get district id
    $districtId = mysql_real_escape_string($_POST['district']);
//    echo $districtId;
//select query
    // Get district name
    $qry = "SELECT
                tbl_locations.LocName
            FROM
                tbl_locations
            WHERE
                tbl_locations.PkLocID = $districtId";
    //query result
    $row = mysql_fetch_array(mysql_query($qry));
    //district name
    $distrctName = $row['LocName'];
    //file name
}
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

                <div class="row">
                    <div class="col-md-12" >
                        <center> <h3 class="page-title row-br-b-wp">District Supply Chain Dashboard - <strong color="green">AFPAK</strong></h3></center>
                        <div class="widget" data-toggle="collapse-widget">
                            <div class="widget-head">
                                <h3 class="heading">Filter by</h3>
                            </div>
                            <div class="widget-body">
                                <?php
//sub_dist_form
                                include('afpak_filters.php');
                                ?>
                            </div>
                        </div>

                    </div>
                </div>

                <script language="Javascript" src="<?php echo PUBLIC_URL; ?>FusionCharts/Charts/FusionCharts.js"></script>
                <script language="Javascript" src="<?php echo PUBLIC_URL; ?>FusionCharts/themes/fusioncharts.theme.fint.js"></script>

                <div class="page-container">


                    <?php
                    $qry = "SELECT
                                    itm_id,reporting_date,warehouse_id,wh_id,
                                    SUM(opening_balance) as total,itm_name
                            FROM
                                    tbl_hf_data


                            INNER JOIN itminfo_tab ON tbl_hf_data.item_id = itminfo_tab.itm_id
                            INNER JOIN tbl_warehouse ON tbl_hf_data.warehouse_id = tbl_warehouse.wh_id
                            INNER JOIN tbl_hf_type_rank ON tbl_warehouse.hf_type_id = tbl_hf_type_rank.hf_type_id
                            WHERE
                            DATE_FORMAT(tbl_hf_data.reporting_date, '%Y-%m') BETWEEN '$date_last_12_mon' AND '$fromDate'  
                             AND tbl_warehouse.dist_id = $districtId
                            AND tbl_hf_type_rank.province_id = $selProv
                             AND itm_id NOT IN (1,31,32)
                        GROUP BY
                        reporting_date,itm_id
                        ORDER BY 
                        reporting_date,itm_id";
//                    echo $qry;exit;
                    $qryRes = mysql_query($qry);
                    $monthly_data_arr = $items_arr =  array();
                    while ($row = mysql_fetch_assoc($qryRes)) {
                        
                        $monthly_data_arr[$row['reporting_date']][$row['itm_id']] = $row['total'];
                        
                        if(!empty($row['total']) && $row['total']>0)
                        $items_arr[$row['itm_id']] = $row['itm_name'];
                    }
                    ?>

                    <div class="row">
                        <div class="col-md-6" >
                            <table>
                                <div class="widget" data-toggle="collapse-widget">
                                    <div class="widget-head">
                                        <h3 class="heading" >Stock On Hand - Monthly Trend</h3>
                                    </div>
                                    <div class="widget widget-tabs">    
                                        <div class="widget-body">
                                            <a href="javascript:exportChart('<?php echo $chart_id; ?>', '<?php echo $downloadFileName; ?>')" style="float:right;"><img class="export_excel" src="<?php echo PUBLIC_URL; ?>images/excel-16.png" alt="Export" /></a>
                                            <?php
                                            $xmlstore = '<chart caption="" showValues="0" staggerLines="3" slantLabels="1" yaxismaxvalue="100"  subcaption="" xaxisname="Months" exportEnabled="1"  yaxisname="Stock"     numberprefix="" theme="fint">';
                                            
                                            
                                            $xmlstore .= '  <categories>';
                                            foreach ($monthly_data_arr as $month => $m_data) {
                                                $xmlstore .= '  <category label="' . date('M-y',strtotime($month)) . '" />';
                                            }
                                            $xmlstore .= '  </categories>';
                                            
                                            
                                            foreach ($items_arr as $itm_id => $itm_name) {
                                                $xmlstore .= '  <dataset seriesname="' . $itm_name . '">';
                                                foreach ($monthly_data_arr as $month => $m_data) {
                                                    $vv = 0;
                                                    if (!empty($m_data[$itm_id]))
                                                        $vv = $m_data[$itm_id];
                                                    $xmlstore .= '  <set value="' . $vv . '" />';
                                                }
                                                $xmlstore .= '  </dataset>';
                                            }
                                            
                                           
                                            $xmlstore .= ' </chart>';

                                            FC_SetRenderer('javascript');
                                            echo renderChart(PUBLIC_URL . "FusionCharts/Charts/MSSpline.swf", "", $xmlstore, '3', '100%', 300, false, false);
                                            ?>
                                        </div>
                                    </div>
                                </div>

                            </table>

                        </div> 
                    <?php
                    $qry = "SELECT
                                    itm_id,reporting_date,warehouse_id,wh_id,
                                    SUM(opening_balance) as total,itm_name
                            FROM
                                    tbl_hf_data
                            INNER JOIN itminfo_tab ON tbl_hf_data.item_id = itminfo_tab.itm_id
                            INNER JOIN tbl_warehouse ON tbl_hf_data.warehouse_id = tbl_warehouse.wh_id
                            INNER JOIN tbl_hf_type_rank ON tbl_warehouse.hf_type_id = tbl_hf_type_rank.hf_type_id
                            WHERE
                            DATE_FORMAT(tbl_hf_data.reporting_date, '%Y-%m') = '$fromDate'  
                             AND tbl_warehouse.dist_id = $districtId
                            AND tbl_hf_type_rank.province_id = $selProv
                             AND itm_id NOT IN (1,31,32)
                            GROUP BY itm_id";
                    $qryRes = mysql_query($qry);
                    $main_data_arr = array();
                    while ($row = mysql_fetch_assoc($qryRes)) {
                        $xmlstore .= '     <set label="' . $row['itm_name'] . '" value="' . $row['total'] . '"/>';
                        $main_data_arr[] = $row;
                    }
                    ?>

                        <div class="col-md-6" >
                            <table>
                                <div class="widget" data-toggle="collapse-widget">
                                    <div class="widget-head">
                                        <h3 class="heading">Product Wise Capacity Occupied</h3>
                                    </div>
                                    <?php
 
                                    ?>

                                    <div class="widget widget-tabs">    
                                        <div class="widget-body">
                                            <a href="javascript:exportChart('<?php echo $chart_id; ?>', '<?php echo $downloadFileName; ?>')" style="float:right;"><img class="export_excel" src="<?php echo PUBLIC_URL; ?>images/excel-16.png" alt="Export" /></a>
                                            <?php
                                            $xmlstore = '<chart caption="" yaxismaxvalue="100"  subcaption="" xaxisname="Products" exportEnabled="1"  yaxisname="Percentage"  palettecolors=""  numberprefix="" theme="fint">';
                                            foreach($main_data_arr as $k=>$row) {
                                                if(!empty($row['total']) && $row['total']>0)
                                                $xmlstore .= '     <set label="' . $row['itm_name'] . '" value="' . $row['total'] . '"/>';
                                            }
                                            $xmlstore .= ' </chart>';

                                            FC_SetRenderer('javascript');
                                            echo renderChart(PUBLIC_URL . "FusionCharts/Charts/Doughnut2D.swf", "", $xmlstore, '5', '100%', 300, false, false);
                                            ?>
                                        </div>
                                    </div>
                                </div>

                            </table>
                        </div>
                    </div>
                    <?php
                     
                    ?>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="widget" data-toggle="collapse-widget">
                                <div class="widget-head">
                                    <h3 class="heading">Products Wise Stock On Hand Status</h3>
                                </div>
                                <div class="widget widget-tabs">    
                                    <div class="widget-body">
                                        <a href="javascript:exportChart('<?php echo $chart_id; ?>', '<?php echo $downloadFileName; ?>')" style="float:right;"><img class="export_excel" src="<?php echo PUBLIC_URL; ?>images/excel-16.png" alt="Export" /></a>
                                            <?php
                                            $xmlstore = '<chart caption="" yaxismaxvalue="100"  subcaption="" xaxisname="Products" exportEnabled="1"  yaxisname="Percentage"  palettecolors=""  numberprefix="" theme="fint">';
                                            foreach($main_data_arr as $k=>$row) {
                                                if(!empty($row['total']) && $row['total']>0)
                                                $xmlstore .= '     <set label="' . $row['itm_name'] . '" value="' . $row['total'] . '"/>';
                                            }
//$xmlstore .= '  </dataset>';
                                            $xmlstore .= ' </chart>';

                                            FC_SetRenderer('javascript');
                                            echo renderChart(PUBLIC_URL . "FusionCharts/Charts/Column2D.swf", "", $xmlstore, '2', '100%', 300, false, false);
                                            ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
//Including footer file
    include PUBLIC_PATH . "/html/footer.php";
    include ('combos.php');
    ?>
</body>



