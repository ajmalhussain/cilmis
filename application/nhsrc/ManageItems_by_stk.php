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
$sql = mysql_query("SELECT
	MAX(frmindex) AS frmindex
FROM
	itminfo_tab
INNER JOIN stakeholder_item ON itminfo_tab.itm_id = stakeholder_item.stk_item
WHERE
	stakeholder_item.stkid = 1222");
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
            //drug_reg_num
            $drug_reg_num = $RowEditStk->drug_reg_num;
            //$itm_type
            $itm_type = $RowEditStk->itm_type;
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
$xmlstore .= "<tbody>";
$counter = 1;
//populate xml
while ($Rowrsadditem = mysql_fetch_object($result_xmlw)) {
    $temp = "\"$Rowrsadditem->itm_id\"";
    $xmlstore .= "<tr>";
    $xmlstore .= "<td>" . $counter++ . "</td>";
    //itm_name
    $xmlstore .= "<td>" . $Rowrsadditem->itm_name . "</td>";
    //generic_name
    //$xmlstore .= "<td>" . $Rowrsadditem->generic_name . "</td>";
    //method_type
    //UnitType
    $xmlstore .= "<td>" . $Rowrsadditem->UnitType . "</td>";
    //ItemStatusName
    //$xmlstore .= "<td>" . $Rowrsadditem->ItemCategoryName . "</td>";
    //frmindex
    $xmlstore .= "<td>" . $Rowrsadditem->frmindex . "</td>";
//    $xmlstore .="<td><a target=\"_blank\" class=\"btn btn-xs green\" href=\"view_manufacturers.php?prod_id=".$Rowrsadditem->itm_id."\">View Manufacturers</a></td>";
    $xmlstore .= "<td><a class=\"btn btn-xs green\" href=\"ManageManufacturersConfig.php?prod_id=" . $Rowrsadditem->itm_id . "\">Edit Manufacturers</a></td>";

    $xmlstore .= "<td>";
    if ($Rowrsadditem->itm_category != 1) {
        $xmlstore .= "<a class=\"btn btn-xs yellow\" href=\"ManageItems_by_stk.php?Do=Edit&Id=" . $Rowrsadditem->itm_id . "\">Edit Product</a>";
    }
    $xmlstore .= "</td>";

    $xmlstore .= "</tr>";
}
//end xml
$xmlstore .= "</tbody>";
?>
</head>
<!-- BEGIN BODY -->
<body class="page-header-fixed page-quick-sidebar-over-content"  >
    <!-- BEGIN HEADER -->
    <div class="page-container">
        <?php include $_SESSION['menu']; ?>
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
                                <form name="abc" id="abc" method="post" action="ManageItemAction_by_stk.php">
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
                                                                                                <label>Product Code</label>
                                                                                                <div class="controls">
                                                                                                    <input autocomplete="off" type="text" name="drug_reg_num" value="<?= $drug_reg_num ?>" id="drug_reg_num" class="form-control input-medium" />
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
<div class="col-md-3">
                                                <div class="control-group">
                                                    <label>Category<font color="#FF0000">*</font></label>
                                                    <div class="controls">
                                                        <select name="txtStkName4" id="txtStkName4" class="form-control input-medium">
                                                            <option value="20" >Clinical Management</option>
                                                            <option value="21" >LAB</option>
                                                            <option value="22" >PPE</option>
                                                            <option value="23" >Misc.</option>       
                                                        </select>

                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="control-group">
                                                    <label>Unit of Measure<font color="#FF0000">*</font></label>
                                                    <div class="controls">
                                                        <select name="txtStkName2" id="txtStkName2" class="form-control input-medium">

                                                            <option value="Each" >Each</option>
                                                            <option value="Pack" >Pack</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            

                                            

                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-3">
                                                <div class="control-group">
                                                    <label>Index<font color="#FF0000">*</font></label>
                                                    <div class="controls"> 
                                                        <!--<input type="text" name="txtStkName8" id="txtStkName8" class="form-control input-medium" value="<?= $frmindex ?>" />
                                                            <img src="images/sort_asc.gif" alt="" onClick="update_counter()" />
                                                            <img src="images/sort_desc.gif" alt="" onClick="update_counter_down()" />-->
                                                        <input type="text" name="txtStkName8" id="spinner1" class="form-control input-medium" style="border:1px solid #d8d9da;text-align:right; padding-right:5px;" value="<?= $frmindex ?>" />
                                                    </div>
                                                </div>
                                            </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">

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

                                            <div class="col-md-3 hide">
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
                                            <div class="col-md-12 right">
                                                <div class="control-group">
                                                    <label>&nbsp;</label>
                                                    <div class="controls">
                                                        <input type="hidden" name="hdnstkId" value="<?= $nstkId ?>" />
                                                        <input  type="hidden" name="hdnToDo" value="<?= $strDo ?>" />
<!--                                                        <input type="submit" value="<?= $strDo ?>" class="btn btn-primary" />-->
                                                        <button type="submit" name="" value="<?= $strDo ?>" class="btn btn-primary" >Save Changes</button>
                                                        <input name="btnAdd" type="button" id="btnCancel" class="btn btn-info" value="Cancel" OnClick="window.location = '<?= $_SERVER["PHP_SELF"]; ?>';">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <input type="hidden" name="txtStkName4" id="txtStkName4" value="4"/>
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
                                <table class=" table table-striped table-bordered table-condensed" width="100%" cellpadding="0" cellspacing="0" align="center">
                                    <thead>
                                        <tr>
                                            <th>Sr No</th> 
                                            <th>Product</th> 
                                            <th>Unit</th> 
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
    <?php
//Including required files
    include PUBLIC_PATH . "/html/footer.php";
    include PUBLIC_PATH . "/html/reports_includes.php";
    ?>
    <script>
        //Edit Manage Items
        function editFunction(val) {
            window.location = "ManageItems_by_stk.php?Do=Edit&Id=" + val;
        }
        //Delete Manage Items
        function delFunction(val) {
            if (confirm("Are you sure you want to delete the record?")) {
                window.location = "ManageItems_by_stk.php?Do=Delete&Id=" + val;
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
    </script>

</body>
</html>