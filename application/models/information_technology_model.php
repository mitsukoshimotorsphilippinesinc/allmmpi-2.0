<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Information_technology_model extends Base_Model 
{

    function __construct() 
	{
		// call the base model constructor
		parent::__construct();

		$this->db = $this->load->database('information_technology', TRUE);

		$this->_TABLES = array(
			'repair_hardware' => 'rf_repair_hardware',
			'repair_summary' => 'rs_repair_summary',
			'repair_detail' => 'rs_repair_detail',
			'department_module' => 'rf_department_module',
			'department_module_submodule' => 'rf_department_module_submodule',
			'repair_status' => 'rf_repair_status',
			'repair_remark' => 'rs_repair_remark',
			'expense' => 'es_expense',
			'expense_signatory' => 'es_expense_signatory',
			'expense_view' => 'es_expense_view',
		);

	}

	// ----------------------------------------------------
	// rf_repair_hardware
	function get_repair_hardware($where = null, $limit = null, $orderby = null, $fields = null) 
	{
		$query = $this->fetch('repair_hardware', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
    }

	function insert_repair_hardware($data) 
	{
		return $this->insert('repair_hardware', $data);
	}

	function update_repair_hardware($data, $where) 
	{
		return $this->update('repair_hardware', $data, $where);
	}

	function delete_repair_hardware($where) 
	{
		return $this->delete('repair_hardware', $where);
	}

	function get_repair_hardware_by_id($repair_hardware_id) 
	{
		$result = $this->get_repair_hardware(array('repair_hardware_id' => $repair_hardware_id));
		$row = NULL;
		if (count($result) > 0) {
			$row = $result[0];
		}
		return $row;
	}

	function get_repair_hardware_count($where = null) {
		// do a sql count instead of row count
		$query = $this->fetch('repair_hardware', 'count(1) as cnt', $where);
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}
	// ----------------------------------------------------
	// ====================================================
	// rs_repair_summary
	function get_repair_summary($where = null, $limit = null, $orderby = null, $fields = null) 
	{
		$query = $this->fetch('repair_summary', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
    }

	function insert_repair_summary($data) 
	{
		return $this->insert('repair_summary', $data);
	}

	function update_repair_summary($data, $where) 
	{
		return $this->update('repair_summary', $data, $where);
	}

	function delete_repair_summary($where) 
	{
		return $this->delete('repair_summary', $where);
	}

	function get_repair_summary_by_id($repair_summary_id) 
	{
		$result = $this->get_repair_summary(array('repair_summary_id' => $repair_summary_id));
		$row = NULL;
		if (count($result) > 0) {
			$row = $result[0];
		}
		return $row;
	}
	
	function get_repair_summary_by_code($repair_code) 
	{
		$result = $this->get_repair_summary(array('repair_code' => $repair_code));
		$row = NULL;
		if (count($result) > 0) {
			$row = $result[0];
		}
		return $row;
	}
	
	function get_repair_summary_count($where = null) {
		// do a sql count instead of row count
		$query = $this->fetch('repair_summary', 'count(1) as cnt', $where);
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}
	
	function search_repair_summary($search, $query, $limit = null, $orderby = null, $fields = null)
	{
		// clear previous get request
		$this->db->flush_cache();

		$this->db->distinct();
		$this->db->like($search,$query,'both');
		
		// No override function, procede with fetch
		($fields!=null) ? $this->db->select($fields) : '';
		($limit!=null) ? $this->db->limit($limit['rows'],$limit['offset']) : '';
		($orderby!=null) ? $this->db->order_by($orderby) : '';

		// set table to use
		$this->db->from($this->_TABLES['repair_summary']);
		$result = $this->db->get();

		$row = $result->result();
		$result->free_result();
		return $row;
	}
	// ----------------------------------------------------
	// ----------------------------------------------------
	// rs_repair_detail
	function get_repair_detail($where = null, $limit = null, $orderby = null, $fields = null) 
	{
		$query = $this->fetch('repair_detail', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
    }

	function insert_repair_detail($data) 
	{
		return $this->insert('repair_detail', $data);
	}

	function update_repair_detail($data, $where) 
	{
		return $this->update('repair_detail', $data, $where);
	}

	function delete_repair_detail($where) 
	{
		return $this->delete('repair_detail', $where);
	}

	function get_repair_detail_by_id($repair_detail_id) 
	{
		$result = $this->get_repair_detail(array('repair_detail_id' => $repair_detail_id));
		$row = NULL;
		if (count($result) > 0) {
			$row = $result[0];
		}
		return $row;
	}
	
	function get_repair_detail_by_repair_summary_id($repair_summary_id) 
	{
		$result = $this->get_salary_repair_detail(array('repair_summary_id' => $repair_summary_id));
		$row = NULL;
		if (count($result) > 0) {
			$row = $result[0];
		}
		return $row;
	}
	
	function get_repair_detail_count($where = null) {
		// do a sql count instead of row count
		$query = $this->fetch('repair_detail', 'count(1) as cnt', $where);
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}
	
	function search_repair_detail($search, $query, $limit = null, $orderby = null, $fields = null)
	{
		// clear previous get request
		$this->db->flush_cache();

		$this->db->distinct();
		$this->db->like($search,$query,'both');
		
		// No override function, procede with fetch
		($fields!=null) ? $this->db->select($fields) : '';
		($limit!=null) ? $this->db->limit($limit['rows'],$limit['offset']) : '';
		($orderby!=null) ? $this->db->order_by($orderby) : '';

		// set table to use
		$this->db->from($this->_TABLES['repair_detail']);
		$result = $this->db->get();

		$row = $result->result();
		$result->free_result();
		return $row;
	}	
	
	// ----------------------------------------------------
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
	
	// ----------------------------------------------------
	// ----------------------------------------------------
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
	// ===========================================================================
	// ----------------------------------------------------
	// rf_repair_status
	function get_repair_status($where = null, $limit = null, $orderby = null, $fields = null) 
	{
		$query = $this->fetch('repair_status', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
    }

	function insert_repair_status($data) 
	{
		return $this->insert('repair_status', $data);
	}

	function update_repair_status($data, $where) 
	{
		return $this->update('repair_status', $data, $where);
	}

	function delete_repair_status($where) 
	{
		return $this->delete('repair_status', $where);
	}
			 
	function get_repair_status_by_id($repair_status_id) 
	{
		$result = $this->get_repair_status(array('repair_status_id' => $repair_status_id));
		$row = NULL;
		if (count($result) > 0) {
			$row = $result[0];
		}
		return $row;
	}

	function get_repair_status_count($where = null) {
		// do a sql count instead of row count
		$query = $this->fetch('repair_status', 'count(1) as cnt', $where);
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}
	// ----------------------------------------------------
	// ----------------------------------------------------
	// rs_repair_remark
	function get_repair_remark($where = null, $limit = null, $orderby = null, $fields = null) 
	{
		$query = $this->fetch('repair_remark', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
    }

	function insert_repair_remark($data) 
	{
		return $this->insert('repair_remark', $data);
	}

	function update_repair_remark($data, $where) 
	{
		return $this->update('repair_remark', $data, $where);
	}

	function delete_repair_remark($where) 
	{
		return $this->delete('repair_remark', $where);
	}

	function get_repair_remark_by_id($repair_remark_id) 
	{
		$result = $this->get_repair_remark(array('repair_remark_id' => $repair_remark_id));
		$row = NULL;
		if (count($result) > 0) {
			$row = $result[0];
		}
		return $row;
	}
	
	function get_repair_remark_count($where = null) {
		// do a sql count instead of row count
		$query = $this->fetch('repair_remark', 'count(1) as cnt', $where);
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}
	
	// ----------------------------------------------------
	// ----------------------------------------------------
	// es_expense
	function get_expense($where = null, $limit = null, $orderby = null, $fields = null) 
	{
		$query = $this->fetch('expense', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
    }

	function insert_expense($data) 
	{
		return $this->insert('expense', $data);
	}

	function update_expense($data, $where) 
	{
		return $this->update('expense', $data, $where);
	}

	function delete_expense($where) 
	{
		return $this->delete('expense', $where);
	}

	function get_expense_by_id($expense_id) 
	{
		$result = $this->get_expense(array('expense_id' => $expense_id));
		$row = NULL;
		if (count($result) > 0) {
			$row = $result[0];
		}
		return $row;
	}

	function get_expense_count($where = null) {
		// do a sql count instead of row count
		$query = $this->fetch('expense', 'count(1) as cnt', $where);
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}
	// ----------------------------------------------------
	// ----------------------------------------------------
	// es_expense_signatory
	function get_expense_signatory($where = null, $limit = null, $orderby = null, $fields = null) 
	{
		$query = $this->fetch('expense_signatory', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
    }

	function insert_expense_signatory($data) 
	{
		return $this->insert('expense_signatory', $data);
	}

	function update_expense_signatory($data, $where) 
	{
		return $this->update('expense_signatory', $data, $where);
	}

	function delete_expense_signatory($where) 
	{
		return $this->delete('expense_signatory', $where);
	}

	function get_expense_signatory_by_id($expense_signatory_id) 
	{
		$result = $this->get_expense_signatory(array('expense_signatory_id' => $expense_signatory_id));
		$row = NULL;
		if (count($result) > 0) {
			$row = $result[0];
		}
		return $row;
	}

	function get_expense_signatory_count($where = null) {
		// do a sql count instead of row count
		$query = $this->fetch('expense_signatory', 'count(1) as cnt', $where);
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}
	// ----------------------------------------------------
	// ----------------------------------------------------
	// es_expense_view
	function get_expense_view($where = null, $limit = null, $orderby = null, $fields = null) 
	{
		$query = $this->fetch('expense_view', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
    }

	function get_expense_view_by_id($expense_id) 
	{
		$result = $this->get_expense_view(array('expense_id' => $expense_id));
		$row = NULL;
		if (count($result) > 0) {
			$row = $result[0];
		}
		return $row;
	}

	function get_expense_view_count($where = null) {
		// do a sql count instead of row count
		$query = $this->fetch('expense_view', 'count(1) as cnt', $where);
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}
	// ----------------------------------------------------
}
	