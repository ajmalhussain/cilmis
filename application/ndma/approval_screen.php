<?php
include("../includes/classes/AllClasses.php");
include(PUBLIC_PATH . "html/header.php");

$userid = $_SESSION['user_id'];
$unique_id = $_REQUEST['unique_id'];
$strSql = "
SELECT
purchase_order.pk_id,
purchase_order.po_date,
purchase_order.po_number,
purchase_order.reference_number,
purchase_order.item_id,
purchase_order.manufacturer,
purchase_order.shipment_quantity,
purchase_order.stk_id,
purchase_order.procured_by,
purchase_order.created_date,
purchase_order.created_by,
purchase_order.modified_by,
purchase_order.modified_date,
purchase_order.`status`,
purchase_order.wh_id,
purchase_order.unit_price,
purchase_order.dollar_rate,
purchase_order.contact_no,
purchase_order.signing_date,
purchase_order.funding_source,
purchase_order.adv_payment_release,
purchase_order.contract_delivery_date,
purchase_order.po_accept_date,
purchase_order.po_cancelled_date,
purchase_order.po_delete_date,
purchase_order.currency,
purchase_order.local_foreign,
purchase_order.country,
purchase_order.sub_cat,
purchase_order.tender_no,
itminfo_tab.itm_name,
stakeholder.stkname,
tbl_warehouse.wh_name as funding_source_name,
countries.`name` AS country_name,
item_requirements.requirement
FROM
purchase_order
LEFT JOIN itminfo_tab ON purchase_order.item_id = itminfo_tab.itm_id
LEFT JOIN stakeholder_item ON purchase_order.manufacturer = stakeholder_item.stk_id
LEFT JOIN stakeholder ON stakeholder_item.stkid = stakeholder.stkid
LEFT JOIN tbl_warehouse ON purchase_order.funding_source = tbl_warehouse.wh_id
LEFT JOIN countries ON purchase_order.country = countries.id
LEFT JOIN item_requirements ON purchase_order.wh_id = item_requirements.wh_id AND purchase_order.item_id = item_requirements.item_id
WHERE
purchase_order.pk_id = '" . $unique_id . "'
    
";
$rsSql = mysql_query($strSql);

if (mysql_num_rows($rsSql) > 0) {
    $po_data = mysql_fetch_assoc($rsSql);
}
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
                <!-- BEGIN PAGE HEADER-->
                <div class="row">
                    <div class="col-md-12">
                        <div class="widget" data-toggle="collapse-widget">
                            <div class="widget-head">
                                <h3 class="heading"> Purchase Order Details</h3>
                            </div>
                            <div class="widget-body">


                                <div class="col-md-8 ">
                                    <div class="portlet green-meadow box">
                                        <div class="portlet-title">
                                            <div class="caption">
                                                <i class="fa fa-cogs"></i>PO Approval Action
                                            </div>
                                        </div>
                                        <div class="portlet-body">
                                            <div class="row static-info">
                                                <div class="col-md-12">
                                                    <div class="col-md-5 name">Product / Item </div>
                                                    <div class="col-md-7 value"><?= $po_data['itm_name'] ?></div>
                                                </div>
                                            </div>

                                            <div class="row static-info">
                                                <div class="col-md-12">
                                                    <div class="col-md-5 name">Vendor</div>
                                                    <div class="col-md-7 value"><?= $po_data['stkname'] ?></div>
                                                </div>
                                            </div>

                                            <div class="row static-info">
                                                <div class="col-md-12">
                                                    <div class="col-md-5 name">PO Number</div>
                                                    <div class="col-md-5 name"><?= $po_data['po_number'] ?></div>
                                                </div>
                                            </div>

                                            <div class="row static-info">
                                                <div class="col-md-12">
                                                    <div class="col-md-5 name">Manual PO Number</div>
                                                    <div class="col-md-7 value"><?= $po_data['reference_number'] ?></div>
                                                </div>
                                            </div>

                                            <div class="row static-info">
                                                <div class="col-md-12">
                                                    <div class="col-md-5 name">PO Date</div>
                                                    <div class="col-md-7 value"><?= $po_data['po_date'] ?></div>
                                                </div>
                                            </div>

                                            <div class="row static-info">
                                                <div class="col-md-12">
                                                    <div class="col-md-5 name">Required Qty</div>
                                                    <div class="col-md-7 value"><?= $po_data['requirement'] ?></div>
                                                </div>
                                            </div>

                                            <div class="row static-info">
                                                <div class="col-md-12">
                                                    <div class="col-md-5 name">Contract No</div>
                                                    <div class="col-md-7 value"><?= $po_data['contact_no'] ?></div>
                                                </div>
                                            </div>

                                            <div class="row static-info">
                                                <div class="col-md-12">
                                                    <div class="col-md-5 name">Signed On</div>
                                                    <div class="col-md-7 value"><?= $po_data['signing_date'] ?></div>
                                                </div>
                                            </div>

                                            <div class="row static-info">
                                                <div class="col-md-12">
                                                    <div class="col-md-5 name">Ordered Qty</div>
                                                    <div class="col-md-7 value"><?= $po_data['shipment_quantity'] ?></div>
                                                </div>
                                            </div>

                                            <div class="row static-info">
                                                <div class="col-md-12">
                                                    <div class="col-md-5 name">Funded By</div>
                                                    <div class="col-md-7 value"> <?= $po_data['funding_source_name'] ?></div>
                                                </div>
                                            </div>

                                            <div class="row static-info">
                                                <div class="col-md-12">
                                                    <div class="col-md-5 name">Local / Foreign</div>
                                                    <div class="col-md-7 value"><?= $po_data['local_foreign'] ?></div>
                                                </div>
                                            </div>

                                            <div class="row static-info">
                                                <div class="col-md-12">
                                                    <div class="col-md-5 name">Country</div>
                                                    <div class="col-md-7 value"><?= $po_data['country_name'] ?></div>
                                                </div>
                                            </div>

                                            <div class="row static-info">
                                                <div class="col-md-12">
                                                    <div class="col-md-5 name">Sub Category</div>
                                                    <div class="col-md-7 value"><?= $po_data['sub_cat'] ?></div>
                                                </div>
                                            </div>

                                            <div class="row static-info">
                                                <div class="col-md-12">
                                                    <div class="col-md-5 name">Tender No</div>
                                                    <div class="col-md-7 value"><?= $po_data['tender_no'] ?></div>
                                                </div>
                                            </div>

                                            <div class="row static-info">
                                                <div class="col-md-12">
                                                    <div class="col-md-5 name">Currency</div>
                                                    <div class="col-md-7 value"><?= $po_data['currency'] ?></div>
                                                </div>
                                            </div>

                                            <div class="row static-info">
                                                <div class="col-md-12">
                                                    <div class="col-md-5 name">Unit Price</div>
                                                    <div class="col-md-7 value"><?= $po_data['unit_price'] ?></div>
                                                </div>
                                            </div>

                                            <div class="row static-info">
                                                <div class="col-md-12">
                                                    <div class="col-md-5 name">Exchange Rate</div>
                                                    <div class="col-md-7 value"><?= $po_data['dollar_rate'] ?></div>
                                                </div>
                                            </div>

                                            <div class="row static-info">
                                                <div class="col-md-12">
                                                    <div class="col-md-5 name">Advance Released</div>
                                                    <div class="col-md-7 value"><?= $po_data['adv_payment_release'] ?></div>
                                                </div>
                                            </div>

                                            <div class="row static-info">
                                                <div class="col-md-12">
                                                    <div class="col-md-5 name">Status</div>
                                                    <div class="col-md-7 value"><?= $po_data['status'] ?></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="portlet blue-hoki box">
                                        <div class="portlet-title">
                                            <div class="caption">
                                                <i class="fa fa-cogs"></i>PO Approval Action
                                            </div>
                                        </div>
                                        <div class="portlet-body">
                                            <form role="form" action="approval_action.php">
                                                <div class="form-group">

                                                    <label for="exampleInputEmail1">
                                                        Remarks / Comments
                                                    </label>
                                                    <textarea rows="7" required class="form-control" id="remarks" name="remarks" ></textarea>
                                                </div>

                                                <div class="clearfix">
                                                    <div class="btn-group" data-toggle="buttons">
                                                        <label class="btn btn-default ">
                                                            <input type="radio" required name="approval_action" value="approve" class="toggle"> Approve </label>
                                                        <label class="btn btn-default">
                                                            <input type="radio" required  name="approval_action" value="reject" class="toggle"> Reject </label>
                                                    </div>
                                                    <button type="submit" class="btn btn-success">
                                                        Submit
                                                    </button>
                                                </div>
                                                <div class="clearfix">

                                                </div>
                                                <input type="hidden" name="module" value="<?= $_REQUEST['module'] ?>" />
                                                <input type="hidden" name="unique_id" value="<?= $_REQUEST['unique_id'] ?>" />
                                                <input type="hidden" name="approval_level" value="<?= $_REQUEST['approval_level'] ?>" />
                                            </form>
                                        </div>
                                    </div>

                                </div>


                                <div class="row">
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>
    <?php
    include PUBLIC_PATH . "/html/footer.php";
    ?>
    <script src="<?php echo PUBLIC_URL; ?>js/dataentry/jquery.mask.min.js"></script>
</body>
</html>