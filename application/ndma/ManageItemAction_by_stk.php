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
if (isset($_REQUEST['sub_cat']) && !empty($_REQUEST['sub_cat'])) {
    $sub_cat = $_REQUEST['sub_cat'];
}
if (isset($_REQUEST['main_cat']) && !empty($_REQUEST['main_cat'])) {
    $main_cat = $_REQUEST['main_cat'];
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
//Getting stkid
if (isset($_REQUEST['stkid']) && !empty($_REQUEST['stkid'])) {
    $stkid = $_REQUEST['stkid'];
}

if (isset($stkid) && is_array($stkid)) {
    $stkid = $stkid[0];
}

//overriding stkid , for stk=7

if ($_SESSION['user_id'] == 10201) {
    $stkid = array(1053);
} else if ($_SESSION['user_id'] == 10208 || $_SESSION['user_id'] == 10210){
    $stkid = array(1192);
} else {
    $stkid = array($_SESSION['user_stakeholder']);
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
$objManageItem->m_itm_category      = $sub_cat;

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

    //editing values from product group table
    //$ItemOfGroup->m_ItemID = $nstkId;
    //$ItemOfGroup->m_GroupID = $productgroupid;
    //$ItemOfGroup->EditItemGroup();
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

    //calling method to add values in stakeholder item
    $objstakeholderitem->m_stk_item = $itemid;

    $objstakeholderitem->m_stkid = $stkid;
    //Add stakeholder item
    //echo '<pre>';print_r($objstakeholderitem);exit;
    $objstakeholderitem->Addstakeholderitem();

    //calling method to add values in item of groups
    //$ItemOfGroup->m_ItemID = $itemid;
    //$ItemOfGroup->m_GroupID = $productgroupid;
    //echo '<pre>';print_r($ItemOfGroup);exit;
    //$ItemOfGroup->AddItemOfGroup1();
    //setting messages
    $_SESSION['err']['text'] = 'Data has been successfully added.';
    $_SESSION['err']['type'] = 'success';
}

if(!empty($_REQUEST['requirement'])){
     mysql_query("Delete from item_requirements WHERE wh_id='".$_SESSION['user_warehouse']."' AND item_id ='".$nstkId."' ");
    $qry = " INSERT INTO `item_requirements` 
                    ( `wh_id`, `user_id`, `item_id`, `requirement`) 
                VALUES 
                    ('".$_SESSION['user_warehouse']."', ".$_SESSION['user_id'].", '".$nstkId."', '".$_REQUEST['requirement']."');";
    mysql_query($qry);
}

header("location:ManageItems_by_stk.php");
exit;
?>