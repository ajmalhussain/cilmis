<?php
include("../includes/classes/AllClasses.php");
//
include(PUBLIC_PATH . "html/header.php");
?>
<script src="tableToExcel.js"></script>
</head>
<!-- END HEAD -->
<body class="page-header-fixed page-quick-sidebar-over-content">
    <div class="page-container">
        <?php include PUBLIC_PATH . "html/top_im.php"; ?>
        <div class="page-content-wrapper">
            <div class="page-content" style="min-height:353px; margin-left: 0px !important">
                <div class="row">
                    <div class="col-md-12">
                        <div class="widget" data-toggle="collapse-widget">
                            <div class="widget-body">

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="col-md-2">
                                            <div class="control-group">
                                                <label>Office Level</label>
                                                <div class="controls">
                                                    <select name="level" id="level" class="form-control input-sm">
                                                        <option value="1">National</option>
                                                        <option value="2">Provincial</option>
                                                        <option value="3">District</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2" id="province_div" style="display:none;">
                                            <div class="control-group">
                                                <label>Province/Region</label>
                                                <div class="controls">
                                                    <select name="province" id="province" class="form-control input-sm">
                                                        <?php
                                                        $qry = "SELECT
																		Province.PkLocID,
																		Province.LocName
																	FROM
																		tbl_locations AS Province
																	WHERE
																		Province.LocLvl = 2
																	AND Province.ParentID IS NOT NULL
																	ORDER BY
																		Province.PkLocID ASC";
                                                        $rsQry = mysql_query($qry) or die();
                                                        while ($row = mysql_fetch_array($rsQry)) {
                                                            ?>
                                                            <option value="<?php echo $row['PkLocID']; ?>" ><?php echo $row['LocName']; ?></option>
                                                            <?php
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-2">
                                            <div class="control-group">
                                                <label>Stakeholder</label>
                                                <div class="controls">
                                                    <select name="stakeholder" id="stakeholder" class="form-control input-sm">
                                                        <option value="">All</option>
                                                        <?php
                                                        $querystk = "SELECT DISTINCT
																			stakeholder.stkid,
																			stakeholder.stkname
																		FROM
																			tbl_warehouse
																		INNER JOIN wh_user ON tbl_warehouse.wh_id = wh_user.wh_id
																		INNER JOIN stakeholder ON tbl_warehouse.stkid = stakeholder.stkid
																		WHERE
																			stakeholder.stk_type_id IN (0, 1)
																		ORDER BY
																			stakeholder.stkorder ASC";
                                                        $rsstk = mysql_query($querystk) or die();
                                                        while ($rowstk = mysql_fetch_array($rsstk)) {
                                                            ?>
                                                            <option value="<?php echo $rowstk['stkid']; ?>"><?php echo $rowstk['stkname']; ?></option>
                                                            <?php
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="control-group">
                                                <label>&nbsp;</label>
                                                <div class="controls">
                                                    <input type="button" id="submit" value="GO" class="btn btn-primary input-sm" onclick="display_result()" >
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="widget">
                            <div class="col-lg-offset-4" style="display:none" id="div_loader"><img src="loading.gif" /></div>
                            <div id="data_div"> </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include PUBLIC_PATH . "/html/footer.php"; ?>
    <style>
        .page-content-wrapper .page-content{
            margin-left:0px !important;
        }
    </style>
    <script>
        function display_result() {
            // document.getElementById("data_div").innerHTML="Data Loading..";
            var prov_id = document.getElementById("province").value;
            var stkeholder_id = document.getElementById("stakeholder").value;
            var initial_level = document.getElementById("level").value;
            $.ajax({
                type: "POST",
                url: "ajax.php",
                data: {provinceId: prov_id, stakeholder_id: stkeholder_id, first_level: initial_level},
                dataType: 'html',
                beforeSend: function () {

                    document.getElementById("data_div").innerHTML = "";
                    $('#div_loader').show();
                },
                success: function (data)
                {
                    $('#data_div').html(data);
                },
                complete: function () {
                    $('#div_loader').hide();
                }
            });
        }
        $(function () {
            $('#level').change(function () {
                officeType($(this).val());
            });
            //        $('#province').change(function() {
            //            var provId = $(this).val();
            //        });

        });
        function officeType(officeLevel)
        {
            if (parseInt(officeLevel) == 1)
            {
                $('#province_div').hide();
            } else if (parseInt(officeLevel) == 2)
            {
                $('#province_div').show();
            } else if (parseInt(officeLevel) == 3)
            {
                $('#province_div').show();
            }
        }

    </script>
</body>
</html>