<?php
include("db.php");
$parent_id = $_POST['parent_id'];
?>

<h4>DATA AVAILABILITY (COMPLETENESS)</h4>
<h4>
    FAMILY PLANNING AND REPRODUCTIVE HEALTH 
</h4>
<br>
<form method="POST" id="form_3_1_exe">

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
                   
                    and item_group = 't03' AND offered = 1";


                $query_1 = $conn->query($sql_1);

              
                $sel_array1 = array();
                 while ($row_1 = $query_1->fetch_assoc()) {
                   $sel_array[] =  $row_1['prod_id'];
                
                }
              
             
                ?>
              
                <select style="width:250px;" class="form-control"  name="sel_pr[]" id="sel_pr"  multiple>
                    <?php
                    $query_P = $conn->query("SELECT
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
                    while ($rowP = $query_P->fetch_assoc()) {
                        $p_id = $rowP["itm_id"];
                        $p_name = $rowP["itm_name"];
                        
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
                <a class="btn btn-default pull-right" onclick="form3_1();" >Submit</a>
            </td>
        </tr>
    </table>
</form>
<div id="load">
    </div>

<?php
if ((count($row_1)) > 0)
    echo '123';
{
    echo "<script> form3_1();
    </script>";
}
?>
