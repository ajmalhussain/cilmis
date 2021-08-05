<?php
//echo '<pre>';print_r($_REQUEST);
include("../includes/classes/AllClasses.php");

$wh_id      = $_REQUEST['wh_id'];
$req_type    = $_REQUEST['req_type'];


    $sq = "SELECT
                shipments.item_id,
                shipments.shipment_quantity,
                shipments.`status`,
                itminfo_tab.itm_name,
                shipments.unit_price,
                shipments.po_number
            FROM
                shipments
            INNER JOIN itminfo_tab ON shipments.item_id = itminfo_tab.itm_id
        WHERE
                shipments.wh_id = $wh_id
               AND shipments.status NOT IN ('Cancelled' )";

    $res  =  mysql_query($sq);
     $response ="";
    $response .="<table class=\"table table-bordered table-condensed\">
                    <tr class=\"bg-grey\">
                    <td>#</td>
                    <td>PO No</td>
                    <td>Unit Price</td>
                    <td>Qty Ordered</td>
                    <td>Total Price</td>
                    </tr>";    
    $c=1;
    $total_spent = 0;
    while($row = mysql_fetch_assoc($res)){
        $response .="<tr>";
        $response .="<td>".$c++."</td>";
        $response .="<td>".$row['po_number']."</td>";
        $response .="<td align=\"right\">".number_format($row['unit_price'],2)."</td>";
        $response .="<td align=\"right\">".number_format($row['shipment_quantity'])."</td>";
        $response .="<td align=\"right\">".number_format($row['shipment_quantity']*$row['unit_price'])."</td>";
        $response .="</tr>";
        
        $total_spent +=$row['shipment_quantity']*$row['unit_price'];
    }    
    $response .="<tr>";
        $response .="<td colspan=\"4\">Grand Total</td>";
        $response .="<td align=\"right\">".number_format($total_spent)."</td>";
        $response .="</tr>";
echo $response;
exit;
?>
