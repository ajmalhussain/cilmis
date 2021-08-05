<?php
/**
 * non_report
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
//include FunctionLib
include(APP_PATH . "includes/report/FunctionLib.php");
//include header
include(PUBLIC_PATH . "html/header.php");
//report id
$report_id = "TOWER";
//action page 
$actionpage = "tower.php";
$rptId = "tower";
$parameters = "TSP";
if (date('d') > 10) {
    //set date
    $date = date('Y-m', strtotime("-1 month", strtotime(date('Y-m-d'))));
} else {
    //set date
    $date = date('Y-m', strtotime("-2 month", strtotime(date('Y-m-d'))));
}
//selected date
$sel_month = date('m', strtotime($date));
//selected year
$sel_year = date('Y', strtotime($date));
$report_title = "Compliance Summary";
if (isset($_POST['go'])) {
   if (!empty($_REQUEST['to_date'])) {
        
        $to_date=$_REQUEST['to_date'];
        $to_date_hf=date('Y-m', strtotime($to_date));
//        print_r($to_date_hf);
    }
     if (!empty($_REQUEST['from_date'])) {
        $from_date=$_REQUEST['from_date'];
        $from_date_hf=date('Y-m', strtotime($from_date));
//        print_r($from_date_hf);exit;
    }
    //selected province
    $sel_prov = mysql_real_escape_string($_POST['province']);

    $sel_wh = mysql_real_escape_string($_POST['warehouse']);
    $sel_dist = mysql_real_escape_string($_POST['district']);
    $sel_stk = mysql_real_escape_string($_POST['stk_sel']);
    $selItem = mysql_real_escape_string($_POST['itm_id']);
    //    $hf_id;
    //    if ($hf_id != 0) {
    //        $and_hf = "  tbl_warehouse.hf_type_id=$hf_id
    //                AND";
    //    } else if ($hf_id == 0) {
    //        $and_hf = '';
    //    }
}
?>
<!-- END HEAD -->
<link rel="stylesheet" type="text/css" href="../../public/assets/global/plugins/select2/select2.css"/>
<style>
    .table-scroll {
        position:relative;
        margin:auto;
        overflow:hidden;
        border:1px solid #000;
    }
    .table-wrap {
        overflow:auto;
    }
    .table-scroll table {
        width:100%;
        margin:auto;
        border-collapse:separate;
        border-spacing:0;
    }
    .table-scroll th, .table-scroll td {
        padding:5px 10px;
        border:1px solid #000;
        background:#fff;
        white-space:nowrap;
        vertical-align:top;
    }
    .table-scroll thead, .table-scroll tfoot {
        background:#f9f9f9;
    }
    .clone {
        position:absolute;
        top:0;
        left:0;
        pointer-events:none;
    }
    .clone th, .clone td {
        visibility:hidden
    }
    .clone td, .clone th {
        border-color:transparent
    }
    .clone tbody th {
        visibility:visible;
        color:red;
    }
    .clone .fixed-side {
        border:1px solid #000;
        background:#eee;
        visibility:visible;
    }
    .clone thead, .clone tfoot{background:transparent;}
</style>
</head><!-- END HEAD -->
<body class="page-header-fixed page-quick-sidebar-over-content">
    <!-- BEGIN HEADER -->
    <div class="page-container">
        <?php
//include top
        include PUBLIC_PATH . "html/top.php";
//include top_im
        include PUBLIC_PATH . "html/top_im.php";
        ?>
        <div class="page-content-wrapper">
            <div class="page-content">
                <h3 class="page-title row-br-b-wp"> <?php echo $report_title; ?></h3>
                <!--                <div class="row">
                                    <div class="col-md-12">
                                        <div class="widget" data-toggle="collapse-widget">
                                            <div class="widget-head">
                                                <h3 class="heading">Filter by</h3>
                                            </div>
                                            <div class="widget-body">
                                                //<?php include('sub_dist_form.php'); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>  -->
                <div class="row">
                    <div class="col-md-12">
                        <form name="searchfrm" id="searchfrm" action="<?php $actionpage ?>" method="post">
                            <div class="widget" data-toggle="collapse-widget">
                                <div class="widget-head">
                                    <h3 class="heading">Filter by</h3>
                                </div>
                                <div class="widget-body">
                                    <div class="row">
                                                    <div class="col-md-2">
                                                        <div class="form-group">
                                                            <label class="control-label">From Date</label>
                                                            <div class="form-group">
                                                                <input type="text" name="from_date" id="from_date"  class="form-control input-sm" value="<?php
                                                                if (isset($_REQUEST['from_date'])) {
                                                                    echo date('Y-m-d', strtotime($from_date));
                                                                } else {

                                                                    echo date('Y-m-d');
                                                                }
                                                                ?>" readonly="readonly" required>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="form-group">
                                                            <label class="control-label">To Date</label>
                                                            <div class="form-group">
                                                                <input type="text" name="to_date" id="to_date"  class="form-control input-sm" value="<?php
                                                                if (isset($_REQUEST['to_date'])) {
                                                                    echo date('Y-m-d', strtotime($to_date));
                                                                } else {

                                                                    echo date('Y-m-d');
                                                                }
                                                                ?>" readonly="readonly" required>
                                                            </div>
                                                        </div>
                                                    </div>
                                        <div class="col-md-3">
                                                <div class="form-group" id="province_div">
                                                    <label class="control-label">Province</label>
                                                    <div class="form-group">
                                                       <select name="province" id="province"  class="form-control input-sm">
                                                            <?php 
                                                            if(!empty($_SESSION['user_province1']))
                                                                {                                                            
                                                                $queryprov = "SELECT
                                                                                tbl_locations.PkLocID AS prov_id,
                                                                                tbl_locations.LocName AS prov_title
                                                                        FROM
                                                                                tbl_locations
                                                                        WHERE
                                                                                tbl_locations.LocLvl = 2
                                                                        AND tbl_locations.PkLocID = ".$_SESSION['user_province1'];
                                                                }
                                                                else
                                                                { ?> <option value="">Select</option>
                                                                <?php
                                                                    
                                                                $queryprov = "SELECT
                                                                            tbl_locations.PkLocID AS prov_id,
                                                                            tbl_locations.LocName AS prov_title
                                                                        FROM
                                                                            tbl_locations
                                                                        WHERE
                                                                            LocLvl = 2
                                                                        AND parentid IS NOT NULL ";
                                                                }
                                                                //query result
                                                                $rsprov = mysql_query($queryprov) or die();
                                                                $prov_name = '';
                                                                while ($rowprov = mysql_fetch_array($rsprov)) {
                                                                    if ($_SESSION['user_province1'] == $rowprov['prov_id']) {
                                                                        $sel = "selected='selected'";
                                                                        $prov_name = $rowprov['prov_title'];
                                                                    } else {
                                                                        $sel = "";
                                                                    }
                                                                    //Populate prov_sel combo
                                                                    ?>
                                                                    <option value="<?php echo $rowprov['prov_id']; ?>" <?php echo $sel; ?>><?php echo $rowprov['prov_title']; ?></option>
                                                                    <?php
                                                                } 
                                                            ?>                        
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        <div class="col-md-3">
                                            <label class="control-label">&nbsp;</label>
                                            <input type="submit" name="go" id="go" value="GO" class="btn btn-primary input-sm" style="display:block" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
<?php
        if(isset($_REQUEST['go'])){
            // Declare two dates 
            $Date1 = $from_date; 
            $Date2 = $to_date; 
            // Declare an empty array 
            $timearray = array(); 

            // Use strtotime function 
            $Variable1 = strtotime($Date1); 
            $Variable2 = strtotime($Date2); 

            // Use for loop to store dates into array 
            // 86400 sec = 24 hrs = 60*60*24 = 1 day 
            for ($currentDate = $Variable1; $currentDate <= $Variable2;) { 

            $Store = date('Y-m-d', $currentDate); 
            $timearray[$Store] = $Store; 
            $currentDate = strtotime('+1 month', $currentDate);
            } 

            // Display the dates in array format 
            //print_r($timearray); 
            //exit();   
               include "compliance_inc_sdp_im_kp_p.php";
//               include "stock_issue_receive_p.php";
        }
        ?>
            </div>
        </div>
    </div>
    <?php include PUBLIC_PATH . "/html/footer.php"; ?>
    <?php
    include PUBLIC_PATH . "/html/reports_includes.php";
    //include ('combos.php');
    ?>
    <script>
        $(function () {
var startDateTextBox = $('#from_date');
        var endDateTextBox = $('#to_date');

        startDateTextBox.datepicker({
            minDate: "-10Y",
            maxDate: 0,
            dateFormat: 'yy-mm-dd',
            changeMonth: true,
            changeYear: true,
            onClose: function (dateText, inst) {
                if (endDateTextBox.val() != '') {
                    var testStartDate = startDateTextBox.datepicker('getDate');
                    var testEndDate = endDateTextBox.datepicker('getDate');
                    if (testStartDate > testEndDate)
                        endDateTextBox.datepicker('setDate', testStartDate);
                } else {
                    endDateTextBox.val(dateText);
                }

            },
            onSelect: function (selectedDateTime) {
                endDateTextBox.datepicker('option', 'minDate', startDateTextBox.datepicker('getDate'));
            }
        });
        endDateTextBox.datepicker({
            maxDate: 0,
            dateFormat: 'yy-mm-dd',
            changeMonth: true,
            changeYear: true,
            onClose: function (dateText, inst) {
                if (startDateTextBox.val() != '') {
                    var testStartDate = startDateTextBox.datepicker('getDate');
                    var testEndDate = endDateTextBox.datepicker('getDate');
                    if (testStartDate > testEndDate)
                        startDateTextBox.datepicker('setDate', testEndDate);
                } else {
                    startDateTextBox.val(dateText);
                }

            },
            onSelect: function (selectedDateTime) {
                startDateTextBox.datepicker('option', 'maxDate', endDateTextBox.datepicker('getDate'));
            }
        });
    });

        </script>
    <script type="text/javascript" src="../../public/assets/global/plugins/select2/select2.min.js"></script>
</body>
<!-- END BODY -->
</html>
