<?php
include("../includes/classes/AllClasses.php");

include(PUBLIC_PATH . "html/header.php");
$caption = "Purchase Orders - Arriving in 30 days ";
$downloadFileName = $caption . ' - ' . date('Y-m-d H:i:s');
$chart_id = 'purchase_order';
?>
<div class="page-content" style="">
<div class="container" >
<div class="widget widget-tabs" style="">    
    <div class="widget-body" >
        
<div class="widget widget-tabs" style="">    
    <div class="widget-body" >
        
        <div class="text-center"><h4>Purchase Orders - Arriving in 30 days </h4></div>
        <div  style="">
            <table class="table table-striped table-hover table-condensed">
                    <thead style="font-size: 10px">
                        <tr>
                            <th>#</th>
                            <th>PO Number</th>
                            <th>Reference No</th>
                            <th>Contract Delivery Date</th>
                            <th>Days Remaining</th>
                        </tr>
                </thead>
                <tbody  style="font-size: 10px">
                    <?php
                    
                                                   
                                                                        
                    
                    $rsSql = $objAlerts->get_po_alerts();

                   $num = mysql_num_rows($rsSql);
                   $prod_avail=0;
                   $c2=1;
                   $stock_row='';
                   $upcoming  = $past = array();
                   while ($row_2 = mysql_fetch_assoc($rsSql)) {
                       if($row_2['contract_delivery_date']>date('Y-m-d')){
                        $upcoming[] = $row_2;
                       }else{
                        $past[] = $row_2;
                       }
                   }
                   if(!empty($upcoming)){
                   foreach($upcoming as $k=>$row){
                       
                       echo '<tr>
                               <td class="center">'.$c2++.'</td>
                               <td class="center">'.$row['po_number'].'</td>
                               <td class="center">'.$row['reference_number'].'</td>
                               <td class="center">'.$row['contract_delivery_date'].'</td>
                               <td class="center">'.$row['delivering_in_days'].'</td>
                           </tr>';

                   }}else{
                       echo '<tr><td colspan="5">No Record</td></tr>';
                   }
                   ?>
                    
                </tbody>
            </table>
        </div>
        <div class="text-center"><h4>Purchase Orders - Delivery Dates In Last Month</h4></div>
        <div  style="">
            <table class="table table-striped table-hover table-condensed">
                    <thead style="font-size: 10px">
                        <tr>
                            <th>#</th>
                            <th>PO Number</th>
                            <th>Reference No</th>
                            <th>Contract Delivery Date</th>
                            <th>Days Passed</th>
                        </tr>
                </thead>
                <tbody  style="font-size: 10px">
                    <?php
                   foreach($past as $k=>$row){
                       
                       echo '<tr>
                               <td class="center">'.$c2++.'</td>
                               <td class="center">'.$row['po_number'].'</td>
                               <td class="center">'.$row['reference_number'].'</td>
                               <td class="center">'.$row['contract_delivery_date'].'</td>
                               <td class="center">'.$row['delivering_in_days'].'</td>
                           </tr>';

                   }
                    ?>   
                </tbody>
            </table>
        </div>
    </div>
</div>
         </div>
</div>
</div>
</div>