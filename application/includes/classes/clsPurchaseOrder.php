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
class clsPurchaseOrder {

    // table name
    protected static $table_name = "purchase_order";
    //db fileds
    protected static $db_fields = array('reference_number', 'procured_by', 'status', 'created_date', 'created_by', 'modified_by', 'wh_id', 'po_number', 'po_date', 'dollar_rate', 'contact_no', 'signing_date', 'adv_payment_release', 'contract_delivery_date', 'po_accept_date', 'po_cancelled_date', 'po_delete_date','currency','local_foreign','country','sub_cat','tender_no','remarks','supplier_id','incoterm','funding_source');
    //pk stock id
    public $pk_id;
    public $reference_number; 
    //from warehouse id
    public $procured_by;
    //from status
    public $status;
    //to warehouse id
    public $created_date;
    //created by
    public $created_by;
    //created on
    public $modified_by; 
    public $po_number;
    public $po_date;
    public $dollar_rate;
    public $contact_no;
    public $signing_date; 
    public $adv_payment_release;
    public $contract_delivery_date;
    public $po_accept_date;
    public $po_cancelled_date;
    public $po_delete_date;
    public $currency;
    public $local_foreign;
    public $country;
    public $sub_cat;
    public $tender_no;
    public $remarks;
    public $supplier_id;
    public $incoterm;
    public $funding_source;
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
        $result_array = static::find_by_sql($strSql);
        return !empty($result_array) ? array_shift($result_array) : false;
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
            $sql .= " WHERE pk_id=" . $database->escape_value($this->pk_id);
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
    public function getPO() {
        $qry = "SELECT DISTINCT
        purchase_order.po_number
        FROM
        purchase_order
        WHERE
        purchase_order.po_number IS NOT NULL
        ";
        $rsSql = mysql_query($qry) or die("Error getPO");
        if (mysql_num_rows($rsSql) > 0) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }
    public function getVendor() {
        $qry = "SELECT DISTINCT
stakeholder.stkname
FROM
purchase_order_product_details
INNER JOIN stakeholder ON purchase_order_product_details.manufacturer_id = stakeholder.stkid

        ";
        $rsSql = mysql_query($qry) or die("Error getVendor");
        if (mysql_num_rows($rsSql) > 0) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }
    function NDMAShipmentSearch($type, $wh_id, $groupby = '', $page_type = '') {

        if ($page_type == 'summary') {
            $detail_column = " (purchase_order_product_details.shipment_quantity) as shipment_quantity ";
        } else {
            $detail_column = " purchase_order_product_details.shipment_quantity";
        }
        //select query
        $strSql = "SELECT
                    purchase_order.pk_id,
                    purchase_order.contract_delivery_date,
                    purchase_order_product_details.unit_price,
                    purchase_order.reference_number,
                     $detail_column,
                    
                    sum(tbl_stock_detail.Qty) as received_qty,     
                    itminfo_tab.itm_name,
                    tbl_warehouse.wh_name as stkname,
                    purchase_order.`status`,
                    itminfo_tab.qty_carton,
		    tbl_itemunits.UnitType,
                    tbl_locations.LocName as procured_by
                    FROM
                    purchase_order
INNER JOIN purchase_order_product_details ON purchase_order.pk_id = purchase_order_product_details.po_master_id
                    INNER JOIN itminfo_tab ON purchase_order_product_details.item_id = itminfo_tab.itm_id
                    LEFT JOIN tbl_warehouse ON purchase_order.stk_id = tbl_warehouse.wh_id
                    LEFT JOIN tbl_itemunits ON itminfo_tab.itm_type = tbl_itemunits.UnitType
                    INNER JOIN tbl_locations ON purchase_order.procured_by = tbl_locations.PkLocID
                    LEFT JOIN tbl_stock_master ON tbl_stock_master.shipment_id = purchase_order.pk_id
                    LEFT JOIN tbl_stock_detail ON tbl_stock_detail.fkStockID = tbl_stock_master.PkStockID
                    ";
        if (isset($_SESSION['user_warehouse'])) {
            $where[] = " purchase_order.wh_id = 96584 ";
        }
        if (isset($_SESSION['user_level']) && $_SESSION['user_level'] == 2) {
            $strSql .= " INNER JOIN funding_stk_prov ON purchase_order.stk_id = funding_stk_prov.funding_source_id ";

            if (isset($_SESSION['user_province1'])) {
                $where[] = " funding_stk_prov.province_id = 8 ";
            }
            if (isset($_SESSION['user_stakeholder1'])) {
                $where[] = " funding_stk_prov.stakeholder_id = 95526 ";
            }
        }

        if (!empty($this->WHID)) {
            $where[] = "purchase_order_product_details.funding_source = '" . $this->WHID . "'";
        }
        if (!empty($this->item_id)) {
            $where[] = "purchase_order_product_details.item_id = '" . $this->item_id . "'";
        }
        if (!empty($this->procured_by)) {
            $where[] = "purchase_order.procured_by = '" . $this->procured_by . "'";
        }
        if (!empty($this->status)) {
            $where[] = "purchase_order.status = '" . $this->status . "'";
        }
        if (!empty($this->fromDate) && !empty($this->toDate)) {
            $where[] = "DATE_FORMAT(purchase_order.contract_delivery_date, '%Y-%m-%d') BETWEEN '" . $this->fromDate . "' AND '" . $this->toDate . "'";
        }



        if (!empty($where) && is_array($where)) {
            $strSql .= " WHERE " . implode(" AND ", $where);
        }
        //$strSql = $strSql . ' GROUP BY tbl_stock_master.TranNo ORDER BY tbl_stock_master.TranNo DESC';
        $groupby = !empty($groupby) ? $groupby : ' ';
        $strSql = $strSql . $groupby;
        $strSql = $strSql . ' ORDER BY purchase_order.contract_delivery_date DESC ';


        //echo $strSql;exit;
        $rsSql = mysql_query($strSql) or trigger_error(mysql_error() . $strSql);
        if (mysql_num_rows($rsSql) > 0) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }
    
    function POSummary($type, $wh_id, $groupby = '',$search_as) {

        if ($search_as == 'summary') {
            $groupby= "  GROUP BY itminfo_tab.generic_name";
        }
       
        else {
            $groupby.= " ,purchase_order_product_details.item_id";
        }
        //select query
        $strSql = "SELECT
                    purchase_order.pk_id,
                    purchase_order.reference_number,
                    SUM(tbl_stock_detail.Qty) AS received_qty,
                    itminfo_tab.itm_name,
                    SUM(unit_price) AS unit_price,
                    tbl_warehouse.wh_name AS stkname,
                    tbl_warehouse.wh_name AS funding_source,
                    purchase_order.`status`,
                    itminfo_tab.qty_carton,
                    tbl_itemunits.UnitType,
                    SUM(
                            shipment_quantity
                    ) AS shipment_quantity,
                    GROUP_CONCAT(unit_price) AS concatenated_unit,
                    GROUP_CONCAT(shipment_quantity) AS concatenated_qty 
            FROM
                    purchase_order
            INNER JOIN purchase_order_product_details ON purchase_order.pk_id = purchase_order_product_details.po_master_id
            INNER JOIN itminfo_tab ON purchase_order_product_details.item_id= itminfo_tab.itm_id
            LEFT JOIN tbl_warehouse ON purchase_order_product_details.funding_source = tbl_warehouse.wh_id
            LEFT JOIN tbl_itemunits ON itminfo_tab.itm_type = tbl_itemunits.UnitType
            LEFT JOIN tbl_stock_master ON tbl_stock_master.shipment_id = purchase_order.pk_id
            LEFT JOIN tbl_stock_detail ON tbl_stock_detail.fkStockID = tbl_stock_master.PkStockID
        ";
        if (isset($_SESSION['user_warehouse'])) {
            $where[] = " purchase_order.wh_id = " . $_SESSION['user_warehouse'] . " ";
        }
        if (isset($_SESSION['user_level']) && $_SESSION['user_level'] == 2) {
            $strSql .= " INNER JOIN funding_stk_prov ON purchase_order_product_details.funding_source = funding_stk_prov.funding_source_id ";

            if (isset($_SESSION['user_province1'])) {
                $where[] = " funding_stk_prov.province_id = " . $_SESSION['user_province1'] . " ";
            }
            if (isset($_SESSION['user_stakeholder1'])) {
                $where[] = " funding_stk_prov.stakeholder_id = " . $_SESSION['user_stakeholder1'] . " ";
            }
        }

        if (!empty($this->WHID)) {
            $where[] = "purchase_order.stk_id = '" . $this->WHID . "'";
        } 
        if (!empty($this->po_number)) {
            $where[] = "purchase_order.po_number LIKE '" . $this->po_number . "'";
        }
        if (!empty($this->manufacturer)) {
            $where[] = "purchase_order_product_details.manufacturer_id = " . $this->manufacturer ;
        }
        if (!empty($this->status)) {
            $where[] = "purchase_order.status = '" . $this->status . "'";
        }
        if (!empty($this->fromDate) && !empty($this->toDate)) {
            $where[] = "DATE_FORMAT(purchase_order.contract_delivery_date, '%Y-%m-%d') BETWEEN '" . $this->fromDate . "' AND '" . $this->toDate . "'";
        }

        if (!empty($where) && is_array($where)) {
            $strSql .= " WHERE " . implode(" AND ", $where);
        }
        //$strSql = $strSql . ' GROUP BY tbl_stock_master.TranNo ORDER BY tbl_stock_master.TranNo DESC';
        $groupby = !empty($groupby) ? $groupby : ' ';
        $strSql = $strSql . $groupby;
        $strSql = $strSql . ' ORDER BY purchase_order.contract_delivery_date DESC ';
//       print_r($strSql);exit;

        $rsSql = mysql_query($strSql) or trigger_error(mysql_error() . $strSql);
        if (mysql_num_rows($rsSql) > 0) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }
    
    function PortfolioSearch($type, $wh_id, $groupby = '',$search_as) {

        if ($search_as == 'summary') {
            $groupby= "  GROUP BY itminfo_tab.generic_name";
        }
       
        else {
            $groupby.= " ,purchase_order_product_details.item_id";
        }
        //select query
        $strSql = "SELECT
	purchase_order.pk_id,
	purchase_order.reference_number,
	SUM(tbl_stock_detail.Qty) AS received_qty,
	itminfo_tab.itm_name,
	SUM(unit_price) AS unit_price,
	tbl_warehouse.wh_name AS stkname,
	purchase_order.`status`,
	itminfo_tab.qty_carton,
	tbl_itemunits.UnitType,
	SUM(shipment_quantity) AS shipment_quantity,
        GROUP_CONCAT(unit_price) AS concatenated_unit,
        GROUP_CONCAT(shipment_quantity) AS concatenated_qty
            FROM
                    purchase_order
            INNER JOIN purchase_order_product_details ON purchase_order.pk_id = purchase_order_product_details.po_master_id
            INNER JOIN itminfo_tab ON purchase_order_product_details.item_id = itminfo_tab.itm_id
            LEFT JOIN tbl_warehouse ON purchase_order_product_details.funding_source = tbl_warehouse.wh_id
            LEFT JOIN tbl_itemunits ON itminfo_tab.itm_type = tbl_itemunits.UnitType 
            LEFT JOIN tbl_stock_master ON tbl_stock_master.shipment_id = purchase_order.pk_id
            LEFT JOIN tbl_stock_detail ON tbl_stock_detail.fkStockID = tbl_stock_master.PkStockID ";
        if (isset($_SESSION['user_warehouse'])) {
            $where[] = " purchase_order.wh_id = " . $_SESSION['user_warehouse'] . " ";
        }
        if (isset($_SESSION['user_level']) && $_SESSION['user_level'] == 2) {
            $strSql .= " INNER JOIN funding_stk_prov ON purchase_order_product_details.funding_source = funding_stk_prov.funding_source_id ";

            if (isset($_SESSION['user_province1'])) {
                $where[] = " funding_stk_prov.province_id = " . $_SESSION['user_province1'] . " ";
            }
            if (isset($_SESSION['user_stakeholder1'])) {
                $where[] = " funding_stk_prov.stakeholder_id = " . $_SESSION['user_stakeholder1'] . " ";
            }
        }

        if (!empty($this->WHID)) {
            $where[] = "purchase_order_product_details.funding_source = '" . $this->WHID . "'";
        } 
        if (!empty($this->po_number)) {
            $where[] = "purchase_order.po_number LIKE '" . $this->po_number . "'";
        }
        if (!empty($this->manufacturer)) {
            $where[] = "purchase_order_product_details.manufacturer_id = " . $this->manufacturer ;
        }
        if (!empty($this->status)) {
            $where[] = "purchase_order.status = '" . $this->status . "'";
        }
        if (!empty($this->fromDate) && !empty($this->toDate)) {
            $where[] = "DATE_FORMAT(purchase_order.contract_delivery_date, '%Y-%m-%d') BETWEEN '" . $this->fromDate . "' AND '" . $this->toDate . "'";
        }

        if (!empty($where) && is_array($where)) {
            $strSql .= " WHERE " . implode(" AND ", $where);
        }
        //$strSql = $strSql . ' GROUP BY tbl_stock_master.TranNo ORDER BY tbl_stock_master.TranNo DESC';
        $groupby = !empty($groupby) ? $groupby : ' ';
        $strSql = $strSql . $groupby;
        $strSql = $strSql . ' ORDER BY purchase_order.contract_delivery_date DESC ';
//        print_r($strSql);exit;

        $rsSql = mysql_query($strSql) or trigger_error(mysql_error() . $strSql);
        if (mysql_num_rows($rsSql) > 0) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }
    function ComprehensiveSearch($type, $wh_id, $groupby = '',$search_as) {
 
        //select query
        $strSql = "SELECT
        purchase_order.pk_id,
        purchase_order.reference_number,
        Sum(tbl_stock_detail.Qty) AS received_qty,
        itminfo_tab.itm_name,
        purchase_order_product_details.unit_price,
        tbl_warehouse.wh_name AS stkname,
        purchase_order.`status`,
        tbl_itemunits.UnitType,
        purchase_order_product_details.shipment_quantity,
        itminfo_tab.itm_des,
        item_requirements.requirement,
        purchase_order.po_number,
        purchase_order.po_date,
        purchase_order.contact_no,
        purchase_order.adv_payment_release,
        purchase_order.po_cancelled_date,
        purchase_order.contract_delivery_date,
        purchase_order.signing_date,
        stakeholder.stkname as vendor,
        stakeholder.contact_numbers
        FROM
        purchase_order
INNER JOIN purchase_order_product_details ON purchase_order.pk_id = purchase_order_product_details.po_master_id
        INNER JOIN itminfo_tab ON purchase_order_product_details.item_id = itminfo_tab.itm_id
        LEFT JOIN tbl_warehouse ON purchase_order_product_details.funding_source = tbl_warehouse.wh_id
        LEFT JOIN tbl_itemunits ON itminfo_tab.itm_type = tbl_itemunits.UnitType
        LEFT JOIN tbl_stock_master ON tbl_stock_master.shipment_id = purchase_order.pk_id
        LEFT JOIN tbl_stock_detail ON tbl_stock_detail.fkStockID = tbl_stock_master.PkStockID
        LEFT JOIN item_requirements ON purchase_order_product_details.item_id = item_requirements.item_id
        LEFT JOIN stakeholder ON purchase_order_product_details.manufacturer_id = stakeholder.stkid ";
        if (isset($_SESSION['user_warehouse'])) {
            $where[] = " purchase_order.wh_id = " . $_SESSION['user_warehouse'] . " ";
        }
        if (isset($_SESSION['user_level']) && $_SESSION['user_level'] == 2) {
            $strSql .= " INNER JOIN funding_stk_prov ON purchase_order_product_details.funding_source = funding_stk_prov.funding_source_id ";

            if (isset($_SESSION['user_province1'])) {
                $where[] = " funding_stk_prov.province_id = " . $_SESSION['user_province1'] . " ";
            }
            if (isset($_SESSION['user_stakeholder1'])) {
                $where[] = " funding_stk_prov.stakeholder_id = " . $_SESSION['user_stakeholder1'] . " ";
            }
        }

        if (!empty($this->WHID)) {
            $where[] = "purchase_order.stk_id = '" . $this->WHID . "'";
        } 
        if (!empty($this->fromDate) && !empty($this->toDate)) {
            $where[] = "DATE_FORMAT(purchase_order.contract_delivery_date, '%Y-%m-%d') BETWEEN '" . $this->fromDate . "' AND '" . $this->toDate . "'";
        }

        if (!empty($where) && is_array($where)) {
            $strSql .= " WHERE " . implode(" AND ", $where);
        }
        //$strSql = $strSql . ' GROUP BY tbl_stock_master.TranNo ORDER BY tbl_stock_master.TranNo DESC';
        $groupby = !empty($groupby) ? $groupby : ' ';
        $strSql = $strSql . $groupby;
        $strSql = $strSql . ' ORDER BY purchase_order.contract_delivery_date DESC ';
//        print_r($strSql);exit;

        $rsSql = mysql_query($strSql) or trigger_error(mysql_error() . $strSql);
        if (mysql_num_rows($rsSql) > 0) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }
    
    function ShipmentSearch($type, $wh_id, $groupby = '', $page_type = '') {

        if ($page_type == 'summary') {
            $detail_column = " (purchase_order_product_details.shipment_quantity) as shipment_quantity ";
        } else {
            $detail_column = " purchase_order_product_details.shipment_quantity";
        }
        //select query
        $strSql = "SELECT
                        purchase_order.*,
                        purchase_order_product_details.*,
                        $detail_column,
                        sum(tbl_stock_detail.Qty) as received_qty,     
                        itminfo_tab.itm_name,
                        tbl_warehouse.wh_name as stkname,
                        itminfo_tab.qty_carton,
                        tbl_itemunits.UnitType,
                        tbl_locations.LocName as procured_by,
                        stakeholder.stkname as vendor_name
                    FROM
                        purchase_order
INNER JOIN purchase_order_product_details ON purchase_order.pk_id = purchase_order_product_details.po_master_id
                    INNER JOIN itminfo_tab ON purchase_order_product_details.item_id = itminfo_tab.itm_id
                    LEFT JOIN tbl_warehouse ON purchase_order_product_details.funding_source = tbl_warehouse.wh_id
                    LEFT JOIN tbl_itemunits ON itminfo_tab.itm_type = tbl_itemunits.UnitType
                    LEFT JOIN tbl_locations ON purchase_order.procured_by = tbl_locations.PkLocID
                    LEFT JOIN tbl_stock_master ON tbl_stock_master.shipment_id = purchase_order.pk_id
                    LEFT JOIN tbl_stock_detail ON tbl_stock_detail.fkStockID = tbl_stock_master.PkStockID
                    LEFT JOIN stakeholder_item ON purchase_order_product_details.manufacturer_id = stakeholder_item.stk_id
                    LEFT JOIN stakeholder ON stakeholder_item.stkid = stakeholder.stkid
                    ";
         if(isset($_SESSION['user_warehouse']))
            {
                $where[] = " purchase_order.wh_id = ".$_SESSION['user_warehouse']." ";
            }
        if(isset($_SESSION['user_level']) && $_SESSION['user_level'] == 2)
        {
            $strSql.= " INNER JOIN funding_stk_prov ON purchase_order_product_details.funding_source = funding_stk_prov.funding_source_id ";
            
            if(isset($_SESSION['user_province1']))
            {
                $where[] = " funding_stk_prov.province_id = ".$_SESSION['user_province1']." ";
            }
            if(isset($_SESSION['user_stakeholder1']))
            {
                $where[] = " funding_stk_prov.stakeholder_id = ".$_SESSION['user_stakeholder1']." ";
            }
           
        }
        
        if (!empty($this->WHID)) {
            $where[] = "purchase_order_product_details.funding_source = '" . $this->WHID . "'";
        }
        if (!empty($this->item_id)) {
            if(is_array($this->item_id)){
                
                $where[] = "purchase_order_product_details.item_id in  (" .implode(',',$this->item_id)  . ") ";
            }
            else{
                $where[] = "purchase_order_product_details.item_id = '" . $this->item_id . "'";
            }
        }
        if (!empty($this->procured_by)) {
            $where[] = "purchase_order.procured_by = '" . $this->procured_by . "'";
        }
        if (!empty($this->status)) {
            $where[] = "purchase_order.status = '" . $this->status . "'";
        }
        if (!empty($this->fromDate) && !empty($this->toDate)) {
            $where[] = "DATE_FORMAT(purchase_order.contract_delivery_date, '%Y-%m-%d') BETWEEN '" . $this->fromDate . "' AND '" . $this->toDate . "'";
        }



        if (!empty($where) && is_array($where)) {
            $strSql .= " WHERE " . implode(" AND ", $where);
        }
        //$strSql = $strSql . ' GROUP BY tbl_stock_master.TranNo ORDER BY tbl_stock_master.TranNo DESC';
        $groupby = !empty($groupby) ? $groupby : ' ';
        $strSql = $strSql . $groupby;
        $strSql = $strSql . ' ORDER BY purchase_order.contract_delivery_date DESC ';


       //echo $strSql;exit;
        $rsSql = mysql_query($strSql) or trigger_error(mysql_error() . $strSql);
        if (mysql_num_rows($rsSql) > 0) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }
    
    function POSearchLight($type, $wh_id, $groupby = '', $page_type = '') {

        if ($page_type == 'summary') {
            $detail_column = " (purchase_order_product_details.shipment_quantity) as shipment_quantity ";
        } else {
            $detail_column = " purchase_order_product_details.shipment_quantity";
        }
        //select query
        $strSql = "SELECT
        purchase_order.pk_id,
        $detail_column,
        purchase_order.po_date,
        purchase_order_product_details.pk_id AS product_detail_id,
        purchase_order.po_number,
        purchase_order.reference_number,
        purchase_order.procured_by,
        purchase_order.created_date,
        purchase_order.created_by,
        purchase_order.modified_by,
        purchase_order.modified_date,
        purchase_order.`status`,
        purchase_order.wh_id,
        purchase_order.dollar_rate,
        purchase_order.contact_no,
        purchase_order.signing_date,
        purchase_order.adv_payment_release,
        purchase_order.contract_delivery_date,
        purchase_order.po_accept_date,
        purchase_order.po_cancelled_date,
        purchase_order.po_delete_date,
        purchase_order.currency,
        purchase_order.local_foreign,
        purchase_order.country,
        purchase_order.sub_cat,
        purchase_order.tender_no,
        purchase_order.remarks,
        purchase_order_product_details.item_id,
        purchase_order_product_details.manufacturer_id,
        purchase_order_product_details.shipment_quantity,
        purchase_order_product_details.unit_price,
        purchase_order_product_details.funding_source,
        purchase_order_product_details.po_master_id,
        itminfo_tab.itm_name,
        tbl_warehouse.wh_name AS stkname,
        itminfo_tab.qty_carton,
        tbl_itemunits.UnitType,
        tbl_locations.LocName AS procured_by,
        stakeholder.stkname AS vendor_name
        FROM
                purchase_order
        INNER JOIN purchase_order_product_details ON purchase_order.pk_id = purchase_order_product_details.po_master_id
        INNER JOIN itminfo_tab ON purchase_order_product_details.item_id = itminfo_tab.itm_id
        LEFT JOIN tbl_warehouse ON purchase_order_product_details.funding_source = tbl_warehouse.wh_id
        LEFT JOIN tbl_itemunits ON itminfo_tab.itm_type = tbl_itemunits.UnitType
        LEFT JOIN tbl_locations ON purchase_order.procured_by = tbl_locations.PkLocID
        LEFT JOIN stakeholder_item ON purchase_order_product_details.manufacturer_id = stakeholder_item.stk_id
        LEFT JOIN stakeholder ON stakeholder_item.stkid = stakeholder.stkid
                    ";
         if(isset($_SESSION['user_warehouse']))
            {
                $where[] = " purchase_order.wh_id = ".$_SESSION['user_warehouse']." ";
            }
        if(isset($_SESSION['user_level']) && $_SESSION['user_level'] == 2)
        {
            $strSql.= " INNER JOIN funding_stk_prov ON purchase_order_product_details.funding_source = funding_stk_prov.funding_source_id ";
            
            if(isset($_SESSION['user_province1']))
            {
                $where[] = " funding_stk_prov.province_id = ".$_SESSION['user_province1']." ";
            }
            if(isset($_SESSION['user_stakeholder1']))
            {
                $where[] = " funding_stk_prov.stakeholder_id = ".$_SESSION['user_stakeholder1']." ";
            }
           
        }
        
        if (!empty($this->WHID)) {
            $where[] = "purchase_order_product_details.funding_source = '" . $this->WHID . "'";
        }
        if (!empty($this->item_id)) {
            if(is_array($this->item_id)){
                
                $where[] = "purchase_order_product_details.item_id in  (" .implode(',',$this->item_id)  . ") ";
            }
            else{
                $where[] = "purchase_order_product_details.item_id = '" . $this->item_id . "'";
            }
        }
        if (!empty($this->procured_by)) {
            $where[] = "purchase_order.procured_by = '" . $this->procured_by . "'";
        }
        if (!empty($this->status)) {
            $where[] = "purchase_order.status = '" . $this->status . "'";
        }
        if (!empty($this->fromDate) && !empty($this->toDate)) {
            $where[] = "DATE_FORMAT(purchase_order.contract_delivery_date, '%Y-%m-%d') BETWEEN '" . $this->fromDate . "' AND '" . $this->toDate . "'";
        }



        if (!empty($where) && is_array($where)) {
            $strSql .= " WHERE " . implode(" AND ", $where);
        }
        //$strSql = $strSql . ' GROUP BY tbl_stock_master.TranNo ORDER BY tbl_stock_master.TranNo DESC';
        $groupby = !empty($groupby) ? $groupby : ' ';
        $strSql = $strSql . $groupby;
        $strSql = $strSql . ' ORDER BY purchase_order.pk_id DESC ';


//       echo $strSql;exit;
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
    public function getReceivedVouhcers($id = 0) {

        //select query
        $strSql = "SELECT
                            tbl_stock_master.PkStockID,
                            tbl_stock_master.TranNo
                    FROM
                            tbl_stock_master
                    WHERE
                            tbl_stock_master.shipment_id = $id 
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

}

?>