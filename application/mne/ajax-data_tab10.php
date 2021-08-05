<?php
include("db.php");
$parent_id = $_POST['parent_id'];
?> 

<div><br>
    <h4>TIMELINESS RATING SUMMARY</h4><br>
    <form method="POST" id="form_10_insert">
        <table border="1" width="100%" class="table table-bordered">

            <tr>
                <th></th>
                <th>A. Number of reports submitted by due date or up to 1 week after</th>
                <th>B. Number of reports submitted between 1-2 weeks after due date</th>
                <th>C. Number of reports submitted between 2-4 weeks after due date</th>
                <th>D. Number of reports submitted more than 4 weeks after due date or not at all</th>
                <th>E. Rating of Data Timeliness</th>
            </tr>
            <tr class="form-group">
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
                <th><p>TO3. FP and RH</p>
                    <input type="hidden" id="form10_1" name="form10_1" value="to3" >
                    <input type="hidden" name="parent_id" value="<?php echo $parent_id; ?>">
                </th>
                <td style="text-align: center;"><?php echo $colA = $row_3_1['total_A']; ?></td>
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


                <td style="text-align: center;"><?php echo $colB = $row_3_2['total_B']; ?></td>

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
                <td style="text-align: center;"><?php echo $colC = $row_3_3['total_C']; ?></td>
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
                <td style="text-align: center;"><?php echo $colD = $row_3_4['total_D']; ?></td>
                <td style="text-align: center;"><?php
                    if ($colA == '3') {
                        echo '3';
                    } else if ($colB == '3') {
                        echo '2';
                    } else if ($colC == '3') {
                        echo '1';
                    } else if ($colD == '3') {
                        echo '0';
                    } else {
                        echo '0';
                    }
                    ?></td>
            </tr>
            <tr class="form-group">
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
                <th><p >TO4. MNCH</p>
                    <input type="hidden" id="form10_2" name="form10_2" value="to4" >
                </th>
                <td style="text-align: center;"><?php echo $col4_A = $row_4_1['total_A']; ?></td>
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
                <td  style="text-align: center;"><?php echo $col4_B = $row_4_2['total_B']; ?></td>
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
                <td  style="text-align: center;"><?php echo $col4_C = $row_4_3['total_C']; ?></td>
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

                <td  style="text-align: center;"><?php echo $col4_D = $row_4_4['total_D']; ?></td>
                <td  style="text-align: center;"><?php
                    if ($col4_A == '3') {
                        echo '3';
                    } else if ($col4_B == '3') {
                        echo '2';
                    } else if ($col4_C == '3') {
                        echo '1';
                    } else if ($col4_D == '3') {
                        echo '0';
                    } else {
                        echo '0';
                    }
                    ?></td>
            </tr>
            <tr class="form-group">

                <th><p >Total</p>
                    <input type="hidden" id="form10_3" name="form10_3" value="total" >
                </th>
                <td  style="text-align: center;"><?php echo $totalA = $col4_A + $colA; ?></td>
                <td  style="text-align: center;"><?php echo $totalB = $col4_B + $colB; ?></td>
                <td  style="text-align: center;"><?php echo $totalC = $col4_C + $colC; ?></td>
                <td  style="text-align: center;"><?php echo $totalD = $col4_D + $colD; ?></td>
                <td  style="text-align: center;"><?php
                    if ($totalA == '3') {
                        echo '3';
                    } else if ($totalB == '3') {
                        echo '2';
                    } else if ($totalC == '3') {
                        echo '1';
                    } else if ($totalD == '3') {
                        echo '0';
                    } else {
                        echo '0';
                    }
                    ?></td>
            </tr>
        </table>

    </form>

    <br>
    <div id="form_10_insert_resp"></div>
    <br><hr>

    <p><b>Columns A-D:</b> Enter the total number of reports submitted in the given timeframe.</p>

    <p><b>Column E:</b> Enter the appropriate rating value based on the result in Columns A-D.</p>
    <br>
    <p><b>Timeliness Rating Guide:</b></p>

    <p><b>3</b> = All required reports submitted on time or up to one week after due date (All entries are in Column A)</p>

    <p><b>2</b> = All required reports submitted between one and 2 weeks after the due date (All entries are in Column A or B)</p>

    <p><b>1</b> = All required reports submitted between 2 and 4 weeks after the due date (All entries are in Columns A through C)</p>

    <p><b>0</b> = Any report that was submitted more than 4 weeks after the due date or not at all (Any entry in Column D)</p>
    <br><br><br><br>
</div>