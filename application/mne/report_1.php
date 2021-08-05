


<?php
//include AllClasses
require("../includes/classes/AllClasses.php");
//include FunctionLib
require("../includes/report/FunctionLib.php");
//include header
include(PUBLIC_PATH . "html/header.php");
?>

<?php



?> 
<body class="page-header-fixed page-quick-sidebar-over-content" >
    <div class="page-container">

  <?php 
        //include top
        include PUBLIC_PATH . "html/top.php"; 
        //include top_im
        include PUBLIC_PATH . "html/top_im.php"; ?>
        <div class="page-content-wrapper">
            <div class="page-content">
                <div class="row">

                    <h2 style="text-align:center;">Summary Report</h2>
                    <br>
                    <div class="form-group col-xs-2">
                        <label for="start_date">Start Date:</label>
                        <input type="text" class="form-control"  name="start_date" value="<?php if (isset($_POST['start_date'])) echo $_POST['start_date']; ?>" id="datepicker"> 
                    </div>
                    <div class="form-group col-xs-2">
                        <label for="end_date">End Date:</label>
                        <input type="text"  class="form-control" name="end_date" value="<?php if (isset($_POST['end_date'])) echo $_POST['end_date']; ?>" id="datepicker1">
                    </div>
                    <div class="form-group col-xs-2" >
                        <label for="province">Province:</label>                       
                        <select name="province" class="form-control" id="province">

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
                                <option value= <?php echo $pk_id; ?> > <?php echo $province_name; ?> </option>
                            <?php }
                            ?>
                        </select>
                    </div>

                    <div class="form-group col-xs-2">

                        <p style="margin-top:23px !important; "><button type="button" class="btn btn-primary" onclick="loaddata();" name="submit" id="submit">Search</button></p>
                    </div>

                </div>

                <div class="row">

                    <div class="loader" id="loader"></div>
                    <div id="display_info" >
                    </div>
                </div>

            </div>
        </div>
    </div>
    <?php
  include PUBLIC_PATH . "/html/footer.php"; 
    //include reports_include
    include PUBLIC_PATH . "/html/reports_includes.php"; ?>

?>
<script type="text/javascript">
    $(function () {
        $('#datepicker1').datepicker({dateFormat: 'yy-mm-dd'});
        $('#datepicker').datepicker({dateFormat: 'yy-mm-dd'});
        $('#loader').hide();
        $('.multiselect-ui').multiselect({
            includeSelectAllOption: true
        });

    });

    function loaddata()
    {
        // var submit=document.getElementById( "submit" );

        var start = $('#datepicker').val();
        var end = $('#datepicker1').val();
        var province = $('#province').val();

        var stakeholder = $('#stakeholder').val();
        var product = $('#product').val();
        //  var product = $('#product').val();


        if (start)
        {
            $('#display_info').html('');
            $('#loader').show();

            $.ajax({
                type: 'post',
                url: 'loaddata.php',
                data: {
                    // user_name:submit,
                    start_date: start,
                    end_date: end,
                    province: province
                  
                },
                success: function (response) {
                    // We get the element having id of display_info and put the response inside it
                    $('#display_info').html(response);
                    $('#loader').hide();
                }
            });

        } else
        {
            $('#display_info').html("No Data Exist");
        }

    }
</script>
</body>


</html>