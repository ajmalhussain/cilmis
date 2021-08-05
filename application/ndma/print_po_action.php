<?php
include("../includes/classes/AllClasses.php");
//echo '<pre>';
//print_r($_REQUEST);
//echo '</pre>';
//exit;
$head_1         ='NULL';
$cc         ='NULL';
$display_date    ='NULL';
$subject    ='NULL';
$body_1     ='NULL';
$body_2     ='NULL';
$tnc        ='NULL';
$body_3     = 'NULL';
$signee_name='NULL';
$signee_desig   ='NULL';
$signee_ph      ='NULL';

if(!empty($_REQUEST['head_1']))                 $head_1="'".mysql_real_escape_string($_REQUEST['head_1'])."'";
if(!empty($_REQUEST['cc']))                 $cc="'".mysql_real_escape_string($_REQUEST['cc'])."'";
if(!empty($_REQUEST['display_date']))       $display_date="'".mysql_real_escape_string($_REQUEST['display_date'])."'";
if(!empty($_REQUEST['subject']))            $subject="'".mysql_real_escape_string($_REQUEST['subject'])."'";
if(!empty($_REQUEST['body_1']))             $body_1="'".htmlspecialchars(mysql_real_escape_string($_REQUEST['body_1']))."'";
if(!empty($_REQUEST['body_2']))             $body_2="'".mysql_real_escape_string($_REQUEST['body_2'])."'";
if(!empty($_REQUEST['body_3']))             $body_3="'".mysql_real_escape_string($_REQUEST['body_3'])."'";
if(!empty($_REQUEST['tnc']))                $tnc="'".mysql_real_escape_string($_REQUEST['tnc'])."'";
if(!empty($_REQUEST['signee_name']))        $signee_name="'".mysql_real_escape_string($_REQUEST['signee_name'])."'";
if(!empty($_REQUEST['signee_desig']))       $signee_desig="'".mysql_real_escape_string($_REQUEST['signee_desig'])."'";
if(!empty($_REQUEST['signee_ph']))          $signee_ph="'".mysql_real_escape_string($_REQUEST['signee_ph'])."'";

$qry = "INSERT INTO `purchase_order_prints` 
        ( `po_id`,`head_1` ,`cc`,`display_date`, `subject`, `body_1`, `body_2`, `tnc`, `body_3`, `signee_name`, `signee_desig`, `signee_ph`) 
        VALUES 
        ( '".$_REQUEST['po']."', $head_1, $cc ,$display_date, $subject, $body_1, $body_2, $tnc, $body_3, $signee_name, $signee_desig, $signee_ph); ";
//echo $qry;exit; 
$res = mysql_query($qry);


$_SESSION["success"] = 1;
header("location:print_po_view.php?po_id=".$_REQUEST['po']);
exit;
?>