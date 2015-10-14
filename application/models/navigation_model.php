<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Navigation_model extends Base_Model 
{

    function __construct() 
	{
		// call the base model constructor
		parent::__construct();

		// assign the table for this model
		$this->_TABLES = array(
			'navigation' => 'sa_navigation'
		);

	}

	function get_navigation($where = null, $limit = null, $orderby = null, $fields = null) 
	{
		$query = $this->fetch('navigation', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
    }
	
	function insert_navigation($data) 
	{
		return $this->insert('navigation', $data);
	}

	function update_navigation($data, $where) 
	{
		return $this->update('navigation', $data, $where);
	}

	function delete_navigation($where) 
	{
		return $this->delete('navigation', $where);
	}

	function get_navigation_by_id($navigation_id) 
	{
		$result = $this->get_navigation(array('navigation_id' => $navigation_id), null, "priority_order ASC");
		$row = NULL;
		if (count($result) > 0) {
			$row = $result[0];
		}
		return $row;
	}
	
	function get_navigation_by_system($system) 
	{
		$where = array('system_code' => $system);
		
		return $this->get_navigation($where,null,"ordering ASC");
	}

	function get_navigation_count($where = null) {
		// do a sql count instead of row count
		$query = $this->fetch('navigation', 'count(1) as cnt', $where);
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}

	function get_navigation_headers_by_system_code($system_code)
	{
		$result = $this->get_navigation(array('system_code' => $system_code));
		/*$row = NULL;
		if (count($result) > 0) {
			$row = $result[0];
		}*/
		return $result;
	}

	function get_navigation_by_system_and_ordering($system,$ordering)
	{
		$result = $this->get_navigation(array('system_code' => $system,'ordering' => $ordering));
		$row = NULL;
		if (count($result) > 0) {
			$row = $result[0];
		}
		return $row;
	}

	function get_section_links_count($system_code, $parent_id)
	{
		// do a sql count instead of row count
		$query = $this->fetch('navigation', 'count(1) as cnt', array('type' => 'HEADER'));
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}

	function get_system_max_order($system)
	{
		$query = $this->fetch('navigation', 'count(1) as cnt', array('system_code' => $system));
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}

}