<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Suppliers_model extends Base_Model {
    function __construct() {
        // call the base model constructor
        parent::__construct();

        // assign the table for this model
        $this->_TABLES = array(
                'suppliers' => 'is_suppliers'
        );

    }

    function get_suppliers($where = null, $limit = null, $orderby = null, $fields = null) {
        $query = $this->fetch('suppliers', $fields, $where, $orderby, $limit);
        $row = $query->result();
        $query->free_result();
        return $row;
    }

    function insert_supplier($data) {
        return $this->insert('suppliers', $data);
    }

    function update_supplier($data, $where) {
        return $this->update('suppliers', $data, $where);
    }

    function delete_supplier($where) {
        return $this->delete('suppliers', $where);
    }

    function get_supplier_by_id($supplier_id) {
        $result = $this->get_suppliers(array('supplier_id' => $supplier_id));
        $row = NULL;
        if (count($result) > 0) {
            $row = $result[0];
        }
        return $row;
    }


    function get_supplier_count($where = null) {
        // do a sql count instead of row count
        $query = $this->fetch('suppliers', 'count(1) as cnt', $where);
        $row = $query->first_row();
        $query->free_result();
        return $row->cnt;
    }
}