<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Warehouse_request extends Admin_Controller {

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

		$this->db_spare_parts = $this->load->database('spare_parts', TRUE);

	}

	public $segment_name = "warehouse_request";

	public function index()
	{		
		$this->template->view('warehouse_request/dashboard');
	}

	public function approval()
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

		if (empty($search_status)) {
			$where = "status IN ('FOR APPROVAL', 'APPROVED', 'DENIED', 'CANCELLATION-FOR APPROVAL', 'CANCELLATION-APPROVED', 'CANCELLATION-DENIED')";
			//$where = "";
		} else {

			if ($search_status == 'ALL') {
				$where = "status IN ('FOR APPROVAL', 'APPROVED', 'DENIED', 'CANCELLATION-FOR APPROVAL', 'CANCELLATION-APPROVED', 'CANCELLATION-DENIED')";
				//$where = "";
			} else {
				$where = "status = '". $search_status ."'";
			}			
		
			if ($where != NULL) {
				if ($search_by == 'name')
					$where = $where . " AND ". $request_search_by ." IN (" . $where_id_numbers . ")";
				else
					$where = $where . " AND ". $search_by ." LIKE '%" . $search_text . "%'";
			} else {
				if ($search_by == 'name')
					$where = $request_search_by ." IN (" . $where_id_numbers . ")";
				else
					$where = $search_by ." LIKE '%" . $search_text . "%'";
			} 	
		}	

		// set pagination data
		$config = array(
				'pagination_url' => "/spare_parts/warehouse_request/approval/",
				'total_items' => $this->spare_parts_model->get_warehouse_request_count($where),
				'per_page' => 10,
				'uri_segment' => 4,
		);

		$this->pager->set_config($config);

		$transfers = $this->spare_parts_model->get_warehouse_request($where, array('rows' => $this->pager->per_page, 'offset' => $this->pager->offset), "insert_timestamp DESC");			
		
		// search vars
		$this->template->search_status = $search_status;
		$this->template->search_by = $search_by;
		$this->template->search_text = $search_text;
		$this->template->search_url = $search_url;
		$this->template->transfers = $transfers;		
		$this->template->view('warehouse_request/approval');	
		
	}	


	public function for_approval_confirm()
	{
		$warehouse_request_id = $this->input->post("warehouse_request_id");
		$warehouse_request_code = $this->input->post("warehouse_request_code");
		$is_approved = $this->input->post("is_approved");

		$warehouse_request = $this->spare_parts_model->get_warehouse_request_by_id($warehouse_request_id);

		if (empty($warehouse_request)) {		
			$html = "<p>There is something wrong with this transaction [Request Code: {$warehouse_request_code}].</p>";
			$title = "Error: Confirm Approval";

			$this->return_json("0","Warehouse Request Code not found in DB", array("html" => $html, "title" => $title));			

		} else {

			if ($is_approved == 'yes') {							
				$html = "You are about to approve the <b>" . $warehouse_request->status . "</b> Warehouse Request with Request Code: <strong>" . $warehouse_request_code . "</strong>. <br/><br/>Do you want to continue?";
				$title = "Confirm Approval :: " . $warehouse_request_code;
			} else {
				$html = "<p>You are about to deny the <b>" . $warehouse_request->status . "</b> Warehouse Request with Request Code: <strong>" . $warehouse_request_code . "</strong>. <br/>
							<div id='reasonremarks-container'>
								<span><strong>Reason/Remarks:</strong></span></br>
								<input id='txt-remarks' style='width:400px;'/><br/>
								<span id='error-reasonremarks' style='color:red;display:none'>Remarks/Reason is required.</span>
							</div>	
							<br/>
							Do you want to continue?</p>";
				$title = "Confirm Disapproval :: " . $warehouse_request_code;			
			}			
				
			$data = array (
				'warehouse_request_id' => $warehouse_request_id,
				'warehouse_request_code' => $warehouse_request_code,
				'is_approved' => $is_approved
				);	

			$this->return_json("1","Confirm Approval of Warehouse Request.",array("html" => $html, "title" => $title, "data" => $data));
		
		}
		
		return;
	}
	
	public function for_approval_proceed()
	{
		$warehouse_request_id = $this->input->post("warehouse_request_id");
		$warehouse_request_code = $this->input->post("warehouse_request_code");
		$is_approved = $this->input->post("is_approved");
		$remarks =  $this->input->post("remarks");
		
		$warehouse_request = $this->spare_parts_model->get_warehouse_request_by_id($warehouse_request_id);		

		if (empty($warehouse_request)) {		
			$html = "<p>There is something wrong with this transaction [Request Code: {$warehouse_request_code}].</p>";
			$title = "Error: Proceed Approval";

			$this->return_json("0","Warehouse Request Code not found in DB", array("html" => $html, "title" => $title));	
			
		} else {
	
			// change status to DENIED
			$current_datetime = date("Y-m-d H:i:s");
			
			if ($is_approved == 'no') {
				$new_remarks = "[" . $current_datetime . "] " . $remarks . "\n" . $warehouse_request->remarks;

				$data = array(				
					'approved_by' => $this->user->user_id,
					'remarks' => $new_remarks,
					'approve_timestamp' => $current_datetime
				);			

				if ($warehouse_request->status == 'FOR APPROVAL') {

					// from spare_parts helper
					$return_html = return_reserved_items($warehouse_request_code, 'DENIED', $remarks);
					$data['status'] = "DENIED";

				} else if ($warehouse_request->status == 'CANCELLATION-FOR APPROVAL') {
					
					$return_html = return_reserved_items($warehouse_request_code, 'CANCELLATION-DENIED', $remarks);
					$data['status'] = "CANCELLATION-DENIED";					
				
				}

				$html = "You have denied the <b>" . $warehouse_request->status . "</b> Warehouse Request with Request Code: <strong>{$warehouse_request_code}</strong>.";
				$title = "Request Denied :: " . $warehouse_request_code;			

			} else {

				$data = array(						
					'approved_by' => $this->user->user_id,					
					'approve_timestamp' => $current_datetime
				);

				if ($warehouse_request->status == 'FOR APPROVAL') {
					$data['status'] = "APPROVED";
				} else if ($warehouse_request->status == 'CANCELLATION-FOR APPROVAL') {
					$data['status'] = "CANCELLATION-APPROVED";
				}
				
				$html = "You have successfully approved the <b>" . $warehouse_request->status . "</b> Warehouse Request with Request Code: <strong>{$warehouse_request_code}</strong>.";
				$title = "Request Approved :: " . $warehouse_request_code;
			}
			
			$where = "warehouse_request_id = " . $warehouse_request_id;
			$this->spare_parts_model->update_warehouse_request($data, $where);

			$this->return_json("1","Successful Approval/Disapproval of Warehouse Request.",array("html" => $html, "title" => $title));
						
		}	
		return;	
	}


	public function view_details()
	{
		$warehouse_request_id = $this->input->post("warehouse_request_id");
		$warehouse_request_code = $this->input->post("warehouse_request_code");
		$listing_action = $this->input->post("listing_action");
		
		$warehouse_request = $this->spare_parts_model->get_warehouse_request_by_id($warehouse_request_id);		

		if (empty($warehouse_request)) {		
			$html = "<p>There is something wrong with this transaction [Request Code: {$warehouse_request_code}].</p>";
			$title = "Error: View Details";

			$this->return_json("0","Warehouse Request Code not found in DB", array("html" => $html, "title" => $title));	
			
		} else {

			$where = "warehouse_request_id = {$warehouse_request_id}";
			$warehouse_request_details = $this->spare_parts_model->get_warehouse_request_detail($where);
			
			$department_module_details = $this->spare_parts_model->get_department_module_by_segment_name($this->segment_name);	

			// check if has items for return
			$where = "department_module_id = ". $department_module_details->department_module_id ." AND request_id = ". $warehouse_request_id ." AND status NOT IN ('CANCELLED')";		
			$reprocessed_item_details = $this->spare_parts_model->get_reprocessed_item($where);

			$data = array(
				//'warehouse_request' => $warehouse_request,
				'segment_request_summary' => $warehouse_request,
				'segment_request_details' =>$warehouse_request_details,
				'listing_action' => $listing_action,
				'segment_request_summary_remarks' => $warehouse_request->remarks,
				'segment_name' => $this->segment_name,
				'reprocessed_item_details' => $reprocessed_item_details,
				'department_module_details' => $department_module_details,
			);

			$html = $this->load->view("template_view_details",$data,true);
			 
			$title = "View Details :: " . $warehouse_request_code;
			$this->return_json("1","View Details Warehouse Request", array("html" => $html, "title" => $title, "request_status" => $warehouse_request->status));
			
		}
			
		return;
	}


	public function listing()
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

		if (empty($search_status)) {
			//$where = "status IN ('PENDING', 'FOR APPROVAL', 'FOR CANCELLATION', 'APPROVED', 'DENIED', 'DENIED (COMPLETED)', 'PROCESSING', 'ON PROCESS', 'COMPLETED', 'CANCELLED', 'CANCELLED (COMPLETED)', 'FORWARDED')";
			$where = "";
		} else {

			if ($search_status == 'ALL') {
				//$where = "status IN ('PENDING', 'FOR APPROVAL', 'FOR CANCELLATION', 'APPROVED', 'DENIED', 'DENIED (COMPLETED)', 'PROCESSING', 'ON PROCESS', 'COMPLETED', 'CANCELLED', 'CANCELLED (COMPLETED)', 'FORWARDED')";
				$where = "";
			} else {
				$where = "status = '". $search_status ."'";
			}
				
			if ($where != NULL) {
				if ($search_by == 'name')
					$where = $where . " AND ". $request_search_by ." IN (" . $where_id_numbers . ")";
				else
					$where = $where . " AND ". $search_by ." LIKE '%" . $search_text . "%'";
			} else {
				if ($search_by == 'name')
					$where = $request_search_by ." IN (" . $where_id_numbers . ")";
				else
					$where = $search_by ." LIKE '%" . $search_text . "%'";
			}
		}	

		// set pagination data
		$config = array(
				'pagination_url' => "/spare_parts/warehouse_request/listing/",
				'total_items' => $this->spare_parts_model->get_warehouse_request_count($where),
				'per_page' => 10,
				'uri_segment' => 4,
		);

		$this->pager->set_config($config);

		$transfers = $this->spare_parts_model->get_warehouse_request($where, array('rows' => $this->pager->per_page, 'offset' => $this->pager->offset), "insert_timestamp DESC");			
		
		// search vars
		$this->template->search_status = $search_status;
		$this->template->search_by = $search_by;
		$this->template->search_text = $search_text;
		$this->template->search_url = $search_url;
		$this->template->transfers = $transfers;
		
		$this->template->view('warehouse_request/listing');	
		
	}	

	public function for_listing_confirm()
	{
		$warehouse_request_id = $this->input->post("warehouse_request_id");
		$warehouse_request_code = $this->input->post("warehouse_request_code");
		$listing_action = $this->input->post("listing_action");

		$warehouse_request = $this->spare_parts_model->get_warehouse_request_by_id($warehouse_request_id);

		if (empty($warehouse_request)) {		
			$html = "<p>There is something wrong with this transaction [Request Code: {$warehouse_request_code}].</p>";
			$title = "Error: Confirm";

			$this->return_json("0","Warehouse Request Code not found in DB", array("html" => $html, "title" => $title));			

		} else {

			if ($listing_action == 'for approval') {
				$title = "File For Approval :: " . $warehouse_request_code;
				$html = "You are about to forward the request for approval with Request Code: <strong>" . $warehouse_request_code . "</strong>. Do you want to continue?";
			}

			if (($listing_action == 'forward to warehouse') || ($listing_action == 'cancellation-forward to warehouse')) {
				$title = "Forward To Warehouse :: " . $warehouse_request_code;
				$html = "<p>You are about to forward the request to Warehouse with Request Code: <strong>" . $warehouse_request_code . "</strong>. <br/>							
							<br/>
							Do you want to continue?</p>";
			}

			if ($listing_action == 'assign mtr') {
				$title = "Assign MTR Number :: " . $warehouse_request_code;
				$html = "<p>Enter a Purchase Order Number for Request Code : <strong>" . $warehouse_request_code . "</strong>. <br/>
							<div id='reasonremarks-container'>
								<span><strong>MTR Number:</strong></span></br>
								<input id='txt-mtrnumber' style='width:100px;' maxlength='10' placeholder='1234567890' /><br/>
								<span id='error-mtrnumber' style='color:red;display:none'>P.O. Number is required.</span>
							</div>	
							<br/>
							Click Proceed to continue...</p>";
			}

			if ($listing_action == 'cancel') {
				$title = "Cancel Request :: " . $warehouse_request_code;
				$html = "<p>You are about to cancel the Warehouse Request with Request Code: <strong>" . $warehouse_request_code . "</strong>. <br/>
							<div id='reasonremarks-container'>
								<span><strong>Reason/Remarks:</strong></span></br>
								<input id='txt-remarks' style='width:400px;' maxlength='320' placeholder='Put remarks here...' /><br/>
								<span id='error-reasonremarks' style='color:red;display:none'>Remarks/Reason is required.</span>
							</div>	
							<br/>
							Do you want to continue?</p>";
			}

			if ($listing_action == 'cancel completed') {
				$title = "For Approval - Cancel Completed Request :: " . $warehouse_request_code;
				$html = "You are about to request the approval of <b>Cancellation of a Completed Request</b> with Request Code: <strong>" . $warehouse_request_code . "</strong>.<br/><br/>Do you want to continue?";
			}	
	
			$data = array (
				'warehouse_request_id' => $warehouse_request_id,
				'warehouse_request_code' => $warehouse_request_code,
				'listing_action' => $listing_action
				);	

			$this->return_json("1","Confirm Action of Warehouse Request.",array("html" => $html, "title" => $title, "data" => $data));
		
		}
		
		return;
	}

	public function for_listing_proceed()
	{
		$warehouse_request_id = $this->input->post("warehouse_request_id");
		$warehouse_request_code = $this->input->post("warehouse_request_code");
		$listing_action = $this->input->post("listing_action");
		$remarks =  $this->input->post("remarks");
		$mtr_number =  abs($this->input->post("mtr_number"));
		
		$warehouse_request = $this->spare_parts_model->get_warehouse_request_by_id($warehouse_request_id);		

		if (empty($warehouse_request)) {		
			$html = "<p>There is something wrong with this transaction [Request Code: {$warehouse_request_code}].</p>";
			$title = "Error: Proceed";

			$this->return_json("0","Warehouse Request Code not found in DB", array("html" => $html, "title" => $title));	
			
		} else {
	
			// change status to DENIED
			$current_datetime = date("Y-m-d H:i:s");
			
			if ($listing_action == 'cancel') {
				$new_remarks = "[" . $current_datetime . "] " . $remarks . "\n" . $warehouse_request->remarks;

				// from spare_parts helper
				$return_html = return_reserved_items($warehouse_request_code, 'CANCELLED', $remarks);

				$data = array(
					'status' => "CANCELLED",
					'approved_by' => $this->user->user_id,
					'remarks' => $new_remarks,
					'approve_timestamp' => $current_datetime
				);

				$html = "You have cancelled the Warehouse Request Code: <strong>{$warehouse_request_code}</strong>.";
				$title = "Cancelled :: " . $warehouse_request_code;	

			} else if ($listing_action == 'for approval') {

				// change status to FOR APPROVAL
				$data = array(
					'status' => "FOR APPROVAL",
					'approved_by' => $this->user->user_id,					
					'approve_timestamp' => $current_datetime
				);

				$html = "You have successfully filed the request for approval with Warehouse Request Code: <strong>{$warehouse_request_code}</strong>.";
				$title = "File For Approval :: " . $warehouse_request_code;
			
			} else if ($listing_action == 'forward to warehouse') {

				// change status to FORWARDED
				$data = array(
					'status' => "FORWARDED",
					'approved_by' => $this->user->user_id,					
					'approve_timestamp' => $current_datetime,
					'mtr_number' =>	 $mtr_number
				);

				$html = "You have successfully forwaded the request to warehouse with Request Code: <strong>{$warehouse_request_code}</strong>.";
				$title = "Forward To Warehouse :: " . $warehouse_request_code;
			
				// change tr_warehouse_reservation to PENDING
				$data_reservation = array(
					'status' => "PENDING",				
					'update_timestamp' => $current_datetime
				);

				$where = "transaction_number = '{$warehouse_request_code}'";
				$this->spare_parts_model->update_warehouse_reservation($data_reservation, $where);

			} else if ($listing_action == 'cancellation-forward to warehouse') {

				// change status to FORWARDED
				$data = array(
					'status' => "CANCELLATION-FORWARDED",
					'approved_by' => $this->user->user_id,					
					'approve_timestamp' => $current_datetime,
					'mtr_number' =>	 $mtr_number
				);

				$html = "You have successfully forwaded the request to warehouse with Request Code: <strong>{$warehouse_request_code}</strong>.";
				$title = "Forward To Warehouse :: " . $warehouse_request_code;

				//spare_parts_helper
				$return_val = return_items_to_process($warehouse_request_id, $warehouse_request_code);

				/*$data_reservation = array(
					'status' => "PENDING",				
					'update_timestamp' => $current_datetime
				);

				$where = "transaction_number = '{$warehouse_request_code}'";
				$this->spare_parts_model->update_warehouse_reservation($data_reservation, $where);	
				*/

			} else if ($listing_action == 'assign mtr') {

				// change status to FOR APPROVAL
				$data = array(					
					'update_timestamp' => $current_datetime,
					'mtr_number' => $mtr_number
				);

				$html = "You have successfully assigned a MTR Number to the request with Request Code: <strong>{$warehouse_request_code}</strong>.";
				$title = "Assign MTR Number :: " . $warehouse_request_code;
			
			}

			if ($listing_action == 'cancel completed') {
				$data = array(
					'status' => "CANCELLATION-FOR APPROVAL",
					'approved_by' => $this->user->user_id,					
					'approve_timestamp' => $current_datetime
				);

				$html = "You have successfully filed the request for approval with Warehouse Request Code: <strong>{$warehouse_request_code}</strong>.";
				$title = "For Approval - Cancel Completed Request :: " . $warehouse_request_code;
			}
			
			$where = "warehouse_request_id = " . $warehouse_request_id;
			$this->spare_parts_model->update_warehouse_request($data, $where);
	
		}	

		$this->return_json("1","Successful Approval of Warehouse Request.",array("html" => $html, "title" => $title));

		return;	
	}




	
	public function download_check()
	{
		$start_date = trim($this->input->post("start_date"));
		$end_date = trim($this->input->post("end_date"));

		// check if start_date and end_date is null
		if (((empty($start_date)) && (empty($start_date))) || (($start_date == NULL) && ($start_date == NULL)))
		{
			$this->return_json("error","Enter both Start Date and End Date.");
			return;
		}

		if ((empty($start_date)) || ($start_date == NULL))
		{
			$this->return_json("error","Enter Start Date.");
			return;
		}

		if ((empty($end_date)) || ($end_date == NULL))
		{
			$this->return_json("error","Enter End Date.");
			return;
		}

		// check if start_date is greater than end_date
		if ($start_date > $end_date)
		{
			$this->return_json("error","Start Date must not exceed End Date.");
			return;
		}

		$current_date = date("Y-n-j");


		// start date must not exceed the current date
		if ($start_date > $current_date)
		{
			$this->return_json("error","Start Date must not exceed Current Date.");
			return;
		}

		$proper_start_date = date('F d, Y', strtotime($start_date));
		$proper_end_date = date('F d, Y', strtotime($end_date));

		// check if query will return records to execute
		$where = "insert_timestamp BETWEEN '$start_date' AND '$end_date'";

		$pending_count = $this->spare_parts_model->get_warehouse_request($where);

		if (empty($pending_count))
		{
			$return_html = "<span>No Dealer Request from <strong>{$proper_start_date}</strong> to <strong>{$proper_end_date}</strong>.</span>";
			$this->return_json("ok",$return_html);
			return;
		} else
		{
			$return_html = "<span>You are to generate a Dealer Requests Report ranging from <strong>{$proper_start_date}</strong> to <strong>{$proper_end_date}</strong>.<br/>Do you want to proceed?</span>";
			$this->return_json("ok",$return_html);
			return;
		}
	}

	public function download_proceed()
	{
		$start_date = trim($this->input->post("start_date"));
		$end_date = trim($this->input->post("end_date"));

		$current_timestamp = date('Y-m-d H:i:s');

		$return_html = "<span>Request Completed.<br/><br/>You may now download the generated spreadsheet file.</span>";

		$this->return_json("ok",$return_html);
		return;

	}

	function export_xls($start_date,$end_date, $search_status = NULL, $search_by = NULL, $search_text = NULL)
	{
		$this->load->library('PHPExcel');
        $this->load->library('PHPExcel/IOFactory');
		
		try {
			
			// adjusting memory limit to accomodate PHPExcel processing 
			set_time_limit(0); // eliminating the timeout
			ini_set('memory_limit', '512M');

	        $objPHPExcel = new PHPExcel();
	        $objPHPExcel->getProperties()->setTitle("dealer requests")->setDescription("none");
			$start_column_num = 3;

			$worksheet = $objPHPExcel->setActiveSheetIndex(0);

			//$where = "insert_timestamp BETWEEN '$start_date' AND '$end_date'";

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

			if (empty($search_status)) {
				//$where = "status IN ('PENDING','FOR APPROVAL', 'APPROVED', 'FORWARDED', FOR CANCELLATION', 'CANCELLED', 'CANCELLED (COMPLETED)', 'DENIED', 'DENIED (COMPLETED)', 'COMPLETED')";
				$where = "";
			} else {

				if ($search_status == 'ALL') {
					//$where = "status IN ('PENDING','FOR APPROVAL', 'APPROVED', 'FORWARDED', FOR CANCELLATION', 'CANCELLED', 'CANCELLED (COMPLETED)', 'DENIED', 'DENIED (COMPLETED)', 'COMPLETED')";
					$where = "";
				} else {
					$where = "status = '". $search_status ."'";
				}			
			
				if ($where != NULL) {
					if ($search_by == 'name')
						$where = $where . " AND ". $request_search_by ." IN (" . $where_id_numbers . ")";
					else
						$where = $where . " AND ". $search_by ." LIKE '%" . $search_text . "%'";
				} else {
					if ($search_by == 'name')
						$where = $request_search_by ." IN (" . $where_id_numbers . ")";
					else
						$where = $search_by ." LIKE '%" . $search_text . "%'";
				} 	
			}	

			$where .= " AND insert_timestamp BETWEEN '{$start_date}' AND '{$end_date}'";

			$warehouse_request_count = $this->spare_parts_model->get_warehouse_request_count($where);

			$filename = "warehouse_requests_" . str_replace("-", "", $start_date) . "-" . str_replace("-", "", $end_date) . ".xls";

			//set width of first column
			$worksheet->getColumnDimension('A')->setWidth(12.00);

			// set column header to bold
			$worksheet->getStyle('A1')->getFont()->setBold(true);
			$worksheet->getStyle('A' . $start_column_num)->getFont()->setBold(true);
			$worksheet->getStyle('B' . $start_column_num)->getFont()->setBold(true);
			$worksheet->getStyle('C' . $start_column_num)->getFont()->setBold(true);
			$worksheet->getStyle('D' . $start_column_num)->getFont()->setBold(true);
			$worksheet->getStyle('E' . $start_column_num)->getFont()->setBold(true);
			$worksheet->getStyle('F' . $start_column_num)->getFont()->setBold(true);
			$worksheet->getStyle('G' . $start_column_num)->getFont()->setBold(true);
			$worksheet->getStyle('H' . $start_column_num)->getFont()->setBold(true);

			//center column names
			$worksheet->getStyle('A' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$worksheet->getStyle('B' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$worksheet->getStyle('C' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$worksheet->getStyle('D' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$worksheet->getStyle('E' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$worksheet->getStyle('F' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$worksheet->getStyle('G' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$worksheet->getStyle('H' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			
			//set column names
			$worksheet->setCellValue('A1', "Dealer Requests from {$start_date} to {$end_date}");
			$worksheet->setCellValue('A' . $start_column_num, 'Request Code');
			$worksheet->setCellValue('B' . $start_column_num, 'Status');
			$worksheet->setCellValue('C' . $start_column_num, 'Requested By');
			$worksheet->setCellValue('D' . $start_column_num, 'Motor Brand/Model');
			$worksheet->setCellValue('E' . $start_column_num, 'Number of Items');
			$worksheet->setCellValue('F' . $start_column_num, 'Warehouse');
			$worksheet->setCellValue('G' . $start_column_num, 'Warehouse Approved By');
			$worksheet->setCellValue('H' . $start_column_num, 'Date Created');
			

			$row = 4;

			$allowed_rows = 5000;

			for($prow = 0;$prow < ceil($warehouse_request_count/$allowed_rows)+1; $prow++)
			{
				$warehouse_requests = $this->spare_parts_model->get_warehouse_request($where, array('rows' => $allowed_rows, 'offset' => $prow*$allowed_rows), 'insert_timestamp ASC');

				foreach ($warehouse_requests as $dr)
				{

					$worksheet->setCellValue('A'. $row, $dr->request_code);
					$worksheet->setCellValue('B'. $row, $dr->status);
					$worksheet->setCellValue('C'. $row, $dr->id_number);
					$worksheet->setCellValue('D'. $row, $dr->motorcycle_brand_model_id);
					$worksheet->setCellValue('E'. $row, $dr->mtr_number);
					$worksheet->setCellValue('F'. $row, $dr->warehouse_id);
					$worksheet->setCellValue('G'. $row, $dr->id_number);
					$worksheet->setCellValue('H'. $row, $dr->insert_timestamp);
					
					// auto resize columns
					$worksheet->getColumnDimension('A')->setAutoSize(false);
					$worksheet->getColumnDimension('B')->setAutoSize(true);
					$worksheet->getColumnDimension('C')->setAutoSize(true);
					$worksheet->getColumnDimension('D')->setAutoSize(true);
					$worksheet->getColumnDimension('E')->setAutoSize(true);
					$worksheet->getColumnDimension('F')->setAutoSize(true);
					$worksheet->getColumnDimension('G')->setAutoSize(true);
					$worksheet->getColumnDimension('H')->setAutoSize(true);
					$row++;
				}
			}

			header('Content-Type: application/vnd.ms-excel');
			header('Content-Disposition: attachment;filename='.$filename.'');
			header('Cache-Control: max-age=0');

			$objWriter = IOFactory::createWriter($objPHPExcel, 'Excel5');
			$objWriter->save('php://output');
			exit(0);
			
		} catch (Exception $e) {
			exit($e->getMessage());
		}
	}

	public function edit($warehouse_request_id = 0)
	{
		$this->add($warehouse_request_id);
	}

	public function add($warehouse_request_id = 0) 
	{

		$department_module_details = $this->spare_parts_model->get_department_module_by_segment_name($this->segment_name);
		
		$warehouse_request_details = $this->spare_parts_model->get_warehouse_request_by_id($warehouse_request_id);

		if (!empty($warehouse_request_details)) {
			$requester_details = $this->human_relations_model->get_employment_information_view_by_id($warehouse_request_details->id_number);
			
			$department_details = $this->human_relations_model->get_department_by_id($requester_details->department_id);
			$position_details = $this->human_relations_model->get_position_by_id($requester_details->position_id);

			$this->template->requester_details = $requester_details;
			$this->template->department_details = $department_details;
			$this->template->position_details = $position_details;

			$request_item_amount_total = get_items_total_amount($warehouse_request_details->request_code);		
			$this->template->request_item_amount_total = $request_item_amount_total;

			// get request items
			$where = "status NOT IN ('CANCELLED', 'DELETED') AND warehouse_request_id = " . $warehouse_request_id;
			$warehouse_request_detail_details = $this->spare_parts_model->get_warehouse_request_detail($where);

			$json_items = array();
			for($k = 0;$k<count($warehouse_request_detail_details);$k++)
			{
				$warehouse_request_detail_id = $warehouse_request_detail_details[$k]->warehouse_request_detail_id;
				
				//$total_amount = $total_amount + ($item_qty[$k]*$item_price[$k]);
				$po_items = array(
						'warehouse_request_detail_id' => $warehouse_request_detail_id,
						'item_id' => $warehouse_request_detail_details[$k]->item_id,
						'srp' => $warehouse_request_detail_details[$k]->srp,
						'discount' => $warehouse_request_detail_details[$k]->discount,
						'discount_amount' => $warehouse_request_detail_details[$k]->discount_amount,
						'good_quantity' => $warehouse_request_detail_details[$k]->good_quantity,
						'bad_quantity' => $warehouse_request_detail_details[$k]->bad_quantity,
						'total_amount' => $warehouse_request_detail_details[$k]->total_amount,
						'remarks' => $warehouse_request_detail_details[$k]->remarks,

				);
				//creates an array of the items that will be json encoded later
				array_push($json_items, $po_items);

			}

			$this->template->json_items = json_encode($json_items);

		}


		$items = $this->spare_parts_model->get_item(null,null,"sku ASC");
		$items_array = array();
		
		foreach($items as $i)
		{
			$items_array[$i->item_id] = $i;
		}

		$motorcycle_brandmodel_details = $this->warehouse_model->get_motorcycle_brand_model_class_view('','', 'brand_name', 'motorcycle_brand_model_id, brand_name, model_name');
		
		$warehouse_details = $this->warehouse_model->get_warehouse("is_active = 1", '', '', 'warehouse_id, warehouse_name, description, manager_id_number, encoder_id_number');	

		//$this->template->return_url = $return_url;
		$this->template->items = $items_array;
		$this->template->motorcycle_brandmodel_details = $motorcycle_brandmodel_details;
		$this->template->warehouse_details = $warehouse_details;
		$this->template->warehouse_request_details = $warehouse_request_details;
		$this->template->department_module_details = $department_module_details;		
		$this->template->view('warehouse_request/add');
	}

	/*public function get_requester()
	{
		$search_key = $this->input->get_post('search_key');
		$search_key = trim($search_key);

		if (empty($search_key)) 
		{
			$this->return_json("error","Search key is empty.");
			return;
		}
		
		$keys = explode(" ", $search_key);
		$escape_keys = array();
		for ($i = 0; $i < count($keys); $i++)
			array_push($escape_keys, $this->human_relations_model->escape("%".$keys[$i]."%") );
			
		$where_first_name = implode(' OR first_name LIKE ', $escape_keys);
		$where_last_name = implode(' OR last_name LIKE ', $escape_keys);
		
		// check if its a string name or part of a name
		$escaped_search_key1 = $this->human_relations_model->escape($search_key);
		$escaped_search_key2 = $this->human_relations_model->escape('%'.$search_key.'%');
		$where = "is_employed = 1 AND ((complete_name like {$where_first_name}) ".(count($keys) > 1 ? "AND" : "OR")." (complete_name like {$where_last_name})) OR id_number like {$escaped_search_key2}";
		$tmp_employees = $this->human_relations_model->get_employment_information_view($where, array('offset' => 0, 'rows' => 50), "id_number ASC, complete_name ASC");
		
		// 20150723 TODO!!!
		// ================
		var_dump($where);
		return;
		// ================

		$employees = array();
		if (count($tmp_employees) == 0)
		{
			// if these is reached then nothing are found.
			$this->return_json("error","Not found.", array('employees' => $employees, 'keys' => $keys));
			return;
		}

		$tmp_position = $this->human_relations_model->get_position();
		$positions = array();
		foreach ($tmp_position as $item)
			$positions[$item->position_id] = $item;

		foreach ($tmp_employees as $mem)
		{
			
			$department_name = "N/A";	
			$position_name = "N/A";
			// get company and department
			$department_details = $this->human_relations_model->get_department_by_id($mem->department_id);
			if (!empty($department_details)) {
				$department_name = $department_details->department_name;
			}

			// get position
			$position_details = $this->human_relations_model->get_position_by_id($mem->position_id);
			if (!empty($position_details)) {
				$position_name = $position_details->position_name;
			}

			// is_employed
			if ($mem->is_employed == 1)
				$is_employed = "YES";
			else
				$is_employed = "NO";

			$employees[$mem->employment_information_id] = array(
				"employment_information_id" => $mem->employment_information_id,
				"id_number" => $mem->id_number,
				"complete_name" => strtoupper($mem->complete_name),
				"company_email_address" => $mem->company_email_address,
				"department_name" => $department_name,
				"position" => $position_name,
				"is_employed" => $is_employed,
			);
		}
			
		
		$this->return_json("ok","Ok.", array('employees' => $employees, 'keys' => $keys));
		return;
		
	}

	public function search_item()
	{
		$search_key = $this->input->get_post('search_key');
		$search_key = trim($search_key);
	
		if (empty($search_key)) 
		{
			$this->return_json("error","Item Name is empty.");
			return;
		}

		$keys = explode(" ", $search_key);
		for ($i = 0; $i < count($keys); $i++)
		{
			$escaped_keys[] = mysql_real_escape_string($keys[$i]);
		}

		$key_count = count($escaped_keys);  

		// get possible combinations
		$combinations = array();

		$this->load->library('Math_Combinatorics');
		$combinatorics = new Math_Combinatorics;
		foreach( range(1, count($escaped_keys)) as $subset_size ) {
    		foreach($combinatorics->permutations($escaped_keys, $subset_size) as $p) {
	  			$combinations[sizeof($p)-1][] = $p;
    		}
		}

		$combinations = array_reverse($combinations);

		// exact match search
		$has_exact = false;
		$tmp_items = array();

		foreach($combinations as $comb_group)
		{
			foreach($comb_group as $comb)
			{
				$name = strtoupper(join('', $comb));
				$sql = "
					SELECT * FROM `is_item_view` WHERE
					(REPLACE(`sku`,' ','') LIKE '%{$name}%') OR (REPLACE(`model_name`,' ','') LIKE '%{$name}%') OR (REPLACE(`description`,' ','') LIKE '%{$name}%') OR (REPLACE(`sku`,' ','') LIKE '%{$name}%') ORDER BY sku, description LIMIT 50;
				";
				$query = $this->db_spare_parts->query($sql);
				if(count($query->result_array()) > 0)
				{
					$tmp_items = $query->result_object();
					$has_exact = true;
					break;
				}
			}
			if($has_exact)
			{
				break;
			}
		}
		
		$return_items = array();

		if (count($tmp_items) == 0)
		{
			// if these is reached then nothing are found.
			$this->return_json("error","Not found.", array('items' => $return_items, 'keys' => $keys));
			return;
		}
		
		foreach ($tmp_items as $itm)
		{
			$return_items[$itm->item_id] = array(
				"item_id" => $itm->item_id,
				"sku" => $itm->sku,
				"brand_model" => $itm->brand_name . ' / ' . $itm->model_name,
				"description" => strtoupper($itm->description),
				"srp" => strtoupper($itm->srp),
				"warehouse_name" => strtoupper($itm->warehouse_name),
				"rack_location" => strtoupper($itm->rack_location),
				"bad_quantity" => $itm->bad_quantity,
				"good_quantity" => $itm->good_quantity,

			);
		}
		
		$this->return_json("ok","Ok.", array('items' => $return_items, 'keys' => $keys));
		return;

	}*/

	public function create_request()
	{
		$request_code = trim($this->input->post("request_code"));
		$item_id = abs($this->input->post("item_id"));
		$srp = abs($this->input->post("srp"));
		$discount = abs($this->input->post("discount"));
		$discount_amount = abs($this->input->post("discount_amount"));
		$good_quantity = abs($this->input->post("good_quantity"));
		$bad_quantity = abs($this->input->post("bad_quantity"));
		$remarks = trim($this->input->post("remarks"));
		$requester_remarks = trim($this->input->post("requester_remarks"));
		$engine = trim($this->input->post("engine"));
		$chassis = trim($this->input->post("chassis"));
		$warehouse_id = abs($this->input->post("warehouse_id"));
		$brandmodel_id = trim($this->input->post("brandmodel"));
		$requester_id = trim($this->input->post("requester_id"));

		$has_error = 0;
		$good_error_message = "";
		$bad_error_message = "";

		if ($good_quantity == '')
			$good_quantity = 0;

		if ($bad_quantity == '')
			$bad_quantity = 0;

		if ($discount_amount == '')
			$discount_amount = 0.00;

		// check first if inventory is enough for the set quantities
		$item_details = $this->spare_parts_model->get_item_by_id($item_id);

		if (count($item_details) == 0) {
			$html = "<p>Error  Item not found in database.</p>";
			$title = "Error :: Item Not Found";

			$this->return_json("0","Item not found in DB", array("html" => $html, "title" => $title));	
			return;
		}

		if ($item_details->good_quantity < $good_quantity) {
			$has_error = 1;
			$good_error_message = "<p>Good Quantity is not enough. There are <strong>" . $item_details->good_quantity . "</strong> good quantities available.</p><br/>";
		}

		if ($item_details->bad_quantity < $bad_quantity) {
			$has_error = 1;
			$bad_error_message = "<p>Bad Quantity is not enough. There are <strong>" . $item_details->bad_quantity . "</strong> bad quantities available.</p><br/>";
		}

		if ($has_error == 1) {
			$html = $good_error_message . $bad_error_message;
			$title = "Error :: Not Enough Inventory";

			$this->return_json("0","Not Enough Inventory", array("html" => $html, "title" => $title));
			return;
		}

		// get year
		$dateyear = date("Y");
		$request_series = substr($dateyear, 2, 2);

		$module_code = $request_code;

		if (strlen($request_code) < 10)
		{

			$manager_id_number = 0;
			// get warehouse info from warehouse db
			$warehouse_details = $this->warehouse_model->get_warehouse_by_id($warehouse_id);
			if (count($warehouse_details)  > 0) {
				$manager_id_number = $warehouse_details->manager_id_number;
			}

			$current_datetime = date('Y-m-d H:i:s');						

			$sql = "INSERT INTO 
						is_warehouse_request 
						(
							`request_series`, 
							`request_number`, 
							`id_number`, 
							`warehouse_approved_by`, 
							`warehouse_id`, 
							`motorcycle_brand_model_id`, 
							`engine`, 
							`chassis`
						)
                    	(
                    	SELECT 
                    		'{$request_series}', 
                    		IFNULL(MAX(request_number) + 1, 1) AS request_number, 
                    		'{$requester_id}', 
                    		'{$manager_id_number}',
                            '{$warehouse_id}', 
                            '{$brandmodel_id}', 
                            '{$engine}', 
                            '{$chassis}'                            
                    	FROM 
                    		is_warehouse_request
                    	WHERE 
                    		request_series = '{$request_series}' 
	                    ORDER BY 
	                    	request_number DESC
                    	)";

			$this->db_spare_parts->query($sql);	

			// get last insert id
			$sql = "SELECT LAST_INSERT_ID() AS last_id FROM is_warehouse_request";
			$query = $this->db_spare_parts->query($sql);
			$warehouse_request_id = $query->first_row();

			$active_warehouse_request_id = $warehouse_request_id->last_id;

			// generate request code
			$sql = "SELECT 
						CONCAT('{$module_code}', '{$request_series}', '-', LPAD(request_number, 5, 0)) AS gen_code
					FROM
						is_warehouse_request		
                    WHERE 
                    	warehouse_request_id = " . $active_warehouse_request_id;

            $query = $this->db_spare_parts->query($sql);
			$request_code_details = $query->first_row();  

			$request_code = $request_code_details->gen_code; 		

			// update request_code
			$data_update = array(
					'request_code' => $request_code
				);

			if (strlen(trim($requester_remarks)) > 0) {
				$data[] =array(
						'datetime' => $current_datetime,
						'message' => $requester_remarks
					);

				$requester_remarks_encoded = json_encode($data);
				$data_update['remarks'] = $requester_remarks_encoded;
			}

			$where_update = "warehouse_request_id = " . $active_warehouse_request_id;
			$this->spare_parts_model->update_warehouse_request($data_update, $where_update);

            //get department module id
            $department_module_details = $this->spare_parts_model->get_department_module_by_code($module_code);        
            
            $data_insert = array (
        		'branch_id' => 1,
        		'department_id' => $department_module_details->department_id,
        		'department_module_id' => $department_module_details->department_module_id,
        		'transaction_number' => $request_code,
        		'status' => 'PREPARING',
            );

         	$this->spare_parts_model->insert_warehouse_reservation($data_insert);

		} else {
			
			$active_warehouse_request_details = $this->spare_parts_model->get_warehouse_request_by_code($request_code);
			$active_warehouse_request_id = $active_warehouse_request_details->warehouse_request_id;
		}	

		// total amount
		if ($discount_amount == 0) {
			$total_amount = $good_quantity * ($srp - ($srp * ($discount / 100)));
			$total_amount = $total_amount + ($bad_quantity  * ($srp - ($srp * ($discount / 100))));
		} else {
			$total_amount = $good_quantity * $discount_amount;
			$total_amount = $total_amount + ($bad_quantity * $discount_amount);
		}

		$formatted_total_amount = number_format($total_amount, 2);

		// add item to details table
		$data_insert = array(
				'warehouse_request_id' => $active_warehouse_request_id,
				'item_id' => $item_id,
				'srp' => $srp,
				'discount' => $discount,
				'discount_amount' => $discount_amount,
				'good_quantity' => $good_quantity,
				'bad_quantity' => $bad_quantity,
				'total_amount' => $total_amount
			);

		if (strlen(trim($remarks)) > 0) {
			$current_datetime = date('Y-m-d H:i:s');
			$data[] =array(
					'datetime' => $current_datetime,
					'message' => $remarks
				);

			$item_remarks_encoded = json_encode($data);
			$data_insert['remarks'] = $item_remarks_encoded;
		}

		$this->spare_parts_model->insert_warehouse_request_detail($data_insert);

		$active_warehouse_request_detail_id = $this->spare_parts_model->insert_id();

		// deduct to warehouse
		$sql = "UPDATE 
					is_item 
				SET 
					good_quantity = good_quantity - {$good_quantity}, 
					bad_quantity = bad_quantity - {$bad_quantity} 
				WHERE 
					item_id = " . $item_id;

		$this->db_spare_parts->query($sql);

		$request_item_amount_total = get_items_total_amount($request_code);

		$html = "<p>Item with SKU <strong>" . $item_details->sku . "</strong> has been added successfully!</p>";
		$title = "Add Item :: Item Request";

		$this->return_json("1","Item Successfully Added", array("html" => $html, "title" => $title, "request_code" => $request_code, "overall_total_amount" => $request_item_amount_total->total_amount, 'active_warehouse_request_detail_id' => $active_warehouse_request_detail_id, 'item_total_amount' => $formatted_total_amount));
		return;
	}	


	public function proceed_reprocess_item()
	{
		$request_code = trim($this->input->post("request_code"));
		$request_detail_id = abs($this->input->post("request_detail_id"));
		$srp = abs($this->input->post("srp"));
		$charge_discount = abs($this->input->post("charge_discount"));
		$charge_discount_amount = abs($this->input->post("charge_discount_amount"));
		$good_quantity = abs($this->input->post("good_quantity"));
		$bad_quantity = abs($this->input->post("bad_quantity"));
		$remarks = trim($this->input->post("remarks"));		
		$action_option = trim($this->input->post("action_option"));
		$id_number = $this->input->post("id_number");

		$has_error = 0;
		$good_error_message = "";
		$bad_error_message = "";

		if ($good_quantity == '')
			$good_quantity = 0;

		if ($bad_quantity == '')
			$bad_quantity = 0;

		if ($charge_discount_amount == '')
			$charge_discount_amount = 0.00;

		$recipient_name = "N/A";
		if ($action_option == "return") {
			$id_number = NULL;
		} else {
			$recipient_details = $this->human_relations_model->get_employment_information_view_by_id($id_number);
			$recipient_name = $recipient_details->complete_name;
		}	

		$action = strtoupper($action_option);

		$department_module_details = $this->spare_parts_model->get_department_module_by_segment_name($this->segment_name);

		$warehouse_request_detail_details = $this->spare_parts_model->get_warehouse_request_detail_by_id($request_detail_id);

		// get remaining number of items available
		$where = "request_detail_id = {$request_detail_id} AND department_module_id = {$department_module_details->department_module_id}
		AND status NOT IN ('CANCELLED', 'DELETED')";
		$reprocessed_item_details = $this->spare_parts_model->get_reprocessed_item($where, NULL, NULL, "SUM(good_quantity) AS good_quantity, SUM(bad_quantity) AS bad_quantity");
		$reprocessed_item_details = $reprocessed_item_details[0];

		$available_good_quantity = $warehouse_request_detail_details->good_quantity - $reprocessed_item_details->good_quantity;
		$available_bad_quantity = $warehouse_request_detail_details->bad_quantity - $reprocessed_item_details->bad_quantity;

		//var_dump($available_good_quantity . '|' .$available_bad_quantity);

		if ($available_good_quantity < $good_quantity) {
			$has_error = 1;
			$good_error_message = "<p>The Good Quantity is greater than the actual request Good Items count. There are <strong>" . $available_good_quantity . "</strong> good quantities available.</p><br/>";
		}

		if ($available_bad_quantity < $bad_quantity) {
			$has_error = 1;
			$bad_error_message = "<p>The Bad Quantity is greater than the actual request Bad Items count. There are <strong>" . $available_bad_quantity . "</strong> bad quantities available.</p><br/>";
		}

		if ($has_error == 1) {
			$html = $good_error_message . $bad_error_message;
			$title = "Error :: Not Enough Inventory";

			$this->return_json("0","Not Enough Inventory", array("html" => $html, "title" => $title));
			return;
		}
	
		// compute the total charge amount		
		if ($charge_discount_amount == 0) {
			$total_amount = $good_quantity * ($srp - ($srp * ($charge_discount / 100)));
			$total_amount = $total_amount + ($bad_quantity  * ($srp - ($srp * ($charge_discount / 100))));
		} else {
			$total_amount = $good_quantity * $charge_discount_amount;
			$total_amount = $total_amount + ($bad_quantity * $charge_discount_amount);
		}

		$formatted_total_amount = number_format($total_amount, 2);

		$item_remarks_encoded  = "";
		if (strlen(trim($remarks)) > 0) {
			$current_datetime = date('Y-m-d H:i:s');
			$data[] =array(
					'datetime' => $current_datetime,
					'message' => $remarks
				);

			$item_remarks_encoded = json_encode($data);
			$data_insert['remarks'] = $item_remarks_encoded;
		}

		// insert to is_reprocessed_item table
		$data_insert = array(
				"department_module_id" => $department_module_details->department_module_id,
				"request_id" => $warehouse_request_detail_details->warehouse_request_id,
				"request_detail_id" => $request_detail_id,
				"id_number" => $id_number,
				"charge_discount" => $charge_discount,
				"charge_discount_amount" => $charge_discount_amount,
				"total_amount" => $total_amount,
				"good_quantity" => $good_quantity,
				"bad_quantity" => $bad_quantity,
				"action" => $action,
				"remarks" => $item_remarks_encoded,				
			);
		
		$this->spare_parts_model->insert_reprocessed_item($data_insert);

		$reprocessed_item_id = $this->spare_parts_model->insert_id();

		$status_sql = "SELECT DISTINCT(action) 
						FROM 
							is_reprocessed_item 
						WHERE 
							department_module_id = {$department_module_details->department_module_id} 
						AND 
							request_id = {$warehouse_request_detail_details->warehouse_request_id} 
						AND 
							status NOT IN ('CANCELLED', 'DELETED') 
						ORDER BY 
							action DESC";

		$tmp_status = array();
		$has_charge = 0;
		$has_return = 0;
		
		$query = $this->db_spare_parts->query($status_sql);
		if(count($query->result_array()) > 0) {
			$tmp_status = $query->result_object();			
		}					

		if (count($tmp_status) > 0) {
			foreach ($tmp_status as $ts) {
				//$new_status = $new_status . substr($ts->action, 0, 1);
				if ($ts->action == 'RETURN')
					$has_return = 1;
				if ($ts->action == 'CHARGE')
					$has_charge = 1; 
			}
		}
		
		if (($has_return == 0) && ($has_charge == 0)) {
			$new_status = "COMPLETED";
		} else if (($has_return == 0) && ($has_charge == 1)) {
			$new_status = "COMPLETED-C";
		} else if (($has_return == 1) && ($has_charge == 0)) {	
			$new_status = "COMPLETED-R";
		} else {
			$new_status = "COMPLETED-RC";
		}	

		$current_datetime = date('Y-m-d H:i:s');		
		$data_update  = array(
				"update_timestamp" => $current_datetime,
				"status" => $new_status,
			);

		$this->spare_parts_model->update_warehouse_request($data_update, "warehouse_request_id = " . $warehouse_request_detail_details->warehouse_request_id);

		// get item details 
		$item_details = $this->spare_parts_model->get_item_view_by_id($warehouse_request_detail_details->item_id);

		$html = "<p>Item with SKU <strong>" . $item_details->sku . "</strong> has been reprocessed successfully!</p>";
		$title = $action . " Item :: Item Request";

		//$this->return_json("1","Item Successfully Reprocessed", array("html" => $html, "title" => $title, "request_code" => $request_code, "overall_total_amount" => $request_item_amount_total->total_amount, 'active_warehouse_request_detail_id' => $active_warehouse_request_detail_id);
		$this->return_json("1","Item Successfully Reprocessed", array("html" => $html, "title" => $title, "item_details" => $item_details, "recipient_name" => $recipient_name, 'item_total_amount' => $formatted_total_amount, 'active_reprocessed_item_id' => $reprocessed_item_id, 'active_warehouse_request_detail_id' => $request_detail_id));		
			
		return;
	}	


	public function confirm_remove_item() {
		$request_code = $this->input->post("request_code");		
		$warehouse_request_detail_id = $this->input->post("warehouse_request_detail_id");

		// get warehouse_request_id
		$warehouse_request_details = $this->spare_parts_model->get_warehouse_request_by_code($request_code);

		$warehouse_request_detail_info = $this->spare_parts_model->get_warehouse_request_detail_by_id($warehouse_request_detail_id);

		$item_view_details = $this->spare_parts_model->get_item_view_by_id($warehouse_request_detail_info->item_id);
		
		$title = "Remove Item :: [SKU] " . $item_view_details->sku;
		$html = "<p>You are about to remove an item from Request Code: <strong>" . $request_code . "</strong>. <br/>
					<label><strong>Model:</strong></label>&nbsp;&nbsp;" . $item_view_details->model_name . "
					<label><strong>Brand:</strong></label>&nbsp;&nbsp;" . $item_view_details->brand_name . "
					<label><strong>Description:</strong></label>&nbsp;&nbsp;" . $item_view_details->description . "
					<div id='reasonremarks-container'>
						<span><strong>Reason/Remarks:</strong></span></br>
						<input id='txt-remarks' style='width:400px;' maxlength='320' placeholder='Put remarks here...' /><br/>
						<span id='error-reasonremarks' style='color:red;display:none'>Remarks/Reason is required.</span>
					</div>	
					<br/>
					Do you want to continue?</p>";

		$this->return_json("1","Confirm Remove Item", array("html" => $html, "title" => $title, 'warehouse_request_id' => $warehouse_request_details->warehouse_request_id));
		return;
	}

	public function proceed_remove_item() {
		$warehouse_request_id = $this->input->post("warehouse_request_id");
		$is_reprocess_item = $this->input->post("is_reprocess_item");
		$warehouse_request_detail_id = $this->input->post("warehouse_request_detail_id");
		$remarks = $this->input->post("remarks");		

		//$where = "warehouse_request_id = '{$warehouse_request_id}' AND item_id = '{$item_id}'";
		//$warehouse_request_detail = $this->spare_parts_model->get_warehouse_request_detail($where);

		$warehouse_request_details = $this->spare_parts_model->get_warehouse_request_by_id($warehouse_request_id);

		$where = "warehouse_request_detail_id = " . $warehouse_request_detail_id;		
		$warehouse_request_detail_info = $this->spare_parts_model->get_warehouse_request_detail_by_id($warehouse_request_detail_id);

		$current_datetime = date('Y-m-d H:i:s');		

		// TODO json_encode remarks
		if ($is_reprocess_item == 0) {			
			$complete_remarks = $warehouse_request_detail_info->remarks . "[" . $current_datetime . "] " . $remarks . "\n";
		} else {
			$complete_remarks = "[" . $current_datetime . "] " . $remarks . "\n";
		}	
		
		if ($is_reprocess_item == 0) {			
			
			// from spare_parts_helper			
			$return_html = return_reserved_items($warehouse_request_details->request_code, 'DELETED', $remarks, $warehouse_request_detail_id);			

		} else {

			// update status to DELETED
			$data = array(
				'status' => 'DELETED',
				'remarks' => $complete_remarks,
				'update_timestamp' => $current_datetime
			);
			
			$request_item_id = $this->input->post("request_item_id");
		
			$where = "reprocessed_item_id = " . $request_item_id;
			$this->spare_parts_model->update_reprocessed_item($data, $where);
		}

		// get department_module datails
		$department_module_details = $this->spare_parts_model->get_department_module_by_segment_name($this->segment_name);

		if ($is_reprocess_item == 1) {
			// check status of warehouse request
			$status_sql = "SELECT DISTINCT(action) 
							FROM 
								is_reprocessed_item 
							WHERE 
								department_module_id = {$department_module_details->department_module_id} 
							AND 
								request_id = {$warehouse_request_id} 
							AND 
								status NOT IN ('CANCELLED', 'DELETED') 
							ORDER BY 
								action DESC";

			$tmp_status = array();
			$has_charge = 0;
			$has_return = 0;
			
			$query = $this->db_spare_parts->query($status_sql);
			if(count($query->result_array()) > 0) {
				$tmp_status = $query->result_object();			
			}					

			if (count($tmp_status) > 0) {
				foreach ($tmp_status as $ts) {					
					if ($ts->action == 'RETURN')
						$has_return = 1;
					if ($ts->action == 'CHARGE')
						$has_charge = 1; 
				}
			}
			
			if (($has_return == 0) && ($has_charge == 0)) {
				$new_status = "COMPLETED";
			} else if (($has_return == 0) && ($has_charge == 1)) {
				$new_status = "COMPLETED-C";
			} else if (($has_return == 1) && ($has_charge == 0)) {	
				$new_status = "COMPLETED-R";
			} else {
				$new_status = "COMPLETED-RC";
			}	

			$current_datetime = date('Y-m-d H:i:s');		
			$data_update  = array(
					"update_timestamp" => $current_datetime,
					"status" => $new_status,
				);

			$this->spare_parts_model->update_warehouse_request($data_update, "warehouse_request_id = " . $warehouse_request_id);
		}	

		$html = "Item is now successfully removed from request.";
		$title = "Delete An Item :: Item Request";

		$this->return_json("1","Item Successfully Tagged as DELETED", array("html" => $html, "title" => $title));
		return;

	}

	
	public function reprocess_items($warehouse_request_id = 0)
	{


		$department_module_details = $this->spare_parts_model->get_department_module_by_segment_name($this->segment_name);
		
		$warehouse_request_details = $this->spare_parts_model->get_warehouse_request_by_id($warehouse_request_id);

		if (!empty($warehouse_request_details)) {
			$requester_details = $this->human_relations_model->get_employment_information_view_by_id($warehouse_request_details->id_number);
			
			$department_details = $this->human_relations_model->get_department_by_id($requester_details->department_id);
			$position_details = $this->human_relations_model->get_position_by_id($requester_details->position_id);

			$this->template->requester_details = $requester_details;
			$this->template->department_details = $department_details;
			$this->template->position_details = $position_details;

			$request_item_amount_total = get_items_total_amount($warehouse_request_details->request_code);		
			$this->template->request_item_amount_total = $request_item_amount_total;

			// get request items
			$where = "status NOT IN ('CANCELLED', 'DELETED') AND warehouse_request_id = " . $warehouse_request_id;
			$warehouse_request_detail_details = $this->spare_parts_model->get_warehouse_request_detail($where);

			$json_items = array();
			for($k = 0;$k<count($warehouse_request_detail_details);$k++)
			{
				$warehouse_request_detail_id = $warehouse_request_detail_details[$k]->warehouse_request_detail_id;
				
				//$total_amount = $total_amount + ($item_qty[$k]*$item_price[$k]);
				$po_items = array(
						'warehouse_request_detail_id' => $warehouse_request_detail_id,
						'item_id' => $warehouse_request_detail_details[$k]->item_id,
						'srp' => $warehouse_request_detail_details[$k]->srp,
						'discount' => $warehouse_request_detail_details[$k]->discount,
						'discount_amount' => $warehouse_request_detail_details[$k]->discount_amount,
						'good_quantity' => $warehouse_request_detail_details[$k]->good_quantity,
						'bad_quantity' => $warehouse_request_detail_details[$k]->bad_quantity,
						'total_amount' => $warehouse_request_detail_details[$k]->total_amount,
						'remarks' => $warehouse_request_detail_details[$k]->remarks,

				);
				//creates an array of the items that will be json encoded later
				array_push($json_items, $po_items);

			}

			$this->template->json_items = json_encode($json_items);

			$json_reprocessed_items = array();			
			$where = "action IN ('RETURN', 'CHARGE') AND status NOT IN ('CANCELLED', 'DELETED') AND department_module_id = " . $department_module_details->department_module_id . " AND request_id = " . $warehouse_request_id;
			$reprocessed_item_details = $this->spare_parts_model->get_reprocessed_item($where);			

			for($l = 0;$l<count($reprocessed_item_details);$l++)
			{				

				$warehouse_request_detail_details = $this->spare_parts_model->get_warehouse_request_detail_by_id($reprocessed_item_details[$l]->request_detail_id);

				$ri_items = array(
						'request_item_id' => $reprocessed_item_details[$l]->reprocessed_item_id,
						'warehouse_request_detail_id' => $reprocessed_item_details[$l]->request_detail_id,
						'item_id' => $warehouse_request_detail_details->item_id,
						'srp' => $warehouse_request_detail_details->srp,
						'id_number' => $reprocessed_item_details[$l]->id_number,
						'discount' => $reprocessed_item_details[$l]->charge_discount,
						'discount_amount' => $reprocessed_item_details[$l]->charge_discount_amount,
						'good_quantity' => $reprocessed_item_details[$l]->good_quantity,
						'bad_quantity' => $reprocessed_item_details[$l]->bad_quantity,
						'total_amount' => $reprocessed_item_details[$l]->total_amount,
						'status' => $reprocessed_item_details[$l]->status,
						'action' => $reprocessed_item_details[$l]->action,
						'remarks' => $reprocessed_item_details[$l]->remarks,
				);

				//creates an array of the items that will be json encoded later
				array_push($json_reprocessed_items, $ri_items);

			}
			
			$this->template->json_reprocessed_items = json_encode($json_reprocessed_items);

		}

		$where = "status IN ('FORWARDED') AND department_module_id = " . $department_module_details->department_module_id . " AND request_id = " . $warehouse_request_id;
		$forwarded_to_wh = $this->spare_parts_model->get_reprocessed_item($where);			

		$is_forwarded = 0;
		if (count($forwarded_to_wh) > 0) {
			$is_forwarded = 1;
		}


		$items = $this->spare_parts_model->get_item(null,null,"sku ASC");
		$items_array = array();
		
		foreach($items as $i)
		{
			$items_array[$i->item_id] = $i;
		}

		$motorcycle_brandmodel_details = $this->warehouse_model->get_motorcycle_brand_model_class_view('','', 'brand_name', 'motorcycle_brand_model_id, brand_name, model_name');
		
		$warehouse_details = $this->warehouse_model->get_warehouse("is_active = 1", '', '', 'warehouse_id, warehouse_name, description, manager_id_number, encoder_id_number');	

		//$this->template->return_url = $return_url;
		$this->template->items = $items_array;
		$this->template->is_forwarded = $is_forwarded;
		$this->template->motorcycle_brandmodel_details = $motorcycle_brandmodel_details;
		$this->template->warehouse_details = $warehouse_details;
		$this->template->warehouse_request_details = $warehouse_request_details;
		$this->template->department_module_details = $department_module_details;
		$this->template->segment_name = $this->segment_name;
		$this->template->view("warehouse_request/reprocess_items");
	}

	public function reports()
	{
		$this->generate_report();
	}

	public function generate_report()
	{
		$from_date = trim($this->input->get_post('from_date'));
	    $to_date = trim($this->input->get_post('to_date'));
		$filter = trim($this->input->get_post('filter'));
		
	    $export = $this->input->get_post('export');
	    $where = "";
		$where_conjuction = "";
	    if (empty($from_date)) $from_date = date('Y-m-d');
	    if (empty($to_date)) $to_date = date('Y-m-d');
    
	    //date set
	    $from_dt = $from_date;
	    $to_dt = $to_date;
	    $from_single = "";
	    $from_t = strtotime($from_date);
	    $to_t = strtotime($to_date);
	    if ($from_t !== false) $from_dt = date('Y-m-d', $from_t); 
	    if ($to_t !== false) $to_dt = date('Y-m-d', $to_t); 
       
	    if ($from_t !== false && $to_t !== false)
	      $where .= "(DATE(insert_timestamp) BETWEEN '{$from_dt}' AND '{$to_dt}') ";
	    else if ($from_t !== false && $to_t === false)
	      $where .= "insert_timestamp >= '{$from_dt}'";
	    else if ($from_t === false && $to_t !== false)
	      $where .= "insert_timestamp <= '{$to_dt}'";
	
	    $data = new ArrayClass(array(
	      'from_date' => $from_date,
	      'to_date' => $to_date,
	      'where' => $where,
	    ));
    
	    // check if to export
	    if ($export == 'excel')
	    {
	      $this->_export($data);
	    }
	    else
	    {
			$display_data = "";
			if($filter == 'yes')
	      		$display_data = $this->report_view($data);
    
			$this->template->search_url = strlen($_SERVER['QUERY_STRING']) > 0 ? '?'.$_SERVER['QUERY_STRING'] : '';
	      	$this->template->from_date = $from_date;
	      	$this->template->to_date = $to_date;
	      	$this->template->display_data = $display_data;
	      	$this->template->view('warehouse_request/reports');
	    }
	}

	public function report_view($data)
	  {
	    $from_date = slugify($data->from_date);
	    $to_date = slugify($data->to_date);
	    $where = $data->where;
	    $current_date = date('Y-m-d');
    
		$total_records = $this->spare_parts_model->get_warehouse_request_count($where);

		$config = array(
			'pagination_url' => '/spare_parts/warehouse_request/generate_report',
			'total_items' => $total_records,
			'per_page' => 10,
			'uri_segment' => 4,
		);
		$this->pager->set_config($config);
		$warehouse_request_details = $this->spare_parts_model->get_warehouse_request($where, array('rows' => $this->pager->per_page, 'offset' => $this->pager->offset), "insert_timestamp DESC");

		$html = "<table class='table table-bordered table-condensed'>
			<thead>
				<th>Request Code</th>
				<th>ID Number</th>
				<th>Motocycle Brand Model ID</th>
				<th>Engine</th>
				<th>Chassis</th>
				<th>Status</th>
				<th>Warehouse ID</th>
				<th>MTR Number</th>
				<th>Remarks</th>
				<th>Date Created</th>
			</thead>
			<tbody>";
		
		if(empty($warehouse_request_details))
		{
			$html .= "<tr><td colspan='7' style='text-align:center'>No records found.</td></tr>";
		}
		foreach($warehouse_request_details as $wrd)
		{
			
			$html .= "<tr>
				<td>{$wrd->request_code}</td>
				<td>{$wrd->id_number}</td>
				<td>{$wrd->motorcycle_brand_model_id}</td>
				<td>{$wrd->engine}</td>
				<td>{$wrd->chassis}</td>
				<td>{$wrd->status}</td>
				<td>{$wrd->warehouse_id}</td>
				<td>{$wrd->mtr_number}</td>
				<td>{$wrd->remarks}</td>	
				<td>{$wrd->insert_timestamp}</td>
			</tr>";
		}

	    $html .= "</tbody></table>";
	    $html .= $this->pager->create_links(strlen($_SERVER['QUERY_STRING']) > 0 ? '?'.$_SERVER['QUERY_STRING'] : '');
        
	    return $html; 
	  }

	  public function _export($data)
	  {
		$from_date = slugify($data->from_date);
	    $to_date = slugify($data->to_date);
	    $where = $data->where;
	    $current_date = date('Y-m-d');
    
	    $current_year = date('Y');
	    $current_month = date('m');
	    $current_day = date('d');
	    $date = $current_month . '-' . $current_day . '-' . $current_year;
	    
	    $this->load->library('PHPExcel');
	    $this->load->library('PHPExcel/IOFactory');
	    $objPHPExcel = new PHPExcel();

	    $warehouse_request_details = $this->spare_parts_model->get_warehouse_request($where, null, "insert_timestamp DESC");

	    if (!empty($warehouse_request_details))
	    {
	      	$objPHPExcel->getProperties()->setTitle("export")->setDescription("none");
	      	$start_column_num = 4;
      	
	      	$objPHPExcel->setActiveSheetIndex(0);
	      	$objPHPExcel->getActiveSheet()->setTitle("Warehouse Request List");
      
	      	// auto resize columns
	      	$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
	      	$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
	      	$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
	      	$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
	      	$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
      
	      	// set column header to bold    
	      	$objPHPExcel->getActiveSheet()->getStyle('A' . $start_column_num . ':J' . $start_column_num)->getFont()->setBold(true);
        
	      	//center column names
	      	$objPHPExcel->getActiveSheet()->getStyle('A' . $start_column_num . ':J' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
      
	      	$header = "SPARE PARTS SYSTEM";
			$header2 = "Warehouse Request List";
			$header3 = "From " . $from_date . " to " . $to_date;
			$print_date = date('M d, Y H:i:s');
			$print_date_header = " (Printed On: {$print_date})";
			$header3 .= $print_date_header;
	      	$objPHPExcel->getActiveSheet()->getStyle('A' . 1)->getFont()->setBold(true);
	      	$objPHPExcel->getActiveSheet()->getStyle('A' . 1)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		    $objPHPExcel->getActiveSheet()->mergeCells('A1:J1');
		    $objPHPExcel->getActiveSheet()->setCellValue('A' . 1, $header);
			
			$objPHPExcel->getActiveSheet()->getStyle('A' . 2)->getFont()->setBold(true);
	      	$objPHPExcel->getActiveSheet()->getStyle('A' . 2)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		    $objPHPExcel->getActiveSheet()->mergeCells('A2:J2');
		    $objPHPExcel->getActiveSheet()->setCellValue('A' . 2, $header2);
		
			$objPHPExcel->getActiveSheet()->getStyle('A' . 3)->getFont()->setBold(true);
	      	$objPHPExcel->getActiveSheet()->getStyle('A' . 3)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		    $objPHPExcel->getActiveSheet()->mergeCells('A3:J3');
		    $objPHPExcel->getActiveSheet()->setCellValue('A' . 3, $header3);
        
	      	//set column names
	      	$objPHPExcel->getActiveSheet()->setCellValue('A' . $start_column_num, 'REQUEST CODE');
	      	$objPHPExcel->getActiveSheet()->setCellValue('B' . $start_column_num, 'REQUESTED BY');
	      	$objPHPExcel->getActiveSheet()->setCellValue('C' . $start_column_num, 'BRAND/MODEL');
			$objPHPExcel->getActiveSheet()->setCellValue('D' . $start_column_num, 'ENGINE');
			$objPHPExcel->getActiveSheet()->setCellValue('E' . $start_column_num, 'CHASSIS');
			$objPHPExcel->getActiveSheet()->setCellValue('F' . $start_column_num, 'STATUS');
			$objPHPExcel->getActiveSheet()->setCellValue('G' . $start_column_num, 'WAREHOUSE NAME');
			$objPHPExcel->getActiveSheet()->setCellValue('H' . $start_column_num, 'MTR NUMBER');
			$objPHPExcel->getActiveSheet()->setCellValue('I' . $start_column_num, 'REMARKS');
			$objPHPExcel->getActiveSheet()->setCellValue('J' . $start_column_num, 'DATE CREATED');
        	
	      	$objPHPExcel->getActiveSheet()->freezePane('A5');
      
	      	$row = $start_column_num + 1;
	      	$current_item_date = 0;
	      	foreach ($warehouse_request_details as $wrd)
	      	{
	        	$objPHPExcel->getActiveSheet()->getStyle('A' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	        	$objPHPExcel->getActiveSheet()->getStyle('B' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	        	$objPHPExcel->getActiveSheet()->getStyle('C' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	
				$objPHPExcel->getActiveSheet()->setCellValue('A' . $row, $wrd->request_code);
				$objPHPExcel->getActiveSheet()->setCellValue('B' . $row, $wrd->id_number);
				$objPHPExcel->getActiveSheet()->setCellValue('C' . $row, $wrd->motorcycle_brand_model_id);
				$objPHPExcel->getActiveSheet()->setCellValue('D' . $row, $wrd->engine);
				$objPHPExcel->getActiveSheet()->setCellValue('E' . $row, $wrd->chassis);
				$objPHPExcel->getActiveSheet()->setCellValue('F' . $row, $wrd->status);
				$objPHPExcel->getActiveSheet()->setCellValue('G' . $row, $wrd->warehouse_id);
				$objPHPExcel->getActiveSheet()->setCellValue('H' . $row, $wrd->mtr_number);
				$objPHPExcel->getActiveSheet()->setCellValue('I' . $row, $wrd->remarks);
				$objPHPExcel->getActiveSheet()->setCellValue('J' . $row, $wrd->insert_timestamp);
        
	        	$row++;
	      	}
	    }else
	    {
    
	    }
  		$objPHPExcel->setActiveSheetIndex(0);   
		if($from_date == $to_date)
			$filename_date = $from_date;
		else
	    	$filename_date = $from_date . '_to_' . $to_date;
    
	    header('Content-Type: application/vnd.ms-excel');
	    header('Content-Disposition: attachment;filename="warehouse_requests_'.$filename_date.'.xls"');
	    header('Cache-Control: max-age=0');   
	    $objWriter = IOFactory::createWriter($objPHPExcel, 'Excel5');
	    $objWriter->save('php://output');   
	}

}
