<?php
//echo '<pre>';print_r($_REQUEST);exit;
ini_set('max_execution_time',0);
include("../includes/classes/AllClasses.php");
include(APP_PATH . "includes/report/FunctionLib.php");
include(PUBLIC_PATH . "html/header.php");

$date = date("Y-m-01");
//$from_date = date("Y-m-01");
$inc= 'ignore';

if (!empty($_REQUEST['to_date']))
    $date = $_REQUEST['to_date'];


$province_arr = (!empty($_REQUEST['province'])?$_REQUEST['province']:'');
$province = implode(',',$province_arr);
//echo $province;exit;
//if (!empty($_REQUEST['from_date']))
//    $from_date = $_REQUEST['from_date'];

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
$qry2 = " SELECT
tbl_locations.PkLocID,
tbl_locations.LocName
FROM
tbl_locations
WHERE
tbl_locations.LocLvl <= 3

 ";
$qryRes2 = mysql_query($qry2);
$locations= array();
$c=1;
while ($row = mysql_fetch_assoc($qryRes2)) {
    $locations[$row['PkLocID']] = $row['LocName'];
}

?>
<style>
    .objbox {
        overflow-x: hidden !important;
    }
    .fill_cell_bg {
/*  width: 25%;   only for demo, not really required */
  background-image: linear-gradient(to right, rgba(224, 69, 69, 1) 0%, rgba(224, 69, 69, 1) 100%);  /* your gradient */
  background-repeat: no-repeat;  /* don't remove */
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
                                            <label class="control-label">Month</label>
                                            <div class="form-group">
                                                <input type="text" name="to_date" id="to_date"  class="form-control input-sm" value="<?php echo $date; ?>">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label">Province</label>
                                                <select required name="province[]" id="province"  multiple class="multiselect-ui form-control input-sm"  >
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
                                                            if (in_array($rowprov['prov_id'],$province_arr)) {
                                                                $sel = "selected='selected'";
                                                                $prov_name[]=$rowprov['prov_title'];
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
                                     

                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label class="control-label">&nbsp;</label>
                                            <div class="form-group">
                                                <button type="submit" name="submit" value="Submit" class="btn btn-primary input-sm">Go</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>

                        </div>
                    </div>
                </div>
<?php

?>
                <div class="row">
                    <div id="div1" class="col-md-12">
                        <table id="cLMIS_table" border="1" width="100%" cellpadding="0" cellspacing="0" style="" class="table table-bordered table-condensed">
                            <div class="col-md-12">
                                <div class="col-md-11 h4 center">SDPs Stocked Out - Based on 'Atleast 3 methods availability'</div>
                                <div class="col-md-11  center"> 
                                    <div class="note note-warning">Due to heavy data , this report is limited to fetch data of single month.</div>
                                </div>
                                <div class=" col-md-1 right">
                                    <a id="btnExport" onclick="javascript:xport.toCSV('cLMIS_table');"><img style="cursor:pointer;" src="<?php echo PUBLIC_URL; ?>images/excel-32.png"   title="Export to Excel" /></a>
                                </div>
                            </div>    
                            <tr class="info">
                                <td colspan="6" > </td>
                                
                                <td colspan="1">No of available facilities</td>
                                
                                <td colspan="1">No of stocked out facilities</td>
                                
                            </tr>
                            <tr class="info">
                                <td>#</td>
                                <td>Province</td>
                                <td>Stakeholder</td>
                                <td>District</td>
                                
                                <td>Year-Month</td>
                                <td>Reported SDPs</td>
                                <td>Atleast 3 Methods Available</td>
                                <td>Not Even 3 Methods Available</td>
                                
                            </tr>
                            <?php
                            if(isset($_REQUEST['submit']) && $_REQUEST['submit']=='Submit'){
//                                echo 'what';exit;
                            $qry = "SELECT 
                            A.prov_id,
                            A.dist_id,
                            A.stkid,
                            A.reporting_date,
                            sum(1) AS reported_sdps,
                            sum(IF (
                                                    A.no_of_prods_avail >= 3 ,
                                                    1,
                                                    0
                                            )) AS having_atleast_three_prods_available,
                            sum(IF (
                                                    A.no_of_prods_avail < 3 ,
                                                    1,
                                                    0
                                            )) AS so_with_not_even_three_prods
                            FROM
                            (
                            SELECT
                            sum(1) AS total_prods,
                            sum(IF (
                                                    tbl_hf_data.closing_balance > 0,
                                                    1,
                                                    0
                                            )) AS no_of_prods_avail,
                            tbl_warehouse.prov_id,
                            tbl_warehouse.stkid,
                            tbl_warehouse.dist_id,
                            tbl_hf_data.reporting_date,
                            tbl_hf_data.warehouse_id
                            FROM
                            tbl_hf_data
                            INNER JOIN tbl_warehouse ON tbl_hf_data.warehouse_id = tbl_warehouse.wh_id
                            WHERE
                            tbl_hf_data.reporting_date = '".$date."' AND 
                            tbl_warehouse.stkid in (1,2,7,9,73) AND 
                            tbl_warehouse.prov_id in ( ".$province.")
                            group by
                            warehouse_id,
                            reporting_date
                            ) A
                            group by
                            A.prov_id,
                            A.stkid,
                            A.dist_id,
                            A.reporting_date

                            ORDER By 

                            A.prov_id,
                            A.stkid,
                            A.dist_id,
                            A.reporting_date "; 
                         
//                            echo $qry;exit;
                            $qryRes = mysql_query($qry);
                            $data = array();
                            $c=1;
                            while ($row = mysql_fetch_assoc($qryRes)) {
                                echo '<tr>';
                                echo '<td>'.$c++.'</td>';
                                echo '<td>'.$locations[$row['prov_id']].'</td>';
                                echo '<td>'.$stakeholders[$row['stkid']].'</td>';
                                echo '<td>'.$locations[$row['dist_id']].'</td>';
                                
                                echo '<td align="right">'.date('Y-M',strtotime($row['reporting_date'])).'</td>';
                                echo '<td align="right">'.$row['reported_sdps'].'</td>';
                                echo '<td align="right">'.$row['having_atleast_three_prods_available'].'</td>';
                                echo '<td align="right" class="fill_cell_bg" style=\'background-size: '.$row['so_with_not_even_three_prods'].'% 100%\'>'.$row['so_with_not_even_three_prods'].'</td>';
                                echo '</tr>';
                            }
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
//            $('#from_date').datepicker({
//                dateFormat: "yy-mm-dd",
//                constrainInput: false,
//                changeMonth: true,
//                changeYear: true,
//                minDate: new Date( 2016, 9, 1 )
//                });
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