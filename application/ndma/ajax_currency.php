<?php
include("../includes/classes/AllClasses.php");
$funding_source_id = $_REQUEST['funding_source_id'];
$strSql = "SELECT
stakeholder.currency,
SUM(funding_source_budget.amount) as budget
FROM
tbl_warehouse
INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
INNER JOIN funding_source_budget ON tbl_warehouse.wh_id = funding_source_budget.funding_source_id
WHERE 
tbl_warehouse.wh_id=$funding_source_id            
";

//echo $strSql;
$currency = mysql_query($strSql);
$row=mysql_fetch_assoc($currency);
$json_arr=array();
$json_arr['currency']=$row['currency'];
$json_arr['amount']=$row['budget'];

$qry_po_budget="SELECT
SUM(unit_price*shipment_quantity) AS total 
FROM
purchase_order
INNER JOIN purchase_order_product_details ON purchase_order.pk_id = purchase_order_product_details.po_master_id
WHERE
purchase_order.funding_source = $funding_source_id";
$result = mysql_query($qry_po_budget);
$row_2=mysql_fetch_assoc($result);
$json_arr['total_po']=$row_2['total'];

echo json_encode($json_arr);
?>