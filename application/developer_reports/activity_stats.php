<?php
//echo '<pre>';print_r($_REQUEST);exit;
//
//include AllClasses
include("../includes/classes/AllClasses.php");
?>
<html>
<html>
    <h3 align="center">Activity stats</h3>
    <body>
        
<?php

$activity_data = $activity_count=$wh_arr= array();

$qry_summary_dist= "SELECT
Count(tbl_stock_master.PkStockID) AS total_rcvd_v,
tbl_stock_master.WHIDTo,
tbl_stock_master.TranTypeID,
tbl_stock_master.TranDate,
tbl_warehouse.wh_name
FROM
tbl_stock_master
INNER JOIN tbl_warehouse ON tbl_stock_master.WHIDTo = tbl_warehouse.wh_id
INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
WHERE
tbl_stock_master.TranTypeID = 1  AND
stakeholder.lvl < 7
GROUP BY 
tbl_stock_master.WHIDTo,
tbl_stock_master.TranDate,
tbl_stock_master.TranTypeID

ORDER BY
tbl_stock_master.WHIDTo ASC

";
//Query result
//echo $qry_summary_dist;
$Res2 =mysql_query($qry_summary_dist);

while($row = mysql_fetch_assoc($Res2))
{
    $wh_arr[$row['WHIDTo']]=$row['wh_name'];
    $tr_d = date('Y-m-d',strtotime($row['TranDate']));
    $activity_data[$row['WHIDTo']]['rcvd'][$tr_d]=$row['total_rcvd_v'];
    
    $compare_date = date('Y-m-d',strtotime('-30 days'));
    //echo '</br>Compare:'.$tr_d.' and '.$compare_date.' END';
    
    @$activity_count[$row['WHIDTo']]['rcvd']['all'] += $row['total_rcvd_v'];
    if($tr_d >= $compare_date)
    {
        @$activity_count[$row['WHIDTo']]['rcvd']['30_days'] += $row['total_rcvd_v'];
    }
    $compare_date2 = date('Y-m-d',strtotime('-7 days'));
    if($tr_d >= $compare_date2)
    {
        @$activity_count[$row['WHIDTo']]['rcvd']['7_days'] += $row['total_rcvd_v'];
    }
    $compare_date3 = date('Y-m-d');
    if($tr_d == $compare_date3)
    {
        @$activity_count[$row['WHIDTo']]['rcvd']['today'] += $row['total_rcvd_v'];
    }
    $compare_date4 = date('Y-m-d',strtotime('-90 days'));
    if($tr_d >= $compare_date4)
    {
        @$activity_count[$row['WHIDTo']]['rcvd']['90_days'] += $row['total_rcvd_v'];
    }
}

$qry_summary_dist= "SELECT
Count(tbl_stock_master.PkStockID) AS total_issued_v,
tbl_stock_master.WHIDFrom,
tbl_stock_master.TranTypeID,
tbl_stock_master.TranDate,
tbl_warehouse.wh_name
FROM
tbl_stock_master
INNER JOIN tbl_warehouse ON tbl_stock_master.WHIDFrom = tbl_warehouse.wh_id
WHERE
tbl_stock_master.TranTypeID = 2  
GROUP BY 
tbl_stock_master.WHIDFrom,
tbl_stock_master.TranDate,
tbl_stock_master.TranTypeID

ORDER BY
tbl_stock_master.WHIDFrom ASC

";
//Query result
//echo $qry_summary_dist;
$Res2 =mysql_query($qry_summary_dist);

while($row = mysql_fetch_assoc($Res2))
{
    $wh_arr[$row['WHIDFrom']]=$row['wh_name'];
    $tr_d = date('Y-m-d',strtotime($row['TranDate']));
    $activity_data[$row['WHIDFrom']]['issued'][$tr_d]=$row['total_issued_v'];
    
    $compare_date = date('Y-m-d',strtotime('-30 days'));
    @$activity_count[$row['WHIDFrom']]['issued']['all'] += $row['total_issued_v'];
    if($tr_d >= $compare_date)
    {
        @$activity_count[$row['WHIDFrom']]['issued']['30_days'] += $row['total_issued_v'];
    }
    $compare_date2 = date('Y-m-d',strtotime('-7 days'));
    if($tr_d >= $compare_date2)
    {
        @$activity_count[$row['WHIDFrom']]['issued']['7_days'] += $row['total_issued_v'];
    }
    $compare_date3 = date('Y-m-d');
    if($tr_d == $compare_date3)
    {
        @$activity_count[$row['WHIDFrom']]['issued']['today'] += $row['total_issued_v'];
    }
    $compare_date4 = date('Y-m-d',strtotime('-90 days'));
    if($tr_d >= $compare_date4)
    {
        @$activity_count[$row['WHIDFrom']]['issued']['90_days'] += $row['total_issued_v'];
    }
}


$qry_summary_dist= "SELECT
Count(tbl_stock_master.PkStockID) AS total_adjusted_v,
tbl_stock_master.WHIDFrom,
tbl_stock_master.TranTypeID,
tbl_stock_master.TranDate,
tbl_warehouse.wh_name
FROM
tbl_stock_master
INNER JOIN tbl_warehouse ON tbl_stock_master.WHIDFrom = tbl_warehouse.wh_id
WHERE
tbl_stock_master.TranTypeID >2  
GROUP BY 
tbl_stock_master.WHIDFrom,
tbl_stock_master.TranDate,
tbl_stock_master.TranTypeID


ORDER BY
tbl_stock_master.WHIDFrom ASC
";
//Query result
//echo $qry_summary_dist;
$Res2 =mysql_query($qry_summary_dist);

while($row = mysql_fetch_assoc($Res2))
{
    $tr_d = date('Y-m-d',strtotime($row['TranDate']));
    $activity_data[$row['WHIDFrom']]['adjusted'][$tr_d]=$row['total_adjusted_v'];
   
    
    $compare_date = date('Y-m-d',strtotime('-30 days'));
    @$activity_count[$row['WHIDFrom']]['adjusted']['all'] += $row['total_adjusted_v'];
    if($tr_d >= $compare_date)
    {
        @$activity_count[$row['WHIDFrom']]['adjusted']['30_days'] += $row['total_adjusted_v'];
    }
    $compare_date2 = date('Y-m-d',strtotime('-7 days'));
    if($tr_d >= $compare_date2)
    {
        @$activity_count[$row['WHIDFrom']]['adjusted']['7_days'] += $row['total_adjusted_v'];
    }
    $compare_date3 = date('Y-m-d');
    if($tr_d == $compare_date3)
    {
        @$activity_count[$row['WHIDFrom']]['adjusted']['today'] += $row['total_adjusted_v'];
    }
    $compare_date4 = date('Y-m-d',strtotime('-90 days'));
    if($tr_d >= $compare_date4)
    {
        @$activity_count[$row['WHIDFrom']]['adjusted']['90_days'] += $row['total_adjusted_v'];
    }
}
//echo '<pre>';
//print_r($activity_data);
//print_r($activity_count);
//exit;
?>
<table border="1">
    
    <?php 
    foreach($wh_arr as $wh_id => $val_wh)
    { 
        ?>
    <tr>
        <td colspan="99" bgcolor="grey"><?=$wh_id.'-'.$val_wh?></td>
    </tr>
    <tr>
        <td>Type</td>
        <td>All</td>
        <td>Last 90 Days</td>
        <td>Last 30 Days</td>
        <td>Last 7 Days</td>
        <td>Today</td>
    </tr>
    <tr>
        <td>Vouchers Issued</td>
        <td><?=(!empty($activity_count[$wh_id]['issued']['all'])?$activity_count[$wh_id]['issued']['all']:'-')?></td>
        <td><?=(!empty($activity_count[$wh_id]['issued']['90_days'])?$activity_count[$wh_id]['issued']['90_days']:'-')?></td>
        <td><?=(!empty($activity_count[$wh_id]['issued']['30_days'])?$activity_count[$wh_id]['issued']['30_days']:'-')?></td>
        <td><?=(!empty($activity_count[$wh_id]['issued']['7_days'])?$activity_count[$wh_id]['issued']['7_days']:'-')?></td>
        <td><?=(!empty($activity_count[$wh_id]['issued']['today'])?$activity_count[$wh_id]['issued']['today']:'-')?></td>
    </tr>
    <tr>
        <td>Vouchers Received</td>
        <td><?=(!empty($activity_count[$wh_id]['rcvd']['all'])?$activity_count[$wh_id]['rcvd']['all']:'-')?></td>
        <td><?=(!empty($activity_count[$wh_id]['rcvd']['90_days'])?$activity_count[$wh_id]['rcvd']['90_days']:'-')?></td>
        <td><?=(!empty($activity_count[$wh_id]['rcvd']['30_days'])?$activity_count[$wh_id]['rcvd']['30_days']:'-')?></td>
        <td><?=(!empty($activity_count[$wh_id]['rcvd']['7_days'])?$activity_count[$wh_id]['rcvd']['7_days']:'-')?></td>
        <td><?=(!empty($activity_count[$wh_id]['rcvd']['today'])?$activity_count[$wh_id]['rcvd']['today']:'-')?></td>
    </tr>
    <tr>
        <td>Vouchers Adjusted</td>
        <td><?=(!empty($activity_count[$wh_id]['adjusted']['all'])?$activity_count[$wh_id]['adjusted']['all']:'-')?></td>
        <td><?=(!empty($activity_count[$wh_id]['adjusted']['90_days'])?$activity_count[$wh_id]['adjusted']['90_days']:'-')?></td>
        <td><?=(!empty($activity_count[$wh_id]['adjusted']['30_days'])?$activity_count[$wh_id]['adjusted']['30_days']:'-')?></td>
        <td><?=(!empty($activity_count[$wh_id]['adjusted']['7_days'])?$activity_count[$wh_id]['adjusted']['7_days']:'-')?></td>
        <td><?=(!empty($activity_count[$wh_id]['adjusted']['today'])?$activity_count[$wh_id]['adjusted']['today']:'-')?></td>
    </tr>
    <?php
    
    }
    ?>
</table>
    </body>
</html>
