<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Shipping_method_model extends Base_Model 
{

    function __construct() 
	{
		// call the base model constructor
		parent::__construct();

		// assign the table for this model
		$this->_TABLES = array(
			'shipping_methods' => 'rf_shipping_methods'
		);

	}

	function get_shipping_methods($where = null, $limit = null, $orderby = null, $fields = null) 
	{
		$query = $this->fetch('shipping_methods', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
    }

	function insert_shipping_method($data) 
	{
		return $this->insert('shipping_methods', $data);
	}

	function update_shipping_method($data, $where) 
	{
		return $this->update('shipping_methods', $data, $where);
	}

	function delete_shipping_method($where) 
	{
		return $this->delete('shipping_methods', $where);
	}

	function get_shipping_method_by_id($shipping_method_id) 
	{
		$result = $this->get_shipping_methods(array('shipping_method_id' => $shipping_method_id));
		$row = NULL;
		if (count($result) > 0) {
			$row = $result[0];
		}
		return $row;
	}
	
	function get_shipping_method_by_name($shipping_method) 
	{
		$result = $this->get_users(array('shipping_method' => $shipping_method));
		$row = NULL;
		if (count($result) > 0) {
			$row = $result[0];
		}
		return $row;
	}

	function get_shipping_method_count($where = null) {
		// do a sql count instead of row count
		$query = $this->fetch('shipping_methods', 'count(1) as cnt', $where);
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}

}