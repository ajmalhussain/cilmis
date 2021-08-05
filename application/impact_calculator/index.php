<?php
ob_start();

//include 'db_connection.php';
include("../includes/classes/Configuration.inc.php");

//Including db file
include(APP_PATH . "includes/classes/db.php");
//Including header file
include(PUBLIC_PATH . "html/header.php");
//$sql = "SELECT year_name FROM motorcube_year ORDER BY year_name DESC";
//$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1"> 
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <style type="text/css">


            /* Center the loader */
            #loader {
                position: relative;
                left: 50%;
                top: 50%;
                z-index: 1;
                display: none;
                width: 100px;
                height: 100px;
                margin: -75px 0 0 -75px;
                border: 16px solid #c3ffbc;
                border-radius: 50%;
                border-top: 16px solid #009C00;
                width: 100px;
                height: 100px;
                -webkit-animation: spin 1s linear infinite;
                animation: spin 1s linear infinite;
            }

            @-webkit-keyframes spin {
                0% { -webkit-transform: rotate(0deg); }
                100% { -webkit-transform: rotate(360deg); }
            }

            @keyframes spin {
                0% { transform: rotate(0deg); }
                100% { transform: rotate(360deg); }
            }

            /* Add animation to "page content" */
            .animate-bottom {
                position: relative;
                -webkit-animation-name: animatebottom;
                -webkit-animation-duration: 1s;
                animation-name: animatebottom;
                animation-duration: 1s
            }

            @-webkit-keyframes animatebottom {
                from { bottom:-100px; opacity:0 } 
                to { bottom:0px; opacity:1 }
            }

            @keyframes animatebottom { 
                from{ bottom:-100px; opacity:0 } 
                to{ bottom:0; opacity:1 }
            }

            #myDiv {
                display: none;
                text-align: center;
            }


            .inline-items > * {
                display: inline-block;
                vertical-align: middle;
            }

            .float-right { float: right; }

        </style> 
        <link rel="stylesheet" type="text/css" href="../../public/assets/global/plugins/select2/select2.css"/>

    </head>
    <body class="page-header-fixed page-quick-sidebar-over-content">

        <div class="page-container">

            <?php
            //Including top file
            include PUBLIC_PATH . "html/top.php";
            //Including top_im file
            include PUBLIC_PATH . "html/top_im.php";
            ?>
            <div class="page-content-wrapper ">
                <div class="page-content">
                    <div class="container-fluid">


                        <div class="row">

                            <div class="widget" data-toggle="">
                                <div class="widget-head"><h3 class="heading">Impact Calculator</h3></div>
                                <div class="widget-body collapse in">
                                    <div class=" ">

                                        <form id="frm1" name="frm1">
                                            <div class="row">
                                                <div class="form-group col-md-3">
                                                    <label for="product">Product:</label>
                                                    <select name="product[]" id="product" class="select2me input-medium" multiple>
                                                        <option value="all">All</option>
                                                        <?php
                                                        $qry = "SELECT DISTINCT
                                                        itminfo_tab.itm_id,
                                                        itminfo_tab.itm_name 
                                                        FROM
                                                        itminfo_tab 
                                                        WHERE
                                                        itminfo_tab.itm_category = 1 
                                                        ORDER BY
                                                        itminfo_tab.frmindex ASC
                                                        ";
                                                        $qryRes = mysql_query($qry);
                                                        while ($row = mysql_fetch_array($qryRes)) {
                                                            ?>
                                                            <option value="<?php echo $row['itm_id']; ?>"><?php echo $row['itm_name']; ?></option>
                                                            <?php
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                                <div class="form-group col-md-2">

                                                    <label for="location">Location:</label>
                                                    <select class="form-control" id="location" name="location" onchange="" required>
                                                        <option value="10">National</option>
                                                        <option value="1">Punjab</option>
                                                        <option value="2">Sindh</option>
                                                        <option value="3">Khyber Pakhtunkhwa</option>
                                                        <option value="4">Balochistan</option>
                                                    </select>
                                                </div>
                                                <div class="form-group col-md-3">

                                                    <label for="sel1">Year:</label>
                                                    <br>
                                                    <select id="year" name="year[]" required class="select2me input-medium" multiple>
                                                        <option value="">Select Year</option>
                                                        <?php
                                                        for ($i = date('Y'); $i >= 2010; $i--) {
                                                            ?>
                                                            <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                                                            <?php
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                                <div class="form-group   col-md-2">
                                                    <label for="show_btn1">  </label>
                                                    <a id="show_btn1" class="btn btn-sm green form-control"  onclick="show_data()"> Show Population </a>
                                                </div>
                                            </div>
                                        </form>

                                        <div id="loader"></div>


                                        <div class="" id="show-fields">
                                        </div>


                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>


                </div>

            </div>
        </div>
<div id="myModal" class="modal fade" role="dialog">
            <div class="modal-dialog">

                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Population Addition</h4>
                    </div>
                    <div class="modal-body">
                        <form name="pop_form" id="pop_form">
                            <label>Total Population</label>
                            <input id="new_pop" name="new_pop" number type='text' class="form-control input-sm"> 
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" data-dismiss="modal" id='save_pop' name='save_pop' onclick="add_pop()">Save</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>

            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <table class="table table-bordered table-hover">
                    <caption style="background-color: #009C00"><h3>CYP FORMULA FOR ALL PRODUCTS</h3></caption>
                    <tr style="background-color: #017ebc">
                        <th>Product</th>
                        <th>CYP</th>
                        <th>Formula</th>
                    </tr>
                    <tr>
                        <td>CONDOM</td>
                        <td>120</td>
                        <td>0.00833</td>
                    </tr>
                    <tr>
                        <td>POP</td>
                        <td>15</td>
                        <td>0.06667</td>
                    </tr>
                    <tr>
                        <td>ECP</td>
                        <td>20</td>
                        <td>0.05</td>
                    </tr>
                    <tr>
                        <td>Multiload</td>
                        <td>1 for 3.3 years average</td>
                        <td>3.3</td>
                    </tr>
                    <tr>
                        <td>Copper-T-380A</td>
                        <td>1 for 4.6 years average</td>
                        <td>4.6</td>
                    </tr>
                    <tr>
                        <td>2-Month Inj</td>
                        <td>6</td>
                        <td>0.16667</td>
                    </tr>
                    <tr>
                        <td>3-Month Inj	</td>
                        <td>4</td>
                        <td>0.25</td>
                    </tr>
                    <tr>
                        <td>Implanon</td>
                        <td>1 for 2.5 years average</td>
                        <td>2.5</td>
                    </tr>
                     <tr>
                        <td>COC</td>
                        <td>15</td>
                        <td>0.06667</td>
                    </tr>
                     <tr>
                        <td>1-Month Inj</td>
                        <td>13</td>
                        <td>0.07692</td>
                    </tr>
                     <tr>
                        <td>Safe Load</td>
                        <td>1 for 3.3 years average</td>
                        <td>3.3</td>
                    </tr>
                     <tr>
                        <td>Jadelle</td>
                        <td>1 for 3.8 years average</td>
                        <td>3.8</td>
                    </tr>
                     <tr>
                        <td>Femplant</td>
                        <td>1 for 3.2 years average	</td>
                        <td>3.2</td>
                    </tr>
                    <tr>
                        <td>Implanon NXT</td>
                        <td>1 for 2.5 years average</td>
                        <td>2.5</td>
                    </tr>
                    <tr>
                        <td>Surgeries</td>
                        <td>MALE/FEMALE</td>
                        <td>13</td>
                    </tr>
                </table>
                
            </div>
            
        </div>
        <?php
//Including footer file
        include PUBLIC_PATH . "/html/footer.php";
        ?>
    <script type="text/javascript" src="../../public/assets/global/plugins/select2/select2.min.js"></script>
        <script type="text/javascript">

            function show_data() {
                $.ajax({
                    url: 'get_record.php',
                    type: 'POST',
                    data: $('#frm1').serialize(),
                    success: function (data) {
                        // alert(data);
                        $('#show-fields').html(data);
                    }
                });

            }
            function calculate()
            {

                $.ajax({
                    url: 'get_record.php',
                    type: 'POST',
                    data: {'calculate': true, location: $('#location').val(), 'selected_year': $('#year').val(), 'female_pop': $(f_pop).val(), product: $("#product").val()},
                    beforeSend: function () {
                        $('#loader').show();
                    },
                    success: function (data) {
                        // alert(data);

                        $('#calculate').html(data);
                        $('#loader').hide();
                    },

                });

            }
            function add_pop(){ 
                $.ajax({
                    url: 'ajax_addpop.php',
                    type: 'POST',
                    data: {new_pop:$("#new_pop").val(),hidden_dist:$("#location").val(),hidden_year:$("#year").val()},
                    success: function (data) {
//                         alert(data);
                        $('#data').html(data);
                    }
                }); 
            }
            function open_popup()
            {
                $('#myModal').modal('show'); 
            }
        </script>
    </body>
</html>

<?php
//$conn->close();

ob_end_flush();
?>