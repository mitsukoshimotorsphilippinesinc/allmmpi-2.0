<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Dpr_model extends Base_Model 
{

    function __construct() 
	{
		// call the base model constructor
		parent::__construct();

		$this->db = $this->load->database('dpr', TRUE);

		// assign the table for this model
		$this->_TABLES = array(			
			'department_module' => 'rf_department_module',
			'department_module_submodule' => 'rf_department_module_submodule',
			'form' => 'rf_form_type',
			'printing_press' => 'rf_printing_press',
			'request_summary' => 'tr_request_summary',
			'request_detail' => 'tr_request_detail',
			'booklet' => 'is_booklet',
			'branch_rack_location' => 'rf_branch_rack_location',
			'branch_rack_location_view' => 'rf_branch_rack_location_view',
			'action_log' => 'at_action_log',
			'job' => 'et_job',
			'job_view' => 'et_job_view',
			'job_type' => 'rf_job_type',			
			'release_summary' => 'tr_release_summary',
			'release_detail' => 'tr_release_detail'
		);

	}

	// ========================================================================
	// rf_department_module
	function get_department_module($where = null, $limit = null, $orderby = null, $fields = null) 
	{
		$query = $this->fetch('department_module', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
    }

	function insert_department_module($data) 
	{
		return $this->insert('department_module', $data);
	}

	function update_department_module($data, $where) 
	{
		return $this->update('department_module', $data, $where);
	}

	function delete_department_module($where) 
	{
		return $this->delete('department_module', $where);
	}

	function get_department_module_by_id($department_module_id) 
	{
		$result = $this->get_department_module(array('department_module_id' => $department_module_id));
		$row = NULL;
		if (count($result) > 0) {
			$row = $result[0];
		}
		return $row;
	}

	function get_department_module_by_code($module_code) 
	{
		$result = $this->get_department_module(array('module_code' => $module_code));
		$row = NULL;
		if (count($result) > 0) {
			$row = $result[0];
		}
		return $row;
	}

	function get_department_module_by_segment_name($segment_name) 
	{
		$result = $this->get_department_module(array('segment_name' => $segment_name));
		$row = NULL;
		if (count($result) > 0) {
			$row = $result[0];
		}
		return $row;
	}
	
	function get_department_module_count($where = null) {
		// do a sql count instead of row count
		$query = $this->fetch('department_module', 'count(1) as cnt', $where);
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}	

	function get_department_module_by_segment($segment_name) 
	{
		$result = $this->get_department_module(array('segment_name' => $segment_name));
		$row = NULL;
		if (count($result) > 0) {
			$row = $result[0];
		}
		return $row;
	}

	// ========================================================================
	// ========================================================================
	// department_module_submodule
	function get_department_module_submodule($where = null, $limit = null, $orderby = null, $fields = null) 
	{
		$query = $this->fetch('department_module_submodule', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
    }

	function insert_department_module_submodule($data) 
	{
		return $this->insert('department_module_submodule', $data);
	}

	function update_department_module_submodule($data, $where) 
	{
		return $this->update('department_module_submodule', $data, $where);
	}

	function delete_department_module_submodule($where) 
	{
		return $this->delete('department_module_submodule', $where);
	}

	function get_department_module_submodule_by_id($department_module_submodule_id) 
	{
		$result = $this->get_department_module_submodule(array('department_module_submodule_id' => $department_module_submodule_id));
		$row = NULL;
		if (count($result) > 0) {
			$row = $result[0];
		}
		return $row;
	}

	function get_department_module_submodule_by_submodule_url($submodule_url) 
	{
		$result = $this->get_department_module_submodule(array('submodule_url' => '/' . $submodule_url));
		$row = NULL;
		if (count($result) > 0) {
			$row = $result[0];
		}
		return $row;
	}
		
	function get_department_module_submodule_count($where = null) {
		// do a sql count instead of row count
		$query = $this->fetch('department_module_submodule', 'count(1) as cnt', $where);
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}

	// ========================================================================
	// ========================================================================
	// rf_form_type
	function get_form_type($where = null, $limit = null, $orderby = null, $fields = null) {
		$query = $this->fetch('form', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;	
	}

	function get_form_type_by_id($form_type_id) 
	{
		$result = $this->get_form_type(array('form_type_id' => $form_type_id));
		$row = NULL;
		if (count($result) > 0) {
			$row = $result[0];
		}
		return $row;
	}

	function update_form_type($data,$where)
	{
		return $this->update('form', $data, $where);
	}

	function insert_form_type($data) 
	{
		return $this->insert('form', $data);
	}

	function get_form_by_id($form_type_id) 
	{
		$result = $this->get_form_type(array('form_type_id' => $form_type_id));
		$row = NULL;
		if (count($result) > 0) {
			$row = $result[0];
		}
		return $row;
	}
	// ========================================================================
	// ========================================================================
	// rf_printing_press
	function get_press_name($where = null, $limit = null, $orderby = null, $fields = null) {
		$query = $this->fetch('printing_press', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;	
	}


	function get_printing_press($where = null, $limit = null, $orderby = null, $fields = null) {
		$query = $this->fetch('printing_press', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;	
	}

	function insert_printing_press($data) 
	{
		return $this->insert('printing_press', $data);
	}

	function update_printing_press($data, $where) 
	{
		return $this->update('printing_press', $data, $where);
	}

	function delete_printing_press($where) 
	{
		return $this->delete('printing_press', $where);
	}

	function get_printing_press_by_id($printing_press_id) 
	{
		$result = $this->get_printing_press(array('printing_press_id' => $printing_press_id));
		$row = NULL;
		if (count($result) > 0) {
			$row = $result[0];
		}
		return $row;
	}

	function get_printing_press_count($where = null) {
		// do a sql count instead of row count
		$query = $this->fetch('printing_press', 'count(1) as cnt', $where);
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}

	// ========================================================================
	// ========================================================================
	// tr_request_summary
	function get_request_summary($where = null, $limit = null, $orderby = null, $fields = null) 
	{
		$query = $this->fetch('request_summary', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
    }

	function insert_request_summary($data) 
	{
		return $this->insert('request_summary', $data);
	}

	function update_request_summary($data, $where) 
	{
		return $this->update('request_summary', $data, $where);
	}

	function delete_request_summary($where) 
	{
		return $this->delete('request_summary', $where);
	}

	function get_request_summary_by_id($request_summary_id) 
	{
		$result = $this->get_request_summary(array('request_summary_id' => $request_summary_id));
		$row = NULL;
		if (count($result) > 0) {
			$row = $result[0];
		}
		return $row;
	}

	function get_request_summary_by_request_code($request_code) 
	{
		$result = $this->get_request_summary(array('request_code' => $request_code));
		$row = NULL;
		if (count($result) > 0) {
			$row = $result[0];
		}
		return $row;
	}
	
	function get_request_summary_count($where = null) {
		// do a sql count instead of row count
		$query = $this->fetch('request_summary', 'count(1) as cnt', $where);
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}	

	// ========================================================================
	// ========================================================================
	// tr_request_detail
	function get_request_detail($where = null, $limit = null, $orderby = null, $fields = null) 
	{
		$query = $this->fetch('request_detail', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
    }

	function insert_new_request($data){
		return $this->insert('request_detail', $data);
	}

	function insert_request_detail($data)
	{
		return $this->insert('request_detail', $data);
	}

	function update_request_detail($data, $where) 
	{
		return $this->update('request_detail', $data, $where);
	}

	function delete_request_detail($where) 
	{
		return $this->delete('request_detail', $where);
	}

	function get_request_detail_by_id($request_detail_id) 
	{
		$result = $this->get_request_detail(array('request_detail_id' => $request_detail_id));
		$row = NULL;
		if (count($result) > 0) {
			$row = $result[0];
		}
		return $row;
	}

	function get_request_detail_by_request_code($request_code) 
	{
		$result = $this->get_request_detail(array('request_code' => $request_code));
		$row = NULL;
		if (count($result) > 0) {
			$row = $result[0];
		}
		return $row;
	}
	
	function get_request_detail_count($where = null) 
	{
		// do a sql count instead of row count
		$query = $this->fetch('request_detail', 'count(1) as cnt', $where);
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}	

	// ========================================================================
	// ========================================================================
	// is_booklet
	function get_booklet($where = null, $limit = null, $orderby = null, $fields = null) 
	{
		$query = $this->fetch('booklet', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
    }

	function insert_booklet($data) 
	{
		return $this->insert('booklet', $data);
	}

	function update_booklet($data, $where) 
	{
		return $this->update('booklet', $data, $where);
	}

	function delete_booklet($where) 
	{
		return $this->delete('booklet', $where);
	}

	function get_booklet_by_id($booklet_id) 
	{
		$result = $this->get_booklet(array('booklet_id' => $booklet_id));
		$row = NULL;
		if (count($result) > 0) {
			$row = $result[0];
		}
		return $row;
	}
	
	function get_booklet_count($where = null) 
	{
		// do a sql count instead of row count
		$query = $this->fetch('booklet', 'count(1) as cnt', $where);
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}	

	// ========================================================================
	// ========================================================================
	// rf_branch_rack_location
	function get_branch_rack_location($where = null, $limit = null, $orderby = null, $fields = null) 
	{
		$query = $this->fetch('branch_rack_location', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
    }

	function insert_branch_rack_location($data) 
	{
		return $this->insert('branch_rack_location', $data);
	}

	function update_branch_rack_location($data, $where) 
	{
		return $this->update('branch_rack_location', $data, $where);
	}

	function delete_branch_rack_location($where) 
	{
		return $this->delete('branch_rack_location', $where);
	}

	function get_branch_rack_location_by_id($branch_rack_location_id) 
	{
		$result = $this->get_branch_rack_location(array('branch_rack_location_id' => $branch_rack_location_id));
		$row = NULL;
		if (count($result) > 0) {
			$row = $result[0];
		}
		return $row;
	}

	function get_branch_rack_location_by_branch_id($branch_id) 
	{
		$result = $this->get_branch_rack_location(array('branch_id' => $branch_id));
		$row = NULL;
		if (count($result) > 0) {
			$row = $result[0];
		}
		return $row;
	}
	
	function get_branch_rack_location_count($where = null) 
	{
		// do a sql count instead of row count
		$query = $this->fetch('branch_rack_location', 'count(1) as cnt', $where);
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}	

	// ========================================================================
	// ========================================================================
	// rf_branch_rack_location_view
	function get_branch_rack_location_view($where = null, $limit = null, $orderby = null, $fields = null) 
	{
		$query = $this->fetch('branch_rack_location_view', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
    }

	function get_branch_rack_location_view_by_branch_rack_location_id($branch_rack_location_id) 
	{
		$result = $this->get_branch_rack_location_view(array('branch_rack_location_id' => $branch_rack_location_id));
		$row = NULL;
		if (count($result) > 0) {
			$row = $result[0];
		}
		return $row;
	}

	function get_branch_rack_location_view_by_branch_id($branch_id) 
	{
		$result = $this->get_branch_rack_location_view(array('branch_id' => $branch_id));
		$row = NULL;
		if (count($result) > 0) {
			$row = $result[0];
		}
		return $row;
	}
	
	function get_branch_rack_location_view_count($where = null) 
	{
		// do a sql count instead of row count
		$query = $this->fetch('branch_rack_location_view', 'count(1) as cnt', $where);
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}	

	// ========================================================================
	// ========================================================================
	// tr_action_LOG	
	function insert_log($data) 
	{
        if(isset($data['remarks'])) {
            $data['remarks'] .= "\n\n URL:" . $this->uri->uri_string();
        } else {
            $data['remarks'] = "URL:" . $this->uri->uri_string();
        }	

	    $result = $this->insert('action_log', $data);
    }

    function get_form_type_count($where = null) {
		// do a sql count instead of row count
		$query = $this->fetch('form', 'count(1) as cnt', $where);
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}	

	function get_release_summary($where = null, $limit = null, $orderby = null, $fields = null) 
	{
		$query = $this->fetch('release_summary', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
    }

    function get_release_detail($where = null, $limit = null, $orderby = null, $fields = null) 
	{
		$query = $this->fetch('release_detail', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
    }

    function get_release_summary_count($where = null) {
		// do a sql count instead of row count
		$query = $this->fetch('release_summary', 'count(1) as cnt', $where);
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}	

	function insert_release_summary($data) 
	{
		return $this->insert('release_summary', $data);
	}

	function insert_release_detail($data) 
	{
		return $this->insert('release_detail', $data);
	}

	function delete_release_detail($where) 
	{
		return $this->delete('release_detail', $where);
	}

	function update_release_summary($data, $where) 
	{
		return $this->update('release_summary', $data, $where);
	}

    // ========================================================================
    // ========================================================================
    // et_job
    function get_job($where = null, $limit = null, $orderby = null, $fields = null) 
	{
		$query = $this->fetch('job_view', $fields, $where, $orderby, $limit);
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
		$result = $this->get_jobs(array('job_type_id' => $job_type_id));
		$row = NULL;
		if (count($result) > 0) {
			$row = $result[0];
		}
		return $row;
	}
	// ========================================================================	
	

}