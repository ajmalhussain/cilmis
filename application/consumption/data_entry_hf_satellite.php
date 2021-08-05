<?php
include("../includes/classes/AllClasses.php");
//Including header file
include(PUBLIC_PATH . "html/header.php");
//Including top_im file
include PUBLIC_PATH . "html/top_im.php";

//get Do
if (isset($_REQUEST['Do']) && !empty($_REQUEST['Do'])) {
    $temp = urldecode($_REQUEST['Do']);
    $tmpStr = substr($temp, 1, strlen($temp) - 1);
    $temp = explode("|", $tmpStr);
    // Warehouse ID
    $wh_id = $temp[0] - 77000;
    //set wh_id
    $objwharehouse_user->m_wh_id = $wh_id;
}
//check user_id
if (isset($_SESSION['user_id'])) {
    //get user_id
    $userid = $_SESSION['user_id'];
    //set user_id
    $objwharehouse_user->m_npkId = $userid;
    //GetProvinceIdByIdc
    $result_province = $objwharehouse_user->GetProvinceIdByIdc();
} else {
    //Display message
    echo "user not login or timeout";
}
//check e
if (isset($_GET['e']) && $_GET['e'] == 'ok') {
    ?>
    <script type="text/javascript">
        function RefreshParent() {
            if (window.opener != null && !window.opener.closed) {
                window.opener.location.reload();
            }
        }
        window.close();
        RefreshParent();
        //window.onbeforeunload = RefreshParent;
    </script>
    <?php
    exit;
}
//initialize variable
//isReadOnly 
$isReadOnly = '';
$ref_tot = array();
//style
$style = '';
//check if im_open
if ($_SESSION['is_allowed_im'] == 1) {
    $isReadOnly = 'readonly="readonly"';
    $style = 'style="background:#CCC"';
} else {
    $isReadOnly = '';
    $style = '';
}
?>
<link href="<?php echo PUBLIC_URL; ?>css/styles.css" rel="stylesheet" type="text/css"/>
<script src="<?php echo PUBLIC_URL; ?>assets/global/plugins/jquery-1.11.0.min.js" type="text/javascript"></script> 
    <script>
                                    $(document).ready(function() {

                                        $('input[type="text"]').each(function() {
                                            if ($(this).val() == '')
                                            {
                                                $(this).val(0);
                                            }
                                        });

                                        $('input[type="text"]').change(function(e) {
                                            if ($(this).val() == '')
                                            {
                                                $(this).val('0');
                                            }
                                        });
                                        $('input[type="text"]').focus(function(e) {
                                            if ($(this).val() == '0')
                                            {
                                                $(this).val('');
                                            }
                                        });
                                        $('input[type="text"]').focusout(function(e) {
                                            if ($(this).val() == '')
                                            {
                                                $(this).val('0');
                                            }
                                        });
                                        $('input[type="text"]').keydown(function(e) {
                                            if (e.shiftKey || e.ctrlKey || e.altKey) { // if shift, ctrl or alt keys held down
                                                e.preventDefault();         // Prevent character input
                                            } else {
                                                var n = e.keyCode;
                                                if (!((n == 8)              // backspace
                                                        || (n == 9)                // Tab
                                                        || (n == 46)                // delete
                                                        || (n >= 35 && n <= 40)     // arrow keys/home/end
                                                        || (n >= 48 && n <= 57)     // numbers on keyboard
                                                        || (n >= 96 && n <= 105))   // number on keypad
                                                        ) {
                                                    e.preventDefault();     // Prevent character input
                                                }
                                            }
                                        });
                                    });

                                    function submitForm()
                                    {
                                        $('#saveBtn').attr('disabled', false);
                                        $('#errMsg').hide();
                                        $('#saveBtn').attr('disabled', true);
                                        $.ajax({
                                            url: 'data_entry_hf_satellite_action.php',
                                            data: $('#frmF7').serialize(),
                                            type: 'POST',
                                            dataType: 'json',
                                            success: function(data) {
                                                if (data.resp == 'err')
                                                {
                                                    $('#errMsg').html(data.msg).show();
                                                }
                                                else if (data.resp == 'ok')
                                                {
                                                    function RefreshParent() {
                                                        if (window.opener != null && !window.opener.closed) {
                                                            window.opener.location.reload();
                                                        }
                                                    }
                                                    window.close();
                                                    RefreshParent();
                                                }
                                            }
                                        })
                                    }

    </script>
</head>
<body class="page-header-fixed page-quick-sidebar-over-content">
    <div class="modal"></div>
    <!-- BEGIN HEADER -->
    <div class="page-container">
        <div class="page-content-wrapper">
            <div class="page-content" style="margin-left:0px !important">
                <?php
                $wh_id = "";
                //check Do
                if (isset($_REQUEST['Do']) && !empty($_REQUEST['Do'])) {
                    //get Do
                    $temp = urldecode($_REQUEST['Do']);
                    $tmpStr = substr($temp, 1, strlen($temp) - 1);
                    $temp = explode("|", $tmpStr);

                    //****************************************************************************
                    // Warehouse ID
                    $wh_id = $temp[0] - 77000;
                    //Report Date
                    $RptDate = $temp[1];
                    //if value=1 then new report
                    $isNewRpt = $temp[2];
                    $tt = explode("-", $RptDate);
                    //Reprot year
                    $yy = $tt[0];
                    //report Month
                    $mm = $tt[1];

                    // Check level
                    //gets 
                    //stakeholder.lvl,
                    //tbl_warehouse.hf_type_id,
                    //tbl_warehouse.prov_id
                    $qryLvl = mysql_fetch_array(mysql_query("SELECT
														stakeholder.lvl,
														tbl_warehouse.hf_type_id,
														tbl_warehouse.prov_id
													FROM
														tbl_warehouse
													INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
													WHERE
														tbl_warehouse.wh_id = $wh_id"));
                    $hfTypeId = $qryLvl['hf_type_id'];
                    $whProvId = $qryLvl['prov_id'];
                    //query check data
                    $checkData = "SELECT
								tbl_hf_satellite_data.reporting_date
							FROM
								tbl_hf_satellite_data
							WHERE
								tbl_hf_satellite_data.warehouse_id = $wh_id
							ORDER BY
								tbl_hf_satellite_data.reporting_date ASC
							LIMIT 1";
                    //result
                    $checkDataRes = mysql_fetch_array(mysql_query($checkData));
                    //openOB 
                    $openOB = ($checkDataRes['reporting_date'] == $RptDate) ? '' : $checkDataRes['reporting_date'];
                    //month
                    $month = date('M', mktime(0, 0, 0, $mm, 1));

                    //****************************************************************************
                    $objwarehouse->m_npkId = $wh_id;
                    //GetStkIDByWHId
                    $stkid = $objwarehouse->GetStkIDByWHId($wh_id);
                    //GetWarehouseNameById
                    $whName = $objwarehouse->GetWarehouseNameById($wh_id);
                    echo "<h3 class=\"page-title row-br-b-wp\">Satellite Camp - " . $whName . " <span class=\"green-clr-txt\">(" . $month . ' ' . $yy . ")</span> </h3>";
                    //If new report
                    if ($isNewRpt == 1) {
                        //Get Previous Month Report Date
                        $PrevMonthDate = $objReports->GetPreviousMonthReportDate($RptDate);
                    } else {
                        $PrevMonthDate = $RptDate;
                    }
                    ?>

                    <form name="frmF7" id="frmF7" method="post">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="portlet box green-meadow">
                                <div class="portlet-title">
                                        <div class="caption">
                                                Satellite Camps Data Entry
                                        </div>
                                        <div class="tools">
                                                <a href="javascript:;" class="collapse" data-original-title="" title=""></a>
                                                </a>
                                        </div>
                                </div>
                                <div class="portlet-body">
                                <div id="errMsg"></div>
                                <table class="table table-bordered table-condensed">
                                    <tr>
                                        <th rowspan="2" class="text-center">S.No.</th>
                                        <th rowspan="2" class="text-center">Article</th>
                                        <th rowspan="2" class="text-center">Issued</th>
                                        <th colspan="2" class="text-center">Cases/Clients</th>
                                        <th rowspan="2" class="text-center">Removals</th>
                                    </tr>
                                    <tr>
                                        <th class="text-center">New</th>
                                        <th class="text-center">Old</th>
                                    </tr>
                                    <?php
                                    //query
                                    //gets
                                    //all from itminfo_tab
                                    $rsTemp1 = mysql_query("SELECT * FROM `itminfo_tab` WHERE `itm_status`=1 AND `itm_id` IN (SELECT `Stk_item` FROM `stakeholder_item` WHERE `stkid` =$stkid) ORDER BY `frmindex`");
                                    $SlNo = 1;
                                    $fldIndex = 0;
                                    $removals = 0;
                                    //loop
                                    while ($rsRow1 = mysql_fetch_array($rsTemp1)) {
                                        //$SlNo = ((strlen($SlNo) < 2) ? $SlNo : $SlNo);
                                        //query
                                        //gets
                                        //all from tbl_hf_satellite_data
                                        $qry = "SELECT * FROM tbl_hf_satellite_data WHERE `warehouse_id`='" . $wh_id . "' AND reporting_date='" . $PrevMonthDate . "' AND `item_id`='$rsRow1[itm_id]'";
                                        $rsTemp3 = mysql_query($qry);
                                        $rsRow2 = mysql_fetch_array($rsTemp3);

                                        $add_date = $rsRow2['created_date'];
                                        // if new report
                                        if ($isNewRpt == 1) {
                                            //check itm_category
                                            if ($rsRow1['itm_category'] == 1) {
                                                $wh_issue_up = 0;
                                                $new = 0;
                                                $old = 0;
                                                
                                                $removal_readonly=true;
                                                if($rsRow1['method_type']=='IUD' || $rsRow1['method_type']=='Implant') {
                                                    $removal_readonly = false;
                                                }
                                                ?>
                                                <tr>
                                                    <td class="text-center"><?php echo $SlNo++; ?></td>
                                                    <td>
                                                        <?php echo $rsRow1['itm_name']; ?>
                                                        <input type="hidden" name="flitmrec_id[]" value="<?php echo $rsRow1['itm_id']; ?>">
                                                        <input type="hidden" name="flitm_category[]" value="<?php echo $rsRow1['itm_category']; ?>">
                                                    </td>
                                                    <td><input class="form-control input-sm text-right" autocomplete="off" name="FLDIsuueUP<?php echo $rsRow1['itm_id']; ?>" id="FLDIsuueUP<?php echo $rsRow1['itm_id']; ?>" value="<?php echo $wh_issue_up; ?>" type="text"  size="8" maxlength="10"></td>
                                                    <td><input class="form-control input-sm text-right" autocomplete="off" type="text" name="FLDnew<?php echo $rsRow1['itm_id']; ?>" id="FLDnew<?php echo $rsRow1['itm_id']; ?>" size="8" maxlength="10"  value="<?php echo $new; ?>"></td>
                                                    <td><input class="form-control input-sm text-right" autocomplete="off" type="text" name="FLDold<?php echo $rsRow1['itm_id']; ?>" id="FLDold<?php echo $rsRow1['itm_id']; ?>" size="8" maxlength="10"  value="<?php echo $old; ?>"></td>
                                                    <td><input class="form-control input-sm text-right" autocomplete="off" type="text" name="Removals<?php echo $rsRow1['itm_id']; ?>" id="Removals<?php echo $rsRow1['itm_id']; ?>" size="8" maxlength="10"  value="<?php echo $removals; ?>" <?=(($removal_readonly)?' readonly ':'')?>></td>
                                                </tr>
                                                <?php //if itm_category = 2 
                                            } else if ($rsRow1['itm_category'] == 2) {
                                                //pk_id
                                                $surgeyArr[$rsRow1['itm_id']]['pk_id'] = $rsRow2['pk_id'];
                                                //name
                                                $surgeyArr[$rsRow1['itm_id']]['name'] = $rsRow1['itm_name'];
                                                //category
                                                $surgeyArr[$rsRow1['itm_id']]['category'] = $rsRow1['itm_category'];
                                                //cases
                                                $surgeyArr[$rsRow1['itm_id']]['cases'] = $wh_issue_up;
                                            }
                                        }
                                        //Old report Edit Mode
                                        else {
                                            
                                             $removal_readonly=true;
                                                if($rsRow1['method_type']=='IUD' || $rsRow1['method_type']=='Implant') {
                                                    $removal_readonly = false;
                                                }
                                                
                                                
                                            if ($rsRow1['itm_category'] == 1) {
                                                
                                                ?>
                                                <tr>
                                                    <td class="text-center"><?php echo $SlNo++; ?></td>
                                                    <td>
                                                        <?php echo $rsRow1['itm_name']; ?>
                                                        <input type="hidden" name="flitmrec_id[]" value="<?php echo $rsRow1['itm_id']; ?>">
                                                        <input type="hidden" name="flitm_category[]" value="<?php echo $rsRow1['itm_category']; ?>">
                                                    </td>
                                                    <td><input class="form-control input-sm text-right" autocomplete="off" name="FLDIsuueUP<?php echo $rsRow1['itm_id']; ?>" id="FLDIsuueUP<?php echo $rsRow1['itm_id']; ?>" value="<?php echo $rsRow2['issue_balance']; ?>" type="text" size="8" maxlength="10"></td>
                                                    <td><input class="form-control input-sm text-right" autocomplete="off"  type="text" name="FLDnew<?php echo $rsRow1['itm_id']; ?>" id="FLDnew<?php echo $rsRow1['itm_id']; ?>" size="8" maxlength="10"  value="<?php echo $rsRow2['new']; ?>"></td>
                                                    <td><input class="form-control input-sm text-right" autocomplete="off"  type="text" name="FLDold<?php echo $rsRow1['itm_id']; ?>" id="FLDold<?php echo $rsRow1['itm_id']; ?>" size="8" maxlength="10"  value="<?php echo $rsRow2['old']; ?>"></td>
                                                    <td><input class="form-control input-sm text-right" autocomplete="off" type="text" name="Removals<?php echo $rsRow1['itm_id']; ?>" id="Removals<?php echo $rsRow1['itm_id']; ?>" size="8" maxlength="10"  value="<?php echo $rsRow2['removals']?>" <?=(($removal_readonly)?' readonly ':'')?>></td>
                                                </tr>
                                                <?php
                                            } else if ($rsRow1['itm_category'] == 2) {
                                                //pk_id
                                                $surgeyArr[$rsRow1['itm_id']]['pk_id'] = $rsRow2['pk_id'];
                                                //name
                                                $surgeyArr[$rsRow1['itm_id']]['name'] = $rsRow1['itm_name'];
                                                //category
                                                $surgeyArr[$rsRow1['itm_id']]['category'] = $rsRow1['itm_category'];
                                                //cases
                                                $surgeyArr[$rsRow1['itm_id']]['cases'] = $rsRow2['issue_balance'];
                                            }
                                        }
                                        
                                        $fldIndex = $fldIndex + 13;
                                    }
                                    //free result
                                    mysql_free_result($rsTemp1);
                                    ?>
                                </table>
                            </div>
                            </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <?php
                                if ($hfTypeId == 4 || $hfTypeId == 5) {
                                    ?>
                                    <div class="col-md-8"  >
                                        <div class="col-md-6" style="padding:1px !important;">

                                            <div class="portlet box green-meadow">
                                            <div class="portlet-title">
                                                    <div class="caption">
                                                            Surgery Cases(Reffered)
                                                    </div>
                                                    <div class="tools">
                                                            <a href="javascript:;" class="collapse" data-original-title="" title=""></a>
                                                            </a>
                                                    </div>
                                            </div>
                                            <div class="portlet-body">
                                            <input type="hidden" name="hf_type_id" id="hf_type_id"  value="<?php echo $hfTypeId; ?>">
                                                <table class="table table-bordered table-condensed">
                                                <tr>
                                                    <th>Reffered By</th>
                                                    <?php
                                                    $counter = 0;
                                                    //get data from surgeyArr
                                                    foreach ($surgeyArr as $itemid => $data) {
                                                        $counter++;
                                                        ?>
                                                        <th><?php echo $data['name']; ?></th>
                                                        <?php
                                                    }
                                                    //check hfTypeId 
                                                    if ($hfTypeId == 4) {
                                                        $and = " AND tbl_hf_type.pk_id != 5";
                                                    } else if ($hfTypeId == 5) {
                                                        $and = " AND tbl_hf_type.pk_id != 4";
                                                    }
                                                    //query
                                                    //gets
                                                    //tbl_hf_type.pk_id,
                                                    //tbl_hf_type.hf_type
                                                    $qry = "SELECT
                                                                                                    tbl_hf_type.pk_id,
                                                                                                    tbl_hf_type.hf_type
                                                                                            FROM
                                                                                                    tbl_hf_type
                                                                                            INNER JOIN tbl_hf_type_province ON tbl_hf_type.pk_id = tbl_hf_type_province.hf_type_id
                                                                                            WHERE
                                                                                                    tbl_hf_type_province.province_id = " . $result_province['prov_id'] . "
                                                                                            AND tbl_hf_type_province.stakeholder_id = 1 $and";
                                                    //result
                                                    $rs_arr = mysql_query($qry);
                                                    $hf_t_arr = array();
                                                    while ($arr1 = mysql_fetch_array($rs_arr)) {
                                                        $hf_t_arr[$arr1['pk_id']]=$arr1['hf_type'];
                                                        ?>
                                                    </tr>
                                                    <td><?php echo $arr1['hf_type']; ?></td>
                                                    <input type="hidden" name="hf_type_id[]" value="<?php echo $arr1['pk_id']; ?>">
                                                    <?php
                                                    //get data from surgeyArr
                                                    
                                                    foreach ($surgeyArr as $itemid => $data) {
                                                        if ($isNewRpt == 1) {
                                                            $hf_pk_id = 0;
                                                            $hf_data_pk = 0;
                                                        } else {
                                                            $hf_pk_id = $arr1['pk_id'];
                                                            $hf_data_pk = $data['pk_id'];
                                                        }
                                                        //mcQry 
                                                        //gets
                                                        //tbl_hf_satellite_data_reffered_by.pk_id,
                                                        //tbl_hf_satellite_data_reffered_by.hf_data_id,
                                                        //tbl_hf_satellite_data_reffered_by.hf_type_id,
                                                        //tbl_hf_satellite_data_reffered_by.ref_surgeries,
                                                        //tbl_hf_satellite_data_reffered_by.static,
                                                        //tbl_hf_satellite_data_reffered_by.camp
                                                        $mcQry = "SELECT
                                                                            tbl_hf_satellite_data_reffered_by.pk_id,
                                                                            tbl_hf_satellite_data_reffered_by.hf_data_id,
                                                                            tbl_hf_satellite_data_reffered_by.hf_type_id,
                                                                            tbl_hf_satellite_data_reffered_by.ref_surgeries,
                                                                            tbl_hf_satellite_data_reffered_by.static,
                                                                            tbl_hf_satellite_data_reffered_by.camp
                                                                    FROM
                                                                            tbl_hf_satellite_data_reffered_by
                                                                    WHERE
                                                                            tbl_hf_satellite_data_reffered_by.hf_type_id = $hf_pk_id
                                                                    AND tbl_hf_satellite_data_reffered_by.hf_data_id = $hf_data_pk";
                                                        //result
                                                        $mcRow = mysql_fetch_array(mysql_query($mcQry));
                                                        ?>
                                                        <td><input class="form-control input-sm text-right reffered<?php echo $itemid; ?>" type="text" name="reffered<?php echo $itemid; ?><?php echo $arr1['pk_id']; ?>" value="<?php echo $mcRow['ref_surgeries']; ?>"   size="8" maxlength="10" /></td>
                                                        <?php
                                                        @$ref_tot[$itemid]+=$mcRow['ref_surgeries'];
                                                    }
                                                    ?>
                                                    </tr>
                                                    <?php
                                                }
                                                ?>
                                                <tr>
                                                    <td>Gross Total /Net Total</td>
                                                    <?php
                                                    //getting data from surgeyArr 
                                                    foreach ($surgeyArr as $itemid => $data) {
                                                        ?>
                                                    <input type="hidden" name="flitmrec_id[]" value="<?php echo $itemid; ?>">
                                                    <input type="hidden" name="flitm_category[]" value="<?php echo $data['category']; ?>">
                                                    <td><input class="form-control input-sm text-right" readonly type="text" name="FLDIsuueUP<?php echo $itemid; ?>" id="FLDIsuueUP<?php echo $itemid; ?>" value="<?php echo $ref_tot[$itemid]; ?>" /></td>
                                                    <?php
                                                }
                                                ?>
                                                </tr>
                                            </table>
                                            </div>
                                            </div>
                                        </div>
                                         <div class="col-md-6" style="padding:1px !important;">
                                        <div class="portlet box green-meadow">
						<div class="portlet-title">
							<div class="caption">
								Cases of Implants
							</div>
							<div class="tools">
								<a href="javascript:;" class="collapse" data-original-title="" title="">
								</a>
								
							</div>
						</div>
						<div class="portlet-body">
							 <input type="hidden" name="hf_type_id" id="hf_type_id"  value="<?php echo $hfTypeId; ?>">
                                        <table class="table table-condensed  table-bordered">
                                            <tr>

                                                <th> </th>
                                                <th>Referred</th>
                                                <th>Performed</th>
                                            </tr>
                                                <?php
                                                $imp_arr = array();
                                                $imp_arr['total']['referred']=0;
                                                $imp_arr['total']['performed']=0;
                                                $counter = 0;
                                                if ($hfTypeId == 4) {
                                                    $and = " AND tbl_hf_type.pk_id != 5";
                                                } else if ($hfTypeId == 5) {
                                                    $and = " AND tbl_hf_type.pk_id != 4";
                                                }
                                                
                                              
                                                $iQry = "SELECT
                                                        tbl_hf_data_implants.*
                                                        FROM
                                                        tbl_hf_data_implants
                                                        WHERE
                                                        tbl_hf_data_implants.wh_id = '$wh_id' AND
                                                        tbl_hf_data_implants.reporting_date = '$RptDate' ";
                                                $rsss = mysql_query($iQry);
                                                while($iRow = mysql_fetch_assoc($rsss)){
                                                    $imp_arr[$iRow['hf_type']]['referred']=$iRow['referred'];
                                                    $imp_arr[$iRow['hf_type']]['performed']=$iRow['performed'];
                                                    @$imp_arr['total']['referred']+=$iRow['referred'];
                                                    @$imp_arr['total']['performed']+=$iRow['performed'];
                                                }
//                                                echo '<prE>xxx';print_r($imp_arr);print_r($hf_t_arr);exit;
                                                
                                                foreach($hf_t_arr as $htype_id => $htype_name){
                                                    ?>
                                                <tr>
                                                <td><?php echo $htype_name; ?></td>
                                                <input type="hidden" name="hf_type_id[]" value="<?php echo $htype_id; ?>">
                                                <td><input class="form-control input-sm text-right imp_ref" autocomplete="off" type="text" name="implants_reffered[<?=$htype_id?>]" value="<?php echo @$imp_arr[$htype_id]['referred']; ?>"   size="8" maxlength="10" /></td>
                                                <td><input class="form-control input-sm text-right imp_perf" autocomplete="off" type="text" name="implants_performed[<?=$htype_id?>]" value="<?php echo @$imp_arr[$htype_id]['performed']; ?>"   size="8" maxlength="10" /></td>
                                                  
                                                </tr>
                                                <?php
                                            }
                                            ?>
                                            <tr>
                                                <td> Total</td>
                                                <td><input class="form-control input-sm text-right" readonly type="text"   id="imp_ref_total" value="<?php echo $imp_arr['total']['referred']; ?>" /></td>
                                                <td><input class="form-control input-sm text-right" readonly type="text"   id="imp_perf_total" value="<?php echo $imp_arr['total']['performed']; ?>" /></td>
                                              
                                            </tr>
                                        </table>
						</div>
					</div>
                                    </div> 
                                    <div class="col-md-12" style="padding:1px !important;top: -20px !important;">
                                        <div class="portlet box green-meadow">
                                            <div class="portlet-title">
                                                    <div class="caption">
                                                             Remarks
                                                    </div>
                                                    <div class="tools">
                                                            <a href="javascript:;" class="collapse" data-original-title="" title=""></a>
                                                            </a>
                                                    </div>
                                            </div>
                                            <div class="portlet-body">
                                               <textarea class="form-control" maxlength="500" name="remarks" id="remarks" rows="1" placeholder="Your remarks / comments here ."></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    </div>
                                    <div class="col-md-4" style="padding:1px !important;">
                                        
                                        <div class="portlet box green-meadow">
                                        <div class="portlet-title">
                                                <div class="caption">
                                                        Surgery Cases(Performed)
                                                </div>
                                                <div class="tools">
                                                        <a href="javascript:;" class="collapse" data-original-title="" title=""></a>
                                                        </a>
                                                </div>
                                        </div>
                                        <div class="portlet-body">
                                        <table class="table table-bordered table-condensed">
                                            <tr>
                                                <td>&nbsp;</td>
                                                <?php
                                                $counter = 0;
                                                //getting data from surgeyArr
                                                foreach ($surgeyArr as $itemid => $data) {
                                                    $counter++;
                                                    ?>
                                                    <th><?php echo $data['name']; ?></th>
                                                    <?php
                                                }

                                                $arr = array('Static Center', 'Camp Cases');
                                                $counter = 1;
                                                //loop
                                                foreach ($arr as $val) {
                                                    ?>
                                                </tr>
                                                <td><?php echo $val; ?></td>
                                                <?php
                                                //getting data from surgeyArr
                                                foreach ($surgeyArr as $itemid => $data) {
                                                    if ($isNewRpt == 1) {
                                                        $hf_data_pk = 0;
                                                    } else {
                                                        $hf_data_pk = $data['pk_id'];
                                                    }
                                                    //query
                                                    //gets
                                                    //male_static
                                                    //male_camp
                                                    $mcQry1 = "SELECT
														tbl_hf_satellite_data_reffered_by.static AS male_static,
														tbl_hf_satellite_data_reffered_by.camp AS male_camp
													FROM
														tbl_hf_satellite_data_reffered_by
													WHERE
														tbl_hf_satellite_data_reffered_by.hf_data_id = $hf_data_pk ";
                                                    //result
                                                    $mcRow1 = mysql_fetch_array(mysql_query($mcQry1));
                                                    //query
                                                    //gets
                                                    //female_static
                                                    //female_camp
                                                    $mcQry2 = "SELECT
														tbl_hf_satellite_data_reffered_by.static AS female_static,
														tbl_hf_satellite_data_reffered_by.camp AS female_camp
													FROM
														tbl_hf_satellite_data_reffered_by
													WHERE
														tbl_hf_satellite_data_reffered_by.hf_data_id = $hf_data_pk ";
                                                    //result
                                                    $mcRow2 = mysql_fetch_array(mysql_query($mcQry2));
                                                    //check itemid
                                                    if ($counter == 1 && $itemid == '31') {
                                                        $static_camp = $mcRow1['male_static'];
                                                        $total_male = $mcRow1['male_static'];
                                                    } else if ($counter == 1 && $itemid = '32') {
                                                        $static_camp = $mcRow2['female_static'];
                                                        $total_female = $mcRow2['female_static'];
                                                    } else if ($counter == 2 && $itemid == '31') {
                                                        $static_camp = $mcRow1['male_camp'];
                                                        $total_male += $mcRow1['male_camp'];
                                                    } else if ($counter == 2 && $itemid = '32') {
                                                        $static_camp = $mcRow2['female_camp'];
                                                        $total_female += $mcRow2['female_camp'];
                                                    }
                                                    ?>
                                                    <td><input type="text" CLASS="form-control input-sm text-right totalStaticCampMale<?php echo $itemid; ?>" name="staticCamp<?php echo $itemid; ?>[]" value="<?php echo $static_camp; ?>" size="8" maxlength="10" /></td>
                                                    <?php
                                                }
                                                ?>
                                                </tr>
                                                <?php
                                                $counter++;
                                            }
                                            ?>
                                            <tr>
                                                <td>Gross Total /Net Total</td>
                                                <td><input class="form-control input-sm text-right" readonly type="text" id="totalStaticCampMale" value="<?php echo $total_male; ?>" /></td>
                                                <td><input class="form-control input-sm text-right" readonly type="text" id="totalStaticCampFemale" value="<?php echo $total_female; ?>" /></td>
                                            </tr>
                                        </table>
                                        </div>
                                        </div>
                                    </div>
                                    <?php
                                } else {
                                    ?>
                                    <div class="col-md-8" ">
                                    <div class="col-md-6" style="padding:1px !important;">
                                       
                                        
                                        <div class="portlet box green-meadow">
                                        <div class="portlet-title">
                                                <div class="caption">
                                                         Surgery Cases(Reffered)
                                                </div>
                                                <div class="tools">
                                                        <a href="javascript:;" class="collapse" data-original-title="" title=""></a>
                                                        </a>
                                                </div>
                                        </div>
                                        <div class="portlet-body">
                                        <input type="hidden" name="hf_type_id" id="hf_type_id"  value="<?php echo $hfTypeId; ?>">
                                            <table class="table table-bordered table-condensed">
                                                <tr>
                                                    <?php
                                                    $counter = 0;
                                                    //getting data from surgeyArr
                                                    foreach ($surgeyArr as $itemid => $data) {
                                                        $counter++;
                                                        ?>
                                                        <th><?php echo $data['name']; ?></th>
                                                        <?php
                                                    }
                                                    ?>
                                                </tr>
                                                <tr>
                                                    <?php
                                                    $counter = 0;
                                                    //getting data from surgeyArr
                                                    foreach ($surgeyArr as $itemid => $data) {
                                                        $counter++;
                                                        ?>
                                                        <td>
                                                            <input type="hidden" name="flitmrec_id[]" value="<?php echo $itemid; ?>">
                                                            <input type="hidden" name="flitm_category[]" value="<?php echo $data['category']; ?>">
                                                            <input class="form-control input-sm text-right" autocomplete="off" name="FLDIsuueUP<?php echo $itemid; ?>" id="FLDIsuueUP<?php echo $itemid; ?>" value="<?php echo $data['cases']; ?>" type="text" size="8" maxlength="10" />
                                                        </td>
                                                        <?php
                                                    }
                                                    ?>
                                                </tr>
                                            </table>
                                        </div>
                                        </div>
                                    </div>
                                         
                                    <div class="col-md-6" style="padding:1px !important;">
                                        <div class="portlet box green-meadow">
						<div class="portlet-title">
							<div class="caption">
								Cases of Implants
							</div>
							<div class="tools">
								<a href="javascript:;" class="collapse" data-original-title="" title="">
								</a>
								
							</div>
						</div>
						<div class="portlet-body">
							 <input type="hidden" name="hf_type_id" id="hf_type_id"  value="<?php echo $hfTypeId; ?>">
                                        <table class="table table-condensed  table-bordered">
                                            <tr>

                                                <th>Referred</th>
                                            </tr>
                                                <?php
                                                $imp_arr = array();
                                                $imp_arr['total']['referred']=0;
                                                $imp_arr['total']['performed']=0;
                                                $counter = 0;
                                                if ($hfTypeId == 4) {
                                                    $and = " AND tbl_hf_type.pk_id != 5";
                                                } else if ($hfTypeId == 5) {
                                                    $and = " AND tbl_hf_type.pk_id != 4";
                                                }
                                                
                                              
                                                $iQry = "SELECT
                                                        tbl_hf_data_implants.*
                                                        FROM
                                                        tbl_hf_data_implants
                                                        WHERE
                                                        tbl_hf_data_implants.wh_id = '$wh_id' AND
                                                        tbl_hf_data_implants.reporting_date = '$RptDate' ";
                                                $rsss = mysql_query($iQry);
                                                while($iRow = mysql_fetch_assoc($rsss)){
                                                    $imp_arr[$iRow['hf_type']]['referred']=$iRow['referred'];
                                                    $imp_arr[$iRow['hf_type']]['performed']=$iRow['performed'];
                                                    @$imp_arr['total']['referred']+=$iRow['referred'];
                                                    @$imp_arr['total']['performed']+=$iRow['performed'];
                                                }
//                                                echo '<prE>xxx';print_r($imp_arr);print_r($hf_t_arr);exit;
                                                
                                                    ?>
                                                <tr>
                                                <td><input class="form-control input-sm text-right imp_ref" autocomplete="off" type="text" name="implants_reffered[<?=$hfTypeId?>]" value="<?php echo @$imp_arr[$hfTypeId]['referred']; ?>"   size="8" maxlength="10" /></td>
                                                  
                                                </tr>
                                              
                                        </table>
						</div>
					</div>
                                    </div> 
                                
                                    
                                    <div class="col-md-12" style="padding:1px !important; ">
                                        <div class="portlet box green-meadow">
                                            <div class="portlet-title">
                                                    <div class="caption">
                                                             Remarks
                                                    </div>
                                                    <div class="tools">
                                                            <a href="javascript:;" class="collapse" data-original-title="" title=""></a>
                                                            </a>
                                                    </div>
                                            </div>
                                            <div class="portlet-body">
                                               <textarea class="form-control" maxlength="500" name="remarks" id="remarks" rows="1" placeholder="Your remarks / comments here ."></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    </div>
                                    <?php
                                }
                                //check whProvId 
                                if ($whProvId == 1 || $whProvId == 2) {
                                    $newText = 'New';
                                    $oldText = 'Old';
                                    $childText = 'Children';
                                } else {
                                    $newText = 'Children';
                                    $oldText = 'Adults';
                                    $childText = 'General Ailment';
                                }
                                //mcQry
                                //pre_natal_new,
                                //pre_natal_old,
                                //post_natal_new,
                                //post_natal_old,
                                //ailment_children,
                                //ailment_adults,
                                //general_ailment
                                $mcQry = "SELECT
                                    tbl_hf_satellite_mother_care.pre_natal_new,
                                    tbl_hf_satellite_mother_care.pre_natal_old,
                                    tbl_hf_satellite_mother_care.post_natal_new,
                                    tbl_hf_satellite_mother_care.post_natal_old,
                                    tbl_hf_satellite_mother_care.ailment_children,
                                    tbl_hf_satellite_mother_care.ailment_adults,
                                    tbl_hf_satellite_mother_care.general_ailment
                                FROM
                                    tbl_hf_satellite_mother_care
                                WHERE
                                    tbl_hf_satellite_mother_care.reporting_date = '$RptDate'
                                AND tbl_hf_satellite_mother_care.warehouse_id = $wh_id";
                                //result
                                $mcRow = mysql_fetch_array(mysql_query($mcQry));
                                ?>
                                         
                                <div class="col-md-4" style="padding:1px !important;">
                                   
                                    <div class="portlet box green-meadow">
                                    <div class="portlet-title">
                                            <div class="caption">
                                                   Mother & Child Care (No. of Cases)
                                            </div>
                                            <div class="tools">
                                                    <a href="javascript:;" class="collapse" data-original-title="" title=""></a>
                                                    </a>
                                            </div>
                                    </div>
                                    <div class="portlet-body">
                                        <table class="table table-bordered table-condensed">
                                            <tr>
                                                <th>&nbsp;</th>
                                                <th class="text-center">New</th>
                                                <th class="text-center">Old</th>
                                            </tr>
                                            <tr>
                                                <td>Ante-natal</td>
                                                <td><input class="form-control input-sm text-right" type="text" name="pre_natal_new" id="pre_natal_new" autocomplete="off" value="<?php echo $mcRow['pre_natal_new']; ?>" /></td>
                                                <td><input class="form-control input-sm text-right" type="text" name="pre_natal_old" id="pre_natal_old" autocomplete="off" value="<?php echo $mcRow['pre_natal_old']; ?>" /></td>
                                            </tr>
                                            <tr>
                                                <td>Post-natal</td>
                                                <td><input class="form-control input-sm text-right" type="text" name="post_natal_new" id="post_natal_new" autocomplete="off" value="<?php echo $mcRow['post_natal_new']; ?>" /></td>
                                                <td><input class="form-control input-sm text-right" type="text" name="post_natal_old" id="post_natal_old" autocomplete="off" value="<?php echo $mcRow['post_natal_old']; ?>" /></td>
                                            </tr>
                                            <tr>
                                                <td><?php echo $childText; ?></td>
                                                <td><?php echo ($whProvId == 3) ? ($newText . '<br />') : ''; ?><input class="form-control input-sm text-right" type="text" name="ailment_child" id="ailment_child" autocomplete="off" value="<?php echo $mcRow['ailment_children']; ?>" /></td>
                                                <td><?php echo ($whProvId == 3) ? ($oldText . '<br />') : ''; ?><input class="form-control input-sm text-right" type="text" name="ailment_adult" id="ailment_adult" autocomplete="off" value="<?php echo $mcRow['ailment_adults']; ?>" /></td>
                                            </tr>
                                            <tr>
                                                <?php if ($whProvId == 1 || $whProvId == 2) { ?>
                                                    <td>General Ailment</td>
                                                <?php } ?>
                                                <?php if ($whProvId == 1 || $whProvId == 2) { ?>
                                                    <td colspan="2"><input class="form-control input-sm text-right" type="text" name="general_ailment" id="general_ailment" autocomplete="off" value="<?php echo $mcRow['general_ailment']; ?>" /></td>
                                                <?php } ?>
                                            </tr>
                                        </table>
                                    </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 text-right">
                                <button class="btn btn-primary" id="saveBtn" name="saveBtn" type="button" onClick="return submitForm()"> Save </button>
                                <button class="btn btn-info" type="submit" onClick="document.frmF7.reset()"> Reset </button>
                            </div>
                        </div>

                        <input type="hidden" name="ActionType" value="Add">
                        <input type="hidden" name="RptDate" value="<?php echo $RptDate; ?>">
                        <input type="hidden" name="wh_id" value="<?php echo $wh_id; ?>">
                        <input type="hidden" name="yy" value="<?php echo $yy; ?>">
                        <input type="hidden" name="mm" value="<?php echo $mm; ?>">
                        <input type="hidden" name="isNewRpt" id="isNewRpt" value="<?php echo $isNewRpt; ?>" />
                        <input type="hidden" name="add_date" id="add_date" value="<?php echo $add_date; ?>" />
                    </form>
                    <?php
                }
                ?>
                
                
                
                <div class="portlet box green-meadow">
                    <div class="portlet-title">
                            <div class="caption">
                                    Previous Remarks
                            </div>
                            <div class="tools">
                                    <a href="javascript:;" class="collapse" data-original-title="" title="">
                                    </a>
                            </div>
                    </div>
                    <div class="portlet-body">
                        
                        <?php
                         $iQry = "SELECT
                                    remarks.user_id,
                                    remarks.remarks,
                                    remarks.created_at,
                                    sysuser_tab.sysusr_name
                                    FROM
                                    remarks
                                    INNER JOIN sysuser_tab ON remarks.user_id = sysuser_tab.UserID
                                    WHERE
                                    remarks.wh_id = $wh_id AND
                                    remarks.reporting_date = '$RptDate' AND
                                    remarks.module = 'camps_data_entry' ";
                        $rsss = mysql_query($iQry);
                        $r_c=0;
                        while($row = mysql_fetch_assoc($rsss)){
                        ?>
                            <div class="note note-info">
                                    <h6 class="block"><?php echo '<b>'.$row['sysusr_name'].'</b> commented on : '.date('Y-M-d H:i:s',strtotime($row['created_at']));?></h6>
                                    <p>
                                    <?php echo nl2br($row['remarks']);?>
                                    </p>
                            </div>
                        <?php
                        $r_c++;
                        }
                        
                        if($r_c==0){
                            echo 'No remarks saved.';
                        }
                        ?>
                    </div>
                    </div>
            </div>
        </div>
    </div>
        
<script language="javascript" type="text/javascript">
    $(document).ready(function () {

        //Total Calculation
            $(".reffered31").on("keyup keydown", function () {
                calculateSum('reffered31');
            });

            $(".reffered32").on("keyup keydown", function () {
                calculateSum('reffered32');
            });

            $(".totalStaticCampMale31").on("keyup keydown", function () {
                calculateSum('totalStaticCampMale31');
            });
            $(".totalStaticCampMale32").on("keyup keydown", function () {
                calculateSum('totalStaticCampMale32');
            });

            $(".imp_ref").on("keyup keydown", function () {
                calculateSum('imp_ref');
            });

            $(".imp_perf").on("keyup keydown", function () {
                calculateSum('imp_perf');
            });

            function calculateSum(field) {

                var sum = 0;
                if (field == 'reffered31') {
                    var total = 'FLDIsuueUP31';
                } else if (field == 'reffered32') {
                    var total = 'FLDIsuueUP32';
                } else if (field == 'totalStaticCampMale31') {
                    var total = 'totalStaticCampMale';
                } else if (field == 'totalStaticCampMale32') {
                    var total = 'totalStaticCampFemale';
                }else if (field == 'imp_ref') {
                    var total = 'imp_ref_total';
                }else if (field == 'imp_perf') {
                    var total = 'imp_perf_total';
                }

                //iterate through each textboxes and add the values
                $("." + field).each(function () {
                    var reffered_male = $(this).val();

                    if (!isNaN(reffered_male) && reffered_male.length != 0) {
                        sum += parseFloat(reffered_male);
                    } else if (reffered_male.length != 0) {
                    }
                });
                $("input#" + total).val(sum);
            }
    });
</script>
</body>
</html>