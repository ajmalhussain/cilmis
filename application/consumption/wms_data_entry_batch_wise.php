<?php
include("../includes/classes/AllClasses.php");
include(PUBLIC_PATH . "html/header.php");
?>
<script src="<?php echo PUBLIC_URL; ?>js/dataentry/dataentry.js"></script>
<?php
include PUBLIC_PATH . "html/top_im.php";
//Checking user_id
if (isset($_SESSION['user_id'])) {
    //Getting user_id
    $userid = $_SESSION['user_id'];
    $objwharehouse_user->m_npkId = $userid;
    //Get ProvinceId By Idc
    $result_province = $objwharehouse_user->GetProvinceIdByIdc();
} else {
    //Display error message
    echo "user not login or timeout";
}

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

?>
<link href="<?php echo PUBLIC_URL; ?>css/styles.css" rel="stylesheet" type="text/css"/>
</head><body class="page-header-fixed page-quick-sidebar-over-content">
    <div class="modal"></div>
    <!-- BEGIN HEADER -->
    <div class="page-container">
        <div class="page-content-wrapper">
            <div class="page-content" style="margin-left:0px !important">
<?php
$wh_id = "";
//Checking Do
if (isset($_REQUEST['Do']) && !empty($_REQUEST['Do'])) {
    //Getting Do
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

    $RptDate = $temp[1];
    $readonly_for_im = false;
    if ($_SESSION['is_allowed_im'] == 1 &&  $RptDate >= $_SESSION['im_start_month']) {
        $isReadOnly = 'readonly="readonly"';
        $style = 'style="background:#CCC"';
        $readonly_for_im = true;
    } else {
        $isReadOnly = '';
        $style = '';
        $readonly_for_im = false;
    }
    
    
    // Check level
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
    $wh_lvl = $qryLvl['lvl'];

    // Check if its 1st Month of Data Entry 
    $whProvId = $qryLvl['prov_id'];
    $checkData = "SELECT
                        tbl_hf_data.reporting_date
                FROM
                        tbl_hf_data
                WHERE
                        tbl_hf_data.warehouse_id = $wh_id
                ORDER BY
                        tbl_hf_data.reporting_date ASC
                LIMIT 1";
    $checkDataRes = mysql_fetch_array(mysql_query($checkData));
    $openOB = ($checkDataRes['reporting_date'] == $RptDate) ? '' : $checkDataRes['reporting_date'];
    //Checking user_stakeholder and user_province1
    if ($_SESSION['user_stakeholder'] == 73 && $_SESSION['user_province1'] == 1) {
        $openOB = '';
    }

    $month = date('M', mktime(0, 0, 0, $mm, 1));
//    print_r($tt);
//    print_r($mm);exit;
    //****************************************************************************
    $objwarehouse->m_npkId = $wh_id;
    //Get Stk ID By WH Id
    $stkid = $objwarehouse->GetStkIDByWHId($wh_id);
    //Get Warehouse Name By Id
    $whName = $objwarehouse->GetWarehouseNameById($wh_id);
    echo "<h3 class=\"page-title row-br-b-wp\">" . $whName . " <span class=\"green-clr-txt\">(" . $month . ' ' . $yy . ")</span> </h3>";
//    include("stock_sources.php");   
    
//If new report
    if ($isNewRpt == 1) {
        //Get Previous Month Report Date
        $PrevMonthDate = $objReports->GetPreviousMonthReportDate($RptDate);
    } else {
        $PrevMonthDate = $RptDate;
    }
    //Including file

    $rowcolspan = "rowspan=2";

 if ($isNewRpt == 1) {
                        
    $qry4 = "SELECT
                    stock_batch.wh_id,
                    stock_batch.batch_id,
                    stock_batch.batch_no,
                    stock_batch.`status`,
                    Sum(tbl_stock_detail.Qty) as Qty,
                    tbl_stock_detail.IsReceived,
                    tbl_stock_master.TranDate,
                    stock_batch.item_id
                FROM
                stock_batch
                INNER JOIN tbl_stock_detail ON tbl_stock_detail.BatchID = stock_batch.batch_id
                INNER JOIN tbl_stock_master ON tbl_stock_detail.fkStockID = tbl_stock_master.PkStockID
                WHERE
                    stock_batch.wh_id = $wh_id AND
                    tbl_stock_detail.IsReceived = 1 AND
                      DATE_FORMAT(tbl_stock_master.TranDate,'%Y-%m') = '".(date('Y-m',strtotime($RptDate)))."'
                GROUP BY
                        stock_batch.item_id
                ";
        //result
//        echo $qry4;exit;
        $rsTemp4 = mysql_query($qry4);
        $rcvd_array = array();
        while ($rsRow4 = mysql_fetch_array($rsTemp4)) {
            $rcvd_array[$rsRow4['item_id']] = $rsRow4['Qty'];
        }
 }
// echo '<pre>';print_r($rcvd_array);exit;
?>
<form name="frmF7" id="frmF7" method="post">
    <div class="row">
        <div class="col-md-12">
            <div id="errMsg"></div>
            <table width="100%" align="center" class="table table-bordered">
                <tr>
                    <th rowspan="2" class="text-center">S.No.</th>
                    <th rowspan="2" class="text-center">Item / Batch Number</th>
                    <th rowspan="2" class="text-center">Opening balance</th>
                    <th <?php echo $rowcolspan; ?> class="text-center">Received</th>                    
                    <th rowspan="2" class="text-center">Issued</th>
                    <th colspan="2" class="text-center">Adjustments</th>
                    <th rowspan="2" class="text-center">Closing Balance</th>
                </tr>
                <tr>
                   
                    <th class="text-center">(+)</th>
                    <th class="text-center">(-)</th>
                </tr>
                <?php
                $qry_b="SELECT
			*
                        FROM
                                stock_batch
                        INNER JOIN itminfo_tab ON stock_batch.item_id = itminfo_tab.itm_id
                        WHERE
                                stock_batch.`wh_id` ='" . $wh_id . "'
                        
                        ORDER BY
                                itminfo_tab.itm_name ASC";
//                print_r($qry);exit;
                $rs_b = mysql_query($qry_b);
                $batches_arr = array();
                while ($row_b = mysql_fetch_array($rs_b)) {
                    $batches_arr[$row_b['itm_id']][$row_b['batch_id']]['batch_no'] = $row_b['batch_no'];
                    $batches_arr[$row_b['itm_id']][$row_b['batch_id']]['soh'] = $row_b['Qty'];
                }
                $qry_soh="SELECT
	itminfo_tab.itm_id,
	Sum(

		IF (
			DATE_FORMAT(
				tbl_stock_master.TranDate,
				'%Y-%m-%d'
			) < '$yy-$mm-01',
			tbl_stock_detail.Qty,
			0
		)
	) AS opening_balance,
	Sum(

		IF (
			DATE_FORMAT(
				tbl_stock_master.TranDate,
				'%Y-%m-%d'
			) >= '$yy-$mm-01'
			AND DATE_FORMAT(
				tbl_stock_master.TranDate,
				'%Y-%m-%d'
			) <= '$yy-$mm-31'
			AND tbl_stock_master.TranTypeID = 1,
			tbl_stock_detail.Qty,
			0
		)
	) AS received,
	Sum(tbl_stock_detail.Qty) AS closing_balance,
	tbl_warehouse.wh_name,
	itminfo_tab.itm_name,
	stock_batch.batch_no,
	tbl_stock_master.TranDate AS last_update,
        stock_batch.batch_id
FROM
	itminfo_tab
INNER JOIN stock_batch ON itminfo_tab.itm_id = stock_batch.item_id
INNER JOIN tbl_stock_detail ON stock_batch.batch_id = tbl_stock_detail.BatchID
INNER JOIN tbl_stock_master ON tbl_stock_detail.fkStockID = tbl_stock_master.PkStockID
INNER JOIN tbl_trans_type ON tbl_stock_master.TranTypeID = tbl_trans_type.trans_id
INNER JOIN tbl_warehouse ON stock_batch.wh_id = tbl_warehouse.wh_id
WHERE
	DATE_FORMAT(
		tbl_stock_master.TranDate,
		'%Y-%m-%d'
	) <= '$yy-$mm-31'
AND (
	(
		tbl_stock_master.TranTypeID = 2
	)
	OR (
		tbl_stock_master.TranTypeID = 1
	)
	OR (
		tbl_stock_master.TranTypeID > 2
	)
)
AND tbl_stock_master.temp = 0
AND stock_batch.wh_id = $wh_id
GROUP BY
	itminfo_tab.itm_id,
	batch_no
ORDER BY
	itm_name ASC";
                $result_soh = mysql_query($qry_soh);
                while ($row_soh = mysql_fetch_array($result_soh)) {
                    $batches_arr[$row_soh['itm_id']][$row_soh['batch_id']]['opening_balance'] = $row_soh['opening_balance'];
                  $batches_arr[$row_soh['itm_id']][$row_soh['batch_id']]['received'] = $row_soh['received'];

                }
//                print_r($batches_arr);exit;
                $province_id_session = $_SESSION['user_province1'];
                 
                //query
                //gets
                //all from itminfo_tab
                $qry="SELECT
			*
		FROM
			stock_batch
		INNER JOIN itminfo_tab ON stock_batch.item_id = itminfo_tab.itm_id
		WHERE
			stock_batch.`wh_id` ='" . $wh_id . "'
                GROUP BY
			itminfo_tab.itm_id
		ORDER BY
			itminfo_tab.itm_name ASC";
//                print_r($qry);exit;
                $rsTemp1 = mysql_query($qry);
                $SlNo = 1;
                $fldIndex = 0;
                //loop
                while ($rsRow1 = mysql_fetch_array($rsTemp1)) {

                    $qry = "SELECT * FROM tbl_hf_data WHERE `warehouse_id`='" . $wh_id . "' AND reporting_date='" . $PrevMonthDate . "' AND `item_id`='$rsRow1[itm_id]'";
                    //result
                    $rsTemp3 = mysql_query($qry);
                    $rsRow2 = mysql_fetch_array($rsTemp3);
                    
                    $add_date = $rsRow2['created_date'];
                    
                    ///// Code for Receive column bifurcation
                    $hf_data_id = $rsRow2['pk_id'];
                    $qryd = "SELECT
                                stock_sources_data.stock_sources_id,
                                stock_sources_data.received,
                                tbl_hf_data.item_id
                        FROM
                                stock_sources_data
                        INNER JOIN tbl_hf_data ON stock_sources_data.hf_data_id = tbl_hf_data.pk_id
                        WHERE
                                stock_sources_data.hf_data_id = $hf_data_id";
                    //result
                    //echo $qryd;exit;
                    $rsTemp4 = mysql_query($qryd);
                    $sources_data = array();
                    if(!empty($hf_data_id))
                    while($rsRow4 = mysql_fetch_array($rsTemp4)){
                        $sources_data[$rsRow4['stock_sources_id']][$rsRow4['item_id']] = $rsRow4['received'];
                    }
                    
                    ///// End of Code for Receive column bifurcation

                    // if new report
                    if ($isNewRpt == 1) {
                        $wh_issue_up = 0;
                        $wh_adja = 0;
                        $wh_adjb = 0;
                        //$wh_received = 0;
                        $wh_received = ((!empty($rcvd_array[$rsRow1['itm_id']])) ? $rcvd_array[$rsRow1['itm_id']] : '0');
                        //ob_a
                        $ob_a = $rsRow2['closing_balance'];
                        //cb_a
                        $cb_a = $rsRow2['closing_balance'];
                        ?>
                        <tr>
                            <td class="text-center"><?php echo $SlNo; ?></td>
                           <td><?php echo $rsRow1['itm_name']." [".$rsRow1['generic_name']."]"; ?></td>
                            
                                <input type="hidden" name="flitmrec_id[]" value="<?php echo $rsRow1['itm_id']; ?>">
                                <input type="hidden" name="flitm_category[]" value="<?php echo $rsRow1['itm_category']; ?>">
                                <input type="hidden" name="flitmname<?php echo $rsRow1['itm_id']; ?>" value="<?php echo $rsRow1['itm_name']; ?>"></td>
                            <td><input class="form-control input-sm text-right" <?php echo (!empty($openOB)) ? 'readonly="readonly"' : ''; ?> autocomplete="off"  type="text" name="FLDOBLA<?php echo $rsRow1['itm_id']; ?>" id="FLDOBLA<?php echo $rsRow1['itm_id']; ?>" size="8" maxlength="10" value="<?php echo $ob_a; ?>" onKeyUp="cal_balance('<?php echo $rsRow1['itm_id']; ?>');"></td>
                            <?php  
                                $cb_a+=$wh_received;
                            ?>
                                <td><input class="form-control input-sm text-right" readonly autocomplete="off"  type="text" name="FLDRecv<?php echo $rsRow1['itm_id']; ?>" id="FLDRecv<?php echo $rsRow1['itm_id']; ?>" size="8" maxlength="10"  value="<?php echo $wh_received; ?>" onKeyUp="cal_balance('<?php echo $rsRow1['itm_id']; ?>');"></td>
                            <td><input class="form-control input-sm text-right" autocomplete="off" name="FLDIsuueUP<?php echo $rsRow1['itm_id']; ?>" id="FLDIsuueUP<?php echo $rsRow1['itm_id']; ?>" value="<?php echo $wh_issue_up; ?>" type="text"  size="8" maxlength="10" onKeyUp="cal_balance('<?php echo $rsRow1['itm_id']; ?>');"></td>
                            <td><input class="form-control input-sm text-right" autocomplete="off" type="text" name="FLDReturnTo<?php echo $rsRow1['itm_id']; ?>" id="FLDReturnTo<?php echo $rsRow1['itm_id']; ?>" size="8" maxlength="10" value="<?php echo $wh_adja; ?>" onKeyUp="cal_balance('<?php echo $rsRow1['itm_id']; ?>');"></td>
                            <td><input class="form-control input-sm text-right" autocomplete="off" type="text" name="FLDUnusable<?php echo $rsRow1['itm_id']; ?>" id="FLDUnusable<?php echo $rsRow1['itm_id']; ?>" size="8" maxlength="10" value="<?php echo $wh_adjb; ?>" onKeyUp="cal_balance('<?php echo $rsRow1['itm_id']; ?>');"></td>
                            <td><input class="form-control input-sm text-right" autocomplete="off" type="text" name="FLDCBLA<?php echo $rsRow1['itm_id']; ?>" id="FLDCBLA<?php echo $rsRow1['itm_id']; ?>" size="8" maxlength="10" value="<?php echo $cb_a; ?>" readonly="readonly"></td>
                        </tr>
                        <?php
                        //isNewRpt == 0
                    } else if ($isNewRpt == 0) {
                        ?>
                        <tr>
                            <td class="text-center"><?php echo $SlNo; ?></td>
                           <td><?php echo $rsRow1['itm_name']." [".$rsRow1['generic_name']."]"; ?></td>
                            
                                <input type="hidden" name="flitmrec_id[]" value="<?php echo $rsRow1['itm_id']; ?>">
                                <input type="hidden" name="flitm_category[]" value="<?php echo $rsRow1['itm_category']; ?>">
                                <input type="hidden" name="flitmname<?php echo $rsRow1['itm_id']; ?>" value="<?php echo $rsRow1['itm_name']; ?>"></td>
                            <td><input class="form-control input-sm text-right" <?php echo (!empty($openOB)) ? 'readonly="readonly"' : ''; ?> autocomplete="off"  type="text" name="FLDOBLA<?php echo $rsRow1['itm_id']; ?>" id="FLDOBLA<?php echo $rsRow1['itm_id']; ?>" size="8" maxlength="10" value="<?php echo $rsRow2['opening_balance']; ?>" onKeyUp="cal_balance('<?php echo $rsRow1['itm_id']; ?>');"></td>
                            
                                <td><input class="form-control input-sm text-right"  readonly autocomplete="off"  type="text" name="FLDRecv<?php echo $rsRow1['itm_id']; ?>" id="FLDRecv<?php echo $rsRow1['itm_id']; ?>" size="8" maxlength="10"  value="<?php echo $rsRow2['received_balance']; ?>" onKeyUp="cal_balance('<?php echo $rsRow1['itm_id']; ?>');"></td>
                            <td><input class="form-control input-sm text-right" autocomplete="off" name="FLDIsuueUP<?php echo $rsRow1['itm_id']; ?>" id="FLDIsuueUP<?php echo $rsRow1['itm_id']; ?>" value="<?php echo $rsRow2['issue_balance']; ?>" type="text" size="8" maxlength="10" onKeyUp="cal_balance('<?php echo $rsRow1['itm_id']; ?>');"></td>
                            <td><input class="form-control input-sm text-right" autocomplete="off"  type="text" name="FLDReturnTo<?php echo $rsRow1['itm_id']; ?>" id="FLDReturnTo<?php echo $rsRow1['itm_id']; ?>" size="8" maxlength="10" value="<?php echo $rsRow2['adjustment_positive']; ?>" onKeyUp="cal_balance('<?php echo $rsRow1['itm_id']; ?>');"></td>
                            <td><input class="form-control input-sm text-right" autocomplete="off"  type="text" name="FLDUnusable<?php echo $rsRow1['itm_id']; ?>" id="FLDUnusable<?php echo $rsRow1['itm_id']; ?>" size="8" maxlength="10" value="<?php echo $rsRow2['adjustment_negative']; ?>" onKeyUp="cal_balance('<?php echo $rsRow1['itm_id']; ?>');"></td>
                            <td><input class="form-control input-sm text-right" autocomplete="off"  type="text" name="FLDCBLA<?php echo $rsRow1['itm_id']; ?>" id="FLDCBLA<?php echo $rsRow1['itm_id']; ?>" size="8" maxlength="10" value="<?php echo $rsRow2['closing_balance']; ?>" readonly="readonly"></td>
                        </tr>
                        <?php
                        //isNewRpt == 2
                    } else if ($isNewRpt == 2) {
                        ?>
                        <tr>
                            <td class="text-center"><?php echo $SlNo; ?></td>
                            <td><?php echo $rsRow1['itm_name']." [".$rsRow1['generic_name']."]"; ?></td>
                            <td class="text-right"><?php echo $rsRow2['opening_balance']; ?></td>
                            
                            <td class="text-right"><?php echo $rsRow2['received_balance']; ?></td>
                            <td class="text-right"><?php echo $rsRow2['issue_balance']; ?></td>
                            <td class="text-right"><?php echo $rsRow2['adjustment_positive']; ?></td>
                            <td class="text-right"><?php echo $rsRow2['adjustment_negative']; ?></td>
                            <td class="text-right"><?php echo $rsRow2['closing_balance']; ?></td>
                        </tr>
                        <?php
                    }
                    
                    if(!empty($batches_arr[$rsRow1['itm_id']])){
                        foreach($batches_arr[$rsRow1['itm_id']] as $batch_id => $batch_arr){
                            echo '<tr>';
                            echo '<td></td>';
                            echo '<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i style="color:black !important;" class="fa  fa-angle-double-right"></i> &nbsp;'.$batch_arr['batch_no'].'</td>';
                            echo '<td align="right"><input class="form-control input-sm text-right batch_inp batch_inp_opening batch_inp_'.$rsRow1['itm_id'].'" type="text" name="batch_opening_'.$batch_id.'" id="batch_opening_'.$batch_id.'" value="'.$batch_arr['opening_balance'].'" readonly></td>';
                            echo '<td><input class="form-control input-sm text-right batch_inp batch_inp_receive batch_inp_'.$rsRow1['itm_id'].'" type="text" name="batch_receive_'.$batch_id.'" id="batch_receive_'.$batch_id.'" value="'.$batch_arr['received'].'" readonly></td>';
                            echo '<td><input class="form-control input-sm text-right batch_inp batch_inp_iss    batch_inp_'.$rsRow1['itm_id'].'" type="text" name="batch_issued_'.$batch_id.'" id="batch_issued_'.$batch_id.'" onKeyUp="cal_balance(\''.$rsRow1['itm_id'].'\');"></td>';
                            echo '<td><input class="form-control input-sm text-right batch_inp batch_inp_adjp   batch_inp_'.$rsRow1['itm_id'].'" type="text" name="batch_adjp_'.$batch_id.'" id="batch_adjp_'.$batch_id.'"></td>';
                            echo '<td><input class="form-control input-sm text-right batch_inp batch_inp_adjn   batch_inp_'.$rsRow1['itm_id'].'" type="text" name="batch_adjn_'.$batch_id.'" id="batch_adjn_'.$batch_id.'"></td>';
                            echo '<td></td>';
                            echo '</tr>';
                        }
                    }
                    
                    
                    $SlNo++;
                    $fldIndex = $fldIndex + 13;
                }
//free result
                mysql_free_result($rsTemp1);
                ?>
            </table>
            <br>
        </div>
    </div>
    <?php
    //isNewRpt != 2
    if ($isNewRpt != 2) {
        ?>
        <div class="row">
            <div class="col-md-12">
                <div class="col-md-10 text-right" style="padding-top: 10px">
                    <div id="eMsg" style="color:#060;"></div>
                </div>
                <div class="col-md-2 text-right">
                    <button class="btn btn-primary" id="saveBtn" name="saveBtn" type="button" onclick="return formvalidate_batch_wise()"> Save </button>
                    <button class="btn btn-info" type="submit" onclick="document.frmF7.reset()"> Reset </button>
                </div>
            </div>
        </div>
        <?php
    }
//Hidden
    ?>
    <input type="hidden" name="ActionType" value="Add">
    <input type="hidden" name="RptDate" value="<?php echo $RptDate; ?>">
    <input type="hidden" name="wh_id" value="<?php echo $wh_id; ?>">
    <input type="hidden" name="yy" value="<?php echo $yy; ?>">
    <input type="hidden" name="mm" value="<?php echo $mm; ?>">
    <input type="hidden" name="isNewRpt" id="isNewRpt" value="<?php echo $isNewRpt; ?>" />
    <input type="hidden" name="add_date" id="add_date" value="<?php echo $add_date; ?>" />
    <input type="hidden" name="redir_url" id="redir_url" value="<?php echo (isset($redirectURL)) ? $redirectURL : ''; ?>" />
</form>
<?php
}
?>       	
            </div>
        </div>
    </div>
    <script src="<?php echo PUBLIC_URL; ?>assets/global/plugins/jquery-1.11.0.min.js" type="text/javascript"></script> 
    
    <script>
                function get_browser_info() {
                    var ua = navigator.userAgent, tem, M = ua.match(/(opera|chrome|safari|firefox|msie|trident(?=\/))\/?\s*(\d+)/i) || [];
                    if (/trident/i.test(M[1])) {
                        tem = /\brv[ :]+(\d+)/g.exec(ua) || [];
                        return {name: 'IE', version: (tem[1] || '')};
                    }
                    if (M[1] === 'Chrome') {
                        tem = ua.match(/\bOPR\/(\d+)/)
                        if (tem != null) {
                            return {name: 'Opera', version: tem[1]};
                        }
                    }
                    M = M[2] ? [M[1], M[2]] : [navigator.appName, navigator.appVersion, '-?'];
                    if ((tem = ua.match(/version\/(\d+)/i)) != null) {
                        M.splice(1, 1, tem[1]);
                    }
                    return {
                        name: M[0],
                        version: M[1]
                    };
                }
                var browser = get_browser_info();
                //alert(browser.name + ' - ' + browser.version);
                if (browser.name == 'Firefox' && browser.version < 30)
                {
                    alert('You are using an outdated version of the Mozilla Firefox. Please update your browser for data entry.');
                    window.close();
                }
                else if (browser.name == 'Chrome' && browser.version < 35)
                {
                    alert('You are using an outdated version of the Chrome. Please update your browser for data entry.');
                    window.close();
                }
                else if (browser.name == 'Opera' && browser.version < 28)
                {
                    alert('You are using an outdated version of the Opera. Please update your browser for data entry.');
                    window.close();
                }
                else if (browser.name == 'MSIE')
                {
                    alert('Please use Mozilla Firefox, Chrome or Opera for data entry.');
                    window.close();
                }
                
                
     function formvalidate_batch_wise()
        {
            $('#saveBtn').attr('disabled', false);
            $('#errMsg').hide();
            var itmLength = $("input[name^='flitmrec_id']").length;
            var itmArr = $("input[name^='flitmrec_id']");
            var FLDOBLAArr = $("input[name^='FLDOBLA']");
            var FLDRecvArr = $("input[name^='FLDRecv']");
            var FLDIsuueUPArr = $("input[name^='FLDIsuueUP']");
            var FLDCBLAArr = $("input[name^='FLDCBLA']");
            var FLDReturnToArr = $("input[name^='FLDReturnTo']");
            var FLDUnusableArr = $("input[name^='FLDUnusable']");
            /*
            var fieldval = document.frmaddF7.itmrec_id[i].value;
            fieldconcat = fieldval.split('-');
            var whobla = 'WHOBLA'+fieldconcat[1];
            var whrecv = 'WHRecv'+fieldconcat[1];
            var whissue = 'IsuueUP'+fieldconcat[1];
            var fldobla = 'FLDOBLA'+fieldconcat[1];
            var fldrecv = 'FLDRecv'+fieldconcat[1];
            var fldissue = 'FLDIsuueUP'+fieldconcat[1];
            */
            for (i = 0; i < itmLength; i++)
            {
                itm = itmArr.eq(i).val();
                var itmInfo = itm.split('-');
                itmId = itmInfo[1];
                var FLDOBLA = parseInt(FLDOBLAArr.eq(i).val());
                var FLDRecv = parseInt(FLDRecvArr.eq(i).val());
                var FLDIsuueUP = parseInt(FLDIsuueUPArr.eq(i).val());
                var FLDCBLA = parseInt(FLDCBLAArr.eq(i).val());
                var FLDReturnTo = parseInt(FLDReturnToArr.eq(i).val());
                var FLDUnusable = parseInt(FLDUnusableArr.eq(i).val());



                if ((FLDIsuueUP + FLDUnusable) > (FLDOBLA + FLDRecv + FLDReturnTo))
                {
                    alert('Invalid Closing Balance.\nClosing Balance = Opening Balance + Received + Adjustment(+) - Issued -  Adjustment(-)');
                    FLDOBLAArr.eq(i).css('background', '#F45B5C');
                    FLDRecvArr.eq(i).css('background', '#F45B5C');
                    FLDIsuueUPArr.eq(i).css('background', '#F45B5C');
                    FLDCBLAArr.eq(i).css('background', '#F45B5C');
                    FLDReturnToArr.eq(i).css('background', '#F45B5C');
                    FLDUnusableArr.eq(i).css('background', '#F45B5C');
                    return false;
                }
            }
            $('#saveBtn').attr('disabled', true);
            $("#eMsg").html('Saving...');
            $('body').addClass("loading");
            $.ajax({
                url: 'wms_data_entry_batch_wise_action.php',
                data: $('#frmF7').serialize(),
                type: 'POST',
                dataType: 'json',
                success: function (data) {
                    $('body').removeClass("loading");
                    if (data.resp == 'err')
                    {
                        $('#errMsg').html(data.msg).show();
                    } else if (data.resp == 'ok')
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
</body>
</html>