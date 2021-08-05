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
if ($strDo == "Delete") {
    //Check if Sub-offices exists
    $checkStk = "SELECT
					COUNT(*) AS num
				FROM
					stakeholder
				WHERE
					stakeholder.stkid != $nstkId
				AND stakeholder.MainStakeholder = $nstkId";
    $stkQryRes = mysql_fetch_array(mysql_query($checkStk));

    if ($stkQryRes['num'] == 0) {
        $objstk->m_npkId = $nstkId;
        //Delete Stakeholder
        //$rsEditCat = $objstk->DeleteStakeholder();

        $_SESSION['err']['text'] = 'Deletion is disabled.';
        $_SESSION['err']['type'] = 'error';
    } else {
        $_SESSION['err']['text'] = "Stakeholder can not be deleted. Please delete stakeholder offices first.";
        $_SESSION['err']['type'] = 'error';
    }
    //redirecting to ManageSuppliers
    echo '<script>window.location="ManageSuppliers.php"</script>';
    exit;
}
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
        $stktype = $RowEditStk->stk_type_id;
        //stkorder
        $stkorder = $RowEditStk->stkorder;
        //lvl_id
        $lvl_id = $RowEditStk->lvl;
    }
}
//Get All Stakeholders
$rsStakeholders = $objstk->GetAllStakeholders();
//GetAllstk types
$rsstktype = $objstkType->GetAllstk_types();
//Get Ranks
$rsranks = $objstk->GetRanks();
//Get All levels
$rslvl = $objlvl->GetAlllevels();
//include file




$query_xmlw = "SELECT
                        stakeholder.stkid,
                        stakeholder.stkname,
                        stakeholder.stkorder,
                        stakeholder_type.stk_type_descr,
                        IF(ifnull(parentstk.stkname,stakeholder.stkname) ='', parentstk.stkname, ifnull(parentstk.stkname,stakeholder.stkname) ) AS Parent,
                        stakeholder.ParentID,
                        stakeholder.stk_type_id,
                        stakeholder.lvl,
                        tbl_dist_levels.lvl_name,
                        stakeholder.MainStakeholder
                    FROM
                        stakeholder
                    Left Join stakeholder AS parentstk ON stakeholder.ParentID = parentstk.stkid
                    Inner Join stakeholder_type ON stakeholder.stk_type_id = stakeholder_type.stk_type_id
                    Left Join tbl_dist_levels ON stakeholder.lvl = tbl_dist_levels.lvl_id
                    where 
                        stakeholder.ParentID is null
                        AND stakeholder.stk_type_id = 2
                    ORDER BY 
                        stakeholder.stkid";
//query result
$result_xmlw = mysql_query($query_xmlw);

//Generating xml for grid
$xmlstore = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
$xmlstore .="<rows>";
$counter = 1;
//populate xml
while ($row_xmlw = mysql_fetch_array($result_xmlw)) {
    $temp = "\"$row_xmlw[stkid]\"";
    $xmlstore .="<row>";
    $xmlstore .="<cell>" . $counter++ . "</cell>";
    $xmlstore .="<cell><![CDATA[" . $row_xmlw['stkname'] . "]]></cell>";
    $xmlstore .="<cell>" . $row_xmlw['stk_type_descr'] . "</cell>";
    $xmlstore .="<cell>" . $row_xmlw['lvl_name'] . "</cell>";
    $xmlstore .="<cell type=\"img\">" . PUBLIC_URL . "dhtmlxGrid/dhtmlxGrid/codebase/imgs/edit.gif^" . PUBLIC_URL . "dhtmlxGrid/dhtmlxGrid/codebase/imgs/edit.gif^javascript:editFunction($temp)^_self</cell>";
    $xmlstore .="<cell  > </cell>";
    $xmlstore .="</row>";
}
$xmlstore .="</rows>";
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
                                <form method="post" action="ManageSuppliers_prov_action.php" id="Managestakeholdersaction">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="col-md-3">
                                                <div class="control-group">
                                                    <label>Jurisdiction<font color="#FF0000">*</font></label>
                                                    <div class="controls">
                                                        <select name="lstLvl" id="lstLvl" class="form-control input-medium" readonly>
                                                            <option value="1">National</option>
                                                        </select>
                                                        <span class="help-block">(National / Provincial / District /Field)</span> </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="control-group">
                                                    <label>Type<font color="#FF0000">*</font></label>
                                                    <div class="controls">
                                                        <select name="lstStktype" class="form-control input-medium" readonly>
                                                            <option value="2">Funding Source</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="control-group">
                                                    <label>Funding Source Name<font color="#FF0000">*</font></label>
                                                    <div class="controls">
                                                        <input name="txtStkName" value="<?= $stkname ?>" class="form-control input-medium" autocomplete="off" />
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
                                                        <input type="hidden" name="redirect_to" value="ManageSuppliers" />
                                                        <input type="hidden" name="hdnstkId" value="<?= $nstkId ?>" />
                                                        <input type="hidden" name="hdnToDo" value="<?= $strDo ?>" />
                                                        <input type="submit" value="<?= $strDo ?>" class="btn btn-primary" />
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
            window.location = "ManageSuppliers.php?Do=Edit&Id=" + val;
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
            mygrid.setHeader("<span title='Serial Number'>Sr. No.</span>,<span title='Supplier Name'>Funding Source</span>,<span title='Stakeholder Level'>Level</span>,<span title='Type'>Type</span>,<span title='Use this column to perform the desired operation'>Actions</span>,#cspan");
            mygrid.attachHeader(",#text_filter,#select_filter,#select_filter,,");
            mygrid.setInitWidths("60,*,150,150,30,30");
            mygrid.setColAlign("center,left,left,left,center,center")
            mygrid.setColSorting(",str,,,,");
            mygrid.setColTypes("ro,ro,ro,ro,img,img");
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