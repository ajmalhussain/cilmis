
<?php
include("db.php");
?>
<?php
//echo '<pre>';print_r($_REQUEST);exit;
$form5_comment= $_POST['form5_comment'];
$parent_id=$_POST['parent_id'];
$item_group = 't04';       
        
$sqliu="SELECT *
            FROM
                    mne_availability
            WHERE
                    basic_id = '$parent_id' AND item_group = '$item_group'";
    $query = $conn->query($sqliu);
    $row_cnt = $query->num_rows;
    
$sql = "SELECT * from mne_to4";
$query = $conn->query($sql);
//echo '<pre>';print_r($_REQUEST);exit;
while ($row = $query->fetch_assoc()) {
  
     $pk_id = $row["pk_id"];
     $product_name = $row["product_to4"];
     $f5p =  $pk_id;
    $f5a= "form5_".$pk_id."_A";
    if(!empty($_POST[$f5a]) && $_POST[$f5a]=='on')
    {
        $_POST[$f5a]= 1;
    }
    else
    {
        $_POST[$f5a] = 0;
    }
    $f5b= "form5_".$pk_id."_B";
    $f5c= "form5_".$pk_id."_C";
    if(!empty($_POST[$f5c]) && $_POST[$f5c]=='on')
    {
        $_POST[$f5c]= 1;
    }
    else
    {
        $_POST[$f5c] = 0;
    }
    $f5d= "form5_".$pk_id."_D";
    $f5e= "form5_".$pk_id."_E";
    $f5f= "form5_".$pk_id."_F";
    $f5g= "form5_".$pk_id."_G";
     $f5h= "form5_".$pk_id."_H";
    $f5i= "form5_".$pk_id."_I";
   $f5j= "form5_".$pk_id."_J";
    $f5k= "form5_".$pk_id."_K";
    
   
    $a = $_POST[$f5a];
    $barray= $_POST[$f5b];
    if(isset($barray))
        $b =implode(",", $barray) ;
    else
        $b='';
    
    $c = $_POST[$f5c];
    $d = $_POST[$f5d];
    $e = $_POST[$f5e];
    $f = $_POST[$f5f];
    $g = $_POST[$f5g];
    $h = $_POST[$f5h];
    $i = $_POST[$f5i];
     $j = $_POST[$f5j];
   $k = $_POST[$f5k];
  
    
    if($row_cnt>0)
    {
        $sqlu = "UPDATE mne_availability SET  offered='$a',stock_tools='$b',tools_available='$c',s_o_h='$d',open_balance='$e',close_balance='$f',receive='$g',issue='$h',a_m_c='$i',min='$j',max='$k' WHERE prod_id='$f5p' AND item_group='$item_group' AND  basic_id='$parent_id'";
        $queryu = $conn->query($sqlu);     
    }
    else {
        $sql2 = "INSERT INTO mne_availability(basic_id,prod_id,item_group,offered,stock_tools,tools_available,s_o_h,open_balance,close_balance,receive,issue,a_m_c,min,max)
              VALUES ('$parent_id','$f5p','$item_group','$a','$b','$c','$d','$e','$f','$g','$h','$i','$j','$k')";
         $query2 = $conn->query($sql2);
    }
    
  
}

$sql3 = "UPDATE mne_basic_parent SET availability_to4_comments='$form5_comment'
WHERE
	pk_id = '$parent_id'";
$query3 = $conn->query($sql3);
