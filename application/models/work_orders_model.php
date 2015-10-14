<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Work_orders_model extends Base_Model 
{

    function __construct() 
	{
		// call the base model constructor
		parent::__construct();

		// assign the table for this model
		$this->_TABLES = array(
			'work_orders' => 'is_work_orders',
			'work_order_forms' => 'is_work_order_forms',
			'process_types' => 'rf_process_types',
			'transaction_code' => 'rf_is_transaction_codes'
		);

	}

	function get_work_orders($where = null, $limit = null, $orderby = null, $fields = null) 
	{
		$query = $this->fetch('work_orders', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
    }

	function insert_work_order($data) 
	{
		return $this->insert('work_orders', $data);
	}

	function update_work_order($data, $where) 
	{
		return $this->update('work_orders', $data, $where);
	}

	function delete_work_order($where) 
	{
		return $this->delete('work_orders', $where);
	}

	function get_work_order_by_tracking_number($tracking_number)
	{
		$result = $this->get_work_orders(array('tracking_number' => $tracking_number));
		$row = NULL;
		if (count($result) > 0) {
			$row = $result[0];
		}
		return $row;
	}

	function get_work_order_by_id($work_order_id) 
	{
		$result = $this->get_work_orders(array('work_order_id' => $work_order_id));
		$row = NULL;
		if (count($result) > 0) {
			$row = $result[0];
		}
		return $row;
	}
	
	function get_work_orders_count($where = null) {
		// do a sql count instead of row count
		$query = $this->fetch('work_orders', 'count(1) as cnt', $where);
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}
	
	function get_process_types($where = null, $limit = null, $orderby = null, $fields = null) 
	{
		$query = $this->fetch('process_types', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
    }

	function get_max_id($where=null)
	{
		$query = $this->fetch('work_orders', 'max(work_order_id) as max_id', $where);
		$row = $query->first_row();

		if (empty($row) || $row->max_id==NULL)
			$max_id = 0;
		else 
			$max_id = $row->max_id;
		
		$query->free_result();		
		return $max_id;		
	}


	function get_work_order_forms($where = null, $limit = null, $orderby = null, $fields = null) 
	{
		$query = $this->fetch('work_order_forms', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
    }

	function insert_work_order_form($data) 
	{
		return $this->insert('work_order_forms', $data);
	}

	function update_work_order_form($data, $where) 
	{
		return $this->update('work_order_forms', $data, $where);
	}

	function delete_work_order_form($where) 
	{
		return $this->delete('work_order_forms', $where);
	}
	
	function get_work_order_form_by_id($work_order_form_id) 
	{
		$result = $this->get_work_order_forms(array('work_order_form_id' => $work_order_form_id));
		$row = NULL;
		if (count($result) > 0) {
			$row = $result[0];
		}
		return $row;
	}

	function get_work_order_forms_by_tracking_number($tracking_number,$exclude = null)
	{
		
		$where = "`tracking_number` = '{$tracking_number}'";
		if(!empty($exclude))
		{
			$where .= " AND `process_type_id` NOT IN ({$exclude})";
		}

		$query = $this->get_work_order_forms($where,null,"insert_timestamp DESC");
		$results = NULL;
		if (count($query) > 0) {
			$results = $query;
		}
		return $results;
	}

	function get_tracking_numbers($tracking_number)
	{

		// clear previous get request
		$this->db->flush_cache();

		$this->db->distinct();
		$this->db->select('tracking_number');
		$this->db->like('tracking_number',$tracking_number,'both');
		

		// set table to use
		$this->db->from($this->_TABLES['work_orders']);
		$result = $this->db->get();
		
		$row = $result->result();
		$result->free_result();
		return $row;
	}
	
	function get_transaction_codes($where = null, $limit = null, $orderby = null, $fields = null)
	{
		$query = $this->fetch('transaction_code', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
	}
	
	function get_transaction_code_by_id($where = null, $limit = null, $orderby = null, $fields = null) 
	{
		$query = $this->fetch('transaction_code', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
				
		if (count($row) > 0) {
			$row = $row[0];
		}
		return $row;
    }
}