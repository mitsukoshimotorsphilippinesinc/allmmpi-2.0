<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Human_relations_model extends Base_Model 
{

    function __construct() 
	{
		// call the base model constructor
		parent::__construct();

		$this->db = $this->load->database('human_relations', TRUE);

		// assign the table for this model
		$this->_TABLES = array(
			'employee_requirement' => 'pm_employee_requirement',
			'employment_requirement' => 'rf_employment_requirement',
			'personal_information' => 'pm_personal_information',
			'employment_information' => 'pm_employment_information',
			'employment_information_view' => 'pm_employment_information_view',
			'position' => 'rf_position',
			'company' => 'rf_company',
			'department' => 'rf_department',
			'branch' => 'rf_branch',
			'agency' => 'rf_agency',
			'job_grade_level' => 'rf_job_grade_level',
			'employment_status' => 'rf_employment_status',
			's4s' => 'el_s4s',
			's4s_asset' => 'el_s4s_asset',
			's4s_acceptance' => 'tr_s4s_acceptance',
			's4s_position' => 'el_s4s_position',
			's4s_position_view' => 'el_s4s_position_view',
		);

	}

	// pm_employee_requirement
	function get_employee_requirement($where = null, $limit = null, $orderby = null, $fields = null) 
	{
		$query = $this->fetch('employee_requirement', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
    }

	function insert_employee_requirement($data) 
	{
		return $this->insert('employee_requirement', $data);
	}

	function update_employee_requirement($data, $where) 
	{
		return $this->update('employee_requirement', $data, $where);
	}

	function delete_employee_requirement($where) 
	{
		return $this->delete('employee_requirement', $where);
	}

	function get_employee_requirement_by_id($employee_requirement_id) 
	{
		$result = $this->get_employment_requirement(array('employee_requirement_id' => $employee_requirement_id));
		$row = NULL;
		if (count($result) > 0) {
			$row = $result[0];
		}
		return $row;
	}
	
	function get_employee_requirement_count($where = null) {
		// do a sql count instead of row count
		$query = $this->fetch('employment_requirement', 'count(1) as cnt', $where);
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}
	
	// rf_employment_requirement
	function get_employment_requirement($where = null, $limit = null, $orderby = null, $fields = null) 
	{
		$query = $this->fetch('employment_requirement', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
    }

	function insert_employment_requirement($data) 
	{
		return $this->insert('employment_requirement', $data);
	}

	function update_employment_requirement($data, $where) 
	{
		return $this->update('employment_requirement', $data, $where);
	}

	function delete_employment_requirement($where) 
	{
		return $this->delete('employment_requirement', $where);
	}

	function get_employment_requirement_by_id($employment_requirement_id) 
	{
		$result = $this->get_employment_requirement(array('employment_requirement_id' => $employment_requirement_id));
		$row = NULL;
		if (count($result) > 0) {
			$row = $result[0];
		}
		return $row;
	}
	
	function get_employment_requirement_count($where = null) {
		$query = $this->fetch('employment_requirement', 'count(1) as cnt', $where);
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}

	// pm_personal_information
	function get_personal_information($where = null, $limit = null, $orderby = null, $fields = null) 
	{
		$query = $this->fetch('personal_information', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
    }

	function insert_personal_information($data) 
	{
		return $this->insert('personal_information', $data);
	}

	function update_personal_information($data, $where) 
	{
		return $this->update('personal_information', $data, $where);
	}

	function delete_personal_information($where) 
	{
		return $this->delete('personal_information', $where);
	}

	function get_personal_information_by_id($personal_information_id) 
	{
		$result = $this->get_personal_information(array('personal_information_id' => $personal_information_id));
		$row = NULL;
		if (count($result) > 0) {
			$row = $result[0];
		}
		return $row;
	}
	
	function get_personal_information_count($where = null) {
		$query = $this->fetch('personal_information', 'count(1) as cnt', $where);
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}

	// ============================================================================================
	// pm_employment_information
	function get_employment_information($where = null, $limit = null, $orderby = null, $fields = null) 
	{
		$query = $this->fetch('employment_information', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
    }

	function insert_employment_information($data) 
	{
		return $this->insert('employment_information', $data);
	}

	function update_employment_information($data, $where) 
	{
		return $this->update('employment_information', $data, $where);
	}

	function delete_employment_information($where) 
	{
		return $this->delete('employment_information', $where);
	}

	function get_employment_information_by_id($id_number) 
	{
		$result = $this->get_employment_information(array('id_number' => $id_number));
		$row = NULL;
		if (count($result) > 0) {
			$row = $result[0];
		}
		return $row;
	}
	
	function get_employment_information_count($where = null) {
		$query = $this->fetch('employment_information', 'count(1) as cnt', $where);
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}
	// ==============================================================
	// ==============================================================
	// pm_employment_information_view
	function get_employment_information_view($where = null, $limit = null, $orderby = null, $fields = null) 
	{
		$query = $this->fetch('employment_information_view', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
    }

	function get_employment_information_view_by_id($id_number) 
	{
		$result = $this->get_employment_information_view(array('id_number' => $id_number));
		$row = NULL;
		if (count($result) > 0) {
			$row = $result[0];
		}
		return $row;
	}
	
	function get_employment_information_view_count($where = null) {
		$query = $this->fetch('employment_information_view', 'count(1) as cnt', $where);
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}
	// ==============================================================
	// ==============================================================
	// rf_position
	function get_position($where = null, $limit = null, $orderby = null, $fields = null) 
	{
		$query = $this->fetch('position', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
    }

	function insert_position($data) 
	{
		return $this->insert('position', $data);
	}

	function update_position($data, $where) 
	{
		return $this->update('position', $data, $where);
	}

	function delete_position($where) 
	{
		return $this->delete('position', $where);
	}
	
	function get_position_count($where = null) {
		$query = $this->fetch('position', 'count(1) as cnt', $where);
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}

	function get_position_by_id($position_id) 
	{
		$result = $this->get_position(array('position_id' => $position_id));
		$row = NULL;
		if (count($result) > 0) {
			$row = $result[0];
		}
		return $row;
	}
	// ==============================================================
	// ==============================================================
	// rf_company
	function get_company($where = null, $limit = null, $orderby = null, $fields = null) 
	{
		$query = $this->fetch('company', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
    }

	function insert_company($data) 
	{
		return $this->insert('company', $data);
	}

	function update_company($data, $where) 
	{
		return $this->update('company', $data, $where);
	}

	function delete_company($where) 
	{
		return $this->delete('company', $where);
	}

	function get_company_by_id($company_id) 
	{
		$result = $this->get_company(array('company_id' => $company_id));
		$row = NULL;
		if (count($result) > 0) {
			$row = $result[0];
		}
		return $row;
	}
	// ==============================================================
	// ==============================================================
	// rf_department
	function get_department($where = null, $limit = null, $orderby = null, $fields = null) 
	{
		$query = $this->fetch('department', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
    }

	function insert_department($data) 
	{
		return $this->insert('department', $data);
	}

	function update_department($data, $where) 
	{
		return $this->update('department', $data, $where);
	}

	function delete_department($where) 
	{
		return $this->delete('department', $where);
	}

	function get_department_by_id($department_id) 
	{
		$result = $this->get_department(array('department_id' => $department_id));
		$row = NULL;
		if (count($result) > 0) {
			$row = $result[0];
		}
		return $row;
	}

	function get_department_by_url($url) 
	{
		$result = $this->get_department(array('url' => $url));
		$row = NULL;
		if (count($result) > 0) {
			$row = $result[0];
		}
		return $row;
	}

	function get_branch($where = null, $limit = null, $orderby = null, $fields = null) 
	{
		$query = $this->fetch('branch', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;	
	}

	function insert_branch($data) 
	{
		return $this->insert('branch', $data);
	}

	function update_branch($data, $where) 
	{
		return $this->update('branch', $data, $where);
	}

	function delete_branch($where) 
	{
		return $this->delete('branch', $where);
	}

	function get_branch_by_id($branch_id) 
	{
		$result = $this->get_branch(array('branch_id' => $branch_id));
		$row = NULL;
		if (count($result) > 0) {
			$row = $result[0];
		}
		return $row;
	}
	// ==============================================================	
	// ==============================================================
	// rf_job_grade_level	
	function get_job_grade_level($where = null, $limit = null, $orderby = null, $fields = null) 
	{
		$query = $this->fetch('job_grade_level', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;	
	}

	function insert_job_grade_level($data) 
	{
		return $this->insert('job_grade_level', $data);
	}

	function update_job_grade_level($data, $where) 
	{
		return $this->update('job_grade_level', $data, $where);
	}

	function delete_job_grade_level($where) 
	{
		return $this->delete('job_grade_level', $where);
	}

	function get_job_grade_level_by_id($job_grade_level_id) 
	{
		$result = $this->get_job_grade_level(array('job_grade_level_id' => $job_grade_level_id));
		$row = NULL;
		if (count($result) > 0) {
			$row = $result[0];
		}
		return $row;
	}
	// ==============================================================
	
	// ==============================================================
	// el_s4s
	function get_s4s($where = null, $limit = null, $orderby = null, $fields = null) 
	{
		$query = $this->fetch('s4s', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;	
	}

	function insert_s4s($data) 
	{
		return $this->insert('s4s', $data);
	}

	function update_s4s($data, $where) 
	{
		return $this->update('s4s', $data, $where);
	}

	function delete_s4s($where) 
	{
		return $this->delete('s4s', $where);
	}

	function get_s4s_by_id($s4s_id) 
	{
		$result = $this->get_s4s(array('s4s_id' => $s4s_id));
		$row = NULL;
		if (count($result) > 0) {
			$row = $result[0];
		}
		return $row;
	}

	function get_s4s_count($where = null) {
		$query = $this->fetch('s4s', 'count(1) as cnt', $where);
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}
	// ==============================================================
	// ==============================================================
	// el_s4s_asset
	function get_s4s_asset($where = null, $limit = null, $orderby = null, $fields = null) 
	{
		$query = $this->fetch('s4s_asset', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;	
	}

	function insert_s4s_asset($data) 
	{
		return $this->insert('s4s_asset', $data);
	}

	function update_s4s_asset($data, $where) 
	{
		return $this->update('s4s_asset', $data, $where);
	}

	function delete_s4s_asset($where) 
	{
		return $this->delete('s4s_asset', $where);
	}

	function get_s4s_asset_by_id($s4s_asset_id) 
	{
		$result = $this->get_s4s_asset(array('s4s_asset_id' => $s4s_asset_id));
		$row = NULL;
		if (count($result) > 0) {
			$row = $result[0];
		}
		return $row;
	}

	function get_s4s_asset_count($where = null) {
		$query = $this->fetch('s4s_asset', 'count(1) as cnt', $where);
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}
	// ==============================================================
	// ==============================================================
	// tr_s4s_acceptance
	function get_s4s_acceptance($where = null, $limit = null, $orderby = null, $fields = null) 
	{
		$query = $this->fetch('s4s_acceptance', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;	
	}

	function insert_s4s_acceptance($data) 
	{
		return $this->insert('s4s_acceptance', $data);
	}

	function update_s4s_acceptance($data, $where) 
	{
		return $this->update('s4s_acceptance', $data, $where);
	}

	function delete_s4s_acceptance($where) 
	{
		return $this->delete('s4s_acceptance', $where);
	}

	function get_s4s_acceptance_by_id($s4s_acceptance_id) 
	{
		$result = $this->get_s4s_acceptance(array('s4s_acceptance_id' => $s4s_acceptance_id));
		$row = NULL;
		if (count($result) > 0) {
			$row = $result[0];
		}
		return $row;
	}

	function get_s4s_acceptance_count($where = null) {
		$query = $this->fetch('s4s_acceptance', 'count(1) as cnt', $where);
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}
	// ==============================================================
	// ==============================================================
	// el_s4s_position
	function get_s4s_position($where = null, $limit = null, $orderby = null, $fields = null) 
	{
		$query = $this->fetch('s4s_position', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;	
	}

	function insert_s4s_position($data) 
	{
		return $this->insert('s4s_position', $data);
	}

	function update_s4s_position($data, $where) 
	{
		return $this->update('s4s_position', $data, $where);
	}

	function delete_s4s_position($where) 
	{
		return $this->delete('s4s_position', $where);
	}

	function get_s4s_position_count($where = null) {
		$query = $this->fetch('s4s_position', 'count(1) as cnt', $where);
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}
	// ==============================================================
	// ==============================================================
	// el_s4s_position_view
	function get_s4s_position_view($where = null, $limit = null, $orderby = null, $fields = null) 
	{
		$query = $this->fetch('s4s_position_view', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;	
	}

	function get_s4s_position_view_count($where = null) {
		$query = $this->fetch('s4s_position_view', 'count(1) as cnt', $where);
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}
	// ==============================================================
	// rf_agency
	function get_agency($where = null, $limit = null, $orderby = null, $fields = null) 
	{
		$query = $this->fetch('agency', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;	
	}

	function insert_agency($data) 
	{
		return $this->insert('agency', $data);
	}

	function update_agency($data, $where) 
	{
		return $this->update('agency', $data, $where);
	}

	function delete_agency($where) 
	{
		return $this->delete('agency', $where);
	}

	function get_agency_by_id($agency_id) 
	{
		$result = $this->get_agency(array('agency_id' => $agency_id));
		$row = NULL;
		if (count($result) > 0) {
			$row = $result[0];
		}
		return $row;
	}
	// ==============================================================	
	// rf_employment_status
	function get_employment_status($where = null, $limit = null, $orderby = null, $fields = null) 
	{
		$query = $this->fetch('employment_status', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;	
	}

	function insert_employment_status($data) 
	{
		return $this->insert('employment_status', $data);
	}

	function update_employment_status($data, $where) 
	{
		return $this->update('employment_status', $data, $where);
	}

	function delete_employment_status($where) 
	{
		return $this->delete('employment_status', $where);
	}

	function get_employment_status_by_id($employment_status_id) 
	{
		$result = $this->get_employment_status(array('employment_status_id' => $employment_status_id));
		$row = NULL;
		if (count($result) > 0) {
			$row = $result[0];
		}
		return $row;
	}
	// ==============================================================	
}