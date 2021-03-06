<?php
/**
 * Manage Stakeholders
 * @package Admin
 * 
 * @author     Muhammad Waqas Azeem 
 * @email <waqas@deliver-pk.org>
 * 
 * @version    2.2
 * 
 */
//including files
include("../includes/classes/AllClasses.php");
include(PUBLIC_PATH . "html/header.php");

//initializing variables
//act
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
    $nbId = $_REQUEST['Id'];
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
if ($strDo == "Update") {
    $query_stk = "SELECT DISTINCT
	stakeholder_item.stk_id,
	stakeholder.stkname
FROM
	stock_batch
LEFT JOIN stakeholder_item ON stock_batch.manufacturer = stakeholder_item.stk_id
LEFT JOIN itminfo_tab ON stock_batch.item_id = itminfo_tab.itm_id
LEFT JOIN stakeholder ON stakeholder_item.stkid = stakeholder.stkid
WHERE
	itminfo_tab.itm_id IN (
		SELECT
			stock_batch.item_id
		FROM
			stock_batch
		WHERE
			stock_batch.batch_id = $nbId
	) AND stakeholder_item.stk_id IS NOT NULL";
    //query result
    $result_stk = mysql_query($query_stk);
    
    $query_stk1 = "SELECT DISTINCT
	itminfo_tab.itm_name,
	stock_batch.batch_no,
	stakeholder_item.stk_id
FROM
	stock_batch
LEFT JOIN stakeholder_item ON stock_batch.manufacturer = stakeholder_item.stk_id
LEFT JOIN itminfo_tab ON stock_batch.item_id = itminfo_tab.itm_id
LEFT JOIN stakeholder ON stakeholder_item.stkid = stakeholder.stkid
WHERE
	stock_batch.batch_id = $nbId";
    //query result
    $result_stk1 = mysql_query($query_stk1);
     while ($result_row = mysql_fetch_array($result_stk1)) {
         $product_name = $result_row['itm_name'];
         $batch_no = $result_row['batch_no'];
         $existing_stk = $result_row['stk_id'];
     }
}

//include file
include("xml_batches.php");
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
                <?php if($strDo == 'Update') { ?>
                <div class="row">
                    <div class="col-md-12">
                        <h3 class="page-title row-br-b-wp">Manage Batches</h3>
                        <div class="widget" data-toggle="collapse-widget">
                            <div class="widget-head">
                                <h3 class="heading">Change Batch Manufacturer</h3>
                            </div>
                            <div class="widget-body">
                                <form method="post" action="ChangeBatchManufcturerAction.php" id="ChangeBatchManufcturerAction">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="col-md-3">
                                                <div class="control-group">
                                                    <label>Product<font color="#FF0000">*</font></label>
                                                    <div class="controls">
                                                        <input type="text" value="<?php echo $product_name; ?>" readonly=""  class="form-control input-medium"/>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="control-group">
                                                    <label>Batch<font color="#FF0000">*</font></label>
                                                    <div class="controls">
                                                        <input type="text" value="<?php echo $batch_no; ?>" readonly=""  class="form-control input-medium"/>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="control-group">
                                                    <label>Manufacturers<font color="#FF0000">*</font></label>
                                                    <div class="controls">
                                                        <select name="Manufacturer" class="form-control input-medium">
                                                            <option value="">Select</option>
                                                            <?php
                                                            //populate lstStktype combo
                                                            while ($Rowstktype = mysql_fetch_object($result_stk)) {
                                                                ?>
                                                                <option value="<?= $Rowstktype->stk_id ?>" <?php
                                                                if ($Rowstktype->stk_id == $existing_stk) {
                                                                    echo 'selected="selected"';
                                                                }
                                                                ?>>
                                                                            <?= $Rowstktype->stkname ?>
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
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="col-md-12 right">
                                                <div class="control-group">
                                                    <label>&nbsp;</label>
                                                    <div class="controls">
                                                        <input type="hidden" name="hdnbatchId" value="<?= $nbId ?>" />
                                                        <input type="hidden" name="hdnToDo" value="<?= $strDo ?>" />
                                                        <input onclick="return confirm('Are you sure, you want to change the batch manufacturer?');" type="submit" value="<?= $strDo ?>" class="btn btn-primary" />
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
                <?php } ?>
                <div class="row">
                    <div class="col-md-12">
                        <div class="widget">
                            <div class="widget-head">
                                <?php
                                //display All Stakeholders
                                ?>
                                <h3 class="heading">All Batches</h3>
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
            window.location = "ManageBatches.php?Do=Update&Id=" + val;
        }
        function delFunction(val) {
            if (confirm("Are you sure you want to delete the record?")) {
                window.location = "ManageBatches.php?Do=Delete&Id=" + val;
            }
        }
        var mygrid;
        //Initializing grid
        function doInitGrid() {
            mygrid = new dhtmlXGridObject('mygrid_container');
            mygrid.selMultiRows = true;
            mygrid.setImagePath("<?php echo PUBLIC_URL; ?>dhtmlxGrid/dhtmlxGrid/codebase/imgs/");
            mygrid.setHeader("<span title='Serial Number'>Sr. No.</span>,<span title='Product'>Product</span>,<span title='Batch'>Batch</span>,<span title='Manufacturer'>Manufacturer</span>,<span title='Use this column to perform the desired operation'>Actions</span>");
            mygrid.attachHeader(",#select_filter,#text_filter,#text_filter,");
            mygrid.setInitWidths("60,*,250,250,30");
            mygrid.setColAlign("center,left,left,left,center")
            mygrid.setColSorting(",str,,,");
            mygrid.setColTypes("ro,ro,ro,ro,img");
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