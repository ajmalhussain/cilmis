<?php
include("../includes/classes/Configuration.inc.php");
Login();
//include db
include(APP_PATH . "includes/classes/db.php");
//include functions
include APP_PATH . "includes/classes/functions.php";
?>
<h2 class="center"><b>Diarrhea and Pneumonia : Products &  Funding gaps</b></h2>
    <?php
$province = (!empty($_REQUEST['prov_sel']) ? $_REQUEST['prov_sel'] : '');
$qry = "SELECT image_path from diarrhea_pneumonia where province_id=$province";
$result = mysql_query($qry);
while ($row = mysql_fetch_array($result)) {
    ?>
<img src="<?php  echo PUBLIC_URL.$row['image_path']; ?>" style="padding-left:15%;padding-top:2%;">
    <?php
}
?> 
