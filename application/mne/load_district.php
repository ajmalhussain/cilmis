<?php

include("db.php");

$province = $_POST['province'];

?>

 <input type="hidden" id="district_h" name="district_h" >
<select name="district" class="form-control"  required  id="district" onchange="on_change_district();  javacript: var dist = this.options[selectedIndex].text; document.getElementById('district_h').value = dist;">
    <option value=""> Select </option>
    <?php
    $query = $conn->query("SELECT
                                    tbl_locations.PkLocID,
                                    tbl_locations.LocName
                                   FROM
                                    tbl_locations
                                   WHERE
                                    tbl_locations.ParentID = '$province'
                                   AND tbl_locations.LocLvl = 3
                                   AND tbl_locations.LocType = 4
                                   ORDER BY tbl_locations.LocName");
    while ($row = $query->fetch_assoc()) {
        $pk_id = $row["PkLocID"];
        $district_name = $row["LocName"];
        ?>
        <option value= "<?php echo $pk_id; ?>"  <?php if(!empty($_POST['district']) && $_POST['district']==$pk_id){ echo 'selected="selected"';}?> > <?php echo $district_name; ?> </option>
    <?php 
    }
    ?>
</select>
 
