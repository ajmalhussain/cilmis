<?php
//echo '<pre>';print_r($_REQUEST);exit;
//
//include AllClasses
include("../includes/classes/AllClasses.php");
$itm_id = $_REQUEST['itm_id'];
?>
<html>
<html>
    <h3 align="center">Brand Details of <?=$itm_id?></h3>
    <body>
        
<?php
 
$qry_brands= "SELECT
itminfo_tab.itm_id,
itminfo_tab.itm_name,
itminfo_tab.generic_name,
stakeholder_item.stk_id,
stakeholder_item.brand_name,
stakeholder.stkid AS manuf_id,
stakeholder.stkname AS manuf_name,
itminfo_tab.itm_category,
Count(stock_batch.batch_id) as batches,
stock_batch.wh_id,
stakeholder_item.quantity_per_pack,
stakeholder_item.pack_length,
stakeholder_item.pack_width,
stakeholder_item.pack_height,
stakeholder_item.carton_per_pallet,
stakeholder_item.gtin,
stakeholder_item.unit_price,
TRIM(stakeholder.report_title1) as report_title1
FROM
itminfo_tab
INNER JOIN stakeholder_item ON itminfo_tab.itm_id = stakeholder_item.stk_item
INNER JOIN stakeholder ON stakeholder_item.stkid = stakeholder.stkid
LEFT JOIN stock_batch ON itminfo_tab.itm_id = stock_batch.item_id AND stakeholder_item.stk_id = stock_batch.manufacturer
LEFT JOIN tbl_warehouse ON stock_batch.wh_id = tbl_warehouse.wh_id
WHERE
itminfo_tab.itm_category > 2 AND
itminfo_tab.itm_id = $itm_id AND
stakeholder.stk_type_id = 3
GROUP BY
itminfo_tab.itm_id,
stakeholder_item.stk_id

";
    
?>
        <form action="merge_brands.php" >
<table border="1">
    <tr>
        <td>#</td>
        <td>Item ID</td>
        <td>Item Name</td>
        <td>Generic</td>
        <td>Cat</td>
        <td># Bat</td>
        <td>Brand</td>
        <td>Manufacturer</td>
        <td>Qty / Crtn</td>
        <td>L</td>
        <td>W</td>
        <td>H</td>
        <td>C / plt</td>
        <td>GTIN</td>
        <td>Price</td>
        <td colspan="2">Merge Brnd</td>
        <td>Del</td>
        <td colspan="2">Split Prod</td>
    </tr>
    <?php
    $c=1;
    $res =mysql_query($qry_brands);
    while($row = mysql_fetch_assoc($res))
    {
        
    ?>
        <tr style="background-color: <?=$clr?>">
            <td><?=$c++?></td>
            <td><?=$row['itm_id']?></td>
            <td><?=$row['itm_name']?></td>
            <td><?=$row['generic_name']?></td>
            <td><?=$row['itm_category']?></td>
            <td><?=$row['batches']?></td>
            <td><?=$row['stk_id'].':'.$row['brand_name']?></td>
            <td><?=$row['manuf_id'].':'.$row['manuf_name']?></td>

            <td><?=$row['quantity_per_pack']?></td>
            <td><?=$row['pack_length']?></td>
            <td><?=$row['pack_width']?></td>
            <td><?=$row['pack_height']?></td>
            <td><?=$row['carton_per_pallet']?></td>
            <td><?=$row['gtin']?></td>
            <td><?=$row['unit_price']?></td>
            <td><input name="merge_these[]" type="checkbox" value="<?=$row['stk_id']?>"></td>
            <td>
                <input type="submit" name="merge_into" value="<?=$row['stk_id']?>">
                <input type="hidden" name="itm_id" value="<?=$row['itm_id']?>">
            </td>
            <td>
                <?php if($row['batches']==0){?>
                <button type="submit" name="delete_this" value="<?=$row['stk_id']?>" >Del</button>
                <?php } ?>
            </td>
            <td>
                <input name="split_these[<?=$row['stk_id']?>]" type="checkbox" value="<?=$row['stk_id']?>">
                <input name="dis_code[<?=$row['stk_id']?>]" size="6" type="text" value="">
                <input name="new_name[<?=$row['stk_id']?>]" size="40" type="text" value="<?=htmlspecialchars($row['itm_name'].' - '.$row['brand_name'].' [by '.strtoupper($row['report_title1']).']')?>">
            </td>
            <?php if($c==2){ ?>
            <td rowspan="99">
                <button type="submit" name="split_prod" value="split" >Split</button>
            </td>
            <?php } ?>
        </tr>

    <?php
    }
    ?>
</table>
        </form>
        
    <h3 align="center">Batch - Warehouse Details</h3>
        
<?php
 
$qry_brands= "SELECT
itminfo_tab.itm_id,
itminfo_tab.itm_name,
itminfo_tab.generic_name,
stakeholder_item.stk_id,
stakeholder_item.brand_name,
stakeholder.stkid AS manuf_id,
stakeholder.stkname AS manuf_name,
itminfo_tab.itm_category,
stock_batch.batch_id,
stock_batch.batch_no,
tbl_warehouse.wh_id,
stock_batch.qty,
tbl_warehouse.wh_name,
tbl_warehouse.stkid
FROM
itminfo_tab
INNER JOIN stakeholder_item ON itminfo_tab.itm_id = stakeholder_item.stk_item
INNER JOIN stakeholder ON stakeholder_item.stkid = stakeholder.stkid
LEFT JOIN stock_batch ON itminfo_tab.itm_id = stock_batch.item_id AND stakeholder_item.stk_id = stock_batch.manufacturer
LEFT JOIN tbl_warehouse ON stock_batch.wh_id = tbl_warehouse.wh_id
WHERE
itminfo_tab.itm_category > 2 AND
itminfo_tab.itm_id = $itm_id AND
stakeholder.stk_type_id = 3
ORDER BY

stakeholder.stkid,
stakeholder_item.stk_id,
tbl_warehouse.wh_id

";
    
?>
<table border="1">
    <tr>
        <td>#</td>
        <td>Item ID</td>
        <td>Item Name</td>
        <td>Generic</td>
        <td>Category</td>
        <td>Batch No</td>
        <td>Batch ID</td>
        <td>Brand</td>
        <td>Manufacturer</td>
        <td>WH</td>
        <td>Stk of wh</td>
        <td>Qty</td>
    </tr>
    <?php
    $c=1;
    $res =mysql_query($qry_brands);
    while($row = mysql_fetch_assoc($res))
    {
        
    ?>
        <tr style="background-color: <?=$clr?>">
            <td><?=$c++?></td>
            <td><?=$row['itm_id']?></td>
            <td><?=$row['itm_name']?></td>
            <td><?=$row['generic_name']?></td>
            <td><?=$row['itm_category']?></td>
            <td><?=$row['batch_no']?></td>
            <td><a target="_blank" href="../im/product-ledger-history.php?id=<?=$row['batch_id']?>"><?=$row['batch_id']?></a></td>
            <td><?=$row['stk_id'].':'.$row['brand_name']?></td>
            <td><?=$row['manuf_id'].':'.$row['manuf_name']?></td>
            <td><?=$row['wh_id'].':'.$row['wh_name']?></td>
            <td><?=$row['stkid']?></td>
            <td><?=$row['qty']?></td>

        </tr>

    <?php
    }
    ?>
</table>
    </body>
</html>
