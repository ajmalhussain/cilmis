<?php
include("../includes/classes/Configuration.inc.php");
//readfile("../../public/assets/global/plugins/select2/select2.css"); 
?>
<?php
$search_type = isset($_REQUEST['search_type']) ? $_REQUEST['search_type'] : '';
$province = isset($_REQUEST['province']) ? $_REQUEST['province'] : '';
if ($search_type == 1) {
    $item_filter = " itminfo_tab.generic_name as itm_name";
    $order_by = " itminfo_tab.generic_name ASC";
} else {
    $item_filter = " itminfo_tab.itm_name as itm_name, itminfo_tab.itm_id AS itm_id";
    $order_by = " itminfo_tab.itm_name ASC";
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

if (isset($_REQUEST['multiple']) && $search_type == 1 && !(isset($_REQUEST['get_select']))) {
    ?>

    <?php
    while ($row = $dist_res->fetch_assoc()) {
        ?>

        <option value="<?php
        if ($search_type == 1)
            echo $row['itm_name'];
        else {
            echo $row['itm_id'];
        }
        ?>"><?php echo $row['itm_name']; ?></option>
                <?php
            }
            ?>



    <?php
} else if ((isset($_REQUEST['multiple']) && $search_type == 1 && (isset($_REQUEST['get_select'])))) {
    ?>
    <script type="text/javascript" src="../../public/assets/global/plugins/select2/select2.min.js"></script>

    <select required  name="product" id="product" class="input-medium select2me">
        <?php
        while ($row = $dist_res->fetch_assoc()) {
            ?>

            <option value="<?php
            if ($search_type == 1)
                echo $row['itm_name'];
            else {
                echo $row['itm_id'];
            }
            ?>"><?php echo $row['itm_name']; ?></option>
                    <?php
                }
                ?>
    </select>

    <?php
} else if (isset($_REQUEST['multiple']) && $search_type != 1) {
    ?>
    <style>
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


    </style>
    <select required  name="product[]" id="product" class="multiselect-ui form-control input-sm" multiple>

        <?php
        while ($row = $dist_res->fetch_assoc()) {
            ?>

            <option value="<?php
            if ($search_type == 1)
                echo $row['itm_name'];
            else {
                echo $row['itm_id'];
            }
            ?>"><?php echo $row['itm_name']; ?></option>
                    <?php
                }
                ?>
    </select>
    <script src="<?= PUBLIC_URL ?>js/bootstrap-multiselect.js"></script>

    <script>
        $(function () {
            $('.multiselect-ui').multiselect({
                includeSelectAllOption: false,
                enableFiltering: true,
                enableCaseInsensitiveFiltering: true,
                onChange: function (option, checked) {
                    var selectedOptions = $('.multiselect-ui option:selected');

                    if (selectedOptions.length >= 2) {
                        // Disable all other checkboxes.
                        var nonSelectedOptions = $('.multiselect-ui option').filter(function () {
                            return !$(this).is(':selected');
                         alert('Only 2 districts can be selected for comparison');
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
} else if (isset($_REQUEST['both_multiple']) && $search_type == 1) {
    ?>
    <select required  name="product[]" id="product" class="multiselect-ui form-control input-sm" multiple>
        <option value="">Select</option>

        <?php
        while ($row = $dist_res->fetch_assoc()) {
            ?>

            <option value="<?php
            if ($search_type == 1)
                echo $row['itm_name'];
            else {
                echo $row['itm_id'];
            }
            ?>"><?php echo $row['itm_name']; ?></option>
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
} else {
    while ($row = $dist_res->fetch_assoc()) {
        ?>

        <option value="<?php
        if ($search_type == 1)
            echo $row['itm_name'];
        else {
            echo $row['itm_id'];
        }
        ?>"><?php echo $row['itm_name']; ?></option>
        <?php
    }
}
?>