<?php
/**
 * new_issue
 * @package im
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
//include AllClasses
include("../includes/classes/AllClasses.php");
//include header
include(PUBLIC_PATH . "html/header.php");
include "../includes/styling/dynamic_theme_color.php";
//echo '<pre>';print_r($_SESSION);exit;
//title
$title = "New Issue";
//transaction number
$TranNo = '';
//stock id
$stock_id = 0;
//user id
$userid = $_SESSION['user_id'];
//warehouse id
$wh_id = $_SESSION['user_warehouse'];
//Received Remarks 
$ReceivedRemarks = $issued_by = $funding_source = $js = '';
//Get Temp Stock Issue
$tempstocksIssue = $objStockMaster->GetTempStockIssue($userid, $wh_id, 2);
if ($tempstocksIssue != FALSE) {
    //result
    $result = mysql_fetch_object($tempstocksIssue);
    //stock id
    $stock_id = $result->PkStockID;
    //transaction date
    $TranDate = date('d/m/Y', strtotime($result->TranDate));
    //funding source
    $funding_source = $result->funding_source;
    //receive remarks
    $ReceivedRemarks = $result->ReceivedRemarks;
    //transaction number
    $TranNo = $result->TranNo;
    //transaction ref
    $TranRef = $result->TranRef;
    //warehouse name
    $wh_name = $result->wh_name;
    //warehouse id
    $whouse_id = $result->WHIDTo;
    //issued by
    $issued_by = $result->issued_by;
    //to warehouse
    $whTo = $result->WHIDTo;
} else {
    //transaction date
    $TranDate = date("d/m/Y");
    //warehouse name
    $wh_name = '';
    //transaction ref
    $TranRef = '';
}
//get user warehouse
//$warehouses = $warehouses1 = $objwarehouse->GetUserWarehouses();
$warehouses = $warehouses1 = $objwarehouse->get_funding_sources_of_province($_SESSION['user_province1']);
//GetTempStocksIssueList
$tempstocks = $objStockMaster->GetTempStocksIssueList($userid, $wh_id, 2);
//GetAllWHProduct
$items = $objManageItem->GetAllWHProduct();

// Query to get the last transaction information of the warehouse

unset($_SESSION['lastTransStk']);
unset($_SESSION['lastTransWH']);
unset($_SESSION['lastTransOffice']);
unset($_SESSION['lastTransProv']);
unset($_SESSION['lastTransDist']);

if ($wh_id == 123) { // If central warehouse
    //select query
    //gets
    //WHIDTo,
    //stkofficeid,
    //stkid,
    //lvl,
    //prov_id,
    //dist_id
    $qry = "SELECT
				tbl_stock_master.WHIDTo,
				tbl_warehouse.stkofficeid,
				tbl_warehouse.stkid,
				stakeholder.lvl,
				tbl_warehouse.prov_id,
				tbl_warehouse.dist_id
			FROM
				tbl_stock_master
			INNER JOIN tbl_warehouse ON tbl_stock_master.WHIDTo = tbl_warehouse.wh_id
			INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
			WHERE
				tbl_stock_master.WHIDFrom = $wh_id
			AND tbl_stock_master.TranTypeID = 2
			ORDER BY
				tbl_stock_master.PkStockID DESC
			LIMIT 1";
    //query result
    $row = mysql_fetch_array(mysql_query($qry));
    $_SESSION['lastTransStk'] = $row['stkid'];
    $_SESSION['lastTransWH'] = $row['WHIDTo'];
    $_SESSION['lastTransOffice'] = $row['lvl'];
    $_SESSION['lastTransProv'] = $row['prov_id'];
    $_SESSION['lastTransDist'] = $row['dist_id'];
}
?>
<link rel="stylesheet" type="text/css" href="../../public/assets/global/plugins/select2/select2.css"/>

</head>
<!-- END HEAD -->

<body class="page-header-fixed page-quick-sidebar-over-content" >
    <!-- BEGIN HEADER -->
    <div class="page-container">
        <?php
        //include top
        //include top_im
        include PUBLIC_PATH . "html/top.php";
        //include top_im
        include PUBLIC_PATH . "html/top_im.php";
        ?>
        <div class="page-content-wrapper">
            <div class="page-content">
                <div class="row">
                    <div class="col-md-12">
                        
                        
                        <div class="panel panel-primary">
                            <div class="panel-heading">
                                <h3 class="panel-title"> <i class="fa fa-shopping-cart"></i> Automated Barcode Stock Issuance</h3>
                                <ul class="list-inline panel-actions">
                                    <li><a href="#" id="panel-fullscreen" role="button" title="Toggle fullscreen"><i class="glyphicon glyphicon-resize-full"></i></a></li>
                                </ul>
                            </div>
                            <div class="panel-body form">
                                <?php if (isset($_GET['warehouse']) && $_GET['warehouse'] == 1) { ?>
                                    <div class="alert alert-danger">
                                        <button data-dismiss="alert" class="close" type="button"> X</button>
                                        Please select warehouse! </div>
                                <?php } ?>
                                <form method="POST" name="new_issue_form" id="new_issue_form" action="bar_issue_action.php">
                                    <input type="hidden" id="show_national" value="yes">
                                    <!-- Row -->
                                    <div class="row">
                                        <div class="col-md-12"> </div>
                                        <div class="col-md-12">
                                            
                                            <div class="col-md-3">
                                                    <div class="control-group">
                                                        <label class="control-label" for="receive_no"> Issue No (Auto Generated) </label>
                                                        <div class="controls"> 
                                                            <div class="input-group">
                                                                <span  class="input-group-addon input-circle-left  bg-blue-madison">
                                                                    <i class="fa fa-th-list"></i>
                                                                </span>
                                                                <input class="form-control input-small" id="issue_no" name="issue_no" type="text" disabled=""
                                                                value="<?php echo $TranNo; ?>"/>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="control-group">
                                                        <label class="control-label" for="receive_no"> Date </label>
                                                        <div class="controls"> 
                                                            <div class="input-group">
                                                                <span  class="input-group-addon input-circle-left  bg-blue-madison">
                                                                    <i class="fa fa-clock-o"></i>
                                                                </span>
                                                                <input class="form-control input-small" id="issue_date" readonly name="issue_date" required type="text" value="<?php echo $TranDate; ?>"/>
                                                    </div>
                                                </div>
                                            </div>
                                            </div>
                                            
                                            <div class="col-md-3">
                                                <div class="control-group">
                                                        <label class="control-label" for="receive_no"> Authority Letter No / Ref No </label>
                                                        <div class="controls"> 
                                                            <div class="input-group">
                                                                <span  class="input-group-addon input-circle-left  bg-blue-madison">
                                                                    <i class="fa fa-tag"></i>
                                                                </span>
                                                                <input class="form-control input-small" id="issue_ref" name="issue_ref" type="text" value="<?php echo $TranRef; ?>" />
                                                    </div>
                                                </div>
                                            </div>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="control-label">
                                                    Delivery Mode /  Issued By
                                                </label>
                                                <div class="controls">
                                                    <div class="input-group">
                                                        <span class="input-group-addon     bg-blue-madison">
                                                            <i class="fa fa-truck"></i>
                                                        </span>
                                                        <select class="form-control input-small" name="issued_by" id="issued_by">
                                                            <?php
                                                            //select quey
                                                            //gets
                                                            //pk id
                                                            //list value
                                                            $qry = "SELECT
                                                                            list_detail.pk_id,
                                                                            list_detail.list_value
                                                                    FROM
                                                                            list_detail
                                                                    WHERE
                                                                            list_detail.list_master_id = 21";
                                                            if (!empty($_SESSION['user_role']) && $_SESSION['user_role'] == 38) {
                                                                $qry .= " AND list_detail.pk_id in (119,798,799) ";
                                                            }
                                                            $qry .= " ORDER BY
                                                                            -list_detail.rank desc,
                                                                            list_value";
                                                            //query result
                                                            $qryRes = mysql_query($qry);
                                                            //fetch result
                                                            while ($row = mysql_fetch_array($qryRes)) {
                                                                $sel = ($issued_by == $row['pk_id']) ? 'selected="selected"' : '';
                                                                echo "<option value=\"$row[pk_id]\" $sel>$row[list_value]</option>";
                                                            }
                                                            ?>
                                                        </select>
                                                        <?php ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <?php if ($wh_id == 123) { ?>
                                            <input type="hidden" id="showSelection" value="<?php echo $_SESSION['lastTransStk']; ?>" />
                                        <?php } ?>
                                        <?php
                                        if ($wh_name == '') {
                                           
                                            $button = 'true';

                                            $user_lvl = (!empty($_SESSION['user_level']) ? $_SESSION['user_level'] : '');
                                               //-------code section originally was in levelcombos_all_level file, now shifted here ---Start----

                                                    $user_lvl = (!empty($_SESSION['user_level']) ? $_SESSION['user_level'] : '' );
                                                    $category = '1,4';
                                                    $stk_where = " ";
                                                    if($_SESSION['user_stakeholder1'] == 145){
                                                        $category='5';
                                                        $stk_where = " AND stakeholder.stkid in (2,7,276,74,145) ";
                                                    }

                                                    if($_SESSION['user_stakeholder1'] == 2){
                                                        $category='5';
                                                        $stk_where = " AND stakeholder.stkid in (2,7,73) ";
                                                    }

                                                    if($_SESSION['user_level'] >= 3){

                                                        //$stk_where = " AND stakeholder.stkid = ".$_SESSION['user_stakeholder1']." ";
                                                    }
                                                    if( $_SESSION['user_stakeholder1'] == 276){
                                                        $category='5';
                                                        $stk_where = " AND stakeholder.stkid in (2,7,276,74,145) ";
                                                    }
                                                    //page name
                                                    $_SESSION['page_name'] = basename($_SERVER['PHP_SELF']);
                                                    //check user level
                                                    switch ($user_lvl) {
                                                        case 1:
                                                            $arrayProv = array(
                                                                '1' => 'National',
                                                                '2' => 'Province',
                                                                '3' => 'District',
                                                                '4' => 'Field/Tehsil/Town',
                                                                '7' => 'Health Facility',
                                                                '8' => 'Individuals'
                                                            );
                                                            break;
                                                        case 2:
                                                            if($_SESSION['user_stakeholder1'] == 145){
                                                                $arrayProv = array(
                                                                    '1' => 'Central',
                                                                    '2' => 'Province',
                                                                    // '3' => 'Division',
                                                                    '3' => 'District',
                                                                    '7' => 'Health Facility',
                                                                    '8' => 'Individuals'
                                                                    );
                                                            }
                                                            elseif($_SESSION['user_stakeholder1'] == 2){
                                                                $arrayProv = array(
                                                                    //'1' => 'Central',
                                                                    //'2' => 'Province',
                                                                    // '3' => 'Division',
                                                                    '3' => 'District',
                                                                    '7' => 'Health Facility',
                                                                    //'8' => 'Individuals'
                                                                    );
                                                            }
                                                            else{
                                                                $arrayProv = array(
                                                                    '1' => 'Central',
                                                                    '2' => 'Province',
                                                                    // '3' => 'Division',
                                                                    '3' => 'District',
                                                                    '7' => 'Health Facility',
                                                                    //'8' => 'Individuals'
                                                                    );
                                                            }

                                                            break;
                                                        case 3:
                                                            $arrayProv = array(
                                                                //'1' => 'Central',
                                                                //  '3' => 'Division',
                                                                '3' => 'District',
                                                                '7' => 'Health Facility',
                                                            );
                                                            break;
                                                        case 4:
                                                            $arrayProv = array(
                                                                '3' => 'District',
                                                                '4' => 'Tehsil/Town',
                                                                '7' => 'Health Facility'
                                                            );
                                                            break;


                                                        default:
                                                            $arrayProv = array(
                                                                '1' => 'Central',
                                                                '2' => 'Province',
                                                                //   '3' => 'Division',
                                                                '3' => 'District'
                                                                    //  '6' => 'Union Council'
                                                            );
                                                            break;
                                                    }
                                                    ?>
                                                    <style>
                                                        .input-small{width:140px !important;}
                                                    </style>
                                                    <div class="col-md-12">
                                                        <div class="col-md-2">
                                                            
                                                                <label class="control-label">
                                                                    Stakeholder
                                                                </label>
                                                                <div class="controls">
                                                                    <div class="input-group">
                                                                        <span class="input-group-addon     bg-blue-madison">
                                                                            <i class="fa fa-building-o"></i>
                                                                        </span>

                                                                
                                                                <select name="mainstkid" id="mainstkid" class="form-control input small">
                                                                    <option value="">Select</option>
                                                                    <?php
                                                                    $getMainStakeholder = 'SELECT DISTINCT
                                                                                                    stakeholder.stkid,
                                                                                                    stakeholder.stkname
                                                                                            FROM
                                                                                                    stakeholder
                                                                                            INNER JOIN tbl_warehouse ON stakeholder.stkid = tbl_warehouse.stkid
                                                                                            WHERE
                                                                                                    stakeholder.ParentID IS NULL
                                                                                            AND stakeholder.stk_type_id IN (0, 1, 4)
                                                                                            '.$stk_where.'
                                                                                            ORDER BY
                                                                                                    stakeholder.stkorder ASC';
                                                                    //result
                                                                    $resMainStk = mysql_query($getMainStakeholder) or die('Error MainStakeholder');
                                                                    //fetch result
                                                                    while ($arryStk = mysql_fetch_assoc($resMainStk)) {
                                                                        $sel = '';
                                                    //                    if ($_SESSION['lastTransStk'] == $arryStk['stkid']) {
                                                    //                        $sel = 'selected="selected"';
                                                    //                    }
                                                                        if($_SESSION['user_stakeholder']==$arryStk['stkid'])
                                                                        {
                                                                        $sel = 'selected="selected"';   
                                                                        }
                                                                        //populate mainstkid combo
                                                                        ?>
                                                                        <option value="<?php echo $arryStk['stkid']; ?>" <?php echo $sel; ?>><?php echo $arryStk['stkname']; ?></option>
                                                                    <?php } ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        </div>
                                                        <div class="col-md-2" style="display:none;" id="office-span">
                                                            <label class="control-label">
                                                                Office
                                                            </label>
                                                            <div class="controls">
                                                                <div class="input-group">
                                                                    <span class="input-group-addon     bg-blue-madison">
                                                                        <i class="fa fa-users"></i>
                                                                    </span>

                                                                <select name="office" id="office" class="form-control small">
                                                                    <option value="">Select</option>
                                                                    <?php
                                                                    //fetch result
                                                                    foreach ($arrayProv as $key => $value) {
                                                                        $sel = '';
                                                                        if($_SESSION['user_level']==2){
                                                                            if($key==3){
                                                                            $sel = 'selected="selected"';
                                                                        }
                                                                        }
                                                                        else{
                                                                        if ($_SESSION['lastTransOffice'] == $key) {
                                                                            $sel = 'selected="selected"';
                                                                        } 
                                                                        }
                                                                        //populate office combo
                                                                        ?>
                                                                        <option value="<?php echo $key; ?>" <?php echo $sel; ?>><?php echo $value; ?></option>
                                                                    <?php } ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        </div>
                                                        <div class="col-md-2" id="div_combo1" <?php if (empty($prov_id) || isset($office_id) == 1 || empty($office_id)) { ?> style="display:none;" <?php } else { ?> style="display:block;"<?php } ?>>
                                                           
                                                            
                                                            <label class="control-label" id="lblcombo1">Province1 <span class="red">*</span></label>
                                                            <div class="controls">
                                                                <div class="input-group">
                                                                    <span class="input-group-addon     bg-blue-madison">
                                                                        <i class="fa fa-globe"></i>
                                                                    </span>
                                                                <select name="combo1" id="combo1" class="form-control input small">
                                                                    <option value="">Select</option>
                                                                    <?php 
                                                                    //fetch result
                                                                    while ($row = mysql_fetch_object($arrayProvince)) {
                                                                        //populate combo1 combo
                                                                        ?>
                                                                        <option value="<?php echo $row->PkLocID; ?>" <?php if ($lastTransProv == $row->PkLocID) { ?>selected="selected"<?php } ?>>
                                                                            <?php echo $row->LocName; ?></option>
                                                                    <?php }
                                                                    ?>
                                                                </select>
                                                            </div>
                                                            </div>
                                                        </div>	
                                                        <div class="col-md-2" id="div_combo2" <?php if (empty($dist_id) || isset($office_id) == 1 || empty($office_id)) { ?> style="display:none;" <?php } ?>>
                                                            <label class="control-label" id="lblcombo2">District <span class="red">*</span></label>
                                                            <div class="controls">
                                                                <div class="input-group">
                                                                    <span class="input-group-addon     bg-blue-madison">
                                                                        <i class="fa fa-map-marker"></i>
                                                                    </span>
                                                                <select name="combo2" id="combo2" class="form-control input small">
                                                                    <option value="">Select</option>
                                                                    <?php
                                                                    //fetch result
                                                                    while ($row = mysql_fetch_object($arrayDistricts)) {
                                                                        //populate combo2 combo
                                                                        ?>
                                                                        <option value="<?php echo $row->PkLocID; ?>" <?php if ($dist_id == $row->PkLocID) { ?>selected=""<?php } ?>>
                                                                            <?php echo $row->LocName; ?></option>
                                                                    <?php }
                                                                    ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        </div>
                                                       
                                                        <div class="col-md-3" id="wh_combo" <?php if (empty($warehouse)) { ?> style="display:none;" <?php } ?>>
                                                            <label class="control-label" id="wh_l"> Store <span class="red">*</span></label>
                                                            <div class="controls">
                                                                <select name="warehouse" id="warehouse" class="input-large select2me">
                                                                    <option value="">Select</option>
                                                                    <?php
                                                                    //fetch result
                                                                    while ($row = mysql_fetch_object($arrayWarehouse)) {
                                                                        //populate warehouse combo
                                                                        ?>
                                                                        <option value="<?php echo $row->wh_id; ?>" <?php if ($warehouse == $row->wh_id) { ?>selected=""<?php } ?>>
                                                                            <?php echo $row->wh_name; ?></option>
                                                                    <?php }
                                                                    ?>
                                                                </select>
                                                                <div class="help-block" id="store-help-block" style="font-size:10px;"><i class="glyphicon glyphicon-info-sign" style="font-size:10px;"></i> Store name &#8212; Stakeholder</div>
                                                            </div>
                                                        </div>
                                                        <div class="span1" id="loader" style="display:none;"><img src="<?php echo SITE_URL; ?>plmis_img/loader.gif" style="margin-top:8px; float:left" id="loader" alt="" /></div>
                                                    </div>
                                                    
                                                    <input type="hidden" name="user_level" id="user_level" value="<?php echo $user_lvl; ?>" />
                                                    
                                    </div>
                                    <div class="row">
                                                    <?php
                                               //-------code section originally was in levelcombos_all_level file, now shifted here ---END----
                                            
                                            ?>
                                            <?php
                                        } else {
                                            ?>
                                            <div class="col-md-12">
                                                <div class="col-md-12">
                                                    <div class="control-group">
                                                        <div class="controls">
                                                        <div class="input-group" style="padding-top:10px !important;">
                                                            <span  class="input-group-addon input-circle-left  bg-blue-madison">
                                                                <i class="fa fa-hospital-o"></i>
                                                            </span>
                                                            <input class="form-control input-large" id="recipient" name="" type="text" disabled="" value="<?php echo $wh_name; ?>"/>
                                                            <input class="form-control input-medium" id="warehouse" name="warehouse" type="hidden" value="<?php echo $whouse_id; ?>"/>
                                                        </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php }
                                        ?>
                                                    
                                        
                                        <div class="col-md-12">
                                        <div class="col-md-6"> 
                                                <!-- Group Receive No-->
                                                <div class="control-group">
                                                    <label class="control-label" for="item_code">Scan Barcode <span class="red">*</span> <i style="padding-left:20px; transform: scale(4,1); " class="fa fa-barcode font-blue-chambray"></i></label>
                                                    <div class="controls">
                                                        <div class="input-group">
                                                        <span style="min-width:70px" class="input-group-addon input-circle-left bg-blue-madison">
                                                            <i style="padding-left:1px; transform: scale(3,1); " class="fa fa-barcode"></i>
                                                        </span>
                                                        <input class="form-control input-circle-right" tabindex="1" id="item_code" name="item_code" type="text" />
                                                    </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="control-group">
                                                    <label class="control-label" for="receive_no">Quantity </label>
                                                    <div class="controls"> 
                                                        <div class="input-group">
                                                            <span  class="input-group-addon input-circle-left  bg-blue-madison">
                                                                <i class="fa fa-sort-numeric-asc"></i>
                                                            </span>
                                                            <input type="text" class="form-control input-medium num" name="qty" id="qty" autocomplete="off" required/>
                                                    <span id="product-unit"></span> <span id="product-unit1" style="display:none;"></span> </div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-3 right" style="margin-top:35px;">
                                                <div class="controls right">
                                                    <button type="submit" class="btn default" id="add_issue" style="display:none;"> Save Entry </button>
                                                    <button type="reset" class="btn btn-info hide"> Reset </button>
                                                    <input type="hidden" name="trans_no" id="trans_no" value="<?php echo $TranNo; ?>"/>
                                                    <input type="hidden" name="stock_id" id="stock_id" value="<?php echo $stock_id; ?>"/>
                                                    <input type="hidden" name="prov_id" id="prov_id" value="<?php echo $_SESSION['user_province']; ?>"/>
                                                    <input type="hidden" name="dist_id" id="prov_id" value="<?php echo isset($_SESSION['dist_id']) ? $_SESSION['dist_id'] : ''; ?>"/>
                                                </div>
                                            </div>
                                        </div>   
                                    </div>
                                     <div class="well margin-top-10 no-margin no-border" style="height:110px;">
                                        <div class="row">           
                                            <div class="col-md-12">
                                                    

                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label class="form-label" ><h4 class="font-green-seagreen ">Product:</h4></label>
                                                        <div class="controls" id="product_div">---</div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label class="form-label"><h4 class="font-green-seagreen ">Manufactured By:</h4></label>
                                                        <div class="controls" id="manuf_div">---</div>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label for="text" style="float:left;margin-right:5px;"><h4 class="font-green-seagreen ">Batch:</h4></label>
                                                        <div class="controls" id="batch_div">---</div>
                                                    </div>
                                               
                                                    <div class="form-group">
                                                        <label for="text" style="float:left;margin-right:5px;"><h4 class="font-green-seagreen ">Available:</h4></label>
                                                        <div class="controls" id="qty_available_div">---</div>
                                                        <input type="hidden" value="" id="ava_qty" name="ava_qty" class="form-control input-medium">
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="text" style="float:left;margin-right:5px;"><h4 class="font-green-seagreen ">Funded By:</h4></label>

                                                            <span id="funding_source_lbl" style="">---</span>
                                                            <input type="hidden" id="funding_source" name="funding_source" >
                                                            <?php if (!empty($funding_source) && !empty($TranNo)) { ?>
                                                                <input type="hidden" name="funding_source" id="funding_source" value="<?php echo $funding_source; ?>" />
                                                            <?php } ?>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="text" style="float:left;margin-right:5px;"><h4 class="font-green-seagreen ">Expiry:</h4></label>
                                                        <div class="" id="expiry_div">---</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row hide"> 
                                        <div class="col-md-12">
                                            <div class="col-md-3">
                                                <label class="control-label" for="firstname"> Comments (Max 300 Char) </label>
                                                <textarea name="comments" id="comments" maxlength="300" style="resize:none;" class="form-control input-medium"><?php echo $ReceivedRemarks; ?></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    
                                     
                                </form>
                             
                        <!-- // Row END -->
                        <?php if ($tempstocks != FALSE) { ?>
                            <!-- Widget -->
                             
                                    <!-- Table -->
                                    <!-- Table -->
                                    <table class="table table-striped table-bordered table-condensed" id="myTable">
                                        <!-- Table heading -->
                                        <thead>
                                            <tr class="bg-blue-hoki" style="color:#FFF;">
                                                <th>Date</th>
                                                <th>Product</th>
                                                <th>Unit</th>
                                                <th>Issue To</th>
                                                <th class="span2">Quantity</th>
                                                <th>Cartons</th>
                                                <th class="span2">Batch</th>
                                                <th>Expiry Date</th>
                                                <th class="center" width="50">Action</th>
                                            </tr>
                                        </thead>
                                        <!-- // Table heading END -->
                                        <!-- Table body -->
                                        <tbody>
                                            <!-- Table row -->
                                            <?php
                                            $i = 1;
                                            $checksumVials = array();
                                            //fetch result
                                            while ($row = mysql_fetch_object($tempstocks)) {
                                                ?>
                                                <tr class="gradeX">
                                                    <td><?php echo date("d/m/y", strtotime($row->TranDate)); ?></td>
                                                    <?php $_SESSION['trans_date'] = $row->TranDate; ?>
                                                    <td><?php echo $row->itm_name; ?></td>
                                                    <td><?php echo $row->UnitType; ?></td>
                                                    <td><?php echo $row->wh_name; ?></td>
                                                    <td class="editableSingle Qty id<?php echo $row->PkDetailID; ?> right"><?php echo number_format(abs($row->Qty)); ?></td>
                                                    <td class="Qty id<?php echo $row->PkDetailID; ?> right"><?php echo number_format((abs($row->Qty) / $row->qty_carton), 2); ?></td>
                                                    <td class="Batch id<?php echo $row->PkDetailID; ?>"><!--editableSingle --> 
                                                        <?php echo $row->batch_no; ?></td>
                                                    <td><?php echo date("d/m/y", strtotime($row->batch_expiry)); ?></td>
                                                    <td class="center"><span data-toggle="notyfy" id="<?php echo $row->PkDetailID; ?>" data-type="confirm" data-layout="top"><img class="cursor" src="<?php echo PUBLIC_URL; ?>images/cross.gif" /></span></td>
                                                </tr>
                                                <?php
                                                $i++;
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                    
                                    
                                    
                                    <div class=" right">
                                        <form name="receive_stock" id="receive_stock" action="bar_issue_action.php" method="POST">
                                            <button type="submit" class="btn btn-success" onClick="return confirm('Are you sure you want to issue this stock?');"> Save Voucher </button>
                                            <button id="print_issue" type="button" class="btn btn-warning"> Print </button>
                                            <input type="hidden" name="stockid" id="stockid" value="<?php echo $stock_id; ?>"/>
                                            <input type="hidden" name="whTo" id="whTo" value="<?php echo $whTo; ?>"/>
                                        </form>
                                    </div>
                                    
                                <!--Panel Ends Here-->
                                </div>
                            </div>
                            <?php
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- // Content END -->
    <?php
//include footer
    include PUBLIC_PATH . "/html/footer.php";
    ?>

    <script src="<?php echo PUBLIC_URL; ?>js/dataentry/jquery.mask.min.js"></script> 
    <script src="<?php echo PUBLIC_URL; ?>js/jquery.validate.js"></script> 
    <script src="<?php echo PUBLIC_URL; ?>js/jquery.inlineEdit.js"></script> 
    <script src="<?php echo PUBLIC_URL; ?>js/dataentry/newissue.js"></script> 
    <script src="<?php echo PUBLIC_URL; ?>js/barcode/barcode_splitter_issue.js"></script> 

    <script type="text/javascript" src="../../public/assets/global/plugins/select2/select2.min.js"></script>
    <script>
            $(function () {
                $(document).keypress(function (e) {
                    if (e.which == 13) {
                        $('#add_receive').trigger('click');
                    }
                });
                $("#item_code").focus();
                $("#item_code").change(function () {
                    string_splitter($(this).val());
                });
                
                
  
            });

    </script>
    <script >
        $('#issue_date').datepicker({
            dateFormat: "dd/mm/yy",
            constrainInput: false,
            maxDate: 0,
            minDate: "-1Y",
            //minDate: "<?= date('d/m/y', strtotime($_SESSION['im_start_month'])) ?>",
            changeMonth: true
        });
    </script>
    <script src="<?php echo PUBLIC_URL; ?>js/barcode/bar_combos.js"></script>
        <?php
    
    if (!empty($_SESSION['success'])) {
        if ($_SESSION['success'] == 1) {
            $text = 'Data has been saved successfully';
        }
        if ($_SESSION['success'] == 2) {
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
    <!-- // Content END -->
</div>
</body>
</html>