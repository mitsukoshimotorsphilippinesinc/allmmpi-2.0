<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Hrdatabase_model extends Base_Model 
{

    function __construct() 
	{
		// call the base model constructor
		parent::__construct();

		$this->db = $this->load->database('hrdatabase', TRUE);

		// assign the table for this model
		$this->_TABLES = array(
			'employment_requirements' => 'Employment_Requirements',
		);

	}

	// pm_employment_requirement
	function get_employment_requirements($where = null, $limit = null, $orderby = null, $fields = null) 
	{
		$query = $this->fetch('employment_requirements', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
    }

	function insert_employment_requirements($data) 
	{
		return $this->insert('employment_requirements', $data);
	}

	function update_employment_requirements($data, $where) 
	{
		return $this->update('employment_requirements', $data, $where);
	}

	function delete_employment_requirements($where) 
	{
		return $this->delete('employment_requirements', $where);
	}

	function get_employment_requirements_by_id($employment_requirement_id) 
	{
		$result = $this->get_employment_requirement(array('IDNo' => $employment_requirement_id));
		$row = NULL;
		if (count($result) > 0) {
			$row = $result[0];
		}
		return $row;
	}
	
	function get_employment_requirements_count($where = null) {
		// do a sql count instead of row count
		$query = $this->fetch('employment_requirements', 'count(1) as cnt', $where);
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}
	

}