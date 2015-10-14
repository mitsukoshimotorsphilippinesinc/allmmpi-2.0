<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Employees_model extends Base_Model 
{

    function __construct() 
	{
		// call the base model constructor
		parent::__construct();

		// assign the table for this model
		$this->_TABLES = array(
			'employees' => 'ad_employees',
			'employee_slots' => 'ad_employee_slots',
			'employee_slots_view' => 'ad_employee_slots_view',
			'employee_products' => 'ad_employee_products',
			'employee_products_view' => 'ad_employee_products_view',
			'employee_types' => 'rf_employee_types'
		);

	}

	function get_employees($where = null, $limit = null, $orderby = null, $fields = null) 
	{
		$query = $this->fetch('employees', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
    }

	function insert_employee($data) 
	{
		return $this->insert('employees', $data);
	}

	function update_employees($data, $where) 
	{
		return $this->update('employees', $data, $where);
	}

	function delete_employees($where) 
	{
		return $this->delete('employees', $where);
	}

	function get_employee_by_id($employee_id) 
	{
		$result = $this->get_employees(array('employee_id' => $employee_id));
		$row = NULL;
		if (count($result) > 0) {
			$row = $result[0];
		}
		return $row;
	}
	
	function get_employee_by_code($employee_code) 
	{
		$result = $this->get_employees(array('employee_code' => $employee_code));
		$row = NULL;
		if (count($result) > 0) {
			$row = $result[0];
		}
		return $row;
	}
	
	function get_employee_count($where = null) {
		// do a sql count instead of row count
		$query = $this->fetch('employees', 'count(1) as cnt', $where);
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}
	
	function search_employees($search, $query, $limit = null, $orderby = null, $fields = null)
	{
		// clear previous get request
		$this->db->flush_cache();

		$this->db->distinct();
		$this->db->like($search,$query,'both');
		
		// No override function, procede with fetch
		($fields!=null) ? $this->db->select($fields) : '';
		($limit!=null) ? $this->db->limit($limit['rows'],$limit['offset']) : '';
		($orderby!=null) ? $this->db->order_by($orderby) : '';

		// set table to use
		$this->db->from($this->_TABLES['employees']);
		$result = $this->db->get();

		$row = $result->result();
		$result->free_result();
		return $row;
	}
	
	function get_employee_slots($where = null, $limit = null, $orderby = null, $fields = null) 
	{
		$query = $this->fetch('employee_slots_view', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
    }

	function insert_employee_slot($data) 
	{
		return $this->insert('employee_slots', $data);
	}

	function update_employee_slots($data, $where) 
	{
		return $this->update('employee_slots', $data, $where);
	}

	function delete_employee_slots($where) 
	{
		return $this->delete('employee_slots', $where);
	}

	function get_employee_slot_by_id($employee_slot_id) 
	{
		$result = $this->get_employee_slots(array('employee_slot_id' => $employee_slot_id));
		$row = NULL;
		if (count($result) > 0) {
			$row = $result[0];
		}
		return $row;
	}
	
	function get_employee_slot_count($where = null) {
		// do a sql count instead of row count
		$query = $this->fetch('employee_slots_view', 'count(1) as cnt', $where);
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}
	
	function get_employee_products($where = null, $limit = null, $orderby = null, $fields = null) 
	{
		$query = $this->fetch('employee_products_view', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
    }

	function insert_employee_product($data) 
	{
		return $this->insert('employee_products', $data);
	}

	function update_employee_products($data, $where) 
	{
		return $this->update('employee_products', $data, $where);
	}

	function delete_employee_products($where) 
	{
		return $this->delete('employee_products', $where);
	}
	
	function get_employee_products_by_employee_id($employee_id) 
	{
		$result = $this->get_employee_products(array('employee_id' => $employee_id));
		$row = NULL;
		if (count($result) > 0) {
			$row = $result;
		}
		return $row;
	}
	
	function get_employee_products_by_employee_id_and_product_id($employee_id,$product_id) 
	{
		$result = $this->get_employee_products(array('employee_id' => $employee_id,'product_id' => $product_id));
		$row = NULL;
		if (count($result) > 0) {
			$row = $result[0];
		}
		return $row;
	}
	
	function get_employee_product_count($where = null) {
		// do a sql count instead of row count
		$query = $this->fetch('employee_products_view', 'count(1) as cnt', $where);
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}
	
	function get_employee_types($where = null, $limit = null, $orderby = null, $fields = null) 
	{
		$query = $this->fetch('employee_types', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
    }

	function get_employee_type_by_id($employee_type_id) 
	{
		$result = $this->get_employee_types(array('employee_type_id' => $employee_type_id));
		$row = NULL;
		if (count($result) > 0) {
			$row = $result[0];
		}
		return $row;
	}
}