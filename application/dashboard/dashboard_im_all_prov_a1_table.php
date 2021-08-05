<?php
include("../includes/classes/Configuration.inc.php");
Login();
//include db
include(APP_PATH . "includes/classes/db.php");
//include functions
include APP_PATH . "includes/classes/functions.php";
//print_r($_REQUEST);exit;
include(PUBLIC_PATH . "html/header.php");

$where_hf = '';
$search_type = (!empty($_REQUEST['search_type']) ? $_REQUEST['search_type'] : '');
$province = (!empty($_REQUEST['prov_sel']) ? $_REQUEST['prov_sel'] : '');
$from_date = (!empty($_REQUEST['from_date']) ? $_REQUEST['from_date'] : '');
$from_date = date('Y-m', strtotime($from_date));
$to_date = (!empty($_REQUEST['to_date']) ? $_REQUEST['to_date'] : '');
$to_date = date('Y-m', strtotime($to_date));
$product = (!empty($_REQUEST['product']) ? $_REQUEST['product'] : '');
$district = (!empty($_REQUEST['district']) ? $_REQUEST['district'] : '');
//print_r($province);exit;
$prod_filter = '';
if ($search_type == 1) {
    $prod_filter = "AND itminfo_tab.generic_name LIKE '%$product%'";
} else {
    $prod_filter = "AND itminfo_tab.itm_id = $product";
}
if ($province == 1) {
    $queryItem = "SELECT DISTINCT
                                                            tbl_warehouse.wh_name,
                                                            tbl_warehouse.wh_id
                                                            FROM
                                                            tbl_warehouse
                                                            INNER JOIN project_locations ON tbl_warehouse.dist_id = project_locations.location_id
                                                            WHERE
                                                            tbl_warehouse.dist_id IN(" . $district . ") AND
                                                            tbl_warehouse.is_allowed_im = 1 AND 
tbl_warehouse.stkid IN (7, 74, 145, 276,951)
ORDER by wh_name
                                                                     ";
//    print_r($queryItem);exit;
    //Result
    $rsprov = mysql_query($queryItem) or die();
    while ($rowItem = mysql_fetch_array($rsprov)) {
        $wh_id[$rowItem['wh_id']] = $rowItem['wh_id'];
    }
} else if ($province == 2) {

    $queryItem = "SELECT DISTINCT
                                                            tbl_warehouse.wh_name,
                                                            tbl_warehouse.wh_id
                                                            FROM
                                                            tbl_warehouse
                                                            INNER JOIN project_locations ON tbl_warehouse.dist_id = project_locations.location_id
                                                            WHERE
                                                           tbl_warehouse.dist_id IN(" . $district . ") AND
                                                            tbl_warehouse.is_allowed_im = 1 AND 
                                                            tbl_warehouse.stkid IN (7, 74, 145, 276,951) 
                                                            ORDER by wh_name
                                                                     ";
//    print_r($queryItem);exit;
    //Result
    $rsprov = mysql_query($queryItem) or die();
    while ($rowItem = mysql_fetch_array($rsprov)) {
        $wh_id[$rowItem['wh_id']] = $rowItem['wh_id'];
    }
} else if ($province == 3) {

    $queryItem = "SELECT DISTINCT
                                                            tbl_warehouse.wh_name,
                                                            tbl_warehouse.wh_id
                                                            FROM
                                                            tbl_warehouse
                                                            INNER JOIN project_locations ON tbl_warehouse.dist_id = project_locations.location_id
                                                            WHERE
                                                             tbl_warehouse.dist_id IN(" . $district . ") AND
                                                            tbl_warehouse.is_allowed_im = 1 AND 
                                                           tbl_warehouse.stkid IN (7, 74, 145, 276,951)
                                                            ORDER by wh_name
                                                                     ";
//    print_r($queryItem);exit;
    //Result
    $rsprov = mysql_query($queryItem) or die();
    while ($rowItem = mysql_fetch_array($rsprov)) {
        $wh_id[$rowItem['wh_id']] = $rowItem['wh_id'];
    }
}
$avg_consumption_date = date('Y-m', strtotime("-3 month", strtotime($from_without_date)));
//$from_date .= '-01';
$from_date_last_day = date('Y-m-t', strtotime($from_date));
//print_r($wh_id);exit;
?>
<div class="widget widget-tabs" style="border:0px;">
    <div class="widget-body" id="a1" style='background-color: white;'>

        <h3 style="text-align: center;"><?php echo "District Stores Month wise Consumption Trend" ?></h3>
        <?php
        $stock_arr = $wh_arr = array();
        $count = 1;
        $counter = 0;



        $qry_soh = "SELECT
	ABS(SUM(tbl_stock_detail.Qty)) AS consumption,
	DATE_FORMAT(
		tbl_stock_master.TranDate,
		'%Y-%m'
	) AS t_date,
	stock_batch.item_id,
        stock_batch.wh_id,
        tbl_warehouse.wh_name,
	itminfo_tab.itm_name
FROM
	tbl_stock_detail
INNER JOIN tbl_stock_master ON tbl_stock_detail.fkStockID = tbl_stock_master.PkStockID
INNER JOIN tbl_warehouse ON tbl_stock_master.WHIDFrom = tbl_warehouse.wh_id
INNER JOIN stock_batch ON tbl_stock_detail.BatchID = stock_batch.batch_id
INNER JOIN itminfo_tab ON stock_batch.item_id = itminfo_tab.itm_id
WHERE
	tbl_warehouse.wh_id IN(" . implode(",", $wh_id) . ")
AND tbl_stock_master.TranTypeID = 2
AND tbl_warehouse.stkid IN (7, 74, 145, 276,951)
$prod_filter
AND (
	DATE_FORMAT(
		tbl_stock_master.TranDate,
		'%Y-%m'
	) >= '$from_date'
	AND DATE_FORMAT(
		tbl_stock_master.TranDate,
		'%Y-%m'
	) <= '$to_date'
)
GROUP BY
	DATE_FORMAT(
		tbl_stock_master.TranDate,
		'%Y-%m'
	),
	stock_batch.wh_id";

//print_r($qry_soh);
//exit;
        $qryRes = mysql_query($qry_soh);
        $num_rows = mysql_num_rows($qryRes);
        while ($row = mysql_fetch_assoc($qryRes)) {

            $stock_arr[$row['wh_id']][$row['t_date']] = $row['consumption'];
            $reporting_array[$row['t_date']] = $row['t_date'];
            $wh_name[$row['wh_id']] = $row['wh_name'];
//        $wh_arr[$row['wh_id']] = $row['wh_name'];
        }
//print_r($stock_arr);exit;
//        foreach ($item_arr as $key => $id) {
//            if (($stock_arr[$id]['soh'] ) == 0 || $stock_arr[$id]['soh'] == null) {
//                $so += $stock_arr[$id]['soh'];
//            }
//        }
        ?>
        <table class="table table-bordered table-condensed table-striped">
            <thead>
                <tr>
                    <th>Warehouse</th>
<?php
foreach ($reporting_array as $key => $value) {
    echo "<th>" . date('M-Y', strtotime($key)) . "</th>";
}
echo "</tr></thead>";


echo '<tbody><tr>';
foreach ($wh_name as $key => $value) {
    echo '<td>' . $value . '</td>';
    foreach ($reporting_array as $date => $val) {
        if (!isset($stock_arr[$key][$date])) {
            echo '<td>0</td>';
        } else {
            echo'<td>' . $stock_arr[$key][$date] . '</td>';
        }
    }
    echo'</tr>';
}
?>

                    </tbody>
        </table>
    </div>
</div>

