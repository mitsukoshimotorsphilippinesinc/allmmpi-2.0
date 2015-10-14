<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Payout_periods extends Systems_Controller {

	function __construct()
	{
		parent::__construct();

		$this->load->model('payment_model');
		$this->load->model('members_model');
		$this->load->model('tracking_model');
		$this->load->model('payout_model');
		// load pager library
		$this->load->library('pager');
	}

	private $_months = array(
			'0' => '-',
			'1' => 'January',
			'2' => 'February',
			'3' => 'March',
			'4' => 'April',
			'5' => 'May',
			'6' => 'June',
			'7' => 'July',
			'8' => 'August',
			'9' => 'September',
			'10' => 'October',
			'11' => 'November',
			'12' => 'December'
	);

	public function index()
	{
		$this->view();
	}

	public function view()
	{

		$search_by = trim($this->input->get("search_option"));
		$search_text = trim($this->input->get("search_string"));


		$search_url = "";
		$count_is = 0;
		$transfers = "";		
		$where = "";
		
		// initialize pagination class
		// set pagination data
		$config = array(
				'pagination_url' => "/admin/payout_periods/index/",
				'total_items' => $this->payout_model->get_payout_periods_count($where),
				'per_page' => 10,
				'uri_segment' => 4,
		);

		$this->pager->set_config($config);

		$transfers = $this->payout_model->get_payout_periods($where, array('rows' => $this->pager->per_page, 'offset' => $this->pager->offset), "start_date DESC, status");	
		
		// search vars
		$this->template->search_by = $search_by;
		$this->template->search_text = $search_text;
		$this->template->search_url = $search_url;
		$this->template->transfers = $transfers;
		
		$this->template->view('payout_periods/dashboard');
	}

	public function edit()
	{
		$payout_period_id = $this->input->post("payout_period_id");
		
		
		$payout_period = $this->payout_model->get_payout_period_by_id($payout_period_id);
	
		if (empty($payout_period)) {
		
			$title = "Edit Payout Period " . str_pad($payout_period_id, 6, "0", STR_PAD_LEFT) . " :: Error";
		
			$html = "<p>There is something wrong with this Payout Cut-off Schedule [Payout Period ID: {$payout_period_id}].</p>";
			$this->return_json("0","Empty Payout Period info",array("html" => $html, "title" => $title));
			
		} else {
		
		
			$data = array(
			"payout_period" => $payout_period
			);
		
			$html = $this->load->view("payout_periods/edit",$data,true);
		 
			$title = "Edit Payout Period " . str_pad($payout_period_id, 6, "0", STR_PAD_LEFT);
			
			$this->return_json("1","With pending transfer request",array("html" => $html, "title" => $title, "payout_period_id"=> $payout_period_id));
		}
		
		return;
	}
	
	
	public function confirm_edit() {
		$payout_period_id = $this->input->post("payout_period_id");
		$payout_type = $this->input->post("payout_type");
		$start_date = $this->input->post("start_date");
		$end_date = $this->input->post("end_date");
		$payout_status = $this->input->post("payout_status");
		$is_official = $this->input->post("is_official");
		$remarks = $this->input->post("remarks");
		
		$payout_period = $this->payout_model->get_payout_period_by_id($payout_period_id);
	
		$proper_period_id = str_pad($payout_period_id, 6, "0", STR_PAD_LEFT);
		
		if (empty($payout_period)) {
		
			$html = "<p>There is something wrong with this transaction [Payout Period ID: {$proper_period_id}].</p>";
			$this->return_json("0","Empty Payout Schedule Info", array("html" => $html));
			
		} else {
			
			// check if schedule exists
			$where = "payout_type = '{$payout_type}' 
						AND payout_period_id <> {$payout_period_id}
						AND ((start_date BETWEEN '{$start_date}' AND '{$end_date}') AND (end_date BETWEEN '{$start_date}' AND '{$end_date}'))";
			
			$conflict_count = $this->payout_model->get_payout_periods_count($where);

			if ($conflict_count == 0) {

				$html = "<p>You are about to Edit Payout Period ID <strong>{$proper_period_id}</strong> with the following details:</p>
					<table class='table table-striped table-bordered'>
						<thead> 
							<tr>
								<td></td>
								<td><strong>FROM</strong></td>
								<td><strong>TO</strong></td>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td style='width:30px;'>
									<label>
										<strong>Payout Type</strong>
									</label>
								</td>
								<td style='width:100px;'>
									<label>
										{$payout_period->payout_type}
									</label>
								</td>						
								<td style='width:100px;'>
									<label>
										{$payout_type}
									</label>
								</td>						
							</tr>					
							<tr>
								<td style='width:30px;'>
									<label>
										<strong>Start Date</strong>
									</label>
								</td>
								<td style='width:100px;'>
									<label>
										{$payout_period->start_date}
									</label>
								</td>						
								<td style='width:100px;'>
									<label>
										{$start_date}
									</label>
								</td>						
							</tr>					
							<tr>
								<td style='width:30px;'>
									<label>
										<strong>End Date</strong>
									</label>
								</td>
								<td style='width:100px;'>
									<label>
										{$payout_period->end_date}
									</label>
								</td>						
								<td style='width:100px;'>
									<label>
										{$end_date}
									</label>
								</td>						
							</tr>			
							<tr>
								<td style='width:30px;'>
									<label>
										<strong>Status</strong>
									</label>
								</td>
								<td style='width:100px;'>
									<label>
										{$payout_period->status}
									</label>
								</td>						
								<td style='width:100px;'>
									<label>
										{$payout_status}
									</label>
								</td>						
							</tr>
							<tr>
								<td style='width:30px;'>
									<label>
										<strong>Is Official</strong>
									</label>
								</td>
								<td style='width:100px;'>
									<label>
										{$payout_period->is_official}
									</label>
								</td>						
								<td style='width:100px;'>
									<label>
										{$is_official}
									</label>
								</td>						
							</tr>						
						</tbody>
					</table>
					
					<div>
						<label class='control-label'>		
							<strong>Remarks*</strong>
						</label>
						<textarea disabled='disabled' id='remarks' class='span5' type='text' value='' style='height:100px;resize=none;' name='remarks'>{$remarks}</textarea>
						<br/>
						
					</div>
					
					
					<p>Do you want to proceed?</p>
					";
				 
				$title = "Confirm Edit Payout Period :: " . str_pad($payout_period_id, 6, "0", STR_PAD_LEFT);
				
				$data = array (
					'payout_period_id' => $payout_period_id,
					'payout_type' => $payout_type,
					'start_date' => $start_date,
					'end_date' => $end_date,
					'payout_status' => $payout_status,
					'is_official' => $is_official,
					'remark' => $remark
					);	


				$this->return_json("1","Confirm Edit Payout Period/Schedule",array("html" => $html, "title" => $title, "data" => $data));
			
			} else {

				// conflict
				$all_logs = $this->payout_model->get_payout_periods($where, "","start_date DESC");

				$html = "Opps! A total of {$conflict_count} conflicts found. Please set another payout period.";

				if ($conflict_count <= 10) {

					$html = "Opps! A total of {$conflict_count} conflict/s found.
						<br/><br/>
						<table class='table table-striped table-bordered'>
						<thead> 					
						</thead>
						<tbody>";

					foreach($all_logs as $al) {
						$html .= "<tr>
								<td>$al->payout_period_id</td>
								<td>$al->start_date</td>
								<td>$al->end_date</td>
								<td>$al->status</td>
							</tr>";
					}

					$html .= "</tbody></table>
								<br/>
							Please set another payout period.";
				}

				$title = "Add Payout Period :: Error";

				$this->return_json("0","Conflict Payout Period",array("html" => $html, "title" => $title));
			}
		}
		
		return;
	}
	
	
	public function proceed_edit() {
	
		$payout_period_id = $this->input->post("payout_period_id");
		$payout_type = $this->input->post("payout_type");
		$start_date = $this->input->post("start_date");
		$end_date = $this->input->post("end_date");
		$payout_status = $this->input->post("payout_status");
		$is_official = $this->input->post("is_official");
		$remarks = $this->input->post("remarks");
				
		$payout_period = $this->payout_model->get_payout_period_by_id($payout_period_id);
	
		$proper_period_id = str_pad($payout_period_id, 6, "0", STR_PAD_LEFT);
		
		if (empty($payout_period)) {
		
			$title = "Edit Payout Period " . str_pad($payout_period_id, 6, "0", STR_PAD_LEFT) . " :: Error";
		
			$html = "<p>There is something wrong with this transaction [Payout Period ID: {$proper_period_id}].</p>";
			$this->return_json("0","Empty Payout Schedule Info", array("html" => $html, "title" => $title));
			
		} else {
	
			// change status to COMPLETED
			$current_datetime = date("Y-m-d H:i:s");
		
			$payout_period_details_before = array(
				"payout_type"=>$payout_period->payout_type, 
				"start_date"=>$payout_period->start_date, 
				"end_date"=>$payout_period->end_date, 
				"status"=>$payout_period->status,
				"is_official"=>$payout_period->is_official,
			);
			
			$details_before = array('id' => $payout_period_id, 'details' => $payout_period_details_before);
			$details_before = json_encode($details_before);
			
			
			// change the status in tr_member_transfers to PROCESSING			
			$data = array(
				'payout_type' => $payout_type,
				'start_date' => $start_date,
				'end_date' => $end_date,
				'status' => $payout_status,
				'is_official' => $is_official
			);
			
			$where = "payout_period_id = " . $payout_period_id;
			$this->payout_model->update_payout_period($data, $where);
						
			// logging after update
			$details_after = array('id' => $payout_period_id, 'details' => $data);
			$details_after = json_encode($details_after);
			$update_payout_period_logs = array(
				'user_id' => $this->user->user_id,
				'module_name' => "UPDATE PAYOUT PERIODS",
				'table_name' => "po_payout_periods",
				'action' => "UPDATE",
				'details_before' => $details_before,
				'details_after' => $details_after,
				'remarks' => $remarks
			);
			$this->tracking_model->insert_logs('admin', $update_payout_period_logs);
			
			$html = "You have successfully updated the details for Payout Period ID <strong>{$proper_period_id}</strong>.";
			
			$title = "Edit Payout Period :: Successful";
			
			$this->return_json("1","Update Payout Periods",array("html" => $html, "title" => $title));		
		}	
		return;	
	}
	
	
	public function add()
	{

		$data = array();
	
		$html = $this->load->view("payout_periods/add",$data,true);
	 
		$title = "Add Payout Period";
		
		$this->return_json("1","Add New Payout Period",array("html" => $html, "title" => $title));
		
		return;
	}
	
	public function confirm_add() {		
		$payout_type = $this->input->post("payout_type");
		$raw_start_date = $this->input->post("start_date");
		$raw_end_date = $this->input->post("end_date");		
		$payout_status = $this->input->post("payout_status");
		$is_official = $this->input->post("is_official");
		$remarks = $this->input->post("remarks");
		
		$start_date = $raw_start_date . ":00";
		$end_date = $raw_end_date . ":59";

		// check if start_date is greater than end_date
		if ($start_date > $end_date)
		{

			$this->return_json("0","Start Date must not exceed End Date.",array("html" => $html, "title" => $title));
			return;
		}
		
		// check if schedule exists
		$where = "payout_type = '{$payout_type}' 
					AND ((start_date BETWEEN '{$raw_start_date}' AND '{$raw_end_date}') AND (end_date BETWEEN '{$raw_start_date}' AND '{$raw_end_date}'))";		
		
		$conflict_count = $this->payout_model->get_payout_periods_count($where);

		if ($conflict_count == 0) {
		
			$html = "<p>You are about to Add a Payout Period with the following details:</p>
				<table class='table table-striped table-bordered'>
					<thead> 					
					</thead>
					<tbody>
						<tr>
							<td style='width:30px;'>
								<label>
									<strong>Payout Type</strong>
								</label>
							</td>						
							<td style='width:100px;'>
								<label>
									{$payout_type}
								</label>
							</td>						
						</tr>					
						<tr>
							<td style='width:30px;'>
								<label>
									<strong>Start Date</strong>
								</label>
							</td>						
							<td style='width:100px;'>
								<label>
									{$start_date}
								</label>
							</td>						
						</tr>					
						<tr>
							<td style='width:30px;'>
								<label>
									<strong>End Date</strong>
								</label>
							</td>						
							<td style='width:100px;'>
								<label>
									{$end_date}
								</label>
							</td>						
						</tr>			
						<tr>
							<td style='width:30px;'>
								<label>
									<strong>Status</strong>
								</label>
							</td>
							<td style='width:100px;'>
								<label>
									{$payout_status}
								</label>
							</td>						
						</tr>		
						<tr>
							<td style='width:30px;'>
								<label>
									<strong>Is Official</strong>
								</label>
							</td>
							<td style='width:100px;'>
								<label>
									{$is_official}
								</label>
							</td>						
						</tr>							
					</tbody>
				</table>
				
				<div>
					<label class='control-label'>		
						<strong>Remarks*</strong>
					</label>
					<textarea disabled='disabled' id='remarks' class='span5' type='text' value='' style='height:100px;resize=none;' name='remarks'>{$remarks}</textarea>
					<br/>
					
				</div>
				
				
				<p>Do you want to proceed?</p>
				";
				 
			$title = "Add Payout Period :: Confirm";

			$data = array(
					'payout_type' => $payout_type,
					'start_date' =>$raw_start_date,
					'end_date' =>$raw_end_date,		
					'payout_status' =>$payout_status,
					'is_official' =>$is_official,
					'remarks' =>$remarks
				);
			
			$this->return_json("1","Confirm Add Payout Period/Schedule",array("html" => $html, "title" => $title, "data" => $data));
		
		} else {
			// conflict
			$all_logs = $this->payout_model->get_payout_periods($where, "","start_date DESC");

			$html = "Opps! A total of {$conflict_count} conflict/s found. Please set another payout period.";

			if ($conflict_count <= 10) {

				$html = "Opps! A total of {$conflict_count} conflict/s found.
					<br/><br/>
					<table class='table table-striped table-bordered'>
					<thead> 					
					</thead>
					<tbody>";

				foreach($all_logs as $al) {
					$html .= "<tr>
							<td>$al->payout_period_id</td>
							<td>$al->start_date</td>
							<td>$al->end_date</td>
							<td>$al->status</td>
						</tr>";
				}

				$html .= "</tbody></table>
							<br/>
						Please set another payout period.";
			}

			$title = "Add Payout Period :: Error";

			$this->return_json("0","Conflict Payout Period",array("html" => $html, "title" => $title));
		}

		return;
	}
	
	public function proceed_add() {
			
		$payout_type = $this->input->post("payout_type");
		$start_date = $this->input->post("start_date") . ":00";
		$end_date = $this->input->post("end_date"). ":59";
		$payout_status = $this->input->post("payout_status");
		$is_official = $this->input->post("is_official");
		$remarks = $this->input->post("remarks");
				
		// change status to COMPLETED
		$current_datetime = date("Y-m-d H:i:s");
	
		$data = array(			
			'payout_type' => $payout_type,
			'start_date' => $start_date,
			'end_date' => $end_date,
			'status' => $payout_status,
			'is_official' => $is_official
		);
				
		$this->payout_model->insert_payout_period($data);
		
		//get next id
		$payout_period_id = $this->payout_model->insert_id();
					
		// logging after update		
		$details_after = array('id' => $payout_period_id, 'details' => $data);
		$details_after = json_encode($details_after);
		$update_member_funds_logs = array(
			'user_id' => $this->user->user_id,
			'module_name' => "UPDATE PAYOUT PERIODS",
			'table_name' => "po_payout_periods",
			'action' => "INSERT",
			'details_before' => "",
			'details_after' => $details_after,
			'remarks' => $remarks
		);
		$this->tracking_model->insert_logs('admin', $update_member_funds_logs);
		
		$proper_period_id = str_pad($payout_period_id, 6, "0", STR_PAD_LEFT);
		
		$html = "You have successfully inserted a New Payout Period with ID <strong>{$proper_period_id}</strong>.";
		
		$title = "Add Payout Period :: Successful";
		
		$this->return_json("1","Insert Payout Periods",array("html" => $html, "title" => $title));

		return;
		
	}
	

	
	public function view_transaction()
	{
		$member_transfer_id = $this->input->post("member_transfer_id");
		
		$transfer = $this->tracking_model->get_member_transfer_by_id($member_transfer_id);
	
		if (empty($transfer)) {
		
			$html = "<p>There is something wrong with this transaction [Transfer ID: {$member_transfer_id}].</p>";
			$this->return_json("0","Empty Transfer info",array("html" => $html));
			
		} else {
	
			// get from_member details			
			$from_member_details = $this->members_model->get_member_by_id($transfer->from_member_id); 
			
			$proper_name_from_member = ucfirst($from_member_details->first_name) . " " . ucfirst($from_member_details->middle_name) . " " . ucfirst($from_member_details->last_name);
			
			$to_member_details = $this->members_model->get_member_by_id($transfer->to_member_id); 
			
			$proper_name_to_member = ucfirst($to_member_details->first_name) . " " . ucfirst($to_member_details->middle_name) . " " . ucfirst($to_member_details->last_name);
			
			$transfer_amount = number_format($transfer->amount , 2);
			
			$html = "<fieldset >		
						<div>
							<label class='control-label' for='from_member_name'><strong>From Member Name</strong></label>								
							<input type='text' class='span5' disabled='disabled' placeholder='' name='from_member_name' id='from_member_name' value='{$proper_name_from_member}'/>								
						</div>
						
						<div>
							<label class='control-label' for='transfer_amount'><strong>Amount</strong></label>								
							<input type='text' class='span3' disabled='disabled' placeholder='' name='transfer_amount' id='transfer_amount' value='{$transfer_amount}'/>								
						</div>
						
						<div>
							<label class='control-label' for='to_member_name'><strong>To Member Name</strong></label>								
							<input type='text' class='span5' disabled='disabled' placeholder='' name='to_member_name' id='to_member_name' value='{$proper_name_to_member}'/>								
						</div>
						
						<div>
							<label class='control-label' for='transaction_type'><strong>Transaction Type</strong></label>								
							<input type='text' class='span3' disabled='disabled' placeholder='' name='transaction_type' id='transaction_type' value='{$transfer->type}'/>								
						</div>
						
						<div>
							<label class='control-label' for='confirmation_code'><strong>Confirmation Code</strong></label>								
							<input type='text' class='span4' disabled='disabled' placeholder='' name='confirmation_code' id='confirmation_code' value='{$transfer->confirmation_code}'/>								
						</div>
						
						<div>
							<label class='control-label' for='date_initiated'><strong>Date Initiated</strong></label>								
							<input type='text' class='span4' disabled='disabled' placeholder='' name='date_initiated' id='date_initiated' value='{$transfer->insert_timestamp}'/>								
						</div>
						
						<div>
							<label class='control-label' for='date_completed'><strong>Date Completed</strong></label>								
							<input type='text' class='span4' disabled='disabled' placeholder='' name='date_completed' id='date_completed' value='{$transfer->update_timestamp}'/>								
						</div>
						
						
						<div align='center'>
							<span><strong> - {$transfer->status} - </strong></span>
						</div>												
					</fieldset>
					";
			 
			$title = "Transfer Details :: " . $transfer->status;
			
			$this->return_json("1","With pending transfer request",array("html" => $html, "title" => $title, "transfer_status"=> $transfer->status));
		}
		
		return;
	}
	
	
	public function info()
	{
		$payout_period_id = $this->input->post("payout_period_id");
		
		
		$payout_period = $this->payout_model->get_payout_period_by_id($payout_period_id);
	
		if (empty($payout_period)) {
		
			$title = "View Info Payout Period ID " . str_pad($payout_period_id, 6, "0", STR_PAD_LEFT) . " :: Error";
		
			$html = "<p>There is something wrong with this Payout Cut-off Schedule [Payout Period ID: {$payout_period_id}].</p>";
			$this->return_json("0","Empty Payout Period info",array("html" => $html, "title" => $title));
			
		} else {
		
		
			$data = array(
			"payout_period" => $payout_period
			);
		
			$html = $this->load->view("payout_periods/info",$data,true);
		 
			$title = "View Info Payout Period " . str_pad($payout_period_id, 6, "0", STR_PAD_LEFT);
			
			$this->return_json("1","View Payout Period Info",array("html" => $html, "title" => $title, "payout_period_id"=> $payout_period_id));
		}
		
		return;
	}
	
	
	public function download_check()
	{
		$start_date = trim($this->input->post("start_date"));
		$end_date = trim($this->input->post("end_date"));

		//hash check
		/*TODO: CREATE HASH CHECK*/

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
		$where = "CAST(start_date AS DATE) BETWEEN '$start_date' AND '$end_date' AND CAST(end_date AS DATE) BETWEEN '$start_date' AND '$end_date'";

		$pending_count = $this->payout_model->get_payout_periods($where);

		if (empty($pending_count))
		{
			$return_html = "<span>No Payout Schedule from <strong>{$proper_start_date}</strong> to <strong>{$proper_end_date}</strong>.</span>";
			$this->return_json("ok",$return_html);
			return;
		} else
		{
			$return_html = "<span>You are to generate Payout Period Schedules from <strong>{$proper_start_date}</strong> to <strong>{$proper_end_date}</strong>.<br/>Do you want to proceed?</span>";
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

	function export_xls($start_date,$end_date)
	{
		$this->load->library('PHPExcel');
        $this->load->library('PHPExcel/IOFactory');
		
		try {
			
			// adjusting memory limit to accomodate PHPExcel processing 
			set_time_limit(0); // eliminating the timeout
			ini_set('memory_limit', '512M');

	        $objPHPExcel = new PHPExcel();
	        $objPHPExcel->getProperties()->setTitle("payout requests")->setDescription("none");
			$start_column_num = 3;

			$worksheet = $objPHPExcel->setActiveSheetIndex(0);

			$where = "CAST(start_date AS DATE) BETWEEN '$start_date' AND '$end_date' AND CAST(end_date AS DATE) BETWEEN '$start_date' AND '$end_date'";
			$member_payout_count = $this->payout_model->get_payout_periods_count($where);

			$filename = "payout_periods_" . str_replace("-", "", $start_date) . "_to_" . str_replace("-", "", $end_date) . ".xls";

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

			//center column names
			$worksheet->getStyle('A' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$worksheet->getStyle('B' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$worksheet->getStyle('C' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$worksheet->getStyle('D' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$worksheet->getStyle('E' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$worksheet->getStyle('F' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$worksheet->getStyle('G' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);			
			
			//set column names
			$worksheet->setCellValue('A1', "Payout Periods from {$start_date} to {$end_date}");
			$worksheet->setCellValue('A' . $start_column_num, 'Status');
			$worksheet->setCellValue('B' . $start_column_num, 'Payout Period ID');
			$worksheet->setCellValue('C' . $start_column_num, 'Start Date');
			$worksheet->setCellValue('D' . $start_column_num, 'End Date');
			$worksheet->setCellValue('E' . $start_column_num, 'Type');
			$worksheet->setCellValue('F' . $start_column_num, 'Is Official');
			$worksheet->setCellValue('G' . $start_column_num, 'Date Created');
			

			$row = 4;

			$allowed_rows = 5000;

			for($prow = 0;$prow < ceil($member_payout_count/$allowed_rows)+1;$prow++)
			{
				$member_payouts = $this->payout_model->get_payout_periods($where, array('rows' => $allowed_rows, 'offset' => $prow*$allowed_rows), 'insert_timestamp ASC');

				foreach ($member_payouts as $mp)
				{

					$pretty_id = str_pad($mp->payout_period_id, 6, "0", STR_PAD_LEFT);
				
					$worksheet->setCellValue('A'. $row, $mp->status);
					$worksheet->setCellValueExplicit('B'. $row, $pretty_id, PHPExcel_Cell_DataType::TYPE_STRING);
					$worksheet->setCellValue('C'. $row, $mp->start_date);
					$worksheet->setCellValue('D'. $row, $mp->end_date);
					$worksheet->setCellValue('E'. $row, $mp->payout_type);
					$worksheet->setCellValue('F'. $row, $mp->is_official);
					$worksheet->setCellValue('G'. $row, $mp->insert_timestamp);
					
					// auto resize columns
					$worksheet->getColumnDimension('A')->setAutoSize(false);
					$worksheet->getColumnDimension('B')->setAutoSize(true);
					$worksheet->getColumnDimension('C')->setAutoSize(true);
					$worksheet->getColumnDimension('D')->setAutoSize(true);
					$worksheet->getColumnDimension('E')->setAutoSize(true);
					$worksheet->getColumnDimension('F')->setAutoSize(true);
					$worksheet->getColumnDimension('G')->setAutoSize(true);
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

	public function confirm_rerun() {
		$payout_period_id = $this->input->post("payout_period_id");
		
		$payout_period = $this->payout_model->get_payout_period_by_id($payout_period_id);
	
		$proper_period_id = str_pad($payout_period_id, 6, "0", STR_PAD_LEFT);
		
		if (empty($payout_period)) {
		
			$html = "<p>There is something wrong with this transaction [Payout Period ID: {$proper_period_id}].</p>";
			$this->return_json("0","Empty Payout Schedule Info", array("html" => $html));
			
		} else {
			
			
			$html = "<p>You are about to Re-run Payout Period ID <strong>{$proper_period_id}</strong> with the following details:</p>
				<table class='table table-striped table-bordered'>
					<thead> 
					</thead>
					<tbody>
						<tr>
							<td style='width:30px;'>
								<label>
									<strong>Payout Type</strong>
								</label>
							</td>
							<td style='width:100px;'>
								<label>
									{$payout_period->payout_type}
								</label>
							</td>										
						</tr>					
						<tr>
							<td style='width:30px;'>
								<label>
									<strong>Start Date</strong>
								</label>
							</td>
							<td style='width:100px;'>
								<label>
									{$payout_period->start_date}
								</label>
							</td>											
						</tr>					
						<tr>
							<td style='width:30px;'>
								<label>
									<strong>End Date</strong>
								</label>
							</td>
							<td style='width:100px;'>
								<label>
									{$payout_period->end_date}
								</label>
							</td>										
						</tr>			
						<tr>
							<td style='width:30px;'>
								<label>
									<strong>Status</strong>
								</label>
							</td>
							<td style='width:100px;'>
								<label>
									{$payout_period->status}
								</label>
							</td>											
						</tr>
						<tr>
							<td style='width:30px;'>
								<label>
									<strong>Is Official</strong>
								</label>
							</td>
							<td style='width:100px;'>
								<label>
									{$payout_period->is_official}
								</label>
							</td>										
						</tr>						
					</tbody>
				</table>
				
				<div>
					<label class='control-label'>		
						<strong>Remarks*</strong>
					</label>
					<textarea disabled='disabled' id='remarks' class='span5' type='text' value='' style='height:100px;resize=none;' name='remarks'></textarea>
					<br/>
					
				</div>
				
				
				<p>Do you want to proceed?</p>
				";
			 
			$title = "Confirm Re-run Payout Period :: " . str_pad($payout_period_id, 6, "0", STR_PAD_LEFT);
			
			
			$this->return_json("1","Confirm Re-run Payout Period/Schedule",array("html" => $html, "title" => $title));	
			
		}
		
		return;
	}

	public function proceed_rerun() {
		$payout_period_id = $this->input->post("payout_period_id");
		
		$payout_period = $this->payout_model->get_payout_period_by_id($payout_period_id);
	
		$proper_period_id = str_pad($payout_period_id, 6, "0", STR_PAD_LEFT);
		
		if (empty($payout_period)) {

			$html = "<p>There is something wrong with this transaction [Payout Period ID: {$proper_period_id}].</p>";
			$this->return_json("0","Empty Payout Schedule Info", array("html" => $html));
			
		} else {
		
			/*// create checker file
			$jsonData = json_encode(array(
				'start_date' => $payout_period->start_date,
				'end_date' => $payout_period->end_date,
				'type' => $payout_period->payout_type
			));

			$myFile = FCPATH . "assets/media/tmp/payoutFileRerun.txt";
			$fh = fopen($myFile, 'w') or die("can't open file");
			fwrite($fh, $jsonData);
			fclose($fh);
			*/

			// update rerun_Status = 'PROCESSING'
			$data_update = array(
				'rerun_status' => 'PROCESSING'
				);

			$where = "payout_period_id = " . $payout_period_id;

			$this->payout_model->update_payout_period($data_update, $where);

			// exec shell command
			$root_path = FCPATH;
			exec("/usr/bin/php {$root_path}jobs.php jobs cron_payout check_payouts ". $payout_period_id ." >> /dev/null 2>&1");

			//var_dump("/usr/bin/php {$root_path}jobs.php jobs cron_payout check_payouts ". $payout_period_id ." >> /dev/null 2>&1");

			/*// wait for the process to be completed
			while(file_exists(FCPATH . "assets/media/tmp/payoutFileRerun.txt"))
			{
				// do nothing
			}
			*/	
			//$pretty_start_date = str_replace("-","",$start_date);
			//$pretty_end_date = str_replace("-","",$end_date);
			//$merged_filename = $type . '_commission_payout_' . $pretty_start_date . '_to_' . $pretty_end_date . '.xlsx';

			//return $merged_filename;

			
			$html = "Payout Report is currently being generated. Please check your email to download the result.";
			 
			$title = "Proceed Re-run Payout Period :: " . str_pad($payout_period_id, 6, "0", STR_PAD_LEFT);
			
			
			$this->return_json("1","Proceed Re-run Payout Period/Schedule",array("html" => $html, "title" => $title));	
			
		}
		
		return;
	}

}