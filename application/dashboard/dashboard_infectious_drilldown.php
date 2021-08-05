<!DOCTYPE html>
<?php
include("../includes/classes/Configuration.inc.php");
//Including db file
include(APP_PATH . "includes/classes/db.php");
//Including header file
include(PUBLIC_PATH . "html/header.php");

include(PUBLIC_PATH . "FusionCharts/Code/PHP/includes/FusionCharts.php");
$province = (!empty($_REQUEST['prov']) ? $_REQUEST['prov'] : '');
$from_date = (!empty($_REQUEST['from_date']) ? $_REQUEST['from_date'] : '');
$itm_arr_request = (!empty($_REQUEST['item']) ? $_REQUEST['item'] : '');
$facility = (!empty($_REQUEST['hf_type']) ? $_REQUEST['hf_type'] : '');
$dist_id = (!empty($_REQUEST['dist_id']) ? $_REQUEST['dist_id'] : '');
$so_type = (!empty($_REQUEST['type']) ? $_REQUEST['type'] : '');
$facility_filter='';
if($facility=='dist')
{
    $facility_filter=" AND stakeholder.lvl = 3";
}
else if($facility=='dhq')
{
    $facility_filter=" AND tbl_warehouse.wh_type_id=21";
}
//print_r($dist_id);exit;
?>

<style>
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
</style>
</head>
<body class="page-header-fixed page-quick-sidebar-over-content">
    <!--<div class="pageLoader"></div>-->
    <!-- BEGIN HEADER -->


    <div class="page-container">
        <?php
//Including top file
//        include PUBLIC_PATH . "html/top.php";
//Including top_im file
        include PUBLIC_PATH . "html/top_im.php";
        ?>

        <div class="">
            <div class="page-content" >
                <div class="row" style="margin-left:0px;">
                    <div class="col-md-12">
                        <div class="widget" data-toggle="">
                            <div class="widget-head">
                                <h3 class="heading">Filter by</h3>
                            </div>
                            <div class="widget-body collapse in">
                                <form name="frm" id="frm" action="" method="get" onsubmit="return false">
                                    <table width="100%">
                                        <tbody>
                                            <tr>


                                                <td class="col-md-2">
                                                    <div class="form-group">
                                                        <label class="control-label">Product</label>
                                                        <div class="form-group ">
                                                            <select required  name="product" id="product" class=" form-control input-sm">
                                                                <option value="">Select</option>
                                                                <?php
                                                                $queryprod = "SELECT DISTINCT
                                                                    itminfo_tab.itm_id,
                                                                    itminfo_tab.itm_name
                                                                    FROM
                                                                    itminfo_tab
                                                                    WHERE itm_category <> 2
                                                                    ORDER BY
                                                                    itminfo_tab.itm_name ASC
                                                                        ";
//query result
                                                                $rsprod = mysql_query($queryprod) or die();

                                                                while ($rowprov = mysql_fetch_array($rsprod)) {
//                                                                    if (!isset($_REQUEST['product'])) {
//
//                                                                        if ($rowprov['itm_id'] == 1 || $rowprov['itm_id'] == 5 || $rowprov['itm_id'] == 7 || $rowprov['itm_id'] == 8 || $rowprov['itm_id'] == 9 || $rowprov['itm_id'] == 13) {
//                                                                            $itm_arr_request[] = $rowprov['itm_id'];
//                                                                            $sel = "selected='selected'";
//                                                                        } else {
//                                                                            $sel = "";
//                                                                        }
//                                                                    }
                                                                    if ($rowprov['itm_id'] == $itm_arr_request) {
                                                                        $sel = "selected='selected'";
                                                                        $itm_name[] = $rowprov['itm_name'];
                                                                    } else {
                                                                        $sel = "";
                                                                    }
                                                                    ?>
                                                                    <option value="<?php echo $rowprov['itm_id']; ?>" <?php echo $sel; ?>><?php echo $rowprov['itm_name']; ?></option>
                                                                    <?php
                                                                }
                                                                ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>


                                                <td class="col-md-2">

                                                    <div class="form-group">
                                                        <label class="control-label">Store</label>
                                                        <div class="form-group ">
                                                            <select required  name="hf" id="hf" class=" form-control input-sm">
                                                                <?php
                                                                $queryprod = "SELECT DISTINCT
                                                                tbl_warehouse.wh_id,
                                                                tbl_warehouse.wh_name,
                                                                tbl_warehouse.wh_type_id,
                                                                tbl_warehouse.stkid
                                                                FROM
                                                                tbl_warehouse
                                                                INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
                                                                WHERE
                                                                tbl_warehouse.dist_id = $dist_id $facility_filter
                                                                        ";
                                                                $rsprod = mysql_query($queryprod) or die();

                                                                while ($rowprov = mysql_fetch_array($rsprod)) {
//                                                              
                                                                  
                                                                    ?>
                                                                    <option value="<?php echo $rowprov['wh_id']; ?>" selected><?php echo $rowprov['wh_name']; ?></option>
                                                                    <?php
                                                                }
                                                                ?>
                                                            </select>
                                                        </div>
                                                    </div>

                                                </td>
                                                <td class="col-md-2">

                                                    <div class="form-group">
                                                        <label class="control-label">Stock Status</label>
                                                        <div class="form-group ">
                                                            <select name="status" id="status" required class="form-control input-sm">
                                                                <option value="">Select</option>
                                                                <option value="so" <?php if($so_type=='so') echo 'selected';?>>Stock Out</option>
                                                                <option value="os"  <?php if($so_type=='os') echo 'selected';?>>Over Stock</option>
                                                                <option value="us"  <?php if($so_type=='us') echo 'selected';?>>Under Stock</option>
                                                                <option value="sat"  <?php if($so_type=='sat') echo 'selected';?>>Satisfactory</option>
                                                            </select>
                                                        </div>
                                                    </div>

                                                </td>
                                        <input type="hidden" name="dist_id" id="dist_id" value="<?php echo $dist_id;?>">
                                        <input type="hidden" name="from_date" id="from_date" value="<?php echo $from_date;?>">
                                         <input type="hidden" name="type" id="type" value="<?php echo $facility;?>">
                                        
                                        <td class="col-md-1">

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

                <div class="row" style="margin-left:0px;margin-right: 0px">
                    <div class="col-md-12 my_dash_cols">
                        <div class="col-md-12 my_dashlets ">
                            <div class="dashlet_graph" id="time_series_trend" href='time_series_stock_sufficiency.php'>

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

    <script language="Javascript" src="<?php echo PUBLIC_URL; ?>FusionCharts/Charts/FusionCharts.js"></script>
    <script language="Javascript" src="<?php echo PUBLIC_URL; ?>FusionCharts/themes/fusioncharts.theme.zune.js"></script>

    <script type="text/javascript">

                                    $(document).on('click', '#submit_btn', function () {
                                        if ($("#prov_sel").val() == '' || $("#level").val() == '' || $("#hf").val() == '' || $("#stakeholder").val() == '' || $("#product").val() == '') {

                                        } else {
                                            $("#time_series_trend").html("<center><div id='loadingmessage'><img src='<?php echo PUBLIC_URL ?>images/ajax-loader.gif'/></div></center>");
                                            var form_data = $('form').serialize();
                                            $.ajax({
                                                type: "POST",
                                                url: 'infectious_product_drilldown_graph.php',
                                                data: form_data,
                                                dataType: 'html',
                                                success: function (data) {
                                                    $("#time_series_trend").html(data);
                                                }
                                            });

                                        }

                                    });

//                                    $("#prov_sel").change(function () {
//
//                                        $.ajax({
//                                            type: "POST",
//                                            url: 'ajax_dropdowns_sufficiency_hf_level.php',
//                                            data: {province: $("#prov_sel").val(), type: 'type', stakeholder: $("#stakeholder").val()},
//                                            dataType: 'html',
//                                            success: function (data) {
//                                                $("#level").html(data);
////                                            alert(data);
//                                            }
//                                        });
//                                    });

                                    $(function () {
                                        $("#time_series_trend").html("<center><div id='loadingmessage'><img src='<?php echo PUBLIC_URL ?>images/ajax-loader.gif'/></div></center>");
                                        var form_data = $('form').serialize();
                                        $.ajax({
                                            type: "POST",
                                            url: 'infectious_product_drilldown_graph.php',
                                            data: form_data,
                                            dataType: 'html',
                                            success: function (data) {
                                                $("#time_series_trend").html(data);
                                            }
                                        });
//                                        loadDashlets();



                                    });

    </script>
</body>
</html>
