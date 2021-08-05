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
                            <h3 class="page-title row-br-b-wp">Product Wise Stock Trend – District Stores</h3>
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
                                                <td class="col-md-3">
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
                                                <td class="col-md-3">
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
                                                <td class="col-md-3">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label class="control-label">Province</label>
                                                            <div class="form-group ">
                                                                <select name="prov_sel" id="prov_sel" required class="form-control input-sm">
                                                                <!--<option value="">Select</option>--> 
                                                                <!--<option value="3">Khyber Pakhtunkhwa</option>-->
                                                                   
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
                                                                { ?> <option value="">Select</option> <?php
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
                                                </td>
                                                <td class="col-md-3">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label class="control-label">District</label>
                                                            <div class="form-group " id="district_div">
                                                                <select required  name="district[]" id="district" class="multiselect-ui form-control input-sm" multiple>
                                                                    <option value="">Select</option>
                                                                </select> 
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>

                                            </tr>
                                            <tr>
                                                <td class="col-md-3">
                                                    <div class="col-md-12">
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
                                                    </div>
                                                </td>
                                                <td class="col-md-3">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label class="control-label">Product</label>
                                                            <div class="controls " id="product_div">
                                                                <select required  name="product" id="product" class="input-medium select2me">
                                                                    <option value="" selected="selected"> Select </option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="col-md-2">

                                                    <!--<label class="control-label">&nbsp;</label>-->
                                                    <input name="submit_btn" class="btn btn-succes" id="submit_btn" style="margin-top:8%" value="Go" type="submit">

                                                </td>
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

                <div class="row" style="margin-left:0px;margin-right: 0px;display:none;" id="graph_row_2" >
                    <div class="col-md-6  my_dash_cols panel panel-default">
                        <div class="col-md-12 my_dashlets panel-body" >
                            <div class="dashlet_graph" id="ss_i1" href='dashboard_im_all_prov_a2.php' >

                            </div>
                        </div>

                    </div>
                    <div class="col-md-6  my_dash_cols panel panel-default" >
                        <div class="col-md-12 my_dashlets panel-body" >
                            <div class="dashlet_graph" id="ss_i2" href='dashboard_im_all_prov_a1.php'>
                            </div>
                        </div>
                    </div>


                </div>
                <div class="row" style="margin-left:0px;margin-right: 0px;display:none;" id="graph_row_3" >
                    <div class="col-md-6 my_dash_cols panel panel-default" >
                        <div class="col-md-12 my_dashlets panel-body" >
                            <div class="dashlet_graph" id="ss_i3" href='dashboard_im_all_prov_a3.php'>

                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 my_dash_cols panel panel-default" >
                        <div class="col-md-12 my_dashlets panel-body" >
                            <div class="dashlet_graph" id="ss_i4" href='dashboard_im_all_prov_a4.php'>

                            </div>
                        </div>
                    </div>

                </div>
                <div class="row" style="margin-left:0px;margin-right: 0px;display:none;" id="graph_row_1">
                    <div class="col-md-12 my_dash_cols panel panel-default" >
                        <div class="col-md-12 my_dashlets panel-body">
                            <div class="dashlet_graph" id="ss_a1" >

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
    <script src="../../public/js/bootstrap_multiselect.js"></script>

    <script language="Javascript" src="<?php echo PUBLIC_URL; ?>FusionCharts/Charts/FusionCharts.js"></script>
    <script language="Javascript" src="<?php echo PUBLIC_URL; ?>FusionCharts/themes/fusioncharts.theme.zune.js"></script>
    <script type="text/javascript" src="../../public/assets/global/plugins/select2/select2.min.js"></script>

    <script type="text/javascript">
                                    $(document).on('click', '#submit_btn', function () {
                                        if ($("#prov_sel").val() == '' || $("#product").val() == '') {

                                        } else {
                                            $("#filter_note").css("display", "none");
                                            $("#graph_row_1").css("display", "block");
                                            $("#graph_row_2").css("display", "block");
                                            $("#graph_row_3").css("display", "block");
                                            $("#graph_row_4").css("display", "block");


                                            $("#ss_i1").html("<center><div id='loadingmessage'><img src='<?php echo PUBLIC_URL ?>images/ajax-loader.gif'/></div></center>");
                                            var form_data = $('form').serialize();
                                            $.ajax({
                                                type: "POST",
                                                url: 'dashboard_im_all_prov_a2.php',
                                                data: form_data,
                                                dataType: 'html',
                                                success: function (data) {
                                                    $("#ss_i1").html(data);
                                                }
                                            });
                                            $("#ss_i2").html("<center><div id='loadingmessage'><img src='<?php echo PUBLIC_URL ?>images/ajax-loader.gif'/></div></center>");

                                            $.ajax({
                                                type: "POST",
                                                url: 'dashboard_im_all_prov_a1.php',
                                                data: form_data,
                                                dataType: 'html',
                                                success: function (data) {
                                                    $("#ss_i2").html(data);
                                                }
                                            });
                                            $("#ss_i3").html("<center><div id='loadingmessage'><img src='<?php echo PUBLIC_URL ?>images/ajax-loader.gif'/></div></center>");

                                            $.ajax({
                                                type: "POST",
                                                url: 'dashboard_im_all_prov_a3.php',
                                                data: form_data,
                                                dataType: 'html',
                                                success: function (data) {
                                                    $("#ss_i3").html(data);
                                                }
                                            });
                                            $("#ss_i4").html("<center><div id='loadingmessage'><img src='<?php echo PUBLIC_URL ?>images/ajax-loader.gif'/></div></center>");

                                            $.ajax({
                                                type: "POST",
                                                url: 'dashboard_im_all_prov_a4.php',
                                                data: form_data,
                                                dataType: 'html',
                                                success: function (data) {
                                                    $("#ss_i4").html(data);
                                                }
                                            });
                                            $("#ss_a1").html("<center><div id='loadingmessage'><img src='<?php echo PUBLIC_URL ?>images/ajax-loader.gif'/></div></center>");

                                            $.ajax({
                                                type: "POST",
                                                url: 'dashboard_im_all_prov_a5.php',
                                                data: form_data,
                                                dataType: 'html',
                                                success: function (data) {
                                                    $("#ss_a1").html(data);
                                                }
                                            });
                                        }
                                    });
                                    $(function () {
//                                        $('.multiselect-ui').multiselect({
//                                            includeSelectAllOption: true,
//                                            onChange: function (option, checked) { 
//                                                var selectedOptions = $('.multiselect-ui option:selected');
//
//                                                if (selectedOptions.length >= 4) {
//                                                    // Disable all other checkboxes.
//                                                    var nonSelectedOptions = $('.multiselect-ui option').filter(function () {
//                                                        return !$(this).is(':selected');
//                                                    });
//
//                                                    nonSelectedOptions.each(function () {
//                                                        var input = $('input[value="' + $(this).val() + '"]');
//                                                        input.prop('disabled', true);
//                                                        input.parent('li').addClass('disabled');
//                                                    });
//                                                } else {
//                                                    // Enable all checkboxes.
//                                                    $('.multiselect-ui option').each(function () {
//                                                        var input = $('input[value="' + $(this).val() + '"]');
//                                                        input.prop('disabled', false);
//                                                        input.parent('li').addClass('disabled');
//                                                    });
//                                                }
//                                            }
//                                        });

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

//                                    $("#prov_sel").change(function () {
//                                        $("#select2-chosen-1").html("Select");
//                                        $.ajax({
//                                            type: "POST",
//                                            url: 'ajax_items.php',
//                                            data: {province: $("#prov_sel").val(), search_type: $("#search_type").val()},
//                                            dataType: 'html',
//                                            success: function (data) {
//                                                $("#product").html(data);
//
//                                            }
//                                        });
//                                    });
                                    $("#search_type").change(function () {
                                        $("#select2-chosen-1").html("Select");
                                        $.ajax({
                                            type: "POST",
                                            url: 'ajax_items_wms.php',
                                            data: {province: $("#prov_sel").val(), search_type: $("#search_type").val()},
                                            dataType: 'html',
                                            success: function (data) {
                                                $("#product").html(data);

                                            }
                                        });
                                    });
//                                    $("#prov_sel").change(function () {
                                        show_dist();
//                                    });
                                    function show_dist() {
                                        $.ajax({
                                            type: "POST",
                                            url: 'multiselect_district.php',
                                            data: {province: $("#prov_sel").val(), all: 'all'},
                                            dataType: 'html',
                                            success: function (data) {
                                                $("#district_div").html(data);
                                                console.log(data);
                                            }
                                        });
                                    }
                                    function click_dashlet_a1(province, wh_id, date, product, search_type, wh_name) {
                                        $("#graph_row_2").css("display", "block");
                                        $("#ss_i2").html("<center><div id='loadingmessage'><img src='<?php echo PUBLIC_URL ?>images/ajax-loader.gif'/></div></center>");

                                        $.ajax({
                                            type: "POST",
                                            url: 'dashboard_im_all_prov_a1_table.php',
                                            data: {province: province, wh_id: wh_id, date: date, product: product, search_type: search_type, wh_name: wh_name},
                                            dataType: 'html',
                                            success: function (data) {
                                                $("#ss_i2").html(data);

                                            }
                                        });
                                    }

    </script>
</body>
</html>
