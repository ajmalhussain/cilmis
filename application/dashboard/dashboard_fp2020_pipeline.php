<?php
//echo '<pre>';print_r($_REQUEST);exit;
//Including files
include("../includes/classes/Configuration.inc.php");
include(APP_PATH."includes/classes/db.php");
include(PUBLIC_PATH."/FusionCharts/Code/PHP/includes/FusionCharts.php");
$subCaption='';

$prov_name= $_REQUEST['prov_name'];
$province = $_REQUEST['province'];
$from_date = date("Y-m-d", strtotime($_REQUEST['from_date']));
//$to_date = date("Y-m-d", strtotime($_REQUEST['to_date']));
$itm_id = $_REQUEST['itm_id'];

//Chart caption
$caption = "Stock Availability Status in Reporting Districts";
//Chart heading sub Caption

//download File Name
$downloadFileName = $caption . ' - ' . date('Y-m-d H:i:s');
//chart_id
$chart_id = 'pipeline_pie';

$qry_f = "SELECT
            funding_stk_prov.funding_source_id
            FROM
            funding_stk_prov
            WHERE
            funding_stk_prov.province_id = $province";
$res_f = mysql_query($qry_f);
$funding_stks=array();
while($row_f=mysql_fetch_assoc($res_f))
{
    $funding_stks[$row_f['funding_source_id']]=$row_f['funding_source_id'];
}


?>
<div class="widget widget-tabs">    
    <div class="widget-body">
    <a href="javascript:exportChart('<?php echo $chart_id;?>', '<?php echo $downloadFileName;?>')" style="float:right;display:none;"><img class="export_excel" src="<?php echo PUBLIC_URL;?>images/excel-16.png" alt="Export" /></a>
	<?php 
        //query for total districts of the stakeholder
        
 $qry_6 = "
                select 	itmrec_id, sum(shipment_quantity) as shipment_quantity, sum(received_qty) as received_qty,yr ,mon from (
                    SELECT
                        itminfo_tab.itmrec_id,
                        (shipments.shipment_quantity) as shipment_quantity,
                        sum(tbl_stock_detail.Qty) as received_qty,
                        year(shipments.shipment_date) as yr,
                        month(shipments.shipment_date) as mon
                    FROM
                            shipments
                    INNER JOIN tbl_locations ON shipments.procured_by = tbl_locations.PkLocID
                    INNER JOIN tbl_warehouse ON shipments.stk_id = tbl_warehouse.wh_id
                    INNER JOIN itminfo_tab ON shipments.item_id = itminfo_tab.itm_id
                    LEFT JOIN tbl_stock_master ON tbl_stock_master.shipment_id = shipments.pk_id
                    LEFT JOIN tbl_stock_detail ON tbl_stock_detail.fkStockID = tbl_stock_master.PkStockID
                    WHERE
                        shipments.shipment_date > '$from_date' 
                        AND shipments.status NOT IN ('Cancelled','Received')
                        AND shipments.stk_id IN  (".implode(',',$funding_stks).") 
                        AND shipments.item_id = $itm_id                 
                        AND shipments.wh_id = 123                
                    GROUP BY
                    	shipments.pk_id,
                        itminfo_tab.itmrec_id,
                        year(shipments.shipment_date),
                        month(shipments.shipment_date) 
                    order by 
                        year(shipments.shipment_date),
                        month(shipments.shipment_date) 
                )A GROUP BY
                itmrec_id,yr,mon
                    ";
        //echo $qry_6;exit;
            $res_6 = mysql_query($qry_6);
            
            while($row_6 = mysql_fetch_assoc($res_6))
            {
                $s_q = isset($row_6['shipment_quantity'])?$row_6['shipment_quantity']:'0';
                $r_q = isset($row_6['received_qty'])?$row_6['received_qty']:'0';
                $remaining_q = $s_q - $r_q;
                $pipeline_arr[$row_6['yr']][$row_6['mon']]=$remaining_q;
            }

  // echo '<pre>';print_r($pipeline_arr);exit;    
  


    $data_1='';
    if(!empty($pipeline_arr))
    foreach($pipeline_arr as $yr => $v)
    {
        foreach($v as $mon => $val)
        {
            $display_month = date('M-Y',strtotime($yr.'-'.$mon.'-01'));
            $data_1 .= "<set label='".$display_month."' value='".$val."' />";
        }
    }
    

    $xmlstore = "<chart showLegend='1' theme='fint' numberSuffix=' ' exportEnabled='1' exportAction='Download' caption='Pipeline Shipments' exportFileName='Capacity Occupation" . date('Y-m-d H:i:s') . "'>";
    $xmlstore .= $data_1;
    $xmlstore .= "</chart>";
    FC_SetRenderer('javascript');
    echo renderChart(PUBLIC_URL."FusionCharts/Charts/Pie2D.swf", "", $xmlstore, 'capacity_occupation', '100%', 450, false, false);
    ?>
	</div>
</div>