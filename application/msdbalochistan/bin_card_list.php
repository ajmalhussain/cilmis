<?php
/**
 * bin_card_list
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
include(PUBLIC_PATH."html/header.php");
//Getting area
$area = $_REQUEST['area'];
//Getting row
$row = $_REQUEST['row'];
$rack_sel = $_REQUEST['rack'];
//Title
$title = "Bin Card - Location (".$area." - ".$row;

$wh_id = $_SESSION['user_warehouse'];
$is_msd_gulberg=false;
if($wh_id==72656) $is_msd_gulberg = true;

$alphabet = range('A', 'Z');

if($is_msd_gulberg){
    $mainSQL = "SELECT	
                * FROM	(SELECT
                stock_batch.batch_expiry AS expiry,
                placements.stock_batch_id AS batchID,
                stock_batch.batch_no AS batchNo,
                stock_batch.item_id AS itemID,
                itminfo_tab.itm_name AS ItemName,
                itminfo_tab.itm_type,
                itminfo_tab.qty_carton AS qty_per_pack_old,
                placements.stock_detail_id AS DetailID,
                placement_config.location_name AS LocationName,
                placement_config.pk_id AS LocationID,
                placements.pk_id AS PlacementID,
                placement_config.warehouse_id AS wh_id,
                abs(SUM((placements.quantity))) AS Qty,
                stakeholder_item.quantity_per_pack as qty_per_pack,
                placement_config.loc_description
	FROM
			placements
	INNER JOIN placement_config ON placements.placement_location_id = placement_config.pk_id
	INNER JOIN stock_batch ON placements.stock_batch_id = stock_batch.batch_id
	INNER JOIN itminfo_tab ON stock_batch.item_id = itminfo_tab.itm_id
        INNER JOIN stakeholder_item ON stock_batch.manufacturer = stakeholder_item.stk_id AND stock_batch.item_id = stakeholder_item.stk_item
	WHERE
            placement_config.warehouse_id = " . $wh_id . " "
            . " and placement_config.location_name like  '". $area .$row."%'  ";
    if(!empty($rack_sel)){
        $mainSQL .= " AND placement_config.rack = ".$rack_sel." ";
     }
    $mainSQL .= "  ".
	"   GROUP BY placements.placement_location_id,batchNo,itemID  order BY placements.placement_location_id,itemID,batchNo) AS A WHERE	A.Qty > 0 ";
}
else{
 
    $mainSQL = "SELECT	
                * FROM	(SELECT
                stock_batch.batch_expiry AS expiry,
                placements.stock_batch_id AS batchID,
                stock_batch.batch_no AS batchNo,
                stock_batch.item_id AS itemID,
                itminfo_tab.itm_name AS ItemName,
                itminfo_tab.itm_type,
                itminfo_tab.qty_carton AS qty_per_pack_old,
                placements.stock_detail_id AS DetailID,
                placement_config.location_name AS LocationName,
                placement_config.pk_id AS LocationID,
                placements.pk_id AS PlacementID,
                placement_config.warehouse_id AS wh_id,
                abs(SUM((placements.quantity))) AS Qty,
                stakeholder_item.quantity_per_pack as qty_per_pack
	FROM
			placements
	INNER JOIN placement_config ON placements.placement_location_id = placement_config.pk_id
	INNER JOIN stock_batch ON placements.stock_batch_id = stock_batch.batch_id
	INNER JOIN itminfo_tab ON stock_batch.item_id = itminfo_tab.itm_id
        INNER JOIN stakeholder_item ON stock_batch.manufacturer = stakeholder_item.stk_id AND stock_batch.item_id = stakeholder_item.stk_item
	WHERE
            placement_config.warehouse_id = " . $wh_id . " and placement_config.location_name like  '". $area .$row."%'".
	"   GROUP BY batchNo,itemID order BY itemID) AS A WHERE	A.Qty > 0";
}
$Bincard = mysql_query($mainSQL) or die("mainSQL");

$rack_name='';
if(!empty($rack_sel)){
      
$r_sql = " SELECT
        list_detail.pk_id,
        list_detail.list_value
        FROM
        list_detail
        WHERE
        list_detail.pk_id = $rack_sel
         ";
$r_res = mysql_query($r_sql);
$r_row = mysql_fetch_assoc($r_res);
$rack_name = $r_row['list_value'];

}
?>
<div id="content_print" style="width:80% !important;">
	<div style="float:right; font-size:12px;">QR/013/01.08.12</div>
	
	<style type="text/css" media="print">
    .page
    {
     -webkit-transform: rotate(-90deg); -moz-transform:rotate(-90deg);
     filter:progid:DXImageTransform.Microsoft.BasicImage(rotation=3);
    }
	@media print
	{    
		#printButt
		{
			display: none !important;
		}
	}
</style>
	<?php
         if($is_msd_gulberg){
                $list_val = (int)$row-1;
                $row_disp = $alphabet[$list_val];
                $row_disp = ''.$row_disp;

                $msd_val =  array_search($area, $alphabet);
                $area_disp = 1+$msd_val; 
                $area_disp = ''.$area_disp; 
//                echo "(Hall - " . $area_disp . " / Zone - " . $row_disp . ")";
                $rptName = 'Bin Card </br> <span style="font-size:20px !important;">Hall - '.$area_disp.' / Zone - '.$row_disp.' '.(!empty($rack_sel)?' / Rack - '.$rack_name:'').'</span>';
            }
            else{
                $rptName = 'Bin Card - Location (Area - '.$area.' / Row # '.$row.')';
            }
    	include('report_header.php');
	?>
    <table class="table table-bordered table-condensed">
        <!-- Table heading -->
        <thead>
            <tr>
                <th width="8%">S. No.</th>
                <?=(($is_msd_gulberg)?"<th width=\"20%\">Location</th>":"")?>
                <th width="20%">Product</th>
                <th width="10%">Batch No.</th>
                <th width="12%">Quantity</th>
                <th width="8%">Unit</th>
                <th width="10%">Cartons</th>
                <th width="15%">Expiry Date</th>
            </tr>
        </thead>
        <!-- // Table heading END -->
        
        <!-- Table body -->
        <tbody>				
        <?php
        $i=1;
		$totalQty = 0;
		$totalCartons = 0;
        while ($row = mysql_fetch_array($Bincard)) {
			$totalQty += $row['Qty'];
			@$totalCartons += $row['Qty'] / $row['qty_per_pack'];
            ?>
                <tr>
                    <td style="text-align:center; font-weight:normal;"><?php echo $i++;?></td>
                    <?=(($is_msd_gulberg)?"<td>".$row["LocationName"]." (".$row['loc_description'].")"."</td>":"")?>
                    <td style="font-weight:normal;"><?php echo $row["ItemName"] ?></td>
                    <td style="font-weight:normal;"><?php echo $row["batchNo"] ?></td>
                    <td style="text-align:right; font-weight:normal;"><?php echo number_format($row["Qty"]) ?></td>
                    <td style="font-weight:normal;"><?php echo $row["itm_type"] ?></td>
                    <td style="text-align:right; font-weight:normal;"><?php echo @number_format($row["Qty"] / $row["qty_per_pack"],1) ?></td>
                    <td style="text-align:center; font-weight:normal;"><?php echo date("Y-M-d", strtotime($row["expiry"]));  ?></td>
                </tr>
			<?php
        }
        ?>
        </tbody>
        <tfoot>
            <tr>
                <th colspan="<?=(($is_msd_gulberg)?"4":"3")?>" style="text-align:right;">Total</th>
                <th style="text-align:right;"><?php echo number_format($totalQty);?></th>
                <th>&nbsp;</th>
                <th style="text-align:right;"><?php echo number_format($totalCartons,1);?></th>
                <th>&nbsp;</th>
            </tr>
        </tfoot>
    </table>
    <div style="float:left; font-size:12px;">
        <br>
        <b>Print Time:</b> <?php echo date('Y-M-d g:i a').' <b>by</b> '.$_SESSION['user_name'];?>
    </div>
    <div style="float:right; margin:20px;" id="printButt">
        <input type="button" name="print" value="Print" class="btn btn-warning" onclick="javascript:printCont();" />
    </div>
    
</div>

<!-- // Content END -->

<script language="javascript">
$(function(){
	printCont();
})
function printCont()
{
	window.print();
}
</script>