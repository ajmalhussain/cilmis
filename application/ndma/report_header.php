    <style>
    *{font-family:"Open Sans",sans-serif;}
    b{font-size:12px;}
    h3{font-size:13px;}
    #report_type{
        font-size:12px;
        font-family: arial;}
    #content_print
    {
        width:624px;
        margin-left:50px;
    }
    table#myTable{
        border:1px solid #E5E5E5;
        font-size:9pt;
        width:100%;
    }
    table, table#myTable tr td{
        border-collapse: collapse;
        border:1px solid #E5E5E5;
        font-size:12px;
    }
    table, table#myTable tr th{
        border:1px solid #E5E5E5;
        border-collapse: collapse;
        font-size:12px;
    }
</style>
<?php
$getWHName = "SELECT
                tbl_warehouse.wh_name,
                tbl_warehouse.stkid,
                tbl_warehouse.prov_id,
                stakeholder.lvl
                FROM
                tbl_warehouse
                INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid where wh_id='" . $_SESSION['user_warehouse'] . "'";
$resWHName = mysql_query($getWHName) or die(mysql_error());
$whName = mysql_fetch_assoc($resWHName);
//print_r($whName);

$getStkLogo = "select report_logo,report_title3 from stakeholder where stkid='" . $whName['stkid'] . "'";
$resStkLogo = mysql_query($getStkLogo) or die(mysql_error());
$logo = mysql_fetch_row($resStkLogo);
?>

<div id="report_type" style="text-align:center;">

    <img src="NDMALogo.png" style="width:30em" />
    <br>
    <span style="line-height:15px"><b>Store: </b><?php echo $whName['wh_name']; ?></span>
    <hr style="margin:3px 10px;" />
    <p><b><?php echo $rptName; ?> as on: <?php echo date('d-M-Y'); ?></b>
    </p>
</div>

<div style="clear:both"></div>