<?php
include("../includes/classes/AllClasses.php");
//@session_start();
//echo $_SESSION['user_province1'];exit;
if($_SESSION['user_province1']==1 )
{
    include "data_entry_hf_pwd_new_format.php";
}
elseif($_SESSION['user_province1']==2 )
{
    include "data_entry_hf_pwd_new_format_2.php";
}
else
{
    include "data_entry_hf_pwd_standard_format.php";
}

?>