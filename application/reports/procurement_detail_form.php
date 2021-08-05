<!DOCTYPE html>
<?php
include("../includes/classes/Configuration.inc.php");
//Including db file
Login();
include(APP_PATH . "includes/classes/db.php");
//Including header file
include(PUBLIC_PATH . "html/header.php");
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
                                                            <label class="control-label">Fiscal Year</label>
                                                            <div class="form-group">
                                                                <select id="year" name="year" required class="form-control input-sm">
                                                        <option value="">Select Year</option>
                                                        
                                                        <?php
                                                        $max=date('Y')+10; 
                                                        for ($i = date('Y'); $i < $max; $i++) {
                                                            ?>
                                                            <option value="<?php echo $i.'-'.($i+1); ?>"><?php echo $i.'-'.($i+1); ?></option>
                                                            <?php
                                                        }
                                                        ?>
                                                    </select>
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
                                                                    tbl_locations.PkLocID,
                                                                    tbl_locations.LocName
                                                                    FROM
                                                                    tbl_locations
                                                                    WHERE
                                                                    tbl_locations.ParentID = 10
                                                                        ";
                                                                //query result
                                                                $rsprov = mysql_query($queryprov) or die();

                                                                while ($rowprov = mysql_fetch_array($rsprov)) {
                                                                    ?>
                                                                    <option value="<?php echo $rowprov['PkLocID']; ?>"><?php echo $rowprov['LocName']; ?></option>

                                                                <?php } ?>
                                                                    <option value='-1'>Regions (AJ&K, GB & ICT)</option>
                                                            </select>
                                                        </div>
                                                    </div>

                                                </td>
                                                <td class="col-md-2">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label class="control-label">Stakeholder</label>
                                                            <select  required name="stakeholder" id="stakeholder" class="form-control input-sm">
                                                                <option value="">Select</option>
                                                                <option value="-1">PWD & DoH</option>
                                                                <?php
                                                                $queryprov = "SELECT
                                                                            stakeholder.stkname,
                                                                            stakeholder.stkid
                                                                            FROM
                                                                            stakeholder
                                                                            WHERE
                                                                            stakeholder.stk_type_id = 0 AND
                                                                            stakeholder.lvl = 1 AND
                                                                            stakeholder.is_reporting = 1
                                                                        ";
                                                                //query result
                                                                $rsprov = mysql_query($queryprov) or die();

                                                                while ($rowprov = mysql_fetch_array($rsprov)) {
                                                                    ?>
                                                                    <option value="<?php echo $rowprov['stkid']; ?>"><?php echo $rowprov['stkname']; ?></option>
                                                                    <?php
                                                                }
                                                                ?>
                                                                    
                                                            </select>
                                                        </div>
                                                    </div>
                                                    </div>

                                                </td>

                                                <td class="col-md-3">
                                                    <div class="form-group">
                                                        <label class="control-label">Planned</label>
                                                        <div class="form-group ">
                                                            <input type="text" class="form-control input-sm" id="planned" name="planned" required>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="col-md-3">
                                                    <div class="form-group">
                                                        <label class="control-label">Expenditure</label>
                                                        <div class="form-group">
                                                            <input type="text" class="form-control input-sm" id="expenditure" name="expenditure" required>

                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="col-md-2">

                                                    <!--<label class="control-label">&nbsp;</label>-->
                                                    <input name="submit_btn" class="btn btn-succes" id="submit_btn" style="margin-top:8%" value="Save" type="submit">

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
                 
                <div class="row" style="margin-left:0px;margin-right: 0px;margin-bottom: 2%;display:none;" id="graph_row_1">
                    <div class="col-md-12 my_dash_cols " >
                        <div class="col-md-12 panel-body">
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
    <script type="text/javascript" src="../../public/assets/global/plugins/select2/select2.min.js"></script>

    <script type="text/javascript">
                                    $(document).on('click', '#submit_btn', function () {
                                        if ($("#prov_sel").val() == '' || $("#stakeholder").val() == '' || $("#planned").val() == ''||$("#expenditure").val()=='') {

                                        } else { 
                                             var form_data = $('form').serialize();
                                            $.ajax({
                                                type: "POST",
                                                url: 'ajax_save_procurement.php',
                                                data: form_data,
                                                dataType: 'html',
                                                success: function (data) {
                                                    
                                                    toastr.success("Data has been saved");
                                                    $("#ss_a1").html('');
                                                    setTimeout(reload, 1000); 
                                                }
                                            });
                                             

                                        }
                                    }); 
                                     
                                    $("#stakeholder , #prov_sel").change(function () { 
                                        $.ajax({
                                            type: "POST",
                                            url: 'ajax_procurement_form.php',
                                            data: {province: $("#prov_sel").val(), stakeholder: $("#stakeholder").val()},
                                            dataType: 'html',
                                            success: function (data) {
                                                 $("#graph_row_1").css("display", "block");
                                                $("#ss_a1").html(data);

                                            }
                                        });
                                    });
                                    function reload()
                                    {
                                        location.reload();
                                    }
    </script>
</body>
</html>
