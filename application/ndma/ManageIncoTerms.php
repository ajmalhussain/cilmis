<?php
include("../includes/classes/AllClasses.php");
include(PUBLIC_PATH . "html/header.php");

//-------------------File Config-------------------//
$module         =   'Inco Terms';       // for display
$module_abbr    =   'incoterms';        // for db
$this_file_name =   'ManageIncoTerms';  //name of this file
//-------------------------------------------------//


$strDo = "Add";
if (isset($_REQUEST['Do']) && !empty($_REQUEST['Do'])) {
    $strDo = $_REQUEST['Do'];
}

if (isset($_REQUEST['id']) && !empty($_REQUEST['id'])) {
    $this_id = $_REQUEST['id'];
}
if ($strDo == "Edit") {
         $qry = "SELECT
                    text_values.pk_id,
                    text_values.stkid,
                    text_values.type,
                    text_values.text_value,
                    text_values.updatedby,
                    text_values.updatedon,
                    text_values.is_active
                    FROM
                    text_values
                    WHERE
                    text_values.pk_id = '".$this_id."' ; ";
    $res = mysql_query($qry);
    if ($res != FALSE && mysql_num_rows($res) > 0) {
        $edit_data = mysql_fetch_assoc($res);
    }
}
//echo '<pre>';
//print_r($edit_data);
//echo '</pre>';
//exit;

$query_xmlw = "SELECT
text_values.pk_id,
text_values.stkid,
text_values.type,
text_values.text_value,
text_values.updatedby,
text_values.updatedon,
text_values.is_active
FROM
text_values
where 
text_values.stkid =" . $_SESSION['user_stakeholder1'] . "
and type = '".$module_abbr."';
 ";
//query result
$result_xmlw = mysql_query($query_xmlw);

//Generating xml for grid
$xmlstore = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
$xmlstore .= "<rows>";
$counter = 1;
//populate xml
while ($row_xmlw = mysql_fetch_array($result_xmlw)) {
    $this_type=$row_xmlw['type'];
    $temp = "\"$row_xmlw[pk_id]\"";
    $xmlstore .= "<row>";
    $xmlstore .= "<cell>" . $counter++ . "</cell>";
    $xmlstore .= "<cell>" . $module . "</cell>";
    
    if($row_xmlw['pk_id'] == 3){
//        echo $row_xmlw['text_value'].nl2br($row_xmlw['text_value']).' <br/><br/> THIS :'.  nl2br(str_replace(array("\r\n", "\r", "\n"), '<br/>', $row_xmlw['text_value']));exit;
    }
    
    $xmlstore .= "<cell><![CDATA[" . str_replace(array("\r\n", "\r", "\n"), ' ', $row_xmlw['text_value']) . "]]></cell>";
    if($row_xmlw['is_active']=='1'){
    $xmlstore .= "<cell>Active</cell>";
    }
    else if($row_xmlw['is_active']=='0'){
        $xmlstore .= "<cell>Disabled</cell>";
    }
    $xmlstore .= "<cell type=\"img\">" . PUBLIC_URL . "dhtmlxGrid/dhtmlxGrid/codebase/imgs/edit.gif^" . PUBLIC_URL . "dhtmlxGrid/dhtmlxGrid/codebase/imgs/edit.gif^javascript:editFunction($temp)^_self</cell>";
    $xmlstore .= "</row>";
}
$xmlstore .= "</rows>";
//echo $xmlstore;exit;
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
                        <h3 class="page-title row-br-b-wp"><?=$module?> Management</h3>
                        <div class="widget" data-toggle="collapse-widget">
                            <div class="widget-head">
                                <h3 class="heading"><?php echo $strDo; ?> <?=$module?></h3>
                            </div>
                            <div class="widget-body">
                                <form method="post" action="<?=$this_file_name?>_action.php" id="Managestakeholdersaction">
                                    <div class="row">
                                        <div class="col-md-12">
                                            
                                            <div class="col-md-3">
                                                <label class="control-label">Type</label>
                                                <div class="controls">
                                                    <select class="form-control input-sm  " maxlength="250" type="text"  id="text_type" name="text_type">
                                                        <option value="<?=$module_abbr?>"><?=$module?></option>
                                                    </select>
                                                </div> 
                                            </div>
                                        
                                            <div class="col-md-6">
                                                <label class="control-label">Value</label>
                                                <div class="controls">
                                                    <textarea id="text_value" rows="3" cols="60" maxlength="2000" name="text_value"><?=(!empty($edit_data['text_value'])?$edit_data['text_value']:'')?></textarea>
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <label class="control-label">Active/InActive Status</label>
                                                <div class="controls">
                                                    <select class="form-control input-sm  " maxlength="250" type="text"  id="status" name="status">
                                                        <option value="1" <?php if(isset($edit_data['is_active'])&&$edit_data['is_active']=='1') echo 'selected="selected"';?>>Active</option>
                                                        <option value="0" <?php if(isset($edit_data['is_active'])&&$edit_data['is_active']=='0') echo 'selected="selected"';?>>InActive</option>
                                                    </select>
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
                                                            <input type="hidden" name="this_file_name" value="<?= $this_file_name ?>" />
                                                            <input type="hidden" name="id" value="<?= @$this_id ?>" />
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
                                    <h3 class="heading">All <?=$module?></h3>
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
                window.location = "<?=$this_file_name?>.php?Do=Edit&id=" + val;
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
                mygrid.setHeader("<span title='Serial Number'>Sr. No.</span>,<span title='Type'>Type</span>,<span title='<?=$module?>'><?=$module?></span>,<span title='Status'>Status</span>,<span title='action'>Action</span>");
                mygrid.attachHeader(",#text_filter,#text_filter,#text_filter,");
                mygrid.setInitWidths("60,200,*,100,60");
                mygrid.setColAlign("center,left,left,left,center");
                mygrid.setColSorting(",,str,,,");
                mygrid.setColTypes("ro,ro,edtxt,ro,img");
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