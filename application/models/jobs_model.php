<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Jobs_model extends Base_Model 
{

    function __construct() 
	{
		// call the base model constructor
		parent::__construct();

		// assign the table for this model
		$this->_TABLES = array(
			'jobs' => 'et_jobs',
			'jobs_view' => 'et_jobs_view',
			'job_types' => 'rf_job_types'
		);

	}

	function get_jobs($where = null, $limit = null, $orderby = null, $fields = null) 
	{
		$query = $this->fetch('jobs_view', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
    }

    function get_job_by_id($job_id) 
	{
		$result = $this->get_jobs(array('job_id' => $job_id));
		$row = NULL;
		if (count($result) > 0) {
			$row = $result[0];
		}
		return $row;
	}

	function insert_job($data) 
	{
		return $this->insert('jobs', $data);
	}

	function update_jobs($data, $where) 
	{
		return $this->update('jobs', $data, $where);
	}

	function delete_jobs($where) 
	{
		return $this->delete('jobs', $where);
	}

	// job types
	function get_job_type_by_job_type_id($job_type_id)
	{
		$result = $this->get_jobs(array('job_type_id' => $job_type_id));
		$row = NULL;
		if (count($result) > 0) {
			$row = $result[0];
		}
		return $row;
	}

}