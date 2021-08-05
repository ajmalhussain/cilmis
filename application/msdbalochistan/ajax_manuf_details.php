<?php
include("../includes/classes/Configuration.inc.php");
include(APP_PATH."includes/classes/db.php");

$sq= "SELECT
	stakeholder.stkid,
	stakeholder.stkname,
	stakeholder.contact_person,
	stakeholder.contact_numbers,
	stakeholder.contact_emails,
	stakeholder.contact_address
	
FROM
	tbl_warehouse
	INNER JOIN stakeholder ON tbl_warehouse.stkid = stakeholder.stkid 
WHERE
        tbl_warehouse.wh_id = '".$_REQUEST['manuf_id']."'  ";
$rs = mysql_query($sq) or die(mysql_error('getting manuf info'));
$resp = mysql_fetch_assoc($rs);


header('Content-Type: application/json');
echo json_encode($resp);
