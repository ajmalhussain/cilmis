<html>
    <head>

        <meta charset="UTF-8">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
        <link rel="stylesheet" href="select2.css">
        <link rel="stylesheet" href="select2-metronic.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
        <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.6.4/jquery.min.js"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/css/bootstrap-datetimepicker.min.css" />
        <script src="http://cdnjs.cloudflare.com/ajax/libs/moment.js/2.5.1/moment.min.js"></script>            
        <script src="http://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/3.0.0/js/bootstrap-datetimepicker.min.js"></script>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
        <link rel="stylesheet" href="/resources/demos/style.css">
    <img src="../../../Users/HP22/Downloads/Screenshot-2018-1-26 http localhost.png" alt=""/>
    <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

</head>

<?php

$end_date = $_REQUEST['end_date'];
$start_date = $_REQUEST['start_date'];
$province = $_REQUEST['province'];

include("db.php");

include 'data_tab7_summ_1.php';
include 'data_tab8_summ_1.php';
include 'data_tab10_summ_1.php';

$parent_id = 1;
?> 
<body>
    <div class="container" >


        <div class="page-content-wrapper">
            <div class="page-content">
               

                <form method="POST">
                    <br><br>
                    <table border="2px" class="table table-bordered">
                        <tr class="form-group">
                            <th colspan="5">
                                Complete item I-Q at the end of the site visit
                            </th>
                        </tr>
                        <tr class="form-group">
                            <th></th>
                            <th>I. Data Availability Rating</th>
                            <th>J. Data Accuracy Rating</th>
                            <th>K. Data Timeliness Rating </th>
                            <th>L. Overall Data Confidence Rating(SUM I+J+K)</th>                  

                        </tr>
                        <tr class="form-group">
                            <td ><p>Family Planning and Resproductive Health</p></td>
                        <input type="hidden" id="parent_id" name="parent_id"  value="1">
                        <input type="hidden" class="form-control" id="form1_to3_D" name="form1_to3_D" value="TO3: Family Planning and Resproductive Health" >

                        <td style="text-align: center"><?php echo $to3; ?></td>
                        <td style="text-align: center"><?php echo $tA_3; ?></td>
                        <td style="text-align: center"><?php echo $tt_3; ?></td>
                        <td style="text-align: center"><?php echo $to3 + $tA_3 + $tt_3; ?></td>
                        </tr >
                        <tr class="form-group">
                            <?php
                            $sql_1_1 = "SELECT
                                        mne_basic_child.pk_id,
                                        mne_basic_child.basic_id,
                                        mne_basic_child.description,
                                        mne_basic_child.available,
                                        mne_basic_child.accuracy,
                                        mne_basic_child.timeliness,
                                        mne_basic_child.total
                                        FROM
                                        mne_basic_child
                                        WHERE
                                        mne_basic_child.basic_id = $parent_id"
                                    . " AND description LIKE '%TO4%'";


                            $query_1_1 = $conn->query($sql_1_1);

                            $row_1_1 = $query_1_1->fetch_assoc();
                            ?>
                            <td>Maternal, Newborn and Child Health</td>
                        <input type="hidden" class="form-control" id="form1_to4_D" name="form1_to4_D" value="TO4: Maternal, Newborn and Child Health" >
                        <td style="text-align: center"><?php echo $to4; ?></td>
                        <td style="text-align: center"><?php echo $tA_4; ?></td>
                        <td style="text-align: center"><?php echo $tt_4; ?></td>
                        <td style="text-align: center"><?php echo $to4 + $tA_4 + $tt_4; ?></td>
                        </tr>
                        <tr class="form-group">
                            <?php
                            $sql_1_2 = "SELECT
                                        mne_basic_child.pk_id,
                                        mne_basic_child.basic_id,
                                        mne_basic_child.description,
                                        mne_basic_child.available,
                                        mne_basic_child.accuracy,
                                        mne_basic_child.timeliness,
                                        mne_basic_child.total
                                        FROM
                                        mne_basic_child
                                        WHERE
                                        mne_basic_child.basic_id = $parent_id"
                                    . " AND description LIKE '%All%'";


                            $query_1_2 = $conn->query($sql_1_2);

                            $row_1_2 = $query_1_2->fetch_assoc();
                            ?>
                            <td>ALL TO: Average Data Confidence Ratings</td>
                        <input type="hidden" class="form-control" id="form1_all_D" name="form1_all_D" value="ALL TO: Average Data Confidence Ratings" >
                        <td style="text-align: center"><?php echo $o_t3 = $to3 + $to4; ?></td>
                        <td style="text-align: center"><?php echo $o_t4 = $tA_3 + $tA_4; ?></td>
                        <td style="text-align: center"><?php echo $o_t5 = $tt_3 + $tt_4; ?></td>
                        <td style="text-align: center"><?php echo $o_t3 + $o_t4 + $o_t5; ?></td>
                        </tr>
                    </table>
                    <div id="form1exeresp"></div>
                </form>

            </div>
        </div>
    </div>
</body>
</html>