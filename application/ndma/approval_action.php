<?php
include("../includes/classes/AllClasses.php");
//echo '<pre>';print_r($_REQUEST);exit;

$strSql = "INSERT INTO `approval_log` 
    (`module`, `unique_id`, `approval_level`, `approval_by`, `comments`, `updated_status`) 
    VALUES 
    ('".mysql_real_escape_string($_REQUEST['module'])."', '".mysql_real_escape_string($_REQUEST['unique_id'])."', 
        '".mysql_real_escape_string($_REQUEST['approval_level'])."', '".mysql_real_escape_string($_SESSION['user_id'])."', 
            ' ".mysql_real_escape_string($_REQUEST['remarks'])."', '".mysql_real_escape_string($_REQUEST['approval_action'])."');
";
mysql_query($strSql) ;
//echo $strSql;

header("location:search_purchase_order.php");
exit;
?>