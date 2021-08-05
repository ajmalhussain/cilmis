<?php

// for CORS following two headers are mandatory , later we can fix the Origin to the server address only 
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: x-requested-with');
header('Content-Type: application/json');

include("../../includes/classes/Configuration.inc.php");
include("../../includes/classes/db.php");
include("include.php");
//token is in include.php

$display_data = $fetched = array();

@$user = $_REQUEST['username'];
@$pass = $_REQUEST['password'];


$fetched_data = array();
if (!empty($_REQUEST['token']) && $_REQUEST['token'] == $token) {
    if (!empty($user) && $user != '' && !empty($pass) && $pass != '') {
        //query for login
        $qry_login = "
				SELECT 
                                        sysuser_tab.UserID, 
                                        sysuser_tab.sysusr_type, 
                                        sysuser_tab.sysusr_name, 
                                        sysuser_tab.sysusr_status, 
                                        sysuser_tab.sysusr_pwd, 
                                        sysuser_tab.sysusrrec_id, 
                                        sysuser_tab.usrlogin_id, 
                                        sysuser_tab.sysusr_cell, 
                                        sysuser_tab.sysusr_email, 
                                        tbl_warehouse.wh_id, 
                                        tbl_warehouse.wh_name, 
                                        tbl_warehouse.stkid, 
                                        tbl_warehouse.stkofficeid, 
                                        sysuser_tab.user_level AS lvl, 
                                        tbl_warehouse.prov_id, 
                                        tbl_warehouse.dist_id, 
                                        tbl_warehouse.is_allowed_im, 
                                        st.stk_type_id, 
                                        st.stkname, 
                                        sysuser_tab.province AS user_province, 
                                        sysuser_tab.stkid AS user_stk, 
                                        resources.resource_name AS landing_page, 
                                        roles.role_level, 
                                        dist_name.LocName AS district_name, 
                                        prov_name.LocName AS province_name
                                FROM
                                        sysuser_tab
                                        LEFT JOIN roles ON sysuser_tab.sysusr_type = roles.pk_id
                                        LEFT JOIN resources ON roles.landing_resource_id = resources.pk_id
                                        LEFT JOIN wh_user ON sysuser_tab.UserID = wh_user.sysusrrec_id
                                        LEFT JOIN tbl_warehouse ON wh_user.wh_id = tbl_warehouse.wh_id
                                        LEFT JOIN stakeholder as st ON tbl_warehouse.stkid = st.stkid
                                        LEFT JOIN stakeholder as sto ON tbl_warehouse.stkofficeid = sto.stkid
                                        LEFT JOIN tbl_locations AS dist_name ON tbl_warehouse.dist_id = dist_name.PkLocID
                                        LEFT JOIN tbl_locations AS prov_name ON tbl_warehouse.prov_id = prov_name.PkLocID
                                WHERE
                                        sysuser_tab.usrlogin_id = '" . $user . "' AND
                                        sysuser_tab.sysusr_pwd = '" . md5($pass) . "' AND
                                        sysusr_status = 'Active' AND
                                        wh_user.is_default = 1
                                ORDER BY
                                        sto.lvl ,wh_user.is_default desc
                                limit 1
			 ";
        //echo $qry_login;exit;
        $Res3 = mysql_query($qry_login);
        if (mysql_num_rows($Res3) > 0) {
            $row = mysql_fetch_assoc($Res3);

            //echo '<pre>';
            //print_r($prod_array);
            $fetched_data['user_id'] = $row['UserID'];
            $fetched_data['user_full_name'] = $row['sysusr_name'];
            $fetched_data['user_type'] = $row['sysusr_type'];
            $fetched_data['user_level'] = $row['lvl'];
            $fetched_data['user_cell_no'] = $row['sysusr_cell'];
            $fetched_data['user_email'] = $row['sysusr_email'];

            $fetched_data['stakeholder_id'] = $row['stkid'];
            $fetched_data['stakeholder_name'] = $row['stkname'];
            $fetched_data['province_id'] = $row['prov_id'];
            $fetched_data['province_name'] = $row['province_name'];
            $fetched_data['district_id'] = $row['dist_id'];
            $fetched_data['district_name'] = $row['district_name'];
            $fetched_data['warehouse_id'] = $row['wh_id'];
            $fetched_data['warehouse_name'] = $row['wh_name'];

            $fetched_data['stakeholder_office_id'] = $row['stkofficeid'];
            $fetched_data['im_allowed'] = $row['is_allowed_im'];


            $wh_id = $row['wh_id'];
            //query for product list
            $qry_product = "SELECT DISTINCT
                                                    itminfo_tab.itm_id,
                                                    itminfo_tab.itm_name
                                                FROM 
                                                    itminfo_tab
                                                    LEFT JOIN stock_batch ON itminfo_tab.itm_id = stock_batch.item_id
                                                WHERE
                                                    stock_batch.wh_id = '" . $wh_id . "' 
                                                ORDER BY
                                                    itminfo_tab.itm_name ASC";
            //echo $qry_product;exit;
            $prod_array = array();
            $t_array=array();
            $qryProdRes = mysql_query($qry_product);
            if (mysql_num_rows($qryProdRes) > 0) {
                while ($row1 = mysql_fetch_array($qryProdRes)) {
                   
                    $t_array['itm_id'] = $row1['itm_id'];
                    $t_array['itm_name'] = $row1['itm_name'];
                    $prod_array[] = $t_array;
                }
            }

            $fetched_data['products_list'] = $prod_array;

            //print_r($row['status']);exit;
            //getting stkid , province_id, sysrec_id
            $prov_id = $row['prov_id'];
            $stk_id = $row['stkid'];
            $sysuserrec_id = $row['UserID'];
            //query for facilities list
            $qry_facilities = "SELECT
                                                * 
                               FROM
                                   (
                                       SELECT
                                                        wh_user.sysusrrec_id,
                                                        sysuser_tab.sysusr_name,
                                                        tbl_warehouse.wh_id,
                                                        tbl_warehouse.wh_name,
                                                        sysuser_tab.usrlogin_id,
                                                        stakeholder.lvl,
                                                        tbl_hf_type_rank.hf_type_rank,
                                                        tbl_warehouse.wh_rank 
                                FROM
                                                        sysuser_tab
                                                        LEFT JOIN wh_user ON wh_user.sysusrrec_id = sysuser_tab.UserID
                                                        LEFT JOIN tbl_warehouse ON wh_user.wh_id = tbl_warehouse.wh_id
                                                        LEFT JOIN stakeholder ON stakeholder.stkid = tbl_warehouse.stkofficeid
                                                        LEFT JOIN tbl_hf_type_rank ON tbl_warehouse.hf_type_id = tbl_hf_type_rank.hf_type_id 
                                 WHERE
                                                        wh_user.sysusrrec_id = '".$sysuserrec_id."' 
                                                        AND stakeholder.lvl > 4 
                                                        AND tbl_hf_type_rank.stakeholder_id = '".$stk_id."'
                                                        AND tbl_hf_type_rank.province_id = '".$prov_id."' 
                                                        AND tbl_warehouse.is_active = 1 
                                  order by wh_name
                                                ) A 
                                GROUP BY
                                                A.wh_name 
                                ORDER BY
                                         
                                                A.wh_name ASC";
//            echo $qry_facilities;exit;
            
              $facility_array = array();
              $t_array = array();
            $qryFacRes = mysql_query($qry_facilities);
            if (mysql_num_rows($qryFacRes) > 0) {
                while ($row2 = mysql_fetch_array($qryFacRes)) {
                    $t_array['wh_id']      = $row2['wh_id'];
                    $t_array['wh_name']    = $row2['wh_name'];
                    $facility_array[]           = $t_array;
                }
            }
            $fetched_data['facilities_list'] = $facility_array;
            $resp_code = '1';
            $msg = 'ok';
            $display_data['data'] = $fetched_data;
        } else {
            $resp_code = '2';
            $msg = 'Credentials are incorrect';
        }
    } else {
        $resp_code = '3';
        $msg = 'Invalid Request Parameters';
    }
} else {
    $resp_code = '4';
    $msg = 'Invalid Token';
}
$display_data['msg'] = $msg;
$display_data['response_code'] = $resp_code;

echo json_encode($display_data);

//------ Response Code : --------------------------------
//----1 = success, 
//----2 = wrong credentials, 
//----3 = Invalid Request Parameters , 
//----4 = Invalid Security Token
//----5 = No Data Found
//-------------------------------------------------------