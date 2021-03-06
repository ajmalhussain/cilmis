<?php
include("../includes/classes/AllClasses.php");
include(PUBLIC_PATH . "html/header.php");
$title = "Stock Issue List";
$userid = $_SESSION['user_id'];
$wh_id = $_SESSION['user_warehouse'];
$orderBy = '';
if ($_GET['type'] == 'loc') {
    $title = 'Location ';
    $groupby = ' GROUP BY tbl_warehouse.wh_name, itminfo_tab.itm_name, tbl_stock_master.TranDate ORDER BY tbl_warehouse.wh_name, tbl_stock_master.TranDate ASC';
} else if ($_GET['type'] == 'prod') {
    $title = 'Product ';
    $groupby = ' GROUP BY itminfo_tab.itm_name, tbl_warehouse.wh_name, tbl_stock_master.TranDate ORDER BY itminfo_tab.itm_name, tbl_stock_master.TranDate ASC';
}

if (isset($_REQUEST['type']) && !empty($_REQUEST['type'])) {

    if (!empty($_REQUEST['searchby']) && !empty($_REQUEST['number'])) {
        //get searchby
        $searchby = $_REQUEST['searchby'];
        //get number
        $number = trim($_REQUEST['number']);
        switch ($searchby) {
            case 1:
                $objStockMaster->TranNo = $number;
                break;
            case 2:
                $objStockMaster->TranRef = $number;
                break;
            case 3:
                $objStockMaster->batch_no = $number;
                break;
        }
    }
//check warehouse
    if (isset($_REQUEST['warehouse']) && !empty($_REQUEST['warehouse'])) {
        //get warehouse

        $objStockMaster->WHIDTo = $_REQUEST['warehouse'];
    }
    //check product
    if (isset($_REQUEST['product']) && !empty($_REQUEST['product'])) {
        //get product

        $objStockMaster->item_id = $_REQUEST['product'];
    }
    //check funding_source
    if (isset($_REQUEST['funding_source']) && !empty($_REQUEST['funding_source']) && strtolower($_REQUEST['funding_source']) != 'undefined') {
        //get funding_source
        $objStockMaster->funding_source = $_REQUEST['funding_source'];
    }
    //check province
    if (isset($_REQUEST['province']) && !empty($_REQUEST['province'])) {
        //get province
        $objStockMaster->province = $_REQUEST['province'];
    }
    //check stakeholder
    if (isset($_REQUEST['stakeholder']) && !empty($_REQUEST['stakeholder'])) {
        //get stakeholder
        $objStockMaster->stakeholder = $_REQUEST['stakeholder'];
    }
//check date_from
    if (isset($_REQUEST['date_from']) && !empty($_REQUEST['date_from'])) {
        //get date_from
        $objStockMaster->fromDate = dateToDbFormat($_REQUEST['date_from']);
    }
    //check date_to
    if (isset($_REQUEST['date_to']) && !empty($_REQUEST['date_to'])) {
        //get date_to
        $objStockMaster->toDate = dateToDbFormat($_REQUEST['date_to']);
    }
}
//Stock Issue Search
$result = $objStockMaster->StockIssueSearch2(2, $wh_id, $groupby, 'summary');
?>

<!-- Content -->

<div id="content_print" style="margin-left:40px;">
    <style type="text/css" media="print">
        @media print
        {    
            #printButt
            {
                display: none !important;
            }
        }
    </style>
    <?php
    //report name
    $rptName = $title . 'wise Stock Issue Summary List';
    include('report_header.php');
   
    //echo '<pre>';print_r($_REQUEST);
    
    

    //product
    $product = '0';
    //location
    $location = '0';
    $i = 0;
//fetch results
    while ($row = mysql_fetch_object($result)) {
        $productArr[$row->itm_name][] = $row;
        $locationArr[$row->wh_name][] = $row;
        
        $funding_source = $row->funding_source;
        $product = $row->itm_name;
        $stk = $row->stakeholder_name;
        $province = $row->province;
        $issued_to = $row->wh_name;
    }

    
    echo '<div style="line-height:1;">';    
    echo '<div id="report_type" style="float:left;  text-align:center; width:100%;">';
    echo '<p>Stock Issued Between <b>'.$_REQUEST['date_from'].'</b> and <b>'.$_REQUEST['date_to'].'</b></p>';
    
    if(!empty($_REQUEST['funding_source'])) echo '<p>Funding Source : <b>'.$funding_source.'</b></p>';
    if(!empty($_REQUEST['product'])) echo '<p>Product : <b>'.$product.'</b></p>';
    if(!empty($_REQUEST['province'])) echo '<p>Province: <b>'.$province.'</b></p>';
    if(!empty($_REQUEST['stakeholder'])) echo '<p>Stakeholder : <b>'.$stk.'</b></p>';
    if(!empty($_REQUEST['warehouse'])) echo '<p> Warehouse : <b>'.$issued_to.'</b></p>';
    echo '';
    echo '</div>';
    echo '</div>';
    
    echo '<hr>';echo '<hr>';
    
    if ($result && $_GET['type'] == 'prod') {
        foreach ($productArr as $key => $data) {
            ?>
            <b><?php echo $key; ?></b>
            <table id="myTable" style="margin-bottom:20px;" class="table-condensed" cellpadding="3">
                <tr>
                    <th width="10%">S.No.</th>
                    <th width="17%">Issue Date</th>
                    <th>Issue To</th>
                    <th width="15%">Quantity</th>
                    <th width="8%">Unit</th>
                    <th width="15%">Price</th>
                    <th width="12%">Cartons</th>
                </tr>
                <tbody>
                    <?php
                    $i = 1;
                    //total qty
                    $totalQty = 0;
                    $total_price = 0;
                    //total carton
                    $totalCartons = 0;
                    foreach ($data as $val) {
                        $totalQty += abs($val->Qty);
                        $totalCartons += abs($val->Qty) / $val->qty_carton;
                        
                        $this_price = 0;
                        if(!empty($val->price_of_qty))
                        $this_price = abs($val->price_of_qty);
                        
                        $total_price += $this_price;
                        ?>
                        <tr>
                            <td style="text-align:center;"><?php echo $i++; ?></td>
                            <td style="text-align:center;"><?php echo date('d/M/y', strtotime($val->TranDate)); ?></td>
                            <td><?php echo $val->wh_name.' ('.$val->stk_office_name.')'; ?></td>
                            <td style="text-align:right;"><?php echo number_format(abs($val->Qty)); ?>&nbsp;</td>
                            <td><?php echo $val->UnitType; ?></td>
                            <td style="text-align:right;"><?php echo (($this_price != 0)?number_format($this_price):'' ); ?>&nbsp;</td>
                            <td style="text-align:right;"><?php echo number_format(abs($val->Qty) / $val->qty_carton); ?>&nbsp;</td>
                        </tr>
                        <?php
                    }
                    ?>
                    <tr>
                        <td colspan="3" align="right"><b>Total</b></td>
                        <td style="text-align:right;"><b><?php echo number_format($totalQty); ?></b></td>
                        <td>&nbsp;</td>
                        <td style="text-align:right;"><b><?php echo number_format($total_price); ?></b></td>
                        <td style="text-align:right;"><b><?php echo number_format($totalCartons); ?></b></td>
                    </tr>
                </tbody>
            </table>
            <?php
        }
    }
    if ($result && $_GET['type'] == 'loc') {
        foreach ($locationArr as $key => $data) {
            ?>
            <b><?php echo $key; ?></b>
            <table id="myTable" style="margin-bottom:20px;" class="table-condensed" cellpadding="3">
                <thead>
                    <tr>
                        <th width="10%">S.No.</th>
                        <th width="17%">Issue Date</th>
                        <th>Product</th>
                        <th width="20%">Quantity</th>
                        <th width="6%">Unit</th>
                        <th width="6%">Price</th>
                        <th width="15%">Cartons</th>
                    </tr>

                </thead>
                <tbody>
                    <?php
                    $i = 1;
                    //total qty
                    $totalQty = 0;
                    $total_price = 0;
                    //total cartons
                    $totalCartons = 0;
                    foreach ($data as $val) {
                        $totalQty += abs($val->Qty);
                        $totalCartons += abs($val->Qty) / $val->qty_carton;
                        
                        $this_price = 0;
                        if(!empty($val->price_of_qty))
                        $this_price = abs($val->price_of_qty);
                        
                        $total_price += $this_price;
                        ?>

                        <tr>
                            <td style="text-align:center;"><?php echo $i++; ?></td>
                            <td style="text-align:center;"><?php echo date('d/M/y', strtotime($val->TranDate)); ?></td>
                            <td><?php echo $val->itm_name; ?></td>
                            <td style="text-align:right;"><?php echo number_format(abs($val->Qty)); ?>&nbsp;</td>
                            <td><?php echo $val->UnitType; ?></td>
                            <td style="text-align:right;"><?php echo number_format($this_price); ?>&nbsp;</td>
                            <td style="text-align:right;"><?php echo number_format(abs($val->Qty) / $val->qty_carton); ?>&nbsp;</td>
                        </tr>
                        <?php
                    }
                    ?>
                    <tr>
                        <td colspan="3" align="right"><b>Total</b></td>
                        <td style="text-align:right;"><b><?php echo number_format($totalQty); ?></b></td>
                        <td>&nbsp;</td>
                        <td style="text-align:right;"><b><?php echo number_format($total_price); ?></b></td>
                        <td style="text-align:right;"><b><?php echo number_format($totalCartons); ?></b></td>
                    </tr>
                </tbody>
            </table>
            <?php
        }
    }
    ?>
    <div style="float:left; font-size:12px;">
        <b>Print Date:</b> <?php echo date('d/M/y') . ' <b>by</b> ' . $_SESSION['user_name']; ?>
    </div>
    <div style="float:right; margin-top:10px;" id="printButt">
        <input type="button" name="print" value="Print" class="btn btn-warning" onclick="javascript:printCont();" />
    </div>

</div>

<?php
//unset session
unset($_SESSION['stock_id']);
?>
<script src="<?php echo PUBLIC_URL; ?>assets/global/plugins/jquery-1.11.0.min.js" type="text/javascript"></script>
<script language="javascript">
            $(function () {
                printCont();
            })
            function printCont()
            {
                window.print();
            }
</script>