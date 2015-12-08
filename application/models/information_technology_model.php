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
			/*'employee_slots' => 'ad_employee_slots',
			'employee_slots_view' => 'ad_employee_slots_view',
			'employee_products' => 'ad_employee_products',
			'employee_products_view' => 'ad_employee_products_view',
			'employee_types' => 'rf_employee_types'*/
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
	
}
	