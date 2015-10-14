<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Facilities_model extends Base_Model 
{

    function __construct() 
	{
		// call the base model constructor
		parent::__construct();

		// assign the table for this model
		$this->_TABLES = array(
			'facilities' => 'is_facilities',
			'facility_items' => 'is_facility_items',
			'facility_items_view' => 'is_facility_items_view',
			'items_receiving' => 'tr_facility_items_receiving',
			'items_receiving_view' => 'tr_facility_items_receiving_view',
			'items_releasing' => 'tr_facility_items_releasing',
			'items_releasing_view' => 'tr_facility_items_releasing_view',
			'products_releasing' => 'tr_pos_products_releasing_view',
			'facility_item_reports' => 'rt_facility_item_reports',
			'facility_item_reports_view' => 'rt_facility_item_reports_view',
			'facility_types' => 'rf_facility_types',
			'facility_items_daily' => 'rt_facility_items',
			'facility_items_releasing' => 'rt_facility_items_releasing_view',
			'facility_items_receiving' => 'rt_facility_items_receiving_view',
			'service_depots' => 'rf_service_depots'
		);

	}

	function get_facilities($where = null, $limit = null, $orderby = null, $fields = null) 
	{
		$query = $this->fetch('facilities', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
    }

	function insert_facility($data) 
	{
		return $this->insert('facilities', $data);
	}

	function update_facility($data, $where) 
	{
		return $this->update('facilities', $data, $where);
	}

	function delete_facility($where) 
	{
		return $this->delete('facilities', $where);
	}

	function get_facility_by_id($facility_id) 
	{
		$result = $this->get_facilities(array('facility_id' => $facility_id));
		$row = NULL;
		if (count($result) > 0) {
			$row = $result[0];
		}
		return $row;
	}
	
	function get_facility_by_name($facility_name) 
	{
		$result = $this->get_facilities(array('facility_name' => $facility_name));
		$row = NULL;
		if (count($result) > 0) {
			$row = $result[0];
		}
		return $row;
	}

	function get_facility_count($where = null) {
		// do a sql count instead of row count
		$query = $this->fetch('facilities', 'count(1) as cnt', $where);
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}


	// facility items
	
	function get_facility_items($where = null, $limit = null, $orderby = null, $fields = null) 
	{
		$query = $this->fetch('facility_items_view', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
    }

	function insert_facility_item($data) 
	{
		return $this->insert('facility_items', $data);
	}

	function update_facility_item($data, $where) 
	{
		return $this->update('facility_items', $data, $where);
	}

	function delete_facility_item($where) 
	{
		return $this->delete('facility_items', $where);
	}

	function get_facility_items_by_facility_id($facility_id) 
	{
		$where = array("facility_id"=>$facility_id);
		$result = $this->get_facility_items($where);
		return $result;
	}

	function get_facility_items_by_facility_id_and_item_id($facility_id,$item_id) 
	{
		$result = $this->get_facility_items(array('facility_id' => $facility_id,'item_id' => $item_id));
		$row = NULL;
		if (count($result) > 0) {
			$row = $result[0];
		}
		return $row;
	}

	
	function get_facility_items_count($where = null) 
	{
		// do a sql count instead of row count
		$query = $this->fetch('facility_items_view', 'count(1) as cnt', $where);
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}	

	// returns new qty
	function add_facility_item_qty($facility_id,$item_id,$qty,$unit_id=1,$transaction_code=100,$remarks="",$user_id=0,$order_id=0)
	{
		// condition
		$where = array("facility_id"=>$facility_id,"item_id"=>$item_id);

		$facility_item = $this->get_facility_items_by_facility_id_and_item_id($facility_id,$item_id);
		
		// check item exists in the facility				
		if (count($facility_item)>0)
		{
			// item exists add qty to existing qty
			$new_qty = $facility_item->qty + $qty;

			// update facility item
			$data = array(
				"qty" => $new_qty,
				"unit_id" => $unit_id,
				"update_timestamp" => date("Y-m-d H:i:s")
			);
			
			$details_before = array('id' => $facility_item->facility_item_id, 'details' => array('qty' => $facility_item->qty, 'unit_id' => $facility_item->unit_id, 'update_timestamp' => $facility_item->update_timestamp));
			$details_before = json_encode($details_before);
			
			$this->update_facility_item($data,$where);
			
			//logging of action
			$details_after = array('id' => $facility_item->facility_item_id, 'details' => $data);
			$details_after = json_encode($details_after);
			$update_facility_item_log_data = array(
				'user_id' => $this->user->user_id,
				'module_name' => 'FACILITY ITEMS',
				'table_name' => 'is_facility_items',
				'action' => 'UPDATE',
				'details_before' => $details_before,
				'details_after' => $details_after,
				'remarks' => "",
			);
										
			$this->tracking_model->insert_logs('admin', $update_facility_item_log_data);
			
		} 
		else // item does not exist create item in facility with specified qty
		{
			$new_qty = $qty;
			
			// insert facility item
			$data = array(
				"facility_id" => $facility_id,
				"item_id" => $item_id,
				"qty" => $new_qty,
				"unit_id" => $unit_id,
				"critical_qty" => 0,
				"update_timestamp" => date("Y-m-d H:i:s")
			);
			
			$this->insert_facility_item($data);
			
			//logging of action
			$details_after = array('id' => 0, 'details' => $data);
			$details_after = json_encode($details_after);
			$new_facility_item_log_data = array(
				'user_id' => $this->user->user_id,
				'module_name' => 'FACILITY ITEMS',
				'table_name' => 'is_facility_items',
				'action' => 'ADD',
				'details_before' => "",
				'details_after' => $details_after,
				'remarks' => "",
			);
										
			$this->tracking_model->insert_logs('admin', $new_facility_item_log_data);

		}
		
		// insert to logs
		//if ($new_qty>0) 
		//{
			$this->insert_to_logs("add",$facility_id,$item_id,$qty,$new_qty,$unit_id,$transaction_code,$remarks,$user_id,$order_id);
			//insert in facility item reports, qty received
			$existing = $this->get_facility_item_reports(array('facility_id' => $facility_id, 'item_id' => $item_id, 'insert_date' => date('Y-m-d')));
			if (empty($existing)) //no records of this item for today
			{
				$data = array(
					'facility_id' => $facility_id,
					'item_id' => $item_id,
					'qty_released' => 0,
					'qty_received' => $qty,
					'total_amount' => 0,
					'insert_date' => date('Y-m-d')
				);
				$this->insert_item_report($data);
			}else
			{
				$existing = $existing[0];
				$current_qty = $existing->qty_received;
				$data = array(
					'qty_received' => $current_qty + $qty,
				);
				$where = array('facility_item_report_id' => $existing->facility_item_report_id);
				$this->update_item_report($data, $where);
			}
		//}
		
		return $new_qty;
	}

	// returns new qty
	function remove_facility_item_qty($facility_id,$item_id,$qty,$unit_id=1,$transaction_code=200,$remarks="",$user_id=0,$order_id=0,$package_id=0)
	{
		// condition
		$where = array("facility_id"=>$facility_id,"item_id"=>$item_id);

		$facility_item = $this->get_facility_items_by_facility_id_and_item_id($facility_id,$item_id);
		
		// check item exists in the facility
		if (count($facility_item)>0)
		{
			// item exists add qty to existing qty
			$new_qty = $facility_item->qty - $qty;
			
			if($transaction_code == 204) //from POI transaction
			{
				$new_pending_qty = $facility_item->qty_pending - $qty;
			}
			else
			{
				$new_pending_qty = $facility_item->qty_pending;
			}
			
			//if ($new_qty<=0) $new_qty = 0; 
			if($new_pending_qty <= 0) $new_pending_qty = 0;

			// update facility item
			$data = array(
				"qty" => $new_qty,
				"qty_pending" => $new_pending_qty,
				"update_timestamp" => date("Y-m-d H:i:s")
			);
			
			$details_before = array('id' => $facility_item->facility_item_id, 'details' => array('qty' => $facility_item->qty, 'qty_pending' => $facility_item->qty_pending, 'update_timestamp' => $facility_item->update_timestamp));
			$details_before = json_encode($details_before);
			
			$this->update_facility_item($data,$where);
			
			//logging of action
			$details_after = array('id' => $facility_item->facility_item_id, 'details' => $data);
			$details_after = json_encode($details_after);
			$update_facility_item_log_data = array(
				'user_id' => $this->user->user_id,
				'module_name' => 'FACILITY ITEMS',
				'table_name' => 'is_facility_items',
				'action' => 'UPDATE',
				'details_before' => $details_before,
				'details_after' => $details_after,
				'remarks' => "",
			);
										
			$this->tracking_model->insert_logs('admin', $update_facility_item_log_data);
		} 
		else // item does not exist 
		{
			// do nothing		
			$new_qty = 0;	
		}
		
		// insert to logs
		//if ($new_qty>0) 
		//{
			$this->insert_to_logs("deduct",$facility_id,$item_id,$qty,$new_qty,$unit_id,$transaction_code,$remarks,$user_id,$order_id,$package_id);
			//insert into facility item reports, qty released
			$existing = $this->get_facility_item_reports(array('facility_id' => $facility_id, 'item_id' => $item_id, 'insert_date' => date('Y-m-d')));
			if (empty($existing)) //no records of this item for today
			{
				$data = array(
					'facility_id' => $facility_id,
					'item_id' => $item_id,
					'qty_released' => $qty,
					'qty_received' => 0,
					'total_amount' => 0,
					'insert_date' => date('Y-m-d')
				);
				$this->insert_item_report($data);
			}else
			{
				$existing = $existing[0];
				$current_qty = $existing->qty_released;
				$data = array(
					'qty_released' => $current_qty + $qty,
				);
				$where = array('facility_item_report_id' => $existing->facility_item_report_id);
				$this->update_item_report($data, $where);
			}
		//}
		
		return $new_qty;
	}

	function add_to_pending($facility_id,$item_id,$qty)
	{
		$where = array(
			'facility_id' => $facility_id,
			'item_id' => $item_id,
		);
		$facility_item = $this->get_facility_items_by_facility_id_and_item_id($facility_id, $item_id);
		
		if($facility_item)
		{
			$data = array(
				'qty_pending' => $facility_item->qty_pending + $qty,
			);
			$this->update_facility_item($data, $where);
		}
		else
		{
			$item_details = $this->items_model->get_item_by_id($item_id);
			$data = array(
				'facility_id' => $facility_id,
				'item_id' => $item_id,
				'qty' => 0,
				'qty_pending' => $qty,
				'unit_id' => $item_details->unit_id
			);
			$this->insert_facility_item($data);
		}
		
		return;
	}
	
	function remove_from_pending($facility_id, $item_id, $qty)
	{
		$where = array(
			'facility_id' => $facility_id,
			'item_id' => $item_id
		);
		$facility_item = $this->get_facility_items_by_facility_id_and_item_id($facility_id, $item_id);
		
		$data = array(
			'qty_pending' => $facility_item->qty_pending - $qty,
		);
		
		$this->update_facility_item($data, $where);
		return;
	}

	function insert_to_logs($type,$facility_id,$item_id,$qty,$balance=0,$unit_id,$transaction_code,$remarks,$user_id,$order_id=0,$package_id=0)
	{
		$data = array(
			"facility_id" => $facility_id,
			"item_id" => $item_id,
			"order_id" => $order_id,
			"transaction_code" => $transaction_code,
			"qty" => $qty,
			"balance" => $balance,
			"unit_id" => $unit_id,
			"user_id" => $user_id,
			"remarks" => $remarks
		);
		
		if ($type=='add') 
		{
			$this->insert_items_receiving($data);						
		} 
		else // type = 'deduct'
		{
			$data["package_id"] = $package_id;
			$this->insert_items_releasing($data);			
		}
	}
	

	// items receiving
	function get_items_receiving($where = null, $limit = null, $orderby = null, $fields = null) 
	{
		$query = $this->fetch('items_receiving', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
    }

	function insert_items_receiving($data) 
	{
		return $this->insert('items_receiving', $data);
	}	

	function get_products_releasing_view($where = null, $limit = null, $orderby = null, $fields = null)
	{
		$query = $this->fetch('products_releasing', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
	}

	// items releasing
	function get_items_releasing($where = null, $limit = null, $orderby = null, $fields = null) 
	{
		$query = $this->fetch('items_releasing', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
    }

	function insert_items_releasing($data) 
	{
		return $this->insert('items_releasing', $data);
	}
	
	function get_facility_item_reports($where = null, $limit = null, $orderby = null, $fields = null)
	{
		$query = $this->fetch('facility_item_reports_view', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
	}
	
	function get_facility_item_reports_by_id($facility_item_report_id)
	{
		$result = $this->get_facility_item_reports(array('facility_item_report_id' => $facility_item_report_id));
		$row = NULL;
		if (count($result) > 0)
		{
			$row = $result[0];
		}
		return $row;
	}
	
	function insert_item_report($data)
	{
		return $this->insert('facility_item_reports', $data);
	}
	
	function update_item_report($data, $where)
	{
		return $this->update('facility_item_reports', $data, $where);
	}


	// FACILITY TYPES
	function get_facility_types($where = null, $limit = null, $orderby = null, $fields = null) 
	{
		$query = $this->fetch('facility_types', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
    }

	function insert_facility_type($data) 
	{
		return $this->insert('facility_types', $data);
	}

	function update_facility_type($data, $where) 
	{
		return $this->update('facility_types', $data, $where);
	}

	function delete_facility_type($where) 
	{
		return $this->delete('facility_types', $where);
	}

	function get_facility_type_by_id($facility_type_id) 
	{
		$result = $this->get_facility_types(array('facility_type_id' => $facility_type_id));
		$row = NULL;
		if (count($result) > 0) {
			$row = $result[0];
		}
		return $row;
	}
	
	function get_facility_items_daily($where = null, $limit = null, $orderby = null, $fields = null) 
	{
		$query = $this->fetch('facility_items_daily', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
    }

	function get_facility_item_daily_by_facility_id($facility_id) 
	{
		$result = $this->get_facility_items_daily(array('facility_id' => $facility_id));
		$row = NULL;
		if (count($result) > 0) {
			$row = $result[0];
		}
		return $row;
	}
	
	function get_facility_items_daily_by_item_id($item_id) 
	{
		$result = $this->get_facility_items_daily(array('item_id' => $item_id));
		$row = NULL;
		if (count($result) > 0) {
			$row = $result[0];
		}
		return $row;
	}
	
	function get_facility_items_daily_count($where = null) {
		// do a sql count instead of row count
		$query = $this->fetch('facility_items_daily', 'count(1) as cnt', $where);
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}
	
	function delete_facility_items_daily($where) 
	{
		return $this->delete('facility_items_daily', $where);
	}
	
	function get_items_released_daily($where = null, $limit = null, $orderby = null, $fields = null)
	{
		$query = $this->fetch('facility_items_releasing', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
	}
	
	function get_items_received_daily($where = null, $limit = null, $orderby = null, $fields = null)
	{
		$query = $this->fetch('facility_items_receiving', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
	}

	/* SERVICE DEPOT */ 
 	function get_service_depots($where = null, $limit = null, $orderby = null, $fields = null)  
 	{ 
		$query = $this->fetch('service_depots', $fields, $where, $orderby, $limit); 
		$row = $query->result(); 
		$query->free_result(); 
		return $row; 
	} 

 	function insert_service_depots($data)  
 	{ 
		return $this->insert('service_depots', $data); 
 	} 
	
 	function update_service_depots($data, $where)  
 	{ 
		return $this->update('service_depots', $data, $where); 
	} 
	
 	function delete_service_depots($where)  
	{ 
		return $this->delete('service_depots', $where); 
 	} 
}