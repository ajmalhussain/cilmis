<?php
ob_start();

//  include 'db_connection.php';
include("../includes/classes/Configuration.inc.php");
include(APP_PATH . "includes/classes/db.php");
//echo '<pre>';print_r($_POST);
$province = $_REQUEST['province'];
$stk = $_REQUEST['stakeholder'];
$sql = " SELECT
fp_procurement_details.fiscal_year,
fp_procurement_details.planned,
fp_procurement_details.expenditure,
stakeholder.stkname,
tbl_locations.LocName,
fp_procurement_details.stk_id ,
fp_procurement_details.prov_id
FROM
fp_procurement_details
LEFT JOIN tbl_locations ON fp_procurement_details.prov_id = tbl_locations.PkLocID
LEFT JOIN stakeholder ON fp_procurement_details.stk_id = stakeholder.stkid
WHERE
fp_procurement_details.prov_id =$province AND
fp_procurement_details.stk_id = $stk
";
//print_r($sql);exit;
$ress = mysql_query($sql);  
if (mysql_num_rows($ress) > 0) {
    ?>
<table class="table table-bordered">
        <thead>
        <th>Province</th>
        <th>Stakeholder</th>
        <th>Year</th>
        <th>Planned</th>
        <th>Expenditure</th>
    </thead>
    <?php
    while ($row = mysql_fetch_array($ress)) {
        ?>
    <tr>
        <td><?php
         if ($province== -1) {
             echo 'Regions (AJ&K, GB & ICT)';
         }else{
        echo $row['LocName']; 
         }
        ?></td>
        <td><?php
            if ($stk== -1) {
                echo 'PWD & DoH';
            } else {
                echo $row['stkname'];
            }
            ?></td>
        <td><?php echo $row['fiscal_year']; ?></td>
        <td><?php echo $row['planned']; ?></td>
        <td><?php echo $row['expenditure']; ?></td>
    </tr>
        <?php
    }
    ?>
    </table>
    <?php
} else {
    echo '<label style="color:red;">No data found</label>';
}
?>
 