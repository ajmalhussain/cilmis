<?php
ini_set('max_execution_time',0);
include("../includes/classes/AllClasses.php");
include(APP_PATH . "includes/report/FunctionLib.php");
include(PUBLIC_PATH . "html/header.php");

$date = date("Y-m-01");
$from_date = date("Y-m-01");
$inc= 'ignore';

if (!empty($_REQUEST['to_date']))
    $date = $_REQUEST['to_date'];

if (!empty($_REQUEST['from_date']))
    $from_date = $_REQUEST['from_date'];

if(!empty($_REQUEST['inc']) )
    $inc= $_REQUEST['inc'];



$qry2 = " SELECT
            stakeholder.stkid,
            stakeholder.stkname
            FROM
            stakeholder
            WHERE
            stakeholder.lvl = 1 AND
            stakeholder.stk_type_id IN (0,1)
 ";
$qryRes2 = mysql_query($qry2);
$stakeholders= array();
$c=1;
while ($row = mysql_fetch_assoc($qryRes2)) {
    $stakeholders[$row['stkid']] = $row['stkname'];
}

?>
<style>
    .objbox {
        overflow-x: hidden !important;
    }
</style>

<body class="page-header-fixed page-quick-sidebar-over-content" onLoad="">
    <div class="page-container">
<?php
//include top
include PUBLIC_PATH . "html/top.php";
//include top_im
include PUBLIC_PATH . "html/top_im.php";
?>
        <div class="page-content-wrapper">
            <div class="page-content">

                <div class="widget" data-toggle="collapse-widget">
                    <div class="widget-head">
                        <h3 class="heading">Filter by</h3>
                    </div>
                    <div class="widget-body">
                        <div class="row">
                            <div class="col-md-12">


                                <form id="aform" action="">

                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label class="control-label">From Date</label>
                                            <div class="form-group">
                                                <input type="text" name="from_date" id="from_date"  class="form-control input-sm" value="<?php echo $from_date; ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label class="control-label">To Date</label>
                                            <div class="form-group">
                                                <input type="text" name="to_date" id="to_date"  class="form-control input-sm" value="<?php echo $date; ?>">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label">Facility Types inclusion/exclusion: </label>
                                            <div class="form-group">
                                                <select class="form-control input-sm" id="inc" name="inc">
                                                    <option value="ignore" <?=(($inc=='ignore')?' selected ':'')?> >Exclude the facility types ( as in cLMIS reports )</option>
                                                    <option value="include" <?=(($inc=='include')?' selected ':'')?> >Include the facility types ( Have a full overview)</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label class="control-label">&nbsp;</label>
                                            <div class="form-group">
                                                <button type="submit" name="submit" class="btn btn-primary input-sm">Go</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>

                        </div>
                    </div>
                </div>
<?php ?>
                <div class="row">
                    <div id="div1" class="col-md-12">
                        <table id="cLMIS_table" border="1" width="100%" cellpadding="0" cellspacing="0" style="" class="table table-bordered table-condensed">
                            <div class="col-md-12">
                                <div class="col-md-11 h4 center">SDPs Stock Out / Availability Comparisons - Based On Multiple Parameters</div>
                                <div class="col-md-11  center"> 
                                    <div class="note note-danger">
                                    Disclaimer : This report excludes following facility type calculations (unless included ,using the dropdown above) : MSU,Social Mobilizer,RHS-B,RMPS,Hakeems,Homeopaths,PLDs,TBAs,Counters,DDPs
                                    </div>
                                </div>
                                <div class=" col-md-1 right">
                                    <a id="btnExport" onclick="javascript:xport.toCSV('cLMIS_table');"><img style="cursor:pointer;" src="<?php echo PUBLIC_URL; ?>images/excel-32.png"   title="Export to Excel" /></a>
                                </div>
                            </div>    
                            <tr class="info">
                                <td colspan="9" > </td>
                                
                                <td colspan="4">Based On SOH = Zero</td>
                                
                                <td colspan="4">Based On MOS = Zero</td>
                                
                                <td colspan="4">Based On MOS < 0.5</td>
                            </tr>
                            <tr class="info">
                                <td>#</td>
                                <td>Province</td>
                                <td>Stakeholder</td>
                                <td>District</td>
                                <td>Product</td>
                                
                                <td>Year-Month</td>
                                <td>Reported SDPs</td>
                                
                                <td>Consumption</td>
                                <td>CYP</td>
                                
                                <td>Stocked Out</td>
                                <td>Available At</td>
                                <td>SO Rate</td>
                                <td>SA Rate</td>
                                
                                <td>Stocked Out</td>
                                <td>Available At</td>
                                <td>SO Rate</td>
                                <td>SA Rate</td>
                                
                                <td>Stocked Out</td>
                                <td>Available At</td>
                                <td>SO Rate</td>
                                <td>SA Rate</td>
                            </tr>
                            <?php
                            $qry = "SELECT
                                            Sum(1) AS total_sdps,
                                            Sum(if(tbl_hf_data.closing_balance>0,1,0)) AS stock_available_at_sdps_soh,
                                            Sum(if(tbl_hf_data.closing_balance<=0,1,0)) AS stock_outs_soh,
                                            sum(if(tbl_hf_data.closing_balance<=0,1,0))*100 / sum(1) AS so_rate_soh,
                                            Sum(if(tbl_hf_data.closing_balance>0,1,0))*100 / sum(1) AS availability_rate_soh,

Sum(if(IFNULL ((tbl_hf_data.closing_balance/tbl_hf_data.avg_consumption),0)>0,1,0)) AS stock_available_at_sdps_mos,
Sum(if(IFNULL ((tbl_hf_data.closing_balance/tbl_hf_data.avg_consumption),0)<=0,1,0)) AS stock_outs_mos,
sum(if(IFNULL ((tbl_hf_data.closing_balance/tbl_hf_data.avg_consumption),0)<=0,1,0))*100 / sum(1) AS so_rate_mos,
Sum(if(IFNULL ((tbl_hf_data.closing_balance/tbl_hf_data.avg_consumption),0)>0,1,0))*100 / sum(1) AS availability_rate_mos,


Sum(if(IFNULL ((tbl_hf_data.closing_balance/tbl_hf_data.avg_consumption),0)>=0.5,1,0)) AS stock_available_at_sdps_mos_05,
Sum(if(IFNULL ((tbl_hf_data.closing_balance/tbl_hf_data.avg_consumption),0)<0.5,1,0)) AS stock_outs_mos_05,
sum(if(IFNULL ((tbl_hf_data.closing_balance/tbl_hf_data.avg_consumption),0)<0.5,1,0))*100 / sum(1) AS so_rate_mos_05,
Sum(if(IFNULL ((tbl_hf_data.closing_balance/tbl_hf_data.avg_consumption),0)>=0.5,1,0))*100 / sum(1) AS availability_rate_mos_05,

Sum(tbl_hf_data.issue_balance) AS total_consumption,
Sum(tbl_hf_data.issue_balance)*provincial_cyp_factors.cyp_factor AS cyp,


                                            tbl_warehouse.prov_id,
                                            tbl_warehouse.stkid,
                                            tbl_hf_data.item_id,
                                            tbl_warehouse.dist_id,
                                            tbl_locations.LocName as district_name,
                                            p.LocName as province_name,
                                            itminfo_tab.itm_name,
                                            tbl_hf_data.reporting_date
                                        FROM
                                            tbl_hf_data
                                        INNER JOIN tbl_warehouse ON tbl_hf_data.warehouse_id = tbl_warehouse.wh_id
                                        INNER JOIN tbl_locations ON tbl_warehouse.dist_id = tbl_locations.PkLocID
                                        INNER JOIN tbl_locations AS p ON tbl_warehouse.prov_id = p.PkLocID
                                        INNER JOIN itminfo_tab ON tbl_hf_data.item_id = itminfo_tab.itm_id
                                        LEFT JOIN provincial_cyp_factors ON tbl_warehouse.prov_id = provincial_cyp_factors.province_id 
                                                    AND itminfo_tab.itm_id = provincial_cyp_factors.item_id 
                                                    AND tbl_warehouse.stkid = provincial_cyp_factors.stakeholder_id

                                        WHERE
                                            tbl_hf_data.reporting_date BETWEEN '" . $from_date . "' AND '" . $date . "' AND
                                            itminfo_tab.itm_category = 1 AND
                                            tbl_warehouse.stkid IN (1, 2, 7,9, 73) ";
                        if(!empty($_REQUEST['inc']) && $_REQUEST['inc']=='ignore'){
                            $qry .= " AND tbl_warehouse.hf_type_id NOT IN (5, 2, 3, 9, 6, 7, 8, 12, 10, 11) ";
                        }    
                        
                        $qry .= "
                                        GROUP BY
                                            tbl_warehouse.prov_id,
                                            tbl_warehouse.stkid,
                                            tbl_warehouse.dist_id,
                                            tbl_hf_data.reporting_date,
                                            tbl_hf_data.item_id
                                        ORDER BY
                                            tbl_warehouse.prov_id,
                                            tbl_warehouse.stkid,
                                            tbl_warehouse.dist_id,
                                            tbl_hf_data.reporting_date,
                                            tbl_hf_data.item_id
                                    ";
//                            echo $qry;exit;
                            $qryRes = mysql_query($qry);
                            $data = array();
                            $c=1;
                            while ($row = mysql_fetch_assoc($qryRes)) {
                                echo '<tr>';
                                echo '<td>'.$c++.'</td>';
                                echo '<td>'.$row['province_name'].'</td>';
                                echo '<td>'.$stakeholders[$row['stkid']].'</td>';
                                echo '<td>'.$row['district_name'].'</td>';
                                echo '<td>'.$row['itm_name'].'</td>';
                                
                                echo '<td align="right">'.date('Y-M',strtotime($row['reporting_date'])).'</td>';
                                echo '<td align="right">'.$row['total_sdps'].'</td>';
                                
                                echo '<td align="right">'.$row['total_consumption'].'</td>';
                                echo '<td align="right">'.number_format($row['cyp'],1).'</td>';
                                
                                echo '<td align="right">'.$row['stock_outs_soh'].'</td>';
                                echo '<td align="right">'.$row['stock_available_at_sdps_soh'].'</td>';
                                echo '<td align="right">'. number_format($row['so_rate_soh'],2).'</td>';
                                echo '<td align="right">'. number_format($row['availability_rate_soh'],2).'</td>';
                                
                                $this_cls="  ";
                                if($row['stock_outs_mos']<>$row['stock_outs_soh'])
                                {
                                    $this_cls=" info ";
                                }
                                
                                echo '<td align="right" class="'.$this_cls.'">'.$row['stock_outs_mos'].'</td>';
                                echo '<td align="right">'.$row['stock_available_at_sdps_mos'].'</td>';
                                echo '<td align="right">'. number_format($row['so_rate_mos'],2).'</td>';
                                echo '<td align="right">'. number_format($row['availability_rate_mos'],2).'</td>';
                                
                                
                                $this_cls="  ";
                                if($row['stock_outs_mos_05']<>$row['stock_outs_soh'])
                                {
                                    $this_cls=" info ";
                                }
                                echo '<td align="right" class="'.$this_cls.'">'.$row['stock_outs_mos_05'].'</td>';
                                echo '<td align="right">'.$row['stock_available_at_sdps_mos_05'].'</td>';
                                echo '<td align="right">'. number_format($row['so_rate_mos_05'],2).'</td>';
                                echo '<td align="right">'. number_format($row['availability_rate_mos_05'],2).'</td>';
                                echo '</tr>';
                            }
                            ?>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- END FOOTER -->
                            <?php
                            include PUBLIC_PATH . "/html/footer.php";
                            include PUBLIC_PATH . "/html/reports_includes.php";
                            ?>
    <script>
        $(function () {
            $('#from_date').datepicker({
                dateFormat: "yy-mm-dd",
                constrainInput: false,
                changeMonth: true,
                changeYear: true,
                minDate: new Date( 2016, 9, 1 )
                });
            $('#to_date').datepicker({
                dateFormat: "yy-mm-dd",
                constrainInput: false,
                changeMonth: true,
                changeYear: true,
                minDate: new Date( 2016, 9, 1 )

<?php


if (!empty($date)) {
    $d1 = explode('-', $date);
    echo ' ,setDate: new Date(' . $d1[0] . ', ' . $d1[1] . ',' . $d1[2] . ') ';
}
?>

            });
        });
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
</html>