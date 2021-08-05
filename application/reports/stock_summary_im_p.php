<?php
//print_r($_REQUEST);
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
$rptId = 'afpak_2';
//user province id
$selwh = $_REQUEST['warehouse'];
$province = $_REQUEST['province'];


if (date('d') > 10) {
    $date = date('Y-m', strtotime("-1 month", strtotime(date('Y-m-d'))));
} else {
    $date = date('Y-m', strtotime("-2 month", strtotime(date('Y-m-d'))));
}
$selMonth = date('m', strtotime($date));
$selYear = date('Y', strtotime($date));
//$fundingSource = $_REQUEST['funding_source'];
//Initialing variables
$date_from = $date_to = $product = $provinceID = $district = $stakeholder = $warehouse = $xmlstore = $selProv = '';

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
                        <h3 class="page-title row-br-b-wp">Stock Summary Report</h3>
                        <div class="widget" data-toggle="collapse-widget">
                            <div class="widget-head">
                                <h3 class="heading">Filter by</h3>
                            </div>
                            <div class="widget-body">
                                <form name="frm" id="frm" action="" method="post" role="form" onsubmit="return false">
                                    <div class="row">               
                                        <div class="col-md-12">
					 <div class="col-md-2">
                                                <div class="form-group">
                                                    <label class="control-label">Date From</label>
                                                    <input type="text" readonly class="form-control input-sm" name="date_from" id="date_from" value="<?php echo $date_from; ?>"/>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label class="control-label">Date To</label>
                                                    <input type="text" readonly class="form-control input-sm" name="date_to" id="date_to" value="<?php echo $date_to; ?>"/>
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
                                                <div class="form-group" id="wh_div">
                                                    <label class="control-label">Warehouse / Store</label>
                                                    <div class="form-group">
                                                        <select name="warehouse" id="warehouse" class="form-control input-sm">
                                                            <option value="">--Select--</option>

                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label class="control-label">&nbsp;</label>
                                                    <div class="form-group">
                                                        <button id="submit_btn" type="submit" name="submit_btn" class="btn btn-primary input-sm">Go</button>
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
 
                <script language="Javascript" src="<?php echo PUBLIC_URL; ?>FusionCharts/Charts/FusionCharts.js"></script>
                <script language="Javascript" src="<?php echo PUBLIC_URL; ?>FusionCharts/themes/fusioncharts.theme.fint.js"></script>

                <div class="page-container"id="report_table">

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
<script language="javascript">
     $(function () {
        var startDateTextBox = $('#date_from');
        var endDateTextBox = $('#date_to');

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
    
    $(document).on('click', '#submit_btn', function () {
        if ($("#province").val() == '' || $("#warehouse").val() == '') {

        } else {
            var form_data = $('form').serialize();
            $('#report_table').html("<center><div id='loadingmessage'><img src='<?php echo PUBLIC_URL; ?>images/ajax-loader.gif'/></div></center>");

            $.ajax({
                type: "POST",
                url: 'ajax_diststore_report_p.php',
                data: form_data,
                dataType: 'html',
                success: function (data) {
                    $("#report_table").html(data);
                }
            });
        }


    });
    
    $("#province").load(function () {
        var form_data = $('form').serialize();
        $.ajax({
            type: "POST",
            url: 'ajax_diststore_p.php',
            data: form_data,
            dataType: 'html',
            success: function (data) {
                $("#warehouse").html(data);
//                        console.log(data);
            }
        });
    });
    
     $(window).on('load',function(){
        var form_data = $('form').serialize();
        $.ajax({
            type: "POST",
            url: 'ajax_diststore_p.php',
            data: form_data,
            dataType: 'html',
            success: function (data) {
                $("#warehouse").html(data);
//                        console.log(data);
            }
        });
    });
//    function printCont()
//    {
//        window.print();
//    }
</script>
