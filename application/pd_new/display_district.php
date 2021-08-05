<?php
include("../includes/classes/AllClasses.php");
//
include(PUBLIC_PATH."html/header.php");

    $province_id=$_POST['provinceId'];
    print_r("province id  is".$province_id);
if(isset($_POST['first_level'])&&$province_id==0){
    if (($_POST['first_level']) == 3) {
        $sql = "select * from tbl_locations where LocLvl=3 order by LocName ASC";
        $res = mysql_query($sql);
        if (mysql_num_rows(mysql_query($sql)) > 0) {
            echo "<option value=''>Select</option>";
            while ($row = mysql_fetch_array($res)) {
                echo '<option value="' . $row["PkLocID"] . '">' . $row["LocName"] . '</option>';
            }
        }
    }
    }
 if(isset($_POST['first_level'])&&$province_id!=0){
     
        $sql = "select * from tbl_locations where LocLvl=3 AND ParentID=".$province_id
                . " order by LocName ASC";
        $res = mysql_query($sql);
        if (mysql_num_rows(mysql_query($sql)) > 0) {
            echo "<option value=''>Select</option>";
            while ($row = mysql_fetch_array($res)) {
                echo '<option value="' . $row["PkLocID"] . '">' . $row["LocName"] . '</option>';
            }
        }
    }

?>