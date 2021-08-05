<?php
include("../includes/classes/AllClasses.php");
include(PUBLIC_PATH . "html/header.php");

if(!empty($_SESSION['user_warehouse']))
    $wh_id = $_SESSION['user_warehouse'];
else
    $wh_id  = 123;

$sCriteria = array();
$number = '';
$date_from = '';
$date_to = '';
$searchby = '';
$warehouse = '';
$product = '';
$manufacturer = '';
$procured_by = '';
//echo '<pre>';print_r($_REQUEST);exit;
//check if submitted
if (isset($_REQUEST['search']) && !empty($_REQUEST['search'])) {
//check search by
    if (!empty($_REQUEST['status']) && !empty($_REQUEST['status'])) {
        //get search by
        $searchby = $_REQUEST['status'];
        $sCriteria['status'] = $searchby;
        $objshipments->status = $searchby;
    }
    //check warehouse
    if (isset($_REQUEST['warehouse']) && !empty($_REQUEST['warehouse'])) {
        //get warehouse
        $warehouse = $_REQUEST['warehouse'];
        $sCriteria['warehouse'] = $warehouse;
        //set from warehouse
        $objshipments->WHID = $warehouse;
    }
    //check product
    if (isset($_REQUEST['product']) && !empty($_REQUEST['product'])) {
        //get product
        $product = $_REQUEST['product'];
        $sCriteria['product'] = $product;
        //set product
        $objshipments->item_id = $product;
    }
    //check manufacturer
    if (isset($_REQUEST['manufacturer']) && !empty($_REQUEST['manufacturer'])) {
        //get manufacturer
        $manufacturer = $_REQUEST['manufacturer'];
        //set manufacturer	
        $objshipments->manufacturer = $manufacturer;
    }
    //check procured by
    if (isset($_REQUEST['procured_by']) && !empty($_REQUEST['procured_by'])) {
        //get manufacturer
        $procured_by = $_REQUEST['procured_by'];
        //set manufacturer	
        $objshipments->procured_by = $procured_by;
        $sCriteria['procured_by'] = $procured_by;
    }
    //check date from
    if (isset($_REQUEST['date_from']) && !empty($_REQUEST['date_from'])) {
        //get date from
        $date_from = $_REQUEST['date_from'];
        $dateArr = explode('/', $date_from);
        $sCriteria['date_from'] = dateToDbFormat($date_from);
        //set date from	
        $objshipments->fromDate = dateToDbFormat($date_from);
    }
    //check to date
    if (isset($_REQUEST['date_to']) && !empty($_REQUEST['date_to'])) {
        //get to date
        $date_to = $_REQUEST['date_to'];
        $dateArr = explode('/', $date_to);
        $sCriteria['date_to'] = dateToDbFormat($date_to);
        //set to date	
        $objshipments->toDate = dateToDbFormat($date_to);
    }
    $_SESSION['sCriteria'] = $sCriteria;
} else {
    //date from
    $date_from = date('01/01/Y');
    //date to
    $date_to = date('t/12/Y');
    //set from date
    $objshipments->fromDate = dateToDbFormat($date_from);
    //set to date
    $objshipments->toDate = dateToDbFormat($date_to);

    $sCriteria['date_from'] = dateToDbFormat($date_from);
    $sCriteria['date_to'] = dateToDbFormat($date_to);
    ;
    $_SESSION['sCriteria'] = $sCriteria;
}

//Stock Search
$gp_by = " GROUP BY purchase_order.pk_id  ";
$result = $objPurchaseOrder->ShipmentSearch(1, $wh_id,$gp_by);
//title
$title = "Shipments";
//Get User Receive From WH
$join1=$where1="";
if(isset($_SESSION['user_level']) && $_SESSION['user_level'] == 2) {
    $join1.= " INNER JOIN funding_stk_prov ON tbl_warehouse.wh_id = funding_stk_prov.funding_source_id ";
    if(isset($_SESSION['user_province1'])) {
        $where1 .= " AND funding_stk_prov.province_id = ".$_SESSION['user_province1']." ";
    }
    if(isset($_SESSION['user_stakeholder1'])) {
        $where1 .= " AND funding_stk_prov.stakeholder_id = ".$_SESSION['user_stakeholder1']." ";
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



$strSql2 = "
        SELECT
            tbl_stock_detail.Qty,
            stock_batch.batch_no,
            itminfo_tab.itm_name,
            tbl_stock_master.PkStockID,
            tbl_stock_master.TranNo,tbl_stock_master.shipment_id,
            purchase_order.*,
            sum(tbl_stock_detail.Qty) as received_qty,     
            tbl_warehouse.wh_name as stkname,
             
            tbl_itemunits.UnitType,
            tbl_locations.LocName as procured_by,
            itminfo_tab.qty_carton as qty_carton_old,
            stakeholder_item.quantity_per_pack as qty_carton
        FROM
            tbl_stock_master
             INNER JOIN tbl_stock_detail ON tbl_stock_master.PkStockID = tbl_stock_detail.fkStockID
             LEFT JOIN stakeholder ON tbl_stock_detail.manufacturer = stakeholder.stkid
             INNER JOIN stock_batch ON tbl_stock_detail.BatchID = stock_batch.batch_id
             INNER JOIN itminfo_tab ON stock_batch.item_id = itminfo_tab.itm_id
             LEFT JOIN tbl_itemunits ON itminfo_tab.itm_type = tbl_itemunits.UnitType
             INNER JOIN shipments ON tbl_stock_master.shipment_id = purchase_order.pk_id
             INNER JOIN tbl_warehouse ON purchase_order.stk_id = tbl_warehouse.wh_id
             INNER JOIN tbl_locations ON purchase_order.procured_by = tbl_locations.PkLocID
             INNER JOIN stakeholder_item ON stock_batch.manufacturer = stakeholder_item.stk_id
        WHERE
                tbl_stock_master.temp = 1 AND
                tbl_stock_master.WHIDTo = '" . $wh_id . "' AND
                tbl_stock_master.CreatedBy = " . $_SESSION['user_id'] . " AND 
                tbl_stock_master.TranTypeID = 1
                AND tbl_stock_master.shipment_id is not null
        ";
//    echo $strSql2;exit;    
    $rsSql21 = mysql_query($strSql2);
      
    $temp_voucher_exists = false;
     $temp_stock = array();
    if (mysql_num_rows($rsSql21) > 0) {
       
        while($row = mysql_fetch_assoc($rsSql21))
        {
            if(!empty($row['shipment_id']))
            {
                $temp_stock[$row['shipment_id']] = $row;
                $temp_voucher_exists = true;
            }
        }
    } 
//    echo '<pre>';
//print_r($temp_stock);exit;


//get all item
$items = $objManageItem->GetAllManageItem();
if (isset($_REQUEST['product']) && !empty($_REQUEST['product'])) {
    //Get All Manufacturers Update
    $manufacturers = $manufacturer_product = $objstk->GetAllManufacturersUpdate($_REQUEST['product']);
}


$requirements_arr = array();
 $sql = mysql_query("SELECT
            item_requirements.*
            FROM
            item_requirements
            WHERE
            item_requirements.wh_id = '".$_SESSION['user_warehouse']."' ");
while($res_req = mysql_fetch_assoc($sql)){
    $requirements_arr[$res_req['item_id']] = $res_req['requirement'];
}
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
$str_do = isset($_REQUEST['DO'])?$_REQUEST['DO']:'';
?>
                <!-- BEGIN PAGE HEADER-->
                <div class="row">
                    <div class="col-md-12">
                        <?php
                        if($str_do !='Edit')
                        {
                        ?>
                        <div  class="widget" data-toggle="collapse-widget">
                            <div class="widget-head">
                                <h3 class="heading">Filter By</h3>
                            </div>
                            <div id="shipment_filter_div" class="widget-body">
                                <form method="POST" name="batch_search" action="" >
                                    <!-- Row -->
                                    <div class="row">
                                        <div class="col-md-12">

                                            <!-- Group -->
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label class="control-label">Date From</label>
                                                    <input type="text" class="form-control input-medium" name="date_from" readonly id="date_from" value="<?php echo $date_from; ?>"/>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label class="control-label">Date To</label>
                                                    <input type="text" class="form-control input-medium" name="date_to"  readonly="" id="date_to" value="<?php echo $date_to; ?>"/>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="control-group">
                                                    <label class="control-label" for="product">Product</label>
                                                    <div class="controls">
                                                        <select multiple name="product[]" id="product" class="input-large  select2me">
                                                            <option value="">Select</option>
                                                            <?php
                                                            if (mysql_num_rows($items) > 0) {
                                                                while ($row = mysql_fetch_object($items)) {
                                                                    $sel = "";
                                                                    if(is_array($product)){
                                                                        if(in_array($row->itm_id, $product)){
                                                                            $sel = " selected ";
                                                                        }
                                                                    }else{
                                                                     if ($product == $row->itm_id) {
                                                                        $sel = " selected ";
                                                                     } 
                                                                    }
                                                                     ?>
                                                                    <option value="<?php echo $row->itm_id; ?>" <?=$sel?> ><?php echo $row->itm_name; ?></option>
                                                                    <?php
                                                                }
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                           
                                            <div class="col-md-1 right">
                                                <div class="form-group">
                                                    <label class="control-label">&nbsp;</label>
                                                    <div class="form-group">
                                                        <button type="submit" name="search" value="search" class="btn btn-primary">Search</button>
                                                    </div>
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
                                <h4 class="heading">Purchase Order Search</h4>
                            </div>
                            <div class="widget-body"> 
                                <table class="shipmentsearchx table table-bordered table-condensed">
                                    <thead>
                                        <tr>
                                            <th>Sr. No.</th>
                                            <th>PO No</th>
                                            <th>Manual Ref No</th>
                                            <th>PO Date</th>
                                            <th>Item</th>
                                            <th>Total Estimated Qty</th>
                                            <th>Order Placed</th>
                                            <th>Unit Price</th>
                                            <th>Total Price</th>
                                            <th>Received Qty</th>
                                            <th>Vendor</th>
                                            <th>Remaining Qty</th>
                                            <th>Expected Delivery Date</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $i = 1;
                                        $transNo = '';
                                        $required_q=0;
                                        $g_total = array();
                                        if ($result != FALSE) {
                                            //fetch result
                                            while ($row = mysql_fetch_object($result)) {

                                                $s_q = isset($row->shipment_quantity)?$row->shipment_quantity:'0';
                                                $r_q = isset($row->received_qty)?$row->received_qty:'0';
                                                $remaining_q = $s_q - $r_q;
                                                $required_q = (!empty($requirements_arr[$row->item_id])?number_format($requirements_arr[$row->item_id]):'-');

                                                $cl='';
                                                if($row->status=='Cancelled')
                                                {
                                                    $cl = 'danger';
                                                }
                                                else
                                                {
                                                    // excluding the CANCELLED shipments out of the totals
                                                    @$g_total['po'] += $s_q;
                                                    @$g_total['t_price'] += $s_q * $row->unit_price;
                                                    @$g_total['rcvd'] += $r_q;
                                                    @$g_total['remaining'] += $remaining_q;
                                                }
                                                ?>
                                                <tr class="gradeX <?=$cl?>" >
                                                    <td class="text-center"><?php echo $i; ?></td>
                                                    <td ><a href="add_purchase_order.php?po_id=<?=$row->pk_id?>&DO=Update"><?php echo $row->po_number; ?></a></td>
                                                    <td><?php echo $row->reference_number; ?></td>
                                                    <td class="" id="<?php echo $row->pk_id; ?>"><?php echo date("Y-M-d", strtotime($row->po_date)); ?></td>

                                                    <td><?php echo $row->itm_name; ?></td>
                                                    <td class="right"><?php echo ($required_q); ?></td>
                                                    <td class="right"><?php echo number_format($s_q); ?></td>
                                                    <td class="right"><?php echo number_format($row->unit_price,2)  ?></td>
                                                    <td class="right"><?php echo number_format($s_q * $row->unit_price)  ?></td>
                                                    <td class="right"><?php echo number_format($r_q); ?></td>
                                                    <td><?php echo $row->vendor_name; ?></td>
                                                    
                                                    <td class="right"><?php echo number_format($remaining_q); ?></td>
                                                    <td><?php echo date("Y-M-d", strtotime($row->po_date)); ?></td>

                                                    <?php

                                                        if($r_q > 0 && $remaining_q > 0)
                                                        {
                                                            $st =  'Partially Received';
                                                            $cls = "";

                                                                if($row->status=='Cancelled')
                                                                {
                                                                    $st =  'Partially Received & Cancelled';
                                                                }
                                                        }
                                                        elseif($r_q > 0 && $remaining_q == 0)
                                                        {
                                                            $st =  'Received';
                                                            $cls = " success ";
                                                        }
                                                        elseif($row->status =='Received')
                                                        {
                                                            $st = $row->status; 
                                                            $cls = " success ";
                                                        }
                                                        else
                                                        {
                                                            $st = $row->status; 
                                                            $cls = "";
                                                        }
                                                        ?>
                                                    <td align="center" class=" <?=$cls?> ">
                                                        <?=$st?>
                                                        <?php
                                                            if($st == 'Received'  || $st== 'Partially Received'){
                                                                echo $objshipments->getReceivedVouhcers($row->pk_id);
                                                            }
                                                        ?>
                                                    </td>

                                                </tr>
                                                
                                                <?php
                                                $i++;
                                            }
                                            ?>
                                                <tr>
                                                    <td colspan="6">Total</td>
                                                    <td align="right"><?=(!empty($g_total['po'])?number_format($g_total['po']):'0')?></td>
                                                    <td></td>
                                                    <td align="right"><?=(!empty($g_total['t_price'])?number_format($g_total['t_price']):'0')?></td>
                                                    <td align="right"><?=(!empty($g_total['rcvd'])?number_format($g_total['rcvd']):'0')?></td>
                                                    <td></td>
                                                    <td align="right"><?=(!empty($g_total['remaining'])?number_format($g_total['remaining']):'0')?></td>
                                                    <td></td>
                                                    <td></td>
                                                </tr
                                                <tr>
                                                    <td colspan="8">Total In Millions</td>
                                                    <td align="right"><?=(!empty($g_total['t_price'])?nice_number($g_total['t_price'],'m') : '0')?> Mill</td>
                                                    <td colspan="5"></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="8">Total In Billions</td>
                                                    <td align="right"><?=(!empty($g_total['t_price'])?nice_number($g_total['t_price'],'b') : '0')?> Bill</td>
                                                    <td colspan="5"></td>
                                                </tr>
                                            <?php
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
    <script src="<?php echo PUBLIC_URL; ?>js/dataentry/search-purchase_order.js"></script>
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
                            if($temp_voucher_exists)
                            {
                                ?>
                        <script type="text/javascript">
                            $( document ).ready(function() {
                                $('#shipment_filter_div').collapse('500');
                            });
                            
                        </script>
                            <?php
                            }
                            ?>
                        
                        <script>
                            $("#receive_date").datepicker({
                                dateFormat: 'yyyy-mm-dd',
                                constrainInput: false,
                                changeMonth: true,
                                changeYear: true
                            });
                        </script>
</body>
</html>