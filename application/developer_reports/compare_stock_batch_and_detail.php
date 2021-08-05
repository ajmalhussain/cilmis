<?php
ini_set('max_execution_time',0);
//echo '<pre>';print_r($_REQUEST);exit;
//
//include AllClasses
include("../includes/classes/AllClasses.php");
?>
<html>

    <head>
    <link href="../../public/assets/global/plugins/bootstrap/css/bootstrap.css" rel="stylesheet" type="text/css"/>
    </head>
    <h3 align="center">Batch wise Comparison  :  <span style="color:green">Stock_Detail and Stock_Batch</span></h3>
    <body>
        <form id="form1" name="form1" method="get" action="">
            <table width="100%" border="1" class="table table-bordered table-condensed table-hover">
            <tr>
              <td><label for="date">Batch</label></td>
              <td>
                 <input id="batch" name="batch" value="<?php echo @$_REQUEST['batch']?>" />
              <td><label for="prov">Province</label></td>
              <td>
                  <input name="prov" id="prov"  value="<?php echo @$_REQUEST['prov']?>" >
              </td>
              <td><input type="checkbox" name="show_all" id="show_all" <?=((!empty($_REQUEST['show_all']) && $_REQUEST['show_all']=='on')?' checked ':'')?> />
             </td>
            </tr>
            <tr>
              <td> </td>
              <td> </td>
              <td><label for="stk">Stakeholder</label></td>
              <td>
                 <input id="stk" name="stk" value="<?php echo @$_REQUEST['stk']?>" />
              </td>
              <td>
              <input type="submit" name="Submit" id="Submit" value="Submit" /></td>
            </tr>
          </table>
        </form>
<?php

     

if(empty($_REQUEST['Submit'])) 
{
    echo 'Select Parameters first.';
    exit;
}

@$stk = $_REQUEST['stk'];          
@$batch = $_REQUEST['batch'];
@$prov=$_REQUEST['prov'];

$and_clause = '';

if(!empty($batch)){
    $and_clause.=" AND stock_batch.batch_id = $batch";
}
if(!empty($stk)){
    $and_clause.="  AND tbl_warehouse.stkid = $stk  ";      
    
}
if(!empty($_REQUEST['prov'])){
    
    $and_clause.="  and tbl_warehouse.prov_id = $prov   ";      
    
}






$qry_one= "
        SELECT
                stock_batch.batch_id,
                stock_batch.batch_no,
                stock_batch.batch_expiry,
                stock_batch.item_id,
                stock_batch.Qty as qty_batch,
                stock_batch.`status`,
                tbl_warehouse.wh_id,
                tbl_warehouse.wh_name,
                itminfo_tab.itm_name,
                    sum(tbl_stock_detail.Qty) AS qty_detail,
tbl_warehouse.stkid,
stakeholder.stkname
        FROM
            stock_batch
        INNER JOIN tbl_warehouse ON stock_batch.wh_id = tbl_warehouse.wh_id
        INNER JOIN itminfo_tab ON stock_batch.item_id = itminfo_tab.itm_id
INNER JOIN stakeholder ON tbl_warehouse.stkid = stakeholder.stkid
        LEFT JOIN tbl_stock_detail ON stock_batch.batch_id = tbl_stock_detail.BatchID
        WHERE 
tbl_warehouse.is_allowed_im = 1
                            ";
$qry_one.=$and_clause;
$qry_one.="             GROUP BY
            stock_batch.batch_id
 ";
//Query result
//echo $qry_one;exit;
$Res2 =mysql_query($qry_one);
$raw_data = array();

while($row = mysql_fetch_assoc($Res2))
{
    $raw_data[]=$row;
    
}
//echo '<pre>';print_r($raw_data);exit;
?>
        <table border="1" class="table table-bordered table-condensed table-hover">
    <tr>
        <td>WH ID</td>
        <td>Warehouse</td>
        <td>Stk</td>
        <td>Item ID</td>
        <td>Item Name</td>
        <td>Batch ID</td>
        <td>Batch Number</td>
        <td>Expiry Date</td>
        <td>Qty - stock_Batch</td>
        <td>Qty - Stock_detail</td>
        <td>Actions</td>
       
    </tr>
    <?php
    $mismatches_count=$total_count = 0;
    foreach($raw_data as $k => $row)
    {
        if(!empty($row['qty_batch']) && !empty($row['qty_detail']) && $row['qty_batch'] != $row['qty_detail'])
        {
            $html = '';
             $html .= '<tr>';
               $html .= '<td>'.$row['wh_id'].'</td>';
               $html .= '<td>'.$row['wh_name'].'</td>';
               $html .= '<td>'.$row['stkname'].'</td>';
               $html .= '<td>'.$row['item_id'].'</td>';
               $html .= '<td>'.$row['itm_name'].'</td>';
               $html .= '<td><a target="_blank" href="../im/product-ledger-history.php?id='.$row['batch_id'].'">'.$row['batch_id'].'</a></td>';
               $html .= '<td>'.$row['batch_no'].'</td>';
               $html .= '<td>'.$row['batch_expiry'].'</td>';
               $html .= '<td>'.$row['qty_batch'].'</td>';
               $html .= '<td>'.$row['qty_detail'].'</td>';
               $html .= '<td><a target="_blank" href="fix_stock_batch.php?id='.$row['batch_id'].'">Fix It</a></td>';
            
               $html .= '</tr>';
               $mismatches_count++;
               echo $html;
        }
        $total_count++;
        
    }
   
    ?>
</table>
        <div><h3>Mismatches in this data : <?=$mismatches_count?> / <?=$total_count?></h3></div>
    </body>
</html>
