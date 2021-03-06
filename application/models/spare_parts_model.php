<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Spare_parts_model extends Base_Model 
{

    function __construct() 
	{
		// call the base model constructor
		parent::__construct();

		$this->db = $this->load->database('spare_parts', TRUE);

		// assign the table for this model
		$this->_TABLES = array(
			//'salary_deduction' => 'is_salary_deduction',
			//'salary_deduction_detail' => 'is_salary_deduction_detail',
			'dealer_request' => 'is_dealer_request',
			'dealer_request_detail' => 'is_dealer_request_detail',
			'free_of_charge' => 'is_free_of_charge',
			'service_unit_detail' => 'is_service_unit_detail',
			'service_unit' => 'is_service_unit',
			'service_unit_detail' => 'is_service_unit_detail',
			//'warehouse_request' => 'is_warehouse_request',
			//'warehouse_request_detail' => 'is_warehouse_request_detail',
			//'warehouse_claim' => 'is_warehouse_claim',
			//'warehouse_claim_detail' => 'is_warehouse_claim_detail',
			'dealer' => 'rf_dealer',
			'agent' => 'rf_agent',
			'runner' => 'rf_runner',
			'warehouse' => 'rf_warehouse',
			'runner_view' => 'rf_runner_view',
			'warehouse_view' => 'rf_warehouse_view',
			'department_module' => 'rf_department_module',
			'warehouse' => 'rf_warehouse',
			'spare_part_stock_level_view' => 'is_spare_part_stock_level_view',
			'warehouse_reservation' => 'is_warehouse_reservation',
			'item' => 'is_item',
			'item_view' => 'is_item_view',
			'department_module_submodule' => 'rf_department_module_submodule',
			'department' => 'rf_department',
			'reprocessed_item' => 'is_reprocessed_item',
			'warehouse_return' => 'tr_warehouse_return',
			'spare_part' => 'rf_spare_part',			
			'counter_summary' => 'is_counter_order_summary',
			'counter_detail' => 'is_counter_order_detail',
			'request_summary' => 'is_request_summary',
			'request_detail' => 'is_request_detail',
			'admin_log'=>'tr_admin_log',
			'warehouse_transaction' => 'is_warehouse_transaction',
			'warehouse_personnel' => 'rf_warehouse_personnel',
			'warehouse_personnel_view' => 'rf_warehouse_personnel_view',
		);

	}

	function get_counter_summary($where = null, $limit = null, $orderby = null, $fields = null) 
	{
		$query = $this->fetch('counter_summary', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
    }

    function get_counter_detail($where = null, $limit = null, $orderby = null, $fields = null) 
	{
		$query = $this->fetch('counter_detail', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
    }

    function insert_counter_summary($data) 
	{
		return $this->insert('counter_summary', $data);
	}

	function insert_counter_detail($data) 
	{
		return $this->insert('counter_detail', $data);
	}

	//===========================================================
	// is_dealer_request
	function get_dealer_request($where = null, $limit = null, $orderby = null, $fields = null) 
	{

		$query = $this->fetch('dealer_request', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
    }

	function insert_dealer_request($data) 
	{
		return $this->insert('dealer_request', $data);
	}

	function update_dealer_request($data, $where) 
	{
		return $this->update('dealer_request', $data, $where);
	}

	function delete_dealer_request($where) 
	{
		return $this->delete('dealer_request', $where);
	}

	function get_dealer_request_by_id($dealer_request_id) 
	{
		$result = $this->get_dealer_request(array('dealer_request_id' => $dealer_request_id));
		$row = NULL;
		if (count($result) > 0) {
			$row = $result[0];
		}
		return $row;
	}
	
	function get_dealer_request_by_code($request_code) 
	{
		$result = $this->get_dealer_request(array('request_code' => $request_code));
		$row = NULL;
		if (count($result) > 0) {
			$row = $result[0];
		}
		return $row;
	}
	
	function get_dealer_request_count($where = null) {
		// do a sql count instead of row count
		$query = $this->fetch('dealer_request', 'count(1) as cnt', $where);
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}
	
	function search_dealer_request($search, $query, $limit = null, $orderby = null, $fields = null)
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
		$this->db->from($this->_TABLES['dealer_request']);
		$result = $this->db->get();

		$row = $result->result();
		$result->free_result();
		return $row;
	}

	// ========================================================================
	// rf_dealer
	function get_dealer($where = null, $limit = null, $orderby = null, $fields = null) 
	{
		$query = $this->fetch('dealer', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
    }

	function insert_dealer($data) 
	{
		return $this->insert('dealer', $data);
	}

	function update_dealer($data, $where) 
	{
		return $this->update('dealer', $data, $where);
	}

	function delete_dealer($where) 
	{
		return $this->delete('dealer', $where);
	}

	function get_dealer_by_id($dealer_id) 
	{
		$result = $this->get_dealer(array('dealer_id' => $dealer_id));
		$row = NULL;
		if (count($result) > 0) {
			$row = $result[0];
		}
		return $row;
	}
	
	function get_dealer_by_code($old_dealer_code) 
	{
		$result = $this->get_dealer(array('old_dealer_code' => $old_dealer_code));
		$row = NULL;
		if (count($result) > 0) {
			$row = $result[0];
		}
		return $row;
	}

	function get_dealer_count($where = null) {
		// do a sql count instead of row count
		$query = $this->fetch('dealer', 'count(1) as cnt', $where);
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}	
	// ========================================================================
	// ========================================================================
	// is_dealer_request_detail
	function get_dealer_request_detail($where = null, $limit = null, $orderby = null, $fields = null) 
	{

		$query = $this->fetch('dealer_request_detail', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
    }

	function insert_dealer_request_detail($data) 
	{
		return $this->insert('dealer_request_detail', $data);
	}

	function update_dealer_request_detail($data, $where) 
	{
		return $this->update('dealer_request_detail', $data, $where);
	}

	function delete_dealer_request_detail($where) 
	{
		return $this->delete('dealer_request_detail', $where);
	}

	function get_dealer_request_detail_by_id($dealer_request_detail_id) 
	{
		$result = $this->get_dealer_request_detail(array('dealer_request_detail_id' => $dealer_request_detail_id));
		$row = NULL;
		if (count($result) > 0) {
			$row = $result[0];
		}
		return $row;
	}
	
	function get_dealer_request_detail_count($where = null) {
		// do a sql count instead of row count
		$query = $this->fetch('dealer_request_detail', 'count(1) as cnt', $where);
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}
	
	function search_dealer_request_detail($search, $query, $limit = null, $orderby = null, $fields = null)
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
		$this->db->from($this->_TABLES['dealer_request_detail']);
		$result = $this->db->get();

		$row = $result->result();
		$result->free_result();
		return $row;
	}
	// ========================================================================
	// ========================================================================
	// rf_agent
	function get_agent($where = null, $limit = null, $orderby = null, $fields = null) 
	{
		$query = $this->fetch('agent', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
    }

	function insert_agent($data) 
	{
		return $this->insert('agent', $data);
	}

	function update_agent($data, $where) 
	{
		return $this->update('agent', $data, $where);
	}

	function delete_agent($where) 
	{
		return $this->delete('agent', $where);
	}

	function get_agent_by_id($agent_id) 
	{
		$result = $this->get_agent(array('agent_id' => $agent_id));
		$row = NULL;
		if (count($result) > 0) {
			$row = $result[0];
		}
		return $row;
	}
	
	function get_agent_count($where = null) {
		// do a sql count instead of row count
		$query = $this->fetch('agent', 'count(1) as cnt', $where);
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}	
	// ========================================================================
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

	// ======================================================================
	// ======================================================================
	// is_service_unit
	function get_service_unit($where = null, $limit = null, $orderby = null, $fields = null) 
	{

		$query = $this->fetch('service_unit', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
    }

	function insert_service_unit($data) 
	{
		return $this->insert('service_unit', $data);
	}

	function update_service_unit($data, $where) 
	{
		return $this->update('service_unit', $data, $where);
	}

	function delete_service_unit($where) 
	{
		return $this->delete('service_unit', $where);
	}

	function get_service_unit_by_id($service_unit_id) 
	{
		$result = $this->get_service_unit(array('service_unit_id' => $service_unit_id));
		$row = NULL;
		if (count($result) > 0) {
			$row = $result[0];
		}
		return $row;
	}
	
	function get_service_unit_by_code($request_code) 
	{
		$result = $this->get_service_unit(array('request_code' => $request_code));
		$row = NULL;
		if (count($result) > 0) {
			$row = $result[0];
		}
		return $row;
	}
	
	function get_service_unit_count($where = null) {
		// do a sql count instead of row count
		$query = $this->fetch('service_unit', 'count(1) as cnt', $where);
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}
	
	function search_service_unit($search, $query, $limit = null, $orderby = null, $fields = null)
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
		$this->db->from($this->_TABLES['service_unit']);
		$result = $this->db->get();

		$row = $result->result();
		$result->free_result();
		return $row;
	}
	// ========================================================================
	// ========================================================================
	// is_service_unit_detail
	function get_service_unit_detail($where = null, $limit = null, $orderby = null, $fields = null) 
	{

		$query = $this->fetch('service_unit_detail', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
    }

	function insert_service_unit_detail($data) 
	{
		return $this->insert('service_unit_detail', $data);
	}

	function update_service_unit_detail($data, $where) 
	{
		return $this->update('service_unit_detail', $data, $where);
	}

	function delete_service_unit_detail($where) 
	{
		return $this->delete('service_unit_detail', $where);
	}

	function get_service_unit_detail_by_id($service_unit_detail_id) 
	{
		$result = $this->get_service_unit_detail(array('service_unit_detail_id' => $service_unit_detail_id));
		$row = NULL;
		if (count($result) > 0) {
			$row = $result[0];
		}
		return $row;
	}
	
	function get_service_unit_detail_count($where = null) {
		// do a sql count instead of row count
		$query = $this->fetch('service_unit_detail', 'count(1) as cnt', $where);
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}
	
	function search_service_unit_detail($search, $query, $limit = null, $orderby = null, $fields = null)
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
		$this->db->from($this->_TABLES['service_unit_detail']);
		$result = $this->db->get();

		$row = $result->result();
		$result->free_result();
		return $row;
	}
	// ======================================================================
	// ========================================================================
	// is_free_of_charge
	function get_free_of_charge($where = null, $limit = null, $orderby = null, $fields = null) 
	{

		$query = $this->fetch('free_of_charge', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
    }

	function insert_free_of_charge($data) 
	{
		return $this->insert('free_of_charge', $data);
	}

	function update_free_of_charge($data, $where) 
	{
		return $this->update('free_of_charge', $data, $where);
	}

	function delete_free_of_charge($where) 
	{
		return $this->delete('free_of_charge', $where);
	}

	function get_free_of_charge_by_id($free_of_charge_id) 
	{
		$result = $this->get_free_of_charge(array('free_of_charge_id' => $free_of_charge_id));
		$row = NULL;
		if (count($result) > 0) {
			$row = $result[0];
		}
		return $row;
	}
	
	function get_free_of_charge_by_code($request_code) 
	{
		$result = $this->get_free_of_charge(array('request_code' => $request_code));
		$row = NULL;
		if (count($result) > 0) {
			$row = $result[0];
		}
		return $row;
	}
	
	function get_free_of_charge_count($where = null) {
		// do a sql count instead of row count
		$query = $this->fetch('free_of_charge', 'count(1) as cnt', $where);
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}
	
	function search_free_of_charge($search, $query, $limit = null, $orderby = null, $fields = null)
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
		$this->db->from($this->_TABLES['free_of_charge']);
		$result = $this->db->get();

		$row = $result->result();
		$result->free_result();
		return $row;
	}
	// ==========================================================================
	// ==========================================================================
	// is_free_of_charge_detail
	function get_free_of_charge_detail($where = null, $limit = null, $orderby = null, $fields = null) 
	{

		$query = $this->fetch('free_of_charge_detail', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
    }

	function insert_free_of_charge_detail($data) 
	{
		return $this->insert('free_of_charge_detail', $data);
	}

	function update_free_of_charge_detail($data, $where) 
	{
		return $this->update('free_of_charge_detail', $data, $where);
	}

	function delete_free_of_charge_detail($where) 
	{
		return $this->delete('free_of_charge_detail', $where);
	}

	function get_free_of_charge_detail_by_id($free_of_charge_detail_id) 
	{
		$result = $this->get_warehouse_request_detail(array('free_of_charge_detail_id' => $free_of_charge_detail_id));
		$row = NULL;
		if (count($result) > 0) {
			$row = $result[0];
		}
		return $row;
	}
	
	function get_free_of_charge_detail_count($where = null) {
		// do a sql count instead of row count
		$query = $this->fetch('free_of_charge_detail', 'count(1) as cnt', $where);
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}
	
	function search_free_of_charge_detail($search, $query, $limit = null, $orderby = null, $fields = null)
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
		$this->db->from($this->_TABLES['free_of_charge_detail']);
		$result = $this->db->get();

		$row = $result->result();
		$result->free_result();
		return $row;
	}
	// ======================================================================
	// ======================================================================
	// rf_warehouse
	function get_warehouse($where = null, $limit = null, $orderby = null, $fields = null) 
	{
		$query = $this->fetch('warehouse', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
    }

	function insert_warehouse($data) 
	{
		return $this->insert('warehouse', $data);
	}

	function update_warehouse($data, $where) 
	{
		return $this->update('warehouse', $data, $where);
	}

	function delete_warehouse($where) 
	{
		return $this->delete('warehouse', $where);
	}

	function get_warehouse_by_id($warehouse_id) 
	{
		$result = $this->get_warehouse(array('warehouse_id' => $warehouse_id));
		$row = NULL;
		if (count($result) > 0) {
			$row = $result[0];
		}
		return $row;
	}
	
	function get_warehouse_count($where = null) {
		// do a sql count instead of row count
		$query = $this->fetch('warehouse', 'count(1) as cnt', $where);
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}
	// ======================================================================

	// ======================================================================
	// is_spare_part_stock_level_view
	function get_spare_part_stock_level_view($where = null, $limit = null, $orderby = null, $fields = null) 
	{
		$query = $this->fetch('spare_part_stock_level_view', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
    }

	function get_spare_part_stock_level_view_by_sku($sku) 
	{
		$result = $this->get_spare_part_stock_level_view(array('sku' => $sku));
		$row = NULL;
		if (count($result) > 0) {
			$row = $result[0];
		}
		return $row;
	}
	
	function get_spare_part_stock_level_view_count($where = null) {
		// do a sql count instead of row count
		$query = $this->fetch('spare_part_stock_level_view', 'count(1) as cnt', $where);
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}
	// ======================================================================


	function get_pending_warehouse_reservations($where=NULL)
	{
		$sql = "SELECT 
					b.module_name,
					a.transaction_number,
					a.status,
					a.insert_timestamp
				FROM 
					`tr_warehouse_reservation` a
				LEFT JOIN 
					`rf_department_module` b ON a.department_module_id = b.department_module_id
				WHERE
					`status` IN ('PENDING', 'PROCESSING') " . $where;

		$query = $this->db->query($sql);		
		$result = $query->result();
		$query->free_result();
		return $result;
	}

	function get_pending_warehouse_reservations_count($where=NULL)
	{
		$sql = "SELECT 
					count(*) as cnt
				FROM 
					`tr_warehouse_reservation` a
				LEFT JOIN 
					`rf_department_module` b ON a.department_module_id = b.department_module_id
				WHERE
					`status` IN ('PENDING', 'PROCESSING') " . $where;

		$query = $this->db->query($sql);		
		$result = $query->first_row();
		$query->free_result();
		return $result->cnt;
	}	

	// ========================================================================
	// 
	function get_warehouse_reservation($where = null, $limit = null, $orderby = null, $fields = null) 
	{

		$query = $this->fetch('warehouse_reservation', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
    }

	function insert_warehouse_reservation($data) 
	{
		return $this->insert('warehouse_reservation', $data);
	}

	function update_warehouse_reservation($data, $where) 
	{
		return $this->update('warehouse_reservation', $data, $where);
	}

	function delete_warehouse_reservation($where) 
	{
		return $this->delete('warehouse_reservation', $where);
	}

	function get_warehouse_reservation_by_id($warehouse_reservation_id) 
	{
		$result = $this->get_warehouse_reservation(array('warehouse_reservation_id' => $warehouse_reservation_id));
		$row = NULL;
		if (count($result) > 0) {
			$row = $result[0];
		}
		return $row;
	}
	
	function get_warehouse_reservation_by_code($transaction_number) 
	{
		$result = $this->get_warehouse_reservation(array('transaction_number' => $transaction_number));
		$row = NULL;
		if (count($result) > 0) {
			$row = $result[0];
		}
		return $row;
	}
	
	function get_warehouse_reservation_count($where = null) {
		// do a sql count instead of row count
		$query = $this->fetch('warehouse_reservation', 'count(1) as cnt', $where);
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}
	
	function search_warehouse_reservation($search, $query, $limit = null, $orderby = null, $fields = null)
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
		$this->db->from($this->_TABLES['warehouse_reservation']);
		$result = $this->db->get();

		$row = $result->result();
		$result->free_result();
		return $row;
	}
	// ===========================================================================
	// ===========================================================================
	// is_item
	function get_item($where = null, $limit = null, $orderby = null, $fields = null) 
	{
		$query = $this->fetch('item', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
    }

	function insert_item($data) 
	{
		return $this->insert('item', $data);
	}

	function update_item($data, $where) 
	{
		return $this->update('item', $data, $where);
	}

	function delete_item($where) 
	{
		return $this->delete('item', $where);
	}

	function get_item_by_id($item_id) 
	{
		$result = $this->get_item(array('item_id' => $item_id));
		$row = NULL;
		if (count($result) > 0) {
			$row = $result[0];
		}
		return $row;
	}
	
	function get_item_by_sku($sku) 
	{
		$result = $this->get_item(array('sku' => $sku));
		$row = NULL;
		if (count($result) > 0) {
			$row = $result[0];
		}
		return $row;
	}
	
	function get_item_count($where = null) {
		// do a sql count instead of row count
		$query = $this->fetch('item', 'count(1) as cnt', $where);
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}
	// ===========================================================================
	// ===========================================================================
	// is_item_view
	function get_item_view($where = null, $limit = null, $orderby = null, $fields = null) 
	{
		$query = $this->fetch('item_view', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
    }

	function get_item_view_by_id($item_id) 
	{
		$result = $this->get_item_view(array('item_id' => $item_id));
		$row = NULL;
		if (count($result) > 0) {
			$row = $result[0];
		}
		return $row;
	}
	
	function get_item_view_count($where = null) {
		// do a sql count instead of row count
		$query = $this->fetch('item_view', 'count(1) as cnt', $where);
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}
	// ===========================================================================
	// ===========================================================================
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
	// ===========================================================================
    // is_reprocessed_item
	function get_reprocessed_item($where = null, $limit = null, $orderby = null, $fields = null) 
	{
		$query = $this->fetch('reprocessed_item', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
    }

	function insert_reprocessed_item($data) 
	{
		return $this->insert('reprocessed_item', $data);
	}

	function update_reprocessed_item($data, $where) 
	{
		return $this->update('reprocessed_item', $data, $where);
	}

	function delete_reprocessed_item($where) 
	{
		return $this->delete('reprocessed_item', $where);
	}

	function get_reprocessed_item_by_id($reprocessed_item_id) 
	{
		$result = $this->get_reprocessed_item(array('reprocessed_item_id' => $reprocessed_item_id));
		$row = NULL;
		if (count($result) > 0) {
			$row = $result[0];
		}
		return $row;
	}
	
	function get_reprocessed_item_by_code($request_code) 
	{
		$result = $this->get_reprocessed_item(array('request_code' => $request_code));
		$row = NULL;
		if (count($result) > 0) {
			$row = $result[0];
		}
		return $row;
	}
	
	function get_reprocessed_item_count($where = null) {
		// do a sql count instead of row count
		$query = $this->fetch('reprocessed_item', 'count(1) as cnt', $where);
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}
	
	function search_reprocessed_item($search, $query, $limit = null, $orderby = null, $fields = null)
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
		$this->db->from($this->_TABLES['reprocessed_item']);
		$result = $this->db->get();

		$row = $result->result();
		$result->free_result();
		return $row;
	}
	// ===========================================================================
	// ===========================================================================
    // tr_warehouse_return
	function get_warehouse_return($where = null, $limit = null, $orderby = null, $fields = null) 
	{
		$query = $this->fetch('warehouse_return', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
    }

	function insert_warehouse_return($data) 
	{
		return $this->insert('warehouse_return', $data);
	}

	function update_warehouse_return($data, $where) 
	{
		return $this->update('warehouse_return', $data, $where);
	}

	function delete_warehouse_return($where) 
	{
		return $this->delete('warehouse_return', $where);
	}

	function get_warehouse_return_by_id($warehouse_return_id) 
	{
		$result = $this->get_reprocessed_item(array('warehouse_return_id' => $warehouse_return_id));
		$row = NULL;
		if (count($result) > 0) {
			$row = $result[0];
		}
		return $row;
	}
	
	function get_warehouse_return_count($where = null) {
		// do a sql count instead of row count
		$query = $this->fetch('warehouse_return', 'count(1) as cnt', $where);
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}
		
	// ===========================================================================
	// ===========================================================================	
	// is_request_summary
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
	
	function get_request_summary_by_code($request_code) 
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
	
	function search_request_summary($search, $query, $limit = null, $orderby = null, $fields = null)
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
		$this->db->from($this->_TABLES['request_summary']);
		$result = $this->db->get();

		$row = $result->result();
		$result->free_result();
		return $row;
	}
	// ----------------------------------------------------
	// is_request_detail
	function get_request_detail($where = null, $limit = null, $orderby = null, $fields = null) 
	{
		$query = $this->fetch('request_detail', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
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
	
	function get_request_detail_by_request_summary_id($request_summary_id) 
	{
		$result = $this->get_salary_request_detail(array('request_summary_id' => $request_summary_id));
		$row = NULL;
		if (count($result) > 0) {
			$row = $result[0];
		}
		return $row;
	}
	
	function get_request_detail_count($where = null) {
		// do a sql count instead of row count
		$query = $this->fetch('request_detail', 'count(1) as cnt', $where);
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}
	
	function search_request_detail($search, $query, $limit = null, $orderby = null, $fields = null)
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
		$this->db->from($this->_TABLES['request_detail']);
		$result = $this->db->get();

		$row = $result->result();
		$result->free_result();
		return $row;
	}	

	//-------------------------------------------------------------------------
	// rf_spare_part
	function get_spare_part($where = null, $limit = null, $orderby = null, $fields = null) 
	{
		$query = $this->fetch('spare_part', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
    }

    function insert_spare_part($data) 
	{
		return $this->insert('spare_part', $data);
	}

	function update_spare_part($data, $where) 
	{
		return $this->update('spare_part', $data, $where);
	}

	function delete_spare_part($where) 
	{
		return $this->delete('spare_part', $where);
	}

	function get_spare_part_by_sku($sku) 
	{
		$result = $this->get_spare_part(array('sku' => $sku));
		$row = NULL;
		if (count($result) > 0) {
			$row = $result[0];
		}
		return $row;
	}

	function get_spare_part_by_id($id) 
	{
		$result = $this->get_spare_part(array('spare_part_id' => $id));
		$row = NULL;
		if (count($result) > 0) {
			$row = $result[0];
		}
		return $row;
	}
	
	function get_spare_part_count($where = null) {
		// do a sql count instead of row count
		$query = $this->fetch('spare_part', 'count(1) as cnt', $where);
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}

	//-------------------------------------------------------------------------
	// tr_admin_log
	function insert_log($type, $data) {	
        if(isset($data['remarks'])) {
            $data['remarks'] .= "\n\n URL:" . $this->uri->uri_string();
        } else {
            $data['remarks'] = "URL:" . $this->uri->uri_string();
        }	
		
		if ($type=='spare_parts')
	        $result = $this->insert('admin_log', $data);
    }		

	//-------------------------------------------------------------------------
	// rf_runner
	function get_runner($where = null, $limit = null, $orderby = null, $fields = null) 
	{
		$query = $this->fetch('runner', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
    }

	function insert_runner($data) 
	{
		return $this->insert('runner', $data);
	}

	function update_runner($data, $where) 
	{
		return $this->update('runner', $data, $where);
	}

	function delete_runner($where) 
	{
		return $this->delete('runner', $where);
	}

	function get_runner_by_id($runner_id) 
	{
		$result = $this->get_runner(array('runner_id' => $runner_id));
		$row = NULL;
		if (count($result) > 0) {
			$row = $result[0];
		}
		return $row;
	}
	
	function get_runner_count($where = null) {
		// do a sql count instead of row count
		$query = $this->fetch('runner', 'count(1) as cnt', $where);
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}	
	// ========================================================================
	//-------------------------------------------------------------------------
	// rf_runner_view
	function get_runner_view($where = null, $limit = null, $orderby = null, $fields = null) 
	{
		$query = $this->fetch('runner_view', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
    }

	
	function get_runner_view_by_id_number($id_number) 
	{
		$result = $this->get_runner_view(array('id_number' => $id_number));
		$row = NULL;
		if (count($result) > 0) {
			$row = $result[0];
		}
		return $row;
	}

	function get_runner_view_by_id($runner_id) 
	{
		$result = $this->get_runner_view(array('runner_id' => $runner_id));
		$row = NULL;
		if (count($result) > 0) {
			$row = $result[0];
		}
		return $row;
	}
	
	function get_runner_view_count($where = null) {
		// do a sql count instead of row count
		$query = $this->fetch('runner_view', 'count(1) as cnt', $where);
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}	
	// ========================================================================	
	//-------------------------------------------------------------------------
	// rf_warehouse_view
	function get_warehouse_view($where = null, $limit = null, $orderby = null, $fields = null) 
	{
		$query = $this->fetch('warehouse_view', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
    }

	function get_warehouse_view_count($where = null) {
		// do a sql count instead of row count
		$query = $this->fetch('warehouse_view', 'count(1) as cnt', $where);
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}	
	// ========================================================================	
	// ===========================================================================	
	// is_warehouse_transaction
	function get_warehouse_transaction($where = null, $limit = null, $orderby = null, $fields = null) 
	{
		$query = $this->fetch('warehouse_transaction', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
    }

	function insert_warehouse_transaction($data) 
	{
		return $this->insert('warehouse_transaction', $data);
	}

	function update_warehouse_transaction($data, $where) 
	{
		return $this->update('warehouse_transaction', $data, $where);
	}

	function delete_warehouse_transaction($where) 
	{
		return $this->delete('warehouse_transaction', $where);
	}

	function get_warehouse_transaction_by_id($warehouse_transaction_id) 
	{
		$result = $this->get_warehouse_transaction(array('warehouse_transaction_id' => $warehouse_transaction_id));
		$row = NULL;
		if (count($result) > 0) {
			$row = $result[0];
		}
		return $row;
	}
	
	function get_warehouse_transaction_count($where = null) {
		// do a sql count instead of row count
		$query = $this->fetch('warehouse_transaction', 'count(1) as cnt', $where);
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}	
	// ----------------------
	//-----------------------
	// rf_warehouse_personnel
	function get_warehouse_personnel($where = null, $limit = null, $orderby = null, $fields = null) 
	{
		$query = $this->fetch('warehouse_personnel', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
    }

    function insert_warehouse_personnel($data) 
	{
		return $this->insert('warehouse_personnel', $data);
	}

	function update_warehouse_personnel($data, $where) 
	{
		return $this->update('warehouse_personnel', $data, $where);
	}

	function delete_warehouse_personnel($where) 
	{
		return $this->delete('warehouse_personnel', $where);
	}

	function get_warehouse_personnel_by_id_number($id_number) 
	{
		$result = $this->get_warehouse_personnel(array('id_number' => $id_number));
		$row = NULL;
		if (count($result) > 0) {
			//$row = $result[0];
			$row = $result;
		}
		return $row;
	}
	
	function get_warehouse_personnel_count($where = null) {
		// do a sql count instead of row count
		$query = $this->fetch('warehouse_personnel', 'count(1) as cnt', $where);
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}
	//----------------------------
	//----------------------------
	// rf_warehouse_personnel_view
	function get_warehouse_personnel_view($where = null, $limit = null, $orderby = null, $fields = null) 
	{
		$query = $this->fetch('warehouse_personnel_view', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
    }

    function insert_warehouse_personnel_view($data) 
	{
		return $this->insert('warehouse_personnel_view', $data);
	}

	function update_warehouse_personnel_view($data, $where) 
	{
		return $this->update('warehouse_personnel_view', $data, $where);
	}

	function delete_warehouse_personnel_view($where) 
	{
		return $this->delete('warehouse_personnel_view', $where);
	}

	function get_warehouse_personnel_view_by_id_number($id_number) 
	{
		$result = $this->get_warehouse_personnel_view(array('id_number' => $id_number));
		$row = NULL;
		if (count($result) > 0) {
			//$row = $result[0];
			$row = $result;
		}
		return $row;
	}
	
	function get_warehouse_personnel_view_count($where = null) {
		// do a sql count instead of row count
		$query = $this->fetch('warehouse_personnel_view', 'count(1) as cnt', $where);
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}

	//-------------------------------------------------------------------------
}