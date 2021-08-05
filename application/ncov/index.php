<?php
include("../includes/classes/AllClasses.php");
include(PUBLIC_PATH . "html/header.php");
?>
</head>
<body class="page-header-fixed page-quick-sidebar-over-content">
    <div class="page-container">
        <?php include $_SESSION['menu']; ?>
        <div class="page-content-wrapper">
            <div class="page-content">

                <div class="row">
                    <div class="col-md-12">
                        <div class="top-news">
                            <a class="btn yellow-casablanca btn-rounded">
                                <span>
                                    <?php
                                    echo "<div>Welcome: " . $_SESSION['user_name'] . " </div>";
                                    ?> 
                                </span>                                
                                <i class="fa fa-globe top-news-icon"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="portlet">
                    <div class="portlet-body">

                        <?php if ($_SESSION['user_id'] == 10201) { ?>
                            <div class="well well-sm">
                                <div class="news-blocks">
                                    <h3>
                                        <a>
                                            CDC - Management Information System </a>
                                    </h3>
                                    <p>
                                        Pakistan Logistics Management Information System
                                    </p>
                                </div>
                            </div>
                        <?php } else {
                            ?>

                            <div class="well well-sm">
                                <div class="news-blocks">
                                    <h3>
                                        <a>
                                            COVID-19 - Management Information System </a>
                                    </h3>
                                    <p>
                                        Pakistan Logistics Management Information System
                                    </p>
                                    </h3>
                                    <p style="text-align: justify;">A novel coronavirus (nCoV) is a new coronavirus that has not been previously identified. The 2019 novel coronavirus (2019-nCoV), is not that same as the coronaviruses that commonly circulate among humans and cause mild illness, like the common cold. A diagnosis with coronavirus 229E, NL63, OC43, or HKU1 is not the same as a 2019-nCoV diagnosis. These are different viruses and patients with 2019-nCoV will be evaluated and cared for differently than patients with common coronavirus diagnosis.

                                    </p><p style="text-align: justify;">

                                        Under the leadership of Prime Minister , H.E. Imran Khan, Special Assistant to Prime Miniter on Health, Honorable Dr. Zafar Mirza and Prof. Dr. Maj. General Aamer Ikram took preventive measures which has saved Pakistan from novel Corona virus. The preparedness is essential for Pakistan as China is the immediate neighbor and major CPEC trade partner of Pakistan with more than 20 direct or indirect flights that land at major airports. Additionally, China hosts around 500,000 Pakistani students who travel back and forth and could be the primary source of virus transmission. Firstly, Pakistan started active screening at its entry points and take extra measure to Quarantine the suspects.

                                    </p><p style="text-align: justify;">MoNHSR&C with the help of Chemonics Inc has developed nCov LMIS to digitize the inventories used to combat nCov Virus from spreading in Pakistan.
                                    </p></div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <h3>Preventive Actions</h3>
                                    <p style="text-align: justify;">Under the leadership of Prime Minister, Mr. Imran Khan, Senior Advisor to PM on Health, honorable Dr. Zafar Mirza and Prof. Dr. Maj. General Aamer Ikram took preventive measures which have saved Pakistan from having a presence of nCov virus. The preparedness was and is essential for Pakistan, as it is the immediate neighbor and major CPEC trade partner of China with more than 20 direct or indirect flights coming from China that land at major airports. Additionally, China hosts around 500,000 Pakistani students who travel back and forth and could be the primary source of virus transmission. Firstly, Pakistan started active screening at its entry points and took extra measures to Quarantine the suspects. </p>
                                </div>
                                <div class="col-md-6">
                                    <img src="http://c.lmis.gov.pk/psmis/application/che/images/minister.png" width="100%"/>
                                </div>
                            </div>

                        <?php } ?>
                        <div class="row">
                            <div class="col-md-12"><?php
                                //chech if record exists
                                $qry_vouchers = "SELECT DISTINCT tbl_stock_master.TranNo,
                                        tbl_stock_detail.IsReceived
                                FROM
                                        tbl_stock_master
                                
                                INNER JOIN tbl_stock_detail ON tbl_stock_master.PkStockID = tbl_stock_detail.fkStockID
                                WHERE
                                        tbl_stock_master.TranTypeID = 2 AND
                                        tbl_stock_detail.IsReceived = 0 AND
                                        tbl_stock_master.WHIDTo = '" . $_SESSION['user_warehouse'] . "' ";

                                if ($_SESSION['is_allowed_im'] == 1 && !empty($_SESSION['im_start_month']) && $_SESSION['im_start_month'] > '2017-01-01')
                                    $qry_vouchers .= "  AND tbl_stock_master.TranDate > '" . $_SESSION['im_start_month'] . "' ";

                                $qry_vouchers .= "               ORDER BY
                                                                        tbl_stock_master.PkStockID ASC";
                                $getStockIssues = mysql_query($qry_vouchers) or die("Err GetStockIssueId");

                                $issueVoucher = '';
                                $a = '';
                                if (mysql_num_rows($getStockIssues) > 0) {

                                    //fetch results
                                    while ($resStockIssues = mysql_fetch_assoc($getStockIssues)) {
                                        $a = " <a href=\"new_receive_wh.php?issue_no=" . $resStockIssues['TranNo'] . "&search=true\">" . $resStockIssues['TranNo'] . "</a>";
                                        $issueVoucher[$resStockIssues['TranNo']] = $a;
                                    }
                                }
                                if (!empty($issueVoucher) && count($issueVoucher) > 0) {
                                    echo 'Pending Vouchers are : ';
                                    echo implode(',', $issueVoucher);
                                    echo '<hr>';
                                }
                                ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php include PUBLIC_PATH . "/html/footer.php"; ?>
</body>
</html>