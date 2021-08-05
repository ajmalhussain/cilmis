<?php
include("../includes/classes/AllClasses.php");
$parent_id = $_POST['parent_id'];
?> 


            <?php
            $sql_1_11 = "SELECT
	COUNT(bal_lmis) AS total
FROM
	mne_accuracy
INNER JOIN mne_basic_parent ON mne_accuracy.basic_id = mne_basic_parent.pk_id
WHERE
	mne_basic_parent.prov_id = '$province'
AND date_Format(
	mne_basic_parent.date_visit,
	'%Y-%m-%d'
) BETWEEN '$start_date'
AND '$end_date'
AND mne_accuracy.item_group = 'to3'
AND bal_lmis <> '0'";

$query_1_11 = mysql_query($sql_1_11);
$row_1_11 = mysql_fetch_array($query_1_11);

        
            ?>
           <?php  $colA = $row_1_11['total']; ?>

                <?php
                $sql_1_12 = "SELECT
	Count(
		mne_accuracy.stock_accurate
	) AS total_B
FROM
	mne_accuracy
INNER JOIN mne_basic_parent ON mne_accuracy.basic_id = mne_basic_parent.pk_id
WHERE
	mne_basic_parent.prov_id = '$province'
AND date_Format(
	mne_basic_parent.date_visit,
	'%Y-%m-%d'
) BETWEEN '$start_date'
AND '$end_date'
AND mne_accuracy.item_group = 'to3'
AND mne_accuracy.stock_accurate <> '1'";

$query_1_12 = mysql_query($sql_1_12);
$row_1_12 = mysql_fetch_array($query_1_12);
 
        
            
               
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
                COUNT(bal_lmis) AS total
        FROM
                mne_accuracy
        INNER JOIN mne_basic_parent ON mne_accuracy.basic_id = mne_basic_parent.pk_id
        WHERE
                mne_basic_parent.prov_id = '$province'
        AND date_Format(
                mne_basic_parent.date_visit,
                '%Y-%m-%d'
        ) BETWEEN '$start_date'
        AND '$end_date'
        AND mne_accuracy.item_group = 'to4'
        AND bal_lmis <> '0'";
$query_1_13 = mysql_query($sql_1_13);
$row_1_13 = mysql_fetch_array($query_1_13);
 
        
            
                ?>
                <?php  $col4_A = $row_1_13['total']; ?>
                 <?php
                $sql_1_14 = "SELECT
	Count(
		mne_accuracy.stock_accurate
	) AS total_B
FROM
	mne_accuracy
INNER JOIN mne_basic_parent ON mne_accuracy.basic_id = mne_basic_parent.pk_id
WHERE
	mne_basic_parent.prov_id = '$province'
AND date_Format(
	mne_basic_parent.date_visit,
	'%Y-%m-%d'
) BETWEEN '$start_date'
AND '$end_date'
AND mne_accuracy.item_group = 'to4'
AND mne_accuracy.stock_accurate <> '1'";

$query_1_14 = mysql_query($sql_1_14);
$row_1_14 = mysql_fetch_array($query_1_14);
 
               
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

           