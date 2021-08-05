<?php
include("../includes/classes/AllClasses.php");

$qry_summary_dist= "
    SELECT
itminfo_tab.itmrec_id,
itminfo_tab.itm_id,
itminfo_tab.itm_name,
itminfo_tab.generic_name,
itminfo_tab.method_type,
itminfo_tab.method_rank,
itminfo_tab.itm_type,
itminfo_tab.itm_category,
itminfo_tab.mnch_id,
itminfo_tab.lhw_kp_id,
itminfo_tab.lhw_punjab_id,
itminfo_tab.mnch_kp_id,
itminfo_tab.dhis_stock_field
FROM
itminfo_tab


";
//Query result
//echo $qry_summary_dist;
$Res2 =mysql_query($qry_summary_dist);
$display_data  = $columns_data = array();

while($row = mysql_fetch_assoc($Res2)){
   $display_data[] = $row;
   $row2=$row;
   //echo '<pre>';print_r($row);
}

foreach($row2 as $k=>$v)
{
   $columns_data[] = $k;
}
//echo '<pre>';print_r($columns_data);print_r($display_data);
?>
<head><style>
* {
  box-sizing: border-box;
}

#myInput {
  background-image: url('/css/searchicon.png');
  background-position: 10px 10px;
  background-repeat: no-repeat;
  width: 80%;
  font-size: 16px;
  padding: 12px 20px 12px 40px;
  border: 1px solid #ddd;
  margin-bottom: 12px;
}

#myTable {
  border-collapse: collapse;
  width: 80%;
  border: 1px solid #ddd;
  font-size: 14px;
}

#myTable th, #myTable td {
  text-align: left;
  padding: 5px;
}

#myTable tr {
  border-bottom: 1px solid #ddd;
}

#myTable tr.header, #myTable tr:hover {
  background-color: #f1f1f1;
}
</style></head>
<input type="text" id="myInput" onkeyup="myFunction()" placeholder="Search here..." title="Type in a name">
<table border="1" id="myTable"  class="table table-condensed table-striped left" >
    <tr bgcolor="#afb5ea">
        <?php
        echo '<th>#</th>';
        foreach($columns_data as $k=>$v)
        {
           echo '<th>'.$v.'</th>';
        }
        ?>
    </tr>
    
    <?php
    $count_of_row = 0;
        foreach($display_data as $k => $disp)
        {
           echo '<tr>';
           echo '<td>'.++$count_of_row.'</td>';
           foreach($columns_data as $k2=>$col)
           {
            echo ' <td>'.$disp[$col].'</td>';
           }   
           echo '<tr>';
        }
        ?>
</table>
    </body>
    
<script src="<?php echo PUBLIC_URL;?>js/jquery-1.4.4.js" type="text/javascript"></script>
<script src="<?php echo PUBLIC_URL;?>js/custom_table_sort.js" type="text/javascript"></script>
<script>
function myFunction() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("myInput");
  filter = input.value.toUpperCase();
  table = document.getElementById("myTable");
  tr = table.getElementsByTagName("tr");
  for (i = 0; i < tr.length; i++) {
    td = tr[i].getElementsByTagName("td")[1];
    //td += tr[i].getElementsByTagName("td")[5];
    
    
    if (td) {
      txtValue = td.textContent || td.innerText;
    
        td = tr[i].getElementsByTagName("td")[2];
        txtValue += td.textContent || td.innerText;
        td = tr[i].getElementsByTagName("td")[3];
        txtValue += td.textContent || td.innerText;
        td = tr[i].getElementsByTagName("td")[4];
        txtValue += td.textContent || td.innerText;
        td = tr[i].getElementsByTagName("td")[5];
        txtValue += td.textContent || td.innerText;
      //console.log(txtValue);
      if (txtValue.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
      } else {
        tr[i].style.display = "none";
      }
    }       
  }
}
</script>
</html>
