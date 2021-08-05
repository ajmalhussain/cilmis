<?php
error_reporting(0);
include("db.php");
$parent_id = $_POST['parent_id'];

?> 

<br><div>
    <h4>ACCURACY RATING SUMMARY</h4><br>
    <form method="POST" id="form_8_insert">
        <table border="1px" width="100%" class="table table-bordered">
            <tr>
                <th> </th>
                <th>A. Number of tracer products assessed  </th>
                <th>B. Number of tracer products with accurate data (reported stock status could be reproduced with source documents)  </th>
                <th>C. Percentage of accurate stock status data (B/A*100)  </th>
                <th>D. Rating of Data Accuracy </th>
            </tr>
            <?php
            $sql_1_11 = "SELECT
                                                    COUNT(bal_lmis) as total
                                                    FROM
                                                    mne_accuracy
                                                    WHERE
                                                    mne_accuracy.basic_id = '$parent_id'
                                                    and item_group = 'to3' and bal_lmis IS NOT NULL";


            $query_1_11 = $conn->query($sql_1_11);

            $row_1_11 = $query_1_11->fetch_assoc();
            ?>
            <tr class="form-group">
                <th> FP and RH
                    <input type="hidden" name="parent_id"  value="<?php echo $parent_id; ?>">
                    <input type="hidden" id="form8_1" name="form8_1" value="to3">
                </th>
                <td style="text-align:center;"><?php echo $colA = $row_1_11['total']; ?></td>

                <?php
                $sql_1_12 = "SELECT
                Count(mne_accuracy.stock_accurate) AS total_B

                FROM
                mne_accuracy
                WHERE
                mne_accuracy.basic_id = '$parent_id'
                and item_group = 'to3'
                and mne_accuracy.stock_accurate =  '1'";


                $query_1_12 = $conn->query($sql_1_12);

                $row_1_12 = $query_1_12->fetch_assoc();
                ?>
                <td style="text-align:center;"><?php echo $colB = $row_1_12['total_B']; ?></td>
                <td style="text-align:center;"><?php echo $colC = ROUND(($colB / $colA) * 100, 2) ?></td>
                <td style="text-align:center;"><?php
                    if ($colC >= '75') {
                        echo '3';
                    } else if ($colC < '75' && $colC >= '50') {
                        echo '2';
                    } else if ($colC < '49' && $colC >= '25') {
                        echo '1';
                    } else if ($colC < '25') {
                        echo '0';
                    }
                    ?></td>

            </tr>
            <tr class="form-group">
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
                <th>TO4. MNCH
                    <input type="hidden" id="form8_2" name="form8_2"value="to4">
                </th>
                <td style="text-align: center;"><?php echo $col4_A = $row_1_13['total']; ?></td>
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
                <td style="text-align: center;"><?php echo $col4_B = $row_1_14['total_B']; ?></td>
                <td style="text-align: center;"><?php echo $col4_C = Round(($col4_B/$col4_A)*100,2); ?></td>
                <td style="text-align: center;"><?php
                    if ($col4_C >= '75') {
                        echo '3';
                    } else if ($col4_C < '75' && $col4_C >= '50') {
                        echo '2';
                    } else if ($col4_C < '49' && $col4_C >= '25') {
                        echo '1';
                    } else if ($col4_C < '25') {
                        echo '0';
                    }
                    ?></td>

            </tr>
            <tr class="form-group">
                
                <th>Total
                    <input type="hidden" id="form8_3" name="form8_3"  value="tot">
                </th>
                <td style="text-align: center"><?php echo $totalA = $col4_A + $colA; ?></td>
                <td style="text-align: center"><?php echo $totalB = $col4_B + $colB; ?></td>
                <td style="text-align: center"><?php echo $totalC = ROUND($totalB/$totalA*100,2) ?></td>
                <td style="text-align: center"><?php
                    if ($totalC >= '75') {
                        echo '3';
                    } else if ($totalC < '75' && $totalC >= '50') {
                        echo '2';
                    } else if ($totalC < '49' && $totalC >= '25') {
                        echo '1';
                    } else if ($totalC < '25') {
                        echo '0';
                    }
                    ?></td>

            </tr>
        </table>
        
    </form>
    <div id="form_8_insert_resp"></div>
    <hr>
    <p><b>Column A:</b> Enter the total number of tracer products assessed per health area, and the overall total of all tracer products</p>

    <p><b>Column B:</b> Enter the total number of tracer products with "Yes" responses (1s) within each health area and the overall total</p>

    <p><b>Column C:</b> Divide Column B by Column A and multiply by 100</p>

    <p><b>Column D:</b> Enter the appropriate rating value based on the result in Column C</p>
    <br>
    <p><b>Accuracy Rating Guide:</b></p>

    <p><b>3</b> = <b>75% or greater</b> of tracer products have accurate data</p>

    <p><b>2</b> = <b>50-74% </b>of tracer products have accurate data</p>

    <p><b>1 </b>= <b>25-49%</b> of tracer products have accurate data</p>

    <p><b>0</b> = <b>Less than 25%</b> of tracer products have accurate data </p>
    <br><br><br><br>
</div>
