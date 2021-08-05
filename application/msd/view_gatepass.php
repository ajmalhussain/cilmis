<?php
/**
 * view_gatepass
 * @package im
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
//include AllClasses file
include("../includes/classes/AllClasses.php");
//include header
include(PUBLIC_PATH . "html/header.php");
//get user_warehouse
$whId = $_SESSION['user_warehouse'];

// If Gate Pass is deleted
if (isset($_REQUEST['id']) && !empty($_REQUEST['act']) && $_REQUEST['act']=='del') {
    $gpId = base64_decode($_REQUEST['id']);
    // Delete gatepass master and detail
//    mysql_query("UPDATE gatepass_detail SET gp_status = 'deleted' WHERE gatepass_detail.gatepass_master_id = $gpId");
    mysql_query("UPDATE gatepass_master SET gp_status = 'deleted' WHERE gatepass_master.pk_id = $gpId");

    $qry = "";
    $_SESSION['e'] = 1;
    //redirecting to view_gatepass
    echo "<script>window.location='view_gatepass.php'</script>";
    exit;
}

$where = 'WHERE gatepass_master.warehouse_id = '.$_SESSION['user_warehouse'].' ';
$vehicle = '';

if (isset($_POST['submit'])) {
//    echo '<pre>';print_r($_REQUEST)s;exit;
    //get vehicle_type
    $vehicleType = $_POST['vehicle_type'];
    //get vehicleText
    $vehicleText = $_POST['vehicleText'];
    //get vehicleList
    $vehicleList = $_POST['vehicleList'];
    //filters
    if (!empty($_POST['date_from']) && !empty($_POST['date_to'])) {
        $f_d = explode('/',$_POST['date_from']);
        $t_d = explode('/',$_POST['date_to']);
        
        $from_formatted = $f_d[2].'-'.$f_d[1].'-'.$f_d[0];
        $to_formatted   = $t_d[2].'-'.$t_d[1].'-'.$t_d[0];
        
        
        $where .= " AND DATE_FORMAT(gatepass_master.transaction_date,'%Y-%m-%d') BETWEEN '" . $from_formatted . "' AND '" . $to_formatted . "'";
        $fromDate = $_POST['date_from'];
        $toDate = $_POST['date_to'];
    }
    if ($vehicleType == -1) {
        $where .= " AND gatepass_vehicles.number LIKE '%" . $vehicleText . "%' ";
    } else {
        $where .= " AND gatepass_vehicles.pk_id = " . $vehicleList;
    }
} else {
    $fromDate = date('01/m/Y');
    $toDate = date('d/m/Y');
    $vehicleType = '';
    $vehicleText = '';
    $vehicleList = '';

    $where .= " AND DATE_FORMAT(gatepass_master.transaction_date,'%d/%m/%Y') BETWEEN '" . $fromDate . "' AND '" . $toDate . "'";
}
//query 
//gets
//srNo,
//gateID,
//gatepassNum,
//transDate,
//vehicles number
$qry = "SELECT
			@n := @n + 1 AS srNo,
			gatepass_master.pk_id AS gateID,
			gatepass_master.number AS gatepassNum,
			DATE_FORMAT(gatepass_master.transaction_date,'%d-%M-%Y') AS transDate,
			gatepass_vehicles.number AS vehNum,
gatepass_master.d_name as driver_name,
gatepass_master.gp_status,
GROUP_CONCAT(distinct tbl_locations.LocName) as dist_name,
GROUP_CONCAT(distinct tbl_stock_master.TranNo) as iss_v
		FROM
			(select @n:=0) AS initvars,gatepass_master
			INNER JOIN gatepass_vehicles ON gatepass_master.gatepass_vehicle_id = gatepass_vehicles.pk_id
INNER JOIN gatepass_detail ON gatepass_detail.gatepass_master_id = gatepass_master.pk_id
LEFT JOIN tbl_stock_detail ON gatepass_detail.stock_detail_id = tbl_stock_detail.PkDetailID
LEFT JOIN tbl_stock_master ON tbl_stock_detail.fkStockID = tbl_stock_master.PkStockID
LEFT JOIN tbl_warehouse ON tbl_stock_master.WHIDTo = tbl_warehouse.wh_id
LEFT JOIN tbl_locations ON tbl_warehouse.dist_id = tbl_locations.PkLocID
		$where"
        . " 
group by gatepass_master.pk_id ORDER BY gatepass_master.pk_id ";
//query result
//echo $qry;
$rs_qry = mysql_query($qry);
?>
<link rel="stylesheet" type="text/css" media="all" href="<?php echo PUBLIC_URL; ?>css/jsDatePick_ltr.css"/>
<style>
    table#myTable tr td {
        padding-left: 10px;
    }
</style>
</head><!-- END HEAD -->

<body class="page-header-fixed page-quick-sidebar-over-content">
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

                <div class="row row_filter">
                    <div class="col-md-12">
                        <div class="widget" data-toggle="collapse-widget">
                            <div class="widget-head">
                                <h3 class="heading">Filters</h3>
                            </div>
                            <div class="widget-body">
                                <form name="frm" id="frm" action="" method="post">
                                    <table cellpadding="7" cellspacing="5" id="myTable">
                                        <tr>
                                            <td class="sb1NormalFont"><label>Date From</label>
                                                <input class="form-control input-small" type="text" name="date_from" id="date_from" value="<?php
                                                if (isset($fromDate)) {
                                                    echo $fromDate;
                                                }
                                                ?>" readonly/></td>
                                            <td class="sb1NormalFont"><label>Date To</label>
                                                <input class="form-control input-small" type="text" name="date_to" id="date_to" value="<?php
                                                if (isset($toDate)) {
                                                    echo $toDate;
                                                }
                                                ?>" readonly/></td>
                                            <td class="sb1NormalFont"><label>Vehicle Type</label>
                                                <select name="vehicle_type" id="vehicle_type" class="form-control input-medium">
                                                    <option value="-1" <?php echo ($vehicleType == -1) ? 'selected="selected"' : ''; ?>>All</option>
                                                    <?php
                                                    //query 
                                                    //gets
                                                    //gatepass_vehicle_types.pk_id,
                                                    //vehicle_type
                                                    $qry = mysql_query("SELECT
                                                                                    gatepass_vehicle_types.pk_id,
                                                                                    gatepass_vehicle_types.vehicle_type
                                                                            FROM
                                                                                    gatepass_vehicle_types
                                                                            ORDER BY
                                                                            gatepass_vehicle_types.vehicle_type ASC");
                                                    //fetch results
                                                    while ($row = mysql_fetch_array($qry)) {
                                                        ?>
                                                        <option value="<?php echo $row['pk_id']; ?>" <?php
                                                        if ($row['pk_id'] == $vehicleType) {
                                                            echo 'selected="selected"';
                                                        } else {
                                                            echo " ";
                                                        }
                                                        ?>><?php echo $row['vehicle_type']; ?></option>
                                                                <?php
                                                            }
                                                            ?>
                                                    
                                                </select></td>
                                            <td class="sb1NormalFont"><label>Vehicle</label>
                                                <select name="vehicleList" id="vehicleList" style="display:none;" class="form-control input-medium">
                                                    <option value="">Select</option>
                                                </select>
                                                <input name="vehicleText" id="vehicleText" style="display:none;" class="form-control input-medium" value="<?php echo $vehicleText; ?>" /></td>
                                            <td colspan="4" style="padding-top:18px;"><input type="submit" name="submit" value="Search" class="btn btn-primary"/></td>
                                        </tr>
                                    </table>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="widget" data-toggle="collapse-widget">
                            <div class="widget-head">
                                <h3 class="heading">Gate Pass List</h3>
                            </div>
                            <div class="widget-body">
                                <?php if ($rs_qry != FALSE && mysql_num_rows($rs_qry) > 0) { 
                                    
                                    
                                    include('export_options.php');
                                    ?>
                                    <table id="myTable" class=" table  table-bordered table-condensed">
                                        <thead>
                                            <tr>
                                                <th style="text-align:center;" width="60px">Sr No.</th>
                                                <th>Gate Pass Number</th>
                                                <th>Gate Pass Date</th>
                                                <th>Issued Voucher/s</th>
                                                <th>Issuance for District/s</th>
                                                <th>Vehicle Number</th>
                                                <th>Driver's Name</th>
                                                
                                                <!--Action column is hidden , bcz of comments in CLMIS-1084 -->
                                                <th>Delete</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            while ($rsPro = mysql_fetch_array($rs_qry)) {
                                                $gp_is_deleted = false;
                                                $color = '';
                                                if(!empty($rsPro['gp_status']) && $rsPro['gp_status']=='deleted'){
                                                    $gp_is_deleted = true;
                                                    $color = 'danger';
                                                }
                                                ?>
                                            <tr class="<?=$color?>">
                                                    <td style="text-align:center;"><?php echo $rsPro['srNo']; ?></td>
                                                    <td><a onClick="window.open('printGatePass.php?id=<?php echo $rsPro['gateID']; ?>', '_blank', 'scrollbars=1,width=842,height=595');" href="javascript:void(0);"><?php echo $rsPro['gatepassNum']; ?></a></td>
                                                    
                                                    <?php 
                                                    if($gp_is_deleted){
                                                        ?>
                                                    <td class="center" colspan="6">DELETED</td>
                                                        <?php
                                                    }else
                                                    {
                                                        ?>
                                                            <td><?php echo $rsPro['transDate']; ?></td>
                                                            <td><?php echo $rsPro['iss_v']; ?></td>
                                                            <td><?php echo $rsPro['dist_name']; ?></td>
                                                            <td><?php echo $rsPro['vehNum']; ?></td>
                                                            <td><?php echo $rsPro['driver_name']; ?></td>
                                                            <td class="center"><a href='view_gatepass.php?act=del&id=<?php echo base64_encode($rsPro['gateID']); ?>' onClick="return confirm('Are you sure you want to delete this Gate Pass?')">Del</a></td>
                                                        <?php
                                                    }
                                                    ?>
                                                    
                                                </tr>
                                                <?php
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                    <?php
                                } else {
                                    echo "New record found";
                                }
                                ?>
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
    <script type="text/javascript" src="<?php echo PUBLIC_URL; ?>js/jsDatePick.full.1.3.js"></script> 
    <script type="text/javascript">
                                                        window.onload = function() {
                                                            $('#date_from').datepicker({
                                                                dateFormat: "dd/mm/yy",
                                                                constrainInput: false,
                                                                maxDate: 0,
                                                                changeMonth: true,
                                                                changeYear: true,
                                                            });
                                                            $('#date_to').datepicker({
                                                                dateFormat: "dd/mm/yy",
                                                                constrainInput: false,
                                                                 
                                                                changeMonth: true,
                                                                changeYear: true,
                                                            });
                                                        };
    </script> 
    <script>
        $(function() {
            getVehicles('');

            $('#vehicle_type').change(function(e) {
                getVehicles('');
            });
            $('#searchBtn').click(function(e) {
                if ($('#date_from').val() == '') {
                    alert('Please select date from');
                    return false;
                }
                if ($('#date_to').val() == '') {
                    alert('Please select date to');
                    return false;
                }
                showTransactions();
            });
            $('#vehicle_other_option').click(function(e) {
                if ($('#vehicle_other_option').is(':checked')) {
                    $('#vehicle_other').show();
                    $('#vehicle').hide();
                }
                else {
                    $('#vehicle_other').hide();
                    $('#vehicle').show();
                }
            });
            $('#issue_no').change(function(e) {
                var issueNum = $('#issue_no').val();
                $.ajax({
                    url: 'gatepass_ajax.php',
                    type: 'POST',
                    data: {issueNum: issueNum},
                    success: function(data) {
                        $('#gridData').html(data);
                        $('#row3').show();
                    }
                })
            });
        })
        function getVehicles(vNum) {
            var vehicleType = $('#vehicle_type').val();
            if (vehicleType != '' && vehicleType != -1) {
                $.ajax({
                    url: 'gatepass_ajax.php',
                    type: 'POST',
                    data: {vehicleType: vehicleType, vNum: vNum},
                    success: function(data) {
                        $('#vehicleList').show();
                        $('#vehicleText').hide();
                        $('#vehicleList').html(data);
                    }
                })
            }
            else {
                //if (vehicleType == -1)
                {
                    $('#vehicleList').hide();
                    $('#vehicleText').show();
                }

            }
        }
        function showTransactions() {
            var dateFrom = $('#date_from').val();
            var dateTo = $('#date_to').val();
            $.ajax({
                url: 'gatepass_ajax.php',
                type: 'POST',
                data: {dateFrom: dateFrom, dateTo: dateTo},
                success: function(data) {
                    if (data != '') {
                        $('#issue_no').html(data);
                        $('#row1').show();
                        $('#row2').show();
                        $('#eMsg').html('');
                    }
                    else {
                        $('#eMsg').html('No record found.');
                        $('#row1').hide();
                        $('#row2').hide();
                    }
                }
            })
        }
    </script>
</div>
</div>
</div>
</div>
<?php
if (isset($_SESSION['e'])) {
    ?>
    <script>
        var self = $('[data-toggle="notyfy"]');
        notyfy({
            force: true,
            text: 'Gate Pass deleted successfully!',
            type: 'success',
            layout: self.data('layout')
        });
    </script>
    <?php
    //unset session e
    unset($_SESSION['e']);
}
?>
</body>
<!-- END BODY -->
</html>