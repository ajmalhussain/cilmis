<?php

//    echo '<pre>';print_r($_REQUEST);exit;
/**
 * add_action_manufacturer
 * @package im
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
//Including AllClasses file
include("../includes/classes/AllClasses.php");
$strDo = "Add";
$nstkId = 0;
$autorun = false;


if (!empty($_REQUEST['new_manufacturer'])) {
    //Getting new_manufacturer
//    echo '<pre>';print_r($_REQUEST);exit;
    $new_manufacturer = $_REQUEST['new_manufacturer'];
    //Getting item_pack_size_id
    $item_pack_size_id = $_REQUEST['item_pack_size_id'];
    $brand_name = (!empty($_REQUEST['brand_name'])) ? mysql_real_escape_string($_REQUEST['brand_name']) : '';

    //check manufacturer
    $checkManufacturer = mysql_query("select stkid,stkname from stakeholder where stkname='" . $new_manufacturer . "' AND stk_type_id = 3") or die('Err of manuf 1:'.mysql_error());
    $manufacturer = mysql_num_rows($checkManufacturer);
    $stkRow = mysql_fetch_assoc($checkManufacturer);
    //if not exist for any product
    if ($manufacturer < 1) {
        // Get Stakeholder Item
        $getStkOrder = "SELECT
							MAX(stakeholder.stkorder) + 1 AS stkorder
						FROM
							stakeholder
						WHERE
							stakeholder.stk_type_id = 3";
        //Query result
        $getStkOrderRes = mysql_fetch_array(mysql_query($getStkOrder));
        $stkOrder = $getStkOrderRes['stkorder'];
        //Assigning data to objstk
        //stkname
        $objstk->m_stkname = $new_manufacturer;
        //stkorder
        $objstk->m_stkorder = $stkOrder;
        //ParentID
        $objstk->ParentID = '1';
        //stk_type_id
        $objstk->m_stk_type_id = '3';
        //level
        $objstk->m_lvl = '1';
        //Add Stakeholder
        $stkid = $objstk->AddStakeholder();
    } else {
        $stkid = $stkRow['stkid'];
    }

    $getStkItem = "select * from stakeholder_item where stk_item='" . $item_pack_size_id . "' AND stkid='" . $stkid . "' AND brand_name = '" . $brand_name . "' ";
//    echo $getStkItem;exit;
    $resStkItem = mysql_query($getStkItem) or die('Err of manuf 2:'.mysql_error());
    $numStkItem = mysql_num_rows($resStkItem);
//    echo $numStkItem;exit;
    if ($numStkItem == 0) {
        //stkid
        $objstakeholderitem->m_stkid = $stkid;
        //stk_item
        $objstakeholderitem->m_stk_item = $item_pack_size_id;
        //brand_name
        $objstakeholderitem->brand_name = (!empty($_REQUEST['brand_name'])) ? mysql_real_escape_string($_REQUEST['brand_name']) : '';
        //carton_per_pallet
        $objstakeholderitem->carton_per_pallet = (!empty($_REQUEST['carton_per_pallet'])) ? mysql_real_escape_string($_REQUEST['carton_per_pallet']) : '';
        //quantity_per_pack
        $objstakeholderitem->quantity_per_pack = (!empty($_REQUEST['quantity_per_pack'])) ? mysql_real_escape_string($_REQUEST['quantity_per_pack']) : '';
        //gtin
        $objstakeholderitem->gtin = (!empty($_REQUEST['gtin'])) ? mysql_real_escape_string($_REQUEST['gtin']) : '';
        //gross_capacity
        $objstakeholderitem->gross_capacity = (!empty($_REQUEST['gross_capacity'])) ? mysql_real_escape_string($_REQUEST['gross_capacity']) : '';
        //net_capacity
        $objstakeholderitem->net_capacity = (!empty($_REQUEST['net_capacity'])) ? mysql_real_escape_string($_REQUEST['net_capacity']) : '';
        //
        $objstakeholderitem->pack_length = (!empty($_REQUEST['pack_length'])) ? mysql_real_escape_string($_REQUEST['pack_length']) : '';
        //pack_length
        $objstakeholderitem->pack_width = (!empty($_REQUEST['pack_width'])) ? mysql_real_escape_string($_REQUEST['pack_width']) : '';
        //pack_height
        $objstakeholderitem->pack_height = (!empty($_REQUEST['pack_height'])) ? mysql_real_escape_string($_REQUEST['pack_height']) : '';
        $objstakeholderitem->unit_price = (!empty($_REQUEST['unit_price'])) ? mysql_real_escape_string($_REQUEST['unit_price']) : '';
        //Add stakeholder item1
        $stkItemId = $objstakeholderitem->Addstakeholderitem1();
    }
    //Query for checking manfacturer
    $checkManufacturer = mysql_query("SELECT
                                                stakeholder.stkid,
                                                stakeholder_item.stk_id,
                                                CONCAT(stakeholder.stkname, ' | ' ,IFNULL(stakeholder_item.brand_name, '')) AS stkname
                                        FROM
                                                stakeholder
                                        INNER JOIN stakeholder_item ON stakeholder.stkid = stakeholder_item.stkid
                                        WHERE
                                                stakeholder.stk_type_id = 3
                                        AND stakeholder_item.stk_item = '" . $item_pack_size_id . "'
                                        ORDER BY
                                                stakeholder.stkname ASC") or die('Err of manuf 3:'.mysql_error());
    //Query results
    $manufacturer = mysql_num_rows($checkManufacturer);


    echo '<option value="">Select</option>';
    while ($val = mysql_fetch_assoc($checkManufacturer)) {
        $sel = ($stkItemId == $val['stk_id']) ? 'selected="selected"' : '';
        echo '<option value="' . $val['stk_id'] . '" ' . $sel . '>' . $val['stkname'] . '</option>';
    }
}

if (!empty($_REQUEST['edit_manufacturer'])) {
    //Getting new_manufacturer
    //echo '<pre>';print_r($_REQUEST);exit;
    $new_manufacturer = $_REQUEST['new_manufacturer_edit'];
    //Getting item_pack_size_id
    $item_pack_size_id = $_REQUEST['item_pack_size_id'];
    $brand_name = (!empty($_REQUEST['brand_name_edit'])) ? mysql_real_escape_string($_REQUEST['brand_name_edit']) : '';

    //check manufacturer
    $checkManufacturer = mysql_query("select stkid,stkname from stakeholder where stkname='" . $new_manufacturer . "' AND stk_type_id = 3") or die('Err of manuf 1:'.mysql_error());
    $manufacturer = mysql_num_rows($checkManufacturer);
    $stkRow = mysql_fetch_assoc($checkManufacturer);
    //if not exist for any product
    
    $stkid = $stkRow['stkid'];
    
        //stkid
        $objstakeholderitem->m_stkid = $stkid;
        //stk_item
        $objstakeholderitem->m_stk_item = $item_pack_size_id;
        //brand_name
        $objstakeholderitem->brand_name = (!empty($_REQUEST['brand_name_edit'])) ? mysql_real_escape_string($_REQUEST['brand_name_edit']) : '';
        //carton_per_pallet
        $objstakeholderitem->carton_per_pallet = (!empty($_REQUEST['carton_per_pallet_edit'])) ? mysql_real_escape_string($_REQUEST['carton_per_pallet_edit']) : '';
        //quantity_per_pack
        $objstakeholderitem->quantity_per_pack = (!empty($_REQUEST['quantity_per_pack_edit'])) ? mysql_real_escape_string($_REQUEST['quantity_per_pack_edit']) : '';
        //gtin
        $objstakeholderitem->gtin = (!empty($_REQUEST['gtin_edit'])) ? mysql_real_escape_string($_REQUEST['gtin_edit']) : '';
        //gross_capacity
        $objstakeholderitem->gross_capacity = (!empty($_REQUEST['gross_capacity_edit'])) ? mysql_real_escape_string($_REQUEST['gross_capacity_edit']) : '';
        //net_capacity
        $objstakeholderitem->net_capacity = (!empty($_REQUEST['net_capacity_edit'])) ? mysql_real_escape_string($_REQUEST['net_capacity_edit']) : '';
        //
        $objstakeholderitem->pack_length = (!empty($_REQUEST['pack_length_edit'])) ? mysql_real_escape_string($_REQUEST['pack_length_edit']) : '';
        //pack_length
        $objstakeholderitem->pack_width = (!empty($_REQUEST['pack_width_edit'])) ? mysql_real_escape_string($_REQUEST['pack_width_edit']) : '';
        //pack_height
        $objstakeholderitem->pack_height = (!empty($_REQUEST['pack_height_edit'])) ? mysql_real_escape_string($_REQUEST['pack_height_edit']) : '';
        
        $objstakeholderitem->m_npkId = (!empty($_REQUEST['edit_manufacturer'])) ? mysql_real_escape_string($_REQUEST['edit_manufacturer']) : '';
        $objstakeholderitem->unit_price = (!empty($_REQUEST['unit_price_edit'])) ? mysql_real_escape_string($_REQUEST['unit_price_edit']) : '';
        //Add stakeholder item1
//        $stkItemId = $objstakeholderitem->Addstakeholderitem1();
        $stkItemId = $objstakeholderitem->UpdateStakeholderItem();
    
 
//END of edit manufacturer
}

if (isset($_REQUEST['show'])) {
    //Getting product
    $item_pack_size_id = $_REQUEST['product'];
    $cq = "SELECT
                                            stakeholder.stkid,
                                            stakeholder_item.stk_id,
                                            CONCAT(stakeholder.stkname, ' | ' ,IFNULL(stakeholder_item.brand_name, '')) AS stkname,
                                            stakeholder_item.quantity_per_pack as qty_per_carton
                                    FROM
                                            stakeholder
                                    INNER JOIN stakeholder_item ON stakeholder.stkid = stakeholder_item.stkid
                                    WHERE
                                            stakeholder.stk_type_id = 3
                                    AND stakeholder_item.stk_item = " . $item_pack_size_id . "
                                    ORDER BY
                                            stakeholder.stkname ASC";
//    echo $cq;exit;
    $checkManufacturer = mysql_query($cq) or die('Err of manuf 4:'.mysql_error());
    $manufacturer = mysql_num_rows($checkManufacturer);
    echo '<option value="">Select</option>';
    while ($val = mysql_fetch_assoc($checkManufacturer)) {
        echo '<option value="' . $val['stk_id'] . '">' . $val['stkname'] . '['.$val['qty_per_carton'].']</option>';
    }
}