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
$remarks = '';
$inco = '';
$supplier_id = '';
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
$dwarehouse = array();

if ($type == 'Update') {
    $po_id = $_REQUEST['po_id'];
    $po_data = $objPurchaseOrder->find_by_id($po_id);
//    echo '<pre>';
//        print_r($po_data);
//        echo '</pre>';
//        exit;
    if (isset($po_data)) {
        //echo '<pre>';print_r($po_data);
        $po_date = $po_data->po_date;
        $po_number = $po_data->po_number;
        $reference_number = $po_data->reference_number;

        $procured_by = $po_data->procured_by;
        $created_date = $po_data->created_date;
        $created_by = $po_data->created_by;
        $modified_by = $po_data->modified_by;
        $modified_date = $po_data->modified_date;
        $status = $po_data->status;
        $wh_id = $po_data->wh_id;
        $dollar_rate = $po_data->dollar_rate;
        $contact_no = $po_data->contact_no;
        $signing_date = $po_data->signing_date;
        $adv_payment_release = $po_data->adv_payment_release;
        $contract_delivery_date = $po_data->contract_delivery_date;
        $po_accept_date = $po_data->po_accept_date;
        $po_cancelled_date = $po_data->po_cancelled_date;
        $po_delete_date = $po_data->po_delete_date;
        $currency = $po_data->currency;
        $local_foreign = $po_data->local_foreign;
        $sub_cat = $po_data->sub_cat;
        $country = $po_data->country;
        $tender_no = $po_data->tender_no;
        $remarks = $po_data->remarks;
        $supplier_id = $po_data->supplier_id;
        $inco = $po_data->incoterm;
        $from_id = $po_data->funding_source;
        $strSql = "SELECT
                    stakeholder.currency
            FROM
            tbl_warehouse
            INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
            WHERE 
            tbl_warehouse.wh_id=$from_id            
            ";

//echo $strSql;
        $currency_result = mysql_query($strSql);
        $row = mysql_fetch_assoc($currency_result);
        $currency = $row['currency'];
//
//        $productID = $po_data->item_id;
//        $manufacturer = $po_data->manufacturer;
//        $shipment_quantity = $po_data->shipment_quantity;
//        $unit_price = $po_data->unit_price;
//        $reqqty = $objManageItem->GetProductReq($productID);
//        $amount = number_format($shipment_quantity * $unit_price);
//        $amountpkr = number_format($shipment_quantity * $unit_price * $dollar_rate);

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
//        print_r($po_data);exit;
        $count = 1;
        while ($row = mysql_fetch_object($po_data)) {
            $ddate[$count] = $row->delivery_date;
            $dunit[$count] = $row->total_unit;
            $ddelivered[$count] = $row->delivered;
            $dbalance[$count] = $row->balance;
            $dwarehouse[$count] = $row->warehouse_id;
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
    }
}
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
            and (tbl_warehouse.wh_id > 97154   )
            GROUP BY tbl_warehouse.wh_name
            ORDER BY
            tbl_warehouse.wh_name ASC";

//echo $strSql;
$warehouses = mysql_query($strSql);
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

$qry = "select stkname,stkid from stakeholder where stk_type_id=6 AND MainStakeholder=" . $_SESSION['user_stakeholder'];
//print_r($qry);exit;
$suppliers = mysql_query($qry);
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



$qry = "SELECT
text_values.pk_id,
text_values.stkid,
text_values.type,
text_values.text_value,
text_values.updatedby,
text_values.updatedon,
text_values.is_active,
order_of_display
FROM
text_values
WHERE
text_values.type = 'incoterms' AND
text_values.stkid = '".$_SESSION['user_stakeholder1']."' AND
text_values.is_active = 1
order by order_of_display ASC
";
$res = mysql_query($qry);
$incoterms_arr = array();
while ($row = mysql_fetch_assoc($res)) {
    $incoterms_arr[$row['pk_id']] = $row['text_value'];
}
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
                                <form method="POST" name="new_receive" id="new_receive" >
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label class="control-label" for="receive_from">System PO#<span class="red">*</span> </label>
                                            <div class="controls">
                                                <input class="form-control dis-field" name="system_po" id="system_po" value="<?php echo (!empty($po_number) ? $po_number : '' ) ?>" type="text" readonly=""/>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="control-label" for="refrence_number"> Manual PO#<span class="red">*</span> (Max:150 chars)</label>
                                            <div class="controls">
                                                <input class="form-control dis-field" id="refrence_number" value="<?php echo (!empty($reference_number) ? $reference_number : '' ) ?>" name="refrence_number" maxlength="150" type="text" required="required" />
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="control-group">
                                                <label class="control-label" for="po_date"> Purchase Order Date</label>
                                                <div class="controls">
                                                    <input class="form-control dis-field"  id="po_date" tabindex="2" name="po_date" type="text" value="<?php echo (!empty($po_date)) ? date("d/m/Y", strtotime($po_date)) : date("d/m/Y"); ?>" required="" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="control-label" for="signing_date"> Signing Date </label>
                                            <div class="controls">
                                                <input type="text" class="form-control dis-field" style="text-align:right" name="signing_date" id="signing_date" value="<?php echo (!empty($po_date)) ? date("d/m/Y", strtotime($po_date)) : date("d/m/Y"); ?>" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label class="control-label" for="contact_no"> Contract No </label>
                                            <div class="controls">
                                                <input type="text" class="form-control dis-field" style="text-align:right" name="contact_no" id="contact_no" value="<?php echo (!empty($contact_no) ? $contact_no : '' ) ?>" />
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="control-label" for="qty"> Adv/Payment Release (PKR)</label>
                                            <div class="controls">
                                                <input type="number" class="form-control dis-field" name="adv_payment_release" id="adv_payment_release" value="<?php echo (!empty($adv_payment_release) ? $adv_payment_release : '' ) ?>" />
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="control-label" for="contract_delivery_date"> Date of Delivery as per contract </label>
                                            <div class="controls">
                                                <input type="text" class="form-control dis-field" name="contract_delivery_date" id="contract_delivery_date" value="<?php echo (!empty($contract_delivery_date)) ? date("d/m/Y", strtotime($contract_delivery_date)) : date("d/m/Y"); ?>" />
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="control-label" for="product"> P.O. Status <span class="red">*</span> </label>
                                            <div class="controls">
                                                <select name="postatus" id="postatus" required="true" class="form-control dis-field">
                                                    <option value="Active" <?php if ($status == 'Active') { ?>selected=""<?php } ?>> Active </option>
                                                    <option value="InActive" <?php if ($status == 'InActive') { ?>selected=""<?php } ?>> InActive </option>
                                                    <option value="Canceled" <?php if ($status == 'Canceled') { ?>selected=""<?php } ?>> Canceled </option>

                                                </select>
                                            </div>
                                        </div>


                                    </div>
                                    <div class="row">

                                        <div class="col-md-3">
                                            <label class="control-label" for="local_foreign"> Local PO / Foreign PO <span class="red">*</span> </label>
                                            <div class="controls">
                                                <select name="local_foreign" id="local_foreign" class="form-control dis-field">
                                                    <option value="local" <?php if ($local_foreign == 'local') { ?>selected=""<?php } ?>>Local PO</option>
                                                    <option value="foreign" <?php if ($local_foreign == 'foreign') { ?>selected=""<?php } ?>>Foreign PO</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <label class="control-label" for="country"> Country of PO <span class="red">*</span> </label>
                                            <div class="controls">
                                                <select name="country" id="country" class="form-control dis-field" <?= (($local_foreign == 'local') ? ' disabled ' : '') ?>>
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
                                                <select name="sub_cat" id="sub_cat" class="form-control dis-field" <?= ($local_foreign == 'foreign') ? 'disabled' : '' ?>>
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
                                                <input type="text" class="form-control dis-field" style="text-align:right" <?= ($sub_cat == 'tender') ? '' : 'readonly' ?> name="tender_no" id="tender_no" value="<?php echo (!empty($tender_no) ? $tender_no : '' ) ?>" />
                                            </div>
                                        </div>



                                    </div>
                                    <div class="row">
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

                                        <div class="col-md-2">
                                            <div class="control-group">
                                                <label class="control-label" for="status_date"> Date</label>
                                                <div class="controls">
                                                    <input class="form-control dis-field"  id="status_date" tabindex="2" name="status_date" type="text" value="<?php echo (!empty($po_status_date)) ? date("d/m/Y", strtotime($po_status_date)) : date("d/m/Y"); ?>" required />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="control-label" for="receive_from"> Received From (Funding Source)<span class="red">*</span> </label>
                                            <div class="controls">
                                                <select name="receive_from" id="receive_from" required="true" class="form-controlx input-medium select2me dis-field" value="<?php echo (!empty($funding_source) ? $funding_source : '' ) ?>" >
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
                                                <?php //if (!empty($from_id) && !empty($TranNo)) {   ?>
                                                    <!--<input type="hidden" name="receive_from" id="receive_from" value="<?php //echo $from_id;                            ?>" />-->
                                                <?php //}    ?>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="control-label" for="currency"> Currency <span class="red">*</span> </label>
                                            <div class="controls">
                                                <input name="currency" id="currency" class="form-control dis-field" readonly="true" value="<?php echo $currency; ?>">
                                                     

                                            </div>
                                        </div>
                                        <div class="col-md-2 " id="dollarrate">
                                            <label class="control-label" for="drate"> Exchange rate of <span class="currency"><?= $currency ?></span></label>
                                            <div class="controls">
                                                <?php
                                                $rdonly = "";
                                                if ($currency == 'PKR') {
                                                    $dollar_rate = 1;
                                                    $rdonly = 'readonly="readonly"';
                                                }
                                                ?>
                                                <input type="number" class="form-control right dis-field" name="drate" id="drate" <?= $rdonly ?> value="<?php echo (!empty($dollar_rate) ? $dollar_rate : '' ) ?>"  />
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="control-label" for="supplier"> Supplier  </label>
                                            <div class="controls">
                                                <select name="supplier" id="supplier" class="input-medium select2me dis-field">
                                                    <?php while ($row2 = mysql_fetch_array($suppliers)) { ?>
                                                        <option value="<?php echo $row2['stkid'] ?>" <?php if ($supplier_id == $row2['stkid']) { ?>selected=""<?php } ?>><?php echo $row2['stkname']; ?></option>
                                                        <?php
                                                    }
                                                    ?>
                                                </select>

                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="control-label" for="incoterm"> Inco Terms  </label>
                                            <div class="controls">
                                                <select name="incoterm" id="incoterm" class="input-medium select2me ">
                                                    <option value="">Select</option>
                                                    <?php foreach($incoterms_arr as $k => $inc) { ?>
                                                        <option value="<?php echo $k ?>" <?php if ($inco == $k) { ?>selected=""<?php } ?>><?php echo $inc; ?></option>
                                                        <?php
                                                    }
                                                    ?>
                                                </select>

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
                                    <div class="row">
                                        <button type="submit" class="btn btn-primary pull-right" id="add_details" style="margin-top:3%;margin-right: 5%;">Save and Enter Products </button>
                                        <!--<hr style="height:2px;border-width:0;color:gray;background-color:gray;margin-top:3%;">-->

                                    </div>
                                </form>
                            </div>

                            <div id="prod_details"  class="well" style="padding:1%;display:none;">
                                <div id="loading_div" style="display:none;height:180px"> <span class="note note-danger" id="loading_text">Loading . Please Wait ... </span></div>
                                <form method="POST" name="new_products" id="new_products" onsumit="return false">
                                    <div class="">
                                        <h3 class="heading"><?php echo $type; ?> Products</h3>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label class="control-label" for="product"> Product / Item <span class="red">*</span> </label>
                                            <div class="controls">
                                                <select name="product" id="product" required="true" class="input-medium select2me">
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
                                            </div>
                                        </div>
                                        <div class="col-md-1" style="margin-top: 30px;margin-left:-2%;margin-right:2%;" >
                                            <a class="btn btn-xs green " href="../ndma/ManageItems_by_stk.php" target="_blank" ><i class="fa fa-plus"></i> Add New Item</a>
                                        </div>

                                        <div class="col-md-3 ">
                                            <label class="control-label" for="manufacturer"> Vendor <span class="red">*</span> </label>
                                            <div class="controls">
                                                <select name="manufacturer" id="manufacturer" class="input-medium  select2me">
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
                                            </div>
                                        </div>
                                        <div class="col-md-1" style="margin-top: 30px;margin-left:-3%;margin-right:4%; ">
                                            <a class="btn btn-xs btn-primary alignvmiddle" style="display:none;" id="add_m_p"  onclick="javascript:void(0);" data-toggle="modal"  href="#modal-manufacturer"><i class="fa fa-plus"></i> Add New Vendor</a>
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
                                            <label class="control-label" for="qty"> QTY Ordered <span class="red">*</span> </label>
                                            <div class="controls">
                                                <input type="text" class="form-control num" name="qty" id="qty" autocomplete="off" value="<?php echo (!empty($shipment_quantity) ? $shipment_quantity : '' ) ?>" />
                                                <span id="product-unit"> </span> <span id="product-unit1" style="display:none;"> </span>

                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="control-label" for="qty"> Unit Price (<span class="currency"><?php echo (!empty($currency) ? $currency : 'PKR') ?></span>) <span class="red">*</span> </label>
                                            <div class="controls">
                                                <input type="text" class="form-control" style="text-align:right" name="unit_price" id="unit_price" value="<?php echo (!empty($unit_price) ? number_format($unit_price, 2) : '' ) ?>" />
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="control-label" for="qty"> Amount in <span class="currency"><?php echo (!empty($currency) ? $currency : 'PKR') ?></span> </label>
                                            <div class="controls">
                                                <input type="text" class="form-control" name="amount" id="amount" readonly="" value="<?php echo (!empty($amount) ? $amount : '' ) ?>" />
                                            </div>
                                        </div>
                                        <div class="col-md-1">
                                            <label class="control-label"></label>
                                            <button type="submit" class="btn btn-primary pull-right" id="add_product" data-id="" style="margin-top:43%;margin-right:-100%;"> Save </button>
                                            <input type="hidden" name="po_master_id" id="po_master_id" value="<?php echo $po_id; ?>">
                                            <input type="hidden" name="edit_field" id="edit_field"  value="">
                                        </div>
                                        <div class="col-md-6">
                                            <div class="note note-info" id="budget_note"></div>
                                            
                                        </div>
                                    </div>
                                    <input type="hidden" name="budget" id="budget" value="">
                                    <input type="hidden" name="po_total" id="po_total" value="">
                                </form>
                                <div class="row">
                                    <div class="col-md-12 " style="" >
                                        <h3 class="left" style="padding:1%;">Selected products for PO</h3>
                                        <table class="table table-condensed table-bordered ">
                                            <thead>
                                                <tr class="info">
                                                    <th>Product</th>
                                                    <th>Vendor</th>
                                                    <th>Quantity Ordered</th>
                                                    <th>Unit Price</th>
                                                    <th>Amount</th>
                                                    <th colspan="2" style="align:center">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody id="table_div"></tbody>

                                        </table>
                                    </div>
                                </div>
                                <br>

                            </div>

                            <div class="row"  >
                                <form method="POST" name="vendor_form" id="vendor_form" onsubmit="return false">
                                    <input type="hidden" name="master_id" id="master_id" value="">
                                    <div class="col-md-7" id="vendor_deets_div" style="display:none;">
                                        <table class="table table-bordered table-condensed">
                                            <tr class="info"><td colspan="5"><h4> Delivery Schedule</h4></td></tr>

                                            <tr>
                                                <th width="20%">Date</th>
                                                <th width="15%">Total unit</th>
                                                <th width="15%">Delivered</th>
                                                <th width="15%">Balance</th>
                                                <th width="30%">Deliver to Site / Store</th>
                                            </tr>
                                            <?php for ($i = 1; $i <= 6; $i++) { ?>
                                                <tr>
                                                    <td><input name="ddate[<?php echo $i; ?>]" id="ddate<?php echo $i; ?>" type="text" class="form-control" value="<?php echo (!empty($ddate[$i])) ? date("d/m/Y", strtotime($ddate[$i])) : ''; ?>"/></td>
                                                    <td><input name="dunit[<?php echo $i; ?>]" id="dunit<?php echo $i; ?>" type="text" class="form-control" value="<?php echo (!empty($dunit[$i]) ? $dunit[$i] : '' ) ?>"/></td>
                                                    <td><input name="ddelivered[<?php echo $i; ?>]" id="ddelivered<?php echo $i; ?>" type="text" class="form-control" value="<?php echo (!empty($ddelivered[$i]) ? $ddelivered[$i] : '' ) ?>"/></td>
                                                    <td><input name="dbalance[<?php echo $i; ?>]" id="dbalance<?php echo $i; ?>" type="text" class="form-control" value="<?php echo (!empty($dbalance[$i]) ? $dbalance[$i] : '' ) ?>"/></td>
                                                    <td>
                                                        <select name="dwarehouse[<?php echo $i; ?>]" id="dwarehouse<?php echo $i; ?>" class="form-control input-sm">
                                                            <option value="" <?php if (empty($dwarehouse[$i])) echo "selected='selected'"; ?> >As per NDMA advice</option>
                                                            <?php
                                                            $qry_wh = "SELECT
                                                            tbl_warehouse.wh_id,
                                                            tbl_warehouse.wh_name
                                                            FROM
                                                            tbl_warehouse
                                                            WHERE
                                                            tbl_warehouse.stkid = " . $_SESSION['user_stakeholder'] . " AND
                                                            tbl_warehouse.is_allowed_im = 1 AND
                                                            tbl_warehouse.stkofficeid = 1192
                                                            ";
                                                            $res = mysql_query($qry_wh);
                                                            while ($row1 = mysql_fetch_array($res)) {
                                                                ?>
                                                                <option value="<?php echo $row1['wh_id']; ?>" <?php if ((@$dwarehouse[$i]) == $row1['wh_id']) echo "selected='selected'"; ?>><?php echo $row1['wh_name']; ?></option>
                                                                <?php
                                                            }
                                                            ?>
                                                        </select>
                                                    </td>

                                                </tr>
                                            <?php } ?>

                                        </table>

                                    </div>
                                    <div class="col-md-5" id="vend_detail_div" style="display:none;">

                                        <table class="table table-bordered">
                                            <tr class="info"><td colspan="2"><h4>Vendor Details</h4></td></tr>

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

                                    <div class="col-md-5">
                                        <?php if ($type == 'Update') { ?>
                                            <div class=" well well-dark"  >
                                                <!--<button data-dismiss="alert" class="close" type="button"> X</button>-->
                                                <input type="checkbox" name="completed" id="completed" <?php if ($status == 'Completed') echo'checked="checked"'; ?> ><label>Complete Purchase Order </label>
                                                <!--                                                <div id="div_qty" style="display:none;">
                                                                                                </div>-->
                                                <br>
                                                <label style="">Remarks</label>
                                                <textarea name="remarks" type="remarks" maxlength="200" class="form-control" ><?= (!empty($remarks) ? $remarks : '') ?></textarea>
                                            </div>
                                            <div>

                                            </div>
                                        <?php } ?>
                                    </div>
                                    <div class="row">

                                        <div class="col-md-12">
                                            <label class="control-label" for="firstname"> &nbsp; </label>
                                            <div class="controls right">
                                                <button type="submit" class="btn btn-primary" id="add_receive" style="display:none;margin-bottom:2%;margin-right:5%;"> <?php echo $type; ?> Purchase Order </button>
                                                <?php //if ($type <> 'new') {    ?><!--<a href="add_purchase_order.php?type=new"><button type="button" class="btn btn-info" id="reset"> New PO? </button></a> --><?php //}    ?>
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

        </div>
    </div>
    <!-- // Content END -->

</div>
<?php
//include footer
include PUBLIC_PATH . "/html/footer.php";
?>
<!--<script src="<?php echo PUBLIC_URL; ?>js/dataentry/add_purchase_order.js"></script>-->
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
            var po_id =<?php echo $po_id; ?>;
            $.ajax({
                type: "POST",
                url: "ajax_complete_po.php",
                data: 'po_id=' + po_id,
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
<script type="text/javascript">
    $(function () {
        $.ajax({
                type: "POST",
                url: "ajax_currency.php",
                data: {
                    funding_source_id: <?php echo $from_id?>
                },
                dataType: 'json',
                success: function (data) {
                    $("#currency").val(data.currency);
                    $(".currency").html(data.currency);
                    $("#budget").val(data.amount);
                    $("#po_total").val(data.total_po);
                    
                    if (data.currency == 'PKR') {
                        $("#drate").prop('readonly', 'readonly');
                        $("#drate").val('1');
                    } else {
                        $("#drate").prop('readonly', '');
                        $("#drate").val('0');
                    }
                }
            });
<?php if ($type == 'Update') {
    ?>
            $("#prod_details").show();
            fetch_po_products();

<?php }
?>

        $("#new_receive").validate({
            rules: {
                system_po: "required",
                country: "required",
                sub_cat: "required",
                refrence_number: "required",
                local_foreign: "required",
                postatus: "required"
            },
            messages: {
                'receive_ref': {
                    required: "Please enter refernce number"
                }
            },
            submitHandler: function (form) {
            }
        });
        $("#new_products").validate({
            rules: {
                product: {
                    required: true
                },
                qty: {
                    required: true
                },
                manufacturer: {
                    required: true
                },
                receive_from: {
                    required: true
                },
                unit_price: {
                    required: true
                } 
            },
            messages: {
                'product': "Please select product"
            },
            submitHandler: function (form) {
            }
        });

        $("#add_details").on("click", function () {
            if ($("#new_receive").valid()) {
                $("#add_details").hide();
                $("#new_receive").submit(function (e) {
                    var url = "add_purchase_order_action_multiple.php";
                    $.ajax({
                        type: "POST",
                        url: url,
                        data: $("#new_receive").serialize(),
                        dataType: 'json',
                        success: function (data)
                        {
                            if (data.master_id != 'undefined' && data.master_id > 0)
                            {
                                $(".dis-field").attr("disabled", true);
                                $("#prod_details").show();
                                $("#po_master_id").val(data.master_id);
                                $("#master_id").val(data.master_id);
                            } else {
                                alert('Something went wrong, could not save.');
                            }
                        }
                    });
                }
                );
            }
        });
        $("#add_product").click(function () {
            if ($("#new_products").valid()) {
                $("#add_product").attr('disabled', 'disabled');
//                $("#new_products").submit(function (e) {
                $.ajax({
                    type: "POST",
                    url: "ajax_add_po_products.php",
                    data: $("#new_products").serialize(),
                    success: function (data)
                    {
                        var temp_po_total= parseFloat($("#po_total").val());
                        var temp_po_total = parseFloat($("#po_total").val())+parseFloat($("#amount").val())
                        $("#po_total").val(temp_po_total);
                        
                        $("#product").select2('val', "");
                        $("#manufacturer").select2('val', "");
                        //$("#receive_from").select2('val',"");
                        $("#amount").val("");
                        $("#qty").val("");
                        $("#unit_price").val("");
                        $("#reqqty").val("");
                        

                        var edit_field = $('#edit_field').val();
                        fetch_po_products(edit_field);
                        $("#add_product").attr('disabled', false);
                    }
                });
//                });
            }
        });
        $("#add_receive").on("click", function () {
<?php
if ($type == "Update") {
    ?>
                //                
                if ($("#new_receive").valid()) {
                    //                    $("#new_receive").submit(function (e) {
                    var url = "add_purchase_order_action_multiple.php";
                    $.ajax({
                        type: "POST",
                        url: url,
                        data: $("#new_receive, #vendor_form").serialize(),
                        dataType: 'json',
                        success: function (data)
                        {
                            window.location = "search_purchase_order.php";
                        }
                    });
                    //                    });
                }
    <?php
} else {
    ?>
                //                $("#vendor_form").submit(function (e) {
                $.ajax({
                    type: "POST",
                    url: "add_vendor_details.php",
                    data: $("#vendor_form").serialize(),
                    success: function (data)
                    {
                        window.location = "search_purchase_order.php";
                    }
                });
                //                });
<?php } ?>
        });
        function fetch_po_products(prod_detail_id = '') {
            var po_id = '';
            if ($("#po_master_id").val() == '') {
<?php if ($type == 'Update') { ?>
                    po_id =<?php echo $po_id ?>
<?php } ?>
            } else {
                po_id = $("#po_master_id").val();
            }
            $.ajax({
                type: "POST",
                url: "ajax_po_products.php",
                data: {master_id: po_id, prod_detail_id: prod_detail_id},
                dataType: 'html',
                success: function (data)
                {
                    $("#add_details").hide();
                    $("#table_div").hide();
                    $("#table_div").html("");
                    $("#table_div").html(data);
                    $("#table_div").fadeIn(600);
                    $("#vendor_deets_div").show();
                    $("#add_receive").show();
                }
            });
        }

        var product = $('#product').val();

        if (product != '') {
            $("#add_m_p").show();

        } else {
            $("#add_m_p").hide();
        }

        $("#save_manufacturer").click(function () {
            var product = $('#product').val();
            var manufacturer = $('#new_manufacturer').val();
            if (manufacturer == '') {
                alert('Enter Vendor.');
                $('#new_manufacturer').focus();
                return false;
            }
            if ($('#brand_name').val() == '') {
                alert('Enter Brand Name.');
                $('#brand_name').focus();
                return false;
            }



            $('#vend_detail_div').hide();
            $.ajax({
                type: "POST",
                url: "add_vendor_action.php",
                data: 'add_action=1&item_pack_size_id=' + product + '&' + $("#addnew").serialize(),
                dataType: 'html',
                success: function (data) {
                    $('#manufacturer').html(data);
                    // Clear the form

                    $('#v_name').html($('#new_manufacturer').val());
                    $('#c_pers').html($('#contact_person').val());
                    $('#c_numb').html($('#contact_numbers').val());
                    $('#c_email').html($('#contact_emails').val());
                    $('#c_addr').html($('#company_address').val());

                    $('#vend_detail_div').slideDown("slow");

                }
            });
        });

        $("#po_date,#signing_date,#status_date").datepicker({
            minDate: "-1Y",
            maxDate: 0,
            dateFormat: 'dd/mm/yy',
            changeMonth: true,
            changeYear: true
        });

        $("#receive_date,#contract_delivery_date,#ddate1,#ddate2,#ddate3,#ddate4,#ddate5,#ddate6").datepicker({
            minDate: "-1Y",
            maxDate: "+5Y",
            dateFormat: 'dd/mm/yy',
            changeMonth: true,
            changeYear: true
        });

        $("#prod_date").datepicker({
            minDate: "-10Y",
            maxDate: 0,
            dateFormat: 'dd/mm/yy',
            changeMonth: true,
            changeYear: true,
            constrainInput: false
        });

        $("#receive_from").change(function () {
            var prov_id = $("#receive_from option:selected").attr('prov_id');
            console.log('PR:' + prov_id);
            $('#procured_by').val(prov_id);
            $.ajax({
                type: "POST",
                url: "ajax_currency.php",
                data: {
                    funding_source_id: $(this).val()
                },
                dataType: 'json',
                success: function (data) {
                    $("#currency").val(data.currency);
                    $(".currency").html(data.currency);
                    $("#budget").val(data.amount);
                    $("#po_total").val(data.total_po);
                    if (data.currency == 'PKR') {
                        $("#drate").prop('readonly', 'readonly');
                        $("#drate").val('1');
                    } else {
                        $("#drate").prop('readonly', '');
                        $("#drate").val('0');
                    }
                }
            });
        }); 
        $("#currency").change(function () {
            var cur = $(this).val();

            $(".currency").html(cur);


            if (cur == 'PKR') {
                $("#drate").prop('readonly', 'readonly');
                $("#drate").val('1');
            } else {
                $("#drate").prop('readonly', '');
                $("#drate").val('0');
            }
        });
        $("#sub_cat").change(function () {
            var cur = $(this).val();

            if (cur == 'tender') {
                $("#tender_no").prop('readonly', '');
            } else {
                $("#tender_no").prop('readonly', 'readonly');
            }
        });
        $("#local_foreign").change(function () {
            var cur = $(this).val();

            if (cur == 'local') {
                $("#country").prop('disabled', 'disabled');
                $("#country").val('130');
                $("#tender_no").prop('readonly', 'readonly');
                $("#sub_cat").prop('disabled', '');
                $("#sub_cat").val('emergency');
            } else {
                $("#country").prop('disabled', '');
                $("#tender_no").prop('readonly', 'readonly');
                $("#sub_cat").prop('disabled', 'disabled');
            }
        });


        $("#manufacturer").change(function () {
            $('#vend_detail_div').hide();
            $.ajax({
                type: "POST",
                url: "ajax_manuf_details.php",
                data: {
                    manuf_id: $(this).val()
                },
                dataType: 'json',
                success: function (data) {

                    $('#v_name').html(data.stkname);
                    $('#c_pers').html(data.contact_person);
                    $('#c_numb').html(data.contact_numbers);
                    $('#c_email').html(data.contact_emails);
                    $('#c_addr').html(data.contact_address);
                    $('#c_ntn').html(data.ntn);
                    $('#c_gstn').html(data.gstn);

                    $('#vend_detail_div').slideDown("slow");
                }
            });

        });
        $("#product").change(function () {
            var product = $('#product').val();
            if (product != '') {
                $("#add_m_p").show();

            } else {
                $("#add_m_p").hide();
            }
            var prodd_name = $("#product option:selected").text();
            $("#pro_loc").html('<h5>Add New Vendor for ' + prodd_name + '</h5>');


            $.ajax({
                type: "POST",
                url: "ajaxproductbatch.php",
                data: {
                    product: $(this).val()
                },
                dataType: 'html',
                success: function (data) {
                    $('#product-unit').html(data);
                }
            });
            $.ajax({
                type: "POST",
                url: "ajaxproductreq.php",
                data: {
                    product: $(this).val()
                },
                dataType: 'html',
                success: function (data) {
                    $('#reqqty').val(data);
                }
            });


            $.ajax({
                type: "POST",
                url: "add_vendor_action.php",
                data: {
                    show: 1,
                    product: $(this).val()
                },
                dataType: 'html',
                success: function (data) {

                    $('#manufacturer').html(data);

                }
            });
        });
        $('#unit_price').priceFormat({
            prefix: '',
            thousandsSeparator: '',
            suffix: '',
            centsLimit: 2
        });
        $('#unit_price,#qty,#drate').on('keyup keypress', function (e) {
            var price = $("#unit_price").val();
            var qty = $("#qty").val();
            var drate = $("#drate").val();
            var spent = $("#po_total").val();
            var budget = $("#budget").val();
            
            qty = qty.replace(/\,/g, '');
            price = price.replace(/\,/g, '');
            $("#amount").val(parseFloat(price) * parseFloat(qty));
            $("#amountpkr").val(parseFloat(price) * parseFloat(qty) * parseFloat(drate));
            var remaining=($("#budget").val())-($("#po_total").val());
            var this_cost = parseFloat(price) * parseFloat(qty);
            console.log('Spent:'+spent+',budget:'+budget+', this cost:'+this_cost);
            if(remaining > 0){
                $("#budget_note").html("You have '"+remaining+"' amount left against the total budget of : "+budget+" for "+$("#receive_from option:selected").text());
            }
            else{
                $("#budget_note").html("You have NO amount left out of : "+budget+" Funded By: "+$("#receive_from option:selected").text());
            }
            if((parseFloat(price) * parseFloat(qty))>(remaining)){
                 console.log("This amount : "+this_cost+" PLUS amount already spent : "+spent+", is greater than total budget of this funding source:"+budget+".");
                 alert("Max amount available is :"+remaining+". You have already created POs worth: "+spent+", out of total budget:"+budget+" for this funding source.");
                 $("#qty").val('');
                 $("#unit_price").val('');
                 $('#amount').val('');
             }
        });
    });
    $('#qty').priceFormat({
        prefix: '',
        thousandsSeparator: ',',
        suffix: '',
        centsLimit: 0,
        limit: 10
    });
    $('#qty').focusout(function () {
        if ($(this).val() == 0)
        {
            $(this).val(1);
        } else
        {
            $(this).val($(this).val());
        }
    });
    $('#add_m_p').click(function () {
        //$('#addnew')[0].reset();
        var prodd_name = $("#product option:selected").text();
        $("#brand_name").val(prodd_name);

    });
    $('.dimensions').focusout(function () {
        var pack_length = $('#pack_length').val();
        var pack_width = $('#pack_width').val();
        var pack_height = $('#pack_height').val();
        var gross = 0;

        if (typeof pack_length == 'undefined')
            pack_length = 0;
        if (typeof pack_width == 'undefined')
            pack_width = 0;
        if (typeof pack_height == 'undefined')
            pack_height = 0;

        gross = pack_length * pack_width * pack_height;

        $('#gross_capacity').val(gross);

    })
    jQuery.validator.addMethod("mindate", function (value, element) {

        var x = new Date();
        var str = value;
        var day = str.substr(0, 2);
        var month = parseInt(str.substr(3, 2)) - 1;
        var year = str.substr(6);

        x.setFullYear(year, month, day);
        var today = new Date();

        return x > today;
    }, ("Expiry date must be future date."));

    jQuery.validator.addMethod("maxdate", function (value, element) {

        if (value != '')
        {
            var x = new Date();
            var str = value;
            var day = str.substr(0, 2);
            var month = parseInt(str.substr(3, 2)) - 1;
            var year = str.substr(6);

            x.setFullYear(year, month, day);
            var today = new Date();
            return x < today;
        } else
        {
            return true;
        }
    }, ("Production date must be past date."));


</script>
<script>
    function fetch_po_products_del() {
        var po_id = $("#po_master_id").val();
        $.ajax({
            type: "POST",
            url: "ajax_po_products.php",
            data: {master_id: po_id},
            dataType: 'html',
            success: function (data)
            {
                $("#add_details").hide();
                $("#table_div").show();
                $("#table_div").html("");
                $("#table_div").html(data);
                $("#vendor_deets_div").show();
                $("#add_receive").show();
                $("#product").select2();
                $("#manufacturer").select2();
            }
        });
    }
    $(document).ready(function () {
        $(document).on("click", ".del_prod", function () {
            console.log("deleting :" + $(this).data("id"));
            $.ajax({
                type: "POST",
                url: "ajax_delete_po_products.php",
                data: {id: $(this).data("id")},
                dataType: 'html',
                beforeSend: function () {
                    return confirm("Confirm to delete this entry?");
                    console.log("delete is confirmed");
                },
                success: function (data)
                {
                    fetch_po_products_del();
                }
            });
        });
        $(document).on("click", ".edit_prod", function () {
            var id = $(this).data("id");
            $("#edit_field").val(id);
            $("#new_products").hide();
            $("#loading_div").show().focus();
            $.ajax({
                type: "POST",
                url: "ajax_edit_po_products.php",
                data: {id: $(this).data("id")},
                dataType: 'json',
                success: function (data)
                {
                    $("#product").val(data.item_id);
                    $("#product").select2('val', data.item_id);
//                    $("#receive_from").val(data.funding_source);
//                    $("#receive_from").select2('val', data.funding_source);
                    $("#qty").val(data.qty);
                    $("#unit_price").val(data.unit_price);
                    $("#amount").val(data.amount);
                    $("#reqqty").val(data.item_req);
                    $("#loading_text").html('Loading Product and Vendor details . Please Wait...');


                    $.ajax({
                        type: "POST",
                        url: "add_vendor_action.php",
                        data: {
                            show: 1,
                            product: data.item_id
                        },
                        dataType: 'html',
                        success: function (response_vendor) {
                            $('#manufacturer').html(response_vendor);
                            $("#manufacturer").val(data.manuf_id);
                            $("#manufacturer").select2('val', data.manuf_id);
                            $("#loading_div").hide();
                            $("#new_products").fadeIn(500);
                            $("#qty").focus();
                        }
                    });

                }
            });
        });
    });
</script>
<!-- END FOOTER -->

<script type="text/javascript" src="../../public/assets/global/plugins/select2/select2.min.js"></script>
<!-- END JAVASCRIPTS -->
</body>
<!-- END BODY -->
</html>