<?php
/**
 * printGatePass
 * @package im
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
include("../includes/classes/AllClasses.php");
include(PUBLIC_PATH . "html/header.php");

$title = "Gate Pass Voucher";
$print = 1;
$stockId = $_GET['id'];
$qry = "SELECT
		tbl_stock_master.TranNo,
		tbl_stock_master.TranDate,
		gatepass_master.transaction_date,
                
                gatepass_master.gatepass_vehicle_id,
		stock_batch.batch_no,
		gatepass_master.number,
		gatepass_detail.quantity,
		itminfo_tab.itm_name,
		CONCAT(tbl_warehouse.wh_name, ' (', stakeholder.stkname, ')') AS wh_name,
		stock_batch.batch_expiry, 
                
                gatepass_master.i_address,
                gatepass_master.warehouse_id,
                gatepass_master.i_name,
                gatepass_master.i_contact,
                gatepass_master.i_cnic,
                gatepass_master.d_name,
                gatepass_master.d_contact,
                gatepass_master.d_cnic,
                gatepass_master.gp_status,
                stakeholder_item.quantity_per_pack as carton_size,
                gatepass_vehicles.number as vehicle_reg_number
	FROM
		gatepass_detail
	LEFT JOIN tbl_stock_detail ON gatepass_detail.stock_detail_id = tbl_stock_detail.PkDetailID
	LEFT JOIN tbl_stock_master ON tbl_stock_detail.fkStockID = tbl_stock_master.PkStockID
	LEFT JOIN stock_batch ON tbl_stock_detail.BatchID = stock_batch.batch_id
	LEFT JOIN itminfo_tab ON stock_batch.item_id = itminfo_tab.itm_id
	LEFT JOIN gatepass_master ON gatepass_detail.gatepass_master_id = gatepass_master.pk_id
	LEFT JOIN tbl_warehouse ON tbl_stock_master.WHIDTo = tbl_warehouse.wh_id
	LEFT JOIN stakeholder ON stakeholder.stkid = tbl_warehouse.stkofficeid
LEFT JOIN gatepass_vehicles ON gatepass_master.gatepass_vehicle_id = gatepass_vehicles.pk_id
LEFT JOIN stakeholder_item ON stock_batch.manufacturer = stakeholder_item.stk_id
	WHERE
		gatepass_detail.gatepass_master_id =" . $_GET['id'];

//echo $qry;exit;
$qryRes2 = mysql_fetch_array(mysql_query($qry));
$qryRes = mysql_query($qry);
$data_arr = $iss_vouchers_arr =  array();
while ($row = mysql_fetch_array($qryRes)) {
    $iss_vouchers_arr[$row['TranNo']] = $row['TranNo'];
    $data_arr[] = $row;
    $wh_id = $row['warehouse_id'];
    $issued_to_wh = $row['wh_name'];
}
//echo '<pre>';
//print_r($qryRes2);
//print_r($data_arr);

 $getWHName = "SELECT
                tbl_warehouse.wh_name,
                tbl_warehouse.stkid,
                tbl_warehouse.prov_id,
                stakeholder.lvl
                FROM
                tbl_warehouse
                INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid where wh_id='" . $wh_id . "'";
$resWHName = mysql_query($getWHName) or die(mysql_error());
$whName = mysql_fetch_assoc($resWHName);
?>

<div id="content_print">
    <div style="float:right; font-size:12px;"><span style="line-height:15px"><?php echo $whName['wh_name']; ?></span></div>

    <style type="text/css" media="print">
        @media print
        {    
            #printButt
            {
                display: none !important;
            }
        }
        .ftable
        {

        }
    </style>
    <?php
    $rptName = 'Gate Pass Voucher';
    include('report_header.php');
    
    ?>
    <div style="width:100%; clear:both; margin-top:30px;  ">
        <?php
            $heading_color = '#d6d6d6';
            if(!empty($qryRes2['gp_status']) && $qryRes2['gp_status'] == 'deleted')
            {
                $heading_color = '#f4a4a4';
                ?>
        <div  style="background-color:<?=$heading_color?> !important;" align="center" ><h2><br/>This Gate Pass was <br/> Marked as Deleted<br/></h2></div>
                <br/>
                <?php
            }
        ?>
        
        <table style="width:100%; border: 0px; ">
            
            <tr>
                <td width="50%">
                    
                    <table width="" cellpadding="5"  id="myTable" class="table-condensed" style="float:left; border:1px black; border-collapse:collapse;">

                        <tbody class="ftable">
                            <tr class="ftable">
                                <th class="ftable" width="10%" scope="row">Gate Pass No:</th>

                                <td width="12%"><span style="line-height:15px"><?php echo $qryRes2['number']; ?></span></td>
                            </tr>
                            <tr class="ftable">
                                <th class="ftable" scope="row">Gate Pass Date:</th>
                                <td width="14%">        <div style="clear:both;">
                                        <?php echo date("d-M-Y", strtotime($qryRes2['transaction_date'])); ?>
                                    </div></td>
                            </tr>
                            <tr class="ftable">
                                <td  style="background-color:<?=$heading_color?> !important;" align="center" colspan="2">Issued To:</td>
                                
                            </tr>
                            <tr class="ftable">
                                <th class="ftable" scope="row">Address:</th>
                                <td width="14%">        <div style="clear:both;">
                                        <?php echo $qryRes2['i_address']; ?>
                                    </div></td>
                            </tr>
                            <tr class="ftable">
                                <th class="ftable" scope="row">Name:</th>
                                <td width="14%">        <div style="clear:both;">
                                        <?php echo $qryRes2['i_name']; ?>
                                    </div></td>
                            </tr>
                            <tr class="ftable" >
                                <th class="ftable" scope="row">Contact #</th>
                                <td width="14%">        <div style="clear:both;">
                                        <?php echo $qryRes2['i_contact']; ?>
                                    </div></td>
                            </tr>
                            <tr class="ftable">
                                <th class="ftable" scope="row">CNIC #</th>
                                <td width="14%">        <div style="clear:both;">
                                        <?php echo $qryRes2['i_cnic']; ?>
                                    </div></td>
                            </tr>  
                        </tbody>
                    </table>
                </td>
                <td width="8%" style="">
                    &nbsp;
                </td>
                <td valign="top">
                    <table width="" cellpadding="5" id="myTable" class="table-condensed" style="float:right; border:1px black; border-collapse:collapse;">
                        <tbody class="ftable">

                            
                            <tr class="ftable">
                                <th class="ftable" scope="row">Issued Vouchers</th>

                                <td width="14%"><div style="clear:both;"><?php echo implode($iss_vouchers_arr,'<br/>'); ?></div></td>
                            </tr>

                            
                            <tr class="ftable">
                                <th class="ftable" scope="row">Vehicle Number</th>

                                <td width="14%"><div style="clear:both;"><?php echo $qryRes2['vehicle_reg_number']; ?></div></td>
                            </tr>
                            
                            <tr class="ftable">
                                <td  style="background-color:<?=$heading_color?> !important;" align="center" colspan="2">Driver's Information:</td>
                                
                            </tr>
                            <tr class="ftable">
                                <th class="ftable" width="10%" scope="row">Driver's Name:</th>
                                <td width="14%">        <div style="clear:both;">
                                        <?php echo $qryRes2['d_name']; ?>
                                    </div></td>
                            </tr>
                            <tr class="ftable">
                                <th class="ftable" scope="row">Driver's Contact#</th>

                                <td width="14%"><div style="clear:both;"><?php echo $qryRes2['d_contact']; ?></div></td>
                            </tr>
                            <tr class="ftable">
                                <th class="ftable" scope="row">Driver's CNIC #</th>

                                <td width="14%"><div style="clear:both;"><?php echo $qryRes2['d_cnic']; ?></div></td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </table>
        <br>
        <br>
    </div> 
    <table id="myTable" class="table-condensed" >
        <tr style="background-color:<?=$heading_color?> !important;">
            <th width="6%">S.No.</th>
            <th>Product</th>
            <th width="15%">Batch No.</th>
            <th width="35%">Issued To</th>
            <th width="10%" align="center">Total Quantity</th>
            <th width="10%">No of Cartons</th>
            <th width="15%">Loose Qty</th>
            <th width="15%">Expiry Date</th>
            <!--<th width="15%">Remark</th>-->
        </tr>
        <tbody>
            <?php
            $i = 1;
            $this_loose = 0 ;
            $total_loose = 0 ;
            $total_cartons =0;
            $total_qty =0;
            foreach ($data_arr as $k => $row) {
                if(!empty($row['carton_size']) && $row['carton_size']>0)
                {
                    $cartons = floor($row['quantity']/$row['carton_size']);
                    $this_loose = ($row['quantity']%$row['carton_size']);
                }
                else
                {
                    $cartons = 0 ;
                }
                $total_cartons+=$cartons;
                $total_loose+=$this_loose;
                $total_qty+=$row['quantity'];
                ?>
                <tr>
                    <td style="text-align:center;"><?php echo $i; ?></td>
                    <td><?php echo $row['itm_name']; ?></td>
                    <td><?php echo $row['batch_no']; ?></td>
                    <td><?php echo $row['wh_name']; ?></td>
                    <td style="text-align:right;"><?php echo number_format($row['quantity']); ?></td>
                    <td style="text-align:right;"><?php echo number_format($cartons) ?></td>
                    <td style="text-align:right;"><?php echo number_format($this_loose) ?></td>
                    <td style="text-align:center;"> <?php echo date("d-M-Y", strtotime($row['batch_expiry'])); ?></td>
                    <!--<td style="text-align:center;"> <?php echo "suleman" ?></td>-->
                </tr>
                <?php
                $i++;
            }
            ?>
        </tbody>
        <tfoot>
            <tr>
                <th colspan="4" style="text-align:right;">Total</th>
                <th style="text-align:right;"><?php echo number_format($total_qty); ?></th>
                <th style="text-align:right;"><?php echo number_format($total_cartons); ?></th>
                <th style="text-align:right;"><?php echo number_format($total_loose); ?></th>
                <th>&nbsp;</th>
                <!--<th>&nbsp;</th>-->
            </tr>
        </tfoot>
    </table>

    <div style="float:right; margin-top:20px;" id="printButt">
        <input type="button" name="print" value="Print" class="btn btn-warning" onclick="javascript:printCont();" />
    </div>


    <br>
    <br>    <br>
    <br>    <br>
    <br>



    <div style="width:100%; clear:both; margin-top:30px;">
        <table width="48%" cellpadding="5" style="float:left; border:0px solid #E5E5E5 !important; border-collapse:collapse;">
            <tr>
                <td><b>Store Incharge:</b></td>
            </tr>

            <tr>
                <td>Signature & Seal: ________________________________________</td>
            </tr>
        </table>
        <table width="48%" cellpadding="5" style="float:right; border:0px solid #E5E5E5 !important; border-collapse:collapse;">
            <tr>
                <td><b>Received by:</b></td>
            </tr>
            <tr>
                <td>Name: ________________________________________</td>
            </tr>
            <tr>
                <td>Signature & Seal: ________________________________________</td>
            </tr>
        </table>
    </div>



</div>
<script language="javascript">
    $(function () {
        //printCont();
    });
    function printCont()
    {
        window.print();
    }
</script>
