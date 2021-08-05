<?php
include("../includes/classes/AllClasses.php");

function my_clr($val){
    if(empty($val) || $val<=0){
        return 'background-color:#ffc4c4;';
    }
}
?>
<html>
<body class="page-header-fixed page-quick-sidebar-over-content" >
    <div class="page-container">
        <div class="page-content-wrapper">
            <div class="page-content">
                <div class="row">
                    <div class="col-md-12">
                        <h3 class="page-title row-br-b-wp center">
                            Manufacturer Wise Product Details
                        </h3>

                        <div class="widget">
                            <div class="widget-body">
                                <?php include('../reports/sub_dist_reports.php'); ?>
                                <div class="row"><br></div>
                                <?php
                                $qry = "SELECT
                                                distinct itminfo_tab.itm_name,
                                                stakeholder.stkname,
                                                stakeholder_item.brand_name,
                                                stakeholder_item.quantity_per_pack,
                                                stakeholder_item.carton_per_pallet,
                                                stakeholder_item.stk_id,
                                                stakeholder_item.pack_length,
                                                stakeholder_item.pack_width,
                                                stakeholder_item.pack_height,
                                                stakeholder_item.net_capacity,
                                                stakeholder_item.gross_capacity,
                                                stakeholder_item.unit_price
                                            FROM
                                                itminfo_tab
                                            INNER JOIN stakeholder_item ON itminfo_tab.itm_id = stakeholder_item.stk_item
                                            INNER JOIN stakeholder ON stakeholder_item.stkid = stakeholder.stkid
                                            INNER JOIN stock_batch ON stakeholder_item.stk_id = stock_batch.manufacturer
                                            WHERE
                                                stakeholder.stk_type_id = 3 AND
                                                stock_batch.wh_id = '".$_SESSION['user_warehouse']."'
                                            ORDER BY
                                                itminfo_tab.frmindex ASC,
                                                stkname,
                                                stakeholder_item.quantity_per_pack ASC
                                            ";
//                                    print_r($qry);
//                                    exit;
                                $res = mysql_query($qry);
                                $num = mysql_num_rows($res);
                                if ($num > 0) {
                                    ?>
                                    <table style="width:95%;margin-left: 2%;" align="center"   id="myTable" class="table table-striped table-bordered table-condensed">
                                        <thead style="background-color:lightgray">
                                            
                                            <tr>
                                                <th >#</th>
                                                <th >Product</th>
                                                <th >Manufacturer</th>
                                                <th >Brand</th>
                                                <th >Qty in One Carton</th>
                                                <th >Cartons in One Pallet</th>
                                                <th >Carton Length (cm)</th>
                                                <th >Carton Width (cm)</th>
                                                <th >Carton Height (cm)</th>
                                                <th >Net Capacity (cm<sup>3</sup>)</th>
                                                <th >Gross Capacity (cm<sup>3</sup>)</th>
                                                <th >Unit Price</th>
                                            </tr>
                                        </thead>
                                        <?php
                                        $counter = 1;
                                        while ($row = mysql_fetch_assoc($res)) {
                                            ?>
                                            <tbody>

                                                <tr>
                                                    <td><?php echo $counter++; ?></td>
                                                    <td><?php echo $row['itm_name']; ?></td>
                                                    <td><?php echo $row['stkname']; ?></td>
                                                    <td><?php echo $row['brand_name']; ?></td>
                                                    <td style="text-align: right;<?=my_clr($row['quantity_per_pack'])?>"><?php echo $row['quantity_per_pack']; ?></td>
                                                    <td style="text-align: right"><?php echo $row['carton_per_pallet']; ?></td>
                                                    <td style="text-align: right;<?=my_clr($row['pack_length'])?>"><?php echo $row['pack_length']; ?></td>
                                                    <td style="text-align: right;<?=my_clr($row['pack_width'])?>"><?php echo $row['pack_width']; ?></td>
                                                    <td style="text-align: right;<?=my_clr($row['pack_height'])?>"><?php echo $row['pack_height']; ?></td>
                                                    <td style="text-align: right;<?=my_clr($row['net_capacity'])?>"><?php echo $row['net_capacity']; ?></td>
                                                    <td style="text-align: right"><?php echo $row['gross_capacity']; ?></td>
                                                    <td style="text-align: right;<?=my_clr($row['unit_price'])?>"><?php echo number_format($row['unit_price'],2); ?></td>
                                                </tr>
                                            </tbody>
                                        <?php 
                                        }
                                        ?>
                                    </table>
                                <?php } else {
                                    ?><div style="margin-left: 15px;"><label> <?php echo 'No record found'; ?>  </label> </div><?php
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <!-- END FOOTER -->

</body>
<!-- END BODY -->
</html>