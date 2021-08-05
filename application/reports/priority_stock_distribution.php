<?php
include("../includes/classes/AllClasses.php");
include(PUBLIC_PATH . "html/header.php");
$title = "Batch Management";
$items = $objManageItem->GetAllManageItem();


$sql = "SELECT
					stock_batch.batch_id,
					stock_batch.batch_no,
					stock_batch.batch_expiry,
					stock_batch.`status`,
					Sum(tbl_stock_detail.Qty) AS BatchQty,
					itminfo_tab.itm_name,
					tbl_itemunits.UnitType,
					tbl_warehouse.wh_name AS funding_source,
                                        IFNULL(manu.stkname,'N/A') manufacturer,
                                        stakeholder_item.quantity_per_pack as qty_carton,
                                        stakeholder_item.carton_per_pallet,
                                        stakeholder_item.unit_price,
                                        stock_batch.dtl,
                                        stock_batch.phy_inspection,
                                         itminfo_tab.itm_id
				FROM
					tbl_stock_detail
				INNER JOIN tbl_stock_master ON tbl_stock_detail.fkStockID = tbl_stock_master.PkStockID
				INNER JOIN stock_batch ON stock_batch.batch_id = tbl_stock_detail.BatchID
				INNER JOIN itminfo_tab ON stock_batch.item_id = itminfo_tab.itm_id
				LEFT JOIN tbl_itemunits ON itminfo_tab.itm_type = tbl_itemunits.UnitType
				INNER JOIN tbl_trans_type ON tbl_stock_master.TranTypeID = tbl_trans_type.trans_id
				LEFT JOIN tbl_warehouse ON stock_batch.funding_source = tbl_warehouse.wh_id
                                LEFT JOIN stakeholder_item ON stock_batch.manufacturer = stakeholder_item.stk_id
                                LEFT JOIN stakeholder AS manu ON stakeholder_item.stkid = manu.stkid";
$where[] = " stock_batch.`wh_id` = " . $_SESSION['user_warehouse'] . "";
$where[] = " stock_batch.Qty <> 0";
if (!empty($where)) {
    $sql .= " WHERE " . implode(" AND ", $where);
}
$sql .= " group by stock_batch.item_id, stock_batch.batch_id, stock_batch.batch_expiry, stock_batch.status "
        . "ORDER BY itminfo_tab.itm_name , stock_batch.batch_expiry";
//query result
//echo $sql;exit;
$result = mysql_query($sql);
$data = $items_list= array();
if (!empty($result) && mysql_num_rows($result) > 0) {
    while ($row = mysql_fetch_assoc($result)) {
        $data[$row['itm_id']][] = $row;
        $items_list[$row['itm_id']] = $row['itm_name'];
    }
}
?>
</head><!-- END HEAD -->

<body class="page-header-fixed page-quick-sidebar-over-content" >
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

                <!-- BEGIN PAGE HEADER-->

                <div class="row">
                    <div class="col-md-12">
                        <div class="widget" data-toggle="collapse-widget">
                            <div class="widget-head">
                                <h3 class="heading">Priority Stock Distribution</h3>
                            </div>
                            <div class="widget-body"> 
                                <table class=" table   table-bordered table-condensed">
                                    <thead>
                                        <tr>
                                            <th width="60">Sr. No.</th>
                                            <th>Product</th>
                                            <th>Batch No.</th>
                                            <th>Manufacturer</th>
                                            <th>Expiry Date</th>
                                            <th>Priority</th>
                                            <th>Quantity</th>
                                            <th>Unit</th>
                                            <th>Cartons</th>
                                            <th>Price</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                <?php
                                //check if result rxists
                                if (!empty($data) ) {
                                    $i = 1;
                                    //fetch result
                                    foreach($data as $item_id => $item_data) {
                                        echo '<tr style="background-color:#c5b1d3 !important;">'
                                                . '<td colspan="99">'.$items_list[$item_id].'</td>'
                                                . '</tr>';
                                    foreach($item_data as $k => $row_assoc) {
                                        $row = (object)$row_assoc;
                                        $price = '';
                                        if (!empty($row->unit_price) && $row->unit_price > 0 && $row->BatchQty) {
                                            $price = $row->BatchQty * $row->unit_price;
                                        }
                                        
                                        $one_month_from_now = date('Y-m-d',strtotime('+1 month'));
                                        $three_month_from_now = date('Y-m-d',strtotime('+3 month'));
                                        $six_month_from_now = date('Y-m-d',strtotime('+6 month'));
                                        $one_year_from_now = date('Y-m-d',strtotime('+1 year'));
                                        //echo $one_month_from_now;exit;
                                        
                                        $row_color ='';
                                        $priority_text ='';
                                        if($row->batch_expiry <=date('Y-m-d')){
                                            $row_color ='#fc6767';
                                            $priority_text ='Un-usable';
                                        }elseif($row->batch_expiry <=$one_month_from_now){
                                            $row_color ='#ffc1c1';
                                            $priority_text ='1';
                                        }elseif($row->batch_expiry <=$three_month_from_now){
                                            $row_color ='#ffe5e5';
                                            $priority_text ='2';
                                        }elseif($row->batch_expiry <=$six_month_from_now){
                                            $row_color ='';
                                            $priority_text ='3';
                                        }elseif($row->batch_expiry <=$one_year_from_now){
                                            $row_color ='';
                                            $priority_text ='4';
                                        }else{
                                            $row_color ='';
                                            $priority_text ='5';
                                        }
                                        ?>
                                                    <!-- Table row -->
                                                    <tr class=" " style="background-color:<?=$row_color?>">
                                                        <td class="center"><?php echo $i; ?></td>
                                                        <td><?php echo $row->itm_name; ?></td>
                                                        <td><?php
                                            $pop = 'onclick="window.open(\'../msd/product-ledger-history.php?id=' . $row->batch_id . '\',\'_blank\',\'scrollbars=1,width=840,height=595\')"';
                                            echo "<a class='alert-link' " . $pop . " >" . $row->batch_no . "</a>";
                                            ?></td>
                                                        <td><?php echo $row->manufacturer; ?></td>
                                                        <td class="editableSingle expiry id<?php echo $row->batch_id; ?>"><?php echo date("Y-M-d", strtotime($row->batch_expiry)); ?></td>
                                                        <td class="right"><?php echo $priority_text; ?></td>
                                                        <td class="right"><?php echo number_format($row->BatchQty); ?></td>
                                                        <td class="right"><?php echo $row->UnitType ?></td>
                                                        <td class="right"><?php
                                                    //carton qty
                                                    $cartonQty = $row->BatchQty / $row->qty_carton;
                                                    echo (floor($cartonQty) != $cartonQty) ? number_format($cartonQty, 2) : number_format($cartonQty);
                                        ?></td>
                                                <td class="right"><?php echo (!empty($price) ? number_format($price) : '') ?></td>
                                                <td id="batch<?php echo $row->batch_id; ?>-status">&nbsp; <?php echo $row->status; ?></td>

                                            </tr>
                                        <?php
                                        $i++;
                                    }
                                }
                                }
                                ?>
                                    </tbody>
                                </table>
                                <div style="clear:both"></div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
<?php
//include footer
include PUBLIC_PATH . "/html/footer.php";
?>
    <script type="text/javascript" src="../../public/assets/global/plugins/select2/select2.min.js"></script>
    <script type="text/javascript">
        $('.dtl_action').on('click', function () {
            var batch = $(this).data('batch-id');
            var val = $(this).data('v');

            $.ajax({
                url: 'update_batch_by_ajax.php',
                type: 'POST',
                data: {batch: batch, property: 'dtl', val, val},
                success: function (data) {
                    toastr.success('DTL Status updated.');
                }
            })

            $('.dtl_' + batch).removeClass('green');
            $('.dtl_' + batch + '_' + val).addClass('green');
            //$(this).addClass('green');
        });
        $('.phy_action').on('click', function () {
            var batch = $(this).data('batch-id');
            var val = $(this).data('v');

            $.ajax({
                url: 'update_batch_by_ajax.php',
                type: 'POST',
                data: {batch: batch, property: 'phy_inspection', val, val},
                success: function (data) {
                    toastr.success('Physical Inspection status updated.');
                }
            })

            $('.phy_' + batch).removeClass('green');
            $('.phy_' + batch + '_' + val).addClass('green');
            //$(this).addClass('green');
        });
    </script>
</body>
<!-- END BODY -->
</html>