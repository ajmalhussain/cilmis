<?php
ob_start();

//  include 'db_connection.php';
include("../includes/classes/Configuration.inc.php");
include(APP_PATH . "includes/classes/db.php");
//echo '<pre>';print_r($_POST);
$locations_arr = array();
$locations_arr[1] = 'Punjab';
$locations_arr[2] = 'Sindh';
$locations_arr[3] = 'Khyber Pakhtunkhwa';
$locations_arr[4] = 'Balochistan';
$locations_arr[10] = 'Pakistan';
$population = $_REQUEST['new_pop'];
$loc = $_REQUEST['hidden_dist'];
$year = implode($_REQUEST['hidden_year'],'');
$loc_name = $locations_arr[$loc];
$qry = "INSERT INTO impact_calculator(female,year,location,source) VALUES($population,$year,$loc,'User')";
mysql_query($qry);
$sql = " SELECT  GROUP_CONCAT(distinct source) as source, sum(female) as female FROM impact_calculator WHERE year in (" . $year . ") AND location = '" . $loc . "' ";
//print_r($sql);exit;
$ress = mysql_query($sql);
$row = mysql_fetch_assoc($ress);
?>
<div class="form-group">
    <label for="usr">Source</label>
    <input type="text" class="form-control" value="<?php echo $row["source"]; ?>" readonly>
</div>

<!-- Female -->
<div class="form-group">
    <label for="usr">Total-Population (<?= $loc_name ?>)</label>
    <input type="text" id="f_pop2" class="form-control" value="<?php echo number_format($row["female"]); ?>" readonly>
    <input type="hidden" id="f_pop" class="form-control" value="<?php echo ($row["female"]); ?>" readonly>
</div>
<button type="button" class="btn btn-warning inline-items" name="calculate_btn" value="Calculate" onclick="calculate()">Calculate Impact</button>
