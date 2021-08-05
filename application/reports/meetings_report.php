<?php
ini_set('max_execution_time', 60);
include("../includes/classes/Configuration.inc.php");

if (isset($_REQUEST['submit'])) {
    //echo '<pre>';print_r($_REQUEST);exit;
}
include(APP_PATH . "includes/classes/db.php");
include APP_PATH . "includes/classes/functions.php";
include(PUBLIC_PATH . "FusionCharts/Code/PHP/includes/FusionCharts.php");
include(PUBLIC_PATH . "html/header.php");

$report_id = "CD";
$rep_level = '';
$chart_id = 'compliance_sdp';
$selDist = '';
$selPro = $selStk = '1';

$months_list = array();
for ($y = 2020; $y <= date('Y'); $y++) {
    for ($m = 1; $m <= 12; $m++) {

        if ($y == date('Y') && $m >= date('m')) {
            continue;
        }
        if ($m < 10) {
            $m = '0' . $m;
        }
        $mo = $y . '-' . $m . '-01';
        $months_list[$mo] = date('M-Y', strtotime($mo));
    }
}

$inp_status = '';
$inp_type = '';


$wh = '';
if (!empty($_REQUEST['submit'])) {
//   echo '<pre>';print_r($_REQUEST);exit;
    if (!empty($_REQUEST['status'])) {
        $wh .= " and `status`='" . $_REQUEST['status'] . "' ";
        $inp_status = $_REQUEST['status'];
    }
    if (!empty($_REQUEST['type'])) {
        $wh .= " and `title`='" . $_REQUEST['type'] . "' ";
        $inp_type = $_REQUEST['type'];
    }
}

//echo '<pre>';print_r($months_list);exit;
?>
</head>
<body class="page-header-fixed page-quick-sidebar-over-content" onLoad="">

    <SCRIPT LANGUAGE="Javascript" SRC="<?php echo PUBLIC_URL; ?>FusionCharts/Charts/FusionCharts.js"></SCRIPT>
    <SCRIPT LANGUAGE="Javascript" SRC="<?php echo PUBLIC_URL; ?>FusionCharts/themes/fusioncharts.theme.fint.js"></SCRIPT>
    <div class="page-container">
        <?php
        include PUBLIC_PATH . "html/top.php";
        include PUBLIC_PATH . "html/top_im.php";
        ?>
        <div class="page-content-wrapper">
            <div class="page-content">
                <div class="row">
                    <div class="col-md-12">
                        <h3 class="page-title row-br-b-wp">Meetings Held</h3>
                        <div class="widget" data-toggle="collapse-widget">
                            <div class="widget-head">
                                <h3 class="heading">Filter by</h3>
                            </div>
                            <div class="widget-body">
                                <form name="frm" id="frm" action="" method="get">
                                    <div class="row">
                                        <div class="col-md-12">

                                            <div class="col-md-4">
                                                <div class="control-group">
                                                    <label>Meeting Status</label>
                                                    <div class="controls">
                                                        <select id="status" name="status" class="form-control input-medium">
                                                            <option value="">All</option>
                                                            <option value="1" <?= (($inp_status == '1') ? ' selected ' : '') ?> >Planned</option>
                                                            <option value="2" <?= (($inp_status == '2') ? ' selected ' : '') ?> >Held</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="control-group">
                                                    <label>Meeting Type</label>
                                                    <div class="controls">
                                                        <select id="type" name="type" class="form-control input-medium">
                                                            <option value="">All</option>
                                                            <option value="DCC Meeting" <?= (($inp_type == 'DCC Meeting') ? ' selected ' : '') ?> >DCC Meeting</option>
                                                            <option value="DTC Meeting" <?= (($inp_type == 'DTC Meeting') ? ' selected ' : '') ?> >DTC Meeting</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-2">
                                                <div class="control-group">
                                                    <label>&nbsp;</label>
                                                    <div class="controls">
                                                        <input type="submit" name="submit" id="go" value="GO" class="btn btn-primary input-sm" />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <?php
                include 'sub_export_options.php';
                $q_r = "SELECT
                        tbl_locations.PkLocID,
                        tbl_locations.LocName,
                        tbl_locations.LocLvl
                        FROM
                        tbl_locations
                        WHERE
                        tbl_locations.ParentID = 1 AND
                        tbl_locations.LocLvl = 3
                        ORDER BY
                        tbl_locations.LocName ASC
                    ";
//                echo $q_r;exit;
                $res_r = mysql_query($q_r);
                $dist_arr = array();
                $loc_count = 0;
                while ($row = mysql_fetch_assoc($res_r)) {
                    $dist_arr[$row['PkLocID']] = $row['LocName'];
                    $loc_count++;
                }

                $q_r = "SELECT
                        `events`.id,
                        `events`.title,
                        `events`.`start`,
                        `events`.user_id,
                        `events`.`status`,
                        sysuser_tab.usrlogin_id,
                        tbl_warehouse.wh_name,
                        stakeholder.lvl,
                        stakeholder.stkname,
                        tbl_warehouse.dist_id,
                        `events`.`end`
                        FROM
                        `events`
                        INNER JOIN sysuser_tab ON `events`.user_id = sysuser_tab.UserID
                        INNER JOIN wh_user ON sysuser_tab.UserID = wh_user.sysusrrec_id
                        INNER JOIN tbl_warehouse ON wh_user.wh_id = tbl_warehouse.wh_id
                        INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
                        WHERE
                        wh_user.is_default = 1 AND
                        stakeholder.lvl = 3
                    ";
                $q_r .= $wh;
                //echo $q_r;
                $res_r = mysql_query($q_r);
                $data_arr = array();
                while ($row = mysql_fetch_assoc($res_r)) {
                    $mon = date('Y-m-01', strtotime($row['start']));
                    $data_arr[$row['dist_id']][$mon] = $row;
                    $uid = $row['user_id'];
                }
//       echo '<pre>';print_r($data_arr);exit;
                ?>
                <div class="row">
                    <div class="col-md-12">

                        <div>

                            <table width="100%" cellpadding="0" cellspacing="0" id="compliance_HF" class="table table-bordered table-condensed">
                                <tr class=" bg-blue-madison">
                                    <td colspan="">#</td>
                                    <td colspan="">District</td>
                                    <?php
                                    foreach ($months_list as $k => $month) {
                                        echo '<td align="center">' . $month . '</td>';
                                    }
                                    ?>
                                    <td colspan="">Meeting_Details</td>
                                </tr>
                                <?php
                                $c = 1;
                                $rep_p = array();
                                foreach ($dist_arr as $loc_id => $loc_name) {

                                    echo ' <tr>
                                            <td>' . $c++ . '</td>
                                            <td>' . $loc_name . '</td>';
                                    foreach ($months_list as $k => $month) {

                                        if (!empty($data_arr[$loc_id][$k])) {
                                            $display = '<td class="bold" style="color:#008000" align="center">';
                                            $display .= 'Yes';
                                            $display .= '</td>';
                                            @$rep_p[$k] ++;
                                        } else {

                                            $display = '<td class="bold" style="color:#FF0000" align="center">';
                                            $display .= 'No';
                                            $display .= '</td>';
                                            @$total_active[$month] ++;
                                        }

                                        echo $display;
                                    }
                                    ?>
                                    <td><a href="<?php echo SITE_URL; ?>events/index_1.php?dist_id=<?php echo $loc_id; ?>&dist_name=<?php echo $loc_name; ?>">Download Details</a></td>
                                    <?php
                                    echo ' </tr>';
                                }
                                echo '<tr class=" bg-blue-madison">';
                                echo '<td colspan="2">Meetings Held</td>';
                                foreach ($months_list as $k => $month) {
                                    echo '<td align="center">' . @$rep_p[$k] . '</td>';
                                }
                                echo '</tr>';
                                ?>
                            </table>

                        </div>

                    </div>
                </div>

                <div class="widget widget-tabs">    
                    <div class="widget-body">
                        <?php
//xml for chart
                        $chart_data = '<chart caption="Meetings Held " exportenabled="0"  subcaption="" captionfontsize="14" subcaptionfontsize="14" basefontcolor="#333333" basefont="Helvetica Neue,Arial" subcaptionfontbold="0" xaxisname="Months" yaxisname="Percentage" showvalues="1" palettecolors="#0075c2,#1aaf5d,#AF1AA5,#AF711A,#D93636" bgcolor="#ffffff" showborder="0" showshadow="0" showalternatehgridcolor="0" showcanvasborder="0" showxaxisline="1" xaxislinethickness="1" xaxislinecolor="#999999" canvasbgcolor="#ffffff" legendborderalpha="0" legendshadow="0" divlinealpha="100" divlinecolor="#999999" divlinethickness="1" divlinedashed="1" divlinedashlen="1" >';

                        $chart_data .= ' <categories>';
                        foreach ($months_list as $k => $month) {
                            $chart_data .= ' <category label="' . $month . '" />';
                        }
                        $chart_data .= ' </categories>';

                        $chart_data .= ' <dataset seriesname="%">';
                        foreach ($months_list as $k => $month) {
                            $val = (!empty($rep_p[$k]) ? 100 * $rep_p[$k] / $loc_count : '0');
                            $chart_data .= '    <set  value="' . number_format($val, 2) . '"  />';
                        }
                        $chart_data .= '  </dataset>';

                        $chart_data .= ' </chart>';
                        FC_SetRenderer('javascript');
                        echo renderChart(PUBLIC_URL . "FusionCharts/Charts/MSSpline.swf", "", $chart_data, $chart_id, '100%', 300, false, false);
//}//end of submit
                        ?>
                    </div>
                </div>     
            </div>
        </div>
    </div>
    <?php
    include PUBLIC_PATH . "/html/footer.php";
    include PUBLIC_PATH . "/html/reports_includes.php";
    ?>

</body>
</html>