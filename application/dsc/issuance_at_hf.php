<?php
include("../includes/classes/AllClasses.php");
include(PUBLIC_PATH . "html/header.php");
$wh_id = $_SESSION['user_warehouse'];
$stk_id = $_SESSION['user_stakeholder1'];

 

if ( isset($wh_id)) {
//    $whTo = mysql_real_escape_string($_REQUEST['wh_id']);
    
//    $qry = "SELECT
//				tbl_warehouse.dist_id,
//				tbl_warehouse.prov_id,
//				tbl_warehouse.stkid,
//				tbl_locations.LocName,
//				MainStk.stkname AS MainStk
//			FROM
//			tbl_warehouse
//			INNER JOIN tbl_locations ON tbl_warehouse.dist_id = tbl_locations.PkLocID
//			INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
//			INNER JOIN stakeholder AS MainStk ON stakeholder.MainStakeholder = MainStk.stkid
//			WHERE
//			tbl_warehouse.wh_id = " . $whTo;
//    //    echo $qry; exit;
//    $qryRes = mysql_fetch_array(mysql_query($qry));
//    $distId = $qryRes['dist_id'];
//    $provId = $qryRes['prov_id'];
//    $distName = $qryRes['LocName'];
//    $mainStk = $qryRes['MainStk'];
   
$strSql = "SELECT
                    stock_batch.batch_no,
                    stock_batch.batch_id,
                    stock_batch.batch_expiry,
                    stock_batch.item_id,
                    SUM(tbl_stock_detail.Qty) as Qty,
                    stock_batch.funding_source,
                    tbl_warehouse.wh_name,
itminfo_tab.itm_name
            FROM
                    stock_batch
            INNER JOIN tbl_stock_detail ON stock_batch.batch_id = tbl_stock_detail.BatchID
            LEFT JOIN tbl_warehouse ON stock_batch.funding_source = tbl_warehouse.wh_id
INNER JOIN itminfo_tab ON stock_batch.item_id = itminfo_tab.itm_id
            WHERE
                    stock_batch.Qty <> 0 AND
                    stock_batch.`status` = 'Running' AND 
                    stock_batch.wh_id = $wh_id AND
                    tbl_stock_detail.temp = 0

            GROUP BY
                    stock_batch.batch_no
            ORDER BY
            itminfo_tab.itm_name,
                    stock_batch.batch_expiry ASC,
                    tbl_warehouse.wh_name,
                    stock_batch.batch_no";

//query result
 //echo $strSql;exit;
    $batchno = '';
    $product = $batches_data =  array();
    $rsSql = mysql_query($strSql) or die("Error: GetBatches_detail");
    while ($row = mysql_fetch_array($rsSql)) {
        $product[$row['item_id']] = $row['itm_name'];
        $batches_data[$row['item_id']][$row['batch_no']]=$row;
    }
}
?>

<style>
* {
  box-sizing: border-box;
}

#myInput {
  background-image: url('/css/searchicon.png');
  background-position: 10px 10px;
  background-repeat: no-repeat;
  width: 80%;
  font-size: 16px;
  padding: 12px 20px 12px 40px;
  border: 1px solid #ddd;
  margin-bottom: 12px;
}

#myTable {
  border-collapse: collapse;
  width: 80%;
  border: 1px solid #ddd;
  font-size: 14px;
}

#myTable th, #myTable td {
  text-align: left;
  padding: 5px;
}

#myTable tr {
  border-bottom: 1px solid #ddd;
}

#myTable tr.header, #myTable tr:hover {
  background-color: #f1f1f1;
}
</style>
</head>
<body class="page-header-fixed page-quick-sidebar-over-content">
    <div class="page-container">
        <?php include PUBLIC_PATH . "html/top.php"; ?>
        <?php include PUBLIC_PATH . "html/top_im.php"; ?>
        <div class="page-content-wrapper">
            <div class="page-content">
                <div class="row">
                    <div class="col-md-12">
                        <div class="widget" id="printing">
                            <style>
                                table#myTable2 {
                                    margin-top: 20px;
                                    border-collapse: collapse;
                                    border-spacing: 0;
                                }

                                /* Print styles */
                                @media only print {
                                    table#myTable2 {
                                        
                                        padding-left: 2 !important;
                                        text-align: left;
                                        border: 1px solid #999;
                                    }

                                    #doNotPrint {
                                        display: none !important;
                                    }
                                }
                            </style>
                            <div class="widget-head">
                                <h3 class="heading">Health Facility  - Stock Issuance to Clients </h3>
                            </div>
                            <div class="widget-body">
<input type="text" id="myInput" onkeyup="myFunction()" placeholder="Search by product name..." title="Type Product Name to search">
                                <form name="frm" id="frm" action="<?php echo APP_URL ?>dsc/issuance_at_hf_action.php" method="post" onSubmit="return formValidation()">
                                    
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="col-md-12">
                                                <table id="myTable2" class="table table-striped1 table-bordered table-condensed">
                                                    <?php
                                                    if (true) {
                                                        ?>
                                                        <thead>
                                                            <tr class="font-white bg-green">
                                                                <th width="5%" style="text-align:center;">S. No.</th>
                                                                <th width="7%">Product</th>
                                                                <th width="55%"> 
                                                                    <table width="100%" class=" " id="myTable">
                                                                        <thead>
                                                                            <tr>
                                                                                <th width="15%">Batch No</th>
                                                                                <th width="30%">Funding Source</th>
                                                                                <th width="15%" style="text-align:left">Expiry</th>
                                                                                <th width="15%" style="text-align:left">Available Qty</th>
                                                                                <th width="15%" style="text-align:right">Issue Qty</th>
                                                                            </tr>
                                                                        </thead>
                                                                    </table>
                                                                </th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php
                                                            $count = 1;
                                                            foreach ($product as $proId => $proName) {
                                                                ?>
                                                                <tr>
                                                                    <input type="hidden" name="product[<?php echo $proId ?>]" id="product" value="<?php echo $proId ?>" />
                                                                        <input type="hidden" name="itmrec[<?php echo $proId ?>]" id="itmrec" value="" />
                                                                    <td class="center"><?php echo $count++; ?></td>
                                                                    <td><?php echo $proName; ?></td>
                                                                    <td><table  width="100%" class=" " id="">
                                                                            <tbody>
                                                                                <?php
                                                                                     
                                                                                    $batch_count = 0 ;
                                                                                    foreach($batches_data[$proId] as $k => $resStockIssues){
                                                                                        $avail = $resStockIssues['Qty'];
                                                                                        $batch_count++;
                                                                                        
                                                                                        
                                                                                        ?>
                                                                                        <tr>
                                                                                            <td width="15%" r_type="sub_row"><h5><?php echo $resStockIssues['batch_no']; ?><h5></td>
                                                                                            <td width="30%" r_type="sub_row"><?php echo $resStockIssues['wh_name']; ?></td>
                                                                                            <td width="15%" r_type="sub_row"><?php echo date('d-M-Y', strtotime($resStockIssues['batch_expiry'])); ?></td>
                                                                                            <td width="15%" r_type="sub_row"><input class="form-control input-small input-sm" type="text" value="<?php echo number_format($avail) ?>" disabled style="text-align:right;"/></td>
                                                                                            <td width="15%" r_type="sub_row" align="right"><input value="" autocomplete="off" max="<?php echo $avail; ?>" class="qty form-control input-small input-sm" style="text-align:right" type="text" name="qty_issued[<?php echo $proId . "|" . $resStockIssues['batch_id']; ?>]" id="<?php echo $resStockIssues['batch_id'] . "-" . $proId; ?>-qty_issued" /></td>
                                                                                        </tr>
                                                                                        <?php
                                                                                    }
                                                                                    if ($num == 1) {
                                                                                        $style = 'style="display:none;"';
                                                                                    } else {
                                                                                        $style = 'style="display:table-row; background-color:#8cbc84;"';
                                                                                    }
                                                                                    
                                                                                    $td1_style=' colspan="4" ';
                                                                                    $td2_style='  ';
                                                                                  
                                                                                    if($batch_count>1){
                                                                                    ?>
                                                                                    <tr <?php echo $style; ?>>
                                                                                        <td <?=$td1_style?> align="right"><b>Total Issuance of <?=$proName?> :</b></td>
                                                                                        <td <?=$td2_style?> align="right"><input type="text" readonly class="issued_qty form-control input-small input-sm" id="<?php echo $proId ?>-total_issued" value="" /></td>
                                                                                    </tr>
                                                                                    <?php } ?>
                                                                                    
                                                                            </tbody>
                                                                        </table></td>
                                                                </tr>
                                                                
                                                                <?php
                                                            }
                                                            ?>
                                                                
                                                                <tr id="">
                                                                    <td colspan="2">Comments:</td>
                                                                    <td colspan="5" style=" border:none; padding-top:10px;">                                                        
                                                                        <textarea id="comments" name="comments" maxlength="290" rows="3" cols="60"></textarea>
                                                                    </td>
                                                                </tr>
                                                                
                                                            <?php
                                                            if (true) {
                                                                ?>
                                                                <tr id="doNotPrint">
                                                                    <td colspan="5" style="text-align:right; border:none; padding-top:10px;">                                                        
                                                                        <button type="submit" id="submit" name="submit" class="btn btn-primary"   > Issue </button>
                                                                        <button type="button" onClick="javascript: history.go(-1)" class="btn btn-warning"> Cancel </button>
                                                                        <a class="btn btn-warning"  onClick="printContents()" href="javascript:void(0);">Print</a>
                                                                    </td>
                                                                </tr>
                                                                <?php
                                                            }
                                                        } else {
                                                            ?>
                                                            <tr>
                                                                <td colspan="7" style="text-align:Center;font-size:14px; border:none; padding-top:10px;"> No Approved Items to Issue. </td>
                                                            </tr>
                                                        <?php }
                                                        ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <input type="hidden" name="warehouse" id="warehouse" value="3"/>
                                    <input type="hidden" name="issue_date" id="issue_date" value="<?php echo date("d/m/Y") ?>"/>
                                    <input type="hidden" name="trans_no" id="trans_no" value="-1"/>
                                    <input type="hidden" name="stock_id" id="stock_id" value="0"/>
                                    
                                    <input type="hidden" name="ref_page" value="<?php echo (!empty($_REQUEST['ref_page'])?$_REQUEST['ref_page']:'') ?>"/>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- END FOOTER -->
        <?php include PUBLIC_PATH . "/html/footer.php"; ?>
        <script src="<?php echo PUBLIC_URL; ?>js/dataentry/clr6issue.js"></script> 
        
    <script>
            function myFunction() {
                var input, filter, table, tr, td, i, txtValue;
                input = document.getElementById("myInput");
                filter = input.value.toUpperCase();
                table = document.getElementById("myTable2");
                tr = table.getElementsByTagName("tr");
                for (i = 0; i < tr.length; i++) {
                  td = tr[i].getElementsByTagName("td")[1];
                  //td += tr[i].getElementsByTagName("td")[5];

                   

                  if (td) 
                  {
                      //this logic is only for issuance screen
                        var r_type = '';
                       r_type = td.getAttribute("r_type");
                       if(r_type != 'sub_row'){
                            txtValue = td.textContent || td.innerText;

    //                      td = tr[i].getElementsByTagName("td")[2];
    //                      txtValue += td.textContent || td.innerText;
    //                      td = tr[i].getElementsByTagName("td")[3];
    //                      txtValue += td.textContent || td.innerText;
    //                      td = tr[i].getElementsByTagName("td")[4];
    //                      txtValue += td.textContent || td.innerText;
                            console.log(txtValue);
                            if (txtValue.toUpperCase().indexOf(filter) > -1) {
                              tr[i].style.display = "";
                            } else {
                              tr[i].style.display = "none";
                            }
                        }
                    }       
                }
            }
    </script>
        <script>
            function openPopUp(pageURL)
            {
                var w = screen.width;
                var h = screen.height;
                var left = 0;
                var top = 0;

                return window.open(pageURL, 'Requisition Approved', 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=no, copyhistory=no, width=' + w + ', height=' + h + ', top=' + top + ', left=' + left);
            }
            $(function() {
                 $("form").submit(function() {
                            // submit more than once return false
                            $(this).submit(function() {
                                    return false;
                            });
                            // submit once return true
                            return true;
                    });
//                                                                        $('#submit').click(function(e){
//                                                                            $('#submit').attr('disabled', true);
//                                                                            $('#submit').val('Submitting...');
//                                                                        });

                $('.qty').priceFormat({
                    prefix: '',
                    thousandsSeparator: ',',
                    suffix: '',
                    centsLimit: 0,
                    limit: 10,
                    clearOnEmpty: true
                });

                $("input[id$='-qty_issued']").keyup(function(e) {
                    var arr = $(this).attr('id').split('-');
                    var proId = arr[1];
                    var sum = 0;
                    $("input[id$='" + proId + "-qty_issued']").each(function(index, element) {
                        var qty = $(this).val().replace(/\,/g, '');
                        if (qty > 0)
                        {
                            sum += parseFloat(qty);
                        }
                    });
                    $('#' + proId + '-total_issued').val(sum).priceFormat({
                        prefix: '',
                        thousandsSeparator: ',',
                        suffix: '',
                        centsLimit: 0,
                        limit: 10,
                        clearOnEmpty: true
                    });
                });
            })

            function formValidation()
            {
                var q = 0;
                var inp = $('.qty');
                for (var i = 0; i < inp.length; i++) {
                    if (inp[i].value != '') {
                        q++;
                        var qtyValue = inp[i].value;
                        qtyValue = parseInt(qtyValue.replace(/\,/g, ''));
                        if (qtyValue == 0)
                        {
                            alert('Quantity can not be 0');
                            inp[i].focus();
                            return false;
                        }
                        else if (qtyValue > parseInt(inp[i].getAttribute('max'))) {
                            alert('Quantity can not be greater than ' + inp[i].getAttribute('max'));
                            inp[i].focus();
                            return false;
                        }
                    }
                }

                if (q == 0) {
                    alert('Please enter at least one quantity to issue');
                    return false;
                }
                var flag = true;
                var errMsg = '';
                $("input[id$='-total_issued']").each(function(index, element) {
                    var issuedQty = $(this).val().replace(/\,/g, '');
                    var arr = $(this).attr('id').split('-');
                    var proId = arr[0];
                    var approvedQty = $('#' + proId + '-approved').val().replace(/\,/g, '');


                    if (parseInt(issuedQty) > 0 && parseInt(approvedQty) != parseInt(issuedQty))
                    {
                        //this part allows to issue quantity only equal to approved qty.
                        //disabling this part , to allow changes in issuance , as per requirement change as on 26 may 2017...
                        //errMsg += 'Total issued quantity must be equal to approved quantity for ' + $('#' + proId).html() + '\n';
                        //flag = false;
                    }
                });
                if (errMsg.length > 0) {
                    alert(errMsg);
                }
                return flag;

                $('#submit').attr('disabled', true);
                $('#submit').val('Submitting...');
            }
        </script> 
        <!-- END JAVASCRIPTS -->
</body>
<!-- END BODY -->
</html>