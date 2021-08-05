<?php
include("db.php");
$parent_id=$_POST['parent_id'];

$sqliu="SELECT *
            FROM
                    mne_avl_rating
            WHERE
                    basic_id = '$parent_id'";
    $query = $conn->query($sqliu);
    $row_cnt = $query->num_rows;

$form7_to3_A = $_POST['form7_to3_A'];
$form7_to3_B = $_POST['form7_to3_B'];
$form7_to3_C = $_POST['form7_to3_C'];
$form7_to3_D = $_POST['form7_to3_D'];
$form7_to3_E = $_POST['form7_to3_E'];
$form7_to3_F = $_POST['form7_to3_F'];

    if($row_cnt>0)
    {
       $sqlu1 = "UPDATE mne_avl_rating SET no_prod='$form7_to3_A',no_req_data='$form7_to3_B',tot_req_data='$form7_to3_C',tot_elements='$form7_to3_D',perc_elements='$form7_to3_E',rating='$form7_to3_F'  WHERE basic_id='$parent_id' AND item_group='to3' ";
        $queryu1 = $conn->query($sqlu1);     
    }
    else {
        $sql = "INSERT INTO 
                                 mne_avl_rating (basic_id,item_group,no_prod,no_req_data,tot_req_data,tot_elements,perc_elements,rating)
                                 VALUES ('$parent_id','to3','$form7_to3_A','$form7_to3_B','$form7_to3_C','$form7_to3_D','$form7_to3_E','$form7_to3_F') ";
        $query = $conn->query($sql);
    }


$form7_to4_A = $_POST['form7_to4_A'];
$form7_to4_B = $_POST['form7_to4_B'];
$form7_to4_C = $_POST['form7_to4_C'];
$form7_to4_D = $_POST['form7_to4_D'];
$form7_to4_E = $_POST['form7_to4_E'];
$form7_to4_F = $_POST['form7_to4_F'];
    if($row_cnt>0)
    {
       $sqlu1 = "UPDATE mne_avl_rating SET no_prod='$form7_to4_A',no_req_data='$form7_to4_B',tot_req_data='$form7_to4_C',tot_elements='$form7_to4_D',perc_elements='$form7_to4_E',rating='$form7_to4_F'  WHERE basic_id='$parent_id' AND item_group='to4' ";
        $queryu1 = $conn->query($sqlu1);     
    }
    else {
        $sql2 = "INSERT INTO 
                                 mne_avl_rating (basic_id,item_group,no_prod,no_req_data,tot_req_data,tot_elements,perc_elements,rating)
                                 VALUES ('$parent_id','to4','$form7_to4_A','$form7_to4_B','$form7_to4_C','$form7_to4_D','$form7_to4_E','$form7_to4_F') ";
        $query2 = $conn->query($sql2);
    }


$form7_total_A = $_POST['form7_total_A'];
$form7_total_B = $_POST['form7_total_B'];
$form7_total_C = $_POST['form7_total_C'];
$form7_total_D = $_POST['form7_total_D'];
$form7_total_E = $_POST['form7_total_E'];
$form7_total_F = $_POST['form7_total_F'];
if($row_cnt>0)
    {
       $sqlu1 = "UPDATE mne_avl_rating SET no_prod='$form7_total_A',no_req_data='$form7_total_B',tot_req_data='$form7_total_C',tot_elements='$form7_total_D',perc_elements='$form7_total_E',rating='$form7_total_F'  WHERE basic_id='$parent_id' AND item_group='tot' ";
        $queryu1 = $conn->query($sqlu1);     
    }
    else {
        $sql3 = "INSERT INTO 
                                 mne_avl_rating (basic_id,item_group,no_prod,no_req_data,tot_req_data,tot_elements,perc_elements,rating)
                                 VALUES ('$parent_id','tot','$form7_total_A','$form7_total_B','$form7_total_C','$form7_total_D','$form7_total_E','$form7_total_F')";
        $query3 = $conn->query($sql3);
    }                

