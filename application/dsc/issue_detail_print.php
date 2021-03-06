<?php
include("../includes/classes/AllClasses.php");
include(PUBLIC_PATH . "html/header.php");
$title = "Stock Issue List";
$userid = $_SESSION['user_id'];
$wh_id = $_SESSION['user_warehouse'];
$totalQty = $totalCartons = '';
$groupby = '';
if ($_GET['grpBy'] == 'loc') {
    $title = 'Location wise ';
    //set group by
    $groupby = ' ORDER BY tbl_warehouse.wh_name, tbl_stock_master.TranDate ASC';
} else if ($_GET['grpBy'] == 'prod') {
    $title = 'Product wise ';
    //set group by
    $groupby = ' ORDER BY itminfo_tab.itm_name, tbl_stock_master.TranDate ASC';
} else {
    $title = '';
}
//check group by
if (isset($_REQUEST['grpBy']) && !empty($_REQUEST['grpBy'])) {
//check search by
    if (!empty($_REQUEST['searchby']) && !empty($_REQUEST['number'])) {
        //get search by
        $searchby = $_REQUEST['searchby'];
        //get number
        $number = trim($_REQUEST['number']);
        //check search by
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
    //check date to
    if (isset($_REQUEST['date_to']) && !empty($_REQUEST['date_to'])) {
        //get date to 
        $objStockMaster->toDate = dateToDbFormat($_REQUEST['date_to']);
    }
}
//Stock Issue Search
$result = $objStockMaster->StockIssueSearch(2, $wh_id, $groupby);
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
$rptName = $title . 'Stock Issue List';
//include report_header
include('report_header.php');
?>
    <?php
    //product 
    $product = '0';
    //location 
    $location = '0';
    //total Vials 
    $totalVials = 0;
    //total Doses 
    $totalDoses = 0;
    $i = 0;
//check group by
    if ($_GET['grpBy'] != 'none') {
        //fetch result
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
    
        if ($result && $_GET['grpBy'] == 'prod') {
            ?>

            <b>Products Summary</b>
            <table id="myTable" class="table-condensed" style="margin-bottom:20px;">
            <thead>
            <tr>
                <th width="6%">S.No.</th>
                <th>Product</th>
                <th>Quantity</th>
                <th>Unit</th>
                <th>Price</th>
                <th>Cartons</th>
            </tr>
            </thead>
            <tbody>
            <?php

            $count = 1;
            foreach ($productArr as $key => $data) {
                    //totla qty
                    $totalQty = 0;
                    $total_price = 0;
                    //total Cartons
                    $totalCartons = 0;
                    foreach ($data as $val) {
                        $i++;
                        //total qty
                        $totalQty += abs($val->Qty);
                        //total Cartons
                        $totalCartons += abs($val->Qty) / $val->qty_carton;
                        $unt = $val->UnitType;
                        
                        if(!empty($val->unit_price))
                        $total_price +=  number_format($val->unit_price,2) * abs($val->Qty);
                        
                    }
                    
                    
                    ?>
                    <tr>
                        <td align="center"><?php echo $count; ?></td>
                        <td align="left"><?php echo $key; ?></td>
                        <td align="right"><?php echo number_format($totalQty); ?> </td>
                        <td align="left"><?php echo $unt; ?> </td>
                        <td align="right"><?php echo number_format($total_price); ?> </td>
                        <td align="right"><?php echo round($totalCartons); ?></td>
                    </tr>

                <?php
                $count++;
            }
?>
            </tbody>
                </table>
<?php
            foreach ($productArr as $key => $data) {
                ?>
                <b><?php echo $key; ?></b>
                <table id="myTable" class="table-condensed" style="margin-bottom:20px;">
                    <thead>
                        <tr>
                            <th width="6%">S.No.</th>
                            <th width="12%">Issue Date</th>
                            <th>Issue To</th>
                            <th width="12%">Batch No.</th>
                            <th width="12%">Manufacturer</th>
                            <th width="12%">Expiry Date</th>
                            <th width="12%">Quantity</th>
                            <th width="8%">Unit</th>
                            <th width="8%">Price</th>
                            <th width="8%">Cartons</th>
                        </tr>
                    </thead>
                    <tbody>
            <?php
            $i = 0;
            //totla qty
            $totalQty = 0;
            $total_price = 0;
            //total Cartons 
            $totalCartons = 0;
            foreach ($data as $val) {
                $i++;
                //total qty
                $totalQty += abs($val->Qty);
                //total Cartons 
                $totalCartons += abs($val->Qty) / $val->qty_carton;
                
                $this_price = 0;
                if(!empty($val->unit_price))
                $this_price = number_format($val->unit_price,2) * abs($val->Qty);

                $total_price += $this_price;
                ?>
                            <tr>
                                <td style="text-align:center;"><?php echo $i; ?></td>
                                <td style="text-align:center;"><?php echo date("d/M/y", strtotime($val->TranDate)); ?></td>
                                <td><?php echo $val->wh_name; ?></td>
                                <td><?php echo $val->batch_no; ?></td>
                                <td><?php echo $val->stkname; ?></td>
                                <td style="text-align:center;"><?php echo date("d/M/y", strtotime($val->batch_expiry)); ?></td>
                                <td style="text-align:right;"><?php echo number_format(abs($val->Qty)); ?></td>
                                <td style="text-align:center;"><?php echo $val->UnitType; ?></td>
                                <td style="text-align:right;"><?php echo (($this_price != 0)?number_format($this_price):'' ); ?>&nbsp;</td>
                                <td style="text-align:right;"><?php echo number_format(abs($val->Qty) / $val->qty_carton); ?>&nbsp;</td>
                            </tr>
                <?php
            }
            ?>
                        <tr>
                            <td colspan="6" align="right"><b>Total</b></td>
                            <td align="right"><b><?php echo number_format($totalQty); ?></b></td>
                            <td>&nbsp;</td>
                            <td style="text-align:right;"><b><?php echo number_format($total_price); ?></b></td>
                            <td style="text-align:right;"><b><?php echo number_format($totalCartons); ?></b></td>
                        </tr>
                    </tbody>
                </table>
                        <?php
                    }
                }
                
                if ($result && $_GET['grpBy'] == 'loc') {
                    //get result
                    foreach ($locationArr as $key => $data) {
                        ?>
                <b><?php echo $key; ?></b>
                <table id="myTable" class="table-condensed" style="margin-bottom:20px;">
                    <thead>    
                        <tr>
                            <th width="6%">S.No.</th>
                            <th width="12%">Issue Date</th>
                            <th>Product</th>
                            <th width="15%">Batch No.</th>
                             <th width="15%">Manufacturer</th>
                            <th width="12%">Expiry Date</th>
                            <th width="12%">Quantity</th>
                            <th width="8%">Unit</th>
                            <th width="8%">Price</th>
                            <th width="8%">Cartons</th>
                        </tr>
                    </thead>
                    <tbody>
            <?php
            $i = 1;
            //total doses
            $totalQty = 0;
            $totalDoses = 0;
            $total_price = 0;
            //total Cartons
            $totalCartons = 0;
            foreach ($data as $val) {
                //total qty
                $totalQty += abs($val->Qty);
                //total Cartons
                $totalCartons += abs($val->Qty) / $val->qty_carton;
                
                
                $this_price = 0;
                if(!empty($val->unit_price))
                $this_price = number_format($val->unit_price,2) * abs($val->Qty);

                $total_price += $this_price;
                ?>
                            <tr>
                                <td style="text-align:center;"><?php echo $i++; ?></td>
                                <td style="text-align:center;"><?php echo date("d/M/y", strtotime($val->TranDate)); ?></td>
                                <td><?php echo $val->itm_name; ?></td>
                                <td><?php echo $val->batch_no; ?></td>
                                 <td><?php echo $val->stkname; ?></td>
                                <td style="text-align:center;"><?php echo date("d/M/y", strtotime($val->batch_expiry)); ?></td>
                                <td style="text-align:right;"><?php echo number_format(abs($val->Qty)); ?></td>
                                <td style="text-align:center;"><?php echo $val->UnitType; ?></td>
                                <td style="text-align:right;"><?php echo (($this_price != 0)?number_format($this_price):'' ); ?>&nbsp;</td>
                                <td style="text-align:right;"><?php echo number_format(abs($val->Qty) / $val->qty_carton); ?>&nbsp;</td>
                            </tr>
                            <?php
                        }
                        ?>
                        <tr>
                            <td colspan="6" align="right"><b>Total</b></td>
                            <td align="right"><b><?php echo number_format($totalQty); ?></b></td>
                            <td>&nbsp;</td>
                            <td style="text-align:right;"><b><?php echo number_format($total_price); ?></b></td>
                            <td style="text-align:right;"><b><?php echo number_format($totalCartons); ?></b></td>
                        </tr>
                    </tbody>
                </table>
            <?php
        }
    }
} else {
    ?>
        
    <?php
    $i = 0;
    //total Vials 
    $totalVials = 0;
    $total_price = 0;
    //total Cartons 
    $totalCartons = 0;
    //fetch result
    $non_grouped= array();
    while ($row = mysql_fetch_object($result)) {
        $non_grouped[]=$row;
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
    ?>
                <table id="myTable" class="table-condensed" style="margin-bottom:20px;">
            <thead>
                <tr>
                    <th width="6%">S.No.</th>
                    <th width="10%">Issue Date</th>
                    <th width="15%">Product</th>
                    <th  width="25%">Issue To</th>
                    <th width="10%">Batch No.</th>
                    <th width="10%">Manufacturer</th>
                    <th width="10%">Expiry Date</th>
                    <th width="10%">Quantity</th>
                    <th width="8%">Unit</th>
                    <th width="8%">Price</th>
                    <th width="8%">Cartons</th>
                </tr>
            </thead>
            <tbody>
    <?php
    foreach($non_grouped as $k => $val)
    {
        $i++;
        //total Vials 
        $totalVials += abs($val->Qty);
        //total Cartons 
        $totalCartons += abs($val->Qty) / $val->qty_carton;
         
        $this_price = 0;
        if(!empty($val->unit_price))
        $this_price = number_format($val->unit_price,2) * abs($val->Qty);

        $total_price += $this_price;   
        ?>

                    <tr>
                        <td style="text-align:center;"><?php echo $i; ?></td>
                        <td style="text-align:center;"><?php echo date("d/M/y", strtotime($val->TranDate)); ?></td>
                        <td><?php echo $val->itm_name; ?></td>
                        <td><?php echo $val->wh_name.' ('.$val->stk_office_name.')'; ?></td>
                        <td><?php echo $val->batch_no; ?></td>
                         <td><?php echo $val->stkname; ?></td>
                        <td style="text-align:center;"><?php echo date("d/M/y", strtotime($val->batch_expiry)); ?></td>
                        <td style="text-align:right;"><?php echo number_format(abs($val->Qty)); ?></td>
                        <td style="text-align:center;"><?php echo $val->UnitType; ?></td>
                                <td style="text-align:right;"><?php echo (($this_price != 0)?number_format($this_price):'' ); ?>&nbsp;</td>
                        <td style="text-align:right;"><?php echo number_format(abs($val->Qty) / $val->qty_carton); ?></td>					
                    </tr>
                    <?php
                }
                ?>
                <tr>
                    <td colspan="7" align="right"><b>Total</b></td>
                    <td align="right"><b><?php echo number_format($totalVials); ?></b></td>
                    <td>&nbsp;</td>
                    <td style="text-align:right;"><b><?php echo number_format($total_price); ?></b></td>
                    <td style="text-align:right;"><b><?php echo number_format($totalCartons); ?></b></td>
                </tr>
            </tbody>
        </table>
    <?php
}
?>
    <div style="float:left; font-size:12px;">
        <b>Print Date:</b> <?php echo date('d/M/y') . ' <b>by</b> ' . $_SESSION['user_name']; ?>
    </div>
    <div style="float:right;" id="printButt">
        <input type="button" name="print" value="Print" class="btn btn-warning" onclick="javascript:printCont();" />
    </div>

</div>
<?php
//unset stock id
unset($_SESSION['stock_id']);
?>
<script language="javascript">
    $(function() {
        printCont();
    })
    function printCont()
    {
        window.print();
    }
</script>