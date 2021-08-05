<?php

include("db.php");
?>
<?php

//                            if(isset($_POST['submit1']))
//                            {
//echo("in form1 execution file");
$parent_id = $_POST['parent_id'];
$sqliu = "SELECT *
            FROM
                    mne_basic_child
            WHERE
                    basic_id = '$parent_id'";
$query = $conn->query($sqliu);
$row_cnt = $query->num_rows;
if (isset($_POST['form1_to3_D'])) {
    $form1_to3_D = $_POST['form1_to3_D'];
    $form1_to3_I = $_POST['form1_to3_I'];
    $form1_to3_J = $_POST['form1_to3_J'];
    $form1_to3_K = $_POST['form1_to3_K'];
    $form1_to3_L = $_POST['form1_to3_L'];

    if ($row_cnt > 0) {
        $sqlu1 = "UPDATE mne_basic_child SET basic_id='$parent_id',description='$form1_to3_D',available='$form1_to3_I',accuracy='$form1_to3_J',timeliness='$form1_to3_K',total='$form1_to3_L' WHERE basic_id='$parent_id' AND description='TO3: Family Planning and Resproductive Health' ";
        $queryu1 = $conn->query($sqlu1);
    } else {
        $sql = "INSERT INTO 
                        mne_basic_child (basic_id,description,available,accuracy,timeliness,total)
                        VALUES ('$parent_id','$form1_to3_D','$form1_to3_I','$form1_to3_J','$form1_to3_K','$form1_to3_L') ";
        $query = $conn->query($sql);
    }



    $form1_to4_D = $_POST['form1_to4_D'];
    $form1_to4_I = $_POST['form1_to4_I'];
    $form1_to4_J = $_POST['form1_to4_J'];
    $form1_to4_K = $_POST['form1_to4_K'];
    $form1_to4_L = $_POST['form1_to4_L'];

    if ($row_cnt > 0) {
        $sqlu2 = "UPDATE mne_basic_child SET basic_id='$parent_id',description='$form1_to4_D',available='$form1_to4_I',accuracy='$form1_to4_J',timeliness='$form1_to4_K',total='$form1_to4_L' WHERE basic_id='$parent_id' AND description='TO4: Maternal, Newborn and Child Health' ";
        $queryu2 = $conn->query($sqlu2);
    } else {
        $sql2 = "INSERT INTO 
                                     mne_basic_child (basic_id,description,available,accuracy,timeliness,total)
                                     VALUES ('$parent_id','$form1_to4_D','$form1_to4_I','$form1_to4_J','$form1_to4_K','$form1_to4_L') ";
        $query = $conn->query($sql2);
    }

    $form1_all_D = $_POST['form1_all_D'];
    $form1_all_I = $_POST['form1_all_I'];
    $form1_all_J = $_POST['form1_all_J'];
    $form1_all_K = $_POST['form1_all_K'];
    $form1_all_L = $_POST['form1_all_L'];
    if ($row_cnt > 0) {
        $sqlu3 = "UPDATE mne_basic_child SET basic_id='$parent_id',description='$form1_all_D',available='$form1_all_I',accuracy='$form1_all_J',timeliness='$form1_all_K',total='$form1_all_L' WHERE basic_id='$parent_id' AND description='ALL TO: Average Data Confidence Ratings' ";
        $queryu3 = $conn->query($sqlu3);
    } else {

        $sql = "INSERT INTO 
                                     mne_basic_child (basic_id,description,available,accuracy,timeliness,total)
                                     VALUES ('$parent_id','$form1_all_D','$form1_all_I','$form1_all_J','$form1_all_K','$form1_all_L') ";
        $query = $conn->query($sql);
    }
}
