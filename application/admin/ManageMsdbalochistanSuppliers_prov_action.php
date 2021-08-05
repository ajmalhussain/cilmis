<?php
include("../includes/classes/AllClasses.php");
//echo '<pre>';print_r($_REQUEST);exit;
$nstkId = 0;
$stkname = "";
$stkgroupid = 0;
$strNewGroupName = "";
$stktype = 0;
$prov_id = 0;
//stk name person
$cpname = "";

//stk number person
$cnnumber = 0;

//stk email
$email = "";

//stk gstn
$gstn = 0;

//stk ntn
$ntn = 0;

//stk address
$address = "";


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

if (isset($_REQUEST['cpname']) && !empty($_REQUEST['cpname'])) {
    //getting lstLvl
    $cpname = $_REQUEST['cpname'];
}
if (isset($_REQUEST['cnnumber']) && !empty($_REQUEST['cnnumber'])) {
    //getting lstLvl
    $cnnumber = $_REQUEST['cnnumber'];
}
if (isset($_REQUEST['email']) && !empty($_REQUEST['email'])) {
    //getting lstLvl
    $email = $_REQUEST['email'];
}
if (isset($_REQUEST['gstn']) && !empty($_REQUEST['gstn'])) {
    //getting lstLvl
    $gstn = $_REQUEST['gstn'];
}
if (isset($_REQUEST['ntn']) && !empty($_REQUEST['ntn'])) {
    //getting lstLvl
    $ntn = $_REQUEST['ntn'];
}
if (isset($_REQUEST['address']) && !empty($_REQUEST['address'])) {
    //getting lstLvl
    $address = $_REQUEST['address'];
}
//Filling value in $objstk objects variables
$objstk->m_stkname = $stkname;
$objstk->m_stk_type_id = $stktype;
$objstk->m_npkId = $nstkId;
$objstk->m_lvl = $lstLvl;
$objstk->m_cpname = $cpname;
$objstk->m_cnnumber = $cnnumber;
$objstk->m_email = $email;
$objstk->m_gstn = $gstn;
$objstk->m_ntn = $ntn;
$objstk->m_address = $address;

/**
 * 
 * Edit Stakeholder
 * 
 */
if ($strDo == "Edit") {
    
    $strSql = "UPDATE stakeholder SET stkid=" . $objstk->m_npkId;
        $stkname = ",stkname='" . $objstk->m_stkname . "'";
        if ($objstk->m_stkname != '') {
            $strSql .=$stkname;
        }
        //stakeholder order
        $stkorder = ",stkorder=" . $objstk->m_stkorder;
        if ($objstk->m_stkorder != '') {
            $strSql .=$stkorder;
        }
        //parent id
        $ParentID = ",ParentID=" . $objstk->m_ParentID;
        if ($objstk->m_ParentID != '') {
            $strSql .=$ParentID;
        }

        if (isset($objstk->m_stk_type_id) && !empty($objstk->m_stk_type_id)) {
            $stk_type_id = ",stk_type_id=" . $objstk->m_stk_type_id;
            $strSql .=$stk_type_id;
        }

        $lvl = ",lvl=" . $objstk->m_lvl;
        if ($objstk->m_lvl != '') {
            $strSql .=$lvl;
        }

        $MainStakeholder = ",MainStakeholder=" . $objstk->m_MainStakeholder;
        if ($objstk->m_MainStakeholder != '') {
            $strSql .=$MainStakeholder;
        }

        $strSql .=" WHERE stkid=" . $objstk->m_npkId;
        $rsSql = mysql_query($strSql) or die("Error EditStakeholder");
         
    if($objstk->m_stk_type_id == 6)
    {
        $strSql1 = "UPDATE  tbl_warehouse SET wh_name='".$objstk->m_stkname."' WHERE stkid='".$nstkId."' AND stkofficeid='".$nstkId."' ";
        $rsSql = mysql_query($strSql1) or die($strSql1 . mysql_error());
    }
    $_SESSION['err']['text'] = 'Supplier Name updated.';
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
    
    if ($objstk->m_stkname == '') {
            $objstk->m_stkname = 'NULL';
        }
        //check stakeholder order
        if ($objstk->m_stkorder == '') {
            $objstk->m_stkorder = 'NULL';
        }
        //check parent id
        if ($objstk->m_ParentID == '') {
            $objstk->m_ParentID = 'NULL';
        }
        //check stakeholder type id
        if ($objstk->m_stk_type_id == '') {
            $objstk->m_stk_type_id = 0;
        }
        //check level
        if ($objstk->m_lvl == '') {
            $objstk->m_lvl = 1;
        }
        if ($objstk->m_MainStakeholder == '') {
            $objstk->m_MainStakeholder = 'NULL';
        }
		if ($objstk->m_cpname == '') {
            $objstk->m_cpname = 'NULL';
        }
		if ($objstk->m_cnnumber == '') {
            $objstk->m_cnnumber = 'NULL';
        }
		if ($objstk->m_email == '') {
            $objstk->m_email = 'NULL';
        }
		if ($objstk->m_gstn == '') {
            $objstk->m_gstn = 'NULL';
        }
		if ($objstk->m_ntn == '') {
            $objstk->m_ntn = 'NULL';
        }
		if ($objstk->m_address == '') {
            $objstk->m_address = 'NULL';
        }
    $strSql = "INSERT INTO  stakeholder (stkname,stkorder,ParentID,stk_type_id,lvl,MainStakeholder,contact_person,contact_emails,contact_numbers,contact_address,ntn,gstn) VALUES('" . $objstk->m_stkname . "'," . $objstk->m_stkorder . "," . $objstk->m_ParentID . "," . $objstk->m_stk_type_id . "," . $objstk->m_lvl . "," . $objstk->m_MainStakeholder . ",'" . $objstk->m_cpname . "','" . $objstk->m_email . "'," . $objstk->m_cnnumber . ",'" . $objstk->m_address . "'," . $objstk->m_ntn . "," . $objstk->m_gstn . ")";
//echo $strSql;exit;
    $rsSql = mysql_query($strSql) or die("Error AddStakeholder");

    $id = mysql_insert_id();
  
    if ($objstk->m_MainStakeholder == 'NULL') {
        $strSql1 = "Update stakeholder SET MainStakeholder=" . $id . " where stkid=" . $id;
        $rsSql = mysql_query($strSql1) or die($strSql1 . mysql_error());
    }

    if($objstk->m_stk_type_id == 6)
    {
        $strSql1 = "INSERT INTO tbl_warehouse SET wh_name='".$objstk->m_stkname."' , stkid='".$id."', stkofficeid='".$id."' , prov_id='".$_SESSION['user_province1']."' , reporting_start_month='".date('Y-m-01')."' ";
        $rsSql = mysql_query($strSql1) or die($strSql1 . mysql_error());
    }

    
    
    
    $_SESSION['err']['text'] = 'Data has been successfully added.';
    $_SESSION['err']['type'] = 'success';
}

//Unsetting session
unset($_SESSION['pk_id']);
if(!empty($_REQUEST['redirect_to']) )
    header("location:".$_REQUEST['redirect_to'].".php");
else
    header("location:ManageStakeholders.php");
exit;
?>