<?php
include("../includes/classes/AllClasses.php");
$id = $_REQUEST['id'];
$qry = "SELECT
tbl_warehouse.wh_name,
funding_source_budget.amount,
funding_source_budget.date,
funding_source_budget.description
FROM
funding_source_budget
INNER JOIN tbl_warehouse ON funding_source_budget.funding_source_id = tbl_warehouse.wh_id
WHERE
funding_source_budget.funding_source_id = $id
ORDER BY
funding_source_budget.date DESC
";
//print_r($qry);exit;
$result = mysql_query($qry);
?>
<table class="table table-condensed">
    <tr>
        <th>Funding source</th>
        <th>Date</th>
        <th>Amount</th>
        <th>Description</th>
    </tr>
    <?php
    while ($row = mysql_fetch_array($result)) {
        ?>
    <tr>
        <td><?php echo $row['wh_name']; ?></td>
        <td><?php echo $row['date']; ?></td>
        <td><?php echo $row['amount']; ?></td>
        <td><?php echo $row['description']; ?></td>
    </tr>
            <?php
    }
    ?>
</table>
