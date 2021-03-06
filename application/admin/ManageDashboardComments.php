<?php
/**
 * Manage Dashboard Comments
 * @package Admin
 *
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 *
 * @version    2.2
 *
 */

//including files

include("../includes/classes/AllClasses.php");
include(PUBLIC_PATH . "html/header.php");
//initializing variables

//act

$act = 2;

//srtDo

$strDo = "Add";

//nstkId

$nId = 0;

//staname
$stkname = "";

//stkgroupid
$stkgroupid = 0;

//stkNewGroupName
$strNewGroupName = "";

//stktype
$stktype = 0;

//stkorder
$stkorder = 0;

//newRank
$newRank = 0;

//lvl_id
$lvl_id = 0;



//register globals

if (!ini_get('register_globals')) {

    $superglobals = array($_GET, $_POST, $_COOKIE, $_SERVER);

    if (isset($_SESSION)) {

        array_unshift($superglobals, $_SESSION);

    }

    foreach ($superglobals as $superglobal) {

        extract($superglobal, EXTR_SKIP);

    }

    ini_set('register_globals', true);

}





if (isset($_REQUEST['Do']) && !empty($_REQUEST['Do'])) {

    //getting Do

    $strDo = $_REQUEST['Do'];

}



if (isset($_REQUEST['Id']) && !empty($_REQUEST['Id'])) {

    //getting Id

    $nId = $_REQUEST['Id'];

}



/**

 * 

 * Delete Manage Stakeholders

 * 

 */

if ($strDo == "Delete") {

    //Check if Sub-offices exists

    $objcomments->pk_id = $nId;

        //Delete Stakeholder

        $rsEditCat = $objcomments->delete();

        $_SESSION['err']['text'] = 'Data has been successfully deleted.';

        $_SESSION['err']['type'] = 'success';

    //redirecting to ManageStakeholders

    redirect("ManageDashboardComments.php");

    exit;

}

//unset pk_id

if (isset($_SESSION['pk_id'])) {

    unset($_SESSION['pk_id']);

}



/**

 * 

 * Edit

 * 

 */

if ($strDo == "Edit") {

    $objcomments->pk_id = $nId;
    //Get Stakeholders By Id

    $rsEditstk = $objcomments->GetCommentsById();

    //getting results

    if ($rsEditstk != FALSE) {
        $RowEditStk = mysql_fetch_object($rsEditstk);
        //pk_id
        $_SESSION['pk_id'] = $nId;
        //lvl_id
        $dashboardid = $RowEditStk->dashboard_id;
        $dashletid = $RowEditStk->dashlet_id;
        $stakeholderid = $RowEditStk->stakeholder_id;
        $locationid = $RowEditStk->location_id;
        $month_year = $RowEditStk->month_year;
        $breaks = array("<br />","<br>","<br/>");
        $comments = str_ireplace($breaks, "\n", $RowEditStk->comments);
    }
}

//Get All Stakeholders
//$rsStakeholders = $objstk->GetAllStakeholders();
//GetAllstk types
//$rsstktype = $objstkType->GetAllstk_types();
//Get Ranks
//$rsranks = $objstk->GetRanks();
//Get All levels
//$rslvl = $objlvl->GetAlllevels();
//include file

include("xml_comments.php");
?>

</head>

<!-- BEGIN BODY -->

<body class="page-header-fixed page-quick-sidebar-over-content" onLoad="doInitGrid()">

    <!-- BEGIN HEADER -->
    <div class="page-container">

        <?php include $_SESSION['menu']; ?>

        <?php include PUBLIC_PATH . "html/top_im.php"; ?>

        <div class="page-content-wrapper">

            <div class="page-content">

                <div class="row">

                    <div class="col-md-12">

                        <h3 class="page-title row-br-b-wp">Manage Dashboard Comments</h3>

                        <div class="widget" data-toggle="collapse-widget">

                            <div class="widget-head">

                                <h3 class="heading"><?php echo $strDo; ?> Comments</h3>

                            </div>

                            <div class="widget-body">

                                <form name="addnew" id="addnew" action="add_comments_action.php" method="POST">

                                    <div class="row">

                                            <div class="col-md-3">

                                                <label class="control-label">Dashboard<span class="red">*</span></label>

                                                <div class="controls">

                                                    <select name="dashboard_id" tabindex="6" id="dashboard_id" class="form-control input-medium">

                                                        <option value="">Select</option>

                                                        <?php

                                                        //Query for items

                                                        $strSQL = "SELECT
	resources.pk_id,
	resources.page_title
FROM
	resources
WHERE
	resources.resource_type_id = 1
ORDER BY
resources.page_title ASC";


                                                        $rsTemp1 = mysql_query($strSQL) or die(mysql_error());

                                                        //Populate itm_id combo

                                                        while ($rsRow1 = mysql_fetch_array($rsTemp1)) {

                                                            ?>

                                                            <option value=<?php echo $rsRow1['pk_id']; ?> <?php if ( !empty($dashboardid) && $rsRow1['pk_id'] == $dashboardid) {

                                                                echo 'selected="selected"';

                                                            } ?> ><?php echo $rsRow1['page_title']; ?></option>

                                                            <?php

                                                        }

                                                        mysql_free_result($rsTemp1);

                                                        ?>

                                                    </select>

                                                </div>

                                            </div>

                                            <div class="col-md-3">

                                                <label class="control-label">Dashlet<span class="red">*</span></label>

                                                <div class="controls">

                                                    <select name="dashlet_id" tabindex="6" id="dashlet_id" class="form-control input-medium">

                                                        <option value="">Select</option>

                                                        <?php

                                                        //Query for items
if(!empty($dashboardid)) {

    $strSQL = "SELECT
	resources.pk_id,
	resources.page_title
FROM
	resources
WHERE
	resources.resource_type_id = 3
	AND resources.parent_id = $dashboardid
ORDER BY
resources.page_title ASC";

    $rsTemp1 = mysql_query($strSQL) or die(mysql_error());

    //Populate itm_id combo

    while ($rsRow1 = mysql_fetch_array($rsTemp1)) {

        ?>

        <option value=<?php echo $rsRow1['pk_id']; ?> <?php if ($rsRow1['pk_id'] == $dashletid) {

            echo 'selected="selected"';

        } ?>><?php echo $rsRow1['page_title']; ?></option>

        <?php

    }

    mysql_free_result($rsTemp1);
}
                                                        ?>

                                                    </select>

                                                </div>

                                            </div>

                                            <div class="col-md-3">

                                                <label class="control-label">Stakeholder<span class="red">*</span></label>

                                                <div class="controls">

                                                    <select name="stakeholder_id" tabindex="6" id="stakeholder_id" class="form-control input-medium">

                                                        <option value="">Select</option>

                                                        <?php

                                                        //Query for items

                                                        $strSQL = "SELECT
	stakeholder.stkname,
	stakeholder.stkid
FROM
	stakeholder
WHERE
	stakeholder.ParentID IS NULL
        AND stakeholder.stk_type_id IN (0, 1)
ORDER BY
stakeholder.stkorder ASC";

                                                        $rsTemp1 = mysql_query($strSQL) or die(mysql_error());

                                                        //Populate itm_id combo

                                                        while ($rsRow1 = mysql_fetch_array($rsTemp1)) {

                                                            ?>

                                                            <option value=<?php echo $rsRow1['stkid']; ?> <?php if (!empty($stakeholderid) && $rsRow1['stkid'] == $stakeholderid) {

                                                                echo 'selected="selected"';

                                                            } ?> ><?php echo $rsRow1['stkname']; ?></option>

                                                            <?php

                                                        }

                                                        mysql_free_result($rsTemp1);

                                                        ?>

                                                    </select>

                                                </div>

                                            </div>


                                            <div class="col-md-3">

                                                <label class="control-label">Location<span class="red">*</span></label>

                                                <div class="controls">

                                                    <select name="location_id" tabindex="6" id="location_id" class="form-control input-medium">

                                                        <option value="">Select</option>

                                                        <?php

                                                        //Query for items

                                                        $strSQL = "SELECT
	tbl_locations.PkLocID,
	tbl_locations.LocName
FROM
	tbl_locations
WHERE
	tbl_locations.LocLvl = 2";

                                                        $rsTemp1 = mysql_query($strSQL) or die(mysql_error());

                                                        //Populate itm_id combo

                                                        while ($rsRow1 = mysql_fetch_array($rsTemp1)) {

                                                            ?>

                                                            <option value=<?php echo $rsRow1['PkLocID']; ?> <?php if (!empty($locationid) && $rsRow1['PkLocID'] == $locationid) {

                                                                echo 'selected="selected"';

                                                            } ?> ><?php echo $rsRow1['LocName']; ?></option>

                                                            <?php

                                                        }

                                                        mysql_free_result($rsTemp1);

                                                        ?>

                                                    </select>

                                                </div>

                                            </div>



                                    </div>

                                    <div class="row">

                                        <div class="col-md-12">

                                                <label class="control-label">Comments</label>

                                                <div class="controls">

                                                    <textarea class="form-control" id="comments" name="comments"><?php echo (!empty($comments)?$comments:''); ?></textarea>

                                                </div>

                                        </div>

                                    </div>



                                    <div class="row">
                                        <div class="col-md-9">
                                            <div class="control-group">
                                                <label>Select Month<font color="#FF0000">*</font></label>
                                                <div class="controls">
                                                    <input type="text" autocomplete="off" name="month_year" id="month_year" value="<?php echo $month_year; ?>" class="form-control input-medium" />
                                                </div>
                                            </div>
                                        </div>

                                            <div class="col-md-3 right">

                                                <div class="control-group">

                                                    <label>&nbsp;</label>

                                                    <div class="controls">

                                                        <input type="hidden" name="hdncommentId" value="<?php echo $nId ?>" />

                                                        <input type="hidden" name="hdnToDo" value="<?= $strDo ?>" />

                                                        <input type="hidden" id="add_comments" name="add_comments" value="1">

                                                        <input type="submit" value="<?= $strDo ?>" class="btn btn-primary" />

                                                        <input name="btnAdd" type="button" id="btnCancel" class="btn btn-info" value="Cancel" OnClick="window.location = '<?= $_SERVER["PHP_SELF"]; ?>';">

                                                    </div>

                                                </div>

                                            </div>

                                    </div>

                                </form>



                            </div>

                        </div>

                    </div>

                </div>

                <div class="row">

                    <div class="col-md-12">

                        <div class="widget">

                            <div class="widget-head">

                                <?php

                                //display All Stakeholders

                                ?>

                                <h3 class="heading">Comment List</h3>

                            </div>

                            <div class="widget-body">

                                <table width="100%" cellpadding="0" cellspacing="0" align="center">

                                    <tr>

                                        <td style="text-align:right;">

                                            <img style="cursor:pointer;" src="<?php echo PUBLIC_URL; ?>images/pdf-32.png" onClick="mygrid.setColumnHidden(4, true);

                                                    mygrid.setColumnHidden(5, true);

                                                    mygrid.toPDF('<?php echo PUBLIC_URL; ?>dhtmlxGrid/dhtmlxGrid/grid2pdf/server/generate.php');

                                                    mygrid.setColumnHidden(4, false);

                                                    mygrid.setColumnHidden(5, false);" />

                                            <img style="cursor:pointer;" src="<?php echo PUBLIC_URL; ?>images/excel-32.png" onClick="mygrid.setColumnHidden(4, true);

                                                    mygrid.setColumnHidden(5, true);

                                                    mygrid.toExcel('<?php echo PUBLIC_URL; ?>dhtmlxGrid/dhtmlxGrid/grid2excel/server/generate.php');

                                                    mygrid.setColumnHidden(4, false);

                                                    mygrid.setColumnHidden(5, false);" />

                                        </td>

                                    </tr>

                                    <tr>

                                        <td><div id="mygrid_container" style="width:100%; height:350px; background-color:white;overflow:hidden"></div></td>

                                    </tr>

                                </table>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

    <?php include PUBLIC_PATH . "/html/footer.php"; ?>

    <?php include PUBLIC_PATH . "/html/reports_includes.php"; ?>

    <script type="text/javascript">

        function editFunction(val) {

            window.location = "ManageDashboardComments.php?Do=Edit&Id=" + val;

        }

        function delFunction(val) {

            if (confirm("Are you sure you want to delete the record?")) {

                window.location = "ManageDashboardComments.php?Do=Delete&Id=" + val;

            }

        }

        $('#dashboard_id').change(function(e) {
            if ($(this).val() != '') {
                $.ajax({
                    url: 'getfromajax.php',
                    type: 'POST',
                    data: {ctype: 13, dashid: $(this).val()},
                    success: function(data) {
                        $('#dashlet_id').html(data);
                    }
                })
            }
        });

        $('.dimensions').focusout(function() {
            var pack_length = $('#pack_length').val();
            var pack_width 	= $('#pack_width').val();
            var pack_height = $('#pack_height').val();
            var gross = 0 ;

            if ( typeof pack_length== 'undefined') 	pack_length=0;
            if ( typeof pack_width== 'undefined') 	pack_width=0;
            if ( typeof pack_height== 'undefined') 	pack_height=0;

            gross = pack_length * pack_width * pack_height;

            $('#gross_capacity').val(gross);

        });

        $(document).ready(function() {
            $('#month_year').datepicker({
                dateFormat: "yy-mm"
            });
        });

        var mygrid;

        //Initializing grid

        function doInitGrid() {

            mygrid = new dhtmlXGridObject('mygrid_container');

            mygrid.selMultiRows = true;

            mygrid.setImagePath("<?php echo PUBLIC_URL; ?>dhtmlxGrid/dhtmlxGrid/codebase/imgs/");

            mygrid.setHeader("<span title='Serial Number'>Sr. No.</span>,<span title='Dashboard'>Dashboard Name</span>,<span title='Dashlet'>Dashlet Name</span>,<span title='Stakeholder'>Stakeholder Name</span>,<span title='Location'>Location</span>,<span title='Location'>Month</span>,<span title='Comments'>Comments</span>,<span title='Use this column to perform the desired operation'>Actions</span>,#cspan");

            mygrid.attachHeader(",#select_filter,#text_filter,#select_filter,,,,,");

            mygrid.setInitWidths("60,150,150,150,150,100,*,30,30");

            mygrid.setColAlign("center,left,left,left,left,left,left,center,center")

            mygrid.setColSorting(",,str,,,,,,");

            mygrid.setColTypes("ro,ro,ro,ro,ro,ro,ro,img,img");

            //mygrid.enableLightMouseNavigation(true);

            mygrid.enableRowsHover(true, 'onMouseOver');

            mygrid.setSkin("light");

            mygrid.init();

            mygrid.clearAll();

            mygrid.loadXMLString('<?php echo $xmlstore; ?>');

        }



    </script>

    <?php

    if (isset($_SESSION['err'])) {

        ?>

        <script>

            var self = $('[data-toggle="notyfy"]');

            notyfy({

                force: true,

                text: '<?php echo $_SESSION['err']['text']; ?>',

                type: '<?php echo $_SESSION['err']['type']; ?>',

                layout: self.data('layout')

            });


        </script>

        <?php

        //Unsetting session

        unset($_SESSION['err']);

    }

    ?>

</body>

</html>