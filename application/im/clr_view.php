<?php

/**
 * clr_view
 * @package im
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
//include AllClasses
include("../includes/classes/AllClasses.php");
//include header
include(PUBLIC_PATH . "html/header.php");
//check id
if (isset($_REQUEST['id']) && isset($_REQUEST['wh_id'])) {
    //get to warehouse
    $whTo = mysql_real_escape_string($_REQUEST['wh_id']);
    //get id
    $id = mysql_real_escape_string($_REQUEST['id']);
    //select query
    //gets
    //district id
    //province id
    //stakeholder id
    //location name
    //main stakeholder
    $qry = "SELECT
				tbl_warehouse.dist_id,
				tbl_warehouse.prov_id,
				tbl_warehouse.stkid,
				tbl_locations.LocName,
				MainStk.stkname AS MainStk
			FROM
			tbl_warehouse
			INNER JOIN tbl_locations ON tbl_warehouse.dist_id = tbl_locations.PkLocID
			INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
			INNER JOIN stakeholder AS MainStk ON stakeholder.MainStakeholder = MainStk.stkid
			WHERE
			tbl_warehouse.wh_id = " . $whTo;
    //query result
    $qryRes = mysql_fetch_array(mysql_query($qry));
    //distrct id
    $distId = $qryRes['dist_id'];
    //province id
    $provId = $qryRes['prov_id'];
    //stakeholder id
    $stkid = $qryRes['stkid'];
    //location name
    $distName = $qryRes['LocName'];
    //main stakeholder
    $mainStk = $qryRes['MainStk'];


    $show_part_a = false;
    if ($stkid == 1 && $provId == 4) {
        $show_part_a = true;
    }

    //select query
    //gets
    //requisition num,
    //date from,
    //date to,
    //pk id,
    //pk master id,
    //avg consumption,
    //soh dist,
    //soh field,
    //total stock,
    //desired stock,
    //replenishment,
    // requested on,
    //item name,
    //item id,
    //item rec id,
    //item type,
    //generic name,
    //method type
    $qry = "SELECT
				clr_master.requisition_num,
				clr_master.date_from,
				clr_master.date_to,
				clr_master.approval_status as master_approval_status,
				clr_details.approval_status as detail_approval_status,
				clr_details.pk_id,
				clr_details.pk_master_id,
				clr_details.avg_consumption,
				clr_details.soh_dist,
				clr_details.soh_field,
				clr_details.total_stock,
				clr_details.desired_stock,
				clr_details.replenishment,
				clr_details.qty_req_dist_lvl1,
                                clr_details.qty_req_dist_lvl2,
                                clr_details.qty_req_prov,
                                clr_details.qty_req_central,
                                clr_details.remarks_dist_lvl1,
                                clr_details.remarks_dist_lvl2,
                                clr_details.remarks_prov,
                                clr_details.remarks_central,
                                clr_details.sale_of_last_month,
                                clr_details.sale_of_last_3_months,
				DATE_FORMAT(clr_master.requested_on, '%d/%m/%Y') AS requested_on,
				itminfo_tab.itm_name,
				itminfo_tab.itm_id,
				itminfo_tab.itmrec_id,
				itminfo_tab.itm_type,
				itminfo_tab.generic_name,
				itminfo_tab.method_type
			FROM
				clr_master
				INNER JOIN clr_details ON clr_details.pk_master_id = clr_master.pk_id
				INNER JOIN itminfo_tab ON clr_details.itm_id = itminfo_tab.itm_id
			WHERE
				clr_master.pk_id = " . $id;
    //query result
    $qryRes = mysql_query($qry);
    //fetch result
    $items_arr = array();
    $show_prov_remarks = $show_dist_remarks = false;
    while ($row = mysql_fetch_array($qryRes)) {
        $master_approval_status = $row['master_approval_status'];
        //requisition Num 
        $requisitionNum = $row['requisition_num'];
        //date from
        $dateFrom = date('M-Y', strtotime($row['date_from']));
        //date to
        $dateTo = date('M-Y', strtotime($row['date_to']));
        //requested on 
        $requestedOn = $row['requested_on'];
        //item ids
        $itemIds[] = $row['itm_id'];
        //product
        $product[$row['method_type']][] = $row['itm_name'];
        $items_arr[$row['itm_id']] = $row['itm_name'];

        // implanon is now opened .
        //if ($row['itm_id'] == 8) 
        //set avg Consumption
        $avgConsumption[$row['itm_id']] = number_format($row['avg_consumption']);
        $sale_of_last_month[$row['itm_id']] = number_format($row['sale_of_last_month']);;
        $sale_of_last_3_months[$row['itm_id']] = number_format($row['sale_of_last_3_months']);;
        //set SOH Dist
        $SOHDist[$row['itm_id']] = number_format($row['soh_dist']);
        //set SOH Field
        $SOHField[$row['itm_id']] = number_format($row['soh_field']);
        //set total Stock
        $totalStock[$row['itm_id']] = number_format($row['total_stock']);
        //set desired Stock
        $desiredStock[$row['itm_id']] = number_format($row['desired_stock']);
        //set replenishment
        $replenishment[$row['itm_id']] = number_format($row['replenishment']);

        //set qty requested and remarks
        $qty_req_dist_lvl1[$row['itm_id']]  = number_format($row['qty_req_dist_lvl1']);;
        $qty_req_dist_lvl2[$row['itm_id']]  = number_format($row['qty_req_dist_lvl2']);;
        $qty_req_prov[$row['itm_id']]       = number_format($row['qty_req_prov']);;
        $qty_req_central[$row['itm_id']]    = number_format($row['qty_req_central']);;

        $remarks_dist_lvl1[$row['itm_id']]  = $row['remarks_dist_lvl1'];
        $remarks_dist_lvl2[$row['itm_id']]  = $row['remarks_dist_lvl2'];
        $remarks_prov[$row['itm_id']]       = $row['remarks_prov'];
        $remarks_central[$row['itm_id']]    = $row['remarks_central'];

        if (!empty($row['remarks_prov'])) $show_prov_remarks = true;
        if (!empty($row['remarks_dist_lvl1'])) $show_dist_remarks = true;

        if (strtoupper($row['method_type']) == strtoupper($row['generic_name'])) {
            $methodType[$row['method_type']]['rowspan'] = 2;
        } else {
            $genericName[$row['generic_name']][] = $row['itm_name'];
        }
    }
    $duration = $dateFrom . ' to ' . $dateTo;
}
//echo '<pre>';print_r($remarks_dist_lvl1);print_r($items_arr);exit;
$print_size = '100';
$print_right = '30';
if ($master_approval_status == 'Pending') {
    $print_word  = 'Draft';
    $print_right = '30';
} elseif ($master_approval_status == 'Dist_Approved') {
    if ($_SESSION['user_level'] <= '2') {
        $print_word  = 'Draft';
    } else {
        $print_word  = 'Approved By District';
        $print_right = '10';
        $print_size = '90';
    }
} elseif ($master_approval_status == 'Prov_Approved') {
    if ($_SESSION['user_level'] == '1') {
        $print_word  = 'Draft';
        $print_right = '20';
    } else {
        $print_word  = 'Approved By Province';
        $print_right = '10';
        $print_size = '90';
    }
} elseif ($master_approval_status == 'Denied') {
    $print_word  = 'Denied';
    $print_right = '20';
} elseif ($master_approval_status == 'Approved' || $master_approval_status == 'Issued'  || $master_approval_status == 'Issue in Process') {
    $print_word  = 'Approved';
    $print_right = '30';
} else {
    $print_word  = 'Draft';
    $print_right = '40';
}

?>
<script>
    function printContents() {
        var dispSetting = "toolbar=yes,location=no,directories=yes,menubar=yes,scrollbars=yes, left=100, top=25";
        var printingContents = document.getElementById("printing").innerHTML;

        var docprint = window.open("", "", printing);


        docprint.document.open();
        docprint.document.write('<html><head><title>CLR6</title>');

        //setting up CSS for the watermark

        docprint.document.write('</head><body onLoad="self.print();"><center>');
        docprint.document.write(printingContents);
        //docprint.document.write('<div id="watermark"><?php echo $print_word; ?></div>');
        docprint.document.write('</center>');

        docprint.document.write('</body></html>');
        docprint.document.close();
        docprint.focus();
    }
</script>
</head>
<!-- END HEAD -->

<body class="page-header-fixed page-quick-sidebar-over-content">
    <!-- BEGIN HEADER -->
    <div class="page-container">
        <?php include PUBLIC_PATH . "html/top.php"; ?>
        <?php include PUBLIC_PATH . "html/top_im.php"; ?>
        <div class="page-content-wrapper">
            <div class="page-content">

                <!-- BEGIN PAGE HEADER-->
                <div class="row">
                    <div class="col-md-12">
                        <div class="widget">
                            <div class="widget-head">
                                <h3 class="heading">Requisitions</h3>
                            </div>
                            <div class="widget-body">
                                <div id="printing" style="clear:both;margin-top:20px;">
                                    <div style="margin-left:0px !important; width:100% !important;">
                                        <style>
                                            body {
                                                margin: 0px !important;
                                                font-family: Arial, Helvetica, sans-serif;
                                            }

                                            table#myTable {
                                                margin-top: 20px;
                                                border-collapse: collapse;
                                                border-spacing: 0;
                                            }

                                            table#myTable tr td,
                                            table#myTable tr th {
                                                font-size: 11px;
                                                padding-left: 5px;
                                                text-align: left;
                                                border: 1px solid #999;
                                            }

                                            table#myTable tr td.TAR {
                                                text-align: right;
                                                padding: 5px;
                                                width: 50px !important;
                                            }

                                            .sb1NormalFont {
                                                color: #444444;
                                                font-family: Verdana, Arial, Helvetica, sans-serif;
                                                font-size: 11px;
                                                font-weight: bold;
                                                text-decoration: none;
                                            }

                                            p {
                                                margin-bottom: 5px;
                                                font-size: 11px !important;
                                                line-height: 1 !important;
                                                padding: 0 !important;
                                            }

                                            table#headerTable tr td {
                                                font-size: 11px;
                                            }

                                            /* Print styles */
                                            @media only print {

                                                table#myTable tr td,
                                                table#myTable tr th {
                                                    font-size: 8px;
                                                    padding-left: 2 !important;
                                                    text-align: left;
                                                    border: 1px solid #999;
                                                }

                                                #doNotPrint {
                                                    display: none !important;
                                                }
                                            }
                                        </style>

                                        <p style="color: #000000; font-size: 20px;text-align:center">
                                            <span style="float:left; font-weight:normal;"><i style="color:black !important" onClick="history.go(-1)" class="fa fa-arrow-left" /></i></span>
                                            <b><u>Contraceptive Requisition Form</u></b>
                                            <span style="float:right; font-weight:normal;">CLR-6</span>
                                        </p>
                                        <p style="text-align:center;margin-right:35px;">
                                            (<?php echo "For $mainStk District $distName"; ?>)
                                        </p>
                                        <table width="200" id="headerTable" align="right">
                                            <tr>
                                                <td align="left">
                                                    <p style="width: 100%; display: table;"> <span style="display: table-cell; width: 20px;">For: </span> <span style="display: table-cell; border-bottom: 1px solid black;"><?php echo $duration; ?></span> </p>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <p style="width: 100%; display: table;"> <span style="display: table-cell; width: 75px;">Requisition No: </span> <span style="display: table-cell; border-bottom: 1px solid black;"><?php echo $requisitionNum; ?></span> </p>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <p style="width: 100%; display: table;"> <span style="display: table-cell; width: 83px;">Requisition Date: </span> <span style="display: table-cell; border-bottom: 1px solid black;"><?php echo $requestedOn; ?></span> </p>
                                                </td>
                                            </tr>
                                        </table>
                                        <div style="clear:both;"></div>
                                        <table width="100%" id="myTable" cellspacing="0" align="center">
                                            <thead>
                                                <tr>
                                                    <td rowspan="3" width="2%" style="text-align:center;">S. No.</td>
                                                    <td rowspan="3" width="18%" id="desc">Product</td>
                                                    <td colspan="7">Part A</td>
                                                    <td colspan="2">Part B</td>
                                                </tr>
                                                <tr>
                                                    <td class="col-md-1">A1</td>
                                                    <td class="col-md-1">A2</td>
                                                    <td class="col-md-1">A3</td>
                                                    <td class="col-md-1">A4</td>
                                                    <td class="col-md-1">A5</td>
                                                    <td class="col-md-1">A6</td>
                                                    <td class="col-md-1">A7</td>
                                                    <td class="col-md-1">1</td>
                                                    <td class="col-md-1">2</td>
                                                </tr>
                                                <tr>
                                                    <td>Consumption During Last Quarter</td>
                                                    <td>Stock at the end of last quarter at district Store</td>
                                                    <td>Stock at the end of last quarter at Service Delivery Points</td>
                                                    <td>Total Stock Available (A2+A3)</td>
                                                    <td>Desired stock level for 2 quarters (A1x2)</td>
                                                    <td>Replenishment Requested (A5-A4)</td>
                                                    <td>Quantity Actually Required</td>
                                                    <td>Quantity Approved By Province</td>
                                                    <td>Relevant Issue Voucher</td>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $colNum = 1;
                                                foreach ($items_arr as $itm => $pname) {
                                                ?>
                                                    <tr>
                                                        <td><?php echo $colNum; ?></td>
                                                        <td class="td_chk" data-itm-id="<?php echo $itm; ?>" style="text-align:center"> <?php echo $pname; ?></td>
                                                    <?php
                                                    $q2 = "SELECT DISTINCT
                                                                                               tbl_stock_master.TranNo,
                                                                                               tbl_stock_master.PKStockId,
                                                                                               tbl_stock_detail.IsReceived
                                                                                       FROM
                                                                                               clr_details
                                                                                       INNER JOIN tbl_stock_master ON clr_details.stock_master_id = tbl_stock_master.PkStockID
                                                                                       INNER JOIN tbl_stock_detail ON tbl_stock_master.PkStockID = tbl_stock_detail.fkStockID
                                                                                       WHERE
                                                                                               tbl_stock_master.TranTypeID = 2 AND
                                                                                               clr_details.pk_master_id = " . $_REQUEST['id'] . "
                                                                                       ORDER BY
                                                                                               tbl_stock_master.PkStockID ASC";
                                                    $getStockIssues = mysql_query($q2) or die("Err GetStockIssueId");

                                                    //chech if record exists
                                                    $issueVoucher = array();
                                                    $a = '';
                                                    if (mysql_num_rows($getStockIssues) > 0) {

                                                        //fetch results
                                                        while ($row = mysql_fetch_assoc($getStockIssues)) {
                                                            $issueVoucher[] = $row['TranNo'];
                                                            $a .= " <a onClick=\"window.open('" . APP_URL . "im/printIssue.php?id=" . $row['PKStockId'] . "', '_blank', 'scrollbars=1,width=842,height=595')\" href=\"javascript:void(0);\">" . $row['TranNo'] . "</a>,</br>";
                                                        }
                                                    }

                                                    echo "<td class=\"TAR\">" . $avgConsumption[$itm] . "</td>";
                                                    echo "<td class=\"TAR\">" . $SOHDist[$itm] . "</td>";
                                                    echo "<td class=\"TAR\">" . $SOHField[$itm] . "</td>";
                                                    echo "<td class=\"TAR\">" . $totalStock[$itm] . "</td>";
                                                    echo "<td class=\"TAR\">" . $desiredStock[$itm] . "</td>";
                                                    echo "<td class=\"TAR\">" . $replenishment[$itm] . "</td>";
                                                    echo "<td class=\"TAR\">" . $qty_req_dist_lvl1[$itm] . "</td>";
                                                    echo "<td class=\"TAR\">" . $qty_req_prov[$itm] . "</td>";
                                                    echo '<td class=\"TAR\">' . $a . '</td>';
                                                    $colNum++; }
                                                    ?>
                                                    </tr>

                                                    <?php
                                                    if ($show_dist_remarks || $show_prov_remarks) {
                                                    ?>
                                                        <tr height="30" style="background-color:#eee">
                                                            <td colspan="11">Remarks</td>
                                                        </tr>

                                                    <?php
                                                    }
                                                    if ($show_dist_remarks) {
                                                    ?>
                                                        <tr>
                                                            <td colspan="11">Remarks By District</td>
                                                        </tr>
                                                        <tr>
                                                            <?php
                                                            foreach ($remarks_dist_lvl1 as $key => $val) {
                                                                if (!empty($val)) {

                                                                    echo '<tr height="30">
                                                                        <td > </td>
                                                                        <td >' . $items_arr[$key] . '</td>
                                                                        <td  colspan="10">' . wordwrap($val, 150, "<br>\n", true) . '</td>
                                                                    </tr>';
                                                                }
                                                            }
                                                            ?>
                                                        </tr>

                                                    <?php
                                                    }
                                                    if ($show_prov_remarks) {
                                                    ?>
                                                        <tr height="30">
                                                            <td colspan="<?php echo count($itemIds) + 3; ?>">Remarks By Province</td>
                                                        </tr>
                                                        <tr>
                                                            <?php
                                                            foreach ($remarks_prov as $key => $val) {
                                                                if (!empty($val)) {

                                                                    echo '<tr  >
                                                                        <td > </td>
                                                                        <td >' . $items_arr[$key] . '</td>
                                                                        <td  colspan="' . (count($itemIds) + 2) . '">' . wordwrap($val, 150, "<br>\n", true) . '</td>
                                                                    </tr>';
                                                                }
                                                            }
                                                            ?>
                                                        </tr>

                                                    <?php
                                                    }
                                                    ?>

                                            </tbody>
                                        </table>
                                        <table width="100%">
                                            <tr>
                                                <td colspan="4">&nbsp;</td>
                                            </tr>
                                            <tr>
                                                <td colspan="4">&nbsp;</td>
                                            </tr>
                                            <tr>
                                                <td style="text-align:right;" width="10%" class="sb1NormalFont">Name:</td>
                                                <td width="40%">__________________________</td>
                                                <td width="30%" style="text-align:right;" class="sb1NormalFont">Signature:</td>
                                                <td width="20%">__________________________</td>
                                            </tr>
                                            <tr>
                                                <td colspan="4">&nbsp;</td>
                                            </tr>
                                            <tr>
                                                <td style="text-align:right;" class="sb1NormalFont">Designation:</td>
                                                <td>__________________________</td>
                                                <td style="text-align:right;" class="sb1NormalFont">Date:</td>
                                                <td>__________________________</td>
                                            </tr>
                                            <tr id="doNotPrint">
                                                <td colspan="4" style="text-align:right; border:none; padding-top:15px;">
                                                    <input type="button" onClick="history.go(-1)" value="Back" class="btn btn-primary" />
                                                    <input type="button" onClick="printContents()" value="Print" class="btn btn-warning" />
                                                </td>
                                            </tr>
                                        </table>

                                        <div id="watermark" style="font-size:<?php echo $print_size; ?>px;font-color:#eeeee;opacity: 0.2;z-index: 5;right: <?php echo $print_right; ?>%;top: 30%;position: absolute;display: block;   -ms-transform: rotate(340deg);  -webkit-transform: rotate(340deg);  transform: rotate(340deg);"><?php echo $print_word; ?></div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- END FOOTER -->
    <?php include PUBLIC_PATH . "/html/footer.php"; ?>
    <!-- END JAVASCRIPTS -->
</body>
<!-- END BODY -->

</html>