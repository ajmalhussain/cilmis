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
<link rel="stylesheet" type="text/css" href="../../public/assets/global/plugins/select2/select2.css"/>
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
                                                 
                                                <td class="col-md-2 ">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label class="control-label">Stock As On:</label>
                                                            <div class="form-group">
                                                                <input type="text" name="to_date" id="to_date"  class="form-control input-sm" value="<?php
                                                                if (isset($_REQUEST['to_date'])) {
                                                                    echo date('Y-m-d', strtotime($to_date));
                                                                } else {

                                                                    echo date('Y-m-d');
                                                                }
                                                                ?>" required readonly="true">
                                                            </div>
                                                        </div>
                                                    </div>

                                                </td>
                                                <td class="col-md-2">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label class="control-label">Product</label>
                                                            <br>
                                                            <select required  name="product" id="product" class="input-large select2me">
                                                                <option value="">Select</option>
                                                                <?php
                                                                //fetching only the relevant products
                                                                 $qry = "
                                                                    SELECT
                                                                            distinct itminfo_tab.itm_id,
                                                                            itminfo_tab.itm_name,
                                                                            itminfo_tab.generic_name
                                                                    FROM
                                                                    itminfo_tab
                                                                    INNER JOIN stock_batch ON itminfo_tab.itm_id = stock_batch.item_id
                                                                    INNER JOIN tbl_warehouse ON stock_batch.wh_id = tbl_warehouse.wh_id
                                                                    INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
                                                                    WHERE tbl_warehouse.prov_id = 1 AND
                                                                    stakeholder.lvl = 2 AND tbl_warehouse.is_allowed_im = 1 AND
                                                                    tbl_warehouse.stkid IN (7,145)
                                                                    ORDER BY
                                                                            itminfo_tab.itm_name ASC
 


                                                                    ";
                                                                $dist_res = mysql_query($qry);
                                                                while ($row = mysql_fetch_array($dist_res)) {
                                                                    ?>

                                                                    <option value="  <?php echo $row['itm_id']; ?>"><?php echo $row['itm_name'].' ['.$row['generic_name'].']'; ?></option>
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
                <div class="row" style="margin-left:0px;margin-right: 0px;display: none;" id="graph_row_1">
                    <div class="col-md-12 my_dash_cols panel panel-default" >
                        <div class="col-md-12 my_dashlets panel-body" >
                            <div class="dashlet_graph" id="ss_a3" href='polar_graph_test.php'>

                            </div>
                        </div>

                    </div>
                    <div class="col-md-12 my_dash_cols panel panel-default" >
                        <div class="col-md-12 my_dashlets panel-body" >
                            
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
                                    if ($("#product").val() == '') {

                                    } else {
                                        $("#graph_row_1").css("display", "block");
                                        $("#filter_note").css("display", "none");

                                        $("#ss_a3").html("<center><div id='loadingmessage'><img src='<?php echo PUBLIC_URL ?>images/ajax-loader.gif'/></div></center>");
                                        var form_data = $('form').serialize();
                                        $.ajax({
                                            type: "POST",
                                            url: 'polar_graph_test.php',
                                            data: form_data,
                                            dataType: 'html',
                                            success: function (data) {
                                                $("#ss_a3").html(data);
                                            }
                                        });

                                    }
                                });
                                $(function () {
                                     
                                     $('#to_date').datepicker({
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
