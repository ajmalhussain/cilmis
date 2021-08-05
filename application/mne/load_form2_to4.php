<?php
include("db.php");
$parent_id=$_POST['parent_id'];
?>
<br>
<h4>FOLLOW UP RECOMMENDATIONS </h4>
<br>
<form method="POST" id="form2_to4_execution"> 
    <table border="2px" class="table table-bordered">
        <tr class="form-group">
            <th></th>
            <th>M. Key Comments from each Section</th>
            <th>N. Follow Up Action Recommended</th>
            <th>O. Resources/Support Needed</th>
            <th>P. Responsible Person</th>
            <th>Q. Expected Completed Date
            <input type="hidden"  name="parent_id"  value="<?php echo $parent_id;?>"></th>
        </tr>
            
        <?php
            $query = $conn->query("SELECT *
                                            FROM
                                            mne_followup_values
                                            ");
            while ($row = $query->fetch_assoc()) {
                $pk_id = $row["pk_id"];
                $product_name = $row["values"];
        ?>
        <tr class="form-group">
            
            <td>
                <label><?php echo $product_name ;?></label>
                <input type="hidden" class="form-control" id="<?php echo "form2_".$pk_id."_D" ;?>" name="<?php echo "form2_".$pk_id."_D" ;?>" value="<?php echo $product_name ;?>" >
                <input type="hidden" class="form-control" id="form2_item_group" name="form2_item_group" value="to4" >
            </td>
              <?php
                                        $sql_1 = "SELECT
                    mne_basic_followup.pk_id,
                    mne_basic_followup.basic_id,
                    mne_basic_followup.followup_value_id,
                    mne_basic_followup.item_group_id,
                    mne_basic_followup.description,
                    mne_basic_followup.actions,
                    mne_basic_followup.support,
                    mne_basic_followup.responsible,
                    mne_basic_followup.completion_date
                    FROM
                    mne_basic_followup
                    WHERE
                    mne_basic_followup.item_group_id = 'to4'
                    and mne_basic_followup.basic_id = '$parent_id'
                    and followup_value_id = '$product_name'";


                                        $query_1 = $conn->query($sql_1);

                                        $row_1 = $query_1->fetch_assoc();
                                        ?>
            <td> 

                <textarea class="form-control" id="<?php echo "form2_".$pk_id."_M" ;?>" name="<?php echo "form2_".$pk_id."_M" ;?>"><?php echo $row_1['description'];?></textarea>
            </td>
            <td> 
                <textarea class="form-control" id="<?php echo "form2_".$pk_id."_N" ;?>" name="<?php echo "form2_".$pk_id."_N" ;?>"><?php echo $row_1['actions'];?></textarea>
            </td>
            <td> 
                <textarea class="form-control" id="<?php echo "form2_".$pk_id."_O" ;?>" name="<?php echo "form2_".$pk_id."_O" ;?>"><?php echo $row_1['support'];?></textarea>
            </td>
            <td> 
                <textarea class="form-control" id="<?php echo "form2_".$pk_id."_P" ;?>" name="<?php echo "form2_".$pk_id."_P" ;?>"><?php echo $row_1['responsible'];?></textarea>
            </td>
            <td> 
                <input type="date" class="form-control" id="<?php echo "form2_".$pk_id."_Q" ;?>" name="<?php echo "form2_".$pk_id."_Q" ;?>" value="<?php echo $row_1['completion_date'];?>">
            </td>
            
            

        </tr>
        <?php 
        }
        ?>
    </table>
    <a class="btn btn-default pull-right" name="submit2" onclick="form2_to4_exe();">Submit</a><br><br><br><br><br><br>
    <div id="form2exeresp"></div>
</form>