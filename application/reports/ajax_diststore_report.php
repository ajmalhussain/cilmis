<?php
include("../includes/classes/Configuration.inc.php");
include(APP_PATH . "includes/classes/db.php");
$date_from = $_REQUEST['date_from'];
$date_to = $_REQUEST['date_to'];
$province = $_REQUEST['province'];
if ((isset($_REQUEST['province']))&&(isset($_REQUEST['warehouse']))) {
    $qry = 
											
											"SELECT
	itminfo_tab.itm_name,
	stock_batch.batch_no,
	stock_batch.batch_expiry,
	SUM(stock_batch.Qty) AS Vials,
	itminfo_tab.generic_name,
	tbl_itemunits.UnitType,
	itminfo_tab.qty_carton AS qty_carton_old,
	stakeholder_item.quantity_per_pack AS qty_carton
FROM
	stock_batch
INNER JOIN itminfo_tab ON stock_batch.item_id = itminfo_tab.itm_id
LEFT JOIN tbl_itemunits ON itminfo_tab.itm_type = tbl_itemunits.UnitType
INNER JOIN stakeholder_item ON stock_batch.manufacturer = stakeholder_item.stk_id
WHERE
stock_batch.wh_id =  '".$_REQUEST['warehouse']."' AND
stakeholder_item.created_date BETWEEN '". $_REQUEST['date_from'] ."' AND '". $_REQUEST['date_to'] ."' 
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
        <table id="tbl" class="table table-condensed table-bordered">
            <thead>
                <tr>
                    <th style="text-align:center">S. No.</th>
                    <th style="text-align:center">Product</th>
                    <th style="text-align:center">Generic Name</th>
					 <th style="text-align:center">Batch No</th>
					  <th style="text-align:center">Batch Expiry</th>
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
                             <td><?php echo $row->generic_name; ?></td>
							 <td><?php echo $row->batch_no; ?></td>
							 <td><?php echo $row->batch_expiry; ?></td>
                            <td style="text-align:right;"><?php echo number_format($row->Vials); ?></td>
                            <td style="text-align:right;"><?php echo $row->UnitType; ?></td>
                            <td style="text-align:right;"><?php echo number_format($row->Vials / $row->qty_carton); ?></td>
                        </tr>
                        <?php
                        $i++;
                    }
                }
                ?>
                <!-- // Table row END -->
            </tbody>
            <tfoot>
                <tr>
				
                    <th colspan="5" style="text-align:right;">Total</th>
                    <th style="text-align:right;"><?php echo number_format($totalQty); ?></th>
                    <th>&nbsp;</th>
                    <th style="text-align:right;"><?php echo number_format($totalCartons); ?></th>
                </tr>
            </tfoot>
        </table>
        <div style="float:right; margin-top:10px;" id="printButt">
            <input type="button" name="print" value="Print" class="btn btn-warning" onclick="javascript:printCont();" />
        </div>

    </div>
    <?php
}?>