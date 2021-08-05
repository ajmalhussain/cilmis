<?php

include("db.php");

$district = (!empty($_POST['district']))?$_POST['district']:'';
$level = $_POST['lvl'];

$where = "1=1";
if(!empty($district) && $level != 1)
    $where .= "  AND  tbl_warehouse.dist_id = ".$district."  ";
$where .= " AND stakeholder.lvl = ".$level."  ";
?>

 <input type="hidden" id="facility_h" name="facility_h" >
<select name="facility"  required  class="form-control" id="facility" onchange="javacript: var fac = this.options[selectedIndex].text; document.getElementById('facility_h').value = fac;">
   
    <option value=""> Select</option>
    <?php
    $query = $conn->query("SELECT
                            tbl_warehouse.wh_id,
                            tbl_warehouse.wh_name,
                            stakeholder.lvl,
                            tbl_warehouse.dist_id,
                            tbl_warehouse.prov_id,
                            tbl_warehouse.stkid,
                            stk.stkname
                            FROM
                            tbl_warehouse
                            INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
                            INNER JOIN stakeholder AS stk ON tbl_warehouse.stkid = stk.stkid
                            WHERE
                           $where
                            ORDER BY
                            tbl_warehouse.wh_name ASC");
    while ($row = $query->fetch_assoc()) {
        $pk_id = $row["wh_id"];
        $facility_name = $row["wh_name"] . ' - ' . $row["stkname"];
        ?>
        <option value="<?php echo $pk_id; ?>"   <?php if(!empty($_POST['facility']) && $_POST['facility']==$pk_id){ echo 'selected="selected"';}?> > <?php echo $facility_name; ?> </option>
    <?php 
    }
    ?>
</select>
