<?php
ini_set('max_execution_time', 0);
/**
 * shipment
 * @package dashboard
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
//include Configuration
include("../includes/classes/Configuration.inc.php");
//login
Login();
if (isset($_REQUEST['submit_btn'])) {
    //echo '<pre>';print_r($_REQUEST);exit;
}

//include db
include(APP_PATH . "includes/classes/db.php");
//include  functions
include APP_PATH . "includes/classes/functions.php";
//include fusion chart
include(PUBLIC_PATH . "FusionCharts/Code/PHP/includes/FusionCharts.php");
//include header
include(PUBLIC_PATH . "html/header.php");
//funding source
$fundingSourceText = 'All Funding Sources';

//caption
$caption = 'Product wise Distribution and SOH';
//sub caption
$subCaption = '';
//downloadFileName 
$downloadFileName = 'a';
//chart id
$chart_id = 'distributionAndSOH';


$f_date = (!empty($_REQUEST['from_date']) ? $_REQUEST['from_date'] : date("Y-m") . '-01');
$t_date = (!empty($_REQUEST['to_date']) ? $_REQUEST['to_date'] : $f_date);
$from_date = date("Y-m-d", strtotime($f_date));
$to_date = date("Y-m-t", strtotime($t_date));

$time1 = strtotime($from_date);
$time2 = strtotime($to_date);
$my = date('mY', $time2);

$months_list = array(date('Y-m-01', $time1));

if ($f_date != $t_date) {
    while ($time1 < $time2) {
        $time1 = strtotime(date('Y-m-d', $time1) . ' +1 month');
        if (date('mY', $time1) != $my && ($time1 < $time2))
            $months_list[] = date('Y-m-01', $time1);
    }

    $months_list[] = date('Y-m-01', $time2);
}
$number_of_months = count($months_list);
//echo '<pre>';print_r($months_list);exit;

$province_arr = (!empty($_REQUEST['province']) ? $_REQUEST['province'] : '');
$stk_arr = (!empty($_REQUEST['stakeholder']) ? $_REQUEST['stakeholder'] : '');
$itm_arr_request = (!empty($_REQUEST['product']) ? $_REQUEST['product'] : '');

if (isset($_REQUEST['submit_btn'])) {
    $province = implode(',', $province_arr);
    $stk = implode(',', $stk_arr);
    $itm = implode(',', $itm_arr_request);
}
$where_clause = "";

if (!empty($province))
    $where_clause .= " AND tbl_warehouse.prov_id in (" . $province . ")  ";
if (!empty($stk))
    $where_clause .= " AND tbl_warehouse.stkid in (" . $stk . ")  ";
?>

<style>
    span.multiselect-native-select {
        position: relative
    }
    span.multiselect-native-select select {
        border: 0!important;
        clip: rect(0 0 0 0)!important;
        height: 1px!important;
        margin: -1px -1px -1px -3px!important;
        overflow: hidden!important;
        padding: 0!important;
        position: absolute!important;
        width: 1px!important;
        left: 50%;
        top: 30px
    }
    .multiselect-container {
        position: absolute;
        list-style-type: none;
        margin: 0;
        padding: 0
    }
    .multiselect-container .input-group {
        margin: 5px
    }
    .multiselect-container>li {
        padding: 0
    }
    .multiselect-container>li>a.multiselect-all label {
        font-weight: 700
    }
    .multiselect-container>li.multiselect-group label {
        margin: 0;
        padding: 3px 20px 3px 20px;
        height: 100%;
        font-weight: 700
    }
    .multiselect-container>li.multiselect-group-clickable label {
        cursor: pointer
    }
    .multiselect-container>li>a {
        padding: 0
    }
    .multiselect-container>li>a>label {
        margin: 0;
        height: 100%;
        cursor: pointer;
        font-weight: 400;
        padding: 3px 0 3px 30px
    }
    .multiselect-container>li>a>label.radio, .multiselect-container>li>a>label.checkbox {
        margin: 0
    }
    .multiselect-container>li>a>label>input[type=checkbox] {
        margin-bottom: 5px
    }


</style>

<body class="page-header-fixed page-quick-sidebar-over-content">
    <!--<div class="pageLoader"></div>-->
    <!-- BEGIN HEADER -->
    <SCRIPT LANGUAGE="Javascript" SRC="<?php echo PUBLIC_URL; ?>FusionCharts/Charts/FusionCharts.js"></SCRIPT>
    <SCRIPT LANGUAGE="Javascript" SRC="<?php echo PUBLIC_URL; ?>FusionCharts/themes/fusioncharts.theme.fint.js"></SCRIPT>

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
                        <div class="widget" data-toggle="">
                            <div class="widget-head">
                                <h3 class="heading">Filter by</h3>
                            </div>
                            <div class="widget-body collapse in">
                                <form name="frm" id="frm" action="" method="get">
                                    <table width="100%">
                                        <tbody>
                                            <tr>
                                                <td class="col-md-2">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label class="control-label">From</label>
                                                            <div class="form-group">
                                                                <input type="text" name="from_date" id="from_date"  class="form-control input-sm" value="<?php echo date('Y-m', strtotime($from_date)); ?>" required>
                                                            </div>
                                                        </div>
                                                    </div>

                                                </td>
                                                <td class="col-md-2">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label class="control-label">To</label>
                                                            <div class="form-group">
                                                                <input type="text" name="to_date" id="to_date"  class="form-control input-sm" value="<?php echo date('Y-m', strtotime($to_date)); ?>" required>
                                                            </div>
                                                        </div>
                                                    </div>

                                                </td>
                                                <td class="col-md-2">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label class="control-label">Province</label>
                                                            <select required name="province[]" id="province"  class="multiselect-ui form-control input-sm" multiple>
                                                                <?php
                                                                $queryprov = "SELECT
                                                                                                tbl_locations.PkLocID AS prov_id,
                                                                                                tbl_locations.LocName AS prov_title
                                                                                            FROM
                                                                                                tbl_locations
                                                                                            WHERE
                                                                                                LocLvl = 2
                                                                                                AND parentid IS NOT NULL
                                                                                                AND tbl_locations.LocType = 2";
                                                                //query result
                                                                $rsprov = mysql_query($queryprov) or die();

                                                                while ($rowprov = mysql_fetch_array($rsprov)) {
                                                                    if (in_array($rowprov['prov_id'], $province_arr)) {
                                                                        $sel = "selected='selected'";
                                                                        $prov_name[] = $rowprov['prov_title'];
                                                                    } else {
                                                                        $sel = "";
                                                                    }
                                                                    ?>
                                                                    <option value="<?php echo $rowprov['prov_id']; ?>" <?php echo $sel; ?>><?php echo $rowprov['prov_title']; ?></option>
                                                                    <?php
                                                                }
                                                                ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </td>

                                                <td class="col-md-2">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label class="control-label">Stakeholder</label>
                                                            <select  required name="stakeholder[]" id="stakeholder" class="multiselect-ui form-control input-sm" multiple>
                                                                <?php
                                                                $queryprov = "SELECT
                                                                            stakeholder.stkname,
                                                                            stakeholder.stkid
                                                                            FROM
                                                                            stakeholder
                                                                            WHERE
                                                                            stakeholder.stk_type_id = 0 AND
                                                                            stakeholder.lvl = 1 AND
                                                                            stakeholder.is_reporting = 1
                                                                        ";
                                                                //query result
                                                                $rsprov = mysql_query($queryprov) or die();

                                                                while ($rowprov = mysql_fetch_array($rsprov)) {
                                                                    if (in_array($rowprov['stkid'], $stk_arr)) {
                                                                        $sel = "selected='selected'";
                                                                        $stk_name[] = $rowprov['stkname'];
                                                                    } else {
                                                                        $sel = "";
                                                                    }
                                                                    ?>
                                                                    <option value="<?php echo $rowprov['stkid']; ?>" <?php echo $sel; ?>><?php echo $rowprov['stkname']; ?></option>
                                                                    <?php
                                                                }
                                                                ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </td>

                                                <td class="col-md-2">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label class="control-label">Product</label>
                                                            <select required  name="product[]" id="product" class="multiselect-ui form-control input-sm" multiple>
                                                                <?php
                                                                $queryprov = "SELECT
                                                                                itminfo_tab.itm_id,
                                                                                itminfo_tab.itm_name
                                                                                FROM
                                                                                itminfo_tab
                                                                                WHERE
                                                                                itminfo_tab.itm_category = 1 AND
                                                                                itminfo_tab.method_type IS NOT NULL
                                                                                ORDER BY
                                                                                itminfo_tab.method_rank ASC
                                                                        ";
//query result
                                                                $rsprov = mysql_query($queryprov) or die();

                                                                while ($rowprov = mysql_fetch_array($rsprov)) {
                                                                    if (in_array($rowprov['itm_id'], $itm_arr_request)) {
                                                                        $sel = "selected='selected'";
                                                                        $itm_name[] = $rowprov['itm_name'];
                                                                    } else {
                                                                        $sel = "";
                                                                    }
                                                                    ?>
                                                                    <option value="<?php echo $rowprov['itm_id']; ?>" <?php echo $sel; ?>><?php echo $rowprov['itm_name']; ?></option>
                                                                    <?php
                                                                }
                                                                ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="col-md-2">

                                                    <label class="control-label">&nbsp;</label>
                                                    <input name="submit_btn" type="submit" class="btn btn-succes" value="Go">

                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class=" col-md-12 right">
                        <a id="btnExport" onclick="javascript:xport.toXLS('DormantFacilities');"><img style="cursor:pointer;" src="<?php echo PUBLIC_URL; ?>images/excel-32.png"  title="Export to Excel" /></a>
                    </div>

                </div>
                <?php
                if (isset($_REQUEST['submit_btn'])) {
                    $where_item = '';
                    if (!empty($itm))
                        $where_item = " AND itminfo_tab.itm_id in (" . $itm . ")";

                    $qry = "SELECT
	tbl_hf_data.reporting_date AS rep_date,
	itminfo_tab.itm_name,
	tbl_warehouse.wh_name,
	tbl_hf_data.warehouse_id AS wh_id,
	itminfo_tab.itm_id,
	tbl_warehouse.prov_id,
	tbl_warehouse.stkid,
	tbl_warehouse.dist_id,
	prov.LocName AS prov_name,
	dist.LocName AS district_name,
	stakeholder.stkname,
	SUM(tbl_hf_data.issue_balance) AS consum,
        SUM(tbl_hf_data.closing_balance) AS soh
FROM
	tbl_warehouse
INNER JOIN stakeholder ON stakeholder.stkid = tbl_warehouse.stkofficeid
INNER JOIN tbl_hf_data ON tbl_warehouse.wh_id = tbl_hf_data.warehouse_id
INNER JOIN tbl_locations AS dist ON tbl_warehouse.dist_id = dist.PkLocID
INNER JOIN tbl_locations AS prov ON tbl_warehouse.prov_id = prov.PkLocID
INNER JOIN tbl_hf_type ON tbl_warehouse.hf_type_id = tbl_hf_type.pk_id
INNER JOIN itminfo_tab ON tbl_hf_data.item_id = itminfo_tab.itm_id
WHERE
tbl_hf_data.reporting_date BETWEEN '$from_date' AND '$to_date'
    $where_clause $where_item
    /*AND tbl_warehouse.hf_type_id NOT IN (5, 2, 3, 9, 6, 7, 8, 12, 10, 11)*/                                        
    AND tbl_warehouse.wh_id NOT IN (
        SELECT
                warehouse_status_history.warehouse_id
        FROM
                warehouse_status_history
        WHERE
                warehouse_status_history.reporting_month BETWEEN '$from_date'
        AND '$to_date'
        AND warehouse_status_history.`status` = 0
    )                                
    GROUP BY
	tbl_hf_data.warehouse_id,itminfo_tab.itm_id
HAVING consum = 0 AND soh = 0";

                    $res = mysql_query($qry);
//$num = mysql_num_rows($res);
                    $dorment_facility = array();
                    while ($row = mysql_fetch_assoc($res)) {
                        $dorment_facility[$row['prov_id']][$row['itm_id']] += 1;
                        $dorment_facility['all'][$row['itm_id']] += 1;

                        $data[$row['prov_name']][$row['stkname']][$row['itm_name']][] = $row['wh_id'];
                    }
                }

                foreach ($data as $province => $stkdata) {
                    //$province_offset[$province] = count($stkdata);
                    foreach ($stkdata as $stk => $itmdata) {
                        foreach ($itmdata as $item => $whdata) {
                            $province_offset[$province] += count($item);
                            $stk_offset[$province][$stk] += count($item);
                        }
                    }
                }

                echo "<table id='DormantFacilities' name='tbl' class='table table-bordered table-condensed' border=''><tr><th>Province</th><th>Stakeholder</th><th>Item</th><th>No of Dorment Facilities</th></tr>";
                foreach ($data as $province => $stkdata) {                    
                    foreach ($stkdata as $stk => $itmdata) {                        
                        foreach ($itmdata as $item => $whdata) {
                            echo "<tr><td>" . $province . "</td>";
                            echo "<td>" . $stk . "</td>";
                            echo "<td>" . $item . "</td>";
                            echo "<td>" . count($whdata) . "</td></tr>";
                        }
                    }
                }
                echo "</table>";
                
//                echo "<table id='DormantFacilities' name='tbl' class='table table-bordered table-condensed' border=''><tr><td>Province</td><td>Stakeholder</td><td>Item</td><td>No of Dorment Facilities</td></tr>";
//                foreach ($data as $province => $stkdata) {
//                    echo "<tr><td rowspan='" . $province_offset[$province] . "'>" . $province . "</td>";
//                    foreach ($stkdata as $stk => $itmdata) {
//                        echo "<td rowspan='" . $stk_offset[$province][$stk] . "'>" . $stk . "</td>";
//                        foreach ($itmdata as $item => $whdata) {
//                            echo "<td>" . $item . "</td>";
//                            echo "<td>" . count($whdata) . "</td></tr>";
//                        }
//                    }
//                }
//                echo "</table>";
                ?>
            </div>
        </div>
    </div>

    <?php
//include footer
    include PUBLIC_PATH . "/html/footer.php";
    ?>
    <script src="<?= PUBLIC_URL ?>js/bootstrap_multiselect.js"></script>


    <script>
                            $(function () {

                                $('.col_btn').click(function () {
                                    var hide_cls = $(this).attr('hide_class');
                                    console.log(hide_cls);
                                    $("." + hide_cls).hide();
                                    var colspan = $(".months_td").attr('colspan');
                                    colspan = colspan - 1;
                                    $(".months_td").attr('colspan', colspan);
                                    $(this).hide();
                                });

                                $('#show_breakdown').click(function () {
                                    $(".prov_row").first().toggle("fast", function showNext() {
                                        $(this).next(".prov_row").toggle("fast", showNext);
                                    });
                                    $(this).hide();
                                    toastr.info('Please click on the Povince name to have District Wise Breakdown of data.');
                                });
                                $('#from_date, #to_date').datepicker({
                                    dateFormat: "yy-mm",
                                    constrainInput: false,
                                    changeMonth: true,
                                    changeYear: true,
                                    maxDate: ''
                                });

                                $('.multiselect-ui').multiselect({
                                    includeSelectAllOption: true
                                });



                            })




                            function showDrillDown(prov, prov_name, from_date, prod_id, prod_name, indicator, stk, stk_name) {

                                var url = 'dev_results_sdp_drilldown.php';
                                var div_id = "drilldown_div";
                                var dataStr = '';
                                dataStr += "province=" + prov + "&prov_name=" + prov_name + "&from_date=" + from_date + "&prod_id=" + prod_id + "&prod_name=" + prod_name + "&indicator=" + indicator + "&stk=" + stk + "&stk_name=" + stk_name;

                                $('#' + div_id).html("<center><div id='loadingmessage'><img src='<?php echo PUBLIC_URL; ?>images/ajax-loader.gif'/></div></center>");

                                $.ajax({
                                    type: "POST",
                                    url: '<?php echo APP_URL; ?>reports/' + url,
                                    data: dataStr,
                                    dataType: 'html',
                                    success: function (data) {
                                        $("#" + div_id).html(data);
                                    }
                                });

                                $('html, body').animate({scrollTop: $('#' + div_id).offset().top}, 'slow');

                            }
    </script>
    <script>
        var xport = {
            _fallbacktoCSV: true,
            toXLS: function (tableId, filename) {
                this._filename = (typeof filename == 'undefined') ? tableId : filename;

                //var ieVersion = this._getMsieVersion();
                //Fallback to CSV for IE & Edge
                if ((this._getMsieVersion() || this._isFirefox()) && this._fallbacktoCSV) {
                    return this.toCSV(tableId);
                } else if (this._getMsieVersion() || this._isFirefox()) {
                    alert("Not supported browser");
                }

                //Other Browser can download xls
                var htmltable = document.getElementById(tableId);
                var html = htmltable.outerHTML;

                this._downloadAnchor("data:application/vnd.ms-excel" + encodeURIComponent(html), 'xls');
            },
            toCSV: function (tableId, filename) {
                this._filename = (typeof filename === 'undefined') ? tableId : filename;
                // Generate our CSV string from out HTML Table
                var csv = this._tableToCSV(document.getElementById(tableId));
                // Create a CSV Blob
                var blob = new Blob([csv], {type: "text/csv"});

                // Determine which approach to take for the download
                if (navigator.msSaveOrOpenBlob) {
                    // Works for Internet Explorer and Microsoft Edge
                    navigator.msSaveOrOpenBlob(blob, this._filename + ".csv");
                } else {
                    this._downloadAnchor(URL.createObjectURL(blob), 'csv');
                }
            },
            _getMsieVersion: function () {
                var ua = window.navigator.userAgent;

                var msie = ua.indexOf("MSIE ");
                if (msie > 0) {
                    // IE 10 or older => return version number
                    return parseInt(ua.substring(msie + 5, ua.indexOf(".", msie)), 10);
                }

                var trident = ua.indexOf("Trident/");
                if (trident > 0) {
                    // IE 11 => return version number
                    var rv = ua.indexOf("rv:");
                    return parseInt(ua.substring(rv + 3, ua.indexOf(".", rv)), 10);
                }

                var edge = ua.indexOf("Edge/");
                if (edge > 0) {
                    // Edge (IE 12+) => return version number
                    return parseInt(ua.substring(edge + 5, ua.indexOf(".", edge)), 10);
                }

                // other browser
                return false;
            },
            _isFirefox: function () {
                if (navigator.userAgent.indexOf("Firefox") > 0) {
                    return 1;
                }

                return 0;
            },
            _downloadAnchor: function (content, ext) {
                var anchor = document.createElement("a");
                anchor.style = "display:none !important";
                anchor.id = "downloadanchor";
                document.body.appendChild(anchor);

                // If the [download] attribute is supported, try to use it

                if ("download" in anchor) {
                    anchor.download = this._filename + "." + ext;
                }
                anchor.href = content;
                anchor.click();
                anchor.remove();
            },
            _tableToCSV: function (table) {
                // We'll be co-opting `slice` to create arrays
                var slice = Array.prototype.slice;

                return slice
                        .call(table.rows)
                        .map(function (row) {
                            return slice
                                    .call(row.cells)
                                    .map(function (cell) {
                                        return '"t"'.replace("t", cell.textContent);
                                    })
                                    .join(",");
                        })
                        .join("\r\n");
            }
        };

    </script>
</body>
<!-- END BODY -->
</html>