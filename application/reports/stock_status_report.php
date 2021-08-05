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
//$province = $objwharehouse_user->m_prov_id;
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


$date_from = $date_to = $product = $provinceID = $district = $stakeholder = $warehouse = $xmlstore = $selProv = '';


$wh_id = $where = $strTitle = $colspan = $header = $header1 = $lvl = $width = $colAlign = $colType = $xmlstore = '';
//if submitted
if (isset($_REQUEST['submit'])) {
//    print_r($_REQUEST);
    $province = !empty($_REQUEST['province']) ? $_REQUEST['province'] : '';
    $date_from = !empty($_REQUEST['date_from']) ? $_REQUEST['date_from'] : '';
    $date_to = !empty($_REQUEST['date_to']) ? $_REQUEST['date_to'] : '';
    $selStk = !empty($_REQUEST['stk_sel']) ? $_REQUEST['stk_sel'] : '';
    $selPro = !empty($_REQUEST['province']) ? $_REQUEST['province'] : '';
    $selDist = !empty($_REQUEST['district']) ? $_REQUEST['district'] : '';
    $sel_wh = !empty($_REQUEST['warehouse']) ? $_REQUEST['warehouse'] : '';
    $selLevel = !empty($_REQUEST['facility_level']) ? $_REQUEST['facility_level'] : '';
    //check report month
    if (!empty($_REQUEST['date_from'])) {
        //get report month
        $date_from = $_REQUEST['date_from'];
        //where
        $where .= "tbl_wh_data.RptDate >= '" . $_POST['date_from'] . "' ";
    }if (!empty($_REQUEST['date_to'])) {
        //get selected year
        $date_to = $_REQUEST['date_to'];
        //where
        $where .= " AND tbl_wh_data.RptDate <= '" . $_POST['date_to'] . "' ";
    }
    //checl warehouse id
    if (isset($_POST['warehouse']) && $_POST['warehouse'] != "") {
        //get warehouse id
        $wh_id = $_REQUEST['warehouse'];
        //set where
        $where .=" AND tbl_wh_data.wh_id='" . $_POST['warehouse'] . "'";
    }
    //include xml_my_report
    include("xml_my_report_ssr.php");
}
//select query
$qryRes = mysql_fetch_array(mysql_query("SELECT * FROM `tbl_warehouse` WHERE `wh_id`='" . $wh_id . "'"));
$whName1 = $qryRes['wh_name'];
$whName = str_replace( ',', '', $whName1);

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

$reportingDate = date('M', mktime(0, 0, 0, $sel_month, 1)) . ' ' . $sel_year;
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
<link rel="stylesheet" type="text/css" href="../../public/assets/global/plugins/select2/select2.css"/>
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
        mygrid.setHeader("<div style='text-align:center; font-size:14px; font-weight:bold;'>Stock Status Report for <?php echo $whName; ?> (Date From <?php echo $date_from; ?> To <?php echo $date_to; ?>)</div>,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan<?php echo $colspan; ?>");
        mygrid.attachHeader("<div style='text-align:center;'>Product</div>,<div style='text-align:center;'>Opening Balance</span>,<span title='Balance received'>Received</span>,<span title='Balance issued'>Issued</span>,<div style='text-align:center;'>Adjustments</div>,#cspan,<span title='Closing balance'>Closing Balance</span><?php echo $header; ?>");
        mygrid.attachHeader("#rspan,#rspan,#rspan,#rspan,<div style='text-align:center;'>(+)</div>,<div style='text-align:center;'>(-)</div>,#rspan<?php echo $header1; ?>");
<?php if ($lvl == 7 && in_array($type, array(4, 5))) { ?>
            mygrid.attachFooter("<div><?php echo $xmlstore1; ?></div>,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan<?php echo $colspan; ?>");
<?php } ?>
        mygrid.setInitWidths("*,120,100,100,60,60,120<?php echo $width; ?>");
        mygrid.setColAlign("left,right,right,right,right,right,right<?php echo $colAlign; ?>");
        mygrid.setColTypes("ro,ro,ro,ro,ro,ro,ro<?php echo $colType; ?>");
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
                        <h3 class="page-title row-br-b-wp">Stock Status Report</h3>
                        <div class="widget" data-toggle="collapse-widget">
                            <div class="widget-head">
                                <h3 class="heading">Filter by</h3>
                            </div>
                            <div class="widget-body">
                                <table width="99%">
                                    <tr>
                                        <td><form action="stock_status_report.php" method="post" id="report_form" name="report_form">
                                                <div class="col-md-12">
<!--                                                    <div class="col-md-2">
                                                        <div class="control-group">
                                                            <label class="control-label">Month</label>
                                                            <div class="controls">
                                                                <SELECT NAME="report_month" id="report_month" CLASS="sb1GeenGradientBoxMiddle form-control input-sm" TABINDEX="3">
                                                                    <?php
                                                                    for ($i = 1; $i <= 12; $i++) {
                                                                        if ($sel_month == $i) {
                                                                            $sel = "selected='selected'";
                                                                        } elseif ($i == 1) {
                                                                            $sel = "selected='selected'";
                                                                        } else {
                                                                            $sel = "";
                                                                        }
                                                                        ?>
                                                                        <option value="<?php echo $i; ?>"<?php echo $sel; ?> ><?php echo date('M', mktime(0, 0, 0, $i, 1)); ?></option>
                                                                        <?php
                                                                    }
                                                                    ?>
                                                                </SELECT>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="control-group">
                                                            <label class="control-label">Year</label>
                                                            <div class="controls">
                                                                <select name="report_year" id="report_year" class="sb1GeenGradientBoxMiddle form-control input-sm" tabindex="2">
                                                                    <?php
                                                                    //End Year 
                                                                    $EndYear = 2010;
                                                                    //Start Year 
                                                                    $StartYear = date('Y');
                                                                    for ($i = $StartYear; $i >= $EndYear; $i--) {
                                                                        if ($sel_year == $i) {
                                                                            $chk4 = "Selected = 'Selected'";
                                                                        } else {
                                                                            $chk4 = "";
                                                                        }
                                                                        echo"<OPTION VALUE='$i' $chk4>$i</OPTION>";
                                                                    }
                                                                    ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>-->

                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label class="control-label">From Date</label>
                                                    <input type="text" readonly class="form-control input-sm" name="date_from" id="date_from" value="<?php echo $date_from; ?>"/>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label class="control-label">To Date</label>
                                                    <input type="text" readonly class="form-control input-sm" name="date_to" id="date_to" value="<?php echo $date_to; ?>"/>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="control-group">
                                                    <label>Stakeholder</label>
                                                    <div class="controls">
                                                        <select name="stk_sel" id="stk_sel"  class="form-control input-sm" required>
                                                            
                                                            <?php
                                                            if(!empty($_SESSION['user_stakeholder1']))
                                                                {  
                                                            $querystk = "SELECT
                                                                        stkid,
                                                                        stkname
                                                                FROM
                                                                        stakeholder
                                                                WHERE
                                                                        stakeholder.ParentID IS NULL
                                                                AND stakeholder.stk_type_id IN (0, 1)
                                                                AND stakeholder.is_reporting = 1
                                                                AND stakeholder.lvl = 1
                                                                AND stakeholder.stkid = ".$_SESSION['user_stakeholder1']."
                                                                ORDER BY
                                                                        stakeholder.stkorder";
                                                                }
                                                                else { ?> <option value="">Select</option> <?php
                                                            $querystk = "SELECT
                                                                                    stkid,
                                                                                    stkname
                                                                            FROM
                                                                                    stakeholder
                                                                            WHERE
                                                                                    stakeholder.ParentID IS NULL AND
                                                                                    stakeholder.stk_type_id = 0 AND
                                                                                    stakeholder.lvl = 1 AND
                                                                                    stakeholder.is_reporting = 1
                                                                            ORDER BY
                                                                                    stkorder";
                                                                }
                                                            //query result
                                                            $rsstk = mysql_query($querystk) or die();
                                                            //fetch result
                                                                $stk_name = 'All Stakeholders';
                                                            while ($rowstk = mysql_fetch_array($rsstk)) {
                                                                //selected stakeholder
                                                                if ($selStk == $rowstk['stkid']) {
                                                                     $sel = "selected='selected'";
                                                                    $stk_name = $rowstk['stkname'];
                                                                } else {
                                                                    $sel = "";
                                                                }
                                                                ?>
                                                                <option value="<?php echo $rowstk['stkid']; ?>" <?php echo $sel; ?>><?php echo $rowstk['stkname']; ?></option>
                                                                <?php
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="control-group">
                                                    <label>Level</label>
                                                    <div class="controls">
                                                        <select name="facility_level" id="facility_level" class="form-control input-sm">
                                                            <option value="1" <?=($selLevel=='1')?' selected ':''?>>Federal Stores</option>
                                                            <option value="2" <?=($selLevel=='2')?' selected ':''?>>Provincial Stores</option>
                                                            <option value="3" <?=($selLevel=='3')?' selected ':''?>>District Stores</option>
                                                            <option value="7" <?=($selLevel=='7')?' selected ':''?>>Health Facility</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                             <div class="col-md-2" id="province_div" style="<?=(!empty($_REQUEST['facility_level']) && $_REQUEST['facility_level']=='1')?'display:none;':''?>">
                                            <label class="control-label">Province/Region</label>
                                            <div class="form-group">
                                                 <select name="province" id="province"  class="form-control input-sm">
                                                            
                                                            <option value="">Select</option> <?php
                                                                $queryprov = "SELECT
                                                                            tbl_locations.PkLocID AS prov_id,
                                                                            tbl_locations.LocName AS prov_title
                                                                        FROM
                                                                            tbl_locations
                                                                        WHERE
                                                                            LocLvl = 2
                                                                        AND parentid IS NOT NULL ";
                                                               
                                                                //query result
                                                                $rsprov = mysql_query($queryprov) or die();
                                                                $prov_name = '';
                                                                while ($rowprov = mysql_fetch_array($rsprov)) {
                                                                    if ($province == $rowprov['prov_id']) {
                                                                        $sel = "selected='selected'";
                                                                        $prov_name = $rowprov['prov_title'];
                                                                    } else {
                                                                        $sel = "";
                                                                    }
                                                                    //Populate prov_sel combo
                                                                    ?>
                                                                    <option value="<?php echo $rowprov['prov_id']; ?>" <?php echo $sel; ?>><?php echo $rowprov['prov_title']; ?></option>
                                                                    <?php
                                                                } 
                                                            ?>                        
                                                        </select>
                                            </div>
                                        </div>
                                        <div class="col-md-2" id="districts" style="<?=(!empty($_REQUEST['facility_level']) && $_REQUEST['facility_level']=='1' || $_REQUEST['facility_level']=='2')?'display:none;':''?>">
                                            <label class="control-label">District</label>
                                            <div class="form-group">
                                                <select name="district" id="district" class="form-control input-sm" data-placeholder="Select..." >
                                                    <option value="">Select</option>
                                                    <?php
                                                    if(isset($province) && $province != '')
                                                    {
                                                    $queryDist = "SELECT
                                                                                tbl_locations.PkLocID,
                                                                                tbl_locations.LocName
                                                                        FROM
                                                                                tbl_locations
                                                                        WHERE
                                                                                tbl_locations.LocLvl = 3
                                                                        AND tbl_locations.parentid = '" . $province. "'
                                                                        ORDER BY
                                                                                tbl_locations.LocName ASC";
                                                        //query result
//                                                    echo $queryDist;
//                                                    echo $selDist;
//                                                    exit();
                                                    
                                                        $rsDist = mysql_query($queryDist) or die();
                                                        //fetch result
                                                        $dist_name ='';
                                                        while ($rowDist = mysql_fetch_array($rsDist)) {
                                                             if ($selDist == $rowDist['PkLocID']) {
                                                                $sel = "selected='selected'";
                                                                $dist_name = $rowDist['LocName'];
                                                            } else {
                                                                $sel = "";
                                                            }
                                                            //populate district combo
                                                            ?>
                                                            <option value="<?php echo $rowDist['PkLocID']; ?>" <?php echo $sel; ?>><?php echo $rowDist['LocName']; ?></option>
                                                            <?php
                                                        }
                                                    }
                                                        ?>
                                                    
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="col-md-3" id="stores">
                                            <label class="control-label">SDPs</label>
                                            <div class="form-group">
                                                <select name="warehouse" id="warehouse" class="form-control input-sm">
                                                    <option value="">Select</option>
                                                  <?php 
                                                  if(isset($selDist) && $selDist != '' && isset($selStk) && $selStk != '')
                                                    {
                                                     $qry = "SELECT
                                                                *
                                                        FROM
                                                                (
                                                                        SELECT
                                                                                tbl_warehouse.wh_id,
                                                                                tbl_warehouse.wh_name,
                                                                                tbl_warehouse.stkid,
                                                                                stakeholder.lvl,
                                                                                tbl_hf_type_rank.hf_type_rank,
                                                                                tbl_warehouse.wh_rank
                                                                        FROM
                                                                                tbl_warehouse
                                                                        INNER JOIN stakeholder ON stakeholder.stkid = tbl_warehouse.stkofficeid
                                                                        LEFT JOIN tbl_hf_type_rank ON tbl_warehouse.hf_type_id = tbl_hf_type_rank.hf_type_id
                                                                        AND tbl_warehouse.prov_id = tbl_hf_type_rank.province_id
                                                                        AND tbl_warehouse.stkid = tbl_hf_type_rank.stakeholder_id
                                                                        WHERE
                                                                                tbl_warehouse.dist_id = $selDist
                                                                        AND tbl_warehouse.stkid = ".$selStk."
                                                                        AND stakeholder.lvl = $selLevel
                                                                ) A
                                                        GROUP BY
                                                                A.wh_id
                                                        ORDER BY
                                                                A.lvl,

                                                        IF (
                                                                A.wh_rank = ''
                                                                OR A.wh_rank IS NULL,
                                                                1,
                                                                0
                                                        ),
                                                         A.wh_rank,

                                                        IF (
                                                                A.hf_type_rank = ''
                                                                OR A.hf_type_rank IS NULL,
                                                                1,
                                                                0
                                                        ),
                                                         A.hf_type_rank ASC,
                                                         A.wh_name ASC";
//                                                   echo $qry;
//                                                  exit;
                                                          $rsprov = mysql_query($qry) or die();
//                                                                $prov_name = '';
                                                                while ($rowprov = mysql_fetch_array($rsprov)) {
                                                                    if ($sel_wh == $rowprov['wh_id']) {
                                                                        $sel = "selected='selected'";
//                                                                        $prov_name = $rowprov['wh_name'];
                                                                    } else {
                                                                        $sel = "";
                                                                    }
                                                                    //Populate prov_sel combo
                                                                    ?>
                                                                    <option value="<?php echo $rowprov['wh_id']; ?>" <?php echo $sel; ?>><?php echo $rowprov['wh_name']; ?></option>
                                                                    <?php
                                                                } 
                                                    }
                                                            ?>  
                                                    
                                                </select>
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
                                        echo "No data entered for $qryRes[wh_name]($qryRes[wh_type_id]) in (Date From  $date_from To  $date_to)";
                                    } else {
                                        echo "No data entered in (Date From $date_from To  $date_to)";
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
//$whName = str_replace( ',', '', $whName);

?>
    
</body>
<!-- END BODY -->
    <script type="text/javascript" src="../../public/assets/global/plugins/select2/select2.min.js"></script>

<script language="javascript">
     $(function () {
         
         <?php if (!isset($_REQUEST['facility_level']))
         {?>
             $('#province_div').hide();
             $('#districts').hide();
         <?php }
         ?>
         
         showDistricts('<?php echo $sel_prov; ?>', '<?php echo $sel_stk; ?>');
//            showStores('<?php echo $sel_dist; ?>');
            showDistricts($('#province').val(), $('#stk_sel').val());
            showStores($('#district option:selected').val());
            
//            <?php if (!isset($_REQUEST['province']) || !empty($_REQUEST['province']) ) { ?>
//            showStores1();
//            <?php }
            elseif (isset($_REQUEST['district']) && !empty($_REQUEST['district']) ) { ?>//
//            showStores($('#district option:selected').val(),$('#province option:selected').val());
//            <?php }
            if (isset($_REQUEST['province']) && empty($_REQUEST['district']) && $_REQUEST['facility_level'] != '1') { ?>//
            showStores2($('#province option:selected').val());
//           <?php  } ?>
            
            
            $('#province, #stk_sel').change(function (e) {
                $('#district').html('<option value="">All</option>');
                $('#warehouse').html('<option value="">Select</option>');
                showDistricts($('#province').val(), $('#stk_sel').val());
            });
            $('#stk_sel').change(function (e) {
                $('#warehouse').html('<option value="">All</option>');
            });

            $(document).on('click', '  #district ', function ()
            {
                showStores($('#district option:selected').val(),$('#province option:selected').val());
            });
            $(document).on('click', '#facility_level', function ()
            {
                showStores1();
            });
            $(document).on('click', '#province', function ()
            {
                showStores2($('#province option:selected').val());
            });
         
         
         
         
        var startDateTextBox = $('#date_from');
        var endDateTextBox = $('#date_to');

        startDateTextBox.datepicker({
            minDate: "-10Y",
            maxDate: 0,
            dateFormat: 'yy-mm-dd',
            changeMonth: true,
            changeYear: true,
            onClose: function (dateText, inst) {
                if (endDateTextBox.val() != '') {
                    var testStartDate = startDateTextBox.datepicker('getDate');
                    var testEndDate = endDateTextBox.datepicker('getDate');
                    if (testStartDate > testEndDate)
                        endDateTextBox.datepicker('setDate', testStartDate);
                } else {
                    endDateTextBox.val(dateText);
                }

            },
            onSelect: function (selectedDateTime) {
                endDateTextBox.datepicker('option', 'minDate', startDateTextBox.datepicker('getDate'));
            }
        });
        endDateTextBox.datepicker({
            maxDate: 0,
            dateFormat: 'yy-mm-dd',
            changeMonth: true,
            changeYear: true,
            onClose: function (dateText, inst) {
                if (startDateTextBox.val() != '') {
                    var testStartDate = startDateTextBox.datepicker('getDate');
                    var testEndDate = endDateTextBox.datepicker('getDate');
                    if (testStartDate > testEndDate)
                        startDateTextBox.datepicker('setDate', testEndDate);
                } else {
                    startDateTextBox.val(dateText);
                }

            },
            onSelect: function (selectedDateTime) {
                startDateTextBox.datepicker('option', 'maxDate', endDateTextBox.datepicker('getDate'));
            }
        });
//        $('#province_div').hide();
//        $('#districts').hide();
        
        $('#facility_level').change(function(e) {
                    var v = $(this).val();
                    if(v == 2){
                        $('#province_div').show();
                        $('#districts').hide();
                    }
                    else if(v == 3 || v == 7)
                    {
                        $('#province_div').show();
                        $('#districts').show();
                    }
                    else{
                        $('#province_div').hide();
                        $('#districts').hide();
                        $("#province").val("0");
                        $("#district").val("0");
                    }
                });
        
        
    });
    
            function showDistricts(prov, stk) {
            if (stk != '' && prov != '')
            {
                $.ajax({
                    type: 'POST',
                    url: 'my_report_ajax_ssr.php',
                    data: {provId: prov, stkId: stk, distId: '<?php echo $selDist; ?>', showAll: 1},
                    success: function (data) {
                        $("#districts").html(data);
//                        $('#district').select2();
//                        $('#district').removeClass('form-control').addClass('select2me');
//                        $('#district').removeClass('input-sm').addClass('input-medium');
                    }
                });
            }
        }
        function showStores(dist,prov) {
            var stk = $('#stk_sel').val();
            var level = $('#facility_level option:selected').val();
//            var dist=$('#districts').val();
//            alert("test");
            if (stk != '')
           {
                $.ajax({
                    type: 'POST',
                    url: 'tower_report_ajax_ssr.php',
                    data: {provId: prov,distId: dist, stkId: stk, whId: '<?php echo $sel_wh; ?>', level : level},
                    success: function (data) {
                        $("#stores").html(data);
                        $('#warehouse').select2();
                        $('#warehouse').removeClass('form-control').addClass('select2me');
                        $('#warehouse').removeClass('input-sm').addClass('input-medium');
                    }
                });
            }
        }
        function showStores1() {
            var stk = $('#stk_sel').val();
            var level = $('#facility_level option:selected').val();
//            var dist=$('#districts').val();
//            alert("test");
            if (stk != '' && level == '1')
           {
                $.ajax({
                    type: 'POST',
                    url: 'tower_report_ajax_ssr.php',
                    data: {stkId: stk,level : level},
                    success: function (data) {
                        $("#stores").html(data);
                        $('#warehouse').select2();
                        $('#warehouse').removeClass('form-control').addClass('select2me');
                        $('#warehouse').removeClass('input-sm').addClass('input-medium');
                    }
                });
            }
        }
        function showStores2(prov) {
            var stk = $('#stk_sel').val();
            var level = $('#facility_level option:selected').val();
            var dist=$('#district option:selected').val();
//            alert("test");
            if (stk != '')
           {
                $.ajax({
                    type: 'POST',
                    url: 'tower_report_ajax_ssr.php',
                    data: {provId: prov, stkId: stk,distId: dist,  level : level},
                    success: function (data) {
                        $("#stores").html(data);
                        $('#warehouse').select2();
                        $('#warehouse').removeClass('form-control').addClass('select2me');
                        $('#warehouse').removeClass('input-sm').addClass('input-medium');
                    }
                });
            }
        }
    
</script>
