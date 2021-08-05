<?php
include("../includes/classes/Configuration.inc.php");
//Including db file
//Login();
//include(APP_PATH . "includes/classes/db.php");
//Including header file
//include(PUBLIC_PATH . "html/header.php");

$district = isset($_REQUEST['district']) ? $_REQUEST['district'] : '';
$stk = isset($_REQUEST['stakeholder']) ? $_REQUEST['stakeholder'] : '';
$hf = isset($_REQUEST['hf']) ? $_REQUEST['hf'] : '';
?>
<style>
    .widget-head ul{padding-left:0px !important;}
    #map{width:100%;height:390px;position: relative}
    #loader{display:none;width: 70px;height: 70px;position:absolute;left:45%;top:40%;z-index: 2000}
    #inputForm{width:50%;height:25px;position: absolute;top:4px;left:10%;z-index: 2000}
    #mapTitle{position:absolute;top:24%;left:2%;width:150px;height:15px;text-align:center;}
    #legendDiv{display:none;position:absolute;padding:2px;border-radius:6px;font-size:8px;background-color:none;border:1px solid black;width:auto;height:auto;top:57%;left:70%;z-index: 3000;}
    .pageLoader {
        position: fixed;
        left: 0px;
        top: 0px;
        width: 100%;
        height: 100%;
        z-index: 9999;
        background: url('../../public/images/ajax-loader.gif') 50% 50% no-repeat rgb(249,249,249);
    }
    /*.col-md-6{min-height:450px !important;}*/
    #loadingmessage{height:450px !important;}
    #loadingmessage img{margin-top:150px !important;}
    select.input-sm{padding:0px !important;}
    .my_dash_cols{
        padding-left: 1px;
        padding-right: 0px;
        padding-top: 1px;
        padding-bottom: 0px;
    }
    .my_dashlets{
        padding-left: 1px;
        padding-right: 0px;
        padding-top: 1px;
        padding-bottom: 0px;
    }

    span.multiselect-native-select {
        position: relative;

    }
    span.multiselect-native-select select {
        border: 0!important;
        clip: rect(0 0 0 0)!important;
        height: 1px!important;
        margin: -1px -1px -1px -3px!important;
        overflow: hidden!important;
        padding: 0!important;
        position: absolute!important;
        width: 1px!important;
        left: 50%;
        top: 30px
    }
    .multiselect-container {
        position: absolute;
        list-style-type: none;
        margin: 0;
        padding: 0
    }
    .multiselect-container .input-group {
        margin: 5px
    }
    .multiselect-container>li {
        padding: 0
    }
    .multiselect-container>li>a.multiselect-all label {
        font-weight: 700
    }
    .multiselect-container>li.multiselect-group label {
        margin: 0;
        padding: 3px 20px 3px 20px;
        height: 100%;
        font-weight: 700
    }
    .multiselect-container>li.multiselect-group-clickable label {
        cursor: pointer
    }
    .multiselect-container>li>a {
        padding: 0
    }
    .multiselect-container>li>a>label {
        margin: 0;
        height: 100%;
        cursor: pointer;
        font-weight: 400;
        padding: 3px 0 3px 30px
    }
    .multiselect-container>li>a>label.radio, .multiselect-container>li>a>label.checkbox {
        margin: 0
    }
    .multiselect-container>li>a>label>input[type=checkbox] {
        margin-bottom: 5px
    }


    .panel-actions a {
        color:#333;
    }
    .panel-fullscreen {
        display: block;
        z-index: 9999;
        position: fixed;
        width: 100%;
        height: 100%;
        top: 0;
        right: 0;
        left: 0;
        bottom: 0;
        overflow: auto;
    }

</style>
</head>
<?php
if (isset($_POST['province']) && !isset($_POST['all'])) {
    $province = $_REQUEST['province'];
    if ($province == 3) {
        $qry = "SELECT
tbl_locations.LocName,
tbl_locations.PkLocID
FROM tbl_locations
where tbl_locations.PkLocID IN(14,77,149,93)";
    } else {
        $qry = "SELECT
tbl_locations.LocName,
tbl_locations.PkLocID
FROM
project_locations
INNER JOIN tbl_locations ON project_locations.location_id = tbl_locations.PkLocID
WHERE
project_locations.project = 'unicef' AND
tbl_locations.ParentID = $province ORDER BY tbl_locations.LocName ASC";
    }
    ?>
    <select required  name="district[]" id="district" class="multiselect-ui form-control input-sm" multiple>
    <?php
    $dist_res = mysqli_query($connc, $qry);
//        print_r($dist_res);exit;
    ?>
        <!--<option value="">Select</option>-->
        <?php
        while ($row = $dist_res->fetch_assoc()) {
            if ($district == $row['PkLocID']) {
                $sel = "selected='selected'";
            } else {
                $sel = "";
            }
            ?>

            <option value="<?php echo $row['PkLocID']; ?>" <?php echo $sel; ?>><?php echo $row['LocName']; ?></option>
            <?php
        }
        ?>
    </select>
            <label class="control-label">
                                                                        <span class="">
                                                                            <label class="text-danger">By press CTRL key you can select multiple values</label>
                                                                            </span>
                                                                        </label>
        <script src="<?= PUBLIC_URL ?>js/bootstrap-multiselect.js"></script>
        <script>
            $(function () {
                $('.multiselect-ui').multiselect({
                    includeSelectAllOption: false,
                    onChange: function (option, checked) {
                        var selectedOptions = $('.multiselect-ui option:selected');

                        if (selectedOptions.length >= 2) {
                            // Disable all other checkboxes.
                            var nonSelectedOptions = $('.multiselect-ui option').filter(function () {
                                return !$(this).is(':selected');
    //                                                    alert('Only 2 districts can be selected for comparison');
                            });

                            nonSelectedOptions.each(function () {
                                var input = $('input[value="' + $(this).val() + '"]');
                                input.prop('disabled', true);
                                input.parent('li').addClass('disabled');
                            });
                        } else {
                            // Enable all checkboxes.
                            $('.multiselect-ui option').each(function () {
                                var input = $('input[value="' + $(this).val() + '"]');
                                input.prop('disabled', false);
                                input.parent('li').addClass('disabled');
                            });
                        }
                    }

                });
            });

        </script>
<?php
} else {
    $province = $_REQUEST['province'];

    if ($province == 3) {
        $qry = "SELECT
tbl_locations.LocName,
tbl_locations.PkLocID
FROM tbl_locations
where tbl_locations.PkLocID IN(14,77,149,93)";
    } else {
        $qry = "SELECT
tbl_locations.LocName,
tbl_locations.PkLocID
FROM
project_locations
INNER JOIN tbl_locations ON project_locations.location_id = tbl_locations.PkLocID
WHERE
project_locations.project = 'unicef' AND
tbl_locations.ParentID = $province ORDER BY tbl_locations.LocName ASC";
    }
    ?>
        <select required  name="district[]" id="district" class="multiselect-ui form-control input-sm" multiple>
        <?php
        $dist_res = mysqli_query($connc, $qry);
//        print_r($dist_res);exit;
        ?>
            <!--<option value="">Select</option>-->
            <?php
            while ($row = $dist_res->fetch_assoc()) {
                if ($district == $row['PkLocID']) {
                    $sel = "selected='selected'";
                } else {
                    $sel = "";
                }
                ?>

                <option value="<?php echo $row['PkLocID']; ?>" <?php echo $sel; ?>><?php echo $row['LocName']; ?></option>
                <?php
            }
            ?>
                    </select>
            <label class="control-label">
                                                                        <span class="">
                                                                            <label class="text-danger">By press CTRL key you can select multiple values</label>
                                                                            </span>
                                                                        </label>
                
            <script src="<?= PUBLIC_URL ?>js/bootstrap-multiselect.js"></script>
            <script>
            $(function () {
                $('.multiselect-ui').multiselect({
                    includeSelectAllOption: true,
                    onChange: function (option, checked) {
                        var selectedOptions = $('.multiselect-ui option:selected');

                        if (selectedOptions.length >= 2) {
                            // Disable all other checkboxes.
                            var nonSelectedOptions = $('.multiselect-ui option').filter(function () {
                                return !$(this).is(':selected');
                                //                                                    alert('Only 2 districts can be selected for comparison');
                            });

                            nonSelectedOptions.each(function () {
                                var input = $('input[value="' + $(this).val() + '"]');
                                input.prop('disabled', true);
                                input.parent('li').addClass('disabled');
                            });
                        } else {
                            // Enable all checkboxes.
                            $('.multiselect-ui option').each(function () {
                                var input = $('input[value="' + $(this).val() + '"]');
                                input.prop('disabled', false);
                                input.parent('li').addClass('disabled');
                            });
                        }
                    }

                });
            });

            </script>
    <?php
}
?>