<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Requisition_model extends Base_Model 
{

    function __construct() 
	{
		// call the base model constructor
		parent::__construct();

		// assign the table for this model
		$this->_TABLES = array(
			'requisition' => 'is_requisition_forms',
			'requisition_workflow' => 'is_requisition_form_workflow',
			'statuses' => 'rf_requisition_form_statuses'
		);

	}

	function get_requisition($where = null, $limit = null, $orderby = null, $fields = null) 
	{
		$query = $this->fetch('requisition', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
    }

	function insert_requisition($data) 
	{
		return $this->insert('requisition', $data);
	}

	function update_requisition($data, $where) 
	{
		return $this->update('requisition', $data, $where);
	}

	function delete_requisition($where) 
	{
		return $this->delete('requisition', $where);
	}

	function get_requisition_by_id($requisition_form_id) 
	{
		$result = $this->get_requisition(array('requisition_form_id' => $requisition_form_id));
		$row = NULL;
		if (count($result) > 0) {
			$row = $result[0];
		}
		return $row;
	}

	function get_requisition_by_number($requisition_form_number) 
	{
		$result = $this->get_requisition(array('requisition_form_number' => $requisition_form_number));
		$row = NULL;
		if (count($result) > 0) {
			$row = $result[0];
		}
		return $row;
	}

	function get_requisition_count($where = null) {
		// do a sql count instead of row count
		$query = $this->fetch('requisition', 'count(1) as cnt', $where);
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}
	
	
	
	
	function get_workflow($where = null, $limit = null, $orderby = null, $fields = null) 
	{
		$query = $this->fetch('requisition_workflow', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
    }

	function insert_workflow($data) 
	{
		return $this->insert('requisition_workflow', $data);
	}

	function update_workflow($data, $where) 
	{
		return $this->update('requisition_workflow', $data, $where);
	}

	function delete_workflow($where) 
	{
		return $this->delete('requisition_workflow', $where);
	}

	function get_workflow_by_id($requisition_form_workflow_id) 
	{
		$result = $this->get_workflow(array('requisition_form_workflow_id' => $requisition_form_workflow_id));
		$row = NULL;
		if (count($result) > 0) {
			$row = $result[0];
		}
		return $row;
	}
	
	function get_workflow_by_requisition_id($requisition_form_id) 
	{
		$result = $this->get_workflow(array('requisition_form_id' => $requisition_form_id));
		/*$row = NULL;
		if (count($result) > 0) {
			$row = $result[0];
		}*/
		return $result;
	}
	
	function get_workflow_count($where = null) {
		// do a sql count instead of row count
		$query = $this->fetch('requisition_workflow', 'count(1) as cnt', $where);
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

	function get_status_by_id($requisition_form_status_id) 
	{
		$result = $this->get_statuses(array('requisition_form_status_id' => $requisition_form_status_id));
		$row = NULL;
		if (count($result) > 0) {
			$row = $result[0];
		}
		return $row;
	}
	
	function get_status_by_name($requisition_form_status) 
	{
		$result = $this->get_statuses(array('requisition_form_status' => $requisition_form_status));
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