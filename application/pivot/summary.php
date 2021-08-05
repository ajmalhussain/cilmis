<?php include("../includes/classes/AllClasses.php"); 
ini_set('display_errors',1);
$dist = $_SESSION['user_district'];
$stk = $_SESSION['user_stakeholder'];
$result = mysql_query("SELECT
	ctbl_pivot_bi.Stakeholder, 
	ctbl_pivot_bi.Province, 
	ctbl_pivot_bi.District, 
	ctbl_pivot_bi.Product, 
	ctbl_pivot_bi.Reporting_Year, 
	ctbl_pivot_bi.Reporting_Month, 
	ctbl_pivot_bi.fiscal, 
	ctbl_pivot_bi.consumption, 
	ctbl_pivot_bi.AMC, 
	ctbl_pivot_bi.SOH, 
	ctbl_pivot_bi.MOS, 
	ctbl_pivot_bi.Reporting_Rate, 
	ctbl_pivot_bi.Total_HF, 
	ctbl_pivot_bi.CYP, 
	ctbl_pivot_bi.StakeholderType
FROM
	ctbl_pivot_bi
	INNER JOIN
	tbl_locations
	ON 
		ctbl_pivot_bi.District = tbl_locations.LocName
	INNER JOIN
	stakeholder
	ON 
		ctbl_pivot_bi.Stakeholder = stakeholder.stkname
WHERE
	tbl_locations.PkLocID = $dist AND
	stakeholder.stkid = $stk");
$data = array();
while ($row = mysql_fetch_assoc($result)) {
    $data[] = $row;
}

echo json_encode($data);