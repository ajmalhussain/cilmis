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
<link rel="stylesheet" type="text/css" href="../../public/assets/global/plugins/select2/select2.css"/>
</head>
<?php
if (isset($_REQUEST['from_date']) || isset($_REQUEST['to_date'])) {

    $from_date = $_REQUEST['from_date'];
    $to_date = $_REQUEST['to_date'];
    $province = $_REQUEST['prov_sel'];
    print_r($province);
    exit;
}
?>
<body class="page-header-fixed page-quick-sidebar-over-content" >
    <!--<div class="pageLoader"></div>-->
    <!-- BEGIN HEADER -->


    <div class="page-container" >
        <?php
//Including top file
        include PUBLIC_PATH . "html/top.php";
//Including top_im file
        include PUBLIC_PATH . "html/top_im.php";
        ?>

        <div class="page-content-wrapper" >
            <div class="page-content" >
                <div class="row">
                    <div class="col-md-12">
                        <div class="tabsbar">
                            <ul>
                                <!--<li ><a href="dashboard_wms.php"> <b>Consolidated Stock Status</b></a></li>-->
                                <li class="active"><a href="#"> <b>District Store wise Stock Status</b></a></li>
                                <li><a href="dashboard_im_all_prov.php"> <b>District Store Stock Trends</b></a></li>
 
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="row" >
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
                                                            <label class="control-label">From Date</label>
                                                            <div class="form-group">
                                                                <input type="text" name="from_date" id="from_date"  class="form-control input-sm" value="<?php
                                                                if (isset($_REQUEST['from_date'])) {
                                                                    echo date('Y-m', strtotime($from_date));
                                                                } else {

                                                                    echo date('Y-m');
                                                                }
                                                                ?>" required readonly="true">
                                                            </div>
                                                        </div>
                                                    </div>

                                                </td>
                                                <td class="col-md-2">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label class="control-label">To Date</label>
                                                            <div class="form-group">
                                                                <input type="text" name="to_date" id="to_date"  class="form-control input-sm" value="<?php
                                                                if (isset($_REQUEST['to_date'])) {
                                                                    echo date('Y-m', strtotime($to_date));
                                                                } else {

                                                                    echo date('Y-m');
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
                                                                <option value="3">Khyber Pakhtunkhwa</option>
                                                                   
                                                            </select>
                                                        </div>
                                                    </div>

                                                </td>
                                                <td class="col-md-3">
                                                    <div class="form-group">
                                                        <label class="control-label">Search As</label>
                                                        <div class="form-group ">
                                                            <select required  name="search_type" id="search_type" class=" form-control input-sm">
                                                                <option value="">Select</option>
                                                                <option value="1" >Generic Name</option>
                                                                <option value="2" >Product Name</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="col-md-3">
                                                    <div class="form-group">
                                                        <label class="control-label">Product</label>
                                                        <div class="controls " id="product_div">
                                                            <select required  name="product" id="product" class="input-medium select2me">
                                                                <option value="" selected="selected"> Select </option>
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
                <div id="filter_note" class="row " style="display:block;">
                    <div class="col-md-12">
                        <div class="note note-info">To view graphs , please click GO button after selecting filters.</div>
                    </div>
                </div>
                <div class="row" style="margin-left:0px;margin-right: 0px;margin-bottom: 2%;display:none;" id="graph_row_1">
                    <div class="col-md-12 my_dash_cols " >
                        <div class="col-md-12 my_dashlets panel-body" style="background-image: linear-gradient(to bottom right, #dff3f4, #1e1e1c);">
                            <div class="dashlet_graph" id="ss_a1" href='dashboard_wms_graph_dist_store.php'>

                            </div>
                        </div>

                    </div>


                </div>
                <div class="row" style="margin-left:0px;margin-right: 0px;display:none;" id="graph_row_2" >
                    <div class="col-md-6  my_dash_cols panel panel-default">
                        <div class="col-md-12 my_dashlets panel-body" >
                            <div class="dashlet_graph" id="ss_i1" href='dashboard_wms_dashlet_dist_i1.php' >

                            </div>
                        </div>

                    </div>
                    <div class="col-md-6  my_dash_cols panel panel-default" >
                        <div class="col-md-12 my_dashlets panel-body" >
                            <div class="dashlet_graph" id="ss_i2" href='dashboard_wms_dashlet_dist_i2.php'>

                            </div>
                        </div>
                    </div>


                </div>
                <div class="row" style="margin-left:0px;margin-right: 0px;display:none;" id="graph_row_3" >
                    <div class="col-md-6 my_dash_cols panel panel-default" >
                        <div class="col-md-12 my_dashlets panel-body" >
                            <div class="dashlet_graph" id="ss_i3" href='dashboard_wms_dashlet_dist_i3.php'>

                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 my_dash_cols panel panel-default" >
                        <div class="col-md-12 my_dashlets panel-body" >
                            <div class="dashlet_graph" id="ss_i4" href='dashboard_wms_dashlet_dist_i4.php'>

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
    <script type="text/javascript" src="../../public/assets/global/plugins/select2/select2.min.js"></script>

    <script type="text/javascript">
                                    $(document).on('click', '#submit_btn', function () {
                                        if ($("#prov_sel").val() == '' || $("#search_type").val() == '' || $("#product").val() == '') {

                                        } else {
                                            $("#filter_note").css("display", "none");
                                            $("#graph_row_1").css("display", "block");
                                            $("#graph_row_2").css("display", "block");
                                            $("#graph_row_3").css("display", "block"); 


                                            $("#ss_a1").html("<center><div id='loadingmessage'><img src='<?php echo PUBLIC_URL ?>images/ajax-loader.gif'/></div></center>");
                                            var form_data = $('form').serialize();
                                            $.ajax({
                                                type: "POST",
                                                url: 'dashboard_wms_graph_dist_store.php',
                                                data: form_data,
                                                dataType: 'html',
                                                success: function (data) {
                                                    $("#ss_a1").html(data);
                                                }
                                            });
                                            $("#ss_i1").html("<center><div id='loadingmessage'><img src='<?php echo PUBLIC_URL ?>images/ajax-loader.gif'/></div></center>");

                                            $.ajax({
                                                type: "POST",
                                                url: 'dashboard_wms_dashlet_dist_i1.php',
                                                data: form_data,
                                                dataType: 'html',
                                                success: function (data) {
                                                    $("#ss_i1").html(data);
                                                }
                                            });
                                            $("#ss_i2").html("<center><div id='loadingmessage'><img src='<?php echo PUBLIC_URL ?>images/ajax-loader.gif'/></div></center>");

                                            $.ajax({
                                                type: "POST",
                                                url: 'dashboard_wms_dashlet_dist_i2.php',
                                                data: form_data,
                                                dataType: 'html',
                                                success: function (data) {
                                                    $("#ss_i2").html(data);
                                                }
                                            });
                                            $("#ss_i3").html("<center><div id='loadingmessage'><img src='<?php echo PUBLIC_URL ?>images/ajax-loader.gif'/></div></center>");

                                            $.ajax({
                                                type: "POST",
                                                url: 'dashboard_wms_dashlet_dist_i3.php',
                                                data: form_data,
                                                dataType: 'html',
                                                success: function (data) {
                                                    $("#ss_i3").html(data);
                                                }
                                            });
                                            $("#ss_i4").html("<center><div id='loadingmessage'><img src='<?php echo PUBLIC_URL ?>images/ajax-loader.gif'/></div></center>");

                                            $.ajax({
                                                type: "POST",
                                                url: 'dashboard_wms_dashlet_dist_i4.php',
                                                data: form_data,
                                                dataType: 'html',
                                                success: function (data) {
                                                    $("#ss_i4").html(data);
                                                }
                                            });
                                            
                                        }
                                    });
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
                                    $("#search_type").change(function () {
                                        $("#select2-chosen-1").html("Select");
                                        $.ajax({
                                            type: "POST",
                                            url: 'ajax_items.php',
                                            data: {province: $("#prov_sel").val(), search_type: $("#search_type").val()},
                                            dataType: 'html',
                                            success: function (data) {
                                                $("#product").html(data);

                                            }
                                        });
                                    });
                                    $("#prov_sel").change(function () {
                                        console.log($("#select2-chosen-1").html("Select"));
                                        $("#select2-chosen-1").html("Select");
                                        $.ajax({
                                            type: "POST",
                                            url: 'ajax_items.php',
                                            data: {province: $("#prov_sel").val(), search_type: $("#search_type").val()},
                                            dataType: 'html',
                                            success: function (data) {
                                                $("#product").html(data);

                                            }
                                        });
                                    });

    </script>
</body>
</html>
