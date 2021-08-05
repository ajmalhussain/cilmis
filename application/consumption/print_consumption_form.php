<?php
include("../includes/classes/AllClasses.php");

$wh_id = $_REQUEST['wh'];
$objwarehouse->m_npkId = $wh_id;
$whName = $objwarehouse->GetWarehouseNameById($wh_id);
echo "<h3 class=\"page-title row-br-b-wp\">" . $whName . " <span style=\"float:right\">Date: _______________________</span></h3>";

$province_id_session = $_SESSION['user_province1'];
$item_category_id = "1";

if ($province_id_session == 3) {
    $item_category_id = "1,5";
}

if ($_SESSION['user_role'] == 65) {
    if (date('Y-m', strtotime($RptDate)) == '2019-10') {
        $openOB = '';
    }
    $sess_wh_id = $_SESSION['user_warehouse'];
    $rsTemp1 = mysql_query("SELECT DISTINCT itminfo_tab.itm_id,
	CONCAT(
		itminfo_tab.itm_name,
		' (',
		itminfo_tab.generic_name,
		')'
	) itm_name FROM itminfo_tab INNER JOIN stock_batch ON itminfo_tab.itm_id = stock_batch.item_id WHERE stock_batch.wh_id = $wh_id ORDER BY itminfo_tab.itm_name");
} else {
    $rsTemp1 = mysql_query("SELECT * FROM `itminfo_tab` WHERE `itm_status`=1 AND `itm_id` IN (SELECT `Stk_item` FROM `stakeholder_item` WHERE `stkid` =$stkid) AND itminfo_tab.itm_category IN ($item_category_id) ORDER BY itm_category,`frmindex`,itm_name ");
}
?>
<table width="100%" align="center" class="table table-bordered" border="1" cellpadding="4" cellspacing="0" >
    <tr>
        <th class="text-center">S.No.</th>
        <th class="text-center">Product</th>
        <th class="text-center" width="13%">Opening balance</th>
        <th class="text-center" width="13%">Received</th>                    
        <th class="text-center" width="13%">Issued</th>                    
        <th class="text-center" width="13%">Closing Balance</th>
        <th class="text-center" width="13%">Demand</th>
    </tr>
    <?php
    $count = 1;
    while ($rsRow1 = mysql_fetch_array($rsTemp1)) {
        ?>
        <tr>
            <td><?php echo $count; ?></td>
            <td><?php echo $rsRow1['itm_name']; ?></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <?php $count++;
    }
    ?>
</table>
<style type="text/css" media="print">
    @media print
    {    
        #printButt
        {
            display: none !important;
        }
    }
</style>

<div style="float:right; margin:20px;" id="printButt">
    <input type="button" name="print" value="Print" onclick="javascript:printCont();" />
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<script language="javascript">
    $(function () {
        printCont();
    })
    function printCont()
    {
        window.print();
    }
</script>