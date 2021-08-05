<?php

include("db.php");
?>


<?php

$form6_comment = $_POST['form6_comment'];
$parent_id=$_POST['parent_id'];
$item_group = 'to4';
$sqliu="SELECT *
            FROM
                    mne_accuracy
            WHERE
                    basic_id = '$parent_id' AND item_group='$item_group'";
    $query = $conn->query($sqliu);
    $row_cnt = $query->num_rows;



$query = $conn->query("SELECT * from mne_to4");

while ($row = $query->fetch_assoc()) {
    $pk_id = $row["pk_id"];
    $product_name = $row["product_to4"];

    $dd = "form6_" . $pk_id . "_D";
    $ee = "form6_" . $pk_id . "_E";
    $ff = "form6_" . $pk_id . "_F";
    if (!empty($_POST[$ff]) && $_POST[$ff] == 'on') {
        $_POST[$ff] = 1;
    } else {
        $_POST[$ff] = 0;
    }
   
    $d = $_POST[$dd];
    $e = $_POST[$ee];
    $f = $_POST[$ff];

    if($row_cnt>0)
    {
       $sqlu1 = "UPDATE mne_accuracy SET bal_current='$d',phycial_count='$e',stock_accurate='$f'  WHERE basic_id='$parent_id' AND  prod_id='$pk_id' AND item_group='$item_group' ";
        $queryu1 = $conn->query($sqlu1);     
    }
    else {
        $sql2 = "INSERT INTO 
                                     mne_accuracy (basic_id,prod_id,item_group,bal_current,phycial_count,stock_accurate)
                                     VALUES ('$parent_id','$pk_id','$item_group','$d','$e','$f') ";
    $query2 = $conn->query($sql2);
    }

}


$sql3 = "UPDATE mne_basic_parent SET accuracy_to4_comments='$form6_comment'
WHERE
	pk_id = '$parent_id'";
$query3 = $conn->query($sql3);
