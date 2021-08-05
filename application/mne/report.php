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

</head>
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
<?php
error_reporting(1);
include("db.php");


?> 
<body>
    <div class="container" >


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
</body>
</html>