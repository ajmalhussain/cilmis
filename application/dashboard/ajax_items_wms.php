<?php
include("../includes/classes/Configuration.inc.php");

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
        position: relative
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
$search_type = isset($_REQUEST['search_type']) ? $_REQUEST['search_type'] : '';
$province = isset($_REQUEST['province']) ? $_REQUEST['province'] : '';
if ($search_type == 1) {
    $item_filter = " itminfo_tab.generic_name as itm_name";
    $order_by=" itminfo_tab.generic_name ASC";
} else {
    $item_filter = " itminfo_tab.itm_name as itm_name, itminfo_tab.itm_id AS itm_id";
    $order_by=" itminfo_tab.itm_name ASC";
}
$qry = "SELECT DISTINCT
                     $item_filter
                     FROM
                     itminfo_tab
                     INNER JOIN stakeholder_item ON itminfo_tab.itm_id = stakeholder_item.stk_item
                      WHERE
                        stakeholder_item.stkid IN (7, 74, 145, 276)
                      
                     ORDER BY
                     $order_by";


//        print_r($qry);exit;
$dist_res = mysqli_query($connc, $qry);
if(isset($_REQUEST['multiple'])&&$search_type!=1){
    ?>
      <select required  name="product[]" id="product" class="multiselect-ui form-control input-sm" multiple>

        <?php
         while ($row = $dist_res->fetch_assoc()) {
  
    ?>

    <option value="<?php if($search_type==1)echo $row['itm_name']; else{ echo $row['itm_id'];} ?>"><?php echo $row['itm_name']; ?></option>
    <?php
}
?>
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
else if (isset($_REQUEST['multiple'])&&$search_type==1){
    ?>
    
    
                                                           
    <?php
  while ($row = $dist_res->fetch_assoc()) {
  
    ?>

    <option value="<?php if($search_type==1)echo $row['itm_name']; else{ echo $row['itm_id'];} ?>"><?php echo $row['itm_name']; ?></option>
    <?php
}
  ?>
    
       
    
      <?php
}
 else if(isset($_REQUEST['both_multiple'])&&$search_type!=1){
    ?>
      <select required  name="product[]" id="product" class="multiselect-ui form-control input-sm" multiple>

        <?php
         while ($row = $dist_res->fetch_assoc()) {
  
    ?>

    <option value="<?php if($search_type==1)echo $row['itm_name']; else{ echo $row['itm_id'];} ?>"><?php echo $row['itm_name']; ?></option>
    <?php
}
?>
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
else if (isset($_REQUEST['both_multiple'])&&$search_type==1){
    ?>
      <select required  name="product[]" id="product" class="multiselect-ui form-control input-sm" multiple>
     <option value="">Select</option>
                                                           
    <?php
  while ($row = $dist_res->fetch_assoc()) {
  
    ?>

    <option value="<?php if($search_type==1)echo $row['itm_name']; else{ echo $row['itm_id'];} ?>"><?php echo $row['itm_name']; ?></option>
    <?php
}
  ?>
       </select>
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
else{
    while ($row = $dist_res->fetch_assoc()) {
  
    ?>

    <option value="<?php if($search_type==1)echo $row['itm_name']; else{ echo $row['itm_id'];} ?>"><?php echo $row['itm_name']; ?></option>
    <?php
}
}?>
