<?php
include("../includes/classes/Configuration.inc.php");
include(APP_PATH . "includes/classes/db.php");
$province = $_REQUEST['province'];
if ((isset($_REQUEST['province']))) {
        if(!empty($_SESSION['user_stakeholder1']))
            {       
    $queryItem = "SELECT DISTINCT
                    tbl_warehouse.wh_name,
                    tbl_warehouse.wh_id
                    FROM
                    tbl_warehouse
                     WHERE
                    tbl_warehouse.prov_id IN (3,6) AND 
                    tbl_warehouse.is_allowed_im = 1 AND 
                    tbl_warehouse.stkid  = ".$_SESSION['user_stakeholder1']."
                    AND tbl_warehouse.wh_id IN (25,72710,3912,72709,356,72711,72712,3856)
                    ORDER by wh_id
                             ";
            }
 else {
         $queryItem = "SELECT DISTINCT
                                                            tbl_warehouse.wh_name,
                                                            tbl_warehouse.wh_id
                                                            FROM
                                                            tbl_warehouse
                                                             WHERE
                                                            tbl_warehouse.prov_id=$province AND 
                                                            tbl_warehouse.is_allowed_im = 1 AND 
                                                            tbl_warehouse.stkid =7
                                                            ORDER by wh_name
                                                                     ";
 }
//    print_r($queryItem);exit;
    //Result
    $rsprov = mysql_query($queryItem) or die();
    ?>
    <option value="">--Select--</option>
    <?php
    while ($rowItem = mysql_fetch_array($rsprov)) {
        if ($selwh == $rowItem['wh_id']) {
            $sel = "selected='selected'";
        } else {
            $sel = "";
        }
        ?>
        <?php //Populate itm_id combo  ?>
        <option value="<?php echo $rowItem['wh_id']; ?>" <?php echo $sel; ?>><?php echo $rowItem['wh_name']; ?></option>
        <?php
    }
}

