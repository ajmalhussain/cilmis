<?php
//print_r($_REQUEST);
ini_set('max_execution_time', 0);

//Including files
include("../includes/classes/Configuration.inc.php");
include(APP_PATH . "includes/classes/db.php");
include(PUBLIC_PATH . "html/header.php");
include(PUBLIC_PATH . "/FusionCharts/Code/PHP/includes/FusionCharts.php");
include("../includes/classes/AllClasses.php");

$caption = 'what';
$downloadFileName = $caption . ' - ' . date('Y-m-d H:i:s');
//chart_id
$chart_id = 'b2';

//include AllClasses
//report id
$rptId = 'afpak_2';
//user province id
$selwh = $_REQUEST['warehouse'];

?>

<body class="page-header-fixed page-quick-sidebar-over-content">
    <div class="page-container">
        <?php
//Including top file
        include PUBLIC_PATH . "html/top.php";
//Including top_im file
        include PUBLIC_PATH . "html/top_im.php";
        ?>

        <div class="page-content-wrapper">
            <div class="page-content">

                <div class="row">
                    <div class="col-md-12" >
                        <center> <h3 class="page-title row-br-b-wp">District Supply Chain - <strong color="green">Current Stock Status</strong></h3></center>
                        <div class="widget" data-toggle="collapse-widget">
                            <div class="widget-head">
                                <h3 class="heading">Filter by</h3>
                            </div>
                            <div class="widget-body">
                                <?php
//sub_dist_form
                                include('afpak_filters.php');
                                ?>
                            </div>
                        </div>

                    </div>
                </div>

                <script language="Javascript" src="<?php echo PUBLIC_URL; ?>FusionCharts/Charts/FusionCharts.js"></script>
                <script language="Javascript" src="<?php echo PUBLIC_URL; ?>FusionCharts/themes/fusioncharts.theme.fint.js"></script>

                <div class="page-container">

                    <?php
                    if(!empty($_REQUEST['warehouse']))
                        {
                    
                        $qry = "SELECT
                                            itminfo_tab.itm_name,
                                            SUM(stock_batch.Qty) AS Vials,
                                            tbl_itemunits.UnitType,
                                            itminfo_tab.qty_carton as qty_carton_old,
                                            stakeholder_item.quantity_per_pack as qty_carton
                                    FROM
                                            stock_batch
                                    INNER JOIN itminfo_tab ON stock_batch.item_id = itminfo_tab.itm_id
                                    LEFT JOIN tbl_itemunits ON itminfo_tab.itm_type = tbl_itemunits.UnitType
                                    INNER JOIN stakeholder_item ON stock_batch.manufacturer = stakeholder_item.stk_id
                                    WHERE
                                            stock_batch.`wh_id` = '" . $_REQUEST['warehouse'] . "'
                                    GROUP BY
                                            itminfo_tab.itm_id
                                    ORDER BY
                                            itminfo_tab.itm_name";

                        //query result
                        $qryRes = mysql_query($qry);
                        $num = mysql_num_rows($qryRes);
                        ?>

                        <div id="content_print">
                            <style type="text/css" media="print">
                                @media print
                                {    
                                    #printButt
                                    {
                                        display: none !important;
                                    }
                                }
                            </style>
                            <?php
                            $rptName = 'Batch Management Summary';
                            //include header
                            ?>
                            <table id="" class="table table-condensed table-bordered">
                                <thead>
                                    <tr>
                                        <th style="text-align:center">S. No.</th>
                                        <th style="text-align:center">Product</th>
                                        <th style="text-align:center">Quantity</th>
                                        <th style="text-align:center">Unit</th>
                                        <th style="text-align:center">Cartons</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    //check num
                                    if ($num > 0) {
                                        $i = 1;
                                        $totalQty = $totalCartons = '';
                                        //fetch data from qryRes
                                        while ($row = mysql_fetch_object($qryRes)) {
                                            //total qty
                                            $totalQty += abs($row->Vials);
                                            //total cartons
                                            $totalCartons += abs($row->Vials) / $row->qty_carton;
                                            ?>
                                            <!-- Table row -->
                                            <tr>
                                                <td style="text-align:center;"><?php echo $i; ?></td>
                                                <td><?php echo $row->itm_name; ?></td>
                                                <td style="text-align:right;"><?php echo number_format($row->Vials); ?></td>
                                                <td style="text-align:right;"><?php echo $row->UnitType; ?></td>
                                                <td style="text-align:right;"><?php echo number_format($row->Vials / $row->qty_carton); ?></td>
                                            </tr>
                                <?php $i++;
                            }
                        } ?>
                                    <!-- // Table row END -->
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="2" style="text-align:right;">Total</th>
                                        <th style="text-align:right;"><?php echo number_format($totalQty); ?></th>
                                        <th>&nbsp;</th>
                                        <th style="text-align:right;"><?php echo number_format($totalCartons); ?></th>
                                    </tr>
                                </tfoot>
                            </table>
                            <div style="float:right; margin-top:10px;" id="printButt">
                                <input type="button" name="print" value="Print" class="btn btn-warning hide" onclick="javascript:printCont();" />
                            </div>

                        </div>
                        <?php } 
                        ?>
                </div>
            </div>
        </div>
    </div>
    <?php
//Including footer file
    include PUBLIC_PATH . "/html/footer.php";
    include ('combos.php');
    ?>
</body>
<script language="javascript">
              
                function printCont()
                {
                    window.print();
                }
</script>