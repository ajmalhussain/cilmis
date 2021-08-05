<!DOCTYPE html>

<?php
error_reporting(0);
include("db.php");
session_start();
//echo '<pre>';print_r($_POST);
?>
<html>
    <head>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
        <link rel="stylesheet" href="select2.css">
        <link rel="stylesheet" href="select2-metronic.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
        <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.6.4/jquery.min.js"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/css/bootstrap-datetimepicker.min.css" />
        <script src="http://cdnjs.cloudflare.com/ajax/libs/moment.js/2.5.1/moment.min.js"></script>            
        <script src="http://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/3.0.0/js/bootstrap-datetimepicker.min.js"></script>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
        <link rel="stylesheet" href="/resources/demos/style.css">
    <img src="../../../Users/HP22/Downloads/Screenshot-2018-1-26 http localhost.png" alt=""/>
        <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
        <script src="select2.min.js"></script>
        <script type="text/javascript">

            function f_type_change() {
                show_hide_func();

                if ($('#facility_level').val() == 1)
                {
                    loadfacility(1);
                }

            }
            function show_hide_func() {
                if ($('#facility_level').val() == 1)
                {

                    $('#province').hide();
                    $("#province").attr('required', false);
                    $('#facility').hide();
                    $("#facility").attr('required', false);
                    $('#district').hide();
                    $("#district").attr('required', false);
                }
                if ($('#facility_level').val() == 3)
                {

                    $('#province').show();
                    $("#province").attr('required', true);
                    $('#facility').hide();
                    $("#facility").attr('required', false);
                    $('#district').hide();
                    $("#district").attr('required', false);
                }
                if ($('#facility_level').val() == 7)
                {

                    $('#province').show();
                    $("#province").attr('required', true);
                    $('#facility').hide();
                    $("#facility").attr('required', false);

                }
            }
            function loaddistrict()
            {
                var province = $('#province').val();

                // $('#display_district').html('');
                // $('#display_facility').html('');
                $.ajax({
                    type: 'post',
                    url: 'load_district.php',
                    data: {

                        province: province

                    },
                    success: function (response) {
                        $('#display_district').html(response);
                    },
                    beforeSend: function () {
                        $('#loader').show();
                    },
                    complete: function () {
                        $('#loader').hide();
                    }
                });

            }
            function on_change_district()
            {
                var lvl = $('#facility_level').val();
                loadfacility(lvl);

                // console.log(lvl);
            }
            function loadfacility(lvl) {
                var district = $('#district').val();

                //   $('#display_facility').html('');
                $.ajax({
                    type: 'post',
                    url: 'load_facility.php',
                    data: {

                        district: district,
                        lvl: lvl

                    },
                    success: function (response) {
                        $('#display_facility').html(response);
                    },
                    beforeSend: function () {
                        $('#loader').show();
                    },
                    complete: function () {
                        $('#loader').hide();
                    }
                });

            }
            function loadfacility2() {
                var district = $('#district').val();

                //   $('#display_facility').html('');
                $.ajax({
                    type: 'post',
                    url: 'load_facility2.php',
                    data: {

                        district: district

                    },
                    success: function (response) {
                        $('#display_facility').html(response);
                    },
                    beforeSend: function () {
                        $('#loader').show();
                    },
                    complete: function () {
                        $('#loader').hide();
                    }
                });

            }

            function form1exe() {
                //  alert("function called");
                var form1_to3_D = $('#form1_to3_D').val();
                var form1_to3_I = $('#form1_to3_I').val();
                var form1_to3_J = $('#form1_to3_J').val();
                var form1_to3_K = $('#form1_to3_K').val();
                var form1_to3_L = $('#form1_to3_L').val();
                var form1_to4_D = $('#form1_to4_D').val();
                var form1_to4_I = $('#form1_to4_I').val();
                var form1_to4_J = $('#form1_to4_J').val();
                var form1_to4_K = $('#form1_to4_K').val();
                var form1_to4_L = $('#form1_to4_L').val();
                var form1_all_D = $('#form1_all_D').val();
                var form1_all_I = $('#form1_all_I').val();
                var form1_all_J = $('#form1_all_J').val();
                var form1_all_K = $('#form1_all_K').val();
                var form1_all_L = $('#form1_all_L').val();
                var parent_id = $('#parent_id').val();

                if (form1_all_L || form1_to3_I)
                {
                    $('#form1exeresp').html('');
                    $.ajax({
                        type: 'post',
                        url: 'form1_execution.php',
                        data: {

                            form1_to3_D: form1_to3_D,
                            form1_to3_I: form1_to3_I,
                            form1_to3_J: form1_to3_J,
                            form1_to3_K: form1_to3_K,
                            form1_to3_L: form1_to3_L,
                            form1_to4_D: form1_to4_D,
                            form1_to4_I: form1_to4_I,
                            form1_to4_J: form1_to4_J,
                            form1_to4_K: form1_to4_K,
                            form1_to4_L: form1_to4_L,
                            form1_all_D: form1_all_D,
                            form1_all_I: form1_all_I,
                            form1_all_J: form1_all_J,
                            form1_all_K: form1_all_K,
                            form1_all_L: form1_all_L,
                            parent_id: parent_id
                        },
                        success: function (response) {
                            $('#form1exeresp').html(response);
                            alert("Succeccfully Inserted!");
                        },
                        beforeSend: function () {
                            $('#loader').show();

                        },
                        complete: function () {
                            $('#loader').hide();

                        }
                    });

                } else
                {
                    $('#form1exeresp').html("No Data Exist");


                }

            }

            function form2_to3_exe() {

                $('#form2_to3_exeresp').html('');
                $.ajax({
                    type: 'post',
                    url: 'form2_to3_execution.php',
                    data: $("#form2_to3_execution").serialize(),
                    success: function (response) {
                        $('#form2_to3_exeresp').html(response);
                        alert("Succeccfully Inserted!");
                    },
                    beforeSend: function () {
                        $('#loader').show();
                    },
                    complete: function () {
                        $('#loader').hide();
                    }
                });
            }
            function form2_to4_exe() {

                $('#form2_to4_exeresp').html('');
                $.ajax({
                    type: 'post',
                    url: 'form2_to4_execution.php',
                    data: $("#form2_to4_execution").serialize(),
                    success: function (response) {
                        $('#form2_to4_exeresp').html(response);
                        alert("Succeccfully Inserted!");
                    },
                    beforeSend: function () {
                        $('#loader').show();
                    },
                    complete: function () {
                        $('#loader').hide();
                    }
                });
            }
            function form3_exe()
            {
                // alert("form 3 execution");
                $('#form3_exeresp').html('');
                $.ajax({
                    type: 'post',
                    url: 'form3_execution.php',
                    data: $("#form_3_exe").serialize(),
                    success: function (response) {
                        $('#form3_exeresp').html(response);
                        alert("Succeccfully Inserted!");
                    },
                    beforeSend: function () {
                        $('#loader').show();
                    },
                    complete: function () {
                        $('#loader').hide();
                    }
                });
            }
            function loadform() {
                $("#f").addClass("active");
                 $("#fp").addClass("active");
                 $("#fp").trigger("click");
                 $('#st1').addClass("active");
                loadform2to3();
            }
            function loadform2to3()
            {
                var parent_id = $('#parent_id').val();
                $.ajax({
                    type: 'post',
                    url: 'load_form2.php',
                    data: {parent_id: parent_id},
                    success: function (response) {
                        $('#form2loadto3').html(response);
                    },
                    beforeSend: function () {
                        $('#loader').show();
                    },
                    complete: function () {
                        $('#loader').hide();
                    }
                });
            }
            function loadform2to4()
            {
                var parent_id = $('#parent_id').val();
                $.ajax({
                    type: 'post',
                    url: 'load_form2_to4.php',
                    data: {parent_id: parent_id},
                    success: function (response) {
                        $('#form2loadto4').html(response);
                    },
                    beforeSend: function () {
                        $('#loader').show();
                    },
                    complete: function () {
                        $('#loader').hide();
                    }
                });
            }
            function form3_1()
            {
                var parent_id = $('#parent_id').val();
                $.ajax({
                    type: 'post',
                    url: 'load_form3_1.php',
                    data: $("#form_3_1_exe").serialize(),
                    success: function (response) {
                        $('#load').html(response);
                    },
                    beforeSend: function () {
                        $('#loader').show();
                    },
                    complete: function () {
                        $('#loader').hide();
                    }
                });
            }
            function form5_1()
            {
                var parent_id = $('#parent_id').val();
                $.ajax({
                    type: 'post',
                    url: 'load_form5_1.php',
                    data: $("#form_5_1_exe").serialize(),
                    success: function (response) {
                        $('#load1').html(response);
                    },
                    beforeSend: function () {
                        $('#loader').show();
                    },
                    complete: function () {
                        $('#loader').hide();
                    }
                });
            }
             function loadform3()
            {
                var parent_id = $('#parent_id').val();
                $.ajax({
                    type: 'post',
                    url: 'load_form3.php',
                    data: {parent_id: parent_id},
                    success: function (response) {
                        $('#display_form3').html(response);
                    },
                    beforeSend: function () {
                        $('#loader').show();
                    },
                    complete: function () {
                        $('#loader').hide();
                    }
                });
            }
            function loadform5()
            {
                var parent_id = $('#parent_id').val();
                $.ajax({
                    type: 'post',
                    url: 'load_form5.php',
                    data: {parent_id: parent_id},
                    success: function (response) {
                        $('#display_form5').html(response);
                    },
                    beforeSend: function () {
                        $('#loader').show();
                    },
                    complete: function () {
                        $('#loader').hide();
                    }
                });
            }
            function loadform6()
            {
                var parent_id = $('#parent_id').val();
                $.ajax({
                    type: 'post',
                    url: 'load_form6.php',
                    data: {parent_id: parent_id},
                    success: function (response) {
                        $('#display_form6').html(response);
                    },
                    beforeSend: function () {
                        $('#loader').show();
                    },
                    complete: function () {
                        $('#loader').hide();
                    }
                });
            }
            function form5_exe()
            {
                //alert("form 5 execution");
                $('#form5_exeresp').html('');
                $.ajax({
                    type: 'post',
                    url: 'form5_execution.php',
                    data: $("#form_5_exe").serialize(),
                    success: function (response) {
                        $('#form5_exeresp').html(response);
                        alert("Succeccfully Inserted!");
                    },
                    beforeSend: function () {
                        $('#loader').show();
                    },
                    complete: function () {
                        $('#loader').hide();
                    }
                });
            }
            function loadform4()
            {
                var parent_id = $('#parent_id').val();
                $.ajax({
                    type: 'post',
                    url: 'load_form4.php',
                    data: {parent_id: parent_id},
                    success: function (response) {
                        $('#display_form4').html(response);
                    },
                    beforeSend: function () {
                        $('#loader').show();
                    },
                    complete: function () {
                        $('#loader').hide();
                    }
                });
                shForm4('mth1');
            }
            function form4_exe()
            {
                // alert("form 4 execution");
                $('#form4_exeresp').html('');
                $.ajax({
                    type: 'post',
                    url: 'form4_execution.php',
                    data: $("#form_4_exe").serialize(),
                    success: function (response) {
                        $('#form4_exeresp').html(response);
                        alert("Succeccfully Inserted!");
                    },
                    beforeSend: function () {
                        $('#loader').show();
                    },
                    complete: function () {
                        $('#loader').hide();
                    }
                });
            }
            function form6_exe()
            {
                // alert("form 6 execution");
                $('#form6_exeresp').html('');
                $.ajax({
                    type: 'post',
                    url: 'form6_execution.php',
                    data: $("#form_6_exe").serialize(),
                    success: function (response) {
                        $('#form6_exeresp').html(response);
                        alert("Succeccfully Inserted!");
                    },
                    beforeSend: function () {
                        $('#loader').show();
                    },
                    complete: function () {
                        $('#loader').hide();
                    }
                });
            }
            function form10_insertion()
            {
                $.ajax({
                    type: 'post',
                    url: 'form10_insertion.php',
                    data: $("#form_10_insert").serialize(),
                    success: function (response) {
                        $('#form_10_insert_resp').html(response);
                        alert("Succeccfully Inserted!");
                    },
                    beforeSend: function () {
                        $('#loader').show();
                    },
                    complete: function () {
                        $('#loader').hide();
                    }
                });
            }
            function form9_insertion()
            {
                $.ajax({
                    type: 'post',
                    url: 'form9_insertion.php',
                    data: $("#form_9_insert").serialize(),
                    success: function (response) {
                        $('#form_9_insert_resp').html(response);
                        alert("Succeccfully Inserted!");
                    },
                    beforeSend: function () {
                        $('#loader').show();
                    },
                    complete: function () {
                        $('#loader').hide();
                    }
                });
            }
            function form8_insertion()
            {
                $.ajax({
                    type: 'post',
                    url: 'form8_insertion.php',
                    data: $("#form_8_insert").serialize(),
                    success: function (response) {
                        $('#form_8_insert_resp').html(response);
                        alert("Succeccfully Inserted!");
                    },
                    beforeSend: function () {
                        $('#loader').show();
                    },
                    complete: function () {
                        $('#loader').hide();
                    }
                });
            }
            function form7_insertion()
            {
                $.ajax({
                    type: 'post',
                    url: 'form7_insertion.php',
                    data: $("#form_7_insert").serialize(),
                    success: function (response) {
                        $('#form_7_insert_resp').html(response);
                        alert("Succeccfully Inserted!");
                    },
                    beforeSend: function () {
                        $('#loader').show();
                    },
                    complete: function () {
                        $('#loader').hide();
                    }
                });
            }


        </script>   
        <style>
            .loader {
                border: 16px solid #f3f3f3;
                border-radius: 50%;
                border-top: 16px solid #3498db;
                width: 120px;
                height: 120px;
                -webkit-animation: spin 2s linear infinite; /* Safari */
                animation: spin 2s linear infinite;
            }

            /* Safari */
            @-webkit-keyframes spin {
                0% { -webkit-transform: rotate(0deg); }
                100% { -webkit-transform: rotate(360deg); }
            }

            @keyframes spin {
                0% { transform: rotate(0deg); }
                100% { transform: rotate(360deg); }
            }


        </style>

    </head>
    <body onload="$('loader').hide();" >
        <div class="container">
            <h3 style="color: red">USAID GLOBAL HEALTH SUPPLY CHAIN PROGRAM</h3>
            <h3 style="color: gray">PROCUREMENT AND SUPPLY MANAGEMENT</h3>
            <h3 style="color: red"><b>SUPPLY CHAIN DATA QUALITY ASSESSMENT TOOL</b></h3>
            <h4>FACILITY SUMMARY</h4>
            <div style="margin-top: 10px;">
                <form method="POST" id="form0">
                    <table border="2px" class="table table-bordered" >
                        <tr>
                            <th colspan="2">Complete items A-H at the beginning of the site visit</th>
                        </tr>
                        <tr class="form-group">
                            <td>
                                Facility Level and Type
                            </td>
                            <td>
                                <?php if (!isset($_POST['submit_parent'])) {
                                    ?>
                                    <input type="hidden" id="facility_level_h" name="facility_level_h" >
                                    <select class="form-control btn btn-default dropdown-toggle" required id="facility_level" name="facility_level" onchange=" f_type_change();
                                                javacript: var flvl = this.options[selectedIndex].text;
                                                document.getElementById('facility_level_h').value = flvl;">
                                        <option value=""> Select</option>
                                        <option value="1" > Central Storage Facility</option>
                                        <option value="3" > Subnational Storage Facility (District Stores)</option>
                                        <option value="7" > Service Delivery Point (SDP)</option>
                                    </select>
                                    <?php
                                } else {
                                    echo $_POST['facility_level_h'];
                                }
                                ?>
                            </td>
                        </tr>
                        <tr class="form-group">
                            <td>
                                Province/State/Region
                            </td>
                            <td>
                                <?php if (!isset($_POST['submit_parent'])) {
                                    ?>
                                    <input type="hidden" id="province_name" name="province_name" >
                                    <select required name="province"  class="form-control" id="province"  onchange="loaddistrict();
                                                javacript: var prov = this.options[selectedIndex].text;
                                                document.getElementById('province_name').value = prov;" >

                                        <option value=""> Select</option>
                                        <?php
                                        $query = $conn->query("SELECT
                                                    tbl_locations.PkLocID,
                                                    tbl_locations.LocName
                                                    FROM
                                                    tbl_locations
                                                    WHERE
                                                    tbl_locations.ParentID = 10 AND
                                                    tbl_locations.LocLvl = 2 AND
                                                    tbl_locations.LocType = 2");
                                        while ($row = $query->fetch_assoc()) {
                                            $pk_id = $row["PkLocID"];
                                            $province_name = $row["LocName"];
                                            ?>
                                            <option value="<?php echo $pk_id; ?>">   <?php echo $province_name; ?> </option>
                                        <?php }
                                        ?>
                                    </select>
                                    <?php
                                } else {
                                    echo $_POST['province_name'];
                                }
                                ?>
                            </td>
                        </tr>
                        <tr class="form-group">
                            <td>
                                District/Local Administrative Unit 
                            </td>
                            <td>
                                <?php if (!isset($_POST['submit_parent'])) {
                                    ?>
                                    <div id="display_district" onchange="//loadfacility();">

                                    </div>
                                    <?php
                                } else {
                                    if (isset($_POST['district_h']))
                                        echo $_POST['district_h'];
                                }
                                ?>
                            </td>
                        </tr>

                        <tr class="form-group">
                            <td>
                                Facility Name
                            </td>
                            <td>
                                <?php if (!isset($_POST['submit_parent'])) {
                                    ?>
                                    <div id="display_facility">
                                    </div>
                                    <?php
                                } else {
                                    echo $_POST['facility_h'];
                                }
                                ?>

                            </td>
                        </tr>

                        <tr class="form-group">
                            <td>
                                Date of Visit
                            </td>
                            <td>

                                <?php
                                if (isset($_POST['submit_parent'])) {
                                    $date_visit_h = $_POST['date_visit'];
                                }
                                if (!isset($_POST['submit_parent'])) {
                                    ?>

                                    <input type="date"  required class="form-control" id="date_visit" name="date_visit">
                                    <?php
                                } else {
                                    echo $date_visit_h;
                                }
                                ?>
                            </td>
                        </tr>
                        <tr class="form-group">
                            <td>
                                Data Quality Assessment conducted by
                            </td>
                            <td class="col-sm-8">
                                <?php
                                if (isset($_POST['submit_parent'])) {
                                    $name_h = $_POST['name'];
                                }
                                if (!isset($_POST['submit_parent'])) {
                                    ?>

                                    <label>Name</label>
                                    <input required type="text" required class="form-control input-sm" id="name"   name="name">
                                    <?php
                                } else {
                                    ?><b>Name: </b><?php
                                    echo $name_h;
                                }
                                ?>
                                <?php
                                if (isset($_POST['submit_parent'])) {
                                    $sig_h = $_POST['sig'];
                                }
                                if (!isset($_POST['submit_parent'])) {
                                    ?>


                                    <input type="hidden" class="form-control input-sm" id="sig"  name="sig">
                                    <?php
                                } else {
                                    ?><b>Signature: </b><?php
                                    $sig_h;
                                }
                                ?>

                            </td>
                        </tr>
                        <tr class="form-group">
                            <td>
                                Stock Data Manager Name(s)
                            </td>
                            <td >
                                <?php
                                if (isset($_POST['submit_parent'])) {
                                    $stk_mngr_name_h = $_POST['stk_mngr_name'];
                                }
                                if (!isset($_POST['submit_parent'])) {
                                    ?>
                                    <input type="text" class="form-control input-sm" required id="stk_mngr_name" name="stk_mngr_name" >
                                    <?php
                                } else {
                                    echo $stk_mngr_name_h;
                                }
                                ?>
                            </td>
                        </tr>
                        <tr class="form-group">
                            <td>
                                Which commodity type are stocked at this facility? 
                            </td>
                            <td>
                                <?php if (!isset($_POST['submit_parent'])) {
                                    ?>
                                    <input type="hidden" id="commodity_type_h" name="commodity_type_h" value="">
                                    <select class="form-control btn btn-default dropdown-toggle"  required id="commodity_type" name="commodity_type" onchange="javacript: var ctype = this.options[selectedIndex].text; document.getElementById('commodity_type_h').value = ctype;">
                                        <option value="" selected=""> Select</option>
                                        <option value="1">Family Planning and Reproductive Health</option>
                                        <option value="2">Material and Child Health</option>
                                    </select>
                                    <?php
                                } else {
                                    echo $_POST['commodity_type_h'];
                                }
                                ?>

                            </td>
                        </tr>
                    </table>
                    <?php if (!isset($_POST['submit_parent'])) {
                        ?>
                        <button type="submit" class="btn btn-default pull-right" name="submit_parent" >Submit</button> 
                        <?php
                    } else {
                        
                    }
                    ?>
                    <!--<button type="submit" class="btn btn-default pull-right" name="submit_parent" onclick="document.getElementById('district').style.display = 'block' ;document.getElementById('facility').style.display = 'block' ;">Submit</button>-->
                </form>    
            </div>
            <br>
            <br
            <?php
            // echo '<pre>';print_r($_REQUEST);exit;
            //  $_SESSION["parent_form"];
            $province = ((isset($_POST['district'])) ? $_POST['province'] : '');

            $district = ((isset($_POST['district'])) ? $_POST['district'] : '');

            $facility = ((isset($_POST['district'])) ? $_POST['facility'] : '');
            $facility_lvl_typ = ((isset($_POST['district'])) ? $_POST['district'] : '');
            $date_visit = ((isset($_POST['district'])) ? $_POST['date_visit'] : '');
            $d_q_a_name = ((isset($_POST['district'])) ? $_POST['name'] : '');
            $d_q_a_sig = ((isset($_POST['district'])) ? $_POST['sig'] : '');
            $s_d_m_name = ((isset($_POST['district'])) ? $_POST['stk_mngr_name'] : '');
            $comm_type = ((isset($_POST['district'])) ? $_POST['commodity_type'] : '');
            ?><br><?php
                if (isset($_REQUEST['submit_parent'])) {

                    $sqls = "SELECT *
                             FROM mne_basic_parent 
                             WHERE ( fac_id = '$facility' AND YEAR(date_visit)=YEAR('$date_visit')) ";

                    $query = $conn->query($sqls);

                    $row = $query->fetch_assoc();
                    $parent_id = $row["pk_id"];

                    $sql_update = "UPDATE mne_basic_parent SET name = '$d_q_a_name',sig='$d_q_a_sig',stock_mgr= '$s_d_m_name',item_group='$comm_type' where mne_basic_parent.pk_id = $parent_id";
                    $conn->query($sql_update);

                    $count = $query->num_rows;

                    if ($count > 0) {
                        echo "M&E Data for this facility / date already exists: " . $parent_id;
                        ?><input type="hidden" id="parent_id" name="parent_id" value="<?php echo $parent_id; ?>"><?php
                } else {
                    $sql = "INSERT INTO 
                                  mne_basic_parent (prov_id,dist_id,fac_id,fac_level,date_visit,name,sig,stock_mgr,item_group)
                                  VALUES ('$province','$district','$facility','$facility_lvl_typ','$date_visit','$d_q_a_name','$d_q_a_sig','$s_d_m_name','$comm_type') ";

                    $queryP = $conn->query($sql);
//                            echo '<pre>';
//                            print_r($_REQUEST);
//                            exit;
                    $parent_id = $conn->insert_id;
                    echo "New M&E Data for this facility / date: " . $parent_id;
                    ?><input type="hidden" id="parent_id" name="parent_id" value="<?php echo $parent_id; ?>"><?php
                }
            }
            if (!empty($parent_id)) {
                ?>
                <div id="allformstab">
                    <div id="main-tabs">
                        <ul class="nav nav-tabs">

                            <li class="active" >
                                <a  href="#t03" data-toggle="tab" onclick="show('t03')">FP</a>
                            </li>
                            <li><a href="#t04" data-toggle="tab" onclick="show('t04')" >MNCH</a>
                            </li>
                            <li><a href="#summary" data-toggle="tab" onclick="show('summ')" >Summary</a>
                            </li>

                        </ul>
                    </div>




                    <div class="tab-content ">
                        <div class="loader" id="loader" style="display:none;" ></div>
                        <div class="tab-pane active" id="t03" >
                            <ul class="nav nav-tabs">


                                <li class="active"><a href="#t3" data-toggle="tab" id="at3" onclick="loadform3();">Data Availability FP</a>
                                </li>
                                <li><a href="#t4" data-toggle="tab"  onclick="loadform4();">Data Accuracy FP</a>
                                </li>
                                <li><a href="#t9" data-toggle="tab">Data Timeliness FP</a>
                                </li>
                            </ul>

                        </div>
                    </div>


                    <div class="tab-content ">
                        <div class="loader" id="loader" style="display:none;" ></div>
                        <div class="tab-pane" id="t04" >
                            <ul class="nav nav-tabs">


                                <li class="active"><a href="#t5" data-toggle="tab" id="at4" onclick="loadform5();">Data Availability MNCH</a>
                                </li>
                                <li>
                                    <a  href="#t6" data-toggle="tab"  onclick="loadform6();">Data Accuracy MNCH</a>
                                </li>
                                <li><a href="#t9t4" data-toggle="tab">Data Timeliness MNCH</a>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="tab-content ">
                        <div class="loader" id="loader" style="display:none;" ></div>
                        <div class="tab-pane" id="summary" >
                            <ul class="nav nav-tabs">
                                <li  class="active">
                                    <a  href="#t1" id="t1_click" data-toggle="tab">Summary</a>
                                </li>
                                <li><a href="#t2" data-toggle="tab" onclick="loadform();">Follow Up</a>
                                </li>


                                <li><a href="#t7" id="t7_click" data-toggle="tab">Availability Rating Summary</a>
                                </li>
                                <li><a href="#t8" id="t8_click" data-toggle="tab">Accuracy Rating Summary</a>
                                </li>

                                <li><a href="#t10" id="t10_click" data-toggle="tab">Timeliness Rating Summary</a>


                            </ul>

                        </div>
                    </div>




                    <div class="tab-content ">

                        <div class="loader" id="loader" style="display:none;" ></div>
                        <div class="tab-pane " id="t1">
                            <div id="tab1_data">
                            </div> 

                        </div>
                        <div class="tab-pane" id="t2"   >

                            <div id="allformstab">
                                <br>
                                <ul class="nav nav-tabs">

                                    <li id="f" >
                                        <a  href="#st1" id="fp" data-toggle="tab"  onclick="loadform2to3();">Family Planning and Resproductive Health</a>
                                    </li>
                                    <li><a href="#st2" data-toggle="tab" onclick="loadform2to4();">Maternal, Newborn and Child Health</a>
                                    </li>

                                </ul>
                                <div class="tab-content ">
                                    <div class="tab-pane active" id="st1">
                                        <br>
                                        <div id="form2loadto3"></div>
                                        <div id="form2_to3_exeresp"></div>

                                    </div>
                                    <div class="tab-pane" id="st2">
                                        <div id="form2loadto4"></div>
                                        <div id="form2_to4_exeresp"></div>
                                    </div>
                                </div>
                            </div>







                        </div>
                        <div class="tab-pane active" id="t3">
                            <br>
                            <div id="display_form3">

                            </div>
                            <div id="form3_exeresp">
                            </div>


                        </div>
                        <div class="tab-pane" id="t4">
                            <div id="display_form4">

                            </div>
                            <div id="form4_exeresp">
                            </div>

                        </div>
                        <div class="tab-pane" id="t5" >
                            <br>
                            <div id="display_form5">

                            </div>
                            <div id="form5_exeresp">
                            </div>



                        </div>
                        <div class="tab-pane" id="t6" >
                            <br>
                            <div id="display_form6">

                            </div>
                            <div id="form6_exeresp">
                            </div>

                        </div>



                        <div class="tab-pane" id="t7">
                            <div id="tab7_data">
                            </div>
                        </div>
                        <div class="tab-pane" id="t8">
                            <div id="tab8_data">
                            </div>
                        </div>
                        <div class="tab-pane" id="t9">
                            <div><br>
                                <h4>DATA TIMELINESS</h4><br>
                                <form method="POST" id="form_9_insert">
                                    <table border="1" width="100%" class="table table-bordered">
                                        <tr>

                                            <th colspan="2" rowspan="2"></th>
                                            <th rowspan="2">A. Date Due</th>
                                            <th rowspan="2">B. Submitted</th>
                                            <th colspan="6">Enter a 1 under the timeframe of reporting that best describes when each report was submitted. Only make one entry per report. Leave all other cells blank.</th>

                                        </tr>
                                        <tr>

                                            <th>C. By due date or up to 1 week after</th>
                                            <th>D. Between 1-2 weeks after due date</th>
                                            <th>E. Between 2 weeks and 1 month after due date</th>
                                            <th>F. More than 1 month after due date</th>
                                            <th>G. Unknown</th>
                                            <th>H. Did not submit</th>

                                        </tr>
                                        <tr class="form-group">
                                            <th rowspan="3">TO3. FP and RH</th>
                                            <td>First Report
                                                <input type="hidden" id="form9_3_fr_id" name="form9_3_fr_id" value="First Report">
                                                <input type="hidden"  name="parent_id"  value="<?php echo $parent_id; ?>">
                                            </td>
                                            <?php
                                            $sql_2_11 = "SELECT
                                                    mne_timeliness.pk_id,
                                                    mne_timeliness.basic_id,
                                                    mne_timeliness.item_group,
                                                    mne_timeliness.report,
                                                    mne_timeliness.date_due,
                                                    mne_timeliness.date_sub,
                                                    mne_timeliness.due_1w,
                                                    mne_timeliness.due_1to2w,
                                                    mne_timeliness.due_2wto1m,
                                                    mne_timeliness.due_1m_above,
                                                    mne_timeliness.unknown,
                                                    mne_timeliness.not_sub
                                                    FROM
                                                    mne_timeliness
                                                    WHERE
                                                    mne_timeliness.basic_id = '$parent_id' AND
                                                    mne_timeliness.item_group = 'to3'"
                                                    . " AND report = 'First Report'";


                                            $query_2_11 = $conn->query($sql_2_11);

                                            $row_2_11 = $query_2_11->fetch_assoc();
                                            ?>
                                            <td><input type="date" class="form-control" id="form9_to3_fr_dd" name="form9_to3_fr_dd" value="<?php echo $row_2_11['date_due']; ?>"></td>
                                            <td><input type="date" class="form-control" id="form9_to3_fr_sd" name="form9_to3_fr_sd" value="<?php echo $row_2_11['date_sub']; ?>"></td>
                                            <td><input type="radio" style="margin-left: 50px;" id="form9_to3_fr" value="due_1w" name="form9_to3_fr" <?php
                                                if ($row_2_11['due_1w'] == '1') {
                                                    echo 'checked="checked"';
                                                }
                                                ?>></td>
                                            <td><input type="radio" style="margin-left: 50px;" id="form9_to3_fr" value="due_1to2w" name="form9_to3_fr" <?php
                                                if ($row_2_11['due_1to2w'] == '1') {
                                                    echo 'checked="checked"';
                                                }
                                                ?>></td>
                                            <td><input type="radio" style="margin-left: 65px;" id="form9_to3_fr" value="due_2wto1m" name="form9_to3_fr" <?php
                                                if ($row_2_11['due_2wto1m'] == '1') {
                                                    echo 'checked="checked"';
                                                }
                                                ?>></td>
                                            <td><input type="radio" style="margin-left: 45px;" id="form9_to3_fr" value="due_1m_above" name="form9_to3_fr" <?php
                                                if ($row_2_11['due_1m_above'] == '1') {
                                                    echo 'checked="checked"';
                                                }
                                                ?>></td>
                                            <td><input type="radio" style="margin-left: 25px;" id="form9_to3_fr" value="unknown" name="form9_to3_fr" <?php
                                                if ($row_2_11['unknown'] == '1') {
                                                    echo 'checked="checked"';
                                                }
                                                ?>></td>
                                            <td><input type="radio" style="margin-left: 25px;" id="form9_to3_fr" value="not_sub" name="form9_to3_fr" <?php
                                                if ($row_2_11['not_sub'] == '1') {
                                                    echo 'checked="checked"';
                                                }
                                                ?>></td>
                                        </tr>
                                        <tr class="form-group">
                                            <?php
                                            $sql_2_12 = "SELECT
                                        mne_timeliness.pk_id,
                                        mne_timeliness.basic_id,
                                        mne_timeliness.item_group,
                                        mne_timeliness.report,
                                        mne_timeliness.date_due,
                                        mne_timeliness.date_sub,
                                        mne_timeliness.due_1w,
                                        mne_timeliness.due_1to2w,
                                        mne_timeliness.due_2wto1m,
                                        mne_timeliness.due_1m_above,
                                        mne_timeliness.unknown,
                                        mne_timeliness.not_sub
                                        FROM
                                        mne_timeliness
                                        WHERE
                                        mne_timeliness.basic_id = '$parent_id' AND
                                        mne_timeliness.item_group = 'to3'"
                                                    . " AND report = 'Second Report'";


                                            $query_2_12 = $conn->query($sql_2_12);

                                            $row_2_12 = $query_2_12->fetch_assoc();
                                            ?>
                                            <td>Second Report
                                                <input type="hidden" id="form9_3_sr_id" name="form9_3_sr_id" value="Second Report">
                                            </td>
                                            <td><input type="date" class="form-control" id="form9_to3_sr_dd" name="form9_to3_sr_dd" value="<?php echo $row_2_12['date_due']; ?>"></td>
                                            <td><input type="date" class="form-control" id="form9_to3_sr_sd" name="form9_to3_sr_sd" value="<?php echo $row_2_12['date_sub']; ?>"></td>
                                            <td><input type="radio" style="margin-left: 50px;" id="form9_to3_sr" value="due_1w" name="form9_to3_sr" <?php
                                                if ($row_2_12['due_1w'] == '1') {
                                                    echo 'checked="checked"';
                                                }
                                                ?>></td>
                                            <td><input type="radio" style="margin-left: 50px;" id="form9_to3_sr" value="due_1to2w" name="form9_to3_sr" <?php
                                                if ($row_2_12['due_1to2w'] == '1') {
                                                    echo 'checked="checked"';
                                                }
                                                ?>></td>
                                            <td><input type="radio" style="margin-left: 65px;" id="form9_to3_sr" value="due_2wto1m" name="form9_to3_sr" <?php
                                                if ($row_2_12['due_2wto1m'] == '1') {
                                                    echo 'checked="checked"';
                                                }
                                                ?>></td>
                                            <td><input type="radio" style="margin-left: 45px;" id="form9_to3_sr" value="due_1m_above" name="form9_to3_sr" <?php
                                                if ($row_2_12['due_1m_above'] == '1') {
                                                    echo 'checked="checked"';
                                                }
                                                ?>></td>
                                            <td><input type="radio" style="margin-left: 25px;" id="form9_to3_sr" value="unknown" name="form9_to3_sr" <?php
                                                if ($row_2_12['unknown'] == '1') {
                                                    echo 'checked="checked"';
                                                }
                                                ?>></td>
                                            <td><input type="radio" style="margin-left: 25px;" id="form9_to3_sr" value="not_sub" name="form9_to3_sr" <?php
                                                if ($row_2_12['not_sub'] == '1') {
                                                    echo 'checked="checked"';
                                                }
                                                ?>></td>
                                        </tr>
                                        <tr class="form-group">
                                            <?php
                                            $sql_2_13 = "SELECT
                                        mne_timeliness.pk_id,
                                        mne_timeliness.basic_id,
                                        mne_timeliness.item_group,
                                        mne_timeliness.report,
                                        mne_timeliness.date_due,
                                        mne_timeliness.date_sub,
                                        mne_timeliness.due_1w,
                                        mne_timeliness.due_1to2w,
                                        mne_timeliness.due_2wto1m,
                                        mne_timeliness.due_1m_above,
                                        mne_timeliness.unknown,
                                        mne_timeliness.not_sub
                                        FROM
                                        mne_timeliness
                                        WHERE
                                        mne_timeliness.basic_id = '$parent_id' AND
                                        mne_timeliness.item_group = 'to3'"
                                                    . " AND report = 'Third Report'";


                                            $query_2_13 = $conn->query($sql_2_13);

                                            $row_2_13 = $query_2_13->fetch_assoc();
                                            ?>
                                            <td>Third Report
                                                <input type="hidden" id="form9_3_tr_id" name="form9_3_tr_id" value="Third Report"></td>
                                            <td><input type="date" class="form-control" id="form9_to3_tr_dd" name="form9_to3_tr_dd" value="<?php echo $row_2_13['date_due']; ?>"></td>
                                            <td><input type="date" class="form-control" id="form9_to3_tr_sd" name="form9_to3_tr_sd" value="<?php echo $row_2_13['date_sub']; ?>"></td>
                                            <td><input type="radio" style="margin-left: 50px;" id="form9_to3_tr" value="due_1w" name="form9_to3_tr" <?php
                                                if ($row_2_13['due_1w'] == '1') {
                                                    echo 'checked="checked"';
                                                }
                                                ?>></td>
                                            <td><input type="radio" style="margin-left: 50px;" id="form9_to3_tr" value="due_1to2w" name="form9_to3_tr"  <?php
                                                if ($row_2_13['due_1to2w'] == '1') {
                                                    echo 'checked="checked"';
                                                }
                                                ?>></td>
                                            <td><input type="radio" style="margin-left: 65px;" id="form9_to3_tr" value="due_2wto1m" name="form9_to3_tr"  <?php
                                                if ($row_2_13['due_2wto1m'] == '1') {
                                                    echo 'checked="checked"';
                                                }
                                                ?>></td>
                                            <td><input type="radio" style="margin-left: 45px;" id="form9_to3_tr" value="due_1m_above" name="form9_to3_tr"  <?php
                                                if ($row_2_13['due_1m_above'] == '1') {
                                                    echo 'checked="checked"';
                                                }
                                                ?>></td>
                                            <td><input type="radio" style="margin-left: 25px;" id="form9_to3_tr" value="unknown" name="form9_to3_tr"  <?php
                                                if ($row_2_13['unknown'] == '1') {
                                                    echo 'checked="checked"';
                                                }
                                                ?>></td>
                                            <td><input type="radio" style="margin-left: 25px;" id="form9_to3_tr" value="not_sub" name="form9_to3_tr"  <?php
                                                if ($row_2_13['not_sub'] == '1') {
                                                    echo 'checked="checked"';
                                                }
                                                ?>></td>
                                        </tr>

                                        <tr class="form-group">
                                            <td colspan="10">
                                                Comment:
                                                <textarea class="form-control" id="form9_comment" name="form9_comment"></textarea>
                                            </td>                    
                                        </tr>
                                    </table>
                                    <a class="btn btn-default pull-right"  onclick="form9_insertion();" >Submit</a><br><br><br><br><br><br>
                                </form>
                                <div id="form_9_insert_resp"></div>
                            </div>

                        </div>


                        <div class="tab-pane" id="t9t4">
                            <div><br>
                                <h4>DATA TIMELINESS</h4><br>
                                <form method="POST" id="form_9_insert">
                                    <table border="1" width="100%" class="table table-bordered">
                                        <tr>

                                            <th colspan="2" rowspan="2"></th>
                                            <th rowspan="2">A. Date Due</th>
                                            <th rowspan="2">B. Submitted</th>
                                            <th colspan="6">Enter a 1 under the timeframe of reporting that best describes when each report was submitted. Only make one entry per report. Leave all other cells blank.</th>

                                        </tr>
                                        <tr>

                                            <th>C. By due date or up to 1 week after</th>
                                            <th>D. Between 1-2 weeks after due date</th>
                                            <th>E. Between 2 weeks and 1 month after due date</th>
                                            <th>F. More than 1 month after due date</th>
                                            <th>G. Unknown</th>
                                            <th>H. Did not submit</th>

                                        </tr>
                                        <tr class="form-group">
                                            <th rowspan="3">TO4. MNCH</th>
                                            <?php
                                            $sql_2_22 = "SELECT
                                                    mne_timeliness.pk_id,
                                                    mne_timeliness.basic_id,
                                                    mne_timeliness.item_group,
                                                    mne_timeliness.report,
                                                    mne_timeliness.date_due,
                                                    mne_timeliness.date_sub,
                                                    mne_timeliness.due_1w,
                                                    mne_timeliness.due_1to2w,
                                                    mne_timeliness.due_2wto1m,
                                                    mne_timeliness.due_1m_above,
                                                    mne_timeliness.unknown,
                                                    mne_timeliness.not_sub
                                                    FROM
                                                    mne_timeliness
                                                    WHERE
                                                    mne_timeliness.basic_id = '$parent_id' AND
                                                    mne_timeliness.item_group = 'to4'"
                                                    . " AND report = 'First Report'";


                                            $query_2_2 = $conn->query($sql_2_22);

                                            $row_2_22 = $query_2_2->fetch_assoc();
                                            ?>
                                            <td>First Report
                                                <input type="hidden" id="form9_4_fr_id" name="form9_4_fr_id" value="First Report"></td>
                                            <td><input type="date" class="form-control" id="form9_to4_fr_dd" name="form9_to4_fr_dd" value="<?php echo $row_2_22['date_due']; ?>"></td>
                                            <td><input type="date" class="form-control" id="form9_to4_fr_sd" name="form9_to4_fr_sd" value="<?php echo $row_2_22['date_sub']; ?>"></td>
                                            <td><input type="radio" style="margin-left: 50px;" id="form9_to4_fr" value="due_1w" name="form9_to4_fr" <?php
                                                if ($row_2_22['due_1w'] == '1') {
                                                    echo 'checked="checked"';
                                                }
                                                ?>  ></td>

                                            <td><input type="radio" style="margin-left: 50px;" id="form9_to4_fr" value="due_1to2w" name="form9_to4_fr" <?php
                                                if ($row_2_22['due_1to2w'] == '1') {
                                                    echo 'checked="checked"';
                                                }
                                                ?> ></td>
                                            <td><input type="radio" style="margin-left: 65px;" id="form9_to4_fr" value="due_2wto1m" name="form9_to4_fr" <?php
                                                if ($row_2_22['due_2wto1m'] == '1') {
                                                    echo 'checked="checked"';
                                                }
                                                ?>></td>
                                            <td><input type="radio" style="margin-left: 45px;" id="form9_to4_fr" value="due_1m_above" name="form9_to4_fr" <?php
                                                if ($row_2_22['due_1m_above'] == '1') {
                                                    echo 'checked="checked"';
                                                }
                                                ?>></td>
                                            <td><input type="radio" style="margin-left: 25px;" id="form9_to4_fr" value="unknown" name="form9_to4_fr" <?php
                                                if ($row_2_22['unknown'] == '1') {
                                                    echo 'checked="checked"';
                                                }
                                                ?>></td>
                                            <td><input type="radio" style="margin-left: 25px;" id="form9_to4_fr" value="not_sub" name="form9_to4_fr" <?php
                                                if ($row_2_22['not_sub'] == '1') {
                                                    echo 'checked="checked"';
                                                }
                                                ?>></td>
                                        </tr>
                                        <tr class="form-group">
                                            <?php
                                            $sql_2_33 = "SELECT
                                                    mne_timeliness.pk_id,
                                                    mne_timeliness.basic_id,
                                                    mne_timeliness.item_group,
                                                    mne_timeliness.report,
                                                    mne_timeliness.date_due,
                                                    mne_timeliness.date_sub,
                                                    mne_timeliness.due_1w,
                                                    mne_timeliness.due_1to2w,
                                                    mne_timeliness.due_2wto1m,
                                                    mne_timeliness.due_1m_above,
                                                    mne_timeliness.unknown,
                                                    mne_timeliness.not_sub
                                                    FROM
                                                    mne_timeliness
                                                    WHERE
                                                    mne_timeliness.basic_id = '$parent_id' AND
                                                    mne_timeliness.item_group = 'to4'"
                                                    . " AND report = 'Second Report'";


                                            $query_2_3 = $conn->query($sql_2_33);

                                            $row_2_33 = $query_2_3->fetch_assoc();
                                            ?>
                                            <td>Second Report
                                                <input type="hidden" id="form9_4_sr_id" name="form9_4_sr_id" value="Second Report"></td>
                                            <td><input type="date" class="form-control" id="form9_to4_sr_dd" name="form9_to4_sr_dd" value="<?php echo $row_2_33['date_due']; ?>"></td>
                                            <td><input type="date" class="form-control" id="form9_to4_sr_sd" name="form9_to4_sr_sd" value="<?php echo $row_2_33['date_sub']; ?>"></td>
                                            <td><input type="radio" style="margin-left: 50px;" id="form9_to4_sr" value="due_1w" name="form9_to4_sr" <?php
                                                if ($row_2_33['due_1w'] == '1') {
                                                    echo 'checked="checked"';
                                                }
                                                ?>></td>
                                            <td><input type="radio" style="margin-left: 50px;" id="form9_to4_sr" value="due_1to2w" name="form9_to4_sr" <?php
                                                if ($row_2_33['due_1to2w'] == '1') {
                                                    echo 'checked="checked"';
                                                }
                                                ?>></td>
                                            <td><input type="radio" style="margin-left: 65px;" id="form9_to4_sr" value="due_2wto1m" name="form9_to4_sr" <?php
                                                if ($row_2_33['due_2wto1m'] == '1') {
                                                    echo 'checked="checked"';
                                                }
                                                ?>></td>
                                            <td><input type="radio" style="margin-left: 45px;" id="form9_to4_sr" value="due_1m_above" name="form9_to4_sr" <?php
                                                if ($row_2_33['due_1m_above'] == '1') {
                                                    echo 'checked="checked"';
                                                }
                                                ?>></td>
                                            <td><input type="radio" style="margin-left: 25px;" id="form9_to4_sr" value="unknown" name="form9_to4_sr" <?php
                                                if ($row_2_33['unknown'] == '1') {
                                                    echo 'checked="checked"';
                                                }
                                                ?>></td>
                                            <td><input type="radio" style="margin-left: 25px;" id="form9_to4_sr" value="not_sub" name="form9_to4_sr" <?php
                                                if ($row_2_33['not_sub'] == '1') {
                                                    echo 'checked="checked"';
                                                }
                                                ?>></td>
                                        </tr>
                                        <tr class="form-group">
                                            <?php
                                            $sql_2_44 = "SELECT
                                                    mne_timeliness.pk_id,
                                                    mne_timeliness.basic_id,
                                                    mne_timeliness.item_group,
                                                    mne_timeliness.report,
                                                    mne_timeliness.date_due,
                                                    mne_timeliness.date_sub,
                                                    mne_timeliness.due_1w,
                                                    mne_timeliness.due_1to2w,
                                                    mne_timeliness.due_2wto1m,
                                                    mne_timeliness.due_1m_above,
                                                    mne_timeliness.unknown,
                                                    mne_timeliness.not_sub
                                                    FROM
                                                    mne_timeliness
                                                    WHERE
                                                    mne_timeliness.basic_id = '$parent_id' AND
                                                    mne_timeliness.item_group = 'to4'"
                                                    . " AND report = 'Third Report'";


                                            $query_2_4 = $conn->query($sql_2_44);

                                            $row_2_44 = $query_2_4->fetch_assoc();
                                            ?>
                                            <td>Third Report
                                                <input type="hidden" id="form9_4_tr_id" name="form9_4_tr_id" value="Third Report"></td>
                                            <td><input type="date" class="form-control" id="form9_to4_tr_dd" name="form9_to4_tr_dd" value="<?php echo $row_2_44['date_due']; ?>"></td>
                                            <td><input type="date" class="form-control" id="form9_to4_tr_sd" name="form9_to4_tr_sd" value="<?php echo $row_2_44['date_sub']; ?>"></td>
                                            <td><input type="radio" style="margin-left: 50px;" id="form9_to4_tr" value="due_1w" name="form9_to4_tr" <?php
                                                if ($row_2_44['due_1w'] == '1') {
                                                    echo 'checked="checked"';
                                                }
                                                ?>></td>
                                            <td><input type="radio" style="margin-left: 50px;" id="form9_to4_tr" value="due_1to2w" name="form9_to4_tr" <?php
                                                if ($row_2_44['due_1to2w'] == '1') {
                                                    echo 'checked="checked"';
                                                }
                                                ?>></td>
                                            <td><input type="radio" style="margin-left: 65px;" id="form9_to4_tr" value="due_2wto1m" name="form9_to4_tr" <?php
                                                if ($row_2_44['due_2wto1m'] == '1') {
                                                    echo 'checked="checked"';
                                                }
                                                ?>></td>
                                            <td><input type="radio" style="margin-left: 45px;" id="form9_to4_tr" value="due_1m_above" name="form9_to4_tr" <?php
                                                if ($row_2_44['due_1m_above'] == '1') {
                                                    echo 'checked="checked"';
                                                }
                                                ?>></td>
                                            <td><input type="radio" style="margin-left: 25px;" id="form9_to4_tr" value="unknown" name="form9_to4_tr" <?php
                                                if ($row_2_44['unknown'] == '1') {
                                                    echo 'checked="checked"';
                                                }
                                                ?>></td>
                                            <td><input type="radio" style="margin-left: 25px;" id="form9_to4_tr" value="not_sub" name="form9_to4_tr" <?php
                                                if ($row_2_44['not_sub'] == '1') {
                                                    echo 'checked="checked"';
                                                }
                                                ?>></td>
                                        </tr>

                                        <tr class="form-group">
                                            <td colspan="10">
                                                Comment:
                                                <textarea class="form-control" id="form9_comment" name="form9_comment"></textarea>
                                            </td>                    
                                        </tr>
                                    </table>
                                    <a class="btn btn-default pull-right"  onclick="form9_insertion();" >Submit</a><br><br><br><br><br><br>
                                </form>
                                <div id="form_9_insert_resp"></div>
                            </div>

                        </div>
                        <div class="tab-pane" id="t10">
                            <div id="tab10_data">
                            </div>

                        </div>
                    </div>
                </div>
                <?php
            }
            ?>                                  

        </div>


    </body>

</html>
<?php
if (empty($parent_id)) {
    $parent_id = 0;
} else {
    $parent_id = $parent_id;
}
?>
<script>

    $(document).ready(function () {

        $("#t7_click").click(function () {
            $.ajax({
                type: "POST",
                url: "ajax-data_tab7.php",
                data: {parent_id: <?php echo $parent_id; ?>},
                dataType: 'html',
                success: function (data) {

                    $('#tab7_data').html(data);
                }
            });
        });

        $("#t8_click").click(function () {
            $.ajax({
                type: "POST",
                url: "ajax-data_tab8.php",
                data: {parent_id: <?php echo $parent_id; ?>},
                dataType: 'html',
                success: function (data) {

                    $('#tab8_data').html(data);
                }
            });
        });

        $("#t10_click").click(function () {
            $.ajax({
                type: "POST",
                url: "ajax-data_tab10.php",
                data: {parent_id: <?php echo $parent_id; ?>},
                dataType: 'html',
                success: function (data) {

                    $('#tab10_data').html(data);
                }
            });
        });

        $("#t1_click").click(function () {
            $.ajax({
                type: "POST",
                url: "ajax-data_tab1.php",
                data: {parent_id: <?php echo $parent_id; ?>},
                dataType: 'html',
                success: function (data) {

                    $('#tab1_data').html(data);
                }
            });
        });
    });
    $('#date_visit').change(function () {

        var facility_level = $('#facility_level').val();
        var facility = $('#facility').val();
        var date_visit = $('#date_visit').val();

        $.ajax({
            type: "POST",
            url: "ajax-data.php",
            data: {facility_level: facility_level, facility: facility, date_visit: date_visit},
            dataType: 'html',
            success: function (data) {
                obj = JSON.parse(data);

                if (obj == null) {

                    $('#name').val('');
                    $('#sig').val('');
                    $('#stk_mngr_name').val('');
                    $('#stk_mngr_name').val('');
                    $('#commodity_type').val('');
                    $('#commodity_type_h').val('');
                } else {
                    $('#name').val(obj.name);
                    $('#sig').val(obj.sig);
                    $('#stk_mngr_name').val(obj.stock_mgr);
                    $('#stk_mngr_name').val(obj.stock_mgr);
                    $('#commodity_type').val(obj.item_group);
                    if (obj.item_group == 1) {
                        $('#commodity_type_h').val('Family Planning and Reproductive Health');
                    } else {
                        $('#commodity_type_h').val('Material and Child Health');
                    }
                }
            }
        });

    });
    $("#facility").select2();
    loadform3();
    function show(v) {
        if (v == 't03') {

            $('li.active').removeClass('active');
            $("#t03 li:first").addClass("active");
            $("#t04").removeClass("active");
            $("#summary").removeClass("active");

            $("#at3").trigger("click");
            $(".tab-pane").removeClass("active");
            $("#t3").addClass("active");


            // loadform3();
        } else if (v == 'summ') {

            $('li.active').removeClass('active');
            $("#summary li:first").addClass("active");

            $("#t04").removeClass("active");
            $("#t03").removeClass("active");
            $('#t1_click').trigger('click');
            $(".tab-pane").removeClass("active");
            $("#t1").addClass("active");

        } else {

            $('li.active').removeClass('active');
            $("#t04 li:first").addClass("active");
            $("#t03").removeClass("active");
            $("#summary").removeClass("active");

            $(".tab-pane").removeClass("active");
            $("#t5").addClass("active");
            $("#at4").trigger("click");
        }
    }

    function shForm4(c) {

        if (c == 'mth1') {

            $(".mth2").attr("readonly", true);
            $(".mth2").val(0);
            $(".mth1").attr("readonly", false);

        } else {
            $(".mth1").attr("readonly", true);
            $(".mth1").val(0);
            $(".mth2").attr("readonly", false);
        }
    }
</script>