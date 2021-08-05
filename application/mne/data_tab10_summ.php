<?php
include("db.php");
$parent_id = $_POST['parent_id'];
?> 

                <?php
                $sql_3_11 = "SELECT
count(mne_timeliness.pk_id) total_A
FROM
mne_timeliness
WHERE
mne_timeliness.basic_id = '$parent_id' AND
mne_timeliness.item_group = 'to3'
and due_1w = 1";


                $query_3_1 = $conn->query($sql_3_11);

                $row_3_1 = $query_3_1->fetch_assoc();
                ?>
           
                <?php  $colA = $row_3_1['total_A']; ?>
                <?php
                $sql_3_2 = "SELECT
                        count(mne_timeliness.pk_id) total_B
                        FROM
                        mne_timeliness
                        WHERE
                        mne_timeliness.basic_id = '$parent_id' AND
                        mne_timeliness.item_group = 'to3'
                        and due_1to2w = 1";


                $query_3_2 = $conn->query($sql_3_2);

                $row_3_2 = $query_3_2->fetch_assoc();
                ?>


               <?php  $colB = $row_3_2['total_B']; ?>

                <?php
                $sql_3_3 = "SELECT
                        count(mne_timeliness.pk_id) total_C
                        FROM
                        mne_timeliness
                        WHERE
                        mne_timeliness.basic_id = '$parent_id' AND
                        mne_timeliness.item_group = 'to3'
                        and due_2wto1m = 1";


                $query_3_3 = $conn->query($sql_3_3);

                $row_3_3 = $query_3_3->fetch_assoc();
                ?>
                <?php  $colC = $row_3_3['total_C']; ?>
                <?php
                $sql_3_4 = "SELECT
                        count(mne_timeliness.pk_id) total_D
                        FROM
                        mne_timeliness
                        WHERE
                        mne_timeliness.basic_id = '$parent_id' AND
                        mne_timeliness.item_group = 'to3'
                        and due_2wto1m = 1";


                $query_3_4 = $conn->query($sql_3_4);

                $row_3_4 = $query_3_4->fetch_assoc();
                ?>
               <?php  $colD = $row_3_4['total_D']; ?>
                <?php
                    if ($colA == '3') {
                        $tt_3 = '3';
                    } else if ($colB == '3') {
                        $tt_3 = '2';
                    } else if ($colC == '3') {
                       $tt_3 = '1';
                    } else if ($colD == '3') {
                       $tt_3 = '0';
                    } else {
                        $tt_3 = '0';
                    }
                    ?>
            
                <?php
                $sql_4_1 = "SELECT
                count(mne_timeliness.pk_id) total_A
                FROM
                mne_timeliness
                WHERE
                mne_timeliness.basic_id = '$parent_id' AND
                mne_timeliness.item_group = 'to4'
                and due_1w = 1";


                $query_4_1 = $conn->query($sql_4_1);

                $row_4_1 = $query_4_1->fetch_assoc();
                ?>
                <?php  $col4_A = $row_4_1['total_A']; ?>
                <?php
                $sql_4_2 = "SELECT
                count(mne_timeliness.pk_id) total_B
                FROM
                mne_timeliness
                WHERE
                mne_timeliness.basic_id = '$parent_id' AND
                mne_timeliness.item_group = 'to4'
                and due_1to2w = 1";


                $query_4_2 = $conn->query($sql_4_2);

                $row_4_2 = $query_4_2->fetch_assoc();
                ?>
               <?php  $col4_B = $row_4_2['total_B']; ?>
                <?php
                $sql_4_3 = "SELECT
                count(mne_timeliness.pk_id) total_C
                FROM
                mne_timeliness
                WHERE
                mne_timeliness.basic_id = '$parent_id' AND
                mne_timeliness.item_group = 'to4'
                and due_2wto1m = 1";


                $query_4_3 = $conn->query($sql_4_3);

                $row_4_3 = $query_4_3->fetch_assoc();
                ?>
                <?php  $col4_C = $row_4_3['total_C']; ?>
                <?php
                $sql_4_4 = "SELECT
                count(mne_timeliness.pk_id) total_D
                FROM
                mne_timeliness
                WHERE
                mne_timeliness.basic_id = '$parent_id' AND
                mne_timeliness.item_group = 'to4'
                and due_1to2w = 1";


                $query_4_4 = $conn->query($sql_4_4);

                $row_4_4 = $query_4_4->fetch_assoc();
                ?>

                <?php  $col4_D = $row_4_4['total_D']; ?>
              <?php
                    if ($col4_A == '3') {
                        $tt_4 = '3';
                    } else if ($col4_B == '3') {
                        $tt_4 =  '2';
                    } else if ($col4_C == '3') {
                        $tt_4 =  '1';
                    } else if ($col4_D == '3') {
                        $tt_4 =  '0';
                    } else {
                        $tt_4 =  '0';
                    }
                    ?></td>
           