<?php

/**
 * update_batch_by_ajax
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

$val        = $_REQUEST['val'];
$batch_id   = $_REQUEST['batch'];
$field_to_edit   = $_REQUEST['property'];

if(!empty($field_to_edit) && $field_to_edit=='dtl')
{
    $qry = "UPDATE stock_batch  SET dtl = '$val'  WHERE batch_id = $batch_id ";
    mysql_query($qry);
}
elseif(!empty($field_to_edit) && $field_to_edit=='phy_inspection')
{
    $qry = "UPDATE stock_batch  SET phy_inspection = '$val'  WHERE batch_id = $batch_id ";
    mysql_query($qry);
}

echo 'Batch info updated.';
?>