<?php

include("db.php");
//echo '<pre>';print_r($_REQUEST);exit;
$parent_id = $_POST['parent_id'];
$sqliu = "SELECT *
            FROM
                    mne_timeliness
            WHERE
                    basic_id = '$parent_id'";
$query = $conn->query($sqliu);
$row_cnt = $query->num_rows;

//echo '<pre>';print_r($_REQUEST);exit;
$form9_3_fr_id = $_POST['form9_3_fr_id'];

$form9_to3_fr_dd = $_POST['form9_to3_fr_dd'];
$form9_to3_fr_sd = $_POST['form9_to3_fr_sd'];
$form9_to3_fr = $_POST['form9_to3_fr'];

if (!empty($_POST['form9_to3_fr'])) {
    if ($row_cnt > 0) {
        $sqlu11 = "UPDATE mne_timeliness SET due_1w='',due_1to2w='',due_2wto1m='',due_1m_above='',unknown='',not_sub='' WHERE basic_id='$parent_id' AND item_group='to3' AND report='$form9_3_fr_id'";
        $queryu11 = $conn->query($sqlu11);
        $sqlu1 = "UPDATE mne_timeliness SET date_due='$form9_to3_fr_dd',date_sub='$form9_to3_fr_sd',". $form9_to3_fr ."='1' WHERE basic_id='$parent_id' AND item_group='to3' AND report='$form9_3_fr_id'";
        $queryu1 = $conn->query($sqlu1);
    } else {
        $sql = "INSERT INTO 
     mne_timeliness (basic_id,item_group,report,date_due,date_sub," . $form9_to3_fr . ")
     VALUES ('$parent_id','to3','$form9_3_fr_id','$form9_to3_fr_dd','$form9_to3_fr_sd','1') ";   
    }
   
} else {
    if ($row_cnt > 0) {
        $sqlu12 = "UPDATE mne_timeliness SET due_1w='',due_1to2w='',due_2wto1m='',due_1m_above='',unknown='',not_sub='' WHERE basic_id='$parent_id' AND item_group='to3' AND report='$form9_3_fr_id'";
        $queryu12 = $conn->query($sqlu12);
        $sqlu2 = "UPDATE mne_timeliness date_due='$form9_to3_fr_dd',date_sub='$form9_to3_fr_sd' SET WHERE basic_id='$parent_id' AND item_group='to3' AND report='$form9_3_fr_id' ";
        $queryu2 = $conn->query($sqlu2);
    } else {
        $sql = "INSERT INTO 
    mne_timeliness (basic_id,item_group,report,date_due,date_sub)
    VALUES ('$parent_id','to3','$form9_3_fr_id','$form9_to3_fr_dd','$form9_to3_fr_sd') ";
    }
  
}


$query = $conn->query($sql);

$form9_3_sr_id = $_POST['form9_3_sr_id'];

$form9_to3_sr_dd = $_POST['form9_to3_sr_dd'];
$form9_to3_sr_sd = $_POST['form9_to3_sr_sd'];
$form9_to3_sr = $_POST['form9_to3_sr'];

if (!empty($_POST['form9_to3_sr'])) {
    if ($row_cnt > 0) {
        $sqlu13 = "UPDATE mne_timeliness SET due_1w='',due_1to2w='',due_2wto1m='',due_1m_above='',unknown='',not_sub='' WHERE basic_id='$parent_id' AND item_group='to3' AND report='$form9_3_sr_id'";
        $queryu13 = $conn->query($sqlu13);
        $sqlu3 = "UPDATE mne_timeliness SET date_due='$form9_to3_sr_dd',date_sub='$form9_to3_sr_sd',". $form9_to3_sr ."='1' WHERE basic_id='$parent_id' AND item_group='to3' AND report='$form9_3_sr_id' ";
        $queryu3 = $conn->query($sqlu3);
    } else {
       $sql2 = "INSERT INTO 
    mne_timeliness (basic_id,item_group,report,date_due,date_sub," . $form9_to3_sr . ")
    VALUES ('$parent_id','to3','$form9_3_sr_id','$form9_to3_sr_dd','$form9_to3_sr_sd','1') ";
    }
    
} else {
    if ($row_cnt > 0) {
        $sqlu14 = "UPDATE mne_timeliness SET due_1w='',due_1to2w='',due_2wto1m='',due_1m_above='',unknown='',not_sub='' WHERE basic_id='$parent_id' AND item_group='to3' AND report='$form9_3_sr_id'";
        $queryu14 = $conn->query($sqlu14);
        $sqlu4 = "UPDATE mne_timeliness SET date_due='$form9_to3_sr_dd',date_sub='$form9_to3_sr_sd' WHERE basic_id='$parent_id' AND item_group='to3' AND report='$form9_3_sr_id'";
        $queryu4 = $conn->query($sqlu4);
    } else {
         $sql2 = "INSERT INTO 
    mne_timeliness (basic_id,item_group,report,date_due,date_sub)
    VALUES ('$parent_id','to3','$form9_3_sr_id','$form9_to3_sr_dd','$form9_to3_sr_sd') ";
    }
  
}

$query2 = $conn->query($sql2);


$form9_3_tr_id = $_POST['form9_3_tr_id'];
$form9_to3_tr_dd = $_POST['form9_to3_tr_dd'];
$form9_to3_tr_sd = $_POST['form9_to3_tr_sd'];
$form9_to3_tr = $_POST['form9_to3_tr'];
if (!empty($_POST['form9_to3_tr'])) {
    if ($row_cnt > 0) {
        $sqlu15 = "UPDATE mne_timeliness SET due_1w='',due_1to2w='',due_2wto1m='',due_1m_above='',unknown='',not_sub='' WHERE basic_id='$parent_id' AND item_group='to3' AND report='$form9_3_tr_id'";
        $queryu15 = $conn->query($sqlu15);
        $sqlu5 = "UPDATE mne_timeliness SET date_due='$form9_to3_tr_dd',date_sub='$form9_to3_tr_sd',". $form9_to3_tr ."='1' WHERE basic_id='$parent_id' AND item_group='to3' AND report='$form9_3_tr_id'";
        $queryu5 = $conn->query($sqlu5);
    } else {
          $sql3 = "INSERT INTO 
    mne_timeliness (basic_id,item_group,report,date_due,date_sub," . $form9_to3_tr . ")
    VALUES ('$parent_id','to3','$form9_3_tr_id','$form9_to3_tr_dd','$form9_to3_tr_sd','1') ";
    }
  
} else {
    if ($row_cnt > 0) {
        $sqlu16 = "UPDATE mne_timeliness SET due_1w='',due_1to2w='',due_2wto1m='',due_1m_above='',unknown='',not_sub='' WHERE basic_id='$parent_id' AND item_group='to3' AND report='$form9_3_tr_id'";
        $queryu16 = $conn->query($sqlu16);
        $sqlu6 = "UPDATE mne_timeliness SET date_due='$form9_to3_tr_dd',date_sub='$form9_to3_tr_sd' WHERE basic_id='$parent_id' AND item_group='to3' AND report='$form9_3_tr_id' ";
        $queryu6 = $conn->query($sqlu6);
    } else {
          $sql3 = "INSERT INTO 
    mne_timeliness (basic_id,item_group,report,date_due,date_sub)
    VALUES ('$parent_id','to3','$form9_3_tr_id','$form9_to3_tr_dd','$form9_to3_tr_sd') ";
    }  
}


$query3 = $conn->query($sql3);

$form9_4_fr_id = $_POST['form9_4_fr_id'];

$form9_to4_fr_dd = $_POST['form9_to4_fr_dd'];
$form9_to4_fr_sd = $_POST['form9_to4_fr_sd'];
$form9_to4_fr = $_POST['form9_to4_fr'];
if (!empty($_POST['form9_to4_fr'])) {
    if ($row_cnt > 0) {
        $sqlu17 = "UPDATE mne_timeliness SET due_1w='',due_1to2w='',due_2wto1m='',due_1m_above='',unknown='',not_sub='' WHERE basic_id='$parent_id' AND item_group='to4' AND report='$form9_4_fr_id'";
        $queryu17 = $conn->query($sqlu17);
        $sqlu7 = "UPDATE mne_timeliness SET date_due='$form9_to4_fr_dd',date_sub='$form9_to4_fr_sd',". $form9_to4_fr ."='1'  WHERE basic_id='$parent_id' AND item_group='to4' AND report='$form9_4_fr_id'";
        $queryu7 = $conn->query($sqlu7);
    } else {
   $sql = "INSERT INTO 
    mne_timeliness (basic_id,item_group,report,date_due,date_sub," . $form9_to4_fr . ")
    VALUES ('$parent_id','to4','$form9_4_fr_id','$form9_to4_fr_dd','$form9_to4_fr_sd','1') ";
    }  
  
} else {
    if ($row_cnt > 0) {
        $sqlu18 = "UPDATE mne_timeliness SET due_1w='',due_1to2w='',due_2wto1m='',due_1m_above='',unknown='',not_sub='' WHERE basic_id='$parent_id' AND item_group='to4' AND report='$form9_4_fr_id'";
        $queryu18 = $conn->query($sqlu18);
        $sqlu8 = "UPDATE mne_timeliness SET date_due='$form9_to4_fr_dd',date_sub='$form9_to4_fr_sd' WHERE basic_id='$parent_id' AND item_group='to4' AND report='$form9_4_fr_id'";
        $queryu8 = $conn->query($sqlu8);
    } else {
      $sql = "INSERT INTO 
    mne_timeliness (basic_id,item_group,report,date_due,date_sub)
    VALUES ('$parent_id','to4','$form9_4_fr_id','$form9_to4_fr_dd','$form9_to4_fr_sd') ";
    }  
 
}


$query = $conn->query($sql);

$form9_4_sr_id = $_POST['form9_4_sr_id'];

$form9_to4_sr_dd = $_POST['form9_to4_sr_dd'];
$form9_to4_sr_sd = $_POST['form9_to4_sr_sd'];
$form9_to4_sr = $_POST['form9_to4_sr'];
if (!empty($_POST['form9_to4_sr'])) {
    if ($row_cnt > 0) {
        $sqlu19 = "UPDATE mne_timeliness SET due_1w='',due_1to2w='',due_2wto1m='',due_1m_above='',unknown='',not_sub='' WHERE basic_id='$parent_id' AND item_group='to4' AND report='$form9_4_sr_id'";
        $queryu19 = $conn->query($sqlu19);
        $sqlu9 = "UPDATE mne_timeliness SET date_due='$form9_to4_sr_dd',date_sub='$form9_to4_sr_sd',". $form9_to4_sr ."='1'  WHERE basic_id='$parent_id' AND item_group='to4' AND report='$form9_4_sr_id'";
        $queryu9 = $conn->query($sqlu9);
    } else {
          $sql2 = "INSERT INTO 
    mne_timeliness (basic_id,item_group,report,date_due,date_sub," . $form9_to4_sr . ")
    VALUES ('$parent_id','to4','$form9_4_sr_id','$form9_to4_sr_dd','$form9_to4_sr_sd','1') ";
    }  
 
} else {
    if ($row_cnt > 0) {
        $sqlu110 = "UPDATE mne_timeliness SET due_1w='',due_1to2w='',due_2wto1m='',due_1m_above='',unknown='',not_sub='' WHERE basic_id='$parent_id' AND item_group='to4' AND report='$form9_4_sr_id'";
        $queryu110 = $conn->query($sqlu110);
        $sqlu10 = "UPDATE mne_timeliness SET date_due='$form9_to4_sr_dd',date_sub='$form9_to4_sr_sd' WHERE basic_id='$parent_id' AND item_group='to4' AND report='$form9_4_sr_id'";
        $queryu10 = $conn->query($sqlu10);
    } else {
        $sql2 = "INSERT INTO 
    mne_timeliness (basic_id,item_group,report,date_due,date_sub)
    VALUES ('$parent_id','to4','$form9_4_sr_id','$form9_to4_sr_dd','$form9_to4_sr_sd') ";
    }  
 
}


$query2 = $conn->query($sql2);

$form9_4_tr_id = $_POST['form9_4_tr_id'];
$form9_to4_tr_dd = $_POST['form9_to4_tr_dd'];
$form9_to4_tr_sd = $_POST['form9_to4_tr_sd'];
$form9_to4_tr = $_POST['form9_to4_tr'];
if (!empty($_POST['form9_to4_tr'])) {
    if ($row_cnt > 0) {
        $sqlu111 = "UPDATE mne_timeliness SET due_1w='',due_1to2w='',due_2wto1m='',due_1m_above='',unknown='',not_sub='' WHERE basic_id='$parent_id' AND item_group='to4' AND report='$form9_4_tr_id'";
        $queryu111 = $conn->query($sqlu111);
        $sqlu11 = "UPDATE mne_timeliness SET date_due='$form9_to4_tr_dd',date_sub='$form9_to4_tr_sd',". $form9_to4_tr ."='1'  WHERE basic_id='$parent_id' AND item_group='to4' AND report='$form9_4_tr_id'";
        $queryu11 = $conn->query($sqlu11);
    } else {
         $sql3 = "INSERT INTO 
    mne_timeliness (basic_id,item_group,report,date_due,date_sub," . $form9_to4_tr . ")
    VALUES ('$parent_id','to4','$form9_4_tr_id','$form9_to4_tr_dd','$form9_to4_tr_sd','1') ";
    }  

} else {
    if ($row_cnt > 0) {
        $sqlu112 = "UPDATE mne_timeliness SET due_1w='',due_1to2w='',due_2wto1m='',due_1m_above='',unknown='',not_sub='' WHERE basic_id='$parent_id' AND item_group='to4' AND report='$form9_4_tr_id'";
        $queryu112 = $conn->query($sqlu112);
        $sqlu12 = "UPDATE mne_timeliness date_due='$form9_to4_tr_dd',date_sub='$form9_to4_tr_sd' SET WHERE basic_id='$parent_id' AND item_group='to4' AND report='$form9_4_tr_id'";
        $queryu12 = $conn->query($sqlu12);
    } else {
 $sql3 = "INSERT INTO 
    mne_timeliness (basic_id,item_group,report,date_due,date_sub)
    VALUES ('$parent_id','to4','$form9_4_tr_id','$form9_to4_tr_dd','$form9_to4_tr_sd') ";
    }  
}


$query3 = $conn->query($sql3);

$form9_comment = $_POST['form9_comment'];
$sql3 = "UPDATE mne_basic_parent SET timeliness_comments='$form9_comment'
WHERE
	pk_id = '$parent_id'";
$query3 = $conn->query($sql3);
