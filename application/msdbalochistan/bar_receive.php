<?php
include("../includes/classes/AllClasses.php");
include(PUBLIC_PATH . "html/header.php");
include "../includes/styling/dynamic_theme_color.php";
$TranNo = '';
$TranRef = '';
$from_id = 0;
$productID = 0;
$unit_price = 0;
$vvmtype = 0;
$vvmstage = 0;
$stock_id = 0;
$manufacturer = '';
$userid = $_SESSION['user_id'];
$wh_id = $_SESSION['user_warehouse'];
$wh_from = '';
$PkStockID = '';
if (isset($_GET['PkStockID'])) {
    $PkStockID = base64_decode($_GET['PkStockID']);
    $tempstocksIssue = $objStockMaster->GetTempStockRUpdate($PkStockID);
} else {
    $tempstocksIssue = $objStockMaster->GetTempStockReceive($userid, $wh_id, 1);
}
if (!empty($tempstocksIssue) && mysql_num_rows($tempstocksIssue) > 0) {
    $result = mysql_fetch_object($tempstocksIssue);
    $stock_id = $result->PkStockID;
    $from_id = $result->WHIDFrom;
    $wh_from = $objwarehouse->GetWHByWHId($from_id);
    $TranDate = $result->TranDate;
    $TranNo = $result->TranNo;
    $TranRef = $result->TranRef;
    $tempstocksIssueDet = $objStockMaster->GetLastInseredTempStocksReceiveList($userid, $wh_id, 1);
    if (!empty($tempstocksIssueDet)) {
        $result1 = mysql_fetch_object($tempstocksIssueDet);
        if (!empty($result1)) {
            $productID = $result1->itm_id;
            $unit_price = $result1->unit_price;
            $manufacturer = $result1->manufacturer;
        }
    }
}
if (!empty($productID)) {
    
}
//Get Temp Stocks Receive List
$tempstocks = $objStockMaster->GetTempStocksReceiveList($userid, $wh_id, 1);
if (!empty($tempstocks) && mysql_num_rows($tempstocks) > 0) {
    
} else {
    $objStockMaster->PkStockID = $stock_id;
    $objStockMaster->delete();
}
$warehouses = $warehouses1 = $objwarehouse->get_funding_sources_of_province($_SESSION['user_province1']);
$stk = $_SESSION['user_stakeholder1'];
$items = $objManageItem->GetAllProduct_of_stk($stk);
$units = $objItemUnits->GetAllItemUnits();
?>
<link rel="stylesheet" type="text/css" href="../../public/assets/global/plugins/select2/select2.css"/>

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
                      
                       
                        <div class="panel panel-primary">
                            <div class="panel-heading">
                                <h3 class="panel-title"> <i class="fa fa-shopping-cart"></i> Automated Barcode Stock Receiver</h3>
                                <ul class="list-inline panel-actions">
                                    <li><a href="#" id="panel-fullscreen" role="button" title="Toggle fullscreen"><i class="glyphicon glyphicon-resize-full"></i></a></li>
                                </ul>
                            </div>
                            <div class="panel-body form">
                            
                                <form method="POST" name="new_receive" id="new_receive" action="bar_receive_action.php" >
                                    <!-- Row -->
                                    <div class="row">
                                        <div class="col-md-12">

                                             <div class="col-md-4"> 
                                                <!-- Group Receive No-->
                                                <div class="control-group">
                                                    <label class="control-label" for="receive_no"> Receipt No (Auto Generated) </label>
                                                    <div class="controls"> 
                                                        <div class="input-group">
                                                            <span  class="input-group-addon input-circle-left  bg-blue-madison">
                                                                <i class="fa fa-th-list"></i>
                                                            </span>
                                                            <input class="form-control input-circle-right" id="receive_no" value="<?php echo $TranNo; ?>" name="receive_no" type="text" readonly />
                                                            <input type="hidden"  id="source_name" name="source_name" value="<?php echo $wh_from; ?> " />
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- // Group END Receive No-->
                                            <div class="col-md-4">
                                                <div class="control-group">
                                                    <label class="control-label" for="receive_ref"> Reference No. <span class="red">*</span> </label>
                                                    <div class="controls">
                                                         <div class="input-group">
                                                            <span  class="input-group-addon input-circle-left  bg-blue-madison">
                                                                <i class="fa fa-tag"></i>
                                                            </span>
                                                            <input class="form-control input-circle-right" required id="receive_ref" name="receive_ref" type="text" value="<?php echo $TranRef; ?>" <?php if (!empty($TranRef)) { ?>disabled="" <?php } ?>/>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="control-group">
                                                    <label class="control-label" for="receive_date"> Received On (Date/Time) </label>
                                                    <div class="controls"> 
                                                            <div class="input-group">
                                                            <span  class="input-group-addon input-circle-left  bg-blue-madison">
                                                                <i class="fa fa-clock-o"></i>
                                                            </span>
                                                            <input class="form-control input-circle-right input-medium" <?php
                                                            if (!empty($TranDate)) {
                                                                echo 'disabled=""';
                                                            } else {
                                                                echo 'readonly="readonly" style="background:#FFF"';
                                                            }
                                                            ?> id="receive_date" name="receive_date" type="text" value="<?php echo (!empty($TranDate)) ? date("d/m/y h:i A", strtotime($TranDate)) : date("d/m/Y h:i A"); ?>" required />
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">&nbsp;</div>
                                        </div>
                                    </div>
                                    <div class="row">                                        
                                        <div class="col-md-12"> 

                                           
                                            
                                            
                                            <div class="col-md-4">
                                                <label class="control-label" for="receive_from"> Received From (Funded By)<span class="red">*</span> </label>
                                                <div class="controls">
                                                    <div class="input-group">
                                                        <span class="input-group-addon     bg-blue-madison">
                                                        <i class="fa fa-money"></i>
                                                    </span>
                                                    <select name="receive_from" id="receive_from" required="true" class="form-control  input-mediumx select2mex" <?php if (!empty($from_id) && !empty($TranNo)) { ?>disabled="" <?php } ?>>
                                                        <option value="">Select</option>
                                                        <?php
                                                        //check if result exists
                                                        if (mysql_num_rows($warehouses) > 0) {
                                                            //fetch result
                                                            while ($row = mysql_fetch_object($warehouses)) {
                                                                //populate receive_from combo
                                                                if ($_SESSION['user_stakeholder'] == '145') {
                                                                    if ($row->wh_id != '33677' && $row->wh_id != '33678' && $row->wh_id != '33680' && $row->wh_id != '20641' && $row->wh_id != '9079')
                                                                        continue;
                                                                }
                                                                if($row->wh_id=='72673'){
                                                                    $sel="selected='selected'";
                                                                }
                                                                else{
                                                                    $sel='';
                                                                }
                                                                ?>
                                                                <option value="<?php echo $row->wh_id; ?>" <?php if ($from_id == $row->wh_id) { ?> selected="" <?php } echo $sel; ?> > <?php echo $row->wh_name; ?> </option>
                                                                <?php
                                                            }
                                                        }
                                                        ?>
                                                    </select>
                                                    <?php if (!empty($from_id) && !empty($TranNo)) { ?>
                                                        <input type="hidden" name="receive_from" id="receive_from" value="<?php echo $from_id; ?>" />
                                                    <?php } ?>
                                                </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="control-label">
                                                    Physical Inspection <span class="red">*</span>
                                                </label>
                                                <div class="controls">
                                                    <div class="input-group">
                                                        <span class="input-group-addon     bg-blue-madison">
                                                            <i class="fa fa-users"></i>
                                                        </span>

                                                        <select style="font-family: 'FontAwesome', 'sans-serif';" name="physical_inspection" id="physical_inspection" required class="bs-select form-control">
                                                            <option value="" >Select</option>
                                                            <option value="2" selected="selected">&#xf00d; NA</option>
                                                            <option value="0" >&#xf110; Inprocess</option>
                                                            <option value="1">&#xf00c; Completed</option>
                                                    </select>                                            
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="control-label" for="dtl">
                                                    DTL <span class="red">*</span>
                                                </label>
                                                <div class="controls">

                                                    <div class="input-group">
                                                            <span class="input-group-addon     bg-blue-madison">
                                                            <i class="fa fa-flask"></i>
                                                        </span>
                                                        <select style="font-family: 'FontAwesome', 'sans-serif';"  name="dtl" id="dtl" class="form-control" required >
                                                            <option value="" >Select</option>
                                                            <option value="2" selected="selected">&#xf00d; NA</option>
                                                            <option value="0" >&#xf110; Inprocess</option>
                                                            <option value="1">&#xf00c; Completed</option>
                                                        </select>

                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3 hide">
                                                <label class="control-label" for="distribution_plan">
                                                    Distribution Plan                                            </label> <span class="red">*</span>
                                                <div class="controls">

                                                    <select name="distribution_plan" id="distribution_plan" class="form-control input-circle-right" >
                                                        <option value="" selected="selected">Select</option>
                                                        <option value="2">NA</option>
                                                        <option value="0">Not Received</option>
                                                        <option value="1">Received</option>
                                                    </select>                                            
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="col-md-6"> 
                                                <!-- Group Receive No-->
                                                <div class="control-group">
                                                    <label class="control-label" for="item_code">Scan Barcode <span class="red">*</span> <i style="padding-left:20px; transform: scale(4,1); " class="fa fa-barcode font-blue-chambray"></i></label>
                                                    <div class="controls">
                                                        <div class="input-group">
                                                        <span style="min-width:70px" class="input-group-addon input-circle-left bg-blue-madison">
                                                            <i style="padding-left:1px; transform: scale(3,1); " class="fa fa-barcode"></i>
                                                        </span>
                                                        <input class="form-control input-circle-right" tabindex="1" id="item_code" name="item_code" type="text" />
                                                    </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="control-label" for="qty"> Quantity <span class="red">*</span> </label>
                                                <div class="controls">
                                                    <div class="input-group">
                                                        <span  class="input-group-addon input-circle-left  bg-blue-madison">
                                                            <i class="fa fa-sort-numeric-asc"></i>
                                                        </span>
                                                    <input type="text" tabindex="2" class="form-control input-circle-right input-medium num" name="qty" id="qty" autocomplete="off" />
                                                    <span id="product-unit"> </span> <span id="product-unit1" style="display:none;"> </span> </div>
                                                </div>
                                            </div>
                                             <div class="col-md-3">
                                                <label class="control-label" for="firstname"> &nbsp; </label>
                                                <div class="controls right">
                                                    <button type="submit" class="btn  " id="add_receive" tabindex="3" style="display:none;"> Save Entry </button>
                                                    
                                                    <input type="hidden" name="trans_no" id="trans_no" value="<?php echo $TranNo; ?>" />
                                                    <input type="hidden" name="stock_id" id="stock_id" value="<?php echo $stock_id; ?>" />

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="well margin-top-10 no-margin no-border" style="height:80px;">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="col-md-4" id="p_1">
                                                    <label class="control-label" ><h4 class="font-green-seagreen ">Product:</h4></label>
                                                    <div class="controls" id="product_div">
                                                        ---
                                                    </div>
                                                </div>

                                                <div class="col-md-4" id="p_2">
                                                        <label class="control-label" ><h4 class="font-green-seagreen " >Manufacturer:</h4></label>
                                                        <div class="controls" id="manufacturer_div">
                                                        ---
                                                        </div>
                                                </div>
                                         
                                                <div class="col-md-2" id="p_3">
                                                    <label class="control-label"  ><h4 class="font-green-seagreen ">Batch No:</h4></label>
                                                    <div class="controls">
                                                        <input id="batch" name="batch" type="hidden" />
                                                        <label id="batch_no" style="">
                                                        ---
                                                        </label>
                                                    </div>
                                                </div> 
                                                <div class="col-md-2" id="expiry_div">
                                                    <label class="control-label"  ><h4 class="font-green-seagreen ">Expiry date:</h4></label>
                                                    <div class="controls">
                                                        <label id="expiry_date_label" style="">
                                                        ---
                                                        </label>
                                                        <input id="expiry_date" name="expiry_date" type="hidden" />
                                                    </div>
                                                </div> 
                                            </div>
                                        </div>
                                    </div>
                                     
                                </form>
                <?php if (!empty($tempstocks) && mysql_num_rows($tempstocks) > 0) { ?>
                                    <table class="table table-striped table-bordered table-condensed" id="myTablex">
                                        <!-- Table heading -->
                                        <thead>
                                            <tr  class="bg-blue-hoki" style=" ">
<!--                                                <th> Receiving Time </th>-->
                                                <th> Product </th>
                                                <th> Manufacturer </th>
                                                <th> Unit </th>
                                                <th> Receive From </th>
                                                <th class="span2"> Quantity </th>
                                                <th> Cartons </th>
                                                <th class="span2"> Batch </th>
                                                <th nowrap> Expiry Date </th>
                                                <th width="50"> Action </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $i = 1;
                                            $checksumVials = array();
                                            $checksumDoses = array();
                                            //fetch result
                                            while ($row = mysql_fetch_object($tempstocks)) {
                                                // Checksum
                                                ?>
                                                <tr class="gradeX">
<!--                                                    <td nowrap><?php echo date("d/m/y h:i A", strtotime($row->TranDate)); ?></td>-->
                                                    <td><?php echo $row->itm_name; ?></td>
                                                    <td>
                                                        <?php
                                                        if (!empty($row->manufacturer)) {
                                                            $getManufacturer = mysql_query("SELECT
                                                                                            CONCAT(stakeholder.stkname, ' | ', stakeholder_item.brand_name) AS stkname
                                                                                    FROM
                                                                                            stakeholder_item
                                                                                    INNER JOIN stakeholder ON stakeholder_item.stkid = stakeholder.stkid
                                                                                    WHERE
                                                                            stakeholder_item.stk_id = $row->manufacturer") or die("err  manufacturer");
                                                            $manufacturerRow = mysql_fetch_assoc($getManufacturer);
                                                            echo $manufacturerRow['stkname'];
                                                        }
                                                        ?>
                                                    </td>
                                                    <td><?php echo $row->UnitType; ?></td>
                                                    <td><?php echo $row->wh_name; ?></td>
                                                    <td class="right editableSingle Qty id<?php echo $row->PkDetailID; ?>"><?php echo number_format(abs($row->Qty)); ?></td>
                                                    <td class="right"><?php echo number_format(abs($row->Qty) / $row->qty_carton); ?></td>
                                                    <td class="editableSingle Batch id<?php echo $row->PkDetailID; ?>"><?php echo $row->batch_no; ?></td>
                                                    <td><?php echo date("d/m/y", strtotime($row->batch_expiry)); ?></td>
                                                    <td class="center"><span data-toggle="notyfy" id="<?php echo $row->PkDetailID; ?>" data-type="confirm" data-layout="top"><img class="cursor" src="<?php echo PUBLIC_URL; ?>images/cross.gif" /></span></td>
                                                </tr>
                                                <?php
                                                $i++;
                                            }
                                            ?>
                                            <!-- // Table row END -->
                                        </tbody>
                                        <!-- // Table body END -->
                                    </table>
                                
                                
                                <div style="margin-top:10px;">
                                    <div class="-body right">
                                        <form name="receive_stock" id="receive_stock" action="bar_receive_action.php" method="POST">
                                            <button  type="submit" class="btn green" onClick="return confirm('Are you sure you want to save data and generate the voucher?');"> Save Voucher</button>
                                            <button id="print_vaccine_placement" type="button" class="btn btn-warning"> Print </button>
                                            <input type="hidden" name="stockid" id="stockid" value="<?php echo $stock_id; ?>" />
                                        </form>
                                    </div>
                                </div>
                                
                                
                                <!-- // Panel END -->
                                </div>
                            </div>
                        </div>
                    </div>
                <?php }
                ?>
            </div>
        </div>
        <!-- // Content END --> 

    </div>
    <?php
    //include footer
    include PUBLIC_PATH . "/html/footer.php";
    ?>

<!--    <script src="<?php echo PUBLIC_URL; ?>js/dataentry/newreceive.js"></script> -->
    <script src="<?php echo PUBLIC_URL; ?>js/dataentry/jquery.mask.min.js"></script> 
    <script src="<?php echo PUBLIC_URL; ?>js/jquery.inlineEdit.js"></script>
    <script src="<?php echo PUBLIC_URL; ?>js/barcode/barcode_splitter_receive.js"></script> 
    <?php
    if (!empty($_SESSION['success'])) {
        if ($_SESSION['success'] == 1) {
            //display message
            $text = 'Data has been saved successfully';
        }
        if ($_SESSION['success'] == 2) {
            //display message
            $text = 'Data has been deleted successfully';
        }
        ?>
        <script>
            
                                    var self = $('[data-toggle="notyfy"]');
                                    notyfy({
                                        force: true,
                                        text: '<?php echo $text; ?>',
                                        type: 'success',
                                        layout: self.data('layout')
                                    });
        </script>
        <?php
        unset($_SESSION['success']);
    }
   
    ?>
    <!-- END FOOTER --> 
<script>
    $(function(){
        $(document).keypress(function (e) {
        if (e.which == 13) {
            $('#add_receive').trigger('click');
        }
    });
    $("#item_code").focus();
    $("#item_code").change(function () {
        string_splitter($(this).val());
    });     
    
    
    


    var notification = [];
    notification['confirm'] = 'You have clicked to Delete. Are you sure you want to Delete?';
    $('[data-toggle="notyfy"]').click(function() {
        var self = $(this);
		$.notyfy.closeAll();
        notyfy({
            text: notification[self.data('type')],
            type: self.data('type'),
            dismissQueue: true,
            layout: self.data('layout'),
            buttons: (self.data('type') != 'confirm') ? false : [
                {
                    addClass: 'btn btn-success btn-medium btn-icon glyphicons ok_2',
                    text: '<i></i> Ok',
                    onClick: function($notyfy) {
                        var id = self.attr("id");
                        $notyfy.close();
                        window.location.href = 'bar_delete_receive.php?id=' + id;
                    }
                },
                {
                    addClass: 'btn btn-danger btn-medium btn-icon glyphicons remove_2',
                    text: '<i></i> Cancel',
                    onClick: function($notyfy) {
                        $notyfy.close();
                        /*notyfy({
                            force: true,
                            text: '<strong>You clicked "Cancel" button<strong>',
                            type: 'error',
                            layout: self.data('layout')
                        });*/
                    }
                }
            ]
        });
        return false;
    });
    $('#qty').priceFormat({
        prefix: '',
        thousandsSeparator: ',',
        suffix: '',
        centsLimit: 0,
        limit: 10
    });
    $("#qty").on('keyup', function(e) {
        var dollar_rate = $("#dollar_rate").val();
        var unit_price = $("#unit_price").val();
        var unit_price_pkr = $("#unit_price_pkr").html();
        var qty = $(this).val();

        $("#amount_dollar").val(qty*unit_price);
        $("#amount_pkr").val(qty*unit_price_pkr);
    });
    $('#unit_price,#qty').on('keyup keypress', function(e) {
        var price = $("#unit_price").val();
        var qty = $("#qty").val();
        qty=qty.replace(/\,/g,'');
        price=price.replace(/\,/g,'');
        $("#amount").val(parseFloat(price)*parseFloat(qty));
    });
});

</script>
    <script type="text/javascript" src="../../public/assets/global/plugins/select2/select2.min.js"></script>
    <!-- END JAVASCRIPTS -->
</body>
<!-- END BODY -->
</html>