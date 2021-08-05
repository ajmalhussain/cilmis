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
                            <a  class="btn yellow-casablanca btn-rounded">
                                <span>
                                    <?php
                                    echo "<div>Welcome: " . $_SESSION['user_name'] . " </div>";
                                    ?> 
                                </span>
                                <em>
                                    <i class="fa fa-tags"></i>
                                    DSC-MIS </em>
                                <i class="fa fa-globe top-news-icon"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="portlet">
                    <div class="portlet-body">
                        <div class="well well-sm">
                            <div class="news-blocks">
                            <h3>
                                <a  >
                                    District Supply Chain - Management Information System </a>
                            </h3>
                            <p>
                                Pakistan Logistics Management Information System
                            </p>
                            </h3>
                            <p>
                                Strengthening the Logistics Management Information Systems (LMIS) is an important objective in addressing the challenges of health commodities distribution in Pakistan. A systematic architected and rational approach was applied to define the country’s needs for supply chain and logistics information management and most effective technology solutions were identified to address those needs. The LMIS is designed and implemented to be a sustainable source for health commodity supply chain monitoring and for informed decision making.
                            </p>
                        </div>
                        </div>
                        <div class="row">
                                <div class="col-md-12"><?php 
                                    //chech if record exists
                                $qry_vouchers= "SELECT DISTINCT tbl_stock_master.TranNo,
                                        tbl_stock_detail.IsReceived
                                FROM
                                        tbl_stock_master
                                
                                INNER JOIN tbl_stock_detail ON tbl_stock_master.PkStockID = tbl_stock_detail.fkStockID
                                WHERE
                                        tbl_stock_master.TranTypeID = 2 AND
                                        tbl_stock_detail.IsReceived = 0 AND
                                        tbl_stock_master.WHIDTo = '".$_SESSION['user_warehouse']."' ";

                                if($_SESSION['is_allowed_im'] == 1 && !empty($_SESSION['im_start_month']) && $_SESSION['im_start_month'] > '2017-01-01')
                                $qry_vouchers.= "  AND tbl_stock_master.TranDate > '".$_SESSION['im_start_month']."' ";

                                $qry_vouchers.= "               ORDER BY
                                                                        tbl_stock_master.PkStockID ASC";
                                $getStockIssues = mysql_query($qry_vouchers) or die("Err GetStockIssueId");

                                $issueVoucher = '';
                                $a='';
                                   if (mysql_num_rows($getStockIssues) > 0) {

                                       //fetch results
                                       while ($resStockIssues = mysql_fetch_assoc($getStockIssues)) {
                                           $a= " <a href=\"new_receive_wh.php?issue_no=" . $resStockIssues['TranNo'] . "&search=true\">" . $resStockIssues['TranNo'] . "</a>";
                                           $issueVoucher[ $resStockIssues['TranNo']] = $a;
                                       }

                                   }
                                if(!empty($issueVoucher) && count($issueVoucher) > 0) {
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