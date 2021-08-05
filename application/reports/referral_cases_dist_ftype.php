<?php
/**
 * stock_status
 * @package reports
 * 
 * @author     Ajmal Hussain 
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
//Including AllClasses
include("../includes/classes/AllClasses.php");
//Including FunctionLib
include(APP_PATH . "includes/report/FunctionLib.php");
//Including header
include(PUBLIC_PATH . "html/header.php");
//Initialing variable report_id
//$report_id = "STOCKISSUANCE";
//Checking date

$date_from = $date_to = $provinceID = '';
//Checking search
if (isset($_REQUEST['search'])) {

    $date_from = $_REQUEST['from_date'];
    $date_to = $_REQUEST['to_date'];
    $stakeholder = $_REQUEST['stakeholder'];
    $provinceID = $_REQUEST['province'];
}
$fileName = "Reffered Cases report";
?>
</head>
<!-- END HEAD -->

<body class="page-header-fixed page-quick-sidebar-over-content" >
    <div class="page-container">
        <?php
//Including top
        include PUBLIC_PATH . "html/top.php";
//Including top_im
        include PUBLIC_PATH . "html/top_im.php";
        ?>
        <div class="page-content-wrapper">
            <div class="page-content">
                <div class="row">
                    <div class="col-md-12">

                        <h3 class="page-title row-br-b-wp"><?php echo $fileName; ?></h3>
                        <div class="widget" data-toggle="collapse-widget">
                            <div class="widget-head">
                                <h3 class="heading">Filter by</h3>
                            </div>
                            <div class="widget-body">
                                <div class="row">
                                    <form method="POST" name="frm" id="frm" action="">
                                        <!-- Row -->
                                        <div class="row">
                                            <div class="col-md-12"> 
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label class="control-label">From Date</label>
                                                        <div class="form-group">
                                                            <input type="text" name="from_date" id="from_date"  class="form-control input-sm" value="<?php
                                                            if (isset($_REQUEST['from_date'])) {
                                                                echo date('Y-m', strtotime($date_from));
                                                            } else {

                                                                echo date('Y-m');
                                                            }
                                                            ?>" required readonly="true">
                                                        </div>
                                                    </div>
                                                </div>


                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label class="control-label">To Date</label>
                                                        <div class="form-group">
                                                            <input type="text" name="to_date" id="to_date"  class="form-control input-sm" value="<?php
                                                            if (isset($_REQUEST['to_date'])) {
                                                                echo date('Y-m', strtotime($date_to));
                                                            } else {

                                                                echo date('Y-m');
                                                            }
                                                            ?>" required readonly="true">
                                                        </div>
                                                    </div>
                                                </div>


                                                <!--                                                <div class="col-md-2">
                                                                                                    <div class="form-group">
                                                                                                        <label class="control-label">Stakeholder</label>
                                                                                                        <select required  name="stakeholder[]" id="stakeholder" class="multiselect-ui form-control input-sm" multiple>
                                                
                                                                                                            <option value="all" <?php echo ($stakeholder == 'all') ? 'selected="selected"' : ''; ?>>All</option>
                                                <?php
//stakeholder query
//gets
//stkid
//stkname
                                                $qry = "SELECT
																	stakeholder.stkid,
																	stakeholder.stkname
																FROM
																	stakeholder
																WHERE
																	stakeholder.ParentID IS NULL
																AND stakeholder.stk_type_id IN (0, 1)
																ORDER BY
																	stakeholder.stkorder ASC";
                                                $qryRes = mysql_query($qry);
                                                if ($qryRes != FALSE) {
                                                    while ($row = mysql_fetch_object($qryRes)) {
                                                        ?>
                                                        <?php
                                                        //Populate stakeholder combo
                                                        if ($row->stkid ==$stakeholder) {
                                                            $sel = "selected='selected'";
                                                        } else {
                                                            $sel = "";
                                                        }
                                                        ?>
                                                                                                                            <option value="<?php echo $row->stkid; ?>" <?php echo $sel; ?>><?php echo $row->stkname; ?></option>
                                                        <?php
                                                    }
                                                }
                                                ?>
                                                                                                        </select>
                                                                                                    </div>
                                                                                                </div>-->
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label class="control-label">Province</label>
                                                        <select required  name="province" id="province" class="form-control input-sm">

                                                            <option value="">Select</option>
                                                            <?php
//Province query
//gets
//Province id
//Province name
                                                            $qry = "SELECT
																	tbl_locations.PkLocID,
																	tbl_locations.LocName
																FROM
																	tbl_locations
																WHERE
																	tbl_locations.LocLvl = 2
																AND tbl_locations.ParentID IS NOT NULL";
                                                            $qryRes = mysql_query($qry);
                                                            if ($qryRes != FALSE) {
                                                                while ($row = mysql_fetch_object($qryRes)) {
                                                                    ?>
                                                                    <?php
                                                                    //Populate province combo

                                                                    if (($row->PkLocID == $provinceID)) {
                                                                        $sel = "selected='selected'";
                                                                    } else {
                                                                        $sel = "";
                                                                    }
                                                                    ?>

                                                                    <option value="<?php echo $row->PkLocID; ?>"<?php echo $sel; ?>><?php echo $row->LocName; ?></option>
                                                                    <?php
                                                                }
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-md-12" style="text-align:right;">
                                                    <label for="firstname">&nbsp;</label>
                                                    <div class="form-group">
                                                        <button type="submit" name="search" value="search" class="btn btn-primary">Search</button>
                                                        <button type="reset" class="btn btn-info">Reset</button>
                                                    </div>
                                                </div>
                                            </div>



                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
                if (isset($_REQUEST['search'])) {
                    $qrynr = "SELECT
tbl_hf_data_reffered_by.pk_id,
tbl_hf_data_reffered_by.hf_data_id,
tbl_hf_data_reffered_by.hf_type_id,
sum(tbl_hf_data_reffered_by.ref_surgeries) as surg,
tbl_hf_type.hf_type,
province.LocName AS province,
District.LocName AS district,
province.PkLocID AS prov_id,
District.PkLocID AS dist_id
FROM
tbl_hf_data_reffered_by
INNER JOIN tbl_hf_type ON tbl_hf_data_reffered_by.hf_type_id = tbl_hf_type.pk_id
INNER JOIN tbl_hf_data ON tbl_hf_data_reffered_by.hf_data_id = tbl_hf_data.pk_id
INNER JOIN tbl_warehouse ON tbl_hf_data.warehouse_id = tbl_warehouse.wh_id
INNER JOIN tbl_locations AS province ON tbl_warehouse.prov_id = province.PkLocID
INNER JOIN tbl_locations AS District ON tbl_warehouse.dist_id = District.PkLocID
WHERE
tbl_hf_data.item_id IN (31, 32) AND
tbl_warehouse.prov_id = $provinceID AND
tbl_hf_data.reporting_date BETWEEN '$date_from-01' AND '$date_to-01' AND
    tbl_warehouse.stkid = 1
    GROUP BY 
hf_type_id,
dist_id
ORDER BY
tbl_hf_type.hf_rank,
District.LocName 
";
//                    print_r($qrynr);
//                    exit;
                    $resnr = mysql_query($qrynr);
                    $numnr = mysql_num_rows($resnr);
                    if ($numnr > 0) {
                        while ($row = mysql_fetch_assoc($resnr)) {
                            $ref_array[$row['dist_id']][$row['hf_type_id']] = $row['surg'];
                            $hf_array[$row['hf_type_id']] = $row['hf_type'];
                            $province = $row['province'];
                            $district_array[$row['dist_id']] = $row['district'];
                            $total[$row['hf_type_id']]+=$row['surg'];
                            $dist_total[$row['dist_id']]+=$row['surg'];
                            $all_total+=$row['surg'];
                        }
//                        print_r($dist_total);exit;
                        ?>

                        <div class="widget">
                            <div class="widget-body">
                                <div class="widget-head">
                                    <h3 class="center">District Wise Referral for CS Cases</h3>
                                    <h4 class="center">For  <?php echo $province . " - " . date('M-y', strtotime($date_from)) . " to " . date('M-y',strtotime($date_to)); ?></h4>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                            <?php include('sub_dist_reports.php'); ?>
                                        <div class="row"><br></div>

                                        <table style="width:95%;margin-left: 2%;" align="center"   id="myTable" class="table table-striped table-bordered table-condensed">
                                            <thead style="background-color:lightgray">
                                            
                                            <th   >District</th>
                                            <?php
                                            foreach ($hf_array as $key => $value) {
                                                echo"<th>" . $value . "</th>";
                                            }
                                            ?>
                                            <th>Total</th>
                                            </thead>
                                            <tbody>
                                                <?php
                                                foreach ($ref_array as $dist => $hf_data) {
                                                    ?>
                                                    <tr>
                                                        <td><?php echo $district_array[$dist]; ?></td>
                                                        <?php
                                                        foreach ($hf_array as $hf => $data) {
                                                             
                                                            echo '<td  style="text-align:right;">' . number_format($hf_data[$hf]) . '</td>';
                                                             
                                                        }
                                                        ?>
                                                        <td  style="text-align:right;"><?php echo $dist_total[$dist]?></td>
                                                    </tr>
                                                    <?php
                                                }
                                                ?>
                                                    
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                            <td>Total</td>
                                            <?php
                                            foreach ($hf_array as $hf => $vslue) {
                                                ?>
                                            <td style="text-align:right;"><?php echo number_format($total[$hf])?></td>
                                                    <?php
                                            }
                                            ?>
                                            <td style="text-align:right;"><?php echo number_format($all_total)?></td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                        <?php
                                    } else {
                                        echo 'No data found';
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>



<?php } ?>
            </div>
        </div>
    </div>
    <!-- END FOOTER -->
    <?php include PUBLIC_PATH . "/html/footer.php"; ?>

<?php include PUBLIC_PATH . "/html/reports_includes.php"; ?>


    <script>

        $(function () {

            $('#from_date').datepicker({
                dateFormat: "yy-mm",
                constrainInput: false,
                changeMonth: true,
                changeYear: true,
                maxDate: ''
            });
            $('#to_date').datepicker({
                dateFormat: "yy-mm",
                constrainInput: false,
                changeMonth: true,
                changeYear: true,
                maxDate: ''
            });

        });


    </script>
</body>
<!-- END BODY -->
</html>