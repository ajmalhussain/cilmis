<?php
/**
 * Manage Product Category
 * @package Admin
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
//Including required files
include("../includes/classes/AllClasses.php");
include(PUBLIC_PATH . "html/header.php");
$ItemcategoryName='';
//Initializing Variables
$act = 2;
$strDo = "Add";
$cat_id = 0;

//Getting Do
if (isset($_REQUEST['Do']) && !empty($_REQUEST['Do'])) {
    $strDo = $_REQUEST['Do'];
}
//Getting Id
if (isset($_REQUEST['Id']) && !empty($_REQUEST['Id'])) {
    $cat_id = $_REQUEST['Id'];
}

//Delete Item Category 
if ($strDo == "Delete") {
    $objitemcategory->m_npkId = $cat_id;
    $rsDelCat = $objitemcategory->DeleteItemCategory();

    //Setting messages
    $_SESSION['err']['text'] = 'Data has been successfully deleted.';
    $_SESSION['err']['type'] = 'success';
    //Redirecting to ManageProductCategory
    echo '<script>window.location="ManageProductCategory.php"</script>';
    exit;
}
if (isset($_SESSION['pk_id'])) {
    unset($_SESSION['pk_id']);
}

/**
 * Edit Item Category
 */
if ($strDo == "Edit") {
    $objitemcategory->m_npkId = $cat_id;
    //Get Item Category By Id
    $rsEditstk = $objitemcategory->GetItemCategoryById();
    if ($rsEditstk != FALSE && mysql_num_rows($rsEditstk) > 0) {
        $RowEditStk = mysql_fetch_object($rsEditstk);
        $ItemcategoryName = $RowEditStk->ItemCategoryName;
    }
}
//Ge tAll Item Category
$ItmCategory = $objitemcategory->GetAllItemCategory();
//Including required file


$qry = "select 
            tbl_product_category.PKItemCategoryID as id,
            tbl_product_category.ItemCategoryName as cat,
            tbl_product_category.parent_id 
        from 
            tbl_product_category 
        where 
            tbl_product_category.PKItemCategoryID = 4 OR tbl_product_category.PKItemCategoryID > 44
        ORDER BY 
        ItemCategoryName
";
$res = mysql_query($qry);
$cats  = $sub_cats = array();

//Generating xml for grid
$xmlstore = "";
$xmlstore .='<table class="table table-bordered table-condensed table-striped">';
$counter = 1;
$all_cats = array();
$is_parent = false;
while ($row = mysql_fetch_assoc($res)) {
    
    if($row['parent_id'] == NULL || $row['parent_id']==''){
        $cats[$row['id']]=$row['cat'];
    }
    else{
        $sub_cats[$row['id']]['name']=$row['cat'];
        $sub_cats[$row['id']]['parent']=$row['parent_id'];
        
        if($row['parent_id'] == $cat_id){
            $is_parent = true;
        }
    }
        $all_cats[$row['id']]['name']=$row['cat'];
        $all_cats[$row['id']]['parent']=$row['parent_id'];
}
$xmlstore .="<tr>";
$xmlstore .="<td>#</td>";
$xmlstore .="<td>Category Name</td>";
$xmlstore .="<td>Parent Category (If any)</td>";
$xmlstore .='<td>Action</td>';
$xmlstore .="</tr>";
    
foreach($all_cats as $cid => $cdata){
    $xmlstore .="<tr>";
    $xmlstore .="<td>" . $counter++ . "</td>";
    $xmlstore .="<td >".$cdata['name']."</td>";
    $xmlstore .="<td class=\"".(empty($cats[$cdata['parent']])?'bg-yellow':'')."\">".(!empty($cats[$cdata['parent']])?$cats[$cdata['parent']]:'')."</td>";
    $xmlstore .='<td><a onclick="javascript:editFunction('.$cid.')">Edit</a></td>';
    $xmlstore .="</tr>";
    
}
$xmlstore .="</table>";
$this_sub_cat = $cat_id;
@$this_main_cat = $sub_cats[$cat_id]['parent'];

//echo '<pre>';
//print_r($cats);
//print_r($sub_cats);
//echo '</pre>';
//exit;

//echo $this_sub_cat.','.$this_main_cat;exit;
?>
</head>

<!-- BEGIN body -->
<body class="page-header-fixed page-quick-sidebar-over-content"  >
    <!-- BEGIN HEADER -->
    <div class="page-container">
        <?php
        //Including required files
        include $_SESSION['menu'];
        include PUBLIC_PATH . "html/top_im.php";
        ?>
        <div class="page-content-wrapper">
            <div class="page-content">
                <div class="row">
                    <div class="col-md-12">
                        <h3 class="page-title row-br-b-wp">Product Category Management</h3>
                        <div class="widget" data-toggle="collapse-widget">
                            <div class="widget-head ">
                                <h3 class="heading"><?php echo $strDo; ?> Product Category</h3>
                            </div>
                            <div class="widget-body">
                                <form method="post" action="ManageProductCategoryAction.php" name="" id="">
                                    <div class="row">
                                        <div class="col-md-12">

                                            <div class="col-md-4">
                                                <div class="control-group">
                                                    <label>Category Name<font color="#FF0000">*</font></label>
                                                    <div class="controls">
                                                        <input required autocomplete="off" type="text" name="productcategory" value="<?= $ItemcategoryName ?>" class="form-control input-medium">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="control-group">
                                                    <label>Parent Category<font color="#FF0000">*</font></label>
                                                    <div class="controls">
                                                        <select name="main_cat" id="main_cat" class="form-control input-medium" required>
                                                            <option value="">Select</option>
                                                            <option value="parent" <?=((empty($this_main_cat) || $this_main_cat=='')?' selected ':'')?> style="background-color: greenyellow">None (This is Parent)</option>
                                                            <?php
                                                            if(!$is_parent){
                                                                foreach($cats as $idd=> $name){

                                                                    $sel = '';
                                                                    if($this_main_cat == $idd)$sel = ' selected ';
                                                                    echo ' <option value="'.$idd.'" '.$sel.'>'.$name.'</option>   ';
                                                                }
                                                            
                                                            }
                                                            ?>
                                                           
                                                        </select>
                                                 
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="control-group">
                                                    <div class="control-group">
                                                        <label>&nbsp;</label>
                                                        <div class="controls">
                                                            <input type="hidden" name="hdnstkId" value="<?= $cat_id ?>" />
                                                            <input  type="hidden" name="hdnToDo" value="<?= $strDo ?>" />
                                                            <input type="submit" class="btn btn-primary" value="<?= $strDo ?>" />
                                                            <input name="btnAdd" class="btn btn-info" type="button" id="btnCancel" value="Cancel" OnClick="window.location = '<?= $_SERVER["PHP_SELF"]; ?>';">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                    <br/>
                    <br/>
                    <div class="row">
                        <div class="col-md-12">
                            <span class="note note-info">Note: Our category structure is based on Main and a Sub Category .Main Categories don't have a parent. For creating a main category , choose 'NONE' in the parent dropdown.</span>
                        </div>
                    </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="widget">
                            <div class="widget-head">
                                <h3 class="heading">All Product Categories</h3>
                            </div>
                            <div class="widget-body">
                                <div id="mygrid_container" style="width:100%;">
                                                
                                                <?php echo $xmlstore; ?>
                                            </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
    //Including required files
    include PUBLIC_PATH . "/html/footer.php";
    include PUBLIC_PATH . "/html/reports_includes.php";
    ?>
    <script>
        //Edit Manage Product Category
        function editFunction(val) {
            window.location = "ManageProductCategory.php?Do=Edit&Id=" + val;
        }
        //Delete Manage Product Category
        function delFunction(val) {
            if (confirm("Are you sure you want to delete the record?")) {
                window.location = "ManageProductCategory.php?Do=Delete&Id=" + val;
            }
        }
        var mygrid;
    </script>
    <?php
    if (isset($_SESSION['err'])) {
        ?>
        <script>
            var self = $('[data-toggle="notyfy"]');
            notyfy({
                force: true,
                text: '<?php echo $_SESSION['err']['text']; ?>',
                type: '<?php echo $_SESSION['err']['type']; ?>',
                layout: self.data('layout')
            });
        </script>
        <?php
        //Unset the session
        unset($_SESSION['err']);
    }
    ?>
</body>
<!-- END body -->
</html>