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
                        <center> <h3 class="page-title row-br-b-wp">Inventory Management - <strong color="green">Current Stock Status</strong></h3></center>
                        <div class="widget" data-toggle="collapse-widget">
                            <div class="widget-head">
                                <h3 class="heading">Filter by</h3>
                            </div>
                            <div class="widget-body">
                                <form name="frm" id="frm" action="" method="post" role="form" onsubmit="return false">
                                    <div class="row">               
                                        <div class="col-md-12">
                                            <div class="col-md-3">
                                                <div class="form-group" id="province_div">
                                                    <label class="control-label">Province</label>
                                                    <div class="form-group">
                                                        <select name="province" id="province" class="form-control input-sm">
                                                            <option value="">Select</option>
                                                            <option value="1" <?php if ($province == 1) echo 'selected=selected' ?>>Punjab</option>
                                                            <option value="2" <?php if ($province == 2) echo 'selected=selected' ?>>Sindh</option>
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
    $(document).on('click', '#submit_btn', function () {
        if ($("#province").val() == '' || $("#warehouse").val() == '') {

        } else {
            var form_data = $('form').serialize();
            $('#report_table').html("<center><div id='loadingmessage'><img src='<?php echo PUBLIC_URL; ?>images/ajax-loader.gif'/></div></center>");

            $.ajax({
                type: "POST",
                url: 'ajax_diststore_report.php',
                data: form_data,
                dataType: 'html',
                success: function (data) {
                    $("#report_table").html(data);
                }
            });
        }


    });
    $("#province").change(function () {
        var form_data = $('form').serialize();
        $.ajax({
            type: "POST",
            url: 'ajax_diststore.php',
            data: form_data,
            dataType: 'html',
            success: function (data) {
                $("#warehouse").html(data);
//                        console.log(data);
            }
        });
    });
    function printCont()
    {
        window.print();
    }
</script>