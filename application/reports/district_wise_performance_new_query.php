<?php
set_time_limit(0);
/**
 * spr3
 * @package reports
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
//include AllClasses
include("../includes/classes/AllClasses.php");
//include header
include(PUBLIC_PATH . "html/header.php");
//report id
$rptId = 'spr3';
//distrcit id
$districtId = '';
//stakeholder 
$stakeholder = 1;
//selected year
$selYear = '';
////selected province
$selProv = '';
//check if submitted
if (isset($_POST['submit'])) {
    //selected province
    $selProv = mysql_real_escape_string($_POST['prov_sel']);
    //from date
    $fromDate = isset($_POST['from_date']) ? mysql_real_escape_string($_POST['from_date']) : '';
    //to date
    $toDate = isset($_POST['to_date']) ? mysql_real_escape_string($_POST['to_date']) : '';
    //start date
    $startDate = $fromDate . '-01';
    //end date
    $endDate = date("Y-m-t", strtotime($toDate));
    //select query
    // Get Province name
    $qry = "SELECT
                tbl_locations.LocName
            FROM
                tbl_locations
            WHERE
                tbl_locations.PkLocID = $selProv";
    //fetch result
    $row = mysql_fetch_array(mysql_query($qry));
    //province name
    $provinceName = $row['LocName'];
    //file name
    $fileName = 'SPR3_' . $provinceName . '_from_' . date('M-Y', strtotime($startDate)) . '_to_' . date('M-Y', strtotime($endDate));
}
?>
</head>
<!-- END HEAD -->
<body class="page-header-fixed page-quick-sidebar-over-content">
    <div class="page-container">
        <?php
        //include top
        include PUBLIC_PATH . "html/top.php";
        //include top_im
        include PUBLIC_PATH . "html/top_im.php";
        ?>
        <div class="page-content-wrapper">
            <div class="page-content">
                <div class="row">
                    <div class="col-md-12">
                        <h3 class="page-title row-br-b-wp">Provincial Summary of Contraceptive Performance</h3>
                        <div class="widget" data-toggle="collapse-widget">
                            <div class="widget-head">
                                <h3 class="heading">Filter by</h3>
                            </div>
                            <div class="widget-body">
                                <?php
                                //include sub_dist_form
                                include('sub_dist_form.php');
                                ?>
                            </div>
                        </div>

                    </div>
                </div>
                <?php
                //if submitted
                if (isset($_POST['submit'])) {
                   
                    if (true) {
                        ?>
                        <?php
                        //include sub_dist_reports
                        include('sub_dist_reports.php');
                        ?>
                        <div class="col-md-12" style="overflow:auto;">

                            <h5 style="margin-top:20px;"  class="center bold">
                                District wise Performance Report <?php echo 'From ' . date('M-Y', strtotime($startDate)) . ' to ' . date('M-Y', strtotime($endDate)); ?><br>
                                Inrespect of Population Welfare Department <?php echo $provinceName ?>
                            </h5>
                            <?php
                            // Unset varibles
                            unset($data, $total, $issue, $totalUsers, $totalCYP, $items, $hfType, $totalOutlets, $product);
                            //select query
                            
                            $qry = "SELECT
                                        tbl_warehouse.wh_name,
provincial_cyp_factors.cyp_factor,
itminfo_tab.user_factor,
tbl_locations.LocName AS dist_name,
tbl_warehouse.dist_id,
itminfo_tab.itm_id,
itminfo_tab.itm_name,
Sum(tbl_wh_data.wh_issue_up) as wh_issue_up,
itminfo_tab.user_factor,
provincial_cyp_factors.cyp_factor,
(cyp_factor * wh_issue_up) AS CYP,
(user_factor * wh_issue_up) AS Users,
tbl_locations.LocName AS dist_name,
tbl_warehouse.dist_id,
itminfo_tab.itm_id,
itminfo_tab.itm_name,
itminfo_tab.method_type
                                    FROM
                                    tbl_wh_data
                                    INNER JOIN tbl_warehouse ON tbl_wh_data.wh_id = tbl_warehouse.wh_id
                                    INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
INNER JOIN provincial_cyp_factors ON tbl_warehouse.prov_id = provincial_cyp_factors.province_id  AND tbl_warehouse.stkid = provincial_cyp_factors.stakeholder_id
INNER JOIN itminfo_tab ON itminfo_tab.itm_id = provincial_cyp_factors.item_id AND tbl_wh_data.item_id = itminfo_tab.itmrec_id
INNER JOIN tbl_locations ON tbl_warehouse.dist_id = tbl_locations.PkLocID
                                    WHERE
                                        tbl_wh_data.RptDate BETWEEN '$startDate' AND '$endDate' AND
                                        tbl_warehouse.prov_id = $selProv AND
                                        tbl_warehouse.stkid = $stakeholder AND
                                        stakeholder.lvl = 4
                                        GROUP BY
tbl_warehouse.wh_name,
tbl_warehouse.dist_id,
itminfo_tab.itm_id
                                    ORDER BY
                                        tbl_warehouse.wh_name ASC,
                                        tbl_wh_data.item_id ASC ";
                            //query results
                            $qryRes = mysql_query($qry);
                            //items
                            $items = $distName = array();
                            //fetch result
                            while ($row = mysql_fetch_array($qryRes)) {
                                $items[$row['itm_id']] = $row['itm_name'];
                                $product[$row['method_type']][$row['itm_id']] = $row['itm_name'];
                                $distName[$row['dist_id']]= $row['dist_name'];
                                @$data[$row['dist_id']]['CYP'] += $row['CYP'];
                                @$data[$row['dist_id']]['Users'] += $row['Users'];
                                $data[$row['dist_id']][$row['itm_name']] = $row['wh_issue_up'];
                                
                                $total['CYP'][] = $row['CYP'];
                                $totalCYP[$row['itm_name']][] = $row['CYP'];
                                $total['Users'][] = $row['Users'];
                                $totalUsers[$row['itm_name']][] = $row['Users'];
                                $total[$row['itm_name']][] = $row['wh_issue_up'];
                            }
                            //echo '<pre>';print_r($data);exit;
                            ?>
                            <table width="100%" id="myTable" cellspacing="0" align="center">
                                <thead>
                                    <tr>
                                        <th rowspan="2">S.No.</th>
                                        <th rowspan="2" width="13%">District</th>
                                        <th rowspan="2" width="7%">No. of Outlets</th>
                                        <?php
                                        foreach ($product as $proType => $proNames) {
                                            if ($proType == 'Condoms') {
                                                echo "<th colspan=" . sizeof($proNames) . ">$proType</th>";
                                            } else {
                                                echo "<th colspan=" . (sizeof($proNames) + 1) . ">$proType</th>";
                                            }
                                        }
                                        ?>
                                        <th rowspan="2">CYP</th>
                                        <th rowspan="2">Users</th>
                                    </tr>
                                    <tr>
                                        <?php
                                        //var
                                        $var = '';
                                        //count
                                        $count = 1;
                                        foreach ($product as $proType => $proNames) {
                                            foreach ($proNames as $name) {
                                                echo "<th width='" . (70 / count($items)) . "%'>$name</th>";
                                            }
                                            if ($proType != $var && $count > 1) {
                                                echo "<th width='100'>Total</th>";
                                            }
                                            //var
                                            $var = $proType;
                                            //count
                                            $count++;
                                        }
                                        ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    //set counter
                                    $counter = 1;
                                    foreach ($distName as $id => $name) {
                                        ?>
                                        <tr>
                                            <td class="center"><?php echo $counter++; ?></td>
                                            <td><?php echo $name; ?></td>
                                            <td class="center"></td>
                                            <?php
                                            //var
                                            $var = '';
                                            //count
                                            $count = 1;
                                            foreach ($product as $proType => $proNames) {
                                                $methodTypeTotal = 0;
                                                foreach ($proNames as $methodName) {
                                                    $methodTypeTotal = $methodTypeTotal + $data[$id][$methodName];
                                                    echo "<td class=\"right\">" . number_format($data[$id][$methodName]) . "</td>";
                                                }
                                                if ($proType != $var && $count > 1) {
                                                    echo "<td class=\"right\">" . number_format($methodTypeTotal) . "</td>";
                                                }
                                                //var
                                                $var = $proType;
                                                //count
                                                $count++;
                                            }
                                            //show CYP
                                            echo "<th class=\"right\">" . number_format($data[$id]['CYP']) . "</th>";
                                            //show users
                                            echo "<th class=\"right\">" . number_format($data[$id]['Users']) . "</th>";
                                            ?>
                                        </tr>
                                        <?php
                                    }
                                    ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th class="right" colspan="2">Total</th>
                                        <th class="center"></th>
                                        <?php
                                        //var
                                        $var = '';
                                        //count
                                        $count = 1;
                                        foreach ($product as $proType => $proNames) {
                                            $methodTypeTotal = 0;
                                            foreach ($proNames as $methodName) {
                                                $methodTypeTotal = $methodTypeTotal + array_sum($total[$methodName]);
                                                echo "<th class=\"right\">" . number_format(array_sum($total[$methodName])) . "</th>";
                                            }
                                            if ($proType != $var && $count > 1) {
                                                echo "<th class=\"right\">" . number_format($methodTypeTotal) . "</th>";
                                            }
                                            //var
                                            $var = $proType;
                                            //cyp
                                            $count++;
                                        }
                                        //show CYP
                                        echo "<th class=\"right\">" . number_format(array_sum($total['CYP'])) . "</th>";
                                        //show users
                                        echo "<th class=\"right\">" . number_format(array_sum($total['Users'])) . "</th>";
                                        ?>
                                    </tr>
                                    <tr>
                                        <th class="right" colspan="3">CYP</th>
                                        <?php
                                        //var
                                        $var = '';
                                        //count
                                        $count = 1;
                                        foreach ($product as $proType => $proNames) {
                                            $methodTypeTotal = 0;
                                            foreach ($proNames as $methodName) {
                                                $methodTypeTotal = $methodTypeTotal + array_sum($totalCYP[$methodName]);
                                                echo "<th class=\"right\">" . number_format(array_sum($totalCYP[$methodName])) . "</th>";
                                            }
                                            if ($proType != $var && $count > 1) {
                                                echo "<th class=\"right\">" . number_format($methodTypeTotal) . "</th>";
                                            }
                                            $var = $proType;
                                            $count++;
                                        }
                                        ?>
                                    </tr>
                                    <tr>
                                        <th class="right" colspan="3">Users</th>
                                            <?php
                                            $var = '';
                                            $count = 1;
                                            foreach ($product as $proType => $proNames) {
                                                $methodTypeTotal = 0;
                                                foreach ($proNames as $methodName) {
                                                    $methodTypeTotal = $methodTypeTotal + array_sum($totalUsers[$methodName]);
                                                    echo "<th class=\"right\">" . number_format(array_sum($totalUsers[$methodName])) . "</th>";
                                                }
                                                if ($proType != $var && $count > 1) {
                                                    echo "<th class=\"right\">" . number_format($methodTypeTotal) . "</th>";
                                                }
                                                $var = $proType;
                                                $count++;
                                            }
                                            ?>
                                    </tr>
                                </tfoot>
                            </table>
                            </td>
                            </tr>
                            </table>
                        </div>
                    </div>
                    <?php
                } else {
                    echo "No record found";
                }
            }
// Unset varibles
            unset($data, $issue, $totalUsers, $totalCYP, $items, $distName, $totalOutlets, $product);
            ?>
        </div>
    </div>
</div>
<?php
//include footer
include PUBLIC_PATH . "/html/footer.php";
//include combos
include ('combos.php');
?>
</body>
</html>