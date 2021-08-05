<?php
//this api has different parameters, due to limitation from android app.
include("../../includes/classes/Configuration.inc.php");
include("../../includes/classes/db.php");
$wh = '';
if(!empty($_REQUEST['from'])){
	$wh .= " AND che_passenger_data.travelled_from= '".$_REQUEST['from']."' ";
}



$output=array();
$sql="SELECT
che_passenger_data.pk_id,
che_passenger_data.full_name,
che_passenger_data.passport_number,
che_passenger_data.contact_number,
che_master_data.arrival_date,
che_airports.airport_name,
che_carrier.carrier_id,
che_passenger_data.action,
che_passenger_data.travelled_from
FROM
che_passenger_data
INNER JOIN che_master_data ON che_passenger_data.master_data_id = che_master_data.pk_id
INNER JOIN che_airports ON che_master_data.airport_id = che_airports.pk_id
INNER JOIN che_carrier ON che_master_data.carrier_fk_id = che_carrier.pk_id
WHERE
che_passenger_data.is_temp = 0 AND
che_passenger_data.action = 1
";
$sql .= $wh;


$result=mysql_query($sql);
while($e=mysql_fetch_assoc($result))
{
	$output[] = $e;
}

print(json_encode($output));
?>