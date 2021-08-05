<?php
error_reporting(0);
include("db.php");
$parent_id = $_POST['parent_id'];
?> 



<?php
$sql_avl = "SELECT
SUM(mne_availability.offered) as offered
FROM
mne_availability
WHERE
mne_availability.basic_id = '$parent_id' AND
mne_availability.item_group = 't03'";

$query_avl = $conn->query($sql_avl);

$row_avl = $query_avl->fetch_assoc();
$offered = $row_avl['offered'];
?>

<?php
$qry_b = "SELECT
	SUM(A.total) AS total
FROM
	(
		SELECT
			IF(count(stock_tools) > 0,1,0) total
		FROM
			mne_availability
		WHERE
			mne_availability.basic_id = '$parent_id'
		AND mne_availability.item_group = 't03'
		AND stock_tools IS NOT NULL
		AND stock_tools != ''
		UNION ALL
			SELECT
				IF(count(tools_available) > 0,1,0) total
			FROM
				mne_availability
			WHERE
				mne_availability.basic_id = '$parent_id'
			AND mne_availability.item_group = 't03'
			AND tools_available = '1'
			UNION ALL
				SELECT
					IF(count(s_o_h) > 0,1,0) total
				FROM
					mne_availability
				WHERE
					mne_availability.basic_id = '$parent_id'
				AND mne_availability.item_group = 't03'
				AND (s_o_h = 'YES' OR s_o_h = 'no')
				UNION ALL
					SELECT
						IF(count(open_balance) > 0,1,0) total
					FROM
						mne_availability
					WHERE
						mne_availability.basic_id = '$parent_id'
					AND mne_availability.item_group = 't03'
					AND (open_balance = 'YES' OR open_balance = 'no')
					UNION ALL
						SELECT
							IF(count(close_balance) > 0,1,0) total
						FROM
							mne_availability
						WHERE
							mne_availability.basic_id = '$parent_id'
						AND mne_availability.item_group = 't03'
						AND (close_balance = 'YES' OR close_balance = 'no')
						UNION ALL
							SELECT
								IF(count(receive) > 0,1,0) total
							FROM
								mne_availability
							WHERE
								mne_availability.basic_id = '$parent_id'
							AND mne_availability.item_group = 't03'
							AND (receive = 'YES' OR receive = 'no')
							UNION ALL
								SELECT
									IF(count(issue) > 0,1,0) total
								FROM
									mne_availability
								WHERE
									mne_availability.basic_id = '$parent_id'
								AND mne_availability.item_group = 't03'
								AND (issue = 'YES' or issue = 'no')
								UNION ALL
									SELECT
										IF(count(a_m_c) > 0,1,0) total
									FROM
										mne_availability
									WHERE
										mne_availability.basic_id = '$parent_id'
									AND mne_availability.item_group = 't03'
									AND (a_m_c = 'YES' or a_m_c = 'no')
									UNION ALL
										SELECT
											IF(count(min) > 0,1,0) total
										FROM
											mne_availability
										WHERE
											mne_availability.basic_id = '$parent_id'
										AND mne_availability.item_group = 't03'
										AND (min = 'YES' or min = 'no')
										UNION ALL
											SELECT
												IF(count(max) > 0,1,0) total
											FROM
												mne_availability
											WHERE
												mne_availability.basic_id = '$parent_id'
											AND mne_availability.item_group = 't03'
											AND (max = 'YES' or max = 'no')
	) A";

$query_B = $conn->query($qry_b);

$row_B = $query_B->fetch_assoc();
$total_B = $row_B['total'];
?>                                     




<?php
$column_D = Round($offered * $total_B);
$qry_c = "SELECT
	SUM(A.total) AS total
FROM
	(
		SELECT
			count(stock_tools) total
		FROM
			mne_availability
		WHERE
			mne_availability.basic_id = '$parent_id'
		AND mne_availability.item_group = 't03'
		AND stock_tools IS NOT NULL
		AND stock_tools != ''
		UNION ALL
			SELECT
				count(tools_available)  total
			FROM
				mne_availability
			WHERE
				mne_availability.basic_id = '$parent_id'
			AND mne_availability.item_group = 't03'
			AND tools_available = '1'
UNION ALL
				SELECT
					count(s_o_h) total
				FROM
					mne_availability
				WHERE
					mne_availability.basic_id = '$parent_id'
				AND mne_availability.item_group = 't03'
				AND s_o_h = 'YES'
				UNION ALL
					SELECT
						count(open_balance) total
					FROM
						mne_availability
					WHERE
						mne_availability.basic_id = '$parent_id'
					AND mne_availability.item_group = 't03'
					AND open_balance = 'YES'
					UNION ALL
						SELECT
							count(close_balance) total
						FROM
							mne_availability
						WHERE
							mne_availability.basic_id = '$parent_id'
						AND mne_availability.item_group = 't03'
						AND close_balance = 'YES'
						UNION ALL
							SELECT
								count(receive) total
							FROM
								mne_availability
							WHERE
								mne_availability.basic_id = '$parent_id'
							AND mne_availability.item_group = 't03'
							AND receive = 'YES'
							UNION ALL
								SELECT
									count(issue) total
								FROM
									mne_availability
								WHERE
									mne_availability.basic_id = '$parent_id'
								AND mne_availability.item_group = 't03'
								AND issue = 'YES'
								UNION ALL
									SELECT
										count(a_m_c) total
									FROM
										mne_availability
									WHERE
										mne_availability.basic_id = '$parent_id'
									AND mne_availability.item_group = 't03'
									AND a_m_c = 'YES'
									UNION ALL
										SELECT
											count(min) total
										FROM
											mne_availability
										WHERE
											mne_availability.basic_id = '$parent_id'
										AND mne_availability.item_group = 't03'
										AND min = 'YES'
										UNION ALL
											SELECT
												count(max) total
											FROM
												mne_availability
											WHERE
												mne_availability.basic_id = '$parent_id'
											AND mne_availability.item_group = 't03'
											AND max = 'YES'
	) A";

$query_C = $conn->query($qry_c);

$row_C = $query_C->fetch_assoc();
$total_C = $row_C['total'];

$columnE = Round(($total_C / $column_D) * 100, 2)
?>
<?php
if ($columnE >= '75') {
     $to3 = '3';
} else if ($columnE < '75' && $columnE >= '50') {
     $to3 = '2';
} else if ($columnE < '49' && $columnE >= '25') {
     $to3 = '1';
} else if ($columnE < '25') {
     $to3 = '0';
}
?>
</tr>

<?php
$sql_avl_to4 = "SELECT
            SUM(mne_availability.offered) as offered
            FROM
            mne_availability
            WHERE
            mne_availability.basic_id = '$parent_id' AND
            mne_availability.item_group = 't04'";

$query_avl_to4 = $conn->query($sql_avl_to4);

$row_avl_to4 = $query_avl_to4->fetch_assoc();
if (empty($row_avl_to4['offered'])) {
    $offered_to4 = 0;
} else {
    $offered_to4 = $row_avl_to4['offered'];
};
?>


<?php
$qry_b_to4 = "SELECT
	SUM(A.total) AS total
FROM
	(
		SELECT
			count(stock_tools) total
		FROM
			mne_availability
		WHERE
			mne_availability.basic_id = '$parent_id'
		AND mne_availability.item_group = 't04'
		AND stock_tools IS NOT NULL
		AND stock_tools != ''
		UNION ALL
			SELECT
				count(tools_available) total
			FROM
				mne_availability
			WHERE
				mne_availability.basic_id = '$parent_id'
			AND mne_availability.item_group = 't04'
			AND tools_available = '1'
			UNION ALL
				SELECT
					count(s_o_h) total
				FROM
					mne_availability
				WHERE
					mne_availability.basic_id = '$parent_id'
				AND mne_availability.item_group = 't04'
				AND s_o_h = 'YES'
				UNION ALL
					SELECT
						count(open_balance) total
					FROM
						mne_availability
					WHERE
						mne_availability.basic_id = '$parent_id'
					AND mne_availability.item_group = 't04'
					AND open_balance = 'YES'
					UNION ALL
						SELECT
							count(close_balance) total
						FROM
							mne_availability
						WHERE
							mne_availability.basic_id = '$parent_id'
						AND mne_availability.item_group = 't04'
						AND close_balance = 'YES'
						UNION ALL
							SELECT
								count(receive) total
							FROM
								mne_availability
							WHERE
								mne_availability.basic_id = '$parent_id'
							AND mne_availability.item_group = 't04'
							AND receive = 'YES'
							UNION ALL
								SELECT
									count(issue) total
								FROM
									mne_availability
								WHERE
									mne_availability.basic_id = '$parent_id'
								AND mne_availability.item_group = 't04'
								AND issue = 'YES'
								UNION ALL
									SELECT
										count(a_m_c) total
									FROM
										mne_availability
									WHERE
										mne_availability.basic_id = '$parent_id'
									AND mne_availability.item_group = 't04'
									AND a_m_c = 'YES'
									UNION ALL
										SELECT
											count(min) total
										FROM
											mne_availability
										WHERE
											mne_availability.basic_id = '$parent_id'
										AND mne_availability.item_group = 't04'
										AND min = 'YES'
										UNION ALL
											SELECT
												count(max) total
											FROM
												mne_availability
											WHERE
												mne_availability.basic_id = '$parent_id'
											AND mne_availability.item_group = 't04'
											AND max = 'YES'
	) A";

$query_B_to4 = $conn->query($qry_b_to4);

$row_B_to4 = $query_B_to4->fetch_assoc();
$total_B_to4 = $row_B_to4['total'];
$column_D_to4 = Round($offered_to4 * $total_B_to4);
?>   


<?php
$qry_c_to4 = "SELECT
	SUM(A.total) AS total
FROM
	(
				SELECT
					count(s_o_h) total
				FROM
					mne_availability
				WHERE
					mne_availability.basic_id = '$parent_id'
				AND mne_availability.item_group = 't04'
				AND s_o_h = 'YES'
				UNION ALL
					SELECT
						count(open_balance) total
					FROM
						mne_availability
					WHERE
						mne_availability.basic_id = '$parent_id'
					AND mne_availability.item_group = 't04'
					AND open_balance = 'YES'
					UNION ALL
						SELECT
							count(close_balance) total
						FROM
							mne_availability
						WHERE
							mne_availability.basic_id = '$parent_id'
						AND mne_availability.item_group = 't04'
						AND close_balance = 'YES'
						UNION ALL
							SELECT
								count(receive) total
							FROM
								mne_availability
							WHERE
								mne_availability.basic_id = '$parent_id'
							AND mne_availability.item_group = 't04'
							AND receive = 'YES'
							UNION ALL
								SELECT
									count(issue) total
								FROM
									mne_availability
								WHERE
									mne_availability.basic_id = '$parent_id'
								AND mne_availability.item_group = 't04'
								AND issue = 'YES'
								UNION ALL
									SELECT
										count(a_m_c) total
									FROM
										mne_availability
									WHERE
										mne_availability.basic_id = '$parent_id'
									AND mne_availability.item_group = 't04'
									AND a_m_c = 'YES'
									UNION ALL
										SELECT
											count(min) total
										FROM
											mne_availability
										WHERE
											mne_availability.basic_id = '$parent_id'
										AND mne_availability.item_group = 't04'
										AND min = 'YES'
										UNION ALL
											SELECT
												count(max) total
											FROM
												mne_availability
											WHERE
												mne_availability.basic_id = '$parent_id'
											AND mne_availability.item_group = 't04'
											AND max = 'YES'
	) A";

$query_C_to4 = $conn->query($qry_c_to4);

$row_C_to4 = $query_C_to4->fetch_assoc();
$total_C_to4 = $row_C_to4['total'];
?> 

<?php $columnE_to4 = Round(($total_C_to4 / $column_D_to4) * 100, 2) ?>
<?php
if ($columnE_to4 >= '75') {
     $to4 = '3';
} else if ($columnE_to4 < '75' && $columnE_to4 >= '50') {
     $to4 = '2';
} else if ($columnE_to4 < '49' && $columnE_to4 >= '25') {
     $to4 = '1';
} else if ($columnE_to4 < '25') {
    $to4 = '0';
}
$over_total_e = $columnE + $columnE_to4;
?>


         
