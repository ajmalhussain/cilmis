<?php
include("../includes/classes/AllClasses.php");
include(PUBLIC_PATH . "html/header.php");

$strDo = "Add";
$nstkId = 0;
$stkname = "";
$stkgroupid = 0;
$strNewGroupName = "";
$stktype = 0;
$stkorder = 0;
$newRank = 0;
$lvl_id = 0;

if (isset($_REQUEST['Do']) && !empty($_REQUEST['Do'])) {
    //getting Do
    $strDo = $_REQUEST['Do'];
}

if (isset($_REQUEST['prod_id']) && !empty($_REQUEST['Iprod_idd'])) {
    $prod_id = $_REQUEST['prod_id'];
}

if ($strDo == "Edit") {
    $objstk->m_npkId = $nstkId;
    //Get Stakeholders By Id
    $rsEditstk = $objstk->GetStakeholdersById();
    //getting results
    if ($rsEditstk != FALSE && mysql_num_rows($rsEditstk) > 0) {
        $RowEditStk = mysql_fetch_object($rsEditstk);
        //stkname
        $stkname = $RowEditStk->stkname;
        //pk_id
        $_SESSION['pk_id'] = $nstkId;
        //stktype
        $stktype = $RowEditStk->stk_type_id;
        //stkorder
        $stkorder = $RowEditStk->stkorder;
        //lvl_id
        $lvl_id = $RowEditStk->lvl;
    }
}
//Get All Stakeholders
$rsStakeholders = $objstk->GetAllStakeholders();
//GetAllstk types
$rsstktype = $objstkType->GetAllstk_types();
//Get Ranks
$rsranks = $objstk->GetRanks();
//Get All levels
$rslvl = $objlvl->GetAlllevels();
//include file





$query_xmlw = "SELECT
            itminfo_tab.itm_id,
            itminfo_tab.itm_name,
            itminfo_tab.itm_category,
            stakeholder.stkname,
            stakeholder_item.brand_name,
            stakeholder_item.stk_id AS brand_id,
            stakeholder.stkid AS manuf_id,
            stakeholder_item.pack_length,
            stakeholder_item.pack_width,
            stakeholder_item.pack_height,
            stakeholder_item.net_capacity,
            stakeholder_item.carton_per_pallet,
            stakeholder_item.quantity_per_pack,
            stakeholder_item.gtin,
            stakeholder_item.gross_capacity,
            stakeholder_item.unit_price
FROM
	stakeholder_item
INNER JOIN itminfo_tab ON stakeholder_item.stk_item = itminfo_tab.itm_id
INNER JOIN stakeholder ON stakeholder_item.stkid = stakeholder.stkid
WHERE
	stakeholder.stk_type_id = 3
        AND itminfo_tab.itm_id = ".$_REQUEST['prod_id']."
ORDER BY
	itminfo_tab.itm_name ASC,
	stakeholder.stkname ASC";
//query result
//echo $query_xmlw;exit;
$result_xmlw = mysql_query($query_xmlw);

//Generating xml for grid
$xmlstore = "";
$xmlstore .="<tbody>";
$counter = 1;
//populate xml
while ($row_xmlw = mysql_fetch_array($result_xmlw)) {
    
    $br_name  = str_replace("\n", '', $row_xmlw['brand_name']);
    $br_name  = str_replace("\r", '', $row_xmlw['brand_name']);
    $br_name  = str_replace(PHP_EOL, '', $row_xmlw['brand_name']);
    
    
    $xmlstore .="<tr>";
    $xmlstore .="<td>" . $counter++ . "</td>";
    $xmlstore .="<td>" . $row_xmlw['itm_name'] . "</td>";
    $xmlstore .="<td>" . $row_xmlw['stkname'] . "</td>";
    $xmlstore .="<td>" . $br_name . "</td>";
    $xmlstore .="<td>" . $row_xmlw['pack_length'] . "</td>";
    $xmlstore .="<td>" . $row_xmlw['pack_width'] . "</td>";
    $xmlstore .="<td>" . $row_xmlw['pack_height'] . "</td>";
    $xmlstore .="<td>" . $row_xmlw['net_capacity'] . "</td>";
    $xmlstore .="<td>" . $row_xmlw['carton_per_pallet'] . "</td>";
    $xmlstore .="<td>" . $row_xmlw['quantity_per_pack'] . "</td>";
    $xmlstore .="<td>" . $row_xmlw['gtin'] . "</td>";
    $xmlstore .="<td>" . $row_xmlw['gross_capacity'] . "</td>";
    $xmlstore .="<td>" . (!empty($row_xmlw['unit_price'])?number_format($row_xmlw['unit_price'],2):'' ) . "</td>";
    
    $xmlstore .="<td>";
    if($row_xmlw['itm_category']==1 ){
           if($_SESSION['user_level']<3 && $_SESSION['user_stakeholder1']==1){
                $xmlstore .="<a class=\"btn btn-xs yellow edit_btn\" data-brand-id=\"".$row_xmlw['brand_id']."\">Edit</a>";
           }
    }
    else{
        $xmlstore .="<a class=\"btn btn-xs yellow edit_btn\" data-brand-id=\"".$row_xmlw['brand_id']."\">Edit</a>";
    }
    $xmlstore .="</td>";
    
    $xmlstore .="</tr>";
}

//Used for grid
$xmlstore .="</tbody>";



?>
</head>
<!-- BEGIN BODY -->
<body class="page-header-fixed page-quick-sidebar-over-content"  >
    <!-- BEGIN HEADER -->
    <div class="page-container">
        <?php include $_SESSION['menu']; ?>
        <?php include PUBLIC_PATH . "html/top_im.php"; ?>
        <div class="page-content-wrapper">
            <div class="page-content">
                <div class="row">
                    <div class="col-md-12">
                        <h3 class="page-title row-br-b-wp">Manufacturer Management</h3>
                        <div class="widget" data-toggle="collapse-widget">
                            <div class="widget-head">
                                <h3 class="heading"><?php echo $strDo; ?> Manufacturer</h3>
                            </div>
                            <div class="widget-body">
                                <form method="post" action="ManageStakeholdersAction.php" id="Managestakeholdersaction">
                                    
                                           
                                    <div class="row">
                                        <div class="col-md-2" style="margin-top: 30px; "> <a class="btn btn-primary alignvmiddle" id="add_m_p"  onclick="javascript:void(0);" data-toggle="modal"  href="#modal-manufacturer">Add New Manufacturer</a> </div>
                                     
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="widget">
                            <div class="widget-head">
                                <?php
                                //display All Stakeholders
                                ?>
                                <h3 class="heading">All Stakeholders</h3>
                            </div>
                            <div class="widget-body">
                                <table class="  table table-striped table-bordered table-condensed" width="100%" cellpadding="0" cellspacing="0" align="center">
                                        <thead>
                                                <tr>
                                                    <th>Sr No</th> 
                                                    <th>Product</th>
                                                    <th>Manufacturer</th>
                                                    <th>Brand</th>
                                                    <th>Length</th>
                                                    <th>Width</th>
                                                    <th>Height</th>
                                                    <th>Net Capacity</th>
                                                    <th>Carton/Pallet</th>
                                                    <th>Qty/Pack</th>
                                                    <th>GTIN</th>
                                                    <th>Gross Capacity</th>
                                                    <th>Unit Price</th>
                                                    <th>Edit</th>
                                                </tr>
                                            </thead>
                                        <?=$xmlstore?>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="modal-manufacturer" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content"> 
                    <!-- Modal heading -->
                    <div class="modal-header">
                        
                        <button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>
                        <div id="pro_loc">Add New Manufacturer</div>
                    </div>
                    <!-- // Modal heading END --> 

                    <!-- Modal body -->
                    <div class="modal-body">
                        <form name="addnew" id="addnew" action="add_action_manufacturer.php" method="POST">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="col-md-6">
                                        <label class="control-label">Manufacturer<span class="red">*</span></label>
                                        <div class="controls">
                                            <input required class="form-control input-medium" type="text" id="new_manufacturer" name="new_manufacturer" value=""/>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="control-label">Brand Name<span class="red">*</span></label>
                                        <div class="controls">
                                            <input required class="form-control input-medium" type="text" id="brand_name" name="brand_name" value=""/>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="col-md-3">
                                        <div class="controls">
                                            <h4 style="padding-top:30px;">Carton Dimension</h4>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="control-label">Length(cm)</label>
                                        <div class="controls">
                                            <input class="form-control input-sm dimensions positive_number" type="text" id="pack_length" name="pack_length" value=""/>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="control-label">Width(cm)</label>
                                        <div class="controls">
                                            <input class="form-control input-sm dimensions positive_number" type="text" id="pack_width" name="pack_width" value=""/>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="control-label">Height(cm)</label>
                                        <div class="controls">
                                            <input class="form-control input-sm dimensions positive_number" type="text" id="pack_height" name="pack_height" value=""/>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="col-md-3">
                                        <label class="control-label">Net Capacity</label>
                                        <div class="controls">
                                            <input required class="form-control input-sm positive_number" type="text"  id="net_capacity" name="net_capacity" value=""/>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="control-label">Gross Cap.(cm<sup>3</sup>):</label> 
                                        <div class="controls"><input class="form-control input-sm " type="text" readonly id="gross_capacity" name="gross_capacity" ></div>

                                    </div>
                                    <div class="col-md-3">
                                        <label class="control-label">Cartons / Pallet<span class="red">*</span></label>
                                        <div class="controls">
                                            <input required class="form-control input-sm positive_number" type="text" id="carton_per_pallet" name="carton_per_pallet" value=""/>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="control-label">Quantity/Pack<span class="red">*</span></label>
                                        <div class="controls">
                                            <input required class="form-control input-sm positive_number" type="text" id="quantity_per_pack" name="quantity_per_pack" value=""/>
                                        </div>
                                    </div>
                                    
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    
                                    <div class="col-md-3">
                                        <label class="control-label">GTIN</label>
                                        <div class="controls">
                                            <input required class="form-control input-sm" type="text" id="gtin" name="gtin" value=""/>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="control-label">Unit Price</label>
                                        <div class="controls">
                                            <input required class="form-control input-sm" maxlength="9" max="999999" type="text" id="unit_price" name="unit_price" value=""/>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" id="add_manufacturer" name="add_manufacturer" value="1"/>
                        </form>
                    </div>
                    <!-- // Modal body END --> 

                    <!-- Modal footer -->
                    <div class="modal-footer"> <a data-dismiss="modal" class="btn btn-default" href="#">Close</a> <a class="btn btn-primary" id="save_manufacturer" data-dismiss="modal" href="#">Save changes</a> </div>
                    <!-- // Modal footer END --> 
                </div>
            </div>
        </div>
    
    
    
    
    <div id="modal-manufacturer-edit" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content"> 
                    <!-- Modal heading -->
                    <div class="modal-header">
                        
                        <button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>
                        <div id="pro_loc">Edit Manufacturer/Brand Details</div>
                    </div>
                    <!-- // Modal heading END --> 

                    <!-- Modal body -->
                    <div class="modal-body">
                        <form name="edit_record" id="edit_record" action="add_action_manufacturer.php" method="POST">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="col-md-6">
                                        <label class="control-label">Manufacturer<span class="red">*</span></label>
                                        <div class="controls">
                                            <input readonly="readonly" class="form-control input-medium" type="text" id="new_manufacturer_edit" name="new_manufacturer_edit" value=""/>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="control-label">Brand Name<span class="red">*</span></label>
                                        <div class="controls">
                                            <input required class="form-control input-medium" type="text" id="brand_name_edit" name="brand_name_edit" value=""/>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="col-md-3">
                                        <div class="controls">
                                            <h4 style="padding-top:30px;">Carton Dimension</h4>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="control-label">Length(cm)</label>
                                        <div class="controls">
                                            <input class="form-control input-sm dimensions_edit positive_number" type="text" id="pack_length_edit" name="pack_length_edit" value=""/>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="control-label">Width(cm)</label>
                                        <div class="controls">
                                            <input class="form-control input-sm dimensions_edit positive_number" type="text" id="pack_width_edit" name="pack_width_edit" value=""/>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="control-label">Height(cm)</label>
                                        <div class="controls">
                                            <input class="form-control input-sm dimensions_edit positive_number" type="text" id="pack_height_edit" name="pack_height_edit" value=""/>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">                            


                                <div class="col-md-12">
                                    <div class="col-md-3">
                                        <label class="control-label">Net Capacity</label>
                                        <div class="controls">
                                            <input required class="form-control input-sm positive_number" type="text"  id="net_capacity_edit" name="net_capacity_edit" value=""/>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="control-label">Gross Cap.(cm<sup>3</sup>):</label> 
                                        <div class="controls"><input class="form-control input-sm " type="text" readonly id="gross_capacity_edit" name="gross_capacity_edit" ></div>

                                    </div>
                                    <div class="col-md-3">
                                        <label class="control-label">Cartons / Pallet<span class="red">*</span></label>
                                        <div class="controls">
                                            <input required class="form-control input-sm positive_number" type="text" id="carton_per_pallet_edit" name="carton_per_pallet_edit" value=""/>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="control-label">Quantity/Pack<span class="red">*</span></label>
                                        <div class="controls">
                                            <input required class="form-control input-sm positive_number" type="text" id="quantity_per_pack_edit" name="quantity_per_pack_edit" value=""/>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            

                            <div class="row">
                                <div class="col-md-12">
                                    
                                    <div class="col-md-3">
                                        <label class="control-label">GTIN</label>
                                        <div class="controls">
                                            <input required class="form-control input-sm" type="text" id="gtin_edit" name="gtin_edit" value=""/>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="control-label">Unit Price</label>
                                        <div class="controls">
                                            <input required class="form-control input-sm" maxlength="9" max="999999" type="text" id="unit_price_edit" name="unit_price_edit" value=""/>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" id="edit_manufacturer" name="edit_manufacturer" value=""/>
                        </form>
                    </div>
                    <!-- // Modal body END --> 

                    <!-- Modal footer -->
                    <div class="modal-footer"> <a data-dismiss="modal" class="btn btn-default" href="#">Close</a> <a class="btn btn-primary" id="save_manufacturer_edit" data-dismiss="modal" href="#">Update changes</a> </div>
                    <!-- // Modal footer END --> 
                </div>
            </div>
        </div>
    
    
    <?php include PUBLIC_PATH . "/html/footer.php"; ?>
    <?php include PUBLIC_PATH . "/html/reports_includes.php"; ?>
    <script type="text/javascript">
        $(function() {
                $("#save_manufacturer").click(function() {
                    var product = <?=$_REQUEST['prod_id']?>;
                    var manufacturer = $('#new_manufacturer').val();
                    if (manufacturer == '') {
                        alert('Enter Manufacturer.');
                                    $('#new_manufacturer').focus();
                        return false;
                    }
                    if ($('#brand_name').val() == '') {
                        alert('Enter Brand Name.');
                                    $('#brand_name').focus();
                        return false;
                    }
                    if ($('#quantity_per_pack').val() == '') {
                        alert('Enter Quantity per Pack.');
                                    $('#quantity_per_pack').focus();
                        return false;
                    }else if (isNaN($('#quantity_per_pack').val())){
                                    alert('Invalid data.');
                                    $('#quantity_per_pack').focus();
                        return false;
                            }
                    $.ajax({
                        type: "POST",
                        url: "../im/add_action_manufacturer.php",
                        data: 'add_action=1&item_pack_size_id='+product+'&'+$("#addnew").serialize(),
                        dataType: 'html',
                        success: function(data) {
                            //reload page
                            location.reload();
                        }
                    });
                });
                $("#save_manufacturer_edit").click(function() {
                    var product = <?=$_REQUEST['prod_id']?>;
                    var manufacturer = $('#new_manufacturer_edit').val();
                    if (manufacturer == '') {
                        alert('Enter Manufacturer.');
                                    $('#new_manufacturer_edit').focus();
                        return false;
                    }
                    if ($('#brand_name_edit').val() == '') {
                        alert('Enter Brand Name.');
                                    $('#brand_name_edit').focus();
                        return false;
                    }
                    if ($('#quantity_per_pack_edit').val() == '') {
                        alert('Enter Quantity per Pack.');
                                    $('#quantity_per_pack_edit').focus();
                        return false;
                    }else if (isNaN($('#quantity_per_pack_edit').val())){
                                    alert('Invalid data.');
                                    $('#quantity_per_pack_edit').focus();
                        return false;
                            }
                    $.ajax({
                        type: "POST",
                        url: "../im/add_action_manufacturer.php",
                        data: 'add_action=1&item_pack_size_id='+product+'&'+$("#edit_record").serialize(),
                        dataType: 'html',
                        success: function(data) {
                            //reload page
                            //alert('D:'+data);
                            location.reload();
                        }
                    });
                });
                
                
                $(".edit_btn").click(function() {
                    var manufacturer = $(this).data('brand-id');
                    
                    $.ajax({
                        type: "POST",
                        url: "ajax_view_manufacturer.php",
                        data: 'show_manuf=1&id='+manufacturer,
                        dataType: 'json',
                        success: function(data) {
                            //alert('stk:'+data.stkname);
                            $('#new_manufacturer_edit').val(data.stkname);
                            $('#brand_name_edit').val(data.brand_name);
                            $('#pack_length_edit').val(data.pack_length);
                            $('#pack_width_edit').val(data.pack_width);
                            $('#pack_height_edit').val(data.pack_height);
                            $('#net_capacity_edit').val(data.net_capacity);
                            $('#carton_per_pallet_edit').val(data.carton_per_pallet);
                            $('#quantity_per_pack_edit').val(data.quantity_per_pack);
                            $('#gtin_edit').val(data.gtin);
                            $('#gross_capacity_edit').val(data.gross_capacity);
                            $('#unit_price_edit').val(data.unit_price);
                            $('#edit_manufacturer').val(data.stk_id);
                            
                            $('#modal-manufacturer-edit').modal('show');
                        }
                    });
                });
                
                
                $('.dimensions').keyup(function() {
                        var pack_length = $('#pack_length').val();
                        var pack_width 	= $('#pack_width').val();
                        var pack_height = $('#pack_height').val();
                        var gross = 0 ;

                    if ( typeof pack_length== 'undefined') 	pack_length=0;
                    if ( typeof pack_width== 'undefined') 	pack_width=0;
                    if ( typeof pack_height== 'undefined') 	pack_height=0;

                        gross = pack_length * pack_width * pack_height;

                   $('#gross_capacity').val(gross);

                })
                $('.dimensions_edit').keyup(function() {
                        var pack_length = $('#pack_length_edit').val();
                        var pack_width 	= $('#pack_width_edit').val();
                        var pack_height = $('#pack_height_edit').val();
                        var gross = 0 ;

                    if ( typeof pack_length== 'undefined') 	pack_length=0;
                    if ( typeof pack_width== 'undefined') 	pack_width=0;
                    if ( typeof pack_height== 'undefined') 	pack_height=0;

                        gross = pack_length * pack_width * pack_height;

                   $('#gross_capacity_edit').val(gross);

                })
        });
        function editFunction(val) {
            window.location = "ManageStakeholders.php?Do=Edit&Id=" + val;
        }
        function delFunction(val) {
            if (confirm("Are you sure you want to delete the record?")) {
                window.location = "ManageStakeholders.php?Do=Delete&Id=" + val;
            }
        }
        var mygrid;
        //Initializing grid
       
    </script>
    <?php
    if (isset($_SESSION['err'])) {
        ?>
        <script>
            var self = $('[data-toggle="notyfy"]');
            notyfy({
                force: true,
                text: '<?php echo $_SESSION['err']['text']; ?>',
                type: '<?php echo $_SESSION['err']['type']; ?>',
                layout: self.data('layout')
            });
        </script>
        <?php
        //Unsetting session
        unset($_SESSION['err']);
    }
    ?>
</body>
</html>	