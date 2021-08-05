<?php
/**
 * add purchase order
 */
include("../includes/classes/AllClasses.php");
include(PUBLIC_PATH . "html/header.php");
$TranNo = '';
$TranRef = '';
$from_id = 0;
$productID = 0;
$unit_price = 0;
$stock_id = 0;
$manufacturer = '';
$userid = $_SESSION['user_id'];
if (!empty($_SESSION['user_warehouse']))
    $wh_id = $_SESSION['user_warehouse'];
else
    $wh_id = 123;

if (isset($_REQUEST['type']) && !empty($_REQUEST['type'])) {
    //get receive date
    $type = $_REQUEST['type'];
}

if ($type <> 'new') {
    $strSql = "SELECT
	CONCAT(
		'PO',
		DATE_FORMAT(
			shipments.created_date,
			'%y%m'
		),
		LPAD(
			(
				SELECT
					COUNT(
						DISTINCT shipments.reference_number
					)
				FROM
					shipments
				GROUP BY
					DATE_FORMAT(
						shipments.created_date,
						'%Y-%m'
					)
				ORDER BY
					shipments.created_date DESC
				LIMIT 1
			),
			4,
			0
		)
	) po_number,
shipments.reference_number,
shipments.po_date
FROM
	shipments
        WHERE
shipments.wh_id = $wh_id
ORDER BY
	shipments.pk_id DESC
LIMIT 1";
    //query result
    $rsSql = mysql_query($strSql) or die("Error GetProduct data");

    if (mysql_num_rows($rsSql) > 0) {
        $po_data = mysql_fetch_assoc($rsSql);
        $po_number = $po_data['po_number'];
        $manual_po = $po_data['reference_number'];
        $po_date = $po_data['po_date'];
    }
} else {
    $strSql = "SELECT
	CONCAT(
		'PO',
		DATE_FORMAT(
			shipments.created_date,
			'%y%m'
		),
		LPAD(
			(
				SELECT
					COUNT(
						DISTINCT shipments.reference_number
					)+1
				FROM
					shipments
				GROUP BY
					DATE_FORMAT(
						shipments.created_date,
						'%Y-%m'
					)
				ORDER BY
					shipments.created_date DESC
				LIMIT 1
			),
			4,
			0
		)
	) po_number,
shipments.reference_number
FROM
	shipments
        WHERE
shipments.wh_id = $wh_id
ORDER BY
	shipments.pk_id DESC
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
if (isset($_GET['PkStockID'])) {
    //get pk stock id
    $PkStockID = base64_decode($_GET['PkStockID']);
    $tempstocksIssue = $objStockMaster->GetTempStockRUpdate($PkStockID);
} else {
    //Get Temp Stock Receive
    $tempstocksIssue = $objStockMaster->GetTempStockReceive($userid, $wh_id, 1);
}
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
            $productID = $result1->itm_id;
            //unit price
            $unit_price = $result1->unit_price;
            //manufacturer
            $manufacturer = $result1->manufacturer;
        }
    }
}
if (!empty($productID)) {
    
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
            GROUP BY tbl_warehouse.wh_name
            ORDER BY
            stakeholder.stkorder ASC";

//echo $strSql;
$warehouses = mysql_query($strSql) or die("Error Getwh");
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
                                <h3 class="heading">Add Purchase Orders</h3>
                            </div>
                            <div class="widget-body">
                                <form method="POST" name="new_receive" id="new_receive" action="add_purchase_order_action.php" >
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
                                                <input class="form-control input-medium" id="refrence_number" value="<?php echo (!empty($manual_po) ? $manual_po : '' ) ?>" name="refrence_number" maxlength="150" type="text" required="" <?php echo (!empty($manual_po) ? 'readonly=""' : '' ) ?> />
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="control-group">
                                                <label class="control-label" for="po_date"> Purchase Order Date</label>
                                                <div class="controls">
                                                    <input class="form-control input-medium"  id="po_date" tabindex="2" name="po_date" type="text" value="<?php echo (!empty($po_date)) ? date("d/m/Y", strtotime($po_date)) : date("d/m/Y"); ?>" required="" <?php echo (!empty($po_date) ? 'readonly=""' : '' ) ?> />
                                                </div>
                                            </div>
                                        </div>
                                        

                                    </div>

                                    <div class="row">
                                        <div class="col-md-3">
                                            <label class="control-label" for="product"> Product / Item <span class="red">*</span> </label>
                                            <div class="controls">
                                                <select name="product" id="product" required="true" class="input-medium  select2me">
                                                    <option value=""> Select </option>
                                                    <?php
//check if result exists
                                                    if (mysql_num_rows($items) > 0) {
                                                        //fetch results
                                                        while ($row = mysql_fetch_object($items)) {

                                                            $sel = '';
                                                            if ($productID == $row->itm_id) {
                                                                $sel = '';
                                                            }
                                                            echo "<option value=" . $row->itm_id . " " . $sel . " >" . $row->itm_name . "</option>";
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3" style="margin-top: 30px;" >
                                            <a class="btn btn-xs green " href="../nhsrc/ManageItems_by_stk.php" target="_blank" ><i class="fa fa-plus"></i> Add New Item / Product</a>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="control-label" for="manufacturer"> Vendor <span class="red">*</span> </label>
                                            <div class="controls">
                                                <select name="manufacturer" id="manufacturer" class="input-medium  select2me">
                                                    <option value="">Select</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-1" style="margin-top: 30px; "> <a class="btn btn-xs btn-primary alignvmiddle" style="display:none;" id="add_m_p"  onclick="javascript:void(0);" data-toggle="modal"  href="#modal-manufacturer"><i class="fa fa-plus"></i> Add New Vendor</a> </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label class="control-label" for="qty"> Quantity <span class="red">*</span> </label>
                                            <div class="controls">
                                                <input type="text" class="form-control input-medium num" name="qty" id="qty" autocomplete="off" />
                                                <span id="product-unit"> </span> <span id="product-unit1" style="display:none;"> </span> </div>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="control-label" for="qty"> Unit Price <span class="red">*</span> </label>
                                            <div class="controls">
                                                <input type="text" class="form-control input-medium" name="unit_price" id="unit_price" />
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="control-label" for="qty"> Amount </label>
                                            <div class="controls">
                                                <input type="text" class="form-control input-medium" name="amount" id="amount" readonly="" />
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-3">
                                            <div class="control-group">
                                                <label class="control-label" for="receive_date"> Delivery Date</label>
                                                <div class="controls">
                                                    <input class="form-control input-medium"  id="receive_date" tabindex="2" name="receive_date" type="text" value="<?php echo date("d/m/Y"); ?>" required />
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-3 hide">
                                            <label class="control-label" for="receive_from">Funded By<span class="red">*</span> </label>
                                            <div class="controls">
                                                <input name="receive_from" id="receive_from" value="95526" type="hidden"/>
                                            </div>
                                        </div>


                                        <div class="col-md-6 hide">
                                            <label class="control-label" for="procured_by"> Procured For <span class="red">*</span> </label>
                                            <div class="controls">
                                                <select name="procured_by" id="procured_by"  class="form-control input-medium">
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

                                    </div>

                                    <div class="row">

                                        <div class="col-md-5 well" id="vend_detail_div" style="display:none;">

                                            <div class="col-md-12">
                                                <h3>&nbsp;</h3>
                                                <h3>Vendor Details</h3>
                                            </div>
                                            <div class="col-md-12">
                                                <b class="font-blue-madison">Vendor Name:</b>
                                                <div  id="v_name" class="form-control-static"></div>
                                            </div>
                                            <div class="col-md-12">
                                                <b class="font-blue-madison">Contact Person:</b>
                                                <div  id="c_pers" class="form-control-static"></div>
                                            </div>
                                            <div class="col-md-12">
                                                <b class="font-blue-madison">Contact Numbers:</b>
                                                <div  id="c_numb" class="form-control-static"></div>
                                            </div>
                                            <div class="col-md-12">
                                                <b class="font-blue-madison">Contact Emails:</b>
                                                <div id="c_email" class="form-control-static"></div>
                                            </div>
                                            <div class="col-md-12">
                                                <b class="font-blue-madison">Address:</b>
                                                <div  id="c_addr" class="form-control-static"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="col-md-3 hide">
                                                <label class="control-label" for="product"> Status <span class="red">*</span> </label>
                                                <div class="controls">
                                                    <select name="status" id="status" required="true" class="form-control input-medium">
                                                        <!--                                                        <option value=""> Select </option>
                                                                                                                <option value="Pre Shipment"> Pre Shipment </option>
                                                                                                                <option value="Tender"> Tender </option>-->
                                                        <option value="PO" selected> Purchase Order </option>
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
                                                    <button type="submit" class="btn btn-primary" id="add_receive"> Save Entry </button>
                                                    <?php if ($type <> 'new') { ?><a href="add_purchase_order.php?type=new"><button type="button" class="btn btn-info" id="reset"> New PO? </button></a> <?php } ?>
                                                    <input type="hidden" name="trans_no" id="trans_no" value="<?php echo $TranNo; ?>" />
                                                    <input type="hidden" name="stock_id" id="stock_id" value="<?php echo $stock_id; ?>" />

                                                </div>
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
                                                    <input required class="form-control input-medium" maxlength="250" type="text" id="new_manufacturer" name="new_manufacturer" value=""/>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="control-label">Brand Name<span class="red">*</span></label>
                                                <div class="controls">
                                                    <input required class="form-control input-medium" maxlength="250" readonly type="text" id="brand_name" name="brand_name" value=""/>
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
                    <div class="widget" data-toggle="collapse-widget"> 


                        <!-- Widget heading -->
                        <div class="widget-head">
                            <h4 class="heading">Purchase Order Details</h4>
                        </div>

                        <!-- // Widget heading END -->

                        <div class="widget-body"> 

                            <!-- Table --> 
                            <!-- Table -->
                            <table class="table table-bordered table-condensed">

                                <!-- Table heading -->
                                <thead>
                                    <tr>
                                        <th>Sr. No.</th>
                                        <th>PO Date</th>
                                        <th>PO#</th>
                                        <th>Product</th>
                                        <th>Received Qty</th>
                                        <th>Unit Price</th>
                                        <th>Amount</th>
                                        <th>Delivery Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <!-- // Table heading END --> 

                                <!-- Table body -->
                                <tbody>

                                    <!-- Table row -->
                                    <?php
                                    $result = mysql_query("SELECT
DATE_FORMAT(shipments.po_date,'%d/%m/%Y') po_date,
shipments.pk_id,
shipments.po_number,
shipments.reference_number,
DATE_FORMAT(shipments.shipment_date,'%d/%m/%Y') shipment_date,
shipments.shipment_quantity,
shipments.wh_id,
shipments.unit_price,
itminfo_tab.itm_name
FROM
shipments
INNER JOIN itminfo_tab ON shipments.item_id = itminfo_tab.itm_id
WHERE shipments.po_number = (SELECT MAX(shipments.po_number) FROM shipments) AND
shipments.wh_id = $wh_id
");
                                    $i = 1;
                                    if ($result != FALSE) {
                                        //fetch result
                                        while ($row = mysql_fetch_object($result)) {
                                            
                                            $cl='';
                                            if(count($i)%2==0){
                                                $cl = 'danger';
                                            }
                                            ?>
                                            <tr class="gradeX <?= $cl ?>" >
                                                <td class="text-center"><?php echo $i; ?></td>
                                                <td><?php echo $row->po_date; ?></td>
                                                <td><?php echo $row->po_number; ?></td>
                                                <td><?php echo $row->itm_name; ?></td>
                                                <td><?php echo number_format($row->shipment_quantity); ?></td>
                                                <td><?php echo $row->unit_price; ?></td>
                                                <td><?php echo number_format($row->shipment_quantity*$row->unit_price); ?></td>
                                                <td><?php echo $row->shipment_date; ?></td>
                                                <td><a href="delete_po.php?id=<?php echo $row->pk_id; ?>" onclick="return confirm('Are you sure your want to delete?')">Delete</a></td>
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

                        </div>
                    </div>
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
    <!-- END FOOTER --> 

    <script type="text/javascript" src="../../public/assets/global/plugins/select2/select2.min.js"></script>
    <!-- END JAVASCRIPTS -->
</body>
<!-- END BODY -->
</html>