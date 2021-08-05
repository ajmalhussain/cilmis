<?php

include("db.php");

$district = $_POST['district'];

?>

 <input type="hidden" id="facility_h" name="facility_h" >
<select name="facility"  required  class="form-control" id="facility" onchange="javacript: var fac = this.options[selectedIndex].text; document.getElementById('facility_h').value = fac;">
   
    <option value=""> Select</option>
    <?php
    $query = $conn->query("SELECT
                                    tbl_warehouse.wh_id,
                                    tbl_warehouse.wh_name
                                    FROM
                                    tbl_warehouse
                                    WHERE
                                    tbl_warehouse.dist_id = '$district'
                                    ORDER BY
                                    tbl_warehouse.wh_name");
    while ($row = $query->fetch_assoc()) {
        $pk_id = $row["wh_id"];
        $facility_name = $row["wh_name"];
        ?>
        <option value="<?php echo $pk_id; ?>"   <?php if(!empty($_POST['facility']) && $_POST['facility']==$pk_id){ echo 'selected="selected"';}?> > <?php echo $facility_name; ?> </option>
    <?php 
    }
    ?>
</select>
