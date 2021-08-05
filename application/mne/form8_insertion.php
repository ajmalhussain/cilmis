<?php

include("db.php");
$parent_id=$_POST['parent_id'];
$form8_1 = $_POST['form8_1'];
$form8_2 = $_POST['form8_2'];
$form8_3 = $_POST['form8_3'];

$sqliu="SELECT *
            FROM
                    mne_acc_rating
            WHERE
                    basic_id = '$parent_id'";
    $query = $conn->query($sqliu);
    $row_cnt = $query->num_rows;

$form8_to3_A = $_POST['form8_to3_A'];
$form8_to3_B = $_POST['form8_to3_B'];
$form8_to3_C = $_POST['form8_to3_C'];
$form8_to3_D = $_POST['form8_to3_D'];
    if($row_cnt>0)
    {
       $sqlu1 = "UPDATE mne_acc_rating SET no_prod='$form8_to3_A',no_accurate_prod='$form8_to3_B',perc_accurate='$form8_to3_C',rating='$form8_to3_D'  WHERE basic_id='$parent_id' AND item_group='to3' ";
        $queryu1 = $conn->query($sqlu1);  

    }
    else {
        $sql = "INSERT INTO 
                                 mne_acc_rating (basic_id,item_group,no_prod,no_accurate_prod,perc_accurate,rating)
                                 VALUES ('$parent_id','to3','$form8_to3_A','$form8_to3_B','$form8_to3_C','$form8_to3_D') ";

        $query = $conn->query($sql);
    }


$form8_to4_A = $_POST['form8_to4_A'];
$form8_to4_B = $_POST['form8_to4_B'];
$form8_to4_C = $_POST['form8_to4_C'];
$form8_to4_D = $_POST['form8_to4_D'];
    if($row_cnt>0)
    {
       $sqlu2 = "UPDATE mne_acc_rating SET no_prod='$form8_to4_A',no_accurate_prod='$form8_to4_B',perc_accurate='$form8_to4_C',rating='$form8_to4_D'  WHERE basic_id='$parent_id' AND item_group='to4' ";
        $queryu1 = $conn->query($sqlu2);  
    }
    else {
        $sql2 = "INSERT INTO 
                                 mne_acc_rating (basic_id,item_group,no_prod,no_accurate_prod,perc_accurate,rating)
                                 VALUES ('$parent_id','to4','$form8_to4_A','$form8_to4_B','$form8_to4_C','$form8_to4_D') ";
$query2 = $conn->query($sql2);
    }


$form8_total_A = $_POST['form8_total_A'];
$form8_total_B = $_POST['form8_total_B'];
$form8_total_C = $_POST['form8_total_C'];
$form8_total_D = $_POST['form8_total_D'];
    if($row_cnt>0)
    {
        $sqlu3 = "UPDATE mne_acc_rating SET no_prod='$form8_total_A',no_accurate_prod='$form8_total_B',perc_accurate='$form8_total_C',rating='$form8_total_D'  WHERE basic_id='$parent_id' AND item_group='tot' ";
        $queryu1 = $conn->query($sqlu3);  
    }
    else {
       $sql3 = "INSERT INTO 
                                 mne_acc_rating (basic_id,item_group,no_prod,no_accurate_prod,perc_accurate,rating)
                                 VALUES ('$parent_id','tot','$form8_total_A','$form8_total_B','$form8_total_C','$form8_total_D')";
        $query3 = $conn->query($sql3);
    }
 
            