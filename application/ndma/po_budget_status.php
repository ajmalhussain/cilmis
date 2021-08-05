<?php
//echo '<pre>';print_r($_REQUEST);exit;
include("../includes/classes/AllClasses.php");
include(PUBLIC_PATH . "html/header.php");

$wh_id = $_SESSION['user_warehouse'];


if(!empty($_REQUEST['budget']) && !empty($_REQUEST['update'])){
    $bud = str_replace(',', '', $_REQUEST['budget']);
    $qi = "UPDATE `wh_budget` SET `budget_allocated`='".$bud."' WHERE wh_id ='".$wh_id."' ";
    mysql_query($qi);
}



$sCriteria = array();
$po_data = array();

$qb= "SELECT
wh_budget.pk_id,
wh_budget.wh_id,
wh_budget.fiscal_year,
wh_budget.budget_allocated
FROM
wh_budget
WHERE
wh_budget.wh_id = '".$wh_id."'
";
$res = mysql_query($qb);
$row = mysql_fetch_assoc($res);
$po_data['total_budget'] = $row['budget_allocated'];


//$po_data['total_budget']=500000000000000;
$po_data['total_amount_spent']=0;
$po_data['remaining_amount']=0;

$q_dash = "SELECT
                purchase_order_product_details.item_id,
                purchase_order_product_details.shipment_quantity AS ordered,
                tbl_stock_detail.Qty AS rcvd,
                purchase_order_product_details.unit_price
            FROM
                purchase_order
INNER JOIN purchase_order_product_details ON purchase_order.pk_id = purchase_order_product_details.po_master_id
            LEFT  JOIN item_requirements ON purchase_order_product_details.item_id = item_requirements.item_id
            LEFT JOIN tbl_stock_master ON purchase_order.pk_id = tbl_stock_master.shipment_id
            LEFT JOIN tbl_stock_detail ON tbl_stock_master.PkStockID = tbl_stock_detail.fkStockID
            WHERE
                purchase_order.wh_id = '".$wh_id."'
                    

";
$res = mysql_query($q_dash) or die("Error po data".$res);

while($row = mysql_fetch_assoc($res)){
    $this_price = 0;
    if(!empty($row['ordered']) && $row['ordered']>0 && !empty($row['unit_price']) && $row['unit_price']> 0){
        $this_price = $row['ordered']*$row['unit_price'];
    }
    
    $po_data['total_amount_spent'] +=$this_price; 
}
$po_data['remaining_amount'] = $po_data['total_budget'] - $po_data['total_amount_spent'];
$title = "Shipments";

//$po_data['total_amount_spent']=number_format($po_data['total_amount_spent']);
//$po_data['remaining_amount']=number_format($po_data['remaining_amount']);


//$po_data['total_budget']=25000;
//$po_data['total_amount_spent']=21000;
//$po_data['remaining_amount']=4000;
//echo '<pre>';
//print_r($po_data);
?>
</head>
<body class="page-header-fixed page-quick-sidebar-over-content">
    <div class="page-container">
        <?php
        include PUBLIC_PATH . "html/top.php";
        include PUBLIC_PATH . "html/top_im.php";
        ?>
        <div class="page-content-wrapper">
            <div class="page-content"> 
                <div class="row">
                    <div class="col-md-12">
                       
                        <div  class="widget" data-toggle="collapse-widget">
                            <div class="widget-head">
                                <h3 class="heading">Update Budget</h3>
                            </div>
                            <div id="shipment_filter_div" class="widget-body">
                                <form method="POST" name="batch_search" action="" >
                                    <!-- Row -->
                                    <div class="row">
                                        <div class="col-md-12">

                                            <!-- Group -->
                                            <div class="col-md-3 ">
                                                <div class="form-group">
                                                    <label class="control-label">Total Budget</label>
                                                    <input type="text" class="form-control input-medium" name="budget" id="budget" value="<?php echo number_format($po_data['total_budget']); ?>"/>
                                                </div>
                                            </div>
                                            <div class="col-md-1 right">
                                                <div class="form-group">
                                                    <label class="control-label">&nbsp;</label>
                                                    <div class="form-group">
                                                        <button type="submit" name="update" value="search" class="btn btn-primary">Update</button>
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
                                <h4 class="heading">Budget Status</h4>
                            </div>
                            <div class="widget-body"> 
                                <div class="row">
                                    <div class="col-md-12">
                                        <div id="" class="col-md-7">
                                                <div class="col-md-12 hidex">                                                   
                                                    <button class="btn col-md-6 btn-circle green-meadow  my_tab_btn" onclick="openTab('two', this)">Spent Percentage </button>

                                                    <button class="btn col-md-6 btn-circle default my_tab_btn" onclick="openTab('one', this)">Total vs Spent vs Remaining</button>
                                                    <button class="btn col-md-3 btn-circle default my_tab_btn hide" onclick="openTab('three', this)">Expanded Donut</button>
                                                    <button class="btn col-md-3 btn-circle default my_tab_btn hide" onclick="openTab('four', this)">Funnel Chart</button>
                                                </div>

                                                <div id="one" class="city col-md-12" style="display:none">
                                                    <h2>Spent Percentage</h2>

                                                        <div id="myDiv1" style="width:600px;height:450px;"></div> 
                                                </div>
                                                <div id="two" class="city col-md-12">
                                                    <h2>Total vs Spent vs Remaining</h2>
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
                        
<script>
    
    function openTab(cityName,ele) {
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
                    values: [".$po_data['total_amount_spent'].", ".$po_data['remaining_amount']."],
                    labels: ['Amount Spent','Amount Remaining' ],
                    type: 'pie',
                    hoverinfo: 'label+percent+name',
                    hole: .4,
                    name : 'Budget'
                    }];

                    ";

    ?>
    <?php echo $chart_config;?>
    Plotly.newPlot('myDiv2', data, {displaylogo: false}, {displayModeBar: true});



    var data = [{
        type: "sunburst",
        labels: ["Budget", "Total Budget", "Amount Spent","Amount Remaining"],
        parents: ["", "Budget", "Total Budget","Total Budget"],
        values:  ['',<?php echo $po_data['total_budget'].", ".$po_data['total_amount_spent'].", ".($po_data['remaining_amount'])?>],
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
            x: ['Total Budget'],
            y: [<?php echo $po_data['total_budget']?>],
            name: 'Total Budget',
            type: 'bar'
            };

        var trace2 = {
            x: ['Amount Spent'],
            y: [<?php echo $po_data['total_amount_spent']?>],
            name: 'Amount Spent',
            type: 'bar'
            };
        var trace3 = {
            x: ['Amount Remaining'],
            y: [<?php echo $po_data['remaining_amount']?>],
            name: 'Amount Remaining',
            type: 'bar'
            };

            var data = [trace1, trace2, trace3];

            var layout = {barmode: 'group'};

            Plotly.newPlot('myDiv1', data, layout, {displaylogo: false}, {displayModeBar: true});



    var gd = document.getElementById('myDiv4');
    var data = [{type: 'funnel', 
            y: ["Total Budget", "Amount Spent", "Amount Remaining"], 
            x: [<?php echo $po_data['total_budget'].", ".$po_data['total_amount_spent'].", ".$po_data['remaining_amount']?>], 
            hoverinfo: 'x+percent previous+percent initial'}
    ];

    var layout = {margin: {l: 150}, width:600, height: 500}

    Plotly.newPlot('myDiv4', data, layout, {displaylogo: false}, {displayModeBar: true});



    myDiv1.on('plotly_click', function(data){
        console.log('Clicked on : '+data['points'][0]['label']+' : '+data['points'][0]['value']);
        var req_type = data['points'][0]['label'];
        var wh_id = <?php echo $_SESSION['user_warehouse']; ?>;
        fetch_detail(wh_id,req_type);
    });
    myDiv2.on('plotly_click', function(data){
        console.log('Clicked on : '+data['points'][0]['label']+' : '+data['points'][0]['value']);
        var req_type = data['points'][0]['label'];
        var wh_id = <?php echo $_SESSION['user_warehouse']; ?>;
        fetch_detail(wh_id,req_type);
    });
    myDiv3.on('plotly_click', function(data){
        console.log('Clicked on : '+data['points'][0]['label']+' : '+data['points'][0]['value']);
        var req_type = data['points'][0]['label'];
        var wh_id = <?php echo $_SESSION['user_warehouse']; ?>;
        fetch_detail(wh_id,req_type);
    });
    myDiv4.on('plotly_click', function(data){
//        console.log('Clicked on : '+data['points'][0]['label']+' : '+data['points'][0]['value']);
        console.log(data);
        console.table('Clicked on : '+data['points'][0]['x']+' : '+data['points'][0]['y']);
        var req_type = data['points'][0]['y'];
        var wh_id = <?php echo $_SESSION['user_warehouse']; ?>;
        fetch_detail(wh_id,req_type);
    });
    
    function fetch_detail(wh_id,req_type){
        
        $('#disp_div1').html('');
        $('#disp_div1').html(' Loading ...');
        $.ajax({
            url: 'ajax_get_budget_detail.php',
            type: 'GET',
            data: {wh_id: wh_id, req_type : req_type},
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