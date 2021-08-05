<?php

include("db.php");
$facility = $_POST['facility'];
$date_visit = $_POST['date_visit'];

$sqls = "SELECT *
                             FROM mne_basic_parent 
                             WHERE ( fac_id = '$facility' AND YEAR(date_visit)=YEAR('$date_visit')) ";

$query = $conn->query($sqls);

$row = $query->fetch_assoc();
echo json_encode($row);
//$parent_id = $row["pk_id"];
//$name = $row["name"];
//$sig = $row["sig"];
//$stock_mgr = $row["stock_mgr"];
//$item_group = $row["item_group"];
//
//
//$count = $query->num_rows;
?>