<?php
error_reporting(0);
include("db.php");
$parent_id = $_POST['parent_id'];
?> 

<div><br>
    <h4>AVAILABILITY RATING SUMMARY</h4><br>
    <form method="POST" id="form_7_insert">
        <table border="1px" width="100%" class="table table-bordered">
            <tr class="form-group">
                <th></th>
                <th>A. Number of tracer products offered </th>
                <th>B. Number of required data elements </th>
                <th>C. Total number of required data elements (A * B) </th>
                <th>D. Total number of data elements available </th>
                <th>E. Percentage of data elements available (D/C*100) </th>
                <th>F. Rating of Data Availability </th>
            </tr>
            <tr class="form-group"> 
                <th><p >FP and RH
                        <input type="hidden" name="parent_id"  value="<?php echo $parent_id; ?>">
                        <input type="hidden" id="form7_1" name="form7_1" value="to3"> </p>
                </th>

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
                <td style="text-align:center"><?php echo $offered; ?></td>
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



                <td style="text-align:center"><?php echo $total_B; ?></td>
                <td style="text-align:center"><?php echo Round($offered * $total_B) ?></td>
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
                ?>  
                <td style="text-align:center"><?php echo $total_C; ?></td>
                <td style="text-align:center"><?php echo $columnE = Round(($total_C / $column_D) * 100, 2) ?></td>
                <td style="text-align:center"><?php
                if ($columnE >= '75') {
                    echo $to3 = '3';
                } else if ($columnE < '75' && $columnE >= '50') {
                    echo $to3 = '2';
                } else if ($columnE < '49' && $columnE >= '25') {
                    echo $to3 = '1';
                } else if ($columnE < '25') {
                    echo $to3 = '0';
                }
               
                ?></td>
            </tr>
            <tr class="form-group">
                <th><p  >TO4. MNCH
                        <input type="hidden" id="form7_2" name="form7_2" value="to4">  </p> </th>
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
                <td style="text-align:center"><?php echo $offered_to4; ?></td>

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

                <td style="text-align:center"><?php echo $total_B_to4; ?></td>
                <td style="text-align:center"><?php echo $column_D_to4; ?></td>
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

                <td style="text-align:center"><?php echo $total_C_to4; ?></td>
                <td style="text-align:center"><?php echo $columnE_to4 = Round(($total_C_to4 / $column_D_to4) * 100, 2) ?></td>
                <td style="text-align:center"><?php
                if ($columnE_to4 >= '75') {
                    echo $to4 = '3';
                } else if ($columnE_to4 < '75' && $columnE_to4 >= '50') {
                    echo $to4 = '2';
                } else if ($columnE_to4 < '49' && $columnE_to4 >= '25') {
                    echo $to4 = '1';
                } else if ($columnE_to4 < '25') {
                    echo $to4 = '0';
                }
                ?></td>
            </tr>
            <tr class="form-group">
                <th><p >Total
                        <input type="hidden" id="form7_3" name="form7_3" value="tot"></p></th>
                <td style="text-align:center"><?php echo $offered_to4 + $offered; ?></td>
                <td style="text-align:center"><?php echo $total_B_to4 + $total_B ?></td>
                <td style="text-align:center"><?php echo $column_D + $column_D_to4; ?></td>
                <td style="text-align:center"><?php echo $total_C + $total_C_to4; ?></td>
                <td style="text-align:center"><?php echo $over_total_e = $columnE + $columnE_to4; ?></td>
                <td style="text-align:center"><?php
                    if ($over_total_e >= '75') {
                        echo '3';
                    } else if ($over_total_e < '75' && $over_total_e >= '50') {
                        echo '2';
                    } else if ($over_total_e < '49' && $over_total_e >= '25') {
                        echo '1';
                    } else if ($over_total_e < '25') {
                        echo '0';
                    }
                ?></td>
            </tr>
        </table>

    </form>
    <div id="form_7_insert_resp"></div>

    <br>
    <br><hr>

    <p><b>Column A:</b> Take the sum of the data entered in column A of each health area-specific table above</p>

    <p><b>Column B:</b>  Enter the number of data elements (B-K on pages 3,5) assessed per tracer product</p>

    <p><b>Column C:</b>  Multiply column A by column B</p>

    <p><b>Column D: </b> Sum the total number of "Yes" responses (1s) entered across all tracer products and data elements for each health area</p>

    <p><b>Column E: </b> Divide Column D by Column C and multiply by 100</p>

    <p><b>Column F:</b>  Enter the appropriate rating value based on the result in Column E</p>
    <br>
    <p><b>Availability Rating Guide:</b> </p>

    <p><b>3 </b>= <b>75% or greater</b> of data elements available</p>

    <p><b>2</b> = <b>50-74%</b> of data elements available</p>

    <p><b>1</b> =<b> 25-49%</b> of data elements available</p>

    <p><b>0</b> = <b>Less than 25%</b> of data elements available</p>
    <br><br><br><br>

</div>