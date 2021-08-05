<?php
//echo '<pre>';print_r($_REQUEST);
include("../includes/classes/AllClasses.php");

$wh_id      = $_REQUEST['wh_id'];
$item_id    = $_REQUEST['item_id'];
$req_type    = $_REQUEST['req_type'];


if($req_type == 'Ordered')
{
    $sq = "SELECT
     
        purchase_order.pk_id,
        purchase_order.po_date,
        purchase_order.po_number,
        purchase_order.reference_number,
        purchase_order_product_details.item_id,
        purchase_order.po_date,
        purchase_order_product_details.shipment_quantity,
        purchase_order_product_details.unit_price,
        purchase_order.`status` 
    FROM
        purchase_order 
INNER JOIN purchase_order_product_details ON purchase_order.pk_id = purchase_order_product_details.po_master_id
    WHERE
            purchase_order.wh_id = $wh_id
    AND purchase_order_product_details.item_id = $item_id
    AND purchase_order.status NOT IN ('Cancelled','Received')";
//echo $sq;
    $res  =  mysql_query($sq);
     $response ="";
    $response .="<h3 align=\"center\">Ordered</h3>";
    $response .="<table class=\"table table-bordered table-condensed\">
                    <tr class=\"bg-grey\">
                    <td>#</td>
                    <td>PO Date</td>
                    <td>PO No</td>
                    <td>Unit Price</td>
                    <td>Qty Ordered</td>
                    </tr>";    
    $c=1;
    while($row = mysql_fetch_assoc($res)){
        $response .="<tr>";
        $response .="<td>".$c++."</td>";
        $response .="<td>".date('Y-M-d',strtotime($row['po_date']))."</td>";
        $response .="<td>".$row['po_number']."</td>";
        $response .="<td align=\"right\">".number_format($row['unit_price'],2)."</td>";
        $response .="<td align=\"right\">".number_format($row['shipment_quantity'])."</td>";
        $response .="</tr>";
    }    
}
elseif($req_type == 'Received')
{
    $sq = "SELECT
            tbl_stock_detail.Qty AS rcvd,
            purchase_order.pk_id,
            purchase_order.po_date,
            purchase_order.po_number,
            purchase_order.reference_number,
            tbl_stock_detail.PkDetailID,
            tbl_stock_master.TranNo,
            tbl_stock_master.TranDate
        FROM
            purchase_order
INNER JOIN purchase_order_product_details ON purchase_order.pk_id = purchase_order_product_details.po_master_id
        LEFT JOIN tbl_stock_master ON purchase_order.pk_id = tbl_stock_master.shipment_id
        LEFT JOIN tbl_stock_detail ON tbl_stock_master.PkStockID = tbl_stock_detail.fkStockID
        WHERE
                purchase_order.wh_id = $wh_id
        AND purchase_order_product_details.item_id = $item_id
        AND purchase_order.status NOT IN ('Cancelled','Received')";

    $res  =  mysql_query($sq);
     $response ="";
    $response .="<h3 align=\"center\">Received</h3>";
    $response .="<table class=\"table table-bordered table-condensed\">
                    <tr class=\"bg-info\">
                    <td>#</td>
                    <td>PO Date</td>
                    <td>PO No</td>
                    <td>Receiving Voucher</td>
                    <td>Received Date</td>
                    <td>Received Qty</td>
                 </tr>";    
    $c=1;
    while($row = mysql_fetch_assoc($res)){
        $response .="<tr>";
        $response .="<td>".$c++."</td>";
        $response .="<td>".date('Y-M-d',strtotime($row['po_date']))."</td>";
        $response .="<td>".$row['po_number']."</td>";
        $response .="<td>".$row['TranNo']."</td>";
        $response .="<td>".date('Y-M-d',strtotime($row['TranDate']))."</td>";
        $response .="<td align=\"right\">".number_format($row['rcvd'])."</td>";
        $response .="</tr>";
    }    
}
elseif($req_type == 'Remaining')
{
    $sq = "SELECT

        purchase_order.pk_id,
        purchase_order.po_date,
        purchase_order.po_number,
        purchase_order.reference_number,
        tbl_stock_detail.PkDetailID,
        tbl_stock_master.TranNo,
        tbl_stock_master.TranDate,
        (purchase_order_product_details.shipment_quantity) as shipment_quantity,
        Sum(tbl_stock_detail.Qty) as rcvd,
        ((purchase_order_product_details.shipment_quantity) - Sum(tbl_stock_detail.Qty)) as remaining
        FROM
        purchase_order
INNER JOIN purchase_order_product_details ON purchase_order.pk_id = purchase_order_product_details.po_master_id
        LEFT JOIN tbl_stock_master ON purchase_order.pk_id = tbl_stock_master.shipment_id
        LEFT JOIN tbl_stock_detail ON tbl_stock_master.PkStockID = tbl_stock_detail.fkStockID
        WHERE
                purchase_order.wh_id = $wh_id
        AND purchase_order_product_details.item_id = $item_id
        AND purchase_order.status NOT IN ('Cancelled','Received')
    GROUP BY
purchase_order.pk_id
    ";

    $res  =  mysql_query($sq);
    $response ="";
    $response .="<h3 align=\"center\">Remaining</h3>";
    $response .="<table class=\"table table-bordered table-condensed\">
                    <tr class=\"bg-info\">
                    <td>#</td>
                    <td>PO No</td>
                    <td>Ordered</td>
                    <td>Received</td>
                    <td>Remaining</td>
                 </tr>";    
    $c=1;
    while($row = mysql_fetch_assoc($res)){
        $response .="<tr>";
        $response .="<td>".$c++."</td>";
        $response .="<td>".$row['po_number']."</td>";
        $response .="<td align=\"right\">".number_format($row['shipment_quantity'])."</td>";
        $response .="<td align=\"right\">".number_format($row['rcvd'])."</td>";
        $response .="<td align=\"right\">".number_format($row['remaining'])."</td>";
        $response .="</tr>";
    }    
}
elseif($req_type == 'Required')
{
    $response =" Breakdown of required quantity is not available.";
}
echo $response;
exit;
?>
