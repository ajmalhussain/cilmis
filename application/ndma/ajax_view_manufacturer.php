<?php
//    echo '<pre>';print_r($_REQUEST);exit;
include("../includes/classes/AllClasses.php");

if (isset($_REQUEST['show_manuf'])) {
    $manuf_id = $_REQUEST['id'];
    $cq = "SELECT
                    stakeholder.stkid,
                    stakeholder_item.stk_id,
                    stakeholder.stkname,
                    stakeholder_item.brand_name,
                    stakeholder_item.pack_length,
                    stakeholder_item.pack_width,
                    stakeholder_item.pack_height,
                    stakeholder_item.net_capacity,
                    stakeholder_item.quantity_per_pack,
                    stakeholder_item.carton_per_pallet,
                    stakeholder_item.gtin,
                    stakeholder_item.gross_capacity,
                    round(stakeholder_item.unit_price,2) as unit_price
                FROM
                        stakeholder
                INNER JOIN stakeholder_item ON stakeholder.stkid = stakeholder_item.stkid
                WHERE
                        stakeholder.stk_type_id = 3
                AND stakeholder_item.stk_id = " . $manuf_id . "
                ORDER BY
                        stakeholder.stkname ASC";
//    echo $cq;exit;
    $checkManufacturer = mysql_query($cq) or die('Err of manuf 5:'.mysql_error());
    $manufacturer = mysql_fetch_assoc($checkManufacturer);
}

header('Content-Type: application/json');
echo json_encode($manufacturer);
