<?php
include("../includes/classes/AllClasses.php");
include(PUBLIC_PATH . "html/header.php");

$wh_id = $_SESSION['user_warehouse'];

$sCriteria = array();
$date_from = '';
$date_to = '';
$product = '';
if (isset($_REQUEST['search']) && !empty($_REQUEST['search'])) {
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
    $date_from = date('01' . '/m/Y');
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
//$gp_by = " GROUP BY shipments.pk_id  ";
//$result = $objshipments->ShipmentSearch(1, $wh_id,$gp_by);

$q_dash = "SELECT
            purchase_order_product_details.item_id,
            item_requirements.requirement,
            Sum(purchase_order_product_details.shipment_quantity) AS ordered,
            Sum(tbl_stock_detail.Qty) AS rcvd 
            FROM
            purchase_order
            INNER JOIN purchase_order_product_details ON purchase_order.pk_id = purchase_order_product_details.po_master_id
            LEFT  JOIN item_requirements ON purchase_order_product_details.item_id = item_requirements.item_id
            LEFT JOIN tbl_stock_master ON purchase_order.pk_id = tbl_stock_master.shipment_id
            LEFT JOIN tbl_stock_detail ON tbl_stock_master.PkStockID = tbl_stock_detail.fkStockID
            WHERE
            purchase_order.wh_id = '".$_SESSION['user_warehouse']."' AND
            purchase_order_product_details.item_id = '".$_REQUEST['product']."'

";
$res = mysql_query($q_dash) or die("Error po data".$q_dash);
$graph_1 = mysql_fetch_assoc($res);


//title
$title = "Shipments";
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

if(!isset($graph_1['requirement']))$graph_1['requirement']=0;
if(!isset($graph_1['rcvd']))$graph_1['rcvd']=0;
//echo '<pre>';
//print_r($graph_1);

if(!isset($graph_1['requirement']))$graph_1['requirement']=0;
if(!isset($graph_1['rcvd']))$graph_1['rcvd']=0;
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
                                            <div class="col-md-3 hide">
                                                <div class="form-group">
                                                    <label class="control-label">Date From</label>
                                                    <input type="text" class="form-control input-medium" name="date_from" readonly id="date_from" value="<?php echo $date_from; ?>"/>
                                                </div>
                                            </div>
                                            <div class="col-md-3 hide">
                                                <div class="form-group">
                                                    <label class="control-label">Date To</label>
                                                    <input type="text" class="form-control input-medium" name="date_to"  readonly="" id="date_to" value="<?php echo $date_to; ?>"/>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="control-group">
                                                    <label class="control-label" for="product">Product</label>
                                                    <div class="controls">
                                                        <select name="product" id="product" class="input-large  select2me">
                                                            <option value="">Select</option>
                                                            <?php
                                                            if (mysql_num_rows($items) > 0) {
                                                                while ($row = mysql_fetch_object($items)) {
                                                                    ?>
                                                                    <option value="<?php echo $row->itm_id; ?>" <?php if ($product == $row->itm_id) { ?> selected="" <?php } ?>><?php echo $row->itm_name; ?></option>
                                                                    <?php
                                                                }
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2 hide">
                                                <div class="control-group">
                                                    <label class="control-label">Status</label>
                                                    <div class="controls">
                                                        <select name="status" id="status" class="form-control input-sm">
                                                            <option value="">Select</option>
                                                            <option value="Pre Shipment" <?php if ($searchby == 'Pre Shipment') { ?> selected <?php } ?>>Pre Shipment</option>
                                                            <option value="Tender" <?php if ($searchby == 'Tender') { ?> selected <?php } ?>>Tender</option>
                                                            <option value="PO" <?php if ($searchby == 'PO') { ?> selected <?php } ?>>Purchase Order</option>
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
                        <div class="widget" data-toggle="collapse-widget"> 
                            <div class="widget-head">
                                <h4 class="heading">Procurement Stock Analysis</h4>
                            </div>
                            <div class="widget-body"> 
                                <div class="row">
                                    <div class="col-md-12">
                                        <div id="" class="col-md-7">
                                                <div class="col-md-12">
                                                    <button class="btn col-md-3 btn-circle green-meadow my_tab_btn" onclick="openCity('one', this)">Bar Comparison</button>
                                                    <button class="btn col-md-3 btn-circle default my_tab_btn" onclick="openCity('two', this)">Donut </button>
                                                    <button class="btn col-md-3 btn-circle default my_tab_btn" onclick="openCity('three', this)">Expanded Donut</button>
                                                    <button class="btn col-md-3 btn-circle default my_tab_btn" onclick="openCity('four', this)">Funnel Chart</button>
                                                </div>

                                                <div id="one" class="city col-md-12">
                                                    <h2>Bar Comparison</h2>

                                                        <div id="myDiv1" style="width:600px;height:450px;"></div> 
                                                </div>
                                                <div id="two" class="city col-md-12" style="display:none">
                                                    <h2>Donut Comparison</h2>
                                                    <div id="myDiv2" style="width:600px;height:450px;"></div> 
                                                </div>
                                                <div id="three" class="city col-md-12" style="display:none">
                                                    <h2>Expanded Pie</h2>
                                                    <div id="myDiv3" style="width:600px;height:550px;"></div> 
                                                </div>
                                                <div id="four" class="city col-md-12" style="display:none">
                                                    <h2>Funnel Comparison</h2>
                                                    <div id="myDiv4" style="width:600px;height:650px;"></div> 
                                                </div>
                                        
                                      </div> 
                                       <div id="disp_div1" class="col-md-5">
                                           <p></p>
                                           <p></p>
                                           <p></p>
                                           <p></p>
                                           <p>
                                               <span class="note note-info">
                                                   Please click on graph to view details.
                                               </span></p>
                                       </div> 


                                        
                                       

                                    </div>
                                </div>
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
    <script src="<?php echo PUBLIC_URL; ?>js/plotly.min.js"></script>
    <script type="text/javascript" src="../../public/assets/global/plugins/select2/select2.min.js"></script>

   
                        
<script>
    function openCity(cityName,ele) {
            $('.my_tab_btn').removeClass('green-meadow');
            $(ele).addClass('green-meadow');
            var i;
            var x = document.getElementsByClassName("city");
            for (i = 0; i < x.length; i++) {
                x[i].style.display = "none";
            }
            document.getElementById(cityName).style.display = "block";
     }

    $("#receive_date").datepicker({
        dateFormat: 'yyyy-mm-dd',
        constrainInput: false,
        changeMonth: true,
        changeYear: true
    });
<?php

        $chart_config = "";

        $chart_config .= " var data = [{
                    values: [".$graph_1['requirement'].", ".$graph_1['ordered'].", ".$graph_1['rcvd'].",".$graph_1['ordered']."-".$graph_1['rcvd']."],
                    labels: ['Required','Ordered','Received','Remaining'],
                    type: 'pie',
                    hoverinfo: 'label+percent+name',
                    hole: .4,
                    name : 'abc'
                    }];

                    ";

    ?>
    <?php echo $chart_config;?>
    Plotly.newPlot('myDiv2', data, {displaylogo: false}, {displayModeBar: true});



    var data = [{
        type: "sunburst",
        labels: ["Quantity","Required", "Ordered","Shortfall", "Received", "Remaining"],
        parents: ["", "Quantity", "Required","Required", "Ordered", "Ordered"],
        values:  ['',<?php echo $graph_1['requirement'].", ".$graph_1['ordered'].", ".($graph_1['requirement']-$graph_1['ordered']).", ".$graph_1['rcvd'].",".($graph_1['ordered']."-".$graph_1['rcvd'])?>],
        outsidetextfont: {size: 20, color: "#377eb8"},
        leaf: {opacity: 0.4},
        marker: {line: {width: 2}} 
        }];

        var layout = {
        margin: {l: 0, r: 0, b: 0, t: 0},
        width: 500,
        height: 500
        };


        Plotly.newPlot('myDiv3', data, layout, {displaylogo: false}, {displayModeBar: true});


        var trace1 = {
            x: ['Required'],
            y: [<?php echo $graph_1['requirement']?>],
            name: 'Required',
            type: 'bar'
            };

        var trace2 = {
            x: ['Ordered'],
            y: [<?php echo $graph_1['ordered']?>],
            name: 'Ordered',
            type: 'bar'
            };
        var trace3 = {
            x: ['Received'],
            y: [<?php echo $graph_1['rcvd']?>],
            name: 'Received',
            type: 'bar'
            };
        var trace4 = {
            x: ['Remaining'],
            y: [<?php echo $graph_1['ordered'] - $graph_1['rcvd']?>],
            name: 'Remaining',
            type: 'bar'
            };

            var data = [trace1, trace2, trace3, trace4];

            var layout = {barmode: 'group'};

            Plotly.newPlot('myDiv1', data, layout, {displaylogo: false}, {displayModeBar: true});



    var gd = document.getElementById('myDiv4');
    var data = [{type: 'funnel', 
            y: ["Required", "Ordered", "Received", "Remaining"], 
            x: [<?php echo $graph_1['requirement'].", ".$graph_1['ordered'].", ".$graph_1['rcvd'].",".($graph_1['ordered']."-".$graph_1['rcvd'])?>], 
            hoverinfo: 'x+percent previous+percent initial'}
    ];

    var layout = {margin: {l: 150}, width:600, height: 500}

    Plotly.newPlot('myDiv4', data, layout, {displaylogo: false}, {displayModeBar: true});



    myDiv1.on('plotly_click', function(data){
        console.log('Clicked on : '+data['points'][0]['label']+' : '+data['points'][0]['value']);
        var req_type = data['points'][0]['label'];
        var wh_id = <?php echo $_SESSION['user_warehouse']; ?>;
        var item_id = <?php echo $_REQUEST['product']; ?>;
        fetch_detail(wh_id,req_type,item_id);
    });
    myDiv2.on('plotly_click', function(data){
        console.log('Clicked on : '+data['points'][0]['label']+' : '+data['points'][0]['value']);
        var req_type = data['points'][0]['label'];
        var wh_id = <?php echo $_SESSION['user_warehouse']; ?>;
        var item_id = <?php echo $_REQUEST['product']; ?>;
        fetch_detail(wh_id,req_type,item_id);
    });
    myDiv3.on('plotly_click', function(data){
        console.log('Clicked on : '+data['points'][0]['label']+' : '+data['points'][0]['value']);
        var req_type = data['points'][0]['label'];
        var wh_id = <?php echo $_SESSION['user_warehouse']; ?>;
        var item_id = <?php echo $_REQUEST['product']; ?>;
        fetch_detail(wh_id,req_type,item_id);
    });
    myDiv4.on('plotly_click', function(data){
//        console.log('Clicked on : '+data['points'][0]['label']+' : '+data['points'][0]['value']);
        console.log(data);
        console.table('Clicked on : '+data['points'][0]['x']+' : '+data['points'][0]['y']);
        var req_type = data['points'][0]['y'];
        var wh_id = <?php echo $_SESSION['user_warehouse']; ?>;
        var item_id = <?php echo $_REQUEST['product']; ?>;
        fetch_detail(wh_id,req_type,item_id);
    });
    
    function fetch_detail(wh_id,req_type,item_id){
        $.ajax({
            url: 'ajax_get_shipment_detail.php',
            type: 'GET',
            data: {wh_id: wh_id, req_type : req_type,item_id : item_id},
            success: function (data) {
                $('#disp_div1').html('');
                $('#disp_div1').hide();
                $('#disp_div1').html(data);
                $('#disp_div1').slideDown(1000);
            }
        })
    }
</script>
</body>
</html>