<?php
include("../includes/classes/AllClasses.php");
include(PUBLIC_PATH . "html/header.php");

$userid = $_SESSION['user_id'];
$unique_id = $_REQUEST['po_id'];
$po_data = array();


$qry = "
SELECT
purchase_order.po_date,
purchase_order.po_number,
purchase_order.reference_number,
purchase_order_product_details.item_id,
purchase_order_product_details.manufacturer_id,
purchase_order_product_details.shipment_quantity,
purchase_order_product_details.unit_price,
itminfo_tab.itm_name,
stakeholder.stkname AS vendor_name,
purchase_order.procured_by,
tbl_warehouse.wh_name AS funding_source,
purchase_order.local_foreign,
countries.`name`,
purchase_order.`status`
FROM
purchase_order
INNER JOIN purchase_order_product_details ON purchase_order.pk_id = purchase_order_product_details.po_master_id
LEFT JOIN itminfo_tab ON purchase_order_product_details.item_id = itminfo_tab.itm_id
LEFT JOIN stakeholder ON purchase_order.supplier_id = stakeholder.stkid
INNER JOIN tbl_warehouse ON purchase_order.funding_source = tbl_warehouse.wh_id
INNER JOIN countries ON purchase_order.country = countries.id
WHERE
purchase_order.pk_id = '" . $unique_id . "'
    
";
//echo $qry;exit;
$res = mysql_query($qry);
$po_details = array();
while ($row = mysql_fetch_assoc($res)) {
    $po_details[] = $row;
    $po_data['po_date'] = $row['po_date'];

    $po_data['po_number'] = $row['po_number'];
    $po_data['reference_number'] = $row['reference_number'];
    $po_data['funding_source'] = $row['funding_source'];
    $po_data['local_foreign'] = $row['local_foreign'];
    $po_data['country'] = $row['name'];
    $po_data['status'] = $row['status'];
    $po_data['vendor_name'] = '<b>M/s </b>' . $row['vendor_name'];
}
$display_date = '' . date('jS F Y', strtotime($po_data['po_date']));
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
                                <h3 class="heading">View </h3>
                            </div>
                            <div class="widget-body">

                                <div width="70%"> 
                                    <div class="panel panel-default">
                                        <div class="panel-heading">Purchase Order Details</div>
                                        <div class="panel-body">
                                            <table align="center"  width="60%">
                                                <tr>
                                                    <td width="50%" align="left" >
                                                        <label><b>System PO Number :</b> <?= $po_data['po_number'] ?></label><br/>
                                                        <label><b>Manual PO Number :</b> <?= $po_data['reference_number'] ?></label><br/>
                                                        <label><b>Vendor Name: </b>  <?= $po_data['vendor_name'] ?> </label><br/>
                                                        <label><b>Date:</b> <?= $display_date ?></label>
                                                    </td>
                                                    <td width="50%" align="left" >
                                                        <label><b>Funded By :</b> <?= $po_data['funding_source'] ?></label><br/>
                                                        <label><b>PO Type :</b> <?= $po_data['local_foreign'] ?></label><br/>
                                                        <label><b>PO Country :</b> <?= $po_data['country'] ?></label><br/>
                                                        <label><b>PO Status:</b> <?= $po_data['status'] ?></label>
                                                    </td>
                                                </tr>
                                            </table> 
                                            <table align="center"  width="20%" border="1" class="table table-bordered">
                                                <tr>
                                                    <th align="center">Sr</th>
                                                    <th align="center">Items Description</th>
                                                    <th align="center">Qty</th>
                                                    <th align="center">Rate(Rs)</th>
                                                </tr>
                                                <?php
                                                $c = 1;
                                                foreach ($po_details as $k => $entry) {
                                                    echo '<tr>
                                                    <td align=""> ' . $c++ . '</td>
                                                    <td align=""> ' . $entry['itm_name'] . '</td>
                                                    <td align="right">' . number_format($entry['shipment_quantity']) . '</td>
                                                    <td align="right">' . number_format($entry['unit_price']) . ' /- </td>
                                                </tr>';
                                                }
                                                ?>

                                            </table>   
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
    include PUBLIC_PATH . "/html/footer.php";
    ?>
</body>
<script>
    $(function () {
        $("#tnc_dd").change(function () {
            var tval = '';
            //    $("#tnc").val(tval);

            $('#tnc').val('');
            $('option:selected', $(this)).each(function () {
                $("#tnc").val($("#tnc").val() + ' \n ' + $(this).val());
//            $("#tnc").val(tval);
            });


        });
    });
</script>
</html>