<?php
//echo '<pre>';print_r($_REQUEST);exit;
//
//include AllClasses
include("../includes/classes/AllClasses.php");
?>
<html>
<html><?php include("menu.php"); ?>
    <h3 align="center">(7b) Warehouse Wise Data Comparison of <span style="color:green"> Opening Balance with last months closing balance WHDATA</span> Only Category 1</h3>
    <body>
        <form id="form1" name="form1" method="get" action="">
          <table width="100%" border="1">
            <tr>
              <td><label for="date">Date *</label></td>
              <td>
              <input type="text" name="date" id="date"  value="<?=(isset($_REQUEST['date'])?$_REQUEST['date']:'')?>"/></td>
              <td><label for="prov">Province *</label></td>
              <td>
              <input type="text" name="prov" id="prov"  value="<?=(isset($_REQUEST['prov'])?$_REQUEST['prov']:'')?>"/></td>
              <td><input type="checkbox" name="show_all" id="show_all" <?=((!empty($_REQUEST['show_all']) && $_REQUEST['show_all']=='on')?' checked ':'')?> />
              <label for="show_all">Show all</label></td>
            </tr>
            <tr>
              <td><label for="dist">District ID</label></td>
              <td>
              <input type="text" name="dist" id="dist"  value="<?=(isset($_REQUEST['dist'])?$_REQUEST['dist']:'')?>"/></td>
              <td><label for="stk">Stakeholder</label></td>
              <td>
              <input type="text" name="stk" id="stk"  value="<?=(isset($_REQUEST['stk'])?$_REQUEST['stk']:'')?>"/></td>
              <td>
              <input type="submit" name="Submit" id="Submit" value="Submit" /></td>
            </tr>
          </table>
        </form>
<?php
if(empty($_REQUEST['date'])) 
{
    echo 'Please enter date to view report';
    exit;
}
//if(empty($_REQUEST['prov'])) 
//{
//    echo 'Please enter Province ID to view report';
//    exit;
//}
if(!empty($_REQUEST['show_all']) && $_REQUEST['show_all']=='on') $show_only_mismatch=false;
else $show_only_mismatch=true;
$date = $_REQUEST['date'];
$date2  = date('Y-m-01',strtotime("-1 month", strtotime($date)));

$dist = $_REQUEST['dist'];
$stk = $_REQUEST['stk'];

$and_clause='';
$and_clause2='';

           
if(!empty($dist)){
    $and_clause.=" AND tbl_warehouse.dist_id = $dist";
    $and_clause2.="  and tbl_hf_type_data.district_id=$dist  ";
}
if(!empty($stk)){
    $and_clause.="  AND tbl_warehouse.stkid = $stk  ";  
    $and_clause2.="  and tbl_hf_type_rank.stakeholder_id =$stk  ";      
    
}
if(!empty($_REQUEST['prov'])){
    $prov=$_REQUEST['prov'];
    $and_clause.="  and tbl_warehouse.prov_id = $prov   ";  
    $and_clause2.="   AND tbl_locations.ParentID  = $prov  ";      
    
}

        
$qry_hf= "SELECT
	tbl_warehouse.dist_id,
	tbl_wh_data.item_id,
	tbl_wh_data.wh_obl_a AS opening,
	tbl_wh_data.RptDate,
	tbl_warehouse.hf_type_id, 
	tbl_warehouse.prov_id AS province,
	tbl_warehouse.wh_id,
	tbl_warehouse.wh_name,
	tbl_warehouse.stkid,
            (SELECT
                    t.wh_cbl_a AS closing
            FROM
            tbl_wh_data t
            WHERE
            t.wh_id = tbl_warehouse.wh_id
            and t.item_id  = tbl_wh_data.item_id
            and t.RptDate = '$date2'
            ) as last_closing
            FROM
                tbl_wh_data
                INNER JOIN tbl_warehouse ON tbl_wh_data.wh_id = tbl_warehouse.wh_id
		 
INNER JOIN itminfo_tab ON tbl_wh_data.item_id = itminfo_tab.itmrec_id
            WHERE
                
                tbl_wh_data.RptDate = '$date'  AND
itminfo_tab.itm_category = 1
                $and_clause  
having opening <> last_closing
";
//Query result
//echo $qry_hf;exit;
$Res =mysql_unbuffered_query($qry_hf);
$orig_data = $hf_type_arr =  array();

while($row = mysql_fetch_assoc($Res))
{
    $orig_data[$row['wh_id']][$row['item_id']]=$row;
   //echo '<pre>';print_r($row);
}
//echo '<pre>';print_r($hf_type_arr);
//$qry_summary_dist= "SELECT
//                tbl_warehouse.dist_id,
//tbl_hf_data.item_id,
//	tbl_hf_data.closing_balance AS closing,
//tbl_hf_data.reporting_date,
//tbl_warehouse.hf_type_id,
//tbl_hf_type.hf_type,
//tbl_warehouse.prov_id AS province,
//tbl_warehouse.wh_id,
//tbl_warehouse.wh_name,
//tbl_warehouse.stkid
//            FROM
//                tbl_hf_data
//                INNER JOIN tbl_warehouse ON tbl_hf_data.warehouse_id = tbl_warehouse.wh_id
//		INNER JOIN tbl_hf_type ON tbl_hf_type.pk_id = tbl_warehouse.hf_type_id
//            WHERE
//                
//                tbl_hf_data.reporting_date = '$date2' 
//                $and_clause  
//
//";
////Query result
////echo $qry_summary_dist;exit;
//$Res2 =mysql_unbuffered_query($qry_summary_dist);
//$summary_dist_data = array();
//
//while($row = mysql_fetch_assoc($Res2))
//{
//    $summary_dist_data[$row['wh_id']][$row['item_id']]=$row;
//   //echo '<pre>';print_r($row);
//}
//echo '<pre>';print_r($orig_data);exit;
//echo '<pre>';print_r($summary_dist_data);
?>
<table border="1">
    <tr>
        <td>District</td>
        <td>Stk</td>
        <td>Date</td>
        <td>WHID</td>
        <td>wh name</td>
        <td>Itm</td>
        <td>Opening Bal( This Month)</td>
        <td>Closing Bal( Last Month)</td>
        <td>Result</td>
    </tr>
    <?php
    $tot_matches = $mismatches_count = 0;
    foreach($orig_data as $wh_id => $whdata)
    {
        foreach($whdata as $itm_id => $row)
        {
            $tot_matches++;

            $hf_val  = $row['opening'];
            if(!empty($row['last_closing']))
                $summ_dist_val = $row['last_closing'];
            else
                $summ_dist_val = '';

            //if($show_only_mismatch && (int)$hf_val==(int)$summ_dist_val) continue;

            $m1=date('m',strtotime($date));
            $m2=date('m',strtotime($date2));
            $y1=date('Y',strtotime($date));
            $y2=date('Y',strtotime($date2));
            
            echo '<tr>';
            echo '<td>'.$row['dist_id'].'</td>';
            echo '<td>'.$row['stkid'].'</td>';
            echo '<td>'.$date.'</td>';
            echo '<td>'.$wh_id.'</td>';
            echo '<td>'.$row['wh_name'].'</td>';
            echo '<td>'.$itm_id.'</td>';
            echo '<td><span onclick="window.open(\'../reports/wh_info.php?whId='.$wh_id.'&month='.$m1.'&year='.$y1.' \', \'_blank\', \'scrollbars=1,width=900,height=500\')">'.$hf_val.'</span></td>';
            echo '<td><span onclick="window.open(\'../reports/wh_info.php?whId='.$wh_id.'&month='.$m2.'&year='.$y2.' \', \'_blank\', \'scrollbars=1,width=900,height=500\')">'.$summ_dist_val.'</span></td>';
            echo '<td '.(((int)$hf_val==(int)$summ_dist_val)?' >ok':'bgcolor="#ffbfbf" >MISMATCH').'</td>';
            echo ' </tr>';


            //inserting log in mismatches table...
            if( (int)$hf_val!=(int)$summ_dist_val)
            {
//                        $mismatches_count++;
//                        $del="DELETE FROM data_mismatches WHERE "
//                                . " match_type = 'hf_data_with_hf_type_data' AND  "
//                                . " district = '".$dist_id."' AND "
//                                . " reporting_date = '".$date."' AND  "
//                                . " hf_type = '".$hf_type."'AND "
//                                . " item_id = '".$itm_id."' AND status='MISMATCH' ";
//                        mysql_query($del);
//
//                        $ins = "INSERT INTO `data_mismatches` 
//                                ( `match_type`, `province`, `district`, `stakeholder`, `reporting_date`, `hf_type`, `item_id`,
//                                `table_1`, `table_2`, `bad_value_1`, `bad_value_2`, `ok_value_1`, `ok_value_2`, `status`) 
//                                VALUES 
//                                ( 'hf_data_with_hf_type_data', '".$itm_data['province']."', '".$dist_id."', NULL, '".$date."', '".$hf_type."', '".$itm_id."', 
//                                 'tbl_hf_data', 'tbl_hf_type_data', '".$hf_val."', '".$summ_dist_val."', NULL, NULL, 'MISMATCH');";
//                        mysql_query($ins);
            }
            //end of inserting log...
        }
         
    }
    
    
//    $ins = " INSERT INTO `data_mismatches_log` "
//            . "( `month`, `province`, `district`, `stakeholder`, `mismatches_count`, `out_of_possibilites_checked`,`comparison_type` ) "
//            . "VALUES ( '$date', '".$_REQUEST['prov']."', '".$dist."', '".$stk."', '".$mismatches_count."', '".$tot_matches."' ,'hf_data_with_hf_type_data'); ";
//    mysql_query($ins);
    ?>
</table>
    </body>
</html>
