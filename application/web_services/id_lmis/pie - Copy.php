<?php
include("../../includes/classes/Configuration.inc.php");
include("../../includes/classes/db.php");
$output=array();
$sql="SELECT
count(*) as susp_total
FROM
che_passenger_data
WHERE
che_passenger_data.action = 1
";
$result=mysql_query($sql);
while($e=mysql_fetch_assoc($result))
{
	$a=array();
	
	$a['growth_rate']=$e['susp_total']; 
	$a['year']='Total Suspected Patients'; 
}
$output[] = $a;
$sql="SELECT
count(*) as susp_china
FROM
che_passenger_data
WHERE
che_passenger_data.action = 1 AND
che_passenger_data.travelled_from = 44
";
$result=mysql_query($sql);
while($e=mysql_fetch_assoc($result))
{
	$a=array();
	
	$a['growth_rate']=$e['susp_china']; 
	$a['year']='Suspected Patients From China'; 
}

$output[] = $a;
print(json_encode($output));
?>