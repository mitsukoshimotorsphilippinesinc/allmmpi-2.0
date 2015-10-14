<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Systems_model extends Base_Model 
{

    function __construct() 
	{
		// call the base model constructor
		parent::__construct();

		// assign the table for this model
		$this->_TABLES = array(
			'systems' => 'ad_systems',
			'privileges'=>'ad_privileges'
		);

	}

	function get_systems($where = null, $limit = null, $orderby = null, $fields = null) 
	{
		$query = $this->fetch('systems', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
    }

	function insert_system($data) 
	{
		return $this->insert('systems', $data);
	}

	function update_systems($data, $where) 
	{
		return $this->update('systems', $data, $where);
	}

	function delete_systems($where) 
	{
		return $this->delete('systems', $where);
	}

	function get_system_by_code($code) 
	{
		$result = $this->get_systems(array('code' => $code));
		$row = NULL;
		if (count($result) > 0) {
			$row = $result[0];
		}
		return $row;
	}

	function get_system_count($where = null) {
		// do a sql count instead of row count
		$query = $this->fetch('systems', 'count(1) as cnt', $where);
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}

	function get_privileges($where = null, $limit = null, $orderby = null, $fields = null) 
	{
		$query = $this->fetch('privileges', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
	}

	function insert_privilege($data) 
	{
		return $this->insert('privileges', $data);
	}

	function update_privileges($data, $where) 
	{
		return $this->update('privileges', $data, $where);
	}

	function delete_privileges($where) 
	{
		return $this->delete('privileges', $where);
	}

	function get_privilege_by_id($privilege_id) 
	{
		$result = $this->get_privileges(array('privilege_id' => $privilege_id));
		$row = NULL;
		if (count($result) > 0) {
			$row = $result[0];
		}
		return $row;
	}

	function get_privilege_count($where = null) {
		// do a sql count instead of row count
		$query = $this->fetch('privileges', 'count(1) as cnt', $where);
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}
}