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
$kpdistrict = array();
if (!empty($_REQUEST['district'])) {
    $kpdistrict = $_REQUEST['district'];
}


$itm_arr_request = array();
$itm_arr_request = (!empty($_REQUEST['district'])) ? $_REQUEST['district'] : '';
$full_supply_prods = array();
$full_supply_prods[14] = '14';
$full_supply_prods[77] = '77';
$full_supply_prods[149] = '149';
$full_supply_prods[93] = '93';

$user_lvl = (!empty($_SESSION['user_level']) ? $_SESSION['user_level'] : '');
//print_r($_SESSION);exit;
$category = '1,4';
$stk_where = " ";
if ($_SESSION['user_stakeholder1'] == 145) {
    $category = '5';
    $stk_where = " AND stakeholder.stkid in (2,7,276,74,145) ";
}

if ($_SESSION['user_stakeholder1'] == 2) {
    $category = '5';
    $stk_where = " AND stakeholder.stkid in (2,7,73) ";
}

if ($_SESSION['user_level'] >= 3) {

    //$stk_where = " AND stakeholder.stkid = ".$_SESSION['user_stakeholder1']." ";
}
if ($_SESSION['user_stakeholder1'] == 276) {
    $category = '5';
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
        if ($_SESSION['user_stakeholder1'] == 145) {
            $arrayProv = array(
                '1' => 'Central',
                '2' => 'Province',
                // '3' => 'Division',
                '3' => 'District',
                '7' => 'Health Facility',
                '8' => 'Individuals'
            );
        } elseif ($_SESSION['user_stakeholder1'] == 2) {
            $arrayProv = array(
                //'1' => 'Central',
                //'2' => 'Province',
                // '3' => 'Division',
                '3' => 'District',
                '7' => 'Health Facility',
                //'8' => 'Individuals'
            );
        } else {
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
        if ($_SESSION['user_stakeholder1'] == 1 && $_SESSION['user_province1'] == 1) {
            $arrayProv = array(
                //'1' => 'Central',
                //  '3' => 'Division',
                '2' => 'Province',
                '3' => 'District',
                '7' => 'Health Facility',
            );
        } elseif ($_SESSION['user_stakeholder1'] == 7 || $_SESSION['user_stakeholder1'] == 951) {
            $arrayProv = array(
                //'1' => 'Central',
                //  '3' => 'Division',
                '2' => 'Province',
                '3' => 'District',
                '7' => 'Health Facility',
            );
        } else {
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
    .input-small {
        width: 140px !important;
    }
</style>

<div class="col-md-2">
    <div class="form-group">
        <label class="control-label">Stakeholder</label>
        <div class="form-group">
            <select name="stakeholder" id="stakeholder" required class="form-control input-sm">

                <?php
                if (!empty($_SESSION['user_stakeholder1'])) {
                    $querys = "SELECT
                                                                        stakeholder.stkid,
                                                                        stakeholder.stkname
                                                                FROM
                                                                        stakeholder
                                                                WHERE
                                                                        stakeholder.ParentID IS NULL
                                                                AND stakeholder.stk_type_id IN (0, 1)
                                                                AND stakeholder.is_reporting = 1
                                                                AND stakeholder.lvl = 1
                                                                AND stakeholder.stkid = " . $_SESSION['user_stakeholder1'] . "
                                                                ORDER BY
                                                                        stakeholder.stkorder";
                } else { ?> <option value="">Select</option> <?php
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
                                                            }
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
<div class="col-md-2 hidden">
    <div class="form-group hidden">
        <label class="control-label hidden">Report By</label>
        <div class="form-group hidden">
            <select name="report_by" id="report_by" required class="form-control input-sm hidden">
                <option value="1" <?php if($_REQUEST['report_by'] == 1) { ?>selected=""<?php } ?>>Product</option>
                <option value="2" <?php if($_REQUEST['report_by'] == 2) { ?>selected=""<?php } ?>>Generic Name</option>
            </select>
        </div>
    </div>
</div>
<div class="col-md-2">
    <div class="form-group">
        <label class="control-label">Province</label>
        <div class="form-group">
            <select name="prov_sel" id="prov_sel" class="form-control input-sm">

                <?php
                if (!empty($_SESSION['user_province1'])) {
                    $queryprov = "SELECT
                                                                                tbl_locations.PkLocID AS prov_id,
                                                                                tbl_locations.LocName AS prov_title
                                                                        FROM
                                                                                tbl_locations
                                                                        WHERE
                                                                                tbl_locations.LocLvl = 2
                                                                        AND tbl_locations.PkLocID = " . $_SESSION['user_province1'];
                } else { ?> <option value="">Select</option> <?php
                                                                $queryprov = "SELECT
                                                                            tbl_locations.PkLocID AS prov_id,
                                                                            tbl_locations.LocName AS prov_title
                                                                        FROM
                                                                            tbl_locations
                                                                        WHERE
                                                                            LocLvl = 2
                                                                        AND parentid IS NOT NULL ";
                                                            }
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
    <div class="col-md-12">
        <div class="form-group">
            <label class="control-label">
                <span class="">
                    <label>Districts</label>
                </span>
            </label>
            <select name="district[]" id="district" size="4" class="multiselect-ui form-control input-sm" multiple>
                <!--<option value="">All</option>-->
                <?php
                //select query
                //gets
                //district id
                //district name
                if (!empty($_SESSION['user_province1'])) {
                    $queryDist = "SELECT
                                                                        tbl_locations.PkLocID,
                                                                        tbl_locations.LocName
                                                                FROM
                                                                        tbl_locations
                                                                INNER JOIN project_locations ON project_locations.location_id = tbl_locations.PkLocID
                                                                WHERE 
                                                                project_locations.project = 'afpak'
                                                                ORDER BY
                                                                        tbl_locations.LocName ASC";
                } else {
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
                }
                //query result
                $rsDist = mysql_query($queryDist) or die();
                //fetch result
                $dist_name = '';
                while ($rowDist = mysql_fetch_array($rsDist)) {

                    //                                                            $sel='';
                    //                                                                        $styleit = "display:none;";
                    //                                                                        $cls2 = "";
                    //                                                             if ($kpdistrict == $rowDist['PkLocID']) {
                    //                                                                $sel = "selected='selected'";
                    //                                                                $dist_name = $rowDist['LocName'];
                    //                                                            } else {
                    //                                                                $sel = "";
                    //                                                            }
                    if (in_array($rowDist['PkLocID'], $full_supply_prods)) {
                        if (empty($itm_arr_request)) {
                            $sel = "selected='selected' ";
                            $cls2 = "full_funded";
                            $styleit = "";
                        } elseif (!empty($itm_arr_request) && in_array($rowDist['PkLocID'], $itm_arr_request)) {
                            $sel = "selected='selected' ";
                            $itm_name[] = $rowDist['LocName'];
                            $cls2 = "full_funded";
                            $styleit = "";
                        } else {
                            $sel = " ";
                            $cls2 = "full_funded";
                            $styleit = "";
                        }
                    } else {
                        $sel = " ";
                        $styleit = "display:none";
                        $cls2 = "";
                    }
                    //populate district combo
                ?>
                    <option class="<?= $cls2 ?>" value="<?php echo $rowDist['PkLocID']; ?>" <?php echo $sel; ?> style="<?= $styleit ?>"><?php echo $rowDist['LocName']; ?></option>
                <?php
                }
                ?>
            </select>
            <label class="control-label">
                <span class="">
                    <label class="text-danger">By press CTRL key you can select multiple values</label>
                </span>
            </label>

        </div>
    </div>
</div>