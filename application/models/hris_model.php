<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Hris_model extends Base_Model 
{

    function __construct() 
	{
		// call the base model constructor
		parent::__construct();

		$this->db = $this->load->database('hrisdb', TRUE);


		// assign the table for this model
		$this->_TABLES = array(
			'employee' => 'hs_hr_employee',
			'attendance_record' => 'ohrm_attendance_record',
		);

	}

	// ==============
	// hs_hr_employee
	// ==============
	function get_employee($where = null, $limit = null, $orderby = null, $fields = null) 
	{
		$query = $this->fetch('employee', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
    }

	function insert_employee($data) 
	{
		return $this->insert('employee', $data);
	}

	function update_employee($data, $where) 
	{
		return $this->update('employee', $data, $where);
	}

	function delete_employee($where) 
	{
		return $this->delete('employee', $where);
	}

	function get_employee_by_emp_number($emp_number) 
	{
		$result = $this->get_employee(array('emp_number' => $emp_number));
		$row = NULL;
		if (count($result) > 0) {
			$row = $result[0];
		}
		return $row;
	}
	
	function get_employee_count($where = null) {
		// do a sql count instead of row count
		$query = $this->fetch('employee', 'count(1) as cnt', $where);
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}
	
	// ======================
	// ohrm_attendance_record
	// ======================
	function get_attendance_record($where = null, $limit = null, $orderby = null, $fields = null) 
	{
		$query = $this->fetch('attendance_record', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
    }

	function insert_attendance_record($data) 
	{
		return $this->insert('attendance_record', $data);
	}

	function update_attendance_record($data, $where) 
	{
		return $this->update('attendance_record', $data, $where);
	}

	function delete_attendance_record($where) 
	{
		return $this->delete('attendance_record', $where);
	}

	function get_attendance_record_by_emp_number($emp_number) 
	{
		$result = $this->get_attendance_record(array('emp_number' => $emp_number));
		$row = NULL;
		if (count($result) > 0) {
			$row = $result[0];
		}
		return $row;
	}
	
	function get_attendance_record_count($where = null) {
		// do a sql count instead of row count
		$query = $this->fetch('attendance_record', 'count(1) as cnt', $where);
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}
	// ======================

}