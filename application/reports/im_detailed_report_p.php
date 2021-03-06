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
    include("im_xml_my_report.php");
}
//select query
$qryRes = mysql_fetch_array(mysql_query("SELECT * FROM `tbl_warehouse` WHERE `wh_id`='" . $wh_id . "'"));
//select query
//gets
//stakeholder level
$qryStkLevel = mysql_fetch_array(mysql_query("SELECT stakeholder.lvl FROM tbl_warehouse INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid WHERE tbl_warehouse.wh_id = '" . $wh_id . "'"));
if ($qryStkLevel['lvl'] == 4) {
    //title
    $strTitle = "Facility";
} elseif ($qryStkLevel['lvl'] == 3) {
    //title
    $strTitle = "Store";
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
    function doInitGrid() {
        mygrid = new dhtmlXGridObject('mygrid_container');
        mygrid.selMultiRows = true;
        mygrid.setImagePath("../plmis_src/operations/dhtmlxGrid/dhtmlxGrid/codebase/imgs/");
        mygrid.setHeader("<div style='text-align:center; font-size:14px; font-weight:bold;'>Monthly <?php echo $strTitle; ?> Report for <?php echo $qryRes['wh_name']; ?> (<?php echo date('d-M-Y', strtotime($from_date)); ?> to <?php echo date('d-M-Y', strtotime($to_date));; ?> )</div>,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan<?php echo $colspan; ?>,#cspan");
        mygrid.attachHeader("<div style='text-align:center;'>Product</div>,<div style='text-align:center;'>Batch No</span>,<div style='text-align:center;'>Opening Balance</span>,<span title='Balance received'>Received</span>,<span title='Balance issued'>Issued</span>,<div style='text-align:center;'>Adjustments</div>,#cspan,<span title='Closing balance'>Closing Balance</span><?php echo $header; ?>,<span title='Last Modified'>Last Modified</span>");
        mygrid.attachHeader("#rspan,#rspan,#rspan,#rspan,#rspan,<div style='text-align:center;'>(+)</div>,<div style='text-align:center;'>(-)</div>,#rspan<?php echo $header1; ?>,#rspan");
<?php if ($lvl == 7 && in_array($type, array(4, 5))) { ?>
            mygrid.attachFooter("<div><?php echo $xmlstore1; ?></div>,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan<?php echo $colspan; ?>,#cspan");
<?php } ?>
        mygrid.setInitWidths("*,120,100,100,100,60,60,120<?php echo $width; ?>,120");
        mygrid.setColAlign("left,left,right,right,right,right,right,right<?php echo $colAlign; ?>,center");
        mygrid.setColTypes("ro,ro,ro,ro,ro,ro,ro,ro<?php echo $colType; ?>,ro");
        mygrid.enableRowsHover(true, 'onMouseOver');   // `onMouseOver` is the css class name.
        mygrid.setSkin("light");
        mygrid.init();
        //mygrid.loadXML("xml/whreport.xml");
        mygrid.clearAll();
        mygrid.loadXMLString('<?php echo $xmlstore; ?>');
    }

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
                        <h3 class="page-title row-br-b-wp">Stock Detail Report</h3>
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

                                                                    echo date('Y-m-d');
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
                                                            <label class="control-label">Store/Facility</label>
                                                            <div class="controls">
                                                                <select name="wh_id" id="wh_id" class="form-control input-sm" required="required">
                                                                    
                                                                    <?php
                                                                    //Get warehouse user By Idc
                                                                    $result1 = $objwharehouse_user->GetwhuserByIdc();
                                                                    if ($result1 != FALSE && mysql_num_rows($result1) > 0) {
                                                                        //fetch results
                                                                        while ($row = mysql_fetch_array($result1)) {
                                                                            ?>
                                                                            <option <?php if ($row['wh_id'] == $wh_id) {
                                                                        echo 'selected="selected"';
                                                                    } ?> value="<?php echo $row['wh_id']; ?>"><?php echo $row['wh_name']; ?></option>
                                                                            <?php
                                                                        }
                                                                    }
                                                                    $objwharehouse_user->m_npkId = $userid;
                                                                    //Get warehouse user HF By Idc
                                                                    $result1 = $objwharehouse_user->GetwhuserHFByIdc();
                                                                    //check if result
                                                                    if ($result1 != FALSE && mysql_num_rows($result1) > 0) {
                                                                        if ($_SESSION['userdata'][7] != 73) {
                                                                            $group = "Health Facilities";
                                                                        } else {
                                                                            $group = "CMWs";
                                                                        }
                                                                        echo "<optgroup label=\"$group\">";
                                                                        //fetch results
                                                                        while ($row = mysql_fetch_array($result1)) {
                                                                            ?>
                                                                            <option <?php if ($row['wh_id'] == $wh_id) {
                                                                        echo 'selected="selected"';
                                                                    } ?> value="<?php echo $row['wh_id']; ?>"><?php echo $row['wh_name']; ?></option>
                                                                            <?php
                                                                        }
                                                                        echo "</optgroup>";
                                                                    }
                                                                    ?>
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
                                        <td><div id="mygrid_container" style="width:100%; height:380px; background-color:white;overflow:hidden"></div></td>
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