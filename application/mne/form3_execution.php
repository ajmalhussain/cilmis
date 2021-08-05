
<?php

include("db.php");
?>
<?php

$parent_id = $_POST['parent_id'];
$item_group = 't03';
  $sqliu = "SELECT *
            FROM
                    mne_availability
            WHERE
                    basic_id = '$parent_id' AND  item_group = '$item_group'";
 
$query = $conn->query($sqliu);
$row_cnt = $query->num_rows;

$form3_comment = $_POST['form3_comment'];

$sql = "SELECT
                                            itminfo_tab.itm_id,
                                            itminfo_tab.itm_name
                                            FROM
                                            itminfo_tab
                                            WHERE
                                            itminfo_tab.itm_category = 1 AND
                                            itminfo_tab.itm_status = 1 AND
                                            itminfo_tab.method_type IS NOT NULL
                                            ORDER BY
                                            itminfo_tab.method_rank ASC";
$query = $conn->query($sql);

while ($row = $query->fetch_assoc()) {

    $pk_id = $row["itm_id"];
    $product_name = $row["itm_name"];
    $f3p = $pk_id;
    $f3a = "form3_" . $pk_id . "_A";

    
    if (!empty($_POST[$f3a]) && $_POST[$f3a] == 'on') {
        $_POST[$f3a] = 1;
    } else {
        $_POST[$f3a] = 0;
    }
    
    $f3b = "form3_" . $pk_id . "_B";
    $f3c = "form3_" . $pk_id . "_C";
    if (!empty($_POST[$f3c]) && $_POST[$f3c] == 'on') {
        $_POST[$f3c] = 1;
    } else {
        $_POST[$f3c] = 0;
    }
    
    $f3d = "form3_" . $pk_id . "_D";
    $f3e = "form3_" . $pk_id . "_E";
    $f3f = "form3_" . $pk_id . "_F";
    $f3g = "form3_" . $pk_id . "_G";
    $f3h = "form3_" . $pk_id . "_H";
    $f3i = "form3_" . $pk_id . "_I";
    $f3j = "form3_" . $pk_id . "_J";
    $f3k = "form3_" . $pk_id . "_K";


    $a = $_POST[$f3a];
   
    $barray = $_POST[$f3b];
    if (isset($barray))
        $b = implode(",", $barray);
    else
        $b = '';

    $c = $_POST[$f3c];
    $d = $_POST[$f3d];
    $e = $_POST[$f3e];
    $f = $_POST[$f3f];
    $g = $_POST[$f3g];
    $h = $_POST[$f3h];
    $i = $_POST[$f3i];
    $j = $_POST[$f3j];
    $k = $_POST[$f3k];


    if ($row_cnt > 0) {
        $sqlu = "UPDATE mne_availability SET offered='$a',stock_tools='$b',tools_available='$c',s_o_h='$d',open_balance='$e',close_balance='$f',receive='$g',issue='$h',a_m_c='$i',min='$j',max='$k' WHERE prod_id='$f3p' AND item_group='$item_group' AND basic_id='$parent_id'";
        $queryu = $conn->query($sqlu);
        // echo $sqlu;
    } else {
         $sql2 = "INSERT INTO mne_availability(basic_id,prod_id,item_group,offered,stock_tools,tools_available,s_o_h,open_balance,close_balance,receive,issue,a_m_c,min,max)
              VALUES ('$parent_id','$f3p','$item_group','$a','$b','$c','$d','$e','$f','$g','$h','$i','$j','$k')";
       
         $query2 = $conn->query($sql2);
        // echo $sql2;
    }
}
//echo '<pre>';print_r($_REQUEST);exit;
$sql3 = "UPDATE mne_basic_parent SET availability_to3_comments='$form3_comment'
WHERE
	pk_id = '$parent_id'";
$query3 = $conn->query($sql3);
