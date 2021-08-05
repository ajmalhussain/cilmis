<?php
/**
 * my_report
 * @package reports
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
//include AllClasses
include("../includes/classes/AllClasses.php");
//include
include(PUBLIC_PATH . "html/header.php");
//check user id
if (isset($_SESSION['user_id'])) {
    //get user id
    $userid = $_SESSION['user_id'];
    //set user id
    $objwharehouse_user->m_npkId = $userid;
    //Get warehouse user By Idc
    $result = $objwharehouse_user->GetwhuserByIdc();
} else {
    //display message
    echo "user not login or timeout";
}
//set user stakeholder
$objwharehouse_user->m_stk_id = $_SESSION['user_stakeholder1'];
//set user province
$objwharehouse_user->m_prov_id = $_SESSION['user_province1'];
//set province
$province = $objwharehouse_user->m_prov_id;
@$grp_by =$_REQUEST['grp_by'];
//check date
if (date('d') > 10) {
    //set date
    $date = date('Y-m', strtotime("-1 month", strtotime(date('Y-m-d'))));
} else {
    //set date
    $date = date('Y-m', strtotime("-2 month", strtotime(date('Y-m-d'))));
}
//selected date
$sel_month = date('m', strtotime($date));
//selected year
$sel_year = date('Y', strtotime($date));
$wh_id = $where = $strTitle = $colspan = $header = $header1 = $lvl = $width = $colAlign = $colType = $xmlstore = '';
//if submitted
if (isset($_POST['submit'])) {
    //check report month
    if (!empty($_REQUEST['to_date'])) {
        
        $to_date=$_REQUEST['to_date'];
        $to_date_hf=date('Y-m', strtotime($to_date));
//        print_r($to_date_hf);
    }
     if (!empty($_REQUEST['from_date'])) {
        $from_date=$_REQUEST['from_date'];
        $from_date_hf=date('Y-m', strtotime($from_date));
//        print_r($from_date_hf);exit;
    }
    //checl warehouse id
    if (isset($_POST['wh_id']) && $_POST['wh_id'] != "") {
        //get warehouse id
        $wh_id = $_REQUEST['wh_id'];
        //set where
        $where .=" AND tbl_wh_data.wh_id='" . $_POST['wh_id'] . "'";
    }
    //include xml_my_report
    include("msd_xml_my_report.php");
}
//select query
$qryRes = mysql_fetch_array(mysql_query("SELECT * FROM `tbl_warehouse` WHERE `wh_id`='" . $wh_id . "'"));
//select query
//gets
//stakeholder level
$qryStkLevel = mysql_fetch_array(mysql_query("SELECT stakeholder.lvl FROM tbl_warehouse INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid WHERE tbl_warehouse.wh_id = '" . $wh_id . "'"));

$qry_wh="
(
SELECT
tbl_warehouse.wh_id,
tbl_warehouse.wh_name,
tbl_warehouse.dist_id,
tbl_warehouse.prov_id
FROM
tbl_warehouse
INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
WHERE
tbl_warehouse.prov_id = ".$province." AND
stakeholder.lvl = 2 AND tbl_warehouse.is_allowed_im = 1 AND
tbl_warehouse.stkid IN (7,145)
 ) 
UNION 

(
SELECT
	tbl_warehouse.wh_id,
	tbl_warehouse.wh_name,
	tbl_warehouse.dist_id,
	tbl_warehouse.prov_id
FROM
	tbl_warehouse
INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
WHERE
tbl_warehouse.prov_id = 1 AND
stakeholder.lvl = 3 AND
tbl_warehouse.is_allowed_im = 1 AND
tbl_warehouse.stkid = 2

)    
"; 
//echo $qry_wh;exit;
        $qryRes_wh = mysql_query($qry_wh); 
        while ($row = mysql_fetch_assoc($qryRes_wh)) {
            $wh_array[$row['wh_id']]=$row['wh_name'];
           
        }

?>
<link rel="STYLESHEET" type="text/css" href="<?php echo PUBLIC_URL; ?>dhtmlxGrid/dhtmlxGrid/codebase/dhtmlxgrid.css">
<style>
    div.gridbox div.ftr td {
        background-color: #a6d785;
        font-style: normal;
        font-weight: bold;
        color: #179417;
    }
</style>
<script src="<?php echo PUBLIC_URL; ?>dhtmlxGrid/dhtmlxGrid/codebase/dhtmlxcommon.js"></script>
<script src="<?php echo PUBLIC_URL; ?>dhtmlxGrid/dhtmlxGrid/codebase/dhtmlxgrid.js"></script>
<script src="<?php echo PUBLIC_URL; ?>dhtmlxGrid/dhtmlxGrid/codebase/dhtmlxgridcell.js"></script>
<script src='<?php echo PUBLIC_URL; ?>dhtmlxGrid/dhtmlxGrid/grid2pdf/client/dhtmlxgrid_export.js'></script>
<script src="<?php echo PUBLIC_URL; ?>dhtmlxGrid/dhtmlxGrid/codebase/excells/dhtmlxgrid_excell_link.js"></script>
<script src="<?php echo PUBLIC_URL; ?>dhtmlxGrid/dhtmlxGrid/codebase/ext/dhtmlxgrid_filter.js"></script>
<script>
    var mygrid;
    
    <?php
    if($_REQUEST['grp_by'] == 'generic_name')
    {
    ?>
    function doInitGrid() {
        mygrid = new dhtmlXGridObject('mygrid_container');
        mygrid.selMultiRows = true;
        mygrid.setImagePath("../plmis_src/operations/dhtmlxGrid/dhtmlxGrid/codebase/imgs/");
        mygrid.setHeader("<div style='text-align:center; font-size:14px; font-weight:bold;'>Inventory Status Report for <?php echo $qryRes['wh_name']; ?> (<?php echo date('d-M-Y', strtotime($from_date)); ?> to <?php echo date('d-M-Y', strtotime($to_date));; ?> )</div>,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan");
        mygrid.attachHeader("<div style='text-align:center;'>Warehouse</span>,<div style='text-align:center;'>Generic Name</div>,<div style='text-align:center;'>Opening Balance</span>,<span title='Balance received'>Received</span>,<span title='Balance issued'>Issued</span>,<div style='text-align:center;'>Adjustments</div>,#cspan,<span title='Closing balance'>Closing Balance</span><?php echo $header; ?>,<span title='Last Modified'>Last Modified</span>");
        mygrid.attachHeader("#select_filter,#text_filter,#rspan,#rspan,#rspan,<div style='text-align:center;'>(+)</div>,<div style='text-align:center;'>(-)</div>,#rspan<?php echo $header1; ?>,#rspan");
        mygrid.setInitWidths("160,*,100,100,100,60,60,120,120");
        mygrid.setColAlign("left,left,right,right,right,right,right,right,center");
        mygrid.setColTypes("ro,ro,ro,ro,ro,ro,ro,ro,ro");
        mygrid.enableRowsHover(true, 'onMouseOver');   // `onMouseOver` is the css class name.
        mygrid.setSkin("light");
        mygrid.init();
        //mygrid.loadXML("xml/whreport.xml");
        mygrid.clearAll();
        mygrid.loadXMLString('<?php echo $xmlstore; ?>');
    }
    
    <?php
    }else{
        ?>
    function doInitGrid() {
        mygrid = new dhtmlXGridObject('mygrid_container');
        mygrid.selMultiRows = true;
        mygrid.setImagePath("../plmis_src/operations/dhtmlxGrid/dhtmlxGrid/codebase/imgs/");
        mygrid.setHeader("<div style='text-align:center; font-size:14px; font-weight:bold;'>Inventory Status Report for <?php echo $qryRes['wh_name']; ?> (<?php echo date('d-M-Y', strtotime($from_date)); ?> to <?php echo date('d-M-Y', strtotime($to_date));; ?> )</div>,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan");
        mygrid.attachHeader("<div style='text-align:center;'>Warehouse</span>,<div style='text-align:center;'>Product</div>,<div style='text-align:center;'>Generic Name</div>,<div style='text-align:center;'>Opening Balance</span>,<span title='Balance received'>Received</span>,<span title='Balance issued'>Issued</span>,<div style='text-align:center;'>Adjustments</div>,#cspan,<span title='Closing balance'>Closing Balance</span><?php echo $header; ?>,<span title='Last Modified'>Last Modified</span>");
        mygrid.attachHeader("#select_filter,#text_filter,#text_filter,#rspan,#rspan,#rspan,<div style='text-align:center;'>(+)</div>,<div style='text-align:center;'>(-)</div>,#rspan<?php echo $header1; ?>,#rspan");
        mygrid.setInitWidths("160,*,150,100,100,100,60,60,120,120");
        mygrid.setColAlign("left,left,left,right,right,right,right,right,right,center");
        mygrid.setColTypes("ro,ro,ro,ro,ro,ro,ro,ro,ro,ro");
        mygrid.enableRowsHover(true, 'onMouseOver');   // `onMouseOver` is the css class name.
        mygrid.setSkin("light");
        mygrid.init();
        //mygrid.loadXML("xml/whreport.xml");
        mygrid.clearAll();
        mygrid.loadXMLString('<?php echo $xmlstore; ?>');
    }
    
    <?php
    }
    
    ?>

</script>
</head><!-- END HEAD -->

<body class="page-header-fixed page-quick-sidebar-over-content" onLoad="doInitGrid();">
    <!-- BEGIN HEADER -->
    <div class="page-container">
        <?php
        //include top
        include PUBLIC_PATH . "html/top.php";
        //include top_im
        include PUBLIC_PATH . "html/top_im.php";
        ?>
        <div class="page-content-wrapper">
            <div class="page-content">
                <div class="row">
                    <div class="col-md-12">
                        <h3 class="page-title row-br-b-wp">Medical Store Depot - Inventory Status</h3>
                        <div class="widget" data-toggle="collapse-widget">
                            <div class="widget-head">
                                <h3 class="heading">Filter by</h3>
                            </div>
                            <div class="widget-body">
                                <table width="99%">
                                    <tr>
                                        <td><form action="" method="post" onSubmit="return formValidate()">
                                                <div class="col-md-12">
                                                     
                                                    <div class="col-md-2">
                                                        <div class="form-group">
                                                            <label class="control-label">From Date</label>
                                                            <div class="form-group">
                                                                <input type="text" name="from_date" id="from_date"  class="form-control input-sm" value="<?php
                                                                if (isset($_REQUEST['from_date'])) {
                                                                    echo date('Y-m-d', strtotime($from_date));
                                                                } else {

                                                                    echo date('Y-m-01');
                                                                }
                                                                ?>" required readonly="true">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="form-group">
                                                            <label class="control-label">To Date</label>
                                                            <div class="form-group">
                                                                <input type="text" name="to_date" id="to_date"  class="form-control input-sm" value="<?php
                                                                if (isset($_REQUEST['to_date'])) {
                                                                    echo date('Y-m-d', strtotime($to_date));
                                                                } else {

                                                                    echo date('Y-m-d');
                                                                }
                                                                ?>" required readonly="true">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="control-group">
                                                            <label class="control-label">Warehouse</label>
                                                            <div class="controls">
                                                                <select name="wh_id" id="wh_id" class="form-control input-sm" >
                                                                    <option value="">All MSD Stores</option>
                                                                    <?php
                                                                    
                                                                    foreach ($wh_array as $this_wh_id => $wh_name) {
                                                                        ?>
                                                                        <option <?php if ($this_wh_id == $wh_id) {
                                                                        echo 'selected="selected"';
                                                                        } ?> value="<?php echo $this_wh_id; ?>"><?php echo $wh_name; ?></option>
                                                                        <?php
                                                                    }
                                                                    
                                                                    ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="control-group">
                                                            <label class="control-label">Aggregate By:</label>
                                                            <div class="controls">
                                                                <select name="grp_by" id="grp_by" class="form-control input-sm" required="required">
                                                                    <option <?=(($grp_by=='brand_name')?' selected ':'')?> value="brand_name">Product / Brand Name</option>
                                                                    <option <?=(($grp_by=='generic_name')?' selected ':'')?> value="generic_name">Generic Name</option>
                                                                    
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="control-group">
                                                            <label class="control-label">&nbsp;</label>
                                                            <div class="controls">
                                                                <input type="submit" value="Go" name="submit" class="btn btn-primary input-sm"/>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
<?php
if (isset($_POST['submit'])) {
    if ($_SESSION['numOfRows'] > 0) {
        ?>
                                <table width="100%" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td align="right" style="padding-right:5px;">
                                            <img title="Click here to export data to PDF file" style="cursor:pointer;" src="<?php echo PUBLIC_URL; ?>images/pdf-32.png" onClick="mygrid.toPDF('<?php echo PUBLIC_URL; ?>dhtmlxGrid/dhtmlxGrid/grid2pdf/server/generate.php');"/>
                                            <img title="Click here to export data to Excel file" style="cursor:pointer;" src="<?php echo PUBLIC_URL; ?>images/excel-32.png" onClick="mygrid.toExcel('<?php echo PUBLIC_URL; ?>dhtmlxGrid/dhtmlxGrid/grid2excel/server/generate.php');"/>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><div id="mygrid_container" style="width:100%; height:580px; background-color:white;overflow:hidden"></div></td>
                                    </tr>
                                </table>
        <?php
    } else {
        $qryRes = mysql_fetch_array(mysql_query("SELECT * FROM `tbl_warehouse` WHERE `wh_id`='" . $wh_id . "'"));
        ?>
                                <script type="text/javascript"></script>
                                <div style="font-size:12px; font-weight:bold; color:#F00; text-align:left">
                                    <?php
                                    if (!empty($_POST['districts'])) {
                                        echo "No data entered for $qryRes[wh_name]($qryRes[wh_type_id]) in $reportingDate.";
                                    } else {
                                        echo "No data entered during ".date('d-M-Y', strtotime($from_date))." and ".date('d-M-Y', strtotime($to_date));
                                    }
                                    ?>
                                </div>
        <?php
    }
}
?>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php
//include footer
include PUBLIC_PATH . "/html/footer.php";
?>
    <script type="text/javascript">
        
        $(function () {
                                        
                                        $('#to_date').datepicker({
                                            dateFormat: "yy-mm-dd",
                                            constrainInput: false,
                                            changeMonth: true,
                                            changeYear: true,
                                            maxDate: ''
                                        });
                                        $('#from_date').datepicker({
                                            dateFormat: "yy-mm-dd",
                                            constrainInput: false,
                                            changeMonth: true,
                                            changeYear: true,
                                            maxDate: ''
                                        });
                                    });
        </script>
</body>
<!-- END BODY -->
</html>