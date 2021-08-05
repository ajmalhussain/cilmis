<?php
/**
 * ajaxbatch
 * @package im
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
//Including AllClasses file
include("../includes/classes/AllClasses.php");
//Checking id
$po_id=$_REQUEST['po_id'];
$qry_total="SELECT 
purchase_order.shipment_quantity as ordered_qty
FROM
	purchase_order 
WHERE 
purchase_order.pk_id = $po_id 
";
$result=mysql_query($qry_total);
while ($row = mysql_fetch_array($result)) {
    $total_qty=$row['ordered_qty'];
}
$qry_received="SELECT
Sum(tbl_stock_detail.Qty) as qty_rcvd,
tbl_stock_master.shipment_id as po_id
FROM
tbl_stock_master
INNER JOIN tbl_stock_detail ON tbl_stock_master.PkStockID = tbl_stock_detail.fkStockID
WHERE 
tbl_stock_master.shipment_id = $po_id
GROUP BY
tbl_stock_master.shipment_id
";
 $res=mysql_query($qry_received);
 while ($row = mysql_fetch_array($res)) {
    $received_qty=$row['qty_rcvd'];
}
?>

<span style="font-size: 18px">Total quantity ordered:<b style="color:blue;"> <?php if(isset($total_qty))echo $total_qty; else { echo '0' ;} ?></b></span>
<br>
<span style="font-size: 18px">Received Quantity :<b style="color:blue;"> <?php if(isset($received_qty)) echo $received_qty; else { echo'0'; } ?></b></span>