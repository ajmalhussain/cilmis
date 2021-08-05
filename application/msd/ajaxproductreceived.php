<?php

/**
 * ajaxproductname
 * @package im
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
include("../includes/classes/AllClasses.php");
if (isset($_POST['product_id'])) {
    $prod_id = $_POST['product_id'];
    $items = $objManageItem->GetWHProductById($prod_id);
    echo"<option>Select</option>";
    foreach ($items as $item) {
        echo "<option selected='true' value=" . $item['id'] . ">" . $item['name'] . " " . ((!empty($item['generic_name']) && $item['generic_name'] != 'NULL') ? "[" . $item['generic_name'] . "]" : "") . "</option>";
    }
}
?>