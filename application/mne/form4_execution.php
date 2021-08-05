<?php

include("db.php");
?>


<?php

$parent_id = $_POST['parent_id'];
$method = $_POST['chke'];
$item_group = 'to3';
$sqliu = "SELECT *
            FROM
                    mne_accuracy
            WHERE
                    basic_id = '$parent_id' AND item_group='$item_group'";
$query = $conn->query($sqliu);
$row_cnt = $query->num_rows;
$form4_comment = $_POST['form4_comment'];


$query = $conn->query("SELECT
                                            itminfo_tab.itm_id,
                                            itminfo_tab.itm_name
                                            FROM
                                            itminfo_tab
                                            WHERE
                                            itminfo_tab.itm_category = 1 AND
                                            itminfo_tab.itm_status = 1 AND
                                            itminfo_tab.method_type IS NOT NULL
                                            ORDER BY
                                            itminfo_tab.method_rank ASC");
while ($row = $query->fetch_assoc()) {
    $pk_id = $row["itm_id"];
    $product_name = $row["itm_name"];


    $bb = "form4_" . $pk_id . "_B";
    $cc = "form4_" . $pk_id . "_C";
    $dd = "form4_" . $pk_id . "_D";
    $ee = "form4_" . $pk_id . "_E";
    $ff = "form4_" . $pk_id . "_F";
    if (!empty($_POST[$ff]) && $_POST[$ff] == 'on') {
        $_POST[$ff] = 1;
    } else {
        $_POST[$ff] = 0;
    }
    $b = $_POST[$bb];
    $c = $_POST[$cc];
    $d = $_POST[$dd];
    $e = $_POST[$ee];
    $f = $_POST[$ff];

    $b = !empty($b) || $b == '0' ? "'$b'" : "NULL";
    $c = !empty($c) || $c == '0' ? "'$c'" : "NULL";
    $d = !empty($b) || $d == '0' ? "'$d'" : "NULL";
    $e = !empty($b) || $e == '0' ? "'$e'" : "NULL";
    $f = !empty($b) || $f == '0' ? "'$f'" : "NULL";

    if ($row_cnt > 0) {
        $sqlu1 = "UPDATE mne_accuracy SET method=$method,bal_lmis=$b,bal_recently_reported=$c,bal_current=$d,phycial_count=$e,stock_accurate=$f WHERE basic_id='$parent_id' AND  prod_id='$pk_id' AND item_group='$item_group' ";

        $queryu1 = $conn->query($sqlu1);
    } else {
        $sql2 = "INSERT INTO 
                                     mne_accuracy (basic_id,prod_id,item_group,method,bal_lmis,bal_recently_reported,bal_current,phycial_count,stock_accurate)
                                     VALUES ('$parent_id','$pk_id','$item_group',$method,$b,$c,$d,$e,$f) ";

        $query2 = $conn->query($sql2);
    }
}

$sql3 = "UPDATE mne_basic_parent SET accuracy_to3_comments='$form4_comment'
WHERE
	pk_id = '$parent_id'";
$query3 = $conn->query($sql3);
