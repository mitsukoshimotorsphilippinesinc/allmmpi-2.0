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

		$where = "status IN ('PENDING', 'PROCESSING')";

		//// get module_id
		//$module_id = get_department_module_id();


		/*if (empty($search_status)) {
			//$where = "status IN ('PENDING', 'FOR APPROVAL', 'FOR CANCELLATION', 'APPROVED', 'DENIED', 'DENIED (COMPLETED)', 'PROCESSING', 'ON PROCESS', 'COMPLETED', 'CANCELLED', 'CANCELLED (COMPLETED)', 'FORWARDED')";
			$where = "department_module_id = {$module_id}";
		} else {

			if ($search_status == 'ALL') {
				//$where = "status IN ('PENDING', 'FOR APPROVAL', 'FOR CANCELLATION', 'APPROVED', 'DENIED', 'DENIED (COMPLETED)', 'PROCESSING', 'ON PROCESS', 'COMPLETED', 'CANCELLED', 'CANCELLED (COMPLETED)', 'FORWARDED')";
				$where = "department_module_id = {$module_id}";
			} else {
				$where = "department_module_id = {$module_id} AND status = '". $search_status ."'";
			}
				
			if ($where != NULL) {
				if ($search_by == 'name')
					$where = $where . " AND ". $request_search_by ." IN (" . $where_id_numbers . ")";
				else
					$where = $where . " AND ". $search_by ." LIKE '%" . $search_text . "%'";
			} else {
				if ($search_by == 'name')
					$where = "department_module_id = {$module_id} AND " . $request_search_by ." IN (" . $where_id_numbers . ")";
				else
					$where = "department_module_id = {$module_id} AND" . $search_by ." LIKE '%" . $search_text . "%'";
			}
		}	*/

		// set pagination data
		$config = array(
				'pagination_url' => "/spare_parts/warehouse/reservation/",				
				'total_items' => $this->spare_parts_model->get_warehouse_reservation_count($where),
				'per_page' => 10,
				'uri_segment' => 4,
		);

		$this->pager->set_config($config);
		
		$transfers = $this->spare_parts_model->get_warehouse_reservation($where, array('rows' => $this->pager->per_page, 'offset' => $this->pager->offset), "insert_timestamp DESC");			
		
		// search vars
		$this->template->search_status = $search_status;
		$this->template->search_by = $search_by;
		$this->template->search_text = $search_text;
		$this->template->search_url = $search_url;
		$this->template->transfers = $transfers;
		
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

			$runner_details = $this->spare_parts_model->get_runner_view_by_id($runner_id);
			
			$html = "<p>Request with code <strong>{$request_code}</strong> has been assigned successfully to runner <strong>{$runner_details->complete_name}</strong>.</p>";

			$title = "Assigned Successfully :: Process Request";

		} else if ($process_action == "set_completed") {
			
			$data = array(
					"status" => 'COMPLETED',
					"update_timestamp" => $current_datetime,
					"tr_number" => $tr_number,
				);

			$where = "transaction_number = '{$request_code}'";
			$this->spare_parts_model->update_warehouse_reservation($data, $where);

			$data = array(
					"status" => 'COMPLETED',
					"update_timestamp" => $current_datetime,
				);

			$where = "request_summary_id = '{$request_summary_id}'";
			$this->spare_parts_model->update_request_summary($data, $where);

			$data = array(
					"reference_number" => $tr_number,
					"status" => 'COMPLETED',
				);

			$where_data = array(
					"warehouse_id" => 1, // DEFAULT MUNA
					"warehouse_reservation_id" => $warehouse_reservation_details->warehouse_reservation_id,					
				);

			$this->spare_parts_model->update_warehouse_transaction($data, $where_data);

			$runner_details = $this->spare_parts_model->get_runner_view_by_id($runner_id);
			
			$html = "<p>Request with code <strong>{$request_code}</strong> has been set as <strong>COMPLETED.</strong></p>";

			$title = "Set As Completed :: Process Request";

		} else {
			// considered as cancelled
			// TODO: CANCELLED

		}


		$this->return_json("1", "OK.", array("html" => $html, "title" => $title));
		return;
	}
}	