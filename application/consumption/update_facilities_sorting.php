<?php

include("../includes/classes/AllClasses.php");

$sorting = $_POST['sorting'];

foreach ($sorting as $wh_id => $val) {
    mysql_query("UPDATE tbl_warehouse SET wh_rank = $val WHERE wh_id=$wh_id");
}

header("location: wh_data_entry.php");