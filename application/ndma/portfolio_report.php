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
$searchby = '';
$warehouse = '';
$product = '';
$po = '';
$vendor = '';
$procured_by = '';
$search_as = '';
//echo '<pre>';print_r($_REQUEST);exit;
//check if submitted
if (isset($_REQUEST['search']) && !empty($_REQUEST['search'])) {
//check search by
    if (!empty($_REQUEST['status']) && !empty($_REQUEST['status'])) {
        //get search by
        $searchby = $_REQUEST['status'];
        $sCriteria['status'] = $searchby;
        $objPurchaseOrder->status = $searchby;
    }
    //check warehouse
    if (isset($_REQUEST['warehouse']) && !empty($_REQUEST['warehouse'])) {
        //get warehouse
        $warehouse = $_REQUEST['warehouse'];
        $sCriteria['warehouse'] = $warehouse;
        //set from warehouse
        $objPurchaseOrder->WHID = $warehouse;
    }
    if (isset($_REQUEST['po']) && !empty($_REQUEST['po'])) {
        //get warehouse
        $po = $_REQUEST['po'];
        //set from warehouse
        $objPurchaseOrder->po_number = $po;
    }

    //check manufacturer
    if (isset($_REQUEST['vendor']) && !empty($_REQUEST['vendor'])) {
        //get manufacturer
        $vendor = $_REQUEST['vendor'];
        //set manufacturer	
        $objPurchaseOrder->manufacturer = $vendor;
    }
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
    if (!empty($_REQUEST['search_as'])) {
        $sCriteria['search_as'] = $_REQUEST['search_as'];
        $search_as = $_REQUEST['search_as'];
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
$result = $objPurchaseOrder->PortfolioSearch(1, $wh_id, $gp_by, $search_as);
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
                                                        <label class="control-label" for="po">P.O #</label>
                                                        <div class="controls">
                                                            <select name="po" id="po" class="input-medium select2me">
                                                                <option value="">Select</option>
                                                                <?php
                                                                if (mysql_num_rows($po_numbers) > 0) {
                                                                    while ($row = mysql_fetch_object($po_numbers)) {
                                                                        $sel = "";
                                                                        if ($po == $row->po_number) {
                                                                            $sel = " selected ";
                                                                        }
                                                                        ?>
                                                                        <option value="<?php echo $row->po_number; ?>" <?= $sel ?> ><?php echo $row->po_number; ?></option>
                                                                        <?php
                                                                    }
                                                                }
                                                                ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="control-group">
                                                        <label class="control-label" for="vendor">Vendor Name</label>
                                                        <div class="controls">
                                                            <select name="vendor" id="vendor" class="input-medium  select2me">
                                                                <option value="">Select</option>
                                                                <?php
                                                                if (mysql_num_rows($vendors) > 0) {
                                                                    while ($row = mysql_fetch_object($vendors)) {
                                                                        $sel = "";
                                                                        if ($vendor == $row->manufacturer) {
                                                                            $sel = " selected ";
                                                                        }
                                                                        ?>
                                                                        <option value="<?php echo $row->manufacturer; ?>" <?= $sel ?> ><?php echo $row->stkname; ?></option>
                                                                        <?php
                                                                    }
                                                                }
                                                                ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
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
                                                <div class="col-md-3">
                                                    <div class="control-group">
                                                        <label class="control-label">Status</label>
                                                        <div class="controls">
                                                            <select name="status" id="status" class="form-control input-medium">
                                                                <option value="">Select</option>
                                                                <option value="Pre Shipment" <?php if ($searchby == 'Pre Shipment') { ?> selected <?php } ?>>Pre Shipment</option>
                                                                <option value="Tender" <?php if ($searchby == 'Tender') { ?> selected <?php } ?>>Tender</option>
                                                                <option value="PO" <?php if ($searchby == 'PO') { ?> selected <?php } ?>>Purchase Order</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="control-group">
                                                        <label class="control-label">Search As</label>
                                                        <div class="controls">
                                                            <select name="search_as" id="search_as" class="form-control input-medium">
                                                                <option value="">Select</option>
                                                                <option value="summary" <?php if ($search_as == 'summary') { ?> selected <?php } ?>>Products Summary</option>
                                                                <option value="detail" <?php if ($search_as == 'detail') { ?> selected <?php } ?>>Products Detail</option>
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
                                <table class="shipmentsearchx table table-bordered table-condensed" id="myTable">
                                    <thead>
                                        <tr>
                                            <th rowspan="2">Sr. No.</th> 
                                            <th rowspan="2">Item</th>
                                            <th style="background-color:#009C00;color:white;align-content: center">Total Quantity Ordered</th>
                                            <th colspan="2" style="background-color:#009C00;color:white;">Total Quantity Received</th>
                                            <th colspan="2" style="background-color:#009C00;color:white;">Total Remaining Quantity</th>
                                            <th style="background-color:#009C00;color:white;">Unit Price</th>
                                            <th style="background-color:#009C00;color:white;">Total Price</th> 
                                        </tr>
                                        <tr>
                                            <th>#s</th>
                                            <th>#s</th>
                                            <th>%</th>
                                            <th>#s</th>
                                            <th>%</th>
                                            <th>#s</th>
                                            <th>#s</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $g_total_price=0;
                                        $i = 1;
                                        $transNo = '';
                                        $required_q = 0;
                                        $g_total = array();
                                        if ($result != FALSE) {
                                            //fetch result
                                            while ($row = mysql_fetch_object($result)) {

                                                $s_q = isset($row->shipment_quantity) ? $row->shipment_quantity : '0';
                                                $r_q = isset($row->received_qty) ? $row->received_qty : '0';
                                                $remaining_q = $s_q - $r_q;
                                                $received_perc = ($r_q / $s_q * 100);
                                                $remaining_perc = ($remaining_q / $s_q * 100);
                                                $cl = '';
                                                if ($row->status == 'Cancelled') {
                                                    $cl = 'danger';
                                                }
                                                if ($search_as == 'summary') {
                                                    $unit_price_arr = explode(',', $row->concatenated_unit);
                                                    $ship_qty_arr = explode(',', $row->concatenated_qty);
                                                }
//                                                print_r($ship_qty_arr[0] * $unit_price_arr[0]);exit;
                                                ?>
                                                <tr class="gradeX <?= $cl ?>" >
                                                    <td class="text-center"><?php echo $i; ?></td>
                                                    <td><?php echo $row->itm_name; ?></td>
                                                    <td align="right"><?php echo ($s_q); ?></td>
                                                    <td align="right"><?php echo number_format($r_q); ?></td>
                                                    <td align="right"><?php echo number_format($received_perc, 2) . '%' ?></td>
                                                    <td align="right"><?php echo $remaining_q ?></td>
                                                    <td align="right"><?php echo number_format($remaining_perc, 2) . '%'; ?></td>
                                                    <td align="right"><?php echo number_format($row->unit_price, 2); ?></td>

                                                    <td align="right"><?php
                                                    if ($search_as == 'summary') {
                                                        $sum=0;
                                                        for ($i = 0; $i < sizeof($ship_qty_arr); $i++) {
//                                                            print_r('into for');exit;
                                                            $sum += ($ship_qty_arr[$i] * $unit_price_arr[$i]);
                                                        }
//                                                        print_r($sum); 
                                                        echo number_format($sum,2);
                                                        $g_total_price += $sum;
                                                    } else {
                                                        echo number_format($s_q * $row->unit_price);
                                                        $g_total_price += ($s_q * $row->unit_price);
                                                    }
                                                ?></td> 




                                                </tr>

        <?php
        $i++;
    }
    ?>
            <tr class="gradeX " >
                <td class="text-center"></td>
                <td>Total</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td align="right"><?=number_format($g_total_price)?></td>
            </tr>
            <tr class="gradeX " >
                <td class="text-center"></td>
                <td>Total in Millions</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td align="right"><?=nice_number($g_total_price,'m')?> Mill</td>
            </tr>
            <tr class="gradeX " >
                <td class="text-center"></td>
                <td>Total in Billions</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td align="right"><?=nice_number($g_total_price,'b')?> Bill</td>
            </tr>
        <?php
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
<?php
//include footer
include PUBLIC_PATH . "/html/footer.php";
?>
<!--    <script src="<?php echo PUBLIC_URL; ?>js/dataentry/add-shipment.js"></script> -->
    <script src="<?php echo PUBLIC_URL; ?>js/jquery.inlineEdit_date.js"></script> 
    <script src="<?php echo PUBLIC_URL; ?>js/dataentry/search-shipments.js"></script>
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