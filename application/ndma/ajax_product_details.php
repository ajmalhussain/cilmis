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
$product_arr = array();
if (isset($_POST['product']) && !empty($_POST['product'])) {
    if(isset($_REQUEST['manuf_id'])){
        $manuf_id=$_REQUEST['manuf_id'];
    }
    $product = $_POST['product'];
    $name = $objManageItem->GetProductName($product);
    $array = $objItemUnits->GetUnitByItemId($product);
    $cat = $objManageItem->GetProductCat($product);
    if ($name != false) {
        $product_arr['name'] = $name;
    }

    if ($array != FALSE) {
//        $type = $array['type'];
        $product_arr['unit_type'] = $array['type'];
        $product_arr['unit_id'] = $array['id'];
//        $id = $array['id'];
//        echo $type;
//        echo '<input type="hidden" name="unit" id="unit" value="' . $id . '" />';
    }

    if ($cat != false) {
        $product_arr['category'] = $cat;
    }
    $manuf_string='';
    $checkManufacturer = mysql_query("SELECT
                                                stakeholder.stkid,
                                                stakeholder_item.stk_id,
                                                CONCAT(stakeholder.stkname, ' | ' ,IFNULL(stakeholder_item.brand_name, '')) AS stkname
                                        FROM
                                                stakeholder
                                        INNER JOIN stakeholder_item ON stakeholder.stkid = stakeholder_item.stkid
                                        WHERE
                                                stakeholder.stk_type_id = 3
                                        AND stakeholder_item.stk_item = " . $product . "
                                        ORDER BY
                                                stakeholder.stkname ASC") or die(mysql_error());
    $manufacturer = mysql_num_rows($checkManufacturer);
    $manuf_string= '<option value="">Select</option>';
    while ($val = mysql_fetch_assoc($checkManufacturer)) {
        $sel='';
        if(!empty($manuf_id)&&$manuf_id==$val['stk_id']){
            $sel="selected='selected'";
        }
        $manuf_string.= '<option value="' . $val['stk_id'] . '"'.$sel.' >' . $val['stkname'] . '</option>';
    }
    $product_arr['manufacturer']=$manuf_string;
    echo json_encode($product_arr);
}
?>