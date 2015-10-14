<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Purchase_orders_model extends Base_Model {

	function __construct()
	{
		// call the base model constructor
		parent::__construct();

		// assign the table for this model
		$this->_TABLES = array(
				'purchase_orders' => 'is_purchase_orders',
				'purchase_order_items' => 'is_purchase_order_items',
				'purchase_order_workflow' => 'is_purchase_order_workflow',
				'payment_terms' => 'rf_payment_terms',
				'po_statuses' => 'rf_purchase_order_statuses'
		);

	}

	// Purchase Orders

	function get_purchase_orders($where = null, $limit = null, $orderby = null, $fields = null)
	{
		$query = $this->fetch('purchase_orders', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
	}


	function get_purchase_order_by_id($purchase_order_id)
	{
		$result = $this->get_purchase_orders(array('purchase_order_id' => $purchase_order_id));
		$row = NULL;
		if (count($result) > 0)
		{
			$row = $result[0];
		}
		return $row;
	}

	function get_purchase_order_by_number($purchase_order_number)
	{
		$result = $this->get_purchase_orders(array('purchase_order_number' => $purchase_order_number));
		$row = NULL;
		if (count($result) > 0)
		{
			$row = $result[0];
		}
		return $row;
	}


	function get_purchase_orders_count($where = null)
	{
		// do a sql count instead of row count
		$query = $this->fetch('purchase_orders', 'count(1) as cnt', $where);
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}


	function insert_purchase_orders($data)
	{
		return $this->insert('purchase_orders', $data);
	}


	function update_purchase_orders($data, $where)
	{
		return $this->update('purchase_orders', $data, $where);
	}


	function delete_purchase_orders($where)
	{
		return $this->delete('purchase_orders', $where);
	}


	// Purchase Order Items

	function get_purchase_order_items($where = null, $limit = null, $orderby = null, $fields = null)
	{
		$query = $this->fetch('purchase_order_items', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
	}


	function insert_purchase_order_items($data)
	{
		return $this->insert('purchase_order_items', $data);
	}


	function update_purchase_order_items($data, $where)
	{
		return $this->update('purchase_order_items', $data, $where);
	}


	function delete_purchase_order_items($where)
	{
		return $this->delete('purchase_order_items', $where);
	}


	// Purchase Order Workflow

	function get_purchase_order_workflows($where = null, $limit = null, $orderby = null, $fields = null)
	{
		$query = $this->fetch('purchase_order_workflow', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
	}

	function insert_purchase_order_workflow($data)
	{
		return $this->insert('purchase_order_workflow', $data);
	}

	function update_purchase_order_workflow($data, $where)
	{
		return $this->update('purchase_order_workflow', $data, $where);
	}

	function delete_purchase_order_workflow($where)
	{
		return $this->delete('purchase_order_workflow', $where);
	}
	
	function get_workflow_by_order_id($purchase_order_id) 
	{
		$result = $this->get_purchase_order_workflows(array('purchase_order_id' => $purchase_order_id));
		/*$row = NULL;
		if (count($result) > 0) {
			$row = $result[0];
		}*/
		return $result;
	}
	
	function get_workflow_by_id($purchase_order_workflow_id) 
	{
		$result = $this->get_purchase_order_workflows(array('purchase_order_workflow_id' => $purchase_order_workflow_id));
		$row = NULL;
		if (count($result) > 0) {
			$row = $result[0];
		}
		return $row;
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
		$query = $this->fetch('purchase_order_workflow', 'count(1) as cnt', $where);
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}


	// Payment Terms

	function get_payment_terms($where = null, $limit = null, $orderby = null, $fields = null)
	{
		$query = $this->fetch('payment_terms', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
	}
	
	function get_payment_term_by_id($payment_term_id) 
	{
		$result = $this->get_payment_terms(array('payment_term_id' => $payment_term_id));
		$row = NULL;
		if (count($result) > 0) {
			$row = $result[0];
		}
		return $row;
	}

	function get_po_statuses($where = null, $limit = null, $orderby = null, $fields = null)
	{
		$query = $this->fetch('po_statuses', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
	}
	
	
	function get_po_status_by_id($purchase_order_status_id )
	{
		$result = $this->get_po_statuses(array('purchase_order_status_id ' => $purchase_order_status_id));
		$row = NULL;
		if (count($result) > 0)
		{
			$row = $result[0];
		}
		return $row;
	}
	
	
	function get_po_status_count($where = null)
	{
		// do a sql count instead of row count
		$query = $this->fetch('po_statuses', 'count(1) as cnt', $where);
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}
	
	
	function insert_po_status($data)
	{
		return $this->insert('po_statuses', $data);
	}
	
	
	function update_po_status($data, $where)
	{
		return $this->update('po_statuses', $data, $where);
	}
	
	
	function delete_po_status($where)
	{
		return $this->delete('po_statuses', $where);
	}


}