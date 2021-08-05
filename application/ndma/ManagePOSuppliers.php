<?php
include("../includes/classes/AllClasses.php");
include(PUBLIC_PATH . "html/header.php");

$act = 2;
//srtDo
$strDo = "Add";
//nstkId
$nstkId = 0;
//staname
$stkname = "";
//stkgroupid
$stkgroupid = 0;
//stkNewGroupName
$strNewGroupName = "";
//stktype
$stktype = 0;
//stkorder
$stkorder = 0;
//newRank
$newRank = 0;
//lvl_id
$lvl_id = 0;
$country = '130'; //pakistan
//register globals
if (!ini_get('register_globals')) {
    $superglobals = array($_GET, $_POST, $_COOKIE, $_SERVER);
    if (isset($_SESSION)) {
        array_unshift($superglobals, $_SESSION);
    }
    foreach ($superglobals as $superglobal) {
        extract($superglobal, EXTR_SKIP);
    }
    ini_set('register_globals', true);
}


if (isset($_REQUEST['Do']) && !empty($_REQUEST['Do'])) {
    //getting Do
    $strDo = $_REQUEST['Do'];
}

if (isset($_REQUEST['Id']) && !empty($_REQUEST['Id'])) {
    //getting Id
    $nstkId = $_REQUEST['Id'];
}

/**
 * 
 * Delete Manage Stakeholders
 * 
 */
/**
 * 
 * Edit
 * 
 */
if ($strDo == "Edit") {
    $objstk->m_npkId = $nstkId;
    //Get Stakeholders By Id
    $rsEditstk = $objstk->GetStakeholdersById();
    //getting results
    if ($rsEditstk != FALSE && mysql_num_rows($rsEditstk) > 0) {
        $RowEditStk = mysql_fetch_object($rsEditstk);
        //stkname
        $stkname = $RowEditStk->stkname;
        //pk_id
        $_SESSION['pk_id'] = $nstkId;
        //stktype 
        $ntn = $RowEditStk->ntn;
        $gstn = $RowEditStk->gstn;
        $contact_person = $RowEditStk->contact_person;
        $contact_number = $RowEditStk->contact_numbers;
        $contact_email = $RowEditStk->contact_emails;
        $address = $RowEditStk->contact_address;
        $country = $RowEditStk->origin_country;
        $status = $RowEditStk->company_status;
        $status_bw = $RowEditStk->b_w_status;
    }
}
//include file
$strSql = " SELECT
countries.id,
countries.`name`,
countries.`status`
FROM
countries
WHERE
countries.`status` = 1
ORDER BY
countries.`name` ASC
";

//echo $strSql;
$countries = mysql_query($strSql);



$query_xmlw = "SELECT
stakeholder.stkid,
stakeholder.stkname,
stakeholder.contact_person,
stakeholder.contact_numbers,
stakeholder.contact_emails,
stakeholder.contact_address,
stakeholder.company_status,
stakeholder.b_w_status,
stakeholder.ntn,
stakeholder.gstn,
countries.`name`
FROM
stakeholder
left JOIN countries ON stakeholder.origin_country = countries.id
WHERE
stakeholder.stk_type_id = 6 AND
stakeholder.relevant_stk =" . $_SESSION['user_stakeholder'] . "
ORDER BY
	stakeholder.stkid";
//query result
$result_xmlw = mysql_query($query_xmlw);

//Generating xml for grid
$xmlstore = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
$xmlstore .= "<rows>";
$counter = 1;
//populate xml
while ($row_xmlw = mysql_fetch_array($result_xmlw)) {
    $temp = "\"$row_xmlw[stkid]\"";
    $xmlstore .= "<row>";
    $xmlstore .= "<cell>" . $counter++ . "</cell>";
    $xmlstore .= "<cell><![CDATA[" . $row_xmlw['stkname'] . "]]></cell>";
    $xmlstore .= "<cell>" . $row_xmlw['contact_person'] . "</cell>";
    $xmlstore .= "<cell>" . $row_xmlw['contact_numbers'] . "</cell>";
    $xmlstore .= "<cell>" . $row_xmlw['contact_emails'] . "</cell>";
    $xmlstore .= "<cell>" . $row_xmlw['contact_address'] . "</cell>";
    if($row_xmlw['company_status']=='active'){
    $xmlstore .= "<cell>Active</cell>";
    }
    else if($row_xmlw['company_status']=='inactive'){
        $xmlstore .= "<cell>Inactive</cell>";
    }
    if($row_xmlw['b_w_status']=='white'){
    $xmlstore .= "<cell>Whitelisted</cell>";
    }
    elseif($row_xmlw['b_w_status']=='black'){
        $xmlstore .= "<cell>Blacklisted</cell>";
    }
    $xmlstore .= "<cell>" . $row_xmlw['ntn'] . "</cell>";
    $xmlstore .= "<cell>" . $row_xmlw['gstn'] . "</cell>";
    $xmlstore .= "<cell>" . $row_xmlw['name'] . "</cell>";
    $xmlstore .= "<cell type=\"img\">" . PUBLIC_URL . "dhtmlxGrid/dhtmlxGrid/codebase/imgs/edit.gif^" . PUBLIC_URL . "dhtmlxGrid/dhtmlxGrid/codebase/imgs/edit.gif^javascript:editFunction($temp)^_self</cell>";
    $xmlstore .= "</row>";
}
$xmlstore .= "</rows>";
?>
</head>
<!-- BEGIN BODY -->
<body class="page-header-fixed page-quick-sidebar-over-content" onLoad="doInitGrid()">
    <!-- BEGIN HEADER -->
    <div class="page-container">
        <?php include $_SESSION['menu']; ?>
        <?php include PUBLIC_PATH . "html/top_im.php"; ?>
        <div class="page-content-wrapper">
            <div class="page-content">
                <div class="row">
                    <div class="col-md-12">
                        <h3 class="page-title row-br-b-wp">Funding Source Management</h3>
                        <div class="widget" data-toggle="collapse-widget">
                            <div class="widget-head">
                                <h3 class="heading"><?php echo $strDo; ?> Funding Source</h3>
                            </div>
                            <div class="widget-body">
                                <form method="post" action="ManagePOSuppliers_action.php" id="Managestakeholdersaction">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="col-md-3">
                                                <div class="control-group">
                                                    <label>Supplier Name<font color="#FF0000">*</font></label>
                                                    <div class="controls">
                                                        <input required class="form-control" maxlength="250" type="text" id="new_supplier" name="new_supplier" value="<?php  if(isset($stkname)) echo $stkname;?>"/>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="control-group">
                                                    <label class="control-label">NTN</label>
                                                    <div class="controls">
                                                        <input  class="form-control" maxlength="250"  type="text" id="ntn" name="ntn" value="<?php  if(isset($ntn)) echo $ntn;?>"/>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="control-group">
                                                    <label class="control-label">GSTN</label>
                                                    <div class="controls">
                                                        <input  class="form-control" maxlength="250"  type="text" id="gstn" name="gstn" value="<?php  if(isset($gstn)) echo $gstn;?>"/>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="control-label" for="country"> Country <span class="red">*</span> </label>
                                                <div class="controls">
                                                    <select name="country" id="country" class="form-control dis-field">
                                                        <?php
//check if result exists
                                                        if (mysql_num_rows($countries) > 0) {
                                                            //fetch result
                                                            while ($row = mysql_fetch_object($countries)) {
                                                                ?>
                                                                <option value="<?php echo $row->id; ?>" <?php if ($country == $row->id) { ?> selected="" <?php } ?>> <?php echo $row->name; ?> </option>
                                                                <?php
                                                            }
                                                        }
                                                        ?></select>
                                                </div>
                                            </div>


                                            <div class="col-md-3">
                                                <label class="control-label">Contact Person</label>
                                                <div class="controls">
                                                    <input class="form-control input-sm  " maxlength="250" type="text" id="contact_person" name="contact_person" value="<?php  if(isset($contact_person)) echo $contact_person;?>"/>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="control-label">Contact Number</label>
                                                <div class="controls">
                                                    <input class="form-control input-sm  " maxlength="250" type="text"  id="contact_number" name="contact_number" value="<?php  if(isset($contact_number)) echo $contact_number;?>"/>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="control-label">Contact Email</label>
                                                <div class="controls">
                                                    <input class="form-control input-sm  " maxlength="250" type="text"  id="contact_email" name="contact_email" value="<?php  if(isset($contact_email)) echo $contact_email;?>"/>
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <label class="control-label">Active/InActive Status</label>
                                                <div class="controls">
                                                    <select class="form-control input-sm  " maxlength="250" type="text"  id="status" name="status">
                                                        <option value="active" <?php if(isset($status)&&$status=='active') echo 'selected="selected"';?>>Active</option>
                                                        <option value="inactive" <?php if(isset($status)&&$status=='inactive') echo 'selected="selected"';?>>InActive</option>
                                                    </select>
                                                </div>

                                            </div>

                                            <div class="col-md-3">
                                                <label class="control-label">Blacklisted/Whitelisted Status</label>
                                                <div class="controls">
                                                    <select class="form-control input-sm  " maxlength="250" type="text"  id="status_bw" name="status_bw">
                                                        <option value="white" <?php if(isset($status_bw)&&$status_bw=='white') echo 'selected="selected"';?>>Whitelisted</option>
                                                        <option value="black" <?php if(isset($status_bw)&&$status_bw=='black') echo 'selected="selected"';?>>Blacklisted</option>
                                                    </select>
                                                </div> 
                                            </div>
                                            <div class="col-md-6">
                                                <label class="control-label">Address</label>
                                                <div class="controls">
                                                    <textarea class="form-control" type="text" id="company_address" name="company_address"  ><?php  if(isset($address)) echo $address;?></textarea>
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
                                                            <input type="hidden" name="hdnToDo" value="<?= $strDo ?>" />
                                                            <input type="submit" value="<?= $strDo ?>" class="btn btn-primary" />
                                                            <input name="btnAdd" type="button" id="btnCancel" class="btn btn-info" value="Cancel" OnClick="window.location = '<?= $_SERVER["PHP_SELF"]; ?>';">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div> 
                                </form>     
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="widget">
                                <div class="widget-head">
                                    <?php
                                    //display All Stakeholders
                                    ?>
                                    <h3 class="heading">All Funding Sources</h3>
                                </div>
                                <div class="widget-body">
                                    <table width="100%" cellpadding="0" cellspacing="0" align="center">
                                        <tr>
                                            <td style="text-align:right;">
                                                <img style="cursor:pointer;" src="<?php echo PUBLIC_URL; ?>images/pdf-32.png" onClick="mygrid.setColumnHidden(4, true);
                                                        mygrid.setColumnHidden(5, true);
                                                        mygrid.toPDF('<?php echo PUBLIC_URL; ?>dhtmlxGrid/dhtmlxGrid/grid2pdf/server/generate.php');
                                                        mygrid.setColumnHidden(4, false);
                                                        mygrid.setColumnHidden(5, false);" />
                                                <img style="cursor:pointer;" src="<?php echo PUBLIC_URL; ?>images/excel-32.png" onClick="mygrid.setColumnHidden(4, true);
                                                        mygrid.setColumnHidden(5, true);
                                                        mygrid.toExcel('<?php echo PUBLIC_URL; ?>dhtmlxGrid/dhtmlxGrid/grid2excel/server/generate.php');
                                                        mygrid.setColumnHidden(4, false);
                                                        mygrid.setColumnHidden(5, false);" />
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><div id="mygrid_container" style="width:100%; height:350px; background-color:white;overflow:hidden"></div></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php include PUBLIC_PATH . "/html/footer.php"; ?>
        <?php include PUBLIC_PATH . "/html/reports_includes.php"; ?>
        <script type="text/javascript">
            function editFunction(val) {
                window.location = "ManagePOSuppliers.php?Do=Edit&Id=" + val;
            }
            function delFunction(val) {
                if (confirm("Are you sure you want to delete the record?")) {
                    window.location = "ManageSuppliers.php?Do=Delete&Id=" + val;
                }
            }
            var mygrid;
            //Initializing grid
            function doInitGrid() {
                mygrid = new dhtmlXGridObject('mygrid_container');
                mygrid.selMultiRows = true;
                mygrid.setImagePath("<?php echo PUBLIC_URL; ?>dhtmlxGrid/dhtmlxGrid/codebase/imgs/");
                mygrid.setHeader("<span title='Serial Number'>Sr. No.</span>,<span title='Supplier Name'>Supplier Name</span>,<span title='person'>Contact Person</span>,<span title='number'>Contact No.</span>,<span title='email'>Email</span>,<span title='address'>Address</span>,<span title='status'>Status</span>,<span title='bw'>Black/White listed</span>,<span title='ntn'>NTN</span>,<span title='gstn'>GSTN</span>,<span title='country'>Country</span>,<span title='action'>Action</span>");
                mygrid.attachHeader(",#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,");
                mygrid.setInitWidths("60,*,100,100,100,150,150,80,60,60,60,60");
                mygrid.setColAlign("center,left,left,left,left,left,left,left,left,left,left,center");
                mygrid.setColSorting(",str,,,,,,,,,,,");
                mygrid.setColTypes("ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,img");
                //mygrid.enableLightMouseNavigation(true);
                mygrid.enableRowsHover(true, 'onMouseOver');
                mygrid.setSkin("light");
                mygrid.init();
                mygrid.clearAll();
                mygrid.loadXMLString('<?php echo $xmlstore; ?>');
            }
        </script>
        <?php
        if (isset($_SESSION['err'])) {
            ?>
            <script>
                var self = $('[data-toggle="notyfy"]');
                notyfy({
                    force: true,
                    text: '<?php echo $_SESSION['err']['text']; ?>',
                    type: '<?php echo $_SESSION['err']['type']; ?>',
                    layout: self.data('layout')
                });
            </script>
            <?php
            //Unsetting session
            unset($_SESSION['err']);
        }
        ?>
</body>
</html>