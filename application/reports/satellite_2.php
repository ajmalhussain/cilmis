<?php
ini_set('max_execution_time', 0);
/**
 * clr15
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
$rptId = 'sat2';
$allOpt ='yes';

$removable = array('Copper-T-380A','Implanon NXT','Implanon','Jadelle');
$rem_type = array();
$rem_type['IUD'] =2;
$rem_type['Implant'] =6;
//default stakeholder
if(empty($stakeholder))
$stakeholder = (!empty($_SESSION['user_stakeholder1'])?$_SESSION['user_stakeholder1']:'1');


//if submitted
if (isset($_POST['submit'])) {
    //get selected stakeholder
    $stakeholder = mysql_real_escape_string($_POST['stakeholder']);
    $stk_is_public = false;
if($stakeholder=='1' || $stakeholder=='2' ||$stakeholder=='7' ||$stakeholder=='73' ||$stakeholder=='9' || $stakeholder=='145')
    $stk_is_public=true;


    //get  from date
    $fromDate = isset($_POST['from_date']) ? mysql_real_escape_string($_POST['from_date']) : '';
    //get  to date
    $toDate = isset($_POST['to_date']) ? mysql_real_escape_string($_POST['to_date']) : '';
    //get selected province
    $selProv = mysql_real_escape_string($_POST['prov_sel']);
    //get district id
    $districtId = mysql_real_escape_string($_POST['district']);
    //select query
    // Get 
    // district name
    $distrctName = '';
    if(!empty($districtId)){
    $qry = "SELECT
                tbl_locations.LocName
            FROM
                tbl_locations
            WHERE
                tbl_locations.PkLocID = $districtId";
    //query result
    $row = mysql_fetch_array(mysql_query($qry));
    $distrctName = $row['LocName'];
    }
    $fileName = 'Camps_for_' . $fromDate . '-' . $toDate;

    // Get District warehouse
    $qry = "SELECT
                tbl_warehouse.wh_id,
                tbl_warehouse.wh_name,
                tbl_warehouse.wh_rank,
tbl_locations.LocName as dist_name
                FROM
                        tbl_warehouse
                INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
INNER JOIN tbl_locations ON tbl_warehouse.dist_id = tbl_locations.PkLocID
                WHERE
                tbl_warehouse.prov_id = $selProv AND
                tbl_warehouse.stkid = $stakeholder AND
                stakeholder.lvl = 7  ";
    if(!empty($districtId) && $districtId>0)
    $qry .= "     AND tbl_warehouse.dist_id = $districtId ";
    $qry .= "     ORDER BY
        dist_name ASC,
                    -tbl_warehouse.wh_rank DESC
    ";

    //query result
//    echo $qry;exit;
    $res = mysql_query($qry);
    $sdp_list = array();
    while($row = mysql_fetch_assoc($res)){
        $sdp_list[$row['dist_name']][$row['wh_id']] = $row['wh_name'];
    }
    
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
                        <h3 class="page-title row-br-b-wp"> PERFORMANCE REPORT OF CAMP</h3>
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
                if (isset($_POST['submit'])) {
                    $qry = "SELECT
                            itminfo_tab.*
                        FROM
                            itminfo_tab
                        INNER JOIN stakeholder_item ON itminfo_tab.itm_id = stakeholder_item.stk_item
                        WHERE
                            itminfo_tab.itm_category IN (1, 2) AND
                            stakeholder_item.stkid =  $stakeholder 
                        ORDER BY
                            itminfo_tab.method_rank ASC,
                            itminfo_tab.frmindex ASC
";
//                    echo $qry;exit;
                    $qryRes = mysql_query($qry);
                    
                    
                    $q_data = "SELECT
                                    tbl_hf_satellite_data.warehouse_id,
                                    tbl_hf_satellite_data.item_id,
                                    Sum(tbl_hf_satellite_data.issue_balance) as issue_balance,
                                    Sum(tbl_hf_satellite_data.removals) as removals
                                FROM
                                    tbl_hf_satellite_data
                                INNER JOIN tbl_warehouse ON tbl_hf_satellite_data.warehouse_id = tbl_warehouse.wh_id
                                WHERE
                                 
                                    tbl_hf_satellite_data.reporting_date BETWEEN '$fromDate-01' AND '$toDate-01'
                                    AND tbl_warehouse.stkid = $stakeholder AND
                                    tbl_warehouse.prov_id = $selProv 
";
                    if(!empty($districtId) && $districtId>0)
                        $q_data .= "     AND tbl_warehouse.dist_id = $districtId ";
                   
                    
                    $q_data .= "    GROUP BY
                                        tbl_hf_satellite_data.warehouse_id,
                                        tbl_hf_satellite_data.item_id ";
                    
//                    echo $q_data;
                    $res_data = mysql_query($q_data);
                    $data_arr = $removals_data = array();
                    while($row = mysql_fetch_assoc($res_data)){
                        $data_arr[$row['warehouse_id']][$row['item_id']] = $row['issue_balance'];
                        $removals_data[$row['warehouse_id']][$row['item_id']] = $row['removals'];
                    }
//                    echo '<pre>';print_r($data_arr); 
//                    if (mysql_num_rows(mysql_query($qry)) > 0 ) {
                    if (true) {
                        ?>
                        <?php include('sub_dist_reports.php'); ?>
                        <div class="col-md-12" style="overflow:auto;">
                            <table width="100%">
                                <tr>
                                    <td align="center">
                                        <h4 class="center bold">
                                            PERFORMANCE REPORT OF CAMP <?php echo $stk_name;?> <br>
                                            <?php
                                            if ($fromDate != $toDate) {
                                                $reportingPeriod = "For the period of " . date('M-Y', strtotime($fromDate)) . ' to ' . date('M-Y', strtotime($toDate));
                                            } else {
                                                $reportingPeriod = "For the month of " . date('M-Y', strtotime($fromDate));
                                            }
                                            ?>
                                            <?php echo $reportingPeriod . ', District ' . $distrctName; ?>
                                        </h4>
                                    </td>
                                    <td>
                                        <h4 class="right">Camps Performance</h4>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        <?php
                                        while ($row = mysql_fetch_array($qryRes)) {
                                            $itemIds[$row['itm_name']] = $row['itm_id'];
                                            $product[$row['method_type']][] = $row['itm_name'];
                                            if (strtoupper($row['method_type']) == strtoupper($row['generic_name'])) {
                                                $methodType[$row['method_type']]['rowspan'] = 2;
                                            } else {
                                                $genericName[$row['generic_name']][] = $row['itm_name'];
                                            }
                                        }
//                                        echo '<pre>';
//                                        print_r($itemIds);
//                                        print_r($product);
//                                        print_r($proNames);
                                        ?>
                                        <table width="100%" id="myTable" cellspacing="0" align="center">
                                            <thead>
                                                <tr>
                                                    <th rowspan="3" width="">District</th>
                                                    <th rowspan="3" width="20%">SDP / Venue</th>
                                                    <th rowspan="3" width="5%">No of Camps</th>
                                                    <?php
                                                    //product
                                                    foreach ($product as $proType => $proNames) {
                                                        $cspan = sizeof($proNames);
                                                        if(!empty($rem_type[$proType]))$cspan = $rem_type[$proType];
                                                        echo "<th colspan=" . $cspan . " rowspan='" . (isset($methodType[$proType]['rowspan']) ? $methodType[$proType]['rowspan'] : '') . "'>$proType</th>";
                                                    }
                                                    ?>
                                                    <th rowspan="3" width="10%">Total</th>
                                                </tr>
                                                <tr>
                                                    <?php
                                                    $col = '';
                                                    //generic name
                                                    foreach ($genericName as $name => $proNames) {
                                                        $cspan = sizeof($proNames);
                                                        if(in_array($name, $removable))$cspan++;
                                                        echo "<th colspan=" . $cspan . ">$name</th>";
                                                    }
                                                    ?>
                                                </tr>
                                                <tr>
                                                    <?php
                                                    $col = '';
                                                    //product
                                                    foreach ($product as $proType => $proNames) {
                                                        foreach ($proNames as $name) {
                                                            echo "<th width='" . (70 / count($itemIds)) . "%'>$name</th>";
                                                            if(in_array($name, $removable))
                                                                echo "<td class=\"bg-danger\">$name Remove</td>";
                                                        }
                                                    }
                                                    ?>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                    foreach($sdp_list as $dist_name => $dist_list){
                                                         echo '<tr>';
                                                            echo '<td style="text-align:center" class="bg-success" colspan="20"><h3>'.$dist_name.'</h3></td>';
                                                         echo '</tr>';
                                                            
                                                        foreach($dist_list as $wh_id => $wh_name){
                                                            echo '<tr>';
                                                            echo '<td>'.$dist_name.'</td>';
                                                            echo '<td>'.$wh_name.'</td>';
                                                            echo '<td></td>';
                                                            foreach ($product as $proType => $proNames) {
                                                                foreach ($proNames as $name) {

    //                                                                echo "<td width=''></td>";
                                                                    echo "<td>".((!empty($data_arr[$wh_id][$itemIds[$name]]))?$data_arr[$wh_id][$itemIds[$name]]:'')."</td>";


                                                                    if(in_array($name, $removable)){

                                                                        echo "<td class=\"bg-danger\">";
                                                                        if(!empty($removals_data[$wh_id][$itemIds[$name]]))
                                                                            echo $removals_data[$wh_id][$itemIds[$name]];
                                                                        echo "</td>";
                                                                    }
                                                                }
                                                            }
                                                            echo '<td></td>';
                                                            echo '</tr>';
                                                        }
                                                    }
                                                ?>
                                            </tbody>
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

            // Unset variables
            unset($ob, $cb, $rcv, $issue, $data, $hfTypes, $itemIds, $product);
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