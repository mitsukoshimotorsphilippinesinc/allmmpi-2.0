<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Job_model extends Base_Model 
{

    function __construct() 
	{
		// call the base model constructor
		parent::__construct();

		// assign the table for this model
		$this->_TABLES = array(
			'job' => 'et_job',
			'job_view' => 'et_job_view',
			'job_types' => 'rf_job_types'
		);

	}

	function get_job($where = null, $limit = null, $orderby = null, $fields = null) 
	{
		$query = $this->fetch('job_view', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
    }

    function get_job_by_id($job_id) 
	{
		$result = $this->get_job(array('job_id' => $job_id));
		$row = NULL;
		if (count($result) > 0) {
			$row = $result[0];
		}
		return $row;
	}

	function insert_job($data) 
	{
		return $this->insert('job', $data);
	}

	function update_job($data, $where) 
	{
		return $this->update('job', $data, $where);
	}

	function delete_job($where) 
	{
		return $this->delete('job', $where);
	}

	// job types
	function get_job_type_by_job_type_id($job_type_id)
	{
		$result = $this->get_job(array('job_type_id' => $job_type_id));
		$row = NULL;
		if (count($result) > 0) {
			$row = $result[0];
		}
		return $row;
	}

}