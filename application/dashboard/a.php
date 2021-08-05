<?php

session_start();
echo '<pre>';
//print_r($_SERVER);
print_r($_SESSION);
$a = $_SESSION["count_test1"];
echo $a;
if(empty($a)) {
	$a=0;
}
$b=(int)$a+1;
$_SESSION["count_test1"]=$b;
echo $a;
?>