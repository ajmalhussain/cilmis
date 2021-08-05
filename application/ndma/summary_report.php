<?php
include("../includes/classes/AllClasses.php");
include(PUBLIC_PATH . "html/header.php");

if (!empty($_SESSION['user_warehouse']))
    $wh_id = $_SESSION['user_warehouse'];
else
    $wh_id = 123;

$sCriteria = array();
$number = '';
$date_from = '';
$date_to = '';
$searchby = '';
$warehouse = '';
$product = '';
$po = '';
$vendor = '';
$procured_by = '';
$search_as = '';
//echo '<pre>';print_r($_REQUEST);exit;
//check if submitted
if (isset($_REQUEST['search']) && !empty($_REQUEST['search'])) {
//check search by
    if (!empty($_REQUEST['status']) && !empty($_REQUEST['status'])) {
        //get search by
        $searchby = $_REQUEST['status'];
        $sCriteria['status'] = $searchby;
        $objPurchaseOrder->status = $searchby;
    }
    //check warehouse
    if (isset($_REQUEST['warehouse']) && !empty($_REQUEST['warehouse'])) {
        //get warehouse
        $warehouse = $_REQUEST['warehouse'];
        $sCriteria['warehouse'] = $warehouse;
        //set from warehouse
        $objPurchaseOrder->WHID = $warehouse;
    }
    if (isset($_REQUEST['po']) && !empty($_REQUEST['po'])) {
        //get warehouse
        $po = $_REQUEST['po'];
        //set from warehouse
        $objPurchaseOrder->po_number = $po;
    }

    //check manufacturer
    if (isset($_REQUEST['vendor']) && !empty($_REQUEST['vendor'])) {
        //get manufacturer
        $vendor = $_REQUEST['vendor'];
        //set manufacturer	
        $objPurchaseOrder->manufacturer = $vendor;
    }
    //check date from
    if (isset($_REQUEST['date_from']) && !empty($_REQUEST['date_from'])) {
        //get date from
        $date_from = $_REQUEST['date_from'];
        $dateArr = explode('/', $date_from);
        $sCriteria['date_from'] = dateToDbFormat($date_from);
        //set date from	
        $objPurchaseOrder->fromDate = dateToDbFormat($date_from);
    }
    //check to date
    if (isset($_REQUEST['date_to']) && !empty($_REQUEST['date_to'])) {
        //get to date
        $date_to = $_REQUEST['date_to'];
        $dateArr = explode('/', $date_to);
        $sCriteria['date_to'] = dateToDbFormat($date_to);
        //set to date	
        $objPurchaseOrder->toDate = dateToDbFormat($date_to);
    }
    if (!empty($_REQUEST['search_as'])) {
        $sCriteria['search_as'] = $_REQUEST['search_as'];
        $search_as = $_REQUEST['search_as'];
    }
    $_SESSION['sCriteria'] = $sCriteria;
} else {
    //date from
    $date_from = date('01/01/Y');
    //date to
    $date_to = date('t/12/Y');
    //set from date
    $objPurchaseOrder->fromDate = dateToDbFormat($date_from);
    //set to date
    $objPurchaseOrder->toDate = dateToDbFormat($date_to);

    $sCriteria['date_from'] = dateToDbFormat($date_from);
    $sCriteria['date_to'] = dateToDbFormat($date_to);
    $_SESSION['sCriteria'] = $sCriteria;
}

//Stock Search
$gp_by = " GROUP BY purchase_order.pk_id  ";
$result = $objPurchaseOrder->POSummary(1, $wh_id, $gp_by, $search_as);
//title
$title = "Shipments";
//Get User Receive From WH
$join1 = $where1 = "";
if (isset($_SESSION['user_level']) && $_SESSION['user_level'] == 2) {
    $join1 .= " INNER JOIN funding_stk_prov ON tbl_warehouse.wh_id = funding_stk_prov.funding_source_id ";
    if (isset($_SESSION['user_province1'])) {
        $where1 .= " AND funding_stk_prov.province_id = " . $_SESSION['user_province1'] . " ";
    }
    if (isset($_SESSION['user_stakeholder1'])) {
        $where1 .= " AND funding_stk_prov.stakeholder_id = " . $_SESSION['user_stakeholder1'] . " ";
    }
} else {
    $where1 .= " AND tbl_stock_master.WHIDTo = $wh_id ";
}
//query copied from clsswharehouse
$strSql = "SELECT
                    tbl_warehouse.wh_name,
                    tbl_warehouse.wh_id
            FROM
            tbl_warehouse
            INNER JOIN tbl_stock_master ON tbl_stock_master.WHIDFrom = tbl_warehouse.wh_id
            INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
            $join1
            WHERE
            tbl_stock_master.TranTypeID = 1
            $where1
            GROUP BY tbl_warehouse.wh_name
            ORDER BY
            stakeholder.stkorder ASC";
//echo $strSql;
$warehouses = mysql_query($strSql) or die("Error Getwh");
$po_numbers = $objPurchaseOrder->getPO();
$vendors = $objPurchaseOrder->getVendor();
?>
<link rel="stylesheet" type="text/css" href="../../public/assets/global/plugins/select2/select2.css"/>
</head>
<!-- END HEAD -->

<body class="page-header-fixed page-quick-sidebar-over-content">
    <!-- BEGIN HEADER -->
    <div class="page-container">
        <?php
//include top
        include PUBLIC_PATH . "html/top.php";
//include top_im
        include PUBLIC_PATH . "html/top_im.php";
        ?>
        <div class="page-content-wrapper">
            <div class="page-content"> 
                <?php
                $str_do = isset($_REQUEST['DO']) ? $_REQUEST['DO'] : '';
                ?>
                <!-- BEGIN PAGE HEADER-->
                <div class="row">
                    <div class="col-md-12">
                        <?php
                        if ($str_do != 'Edit') {
                            ?>
                            <div  class="widget" data-toggle="collapse-widget">
                                <div class="widget-head">
                                    <h3 class="heading">Filter By</h3>
                                </div>
                                <div id="shipment_filter_div" class="widget-body">
                                    <form method="POST" name="batch_search" action="" >
                                        <!-- Row -->
                                        <div class="row">

                                            <!-- Group -->
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label class="control-label">Date From</label>
                                                    <input type="text" class="form-control input-sm" name="date_from" readonly id="date_from" value="<?php echo $date_from; ?>"/>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label class="control-label">Date To</label>
                                                    <input type="text" class="form-control input-sm" name="date_to"  readonly="" id="date_to" value="<?php echo $date_to; ?>"/>
                                                </div>
                                            </div>
                                            <div class="col-md-4 right">
                                                <div class="form-group">
                                                    <label class="control-label">&nbsp;</label>
                                                    <div class="form-group">
                                                        <button type="submit" name="search" value="search" class="btn btn-primary">Search</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <?php
                        }
                        ?>
                        <div class="widget" data-toggle="collapse-widget"> 
                            <div class="widget-head">
                                <h4 class="heading">Summary Report</h4>
                            </div>
                            <div class="widget-body"> 
                                <table class="table table-bordered table-condensed" style="overflow: auto;" id="myTable">


                                    <?php
                                    $data = array();
                                    $items = array();
                                    $funding_source = array();
                                    if ($result != FALSE) {
                                        while ($row = mysql_fetch_object($result)) {
                                            $items[$row->itm_name] = $row->itm_name;
                                            $funding_source[$row->funding_source] = $row->funding_source;

                                            $data[$row->itm_name][$row->funding_source]['shp_q'] = isset($row->shipment_quantity) ? $row->shipment_quantity : '0';
                                            $data[$row->itm_name][$row->funding_source]['rcv_q'] = isset($row->received_qty) ? $row->received_qty : '0';
                                            $data[$row->itm_name][$row->funding_source]['rem_q'] = $row->shipment_quantity - $row->received_qty;
                                        }
                                    }
                                    $i = 1;
                                    $transNo = '';
                                    $required_q = 0;
                                    $g_total = array();
                                    ?>
                                    <thead>
                                        <tr>
                                            <th rowspan="2">S.No</th>
                                            <th rowspan="2">Item</th>
                                            <?php
                                            foreach ($funding_source as $fs) {
                                                ?>

                                            <th colspan="3" class="center" style="background-color:#009C00;color:white;"><?php echo $fs; ?></th>
                                                <?php
                                            }
                                            ?>
                                            <th colspan="3" class="center" style="background-color:#009C00;color:white;">Summary Overall</th>
                                        </tr>
                                        <tr>
                                            <?php
                                            foreach ($funding_source as $fs) {
                                                ?>
                                                <th>Qty Ordered</th>
                                                <th>Delivered</th>
                                                <th>Balance</th>
                                                <?php
                                            }
                                            ?>
                                            <th>Qty Ordered</th>
                                            <th>Delivered</th>
                                            <th>Balance</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        foreach ($items as $item) {
                                            ?>
                                            <tr class="gradeX <?= $cl ?>" >
                                                <td class="text-center"><?php echo $i; ?></td>
                                                <td><?php echo $item; ?></td>                                            

                                                <?php
                                                foreach ($funding_source as $fs) {
                                                    @$shp_total += $data[$item][$fs]['shp_q'];
                                                    @$rcv_total += $data[$item][$fs]['rcv_q'];
                                                    @$rem_total += $data[$item][$fs]['rem_q'];
                                                    
                                                    ?>
                                                <td class="right"><?php echo number_format($data[$item][$fs]['shp_q']); ?></td>
                                                    <td class="right"><?php echo number_format($data[$item][$fs]['rcv_q']); ?></td>
                                                    <td class="right"><?php echo number_format($data[$item][$fs]['rem_q']); ?></td>
                                                    <?php
                                                }
                                                ?>
                                                <td class="right"><?php echo number_format($shp_total); ?></td>
                                                <td class="right"><?php echo number_format($rcv_total); ?></td>
                                                <td class="right"><?php echo number_format($rem_total); ?></td>
                                            </tr>

                                            <?php
                                            $i++;
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
//include footer
    include PUBLIC_PATH . "/html/footer.php";
    ?>
<!--    <script src="<?php echo PUBLIC_URL; ?>js/dataentry/add-shipment.js"></script> -->
    <script src="<?php echo PUBLIC_URL; ?>js/jquery.inlineEdit_date.js"></script> 
    <script src="<?php echo PUBLIC_URL; ?>js/dataentry/search-shipments.js"></script>
    <script type="text/javascript" src="../../public/assets/global/plugins/select2/select2.min.js"></script>
    <?php
//unset stock id
    unset($_SESSION['stock_id']);
    if (!empty($_REQUEST['s']) && $_REQUEST['s'] == 't') {
        ?>
        <script type="text/javascript">
            var self = $('[data-toggle="notyfy"]');
            notyfy({
                force: true,
                text: 'Data has been deleted successfully!',
                type: 'success',
                layout: self.data('layout')
            });
        </script>
    <?php } ?>

    <?php
    if ($temp_voucher_exists) {
        ?>
        <script type="text/javascript">
            $(function () {
                $('#shipment_filter_div').collapse('500');
            });

        </script>
        <?php
    }
    ?>

    <script>
        $(function () {

            $('#myTable').DataTable({
                "aaSorting": []
            });
        });
        $("#receive_date").datepicker({
            dateFormat: 'yyyy-mm-dd',
            constrainInput: false,
            changeMonth: true,
            changeYear: true
        });
    </script>
</body>
</html>