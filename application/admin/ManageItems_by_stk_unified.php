<?php
/**
 * Manage Items
 * @package Admin
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
//Including required file
include("../includes/classes/AllClasses.php");
include(PUBLIC_PATH . "html/header.php");

//Initializing variables
$act = 2;
$stakeid = array('');
$groupid = array('');
$strDo = "Add";
$nstkId = 0;
$stk_id = "";
//item name
$itm_name = "";
//generic name
$generic_name = "";
$drug_reg_num = "";
$method_type = "";
//item type
$itm_type = "";
//item category
$itm_category = "";
//qty carton
$qty_carton = 0;
//field color
$field_color = "";
//item_des
$itm_des = "";
//item_status
$itm_status = "";
//frmindex
$frmindex = 0;
//extra
$extra = "";
//stkname
$stkname = "";
//stkorder
$stkorder = 0;
$manufacturer = "";
$brand_name = "";
$length = "";
$width = "";
$height = "";
$net_capacity = "";
$qty_per_pack = "";
$carton_per_pallete = "";
$gtin = "";
$gross_capacity = "";
$unit_price = "";
// Getting Do
if (isset($_REQUEST['Do']) && !empty($_REQUEST['Do'])) {
    $strDo = $_REQUEST['Do'];
}
// Getting form Id
if (isset($_REQUEST['Id']) && !empty($_REQUEST['Id'])) {
    $nstkId = $_REQUEST['Id'];
}

/**
 * 
 * Delete 
 * 
 */
//retrieving maximum value of an index
$sql = mysql_query("Select MAX(frmindex) AS frmindex from itminfo_tab");
$sql_index = mysql_fetch_array($sql);
$frmindex = $sql_index['frmindex'] + 1;

//unset pk_id
if (isset($_SESSION['pk_id'])) {
    unset($_SESSION['pk_id']);
}

/**
 * 
 * Edit 
 * 
 */
if ($strDo == "Edit") {
    $objManageItem->m_npkId = $nstkId;
    $_SESSION['pk_id'] = $nstkId;

    //Get Manage Item By Id
    $rsEditstk = $objManageItem->GetManageItemById();
    //Gettin results
    if ($rsEditstk != FALSE && mysql_num_rows($rsEditstk) > 0) {
        $n = 0;
        //getting results
        while ($RowEditStk = mysql_fetch_object($rsEditstk)) {
            //$itm_name
            $itm_name = $RowEditStk->itm_name;
            //$generic_name
            $generic_name = $RowEditStk->generic_name;
            $method_type = $RowEditStk->method_type;
            $drug_reg_num = $RowEditStk->drug_reg_num;
            //$itm_type
            $itm_type = $RowEditStk->item_unit_id;
            //$itm_category
            $itm_category = $RowEditStk->itm_category;
            //$itm_des
            $itm_des = $RowEditStk->itm_des;
            //$itm_status
            $itm_status = $RowEditStk->itm_status;
            //$frmindex
            $frmindex = $RowEditStk->frmindex;
            //$stakeid
            $stakeid[$n] = $RowEditStk->stkid;
            //$groupid
            $groupid[$n] = $RowEditStk->GroupID;

            $n++;
        }
    }
    $cq = "SELECT
                    stakeholder.stkid,
                    stakeholder_item.stk_id,
                    stakeholder.stkname,
                    stakeholder_item.brand_name,
                    stakeholder_item.pack_length,
                    stakeholder_item.pack_width,
                    stakeholder_item.pack_height,
                    stakeholder_item.net_capacity,
                    stakeholder_item.quantity_per_pack,
                    stakeholder_item.carton_per_pallet,
                    stakeholder_item.gtin,
                    stakeholder_item.gross_capacity,
                    round(stakeholder_item.unit_price,2) as unit_price
                FROM
                        stakeholder
                INNER JOIN stakeholder_item ON stakeholder.stkid = stakeholder_item.stkid
                WHERE
                        stakeholder.stk_type_id = 3
                AND stakeholder_item.stk_item = $nstkId
                ORDER BY
                        stakeholder.stkname ASC";
//    echo $cq;exit;
    $checkManufacturer = mysql_query($cq) or die('Err of manuf 5:' . mysql_error());
    if ($checkManufacturer != FALSE && mysql_num_rows($checkManufacturer) > 0) {
        $n = 0;
        //getting results
        while ($row = mysql_fetch_assoc($checkManufacturer)) {
            $manufacturer = $row['stkid'];
            $brand_name = $row['brand_name'];
            $length = $row['pack_length'];
            $width = $row['pack_width'];
            $height = $row['pack_height'];
            $net_capacity = $row['net_capacity'];
            $qty_per_pack = $row['quantity_per_pack'];
            $carton_per_pallete = $row['carton_per_pallet'];
            $gtin = $row['gtin'];
            $gross_capacity = $row['gross_capacity'];
            $unit_price = $row['unit_price'];
            $stk_id = $row['stk_id'];
        }
    }
}

//retrieving All Stakeholders
//$rsStakeholders = $objstk->GetAllStakeholders();
//$rsStakeholders = $objstk->GetStakeholdersByUserId($_SESSION['user_stakeholder1']);
//retrieving All Item Group
$rsranks = $ItemGroup->GetAllItemGroup();

//retrieving product type
$ItmType = $objItemUnits->GetAllItemUnits();

//retrieving product category
$ItmCategory = $objitemcategory->GetAllItemCategory();

//retrieving product status
$ItmStatus = $objitemstatus->GetAllItemStatus();


$stk = $_SESSION['user_stakeholder1'];
if ($_SESSION['user_stakeholder1'] == '276' || $_SESSION['user_stakeholder1'] == '74') {
    $stk = 7;
}

$objitem = "SELECT
			itminfo_tab.itmrec_id,
			itminfo_tab.itm_id,
			itminfo_tab.itm_name,
			itminfo_tab.generic_name,
			itminfo_tab.method_type,
			tbl_itemunits.UnitType,
			itminfo_tab.itm_des,
			tbl_product_category.ItemCategoryName,
			tbl_product_status.ItemStatusName,
			itminfo_tab.frmindex,
                        itminfo_tab.drug_reg_num,
itminfo_tab.itm_category
		FROM
			itminfo_tab
		LEFT JOIN tbl_itemunits ON tbl_itemunits.pkUnitID = itminfo_tab.item_unit_id
		INNER JOIN tbl_product_category ON itminfo_tab.itm_category = tbl_product_category.PKItemCategoryID
		INNER JOIN tbl_product_status ON itminfo_tab.itm_status = tbl_product_status.PKItemStatusID
		INNER JOIN stakeholder_item ON itminfo_tab.itm_id = stakeholder_item.stk_item
                WHERE
                stakeholder_item.stkid = " . $stk . "
                ORDER BY
			itminfo_tab.itm_name ASC";
//echo $objitem;exit;
$result_xmlw = mysql_query($objitem);
//xml for grid
$xmlstore = "";
$xmlstore .= "<tbody id='productTable'>";
$counter = 1;
//populate xml
while ($Rowrsadditem = mysql_fetch_object($result_xmlw)) {
    $temp = "\"$Rowrsadditem->itm_id\"";
    $xmlstore .= "<tr>";
    $xmlstore .= "<td>" . $counter++ . "</td>";
    //itm_name
    $xmlstore .= "<td>" . $Rowrsadditem->itm_name . "</td>";
    //generic_name
    $xmlstore .= "<td>" . $Rowrsadditem->generic_name . "</td>";
    //method_type
    $xmlstore .= "<td>" . $Rowrsadditem->method_type . "</td>";
    //UnitType
    $xmlstore .= "<td>" . $Rowrsadditem->UnitType . "</td>";
    //ItemStatusName
    $xmlstore .= "<td>" . $Rowrsadditem->ItemCategoryName . "</td>";
    //frmindex
    $xmlstore .= "<td>" . $Rowrsadditem->drug_reg_num . "</td>";
    $xmlstore .= "<td>" . $Rowrsadditem->frmindex . "</td>";
//    $xmlstore .="<td><a target=\"_blank\" class=\"btn btn-xs green\" href=\"view_manufacturers.php?prod_id=".$Rowrsadditem->itm_id."\">View Manufacturers</a></td>";
//    $xmlstore .= "<td><a class=\"btn btn-xs green\" href=\"ManageManufacturersConfig.php?prod_id=" . $Rowrsadditem->itm_id . "\">Edit Manufacturers</a></td>";

    $xmlstore .= "<td>";
    if ($Rowrsadditem->itm_category != 1) {
        $xmlstore .= "<a class=\"btn btn-xs yellow\" href=\"ManageItems_by_stk_unified.php?Do=Edit&Id=" . $Rowrsadditem->itm_id . "\">Edit Product</a>";
    }
    $xmlstore .= "</td>";

    $xmlstore .= "</tr>";
}
//end xml
$xmlstore .= "</tbody>";
?>
<link rel="stylesheet" type="text/css" href="../../public/assets/global/plugins/select2/select2.css"/>
</head>
<!-- BEGIN BODY -->
<body class="page-header-fixed page-quick-sidebar-over-content"  >
    <!-- BEGIN HEADER -->
    <div class="page-container">
        <?php
//    print_r($_SESSION['menu']);exit;
        include $_SESSION['menu'];
        ?>
        <?php include PUBLIC_PATH . "html/top_im.php"; ?>
        <div class="page-content-wrapper">
            <div class="page-content">
                <div class="row">
                    <div class="col-md-12">
                        <h3 class="page-title row-br-b-wp">Product Management
                            <a   class="btn blue right pull-right " onclick="window.open('../msd/cartons_info.php', '_blank', 'scrollbars=1,width=600,height=500');"><i class="fa fa-info-circle"></i> Cartons / Pallets Information</a>
                        </h3>

                        <div class="widget" data-toggle="collapse-widget">
                            <?php
//display all product
                            ?>
                            <div class="widget-head">
                                <h3 class="heading"><?php echo $strDo; ?> Product</h3>
                            </div>
                            <div class="widget-body">
                                <form name="manageitems" id="manageitems" method="post" action="ManageItemAction_by_stk_unified.php">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="col-md-3">
                                                <div class="control-group">
                                                    <label>Product<font color="#FF0000">*</font></label>
                                                    <div class="controls">
                                                        <input placeholder="Name of Product / Medicine" autocomplete="off" type="text" name="txtStkName1" value="<?= $itm_name ?>" id="txtStkName1" class="form-control input-medium" />
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="control-group">
                                                    <label>Generic Name<span class="red">*</span></label>
                                                    <div class="controls">
                                                        <input autocomplete="off" type="text" name="generic_name" value="<?= $generic_name ?>" id="generic_name" class="form-control input-medium" />
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="control-group">
                                                    <label>Method Type / Form</label>
                                                    <div class="controls">
                                                        <input autocomplete="on" type="text" name="method_type" value="<?= $method_type ?>" id="method_type" placeholder="Syrup / Tablet / Injection " required="" class="form-control input-medium" />
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="control-group">
                                                    <label>Unit of Measure<font color="#FF0000">*</font></label>
                                                    <div class="controls">
                                                        <select name="txtStkName2" id="txtStkName2" class="form-control input-small" required="true">
                                                            <option value="">Select</option>
                                                            <?php
//populate txtStkName2 combo
                                                            while ($RowItmType = mysql_fetch_object($ItmType)) {
                                                                ?>
                                                                <option value="<?= $RowItmType->pkUnitID . '-' . $RowItmType->UnitType ?>" <?php
                                                                if ($RowItmType->pkUnitID == $itm_type) {
                                                                    echo 'selected="selected"';
                                                                }
                                                                ?>>
                                                                            <?= $RowItmType->UnitType ?>
                                                                </option>
                                                                <?php
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
                                                    <label>Category<font color="#FF0000">*</font></label>
                                                    <div class="controls">
                                                        <select name="txtStkName4" id="txtStkName4" class="form-control input-medium">
                                                            <?php
//populate txtStkName4
                                                            while ($RowItmCategory = mysql_fetch_object($ItmCategory)) {
                                                                if ($RowItmCategory->PKItemCategoryID < 5)
                                                                    continue;
                                                                ?>
                                                                <option value="<?= $RowItmCategory->PKItemCategoryID ?>" <?php
                                                                if ($RowItmCategory->PKItemCategoryID == $itm_category) {
                                                                    echo 'selected="selected"';
                                                                }
                                                                ?>>
                                                                            <?= $RowItmCategory->ItemCategoryName ?>
                                                                </option>
                                                                <?php
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <div class="control-group">
                                                    <label>Drug Registration No</label>
                                                    <div class="controls">
                                                        <input autocomplete="off" type="text" name="drug_reg_num" value="<?= $drug_reg_num ?>" id="drug_reg_num" class="form-control input-medium" />
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="control-group">
                                                    <label>Description</label>
                                                    <div class="controls">
                                                        <input type="text" name="txtStkName7" value="<?= $itm_des ?>" class="form-control input-medium">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="control-group">
                                                    <label>Index<font color="#FF0000">*</font></label>
                                                    <div class="controls"> 
                                                        <!--<input type="text" name="txtStkName8" id="txtStkName8" class="form-control input-medium" value="<?= $frmindex ?>" />
                                                            <img src="images/sort_asc.gif" alt="" onClick="update_counter()" />
                                                            <img src="images/sort_desc.gif" alt="" onClick="update_counter_down()" />-->
                                                        <input type="text" name="txtStkName8" id="spinner1" class="form-control input-small" style="border:1px solid #d8d9da;text-align:right; padding-right:5px;" value="<?= $frmindex ?>" />
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2 hide">
                                                <div class="control-group">
                                                    <label>Status<font color="#FF0000">*</font></label>
                                                    <div class="controls">
                                                        <input name="txtStkName6" type="hidden1 " value="1" id="txtStkName6" class="form-control input-small">

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="col-md-3">
                                                <label class="control-label">Barcode / GTIN</label>
                                                <div class="controls">
                                                    <input class="form-control input-sm" type="text" id="gtin" name="gtin" value="<?php echo $gtin; ?>"/>
                                                </div>
                                            </div>
                                            <div class="col-md-3 hide">
                                                <div class="control-group">
                                                    <label>Stakeholders<font color="#FF0000">*</font></label>
                                                    <div class="controls">
                                                        <select name="stkid[]" size="5" multiple="multiple" class="form-control input-medium">
                                                            <option value="<?= $_SESSION['user_stakeholder1'] ?>" selected><?= $_SESSION['user_stakeholder1'] ?></option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="col-md-3">
                                                <div class="controls">
                                                    <h4 style="padding-top:30px;color:brown;">Manufacturer Details</h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="col-md-3">
                                                <label class="control-label" >Manufacturer<span class="red">*</span></label>
                                                <div class="controls">
                                                    <select required class="input-medium select2me" id="new_manufacturer" name="new_manufacturer" <?php // echo $manufacturer;        ?>>
                                                        <?php
                                                        $sql = "SELECT
                                                        stakeholder.stkid,
                                                        stakeholder.stkname
                                                        FROM
                                                        stakeholder
                                                        WHERE
                                                        stakeholder.stk_type_id = 3
                                                        ORDER BY
                                                        stakeholder.stkname ASC
                                                         ";
                                                        $result = mysql_query($sql);
                                                        $sel = '';
                                                        while ($row = mysql_fetch_array($result)) {
                                                            if ($row['stkid'] == $manufacturer) {
                                                                $sel = "selected='selected'";
                                                            } else {
                                                                $sel = "";
                                                            }
                                                            echo "<option value='" . $row['stkid'] . "' $sel>" . $row['stkname'] . "</option>";
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="controls">
                                                    <button type="button" style="margin-top:13%;" class="btn btn-warning" data-toggle="modal" data-target="#myModal">Add manufacturer</button>
                                                </div>
                                            </div>


                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="col-md-3">
                                                <div class="controls">
                                                    <h4 style="padding-top:30px;color:brown;">Carton Dimension</h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="col-md-3">
                                                <label class="control-label">Length(cm)</label>
                                                <div class="controls">
                                                    <input class="form-control input-sm dimensions positive_number" type="text" id="pack_length" name="pack_length" value="<?php echo $length; ?>"/>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="control-label">Width(cm)</label>
                                                <div class="controls">
                                                    <input class="form-control input-sm dimensions positive_number" type="text" id="pack_width" name="pack_width" value="<?php echo $width; ?>"/>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="control-label">Height(cm)</label>
                                                <div class="controls">
                                                    <input class="form-control input-sm dimensions positive_number" type="text" id="pack_height" name="pack_height" value="<?php echo $height; ?>"/>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="control-label">Gross Cap.(cm<sup>3</sup>):</label> 
                                                <div class="controls"><input class="form-control input-sm " type="text" readonly id="gross_capacity" name="gross_capacity" value="<?php echo $gross_capacity; ?>"></div>

                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="col-md-3">
                                                <label class="control-label">Net Capacity</label>
                                                <div class="controls">
                                                    <input class="form-control input-sm positive_number" type="text"  id="net_capacity" name="net_capacity" value="<?php echo $net_capacity; ?>"/>
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <label class="control-label">Cartons / Pallet<span class="red">*</span></label>
                                                <div class="controls">
                                                    <input required class="form-control input-sm positive_number" type="text" id="carton_per_pallet" name="carton_per_pallet" value="<?php echo $carton_per_pallete; ?>"/>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="control-label">Quantity/Pack<span class="red">*</span></label>
                                                <div class="controls">
                                                    <input required class="form-control input-sm positive_number" type="text" id="quantity_per_pack" name="quantity_per_pack" value="<?php echo $qty_per_pack; ?>"/>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="control-label">Unit Price</label>
                                                <div class="controls">
                                                    <input class="form-control input-sm" maxlength="9" max="999999" type="text" id="unit_price" name="unit_price" value="<?php echo $unit_price; ?>"/>
                                                </div>
                                            </div>
                                        </div>
                                    </div> 
                                    <input type="hidden" id="add_manufacturer" name="add_manufacturer" value="1"/>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="col-md-12 right">
                                                <div class="control-group">
                                                    <label>&nbsp;</label>
                                                    <div class="controls">
                                                        <input type="hidden" name="hdnstkId" value="<?= $nstkId ?>" />
                                                        <input  type="hidden" name="hdnToDo" value="<?= $strDo ?>" />
                                                        <input type="hidden" id="edit_manufacturer" name="edit_manufacturer" value="<?php echo $stk_id ?>"/>

<!--                                                        <input type="submit" value="<?= $strDo ?>" class="btn btn-primary" />-->
                                                        <button type="submit" name="" value="<?= $strDo ?>" class="btn btn-primary" >Save Changes</button>
                                                        <input name="btnAdd" type="button" id="btnCancel" class="btn btn-info" value="Cancel" OnClick="window.location = '<?= $_SERVER["PHP_SELF"]; ?>';">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="widget">
                            <div class="widget-head">
                                <h3 class="heading">All Products</h3>
                                
                            </div>
                            <div class="widget-body">
                                <input id="searchProduct" type="text" placeholder="Search..">
                                <table class=" table table-striped table-bordered table-condensed" width="100%" cellpadding="0" cellspacing="0" align="center">
                                    <thead>
                                        <tr>
                                            <th>Sr No</th> 
                                            <th>Product</th> 
                                            <th>Generic Name</th> 
                                            <th>Method</th> 
                                            <th>Unit</th> 
                                            <th>Category</th> 
                                            <th>Drug Reg No</th> 
                                            <th>Index</th> 
                                            <th colspan="2">Action</th> 
                                        </tr>
                                    </thead>
                                    <?= $xmlstore ?>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="myModal" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Request manufacturer addition</h4>
                </div>
                <div class="modal-body">
                    <p>Please email us support@lmis.gov.pk to add new manufacturers</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>

        </div>
    </div>
    <?php
//Including required files
    include PUBLIC_PATH . "/html/footer.php";
    include PUBLIC_PATH . "/html/reports_includes.php";
    ?>
    <script type="text/javascript" src="../../public/assets/global/plugins/select2/select2.min.js"></script>
    <script>
                                                            $(function () {
                                                                $('.dimensions').keyup(function () {
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
                                                                $('.dimensions_edit').keyup(function () {
                                                                    var pack_length = $('#pack_length_edit').val();
                                                                    var pack_width = $('#pack_width_edit').val();
                                                                    var pack_height = $('#pack_height_edit').val();
                                                                    var gross = 0;

                                                                    if (typeof pack_length == 'undefined')
                                                                        pack_length = 0;
                                                                    if (typeof pack_width == 'undefined')
                                                                        pack_width = 0;
                                                                    if (typeof pack_height == 'undefined')
                                                                        pack_height = 0;

                                                                    gross = pack_length * pack_width * pack_height;

                                                                    $('#gross_capacity_edit').val(gross);

                                                                })
                                                            });
                                                            //Edit Manage Items
                                                            function editFunction(val) {
                                                                window.location = "ManageItems_by_stk_unified.php?Do=Edit&Id=" + val;
                                                            }
                                                            //Delete Manage Items
                                                            function delFunction(val) {
                                                                if (confirm("Are you sure you want to delete the record?")) {
                                                                    window.location = "ManageItems_by_stk_unified.php?Do=Delete&Id=" + val;
                                                                }
                                                            }

                                                            function view_manufacturers(val) {
                                                                window.open("view_manufacturers.php?prod_id=" + val, '_blank', 'scrollbars=1,width=600,height=500');
                                                            }
                                                            function manage_manuf(val) {
                                                                window.location = "ManageManufacturersConfig.php?prod_id=" + val;
                                                            }
                                                            var mygrid;
                                                            /**
                                                             * Initializing Grid
                                                             */

                                                            $('#spinner1').spinner();

                                                            $(document).ready(function () {
                                                                $("#searchProduct").on("keyup", function () {
                                                                    var value = $(this).val().toLowerCase();
                                                                    $("#productTable tr").filter(function () {
                                                                        $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                                                                    });
                                                                });
                                                            });

    </script>

</body>
</html>