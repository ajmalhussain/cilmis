<?php
include("db.php");
$parent_id=$_POST['parent_id'];
$form10_1 = $_POST['form10_1'];
$sqliu="SELECT *
            FROM
                    mne_timerating
            WHERE
                    basic_id = '$parent_id'";
    $query = $conn->query($sqliu);
    $row_cnt = $query->num_rows;
    
    
$form10_to3_A = $_POST['form10_to3_A'];
$form10_to3_B = $_POST['form10_to3_B'];
$form10_to3_C = $_POST['form10_to3_C'];
$form10_to3_D = $_POST['form10_to3_D'];
$form10_to3_E = $_POST['form10_to3_E'];
$item_group="to3";
   if($row_cnt>0)
    {
        $sqlu1 = "UPDATE mne_timerating SET report_1w='$form10_to3_A',report_1to2w='$form10_to3_B',report2to4w='$form10_to3_C',report4w_above='$form10_to3_D',rating='$form10_to3_E' WHERE basic_id='$parent_id' AND item_group='to3' ";
        $queryu1 = $conn->query($sqlu1);  
      //  echo $sqlu1;
    }
    else {
       $sql = "INSERT INTO 
                                 mne_timerating (basic_id,item_group,report_1w,report_1to2w,report2to4w,report4w_above,rating)
                                 VALUES ('$parent_id','$item_group','$form10_to3_A','$form10_to3_B','$form10_to3_C','$form10_to3_D','$form10_to3_E') ";
      
      $query = $conn->query($sql);
    }
$form10_2 = $_POST['form10_2'];
$form10_to4_A = $_POST['form10_to4_A'];
$form10_to4_B = $_POST['form10_to4_B'];
$form10_to4_C = $_POST['form10_to4_C'];
$form10_to4_D = $_POST['form10_to4_D'];
$form10_to4_E = $_POST['form10_to4_E'];
$item_group="to4";
   if($row_cnt>0)
    {
        $sqlu2 = "UPDATE mne_timerating SET report_1w='$form10_to4_A',report_1to2w='$form10_to4_B',report2to4w='$form10_to4_C',report4w_above='$form10_to4_D',rating='$form10_to4_E' WHERE basic_id='$parent_id' AND item_group='to4' ";
        $queryu2 = $conn->query($sqlu2);  
        //echo $sqlu2;
    }
    else {
        $sql2 = "INSERT INTO 
                                 mne_timerating (basic_id,item_group,report_1w,report_1to2w,report2to4w,report4w_above,rating)
                                 VALUES ('$parent_id','$item_group','$form10_to4_A','$form10_to4_B','$form10_to4_C','$form10_to4_D','$form10_to4_E') ";
        $query2 = $conn->query($sql2);

    }

$form10_3 = $_POST['form10_3'];
$form10_total_A = $_POST['form10_total_A'];
$form10_total_B = $_POST['form10_total_B'];
$form10_total_C = $_POST['form10_total_C'];
$form10_total_D = $_POST['form10_total_D'];
$form10_total_E = $_POST['form10_total_E'];
$item_group="tot";
   if($row_cnt>0)
    {
        $sqlu3 = "UPDATE mne_timerating SET report_1w='$form10_total_A',report_1to2w='$form10_total_B',report2to4w='$form10_total_C',report4w_above='$form10_total_D',rating='$form10_total_E' WHERE basic_id='$parent_id' AND item_group='tot' ";
        $queryu3 = $conn->query($sqlu3);  
       // echo $sqlu3;
    }
    else {
        $sql3 = "INSERT INTO 
                                 mne_timerating (basic_id,item_group,report_1w,report_1to2w,report2to4w,report4w_above,rating)
                                 VALUES ('$parent_id','$item_group','$form10_total_A','$form10_total_B','$form10_total_C','$form10_total_D','$form10_total_E') ";
        $query3 = $conn->query($sql3);
    }
 

 