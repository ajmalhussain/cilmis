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

 
$qry_brands= "SELECT
stakeholder_item.stk_item,
Count(stakeholder_item.stk_id) as cnt,
itminfo_tab.itm_id,
itminfo_tab.itm_name,
itminfo_tab.generic_name,
itminfo_tab.itm_category 
FROM
stakeholder_item
INNER JOIN itminfo_tab ON stakeholder_item.stk_item = itminfo_tab.itm_id
INNER JOIN stakeholder ON stakeholder_item.stkid = stakeholder.stkid
WHERE
itminfo_tab.itm_category > 2 AND
stakeholder.stk_type_id = 3
GROUP BY
stakeholder_item.stk_item
ORDER BY Count(stakeholder_item.stk_id) desc
";
$brands =array();
$itms =array();
$res =mysql_query($qry_brands);
while($row = mysql_fetch_assoc($res))
{
    $brands[$row['stk_item']] = $row['cnt'];
    $itms[$row['itm_id']] = $row['itm_name'];
}

$qry_batches= "SELECT
stock_batch.item_id,
stock_batch.manufacturer AS manuf_id,
Count(stock_batch.batch_id) AS no_of_batches_in_manuf,
stakeholder.stkname
FROM
stock_batch
INNER JOIN stakeholder_item ON stock_batch.manufacturer = stakeholder_item.stk_id
INNER JOIN stakeholder ON stakeholder_item.stkid = stakeholder.stkid
GROUP BY
stock_batch.item_id,stock_batch.manufacturer

 
";

$batches = $manuf_names = array();
$res =mysql_query($qry_batches);
while($row = mysql_fetch_assoc($res))
{
    $batches[$row['item_id']][$row['manuf_id']] = $row['no_of_batches_in_manuf'];
    $manuf_names[$row['manuf_id']] = $row['stkname'];
}

?>
<table border="1">
    <tr>
        <td>#</td>
        <td>Item ID</td>
        <td>Item Name</td>
        <td>No of brands</td>
        <td>No of Batches</td>
        <td></td>
    </tr>
    <?php
    $c=1;
    foreach($brands as $itm_id => $brand_cnt)
    {
        $clr='';
        if(!isset($itms[$itm_id])) continue;
        @$item_name = $itms[$itm_id];
        
        if($brands[$itm_id] < 2 ){continue;$clr='#93ff97';}
        if(empty($batches[$itm_id])  ||  count($batches[$itm_id]) < $brands[$itm_id] ){ $clr='#b2c8ff'; }
    ?>
        <tr style="background-color: <?=$clr?>">
            <td><?=$c++?></td>
            <td><?=$itm_id?></td>
            <td><?=@$item_name?></td>
            <td><?=@$brands[$itm_id]?></td>
            <td><table><?php
                if(!empty($batches[$itm_id])){
                    foreach($batches[$itm_id] as $manuf_id =>$no_of_bat){
//                        echo $manuf_id.':'.$manuf_names[$manuf_id].':'.$no_of_bat.'<br/>';
                        echo '<tr><td>'.$manuf_id.'</td><td>'.$manuf_names[$manuf_id].'</td><td>'.$no_of_bat.'</td></tr>';
                    }
                }
                ?></table>
            </td>
            <td><a target="_blank" href="multi_manuf_prods_2.php?itm_id=<?=$itm_id?>">Details</a></td>
        </tr>

    <?php
    }
    ?>
</table>
    </body>
</html>
