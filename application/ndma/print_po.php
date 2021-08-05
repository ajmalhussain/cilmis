<?php
include("../includes/classes/AllClasses.php");
include(PUBLIC_PATH . "html/header.php");


$head_1='F. 2(5)/2019-20 -NDMA (Proc)';
$cc='PS to Chairman, NDMA';
$display_date='';
$subject='WORK ORDER NO XXX FOR SUPPLY OF ???';
$body_1='Reference your quotation for supply of ??? as per available stocks with your firm dated DD MM YYYY';
$body_2='2.The competent authority has been pleased to award you supply of ??? as per detail given below:-';
//$tnc='    a.    Delivery of the aforementioned may be completed within 05 days’ time however in case of any delay in delivery the NDMA reserve the right to refuse the acceptance of Item.
//    b.    In case of unsatisfactory services in any manner including quality & quantity and time line, NDMA reserve the right to withhold your payment or even to black list your firm.
//    c.    Bills/ delivery challan should be submitted along with GST invoice.';
$tnc='';
$body_3 = '';
$signee_name='Muhammad Idrees Mahsud';
$signee_desig='President Procurement Committee, NDMA';
$signee_ph='+9251-9087866';

$userid = $_SESSION['user_id'];
$unique_id = $_REQUEST['po_id'];
$po_data=array();


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
//echo $qry;exit;
$res = mysql_query($qry);
$po_details = array();
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
$display_date=''.date('jS F Y',strtotime($po_data['po_date']));

$qry = "SELECT
text_values.pk_id,
text_values.stkid,
text_values.type,
text_values.text_value,
text_values.updatedby,
text_values.updatedon,
text_values.is_active,
order_of_display
FROM
text_values
WHERE
text_values.type = 'tnc' AND
text_values.stkid = '".$_SESSION['user_stakeholder1']."' AND
text_values.is_active = 1
order by order_of_display ASC
";
$res = mysql_query($qry);
$tnc_arr = array();
while ($row = mysql_fetch_assoc($res)) {
    $tnc_arr[] = $row;
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
        <a class="dontprint btn btn-xs btn-info" target="_blank" href="print_help.php">How to change print settings in your browser?</a>
                <!-- BEGIN PAGE HEADER-->
                <div class="row">
                    <div class="col-md-12">
                        <div class="widget" data-toggle="collapse-widget">
                            <div class="widget-head">
                                <h3 class="heading"> Purchase Order - Print Out</h3>
                            </div>
                            <div class="widget-body">

                                <div width="70%">
                                    <form id="po" action="print_po_action.php">
                                    <table align="center"  width="70%">
                                        <tr>
                                            <td ><img src="govt_of_pak.jpg" width="160px"> </td>
                                            <td align="center"><input id="subject" align="center" name="head_1" id="head_1"   size="35" type="text" value="<?=$head_1?>" style="background: #f5f6fb;"><br/>
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
                                    <table align="center"  width="60%">
                                        <tr>
                                            <td width="70%">To,<br/>
                                                    <?=$po_data['vendor_name']?><br/>
                                                    <?=$po_data['contact_address']?><br/>
                                                    <?=$po_data['contact_numbers']?><br/>
                                                    <?=$po_data['contact_emails']?>
                                            </td>
                                            <td align="right">Date:<input name="display_date" id="display_date"   size="25" type="text" value="<?=$display_date?>" style="background: #f5f6fb;"></td>
                                        </tr>
                                    </table>
                                    <table align="center"  width="70%">
                                        <tr>
                                            <td align="left"><u>Subject:</u><input id="subject" name="subject" id="" name="" size="110" type="text" value="<?=$subject?>" style="background: #f5f6fb;">

 </td>
                                        </tr>
                                    </table>
                                    <table align="center"  width="70%">
                                        <tr>
                                            <td align="left"><textarea id="body_1" name="body_1" cols="120" rows="1" style="background: #f5f6fb;"><?=nl2br($body_1)?></textarea></td>
                                        </tr>
                                    </table>
                                    <table align="center"  width="70%">
                                        <tr>
                                            <td align="left"><textarea id="body_2" name="body_2" cols="120" rows="1"  style="background: #f5f6fb;"><?=nl2br($body_2)?></textarea></td>
                                        </tr>
                                    </table>
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
                                    <table align="center"  width="70%">
                                        <tr>
                                            <td align="left">
                                                Select the Terms & Conditions (Multiple): 
                                            </td>
                                            <td align="left">
                                                <select id="tnc_dd" name="tnc_dd[]" class="select2 form-control" multiple>
                                                    <?php
                                                    foreach($tnc_arr as $k=>$this_tnc){
                                                        echo '<option selected>'.$this_tnc['text_value'].'</option>';
                                                    }
                                                    ?>
                                                    <option></option>
                                                </select>
                                            </td>
                                        </tr>
                                    </table>
                                    <table align="center"  width="70%">
                                        <tr>
                                            <td align="left">2.    Terms and Conditions applied includes:-<br/>
                                                <textarea  id="tnc" name="tnc" cols="120" rows="5"  style="background: #f5f6fb;"><?php
                                                    foreach($tnc_arr as $k=>$this_tnc){
                                                        echo ''.$this_tnc['text_value'].PHP_EOL;
                                                    }
                                                    ?>

                                                </textarea>
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
                                                <textarea id="body_3" name="body_3" cols="120" rows="1"  style="background: #f5f6fb;"><?=nl2br($body_3)?></textarea>
                                            </td>
                                        </tr>
                                    </table>
                                        
                                    <table align="center"  width="70%">
                                        <tr>
                                            <td align="left">Cc:<textarea id="cc" name="cc" cols="120" rows="4"  style="background: #f5f6fb;"><?=$cc?></textarea><td>
                                        </tr>
                                    </table>
                                    <table align="center"  width="70%">
                                        <tr>
                                            <td align="right">
                                                (<input id="signee_name" name="signee_name" type="text" size="25" value="<?=$signee_name?>" style="background: #f5f6fb;">)<br/>
                                                <input id="signee_desig" name="signee_desig" type="text" size="25" value="<?=$signee_desig?>" style="background: #f5f6fb;"><br/>
                                                Ph# <input id="signee_ph" name="signee_ph" type="text" size="25" value="<?=$signee_ph?>" style="background: #f5f6fb;">
                                            </td>
                                        </tr>
                                    </table>
                                        <input type="hidden" name="po" value="<?=$unique_id?>">
                                        <button class="btn btn-success pull-right" type="submit">Print</button>
                                    </form>
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
    $(function() {
        $("#tnc_dd").change(function () {
             var tval = '';
            //    $("#tnc").val(tval);

            $('#tnc').val('');
            $('option:selected', $(this)).each(function() {
            $("#tnc").val($("#tnc").val()+' \n '+$(this).val());
//            $("#tnc").val(tval);
            });
            
            
        });
});
</script>
</html>