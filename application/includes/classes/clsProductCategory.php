<?php

/**
 * clsProductCategory
 * @package includes/class
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
//class clsItemCategory
class clsItemCategory {

    //npkId
    var $m_npkId;
    //PKItemCategoryID
    var $m_PKItemCategoryID;
    //ItemCategoryName
    var $m_ItemCategoryName;

    /**
     * AddItemCategory
     * @return int
     */
    function AddItemCategory() {
        if ($this->m_ItemCategoryName == '') {
            $this->m_ItemCategoryName = 'NULL';
        }
        
        
        if (!empty($this->main_cat) && $this->main_cat != '') {
            if($this->main_cat == 'parent'){
                $parent = "NULL ";
            }else{
                $parent = "'".$this->main_cat."'";
            }
        }
        //add query
        $strSql = "INSERT INTO  tbl_product_category(ItemCategoryName,parent_id) VALUES('" . $this->m_ItemCategoryName . "',$parent)";
        //query result
        $rsSql = mysql_query($strSql) or die("Error AddItemCategory");
        if (mysql_insert_id() > 0) {
            return mysql_insert_id();
        } else {
            return 0;
        }
    }

    /**
     * EditItemCategory
     * @return boolean
     */
    function EditItemCategory() {
        //edit query
        $strSql = "UPDATE tbl_product_category SET " ;
        $ItemCategoryName = " ItemCategoryName='" . $this->m_ItemCategoryName . "'";
        if ($this->m_ItemCategoryName != '') {
            $strSql .=$ItemCategoryName;
        }
        $parent = " ,parent_id='" . $this->main_cat . "' ";
        if (!empty($this->main_cat) && $this->main_cat != '') {
            if($this->main_cat == 'parent'){
                $parent = " ,parent_id=NULL ";
            }
            $strSql .=$parent;
        }

        $strSql .=" WHERE PKItemCategoryID=" . $this->m_npkId;
        //query result
//        echo $strSql;exit;
        $rsSql = mysql_query($strSql) or die("Error EditItemCategory");
        if (mysql_affected_rows()) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }

    /**
     * DeleteItemCategory
     * @return boolean
     */
    function DeleteItemCategory() {
        //delete query
        $strSql = "DELETE FROM tbl_product_category WHERE PKItemCategoryID=" . $this->m_npkId;
        //query result
        $rsSql = mysql_query($strSql) or die("Error DeleteItemCategory");
        if (mysql_affected_rows()) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * GetAllItemCategory
     * @return boolean
     */
    function GetAllItemCategory() {

        $strSql = "SELECT
					tbl_product_category.PKItemCategoryID,
					tbl_product_category.ItemCategoryName
					FROM
					tbl_product_category";


        //query result
        $rsSql = mysql_query($strSql) or die("Error GetAllManageItem");

        if (mysql_num_rows($rsSql) > 0) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }

    /**
     * GetItemCategoryById
     * @return boolean
     */
    function GetItemCategoryById() {
        $strSql = "
				SELECT
					tbl_product_category.PKItemCategoryID,
					tbl_product_category.ItemCategoryName
					FROM
					tbl_product_category
					WHERE tbl_product_category.PKItemCategoryID=" . $this->m_npkId;
        //query result
        $rsSql = mysql_query($strSql) or die("Error GetItemCategoryById");
        if (mysql_num_rows($rsSql) > 0) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }

}

?>
