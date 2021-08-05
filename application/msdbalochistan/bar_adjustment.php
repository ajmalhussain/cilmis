<?php
/**
 * add_adjustment
 * @package im
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
//Including AllClasses file
include("../includes/classes/AllClasses.php");
//Including header file
include(PUBLIC_PATH . "html/header.php");
include "../includes/styling/dynamic_theme_color.php";
//Initializing variables
$title = "New Issue";
$TranRef = '';
//Get All WH Product
//$category = '1,4';
//if($_SESSION['user_stakeholder1'] == 145) $category='5';
//$items = $objManageItem->GetAllManageItem($category);
//

$stk = $_SESSION['user_stakeholder1'];
$items = $objManageItem->GetAllProduct_of_stk($stk);
//Get Adjusment Types
$types = $objTransType->getAdjusmentTypes();
?>
<link rel="stylesheet" type="text/css" href="<?php echo PUBLIC_URL; ?>assets/global/plugins/select2/select2.css"/>
</head><!-- END HEAD -->

<body class="page-header-fixed page-quick-sidebar-over-content" >
    <!-- BEGIN HEADER -->
    <div class="page-container">
        <?php
        //Including top file
        include PUBLIC_PATH . "html/top.php";
        //Including top_im file
        include PUBLIC_PATH . "html/top_im.php";
        ?>
        <div class="page-content-wrapper">
            <div class="page-content">
                <div class="row">
                    <div class="col-md-12">
                        <form method="POST" name="batch_search" id="batch_search" action="bar_add_adjustment_action.php" >
                            <div class="panel panel-primary">
                            <div class="panel-heading">
                                <h3 class="panel-title"> <i class="fa fa-shopping-cart"></i> Automated Barcode Stock Issuance</h3>
                                <ul class="list-inline panel-actions">
                                    <li><a href="#" id="panel-fullscreen" role="button" title="Toggle fullscreen"><i class="glyphicon glyphicon-resize-full"></i></a></li>
                                </ul>
                            </div>
                                <div class="widget-body" style="padding-bottom:3%;"> 
                                    <!-- Row -->
                                    <div class="row">
                                        <div class="col-md-12">

                                            <div class="col-md-11">
                                                <div class="note note-info" id="note_16" style="display:none;"> <b>Important:</b> Opening Balance should be entered only for New Batches. This quantity will not be shown as adjustment in Reports, instead it will be displayed as Opening Balance of that month. </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            
                                            <div class="col-md-4">
                                                <div class="control-group">
                                                    <label for="firstname" class="control-label"> Adjustment Date </label>
                                                    <div class="controls">
                                                        <div class="input-group">
                                                            <span  class="input-group-addon input-circle-left  bg-blue-madison">
                                                                <i class="fa fa-clock-o"></i>
                                                            </span>
                                                            <?php
                                                            if ($_SESSION['user_level'] >= 3) {
                                                                ?>
                                                                <input class="form-control input-medium" id="adjustment_date1" readonly name="adjustment_date" type="text" value="<?php echo date("d/m/Y"); ?>" required />
                                                                <?php
                                                            } else {
                                                                ?>
                                                                <input class="form-control input-medium" id="adjustment_date" name="adjustment_date" type="text" value="<?php echo date("d/m/Y"); ?>" required />
                                                                <?php
                                                            }
                                                            ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div> 
                                            <div class="col-md-4">
                                                <div class="control-group">
                                                    <label class="control-label" for="firstname"> Adjustment Type <span class="red">*</span> </label>
                                                    <div class="controls">
                                                        <div class="input-group">
                                                            <span  class="input-group-addon   bg-blue-madison">
                                                                <i class="fa fa-th-list"></i>
                                                            </span>
                                                            <select name="types" id="types" class="form-control input-medium" required="true">
                                                                <option value="">Select</option>
                                                                <?php
                                                                $tranNature = array();
                                                                foreach ($types as $type) {
                                                                    if ($_SESSION['user_province1'] == 1 && $_SESSION['user_level'] == 3) {
                                                                        //if($type->trans_id != 16)
                                                                        //continue;
                                                                    }
                                                                    if ($type->trans_nature == '-') {
                                                                        $tranNature[] = $type->trans_id;
                                                                    }
                                                                    //Populate types combo

                                                                    $clr = '';
                                                                    if ($type->trans_nature == '-') {
                                                                        $clr = 'background-color:#ffc4c8;';
                                                                    } elseif ($type->trans_nature == '+') {
                                                                        $clr = 'background-color:#86CF86;';
                                                                    }

                                                                    echo "<option value=" . $type->trans_id . " style=" . $clr . ">" . $type->trans_type . " (" . $type->trans_nature . ")</option>";
                                                                }
                                                                ?>
                                                            </select>
                                                            <input type="hidden" id="negTransType" value="<?php echo implode(',', $tranNature); ?>" />
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="control-group">
                                                    <label for="firstname"> Ref. No. </label>
                                                    <div class="controls">
                                                        <div class="input-group">
                                                            <span  class="input-group-addon input-circle-left  bg-blue-madison">
                                                                <i class="fa fa-tag"></i>
                                                            </span>
                                                            <input class="form-control input-medium" id="ref_no" name="ref_no" type="text" value="" />
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Group Receive Date-->
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
                                            <div class="col-md-5">
                                                <div class="control-group">
                                                    <label class="control-label" for="firstname"> Quantity <span class="red">*</span> </label>
                                                    <div class="controls">
                                                        <div class="input-group">
                                                            <span  class="input-group-addon input-circle-left  bg-blue-madison">
                                                                <i class="fa fa-sort-numeric-asc"></i>
                                                            </span>
                                                            <input class="form-control input-medium" id="quantity" name="quantity" type="text" value="<?php echo $TranRef; ?>" required style="text-align:right" />
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-2">
                                                <div class="control-group">
                                                    <label class="control-label" >&nbsp;</label>
                                                    <div class="controls"> <a class="btn btn-primary alignvmiddle" style="display:none;" id="add_m_p"  onclick="javascript:void(0);
//                                                            document.getElementById('available_div').style.display = 'none';
                                                            document.getElementById('batch_no').value = ''" data-toggle="modal"  href="#modal-manufacturer">Add New Batch</a> </div>
                                                </div>
                                            </div>
                                        </div> 
                                        <div class="col-md-12">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label class="control-label">
                                                        <h4 class="font-green-seagreen "> Product</h4> </label>
                                                    <div class="controls" id="product_div">---
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label class="control-label" for="batch_no">
                                                        <h4 class="font-green-seagreen ">Batch No</h4> </label>
                                                    <div class="controls" id="batch_div">---
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-4" id=" ">
                                                <div class="control-group">
                                                    <label class="control-label" for="available"> Available </label>
                                                    <div class="controls"> 
                                                        <div class="input-group">
                                                            <span  class="input-group-addon input-circle-left  bg-blue-madison">
                                                                <i class="fa fa-sort-numeric-desc"></i>
                                                            </span>
                                                            <span id="itembatches">
                                                                <input class="form-control input-medium num" id="available" name="available" type="text" value="<?php echo $TranRef; ?>" disabled="" style="display:inline !important"/>
                                                            </span> <span id="product-unit">Unit</span> </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="col-md-3">
                                                <div class="control-group">
                                                    <label class="control-label" for="firstname"> Comment </label>
                                                    <div class="controls">
                                                        <div class="input-group">
                                                            <span  class="input-group-addon input-circle-left  bg-blue-madison">
                                                                <i class="fa fa-th-list"></i>
                                                            </span>
                                                            <textarea name="comments" id="comments" class="form-control input-medium"></textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-9 add-adjustment-btn right">
                                                <label class="control-label" for="firstname">&nbsp;</label>
                                                <div class="controls">
                                                    <button type="submit"  class="btn default" id="add_adjustment" style="display:none;">Save</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form> 
                        <!-- // Content END --> 
                    </div>
                </div>
            </div>
            <?php include PUBLIC_PATH . "/html/footer.php"; ?>
            <script src="<?php echo PUBLIC_URL; ?>js/dataentry/jquery.mask.min.js"></script> 
            <script src="<?php echo PUBLIC_URL; ?>js/jquery.validate.js"></script> 
            <script src="<?php echo PUBLIC_URL; ?>js/dataentry/add_adjustment.js"></script>
            <script src="<?php echo PUBLIC_URL; ?>js/barcode/barcode_splitter_adj.js"></script> 
            <script type="text/javascript" src="<?php echo PUBLIC_URL; ?>assets/global/plugins/select2/select2.min.js"></script>
            <script>
                                                        $(function () {
                                                            $(document).keypress(function (e) {
                                                                if (e.which == 13) {
                                                                    $('#add_receive').trigger('click');
                                                                }
                                                            });
                                                            $("#item_code").focus();
                                                            $("#item_code").change(function () {
                                                                string_splitter($(this).val());
                                                            });
                                                        });

            </script>
            <?php
            $_SESSION['stockIssueArray'] = $stockArray;
            if (isset($_SESSION['success']) && !empty($_SESSION['success'])) {
                ?>
                <script>
                    var self = $('[data-toggle="notyfy"]');
                    notyfy({
                        force: true,
                        text: 'Data has been saved successfully!',
                        type: 'success',
                        layout: self.data('layout')
                    });
                </script>
                <?php
                unset($_SESSION['success']);
            }
            ?>
            <!-- END FOOTER --> 
            <!-- END JAVASCRIPTS -->
            </body>
            <!-- END BODY -->
            </html>