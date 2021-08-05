<?php
include("db.php");
$parent_id = $_POST['parent_id'];
?>

<h4>DATA AVAILABILITY (COMPLETENESS)</h4>
<h4>
    FAMILY PLANNING AND REPRODUCTIVE HEALTH 
</h4>
<br>
<form method="POST" id="form_5_1_exe">

    <table class="table table-bordered">

        <tr>
            <td > 
                <input type="hidden"  name="parent_id"  value="<?php echo $parent_id; ?>">
                <b>  Select Products to generate Form: </b>
                <?php
                $sql_1 = "SELECT
                    
                    mne_availability.prod_id
                    
                    FROM
                    mne_availability
                    WHERE
                    mne_availability.basic_id = '$parent_id'
                   
                    and item_group = 't04' AND offered = 1";

                 $query_1 = $conn->query($sql_1);

              
                $sel_array1 = array();
                 while ($row_2 = $query_1->fetch_assoc()) {
                   $sel_array[] =  $row_2['prod_id'];
                
                }
                ?>

                <select style="width:250px;" class="form-control"  name="sel_pr[]" id="sel_pr"  multiple>
                    <?php
                    $query_P = $conn->query("SELECT * from mne_to4");
                    while ($rowP = $query_P->fetch_assoc()) {
                        $p_id = $rowP["pk_id"];
                        $p_name = $rowP["product_to4"];

                        if (in_array($p_id, $sel_array)) {

                            $sel_var = 'selected="selected"';
                        } else {
                            $sel_var = '';
                        }
                        ?> 
                        <option value="<?php echo $p_id; ?>" <?php echo $sel_var; ?>><?php echo $p_name; ?></option>                  
                        <?php
                    }
                    ?>
                </select>

                <p style="color: red;">* Ctrl </p>
                <a class="btn btn-default pull-right" onclick="form5_1();" >Submit</a>
            </td>
        </tr>
    </table>
</form>
<div id="load1">
</div>

<?php
if ((count($row_2)) > 0) 
    echo '123';
    {
    
    echo "<script> form5_1();
    </script>";
}
?>
