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
    //check product
    if (isset($_REQUEST['product']) && !empty($_REQUEST['product'])) {
        //get product
        $product = $_REQUEST['product'];
        $sCriteria['product'] = $product;
        //set product
        $objPurchaseOrder->item_id = $product;
    }
    //check manufacturer
    if (isset($_REQUEST['manufacturer']) && !empty($_REQUEST['manufacturer'])) {
        //get manufacturer
        $manufacturer = $_REQUEST['manufacturer'];
        //set manufacturer	
        $objPurchaseOrder->manufacturer = $manufacturer;
    }
    //check procured by
    if (isset($_REQUEST['procured_by']) && !empty($_REQUEST['procured_by'])) {
        //get manufacturer
        $procured_by = $_REQUEST['procured_by'];
        //set manufacturer	
        $objPurchaseOrder->procured_by = $procured_by;
        $sCriteria['procured_by'] = $procured_by;
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
    ;
    $_SESSION['sCriteria'] = $sCriteria;
}

//Stock Search
$gp_by = " GROUP BY shipments.pk_id  ";
//$result = $objPurchaseOrder->ShipmentSearch(1, $wh_id,$gp_by);

$items = $objManageItem->GetAllManageItem();
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
                                <h4 class="heading">Purchase Order Status Wise Count</h4>
                            </div>
                            <div class="widget-body"> 
                                <table class="shipmentsearchx table table-bordered table-condensed">
                                    <thead>
                                        <tr>
                                            <th>Sr. No.</th>
                                            <th>Status</th>
                                            <th>No of POs</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        
                              $qry="SELECT
                                    purchase_order.`status`,
                                    Count(purchase_order.pk_id) as num
                                    FROM
                                            purchase_order
                                    WHERE
                                            purchase_order.wh_id = ".$_SESSION['user_warehouse']." ";
                              if(!empty($date_from) && !empty($date_to)){
                                $qry .= "
                                        AND DATE_FORMAT(purchase_order.contract_delivery_date,'%Y-%m-%d') 
                                        BETWEEN '" . $date_from . "' AND '" . $date_to . "' ";
                              } 
                              if(!empty($product)){
                                $qry .= "
                                        AND item_id in (".implode(',',$product).") ";
                              }
                              $qry .=" 
                                    GROUP BY
                                        purchase_order.`status` ";
                                         
                              //echo $qry;exit;
                              $res = mysql_query($qry);
                              $st_arr = array();
                              while($row = mysql_fetch_assoc($res)){
                                  $st_arr[$row['status']] = $row['num'];
                                  
                              }
                                  echo '<tr>';
                                  echo '<td>1</td>';
                                  echo '<td>Total POs Issued</td>';
                                  echo '<td>'.$st_arr['Active'].'</td>';
                                  echo '</tr>';
                                  echo '<tr>';
                                  echo '<td>2</td>';
                                  echo '<td>No of Cancelled POs</td>';
                                  echo '<td>'.(!isset($st_arr['Cancelled'])?0:$st_arr['Cancelled']).'</td>';
                                  echo '</tr>';
                                  echo '<tr>';
                                  echo '<td>3</td>';
                                  echo '<td>No of Non Response POs</td>';
                                  echo '<td>'.(!isset($st_arr['InActive'])?0:$st_arr['InActive']).'</td>';
                                  echo '</tr>';
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
</body>
</html>