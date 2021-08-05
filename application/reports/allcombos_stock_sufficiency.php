<?php
/**
 * levelcombos_all_levels
 * @package im
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
//user level
$user_lvl = (!empty($_SESSION['user_level']) ? $_SESSION['user_level'] : '' );
//print_r($_SESSION);exit;
$category = '1,4';
$stk_where = " ";
if($_SESSION['user_stakeholder1'] == 145){
    $category='5';
    $stk_where = " AND stakeholder.stkid in (2,7,276,74,145) ";
}

if($_SESSION['user_stakeholder1'] == 2){
    $category='5';
    $stk_where = " AND stakeholder.stkid in (2,7,73) ";
}

if($_SESSION['user_level'] >= 3){
 
    //$stk_where = " AND stakeholder.stkid = ".$_SESSION['user_stakeholder1']." ";
}
if( $_SESSION['user_stakeholder1'] == 276){
    $category='5';
    $stk_where = " AND stakeholder.stkid in (2,7,276,74,145) ";
}
//page name
$_SESSION['page_name'] = basename($_SERVER['PHP_SELF']);
//check user level
switch ($user_lvl) {
    case 1:
        $arrayProv = array(
            '1' => 'National',
            '2' => 'Province',
            '3' => 'District',
            '4' => 'Field/Tehsil/Town',
            '7' => 'Health Facility',
            '8' => 'Individuals'
        );
        break;
    case 2:
        if($_SESSION['user_stakeholder1'] == 145){
            $arrayProv = array(
                '1' => 'Central',
                '2' => 'Province',
                // '3' => 'Division',
                '3' => 'District',
                '7' => 'Health Facility',
                '8' => 'Individuals'
                );
        }
        elseif($_SESSION['user_stakeholder1'] == 2){
            $arrayProv = array(
                //'1' => 'Central',
                //'2' => 'Province',
                // '3' => 'Division',
                '3' => 'District',
                '7' => 'Health Facility',
                //'8' => 'Individuals'
                );
        }
        else{
            $arrayProv = array(
                '1' => 'Central',
                '2' => 'Province',
                // '3' => 'Division',
                '3' => 'District',
                '7' => 'Health Facility',
                //'8' => 'Individuals'
                );
        }
        
        break;
    case 3:
        if($_SESSION['user_stakeholder1'] == 1 && $_SESSION['user_province1'] == 1 ){
                $arrayProv = array(
                    //'1' => 'Central',
                    //  '3' => 'Division',
                    '2' => 'Province',
                    '3' => 'District',
                    '7' => 'Health Facility',
                );
        }elseif ($_SESSION['user_stakeholder1'] == 7 || $_SESSION['user_stakeholder1'] == 951) {
            $arrayProv = array(
                //'1' => 'Central',
                //  '3' => 'Division',
                '2' => 'Province',
                '3' => 'District',
                '7' => 'Health Facility',
            );
        }
        else{
            $arrayProv = array(
                    //'1' => 'Central',
                    //  '3' => 'Division',
                    '3' => 'District',
                    '7' => 'Health Facility',
                );
        }
        
        break;
    case 4:
        $arrayProv = array(
            '3' => 'District',
            '4' => 'Tehsil/Town',
            '7' => 'Health Facility'
        );
        break;


    default:
        $arrayProv = array(
            '1' => 'Central',
            '2' => 'Province',
            //   '3' => 'Division',
            '3' => 'District'
                //  '6' => 'Union Council'
        );
        break;
}
?>
<style>
    .input-small{width:140px !important;}
</style>

     <div class="col-md-2">
                                                <div class="form-group">
                                                    <label class="control-label">Stakeholder</label>
                                                    <div class="form-group">
                                                        <select name="stakeholder" id="stakeholder" required class="form-control input-sm">
                                                            <option value="">Select</option> <?php
                                                                  $querys = "SELECT
                                                        stakeholder.stkid,
                                                        stakeholder.stkname
                                                        FROM
                                                        stakeholder
                                                        WHERE
                                                        stakeholder.ParentID IS NULL
                                                        AND stakeholder.stk_type_id IN (0, 1) AND
                                                        stakeholder.is_reporting = 1 AND stakeholder.lvl=1
                                                        $where
                                                        ORDER BY
                                                        stakeholder.stkorder";  
                                                            //query result
                                                            $rsprov = mysql_query($querys) or die();
                                                            $stk_name = '';
                                                            while ($rowp = mysql_fetch_array($rsprov)) {
                                                                if ($_SESSION['user_stakeholder'] == $rowp['stkid']) {
                                                                    $sel = "selected='selected'";
                                                                    $stk_name = $rowp['stkname'];
                                                                } else {
                                                                    $sel = "";
                                                                }
                                                                //Populate prov_sel combo
                                                                ?>
                                                                <option value="<?php echo $rowp['stkid']; ?>" <?php echo $sel; ?>><?php echo $rowp['stkname']; ?></option>
                                                                <?php
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
   <div class="col-md-2">
                                                <div class="form-group">
                                                    <label class="control-label">Province</label>
                                                    <div class="form-group">
                                                        <select name="prov_sel" id="prov_sel"  class="form-control input-sm">
                                                            
                                                            <option value="">Select</option> <?php
                                                               $queryprov = "SELECT
                                                                            tbl_locations.PkLocID AS prov_id,
                                                                            tbl_locations.LocName AS prov_title
                                                                        FROM
                                                                            tbl_locations
                                                                        WHERE
                                                                            LocLvl = 2
                                                                        AND parentid IS NOT NULL ";    
                                                                
                                                                //query result
                                                                $rsprov = mysql_query($queryprov) or die();
                                                                $prov_name = '';
                                                                while ($rowprov = mysql_fetch_array($rsprov)) {
                                                                    if ($_SESSION['user_province1'] == $rowprov['prov_id']) {
                                                                        $sel = "selected='selected'";
                                                                        $prov_name = $rowprov['prov_title'];
                                                                    } else {
                                                                        $sel = "";
                                                                    }
                                                                    //Populate prov_sel combo
                                                                    ?>
                                                                    <option value="<?php echo $rowprov['prov_id']; ?>" <?php echo $sel; ?>><?php echo $rowprov['prov_title']; ?></option>
                                                                    <?php
                                                                } 
                                                            ?>                        
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
											 <div class="col-md-2">
                                            <div class="control-group" id="districtsCol">
                                                <label>District</label>
                                                <div class="controls">
                                                    <select name="district" id="district"  class="form-control input-sm" required>
                                                        <?php
                                                        //select query
                                                        //gets
                                                        //district id
                                                        //district name
                                                        
                                                                        $queryDist = "SELECT
                                                                                tbl_locations.PkLocID,
                                                                                tbl_locations.LocName
                                                                        FROM
                                                                                tbl_locations
                                                                        WHERE
                                                                                tbl_locations.LocLvl = 3
                                                                        AND tbl_locations.parentid = '" . $_SESSION['user_province1'] . "'
                                                                        ORDER BY
                                                                                tbl_locations.LocName ASC"; 
                                                                
                                                        //query result
                                                        $rsDist = mysql_query($queryDist) or die();
                                                        //fetch result
                                                        $dist_name ='';
                                                        while ($rowDist = mysql_fetch_array($rsDist)) {
                                                             if ($_SESSION['user_district'] == $rowDist['PkLocID']) {
                                                                $sel = "selected='selected'";
                                                                $dist_name = $rowDist['LocName'];
                                                            } else {
                                                                $sel = "";
                                                            }
                                                            //populate district combo
                                                            ?>
                                                            <option
                                                                value="<?php echo $rowDist['PkLocID']; ?>" <?php echo $sel; ?>><?php echo $rowDist['LocName']; ?></option>
                                                            <?php
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>