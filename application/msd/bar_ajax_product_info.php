<?php
include("../includes/classes/AllClasses.php");
if (isset($_POST['manufacturer_id'])) {
    $manuf_id = $_POST['manufacturer_id'];
     $qry="SELECT
        stakeholder.stkid,
        stakeholder_item.stk_id,
        CONCAT(
                        stakeholder.stkname,
                        ' | ',
                        IFNULL(
                                stakeholder_item.brand_name,
                                ''
                        )
                ) AS stkname,
        stakeholder_item.stk_item,
        stakeholder_item.quantity_per_pack,
        itminfo_tab.itm_name,
        itminfo_tab.generic_name
        FROM
                stakeholder
        INNER JOIN stakeholder_item ON stakeholder.stkid = stakeholder_item.stkid
        INNER JOIN itminfo_tab ON stakeholder_item.stk_item = itminfo_tab.itm_id

        WHERE
                stakeholder.stk_type_id = 3 
        AND stakeholder_item.stk_id = $manuf_id
        ORDER BY
                stakeholder.stkname ASC
        limit 1
        ";
//        print_r($qry);exit;
        $rsSql = mysql_query($qry) or die("Error");
        if (mysql_num_rows($rsSql) > 0) {
                while ($row = mysql_fetch_object($rsSql)) {
                    $item = array(
                        'item_id' => $row->stk_item,
                        'manufacturer' => $row->stkname,
                        'manufacturer_id'=>$row->stk_id,
                        'quantity' => $row->quantity_per_pack,
                        'item_name'=>$row->itm_name,
                        'generic_name'=>$row->generic_name
                    );
                }

                $json_array = array();
                //        echo "<option selected='true' value=" . $item['item_id'] . ">" . $item['item_name'] . " " . ((!empty($item['generic_name']) && $item['generic_name'] != 'NULL') ? "[" . $item['generic_name'] . "]" : "") . "</option>";
                //            if (isset($_POST['product'])) {
                $product_data = '<label style="">' . $item['item_name'] . ((!empty($item['generic_name']) && $item['generic_name'] != 'NULL') ? "[" . $item['generic_name'] . "]" : "") . '</label>'
                        . '<input type="hidden" id="product" name="product" value="' . $item['item_id'] . '">';

                $json_array['product'] =$product_data; 
                $manuf_data = '<label style="">' . $item['manufacturer'] . ((!empty($item['generic_name']) && $item['generic_name'] != 'NULL') ? "[" . $item['generic_name'] . "]" : "") . '</label>'
                        . '<input type="hidden" id="manufacturer" name="manufacturer" value="' . $item['manufacturer_id'] . '">';
                $json_array['manufacturer'] = $manuf_data;
                $qty_data = $item['quantity'];
                $json_array['quantity']=$qty_data; 
                $json_array['msg']='Product Details Found'; 
                $json_array['err']='no'; 
        }
        else{
            $json_array['msg']='Invalid Request - Product not found'; 
            $json_array['err']='yes'; 
        }
    header('Content-Type: application/json');
    echo json_encode($json_array);
}
?>