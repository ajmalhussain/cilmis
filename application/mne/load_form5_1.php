<?php
include("db.php");
$parent_id = $_POST['parent_id'];
 $sql_1 = "SELECT
                    
                    mne_availability.prod_id
                    
                    FROM
                    mne_availability
                    WHERE
                    mne_availability.basic_id = '$parent_id'
                   
                    and item_group = 't04' AND offered = 1";


                $query_1 = $conn->query($sql_1);

              
                $sel_array1 = array();
                 while ($row_1 = $query_1->fetch_assoc()) {
                   $sel_array[] =  $row_1['prod_id'];
                
                }
                $row_cnt = $query_1->num_rows;
                if ($row_cnt > 0){
                $barray = $sel_array;     
                }else {
 $barray = $_POST['sel_pr'];
                }

    if (isset($barray)) {
        $b = implode(",", $barray);
    $where = "where pk_id IN ($b)";}
    else{
        $where = '1<>1';
        $b = '';
    }
    
    
?>







<form method="POST" id="form_5_exe">
    <input type="hidden" name="parent_id"  value="<?php echo $parent_id; ?>">
    <table class="table table-bordered">

        <tr class="form-group">
            <th rowspan="2">Trace Product</th>
            <th rowspan="2">A. Is this product offered at this facility?</th>
            <th rowspan="2" style="width:220px !important; padding:0px !important;">B. What is/are the required stock monitoring tool(s) for this product?</th>
            <th rowspan="2" >C. Are all the required monitoring tool(s) avaliable?</th>
            <th colspan="8"><p>For items D-K, enter 1 if the data for the tracer product has been entered on the monitoring tool correctly within the last month. Enter 0 if data element is missing, blank, or entered incorrectly. Adjust the data elements as needed based on the countryâ€™s stock report or ordering tool. </p></th>
        </tr>
        <tr class="form-group">
            <th>D. Stock on hand </th>
            <th>E. Opening Balance </th>
            <th>F. Closing Balance </th>
            <th>G. Quantity Received </th>
            <th>H. Quantity Issued </th>
            <th>I. Average Monthly Consumption </th>
            <th>J. Minimum (district only) </th>
            <th>K. Maximum (district only) </th>
        </tr>


        <?php
        $query = $conn->query("SELECT * from mne_to4  $where");
        
        while ($row = $query->fetch_assoc()) {
            
            $pk_id = $row["pk_id"];
            $product_name = $row["product_to4"];
            ?>
            <tr class="form-group">
                  <?php
                $sql_1 = "SELECT
                    mne_availability.pk_id,
                    mne_availability.basic_id,
                    mne_availability.prod_id,
                    mne_availability.item_group,
                    mne_availability.offered,
                    mne_availability.stock_tools,
                    mne_availability.tools_available,
                    mne_availability.s_o_h,
                    mne_availability.open_balance,
                    mne_availability.close_balance,
                    mne_availability.receive,
                    mne_availability.issue,
                    mne_availability.a_m_c,
                    mne_availability.min,
                    mne_availability.max
                    FROM
                    mne_availability
                    WHERE
                    mne_availability.basic_id = '$parent_id'
                    and prod_id = '$pk_id'
                    and item_group = 't04'";


                $query_1 = $conn->query($sql_1);

                $row_1 = $query_1->fetch_assoc();
                ?>
                <td class="" name="<?php echo "form5_" . $pk_id . "_P"; ?>" id=" <?php echo "form5_" . $pk_id . "_P"; ?>" ><?php echo $product_name; ?></td>
                <td><input type="checkbox" style="margin-left: 30px;"  name="<?php echo "form5_" . $pk_id . "_A"; ?>" id=" <?php echo "form5_" . $pk_id . "_A"; ?>" <?php
                    if ($row_1['offered'] == 1) {
                        echo 'checked="checked"';
                    }
                    ?>></td>
                <td > 
<?php
                    $string = $row_1['stock_tools'];
                    $sel_array = explode(',', $string);
                   
                    ?>
                    <select  class="form-control"  name="<?php echo "form5_" . $pk_id . "_B[]"; ?>" id=" <?php echo "form5_" . $pk_id . "_B"; ?>"  multiple>
                        <?php
                        $query12 = $conn->query("SELECT *                                            
                                            FROM
                                            mne_monitoring_tools");
                        while ($row1 = $query12->fetch_assoc()) {
                            $pk_id1 = $row1["pk_id"];
                            $product_name = $row1["monitoring_tool"];
                              if (in_array($pk_id1, $sel_array)) {
                               
                                    $sel_var = 'selected="selected"';
                                                                
                                
                            }else {
                                   $sel_var = '';
                            }
                            ?> 
                            <option value="<?php echo $pk_id1; ?>" <?php echo $sel_var; ?>><?php echo $product_name; ?></option>                  
                            <?php
                        }
                        ?>
                    </select>

                    <p style="color: red;">* Ctrl</p>
                </td>
                <td><input type="checkbox" style="margin-left: 40px;" name="<?php echo "form5_" . $pk_id . "_C"; ?>" id=" <?php echo "form5_" . $pk_id . "_C"; ?>" <?php
                    if ($row_1['tools_available'] == 1) {
                        echo 'checked="checked"';
                    }
                        ?>></td>
                <td>
                    <select class="form-control" style="padding:0px !important" name="<?php echo "form5_" . $pk_id . "_D"; ?>" id=" <?php echo "form5_" . $pk_id . "_D"; ?>">
                        <option value="na" <?php
                if ($row_1['s_o_h'] == 'na') {
                    echo 'selected="selected"';
                }
                        ?>>NA</option> 
                        <option value="yes" <?php
                    if ($row_1['s_o_h'] == 'yes') {
                        echo 'selected="selected"';
                    }
                        ?>>Yes</option>
                        <option value="no" <?php
                    if ($row_1['s_o_h'] == 'no') {
                        echo 'selected="selected"';
                    }
                        ?>>No</option>

                    </select>
                </td>
                <td>
                    <select class="form-control" style="padding:0px !important" name="<?php echo "form5_" . $pk_id . "_E"; ?>" id=" <?php echo "form5_" . $pk_id . "_E"; ?>">
                        <option value="na" <?php
                    if ($row_1['open_balance'] == 'na') {
                        echo 'selected="selected"';
                    }
                        ?>>NA</option> 
                        <option value="yes" <?php
                    if ($row_1['open_balance'] == 'yes') {
                        echo 'selected="selected"';
                    }
                        ?>>Yes</option>
                        <option value="no" <?php
                    if ($row_1['open_balance'] == 'no') {
                        echo 'selected="selected"';
                    }
                        ?>>No</option>
                                                  
                    </select>
                </td>
                <td><select class="form-control" style="padding:0px !important" name="<?php echo "form5_" . $pk_id . "_F"; ?>" id=" <?php echo "form5_" . $pk_id . "_F"; ?>">
                        <option value="na" <?php
                    if ($row_1['close_balance'] == 'na') {
                        echo 'selected="selected"';
                    }
                        ?>>NA</option> 
                        <option value="yes" <?php
                    if ($row_1['close_balance'] == 'yes') {
                        echo 'selected="selected"';
                    }
                        ?>>Yes</option>
                        <option value="no" <?php
                    if ($row_1['close_balance'] == 'no') {
                        echo 'selected="selected"';
                    }
                        ?>>No</option>

                    </select>
                </td>
                <td>
                    <select class="form-control" style="padding:0px !important" name="<?php echo "form5_" . $pk_id . "_G"; ?>" id=" <?php echo "form5_" . $pk_id . "_G"; ?>">
                          <option value="na" <?php
                    if ($row_1['receive'] == 'na') {
                        echo 'selected="selected"';
                    }
                        ?>>NA</option> 
                        <option value="yes" <?php
                    if ($row_1['receive'] == 'yes') {
                        echo 'selected="selected"';
                    }
                        ?>>Yes</option>
                        <option value="no" <?php
                    if ($row_1['receive'] == 'no') {
                        echo 'selected="selected"';
                    }
                        ?>>No</option>

                    </select>
                </td>
                <td>
                    <select class="form-control" style="padding:0px !important" name="<?php echo "form5_" . $pk_id . "_H"; ?>" id=" <?php echo "form5_" . $pk_id . "_H"; ?>"> 
                       <option value="na" <?php
                    if ($row_1['issue'] == 'na') {
                        echo 'selected="selected"';
                    }
                        ?>>NA</option> 
                        <option value="yes" <?php
                    if ($row_1['issue'] == 'yes') {
                        echo 'selected="selected"';
                    }
                        ?>>Yes</option>
                        <option value="no" <?php
                    if ($row_1['issue'] == 'no') {
                        echo 'selected="selected"';
                    }
                        ?>>No</option>

                    </select>
                </td>
                <td>
                    <select class="form-control" style="padding:0px !important" name="<?php echo "form5_" . $pk_id . "_I"; ?>" id=" <?php echo "form5_" . $pk_id . "I"; ?>">
                       <option value="na" <?php
                    if ($row_1['a_m_c'] == 'na') {
                        echo 'selected="selected"';
                    }
                        ?>>NA</option> 
                        <option value="yes" <?php
                    if ($row_1['a_m_c'] == 'yes') {
                        echo 'selected="selected"';
                    }
                        ?>>Yes</option>
                        <option value="no" <?php
                    if ($row_1['a_m_c'] == 'no') {
                        echo 'selected="selected"';
                    }
                        ?>>No</option>

                    </select>
                </td>
                <td>
                    <select class="form-control" style="padding:0px !important" name="<?php echo "form5_" . $pk_id . "_J"; ?>" id=" <?php echo "form5_" . $pk_id . "_J"; ?>">
                        <option value="na" <?php
                    if ($row_1['min'] == 'na') {
                        echo 'selected="selected"';
                    }
                        ?>>NA</option> 
                        <option value="yes" <?php
                    if ($row_1['min'] == 'yes') {
                        echo 'selected="selected"';
                    }
                        ?>>Yes</option>
                        <option value="no" <?php
                    if ($row_1['min'] == 'no') {
                        echo 'selected="selected"';
                    }
                        ?>>No</option>

                    </select>
                </td>
                <td>
                    <select class="form-control" style="padding:0px !important" name="<?php echo "form5_" . $pk_id . "_K"; ?>" id=" <?php echo "form5_" . $pk_id . "_K"; ?>">
                         <option value="na" <?php
                    if ($row_1['max'] == 'na') {
                        echo 'selected="selected"';
                    }
                        ?>>NA</option> 
                        <option value="yes" <?php
                    if ($row_1['max'] == 'yes') {
                        echo 'selected="selected"';
                    }
                        ?>>Yes</option>
                        <option value="no" <?php
                    if ($row_1['max'] == 'no') {
                        echo 'selected="selected"';
                    }
                        ?>>No</option>


                    </select>
                </td>

            </tr>
            <?php
        }
        ?>
        <tr >
            <td colspan="12">
                <p>Comments. Please also mention if there are any issues with the availability of supplementary material (swabs, syringes, safety boxes, IUCD kit, etc.) </p>
                <textarea class="form-control"  name="form5_comment" id="form3_comment"></textarea>
            </td>
        </tr>
    </table>
    <a class="btn btn-default pull-right" onclick="form5_exe();" >Submit</a><br><br><br><br><br><br>
</form>
