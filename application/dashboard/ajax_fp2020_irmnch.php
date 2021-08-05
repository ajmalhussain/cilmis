<?php
/**
 * ajax
 * @package dashboard
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
//include Configuration
include("../includes/classes/Configuration.inc.php");
//include db
include(APP_PATH . "includes/classes/db.php");
?>
<input type="hidden" id="prov_name" value="<?php echo $_REQUEST['prov_name']; ?>">
<input type="hidden" id="province" value="<?php echo $_REQUEST['province']; ?>">
<input type="hidden" id="from_date" value="<?php echo date("Y-m-d", strtotime($_REQUEST['from_date'])); ?>">
<input type="hidden" id="funding_source" value="<?php echo (!empty($_REQUEST['stakeholder']) ? $_REQUEST['stakeholder'] : ''); ?>">
<input type="hidden" id="product" value="<?php echo $_REQUEST['product']; ?> ">
<div id="data">

</div>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
<script>
    $(function () {
        $("#data").html("<center><div id='loadingmessage'><img src='<?php echo PUBLIC_URL ?>images/ajax-loader.gif'/></div></center>");
        var prov_name = $("#prov_name").val();
        var province = $("#province").val();
        var from_date = $("#from_date").val();
        var funding_source = $("#funding_source").val();
        var product = $("#product").val();
        $.ajax({
            type: "POST",
            url: '<?php echo APP_URL; ?>dashboard/dashboard_fp2020_irmnch.php',
            data: {prov_name: prov_name, province: province, from_date: from_date, funding_source: funding_source, product: product},
            success: function (data) {
                $("#data").html(data);
            }
        });
    });
</script>