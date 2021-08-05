<?php

include("../includes/classes/AllClasses.php");
//echo '<pre>';print_r($_REQUEST);exit;
$nstkId = 0;
$stkname = "";
$stkgroupid = 0;
$strNewGroupName = "";
$stktype = 0;
$prov_id = 0;

if (isset($_REQUEST['hdnstkId']) && !empty($_REQUEST['hdnstkId'])) {
    //getting hdnstkId
    $nstkId = $_REQUEST['hdnstkId'];
}

if (isset($_REQUEST['hdnToDo']) && !empty($_REQUEST['hdnToDo'])) {
    //getting hdnToDo
    $strDo = $_REQUEST['hdnToDo'];
}

if (isset($_REQUEST['txtStkName']) && !empty($_REQUEST['txtStkName'])) {
    //Stakeholder name
    $stkname = $_REQUEST['txtStkName'];
}

if (isset($_REQUEST['lstStktype']) && !empty($_REQUEST['lstStktype'])) {
    //lst Stakeholder type
    $stktype = $_REQUEST['lstStktype'];
}
if (isset($_REQUEST['lstLvl']) && !empty($_REQUEST['lstLvl'])) {
    //getting lstLvl
    $lstLvl = $_REQUEST['lstLvl'];
}
//Filling value in $objstk objects variables


/**
 * 
 * Edit Stakeholder
 * 
 */
if ($strDo == "Edit") {
    $id = $_SESSION['user_stakeholder'];
    $strSql1 = "UPDATE  tbl_warehouse SET wh_name='" . $stkname . "' WHERE wh_id='" . $nstkId . "' AND stkofficeid='" . $id . "' ";

    $rsSql = mysql_query($strSql1) or die($strSql1 . mysql_error());
    $_SESSION['err']['text'] = 'Supplier Name updated.';
    $_SESSION['err']['type'] = 'success';
}
/**
 * 
 * Add Stakeholder
 * 
 */
if ($strDo == "Add") {

    $id  = $_SESSION['user_stakeholder'];
    $strSql1 = "INSERT INTO tbl_warehouse SET wh_name='" . $stkname . "' , stkid='" . $id . "', stkofficeid='" . $id . "' , prov_id='" . $_SESSION['user_province1'] . "' , reporting_start_month='" . date('Y-m-01') . "' ";
    $rsSql = mysql_query($strSql1) or die($strSql1 . mysql_error());
    
    $_SESSION['err']['text'] = 'Data has been successfully added.';
    $_SESSION['err']['type'] = 'success';
}

//Unsetting session
unset($_SESSION['pk_id']);
if (!empty($_REQUEST['redirect_to']))
    header("location:" . $_REQUEST['redirect_to'] . ".php");
else
    header("location:ManageStakeholders.php");
exit;
?>