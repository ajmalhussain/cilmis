<?php
ob_start();

//  include 'db_connection.php';
include("../includes/classes/Configuration.inc.php");
include(APP_PATH . "includes/classes/db.php");
//echo '<pre>';print_r($_POST);

$locations_arr = array();
$locations_arr[1] = 'Punjab';
$locations_arr[2] = 'Sindh';
$locations_arr[3] = 'Khyber Pakhtunkhwa';
$locations_arr[4] = 'Balochistan';
$locations_arr[10] = 'Pakistan';
$product = $_POST['product'];
$filter = '';
if (!in_array('all', $product)) {
    $filter = " AND itminfo_tab.itm_id IN (" . implode($product, ',') . ")";
}
if (isset($_POST['year']) AND ! empty($_POST['year'])) {

    $years = implode(',', $_POST['year']);
    $loc_id = $_POST['location'];
    $loc_name = $locations_arr[$loc_id];
    $sql = " SELECT  GROUP_CONCAT(distinct source) as source, sum(female) as female FROM impact_calculator WHERE year in (" . $years . ") AND location = '" . $loc_id . "' ";
//    print_r($sql);exit;
    $ress = mysql_query($sql);
    $nn = mysql_num_rows($ress);
    $row = mysql_fetch_assoc($ress);
    ?>
    <div class="inline-items" id='data'>
        <?php
        if (!empty($row['female']) && $nn > 0) {


            //echo '<pre>A';print_r($row);
            ?>



            <!-- Resource -->
            <div class="form-group">
                <label for="usr">Source</label>
                <input type="text" class="form-control" value="<?php echo $row["source"]; ?>" readonly>
            </div>

            <!-- Female -->
            <div class="form-group">
                <label for="usr">Total-Population (<?= $loc_name ?>)</label>
                <input type="text" id="f_pop2" class="form-control" value="<?php echo number_format($row["female"]); ?>" readonly>
                <input type="hidden" id="f_pop" class="form-control" value="<?php echo ($row["female"]); ?>" readonly>
            </div>
            <button type="button" class="btn btn-warning inline-items" name="calculate_btn" value="Calculate" onclick="calculate()">Calculate Impact</button>



    <?php } else {
        ?>
            <p>No data found . Please configure population of this province .</p>
            <button type="button" class="btn btn-warning" id='open_modal' name='open_modal' onclick="open_popup()">Add Population</button>

            <?php
        }
    } // end of main if
    ?> 
</div>
<div id="calculate"></div>
<?php
if (isset($_POST['calculate'])) {
    if (!is_string($product)) {
        if (!in_array('all', $product)) {
            $qry_prod = "SELECT itm_name,itm_id from itminfo_tab where itminfo_tab.itm_id IN (" . implode($product, ',') . ")";
//        print_r($qry_prod);
//        exit;
            $result = mysql_query($qry_prod);
            while ($row_p = mysql_fetch_array($result)) {
                $itm_array[$row_p['itm_name']] = $row_p['itm_name'];
            }
            $item_name = implode($itm_array, ',');
        }
    }
    $years = implode(',', $_POST['selected_year']);
    $year_f = $_POST['selected_year'][0];
    $year_t = $_POST['selected_year'][0];

    foreach ($_POST['selected_year'] as $k => $year) {
        $year_t = $year;
    }
    //echo 'FROM : '.$year_f.' ,to :'.$year_t;    
    $loc_id = $_POST['location'];
    $loc_name = $locations_arr[$loc_id];

    $sql = "SELECT sum(A.total_cyp) as total_cyp,A.itm_name AS item_name  "
            . "FROM ( SELECT
                itminfo_tab.itmrec_id,
                itminfo_tab.itm_name,
                itminfo_tab.extra AS old,
                provincial_cyp_factors.cyp_factor AS extra,
                SUM(tbl_wh_data.wh_issue_up) as issuance,
                SUM(tbl_wh_data.wh_issue_up) * provincial_cyp_factors.cyp_factor AS total_cyp
            FROM
                            tbl_warehouse
            INNER JOIN tbl_wh_data ON tbl_warehouse.wh_id = tbl_wh_data.wh_id
            INNER JOIN tbl_locations ON tbl_warehouse.prov_id = tbl_locations.PkLocID
            INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
            INNER JOIN itminfo_tab ON tbl_wh_data.item_id = itminfo_tab.itmrec_id
            LEFT JOIN provincial_cyp_factors ON tbl_warehouse.prov_id = provincial_cyp_factors.province_id
            AND tbl_warehouse.stkid = provincial_cyp_factors.stakeholder_id
            AND itminfo_tab.itm_id = provincial_cyp_factors.item_id
            WHERE
             YEAR (tbl_wh_data.RptDate) in (" . $years . ") " .
            $filter
            . " AND itminfo_tab.itm_category = 1 "
            . " AND stakeholder.stk_type_id = 0 "
            . " AND stakeholder.lvl = 4 ";
    if (!empty($loc_id) && $loc_id != 10) {
        $sql .= " AND tbl_warehouse.prov_id = $loc_id ";
    }
    $sql .= " GROUP BY tbl_wh_data.item_id "
            . " ORDER BY itminfo_tab.frmindex ASC "
            . " ) "
            . " as A";
//    echo $sql;
//    exit;
    $result = mysql_query($sql);

    $test = false;
    if ($_SERVER['SERVER_ADDR'] == '::1') {
        $test = true;
        $row = array();
        $row['total_cyp'] = 5190897;
    }
    if (mysql_num_rows($result) > 0 || $test) {

        $row = mysql_fetch_array($result);
//        print_r($row);exit;
        $cyp = (int) $row['total_cyp'];
        $pop = (int) $_POST['female_pop'];


        $val_1a = $pop * 0.17;              //MWRA is 17% of total population
        $val_2a = 0.288;                    //unintended pregnancies averted per CYP
        $val_3a = ($val_2a) * (1 - 0.13);   //unintended births or abortions averted per CYP
        $val_4a = ($val_2a) * (1 - 0.6);    //unintended births averted per CYP
        $val_5a = 64 / 1000;                //infant mortality rate
        $val_6a = 260 / 100000;             //maternal mortality ratio

        $val_1b = ($cyp / $val_1a) * 100;     //CPR of 1
        $val_2b = $cyp * $val_2a;           //CPR of 2
        $val_3b = $cyp * $val_3a;           //CPR of 3
        $val_4b = $cyp * $val_4a;           //CPR of 4
        $val_5b = $val_4b * $val_5a;        //CPR of 5
        $val_6b = $val_4b * $val_6a;        //CPR of 6
        ?>

        <div class="well">
            <label for="usr"><h4>CYP : </h4></label>
            <label ><h4><?php echo number_format($row["total_cyp"]); ?></h4></label>
        </div>
        <!-- Table 1 -->

        <table class="table table-condensed table-bordered table-hover">
            <thead class="bdr text-center bg-primary ">

            <td  colspan="3">
                <b>
                    <?php
//                    print_r($item_name);
//                    exit;
                    if ((is_string($product))) {
                        ?>
                        POTENTIAL IMPACT OF USAID CONTRACEPTIVE COMMODITY AND 
                        SUPPLY CHAIN SUPPORT, GHSC-PSM, PAKISTAN <?= (($loc_id != 10) ? '(' . $loc_name . ')' : '') ?> 
                        <?php
                    } else {
                        ?>
                        POTENTIAL IMPACT OF USAID CONTRACEPTIVE COMMODITY AND 
                        SUPPLY CHAIN SUPPORT, GHSC-PSM, PAKISTAN <?= (($loc_id != 10) ? '(' . $loc_name . ')' : '') ?> 
                        <?php
                        if (!empty($item_name)) {
                            echo 'USING <h3 style="color:white;">' . $item_name . "</h3>";
                        }
                        ?>

        <?php }
        ?>
                </b>
            </td>
        </thead>

        <tr class="bdr text-center bg-primary ">
            <td colspan="2"></td>
            <td>CPR</td>
        </tr>
        <tr>
            <td><strong>Female Population, 15-49 [MWRA = 17% of Total Population)] </strong></td>
            <td align="right"><strong><?php echo number_format($val_1a); ?> </strong></td>
            <td align="right"><strong><?php echo number_format($val_1b); ?>%</strong></td>
        </tr>

        <tr>
            <td><strong>Unintended pregnancies averted per CYP</strong></td>
            <td  align="right"><strong><?php echo number_format($val_2a, 2); ?></strong></td>
            <td  align="right"><strong><?php echo number_format($val_2b); ?></strong></td>
        </tr>
        <tr >

            <td ><strong>Unintended births or abortions averted per CYP</strong></td>
            <td  align="right"><strong><?php echo number_format($val_3a, 2); ?></strong></td>
            <td  align="right"><strong><?php echo number_format($val_3b); ?></strong></td>
        </tr>
        <tr >
            <td ><strong>Unintended births averted per CYP</strong></td>
            <td  align="right"><strong><?php echo number_format($val_4a, 2); ?></strong></td>
            <td  align="right"><strong><?php echo number_format($val_4b); ?></strong></td>
        </tr>    
        <tr>
            <td ><strong>Infant Mortality Rate</strong></td>
            <td  align="right"><strong><?php echo number_format($val_5a, 2); ?></strong></td>
            <td align="right"><strong><?php echo number_format($val_5b); ?></strong></td>
        </tr> 
        <tr>
            <td><strong>Maternal Mortality Ratio</strong></td>
            <td align="right"><strong><?php echo number_format($val_6a, 4); ?></strong></td>
            <td align="right"><strong><?php echo number_format($val_6b); ?></strong></td>   
        </tr>
        </table>

        <!-- Table 2 -->
        <table class="table table-condensed table-bordered table-hover">
            <thead class="bdr text-center bg-primary ">

            <td  colspan="3">
                <b>
                    POTENTIAL IMPACT OF USAID 
                    CONTRACEPTIVE COMMODITY AND 
                    SUPPLY CHAIN SUPPORT, 
                    USAID| DELIVER PROJECT PAKISTAN <?= (($loc_id != 10) ? '(' . $loc_name . ')' : '') ?>
                </b>
            </td>
        </thead>
        <tbody>
            <tr>
                <td  width="18%"><strong>CYPs generated by commodities consumed/dispatched</strong></td>
                <td align="right"><strong><?php echo number_format($cyp); ?></strong></td>
                <td> </td>
            </tr>

            <tr>
                <td><strong>Number of unintended pregnancies averted</strong></td>
                <td align="right"><strong><?php echo number_format($val_2b); ?></strong></td>
                <td>Note: If all of these users (represented by the CYPs) had not used the USAID-funded contraception, approximately 29% of them would have become unintentionally pregnant (used rate for all developing countries for estimating unintended pregnancies averted per CYP). In other words, USAID-funded contraceptives prevented approximately this many unintended pregnancies in <?= $loc_name ?> during <?= $years ?>.</td>
            </tr>

            <tr>
                <td ><strong>Number of unintended births or abortions averted</strong></td>
                <td align="right"><strong><?php echo number_format($val_3b); ?></strong></td>
                <td>Note: If all of these users (represented by CYPs) had not used the USAID-funded contraception and had gotten pregnant, approximately 87% of those pregnant women would have had a unintended live birth or induced abortion; excludes those pregnancies ending in miscarriage (for unintended pregnancies, the sub-regional miscarriage and induced abortion rates are used). In other words, USAID-funded contraceptives prevented approximately this many unintended births and induced abortions in <?= $loc_name ?> during <?= $years ?>.</td>
            </tr>

            <tr>
                <td><strong>Number of unintended births averted</strong></td>
                <td align="right"><strong><?php echo number_format($val_4b); ?></strong></td>
                <td>Note: If all of these users (represented by CYPs) had not used the USAID-funded contraception and had gotten pregnant, approximately 40% of those pregnant women would have had a unintended live birth; excludes those pregnancies ending in miscarriage or induced abortion (for unintended pregnancies, the sub-regional miscarriage and induced abortion rates are used). In other words, USAID-funded contraceptives prevented approximately this many unintended births in <?= $loc_name ?> during <?= $years ?>.</td>
            </tr>

            <tr>
                <td><strong>Number of infant deaths averted</strong></td>
                <td align="right"><strong><?php echo number_format($val_5b); ?></strong></td>
                <td>Note: Among all of these women who had unintended births, this many infants would have died, based on the national IMRs = # of infant deaths per 1,000 live births (IMR varies significantly by country; national rates were used). In other words, USAID-funded contraceptives prevented approximately this many infant deaths in <?= $loc_name ?> during <?= $years ?>. </td>
            </tr>

            <tr>
                <td><strong>Number of maternal deaths averted</strong></td>
                <td align="right"><strong><?php echo number_format($val_6b); ?></strong></td>
                <td>Note: Among all of these women who had unintended births, this many mothers would have died, based on the national MMRs = # of maternal deaths per 100,000 live births (MMRs also vary widely by country; national rates were used). In other words, USAID-funded contraceptives prevented approximately this many maternal deaths in <?= $loc_name ?> during <?= $years ?>. </td>
            </tr>
        </tbody>
        </table>


        <!-- Table 3 -->
        <table class="table table-condensed table-bordered table-hover">
            <thead class="bdr text-center bg-primary ">

            <td  class="text-center" colspan="2">
                <b>
                    NOTES:
                </b>
            </td>




        </thead>
        <tbody>
            <tr>
                <td width="18%"><strong>CYP</strong></td>
                <td> Where each CYP equals 1 woman of reproductive age (WRA) using that method (with a partner). From USAID's web site: "CYP conversion factors are based on how a method is used, failure rates, wastage, and how many units of the method are typically needed to provide one year of contraceptive protection for a couple. The calculation takes into account that some methods, like condoms and oral contraceptives, for example, may be used incorrectly and then discarded, or that IUDs and implants may be removed before their life span is realized."</td>
            </tr>

            <tr>
                <td><strong>CPR</strong></td>
                <td># users/WRA</td>
            </tr>

            <tr >
                <td ><strong>Unintended pregnancies averted</strong></td>
                <td># users (CYPs) * rate of unintended pregnancies averted per CYP (0.288).
                    "For CYPs calculated using USAID or other conversion factors that take method use-failure into account, use a ratio of 0.288 for estimating unintended pregnancies averted per CYP….use the same impact ratio across all developing world regions."
                    Source: "Estimating Unintended Pregnancies Averted from Couple-Years of Protection (CYP)," Jacqueline Darroch and Susheela Singh, Guttmacher Institute,  Sept 30, 2011. </td>
            </tr>

            <tr >
                <td><strong>Unintended births/ abortions averted</strong></td>
                <td># users (CYPs) * births averted rate (if all of these users had not used contraception and had gotten pregnant, this many would have had a live birth or induced abortion) Adding it Up provides sub-regional estimates of miscarriage for unintended pregnancies; these sub-regional rates were used for this calculation.
                    Source: "Adding it Up: The Costs and Benefits of Investing in Family Planning and Maternal and Newborn Health, Estimation Methodology" by Jacqueline Darroch and Susheela Singh, Guttmacher Institute, Oct 2011</td>
            </tr>

            <tr>
                <td><strong>Unintended births averted</strong></td>
                <td># users (CYPs) * births averted rate (if all of these users had not used contraception and had gotten pregnant, this many would have had a live birth; excludes those whose pregnancies end in miscarriage or induced abortion). Adding it Up provides sub-regional estimates of miscarriage and induced abortion rates for unintended pregnancies; these sub-regional rates were used for this calculation.
                    Source: "Adding it Up: The Costs and Benefits of Investing in Family Planning and Maternal and Newborn Health, Estimation Methodology" by Jacqueline Darroch and Susheela Singh, Guttmacher Institute, Oct 2011</td>
            </tr>

            <tr>
                <td><strong>Infants deaths averted</strong></td>
                <td># births averted * IMR (Among all of these women who had unintended births, this many infants would have died, based on the national IMR = # of infant deaths per 1,000 live births)
                    Source: used national rates from Population Reference Bureau </td>
            </tr>

            <tr>
                <td><strong>Maternal deaths averted</strong></td>
                <td># births averted * MMR (Among all of these women who had unintended births, this many mothers would have died, based on the national MMR = # of maternal deaths per 100,000 live births)
                    Source: used national rates from Population Reference Bureau </td>
            </tr>
        </tbody>
        </table>

        <?php
    }
}

//  $conn->close();

ob_end_flush();
?> 
