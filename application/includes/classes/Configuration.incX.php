<?php
//error_reporting(0);
session_start();
date_default_timezone_set('Asia/Karachi');

$db_host = 'localhost';
$db_user = 'root';
$db_password = '';
$db_name = 'db';

$e2eHost = "";
$e2eUser = "";
$e2ePass = "";
$e2eDB = "";

define("DB_HOST",$db_host);
define("DB_USER",$db_user);
define("DB_PASS",$db_password);
define("DB_NAME",$db_name);

$conn = mysql_connect($db_host, $db_user, $db_password);
if (!$conn) {
    die('Could not connect: ' . mysql_error());
}

mysql_select_db($db_name) or die("cannot select DB");
//hf array
$hfArr = array(5, 2, 3, 9, 6, 7, 8, 12, 10, 11);

if($_SERVER['SERVER_ADDR'] == '::1' || $_SERVER['SERVER_ADDR'] == 'localhost'  || $_SERVER['SERVER_ADDR'] == '127.0.0.1') {
	define('SITE_URL', 'http://'.$_SERVER['SERVER_NAME'].':'.$_SERVER['SERVER_PORT'].'/clmis/');
	define('SITE_PATH', $_SERVER['DOCUMENT_ROOT'].'/clmis/');
} else {
	define('SITE_URL', 'http://'.$_SERVER['SERVER_NAME'].':'.$_SERVER['SERVER_PORT'].'/clmisapp/');
	define('SITE_PATH', $_SERVER['DOCUMENT_ROOT'].'/clmisapp/');
}
define('PUBLIC_URL', SITE_URL.'public/');
define('PUBLIC_PATH', SITE_PATH.'public/');
define('APP_URL', SITE_URL.'application/');
define('APP_PATH', SITE_PATH.'application/');
//------------------------------END OF CONFIG-----------------------------------------







function Login() {
    
	if(!isset($_SESSION['user_id'])) {
		$location = SITE_URL.'index.php';
		?>
		<script type="text/javascript">
			window.location = "<?php echo $location;?>";
		</script>
		<?php
	}
}

function force_login_as_guest() {
    
	if(!isset($_SESSION['user_id'])) {
                    $_SESSION['user_id'] =2054;
                    $_SESSION['user_role'] =16;
                    $_SESSION['user_name'] ='Guest';
                    $_SESSION['user_warehouse'] =123;
                    $_SESSION['user_stakeholder'] =1;
                    $_SESSION['user_stakeholder_office'] =1;
                    $_SESSION['user_level'] ='';
                    $_SESSION['user_province'] =10;
                    $_SESSION['user_district'] =151;
                    $_SESSION['is_allowed_im'] =1;
                    $_SESSION['im_start_month'] ='';
                    $_SESSION['user_stakeholder_type'] =0;
                    $_SESSION['user_province1'] =10;
                    $_SESSION['user_stakeholder1'] =1;
                    $_SESSION['landing_page'] ='application/dashboard/dashboard.php';
                    $_SESSION['menu'] ='/home/clmispk/public_html/public/html/top.php';

                    $_SESSION['alert_counts']['stock_out_alert_count'] =0;
                    $_SESSION['alert_counts']['expiry_alert_count'] = 0;
                    $_SESSION['alert_counts']['shipment_alert_count'] =0;

                    $_SESSION['im_open'] =1;

	}
}

function force_login_as_edashbaord() {

    if (!isset($_SESSION['user_id'])) {
        $_SESSION['user_id'] = 10196;
        $_SESSION['user_role'] = 78;
        $_SESSION['user_name'] = 'Executive Dashboard';
        $_SESSION['user_warehouse'] = 123;
        $_SESSION['user_stakeholder'] = 1;
        $_SESSION['user_stakeholder_office'] = 1;
        $_SESSION['user_level'] = '';
        $_SESSION['user_province'] = 10;
        $_SESSION['user_district'] = 151;
        $_SESSION['is_allowed_im'] = 1;
        $_SESSION['im_start_month'] = '';
        $_SESSION['user_stakeholder_type'] = 0;
        $_SESSION['user_province1'] = 10;
        $_SESSION['user_stakeholder1'] = 1;
        $_SESSION['landing_page'] = 'application/dashboard/executive_dashboard.php';
        $_SESSION['menu'] = '/home/clmispk/public_html/public/html/top.php';

        $_SESSION['alert_counts']['stock_out_alert_count'] = 0;
        $_SESSION['alert_counts']['expiry_alert_count'] = 0;
        $_SESSION['alert_counts']['shipment_alert_count'] = 0;

        $_SESSION['im_open'] = 1;
    }
}

?>