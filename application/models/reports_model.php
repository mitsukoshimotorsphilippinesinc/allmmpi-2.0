<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Reports_model extends Base_Model 
{

    function __construct() 
	{
		// call the base model constructor
		parent::__construct();

		// assign the table for this model
		$this->_TABLES = array(
			'member_order_reports' => 'rt_member_orders',
			'member_order_reports_view' => 'rt_member_orders_view',
			'facility_item_reports' => 'rt_facility_item_reports',
			'facility_item_reports_view' => 'rt_facility_item_reports_view',
			'facility_items_daily' => 'rt_facility_items',
			'facility_items_daily_view' => 'rt_facility_items_view',
			'facility_items_releasing' => 'rt_facility_items_releasing_view',
			'facility_items_receiving' => 'rt_facility_items_receiving_view',
			'payment_transactions' => 'rt_payment_transactions'
		);

	}

	function get_member_order_reports($where = null, $limit = null, $orderby = null, $fields = null)
	{
		$query = $this->fetch('member_order_reports_view', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
	}
	
	function get_facility_item_reports($where = null, $limit = null, $orderby = null, $fields = null)
	{
		$query = $this->fetch('facility_item_reports_view', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
	}
	
	function get_facility_item_reports_by_id($facility_item_report_id)
	{
		$result = $this->get_facility_item_reports(array('facility_item_report_id' => $facility_item_report_id));
		$row = NULL;
		if (count($result) > 0)
		{
			$row = $result[0];
		}
		return $row;
	}
	
	function insert_item_report($data)
	{
		return $this->insert('facility_item_reports', $data);
	}
	
	function update_item_report($data, $where)
	{
		return $this->update('facility_item_reports', $data, $where);
	}
	
	function get_facility_items_daily($where = null, $limit = null, $orderby = null, $fields = null) 
	{
		$query = $this->fetch('facility_items_daily_view', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
    }

	function get_facility_item_daily_by_facility_id($facility_id) 
	{
		$result = $this->get_facility_items_daily(array('facility_id' => $facility_id));
		$row = NULL;
		if (count($result) > 0) {
			$row = $result[0];
		}
		return $row;
	}
	
	function get_facility_items_daily_by_item_id($item_id) 
	{
		$result = $this->get_facility_items_daily(array('item_id' => $item_id));
		$row = NULL;
		if (count($result) > 0) {
			$row = $result[0];
		}
		return $row;
	}
	
	function get_items_released_daily($where = null, $limit = null, $orderby = null, $fields = null)
	{
		$query = $this->fetch('facility_items_releasing', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
	}
	
	function get_items_received_daily($where = null, $limit = null, $orderby = null, $fields = null)
	{
		$query = $this->fetch('facility_items_receiving', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
	}
	
	function get_payment_transactions($where = null, $limit = null, $orderby = null, $fields = null)
	{
		$query = $this->fetch('payment_transactions', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
	}
	
	function get_payment_transactions_by_transaction_id($transaction_id) 
	{
		$result = $this->get_payment_transactions(array('transaction_id' => $transaction_id));
		$row = NULL;
		if (count($result) > 0) {
			$row = $result;
		}
		return $row;
	}
	
	function get_payment_transactions_by_transaction_code($transaction_code) 
	{
		$result = $this->get_payment_transactions(array('transaction_code' => $transaction_code));
		$row = NULL;
		if (count($result) > 0) {
			$row = $result;
		}
		return $row;
	}
	
	function get_payment_transactions_by_transaction_type($transaction_type) 
	{
		$result = $this->get_payment_transactions(array('transaction_type' => $transaction_type));
		$row = NULL;
		if (count($result) > 0) {
			$row = $result;
		}
		return $row;
	}

}