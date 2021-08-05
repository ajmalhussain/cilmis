<?php

//echo '<pre>';print_r($_REQUEST);exit;
/**
 * Manage Item Action
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

//Initializing variables
$strDo = "Add";
$nstkId = 0;
$itm_name = "";
$itm_type = "";
$itm_category = "";
$qty_carton = 0;
$field_color = "";
$itm_des = "";
$itm_status = "";
$frmindex = 0;
$extra = "";
$stkname = "";
$stkid = '';
$stkorder = 0;

//Getting hdnstkId
if (isset($_REQUEST['hdnstkId']) && !empty($_REQUEST['hdnstkId'])) {
    //Getting hdnstkId
    $nstkId = $_REQUEST['hdnstkId'];
}
//Getting hdnToDo
if (isset($_REQUEST['hdnToDo']) && !empty($_REQUEST['hdnToDo'])) {
    $strDo = $_REQUEST['hdnToDo'];
}
//Getting txtStkName1
if (isset($_REQUEST['txtStkName1']) && !empty($_REQUEST['txtStkName1'])) {
    $itm_name = $_REQUEST['txtStkName1'];
}
//Getting txtStkName2
if (isset($_REQUEST['txtStkName2']) && !empty($_REQUEST['txtStkName2'])) {
    $itm_type = $_REQUEST['txtStkName2'];
}
//Getting txtStkName4
if (isset($_REQUEST['txtStkName4']) && !empty($_REQUEST['txtStkName4'])) {
    $itm_category = $_REQUEST['txtStkName4'];
}
//Getting txtStkName5
if (isset($_REQUEST['txtStkName5']) && !empty($_REQUEST['txtStkName5'])) {
    $field_color = $_REQUEST['txtStkName5'];
}
//Getting txtStkName6
if (isset($_REQUEST['txtStkName6']) && !empty($_REQUEST['txtStkName6'])) {
    $itm_status = $_REQUEST['txtStkName6'];
}
//Getting txtStkName7
if (isset($_REQUEST['txtStkName7']) && !empty($_REQUEST['txtStkName7'])) {
    $itm_des = $_REQUEST['txtStkName7'];
}
//Getting txtStkName8
if (isset($_REQUEST['txtStkName8']) && !empty($_REQUEST['txtStkName8'])) {
    $frmindex = $_REQUEST['txtStkName8'];
}
//Getting generic_name
if (isset($_REQUEST['generic_name']) && !empty($_REQUEST['generic_name'])) {
    $generic_name = $_REQUEST['generic_name'];
} else
    $generic_name = '';

if (isset($_REQUEST['drug_reg_num']) && !empty($_REQUEST['drug_reg_num'])) {
    $drug_reg_num = $_REQUEST['drug_reg_num'];
} else
    $drug_reg_num = '';

if (isset($_REQUEST['method_type']) && !empty($_REQUEST['method_type'])) {
    $method_type = $_REQUEST['method_type'];
} else
    $method_type = '';
if (isset($_REQUEST['new_manufacturer']) && !empty($_REQUEST['new_manufacturer'])) {
    $new_manufacturer = $_REQUEST['new_manufacturer'];
} else
    $new_manufacturer = '';
if (isset($_REQUEST['txtStkName1']) && !empty($_REQUEST['txtStkName1'])) {
    $brand_name = mysql_real_escape_string($_REQUEST['txtStkName1']);
} else
    $brand_name = '';
if (isset($_REQUEST['pack_length']) && !empty($_REQUEST['pack_length'])) {
    $length = $_REQUEST['pack_length'];
} else
    $length = '';
if (isset($_REQUEST['pack_width']) && !empty($_REQUEST['pack_width'])) {
    $width = $_REQUEST['pack_width'];
} else
    $width = '';
if (isset($_REQUEST['pack_height']) && !empty($_REQUEST['pack_height'])) {
    $height = $_REQUEST['pack_height'];
} else
    $height = '';
if (isset($_REQUEST['net_capacity']) && !empty($_REQUEST['net_capacity'])) {
    $net_capacity = $_REQUEST['net_capacity'];
} else
    $net_capacity = '';
if (isset($_REQUEST['gross_capacity']) && !empty($_REQUEST['gross_capacity'])) {
    $gross_capacity = $_REQUEST['gross_capacity'];
} else
    $gross_capacity = '';
if (isset($_REQUEST['carton_per_pallet']) && !empty($_REQUEST['carton_per_pallet'])) {
    $carton = $_REQUEST['carton_per_pallet'];
} else
    $carton = '';
if (isset($_REQUEST['quantity_per_pack']) && !empty($_REQUEST['quantity_per_pack'])) {
    $quanity = $_REQUEST['quantity_per_pack'];
} else
    $quanity = '';
if (isset($_REQUEST['gtin']) && !empty($_REQUEST['gtin'])) {
    $gtin = $_REQUEST['gtin'];
} else
    $gtin = '';
if (isset($_REQUEST['unit_price']) && !empty($_REQUEST['unit_price'])) {
    $price = $_REQUEST['unit_price'];
} else
    $price = '';
//Getting stkid
if (isset($_REQUEST['stkid']) && !empty($_REQUEST['stkid'])) {
    $stkid = $_REQUEST['stkid'];
}

if (isset($stkid) && is_array($stkid)) {
    $stkid = $stkid[0];
}

//overriding stkid , for stk=7
if ($stkid != '1' && $stkid != '145') {
    $stkid = '7';
}


//product group id here
//
//Getting select2
if (isset($_REQUEST['select2']) && !empty($_REQUEST['select2'])) {
    $productgroupid = $_REQUEST['select2'];
}

//Filling value in Manage Item objects variables

list($unit, $type) = explode('-', $itm_type);
$objManageItem->m_npkId = $nstkId;
$objManageItem->m_itm_name = $itm_name;
$objManageItem->m_generic_name = $generic_name;
$objManageItem->m_drug_reg_num = $drug_reg_num;
$objManageItem->m_method_type = $method_type;
$objManageItem->m_itm_type = $type;
$objManageItem->m_itm_unit = $unit;
$objManageItem->m_itm_category = $itm_category;

$objManageItem->m_qty_carton = $qty_carton;
$objManageItem->m_itm_des = $itm_des;
$objManageItem->m_itm_status = $itm_status;

$objManageItem->m_frmindex = $frmindex;
$objManageItem->m_extra = $extra;

/**
 * 
 * EditManageItem
 * 
 */
if ($strDo == "Edit") {
    $objManageItem->EditManageItem();

    //editing value from stakeholder table
    $objstakeholderitem->m_stk_item = $nstkId;
    $objstakeholderitem->m_stkid = $stkid;
    $objstakeholderitem->Editstkholderitem(); 
    $item_pack_size_id = $nstkId;
    //setting messages
    $_SESSION['err']['text'] = 'Data has been successfully updated.';
    $_SESSION['err']['type'] = 'success';
}
/**
 * 
 * AddManageItem
 * 
 */
if ($strDo == "Add") {

    $itemid = $objManageItem->AddManageItem(); 
    $objstakeholderitem->m_stk_item = $itemid; 
    $objstakeholderitem->m_stkid = $stkid; 
    $objstakeholderitem->Addstakeholderitem(); 
    $item_pack_size_id = $itemid;
    //check manufacturer
    //setting messages
    $_SESSION['err']['text'] = 'Data has been successfully added.';
    $_SESSION['err']['type'] = 'success';
}
$checkManufacturer = mysql_query("select stkid,stkname from stakeholder where stkid='" . $new_manufacturer . "' AND stk_type_id = 3") or die('Err of manuf 1:' . mysql_error());
//print_r($checkManufacturer);exit;
$manufacturer = mysql_num_rows($checkManufacturer);
//print_r($manufacturer);exit;
$stkRow = mysql_fetch_assoc($checkManufacturer);
//if not exist for any product
if ($manufacturer < 1) {
    // Get Stakeholder Item
    $getStkOrder = "SELECT
							MAX(stakeholder.stkorder) + 1 AS stkorder
						FROM
							stakeholder
						WHERE
							stakeholder.stk_type_id = 3";
    //Query result
    $getStkOrderRes = mysql_fetch_array(mysql_query($getStkOrder));
    $stkOrder = $getStkOrderRes['stkorder'];
    //Assigning data to objstk
    //stkname
    $objstk->m_stkname = $new_manufacturer;
    //stkorder
    $objstk->m_stkorder = $stkOrder;
    //ParentID
    $objstk->ParentID = '1';
    //stk_type_id
    $objstk->m_stk_type_id = '3';
    //level
    $objstk->m_lvl = '1';
    //Add Stakeholder
    $stkid = $objstk->AddStakeholder();
} else {
    $stkid = $stkRow['stkid'];
}
    //stkid
    $objstakeholderitem->m_stkid = $stkid;
    //stk_item
    $objstakeholderitem->m_stk_item = $item_pack_size_id;
    //brand_name
    $objstakeholderitem->brand_name = (!empty($_REQUEST['txtStkName1'])) ? mysql_real_escape_string($_REQUEST['txtStkName1']) : '';
    //carton_per_pallet
    $objstakeholderitem->carton_per_pallet = (!empty($_REQUEST['carton_per_pallet'])) ? mysql_real_escape_string($_REQUEST['carton_per_pallet']) : '';
    //quantity_per_pack
    $objstakeholderitem->quantity_per_pack = (!empty($_REQUEST['quantity_per_pack'])) ? mysql_real_escape_string($_REQUEST['quantity_per_pack']) : '';
    //gtin
    $objstakeholderitem->gtin = (!empty($_REQUEST['gtin'])) ? mysql_real_escape_string($_REQUEST['gtin']) : '';
    //gross_capacity
    $objstakeholderitem->gross_capacity = (!empty($_REQUEST['gross_capacity'])) ? mysql_real_escape_string($_REQUEST['gross_capacity']) : '';
    //net_capacity
    $objstakeholderitem->net_capacity = (!empty($_REQUEST['net_capacity'])) ? mysql_real_escape_string($_REQUEST['net_capacity']) : '';
    //
    $objstakeholderitem->pack_length = (!empty($_REQUEST['pack_length'])) ? mysql_real_escape_string($_REQUEST['pack_length']) : '';
    //pack_length
    $objstakeholderitem->pack_width = (!empty($_REQUEST['pack_width'])) ? mysql_real_escape_string($_REQUEST['pack_width']) : '';
    //pack_height
    $objstakeholderitem->pack_height = (!empty($_REQUEST['pack_height'])) ? mysql_real_escape_string($_REQUEST['pack_height']) : '';
    $objstakeholderitem->unit_price = (!empty($_REQUEST['unit_price'])) ? mysql_real_escape_string($_REQUEST['unit_price']) : '';
//    //Add stakeholder item1
//    $getStkItem = "select * from stakeholder_item where stk_item=" . $item_pack_size_id . " AND stkid=" . $stkid . " AND brand_name = '" . $brand_name . "' ";
////    print_r($getStkItem);exit;
//    $resStkItem = mysql_query($getStkItem) or die(mysql_error());
//    $numStkItem = mysql_num_rows($resStkItem);
    if ($strDo == "Add") {
        $stkItemId = $objstakeholderitem->Addstakeholderitem1();
    } else if ($strDo == "Edit") {
        $objstakeholderitem->m_npkId = (!empty($_REQUEST['edit_manufacturer'])) ? mysql_real_escape_string($_REQUEST['edit_manufacturer']) : '';
        $stkItemId = $objstakeholderitem->UpdateStakeholderItem();
    } 
//    exit;
header("location:bar_manage_items.php");
exit;
?>