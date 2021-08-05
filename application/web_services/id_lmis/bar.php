<?php
//this api has different parameters, due to limitation from android app.
include("../../includes/classes/Configuration.inc.php");
include("../../includes/classes/db.php");

$display_type = 'old';
if(!empty($_REQUEST['display_type']))$display_type = $_REQUEST['display_type'];

$output=array();
$output2=array();
$sql="
SELECT
count(*) as ct
FROM
che_passenger_data
WHERE
che_passenger_data.is_temp = 0

";
$result=mysql_query($sql);
while($e=mysql_fetch_assoc($result))
{
	$output['tr_total'] = (int)$e['ct'];
	$a = array();
	$a['growth_rate'] = (int)$e['ct'];
	$a['year'] = 101;
	$output2[]=$a;
}
$sql="
SELECT
Count(*) AS ct
FROM
che_passenger_data
WHERE
che_passenger_data.is_temp = 0 AND
che_passenger_data.flight_mode = 'Direct' AND
che_passenger_data.travelled_from = 44


";
$result=mysql_query($sql);
while($e=mysql_fetch_assoc($result))
{
	$output['tr_direct_from_china'] = (int)$e['ct'];
	$a = array();
	$a['growth_rate'] = (int)$e['ct'];
	$a['year'] = 102;
	$output2[]=$a;
}


	$output['tr_other_countries'] = $output['tr_total'] - $output['tr_direct_from_china'];
	$a = array();
	$a['growth_rate'] = $output['tr_other_countries'];
	$a['year'] = 103;
	$output2[]=$a;


$sql="
SELECT
count( distinct 
che_master_data.arrival_date,
che_master_data.carrier_fk_id) as ct
FROM
che_passenger_data
INNER JOIN che_master_data ON che_passenger_data.master_data_id = che_master_data.pk_id
WHERE
che_master_data.is_temp = 0
";
$result=mysql_query($sql);
while($e=mysql_fetch_assoc($result))
{
	$output['flights_total'] = (int)$e['ct'];
	$a = array();
	$a['growth_rate'] = (int)$e['ct'];
	$a['year'] = 104;
	$output2[]=$a;
}


$sql="
SELECT
count( distinct 
che_master_data.arrival_date,
che_master_data.carrier_fk_id) as ct
FROM
che_passenger_data
INNER JOIN che_master_data ON che_passenger_data.master_data_id = che_master_data.pk_id
WHERE
che_master_data.is_temp = 0 AND
che_passenger_data.travelled_from = 44 AND
che_passenger_data.flight_mode = 'Direct'

";
$result=mysql_query($sql);
while($e=mysql_fetch_assoc($result))
{
	$output['flights_direct_from_china'] = (int)$e['ct'];
	$a = array();
	$a['growth_rate'] = (int)$e['ct'];
	$a['year'] = 105;
	$output2[]=$a;
}

	$output['flights_other_countries'] = $output['flights_total'] - $output['flights_direct_from_china'];
	$a = array();
	$a['growth_rate'] = $output['flights_other_countries'];
	$a['year'] = 106;
	$output2[]=$a;

	//echo '<pre>';
	//print_r($output);
if($display_type == 'new'){
	print(json_encode($output));
}
else{
	print(json_encode($output2));
}
?>