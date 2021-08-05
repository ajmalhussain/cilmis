<?php
include("../includes/classes/AllClasses.php");
include(PUBLIC_PATH . "html/header.php");

if (!empty($_SESSION['user_warehouse']))
    $wh_id = $_SESSION['user_warehouse'];
else
    $wh_id = 123;

$sCriteria = array();
$number = '';
$date_from = '';
$date_to = '';
//echo '<pre>';print_r($_REQUEST);exit;
//check if submitted
if (isset($_REQUEST['search']) && !empty($_REQUEST['search'])) {
//check search by
    //check date from
    if (isset($_REQUEST['date_from']) && !empty($_REQUEST['date_from'])) {
        //get date from
        $date_from = $_REQUEST['date_from'];
        $dateArr = explode('/', $date_from);
        $sCriteria['date_from'] = dateToDbFormat($date_from);
        //set date from	
        $objPurchaseOrder->fromDate = dateToDbFormat($date_from);
    }
    //check to date
    if (isset($_REQUEST['date_to']) && !empty($_REQUEST['date_to'])) {
        //get to date
        $date_to = $_REQUEST['date_to'];
        $dateArr = explode('/', $date_to);
        $sCriteria['date_to'] = dateToDbFormat($date_to);
        //set to date	
        $objPurchaseOrder->toDate = dateToDbFormat($date_to);
    }
    $_SESSION['sCriteria'] = $sCriteria;
} else {
    //date from
    $date_from = date('01/01/Y');
    //date to
    $date_to = date('t/12/Y');
    //set from date
    $objPurchaseOrder->fromDate = dateToDbFormat($date_from);
    //set to date
    $objPurchaseOrder->toDate = dateToDbFormat($date_to);

    $sCriteria['date_from'] = dateToDbFormat($date_from);
    $sCriteria['date_to'] = dateToDbFormat($date_to);
    $_SESSION['sCriteria'] = $sCriteria;
}

//Stock Search
$gp_by = " GROUP BY purchase_order.pk_id  ";
$result = $objPurchaseOrder->ComprehensiveSearch(1, $wh_id, $gp_by, $search_as);
//title
$title = "Shipments";
//Get User Receive From WH
$join1 = $where1 = "";
if (isset($_SESSION['user_level']) && $_SESSION['user_level'] == 2) {
    $join1 .= " INNER JOIN funding_stk_prov ON tbl_warehouse.wh_id = funding_stk_prov.funding_source_id ";
    if (isset($_SESSION['user_province1'])) {
        $where1 .= " AND funding_stk_prov.province_id = " . $_SESSION['user_province1'] . " ";
    }
    if (isset($_SESSION['user_stakeholder1'])) {
        $where1 .= " AND funding_stk_prov.stakeholder_id = " . $_SESSION['user_stakeholder1'] . " ";
    }
} else {
    $where1 .= " AND tbl_stock_master.WHIDTo = $wh_id ";
}
//query copied from clsswharehouse
$strSql = "SELECT
                    tbl_warehouse.wh_name,
                    tbl_warehouse.wh_id
            FROM
            tbl_warehouse
            INNER JOIN tbl_stock_master ON tbl_stock_master.WHIDFrom = tbl_warehouse.wh_id
            INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
            $join1
            WHERE
            tbl_stock_master.TranTypeID = 1
            $where1
            GROUP BY tbl_warehouse.wh_name
            ORDER BY
            stakeholder.stkorder ASC";
//echo $strSql;
$warehouses = mysql_query($strSql) or die("Error Getwh");
$po_numbers = $objPurchaseOrder->getPO();
$vendors = $objPurchaseOrder->getVendor();
?>
<link rel="stylesheet" type="text/css" href="../../public/assets/global/plugins/select2/select2.css"/>
</head>
<!-- END HEAD -->

<body class="page-header-fixed page-quick-sidebar-over-content">
    <!-- BEGIN HEADER -->
    <div class="page-container">
        <?php
//include top
        include PUBLIC_PATH . "html/top.php";
//include top_im
        include PUBLIC_PATH . "html/top_im.php";
        ?>
        <div class="page-content-wrapper">
            <div class="page-content"> 
                <?php
                $str_do = isset($_REQUEST['DO']) ? $_REQUEST['DO'] : '';
                ?>
                <!-- BEGIN PAGE HEADER-->
                <div class="row">
                    <div class="col-md-12">
                        <?php
                        if ($str_do != 'Edit') {
                            ?>
                            <div  class="widget" data-toggle="collapse-widget">
                                <div class="widget-head">
                                    <h3 class="heading">Filter By</h3>
                                </div>
                                <div id="shipment_filter_div" class="widget-body">
                                    <form method="POST" name="batch_search" action="" >
                                        <!-- Row -->
                                        <div class="row">
                                            <div class="col-md-12">

                                                <!-- Group -->
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label class="control-label">Date From</label>
                                                        <input type="text" class="form-control input-sm" name="date_from" readonly id="date_from" value="<?php echo $date_from; ?>"/>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label class="control-label">Date To</label>
                                                        <input type="text" class="form-control input-sm" name="date_to"  readonly="" id="date_to" value="<?php echo $date_to; ?>"/>
                                                    </div>
                                                </div> 
                                                <div class="col-md-3">
                                                    <div class="control-group">
                                                        <label class="control-label" for="warehouse">Funding Source</label>
                                                        <div class="controls">
                                                            <select name="warehouse" id="warehouse" class="input-medium  select2me">
                                                                <option value="">Select</option>
                                                                <?php
                                                                if (mysql_num_rows($warehouses) > 0) {
                                                                    while ($row = mysql_fetch_object($warehouses)) {
                                                                        $sel = "";
                                                                        if ($warehouse == $row->wh_id) {
                                                                            $sel = " selected ";
                                                                        }
                                                                        ?>
                                                                        <option value="<?php echo $row->wh_id; ?>" <?= $sel ?> ><?php echo $row->wh_name; ?></option>
                                                                        <?php
                                                                    }
                                                                }
                                                                ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-1 right">
                                                    <div class="form-group">
                                                        <label class="control-label">&nbsp;</label>
                                                        <div class="form-group">
                                                            <button type="submit" name="search" value="search" class="btn btn-primary">Search</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div> 
                                    </form>
                                </div>
                            </div>
                            <?php
                        }
                        ?>
                        <div class="widget" data-toggle="collapse-widget"> 
                            <div class="widget-head">
                                <h4 class="heading">Purchase Order Search</h4>
                            </div>
                            <div class="widget-body"> 
                                <div class="col-md-12 right">
                                    <img src="<?php echo PUBLIC_URL; ?>images/excel-16.png" onClick="tableToExcel('export3', 'sheet 1', 'approval_list')" alt="Excel" style="cursor:pointer;" />
                                </div>
                                <div id="export3"> 
                                <table class="shipmentsearchx table table-bordered table-condensed" id="myTable">
                                    <thead>
                                        <tr>
                                            <th>Sr. No.</th> 
                                            <th>Item</th>
                                            <th>Specs</th>
                                            <th>Required Quantity</th>
                                            <th>P.O. #</th>
                                            <th>Date of P.O</th>
                                            <th>Contract No.</th>
                                            <th>Signing Date</th>
                                            <th>Vendor Name</th>
                                            <th>Contact No.</th>
                                            <th>Canceled</th>
                                            <th>Unit Price</th>
                                            <th>Funds Committed</th>
                                            <th>Advance Payment Released</th>
                                            <th>Expected Delivery Date</th>
                                            <th>Delivered</th>
                                            <th>Delivery Date</th> 
                                            <th>1) Delivery Date</th> 
                                            <th>Balance</th> 
                                            <th>2) Delivery Date</th> 
                                            <th>Balance</th> 
                                            <th>3) Delivery Date</th> 
                                            <th>Balance</th> 
                                        </tr> 
                                    </thead>
                                    <tbody>
                                        <?php
                                        $i = 1;
                                        $transNo = '';
                                        $required_q = 0;
                                        $g_total = array();
                                        if ($result != FALSE) {
                                            //fetch result
                                            while ($row = mysql_fetch_object($result)) {
                                                $trail = $objPurchaseOrderDetails->getTrail($row->pk_id);
                                                $j = 0;
                                                if($trail != FALSE){
                                                    while ($row_1 = mysql_fetch_array($trail)) {
                                                        $delivery_arr[$j]['delivery_date'] = $row_1['delivery_date'];
                                                        $delivery_arr[$j]['balance'] = $row_1['balance'];
                                                        $j++;
                                                    }
                                                }
                                                $s_q = isset($row->shipment_quantity) ? $row->shipment_quantity : '0';
                                                $r_q = isset($row->received_qty) ? $row->received_qty : '0';
                                                $remaining_q = $s_q - $r_q;
                                                $received_perc = ($r_q / $s_q * 100);
                                                $remaining_perc = ($remaining_q / $s_q * 100);
                                                $cl = '';
                                                if ($row->status == 'Cancelled') {
                                                    $cl = 'danger';
                                                }
//                                                print_r($ship_qty_arr[0] * $unit_price_arr[0]);exit;
                                                ?>
                                                <tr class="gradeX <?= $cl ?>" >
                                                    <td class="text-center"><?php echo $i; ?></td>
                                                    <td><?php echo $row->itm_name; ?></td>
                                                    <td><?php echo $row->itm_des ?></td>
                                                    <td><?php echo number_format($row->requirement, 2); ?></td>
                                                    <td><a href="add_purchase_order.php?DO=Update&po_id=<?=$row->pk_id?>"><?php echo $row->po_number; ?></a></td>
                                                    <td><?php echo $row->po_date ?></td>
                                                    <td><?php echo $row->contact_no; ?></td>
                                                    <td><?php echo $row->signing_date; ?></td>
                                                    <td><?php echo $row->vendor; ?></td> 
                                                    <td><?php echo $row->contact_numbers; ?></td>
                                                    <td><?php
                                                        if ($row->status == 'Cancelled') {
                                                            echo 'Cancelled';
                                                        } else {
                                                            echo 'Not cancelled';
                                                        }
                                                        ?></td>
                                                    <td><?php echo $row->unit_price; ?></td>
                                                    <td><?php echo number_format($row->unit_price * $s_q, 2); ?></td>
                                                    <td><?php echo $row->adv_payment_release; ?></td>
                                                    <td><?php echo $row->contract_delivery_date; ?></td>
                                                    <td><?php if (($row->shipment_quantity - $row->received_qty) == 0)
                                                    echo 'Delivered';
                                                else
                                                    'Not Delivered'
                                                            ?></td>
                                                    <td></td>
                                                        <?php for ($i = 0; $i < 3; $i++) { ?>

                                                        <td><?php
                                                            if (!isset($delivery_arr[$i]['delivery_date'])) {
                                                                echo 'NULL';
                                                            } else {
                                                                echo $delivery_arr[$i]['delivery_date'];
                                                            }
                                                            ?></td>
                                                        <td><?php
                                                if (!isset($delivery_arr[$i]['balance'])) {
                                                    echo 'NULL';
                                                } else {
                                                    echo $delivery_arr[$i]['balance'];
                                                }
                                                ?></td>  

                                                <?php } ?>


                                                </tr>

        <?php
        $i++;
    }
}
?>
                                    </tbody>
                                </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php
//include footer
include PUBLIC_PATH . "/html/footer.php";
?>
<!--    <script src="<?php echo PUBLIC_URL; ?>js/dataentry/add-shipment.js"></script> -->
    <script src="<?php echo PUBLIC_URL; ?>js/jquery.inlineEdit_date.js"></script> 
    <script src="<?php echo PUBLIC_URL; ?>js/dataentry/search-shipments.js"></script>
    <script src="../../public/js/tableToExcel.js"></script>
    <script type="text/javascript" src="../../public/assets/global/plugins/select2/select2.min.js"></script>
<?php
//unset stock id
unset($_SESSION['stock_id']);
if (!empty($_REQUEST['s']) && $_REQUEST['s'] == 't') {
    ?>
        <script type="text/javascript">
                                            var self = $('[data-toggle="notyfy"]');
                                            notyfy({
                                                force: true,
                                                text: 'Data has been deleted successfully!',
                                                type: 'success',
                                                layout: self.data('layout')
                                            });
        </script>
<?php } ?>

<?php
if ($temp_voucher_exists) {
    ?>
        <script type="text/javascript">
            $(function () {
                $('#shipment_filter_div').collapse('500');
            });

        </script>
    <?php
}
?>

    <script>
        $(function () {

            $('#myTable').DataTable({
                "aaSorting": []
            });
        });
        $("#receive_date").datepicker({
            dateFormat: 'yyyy-mm-dd',
            constrainInput: false,
            changeMonth: true,
            changeYear: true
        });
    </script>
</body>
</html>