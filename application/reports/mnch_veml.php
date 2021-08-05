<?php
/**
 * stock_sufficiency_report
 * @package reports
 *
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 *
 * @version    2.2
 *
 */
//Including AllClasses
session_start();
$_SESSION['user_id'] = 1;
include("../includes/classes/AllClasses.php");
include(APP_PATH . "includes/report/FunctionLib.php");
include(PUBLIC_PATH . "html/header.php");
$report_id = "STOCKSUFFICIENCY";
$districtId = '';
$filter_prov = '';
if (isset($_POST['month_sel'])) {
//    echo '<pre>';
//    print_r($_REQUEST);
//    exit;
    $selMonth = mysql_real_escape_string($_POST['month_sel']);
    $selYear = mysql_real_escape_string($_POST['year_sel']);
    $array = array_values($_POST['district']);
    $List = implode(',', $array);
    $stakeholder = mysql_real_escape_string($_POST['stakeholder']);
    $selProv = mysql_real_escape_string($_POST['prov_sel']);
    $selDist = $List;
    //$dist_arr = explode(',',$selDist);
    $dist_arr=array();
    $dist_arr[14]='Charsadda';
    $dist_arr[77]='Lakki Marwat';
    $dist_arr[149]='Swat';
    $dist_arr[93]='Mohmand Agency';
//print_r($dist_arr) ; exit;
     
    $reportingDate = mysql_real_escape_string($_POST['year_sel']) . '-' . $selMonth . '-01';
    
    $qry = "SELECT
                tbl_locations.LocName
            FROM
                tbl_locations
            WHERE
                tbl_locations.PkLocID = $selProv";
    $row = mysql_fetch_array(mysql_query($qry));
    $provinceName = $row['LocName'];

    $fileName = 'stock_sufficiency_report_' . $provinceName . '_for_' . date('M-Y', strtotime($reportingDate));
}
?>
</head>
<!-- END HEAD -->

<body class="page-header-fixed page-quick-sidebar-over-content">
    <div class="page-container">
        <?php
        include PUBLIC_PATH . "html/top.php";
        include PUBLIC_PATH . "html/top_im.php";
        ?>
        <div class="page-content-wrapper">
            <div class="page-content">
                <div class="row">
                    <div class="col-md-12">
                        <h3 class="page-title row-br-b-wp">MNCH VEML Stock Status</h3>
                        <div style="display: block;" id="alert-message" class="alert alert-info text-message"><?php echo stripslashes(getReportDescription($report_id)); ?></div>
                        <div class="widget" data-toggle="collapse-widget">
                            <div class="widget-head">
                                <h3 class="heading">Filter by</h3>
                            </div>
                            <div class="widget-body">
                                <form name="frm" id="frm" action="" method="post" role="form">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label class="control-label">Month</label>
                                                    <div class="form-group">
                                                        <select name="month_sel" id="month_sel" class="form-control input-sm" required>
                                                            <?php
                                                            for ($i = 1; $i <= 12; $i++) {
                                                                //check selMonth
                                                                if ($selMonth == $i) {
                                                                    $sel = "selected='selected'";
                                                                } else {
                                                                    $sel = "";
                                                                }
                                                                ?>
                                                                <?php // Populate month_sel combo?>
                                                                <option value="<?php echo date('m', mktime(0, 0, 0, $i, 1)); ?>"<?php echo $sel; ?> ><?php echo date('M', mktime(0, 0, 0, $i, 1)); ?></option>
                                                                <?php
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label class="control-label">Year</label>
                                                    <div class="form-group">
                                                        <select name="year_sel" id="year_sel" class="form-control input-sm" required>
                                                            <?php
                                                            for ($j = date('Y'); $j >= 2010; $j--) {
                                                                //check selYear
                                                                if ($selYear == $j) {
                                                                    $sel = "selected='selected'";
                                                                } else {
                                                                    $sel = "";
                                                                }
                                                                ?>
                                                                <?php // Populate year_sel combo?>
                                                                <option value="<?php echo $j; ?>" <?php echo $sel; ?> ><?php echo $j; ?></option>
                                                                <?php
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                         
                                            <?php
                                            if (@$wh_name == '') {
                                                ?>
                                                <?php
                                                $button = 'true';

                                                $user_lvl = (!empty($_SESSION['user_level']) ? $_SESSION['user_level'] : '');
                                                switch ($user_lvl) {
                                                    case 1:
                                                    case 2:
                                                    case 3:
                                                    case 4:
                                                        //include levelcombos_all_levels
                                                        include("allcombos_mnch_veml.php");
                                                        $js = 'levelcombos_all_levels.js';
                                                        break;
                                                    /* case 4:
                                                      include("levelcombos.php");
                                                      $js = 'levelcombos.js';
                                                      break; */
                                                }
                                                ?>
                                                <?php
                                            } else {
                                                ?>
                                                <div class="col-md-12">
                                                    <div class="col-md-3">
                                                        <div class="control-group">
                                                            <div class="controls">
                                                                <input class="form-control input-medium" id="recipient" name="" type="text" disabled="" value="<?php echo $wh_name; ?>"/>
                                                                <input class="form-control input-medium" id="warehouse" name="warehouse" type="hidden" value="<?php echo $whouse_id; ?>"/>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php }
                                            ?>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label class="control-label">&nbsp;</label>
                                                    <div class="form-group">
                                                        <button type="submit" name="submit" class="btn btn-primary input-sm">Go</button>
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
//    echo '<pre>';
//    print_r($_POST);
//    echo ''.$_POST['month_sel'];
//    exit;
                if (!empty($_POST['month_sel'])) {
$to_date = date('Y-m-t',strtotime($reportingDate));
$to_date2 = date('Y-m-01',strtotime($reportingDate));
$from_date = date("Y-m-d", strtotime($to_date. " -12 month"));
                    //getting issuance of district stores
                    $qry_dist_issuance = "SELECT
                                                Sum(tbl_stock_detail.Qty) AS wh_issue_up, 
                                                stock_batch.item_id, 
                                                tbl_warehouse.dist_id
                                            FROM
                                                tbl_stock_detail
                                                INNER JOIN tbl_stock_master ON tbl_stock_detail.fkStockID = tbl_stock_master.PkStockID
                                                INNER JOIN stock_batch ON  tbl_stock_detail.BatchID = stock_batch.batch_id
                                                INNER JOIN tbl_warehouse ON stock_batch.wh_id = tbl_warehouse.wh_id
                                                INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
                                            WHERE
                                                    tbl_stock_master.TranTypeID = 2
                                                    AND tbl_stock_master.TranDate between '$from_date' and '$to_date'
                                                    AND tbl_warehouse.dist_id IN ($selDist)
                                                AND tbl_warehouse.stkid = ($stakeholder) AND
                                                stakeholder.lvl = 3
                                            GROUP BY
                                            stock_batch.item_id,
                                            tbl_warehouse.dist_id
                                                ";
                    //and tbl_stock_master.transdate between from and to
                    //and district in ()
                    $dist_issuance = array();
//                    echo $qry_dist_issuance;
                    $qryResDistIssuance = mysql_query($qry_dist_issuance);
                    $dist_issuance = array();
                    if (mysql_num_rows(mysql_query($qry_dist_issuance)) > 0) {
                        while ($row = mysql_fetch_array($qryResDistIssuance)) {
                            $dist_issuance[$row['dist_id']][$row['item_id']] = abs($row['wh_issue_up']);
                        }
                    }
//                     echo '<pre>';
//                   print_r($dist_issuance);
//                   exit;
                    $qry_dist_cb = "SELECT
                                                Sum(tbl_stock_detail.Qty) AS wh_cbl_a, 
                                                stock_batch.item_id, 
                                                tbl_warehouse.dist_id
                                            FROM
                                                tbl_stock_detail
                                                INNER JOIN tbl_stock_master ON tbl_stock_detail.fkStockID = tbl_stock_master.PkStockID                                                 
                                                INNER JOIN stock_batch ON  tbl_stock_detail.BatchID = stock_batch.batch_id
                                                INNER JOIN tbl_warehouse ON  stock_batch.wh_id = tbl_warehouse.wh_id
                                                INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
                                            WHERE
                                                tbl_stock_master.TranDate <= '$to_date'
                                                AND tbl_warehouse.dist_id IN ($selDist)
                                                AND tbl_warehouse.stkid = ($stakeholder) AND
                                                stakeholder.lvl = 3
                                            GROUP BY
                                            stock_batch.item_id,
                                            tbl_warehouse.dist_id";
                    //and tbl_stock_master.transdate <= $to
                    //and district in ()
                    $dist_cb = array();
                    
                    $qryResDistCb = mysql_query($qry_dist_cb);
                    //fetch data from qry
                    if (mysql_num_rows(mysql_query($qry_dist_cb)) > 0) {

                        while ($row = mysql_fetch_array($qryResDistCb)) {
                            $dist_cb[$row['dist_id']][$row['item_id']] = $row['wh_cbl_a'];
                        }
                    }
//                    echo $qry_dist_cb;
// echo '<pre>';
//                   print_r($dist_cb);
//                   exit;

                    //getting issuance of hf 
                    $qry_hf_issuance = "SELECT
                                                SUM(tbl_hf_data.issue_balance) as issue_balance, 
                                                tbl_warehouse.dist_id, 
                                                tbl_hf_data.item_id
                                        FROM
                                                tbl_hf_data 
                                                INNER JOIN tbl_warehouse ON  tbl_hf_data.warehouse_id = tbl_warehouse.wh_id
                                        WHERE
                                                tbl_hf_data.reporting_date BETWEEN  '$from_date' and '$to_date'
                                                AND tbl_warehouse.dist_id IN ($selDist) 
                                                AND tbl_warehouse.stkid = ($stakeholder)
                     
                                        GROUP BY
                                        tbl_warehouse.dist_id,
                                        tbl_hf_data.item_id
                    ";
                    // where reporting date between $from and $to
                    // and dist_id in ()
                    $hf_issuance = array();
                    $qryResHfIssuance = mysql_query($qry_hf_issuance);
                    //fetch data from qry
                    if (mysql_num_rows(mysql_query($qry_hf_issuance)) > 0) {

                        while ($row = mysql_fetch_array($qryResHfIssuance)) {
                            @$hf_issuance[$row['dist_id']][$row['item_id']] += $row['issue_balance'];
                        }
                    }
//
//echo $qry_hf_issuance;
// echo '<pre>';
//                   print_r($hf_issuance);
//                   exit;

                    //getting closing of hf 
                    $qry_hf_cb = "SELECT
                                                SUM(tbl_hf_data.closing_balance) as closing_balance, 
                                                tbl_warehouse.dist_id, 
                                                tbl_hf_data.item_id
                                        FROM
                                                tbl_hf_data
                                           
                                                INNER JOIN tbl_warehouse ON  tbl_hf_data.warehouse_id = tbl_warehouse.wh_id
                                        WHERE 
                                                tbl_hf_data.reporting_date = '$to_date2' 
                                                AND tbl_warehouse.dist_id IN ($selDist) 
                                                AND tbl_warehouse.stkid =($stakeholder)
                     
                                        GROUP BY
                                        tbl_warehouse.dist_id,
                                        tbl_hf_data.item_id";
                    // where reporting date = $to
                    // and dist_id in ()

                    $hf_cb = array();
                    $qryResHfCb = mysql_query($qry_hf_cb);
                    if (mysql_num_rows(mysql_query($qry_hf_cb)) > 0) {

                        while ($row = mysql_fetch_array($qryResHfCb)) {
                            @$hf_cb[$row['dist_id']][$row['item_id']] += $row['closing_balance'];
                        }
                    }

//echo $qry_hf_cb;
// echo '<pre>';
//print_r($hf_cb);
//exit;
                    //qry to get specific items
//                    $qryitm = "SELECT
//                                    itminfo_tab.itm_name,
//                                    itminfo_tab.itm_id
//                                FROM
//                                    itminfo_tab
//                                WHERE
//                                    itminfo_tab.itm_id IN (35,36,50,55,46,114,58,122,121,117,45,49,54,42,51,119,85,66,52,57,120,40,222,223,228,231,232,234,249,257,264,244,256,242)
//                            ORDER by itm_name                                    
//                            ";
                    $qryitm = "SELECT
                                    itminfo_tab.itm_name,
                                    itminfo_tab.itm_id,
                                    generic_updated
                                FROM
                                    itminfo_tab
                                WHERE
                                    generic_updated is not null
                            ORDER by generic_updated                                    
                            ";
                    $itm_arr = $generic_arr  = $itm_gen = array();
                    $qryResItm = mysql_query($qryitm);
                    //fetch data from qry
                    if (mysql_num_rows(mysql_query($qryitm)) > 0) {

                        while ($row = mysql_fetch_array($qryResItm)) {
                            $row['generic_updated']                                 = str_replace(array("\n", "\r"), '', $row['generic_updated']);
                            $itm_arr[$row['itm_id']]                                = $row['itm_name'];
                            $itm_gen[$row['itm_id']]                                = $row['generic_updated'];
                            $generic_arr[$row['generic_updated']][$row['itm_id']]   = $row['itm_name'];
                            $generic_arr2[$row['generic_updated']]                  = $row['generic_updated'];
                        }
                    }
                    
                    //converting item wise data to generic aggregated data
                    $dist_cb2 = $dist_issuance2 = $hf_cb2 = $hf_issuance2 = array();
                    foreach ($itm_arr as $itmid => $itm_name) {
                        $this_generic = $itm_gen[$itmid];
                        foreach ($dist_arr as $distid => $dist_name) {
                                 
                                 $dist_mos = $field_mos=$total_mos = 0;
                                 if(!empty($dist_cb[$distid][$itmid]) ){
                                    @$dist_cb2[$distid][$this_generic] += $dist_cb[$distid][$itmid];
                                 }
                                 if(!empty($dist_issuance[$distid][$itmid])){
                                    @$dist_issuance2[$distid][$this_generic] += $dist_issuance[$distid][$itmid];
                                 }
                                 if(!empty($hf_cb[$distid][$itmid]) ){
                                    @$hf_cb2[$distid][$this_generic] += $hf_cb[$distid][$itmid];
                                 }
                                 if(!empty($hf_issuance[$distid][$itmid])){
                                    @$hf_issuance2[$distid][$this_generic] += $hf_issuance[$distid][$itmid];
                                 }
                             }
                    }
                      
//                   echo '<pre>';
//                   print_r($dist_issuance);
//                   print_r($dist_cb);
//                   print_r($hf_issuance);
//                   print_r($hf_cb);
//                   print_r($generic_arr);
//                   print_r($dist_cb2);
//                   exit;
                            ?>
                 <table width="100%" >
                                    <tr>
                                        <td align="center"><h4 class="center"> Provincial MNCH VEML Stock Status <br>
                                                For the month of <?php echo date('M', mktime(0, 0, 0, $selMonth, 1)) . '-' . $selYear . ', Province ' . $provinceName; ?> </h4></td>
                                    </tr>
                 </table>
                    <table id="myTable" class="table table-bordered" cellspacing="0" align="center">
                        <thead>
                            <tr class = "bg-success">
                                <th rowspan="2">S.No</th>
                                <th rowspan="2" width="100">Products </th>
                                <?php
                                
                             foreach ($dist_arr as $distid => $dist_name) {
                                 
                                echo '<th colspan="4" align="center">'.$dist_name.'</th>';
                             }
                                ?>
                            </tr>
                            <tr class = "bg-success">
                               
                                 <?php
                                
                             foreach ($dist_arr as $distid => $dist_name) {
                                 
                                echo '<th align="center" width=\"50\">Dist Store MOS</th>';
                                echo '<th align="center" width=\"50\">Field MOS</th>';
                                echo '<th align="center" width=\"50\">Total MOS</th>';
                                echo '<th align="center" width=\"50\">Remarks</th>';
                             }
                                ?>
                            </tr>
                        </thead>



                        <?php
                        $counter = 1;
                        foreach ($generic_arr2 as $itm_name => $itm_data) {
                            $itmid= $itm_name;
                            echo '<tr>';
                            echo '<th align="center">'.$counter++.'</th>';
                            echo '<th>'.$itm_name.'</th>';
                             foreach ($dist_arr as $distid => $dist_name) {
                                 
                                 $dist_mos = $field_mos=$total_mos = 0;
                                 if(!empty($dist_cb2[$distid][$itmid]) && !empty($dist_issuance2[$distid][$itmid]) && $dist_issuance2[$distid][$itmid] > 0){
                                    $dist_mos = $dist_cb2[$distid][$itmid] / ($dist_issuance2[$distid][$itmid]/12);
                                 }
                                 if(!empty($hf_cb2[$distid][$itmid]) && !empty($hf_issuance2[$distid][$itmid]) && $hf_issuance2[$distid][$itmid] > 0){
                                    $field_mos = $hf_cb2[$distid][$itmid] / ($hf_issuance2[$distid][$itmid]/12);
                                 }
                                 @$total_mos= $dist_mos + $field_mos;
//                                echo '<td>D:'.$distid.',i:'.$itmid.', MOS:'.@$dist_cb2[$distid][$itmid].'/('.@$dist_issuance2[$distid][$itmid].'/12):'.$dist_mos.'</td>';
                                echo '<td align="right" title="'.@$dist_cb2[$distid][$itmid].'/('.@$dist_issuance2[$distid][$itmid].'/12)">'.(($dist_mos==0)?'0':number_format($dist_mos,2)).'</td>';
//                                echo '<td>'.@$hf_cb2[$distid][$itmid].'/('.@$hf_issuance2[$distid][$itmid].'/12):'.$field_mos.'</td>';
                                echo '<td align="right" title="'.@$hf_cb2[$distid][$itmid].'/('.@$hf_issuance2[$distid][$itmid].'/12):">'.(($field_mos==0)?'0':number_format($field_mos,2)).'</td>';
                                echo '<td align="right" >'.(($total_mos==0)?'0':number_format($total_mos,2)).'</td>';
                                echo '<td></td>';
                             }
                            echo '</tr>';
                    }
                    ?>
                            </table> 
                            <?php
                }
                ?>
</body>