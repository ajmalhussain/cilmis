<?php
include("../includes/classes/AllClasses.php");
$month = $_REQUEST['month'];
$wh_id = $_REQUEST['wh'];
$stkid = $_SESSION['user_stakeholder'];
    
$rsTemp1 = mysql_query("SELECT itmrec_id FROM `itminfo_tab` WHERE `itm_status`=1 AND `itm_id` IN (SELECT `Stk_item` FROM `stakeholder_item` WHERE `stkid` =$stkid) AND itminfo_tab.itm_category IN (1) ORDER BY itm_category,`frmindex`,itm_name ");
while ($rsRow1 = mysql_fetch_array($rsTemp1)) {
    $item_char = $rsRow1['itmrec_id'];
    mysql_query("SELECT REPUpdateCarryForwardHF('$month','$item_char',$wh_id) FROM DUAL");
}

$do = $_REQUEST['Do'];
header("location: data_entry_hf.php?Do=$do");