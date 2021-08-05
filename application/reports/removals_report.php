<?php
//echo '<pre>';print_r($_REQUEST);exit;
include("../includes/classes/AllClasses.php");


//****************************
//Quick Query Settings
$title= 'Removals Report - Punjab - PWD';
//****************************

?>
<html>
    <h3 align="center"><?=$title?></h3>
    <body>
      
<?php

$qry_summary_dist= "
    SELECT
tbl_locations.LocName,
itminfo_tab.itm_name,
tbl_hf_data.reporting_date,
Sum(tbl_hf_data.removals) as sum_removals

FROM
tbl_hf_data
INNER JOIN tbl_warehouse ON tbl_hf_data.warehouse_id = tbl_warehouse.wh_id
INNER JOIN tbl_locations ON tbl_warehouse.dist_id = tbl_locations.PkLocID
INNER JOIN itminfo_tab ON tbl_hf_data.item_id = itminfo_tab.itm_id
WHERE
tbl_warehouse.prov_id = 1 AND
tbl_warehouse.stkid = 1 AND
tbl_hf_data.item_id IN (5,8,13) AND
tbl_hf_data.reporting_date >= '2019-04-01'
GROUP BY
tbl_locations.LocName,
itminfo_tab.itm_name,
tbl_hf_data.reporting_date
ORDER BY
tbl_locations.LocName ASC,
itminfo_tab.itm_name ASC,
tbl_hf_data.reporting_date


";
//Query result
//echo $qry_summary_dist;
$Res2 =mysql_query($qry_summary_dist);
$display_data  = $columns_data = array();

while($row = mysql_fetch_assoc($Res2))
{
   $display_data[] = $row;
   $row2=$row;
   //echo '<pre>';print_r($row);
}

foreach($row2 as $k=>$v)
{
   $columns_data[] = $k;
}
//echo '<pre>';print_r($columns_data);print_r($display_data);
?>
<table border="1" class="table table-condensed table-striped left" >
    <tr bgcolor="#afb5ea">
        <?php
        echo '<td>#</td>';
        foreach($columns_data as $k=>$v)
        {
           echo '<td>'.$v.'</td>';
        }
        ?>
    </tr>
    
    <?php
    $count_of_row = 0;
        foreach($display_data as $k => $disp)
        {
           echo '<tr>';
           echo '<td>'.++$count_of_row.'</td>';
           foreach($columns_data as $k2=>$col)
           {
            echo ' <td>'.$disp[$col].'</td>';
           }   
           echo '<tr>';
        }
        ?>
</table>
    </body>
    
<script src="<?php echo PUBLIC_URL;?>js/jquery-1.4.4.js" type="text/javascript"></script>
<script src="<?php echo PUBLIC_URL;?>js/custom_table_sort.js" type="text/javascript"></script>
</html>
