<?php
ini_set('max_execution_time', 300);
include("../includes/classes/AllClasses.php");
include(PUBLIC_PATH . "html/header.php");
$rptId = 'src_d';

if (isset($_REQUEST['submit'])) {
    $selProv = mysql_real_escape_string($_REQUEST['prov_sel']);
    $selDist = mysql_real_escape_string($_REQUEST['district']);
    $report_display_type = $_REQUEST['summary_or_detail'];

    $stakeholder = mysql_real_escape_string($_REQUEST['stakeholder']);
    $stakeholder = (!empty($stakeholder)?$stakeholder:1);
    $selMonth=$mon = $_REQUEST['month_sel'];
    $selYear=$year = $_REQUEST['year_sel'];
    $fromDate = $year.'-'.$mon.'-01';
//echo $fromDate;
//    $toDate = $_REQUEST['to_date'];
    //get reporting date
    //$reportingDate = mysql_real_escape_string($_REQUEST['year_sel']) . '-' . $selMonth . '-01';
    $reportingDate = "  = '" . $fromDate . "' ";

    $objstk->m_npkId = $stakeholder;
    $rsSql = $objstk->GetStakeholdersById();
    $stk_data = mysql_fetch_assoc($rsSql);
    //echo '<pre>';print_r($stk_data);exit;
   //reporting period	
        $reportingPeriod = "For the month of " . date('M-Y', strtotime($fromDate));
   
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
                            AND dist.PkLocID = $selDist
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
                tbl_warehouse.dist_id = $selDist AND
                tbl_warehouse.stkid = $stakeholder AND
                tbl_warehouse.is_active = 1 ";
    if($stakeholder == 1){
            if($selProv==2){ $qry .= " AND tbl_warehouse.hf_type_id IN (1,2,4) ";}
         else{ $qry .= " AND tbl_warehouse.hf_type_id IN (1,2) ";}
     }
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
                itminfo_tab.itm_category in(1,2)AND
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
                   tbl_hf_data.warehouse_id,
tbl_hf_data.item_id,
tbl_hf_data.reporting_date,
tbl_warehouse.wh_name,
tbl_hf_data.received_balance,
tbl_hf_data.opening_balance,
tbl_hf_data.issue_balance,
tbl_hf_data.closing_balance,
tbl_hf_data.adjustment_positive,
tbl_hf_data.adjustment_negative,
tbl_hf_data.new,
tbl_hf_data.old
FROM
tbl_hf_data
INNER JOIN tbl_warehouse ON tbl_hf_data.warehouse_id = tbl_warehouse.wh_id
WHERE
                    tbl_hf_data.reporting_date = '" . $fromDate . "' AND
                    tbl_warehouse.prov_id = $selProv AND
                    tbl_warehouse.dist_id = $selDist AND
                    tbl_warehouse.stkid = $stakeholder 

            ";
//echo $qryissbal;
    $qryib = mysql_query($qryissbal);
    $hf_data = array();
    while ($row = mysql_fetch_assoc($qryib)) {
        $hf_data[$row['warehouse_id']][$row['item_id']] = $row; 
    }
    
    
    $qryissbal = "
                SELECT
                    stock_sources_data.stock_sources_id,
                    stock_sources_data.received,
                    tbl_hf_data.warehouse_id,
                    tbl_hf_data.item_id,
                    tbl_hf_data.reporting_date,
list_detail.list_value
                FROM
                tbl_hf_data
                INNER JOIN tbl_warehouse ON tbl_hf_data.warehouse_id = tbl_warehouse.wh_id
                INNER JOIN stock_sources_data ON stock_sources_data.hf_data_id = tbl_hf_data.pk_id
INNER JOIN list_detail ON stock_sources_data.stock_sources_id = list_detail.pk_id
                WHERE
                    tbl_hf_data.reporting_date = '" . $fromDate . "' AND
                    tbl_warehouse.prov_id = $selProv AND
                    tbl_warehouse.dist_id = $selDist AND
                    tbl_warehouse.stkid = $stakeholder 

            ";
//echo $qryissbal;
    $qryib = mysql_query($qryissbal);
    $data_sources = $sources_list = array();
    while ($row = mysql_fetch_assoc($qryib)) {
        $data_sources[$row['warehouse_id']][$row['item_id']][$row['stock_sources_id']] = $row['received']; 
        $sources_list[$row['stock_sources_id']]=$row['list_value'];
    }

    $fileName = 'Report_' . $provinceName . '_for_' . str_replace(" ", "", str_replace("'", "", str_replace("-", "", $reportingDate)));
}
//echo '<pre>';print_r($sources_list);
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
                    <h3 class="page-title row-br-b-wp">District Consumption Explorer</h3>
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
                        <th width="30%">SDP</th>
                        <th width="20%">Item</th>
                        <th width="10%">Opening Bal</th>
                        <th width="10%" colspan="<?=count($sources_list)+1?>">Received</th>
                        <th width="10%">Issuance</th>
                        <th width="10%">Adj Positive</th>
                        <th width="10%">Adj Negative</th>
                        <th width="10%">Closing Balance</th>
                        <th width="10%">New</th>
                        <th width="10%">Old</th>
                    </tr>
                    <tr>
                        <td colspan="5"></td>
                        <?php 
                            foreach($sources_list as $kk=>$vv){
                                
                                echo '<td>'.$vv.'</td>';
                            }
                                echo '<td>Total</td>';
                        ?>
                    </tr>

                    <?php
                    $all_dist_totals = array();
                        $counter = 1;
                    //start of dist loop
                    foreach ($distName as $dist_id => $dist_name) {
                        ?>
                            <tr style="background-color:#74c576 !important"><td colspan="99"><?php echo $dist_name; ?></td></tr>
                        <?php
                        foreach($wh_list[$dist_id] as  $wh_id => $wh_name){ 
                            foreach($hf_data[$wh_id] as  $item_id => $item_data){ 
                                
                                $surgery_class = '';
                                if($item_id == '31' || $item_id == '32')
                                {
                                    $surgery_class = 'label-default';
                                }
                                ?>

                                        <tr>
                                            <td class="center"><?php echo $counter++; ?></td>
                                            <td><?php echo $dist_name; ?></td>
                                            <td><?php echo $wh_name; ?></td>
                                            <td><?php echo $items[$item_id]; ?></td>
                                            <td class="<?=$surgery_class?>"><?php echo $item_data['opening_balance']; ?></td>
                                            <?php 
                                            $this_rcv = 0;
                                                foreach($sources_list as $kk=>$vv){
                                                    $a = 0;
                                                    @$a = $data_sources[$wh_id][$item_id][$kk];

                                                    if($kk != 216 && $a>0){
                                                            echo '<td class="info">'.$a.'</td>';
                                                    }else{
                                                            echo '<td class="'.$surgery_class.'">'.$a.'</td>';
                                                    }
                                                    $this_rcv+=$a;
                                                }

                                                $cls = 'danger';
                                                if($this_rcv == $item_data['received_balance']) $cls = 'success';
                                                if($surgery_class!='')$cls = $surgery_class;
                                                echo '<td class="'.$cls.'">'.$item_data['received_balance'].'</td>'; 
                                            ?>
                                            <td><?php echo $item_data['issue_balance']; ?></td>
                                            <td class="<?=$surgery_class?>"><?php echo $item_data['adjustment_positive']; ?></td>
                                            <td class="<?=$surgery_class?>"><?php echo $item_data['adjustment_negative']; ?></td>
                                            <td class="<?=$surgery_class?>"><?php echo $item_data['closing_balance']; ?></td>
                                            <td class="<?=$surgery_class?>"><?php echo $item_data['new']; ?></td>
                                            <td class="<?=$surgery_class?>"><?php echo $item_data['old']; ?></td>
                                <?php
                                }//end of item list 
                            }//end of $wh_list 
                    
            }//end of foreach districts
            ?>  
                                <tr style="background-color: #4b8df9">
                                    <td colspan="3"><b>Province Total</b></td>
                                    <?php
                                   
                                    
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