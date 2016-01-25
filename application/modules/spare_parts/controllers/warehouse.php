<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Warehouse extends Admin_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->library('PHPExcel');
        $this->load->library('PHPExcel/IOFactory');
	
		$this->load->model('spare_parts_model');
		$this->load->model('human_relations_model');
		$this->load->model('warehouse_model');
		$this->load->library('pager');		
		$this->load->helper("spare_parts_helper");
		$this->load->helper("breadcrumb_helper");
		$this->load->helper("systems_helper");

		$this->db_spare_parts = $this->load->database('spare_parts', TRUE);

	}

	public $segment_name = "warehouse";

	public function index()
	{		
		$this->template->view('warehouse/dashboard');
	}

	public function reservation()
	{
		
		$search_status = trim($this->input->get("search_status"));
		$search_by = trim($this->input->get("search_option"));
		$search_text = trim($this->input->get("search_string"));
		$warehouse_id = abs($this->input->get("warehouse_id"));

		$warehouse_details = $this->spare_parts_model->get_warehouse_personnel_view_by_id_number($this->user->id_number);
		
		if ($warehouse_id == "") {
			if (empty($warehouse_details)) {
				// TODO - No ACCESS
				$warehouse_id = 0;
			} else {				
					$warehouse_id = $warehouse_details[0]->warehouse_id;				
			}
		} 
		
		$warehouse_where = $warehouse_id;
		
		$search_url = "";
		$count_is = 0;
		$transfers = "";
		$request_search_by = "";	

		if ($search_by == 'name') {
			$request_search_by = "id_number";

			// get all personal_information_id in pm_personal_information
			$where = "complete_name LIKE '%" . $search_text . "%'";
			$personal_information_details = $this->human_relations_model->get_personal_information($where, NULL, NULL, "personal_information_id, complete_name");

			$where_id_numbers = "";
			$count_id_num = 0;
			// get the id_numbers within the personal_information_id results above
			if (count($personal_information_details) > 0) {
				foreach ($personal_information_details as $pid) {
					
					$employment_information_details = $this->human_relations_model->get_employment_information("personal_information_id = ". $pid->personal_information_id);
					
					if (count($employment_information_details) > 0) {
						foreach ($employment_information_details as $eid) {
							if ($count_id_num == 0)
								$where_id_numbers = "'" . $eid->id_number . "'";
							else 		
								$where_id_numbers = $where_id_numbers . ", '" . $eid->id_number . "'";

							$count_id_num++;
						}
					}
				}	
			}
		} 

		$where = "d.warehouse_id IN (" . $warehouse_where . ") AND a.status IN ('PENDING', 'PROCESSING')";

		$sql = "SELECT a.*, 
				d.item_id,
				d.warehouse_id,
				d.sku,
				d.good_quantity,
				d.bad_quantity,
				d.rack_location 
				FROM 
					is_warehouse_reservation a
				LEFT JOIN 
					is_request_summary b ON a.transaction_number = b.request_code
				LEFT JOIN  
					is_request_detail c ON b.request_summary_id = c.request_summary_id
				LEFT JOIN  
					is_item d ON c.item_id = d.item_id 
				WHERE 
					{$where}
				ORDER BY	
					FIELD(a.status, 'PENDING', 'PROCESSING'), a.insert_timestamp ASC";

		$query = $this->db_spare_parts->query($sql);
		$transfers = $query->result();			
		$query->free_result();

		// set pagination data
		$config = array(
				'pagination_url' => "/spare_parts/warehouse/reservation/",				
				//'total_items' => $this->spare_parts_model->get_warehouse_reservation_count($where),
				'total_items' => count($transfers),
				'per_page' => 30,
				'uri_segment' => 4,
		);

		$this->pager->set_config($config);
		
		$current_offset = $this->pager->per_page * $this->pager->offset;

		//$transfers = $this->spare_parts_model->get_warehouse_reservation($where, array('rows' => $this->pager->per_page, 'offset' => $this->pager->offset), "insert_timestamp DESC");			

		$sql = "SELECT a.*, 
				d.item_id,
				d.warehouse_id,
				d.sku,
				d.good_quantity,
				d.bad_quantity,
				d.rack_location 
				FROM 
					is_warehouse_reservation a
				LEFT JOIN 
					is_request_summary b ON a.transaction_number = b.request_code
				LEFT JOIN  
					is_request_detail c ON b.request_summary_id = c.request_summary_id
				LEFT JOIN  
					is_item d ON c.item_id = d.item_id 
				WHERE 
					{$where}
				ORDER BY	
					FIELD(a.status, 'PENDING', 'PROCESSING'), a.insert_timestamp ASC	
				LIMIT 
					{$this->pager->per_page} 
				OFFSET 
					{$current_offset}";	

		$query = $this->db_spare_parts->query($sql);
		$transfers = $query->result();			
		$query->free_result();			

		// search vars
		$this->template->search_status = $search_status;
		$this->template->search_by = $search_by;
		$this->template->search_text = $search_text;
		$this->template->search_url = $search_url;
		$this->template->transfers = $transfers;
		$this->template->warehouse_details = $warehouse_details;
		$this->template->warehouse_id = $warehouse_id;
				
		$this->template->view('warehouse/reservation_listing');	
	}

	public function initial_process()
	{
		$request_summary_id = $this->input->post("request_summary_id");
		$request_code = $this->input->post("request_code");
		$process_action = $this->input->post("process_action");

		if ($process_action == "assign_runner") {
			$runner_view_details = $this->spare_parts_model->get_runner_view("is_active = 1");

			$runner_options = array();
			$runner_options = array('0' => 'None');
			foreach ($runner_view_details as $rd) {
			 	$runner_options[$rd->runner_id] = $rd->complete_name;
			}				
			
			$html = "<p>Please select a runner:</p>";
			$html .= form_dropdown('runner_name',$runner_options, NULL,'id="runner_name" class="span4"');
			$html .= "<br/><span id='error-runner' style='color:red;display:none;'>Remarks/Runner is required.</span>";

			$title = "Select Runner :: Process Request";
		
		} else if ($process_action == "set_completed") {

			$html = "<p>You are about to set this request with Request Code <strong>{$request_code}</strong> as COMPLETED.</p>
					<p><strong>TR NUMBER:</strong> <input id='tr_number' style='margin-top:5px' placeholder='TR Number Here...'/></p>
					<span id='error-tr-number' style='color:red;display:none;'>TR Number is required.</span>
					<p>Are you sure you want to continue?</p>";

			$title = "Set As Completed :: Process Request";
		
		} else {

			$html = "<p>You are about to set this request with Request Code <strong>{$request_code}</strong> as CANCELLED.</p><p>Are you sure you want to continue?</p>";

			$title = "Set As Cancelled :: Process Request";
		}	

		$this->return_json("1", "OK.", array("html" => $html, "title" => $title));
		return;
	}

	public function proceed_request()
	{
		$request_summary_id = $this->input->post("request_summary_id");
		$request_code = $this->input->post("request_code");
		$process_action = $this->input->post("process_action");
		$runner_id = $this->input->post("runner_id");
		$tr_number = $this->input->post("tr_number");
		$warehouse_id = $this->input->post("warehouse_id");

		$current_datetime = date("Y-m-d H:i:s");

		$warehouse_reservation_details = $this->spare_parts_model->get_warehouse_reservation_by_code($request_code);

		if ($process_action == "assign_runner") {
			// update is_warehouse_reservation, set status = PROCESSING
			$data = array(
					"status" => 'PROCESSING',
					"update_timestamp" => $current_datetime
				);

			$where = "transaction_number = '{$request_code}'";

			$this->spare_parts_model->update_warehouse_reservation($data, $where);			

			// insert to is_warehouse_transaction
			$data = array(
					"warehouse_id" => '1',
					"reference_type" => 'TR',
					"warehouse_reservation_id" => $warehouse_reservation_details->warehouse_reservation_id,
					"runner_id" => $runner_id,
					"status" => 'PROCESSING'
				);	

			$this->spare_parts_model->insert_warehouse_transaction($data);

			// update is_request_summary
			$data = array(
					"status" => "PROCESSING",
					"update_timestamp" => $current_datetime
				);

			$this->spare_parts_model->update_request_summary($data, array("request_summary_id" => $request_summary_id));

			// update is_request_detail
			$sql = "SELECT 
						a.request_detail_id
					FROM 
						is_request_detail a
					LEFT JOIN 
						is_item b ON a.item_id = b.item_id
					WHERE 
						a.request_summary_id = {$request_summary_id}
					AND 
						b.warehouse_id = {$warehouse_id}";

			$query = $this->db_spare_parts->query($sql);
			$request_detail_list = $query->result();			
			$query->free_result();

			$data = array(
					"status" => "PROCESSING",
					"update_timestamp" => $current_datetime
				);

			foreach ($request_detail_list as $rdl) {
				$this->spare_parts_model->update_request_detail($data,  array("request_detail_id" => $rdl->request_detail_id));
			}

			$runner_details = $this->spare_parts_model->get_runner_view_by_id($runner_id);
			
			$html = "<p>Request with code <strong>{$request_code}</strong> has been assigned successfully to runner <strong>{$runner_details->complete_name}</strong>.</p>";

			$title = "Assigned Successfully :: Process Request";

		} else if ($process_action == "set_completed") {

			// update is_request_detail
			$sql = "SELECT 
						a.request_detail_id
					FROM 
						is_request_detail a
					LEFT JOIN 
						is_item b ON a.item_id = b.item_id
					WHERE 
						a.request_summary_id = {$request_summary_id}
					AND 
						b.warehouse_id = {$warehouse_id}";

			$query = $this->db_spare_parts->query($sql);
			$request_detail_list = $query->result();			
			$query->free_result();

			$data = array(
					"status" => "COMPLETED",
					"update_timestamp" => $current_datetime
				);

			foreach ($request_detail_list as $rdl) {
				$this->spare_parts_model->update_request_detail($data,  array("request_detail_id" => $rdl->request_detail_id));
			}

			// check if all items are COMPLETED
			$request_item_details = $this->spare_parts_model->get_request_detail(array("request_summary_id" => $request_summary_id));

			$not_all_completed = 0;

			foreach ($request_item_details as $rid) {
				if (!($rid->status == "COMPLETED")) {
					$not_all_completed = 1;
					break;
				}
			}

			var_dump($not_all_completed);

			if ($not_all_completed == 0) {
				// update is_repair_summary = "PROCESSED"
				$data = array(
						"status" => "PROCESSED"
					);

				$this->spare_parts_model->update_request_summary($data, array("request_summary_id"=> $request_summary_id));
			
				$data = array(
						"status" => 'COMPLETED',
						"update_timestamp" => $current_datetime,
						"tr_number" => $tr_number,
					);

				$where = "transaction_number = '{$request_code}'";
				$this->spare_parts_model->update_warehouse_reservation($data, $where);
			}

			//$data = array(
			//		"status" => 'COMPLETED',
			//		"update_timestamp" => $current_datetime,
			//	);

			//$where = "request_summary_id = '{$request_summary_id}'";
			//$this->spare_parts_model->update_request_summary($data, $where);

			$data = array(
					"reference_number" => $tr_number,
					"status" => 'COMPLETED',
				);

			$where_data = array(
					"warehouse_id" => $warehouse_id, // DEFAULT MUNA
					"warehouse_reservation_id" => $warehouse_reservation_details->warehouse_reservation_id,					
				);

			$this->spare_parts_model->update_warehouse_transaction($data, $where_data);

			$runner_details = $this->spare_parts_model->get_runner_view_by_id($runner_id);
			
			$html = "<p>Request with code <strong>{$request_code}</strong> has been set as <strong>COMPLETED.</strong></p>";

			$title = "Set As Completed :: Process Request";

		} else {

			// ????????????????????????

			// considered as cancelled
			// 20160119

			// cancel item of specific warehouse (or request_detail_id)

			// update status CANCELLED

			// return inventory

			// if other items are CANCELLED, set to CANCELLED

			// ?????????????????????????

		}


		$this->return_json("1", "OK.", array("html" => $html, "title" => $title));
		return;
	}

	public function view_details()
	{
		$request_summary_id = $this->input->post("request_summary_id");
		$request_code = $this->input->post("request_code");
		$listing_action = $this->input->post("listing_action");
		$warehouse_id = $this->input->post("warehouse_id");
		
		$request_summary = $this->spare_parts_model->get_request_summary_by_id($request_summary_id);		

		if (empty($request_summary)) {		
			$html = "<p>There is something wrong with this transaction [Request Code: {$request_code}].</p>";
			$title = "Error: View Details";

			$this->return_json("0","Request Code not found in DB", array("html" => $html, "title" => $title));	
			
		} else {
			
			$where = "c.request_summary_id = {$request_summary_id} AND d.warehouse_id = {$warehouse_id}";
			//$segment_request_details = $this->spare_parts_model->get_request_detail($where);

			$sql = "SELECT c.*, 
				d.item_id,
				d.warehouse_id,
				d.sku,
				d.good_quantity,
				d.bad_quantity,
				d.rack_location 
				FROM 					
					is_request_detail c
				LEFT JOIN  
					is_item d ON c.item_id = d.item_id 
				WHERE 
					{$where}";	

			$query = $this->db_spare_parts->query($sql);
			$segment_request_details = $query->result();			
			$query->free_result();		
			
			$department_module_details = $this->spare_parts_model->get_department_module_by_segment_name($this->segment_name);	

			// check if has items for return
			$where = "department_module_id = ". $request_summary->department_module_id ." AND request_id = ". $request_summary_id ." AND status NOT IN ('CANCELLED')";		
			$reprocessed_item_details = $this->spare_parts_model->get_reprocessed_item($where);

			$data = array(				
				'segment_request_summary' => $request_summary,
				'segment_request_details' =>$segment_request_details,
				'listing_action' => $listing_action,
				'segment_request_summary_remarks' => $request_summary->remarks,
				'segment_name' => $this->segment_name,
				'reprocessed_item_details' => $reprocessed_item_details,
				'department_module_details' => $department_module_details,
			);

			$html = $this->load->view("template_view_details",$data, true);
			 
			$title = "View Details :: " . $request_code;
			$this->return_json("1","View Details Request Summary", array("html" => $html, "title" => $title, "request_status" => $request_summary->status));
			
		}
			
		return;
	}
}	