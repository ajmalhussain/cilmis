<?php
include("../includes/classes/AllClasses.php");
include(PUBLIC_PATH . "html/header.php");
include "../includes/styling/dynamic_theme_color.php";
$act = 2;
$stakeid = array('');
$groupid = array('');
$strDo = "Add";
$nstkId = 0;
$stk_id = "";
$itm_name = "";
$generic_name = "";
$drug_reg_num = "";
$method_type = "";
$itm_type = "";
$itm_category = "";
$qty_carton = 0;
$field_color = "";
$itm_des = "";
$itm_status = "";
$frmindex = 0;
$extra = "";
$stkname = "";
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
$sql = mysql_query("Select MAX(frmindex) AS frmindex from itminfo_tab");
$sql_index = mysql_fetch_array($sql);
$frmindex = $sql_index['frmindex'] + 1;

if (isset($_SESSION['pk_id'])) {
    unset($_SESSION['pk_id']);
}

if ($strDo == "Edit") {
    $objManageItem->m_npkId = $nstkId;
    $_SESSION['pk_id'] = $nstkId;

    $rsEditstk = $objManageItem->GetManageItemById();
    if ($rsEditstk != FALSE && mysql_num_rows($rsEditstk) > 0) {
        $n = 0;
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
$rsranks = $ItemGroup->GetAllItemGroup();
$ItmType = $objItemUnits->GetAllItemUnits();
$ItmCategory = $objitemcategory->GetAllItemCategory();
$ItmStatus = $objitemstatus->GetAllItemStatus();

$stk = $_SESSION['user_stakeholder1'];
if ($_SESSION['user_stakeholder1'] == '276' || $_SESSION['user_stakeholder1'] == '74') {
    $stk = 7;
}

$stk_itms = "SELECT
itminfo_tab.itm_id
FROM
itminfo_tab
INNER JOIN stakeholder_item ON itminfo_tab.itm_id = stakeholder_item.stk_item
WHERE
stakeholder_item.stkid = " . $stk . "  
";
$res= mysql_query($stk_itms);
$items_assigned = array();
while ($row = mysql_fetch_assoc($res)) {
    $items_assigned[$row['itm_id']] = $row['itm_id'];
}

$objitem = "SELECT
itminfo_tab.itmrec_id,
itminfo_tab.itm_id,
itminfo_tab.itm_name,
stakeholder_item.brand_name,
itminfo_tab.generic_name,
itminfo_tab.method_type,
itminfo_tab.itm_category,
tbl_itemunits.UnitType,
tbl_product_category.ItemCategoryName,
itminfo_tab.drug_reg_num,
stakeholder.stkname AS manuf_name,
stakeholder_item.unit_price,
stakeholder_item.quantity_per_pack,
stakeholder_item.gtin,
stakeholder_item.stk_id AS id_for_barcode
FROM
itminfo_tab
LEFT JOIN tbl_itemunits ON tbl_itemunits.pkUnitID = itminfo_tab.item_unit_id
INNER JOIN tbl_product_category ON itminfo_tab.itm_category = tbl_product_category.PKItemCategoryID
INNER JOIN stakeholder_item ON itminfo_tab.itm_id = stakeholder_item.stk_item
INNER JOIN stakeholder ON stakeholder_item.stkid = stakeholder.stkid
WHERE
stakeholder.stk_type_id = 3
ORDER BY
itminfo_tab.itm_name ASC
";
//echo $objitem;exit;
$result_xmlw = mysql_query($objitem);
//xml for grid
$xmlstore = "";
$xmlstore .= "<tbody>";
$counter = 1;
//populate xml
while ( $row_item = mysql_fetch_object($result_xmlw)) {
    
    if(in_array( $row_item->itm_id, $items_assigned))
    {
    
        $temp = "\" $row_item->itm_id\"";
        $xmlstore .= "<tr>";
        $xmlstore .= "<td>" . $counter++ . "</td>";
        $xmlstore .= "<td>" .  $row_item->itm_name . "</td>";
        $xmlstore .= "<td>" .  $row_item->manuf_name. "</td>";
        $xmlstore .= "<td>" .  $row_item->generic_name . "</td>";
        $xmlstore .= "<td>" .  $row_item->method_type . "</td>";
        $xmlstore .= "<td>" .  $row_item->UnitType . "</td>";
        $xmlstore .= "<td>" .  $row_item->ItemCategoryName . "</td>";
        $xmlstore .= "<td>" .  $row_item->drug_reg_num . "</td>";
        $xmlstore .= "<td>" .  $row_item->gtin . "</td>";
        $xmlstore .= "<td>" .  $row_item->id_for_barcode . "</td>";

        $xmlstore .= "<td>";
        if ( $row_item->itm_category > 2) {
            $xmlstore .= "<a class=\"btn btn-xs yellow\" href=\"bar_manage_items.php?Do=Edit&Id=" .  $row_item->itm_id . "\">Edit</a>";
        }
        $xmlstore .= "</td>";

        $xmlstore .= "</tr>";
    }
}
//end xml
$xmlstore .= "</tbody>";
?>
<link rel="stylesheet" type="text/css" href="../../public/assets/global/plugins/select2/select2.css"/>
</head>
<body class="page-header-fixed page-quick-sidebar-over-content"  >
    <div class="page-container">
        <?php
        include $_SESSION['menu'];
        ?>
        <?php include PUBLIC_PATH . "html/top_im.php"; ?>
        <div class="page-content-wrapper">
            <div class="page-content">
                <div class="row">
                    <div class="col-md-12">
                        <h3 class="page-title row-br-b-wp">Item / Product Management Module
                            <a   class="btn blue right pull-right " onclick="window.open('../msd/cartons_info.php', '_blank', 'scrollbars=1,width=600,height=500');"><i class="fa fa-info-circle"></i> Cartons / Pallets Information</a>
                        </h3>

                        <div class="panel panel-primary" >
                            <div class="panel-heading">
                                <h3 class="panel-title"> <i class="fa fa-shopping-cart"></i> <?php echo $strDo; ?> Item / Product</h3>
                                <ul class="list-inline panel-actions">
                                    <li><a href="#" id="panel-fullscreen" role="button" title="Toggle fullscreen"><i class="glyphicon glyphicon-resize-full"></i></a></li>
                                </ul>
                            </div>
                            <div class="panel-body form">
                                <form name="manage_items_barcode" id="manage_items_barcode" method="post" action="bar_manage_items_action.php">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="col-md-3">
                                                <div class="control-group">
                                                    <label>Full Item Name (Type + Name + Dose)<font color="#FF0000">*</font></label>
                                                    <div class="controls">
                                                        <div class="input-group">
                                                            <span  class="input-group-addon input-circle-left  bg-blue-madison">
                                                                <i class="fa fa-th-list"></i>
                                                            </span>

                                                        <input placeholder="Example: Syp Manacid 120ml" autocomplete="off" type="text" name="txtStkName1" value="<?= $itm_name ?>" <?=(($strDo == "Edit")?'readonly':'')?> id="txtStkName1" class="form-control  " />
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="control-group">
                                                    <label>Generic Name<span class="red">*</span></label>
                                                    <div class="controls">
                                                        <div class="input-group">
                                                            <span  class="input-group-addon input-circle-left  bg-blue-madison">
                                                                <i class="fa fa-th-list"></i>
                                                            </span>

                                                        
                                                        <input placeholder="Example: Aluminium Hydroxide" autocomplete="off" type="text" name="generic_name" value="<?= $generic_name ?>" <?=(($strDo == "Edit")?'readonly':'')?> id="generic_name" class="form-control  " />
                                                    </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="control-group">
                                                    <label>Method Type / Form of Dosage<span class="red">*</span></label>
                                                    <div class="controls">
                                                        <div class="input-group">
                                                            <span  class="input-group-addon input-circle-left  bg-blue-madison">
                                                                <i class="fa fa-th-list"></i>
                                                            </span>

                                                        
                                                        <input autocomplete="on" type="text" name="method_type" value="<?= $method_type ?>" id="method_type" placeholder="Syrup / Tablet / Injection " required="" class="form-control  " />
                                                    </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="control-group">
                                                    <label>Unit of Measure<font color="#FF0000">*</font></label>
                                                    <div class="controls">
                                                    <div class="input-group">
                                                        <span class="input-group-addon     bg-blue-madison">
                                                        <i class="fa fa-money"></i>
                                                    </span>
                                                        <select name="txtStkName2" id="txtStkName2" class="form-control input-small">
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
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">

                                            <div class="col-md-3">
                                                <div class="control-group">
                                                    <label>Category<font color="#FF0000">*</font></label>
                                                    <div class="controls">
                                                        <div class="input-group">
                                                            <span  class="input-group-addon  bg-blue-madison">
                                                                <i class="fa fa-th-list"></i>
                                                            </span>

                                                        
                                                        <select name="txtStkName4" id="txtStkName4" class="form-control  ">
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
                                            </div>

                                            <div class="col-md-3">
                                                <div class="control-group">
                                                    <label>Drug Registration No.</label>
                                                    <div class="controls">
                                                        <div class="input-group">
                                                            <span  class="input-group-addon input-circle-left  bg-blue-madison">
                                                                <i class="fa fa-th-list"></i>
                                                            </span>

                                                        
                                                        <input autocomplete="off" type="text" name="drug_reg_num" value="<?= $drug_reg_num ?>" id="drug_reg_num" class="form-control  " />
                                                    </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="control-group">
                                                    <label>Description</label>
                                                    <div class="controls">
                                                        <div class="input-group">
                                                            <span  class="input-group-addon input-circle-left  bg-blue-madison">
                                                                <i class="fa fa-th-list"></i>
                                                            </span>

                                                        
                                                        <input type="text" name="txtStkName7" value="<?= $itm_des ?>" class="form-control  ">
                                                    </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3 hide">
                                                <div class="control-group">
                                                    <label>Index</label>
                                                    <div class="controls"> 
                                                        <div class="input-group">
                                                            <span  class="input-group-addon input-circle-left  bg-blue-madison">
                                                                <i class="fa fa-th-list"></i>
                                                            </span>
                                                        <!--<input type="text" name="txtStkName8" id="txtStkName8" class="form-control  " value="<?= $frmindex ?>" />
                                                            <img src="images/sort_asc.gif" alt="" onClick="update_counter()" />
                                                            <img src="images/sort_desc.gif" alt="" onClick="update_counter_down()" />-->
                                                        <input type="text" name="txtStkName8" id="spinner1" class="form-control input-small" style="border:1px solid #d8d9da;text-align:right; padding-right:5px;" value="<?= $frmindex ?>" />
                                                    </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2 hide">
                                                <div class="control-group">
                                                    <label>Status<font color="#FF0000">*</font></label>
                                                    <div class="controls">
                                                        <div class="input-group">
                                                            <span  class="input-group-addon input-circle-left  bg-blue-madison">
                                                                <i class="fa fa-th-list"></i>
                                                            </span>
                                                        <input name="txtStkName6" type="hidden1 " value="1" id="txtStkName6" class="form-control input-small">

                                                    </div>
                                                    </div>
                                                </div>
                                            </div> 
                                            <div class="col-md-3">
                                                <label class="control-label">GTIN / Barcode (Defualt)</label><span data-toggle="tooltip" title="Global Trade Item Number"> <i class="fa fa-question-circle font-blue-madison"></i></span>
                                                <div class="controls">
                                                        <div class="input-group">
                                                            <span  class="input-group-addon input-circle-left  bg-blue-madison">
                                                                <i class="fa fa-th-list"></i>
                                                            </span>
                                                        
                                                    <input  placeholder="Example:10023456789" class="form-control" type="text" id="gtin" name="gtin" value="<?php echo $gtin; ?>"/>
                                                </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3 hide">
                                                <div class="control-group">
                                                    <label>Stakeholders<font color="#FF0000">*</font></label>
                                                    <div class="controls">
                                                        <div class="input-group">
                                                            <span  class="input-group-addon   bg-blue-madison">
                                                                <i class="fa fa-th-list"></i>
                                                            </span>
                                                        
                                                        <select name="stkid[]" size="5" multiple="multiple" class="form-control  ">
                                                            <option value="<?= $_SESSION['user_stakeholder1'] ?>" selected><?= $_SESSION['user_stakeholder1'] ?></option>
                                                        </select>
                                                    </div>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="col-md-12">
                                                <div class="controls">
                                                    <hr/>
                                                    <h4 style="padding-top:30px;color:brown;">Manufacturer / Packaging Details</h4>
                                                    
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="col-md-6">
                                                <label class="control-label" >Manufacturer <span class="red">*</span></label>
                                                <div class="controls">
                                                        <div class="input-group">
                                                            <span  class="input-group-addon  bg-blue-madison">
                                                                <i class="fa fa-th-list"></i>
                                                            </span>
                                                    <?php
                                                    $manuf_opts = "";
                                                    $manuf_name = "";
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
                                                                $manuf_name = $row['stkname'];
                                                            } else {
                                                                $sel = "";
                                                            }
                                                            $manuf_opts .=  "<option value='" . $row['stkid'] . "' $sel>" . $row['stkname'] . "</option>";
                                                        }
                                                    
                                                     if($strDo == "Edit"){
                                                         echo '<input class="form-control input-xlarge" readonly   value="'.$manuf_name.'">';
                                                         echo '<input type="hidden" id="new_manufacturer" name="new_manufacturer" value="'.$manufacturer.'">';
                                                     }
                                                     else{
                                                    ?>    
                                                    <select required class="form-controlx input-xlarge select2me" id="new_manufacturer" name="new_manufacturer" <?php // echo $manufacturer;       ?>>
                                                        <?php
                                                        echo $manuf_opts;
                                                        ?>
                                                    </select>
                                                     <?php
                                                     }
                                                     ?>
                                                </div>
                                                </div>
                                            </div>
                                            <?php
                                            if($strDo == "Edit"){
                                                echo '<div class="col-md-6">
                                                        <div class="controls">';
                                                echo '<div class="note note-danger"> Note: Because same items of LMIS are used across the country, users are restricted from Renaming Items or Changing Manufacturer.<br/> In case of any query you may contact at support@lmis.gov.pk </div>';
                                                echo '</div></div>';
                                            }
                                            else{
                                            ?>
                                            <div class="col-md-3">
                                                <div class="controls">
                                                    <button type="button" style="margin-top:13%;" class="btn btn-warning" data-toggle="modal" data-target="#myModal">Add New Manufacturer</button>
                                                </div>
                                            </div>
                                            
                                            <?php
                                            }
                                            
                                            ?>


                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="col-md-12">
                                                <div class="controls">
                                                    <hr/>
                                                    <h4 style="padding-top:30px;color:brown;">Carton Size & Packaging Info</h4>
                                                    
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="col-md-3">
                                                <label class="control-label">Length of carton(cm)</label>
                                                <div class="controls">
                                                        <div class="input-group">
                                                            <span  class="input-group-addon input-circle-left  bg-blue-madison">
                                                                <i class="fa fa-th-list"></i>
                                                            </span>
                                                        
                                                    <input class="form-control input-sm dimensions positive_number" type="text" id="pack_length" name="pack_length" value="<?php echo $length; ?>"/>
                                                </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="control-label">Width of carton(cm)</label>
                                                <div class="controls">
                                                        <div class="input-group">
                                                            <span  class="input-group-addon input-circle-left  bg-blue-madison">
                                                                <i class="fa fa-th-list"></i>
                                                            </span>
                                                        
                                                    <input class="form-control input-sm dimensions positive_number" type="text" id="pack_width" name="pack_width" value="<?php echo $width; ?>"/>
                                                </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="control-label">Height of carton(cm)</label>
                                                <div class="controls">
                                                        <div class="input-group">
                                                            <span  class="input-group-addon input-circle-left  bg-blue-madison">
                                                                <i class="fa fa-th-list"></i>
                                                            </span>
                                                        
                                                    <input class="form-control input-sm dimensions positive_number" type="text" id="pack_height" name="pack_height" value="<?php echo $height; ?>"/>
                                                </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="control-label">Gross Capacity of carton(cm<sup>3</sup>):</label> 
                                                <div class="controls">
                                                    <div class="input-group">
                                                        <span  class="input-group-addon input-circle-left  bg-blue-madison">
                                                            <i class="fa fa-th-list"></i>
                                                        </span>
                                                    <input class="form-control input-sm " type="text" readonly id="gross_capacity" name="gross_capacity" value="<?php echo $gross_capacity; ?>"></div>

                                            </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="col-md-3">
                                                <label class="control-label">Net Capacity</label>
                                                <div class="controls">
                                                        <div class="input-group">
                                                            <span  class="input-group-addon input-circle-left  bg-blue-madison">
                                                                <i class="fa fa-th-list"></i>
                                                            </span>
                                                        
                                                    <input class="form-control input-sm positive_number" type="text"  id="net_capacity" name="net_capacity" value="<?php echo $net_capacity; ?>"/>
                                                </div>
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <label class="control-label">No of Cartons Per Pallet<span class="red">*</span></label>
                                                <div class="controls">
                                                        <div class="input-group">
                                                            <span  class="input-group-addon input-circle-left  bg-blue-madison">
                                                                <i class="fa fa-th-list"></i>
                                                            </span>
                                                        
                                                    <input required class="form-control input-sm positive_number" type="text" id="carton_per_pallet" name="carton_per_pallet" value="<?php echo $carton_per_pallete; ?>"/>
                                                </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="control-label">Quantity per Carton<span class="red">*</span></label>
                                                <div class="controls">
                                                        <div class="input-group">
                                                            <span  class="input-group-addon input-circle-left  bg-blue-madison">
                                                                <i class="fa fa-th-list"></i>
                                                            </span>
                                                        
                                                    <input required class="form-control input-sm positive_number" type="text" id="quantity_per_pack" name="quantity_per_pack" value="<?php echo $qty_per_pack; ?>"/>
                                                </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="control-label">Unit Price (PKR)</label>
                                                <div class="controls">
                                                        <div class="input-group">
                                                            <span  class="input-group-addon input-circle-left  bg-blue-madison">
                                                                <i class="fa fa-th-list"></i>
                                                            </span>
                                                        
                                                    <input class="form-control input-sm" maxlength="9" max="999999" type="text" id="unit_price" name="unit_price" value="<?php echo $unit_price; ?>"/>
                                                </div>
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
                                                        <button type="submit" name="" value="<?= $strDo ?>" class="btn btn-success" ><?=(($strDo=='Edit')?'Update Details':'Add New Product')?></button>
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
                        <div class="panel panel-primary"> 
                            
                            <div class="panel-heading">
                                <h3 class="panel-title"> <i class="fa fa-shopping-cart"></i> All Items / Products</h3>
                                <ul class="list-inline panel-actions">
                                    <li><a href="#" id="" role="button" title="Toggle fullscreen"></a></li>
                                </ul>
                            </div>
                            <div class="panel-body form">
                    <div class="container">
                       
                                <table class=" table table-striped table-bordered table-condensed" style="table-layout: fixed;width: 100%;word-break:break-all;  "  >
                                    <thead>
                                        <tr>
                                            <th width="3%">Sr No</th> 
                                            <th width="20%">Product</th> 
                                            <th width="15%">Manufactured By</th> 
                                            <th width="20%">Generic Name</th> 
                                            <th width="8%">Method</th> 
                                            <th width="7%">Unit</th> 
                                            <th width="7%">Category</th> 
                                            <th width="7%">Drug Reg No</th> 
                                            <th width="7%">GTIN</th> 
                                            <th width="7%">LMIS Barcode ID</th> 
                                            <th width="5%">Action</th> 
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
                                                                window.location = "bar_manage_items.php?Do=Edit&Id=" + val;
                                                            }
                                                            //Delete Manage Items
                                                            function delFunction(val) {
                                                                if (confirm("Are you sure you want to delete the record?")) {
                                                                    window.location = "bar_manage_items.php?Do=Delete&Id=" + val;
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