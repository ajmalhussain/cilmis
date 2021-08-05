<?php
/**
 * add purchase order
 */
include("../includes/classes/AllClasses.php");
include(PUBLIC_PATH . "html/header.php");
$TranNo = '';
$TranRef = '';
$from_id = 95526; //default Federal Govt
$productID = 0;
$unit_price = 0;
$stock_id = 0;
$manufacturer = '';
$local_foreign = 'local'; //default val
$sub_cat = '';
$currency = 'PKR'; // default PKR
$country = '130'; //pakistan
$sub_cat = 'emergency'; //default val        
$userid = $_SESSION['user_id'];
if (!empty($_SESSION['user_warehouse']))
    $wh_id = $_SESSION['user_warehouse'];
else
    $wh_id = 123;

$type = 'Add';
if (isset($_REQUEST['DO']) && !empty($_REQUEST['DO'])) {
    //get receive date
    $type = $_REQUEST['DO'];
}

$po_id = '';

if ($type == 'Update') {
    $po_id = $_REQUEST['po_id'];
    $po_data = $objPurchaseOrder->find_by_id($po_id);
    if (isset($po_data)) {
        //echo '<pre>';print_r($po_data);
        $po_date = $po_data->po_date;
        $po_number = $po_data->po_number;
        $reference_number = $po_data->reference_number;
        $productID = $po_data->item_id;
        $manufacturer = $po_data->manufacturer;
        $shipment_quantity = $po_data->shipment_quantity;
        $stk_id = $po_data->stk_id;
        $procured_by = $po_data->procured_by;
        $created_date = $po_data->created_date;
        $created_by = $po_data->created_by;
        $modified_by = $po_data->modified_by;
        $modified_date = $po_data->modified_date;
        $status = $po_data->status;
        $wh_id = $po_data->wh_id;
        $unit_price = $po_data->unit_price;
        $dollar_rate = $po_data->dollar_rate;
        $contact_no = $po_data->contact_no;
        $signing_date = $po_data->signing_date;
        $from_id = $po_data->funding_source;
        $adv_payment_release = $po_data->adv_payment_release;
        $contract_delivery_date = $po_data->contract_delivery_date;
        $po_accept_date = $po_data->po_accept_date;
        $po_cancelled_date = $po_data->po_cancelled_date;
        $po_delete_date = $po_data->po_delete_date;
        $reqqty = $objManageItem->GetProductReq($productID);
        $amount = number_format($shipment_quantity * $unit_price);
        $amountpkr = number_format($shipment_quantity * $unit_price * $dollar_rate);
        $currency = $po_data->currency;
        $local_foreign = $po_data->local_foreign;
        $sub_cat = $po_data->sub_cat;
        $country = $po_data->country;
        $tender_no = $po_data->tender_no;


        switch ($status) {
            case 'Active':
                $po_status_date = $po_accept_date;
                break;
            case 'Canceled':
                $po_status_date = $po_cancelled_date;
                break;
            case 'InActive':
                $po_status_date = $po_delete_date;
                break;
        }

        $po_data = $objPurchaseOrderDetails->find_by_id($po_id);
        $count = 1;
        foreach ($po_data as $row) {
            $ddate[$count] = $row->delivery_date;
            $dunit[$count] = $row->total_unit;
            $ddelivered[$count] = $row->delivered;
            $dbalance[$count] = $row->balance;
            $count++;
        }
    }
} else {
    $strSql = "SELECT
	CONCAT(
		'PO',
		DATE_FORMAT(
			purchase_order.created_date,
			'%y%m'
		),
		LPAD(
			(
				SELECT
					COUNT(
						DISTINCT purchase_order.reference_number
					)+1
				FROM
					purchase_order
				GROUP BY
					DATE_FORMAT(
						purchase_order.created_date,
						'%Y-%m'
					)
				ORDER BY
					purchase_order.created_date DESC
				LIMIT 1
			),
			4,
			0
		)
	) po_number,
purchase_order.reference_number
FROM
	purchase_order
        WHERE
purchase_order.wh_id = $wh_id
ORDER BY
	purchase_order.pk_id DESC
LIMIT 1";
    //query result
    $rsSql = mysql_query($strSql) or die("Error GetProduct data");

    if (mysql_num_rows($rsSql) > 0) {
        $po_data = mysql_fetch_assoc($rsSql);
        $po_number = $po_data['po_number'];

//        $dataArr1 = explode(' ', $po_date);
//        $time1 = date('H:i:s', strtotime($dataArr1[1] . $dataArr1[2]));
//        $po_date = dateToUserFormat($dataArr1[1]).' '.$time1;
    }
}

//from warehouse
$wh_from = '';
//pk stock id
$PkStockID = '';
//check pk stock id

if (!empty($tempstocksIssue) && mysql_num_rows($tempstocksIssue) > 0) {
    //fetch result
    $result = mysql_fetch_object($tempstocksIssue);
    //stock id
    $stock_id = $result->PkStockID;
    //from id
    $from_id = $result->WHIDFrom;
    //from warehouse
    $wh_from = $objwarehouse->GetWHByWHId($from_id);
    //transaction date
    $TranDate = $result->TranDate;
    //transaction number
    $TranNo = $result->TranNo;
    //transaction ref
    $TranRef = $result->TranRef;
    //Get Last Insered Temp Stocks Receive List
    $tempstocksIssueDet = $objStockMaster->GetLastInseredTempStocksReceiveList($userid, $wh_id, 1);
    if (!empty($tempstocksIssueDet)) {
        //fetch result
        $result1 = mysql_fetch_object($tempstocksIssueDet);
        if (!empty($result1)) {
            //product id
            //$productID = $result1->itm_id;
            //unit price
            $unit_price = $result1->unit_price;
            //manufacturer
            $manufacturer = $result1->manufacturer;
        }
    }
}
//Get Temp Stocks Receive List
$tempstocks = $objStockMaster->GetTempStocksReceiveList($userid, $wh_id, 1);
if (!empty($tempstocks) && mysql_num_rows($tempstocks) > 0) {
    
} else {
    $objStockMaster->PkStockID = $stock_id;
    $objStockMaster->delete();
}
//Get User Warehouses
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
    $join1 .= " LEFT JOIN funding_stk_prov ON tbl_warehouse.wh_id = funding_stk_prov.funding_source_id ";
    //$where1 .= " AND tbl_stock_master.WHIDTo = $wh_id ";
}

//query copied from clsswharehouse
//$strSql = "SELECT
//                    tbl_warehouse.wh_name,
//                    tbl_warehouse.wh_id,
//                    funding_stk_prov.province_id
//            FROM
//            tbl_warehouse
//            INNER JOIN tbl_stock_master ON tbl_stock_master.WHIDFrom = tbl_warehouse.wh_id
//            INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
//            LEFT JOIN funding_stk_prov ON tbl_warehouse.wh_id = funding_stk_prov.funding_source_id
//            $join1
//            WHERE
//            tbl_stock_master.TranTypeID = 1
//            $where1
//            GROUP BY tbl_warehouse.wh_name
//            ORDER BY
//            stakeholder.stkorder ASC";
//            
//query copied from clsswharehouse
$strSql = "SELECT
                    tbl_warehouse.wh_name,
                    tbl_warehouse.wh_id,
                    funding_stk_prov.province_id
            FROM
            tbl_warehouse
            INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
            $join1
            WHERE
            stakeholder.stk_type_id = 2
            $where1
            and tbl_warehouse.wh_id > 97154
            GROUP BY tbl_warehouse.wh_name
            ORDER BY
            stakeholder.stkorder ASC";

//echo $strSql;
$warehouses = mysql_query($strSql) or die("Error Getwh");
$strSql = " SELECT
countries.id,
countries.`name`,
countries.`status`
FROM
countries
WHERE
countries.`status` = 1
ORDER BY
countries.`name` ASC
";

//echo $strSql;
$countries = mysql_query($strSql);
//Get Procured By

if (isset($_SESSION['user_level']) && $_SESSION['user_level'] == 2) {
    $objloc->LocLvl = 2;
    $procured_by = $objloc->GetLocationsById($_SESSION['user_province1']);
} else {
    $procured_by = $objwarehouse->GetProvincialLocations();
}
//Get All Manage Item
$items = $objManageItem->GetAllManageItem();
//Get All Item Units
$units = $objItemUnits->GetAllItemUnits();
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
                <!-- BEGIN PAGE HEADER-->
                <div class="row">
                    <div class="col-md-12">
                        <div class="widget" data-toggle="collapse-widget">
                            <div class="widget-head">
                                <h3 class="heading"><?php echo $type; ?> Purchase Orders</h3>
                            </div>
                            <div class="widget-body">
                                <form method="POST" name="new_receive" id="new_receive" action="add_purchase_order_action.php" >
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label class="control-label" for="product"> Product / Item <span class="red">*</span> </label>
                                            <div class="controls">
                                                <select name="product" id="product" required="true" class="input-medium  select2me"
                                                        <?php 
                                                        if($type=="Update"){
                                                            echo 'disabled';
                                                        }
                                                        ?>
                                                        >
                                                    <option value=""> Select </option>
                                                    <?php
//check if result exists
                                                    if (mysql_num_rows($items) > 0) {
                                                        //fetch results
                                                        while ($row = mysql_fetch_object($items)) {

                                                            $sel = '';
                                                            if ($productID == $row->itm_id) {
                                                                $sel = 'selected=';
                                                            }
                                                            echo "<option value=" . $row->itm_id . " " . $sel . " >" . $row->itm_name . "</option>";
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                                <?php
                                                if($type=="Update"){
                                                  ?>
                                                <input type="hidden" id="product" name="product" value="<?php echo $productID?>">
                                                    <?php
                                                }
                                                
                                                ?>
                                            </div>
                                        </div>
                                        <div class="col-md-3 <?php if ($type == "Update") { ?>hide<?php } ?>" style="margin-top: 30px;" >
                                            <a class="btn btn-xs green " href="../ndma/ManageItems_by_stk.php" target="_blank" ><i class="fa fa-plus"></i> Add New Item / Product</a>
                                        </div>
                                        <div class="col-md-3 ">
                                            <label class="control-label" for="manufacturer"> Vendor <span class="red">*</span> </label>
                                            <div class="controls">
                                                <select name="manufacturer" id="manufacturer" class="input-medium  select2me"
                                                        <?php 
                                                        if($type=="Update"){
                                                            echo 'disabled';
                                                        }
                                                        ?>
                                                        >
                                                    <option value="">Select</option>
                                                    <?php
                                                    if (!empty($productID)) {
                                                        $checkManufacturer = mysql_query("SELECT
                                                stakeholder.stkid,
                                                stakeholder_item.stk_id,
                                                CONCAT(stakeholder.stkname, ' | ' ,IFNULL(stakeholder_item.brand_name, '')) AS stkname
                                        FROM
                                                stakeholder
                                        INNER JOIN stakeholder_item ON stakeholder.stkid = stakeholder_item.stkid
                                        WHERE
                                                stakeholder.stk_type_id = 3
                                        AND stakeholder_item.stk_item = " . $productID . "
                                        ORDER BY
                                                stakeholder.stkname ASC") or die(mysql_error());
                                                        $manufacturer1 = mysql_num_rows($checkManufacturer);
                                                        echo '<option value="">Select</option>';
                                                        while ($val = mysql_fetch_assoc($checkManufacturer)) {
                                                            $sel = '';
                                                            if ($manufacturer == $val['stk_id']) {
                                                                $sel = 'selected=';
                                                            }
                                                            echo '<option value="' . $val['stk_id'] . '" ' . $sel . '>' . $val['stkname'] . '</option>';
                                                        }
                                                    }
                                                    ?>

                                                </select>
                                                <?php
                                                if($type=="Update"){
                                                  ?>
                                                <input type="hidden" id="manufacturer" name="manufacturer" value="<?php echo $manufacturer?>">
                                                    <?php
                                                }
                                                
                                                ?>
                                            </div>
                                        </div>
                                        <div class="col-md-1 <?php if ($type == "Update") { ?>hide<?php } ?>" style="margin-top: 30px; "> <a class="btn btn-xs btn-primary alignvmiddle" style="display:none;" id="add_m_p"  onclick="javascript:void(0);" data-toggle="modal"  href="#modal-manufacturer"><i class="fa fa-plus"></i> Add New Vendor</a> </div>
                                    </div>

                                    <!-- Row -->
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label class="control-label" for="receive_from">System PO#<span class="red">*</span> </label>
                                            <div class="controls">
                                                <input class="form-control" name="system_po" id="system_po" value="<?php echo (!empty($po_number) ? $po_number : '' ) ?>" type="text" readonly=""/>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="control-label" for="refrence_number"> Manual PO#<span class="red">*</span> (Max:150 chars)</label>
                                            <div class="controls">
                                                <input class="form-control" id="refrence_number" value="<?php echo (!empty($reference_number) ? $reference_number : '' ) ?>" name="refrence_number" maxlength="150" type="text" required="" />
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="control-group">
                                                <label class="control-label" for="po_date"> Purchase Order Date</label>
                                                <div class="controls">
                                                    <input class="form-control"  id="po_date" tabindex="2" name="po_date" type="text" value="<?php echo (!empty($po_date)) ? date("d/m/Y", strtotime($po_date)) : date("d/m/Y"); ?>" required="" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="control-label" for="reqqty"> Required QTY <span class="red">*</span> </label>
                                            <div class="controls">
                                                <input type="text" class="form-control num" name="reqqty" id="reqqty" value="<?php echo (!empty($reqqty) ? number_format($reqqty) : '' ) ?>" autocomplete="off" readonly="" />
                                                <!--<span id="product-unit"> </span> <span id="product-unit1" style="display:none;"> </span>--> 
                                            </div>
                                        </div>

                                    </div>


                                    <div class="row">

                                        <div class="col-md-3">
                                            <label class="control-label" for="contact_no"> Contract No </label>
                                            <div class="controls">
                                                <input type="text" class="form-control" style="text-align:right" name="contact_no" id="contact_no" value="<?php echo (!empty($contact_no) ? $contact_no : '' ) ?>" />
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="control-label" for="signing_date"> Signing Date </label>
                                            <div class="controls">
                                                <input type="text" class="form-control" style="text-align:right" name="signing_date" id="signing_date" value="<?php echo (!empty($po_date)) ? date("d/m/Y", strtotime($po_date)) : date("d/m/Y"); ?>" />
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="control-label" for="qty"> QTY Ordered <span class="red">*</span> </label>
                                            <div class="controls">
                                                <input type="text" class="form-control num" name="qty" id="qty" autocomplete="off" value="<?php echo (!empty($shipment_quantity) ? $shipment_quantity : '' ) ?>" />
                                                <span id="product-unit"> </span> <span id="product-unit1" style="display:none;"> </span> </div>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="control-label" for="receive_from"> Received From (Funding Source)<span class="red">*</span> </label>
                                            <div class="controls">
                                                <select name="receive_from" id="receive_from" required="true" class="form-controlx input-medium select2me" value="<?php echo (!empty($funding_source) ? $funding_source : '' ) ?>" >
                                                    <option value="">Select</option>
                                                    <?php
//check if result exists
                                                    if (mysql_num_rows($warehouses) > 0) {
                                                        //fetch result
                                                        while ($row = mysql_fetch_object($warehouses)) {
                                                            ?>
                                                            <option value="<?php echo $row->wh_id; ?>" <?php if ($from_id == $row->wh_id) { ?> selected="" <?php } ?>> <?php echo $row->wh_name; ?> </option>
                                                            <?php
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                                <?php //if (!empty($from_id) && !empty($TranNo)) { ?>
                                                    <!--<input type="hidden" name="receive_from" id="receive_from" value="<?php //echo $from_id;  ?>" />-->
                                                <?php //}  ?>
                                            </div>
                                        </div>

                                    </div>

                                    <div class="row">
                                        <div class="col-md-3">
                                            <label class="control-label" for="local_foreign"> Local PO / Foreign PO <span class="red">*</span> </label>
                                            <div class="controls">
                                                <select name="local_foreign" id="local_foreign" class="form-control">
                                                    <option value="local" <?php if ($local_foreign == 'local') { ?>selected=""<?php } ?>>Local PO</option>
                                                    <option value="foreign" <?php if ($local_foreign == 'foreign') { ?>selected=""<?php } ?>>Foreign PO</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="control-label" for="country"> Country of PO <span class="red">*</span> </label>
                                            <div class="controls">
                                                <select name="country" id="country" class="form-control" <?= (($local_foreign == 'local') ? ' disabled ' : '') ?>>
                                                    <?php
//check if result exists
                                                    if (mysql_num_rows($countries) > 0) {
                                                        //fetch result
                                                        while ($row = mysql_fetch_object($countries)) {
                                                            ?>
                                                            <option value="<?php echo $row->id; ?>" <?php if ($country == $row->id) { ?> selected="" <?php } ?>> <?php echo $row->name; ?> </option>
                                                            <?php
                                                        }
                                                    }
                                                    ?></select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="control-label" for="sub_cat"> Type <span class="red">*</span> </label>
                                            <div class="controls">
                                                <select name="sub_cat" id="sub_cat" class="form-control" <?= ($local_foreign == 'foreign') ? 'disabled' : '' ?>>
                                                    <option value="emergency"       <?php if ($sub_cat == 'emergency') { ?>selected="selected" <?php } ?>>Emergency</option>
                                                    <option value="tender"          <?php if ($sub_cat == 'tender') { ?>selected="selected" <?php } ?>>Tender</option>
                                                    <option value="petty_purchase"  <?php if ($sub_cat == 'petty_purchase') { ?>selected="selected" <?php } ?>>Petty Purchase</option>
                                                    <option value="limited"         <?php if ($sub_cat == 'limited') { ?>selected="selected" <?php } ?>>Limited Tender</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="control-label" for="tender_no"> Tender Number</label>
                                            <div class="controls">
                                                <input type="text" class="form-control" style="text-align:right" <?= ($sub_cat == 'tender') ? '' : 'readonly' ?> name="tender_no" id="tender_no" value="<?php echo (!empty($tender_no) ? $tender_no : '' ) ?>" />
                                            </div>
                                        </div>

                                    </div>

                                    <div class="row">
                                        <div class="col-md-3">
                                            <label class="control-label" for="currency"> Currency <span class="red">*</span> </label>
                                            <div class="controls">
                                                <select name="currency" id="currency" class="form-control">
                                                    <option value="PKR" <?php if ($currency == 'PKR') { ?>selected=""<?php } ?>>PKR</option>
                                                    <option value="USD" <?php if ($currency == 'USD') { ?>selected=""<?php } ?>>USD</option>
                                                </select>

                                                <span id="product-unit"> </span> <span id="product-unit1" style="display:none;"> </span> </div>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="control-label" for="qty"> Unit Price (<span class="currency"><?php echo (!empty($currency) ? $currency : 'PKR') ?></span>) <span class="red">*</span> </label>
                                            <div class="controls">
                                                <input type="text" class="form-control" style="text-align:right" name="unit_price" id="unit_price" value="<?php echo (!empty($unit_price) ? number_format($unit_price, 2) : '' ) ?>" />
                                            </div>
                                        </div>
                                        <div class="col-md-3 " id="dollarrate">
                                            <label class="control-label" for="drate"> Exchange rate of <span class="currency"><?= $currency ?></span></label>
                                            <div class="controls">
                                                <?php
                                                $rdonly = "";
                                                if ($currency == 'PKR') {
                                                    $dollar_rate = 1;
                                                    $rdonly = 'readonly="readonly"';
                                                }
                                                ?>
                                                <input type="text" class="form-control right" name="drate" id="drate" <?= $rdonly ?> value="<?php echo (!empty($dollar_rate) ? $dollar_rate : '' ) ?>"  />
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="control-label" for="qty"> Amount in <span class="currency"><?php echo (!empty($currency) ? $currency : 'PKR') ?></span> </label>
                                            <div class="controls">
                                                <input type="text" class="form-control" name="amount" id="amount" readonly="" value="<?php echo (!empty($amount) ? $amount : '' ) ?>" />
                                            </div>
                                        </div>


                                    </div>


                                    <div class="row">
                                        <div class="col-md-3">
                                            <label class="control-label" for="qty"> Adv/Payment Release (PKR)</label>
                                            <div class="controls">
                                                <input type="text" class="form-control" name="adv_payment_release" id="adv_payment_release" value="<?php echo (!empty($adv_payment_release) ? $adv_payment_release : '' ) ?>" />
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="control-label" for="contract_delivery_date"> Date of Delivery as per contract </label>
                                            <div class="controls">
                                                <input type="text" class="form-control" name="contract_delivery_date" id="contract_delivery_date" value="<?php echo (!empty($contract_delivery_date)) ? date("d/m/Y", strtotime($contract_delivery_date)) : date("d/m/Y"); ?>" />
                                            </div>
                                        </div>


                                        <div class="col-md-6 hide">
                                            <label class="control-label" for="procured_by"> Procured For <span class="red">*</span> </label>
                                            <div class="controls">
                                                <select name="procured_by" id="procured_by"  class="form-control">
                                                    <option value=""> Select </option>
                                                    <?php
//check if result exists
                                                    if (mysql_num_rows($procured_by) > 0) {
                                                        //fetch results
                                                        while ($row = mysql_fetch_object($procured_by)) {

                                                            $sel = '';
                                                            if (10 == $row->PkLocID) {
                                                                $sel = ' selected ';
                                                            }
                                                            echo "<option value=" . $row->PkLocID . " " . $sel . " >" . $row->LocName . "</option>";
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>







                                        <div class="col-md-3">
                                            <label class="control-label" for="product"> P.O. Status <span class="red">*</span> </label>
                                            <div class="controls">
                                                <select name="postatus" id="postatus" required="true" class="form-control">
                                                    <option value="Active" <?php if ($status == 'Active') { ?>selected=""<?php } ?>> Active </option>
                                                    <option value="InActive" <?php if ($status == 'InActive') { ?>selected=""<?php } ?>> InActive </option>
                                                    <option value="Canceled" <?php if ($status == 'Canceled') { ?>selected=""<?php } ?>> Canceled </option>

                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="control-group">
                                                <label class="control-label" for="status_date"> Date</label>
                                                <div class="controls">
                                                    <input class="form-control"  id="status_date" tabindex="2" name="status_date" type="text" value="<?php echo (!empty($po_status_date)) ? date("d/m/Y", strtotime($po_status_date)) : date("d/m/Y"); ?>" required />
                                                </div>
                                            </div>
                                        </div>


                                        <div class="col-md-3 hide">
                                            <label class="control-label" for="product"> Status <span class="red">*</span> </label>
                                            <div class="controls">
                                                <select name="status" id="status" required="true" class="form-control">
                                                    <!--                                                        <option value=""> Select </option>
                                                                                                            <option value="Pre Shipment"> Pre Shipment </option>
                                                                                                            <option value="Tender"> Tender </option>-->
                                                    <option value="PO" selected> Purchase Order </option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <br>
                                    <div class="row">

                                        <div class="col-md-6">
                                            <table class="table table-bordered">
                                                <tr><td colspan="4"><h4>Delivery Schedule</h4></td></tr>

                                                <tr>
                                                    <th>Date</th>
                                                    <th>Total unit</th>
                                                    <th>Delivered</th>
                                                    <th>Balance</th>                                                    
                                                </tr>
                                                <?php for ($i = 1; $i <= 6; $i++) { ?>
                                                    <tr>
                                                        <td><input name="ddate[<?php echo $i; ?>]" id="ddate<?php echo $i; ?>" type="text" class="col-md-12" value="<?php echo (!empty($ddate[$i])) ? date("d/m/Y", strtotime($ddate[$i])) : ''; ?>"/></td>
                                                        <td><input name="dunit[<?php echo $i; ?>]" id="dunit<?php echo $i; ?>" type="text" class="col-md-12" value="<?php echo (!empty($dunit[$i]) ? $dunit[$i] : '' ) ?>"/></td>
                                                        <td><input name="ddelivered[<?php echo $i; ?>]" id="ddelivered<?php echo $i; ?>" type="text" class="col-md-12" value="<?php echo (!empty($ddelivered[$i]) ? $ddelivered[$i] : '' ) ?>"/></td>
                                                        <td><input name="dbalance[<?php echo $i; ?>]" id="dbalance<?php echo $i; ?>" type="text" class="col-md-12" value="<?php echo (!empty($dbalance[$i]) ? $dbalance[$i] : '' ) ?>"/></td>
                                                    </tr>
                                                <?php } ?>

                                            </table>

                                        </div>
                                        <div class="col-md-6">
                                            <?php if ($type == 'Update') { ?>
                                                <div class="alert alert-danger" style="background-color:lightgray;color:black;">
                                                    <!--<button data-dismiss="alert" class="close" type="button"> X</button>-->
                                                    <input type="checkbox" name="completed" id="completed" <?php if($status=='Completed') echo'checked="checked"';?> ><label><h3>Complete Purchase Order </h3></label>
                                                    <div id="div_qty"> 
                                                    </div>
                                                    <label style="font-size:18px;">Remarks</label>
                                                    <textarea name="remarks" type="remarks" maxlength="200" class="form-control" ></textarea>
                                                </div>
                                                <div>

                                                </div>
                                            <?php } ?>
                                        </div>
                                        <div class="col-md-6" id="vend_detail_div" style="display:none;">

                                            <table class="table table-bordered">
                                                <tr><td colspan="2"><h4>Vendor Details</h4></td></tr>

                                                <tr><td>Vender Name</td>
                                                    <td id="v_name"></td>
                                                </tr>
                                                <tr><td>Contact Person</td>
                                                    <td id="c_pers"></td>
                                                </tr>
                                                <tr><td>Contact Numbers</td>
                                                    <td id="c_numb"></td>
                                                </tr>
                                                <tr><td>Contact Emails</td>
                                                    <td id="c_email"></td>
                                                </tr>
                                                <tr><td>Address</td>
                                                    <td id="c_addr"></td>
                                                </tr>
                                                </tr>
                                                <tr><td>NTN</td>
                                                    <td id="c_ntn"></td>
                                                </tr>
                                                </tr>
                                                <tr><td>GST Number</td>
                                                    <td id="c_gstn"></td>
                                                </tr>
                                            </table>

                                        </div>

                                    </div>
                                    <div class="row">

                                        <div class="col-md-12">
                                            <label class="control-label" for="firstname"> &nbsp; </label>
                                            <div class="controls right">
                                                <button type="submit" class="btn btn-primary" id="add_receive"  name="save_exit"> <?php echo $type; ?> and exit</button>
                                                <button type="submit" class="btn btn-info" id="add_receive" name="save_stay"> <?php echo $type; ?> and stay</button>
                                                <a href="search_purchase_order.php" class="btn btn-danger"  onclick="return confirm('Confirm, Exit without saving?')" > Exit without saving</a>
                                                <?php //if ($type <> 'new') {  ?><!--<a href="add_purchase_order.php?type=new"><button type="button" class="btn btn-info" id="reset"> New PO? </button></a> --><?php //}  ?>
                                                <input type="hidden" name="trans_no" id="trans_no" value="<?php echo $TranNo; ?>" />
                                                <input type="hidden" name="po_id" id="po_id" value="<?php echo $po_id; ?>" />

                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                    </div>
                </div>
                <!-- // Row END -->
                <div id="modal-manufacturer" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content"> 
                            <!-- Modal heading -->
                            <div class="modal-header">
                                <button aria-hidden="true" data-dismiss="modal" class="close" type="button">Ã—</button>
                                <div id="pro_loc"></div>
                            </div>
                            <!-- // Modal heading END --> 

                            <!-- Modal body -->
                            <div class="modal-body">
                                <form name="addnew" id="addnew" action="add_vendor_action.php" method="POST">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="col-md-6">
                                                <label class="control-label">Vendor Name<span class="red">*</span></label>
                                                <div class="controls">
                                                    <input required class="form-control" maxlength="250" type="text" id="new_manufacturer" name="new_manufacturer" value=""/>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="control-label">Brand Name<span class="red">*</span></label>
                                                <div class="controls">
                                                    <input required class="form-control" maxlength="250" readonly type="text" id="brand_name" name="brand_name" value=""/>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="control-label">NTN</label>
                                                <div class="controls">
                                                    <input  class="form-control" maxlength="250"  type="text" id="ntn" name="ntn" value=""/>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="control-label">GSTN</label>
                                                <div class="controls">
                                                    <input  class="form-control" maxlength="250"  type="text" id="gstn" name="gstn" value=""/>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="col-md-12">
                                                <div class="controls">
                                                    <h4 style="padding-top:30px;">Contact Details</h4>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="control-label">Contact Person</label>
                                                <div class="controls">
                                                    <input class="form-control input-sm  " maxlength="250" type="text" id="contact_person" name="contact_person" value=""/>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="control-label">Contact Number(s)</label>
                                                <div class="controls">
                                                    <input class="form-control input-sm  " maxlength="250" type="text"  id="contact_numbers" name="contact_numbers" value=""/>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="control-label">Contact Email(s)</label>
                                                <div class="controls">
                                                    <input class="form-control input-sm  " maxlength="250" type="text"  id="contact_emails" name="contact_emails" value=""/>
                                                </div>
                                            </div>
                                        </div>
                                    </div> 
                                    <div class="row">
                                        <div class="col-md-12">

                                            <div class="col-md-9">
                                                <label class="control-label">Address</label>
                                                <div class="controls">
                                                    <textarea class="form-control    " type="text" id="company_address" name="company_address"  ></textarea>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="control-label">Status</label>
                                                <div class="controls">
                                                    <input type="radio" name="company_status" value="active" checked> Active<br>
                                                    <input type="radio" name="company_status" value="inactive"> InActive<br>
                                                    <input type="radio" name="company_status" value="black_list"> Black Listed
                                                </div>
                                            </div>
                                        </div>
                                    </div> 

                                    <input type="hidden" id="add_manufacturer" name="add_manufacturer" value="1"/>
                                </form>
                            </div>
                            <!-- // Modal body END --> 

                            <!-- Modal footer -->
                            <div class="modal-footer"> <a data-dismiss="modal" class="btn btn-default" href="#">Close</a> <a class="btn btn-primary" id="save_manufacturer" data-dismiss="modal" href="#">Save changes</a> </div>
                            <!-- // Modal footer END --> 
                        </div>
                    </div>
                </div>

                <?php if ($type <> 'new') { ?>
                    <!--                    <div class="widget" data-toggle="collapse-widget"> 
                    
                    
                                             Widget heading 
                                            <div class="widget-head">
                                                <h4 class="heading">Purchase Order Details</h4>
                                            </div>
                    
                                             // Widget heading END 
                    
                                            <div class="widget-body"> 
                    
                                                 Table  
                                                 Table 
                                                <table class="table table-bordered table-condensed">
                    
                                                     Table heading 
                                                    <thead>
                                                        <tr>
                                                            <th>Sr. No.</th>
                                                            <th>PO Date</th>
                                                            <th>PO#</th>
                                                            <th>Product</th>
                                                            <th>PO Qty Ordered</th>
                                                            <th>Unit Price</th>
                                                            <th>Amount</th>
                                                            <th>Delivery Date</th>
                                                            <th>Action</th>
                                                        </tr>
                                                    </thead>
                                                     // Table heading END  
                    
                                                     Table body 
                                                    <tbody>
                    
                                                         Table row 
                    <?php
//                                    $result = mysql_query("SELECT
//DATE_FORMAT(purchase_order.po_date,'%d/%m/%Y') po_date,
//purchase_order.pk_id,
//purchase_order.po_number,
//purchase_order.reference_number,
//DATE_FORMAT(purchase_order.contract_delivery_date,'%d/%m/%Y') contract_delivery_date,
//purchase_order.shipment_quantity,
//purchase_order.wh_id,
//purchase_order.unit_price,
//itminfo_tab.itm_name
//FROM
//purchase_order
//INNER JOIN itminfo_tab ON purchase_order.item_id = itminfo_tab.itm_id
//WHERE purchase_order.po_number = (SELECT MAX(purchase_order.po_number) FROM purchase_order) AND
//purchase_order.wh_id = $wh_id
//");
//                                    $i = 1;
//                                    if ($result != FALSE) {
//                                        //fetch result
//                                        while ($row = mysql_fetch_object($result)) {
//
//                                            $cl = '';
//                                            if (count($i) % 2 == 0) {
//                                                $cl = 'danger';
//                                            }
                    ?>
                                                                <tr class="gradeX <?= $cl ?>" >
                                                                    <td class="text-center"><?php echo $i; ?></td>
                                                                    <td><?php echo $row->po_date; ?></td>
                                                                    <td><?php echo $row->po_number; ?></td>
                                                                    <td><?php echo $row->itm_name; ?></td>
                                                                    <td><?php echo number_format($row->shipment_quantity); ?></td>
                                                                    <td><?php echo number_format($row->unit_price, 2); ?></td>
                                                                    <td><?php echo number_format($row->shipment_quantity * $row->unit_price); ?></td>
                                                                    <td><?php echo $row->contract_delivery_date; ?></td>
                                                                    <td><a href="delete_po.php?id=<?php echo $row->pk_id; ?>" onclick="return confirm('Are you sure your want to delete?')">Delete</a></td>
                                                                </tr>
                    <?php
//                                            $i++;
//                                        }
//                                    }
                    ?>
                                                         // Table row END 
                                                    </tbody>
                                                     // Table body END 
                    
                                                </table>
                    
                                            </div>
                                        </div>-->
                <?php } ?>
            </div>
        </div>
        <!-- // Content END --> 

    </div>
    <?php
//include footer
    include PUBLIC_PATH . "/html/footer.php";
    ?>
    <script src="<?php echo PUBLIC_URL; ?>js/dataentry/add_purchase_order.js"></script>
    <script src="<?php echo PUBLIC_URL; ?>js/dataentry/jquery.mask.min.js"></script>
    <script src="<?php echo PUBLIC_URL; ?>js/jquery.inlineEdit.js"></script>
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
    if ($type == 'Update') {
        ?>
        <script>
            $(function () {
                var po_id=<?php echo $po_id;?>;
                $.ajax({
            type: "POST",
            url: "ajax_complete_po.php",
            data: 'po_id=' + po_id ,
            dataType: 'html',
            success: function (data) {
                $('#div_qty').html(data); 
                $('#div_qty').slideDown("slow");

            }
        });
            });
        </script>
        <?php
    }
    ?>
    <!-- END FOOTER --> 

    <script type="text/javascript" src="../../public/assets/global/plugins/select2/select2.min.js"></script>
    <!-- END JAVASCRIPTS -->
</body>
<!-- END BODY -->
</html>