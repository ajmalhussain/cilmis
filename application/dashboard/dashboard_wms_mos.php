<!DOCTYPE html>
<?php
include("../includes/classes/Configuration.inc.php");
//Including db file
//Login();
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
                                                            <label class="control-label">Month</label>
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

                                                    <div class="form-group">
                                                        <label class="control-label">Province</label>
                                                        <div class="form-group ">
                                                            <select name="prov_sel" id="prov_sel" required class="form-control input-sm">
                                                                <option value="">Select</option>
                                                                <?php
                                                                $queryprov = "SELECT
                                                                            tbl_locations.PkLocID AS prov_id,
                                                                            tbl_locations.LocName AS prov_title
                                                                        FROM
                                                                            tbl_locations
                                                                        WHERE
                                                                            LocLvl = 2
                                                                        AND parentid IS NOT NULL
                                                                        AND LocType=2
                                                                        LIMIT 3                                                                        
                                                                            ";
//query result
                                                                $rsprov = mysql_query($queryprov);
//                                                                    print_r( $rsprov);exit;
                                                                $prov_name = '';
                                                                $sel = '';

                                                                while ($rowprov = mysql_fetch_array($rsprov)) {
                                                                    if ($province == $rowprov['prov_id']) {
//                                                                            echo 'checkk';
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

                                                </td>
                                                <td class="col-md-3">
                                                <div class="form-group">
                                                        <label class="control-label">Level</label>
                                                        <div class="form-group "> 
                                                            <select   name="level" id="level"  required class="form-control input-sm" >
                                                                <option value="">Select</option>
                                                                <option value="1">Province</option>
                                                                <option value="2">District</option>
                                                                <option value="3">Health Facility</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="col-md-3">
                                                    <div class="form-group">
                                                        <label class="control-label">Product</label>
                                                        <div class="controls ">
                                                            <select required  name="product[]" id="product" class="multiselect-ui form-control input-sm" multiple>
                                                                <!--<option value="" selected="selected"> Select </option>-->
                                                                <?php
                                                                $queryprod = "SELECT
                                                                        itminfo_tab.itm_id,
                                                                        itminfo_tab.itm_name
                                                                        FROM
                                                                        itminfo_tab
                                                                        ORDER BY
                                                                        itminfo_tab.itm_name ASC
                                                                            ";
//query result
                                                                $rs_prod = mysql_query($queryprod);
//                                                                

                                                                while ($row = mysql_fetch_array($rs_prod)) {
                                                                    ?>
                                                                    <option value="<?php echo $row['itm_id'] ?>"><?php echo $row['itm_name'] ?></option>
                                                                    <?php
                                                                }
                                                                ?>
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
<!--                <div class="row" style="margin-left:0px;margin-right: 0px;margin-bottom: 2%;display:none;" id="graph_row_1">
                    <div class="col-md-12 my_dash_cols " >
                        <div class="col-md-12 my_dashlets panel-body" style="background-image: linear-gradient(to bottom right, #dff3f4, #1e1e1c);">
                            <div class="dashlet_graph" id="ss_a1" >

                            </div>
                        </div>

                    </div>


                </div>-->
                <div class="row" style="margin-left:0px;margin-right: 0px;display:none;" id="graph_row_2" >
                    <div class="col-md-12  my_dash_cols panel panel-default">
                        <div class="col-md-12 my_dashlets panel-body" >
                            <div class="dashlet_graph" id="ss_i1"  >

                            </div>
                        </div>

                    </div>
                    <!--                    <div class="col-md-6  my_dash_cols panel panel-default" >
                                            <div class="col-md-12 my_dashlets panel-body" >
                                                <div class="dashlet_graph" id="ss_i2" href='dashboard_wms_dashlet_dist_i2.php'>
                                                    
                                                </div>
                                            </div>
                                        </div>-->


                </div>
                <div class="row" style="margin-left:0px;margin-right: 0px;display:none;" id="graph_row_3" >
                    <div class="col-md-12 my_dash_cols panel panel-default" >
                        <div class="col-md-12 my_dashlets panel-body" >
                            <div class="dashlet_graph" id="ss_i2" >

                            </div>
                        </div>
                    </div>
                    <!--                    <div class="col-md-6 my_dash_cols panel panel-default" >
                                            <div class="col-md-12 my_dashlets panel-body" >
                                                <div class="dashlet_graph" id="ss_i4" href='dashboard_wms_dashlet_dist_i4.php'>
                                                    
                                                </div>
                                            </div>
                                        </div>-->

                </div>
                <div class="row" style="margin-left:0px;margin-right: 0px;display:none;" id="graph_row_4" >
                    <div class="col-md-12 my_dash_cols panel panel-default" >
                        <div class="col-md-12 my_dashlets panel-body" >
                            <div class="dashlet_graph" id="ss_i3" >

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
                                        if ($("#prov_sel").val() == '' || $("#level").val() == '' || $("#product").val() == '') {

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
                                                url: 'dashboard_wms_mos_dashlet_a1.php',
                                                data: form_data,
                                                dataType: 'html',
                                                success: function (data) {
                                                    $("#ss_i1").html(data);
                                                }
                                            });
                                            $("#ss_i2").html("<center><div id='loadingmessage'><img src='<?php echo PUBLIC_URL ?>images/ajax-loader.gif'/></div></center>");
                                            
                                            $.ajax({
                                                type: "POST",
                                                url: 'dashboard_wms_mos_dashlet_a2.php',
                                                data: form_data,
                                                dataType: 'html',
                                                success: function (data) {
                                                    $("#ss_i2").html(data);
                                                }
                                            });
                                            $("#ss_i3").html("<center><div id='loadingmessage'><img src='<?php echo PUBLIC_URL ?>images/ajax-loader.gif'/></div></center>");
                                            
                                            $.ajax({
                                                type: "POST",
                                                url: 'dashboard_wms_mos_dashlet_a3.php',
                                                data: form_data,
                                                dataType: 'html',
                                                success: function (data) {
                                                    $("#ss_i3").html(data);
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
                                        
                                        $('.multiselect-ui').multiselect({
                                            includeSelectAllOption: false,
                                            enableFiltering: true,
                                             enableCaseInsensitiveFiltering: true
                                        });
                                    });
                                   
//                                    $("#prov_sel").change(function () {
//                                        console.log($("#select2-chosen-1").html("Select"));
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

    </script>
</body>
</html>
