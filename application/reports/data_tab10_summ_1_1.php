<?php
include("../includes/classes/AllClasses.php");
$parent_id = $_POST['parent_id'];
?> 

                <?php
                $sql_3_11 = "SELECT
count(mne_timeliness.pk_id) total_A
FROM
mne_timeliness
INNER JOIN mne_basic_parent ON mne_timeliness.basic_id = mne_basic_parent.pk_id
WHERE
	mne_basic_parent.prov_id = '$province'
AND date_Format(
	mne_basic_parent.date_visit,
	'%Y-%m-%d'
) BETWEEN '$start_date'
AND '$end_date'
AND
mne_timeliness.item_group = 'to3'
and due_1w = 1";
  $query_3_1 = mysql_query($sql_3_11);

                            $row_3_1 = mysql_fetch_array($query_3_1);

                
                ?>
           
                <?php  $colA = $row_3_1['total_A']; ?>
                <?php
                $sql_3_2 = "SELECT
                        count(mne_timeliness.pk_id) total_B
                FROM
                        mne_timeliness
                INNER JOIN mne_basic_parent ON mne_timeliness.basic_id = mne_basic_parent.pk_id
                WHERE
                        mne_basic_parent.prov_id = '$province'
                AND date_Format(
                        mne_basic_parent.date_visit,
                        '%Y-%m-%d'
                ) BETWEEN '$start_date'
                AND '$end_date'
                AND mne_timeliness.item_group = 'to3'
                AND due_1to2w = 1";
$query_3_2 = mysql_query($sql_3_2);

                            $row_3_2 = mysql_fetch_array($query_3_2);

               
                ?>


               <?php  $colB = $row_3_2['total_B']; ?>

                <?php
                $sql_3_3 = "SELECT
	count(mne_timeliness.pk_id) total_C
FROM
	mne_timeliness
INNER JOIN mne_basic_parent ON mne_timeliness.basic_id = mne_basic_parent.pk_id
WHERE
	mne_basic_parent.prov_id = '$province'
AND date_Format(
	mne_basic_parent.date_visit,
	'%Y-%m-%d'
) BETWEEN '$start_date'
AND '$end_date'
AND mne_timeliness.item_group = 'to3'
AND due_2wto1m = 1";

$query_3_3 = mysql_query($sql_3_3);
$row_3_3 = mysql_fetch_array($query_3_3);
             
                ?>
                <?php  $colC = $row_3_3['total_C']; ?>
                <?php
                $sql_3_4 = "SELECT
	count(mne_timeliness.pk_id) total_D
FROM
	mne_timeliness
INNER JOIN mne_basic_parent ON mne_timeliness.basic_id = mne_basic_parent.pk_id
WHERE
	mne_basic_parent.prov_id = '$province'
AND date_Format(
	mne_basic_parent.date_visit,
	'%Y-%m-%d'
) BETWEEN '$start_date'
AND '$end_date'
AND mne_timeliness.item_group = 'to3'
AND due_2wto1m = 1";

$query_3_4 = mysql_query($sql_3_4);
$row_3_4 = mysql_fetch_array($query_3_4);
                
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
            INNER JOIN mne_basic_parent ON mne_timeliness.basic_id = mne_basic_parent.pk_id
WHERE
	mne_basic_parent.prov_id = '$province'
AND date_Format(
	mne_basic_parent.date_visit,
	'%Y-%m-%d'
) BETWEEN '$start_date'
AND '$end_date'
AND mne_timeliness.item_group = 'to4'
AND due_1w = 1";


$query_4_1 = mysql_query($sql_4_1);
$row_4_1 = mysql_fetch_array($query_4_1);
              
                ?>
                <?php  $col4_A = $row_4_1['total_A']; ?>
                <?php
               $sql_4_2 = "SELECT
	count(mne_timeliness.pk_id) total_B
FROM
	mne_timeliness
INNER JOIN mne_basic_parent ON mne_timeliness.basic_id = mne_basic_parent.pk_id
WHERE
	mne_basic_parent.prov_id = '$province'
AND date_Format(
	mne_basic_parent.date_visit,
	'%Y-%m-%d'
) BETWEEN '$start_date'
AND '$end_date'
AND mne_timeliness.item_group = 'to4'
AND due_1to2w = 1";

$query_4_2 = mysql_query($sql_4_2);
$row_4_2 = mysql_fetch_array($query_4_2);
               
                ?>
               <?php  $col4_B = $row_4_2['total_B']; ?>
                <?php
                $sql_4_3 = "SELECT
	count(mne_timeliness.pk_id) total_C
FROM
	mne_timeliness
INNER JOIN mne_basic_parent ON mne_timeliness.basic_id = mne_basic_parent.pk_id
WHERE
	mne_basic_parent.prov_id = '$province'
AND date_Format(
	mne_basic_parent.date_visit,
	'%Y-%m-%d'
) BETWEEN '$start_date'
AND '$end_date'
AND mne_timeliness.item_group = 'to4'
AND due_2wto1m = 1";
$query_4_3 = mysql_query($sql_4_3);
$row_4_3 = mysql_fetch_array($query_4_3);
            

                ?>
                <?php  $col4_C = $row_4_3['total_C']; ?>
                <?php
                $sql_4_4 = "SELECT
	count(mne_timeliness.pk_id) total_D
FROM
	mne_timeliness
INNER JOIN mne_basic_parent ON mne_timeliness.basic_id = mne_basic_parent.pk_id
WHERE
	mne_basic_parent.prov_id = '$province'
AND date_Format(
	mne_basic_parent.date_visit,
	'%Y-%m-%d'
) BETWEEN '$start_date'
AND '$end_date'
AND mne_timeliness.item_group = 'to4'
AND due_1to2w = 1";
$query_4_4 = mysql_query($sql_4_4);
$row_4_4 = mysql_fetch_array($query_4_4);
         

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
           