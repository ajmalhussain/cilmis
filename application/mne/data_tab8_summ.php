<?php
include("db.php");
$parent_id = $_POST['parent_id'];
?> 


            <?php
            $sql_1_11 = "SELECT
                                                    COUNT(bal_lmis) as total
                                                    FROM
                                                    mne_accuracy
                                                    WHERE
                                                    mne_accuracy.basic_id = '$parent_id'
                                                    and item_group = 'to3' and bal_lmis <>  '0'";


            $query_1_11 = $conn->query($sql_1_11);

            $row_1_11 = $query_1_11->fetch_assoc();
            ?>
           <?php  $colA = $row_1_11['total']; ?>

                <?php
                $sql_1_12 = "SELECT
                Count(mne_accuracy.stock_accurate) AS total_B

                FROM
                mne_accuracy
                WHERE
                mne_accuracy.basic_id = '$parent_id'
                and item_group = 'to3'
                and mne_accuracy.stock_accurate <>  '1'";


                $query_1_12 = $conn->query($sql_1_12);

                $row_1_12 = $query_1_12->fetch_assoc();
               
               $colB = $row_1_12['total_B']; ?>
                <?php  $colC = ROUND(($colB / $colA) * 100, 2) ?>
               <?php
                    if ($colC >= '75') {
                        $tA_3 = '3';
                    } else if ($colC < '75' && $colC >= '50') {
                       $tA_3 =  '2';
                    } else if ($colC < '49' && $colC >= '25') {
                       $tA_3 = '1';
                    } else if ($colC < '25') {
                       $tA_3 = '0';
                    }
                    ?>

                <?php
                $sql_1_13 = "SELECT
                                                    COUNT(bal_lmis) as total
                                                    FROM
                                                    mne_accuracy
                                                    WHERE
                                                    mne_accuracy.basic_id = '$parent_id'
                                                    and item_group = 'to4' and bal_lmis <>  '0'";


                $query_1_13 = $conn->query($sql_1_13);

                $row_1_13 = $query_1_13->fetch_assoc();
                ?>
                <?php  $col4_A = $row_1_13['total']; ?>
                 <?php
                $sql_1_14 = "SELECT
                Count(mne_accuracy.stock_accurate) AS total_B

                FROM
                mne_accuracy
                WHERE
                mne_accuracy.basic_id = '$parent_id'
                and item_group = 'to4'
                and mne_accuracy.stock_accurate <>  '1'";


                $query_1_14 = $conn->query($sql_1_14);

                $row_1_14 = $query_1_14->fetch_assoc();
                ?>
                <?php  $col4_B = $row_1_14['total_B']; ?>
                <?php  $col4_C = Round(($col4_B/$col4_A)*100,2); ?>
                <?php
                    if ($col4_C >= '75') {
                        $tA_4  = '3';
                    } else if ($col4_C < '75' && $col4_C >= '50') {
                        $tA_4  =  '2';
                    } else if ($col4_C < '49' && $col4_C >= '25') {
                        $tA_4  = '1';
                    } else if ($col4_C < '25') {
                        $tA_4  = '0';
                    }
                    ?>

           