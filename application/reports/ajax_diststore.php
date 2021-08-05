<?php
include("../includes/classes/Configuration.inc.php");
include(APP_PATH . "includes/classes/db.php");
$province = $_REQUEST['province'];
if ((isset($_REQUEST['province']))) {
        
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
