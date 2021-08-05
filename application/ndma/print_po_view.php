<?php
include("../includes/classes/AllClasses.php");
include(PUBLIC_PATH . "html/header.php");

$userid = $_SESSION['user_id'];
$unique_id = $_REQUEST['po_id'];



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
purchase_order_product_details.manufacturer_id,
stakeholder.contact_numbers,
stakeholder.contact_emails,
stakeholder.contact_address,
stakeholder.company_status
FROM
purchase_order
INNER JOIN purchase_order_product_details ON purchase_order.pk_id = purchase_order_product_details.po_master_id
LEFT JOIN itminfo_tab ON purchase_order_product_details.item_id = itminfo_tab.itm_id
INNER JOIN stakeholder ON purchase_order.supplier_id = stakeholder.stkid
WHERE
purchase_order.pk_id = '" . $unique_id . "'
    
";
$res = mysql_query($qry);
$po_details = array();
$po_data = array();
while ($row = mysql_fetch_assoc($res)) {
    $po_details[]=$row;
    $po_data['po_date'] =$row['po_date'];
    
    $po_data['po_number']               =$row['po_number'];
    $po_data['reference_number']        =$row['reference_number'];
    $po_data['vendor_name']     ='<b>M/s </b>'.$row['vendor_name'];
    $po_data['contact_address'] ='<b>Address : </b>'.$row['contact_address'];
    $po_data['contact_numbers'] ='<b>Phone : </b>'.$row['contact_numbers'];
    $po_data['contact_emails']  ='<b>Email : </b>'.$row['contact_emails'];
}

$qry = " select * from purchase_order_prints where po_id = '" . $unique_id . "' order by pk_id desc limit 1";
$res = mysql_query($qry);
$print_out = mysql_fetch_array($res);


?>
<style type="text/css" media="print">
    @media print
    {    
        #printButt, .dontprint
        {
            display: none !important;
        }
        html, body, b, table, h3{
        font-size:10pt !important;
        }
    

    }
/*    ol {counter-reset: section;  list-style-type:none;padding-left:0}
    ol li:before {counter-increment: section;font-weight:700;content: counters(section, ".") ". "}*/
/*    *{font-family:"Open Sans",sans-serif;}*/
    b{
        font-weight:bold;
    }
    #report_type{
        font-size:11px;
        font-family: arial;}
    #content_print
    {
        width:100%;
        margin-left:5px;
    }
    table{
        width:80%;
    }
    html, body, b, table, h3{
        font-size:10pt !important;
        }

</style>
</head>

<body class="page-header-fixed page-quick-sidebar-over-content">

    <div id="content_print">

        <a class="dontprint btn btn-xs btn-info" target="_blank" href="print_help.php">How to change print settings in your browser?</a>

        <div width="70%">
            <form id="po" action="print_po_action.php">
                <table align="center"  width="70%">
                    <tr>
                        <td ><img src="govt_of_pak.jpg" width="160px"> </td>
                        <td align="center"><?= $print_out['head_1'] ?><br/>
                            Government of Pakistan<br/>
                            Prime Minister’s Office<br/>
                            National Disaster Management Authority<br/>
                            ************</td>
                        <td ><img src="ndma_separate.jpg" width="160px"> </td>
                    </tr>
                </table>

                                    <table align="center"  width="60%">
                                        <tr>
                                            <td width="100%" align="center" >
                                                    System PO Number : <b><?=$po_data['po_number']?></b><br/>
                                                    Manual PO Number : <b><?=$po_data['reference_number']?></b><br/>
                                            </td>
                                        </tr>
                                    </table>
                <table align="center"  width="70%">
                    <tr>
                        <td width="70%">
                            <?= $po_data['vendor_name'] ?><br/>
                            <?= nl2br($po_data['contact_address']) ?><br/>
                            <?= $po_data['contact_numbers'] ?><br/>
                            <?= $po_data['contact_emails'] ?>
                        </td>
                        <td align="right">Date:<?= $print_out['display_date'] ?></td>
                    </tr>
                </table>
                <br/>
                <br/>
                <table align="center"  width="70%">
                    <tr>
                        <td align="left"><u><b>Subject:<?= $print_out['subject'] ?></b></u></td>
                    </tr>
                </table>
                <br/>
                <table align="center"  width="70%">
                    <tr>
                        <td align="left"><?= $print_out['body_1'] ?></td>
                    </tr>
                </table>
                <table align="center"  width="70%">
                    <tr>
                        <td align="left"><?= $print_out['body_2'] ?></td>
                    </tr>
                </table>
                <br/>
                <table align="center"  width="60%" border="1">
                    <tr>
                                            <td align="center">Sr</td>
                                            <td align="center">Items Description</td>
                                            <td align="center">Qty</td>
                                            <td align="center">Rate(Rs)</td>
                                        </tr>
                                        <?php
                                        $c=1;
                                        foreach($po_details as $k=>$entry)
                                        {
                                        echo '<tr>
                                                    <td align=""> '.$c++.'</td>
                                                    <td align=""> '.$entry['itm_name'].'</td>
                                                    <td align="right">'.number_format($entry['shipment_quantity']).'</td>
                                                    <td align="right">'.number_format($entry['unit_price']).'</td>
                                                </tr>';
                                        }
                                        ?>
                </table>
                <br/>
                <table align="center"  width="70%">
                    <tr>
                        <td align="left"><b>2.    Terms and Conditions applied includes:-</b><br/>
                            <p><?= nl2br($print_out['tnc']) ?></p>
                        </td>
                    </tr>
                </table>
                <table align="center"  width="70%">
                    <tr>
                        <td align="left">3.     After completion of the satisfactory work, you are advised to submit the bill and delivery challan/ work completion certificate to the undersigned for early payment.</td>
                    </tr>
                </table>
                <table align="center"  width="70%">
                    <tr>
                        <td align="left">
                            <?= $print_out['body_3'] ?>
                        </td>
                    </tr>
                </table>
                <br/>
                <table align="center"  width="70%">
                    <tr>
                        <td align="left"><b>Cc:</b><br/><ol><?= nl2br($print_out['cc']) ?></ol><td>
                    </tr>
                </table>
                <br/>
                <br/>
                <table align="center"  width="70%">
                    <tr>
                        <td align="right">
                            (<?= $print_out['signee_name'] ?>)<br/>
                            <?= $print_out['signee_desig'] ?><br/>
                            Ph# <?= $print_out['signee_ph'] ?>
                        </td>
                    </tr>
                </table>

            </form>
        </div>
    </div>

    <script src="<?php echo PUBLIC_URL; ?>assets/global/plugins/jquery-1.11.0.min.js" type="text/javascript"></script>
    <script language="javascript">
        $(function() {
            printCont();
        })
        function printCont()
        {
            window.print();
        }
    </script>
</body>
</html>