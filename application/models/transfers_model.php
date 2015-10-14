<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Transfers_model extends Base_Model 
{

    function __construct() 
	{
		// call the base model constructor
		parent::__construct();

		// assign the table for this model
		$this->_TABLES = array(
			'transfers' => 'is_stocks_transfers',
			'transfer_items' => 'is_stocks_transfer_items',
			'transfer_workflow' => 'is_stocks_transfer_workflow',
			'statuses' => 'rf_stocks_transfer_statuses'
		);

	}

	function get_transfers($where = null, $limit = null, $orderby = null, $fields = null) 
	{
		$query = $this->fetch('transfers', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
    }

	function insert_transfer($data) 
	{
		return $this->insert('transfers', $data);
	}

	function update_transfer($data, $where) 
	{
		return $this->update('transfers', $data, $where);
	}

	function delete_transfer($where) 
	{
		return $this->delete('transfers', $where);
	}

	function get_transfer_by_id($stocks_transfer_id) 
	{
		$result = $this->get_transfers(array('stocks_transfer_id' => $stocks_transfer_id));
		$row = NULL;
		if (count($result) > 0) {
			$row = $result[0];
		}
		return $row;
	}
	
	function get_transfer_by_number($stocks_transfer_number) 
	{
		$result = $this->get_transfers(array('stocks_transfer_number' => $stocks_transfer_number));
		$row = NULL;
		if (count($result) > 0) {
			$row = $result[0];
		}
		return $row;
	}
	
	function get_transfer_by_order_id($purchase_order_id) 
	{
		$result = $this->get_transfers(array('purchase_order_id' => $purchase_order_id));
		$row = NULL;
		if (count($result) > 0) {
			$row = $result[0];
		}
		return $row;
	}

	function get_transfers_count($where = null) {
		// do a sql count instead of row count
		$query = $this->fetch('transfers', 'count(1) as cnt', $where);
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}
	
	function get_items($where = null, $limit = null, $orderby = null, $fields = null) 
	{
		$query = $this->fetch('transfer_items', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
    }

	function insert_items($data) 
	{
		return $this->insert('transfer_items', $data);
	}

	function update_items($data, $where) 
	{
		return $this->update('transfer_items', $data, $where);
	}

	function delete_items($where) 
	{
		return $this->delete('transfer_items', $where);
	}

	function get_item_by_transfer_id($stocks_transfer_id) 
	{
		$result = $this->get_items(array('stocks_transfer_id' => $stocks_transfer_id));
		$row = NULL;
		if (count($result) > 0) {
			$row = $result[0];
		}
		return $row;
	}
	
	function get_items_count($where = null) {
		// do a sql count instead of row count
		$query = $this->fetch('transfer_items', 'count(1) as cnt', $where);
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}
	
	function get_workflow($where = null, $limit = null, $orderby = null, $fields = null) 
	{
		$query = $this->fetch('transfer_workflow', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
    }

	function insert_workflow($data) 
	{
		return $this->insert('transfer_workflow', $data);
	}

	function update_workflow($data, $where) 
	{
		return $this->update('transfer_workflow', $data, $where);
	}

	function delete_workflow($where) 
	{
		return $this->delete('transfer_workflow', $where);
	}

	function get_workflow_by_id($stocks_transfer_workflow_id) 
	{
		$result = $this->get_workflow(array('stocks_transfer_workflow_id' => $stocks_transfer_workflow_id));
		$row = NULL;
		if (count($result) > 0) {
			$row = $result[0];
		}
		return $row;
	}
	
	function get_workflow_by_transfer_id($stocks_transfer_id) 
	{
		$result = $this->get_workflow(array('stocks_transfer_id' => $stocks_transfer_id));
		/*$row = NULL;
		if (count($result) > 0) {
			$row = $result[0];
		}*/
		return $result;
	}
	
	function get_workflow_by_user_id($user_id) 
	{
		$result = $this->get_workflow(array('user_id' => $user_id));
		/*$row = NULL;
		if (count($result) > 0) {
			$row = $result[0];
		}*/
		return $result;
	}
	
	function get_workflow_count($where = null) {
		// do a sql count instead of row count
		$query = $this->fetch('transfer_workflow', 'count(1) as cnt', $where);
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

	function get_status_by_id($stocks_transfer_status_id) 
	{
		$result = $this->get_statuses(array('stocks_transfer_status_id' => $stocks_transfer_status_id));
		$row = NULL;
		if (count($result) > 0) {
			$row = $result[0];
		}
		return $row;
	}
	
	function get_status_by_name($stocks_transfer_status) 
	{
		$result = $this->get_statuses(array('stocks_transfer_status' => $stocks_transfer_status));
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