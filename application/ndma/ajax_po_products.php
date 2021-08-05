<?php
include("../includes/classes/AllClasses.php");
$master_id = $_REQUEST['master_id'];
$products = $objPurchaseOrderProductDetails->getPOProducts($master_id);
if ($products) {
        while ($row = mysql_fetch_array($products)) {
            $cls = '';
            if(!empty($_REQUEST['prod_detail_id']) && $_REQUEST['prod_detail_id'] == $row['pk_id']){
                $cls = 'success';
            }
            ?>
            <tr id="row_<?=$row['pk_id']?>" class="<?=$cls?>" >
                <td><?php echo $row['itm_name'] ?></td>
                <td><?php echo $row['stkname'] ?></td>
                <td align="right"><?php echo $row['shipment_quantity'] ?></td>
                <td align="right"><?php echo number_format($row['unit_price'],2) ?></td>
                <td align="right"><?php echo ($row['shipment_quantity'] * $row['unit_price']); ?></td>
                <td><a name="delete_product" id="<?php echo $row['pk_id'] . "_delete_product"; ?>"  class="del_prod" data-id="<?php echo $row['pk_id']; ?>">Delete</a></td>
                <td><a name="edit_product" id="<?php echo $row['pk_id'] . "_edit_product"; ?>"     class="edit_prod" data-id="<?php echo $row['pk_id']; ?>">Edit</a></td>
            </tr>
            <?php
        }
        ?> 
    <?php
} else {
    echo '<h3>No products found</h3>';
}
?>