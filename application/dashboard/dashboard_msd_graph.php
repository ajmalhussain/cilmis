<?php
include("../includes/classes/Configuration.inc.php");
Login();
//include db
include(APP_PATH . "includes/classes/db.php");
//include functions
include APP_PATH . "includes/classes/functions.php";

//include FusionCharts
include(PUBLIC_PATH . "FusionCharts/Code/PHP/includes/FusionCharts.php");


$where_hf = '';
$product = (!empty($_REQUEST['product']) ? $_REQUEST['product'] : '');
//$from_date = (!empty($_REQUEST['from_date']) ? $_REQUEST['from_date'] : '');
//$from_date = date('Y-m-d', strtotime($from_date));
$to_date=(!empty($_REQUEST['to_date']) ? $_REQUEST['to_date'] : '');
$to_date = date('Y-m-d', strtotime($to_date));
?>

<div class="widget widget-tabs" style="border:0px;">
    <div class="widget-body" id="a2" style='background-color: white;'>
        <ul class="list-inline panel-actions" style='float: right;' >
            <li><a   id="panel-fullscreen_a1" role="button" title="Toggle fullscreen"><i class="glyphicon glyphicon-resize-full"></i></a></li>
        </ul>
        <h3 style="text-align: center;">MSD Stores Comparison</h3>
        <?php
        $stock_arr = array();
        $qry_wh="SELECT
tbl_warehouse.wh_id,
tbl_warehouse.wh_name,
tbl_warehouse.dist_id,
tbl_warehouse.prov_id
FROM
tbl_warehouse
INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
WHERE
tbl_warehouse.prov_id = 1 AND
stakeholder.lvl = 2 AND tbl_warehouse.is_allowed_im = 1 AND
tbl_warehouse.stkid IN (7,145)"; 
        $qryRes_wh = mysql_query($qry_wh); 
        while ($row = mysql_fetch_assoc($qryRes_wh)) {
            $wh_array[$row['wh_id']]=$row['wh_id'];
            $wh_name_array[$row['wh_id']]=$row['wh_name'];
           
        }
        $qry_soh = "SELECT
itminfo_tab.itm_id,
Sum(tbl_stock_detail.Qty) AS soh,
tbl_warehouse.wh_name,
tbl_warehouse.wh_id,
itminfo_tab.itm_name,
stock_batch.batch_no,
tbl_stock_master.TranDate AS last_update
FROM
itminfo_tab
INNER JOIN stock_batch ON itminfo_tab.itm_id = stock_batch.item_id
INNER JOIN tbl_stock_detail ON stock_batch.batch_id = tbl_stock_detail.BatchID
INNER JOIN tbl_stock_master ON tbl_stock_detail.fkStockID = tbl_stock_master.PkStockID
INNER JOIN tbl_trans_type ON tbl_stock_master.TranTypeID = tbl_trans_type.trans_id
INNER JOIN tbl_warehouse ON stock_batch.wh_id = tbl_warehouse.wh_id
WHERE
DATE_FORMAT(
				tbl_stock_master.TranDate,
				'%Y-%m-%d'
			) <= '$to_date' AND
((tbl_stock_master.TranTypeID = 2) OR
(tbl_stock_master.TranTypeID = 1) OR
(tbl_stock_master.TranTypeID > 2)) AND
tbl_stock_master.temp = 0 AND
stock_batch.wh_id IN (".implode($wh_array,',').") AND
itminfo_tab.itm_id = $product
GROUP BY
	itminfo_tab.itm_id,wh_id 
ORDER BY
itm_name ASC";

//        print_r($qry_soh);
//        exit;
        $qryRes = mysql_query($qry_soh);
        $num_rows = mysql_num_rows($qryRes);
        while ($row = mysql_fetch_assoc($qryRes)) {
            $stock_arr[$row['wh_id']]['soh'] = $row['soh']; 
            
        }
//        print_r($stock_arr);exit;
        $xmlstore = "<chart  theme='zune' yAxisMaxValue='100' labelDisplay='auto' showValues='1' >";
	
        $xmlstore .= "<categories>";
        foreach ($wh_name_array as $wh_id => $wh_name) {
            $xmlstore .= "<category label='$wh_name' />";
        }
//        print_r($xmlstore);exit;
        $xmlstore .= "</categories>";
        $xmlstore .= "<dataset seriesName='Stock on Hand'>";
         foreach ($wh_name_array as $wh_id => $wh_name) {
            $xmlstore .= '<set value="' .  @$stock_arr[$wh_id]['soh'] . '"    />';
        }
        $xmlstore .= "</dataset>";
         
        $xmlstore .= ' </chart>';
//        print_r($xmlstore);
//        exit;
        FC_SetRenderer('javascript');
        echo renderChart(PUBLIC_URL . "FusionCharts/Charts/MSColumn2D.swf", "", $xmlstore, 'dist_im_bar', '100%', 300, false, false);
        ?>

        <table class="table table-bordered table-condensed table-hover">
            <tr class="bg-green">
                <td>#</td>
                <td>Warehouse</td>
                <td>Quantity</td>
            </tr>
            <?php
            $c=1;
                foreach($wh_name_array as $wh_id => $wh_name){
                    @$qty = number_format($stock_arr[$wh_id]['soh']);
                    echo '
                        <tr>
                            <td>'.$c++.'</td>
                            <td>'.$wh_name.'</td>
                            <td align="right">'.$qty.'</td>
                        </tr>
                    ';
                }
            ?>
        </table>
    </div>
</div>


<script>
    $(document).ready(function () {
        //Toggle fullscreen
        $("#panel-fullscreen_a1").click(function (e) {
            e.preventDefault();
//        console.log('into js');
            var $this = $(this);

            if ($this.children('i').hasClass('glyphicon-resize-full'))
            {
                $this.children('i').removeClass('glyphicon-resize-full');
                $this.children('i').addClass('glyphicon-resize-small');
            } else if ($this.children('i').hasClass('glyphicon-resize-small'))
            {
                $this.children('i').removeClass('glyphicon-resize-small');
                $this.children('i').addClass('glyphicon-resize-full');
            }
            $(this).closest('div').toggleClass('panel-fullscreen');
        });
    });


</script>