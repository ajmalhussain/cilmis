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

                <div class="row" style="margin-left:0px;margin-right: 0px;margin-bottom: 2%;" id="graph_row_1">
                    <div class="col-md-12 my_dash_cols " >
                        <div class="col-md-12 panel-body">
                            <div class="dashlet_graph" id="ss_a1" >
                                <h3 class="center">FP Procurement Details – Report
                                </h3>
                                <?php
                                $qry = " SELECT
                                fp_procurement_details.fiscal_year,
                                fp_procurement_details.planned,
                                fp_procurement_details.expenditure,
                                stakeholder.stkname,
                                tbl_locations.LocName,
                                fp_procurement_details.stk_id ,
                                fp_procurement_details.prov_id
                                FROM
                                fp_procurement_details
                                LEFT JOIN tbl_locations ON fp_procurement_details.prov_id = tbl_locations.PkLocID
                                LEFT JOIN stakeholder ON fp_procurement_details.stk_id = stakeholder.stkid
                                ORDER BY
                                    fp_procurement_details.prov_id ASC,
                                    fp_procurement_details.fiscal_year ASC
";
                                $result = mysql_query($qry);
                                if (mysql_num_rows($result) > 0) {
                                    ?>
                                    <table class="table table-bordered table-condensed">
                                        <thead>
                                            <tr>
                                                <th colspan="5" style="background-color:maroon;text-align: center;color:white;" >Pakistan Provincial Allocations for Procurement of FP Commodities (Planned vs Expenditure) – Amount in million
                                                </th>
                                            </tr>
                                            <tr style="background-color:navy;color:white;">
                                                <th>Province</th>
                                                <th>Stakeholder</th>
                                                <th>Year</th>
                                                <th>Planned</th>
                                                <th>Expenditure</th>
                                            </tr>
                                        </thead>
                                        <?php while ($row = mysql_fetch_array($result)) { ?>
                                            <tr>
                                                <td><?php
                                                    if ($row['prov_id'] == -1) {
                                                    echo 'Regions (AJ&K, GB & ICT)';
                                                    } else{
                                                    echo $row['LocName'];
                                                    }
                                                    ?></td>
                                                <td><?php
                                                    if ($row['stk_id'] == -1) {
                                                        echo 'PWD & DoH';
                                                    } else {
                                                        echo $row['stkname'];
                                                    }
                                                    ?></td>
                                                <td><?php echo $row['fiscal_year']; ?></td>
                                                <td><?php echo '$' . $row['planned']; ?></td>
                                                <td><?php echo '$' . $row['expenditure']; ?></td>
                                            </tr>
                                            <?php
                                        }
                                        ?>
                                    </table>
                                    <?php
                                }
                                ?>
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
            if ($("#prov_sel").val() == '' || $("#stakeholder").val() == '' || $("#planned").val() == '' || $("#expenditure").val() == '') {

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
                        setTimeout(reload, 2000);
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
