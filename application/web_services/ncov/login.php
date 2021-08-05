<?php

include("../../includes/classes/Configuration.inc.php");
include("../../includes/classes/db.php");

$username = $_REQUEST['user'];
$password = md5($_REQUEST['pass']); 

$qry_second = "SELECT
che_user.`password`,
che_user.username
FROM
che_user
WHERE
username = '".$username."' AND
`password` = '".$password."'
";
//echo $qry_second;exit;
$queryB = mysql_query($qry_second);

$out = array();

while ($row_wh = mysql_fetch_assoc($queryB)) {
    $out['username'] 	= $row_wh['username'];
    $out['status'] 		= 'Active';

}

$output['che_user'] = $out;
//header('Content-Type: application/json');
$myJSON = json_encode($output);
echo $myJSON;