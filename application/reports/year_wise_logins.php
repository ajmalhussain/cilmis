<?php
ini_set('max_execution_time', 0);
include("../includes/classes/Configuration.inc.php");
include(APP_PATH . "includes/classes/db.php");
include(PUBLIC_PATH . "html/header.php");
include(PUBLIC_PATH . "/FusionCharts/Code/PHP/includes/FusionCharts.php");

$caption = 'what';
$downloadFileName = $caption . ' - ' . date('Y-m-d H:i:s');
$chart_id = 'b2';

$qry = "SELECT
	itm_id,
	SUM(opening_balance) as total,itm_name
FROM
	tbl_hf_data
INNER JOIN itminfo_tab ON tbl_hf_data.item_id = itminfo_tab.itm_id
GROUP BY itm_id";
$qryRes = mysql_query($qry);
?>
<body class="page-header-fixed page-quick-sidebar-over-content">
    <div class="page-container">
        <?php
        include PUBLIC_PATH . "html/top.php";
        include PUBLIC_PATH . "html/top_im.php";
        ?>

        <div class="page-content-wrapper">
            <div class="page-content">
                <script language="Javascript" src="<?php echo PUBLIC_URL; ?>FusionCharts/Charts/FusionCharts.js"></script>
                <script language="Javascript" src="<?php echo PUBLIC_URL; ?>FusionCharts/themes/fusioncharts.theme.fint.js"></script>

                <div class="page-container">
     <?php               
$qry_summary_dist= "
    SELECT
        (CASE WHEN (sysuser_tab.province<1) THEN 10 ELSE sysuser_tab.province  END) as province,
        (CASE WHEN (sysuser_tab.province<1) THEN 'National' ELSE tbl_locations.LocName  END) as province_name,
        DATE_FORMAT(tbl_user_login_log.login_time,'%Y') as `year`,
        count(distinct  tbl_user_login_log.user_id) as Active_Users_Count
        FROM
        tbl_user_login_log
        INNER JOIN sysuser_tab ON tbl_user_login_log.user_id = sysuser_tab.UserID
        LEFT JOIN tbl_locations ON sysuser_tab.province = tbl_locations.PkLocID
        group by  
        (CASE WHEN (sysuser_tab.province<1) THEN 10 ELSE sysuser_tab.province  END),
        DATE_FORMAT(tbl_user_login_log.login_time,'%Y')
        ORDER BY 
        sysuser_tab.province ,DATE_FORMAT(tbl_user_login_log.login_time,'%Y')

";
//Query result
//echo $qry_summary_dist;
$Res2 =mysql_query($qry_summary_dist);
$display_data  = $columns_data = $pro_arr =  array();

while($row = mysql_fetch_assoc($Res2))
{
   $display_data[$row['province_name']][$row['year']] = $row['Active_Users_Count'];
   $columns_data[$row['year']]=$row['year'];
   $pro_arr[$row['province']]=$row['province_name'];
   //echo '<pre>';print_r($row);
}

ksort($pro_arr);
ksort($columns_data);
   // echo '<pre>';print_r($pro_arr);print_r($display_data);
?>
        <h3>Year Wise Number of Active Users</h3>
<table width ="80%" border="1" class="table table-condensed table-striped left" >
    <tr bgcolor="#afb5ea">
        <?php
        echo '<td>#</td>';
        echo '<td>Province / Year</td>';
        foreach($columns_data as $k=>$v)
        {
           echo '<td>'.$v.'</td>';
        }
        ?>
    </tr>
    
    <?php
    $count_of_row = 0;
        foreach($pro_arr as $pro_id => $pro_name){
            
               echo '<tr>';
               echo '<td>'.++$count_of_row.'</td>';
               echo '<td>'.$pro_name.'</td>';
               foreach($columns_data as $k2=>$col)
               {
                echo ' <td>'.@$display_data[$pro_name][$k2].'</td>';
               }   
               echo '<tr>';
        }
        ?>
</table>
        
        
        <?php

$qry_summary_dist= "
    SELECT
            (CASE WHEN (sysuser_tab.province<1) THEN 10 ELSE sysuser_tab.province  END) as province,
            (CASE WHEN (sysuser_tab.province<1) THEN 'National' ELSE tbl_locations.LocName  END) as province_name,
            DATE_FORMAT(tbl_user_login_log.login_time,'%Y') as `year`,
            count(   tbl_user_login_log.user_id) as Active_Users_Count
            FROM
            tbl_user_login_log
            INNER JOIN sysuser_tab ON tbl_user_login_log.user_id = sysuser_tab.UserID
            LEFT JOIN tbl_locations ON sysuser_tab.province = tbl_locations.PkLocID
            group by  
            (CASE WHEN (sysuser_tab.province<1) THEN 10 ELSE sysuser_tab.province  END),
            DATE_FORMAT(tbl_user_login_log.login_time,'%Y')
            ORDER BY 
            sysuser_tab.province ,DATE_FORMAT(tbl_user_login_log.login_time,'%Y')

";
//Query result
//echo $qry_summary_dist;
$Res2 =mysql_query($qry_summary_dist);
$display_data    = $pro_arr =  array();

while($row = mysql_fetch_assoc($Res2))
{
   $display_data[$row['province_name']][$row['year']] = $row['Active_Users_Count'];
   $columns_data[$row['year']]=$row['year'];
   $pro_arr[$row['province']]=$row['province_name'];
   
   //echo '<pre>';print_r($row);
}

ksort($pro_arr);
   // echo '<pre>';print_r($pro_arr);print_r($display_data);
?>
        <h3>Year Wise Number of Login Counts</h3>
<table width ="80%" border="1" class="table table-condensed table-striped left" >
    <tr bgcolor="#afb5ea">
        <?php
        echo '<td>#</td>';
        echo '<td>Province / Year</td>';
        foreach($columns_data as $k=>$v)
        {
           echo '<td>'.$v.'</td>';
        }
        ?>
    </tr>
    
    <?php
    $count_of_row = 0;
        foreach($pro_arr as $pro_id => $pro_name){
            
               echo '<tr>';
               echo '<td>'.++$count_of_row.'</td>';
               echo '<td>'.$pro_name.'</td>';
               foreach($columns_data as $k2=>$col)
               {
                echo ' <td>'.@$display_data[$pro_name][$k2].'</td>';
               }   
               echo '<tr>';
        }
        ?>
</table>
                    <?php
                    ksort($columns_data);
                    ?>
                    <div class="row">
                        <div class="widget" data-toggle="">
                            <div class="widget-head">
                                <h3 class="heading">Year wise Login Trend</h3>
                            </div>
                            <div class="widget widget-tabs">    
                                <div class="widget-body">
                                    <a href="javascript:exportChart('<?php echo $chart_id; ?>', '<?php echo $downloadFileName; ?>')" style="float:right;"><img class="export_excel" src="<?php echo PUBLIC_URL; ?>images/excel-16.png" alt="Export" /></a>
                                        <?php
                                        $xmlstore = '<chart caption="" yaxismaxvalue="100"  subcaption="" xaxisname="Products" exportEnabled="1"  yaxisname="Percentage"      numberprefix="" theme="fint">';

                                        $xmlstore .= '  <categories>';
                                        foreach ($columns_data as $id => $year) {
                                            $xmlstore .= '  <category label="' . $year . '" />';
                                        }
                                        $xmlstore .= '  </categories>';

                                        foreach ($display_data as $pro => $arr) {
                                            $xmlstore .= '  <dataset seriesname="' . $pro . '">';
                                            foreach ($columns_data as $id => $year) {
                                                $vv = 0;
                                                if (!empty($display_data[$pro][$year]))
                                                    $vv = $display_data[$pro][$year];
                                                $xmlstore .= '  <set value="' . $vv . '" />';
                                            }
                                            $xmlstore .= '  </dataset>';
                                        }


                                        $xmlstore .= ' </chart>';

                                        FC_SetRenderer('javascript');
                                        echo renderChart(PUBLIC_URL . "FusionCharts/Charts/MSLine.swf", "", $xmlstore, '1', '100%', 300, false, false);
                                        ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
                <?php
//Including footer file
                include PUBLIC_PATH . "/html/footer.php";
                ?>
            </div>
            </body>
