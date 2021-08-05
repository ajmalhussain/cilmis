
		<?php
		include("../../includes/classes/Configuration.inc.php");
		include("../../includes/classes/db.php");
		$output=array();
		$sql="SELECT pk_id,name FROM che_trans_service";
		$result=mysql_query($sql);
		while($e=mysql_fetch_assoc($result))
		{
			$output['trans'][$e['pk_id']]=$e['name']; 
		}
		$sql="SELECT   pk_id,country_name  FROM che_countries";
		$result=mysql_query($sql);
		while($e=mysql_fetch_assoc($result))
		{
			$output['countries'][$e['pk_id']]=$e['country_name']; 
		}
		$sql="SELECT   pk_id,airport_name FROM che_airports";
		$result=mysql_query($sql);
		while($e=mysql_fetch_assoc($result))
		{
			$output['airports'][$e['pk_id']]=$e['airport_name']; 
		}
		print(json_encode($output)); 

		?>