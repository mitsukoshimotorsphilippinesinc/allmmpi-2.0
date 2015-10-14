<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Receiving_model extends Base_Model 
{

    function __construct() 
	{
		// call the base model constructor
		parent::__construct();

		// assign the table for this model
		$this->_TABLES = array(
			'receiving' => 'is_stocks_receiving',
			'statuses' => 'rf_stocks_receiving_statuses'
		);

	}

	function get_receiving($where = null, $limit = null, $orderby = null, $fields = null) 
	{
		$query = $this->fetch('receiving', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
    }

	function insert_receiving($data) 
	{
		return $this->insert('receiving', $data);
	}

	function update_receiving($data, $where) 
	{
		return $this->update('receiving', $data, $where);
	}

	function delete_receiving($where) 
	{
		return $this->delete('receiving', $where);
	}

	function get_receiving_by_id($stocks_receiving_id) 
	{
		$result = $this->get_receiving(array('stocks_receiving_id' => $stocks_receiving_id));
		$row = NULL;
		if (count($result) > 0) {
			$row = $result[0];
		}
		return $row;
	}
	
	function get_receiving_by_order_id($purchase_order_id) 
	{
		$result = $this->get_receiving(array('purchase_order_id' => $purchase_order_id));
		$row = NULL;
		if (count($result) > 0) {
			$row = $result[0];
		}
		return $row;
	}
	
	function get_receiving_by_transfer_id($stocks_transfer_id) 
	{
		$result = $this->get_receiving(array('stocks_transfer_id' => $stocks_transfer_id));
		$row = NULL;
		if (count($result) > 0) {
			$row = $result[0];
		}
		return $row;
	}
	
	function get_receiving_by_number($stocks_receiving_number) 
	{
		$result = $this->get_receiving(array('stocks_receiving_number' => $stocks_receiving_number));
		$row = NULL;
		if (count($result) > 0) {
			$row = $result[0];
		}
		return $row;
	}

	function get_receiving_count($where = null) {
		// do a sql count instead of row count
		$query = $this->fetch('receiving', 'count(1) as cnt', $where);
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}
	
	function get_statuses($where = null, $limit = null, $orderby = null, $fields = null) 
	{
		$query = $this->fetch('statuses', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
    }

	function insert_status($data) 
	{
		return $this->insert('statuses', $data);
	}

	function update_status($data, $where) 
	{
		return $this->update('statuses', $data, $where);
	}

	function delete_status($where) 
	{
		return $this->delete('statuses', $where);
	}

	function get_status_by_id($stocks_receiving_status_id) 
	{
		$result = $this->get_statuses(array('stocks_receiving_status_id' => $stocks_receiving_status_id));
		$row = NULL;
		if (count($result) > 0) {
			$row = $result[0];
		}
		return $row;
	}
	
	function get_status_by_name($stocks_receiving_status) 
	{
		$result = $this->get_statuses(array('stocks_receiving_status' => $stocks_receiving_status));
		$row = NULL;
		if (count($result) > 0) {
			$row = $result[0];
		}
		return $row;
	}

	function get_status_count($where = null) {
		// do a sql count instead of row count
		$query = $this->fetch('statuses', 'count(1) as cnt', $where);
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}

}