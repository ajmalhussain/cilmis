<?php
include("db.php");
?>
<?php

$parent_id=$_POST['parent_id'];
$form2_item_group = $_POST['form2_item_group'];
$sqliu="SELECT *
            FROM
                    mne_basic_followup
            WHERE
                    basic_id = '$parent_id' AND item_group_id='$form2_item_group'";
    $query = $conn->query($sqliu);
    $row_cnt = $query->num_rows;
    


$sql = "SELECT *
                                            FROM
                                            mne_followup_values";
$query = $conn->query($sql);

while ($row = $query->fetch_assoc()) {
    $pk_id = $row['pk_id'];
    $values = $row['values'];
 
    $d = "form2_". $pk_id ."_D";
    $m = "form2_" . $pk_id . "_M";
    $n = "form2_" . $pk_id . "_N";
    $o = "form2_" . $pk_id . "_O";
    $p = "form2_" . $pk_id . "_P";
    $q = "form2_" . $pk_id . "_Q";

    $form2_D = $_POST[$d];
    $form2_M = $_POST[$m];
    $form2_N = $_POST[$n];
    $form2_O = $_POST[$o];
    $form2_P = $_POST[$p];
    $form2_Q = $_POST[$q];
    
     if($row_cnt>0)
    {
        $sqlu1 = "UPDATE mne_basic_followup SET basic_id='$parent_id',item_group_id='$form2_item_group',followup_value_id='$form2_D',description='$form2_M',actions='$form2_N',support='$form2_O',responsible='$form2_P',completion_date='$form2_Q' WHERE basic_id='$parent_id' AND  followup_value_id='$form2_D' AND item_group_id='$form2_item_group' ";
        $queryu1 = $conn->query($sqlu1);
     
    }
    else {
       $sql2 = "INSERT INTO 
                                     mne_basic_followup (basic_id,item_group_id,followup_value_id,description,actions,support,responsible,completion_date)
                                     VALUES ('$parent_id','$form2_item_group','$form2_D','$form2_M','$form2_N','$form2_O','$form2_P','$form2_Q') ";
        $query2 = $conn->query($sql2);
    }
 

}




