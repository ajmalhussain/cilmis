<?php
include("../includes/classes/AllClasses.php");
//
include(PUBLIC_PATH . "html/header.php");

// Show users
if (isset($_POST['first_level'])) {
    // Get variable values
    $level = mysql_real_escape_string($_POST['first_level']);
    $province = mysql_real_escape_string($_POST['provinceId']);
    $stakeholder = mysql_real_escape_string($_POST['stakeholder_id']);
    //echo "hey stakeholder".$stakeholder."province id is".$province."level is".$level;
    $and = '';
    $check = '';

    if ($level == 1) {
        $and = '';
    } else if ($level != 1) {
        $and = " sysuser_tab.province = $province AND ";
    }
    if ($stakeholder == null) {
        $check = '';
    } else if ($stakeholder != null) {
        $check = " sysuser_tab.stkid = $stakeholder AND ";
    }

    $qry = "SELECT
sysuser_tab.UserID as user_id,
sysuser_tab.province,
sysuser_tab.stkid,
sysuser_tab.sysusr_ph as contact,
sysuser_tab.sysusr_email as email,
sysuser_tab.sysusr_name as name,
sysuser_tab.usrlogin_id as id,
sysuser_tab.user_level,
sysuser_tab.whrec_id,
tbl_locations.LocName AS prov_name,
stakeholder.stkname,
sysuser_tab.sysusr_status as status,
roles.role_name as role
,
	(
		SELECT
			(login_time)
		FROM
			tbl_user_login_log
		WHERE
			tbl_user_login_log.user_id = UserID
order by tbl_user_login_log.pk_id desc
limit 1
	) AS login_time
FROM
sysuser_tab
INNER JOIN tbl_locations ON sysuser_tab.province = tbl_locations.PkLocID
INNER JOIN stakeholder ON sysuser_tab.stkid = stakeholder.stkid
INNER JOIN roles ON roles.pk_id = sysuser_tab.sysusr_type
			WHERE
			$and $check
sysuser_tab.user_level =$level
			";

    $qryRes = mysql_query($qry);
    $num = mysql_num_rows(mysql_query($qry));
    if ($num > 0) {
        ?>
<button name="create_excel" id="create_excel" class="btn btn-success" style="float: right;" onClick="tableToExcel('export', 'sheet 1', '<?php echo 'Data'; ?>')">Export To Excel</button>  
         <div id="export">   
        <table class="table table-bordered table-condensed " style="width:100%">
            <thead>
                <tr>
                    <th class="text-center">Sr. No.</th>

                    <th>Province/Region</th>
                    <th>Stakeholder</th>
                    <th>Login User</th>
                    <th>User Role</th>
                    <th>Status</th>
                    <th>Operator Name</th>
                    <th>Contact Number</th>
                    <th>Email</th>
                    <th>Last Login</th>
                    <th>Contact History</th>
                    
                </tr>
            </thead>
            <tbody>
        <?php
        $counter = 1;
        $stakeholder = '';
        print_r($stakeholder);
        while ($row = mysql_fetch_array($qryRes)) {
//                    if ($row['stkid'] != $stakeholder) {
//                        $counter = 1;
//                        echo "<tr bgcolor=\"#D8E6FD\">";
//                        echo "<th colspan=\"3\">" . $row['stkname'] . "</th>";
//                        echo "</tr>";
//                    }
//                    else{
            echo "<tr>";
            echo "<td class=\"text-center\" width=\"60\">" . $counter++ . "</td>";
            ?>
                <td> <?php echo $row['prov_name'] ?></td>
                <td> <?php echo $row['stkname'] ?></td>
                    <?php echo "<td>" . $row['id'] . "</td>"; ?>
 <td> <?php echo $row['role'] ?></td>
                <td> <?php echo $row['status'] ?></td>
                <td> <?php echo $row['name'] ?></td>
                <td> <?php echo $row['contact'] ?></td>
                <td> <?php echo $row['email'] ?></td>
                <td> <?php echo $row['login_time'] ?></td>
                <td> <?php echo ' <a  class="pull-left " onclick="window.open(\'contact_history.php?id=' . $row['user_id'] . '&user_email=' . $row['email'] . '&phone_num=' . $row['contact'] . '\', \'_blank\', \'scrollbars=1,width=800,height=500\');"><i class="fa fa-history" style="color:black !important;padding-top:5px;font-size:25px;"></i> </a>';
            ?></td>
            <?php
            echo "</tr>";
        }
        ?>
        </tbody>
        </table>
         </div>
            <?php
        } else {
            echo "No record found";
        }
    }
