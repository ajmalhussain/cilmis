<!DOCTYPE html>
<?php
include("../includes/classes/Configuration.inc.php");
//Including db file
Login();
include(APP_PATH . "includes/classes/db.php");
//Including header file
include(PUBLIC_PATH . "html/header.php");

include(PUBLIC_PATH . "FusionCharts/Code/PHP/includes/FusionCharts.php");
$province = (!empty($_REQUEST['prov_sel']) ? $_REQUEST['prov_sel'] : '');
$itm_arr_request = (!empty($_REQUEST['product']) ? $_REQUEST['product'] : '');
$stk_arr_request = (!empty($_REQUEST['stakeholder']) ? $_REQUEST['stakeholder'] : '');
$hf_arr_request = (!empty($_REQUEST['hf']) ? $_REQUEST['hf'] : '');
$dist_arr_request = (!empty($_REQUEST['district']) ? $_REQUEST['district'] : '');
//print_r($province);exit;
?>

<style>
    .widget-head ul{padding-left:0px !important;}
    #map{width:100%;height:390px;position: relative}
    #loader{display:none;width: 70px;height: 70px;position:absolute;left:45%;top:40%;z-index: 2000}
    #inputForm{width:50%;height:25px;position: absolute;top:4px;left:10%;z-index: 2000}
    #mapTitle{position:absolute;top:24%;left:2%;width:150px;height:15px;text-align:center;}
    #legendDiv{display:none;position:absolute;padding:2px;border-radius:6px;font-size:8px;background-color:none;border:1px solid black;width:auto;height:auto;top:57%;left:70%;z-index: 3000;}
    .pageLoader {
        position: fixed;
        left: 0px;
        top: 0px;
        width: 100%;
        height: 100%;
        z-index: 9999;
        background: url('../../public/images/ajax-loader.gif') 50% 50% no-repeat rgb(249,249,249);
    }
    /*.col-md-6{min-height:450px !important;}*/
    #loadingmessage{height:450px !important;}
    #loadingmessage img{margin-top:150px !important;}
    select.input-sm{padding:0px !important;}
    .my_dash_cols{
        padding-left: 1px;
        padding-right: 0px;
        padding-top: 1px;
        padding-bottom: 0px;
    }
    .my_dashlets{
        padding-left: 1px;
        padding-right: 0px;
        padding-top: 1px;
        padding-bottom: 0px;
    }

    span.multiselect-native-select {
        position: relative
    }
    span.multiselect-native-select select {
        border: 0!important;
        clip: rect(0 0 0 0)!important;
        height: 1px!important;
        margin: -1px -1px -1px -3px!important;
        overflow: hidden!important;
        padding: 0!important;
        position: absolute!important;
        width: 1px!important;
        left: 50%;
        top: 30px
    }
    .multiselect-container {
        position: absolute;
        list-style-type: none;
        margin: 0;
        padding: 0
    }
    .multiselect-container .input-group {
        margin: 5px
    }
    .multiselect-container>li {
        padding: 0
    }
    .multiselect-container>li>a.multiselect-all label {
        font-weight: 700
    }
    .multiselect-container>li.multiselect-group label {
        margin: 0;
        padding: 3px 20px 3px 20px;
        height: 100%;
        font-weight: 700
    }
    .multiselect-container>li.multiselect-group-clickable label {
        cursor: pointer
    }
    .multiselect-container>li>a {
        padding: 0
    }
    .multiselect-container>li>a>label {
        margin: 0;
        height: 100%;
        cursor: pointer;
        font-weight: 400;
        padding: 3px 0 3px 30px
    }
    .multiselect-container>li>a>label.radio, .multiselect-container>li>a>label.checkbox {
        margin: 0
    }
    .multiselect-container>li>a>label>input[type=checkbox] {
        margin-bottom: 5px
    }


    .panel-actions a {
        color:#333;
    }
    .panel-fullscreen {
        display: block;
        z-index: 9999;
        position: fixed;
        width: 100%;
        height: 100%;
        top: 0;
        right: 0;
        left: 0;
        bottom: 0;
        overflow: auto;
    }

</style>
</head>
<?php
if (isset($_REQUEST['from_date']) || isset($_REQUEST['to_date'])) {

    $from_date = $_REQUEST['from_date'];
    $to_date = $_REQUEST['to_date'];
}
?>
<body class="page-header-fixed page-quick-sidebar-over-content">
    <!--<div class="pageLoader"></div>-->
    <!-- BEGIN HEADER -->


    <div class="page-container">
        <?php
//Including top file
        include PUBLIC_PATH . "html/top.php";
//Including top_im file
        include PUBLIC_PATH . "html/top_im.php";
        ?>

        <div class="page-content-wrapper" >
            <div class="page-content">
                <div class="row">
                    <div class="col-md-12">
                        <div class="tabsbar">
                            <ul>
                                <li class="active"><a href="#"> <b>Consolidated Stock Status</b></a></li>
                                <li><a href="dashboard_infectious_kp_dhq.php"> <b>DHQ Stock Status</b></a></li>
                                <li><a href="dashboard_infectious_kp_dist.php"> <b>District Store Stock Status</b></a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12" >
                        <div class="widget" data-toggle="" >
                            <div class="widget-head" >
                                <h3 class="heading">Filter by</h3>
                            </div>
                            <div class="widget-body collapse in">

                                <form name="frm" id="frm" action="" method="POST" onsubmit="return false">
                                    <table width="100%">
                                        <tbody>
                                            <tr>
                                                <td class="col-md-2">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label class="control-label">Reporting Month</label>
                                                            <div class="form-group">
                                                                <input type="text" name="from_date" id="from_date"  class="form-control input-sm" value="<?php
                                                                if (isset($_REQUEST['from_date'])) {
                                                                    echo date('Y-m-d', strtotime($from_date));
                                                                } else {

                                                                    echo date('Y-m-d');
                                                                }
                                                                ?>" required readonly="true">
                                                            </div>
                                                        </div>
                                                    </div>

                                                </td>

                                                <td class="col-md-2">

                                                    <div class="form-group">
                                                        <label class="control-label">Province</label>
                                                        <div class="form-group ">
                                                            <select name="prov_sel" id="prov_sel" required class="form-control input-sm">
                                                                <option value="">Select</option>
                                                                <option value="3" <?php  if (isset($_REQUEST['prov_sel'])) echo 'selected'; ?>>Khyber Pakhtunkhwa</option>
                                                            </select>
                                                        </div>
                                                    </div>

                                                </td>

                                                <td class="col-md-2">

                                                    <!--<label class="control-label">&nbsp;</label>-->
                                                    <input name="submit_btn" class="btn btn-succes" id="submit_btn" style="margin-top:8%" value="Go" type="submit">

                                                </td>
                                                <td class="col-md-2"></td>
                                                <td class="col-md-2"></td>
                                            </tr>
                                        </tbody>
                                    </table>

                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row" style="margin-left:0px;margin-right: 0px">
                    <div class="col-md-6 my_dash_cols panel panel-default" >
                        <div class="col-md-12 my_dashlets panel-body" >
                            <div class="dashlet_graph" id="ss_a1" href='dashboard_infectious_graph_dist_store.php'>
                                Please press Go button to load data
                            </div>
                        </div>

                    </div>
                    <div class="col-md-6 my_dash_cols panel panel-default" >
                        <div class="col-md-12 my_dashlets panel-body" >
                            <div class="dashlet_graph" id="ss_a2" href='dashboard_infectious_graph_dhq.php'>
                                Please press Go button to load data
                            </div>
                        </div>
                    </div>

                </div>

                <div class="row" style="margin-left:0px;margin-right: 0px">
                    <div class="col-md-6 my_dash_cols panel panel-default" >
                        <div class="col-md-12 my_dashlets panel-body" >
                            <div class="dashlet_graph" id="ss_a3" href='dashboard_infectious_graph_dist_store.php'>
                                Please press Go button to load data
                            </div>
                        </div>

                    </div>
                    <div class="col-md-6 my_dash_cols panel panel-default">
                        <div class="col-md-12 my_dashlets panel-body">
                            <div class="dashlet_graph" id="ss_a4" href='dashboard_infectious_graph_dhq.php'>
                                Please press Go button to load data
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
    ?>
    <script src="<?= PUBLIC_URL ?>js/bootstrap-multiselect.js"></script>

    <script language="Javascript" src="<?php echo PUBLIC_URL; ?>FusionCharts/Charts/FusionCharts.js"></script>
    <script language="Javascript" src="<?php echo PUBLIC_URL; ?>FusionCharts/themes/fusioncharts.theme.zune.js"></script>

    <script type="text/javascript">
                                    $(document).on('click', '#submit_btn', function () {
                                        if ($("#prov_sel").val() == '') {

                                        } else {
                                            $("#ss_a1").html("<center><div id='loadingmessage'><img src='<?php echo PUBLIC_URL ?>images/ajax-loader.gif'/></div></center>");
                                            var form_data = $('form').serialize();
                                            $.ajax({
                                                type: "POST",
                                                url: 'dashboard_infectious_graph_dist_store.php',
                                                data: form_data,
                                                dataType: 'html',
                                                success: function (data) {
                                                    $("#ss_a1").html(data);
                                                }
                                            });
                                             $("#ss_a2").html("<center><div id='loadingmessage'><img src='<?php echo PUBLIC_URL ?>images/ajax-loader.gif'/></div></center>");

                                            $.ajax({
                                                type: "POST",
                                                url: 'dashboard_infectious_graph_dhq.php',
                                                data: form_data,
                                                dataType: 'html',
                                                success: function (data) {
                                                    $("#ss_a2").html(data);
                                                }
                                            });
                                            
                                            $("#ss_a3").html("<center><div id='loadingmessage'><img src='<?php echo PUBLIC_URL ?>images/ajax-loader.gif'/></div></center>");
                                            var form_data = $('form').serialize();
                                            $.ajax({
                                                type: "POST",
                                                url: 'dashboard_infectious_graph_dist_store_bar.php',
                                                data: form_data,
                                                dataType: 'html',
                                                success: function (data) {
                                                    $("#ss_a3").html(data);
                                                }
                                            });
                                             $("#ss_a4").html("<center><div id='loadingmessage'><img src='<?php echo PUBLIC_URL ?>images/ajax-loader.gif'/></div></center>");

                                            $.ajax({
                                                type: "POST",
                                                url: 'dashboard_infectious_graph_dhq_bar.php',
                                                data: form_data,
                                                dataType: 'html',
                                                success: function (data) {
                                                    $("#ss_a4").html(data);
                                                }
                                            });
                                        }
                                    });
                                    $(function () {
                                        $('#from_date').datepicker({
                                            dateFormat: "yy-mm-dd",
                                            constrainInput: false,
                                            changeMonth: true,
                                            changeYear: true,
                                            maxDate: ''
                                        });
                                    });


    </script>
</body>
</html>
