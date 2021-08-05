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
$manufacturer = '';
$procured_by = '';
//echo '<pre>';print_r($_SESSION);exit;
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
    //check product
    if (isset($_REQUEST['product']) && !empty($_REQUEST['product'])) {
        //get product
        $product = $_REQUEST['product'];
        $sCriteria['product'] = $product;
        //set product
        $objPurchaseOrder->item_id = $product;
    }
    //check manufacturer
    if (isset($_REQUEST['manufacturer']) && !empty($_REQUEST['manufacturer'])) {
        //get manufacturer
        $manufacturer = $_REQUEST['manufacturer'];
        //set manufacturer	
        $objPurchaseOrder->manufacturer = $manufacturer;
    }
    //check procured by
    if (isset($_REQUEST['procured_by']) && !empty($_REQUEST['procured_by'])) {
        //get manufacturer
        $procured_by = $_REQUEST['procured_by'];
        //set manufacturer	
        $objPurchaseOrder->procured_by = $procured_by;
        $sCriteria['procured_by'] = $procured_by;
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
    ;
    $_SESSION['sCriteria'] = $sCriteria;
}

//Stock Search
$gp_by = "   ";
$result = $objPurchaseOrder->POSearchLight(1, $wh_id, $gp_by);
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



$strSql2 = "
       SELECT
	tbl_stock_detail.Qty,
	stock_batch.batch_no,
	itminfo_tab.itm_name,
	tbl_stock_master.PkStockID,
	tbl_stock_master.TranNo,
	tbl_stock_master.shipment_id,
	purchase_order.*, sum(tbl_stock_detail.Qty) AS received_qty,
	tbl_warehouse.wh_name AS stkname,
	tbl_itemunits.UnitType,
	tbl_locations.LocName AS procured_by,
	itminfo_tab.qty_carton AS qty_carton_old,
	stakeholder_item.quantity_per_pack AS qty_carton
        FROM
        tbl_stock_master
        INNER JOIN tbl_stock_detail ON tbl_stock_master.PkStockID = tbl_stock_detail.fkStockID
        LEFT JOIN stakeholder ON tbl_stock_detail.manufacturer = stakeholder.stkid
        INNER JOIN stock_batch ON tbl_stock_detail.BatchID = stock_batch.batch_id
        INNER JOIN itminfo_tab ON stock_batch.item_id = itminfo_tab.itm_id
        LEFT JOIN tbl_itemunits ON itminfo_tab.itm_type = tbl_itemunits.UnitType
        INNER JOIN purchase_order ON tbl_stock_master.shipment_id = purchase_order.pk_id 
        INNER JOIN purchase_order_product_details ON purchase_order.pk_id = purchase_order_product_details.po_master_id
        INNER JOIN tbl_warehouse ON purchase_order_product_details.funding_source = tbl_warehouse.wh_id
        INNER JOIN tbl_locations ON purchase_order.procured_by = tbl_locations.PkLocID
        INNER JOIN stakeholder_item ON stock_batch.manufacturer = stakeholder_item.stk_id
        WHERE
                tbl_stock_master.temp = 1 AND
                tbl_stock_master.WHIDTo = '" . $wh_id . "' AND
                tbl_stock_master.CreatedBy = " . $_SESSION['user_id'] . " AND 
                tbl_stock_master.TranTypeID = 1
                AND tbl_stock_master.shipment_id is not null
        ";
//    echo $strSql2;exit;    
$rsSql21 = mysql_query($strSql2);

$temp_voucher_exists = false;
$temp_stock = array();
if (mysql_num_rows($rsSql21) > 0) {

    while ($row = mysql_fetch_assoc($rsSql21)) {
        if (!empty($row['shipment_id'])) {
            $temp_stock[$row['shipment_id']] = $row;
            $temp_voucher_exists = true;
        }
    }
}

$qry = "SELECT
Sum(tbl_stock_detail.Qty) as qty_rcvd, 
tbl_stock_master.po_detail as detail_id
FROM
tbl_stock_master
INNER JOIN tbl_stock_detail ON tbl_stock_master.PkStockID = tbl_stock_detail.fkStockID
WHERE
tbl_stock_master.WHIDTo = '" . $wh_id . "' AND
tbl_stock_master.TranTypeID = 1
GROUP BY
tbl_stock_master.po_detail
";
$res = mysql_query($qry);
$rcvd_arr = array();
while ($row = mysql_fetch_assoc($res)) {
    $rcvd_arr[$row['detail_id']] = $row['qty_rcvd'];
}

//    echo '<pre>';
//print_r($rcvd_arr);exit;
//get all item
$items = $objManageItem->GetAllManageItem();
if (isset($_REQUEST['product']) && !empty($_REQUEST['product'])) {
    //Get All Manufacturers Update
    $manufacturers = $manufacturer_product = $objstk->GetAllManufacturersUpdate($_REQUEST['product']);
}


//------Call these two funcs , for generic approval flow ------
fetch_approval_flow();
fetch_approval_status('po');
//echo '<pre>';
//    print_r($module_approval_flow);
//    print_r($module_approval_status);
//    exit;
?>
<link rel="stylesheet" type="text/css" href="../../public/assets/global/plugins/select2/select2.css"/>
<link rel="stylesheet" type="text/css" href="../../public/assets/admin/pages/css/timeline.css"/>
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
                if (isset($_REQUEST['DO'])) {
                    $strDo = $_REQUEST['DO'];
                    if ($_REQUEST['DO'] == 'Edit') {

                        $shipment_id = $_REQUEST['shipment_id'];
                        $shipment_detail = $objPurchaseOrder->get_shipment_by_id($shipment_id);

                        $contract_delivery_date = $shipment_detail->contract_delivery_date;
                        $ref_no = $shipment_detail->reference_number;
                        $funding_source = $shipment_detail->stk_id;
                        $itm_id = $shipment_detail->item_id;
                        $manufacturer = $shipment_detail->manufacturer;
                        $qty = $shipment_detail->shipment_quantity;
                        $procured_by = $shipment_detail->procured_by;
                        $status = $shipment_detail->status;
                        $po_number = $shipment_detail->po_number;
                        $po_date = $shipment_detail->po_date;
                        $unit_price = $shipment_detail->unit_price;
                        ?>

                        <div class="row">
                            <div class="col-md-12">

                                <div class="widget" data-toggle="collapse-widget">
                                    <div class="widget-head">
                                        <h3 class="heading"><?= $strDo ?> Pipeline Shipments</h3>
                                    </div>
                                    <div class="widget-body">
                                        <form method="POST" name="new_receive" id="new_receive" action="search_shipments_action.php" >
                                            <!-- Row -->
                                            <div class="row">
                                                <div class="col-md-12">

                                                    <div class="col-md-3">
                                                        <div class="control-group">
                                                            <label class="control-label" for="receive_date"> PO Date</label>
                                                            <div class="note note-info">
                                                                <?php echo (!empty($po_date)) ? date('Y-M-d', strtotime($po_date)) : '' ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="control-group">
                                                            <label class="control-label" for="receive_date"> PO Number</label>
                                                            <div class="note note-info">
                                                                <?php echo (!empty($po_number)) ? $po_number : '' ?>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-3">
                                                        <label class="control-label" for="refrence_number"> PO Reference Number<span class="red">*</span> </label>
                                                        <div class="controls">
                                                            <input readonly value="<?= $ref_no ?>" class="form-control input-medium" id="refrence_number" name="refrence_number" type="text" required />
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="col-md-3">
                                                        <div class="control-group">
                                                            <label class="control-label" for="receive_date"> Shipment Date / Delivery Date</label>
                                                            <div class="controls">
                                                                <input class="form-control input-medium"  id="receive_date" tabindex="2" name="receive_date" type="text" value="<?php echo (!empty($contract_delivery_date)) ? $contract_delivery_date : '' ?>" required />
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="control-label" for="qty"> Quantity <span class="red">*</span> </label>
                                                        <div class="controls">
                                                            <input  value="<?= $qty ?>" type="text" class="form-control input-medium num" name="qty" id="qty" autocomplete="off" />
                                                            <span id="product-unit"> </span> <span id="product-unit1" style="display:none;"> </span> </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="control-label" for="qty"> Unit Price</label>
                                                        <div class="controls">
                                                            <input  value="<?= number_format($unit_price, 2) ?>" type="text" class="form-control input-medium num" name="unit_price" id="unit_price" autocomplete="off" />

                                                        </div>
                                                    </div>


                                                    <div class="col-md-3">
                                                        <label class="control-label" for="product"> Status <span class="red">*</span> </label>
                                                        <div class="controls">
                                                            <select name="status" id="status" required="true" class="form-control input-medium">
                                                                <option value=""> Select </option>
                                                                <option <?= (($status == 'Accept') ? ' selected ' : '') ?> value="Accept"> Accept </option>
                                                                <option <?= (($status == 'Cancelled') ? ' selected ' : '') ?> value="Cancelled"> Canceled </option>
                                                                <option <?= (($status == 'Deleted') ? ' selected ' : '') ?> value="Deleted"> Deleted </option>
                                                            </select>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12">

                                                    <div class="col-md-9">
                                                        <label class="control-label" for="firstname"> &nbsp; </label>
                                                        <div class="controls right">
                                                            <button type="submit" class="btn btn-primary" id="add_receive"> <?= $strDo ?> Shipment</button>
                                                            <button type="reset" class="btn btn-info" id="reset"> Reset </button>
                                                            <input type="hidden" name="action" id="action" value="<?php echo $strDo; ?>" />
                                                            <input type="hidden" name="shipment_id" id="shipment_id" value="<?php echo $shipment_id; ?>" />

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>

                            </div>
                        </div>


                        <?php
                    }
                }
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



                                                <div class="col-md-3">
                                                    <div class="control-group">
                                                        <label class="control-label" for="warehouse">Funding Source</label>
                                                        <div class="controls">
                                                            <select name="warehouse" id="warehouse" class="form-control input-medium">
                                                                <option value="">Select</option>
                                                                <?php
//check if record exists
                                                                if (mysql_num_rows($warehouses) > 0) {
                                                                    while ($row = mysql_fetch_object($warehouses)) {
                                                                        ?>
                                                                        <option value="<?php echo $row->wh_id; ?>" <?php if ($warehouse == $row->wh_id) { ?> selected="" <?php } ?>><?php echo $row->wh_name; ?></option>
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
                                                        <label class="control-label" for="product">Product</label>
                                                        <div class="controls">
                                                            <select name="product" id="product" class="input-large  select2me">
                                                                <option value="">Select</option>
                                                                <?php
//check if record exists
                                                                if (mysql_num_rows($items) > 0) {
                                                                    while ($row = mysql_fetch_object($items)) {
                                                                        ?>
                                                                        <option value="<?php echo $row->itm_id; ?>" <?php if ($product == $row->itm_id) { ?> selected="" <?php } ?>><?php echo $row->itm_name; ?></option>
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
                                                                <option value="Accept" <?php if ($searchby == 'Accept') { ?> selected <?php } ?>>Accept</option>
                                                                <option value="Cancelled" <?php if ($searchby == 'Cancelled') { ?> selected <?php } ?>>Canceled</option>
                                                                <option value="Deleted" <?php if ($searchby == 'Deleted') { ?> selected <?php } ?>>Deleted</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12 "> 
                                                <div class="col-md-3 hide" id="ProvincesCol">
                                                    <div class="control-group">
                                                        <label>Procured by </label>
                                                        <div class="controls">
                                                            <select name="procured_by" id="procured_by" class="form-control input-medium">
                                                                <option value="">Select</option>
                                                                <?php
                                                                //Populate select3 combo

                                                                if (isset($_SESSION['user_level']) && $_SESSION['user_level'] == 2) {
                                                                    $objloc->LocLvl = 2;
                                                                    $rsloc = $objloc->GetLocationsById($_SESSION['user_province1']);
                                                                } else {
                                                                    $rsloc = $objloc->GetAllLocationsL2();
                                                                }

                                                                if ($rsloc != FALSE && mysql_num_rows($rsloc) > 0) {
                                                                    while ($RowLoc = mysql_fetch_object($rsloc)) {
                                                                        ?>
                                                                        <option value="<?= $RowLoc->PkLocID ?>" <?php
                                                                        if ($RowLoc->PkLocID == $procured_by) {
                                                                            echo 'selected="selected"';
                                                                        }
                                                                        ?>> <?php echo $RowLoc->LocName; ?> </option>
                                                                                <?php
                                                                            }
                                                                        }
                                                                        ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- Group -->
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label class="control-label">Date From</label>
                                                        <input type="text" class="form-control input-medium" name="date_from" readonly id="date_from" value="<?php echo $date_from; ?>"/>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label class="control-label">Date To</label>
                                                        <input type="text" class="form-control input-medium" name="date_to"  readonly="" id="date_to" value="<?php echo $date_to; ?>"/>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 right">
                                                    <div class="form-group">
                                                        <label class="control-label">&nbsp;</label>
                                                        <div class="form-group">
                                                            <button type="submit" name="search" value="search" class="btn btn-primary">Search</button>
                                                            <button type="reset" class="btn btn-info" id="reset">Reset</button>
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
                        <!-- Widget --> <?php
                        if ($temp_voucher_exists) {
                            ?>
                            <div class="note note-danger">Please process the temporary vouchers of following POs first. They are in draft mode.<br /><br />Q: How to do that ? <br />Ans :Click 'Receive' button at the end of row and then SAVE OR Delete the temporary voucher, as per your requirement.</div>

                            <?php
                        }
                        ?>
                        <!--                            <div class="row">
                                                        <div class="col-md-6">Total amount in Million: <span id="aminm"></span></div>
                                                        <div class="col-md-6">Total amount in Billion: <span id="aminb"></span></div>
                                                    </div>-->
                        <div class="widget" data-toggle="collapse-widget"> 


                            <!-- Widget heading -->
                            <div class="widget-head">
                                <h4 class="heading">Purchase Order Search</h4>
                            </div>

                            <!-- // Widget heading END -->

                            <div class="widget-body"> 

                                <!-- Table --> 
                                <!-- Table -->
                                <table class="shipmentsearch table table-bordered table-condensed">

                                    <!-- Table heading -->
                                    <thead>
                                        <tr>
                                            <th>Sr. No.</th>
                                            <th>PO No</th>
                                            <th>Manual Ref No</th>
                                            <th>PO Date</th>
                                            <th>Product</th>
                                            <th>PO Qty Ordered</th>
                                            <th>Received Qty</th>
                                            <th>Remaining Qty</th>                       
<!--                                            <th>Total Cartons</th>-->
                                            <th>Unit Price</th>                                            
                                            <th>Total Amount</th>
                                            <th>Status</th>
                                            <th>Actions</th>


                                        </tr>
                                    </thead>
                                    <!-- // Table heading END --> 

                                    <!-- Table body -->
                                    <tbody>

                                        <!-- Table row -->
                                        <?php
                                        $i = 1;
                                        $transNo = '';
                                        $g_total = array();
                                        if ($temp_voucher_exists == false) {
                                            if ($result != FALSE) {
                                                //fetch result
                                                while ($row = mysql_fetch_object($result)) {

                                                    $s_q = isset($row->shipment_quantity) ? $row->shipment_quantity : '0';
                                                    $r_q = isset($rcvd_arr[$row->product_detail_id]) ? $rcvd_arr[$row->product_detail_id] : '0';
                                                    $remaining_q = $s_q - $r_q;

                                                    $cl = '';
                                                    if ($row->status == 'Cancelled') {
                                                        $cl = 'danger';
                                                    } else {
                                                        // excluding the CANCELLED shipments out of the totals
                                                        @$g_total['po'] += $s_q;
                                                        @$g_total['t_price'] += $s_q * $row->unit_price * $row->dollar_rate;
                                                        @$g_total['rcvd'] += $r_q;
                                                        @$g_total['remaining'] += $remaining_q;
                                                    }
                                                    ?>
                                                    <tr class="grade <?= $cl ?>" >
                                                        <td class="text-center"><?php echo $i; ?></td>
                                                        <td><?php echo $row->po_number; ?></td>
                                                        <td><?php echo $row->reference_number; ?></td>
                                                        <td class="editableSinglex" id="<?php echo $row->pk_id; ?>"><?php echo date("Y-M-d", strtotime($row->po_date)); ?></td>

                                                        <td><?php echo $row->itm_name; ?></td>
                                                        <td ><?php echo number_format($s_q); ?></td>
                                                        <td ><?php echo number_format($r_q); ?></td>
                                                        <td ><?php echo number_format($remaining_q); ?></td>


                                                            <!--                                                        <td ><?php
                                                        //carton qty
                                                        //@$cartonQty = $row->unit_price * $s_q;
                                                        //echo (floor($cartonQty) != $cartonQty) ? number_format($cartonQty, 2) : number_format($cartonQty);
                                                        ?>
                                                            </td>-->
                                                        <td ><?php echo number_format($row->unit_price, 2) ?></td>                                                        
                                                        <td >
                                                            <?php
                                                            echo $row->currency;
                                                            echo ' ' . number_format($s_q * $row->unit_price);

                                                            if ($row->currency != 'PKR') {
                                                                echo ' (PKR ' . number_format($s_q * $row->unit_price * $row->dollar_rate) . ')';
                                                            }
                                                            ?>
                                                        </td>

                                                        <?php
                                                        $cls2 = 'info';
                                                        if ($r_q > 0 && $remaining_q > 0) {
                                                            $st = 'Partially Received';
                                                            $cls = "";

                                                            if ($row->status == 'Cancelled') {
                                                                $st = 'Partially Received & Cancelled';
                                                            }
                                                        } elseif ($r_q > 0 && $remaining_q == 0) {
                                                            $st = 'Received';
                                                            $cls = " success ";
                                                        } elseif ($row->status == 'Received') {
                                                            $st = $row->status;
                                                            $cls = " success ";
                                                        } else {
                                                            $st = $row->status;
                                                            $cls = "";
                                                        }
                                                        if ($row->status == 'Cancelled') {
                                                            $cls = "danger";
                                                        }
                                                        if ($row->status == 'Completed') {
                                                            $cls2 = 'warning';
                                                        }
                                                        ?>
                                                        <td  class=" <?= $cls ?> ">
                                                            <span class="badge badge-<?= $cls2 ?>"><?= $st ?></span>
                                                            <?php echo show_approval_status('po', $row->pk_id); ?>
                                                            <span class="badge badge-dark show_app_history" module="po" unique_id="<?= $row->pk_id ?>"><i class="fa fa-history"></i> History</span>
                                                        </td>
                                                        <?php ?>
                                                        <td>
                                                            <?php
                                                            if ($_SESSION['user_role'] != 88) {
                                                                if ($row->status != 'Cancelled' && ($row->status != 'Received')) {
                                                                    echo '<a class="badge badge-info" style="padding-bottom:20px !important;" href="add_purchase_order_multiple.php?DO=Update&po_id=' . $row->pk_id . '"><i class="fa fa-edit"></i> Update</a>';

                                                                    if (($row->status != 'Completed')) {
                                                                        if ($r_q == 0) {
                                                                            echo '';
                                                                            echo '<a class="badge badge-danger" style="padding-bottom:20px !important;" href="delete_purchase_order.php?action=delete&po_id=' . $row->pk_id . '&detail_id=' . $row->product_detail_id . '" onclick="return confirm(\'Are you sure you want to parmanently delete this entry?\')"><i class="fa fa-times"></i> Delete</a>';
                                                                        }
                                                                        //echo ' | ';

                                                                        if ($remaining_q > 0) {
                                                                            echo ' <a class="badge badge-success" style="padding-bottom:20px !important;"  href="receive_shipment.php?shipment_id=' . $row->pk_id . '&detail_id=' . $row->product_detail_id . '"><i class="fa fa-truck"></i> Receive</a>';
                                                                        }
                                                                    }
                                                                }
                                                                echo ' <a class="badge badge-warning" style="padding-bottom:20px !important;"  href="print_po.php?po_id=' . $row->pk_id . '"><i class="fa fa-print"></i> Print</a>';
                                                            }
                                                            echo ' <a class="badge badge-secondary" style="padding-bottom:20px !important;"  href="view_po.php?po_id=' . $row->pk_id . '"><i class="fa fa-eye"></i> View PO</a>';

//                                                        print_r($st);exit;
                                                            if ($st == 'Received' || $st == 'Partially Received') {
//                                                            print_r("intoif");exit;
                                                                $value = $objPurchaseOrderProductDetails->getReceivedVouhcers($row->pk_id, $row->product_detail_id);
                                                                if (!empty($value)) {
                                                                    echo $value;
                                                                } else {
                                                                    //echo "No quantity received";
                                                                }
                                                            }
                                                            ?>
                                                            <?php
                                                            if ($_SESSION['user_role'] != 88) {
                                                                echo show_approval_btn('po', $row->pk_id);
                                                            }
                                                            ?>
                                                        </td>

                                                    </tr>
                                                    <?php
                                                    $i++;
                                                }
                                                ?>
                                                <tr>
                                                    <td class="bg-grey">Total</td>
                                                    <td class="bg-grey"></td>
                                                    <td class="bg-grey"></td>
                                                    <td class="bg-grey"></td>
                                                    <td ><?= (!empty($g_total['po']) ? number_format($g_total['po']) : '0') ?></td>
                                                    <td ><?= (!empty($g_total['rcvd']) ? number_format($g_total['rcvd']) : '0') ?></td>
                                                    <td ><?= (!empty($g_total['remaining']) ? number_format($g_total['remaining']) : '0') ?></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td >PKR <?= number_format(!empty($g_total['t_price']) ? $g_total['t_price'] : '0') ?></td>
                                                    <td></td>
                                                    <td></td>
                                                </tr>
                                                <tr>

                                                    <td class="bg-grey"></td>
                                                    <td class="bg-grey"></td>
                                                    <td class="bg-grey"></td>
                                                    <td class="bg-grey"></td>
                                                    <td class="bg-grey"></td>
                                                    <td class="bg-grey"></td>
                                                    <td class="bg-grey"></td>
                                                    <td class="bg-grey"></td>
                                                    <td >Total in Million</td>
                                                    <td id="total_in_m">PKR <?= (!empty($g_total['t_price']) ? nice_number($g_total['t_price'], 'm') : '0') ?>M</td>

                                                    <td class="bg-grey"></td>
                                                    <td class="bg-grey"></td>
                                                </tr>
                                                <tr>
                                                    <td class="bg-grey"></td>
                                                    <td class="bg-grey"></td>
                                                    <td class="bg-grey"></td>
                                                    <td class="bg-grey"></td>
                                                    <td class="bg-grey"></td>
                                                    <td class="bg-grey"></td>
                                                    <td class="bg-grey"></td>
                                                    <td class="bg-grey"></td>
                                                    <td>Total in Billion</td>
                                                    <td id="total_in_b">PKR <?= (!empty($g_total['t_price']) ? nice_number($g_total['t_price'], 'b') : '0') ?>B</td>

                                                    <td class="bg-grey"></td>
                                                    <td class="bg-grey"></td> 
                                                </tr>
                                                <?php
                                            }
                                        } else {
                                            foreach ($temp_stock as $k => $row2) {
                                                $row = (object) $row2;
                                                //echo '<pre>';print_r($row2);print_r($row);exit;
                                                $s_q = isset($row->shipment_quantity) ? $row->shipment_quantity : '0';
                                                $r_q = isset($row->received_qty) ? $row->received_qty : '0';
                                                $remaining_q = $s_q - $r_q;
                                                ?>
                                                <tr class="gradeX" >
                                                    <td class="text-center"><?php echo $i; ?></td>
                                                    <td class="editableSinglex" id<?php echo $row->pk_id; ?>"><?php echo date("Y-M-d", strtotime($row->contract_delivery_date)); ?></td>

                                                    <td><?php echo $row->reference_number; ?></td>
                                                    <td><?php echo $row->stkname; ?></td>
                                                    <td><?php echo $row->procured_by; ?></td>
                                                    <td><?php echo $row->itm_name; ?></td>
                                                    <td ><?php echo number_format($s_q); ?></td>
                                                    <td ><?php echo number_format($r_q); ?></td>
                                                    <td ><?php echo number_format($remaining_q); ?></td>
                                                    <td ><?php echo $row->UnitType ?></td>
                                                    <td >
                                                    </td>
                                                    <?php
                                                    if ($r_q > 0 && $remaining_q > 0) {
                                                        $st = 'Partially Received';
                                                        $cls = "";
                                                    } elseif ($r_q > 0 && $remaining_q == 0) {
                                                        $st = 'Received';
                                                        $cls = " success ";
                                                    } elseif ($row->status == 'Received') {
                                                        $st = $row->status;
                                                        $cls = " success ";
                                                    } else {
                                                        $st = $row->status;
                                                        $cls = "";
                                                    }
                                                    ?>
                                                    <td  class=" <?= $cls ?> ">
                                                        <?= $st ?>

                                                    </td>

                                                    <td>
                                                        <?php
                                                        echo '<a  href="receive_shipment.php?shipment_id=' . $row->pk_id . '">Receive</a>';
                                                        ?>

                                                    </td>
                                                </tr>
                                                <?php
                                                $i++;
                                            }
                                        }
                                        ?>
                                        <!-- // Table row END -->
                                    </tbody>
                                    <!-- // Table body END -->

                                </table>
                                <!-- // Table END -->
                                <?php if ($result != FALSE) { ?>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="left pull-left col-md-6" style="margin-top:10px !important;">
                                                <div class="col-md-2">
                                                    <button id="print_shipment_summary" type="button" class="btn btn-warning">Print</button>
                                                </div>
                                                <div class="col-md-2">
                                                    <label class="control-label">Summary:</label>
                                                </div>
                                                <div class="col-md-2">
                                                    <select class="form-control input-medium" id="print_summary_dd">
                                                        <option value="funding_source_wise">Funding Source Wise</option>
                                                        <option value="product_wise">Product Wise</option>
                                                    </select>
                                                </div>
                                                <div style="clear:both;"></div>
                                            </div>

                                            <div class="right pull-right col-md-6" style="margin-top:10px !important;float:left">
                                                <div class="col-md-2">
                                                    <button id="print_shipment_detail" type="button" class="btn btn-warning">Print</button>

                                                </div>
                                                <div class="col-md-1">
                                                    <label class="control-label">Detail:</label>
                                                </div>
                                                <div class="col-md-3">
                                                    <select class="form-control input-medium"  id="print_detail_dd">

                                                        <option value="funding_source_wise">Funding Source Wise</option>
                                                        <option value="product_wise">Product Wise</option>
                                                    </select>
                                                </div>
                                                <div style="clear:both;"></div>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>

                        <!-- // Content END --> 
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- // Row END -->
    <div id="approval_history" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content"> 
                <div class="modal-header">
                    Approvals history
                </div>
                <div class="modal-body" id="app_history_body">
                    Loading approval history ... 
                </div>
                <div class="modal-footer"> <a data-dismiss="modal" class="btn btn-default" href="#">Close</a>  </div>
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
    if (!empty($_SESSION['success'])) {
        if ($_SESSION['success'] == 1) {
            //display message
            $text = 'Data has been saved successfully';
        }
        if ($_SESSION['success'] == 2) {
            //display message
            $text = 'Data has been deleted successfully';
        }
        ?>
        <script>
            var self = $('[data-toggle="notyfy"]');
            notyfy({
                force: true,
                text: '<?php echo $text; ?>',
                type: 'success',
                layout: self.data('layout')
            });
        </script>
        <?php
        unset($_SESSION['success']);
    }
    ?>

    <?php
    if ($temp_voucher_exists) {
        ?>
        <script type="text/javascript">
            $(document).ready(function () {
                $('#shipment_filter_div').collapse('500');
            });

        </script>
        <?php
    }
    ?>

    <script>
        $("#receive_date").datepicker({
            dateFormat: 'yyyy-mm-dd',
            constrainInput: false,
            changeMonth: true,
            changeYear: true
        });
        $(document).ready(function () {
            $('.show_app_history').on('click', function () {
                $("#approval_history").modal('show');
                $.ajax({
                    type: "POST",
                    url: "ajax_show_app_history.php",
                    data: {
                        module: $(this).attr('module'),
                        unique_id: $(this).attr('unique_id')
                    },
                    dataType: 'html',
                    success: function (data) {

                        $('#app_history_body').html(data);

                        $('#app_history_body').slideDown("slow");
                    }
                });
            });
        });
    </script>
    <!-- END JAVASCRIPTS -->
</body>
<!-- END BODY -->
</html>