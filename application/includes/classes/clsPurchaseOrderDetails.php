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
class clsPurchaseOrderDetails {

    // table name
    protected static $table_name = "purchase_order_details";
    //db fileds
    protected static $db_fields = array('po_id', 'delivery_date', 'total_unit', 'delivered', 'balance', 'warehouse_id');
    //pk stock id
    public $pk_id;
    public $po_id;
    public $delivery_date;
    public $total_unit;
    public $delivered;
    public $balance;
    public $warehouse_id;

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
        $strSql = "SELECT * FROM " . static::$table_name . " WHERE po_id={$id}";
        //query result
//        return static::find_by_sql($strSql);
        $result_array = mysql_query($strSql);
        return $result_array;
    }

    /**
     * 
     * find_by_id
     * @param type $id
     * @return type
     * 
     * 
     */
    public function get_shipment_by_id($id = 0) {

        //select query
        $strSql = "SELECT * FROM " . static::$table_name . " WHERE pk_id={$id} LIMIT 1";
        //query result
        $result_array = static::find_by_sql($strSql);
        return !empty($result_array) ? array_shift($result_array) : false;
    }

    /**
     * 
     * find_by_trans_no
     * @param type $trans_no
     * @return type
     * 
     * 
     */
    public function find_by_trans_no($trans_no = '') {
        //select query
        $result_array = static::find_by_sql("SELECT * FROM " . static::$table_name . " WHERE TranNo='{$trans_no}' LIMIT 1 DESC");
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
                 if ($this->$field != '') {
                    $attributes[$field] = $this->$field;
                }
                
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
//echo $sql;exit;
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
        if ($this->po_id) {
            //delete query
            $sql = "DELETE FROM " . static::$table_name;
            $sql .= " WHERE po_id=" . $database->escape_value($this->po_id);
            //$sql .= " LIMIT 1";
            $database->query($sql);
            return ($database->affected_rows() == 1) ? true : false;

            // NB: After deleting, the instance of User still
            // exists, even though the database entry does not.
            // This can be useful, as in:
            // but, for example, we can't call $user->update()
            // after calling $user->delete().
        }
    }

    public function getTrail($po_id) {
        //select query
        $strSql = "SELECT * FROM " . static::$table_name . " WHERE po_id={$po_id} ORDER BY delivery_date DESC LIMIT 3";
//        print_r($strSql);exit;        
//query result
        $rsSql = mysql_query($strSql) or trigger_error(mysql_error() . $strSql);
        if (mysql_num_rows($rsSql) > 0) {
            return $rsSql;
        } else {
            return FALSE;
        }
        //return !empty($result_array) ? array_shift($result_array) : false;
    }

}

?>