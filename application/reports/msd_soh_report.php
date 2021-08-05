<?php
//echo '<pre>';print_r($_REQUEST);exit;
include("../includes/classes/AllClasses.php");
include(PUBLIC_PATH . "html/header.php");
if (isset($_SESSION['user_id'])) {
    $userid = $_SESSION['user_id'];
    $objwharehouse_user->m_npkId = $userid;
    $result = $objwharehouse_user->GetwhuserByIdc();
} else {
    echo "user not login or timeout";
}
$objwharehouse_user->m_stk_id = $_SESSION['user_stakeholder1'];
$objwharehouse_user->m_prov_id = $_SESSION['user_province1'];
$province = $objwharehouse_user->m_prov_id;
$wh_id = $_REQUEST['wh_id'];

$qry_wh="
(
SELECT
tbl_warehouse.wh_id,
tbl_warehouse.wh_name,
tbl_warehouse.dist_id,
tbl_warehouse.prov_id
FROM
tbl_warehouse
INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
WHERE
tbl_warehouse.prov_id = ".$province." AND
stakeholder.lvl = 2 AND tbl_warehouse.is_allowed_im = 1 AND
tbl_warehouse.stkid IN (7,145)
 ) 
UNION 

(
SELECT
	tbl_warehouse.wh_id,
	tbl_warehouse.wh_name,
	tbl_warehouse.dist_id,
	tbl_warehouse.prov_id
FROM
	tbl_warehouse
INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
WHERE
tbl_warehouse.prov_id = 1 AND
stakeholder.lvl = 3 AND
tbl_warehouse.is_allowed_im = 1 AND
tbl_warehouse.stkid = 2

)
"; 
//echo $qry_wh;exit;
        $qryRes_wh = mysql_query($qry_wh); 
        while ($row = mysql_fetch_assoc($qryRes_wh)) {
            $wh_array[$row['wh_id']]=$row['wh_id'];
            $wh_names_array[$row['wh_id']]=$row['wh_name'];
           
        }
$qry = "SELECT
			itminfo_tab.itm_name,
			SUM(stock_batch.Qty) AS soh,
			tbl_itemunits.UnitType,
                        itminfo_tab.qty_carton as qty_carton_old,
                        stakeholder_item.quantity_per_pack as qty_carton,
tbl_warehouse.wh_name
		FROM
			stock_batch
                    INNER JOIN itminfo_tab ON stock_batch.item_id = itminfo_tab.itm_id
                    LEFT JOIN tbl_itemunits ON itminfo_tab.itm_type = tbl_itemunits.UnitType
                    INNER JOIN stakeholder_item ON stock_batch.manufacturer = stakeholder_item.stk_id
                    INNER JOIN tbl_warehouse ON stock_batch.wh_id = tbl_warehouse.wh_id
		WHERE
                            ";
//echo '<pre>';print_r($_REQUEST);exit;
if(!empty($_REQUEST['wh_id']) && $_REQUEST['wh_id']>0){
    $qry .= " stock_batch.wh_id = " .$_REQUEST['wh_id']. " ";
}else{
    $qry .= " stock_batch.wh_id IN (" . implode($wh_array, ',') . ") ";
}
$qry .= "
		GROUP BY tbl_warehouse.wh_id,
			itminfo_tab.itm_id
		ORDER BY
			tbl_warehouse.wh_name,
	itminfo_tab.itm_name";

    //query result
//echo $_REQUEST['wh_id'].' --- '.$qry;exit;
    $qryRes_soh = mysql_query($qry);
    $num = mysql_num_rows($qryRes_soh);
?>

</head><!-- END HEAD -->

<body class="page-header-fixed page-quick-sidebar-over-content" onLoad="doInitGrid();">
    <!-- BEGIN HEADER -->
    <div class="page-container">
        <?php
        //include top
        include PUBLIC_PATH . "html/top.php";
        //include top_im
        include PUBLIC_PATH . "html/top_im.php";
        ?>
        <div class="page-content-wrapper">
            <div class="page-content">
                <div class="row">
                    <div class="col-md-12">
                        <h3 class="page-title row-br-b-wp">Medical Store Depot - Stock On Hand</h3>
                        <div class="widget" data-toggle="collapse-widget">
                            <div class="widget-head">
                                <h3 class="heading">Filter by</h3>
                            </div>
                            <div class="widget-body">
                                <table width="99%">
                                    <tr>
                                        <td><form action="" method="post" onSubmit="return formValidate()">
                                                <div class="col-md-12">
                                                     
                                                    
                                                    <div class="col-md-3">
                                                        <div class="control-group">
                                                            <label class="control-label">Warehouse</label>
                                                            <div class="controls">
                                                                <select name="wh_id" id="wh_id" class="form-control input-sm" >
                                                                    <option value="">All MSD Stores</option>
                                                                    <?php
                                                                    
                                                                    foreach ($wh_names_array as $this_wh_id => $wh_name) {
                                                                        ?>
                                                                        <option <?php if ($this_wh_id == $wh_id) {
                                                                        echo 'selected="selected"';
                                                                        } ?> value="<?php echo $this_wh_id; ?>"><?php echo $wh_name; ?></option>
                                                                        <?php
                                                                    }
                                                                    
                                                                    ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="col-md-2">
                                                        <div class="control-group">
                                                            <label class="control-label">&nbsp;</label>
                                                            <div class="controls">
                                                                <input type="submit" value="Go" name="submit" class="btn btn-primary input-sm"/>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">

                        <table class="table table-bordered table-condensed table-hover ">
                            <thead>
                            <tr class="bg-green">
                                <th>#</th>
                                <th>Warehouse / Store</th>
                                <th>Item / Medicine</th>
                                <th>Current Stock</th>
                                <th>Unit</th>
                                <th>Cartons</th>
                            </tr>
                            </thead>
                            <?php
                            $c=1;
                            while ($row = mysql_fetch_array($qryRes_soh)){
                                $this_cartons = 0; // batch qty DIVIDED BY qty per carton
                                if(!empty($row['qty_carton']) && $row['qty_carton'] > 0 && (!empty($row['soh'])) && $row['soh'] > 0) {
                                    $this_cartons = number_format($row['soh'] / $row['qty_carton']);
                                }
                                
                                $soh_disp = number_format($row['soh']);
                                
                                if(empty($row['soh']) || $row['soh'] == 0){
                                    $soh_disp = '<span class="label label-sm label-danger">Out of Stock</span>';
                                }
                              echo '
                                <tr>
                                    <td>'.$c++.'</td>
                                    <td class="highlight">'.$row['wh_name'].'</td>
                                    <td>'.$row['itm_name'].'</td>
                                    <td align="right">'.$soh_disp.'</td>
                                    <td>'.$row['UnitType'].'</td>
                                    <td align="right">'.$this_cartons.'</td>
                                </tr>';
                            }
                           
                            ?>
                        </table>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php
//include footer
include PUBLIC_PATH . "/html/footer.php";
?>
    <script type="text/javascript">
        
        $(function () {
                                        
                                        $('#to_date').datepicker({
                                            dateFormat: "yy-mm-dd",
                                            constrainInput: false,
                                            changeMonth: true,
                                            changeYear: true,
                                            maxDate: ''
                                        });
                                        $('#from_date').datepicker({
                                            dateFormat: "yy-mm-dd",
                                            constrainInput: false,
                                            changeMonth: true,
                                            changeYear: true,
                                            maxDate: ''
                                        });
                                    });
        </script>
</body>
<!-- END BODY -->
</html>