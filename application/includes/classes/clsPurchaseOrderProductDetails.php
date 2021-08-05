<?php

/**
 * clsStockMaster
 * @package includes/class
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
// If it's going to need the database, then it's
// probably smart to require it before we start.
class clsPurchaseOrderProductDetails {

    // table name
    protected static $table_name = "purchase_order_product_details";
    //db fileds
    protected static $db_fields = array('item_id', 'manufacturer_id', 'shipment_quantity', 'unit_price', 'po_master_id');
    //pk stock id
    public $pk_id;
    //transaction date
    public $item_id;
    //transaction date
    public $manufacturer_id;
    //transaction type id
    public $shipment_quantity;
    //transaction ref 
    public $unit_price;
//    public $funding_source;
    public $po_master_id;

    // Common Database Methods
    /**
     * 
     * find_all
     * @return type
     * 
     * 
     */
    public function find_all() {
        return static::find_by_sql("SELECT * FROM " . static::$table_name);
    }

    /**
     * 
     * find_by_id
     * @param type $id
     * @return type
     * 
     * 
     */
    public function find_by_id($id = 0) {

        //select query
        $strSql = "SELECT * FROM " . static::$table_name . " WHERE pk_id={$id} LIMIT 1";
//        print_r($strSql);exit;
        //query result
       $rsSql = mysql_query($strSql) or trigger_error(mysql_error() . $strSql);
        if (mysql_num_rows($rsSql) > 0) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }

    /**
     * 
     * find_by_id
     * @param type $id
     * @return type
     * 
     * 
     */
    public function get_PO_by_id($id = 0) {

        //select query
        $strSql = "SELECT * FROM " . static::$table_name . " WHERE po_master_id={$id} LIMIT 1";
        //query result
        $result_array = static::find_by_sql($strSql);
        return !empty($result_array) ? array_shift($result_array) : false;
    }

    /**
     * 
     * find_by_sql
     * @param type $sql
     * @return type
     * 
     * 
     */
    public function find_by_sql($sql = "") {
        $result_set = mysql_query($sql);
        //query result
        $object_array = array();
        while ($row = mysql_fetch_array($result_set)) {
            $object_array[] = static::instantiate($row);
        }
        return $object_array;
    }

    /**
     * 
     * count_all
     * @global type $database
     * @return type
     * 
     * 
     */
    public function count_all() {
        global $database;
        //select query
        $sql = "SELECT COUNT(*) FROM " . static::$table_name;
        //query result
        $result_set = $database->query($sql);
        $row = $database->fetch_array($result_set);
        return array_shift($row);
    }

    /**
     * 
     * instantiate
     * @param type $record
     * @return \self
     * 
     * 
     */
    private function instantiate($record) {
        // Could check that $record exists and is an array
        $object = new self;
        // Simple, long - form approach:
        // More dynamic, short - form approach:
        foreach ($record as $attribute => $value) {
            if ($object->has_attribute($attribute)) {
                $object->$attribute = $value;
            }
        }
        return $object;
    }

    /**
     * 
     * has_attribute
     * @param type $attribute
     * @return type
     * 
     * 
     */
    private function has_attribute($attribute) {
        // We don't care about the value, we just want to know if the key exists
        // Will return true or false
        return array_key_exists($attribute, $this->attributes());
    }

    /**
     * 
     * attributes
     * @return type
     * 
     * 
     */
    protected function attributes() {
        // return an array of attribute names and their values
        $attributes = array();
        foreach (static::$db_fields as $field) {
            if (property_exists($this, $field)) {
                $attributes[$field] = $this->$field;
            }
        }
        return $attributes;
    }

    /**
     * 
     * sanitized_attributes
     * @global type $database
     * @return type
     * 
     * 
     */
    protected function sanitized_attributes() {
        global $database;
        $clean_attributes = array();
        // sanitize the values before submitting
        // Note: does not alter the actual value of each attribute
        foreach ($this->attributes() as $key => $value) {
            $clean_attributes[$key] = $database->escape_value($value);
        }
        return $clean_attributes;
    }

    /**
     * 
     * save
     * @return type
     * 
     * 
     */
    public function save() {
        // A new record won't have an id yet.
        return isset($this->pk_id) ? $this->update() : $this->create();
    }

    /**
     *
     * create
     * @global type $database
     * @return boolean
     * 
     * 
     */
    public function create() {
        global $database;
        // Don't forget your SQL syntax and good habits:
        // - INSERT INTO table (key, key) VALUES ('value', 'value')
        // - single - quotes around all values
        // - escape all values to prevent SQL injection
        $attributes = $this->sanitized_attributes();
        //insert query
        $sql = "INSERT INTO " . static::$table_name . " (";
        $sql .= join(", ", array_keys($attributes));
        $sql .= ") VALUES ('";
        $sql .= join("', '", array_values($attributes));
        $sql .= "')";
//        echo $sql;exit; 
        if ($database->query($sql)) {
            return $database->insert_id();
        } else {
            return false;
        }
    }

    /**
     * 
     * update
     * @global type $database
     * @return type
     * 
     * 
     */
    public function update() {
        global $database;
        // Don't forget your SQL syntax and good habits:
        // - UPDATE table SET key = 'value', key = 'value' WHERE condition
        // - single - quotes around all values
        // - escape all values to prevent SQL injection
        $attributes = $this->sanitized_attributes();
        $attribute_pairs = array();
        foreach ($attributes as $key => $value) {
            $attribute_pairs[] = "{$key}='{$value}'";
        }
        $sql = "UPDATE " . static::$table_name . " SET ";
        $sql .= join(", ", $attribute_pairs);
        $sql .= " WHERE pk_id=" . $database->escape_value($this->pk_id);
//        echo $sql;  exit;
        $database->query($sql);
        return ($database->affected_rows() == 1) ? true : false;
    }

    /**
     * 
     * delete
     * @global type $database
     * @return type
     * 
     * 
     */
    public function delete() {
        global $database;
        // Don't forget your SQL syntax and good habits:
        // - DELETE FROM table WHERE condition LIMIT 1
        // - escape all values to prevent SQL injection
        // - use LIMIT 1
        if ($this->pk_id) {
            //delete query
            $sql = "DELETE FROM " . static::$table_name;
            $sql .= " WHERE pk_id='" . $database->escape_value($this->pk_id)."' ";
            $sql .= " LIMIT 1";
            //echo $sql;exit;
            $database->query($sql);
            return ($database->affected_rows() == 1) ? true : false;
        }
    }

    public function getReceivedVouhcers($id = 0,$detail_id=0) {

        //select query
        $strSql = "SELECT
                            tbl_stock_master.PkStockID,
                            tbl_stock_master.TranNo
                    FROM
                            tbl_stock_master
                    WHERE
                            tbl_stock_master.shipment_id = $id 
                            AND  tbl_stock_master.po_detail = $detail_id 
                            AND tbl_stock_master.temp = 0";
//        print_r($strSql);exit;
        //query result
        $rsSql = mysql_query($strSql) or trigger_error(mysql_error() . $strSql);
        $result = array();
        if (mysql_num_rows($rsSql) > 0) {
            while ($row = mysql_fetch_assoc($rsSql)) {
                $voucher_id = $row['PkStockID'];
                $voucher_no = $row['TranNo'];
                $result[] = "<a onclick=window.open('printReceive.php?id=$voucher_id','_blank','scrollbars=1,width=842,height=595') href=javascript:void(0)>$voucher_no</a>";
            }
        }
        return implode("<br>", $result);
    }

    public function getPOProducts($master_id) {
        $qry = "SELECT
            purchase_order_product_details.pk_id,
            purchase_order_product_details.item_id,
            purchase_order_product_details.manufacturer_id,
            purchase_order_product_details.shipment_quantity,
            purchase_order_product_details.unit_price, 
            purchase_order_product_details.po_master_id,
            itminfo_tab.itm_name, 
            stakeholder.stkname
        FROM
            purchase_order_product_details
        INNER JOIN itminfo_tab ON purchase_order_product_details.item_id = itminfo_tab.itm_id
         LEFT JOIN stakeholder_item ON purchase_order_product_details.manufacturer_id = stakeholder_item.stk_id
        LEFT JOIN stakeholder ON stakeholder_item.stkid = stakeholder.stkid
        WHERE
            purchase_order_product_details.po_master_id = $master_id
        ";
//        echo $qry;exit;
        $rsSql = mysql_query($qry) or trigger_error(mysql_error() . $qry);
        if (mysql_num_rows($rsSql) > 0) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }
   

}

?>