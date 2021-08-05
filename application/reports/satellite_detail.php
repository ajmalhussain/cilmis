<?php
ini_set('max_execution_time', 300);
include("../includes/classes/AllClasses.php");
include(PUBLIC_PATH . "html/header.php");
$rptId = 'satellite_detail';

if (isset($_REQUEST['submit'])) {
    $selProv = mysql_real_escape_string($_REQUEST['prov_sel']);
    $report_display_type = $_REQUEST['summary_or_detail'];

    $stakeholder = mysql_real_escape_string($_REQUEST['stakeholder']);
    $stakeholder = (!empty($stakeholder)?$stakeholder:1);
    $fromDate = $_REQUEST['from_date'];
//echo $fromDate;
    $toDate = $_REQUEST['to_date'];
    //get reporting date
    //$reportingDate = mysql_real_escape_string($_REQUEST['year_sel']) . '-' . $selMonth . '-01';
    $reportingDate = "BETWEEN '" . $fromDate . "' AND '" . $toDate . "'";

    $objstk->m_npkId = $stakeholder;
    $rsSql = $objstk->GetStakeholdersById();
    $stk_data = mysql_fetch_assoc($rsSql);
    //echo '<pre>';print_r($stk_data);exit;
    if ($fromDate != $toDate) {
        //reporting period							
        $reportingPeriod = "For the period of " . date('M-Y', strtotime($fromDate)) . ' to ' . date('M-Y', strtotime($toDate));
    } else {
        //reporting period	
        $reportingPeriod = "For the month of " . date('M-Y', strtotime($fromDate));
    }
    $qry = "SELECT DISTINCT
                        prov.LocName AS prov_name,
                        dist.PkLocID AS dist_id,
                        dist.LocName AS dist_name
                FROM
                        tbl_locations AS prov
                INNER JOIN tbl_locations AS dist ON dist.ParentID = prov.PkLocID
                INNER JOIN tbl_warehouse ON tbl_warehouse.dist_id = dist.PkLocID
                INNER JOIN wh_user ON wh_user.wh_id = tbl_warehouse.wh_id
                INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
                WHERE
                        prov.PkLocID = $selProv
                AND stakeholder.lvl = 3
                AND 
                tbl_warehouse.stkid = $stakeholder

                ORDER BY
                        dist_name ASC";
    $distName = array();
    $res = mysql_query($qry);
    while ($row = mysql_fetch_assoc($res)) {
        $provinceName = $row['prov_name'];
        $distName[$row['dist_id']] = $row['dist_name'];
    }

    $qry = "SELECT
                tbl_warehouse.wh_id,
                tbl_warehouse.wh_name,
                tbl_warehouse.dist_id
                FROM
                tbl_warehouse
                INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
                WHERE
                stakeholder.lvl = 7 AND
                tbl_warehouse.prov_id = $selProv AND
                tbl_warehouse.stkid = $stakeholder AND
                tbl_warehouse.is_active = 1 ";
    if($selProv==2){ $qry .= " AND tbl_warehouse.hf_type_id IN (1,2,4) ";}
    else{ $qry .= " AND tbl_warehouse.hf_type_id IN (1,2) ";}
    $qry .= " ORDER BY
                tbl_warehouse.wh_name ASC
                ";
//    echo $qry;
    $qryRes = mysql_query($qry);
    $wh_list = array();
    while ($row2 = mysql_fetch_array($qryRes)) {
        $wh_list[$row2['dist_id']][$row2['wh_id']] = $row2['wh_name'];
    }
//    echo '<pre>';print_r($wh_list);exit;
    $qryname = "SELECT
                itminfo_tab.itm_name,  
                itminfo_tab.itm_id
                from itminfo_tab
                WHERE
                itminfo_tab.itm_category = 1 AND
                itminfo_tab.method_type IS NOT NULL
                ORDER BY
                itminfo_tab.frmindex ASC
                ";
    $itemnames = mysql_query($qryname);
    $items = array();
    while ($row2 = mysql_fetch_array($itemnames)) {
        $items[$row2['itm_id']] = $row2['itm_name'];
    }

    $qryissbal = "SELECT
                        sum(tbl_hf_satellite_data.issue_balance) as issue_balance,
                        tbl_hf_satellite_data.item_id,
                        tbl_hf_satellite_data.warehouse_id,
tbl_warehouse.dist_id
                        FROM
                        tbl_hf_satellite_data
                        INNER JOIN itminfo_tab ON tbl_hf_satellite_data.item_id = itminfo_tab.itm_id
                        INNER JOIN tbl_warehouse ON tbl_warehouse.wh_id=tbl_hf_satellite_data.warehouse_id
                        WHERE
                        tbl_hf_satellite_data.issue_balance > 0
                        AND tbl_warehouse.prov_id = $selProv AND
                            tbl_warehouse.stkid = $stakeholder AND
                            tbl_hf_satellite_data.reporting_date BETWEEN '" . $fromDate . "-01' and '" . $toDate . "-01'
                                GROUP BY item_id,warehouse_id";
//echo $qryissbal;
    $qryib = mysql_query($qryissbal);
    $data = array();
    while ($row = mysql_fetch_assoc($qryib)) {
        $data[$row['warehouse_id']][$row['item_id']] = $row['issue_balance']; // add the row in to the results (data) array
    }

    $fileName = 'Form14_' . $provinceName . '_for_' . str_replace(" ", "", str_replace("'", "", str_replace("-", "", $reportingDate)));
}
?>
</head>
<body> 

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
                    <h3 class="page-title row-br-b-wp">Performance Report Of Satellite Camps</h3>
                    <div class="widget" data-toggle="collapse-widget">
                        <div class="widget-head">
                            <h3 class="heading">Filter by</h3>
                        </div>
                        <div class="widget-body">
                            <?php
//include sub_dist_form
                            include('sub_dist_form.php');
                            ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php
//if submitted
            if (isset($_REQUEST['submit'])) {
                ?>
                <table border="1" id="myTable" class="table table-bordered table-condensed" style=" border:1px solid black; ">
                    
                    <tr>
                        <td align="center" colspan="99">
                            <h4 class="center">
                                Performance Report of Satellite Camps <br>
                                <?=$reportingPeriod?>
                            </h4>
                        </td>
                    </tr>
                    <tr style="background-color:#3da24f !important; border:1px solid black; ">
                        <th rowspan="">S.No.</th>
                        <th width="15%">District</th>
                        <th width="30%"><?=(!empty($report_display_type) && $report_display_type=='detail')?'Warehouse Name':''?></th>
                        <?php
                        foreach ($items as $item_id => $item_name) {
                            ?>
                            <th width="20%"><?php echo $item_name; ?></th>

                            <?php
                        }
                        ?>
                    </tr>

                    <?php
                    $all_dist_totals = array();
                        $counter = 1;
                    //start of dist loop
                    foreach ($distName as $dist_id => $dist_name) {
                        $this_dist_totals = array();
                         if(!empty($report_display_type) && $report_display_type=='detail')
                         {
                                ?>
                            <tr style="background-color:#74c576 !important"><td colspan="99"><?php echo $dist_name; ?></td></tr>
                            <?php
                        }
                        foreach($wh_list[$dist_id] as  $wh_id => $wh_name){
                            
                                if(!empty($report_display_type) && $report_display_type=='detail')
                                {
                                ?>

                                    <tr>
                                        <td class="center"><?php echo $counter++; ?></td>
                                        <td><?php echo $dist_name; ?></td>
                                        <td><?php echo 'Satellite Camp - '.$wh_name; ?></td>
                                        <?php
                                }
                                    foreach ($items as $item_id => $item_name) {
                                            $this_val = '';

                                            if (!empty($data[$wh_id][$item_id]))
                                                $this_val = $data[$wh_id][$item_id];

                                            @$this_dist_totals[$item_id] +=$this_val;
                                            @$all_dist_totals[$item_id] +=$this_val;

                                            if(!empty($report_display_type) &&  $report_display_type=='detail')
                                            {
                                                ?>
                                                <td align="right"><?=(!empty($this_val)?number_format($this_val):'')?></td>
                                                <?php
                                            }
                                    }//end of items
                                if(!empty($report_display_type) &&  $report_display_type=='detail')
                                {
                                ?>
                                    </tr>
                                <?php 
                                }
                            }//end of $wh_list 
                    ?>        
                                <tr>
                                    <td class="center"><?=(!empty($report_display_type) && $report_display_type=='detail')?'':$counter++?></td>
                                    <td colspan="2"><b><?=$dist_name?></b></td>
                                    <?php
                                    foreach ($items as $item_id => $item_name) {
                                        $this_val = '';

                                        if (!empty($this_dist_totals[$item_id]))
                                            $this_val = $this_dist_totals[$item_id];
                                        
                                        $this_val= (double)$this_val;
                                        ?>
                                    <td align="right"><b><?php echo number_format($this_val); ?></b></td>
                                        <?php
                                    }//end of items
                                    ?>
                                </tr>
                    <?php
                    
            }//end of foreach districts
            ?>  
                                <tr style="background-color: #4b8df9">
                                    <td colspan="3"><b>Province Total</b></td>
                                    <?php
                                    foreach ($items as $item_id => $item_name) {
                                        $this_val = '';

                                        if (!empty($all_dist_totals[$item_id]))
                                            $this_val = $all_dist_totals[$item_id];
                                        
                                        $this_val= (double)$this_val;
                                        ?>
                                    <td align="right"><b><?php echo number_format($this_val); ?></b></td>
                                        <?php
                                    }//end of items
                                    
            }//end of if submit
                                    ?>
                                </tr>
</table>
        </div>
    </div>
    <?php
    include PUBLIC_PATH . "/html/footer.php";
    include ('combos.php');
    ?>
    <script>
        $(function () {
            $('#stakeholder').change(function (e) {
                $('#itm_id, #prov_sel').html('<option value="">Select</option>');

                showProvinces('');
            });
        })
        function showProvinces(pid) {
            var stk = $('#stakeholder').val();
            if (typeof stk !== 'undefined')
            {
                $.ajax({
                    url: 'ajax_stk.php',
                    type: 'POST',
                    data: {stakeholder: stk, provinceId: pid, showProvinces: 1},
                    success: function (data) {
                        $('#prov_sel').html(data);
                    }
                })
            }
        }
        $(function () {
            $('#from_date, #to_date').datepicker({
                dateFormat: "yy-mm",
                constrainInput: false,
                changeMonth: true,
                changeYear: true,
                maxDate: 0
            });
        })
    </script>
</body>
</html>