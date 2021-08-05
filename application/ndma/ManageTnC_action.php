<?php

include("../includes/classes/AllClasses.php");
//echo '<pre>';print_r($_REQUEST);exit;
 
$strDo=$_REQUEST['hdnToDo'];
if (isset($_REQUEST['id']) && !empty($_REQUEST['id'])) {
    $id = $_REQUEST['id'];
}
if (isset($_REQUEST['text_type']) && !empty($_REQUEST['text_type'])) {
    $text_type = $_REQUEST['text_type'];
}
if (isset($_REQUEST['text_value']) && !empty($_REQUEST['text_value'])) {
    $text_value = $_REQUEST['text_value'];
}
$status='0';
if (isset($_REQUEST['status']) && !empty($_REQUEST['status'])) {
    $status = $_REQUEST['status'];
}

if ($strDo == "Edit") {

    $strSql = "UPDATE text_values ";
    $strSql .= "SET text_value='" . $text_value . "'";
    $strSql .= ",type='" . $text_type."' ";
    $strSql .= ",is_active=" . $status;

    $strSql .= " WHERE pk_id='" . $id."' ";
//        print_r($strSql);exit;
    $rsSql = mysql_query($strSql) or die("Error Edit Text Val");
    $_SESSION['err']['text'] = 'Data has been updated.';
    $_SESSION['err']['type'] = 'success';
}
 
if ($strDo == "Add") {
    $strSql = "INSERT INTO `text_values` (`stkid`, `type`, `text_value`, `updatedby`,  `is_active`) 
        VALUES ( '".$_SESSION['user_stakeholder1']."', '".$text_type."', '".$text_value."', ".$_SESSION['user_id'].",  '".$status."');
";
//echo $strSql;exit;
    $rsSql = mysql_query($strSql) or die("Error Text Vals");

    $id = mysql_insert_id();
    
    $_SESSION['err']['text'] = 'Data has been successfully added.';
    $_SESSION['err']['type'] = 'success';
}

//Unsetting session
unset($_SESSION['pk_id']);
header("location:".$_REQUEST['this_file_name'].".php");
exit;
?>