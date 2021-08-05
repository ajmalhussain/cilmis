<?php
include("db.php");
$parent_id = $_POST['parent_id'];
?>
<br>
<form  method="POST" id="form_6_exe">
    <h4>
        DATA ACCURACY
    </h4>
    <h4>
         MATERNAL, NEWBORN AND CHILD HEALTH 
    </h4>
    <br>
    <table border="1px" width="100%" class="table table-bordered">
        <tr>
            <th colspan="4">
                A.  Method of data accuracy assessment  
            </th>
        </tr>
        <tr>
            <th colspan="4" style="text-align: left;">

                <p> Current stock on hand data (per stock register/bin card/stock card) compared to physical count </p>
            </th>
        </tr>
        <tr>
            <th>Tracer Product </th>

            <th>D. Stock register/bin/stock card ending balance</th>

            <th>E. Physical stock count (if applicable) </th>
            <th>F. Is stock level data accurate? (1=Yes, 0=No) <input type="hidden" name="parent_id"  value="<?php echo $parent_id; ?>"></th>
        </tr>

        <?php
        $query = $conn->query("SELECT * from mne_to4");
        while ($row = $query->fetch_assoc()) {
            $pk_id = $row["pk_id"];
            $product_name = $row["product_to4"];
            ?>
            <?php
            $sql_1 = "SELECT
                    mne_accuracy.pk_id,
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
                    and item_group = 'to4'";


            $query_1 = $conn->query($sql_1);

            $row_1 = $query_1->fetch_assoc();
            ?>
            <tr class="form-group">
                <td class="" name="<?php echo "form6_" . $pk_id . "_P"; ?>" id=" <?php echo "form6_" . $pk_id . "_P"; ?>" ><?php echo $product_name; ?></td>

                <td><input type="number" class="form-control" name="<?php echo "form6_" . $pk_id . "_D"; ?>" id=" <?php echo "form6_" . $pk_id . "_D"; ?>" value="<?php echo $row_1['bal_current'];?>"></td>
                <td><input type="number" class="form-control" name="<?php echo "form6_" . $pk_id . "_E"; ?>" id=" <?php echo "form6_" . $pk_id . "_E"; ?>" value="<?php echo $row_1['phycial_count'];?>"></td>
                <td><input type="checkbox" style="margin-left: 150px;" name="<?php echo "form6_" . $pk_id . "_F"; ?>" id=" <?php echo "form6_" . $pk_id . "_F"; ?>"  <?php if ($row_1['stock_accurate']==1){echo 'checked="checked"';} ?>></td>
            </tr>
            <?php
        }
        ?>
        <tr >
            <td colspan="12">
                <p>Comments. Please also mention if there are any issues with the availability of supplementary material (swabs, syringes, safety boxes, IUCD kit, etc.) </p>
                <textarea class="form-control"  name="form6_comment" id="form6_comment"></textarea>
            </td>
        </tr>
    </table>
    <a class="btn btn-default pull-right"  onclick="form6_exe();">Submit</a><br><br><br><br><br><br>
</form>