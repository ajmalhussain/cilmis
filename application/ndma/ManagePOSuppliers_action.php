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

if (isset($_REQUEST['new_supplier']) && !empty($_REQUEST['new_supplier'])) {
    //Stakeholder name
    $stkname = $_REQUEST['new_supplier'];
}
if (isset($_REQUEST['ntn']) && !empty($_REQUEST['ntn'])) {
    //Stakeholder name
    $ntn = $_REQUEST['ntn'];
}
if (isset($_REQUEST['gstn']) && !empty($_REQUEST['gstn'])) {
    //Stakeholder name
    $gstn = $_REQUEST['gstn'];
}
if (isset($_REQUEST['country']) && !empty($_REQUEST['country'])) {
    //Stakeholder name
    $country = $_REQUEST['country'];
}
if (isset($_REQUEST['contact_person']) && !empty($_REQUEST['contact_person'])) {
    //Stakeholder name
    $contact_person = $_REQUEST['contact_person'];
}
if (isset($_REQUEST['contact_number']) && !empty($_REQUEST['contact_number'])) {
    //Stakeholder name
    $contact_number = $_REQUEST['contact_number'];
}

if (isset($_REQUEST['contact_email']) && !empty($_REQUEST['contact_email'])) {
    //lst Stakeholder type
    $contact_email = $_REQUEST['contact_email'];
}
if (isset($_REQUEST['status']) && !empty($_REQUEST['status'])) {
    //getting lstLvl
    $status = $_REQUEST['status'];
}
if (isset($_REQUEST['status_bw']) && !empty($_REQUEST['status_bw'])) {
    //getting lstLvl
    $status_bw = $_REQUEST['status_bw'];
}
if (isset($_REQUEST['company_address']) && !empty($_REQUEST['company_address'])) {
    //getting lstLvl
    $company_address = $_REQUEST['company_address'];
}
//Filling value in $objstk objects variables
$objstk->m_stkname = $stkname;
$objstk->m_stk_type_id = 6;
$objstk->m_lvl = 1;
$objstk->relevant_stk = $_SESSION['user_stakeholder'];
$objstk->ntn = $ntn;
$objstk->gstn = $gstn;
$objstk->m_cpname = $contact_person;
$objstk->m_cnnumber = $contact_number;
$objstk->email = $contact_email;
$objstk->address = $company_address;
$objstk->origin_country = $country;
$objstk->company_status = $status;
$objstk->b_w_status = $status_bw;
$objstk->m_npkId = $nstkId;


/**
 * 
 * Edit Stakeholder
 * 
 */
if ($strDo == "Edit") {

    $strSql = "UPDATE stakeholder ";
    $stkname = "SET stkname='" . $objstk->m_stkname . "'";
    if ($objstk->m_stkname != '') {
        $strSql .= $stkname;
    }
    if (isset($objstk->m_stk_type_id) && !empty($objstk->m_stk_type_id)) {
        $stk_type_id = ",stk_type_id=" . $objstk->m_stk_type_id;
        $strSql .= $stk_type_id;
    }

    $lvl = ",lvl=" . $objstk->m_lvl;
    if ($objstk->m_lvl != '') {
        $strSql .= $lvl;
    }

    $relStakeholder = ",relevant_stk=" . $objstk->relevant_stk;
    if ($objstk->relevant_stk != '') {
        $strSql .= $relStakeholder;
    }
    $ntn_sql = ",ntn='" . $objstk->ntn . "'";
    if ($objstk->ntn != '') {
        $strSql .= $ntn_sql;
    }
    $gstn_sql = ",gstn='" . $objstk->gstn . "'";
    if ($objstk->gstn != '') {
        $strSql .= $gstn_sql;
    }
    $sql_p = ",contact_person='" . $objstk->m_cpname . "'";
    if ($objstk->m_cpname != '') {
        $strSql .= $sql_p;
    }
    $sql_n = ",contact_numbers='" . $objstk->m_cnnumber . "'";
    if ($objstk->m_cnnumber != '') {
        $strSql .= $sql_n;
    }
    $sql_e = ",contact_emails='" . $objstk->email . "'";
    if ($objstk->email != '') {
        $strSql .= $sql_e;
    }
    $sql_a = ",contact_address='" . $objstk->address . "'";
    if ($objstk->address != '') {
        $strSql .= $sql_a;
    }
    $sql_s = ",company_status='" . $objstk->company_status . "'";
    if ($objstk->company_status != '') {
        $strSql .= $sql_s;
    }
    $sql_c = ",origin_country=" . $objstk->origin_country;
    if ($objstk->origin_country != '') {
        $strSql .= $sql_c;
    }
    $sql_w = ",b_w_status='" . $objstk->b_w_status . "'";
    if ($objstk->b_w_status != '') {
        $strSql .= $sql_w;
    }

    $strSql .= " WHERE stkid=" . $objstk->m_npkId;
//        print_r($strSql);exit;
    $rsSql = mysql_query($strSql) or die("Error EditSupplier");
    $_SESSION['err']['text'] = 'Supplier has been updated.';
    $_SESSION['err']['type'] = 'success';
}
/**
 * 
 * Add Stakeholder
 * 
 */
if ($strDo == "Add") {
    //GetMaxRank
    $objstk->m_stkorder = $objstk->GetMaxRank() + 1;
    $strSql = "INSERT INTO  stakeholder (stkname,stkorder,stk_type_id,lvl,relevant_stk,contact_person,contact_numbers,contact_emails,contact_address,company_status,origin_country,b_w_status,ntn,gstn) VALUES('" . $objstk->m_stkname . "'," . $objstk->m_stkorder . "," . $objstk->m_stk_type_id . "," . $objstk->m_lvl . "," . $objstk->relevant_stk . ",'" . $objstk->m_cpname . "','" . $objstk->m_cnnumber . "','" . $objstk->email . "','" . $objstk->address . "','" . $objstk->company_status . "'," . $objstk->origin_country . ",'" . $objstk->b_w_status . "','" . $objstk->ntn . "','" . $objstk->gstn . "' )";
//echo $strSql;exit;
    $rsSql = mysql_query($strSql) or die("Error AddSupplier");

    $id = mysql_insert_id();
    if ($objstk->m_MainStakeholder == '') {
        $objstk->m_MainStakeholder = 'NULL';
    }
    if ($objstk->m_MainStakeholder == 'NULL') {
        $strSql1 = "Update stakeholder SET MainStakeholder=" . $id . " where stkid=" . $id;
        $rsSql = mysql_query($strSql1) or die($strSql1 . mysql_error());
    }
    $_SESSION['err']['text'] = 'Data has been successfully added.';
    $_SESSION['err']['type'] = 'success';
}

//Unsetting session
unset($_SESSION['pk_id']);
header("location:ManagePOSuppliers.php");
exit;
?>