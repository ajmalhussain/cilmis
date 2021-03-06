<?php

/**
 * clsTransTypes
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
class clsTransTypes {

    protected static $table_name = "tbl_trans_type";
    protected static $db_fields = array('trans_id', 'trans_type', 'trans_nature', 'is_adjustment');
    public $trans_id;
    public $trans_type;
    public $trans_nature;
    public $is_adjustment;

    /**
     * Get Adjusment Types
     * 
     * @return type
     */
    public function getAdjusmentTypes() {
        return static::find_by_sql("SELECT
                tbl_trans_type.trans_type,
                tbl_trans_type.trans_id,
				tbl_trans_type.trans_nature
                FROM
                tbl_trans_type
                WHERE
                tbl_trans_type.is_adjustment > 0
                ORDER BY
                tbl_trans_type.trans_type ASC
                ");
    }

    // Common Database Methods
    /**
     * Find All
     * 
     * @return type
     */
    public function find_all() {
        return static::find_by_sql("SELECT * FROM " . static::$table_name . " WHERE is_adjustment = 1");
    }

    /**
     * Get All
     * 
     * @return type
     */
    public function get_all() {
        return static::find_by_sql("SELECT * FROM " . static::$table_name);
    }

    public function find_by_id($id = 0) {
        $result_array = static::find_by_sql("SELECT * FROM " . static::$table_name . " WHERE trans_id={$id} LIMIT 1");
        return !empty($result_array) ? array_shift($result_array) : false;
    }

    /**
     * Find by sql
     * 
     * @param type $sql
     * @return type
     */
    public function find_by_sql($sql = "") {
        $result_set = mysql_query($sql);
        $object_array = array();
        while ($row = mysql_fetch_array($result_set)) {
            $object_array[] = static::instantiate($row);
        }
        return $object_array;
    }

    /**
     * Count all
     * 
     * @global type $database
     * @return type
     */
    public function count_all() {
        global $database;
        $sql = "SELECT COUNT(*) FROM " . static::$table_name;
        $result_set = $database->query($sql);
        $row = $database->fetch_array($result_set);
        return array_shift($row);
    }

    /**
     * Instantiate
     * 
     * @param type $record
     * @return \self
     */
    private function instantiate($record) {
        // Could check that $record exists and is an array
        $object = new self;
        // Simple, long-form approach:
        // More dynamic, short-form approach:
        foreach ($record as $attribute => $value) {
            if ($object->has_attribute($attribute)) {
                $object->$attribute = $value;
            }
        }
        return $object;
    }

    /**
     * Has attribute
     * 
     * @param type $attribute
     * @return type
     */
    private function has_attribute($attribute) {
        // We don't care about the value, we just want to know if the key exists
        // Will return true or false
        return array_key_exists($attribute, $this->attributes());
    }

    /**
     * Attributes
     * 
     * @return type
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
     * Sanitized attributes
     * 
     * @global type $database
     * @return type
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
     * Save
     * 
     * @return type
     */
    public function save() {
        // A new record won't have an id yet.
        return isset($this->trans_id) ? $this->update() : $this->create();
    }

    /**
     * Create
     * 
     * @global type $database
     * @return boolean
     */
    public function create() {
        global $database;
        // Don't forget your SQL syntax and good habits:
        // - INSERT INTO table (key, key) VALUES ('value', 'value')
        // - single-quotes around all values
        // - escape all values to prevent SQL injection
        $attributes = $this->sanitized_attributes();
        $sql = "INSERT INTO " . static::$table_name . " (";
        $sql .= join(", ", array_keys($attributes));
        $sql .= ") VALUES ('";
        $sql .= join("', '", array_values($attributes));
        $sql .= "')";

        if ($database->query($sql)) {
            $this->trans_id = $database->insert_id();
            return true;
        } else {
            return false;
        }
    }

    /**
     * Update
     * 
     * @global type $database
     * @return type
     */
    public function update() {
        global $database;
        $sql = "UPDATE " . static::$table_name . " SET ";
        $sql .= " temp=0";
        $sql .= " WHERE trans_id=" . $database->escape_value($this->trans_id);
        $database->query($sql);
        return ($database->affected_rows() == 1) ? true : false;
    }

    /**
     * Delete
     * 
     * @global type $database
     * @return type
     */
    public function delete() {
        global $database;
        // Don't forget your SQL syntax and good habits:
        // - DELETE FROM table WHERE condition LIMIT 1
        // - escape all values to prevent SQL injection
        // - use LIMIT 1
        $sql = "DELETE FROM " . static::$table_name;
        $sql .= " WHERE trans_id=" . $database->escape_value($this->trans_id);
        $sql .= " LIMIT 1";
        $database->query($sql);
        return ($database->affected_rows() == 1) ? true : false;

        // NB: After deleting, the instance of User still 
        // exists, even though the database entry does not.
        // This can be useful, as in:
        // but, for example, we can't call $user->update() 
        // after calling $user->delete().
    }

}

?>