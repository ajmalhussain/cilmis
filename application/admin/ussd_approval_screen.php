<?php
include("../includes/classes/Configuration.inc.php");
include(APP_PATH . "includes/classes/db.php");
include(PUBLIC_PATH . "html/header.php");
include("../includes/classes/ussd_functions.php");

$province = $_SESSION['user_province1'];
$district = (!empty($_REQUEST['dist_id']))?$_REQUEST['dist_id']:'';
$selProv = (!empty($_REQUEST['province']))?$_REQUEST['province']:'4';
$sdp = (!empty($_REQUEST['sdp_id']))?$_REQUEST['sdp_id']:'';
$show_all_products = (!empty($_REQUEST['prod_check']))?true:false;
$fromDate = (!empty($_REQUEST['from_date']))?$_REQUEST['from_date']:date("Y-m-d");
$itm_arr_request=array();
$itm_arr_request = (!empty($_REQUEST['product']))?$_REQUEST['product']:'';
$full_supply_prods=array();
$full_supply_prods[1] = '1';
$full_supply_prods[5] = '5';
$full_supply_prods[7] = '7';
$full_supply_prods[9] = '9';
//$toDate = $fromDate;
//echo '<pre>';print_r($_REQUEST);exit;

//$ccc_arr = calc_ob_ussd('2019-02-11','31057','5');
//$ccc_arr2 = calc_cb_ussd('2019-02-11','31057','5');
//echo '<pre>';print_r($ccc_arr);print_r($ccc_arr2);exit;
 
$d_exp = explode('-',$fromDate);
$from_y_m = $d_exp[0].'-'.$d_exp[1];

$selected_month = date('m',strtotime($fromDate));
$selected_year = date('Y',strtotime($fromDate));

?>
    <style>
        .my_dash_cols{
            padding-left: 1px;
            padding-right: 0px;
            padding-top: 1px;
            padding-bottom: 0px;
        }
        .my_dashlets{
            /*padding-left: 1px;
            padding-right: 0px;
            padding-top: 1px;
            padding-bottom: 0px;*/
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
                            <div class="widget" data-toggle="">
                                <div class="widget-head">
                                    <h3 class="heading">Filter by</h3>
                                </div>
                                <div class="widget-body collapse in">
                                    <form name="frm" id="frm" action="" method="GET">
                                        <table width="100%">
                                            <tbody>
                                            <tr>
                                                <td class="col-md-2 hide">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label class="control-label">Reporting Month</label>
                                                            <div class="form-group">
                                                                <input type="text" name="from_date" id="from_date"  class="form-control input-sm" value="<?php echo $fromDate; ?>" required>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                </td>
                                                <td class="col-md-2">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label class="control-label">Province</label>
                                                                <select name="province" id="province"  onchange="showDistricts()"  class="form-control input-sm">
                                                                         <option value="4"  >Balochistan</option>
                                                                </select>
                                                        </div>
                                                    </div>
                                                </td>
                                                
                                                <td class="col-md-2 filter1" id="td_dist" style=""><label class="sb1NormalFont">District</label>
                                                    <select name="dist_id" id="dist_id" class="form-control input-sm">
                                                        <option value="">All</option>
                                                            <?php
                                                            $queryDist = "SELECT
                                                                                tbl_locations.PkLocID,
                                                                                tbl_locations.LocName
                                                                        FROM
                                                                                tbl_locations
                                                                        WHERE
                                                                                tbl_locations.LocLvl = 3
                                                                        AND tbl_locations.parentid = '" . $selProv . "'
                                                                        ORDER BY
                                                                                tbl_locations.LocName ASC";
                                                            //query result
                                                            $rsDist = mysql_query($queryDist) or die();
                                                            //fetch result
                                                            $dist_name = "Attock";
                                                            while ($rowDist = mysql_fetch_array($rsDist)) {
                                                                if ($district == $rowDist['PkLocID'] ) {
                                                                    $sel = "selected='selected'";
                                                                    $dist_name=$rowDist['LocName'];
                                                                } else {
                                                                    $sel = "";
                                                                }
                                                                
                                                                //if($_SESSION['user_level'] == 3 && isset($_SESSION['user_district']) && $_SESSION['user_district'] == $rowDist['PkLocID'] ) 
                                                                    echo '<option value="'.$rowDist['PkLocID'].'" '.$sel.'>'.$rowDist['LocName'].'</option>';
                                                            }
                                                            ?>
                                                    </select>
                                                </td>
                                                
                                                <td class="col-md-2 " id="td_sdp" style=""><label class="sb1NormalFont">Facilities</label>
                                                    <select name="sdp_id" id="sdp_id" class="form-control input-sm">
                                                        <option value="">All</option>
                                                            <?php
                                                             if(!empty($district)){
                                                                    
                                                                    $queryDist = "SELECT
                                                                                        tbl_warehouse.wh_id,
                                                                                        tbl_warehouse.wh_name
                                                                                    FROM
                                                                                        tbl_warehouse
                                                                                    INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
                                                                                    WHERE
                                                                                        tbl_warehouse.dist_id =  '" . $district . "' AND
                                                                                        stakeholder.lvl = 7
                                                                                    ORDER BY
                                                                                        tbl_warehouse.wh_name ASC
                                                                                 ";
                                                                    $rsDist = mysql_query($queryDist) or die();
                                                                    while ($rowDist = mysql_fetch_array($rsDist)) {
                                                                        if ($sdp == $rowDist['wh_id'] ) {
                                                                            $sel = "selected='selected'";
                                                                            $sdp_name=$rowDist['LocName'];
                                                                        } else {
                                                                            $sel = "";
                                                                        }

                                                                            echo '<option value="'.$rowDist['wh_id'].'" '.$sel.'>'.$rowDist['wh_name'].'</option>';
                                                                    }
                                                             }
                                                            ?>
                                                    </select>
                                                </td>
                                                <td class="col-md-2">
                                                    <label class="control-label">&nbsp;</label>
                                                    <input type="submit" class="btn btn-succes" value="Go">
                                                    
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </form>
                                </div>
                            </div>
                        </div>
                    
                        
                      <div class="row hide_divs" >
                            <div class="col-md-12">
                                <h3 class="page-title row-br-b-wp center"> USSD - Data Approval - Balochistan <?=(!empty($district)?' - '.$dist_name:'')?> 
                                </h3>

                                <div>
                                    <table class="table table-condensed table-hover table-bordered" >
                                        <tr class="info ">
                                            <td>#</td>
                                            <td>District</td>
                                            <td>Facility Name</td>
                                            <td>Cell Number</td>
                                            <td>Item</td>
                                            <td>Week Start Date</td>
                                            <td>Week End Date</td>
                                            <!--<td>Opening Balance</td>-->
                                            <td>Received</td>
                                            <td>Consumed</td>
                                            <td>Adjustment (+ ve)</td>
                                            <td>Adjustment (- ve)</td>
                                            <!--<td>Closing Balance</td>-->
                                            <td>Last Updated</td>
<!--                                            <td>Master ID</td>
                                            <td>Child ID</td>-->
                                            <td>Status</td>
                                            <td>Action</td>
                                        </tr>
                                    <?php
                                    if(!empty($_REQUEST['province']))
                                    {
                                            $qry_sel= "SELECT
                                                            ussd_session_master.pk_id,
                                                            ussd_sessions.pk_id AS session_child_id,
                                                            ussd_session_master.wh_name,
                                                            itminfo_tab.itm_name,
                                                            ussd_weeks.date_start,
                                                            ussd_weeks.date_end,
                                                            ussd_sessions.stock_received,
                                                            ussd_sessions.stock_consumed,
                                                            ussd_sessions.stock_adjustment_p,
                                                            ussd_sessions.stock_adjustment_n,
                                                            ussd_sessions.insert_date,
                                                            ussd_sessions.is_processed,
                                                            tbl_locations.LocName as dist_name,
                                                            ussd_session_master.phone_number,
                                                            ussd_session_master.wh_id,
                                                            ussd_sessions.item_id
                                                        FROM
                                                            ussd_session_master
                                                        INNER JOIN ussd_sessions ON ussd_sessions.ussd_master_id = ussd_session_master.pk_id
                                                        INNER JOIN itminfo_tab ON ussd_sessions.item_id = itminfo_tab.itm_id
                                                        INNER JOIN ussd_weeks ON ussd_session_master.reporting_year = ussd_weeks.`year` AND ussd_session_master.reporting_month = ussd_weeks.`month` AND ussd_session_master.week_number = ussd_weeks.`week`
                                                        INNER JOIN tbl_warehouse ON ussd_session_master.wh_id = tbl_warehouse.wh_id
                                                        INNER JOIN tbl_locations ON tbl_warehouse.dist_id = tbl_locations.PkLocID
                                                        WHERE
                                                            is_processed = 0 AND 
                                                            tbl_locations.ParentID = $province   ";

                                                if(!empty($_REQUEST['dist_id'])){
                                                    $qry_sel .= "  AND tbl_warehouse.dist_id = '".$_REQUEST['dist_id']."' ";
                                                } 
                                                if(!empty($_REQUEST['sdp_id'])){
                                                    $qry_sel .= "  AND tbl_warehouse.wh_id = '".$_REQUEST['sdp_id']."' ";
                                                }
                                                $qry_sel .= "

                                                             ";

                                            if(!empty($_REQUEST['product'])){
                                                $qry_sel .= "  AND ussd_sessions.item_id IN (".implode(',',$_REQUEST['product']).") ";
                                            } 
                                            if(!empty($_REQUEST['dist_id'])){
                                                $qry_sel .= "  AND tbl_warehouse.dist_id = '".$_REQUEST['dist_id']."' ";
                                            } 
                                            if(!empty($_REQUEST['sdp_id'])){
                                                $qry_sel .= "  AND tbl_warehouse.wh_id = '".$_REQUEST['sdp_id']."' ";
                                            }
                                            $qry_sel .= " 

                                                        ORDER BY
                                                            ussd_session_master.wh_name ASC,
                                                            ussd_weeks.date_start ASC,
                                                            itminfo_tab.itm_name ASC
                                                ";
        //                                    echo $qry_sel;
                                            $rsp  = mysql_query($qry_sel) or die();
                                            $c=1;
                                            while ($row= mysql_fetch_array($rsp)) {
                                                $this_id = $row['session_child_id'];

                                                $calculated_ob = calc_ob_ussd($row['date_start'],$row['wh_id'],$row['item_id']);

                                                if(!empty($calculated_ob[$row['item_id']]))
                                                    $this_ob = $calculated_ob[$row['item_id']];
                                                else
                                                    $this_ob = 0;

                                                $calculated_cb = calc_cb_ussd($row['date_start'],$row['wh_id'],$row['item_id']);
                                                $this_cb = $calculated_cb[$row['item_id']];

                                                echo '<tr>';
                                                    echo '<td>'.$c++.'</td>';
                                                    echo '<td>'.$row['dist_name'].'</td>';
                                                    echo '<td>'.$row['wh_name'].'</td>';
                                                    echo '<td>'.$row['phone_number'].'</td>';
                                                    echo '<td>'.$row['itm_name'].'</td>';
                                                    echo '<td>'.date('Y-M-d',strtotime($row['date_start'])).'</td>';
                                                    echo '<td>'.date('Y-M-d',strtotime($row['date_end'])).'</td>';

                                                    $ob_cls= '';
                                                    if(!empty($this_ob) && $this_ob < 0) $cb_ols = ' danger ';
                                                    //echo '<td class="'.$ob_cls.'" id="td_'.$this_id.'_ob" align="right">'
                                                    //        . ''. number_format($this_ob).''
                                                    //        . '</td>';                                            
                                                    echo '<input type="hidden" name="inp_'.$this_id.'_ob"  id="inp_'.$this_id.'_ob" value="'.$this_ob.'">';


                                                    $rcv  = ((!empty($row['stock_received']) || $row['stock_received'] == '0') ?      ($row['stock_received']):'');
                                                    $cons = ((!empty($row['stock_consumed']) || $row['stock_consumed'] == '0') ?      ($row['stock_consumed']):'');
                                                    $adj_p = ((!empty($row['stock_adjustment_p']) || $row['stock_adjustment_p'] == '0') ?      ($row['stock_adjustment_p']):'');
                                                    $adj_n = ((!empty($row['stock_adjustment_n']) || $row['stock_adjustment_n'] == '0') ?      ($row['stock_adjustment_n']):'');

                                                    echo '<td align="right"><input class="inp_fields" name="inp_'.$this_id.'_r"  id="inp_'.$this_id.'_r"  type="number" style="width:100px !important;" size="10" data-childid="' .$this_id. '" value="'.$rcv.'" ></td>';
                                                    echo '<td align="right"><input class="inp_fields" name="inp_'.$this_id.'_c"  id="inp_'.$this_id.'_c"  type="number" style="width:100px !important;" size="10" data-childid="' .$this_id. '" value="'.$cons.'" ></td>';
                                                    echo '<td align="right"><input class="inp_fields" name="inp_'.$this_id.'_ap" id="inp_'.$this_id.'_ap" type="number" style="width:100px !important;" size="10" data-childid="' .$this_id. '" value="'.$adj_p.'" ></td>';
                                                    echo '<td align="right"><input class="inp_fields" name="inp_'.$this_id.'_an" id="inp_'.$this_id.'_an" type="number" style="width:100px !important;" size="10" data-childid="' .$this_id. '" value="'.$adj_n.'" ></td>';

                                                    $cb_cls= '';
                                                    if(!empty($this_cb) && $this_cb < 0) $cb_cls = ' danger ';
                                                    //echo '<td class="'.$cb_cls.'" id="td_'.$this_id.'_cb" align="right">'. number_format($this_cb).'</td>';


                                                    echo '<td>'.$row['insert_date'].'</td>';
                                                    //echo '<td>'.$row['pk_id'].'</td>';
                                                    //echo '<td>'.$row['session_child_id'].'</td>';
                                                    if(!empty($row['is_processed']) && $row['is_processed'] == 1){
                                                        $st = 'Approved';
                                                        $cl= ' success ';
                                                    }
                                                    else{
                                                        $st = 'Pending';
                                                        $cl =' danger ';
                                                    }
                                                    echo '<td class="'.$cl.'" id="td_status_'.$this_id.'">'.$st.'</td>';
                                                    echo '<td><div id="btn_' . $this_id . '" data-childid="' .$this_id. '" data-masterid="'.$row['pk_id'].'" data-itemid="'.$row['item_id'].'" class="btn btn-xs green save_cls " > Save </div></td>';
                                                echo '</tr>';
                                            }
                                    }
                                    ?>
                                    </table>
                                    <div class="note note-info">Please approve the data in order of display, i.e. approve the top row first.</div>
                                </div>
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
                $(function() { 
                    
                    $('#dist_id').change(function(e) {
                        console.log('Dist Changed');
                            $.ajax({
                                    url: '../reports/ajax_calls.php',
                                    data: {dist_id: $(this).val(), show_what: 'sdps', stk_id: '<?=$_SESSION['user_stakeholder1']?>'},
                                    type: 'POST',
                                    success: function(data){
                                        $('#sdp_id').html(data);
                                    }
                                });
                            });
                    $('.inp_fields').on('keyup blur', function (e){
                        //toastr.success('a');
                        var child_id = $(this).data('childid');
                        
                        var this_ob = parseInt($('#inp_'+child_id+'_ob').val()) || 0;
                        var this_r  = parseInt($('#inp_'+child_id+'_r').val()) || 0;
                        var this_c  = parseInt($('#inp_'+child_id+'_c').val()) || 0;
                        var this_ap = parseInt($('#inp_'+child_id+'_ap').val()) || 0;
                        var this_an = parseInt($('#inp_'+child_id+'_an').val()) || 0;
                        
                        var this_cb = 0;
                        
                        this_cb = this_ob + this_r - this_c + this_ap - this_an;
                        console.log('OB:'+this_ob+',R:'+this_r+',C:'+this_c+',AP:'+this_ap+',AN:'+this_an+',CB:'+this_cb);
                        $('#td_'+child_id+'_cb').html(this_cb);
                    });
                    
                    $('.save_cls').click(function (e) {

                    var child_id    = $(this).data('childid');
                    var master_id   = $(this).data('masterid');
                    var item_id     = $(this).data('itemid');
                    var this_r = $('#inp_'+child_id+'_r').val();
                    var this_c = $('#inp_'+child_id+'_c').val();
                    var this_ap = $('#inp_'+child_id+'_ap').val();
                    var this_an = $('#inp_'+child_id+'_an').val();

                    $(this).attr('disabled', 'disabled');
                    $(this).html('Saving ...');
                    var th = $(this);
                    $.ajax({
                        url: "../../../ussd/ussd_approval_action.php",
                        data: {session_child_id:child_id, master_id:master_id,item_id:item_id , rec:this_r,cons:this_c,adj_p:this_ap,adj_n:this_an},
                        success: function (result) {
                            //alert(result);
                            $(th).attr('disabled', false);
                            
                            if(result=='Approved'){
                                $(th).html('Saved');
                                $(th).removeClass('green').addClass('yellow');
                                $(th).parents('tr').addClass('warning');
                                $('#td_status_'+child_id).html('Approved');
                                $('#td_status_'+child_id).removeClass('danger').addClass('success');
                                toastr.success(result);
                            }else{
                                toastr.error(result);
                                $(th).html('Save');
                            }
                            
                        },
                        error: function (result) {
                            $(th).attr('disabled', false);
                            $(th).html('Save');
                            $(th).removeClass('green').addClass('red');
                            toastr.error('Some error while saving.'+result);
                        }
                    });
                });

		});
    </script>
</body>
<!-- END BODY -->
</html>