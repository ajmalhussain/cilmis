<?php
//echo '<pre>';print_r($_REQUEST);exit;
include("../includes/classes/Configuration.inc.php");
Login();

//echo '<pre>';print_r($_SESSION);exit;
include(APP_PATH . "includes/classes/db.php");
include(PUBLIC_PATH . "html/header.php");
include(PUBLIC_PATH . "FusionCharts/Code/PHP/includes/FusionCharts.php");
if (date('d') > 10) {
    $date = date('Y-m', strtotime("-1 month", strtotime(date('Y-m-d'))));
} else {
    $date = date('Y-m', strtotime("-2 month", strtotime(date('Y-m-d'))));
}
$sel_month = date('m', strtotime($date));
$sel_year = date('Y', strtotime($date));
$sel_stk = $sel_prov = $sel_dist = $sel_wh = $stkName = $provName = $distName = $whName = $where = $where1 = $where2 = $lvl = $whid = '';
$colspan = $header = $header1 = $header2 = $lvl = $width = $colAlign = $colType = $xmlstore = '';

?>
<style>
    .panel-actions {
  margin-top: -20px;
  margin-bottom: 0;
  text-align: right;
}
.panel-actions a {
  color:#333;
}
.panel-fullscreen {
    display: block;
    z-index: 9999;
    position: fixed;
    width: 100%;
    height: 100%;
    top: 0;
    right: 0;
    left: 0;
    bottom: 0;
    overflow: auto;
}
    
</style>
</head>
<body class="page-header-fixed page-quick-sidebar-over-content">
    <div class="page-container">
        <?php
        include PUBLIC_PATH . "html/top.php";
        include PUBLIC_PATH . "html/top_im.php";
        ?>

        <div class="page-content-wrapper">
            <div class="page-content">
                
                <div class="container-fluid">
                   
                    <div class="row">
                        <div class="col-md-12">
                            <h3 class="page-title row-br-b-wp">Activity Stats</h3>

                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                          
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                           
                                   
                    <?php

                    $activity_data = $activity_count=$wh_arr= array();

                    $qry_summary_dist= "SELECT
                            Count(tbl_stock_master.PkStockID) AS total_rcvd_v,
                            tbl_stock_master.WHIDTo,
                            tbl_stock_master.TranTypeID,
                            tbl_stock_master.TranDate,
                            tbl_warehouse.wh_name,
                            stakeholder.stkname
                        FROM
                        tbl_stock_master
                        INNER JOIN tbl_warehouse ON tbl_stock_master.WHIDTo = tbl_warehouse.wh_id
                        INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
                        WHERE
                            tbl_stock_master.TranTypeID = 1  AND
                            stakeholder.lvl < 7
                        GROUP BY 
                            tbl_stock_master.WHIDTo,
                            tbl_stock_master.TranDate,
                            tbl_stock_master.TranTypeID
                        ORDER BY
                            stakeholder.lvl,
                            tbl_warehouse.prov_id,
                            tbl_warehouse.stkid,
                            tbl_warehouse.wh_name ASC
                    ";
                    //Query result
                    //echo $qry_summary_dist;
                    $Res2 =mysql_query($qry_summary_dist);

                    while($row = mysql_fetch_assoc($Res2))
                    {
                        $wh_arr[$row['WHIDTo']]=$row['wh_name'].' - '.$row['stkname'];
                        $tr_d = date('Y-m-d',strtotime($row['TranDate']));
                        $activity_data[$row['WHIDTo']]['rcvd'][$tr_d]=$row['total_rcvd_v'];

                        $compare_date = date('Y-m-d',strtotime('-30 days'));
                        //echo '</br>Compare:'.$tr_d.' and '.$compare_date.' END';

                        @$activity_count[$row['WHIDTo']]['rcvd']['all'] += $row['total_rcvd_v'];
                        if($tr_d >= $compare_date)
                        {
                            @$activity_count[$row['WHIDTo']]['rcvd']['30_days'] += $row['total_rcvd_v'];
                        }
                        $compare_date2 = date('Y-m-d',strtotime('-7 days'));
                        if($tr_d >= $compare_date2)
                        {
                            @$activity_count[$row['WHIDTo']]['rcvd']['7_days'] += $row['total_rcvd_v'];
                        }
                        $compare_date3 = date('Y-m-d');
                        if($tr_d == $compare_date3)
                        {
                            @$activity_count[$row['WHIDTo']]['rcvd']['today'] += $row['total_rcvd_v'];
                        }
                        $compare_date4 = date('Y-m-d',strtotime('-90 days'));
                        if($tr_d >= $compare_date4)
                        {
                            @$activity_count[$row['WHIDTo']]['rcvd']['90_days'] += $row['total_rcvd_v'];
                        }
                    }

                    $qry_summary_dist= "SELECT
                    Count(tbl_stock_master.PkStockID) AS total_issued_v,
                    tbl_stock_master.WHIDFrom,
                    tbl_stock_master.TranTypeID,
                    tbl_stock_master.TranDate,
                    tbl_warehouse.wh_name
                    FROM
                    tbl_stock_master
                    INNER JOIN tbl_warehouse ON tbl_stock_master.WHIDFrom = tbl_warehouse.wh_id
                    WHERE
                    tbl_stock_master.TranTypeID = 2  
                    GROUP BY 
                    tbl_stock_master.WHIDFrom,
                    tbl_stock_master.TranDate,
                    tbl_stock_master.TranTypeID

                    ORDER BY
                    tbl_stock_master.WHIDFrom ASC

                    ";
                    //Query result
                    //echo $qry_summary_dist;
                    $Res2 =mysql_query($qry_summary_dist);

                    while($row = mysql_fetch_assoc($Res2))
                    {
                        if(empty($wh_arr[$row['WHIDFrom']]))$wh_arr[$row['WHIDFrom']]=$row['wh_name'];
                        $tr_d = date('Y-m-d',strtotime($row['TranDate']));
                        $activity_data[$row['WHIDFrom']]['issued'][$tr_d]=$row['total_issued_v'];

                        $compare_date = date('Y-m-d',strtotime('-30 days'));
                        @$activity_count[$row['WHIDFrom']]['issued']['all'] += $row['total_issued_v'];
                        if($tr_d >= $compare_date)
                        {
                            @$activity_count[$row['WHIDFrom']]['issued']['30_days'] += $row['total_issued_v'];
                        }
                        $compare_date2 = date('Y-m-d',strtotime('-7 days'));
                        if($tr_d >= $compare_date2)
                        {
                            @$activity_count[$row['WHIDFrom']]['issued']['7_days'] += $row['total_issued_v'];
                        }
                        $compare_date3 = date('Y-m-d');
                        if($tr_d == $compare_date3)
                        {
                            @$activity_count[$row['WHIDFrom']]['issued']['today'] += $row['total_issued_v'];
                        }
                        $compare_date4 = date('Y-m-d',strtotime('-90 days'));
                        if($tr_d >= $compare_date4)
                        {
                            @$activity_count[$row['WHIDFrom']]['issued']['90_days'] += $row['total_issued_v'];
                        }
                    }


                    $qry_summary_dist= "SELECT
                    Count(tbl_stock_master.PkStockID) AS total_adjusted_v,
                    tbl_stock_master.WHIDFrom,
                    tbl_stock_master.TranTypeID,
                    tbl_stock_master.TranDate,
                    tbl_warehouse.wh_name
                    FROM
                    tbl_stock_master
                    INNER JOIN tbl_warehouse ON tbl_stock_master.WHIDFrom = tbl_warehouse.wh_id
                    WHERE
                    tbl_stock_master.TranTypeID >2  
                    GROUP BY 
                    tbl_stock_master.WHIDFrom,
                    tbl_stock_master.TranDate,
                    tbl_stock_master.TranTypeID


                    ORDER BY
                    tbl_stock_master.WHIDFrom ASC
                    ";
                    //Query result
                    //echo $qry_summary_dist;
                    $Res2 =mysql_query($qry_summary_dist);

                    while($row = mysql_fetch_assoc($Res2))
                    {
                        $tr_d = date('Y-m-d',strtotime($row['TranDate']));
                        $activity_data[$row['WHIDFrom']]['adjusted'][$tr_d]=$row['total_adjusted_v'];


                        $compare_date = date('Y-m-d',strtotime('-30 days'));
                        @$activity_count[$row['WHIDFrom']]['adjusted']['all'] += $row['total_adjusted_v'];
                        if($tr_d >= $compare_date)
                        {
                            @$activity_count[$row['WHIDFrom']]['adjusted']['30_days'] += $row['total_adjusted_v'];
                        }
                        $compare_date2 = date('Y-m-d',strtotime('-7 days'));
                        if($tr_d >= $compare_date2)
                        {
                            @$activity_count[$row['WHIDFrom']]['adjusted']['7_days'] += $row['total_adjusted_v'];
                        }
                        $compare_date3 = date('Y-m-d');
                        if($tr_d == $compare_date3)
                        {
                            @$activity_count[$row['WHIDFrom']]['adjusted']['today'] += $row['total_adjusted_v'];
                        }
                        $compare_date4 = date('Y-m-d',strtotime('-90 days'));
                        if($tr_d >= $compare_date4)
                        {
                            @$activity_count[$row['WHIDFrom']]['adjusted']['90_days'] += $row['total_adjusted_v'];
                        }
                    }
                    //echo '<pre>';
                    //print_r($activity_data);
                    //print_r($activity_count);
                    //exit;
                    
                    
                    ?>
                    <div class="row">
                            <div class="tile double bg-purple-studio" style="height:80px !important;">
                                <div class="tile-object">
                                    <div class="h4 ">Vouchers Created All Time</div>
                                </div>
                            </div>
                            <div class="tile   bg-green" style="height:80px !important;">
					<div class="tile-object">
                                    <div class="h4 ">Last 90 Days</div>
					</div>
				</div>
                            <div class="tile   bg-blue-steel" style="height:80px !important;">
					<div class="tile-object">
                                    <div class="h4 ">Last 30 Days</div>
					</div>
				</div>
                            <div class="tile   bg-yellow-saffron" style="height:80px !important;">
					<div class="tile-object">
                                    <div class="h4 ">Last 7 Days</div>
					</div>
				</div>
                            <div class="tile   bg-red-intense" style="height:80px !important;">
					<div class="tile-object">
                                    <div class="h4 ">Today</div>
					</div>
				</div>
                        </div>        
                    <?php
                    foreach($wh_arr as $wh_id => $val_wh)
                        { 
                            ?>
                        <div class="row">
                        <div class="">
                            <div class=" bg-green-meadow" style="height:30px !important;  "><?=$wh_id.'-'.$val_wh?></div>
                        </div>
                        </div>
                        <div class="row">
                            <div class="tile double bg-purple-studio" style="height:80px !important;">
					 
					<div class="tile-object">
                                            <div class="name "><p>Issued <br/> Recieved <br/> Adjusted</p></div>
                                            <div class="number">
                                                <p>
                                                <?=(!empty($activity_count[$wh_id]['issued']['all'])?$activity_count[$wh_id]['issued']['all']:'-')?><br/> 
                                                <?=(!empty($activity_count[$wh_id]['rcvd']['all'])?$activity_count[$wh_id]['rcvd']['all']:'-')?><br/> 
                                                <?=(!empty($activity_count[$wh_id]['adjusted']['all'])?$activity_count[$wh_id]['adjusted']['all']:'-')?>
                                            </p></div>
					
					</div>
				</div>
                            <div class="tile   bg-green" style="height:80px !important;">
					 
					<div class="tile-object">
                                            <div class="name "><p>Issued <br/> Recieved <br/> Adjusted</p></div>
                                            <div class="number"><p>
                                                <?=(!empty($activity_count[$wh_id]['issued']['90_days'])?$activity_count[$wh_id]['issued']['90_days']:'-')?><br/> 
                                                <?=(!empty($activity_count[$wh_id]['rcvd']['90_days'])?$activity_count[$wh_id]['rcvd']['90_days']:'-')?><br/> 
                                                <?=(!empty($activity_count[$wh_id]['adjusted']['90_days'])?$activity_count[$wh_id]['adjusted']['90_days']:'-')?>
                                            </p></div>
					
					</div>
				</div>
                            <div class="tile   bg-blue-steel" style="height:80px !important;">
					 
					<div class="tile-object">
                                            <div class="name "><p>Issued <br/> Recieved <br/> Adjusted</p></div>
                                            <div class="number"><p>
                                                <?=(!empty($activity_count[$wh_id]['issued']['30_days'])?$activity_count[$wh_id]['issued']['30_days']:'-')?><br/> 
                                                <?=(!empty($activity_count[$wh_id]['rcvd']['30_days'])?$activity_count[$wh_id]['rcvd']['30_days']:'-')?><br/> 
                                                <?=(!empty($activity_count[$wh_id]['adjusted']['30_days'])?$activity_count[$wh_id]['adjusted']['30_days']:'-')?>
                                            </p></div>
					
					</div>
				</div>
                            <div class="tile   bg-yellow-saffron" style="height:80px !important;">
					 
					<div class="tile-object">
                                            <div class="name "><p>Issued <br/> Recieved <br/> Adjusted</p></div>
                                            <div class="number"><p>
                                                <?=(!empty($activity_count[$wh_id]['issued']['7_days'])?$activity_count[$wh_id]['issued']['7_days']:'-')?><br/> 
                                                <?=(!empty($activity_count[$wh_id]['rcvd']['7_days'])?$activity_count[$wh_id]['rcvd']['7_days']:'-')?><br/> 
                                                <?=(!empty($activity_count[$wh_id]['adjusted']['7_days'])?$activity_count[$wh_id]['adjusted']['7_days']:'-')?>
                                            </p></div>
					
					</div>
				</div>
                            <div class="tile   bg-red-intense" style="height:80px !important;">
					 
					<div class="tile-object">
                                            <div class="name "><p>Issued <br/> Recieved <br/> Adjusted</p></div>
                                            <div class="number"><p>
                                                <?=(!empty($activity_count[$wh_id]['issued']['today'])? $activity_count[$wh_id]['issued']['today']:'-')?><br/> 
                                                <?=(!empty($activity_count[$wh_id]['rcvd']['today'])?   $activity_count[$wh_id]['rcvd']['today']:'-')?><br/> 
                                                <?=(!empty($activity_count[$wh_id]['adjusted']['today'])?$activity_count[$wh_id]['adjusted']['today']:'-')?>
                                            </p></div>
					
					</div>
				</div>
                        </div>
                        
                        <?php

                        }
                    
                    ?>
                            <table   class="table table-bordered table-hover table-condensed">

                        <?php 
                        foreach($wh_arr as $wh_id => $val_wh)
                        { 
                            //start of foreach loop wh_arr , issue , recieved , adjusted.
                            ?>
                        <tr>
                            <td colspan="99" bgcolor="grey"><?=$wh_id.'-'.$val_wh?></td>
                        </tr>
                        <tr>
                            <td>Type</td>
                            <td>All Time Total</td>
                            <td>Last 90 Days</td>
                            <td>Last 30 Days</td>
                            <td>Last 7 Days</td>
                            <td>Today</td>
                        </tr>
                        <tr>
                            <td>Vouchers Issued</td>
                            <td><?=(!empty($activity_count[$wh_id]['issued']['all'])?$activity_count[$wh_id]['issued']['all']:'-')?></td>
                            <td><?=(!empty($activity_count[$wh_id]['issued']['90_days'])?$activity_count[$wh_id]['issued']['90_days']:'-')?></td>
                            <td><?=(!empty($activity_count[$wh_id]['issued']['30_days'])?$activity_count[$wh_id]['issued']['30_days']:'-')?></td>
                            <td><?=(!empty($activity_count[$wh_id]['issued']['7_days'])?$activity_count[$wh_id]['issued']['7_days']:'-')?></td>
                            <td><?=(!empty($activity_count[$wh_id]['issued']['today'])?$activity_count[$wh_id]['issued']['today']:'-')?></td>
                        </tr>
                        <tr>
                            <td>Vouchers Received</td>
                            <td><?=(!empty($activity_count[$wh_id]['rcvd']['all'])?$activity_count[$wh_id]['rcvd']['all']:'-')?></td>
                            <td><?=(!empty($activity_count[$wh_id]['rcvd']['90_days'])?$activity_count[$wh_id]['rcvd']['90_days']:'-')?></td>
                            <td><?=(!empty($activity_count[$wh_id]['rcvd']['30_days'])?$activity_count[$wh_id]['rcvd']['30_days']:'-')?></td>
                            <td><?=(!empty($activity_count[$wh_id]['rcvd']['7_days'])?$activity_count[$wh_id]['rcvd']['7_days']:'-')?></td>
                            <td><?=(!empty($activity_count[$wh_id]['rcvd']['today'])?$activity_count[$wh_id]['rcvd']['today']:'-')?></td>
                        </tr>
                        <tr>
                            <td>Vouchers Adjusted</td>
                            <td><?=(!empty($activity_count[$wh_id]['adjusted']['all'])?$activity_count[$wh_id]['adjusted']['all']:'-')?></td>
                            <td><?=(!empty($activity_count[$wh_id]['adjusted']['90_days'])?$activity_count[$wh_id]['adjusted']['90_days']:'-')?></td>
                            <td><?=(!empty($activity_count[$wh_id]['adjusted']['30_days'])?$activity_count[$wh_id]['adjusted']['30_days']:'-')?></td>
                            <td><?=(!empty($activity_count[$wh_id]['adjusted']['7_days'])?$activity_count[$wh_id]['adjusted']['7_days']:'-')?></td>
                            <td><?=(!empty($activity_count[$wh_id]['adjusted']['today'])?$activity_count[$wh_id]['adjusted']['today']:'-')?></td>
                        </tr>
                        <?php

                        }
                        ?>
                    </table> 

                        </div>
                    </div>
                        
                </div>
            </div>
        </div>
    </div>

    <?php 
    //Including footer file
    include PUBLIC_PATH . "/html/footer.php"; ?>

    <script language="Javascript" src="<?php echo PUBLIC_URL; ?>FusionCharts/Charts/FusionCharts.js"></script>
    <script language="Javascript" src="<?php echo PUBLIC_URL; ?>FusionCharts/themes/fusioncharts.theme.fint.js"></script>
    <script type="text/javascript">
        $(document).ready(function () {
    //Toggle fullscreen
    $(".fullscreen").click(function (e) {
        e.preventDefault();
        
        var $this = $(this);
    
        if ($this.children('i').hasClass('glyphicon-resize-full'))
        {
            $this.children('i').removeClass('glyphicon-resize-full');
            $this.children('i').addClass('glyphicon-resize-small');
        }
        else if ($this.children('i').hasClass('glyphicon-resize-small'))
        {
            $this.children('i').removeClass('glyphicon-resize-small');
            $this.children('i').addClass('glyphicon-resize-full');
        }
        $(this).closest('.portlet').toggleClass('panel-fullscreen');
    });
});

                $(function() {
			if(!$('#accordion').hasClass('page-sidebar-menu-closed'))
                        {
                            $(".sidebar-toggler").trigger("click");
                        }
		})
                
                
		$(function() {
			//loadDashlets();

                        if(!$('#accordion').hasClass('page-sidebar-menu-closed'))
                        {
                            $(".sidebar-toggler").trigger("click");
                        }
                        
                        $('.btn_load_dashlet').click(function(){
                            var dashlet = $(this).data('dashlet');
                            load_this_dashlet('dashlet_'+dashlet);
                        });
                       
                       
                        
		})
                function load_this_dashlet(id){
                    
                    var url = $('#'+id).attr('href');
                    var id = $('#'+id).attr('id');

                    var dataStr='';
                    $('#' + id).html("<center><div id='loadingmessage'><img src='<?php echo PUBLIC_URL; ?>images/ajax-loader.gif'/></div></center>");
                    $.ajax({
                        type: "POST",
                        url: '<?php echo APP_URL; ?>trends/' + url,
                        data: dataStr,
                        dataType: 'html',
                        success: function(data) {
                                $("#" + id).html(data);
                        }
                    });
                }
                
		function loadDashlets(stkId='1')
		{
			$('.dashlet_graph').each(function(i, obj) {
				
				var url = $(this).attr('href');
				var id = $(this).attr('id');
				
                                var dataStr='';
                                dataStr += 'province=' + $('#province').val();
                                //dataStr += '&prov_name=' + $('#prov_name').val();
                                dataStr += '&from_date=' + $('#report_year').val()+'-'+ $('#report_month').val()+'-01';
                                //dataStr += '&to_date=' + $('#to_date').val();
                                dataStr += '&dist=' + $('#district_id').val();
                                //dataStr += '&dist_name='    + $('#dist_name').val();
                                dataStr += '&stk='          + $('#stk_sel').val();
                                dataStr += '&products='     + $('#products').val();
                                dataStr += '&warehouse='    + $('#warehouse_id').val();

                                $('#' + id).html("<center><div id='loadingmessage'><img src='<?php echo PUBLIC_URL; ?>images/ajax-loader.gif'/></div></center>");

                                $.ajax({
                                        type: "POST",
                                        url: '<?php echo APP_URL; ?>trends/' + url,
                                        data: dataStr,
                                        dataType: 'html',
                                        success: function(data) {
                                                $("#" + id).html(data);
                                        }
                                });
				
			});
                        
                        
		}
                
    </script>
    
    <script>
        $(function() {
            showDistricts('<?php echo $sel_prov; ?>', '<?php echo $sel_stk; ?>');
            showStores('<?php echo $sel_dist; ?>');

            $('#province, #stk_sel').change(function(e) {
                $('#district').html('<option value="">All</option>');
                $('#warehouse').html('<option value="">Select</option>');
                showDistricts($('#province').val(), $('#stk_sel').val());
            });
            $('#stk_sel').change(function(e) {
                $('#warehouse').html('<option value="">All</option>');
            });

            $(document).on('change', '#province, #stk_sel, #district', function() {
                showStores($('#district option:selected').val());
            })
        })
        function showDistricts(prov, stk) {
            if (stk != '' && prov != '')
            {
                $.ajax({
                    type: 'POST',
                    url: 'my_report_ajax.php',
                    data: {provId: prov, stkId: stk, distId: '<?php echo $sel_dist; ?>', showAll: 1},
                    success: function(data) {
                        $("#districts").html(data);
                    }
                });
            }
        }
        function showStores(dist) {
            var stk = $('#stk_sel').val();
            if (stk != '' && dist != '')
            {
                $.ajax({
                    type: 'POST',
                    url: 'my_report_ajax.php',
                    data: {distId: dist, stkId: stk, whId: '<?php echo $sel_wh; ?>'},
                    success: function(data) {
                        $("#stores").html(data);
                    }
                });
            }
        }
    </script>
    
</body>
<!-- END BODY -->
</html>