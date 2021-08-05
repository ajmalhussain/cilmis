<?php
//this api has different parameters, due to limitation from android app.
include("../../includes/classes/Configuration.inc.php");
include("../../includes/classes/db.php");
$key=0;
$output=array();
$sql="SELECT
count(*) as disp_val,
che_passenger_data.action
FROM
che_passenger_data
group by 
che_passenger_data.action
";
$result=mysql_query($sql);
$tot_susp=0;
while($e=mysql_fetch_assoc($result))
{
	$a=array();
	if($e['action']==1)$key=2;
	if($e['action']==2)$key=3;
	if($e['action']==1||$e['action']==2){
		$a['growth_rate']	=(int)$e['disp_val']; 
		$a['year']			=$key; 
		$output[] = $a;
	}
	$tot_susp+=$e['disp_val'];
}

$a['growth_rate']	=$tot_susp; 
$a['year']			=1; 
$output[] = $a;

$tot_susp=0;
$sql="SELECT
count(*) as disp_val,
che_passenger_data.action
FROM
che_passenger_data
WHERE
che_passenger_data.travelled_from = 44
group by 
che_passenger_data.action
";
$result=mysql_query($sql);
while($e=mysql_fetch_assoc($result))
{
	$a=array();
	
	if($e['action']==1)$key=5;
	if($e['action']==2)$key=6;
	if($e['action']==1||$e['action']==2){
		$a['growth_rate']	=(int)$e['disp_val'];  
		$a['year']			=$key; 
		$output[] = $a;
	}
	$tot_susp+=$e['disp_val'];
}

$a['growth_rate']	=$tot_susp; 
$a['year']			=4; 
$output[] = $a;

print(json_encode($output));
?>