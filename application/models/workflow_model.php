<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Workflow_model extends Base_Model 
{

    function __construct() 
	{
		// call the base model constructor
		parent::__construct();

		// assign the table for this model
		$this->_TABLES = array(
			'statuses' => 'rf_workflow_statuses',
			'defaults' => 'is_workflow_defaults',
			'actions' => 'rf_workflow_action_types'
		);

	}

	function get_defaults($where = null, $limit = null, $orderby = null, $fields = null) 
	{
		$query = $this->fetch('defaults', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
    }

	function insert_default($data) 
	{
		return $this->insert('defaults', $data);
	}

	function update_default($data, $where) 
	{
		return $this->update('defaults', $data, $where);
	}

	function delete_default($where) 
	{
		return $this->delete('defaults', $where);
	}

	function get_default_by_id($workflow_default_id) 
	{
		$result = $this->get_defaults(array('workflow_default_id' => $workflow_default_id));
		$row = NULL;
		if (count($result) > 0) {
			$row = $result[0];
		}
		return $row;
	}
	
	 function get_default_by_user($user_id) 
  {
    $result = $this->get_defaults(array('user_id' => $user_id));
    $row = NULL;
    if (count($result) > 0) {
      $row = $result[0];
    }
    return $row;
  }
  
  function get_default_by_action($action) 
  {
    $result = $this->get_defaults(array('action' => $action));
    $row = NULL;
    if (count($result) > 0) {
      $row = $result[0];
    }
    return $row;
  }
  
  function get_default_by_form_handled($form_handled) 
  {
    $result = $this->get_defaults(array('form_handled' => $form_handled));
    $row = NULL;
    if (count($result) > 0) {
      $row = $result[0];
    }
    return $row;
  }

  function get_defaults_count($where = null) {
    // do a sql count instead of row count
    $query = $this->fetch('defaults', 'count(1) as cnt', $where);
    $row = $query->first_row();
    $query->free_result();
    return $row->cnt;
  }
  
  
  
  
  
  
  function get_status($where = null, $limit = null, $orderby = null, $fields = null) 
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

  function get_status_by_id($workflow_status_id) 
  {
    $result = $this->get_status(array('workflow_status_id' => $workflow_status_id));
    $row = NULL;
    if (count($result) > 0) {
      $row = $result[0];
    }
    return $row;
  }
  
  function get_status_by_name($workflow_status) 
  {
    $result = $this->get_status(array('workflow_status' => $workflow_status));
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
  
  
  
  
  
  function get_actions($where = null, $limit = null, $orderby = null, $fields = null) 
  {
    $query = $this->fetch('actions', $fields, $where, $orderby, $limit);
    $row = $query->result();
    $query->free_result();
    return $row;
    }

  function insert_action($data) 
  {
    return $this->insert('actions', $data);
  }

  function update_action($data, $where) 
  {
    return $this->update('actions', $data, $where);
  }

  function delete_action($where) 
  {
    return $this->delete('actions', $where);
  }
 
  function get_action_by_id($workflow_action_type_id) 
  {
	$result = $this->get_actions(array('workflow_action_type_id' => $workflow_action_type_id));
    $row = NULL;
    if (count($result) > 0) {
      $row = $result[0];
    }
    return $row;
  }
  
  function get_action_by_type($workflow_action_type) 
  {
    $result = $this->get_actions(array('workflow_action_type' => $workflow_action_type));
    $row = NULL;
    if (count($result) > 0) {
      $row = $result[0];
    }
    return $row;
  }
  
  function get_action_count($where = null) {
    // do a sql count instead of row count
    $query = $this->fetch('actions', 'count(1) as cnt', $where);
    $row = $query->first_row();
    $query->free_result();
    return $row->cnt;
  }
	
}