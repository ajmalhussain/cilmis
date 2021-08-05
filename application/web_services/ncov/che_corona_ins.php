<?php

include("../../includes/classes/Configuration.inc.php");
include("../../includes/classes/db.php");


$airport_id = $_REQUEST['airport_id']; 
$date = $_REQUEST['date']; 
$indirect_exit = $_REQUEST['indirect_exit']; 
$suspected_passengers = $_REQUEST['suspected_passengers']; 
$china_flights = $_REQUEST['china_flights']; 
$indirect_flights = $_REQUEST['indirect_flights']; 
$china_exit = $_REQUEST['china_exit']; 
$screened_passengers = $_REQUEST['screened_passengers']; 
$total_passengers = $_REQUEST['total_passengers']; 

$qry_second = "
INSERT INTO `che_corona` SET ";
$qry_second .= "`airport_id`='$airport_id' ";
if(!empty($china_flights))          $qry_second .= ",`china_flights`='$china_flights' ";
if(!empty($indirect_flights))       $qry_second .= ",`indirect_flights`='$indirect_flights' ";
if(!empty($total_passengers))       $qry_second .= ",`total_passengers`='$total_passengers' ";
if(!empty($screened_passengers))    $qry_second .= ",`screened_passengers`='$screened_passengers' ";
if(!empty($china_exit))             $qry_second .= ",`china_exit`='$china_exit' ";
if(!empty($indirect_exit))          $qry_second .= ",`indirect_exit`='$indirect_exit' ";
if(!empty($suspected_passengers))   $qry_second .= ",`suspected_passengers`='$suspected_passengers' ";

if(!empty($date)){
    $date = date('Y-m-d',strtotime($date));
    $qry_second .= ",`date`='$date' ";
}

//echo $qry_second;exit;
$queryB = mysql_query($qry_second);

$output=array();
$output['msg'] = 'Saved';
//header('Content-Type: application/json');
$myJSON = json_encode($output);
echo $myJSON;