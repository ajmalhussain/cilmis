<?php
include("db.php");
$parent_id = $_POST['parent_id'];
?>
<br>
<form  method="POST" id="form_4_exe">
    <h4>
        DATA ACCURACY
    </h4>
    <h4>
        FAMILY PLANNING AND REPRODUCTIVE HEALTH
    </h4>
    <br>
    <table border="1px" width="100%" class="table table-bordered">
        <tr>
            <th colspan="6">
                A. Select method of data accuracy assessment  <input type="hidden"   name="parent_id" value="<?php echo $parent_id; ?>">
            </th>
        </tr>
        <?php
        $sql_1m = "SELECT
                    mne_accuracy.method
                    
                    FROM
                    mne_accuracy
                    WHERE
                    mne_accuracy.basic_id = '$parent_id'
                    
                    and item_group = 'to3' LIMIT 1";
        $query_1m = $conn->query($sql_1m);
        $rowm = $query_1m->fetch_assoc()
        ?>
        <tr>
            <th colspan="6" style="text-align: left;">
                <p><input type="radio" <?php
                    if ($rowm['method'] == '1') {
                        echo "checked=checked";
                    }
                    ?> value="1" name="chke" id="mth1" onclick="shForm4('mth1')" > Most recently reported ending balance (as per LMIS) or other stock level observation compared to source documentation (stock register, etc.)</p>
                <p><input type="radio" <?php
                    if ($rowm['method'] == '2') {
                        echo "checked=checked";
                    }
                    ?> value="2" name="chke" id="mth2" onclick="shForm4('mth2')"> Current stock on hand data (per stock register/bin card/stock card) compared to physical count </p>
            </th>
        </tr>
        <tr>
            <th>Tracer Product </th>
            <th>B. Stock report ending balance from most recent report (LMIS) </th>
            <th>C. Stock register/bin/stock card ending balance as of most recently reported month </th>
            <th>D. Stock register/bin/stock card current ending balance (if comparing to physical count) </th>
            <th>E. Physical stock count (if applicable) </th>
            <th>F. Is stock level data accurate? (1=Yes, 0=No) </th>
        </tr>

        <?php
        $query = $conn->query("SELECT
                                            itminfo_tab.itm_id,
                                            itminfo_tab.itm_name
                                            FROM
                                            itminfo_tab
                                            WHERE
                                            itminfo_tab.itm_category = 1 AND
                                            itminfo_tab.itm_status = 1 AND
                                            itminfo_tab.method_type IS NOT NULL
                                            ORDER BY
                                            itminfo_tab.method_rank ASC");
        while ($row = $query->fetch_assoc()) {
            $pk_id = $row["itm_id"];
            $product_name = $row["itm_name"];
            ?>
            <?php
            $sql_1 = "SELECT
                    mne_accuracy.pk_id,
                    mne_accuracy.method,
                    mne_accuracy.basic_id,
                    mne_accuracy.prod_id,
                    mne_accuracy.item_group,
                    mne_accuracy.bal_lmis,
                    mne_accuracy.bal_recently_reported,
                    mne_accuracy.bal_current,
                    mne_accuracy.phycial_count,
                    mne_accuracy.stock_accurate
                    FROM
                    mne_accuracy
                    WHERE
                    mne_accuracy.basic_id = '$parent_id'
                    and prod_id = '$pk_id'
                    and item_group = 'to3'";


            $query_1 = $conn->query($sql_1);

            $row_1 = $query_1->fetch_assoc();
            ?>
            <tr class="form-group">
                <td class="" name="<?php echo "form4_" . $pk_id . "_P"; ?>" id=" <?php echo "form4_" . $pk_id . "_P"; ?>" ><?php echo $product_name; ?></td>
                <td><input type="number" <?php if ($row_1['method'] == '2') {
                echo 'readonly="readonly"';
            } ?> class="mth1 form-control" name="<?php echo "form4_" . $pk_id . "_B"; ?>" id=" <?php echo "form4_" . $pk_id . "_B"; ?>" value="<?php echo $row_1['bal_lmis']; ?>"></td>
                <td><input type="number" <?php if ($row_1['method'] == '2') {
                    echo 'readonly="readonly"';
                } ?> class="mth1 form-control" name="<?php echo "form4_" . $pk_id . "_C"; ?>" id=" <?php echo "form4_" . $pk_id . "_C"; ?>" value="<?php echo $row_1['bal_recently_reported']; ?>"></td>
                <td><input type="number" <?php if ($row_1['method'] == '1') {
                    echo 'readonly="readonly"';
                } ?> class="mth2 form-control" name="<?php echo "form4_" . $pk_id . "_D"; ?>" id=" <?php echo "form4_" . $pk_id . "_D"; ?>" value="<?php echo $row_1['bal_current']; ?>"></td>
                <td><input type="number" <?php if ($row_1['method'] == '1') {
            echo 'readonly="readonly"';
        } ?> class="mth2 form-control" name="<?php echo "form4_" . $pk_id . "_E"; ?>" id=" <?php echo "form4_" . $pk_id . "_E"; ?>" value="<?php echo $row_1['phycial_count']; ?>"></td>
                <td><input type="checkbox" style="margin-left: 70px;" name="<?php echo "form4_" . $pk_id . "_F"; ?>" id=" <?php echo "form4_" . $pk_id . "_F"; ?>" <?php
        if ($row_1['stock_accurate'] == 1) {
            echo 'checked="checked"';
        }
        ?>></td>
            </tr>
    <?php
}
?>
        <tr >
            <td colspan="12">
                <p>Comments. Please also mention if there are any issues with the availability of supplementary material (swabs, syringes, safety boxes, IUCD kit, etc.) </p>
                <textarea class="form-control"  name="form4_comment" id="form4_comment"></textarea>
            </td>
        </tr>
    </table>
    <a class="btn btn-default pull-right"  onclick="form4_exe();">Submit</a><br><br><br><br><br><br>
</form>