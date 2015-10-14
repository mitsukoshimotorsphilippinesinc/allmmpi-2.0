<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Voucher_transfers extends Systems_Controller {

	function __construct()
	{
		parent::__construct();

		$this->load->model('members_model');
		$this->load->model('tracking_model');
		$this->load->model('vouchers_model');
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
		$result = "";
		
		if ((trim($search_text) == "") || empty($search_text)) {
			$where = "status = 'TRANSFERRING'";		
			$count_is = $this->vouchers_model->get_member_account_vouchers_count($where);		
		} else {
				
			if (($search_by == "last_name") || ($search_by == "first_name")) {
				$sql = "select count(*) as cnt from (select * from cm_member_account_vouchers where status = 'TRANSFERRING' AND member_id in 
						(select member_id from cm_members where upper(". $search_by .") like upper('%". $search_text ."%'))
						UNION
						select * from cm_member_account_vouchers where status = 'TRANSFERRING' AND to_member_id in 
						(select member_id from cm_members where upper(". $search_by .") like upper('%". $search_text ."%'))) a
					";

				$query = $this->db->query($sql);
				$total_results = $query->result();
				$query->free_result();

				$count_is = $total_results[0]->cnt;

				$offset = 0;
				if($this->uri->segment(4))	
				{
					$offset = ($this->uri->segment(4)-1)*10;
				}
				
				$sql = "select * from cm_member_account_vouchers where status = 'TRANSFERRING' AND member_id in 
						(select member_id from cm_members where upper(". $search_by .") like upper('%". $search_text ."%'))
						UNION
						select * from cm_member_account_vouchers where status = 'TRANSFERRING' AND to_member_id in 
						(select member_id from cm_members where upper(". $search_by .") like upper('%". $search_text ."%'))
						LIMIT  ". $offset .", 10
					";

				$query = $this->db->query($sql);
				$result = $query->result();
				$query->free_result();
				
				
				$transfers = $result;
				//var_dump($result[0] ."|".$total_results[0]->cnt);
				
			} else {
			
				if ($search_by == "voucher_code") {
					
					$search_text = ltrim($search_text, "0");
					$where = $search_by.' = "'.$search_text.'"';
				} else if ($search_by == "voucher_id") {	
					$where = $search_by. " LIKE TRIM(LEADING '0' FROM '{$search_text}')";
				} else {				
					$where = $search_by.' LIKE LOWER("%'.$search_text.'%")';
				}
				
				$count_is = $this->vouchers_model->get_member_account_vouchers_count($where);

				
			}

			$search_url = "?search_option=" . $search_by . "&search_string=" . $search_text;
			
		}
		// initialize pagination class
		// set pagination data

		$config = array(
				'pagination_url' => "/admin/voucher_transfers/index/",
				'total_items' => $count_is,
				'per_page' => 10,
				'uri_segment' => 4,
		);

		$this->pager->set_config($config);
		
		if ((($search_by == "last_name") || ($search_by == "first_name")) && (trim($search_text) != "")) {
			$transfers = $result;
		}
		else
		{		
			$transfers = $this->vouchers_model->get_member_account_vouchers($where, array('rows' => $this->pager->per_page, 'offset' => $this->pager->offset), "updated_timestamp DESC");
		}	
		
		foreach ($transfers as $k => $p)
		{
			$member = $this->members_model->get_member_by_id($p->member_id);

			$p->first_name = $member->first_name;
			$p->last_name = $member->last_name;

			$transfers[$k] = $p;
		}

		$_days = array('0' => '-');
		for ($i = 1; $i <= 31; $i++)
			$_days[$i] = $i;

		$_years = array('0' => '-');
		$_today = getdate();
		for ($i = $_today['year']; $i >= 2000; $i--)
			$_years[$i] = $i;

		// search vars
		$this->template->search_by = $search_by;
		$this->template->search_text = $search_text;
		$this->template->search_url = $search_url;

		$this->template->months = $this->_months;
		$this->template->days = $_days;
		$this->template->years = $_years;
		$this->template->transfers = $transfers;
		
		$this->template->view('voucher_transfers/dashboard');
	}

	public function resend_code()
	{
		$voucher_id = $this->input->post("voucher_id");
		
		$transfer = $this->vouchers_model->get_member_account_voucher_by_id($voucher_id);
	
		if (empty($transfer)) {
		
			$html = "<p>There is something wrong with this voucher [Voucher ID: {$voucher_id}].</p>";
			$this->return_json("0","Empty Transfer info",array("html" => $html));
			
		} else {
	
			// get from_member details			
			$from_member_details = $this->members_model->get_member_by_id($transfer->member_id); 
			
			$proper_name_from_member = ucfirst($from_member_details->first_name) . " " . ucfirst($from_member_details->middle_name) . " " . ucfirst($from_member_details->last_name);
			
			$to_member_details = $this->members_model->get_member_by_id($transfer->to_member_id); 
			
			$proper_name_to_member = ucfirst($to_member_details->first_name) . " " . ucfirst($to_member_details->middle_name) . " " . ucfirst($to_member_details->last_name);
			
			$proper_voucher_id = str_pad($voucher_id, 8, "0", STR_PAD_LEFT);
			
			// send email notification to member who requested
			$params = array(
				"first_name"=>ucfirst($from_member_details->first_name),
				"proper_transaction_id"=>$proper_voucher_id,
				"proper_to_member_name"=>$proper_name_to_member,
				"voucher_code"=>$transfer->voucher_code,
				"proper_voucher_id"=>$proper_voucher_id,
				"confirmation_code"=>$transfer->confirmation_code					
			);

			
			$data = array(
				"email"=>$from_member_details->email,
				"type"=>"resend_voucher_code_via_admin_from_member",
				"params"=>$params
			);

			//send email to user
			Modules::run('jobs/notifications/send_email',$data);

		
			// logging after update
			$details_before = array('id' => $transfer->voucher_id, 'details' => $params);
			$details_before = json_encode($details_before);
			$update_member_funds_logs = array(
				'user_id' => $this->user->user_id,
				'module_name' => "VOUCHER TRANSFER - RESEND CODE",
				'table_name' => "cm_member_account_vouchers",
				'action' => "RESEND CODE",
				'details_before' => $details_before,
				'details_after' => $details_before
			);
			$this->tracking_model->insert_logs('admin', $update_member_funds_logs);
			
			$html = "The Confirmation Code was sent successfully via email to <strong>{$proper_name_from_member}</strong>. <br/>Transaction ID : <strong>{$proper_voucher_id}</strong>";		
			$this->return_json("1","Resend Transfer Code",array("html" => $html));
		}
		
		return;
	}
	
	
	public function view_transaction()
	{
		$voucher_id = $this->input->post("voucher_id");
		
		$transfer = $this->vouchers_model->get_member_account_voucher_by_id($voucher_id);
	
		if (empty($transfer)) {
		
			$html = "<p>There is something wrong with this transaction [Transfer ID: {$voucher_id}].</p>";
			$this->return_json("0","Empty Transfer info",array("html" => $html));
			
		} else {
	
			// get from_member details			
			$from_member_details = $this->members_model->get_member_by_id($transfer->member_id); 
			
			$proper_name_from_member = ucfirst($from_member_details->first_name) . " " . ucfirst($from_member_details->middle_name) . " " . ucfirst($from_member_details->last_name);
			
			$proper_name_to_member = '';

			if($transfer->to_member_id)
			{
				$to_member_details = $this->members_model->get_member_by_id($transfer->to_member_id); 
				$proper_name_to_member = ucfirst($to_member_details->first_name) . " " . ucfirst($to_member_details->middle_name) . " " . ucfirst($to_member_details->last_name);
			}
			
			$html = "<fieldset >		
						<div>
							<label class='control-label' for='from_member_name'><strong>From Member Name</strong></label>								
							<input type='text' class='span5' disabled='disabled' placeholder='' name='from_member_name' id='from_member_name' value='{$proper_name_from_member}'/>								
						</div>
						
						<div>
							<label class='control-label' for='voucher_code'><strong>Amount</strong></label>								
							<input type='text' class='span3' disabled='disabled' placeholder='' name='voucher_code' id='voucher_code' value='{$transfer->voucher_code}'/>								
						</div>
						
						<div>
							<label class='control-label' for='voucher_type'><strong>Amount</strong></label>								
							<input type='text' class='span3' disabled='disabled' placeholder='' name='voucher_type' id='voucher_type' value='{$transfer->voucher_type_id}'/>								
						</div>
						
						<div>
							<label class='control-label' for='to_member_name'><strong>To Member Name</strong></label>								
							<input type='text' class='span5' disabled='disabled' placeholder='' name='to_member_name' id='to_member_name' value='{$proper_name_to_member}'/>								
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
							<input type='text' class='span4' disabled='disabled' placeholder='' name='date_completed' id='date_completed' value='{$transfer->updated_timestamp}'/>								
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
	
	public function confirm_cancel() {
	
		$voucher_id = $this->input->post("voucher_id");
				
		$transfer = $this->vouchers_model->get_member_account_voucher_by_id($voucher_id);
	
		if (empty($transfer)) {
		
			$html = "<p>There is something wrong with this transaction [Transfer ID: {$voucher_id}].</p>";
			$this->return_json("0","Empty Transfer info",array("html" => $html));
			
		} else {
		
			// get from_member details			
			$from_member_details = $this->members_model->get_member_by_id($transfer->member_id); 
			
			$proper_name_from_member = ucfirst($from_member_details->first_name) . " " . ucfirst($from_member_details->middle_name) . " " . ucfirst($from_member_details->last_name);
			
			$to_member_details = $this->members_model->get_member_by_id($transfer->to_member_id); 
			
			$proper_name_to_member = ucfirst($to_member_details->first_name) . " " . ucfirst($to_member_details->middle_name) . " " . ucfirst($to_member_details->last_name);
			
			$proper_voucher_id = str_pad($voucher_id, 8, "0", STR_PAD_LEFT);
			
			$html =	"<h4>Transaction ID: {$proper_voucher_id}</h4>
			<p>Are you sure you want to cancel this Voucher Transfer transaction with code <strong>{$transfer->voucher_code}</strong>  from <strong>{$proper_name_from_member}</strong> to <strong>{$proper_name_to_member}</strong>?
			</p>
			<label><strong>Remarks:</strong></label>
			<textarea id='cancel_remarks' style='width:400px;' placeholder='Put remarks here...'></textarea>
			<span id='cancel_remarks_error' style='display:none' class='label label-important'>Remarks Required.</span>"
			;			
			
			$this->return_json("1","Confirmation before cancelling transfer",array("html" => $html, "transfer_status"=> $transfer->status));
		}
		
		return;
	
	}
		
	public function proceed_cancel() {
	
		$voucher_id = $this->input->post("voucher_id");
		$remarks = $this->input->post("remarks");
				
		$transfer = $this->vouchers_model->get_member_account_voucher_by_id($voucher_id);
	
		if (empty($transfer)) {
		
			$html = "<p>There is something wrong with this transaction [Transfer ID: {$voucher_id}].</p>";
			$this->return_json("0","Empty Transfer info",array("html" => $html));
			
		} else {
		
			// change status to ACTIVE
			$current_datetime = date("Y-m-d H:i:s");		
			
			// get from_member details			
			$from_member_details = $this->members_model->get_member_by_id($transfer->member_id); 
			
			$proper_name_from_member = ucfirst($from_member_details->first_name) . " " . ucfirst($from_member_details->middle_name) . " " . ucfirst($from_member_details->last_name);
			
			// get from_member details			
			$to_member_details = $this->members_model->get_member_by_id($transfer->to_member_id); 
			
			$proper_name_to_member = ucfirst($to_member_details->first_name) . " " . ucfirst($to_member_details->middle_name) . " " . ucfirst($to_member_details->last_name);
			
			// =======
			// LOGGING
			// get details before update	
			$details_before = $transfer;	
			$details_before = json_encode($details_before);	
			
			// update member_id = to_member_id
			 $data = array(            
				'member_id' => $transfer->member_id,			
				'to_member_id' => 0,			            
				'status' => 'ACTIVE',
				'confirmation_code' => '',
				'updated_timestamp' => $current_datetime
			);
			$this->vouchers_model->update_member_account_vouchers($data, "voucher_id = " . $voucher_id);
			
			// logs 
			$details_after = array('voucher_id' =>  $voucher_id, 'details' => $data);
			$details_after = json_encode($details_after);
			$log_data = array(
				'voucher_id' => $voucher_id,
				'from_member_id' => $transfer->member_id,
				'to_member_id' =>  $transfer->to_member_id,				
				'details_before' => $details_before,
				'details_after' => $details_after
			);
			$this->tracking_model->insert_member_voucher_transfer_logs($log_data);
			
			
			// log to tr_member_logs
			$update_member_logs = array(
				'user_id' => $this->user->user_id,
				'module_name' => "CANCEL VOUCHER TRANSFER :: SUCCESSFUL",
				'table_name' => "cm_member_account_vouchers",
				'action' => "CANCEL",
				'details_before' => $details_before,
				'details_after' => $details_after,
				'remarks' => ""
			);
			$this->tracking_model->insert_logs('admin', $update_member_logs);		
			// END LOGGING
			// ===========
			
			$html = "You have successfully cancelled the Voucher Transfer to <strong>{$proper_name_to_member}</strong> with code {$transfer->voucher_code}.";
			
			$proper_member_voucher_id = str_pad($voucher_id, 8, "0", STR_PAD_LEFT);
			
			//// send email as a receipt to recipient
			//$params = array(
			//	"first_name"=>ucfirst($to_member_details->first_name),     
			//	"voucher_code"=>strtoupper($transfer->voucher_code),     			
			//	"proper_sender_name"=>$proper_name_from_member,
			//	"proper_voucher_id"=>$proper_member_voucher_id
			//);
		    //
			//$data = array(
			//	"email"=>$to_member_details->email,
			//	"type"=>"cancel_transfer_voucher_acceptance_receipt",
			//	"params"=>$params
			//);
		
			////send email to user
			//Modules::run('jobs/notifications/send_email',$data);
				
			// send email as a receipt to sender
			$params = array(
				"first_name"=>ucfirst($from_member_details->first_name),
				"voucher_code"=>strtoupper($transfer->voucher_code), 		
				"proper_recipient_name"=>$proper_name_to_member,
				"proper_voucher_id"=>$proper_member_voucher_id			
			);
		
			$data = array(
				"email"=>$from_member_details->email,
				"type"=>"cancel_transfer_voucher_notification_receipt",
				"params"=>$params
			);
		
			//send email to user
			Modules::run('jobs/notifications/send_email',$data);
			 
			
			$this->return_json("1","Update Voucher",array("html" => $html));
			
			return;
		}
	}
	
	
	public function confirm_transfer() {
	
		$voucher_id = $this->input->post("voucher_id");
		
		$transfer = $this->vouchers_model->get_member_account_voucher_by_id($voucher_id);
		
		if (empty($transfer)) {
		
			$html = "<p>There is something wrong with this transaction [Transfer ID: {$voucher_id}].</p>";
			$this->return_json("0","Empty Transfer info",array("html" => $html));
			
		} else {
		
			// get from_member details			
			$from_member_details = $this->members_model->get_member_by_id($transfer->member_id); 
			
			$proper_name_from_member = ucfirst($from_member_details->first_name) . " " . ucfirst($from_member_details->middle_name) . " " . ucfirst($from_member_details->last_name);
			
			$to_member_details = $this->members_model->get_member_by_id($transfer->to_member_id); 
			
			$proper_name_to_member = ucfirst($to_member_details->first_name) . " " . ucfirst($to_member_details->middle_name) . " " . ucfirst($to_member_details->last_name);
			
				
			$html =	"<p>You are to transfer a voucher with code <strong>{$transfer->voucher_code}</strong> from <strong>{$proper_name_from_member}</strong> to <strong>{$proper_name_to_member}</strong>.<br/><br/>Do you want to proceed?
			</p>					
			";
			
			$this->return_json("1","Confirmation before transferring voucher to member",array("html" => $html));
		}
		
		return;
	
	}
	
	public function proceed_transfer() {
	
		$voucher_id = $this->input->post("voucher_id");
				
		$transfer = $this->vouchers_model->get_member_account_voucher_by_id($voucher_id);
	
		if (empty($transfer)) {
		
			$html = "<p>There is something wrong with this transaction [Transfer ID: {$voucher_id}].</p>";
			$this->return_json("0","Empty Transfer info",array("html" => $html));
			
		} else {
		
			// change status to TRANSFERRING
			$current_datetime = date("Y-m-d H:i:s");		
			
			// get from_member details			
			$from_member_details = $this->members_model->get_member_by_id($transfer->member_id); 
			
			$proper_name_from_member = ucfirst($from_member_details->first_name) . " " . ucfirst($from_member_details->middle_name) . " " . ucfirst($from_member_details->last_name);
			
			// get from_member details			
			$to_member_details = $this->members_model->get_member_by_id($transfer->to_member_id); 
			
			$proper_name_to_member = ucfirst($to_member_details->first_name) . " " . ucfirst($to_member_details->middle_name) . " " . ucfirst($to_member_details->last_name);
			
			// =======
			// LOGGING
			// get details before update	
			$details_before = $transfer;	
			$details_before = json_encode($details_before);	
			
			// update member_id = to_member_id
			 $data = array(            
				'member_id' => $transfer->to_member_id,			
				'to_member_id' => 0,			            
				'status' => 'ACTIVE',
				'transfer_count' => $transfer->transfer_count++,
				'updated_timestamp' => $current_datetime
			);
			$this->vouchers_model->update_member_account_vouchers($data, "voucher_id = " . $voucher_id);
			
			// logs 
			$details_after = array('voucher_id' =>  $voucher_id, 'details' => $data);
			$details_after = json_encode($details_after);
			$log_data = array(
				'voucher_id' => $voucher_id,
				'from_member_id' => $transfer->member_id,
				'to_member_id' =>  $transfer->to_member_id,				
				'details_before' => $details_before,
				'details_after' => $details_after
			);
			$this->tracking_model->insert_member_voucher_transfer_logs($log_data);
			
			
			// log to tr_member_logs
			$update_member_logs = array(
				'user_id' => $this->user->user_id,
				'module_name' => "UPDATE VOUCHER TRANSFER :: SUCCESSFUL",
				'table_name' => "cm_member_account_vouchers",
				'action' => "UPDATE",
				'details_before' => $details_before,
				'details_after' => $details_after,
				'remarks' => ""
			);
			$this->tracking_model->insert_logs('admin', $update_member_logs);		
			// END LOGGING
			// ===========
			
			$html = "Congratulations! Voucher {$transfer->voucher_code} was successfully transferred to <strong>{$proper_name_to_member}</strong>.";
			
			$proper_member_voucher_id = str_pad($voucher_id, 8, "0", STR_PAD_LEFT);
			
			// send email as a receipt to recipient
			$params = array(
				"first_name"=>ucfirst($to_member_details->first_name),     
				"voucher_code"=>strtoupper($transfer->voucher_code),     			
				"proper_sender_name"=>$proper_name_from_member,
				"proper_voucher_id"=>$proper_member_voucher_id
			);
		
			$data = array(
				"email"=>$to_member_details->email,
				"type"=>"transfer_voucher_acceptance_receipt",
				"params"=>$params
			);
		
			//send email to user
			Modules::run('jobs/notifications/send_email',$data);
				
			// send email as a receipt to sender
			$params = array(
				"first_name"=>ucfirst($from_member_details->first_name),
				"voucher_code"=>strtoupper($transfer->voucher_code), 		
				"proper_recipient_name"=>$proper_name_to_member,
				"proper_voucher_id"=>$proper_member_voucher_id			
			);
		
			$data = array(
				"email"=>$from_member_details->email,
				"type"=>"transfer_voucher_notification_receipt",
				"params"=>$params
			);
		
			//send email to user
			Modules::run('jobs/notifications/send_email',$data);
			 
			
			$this->return_json("1","Update Voucher",array("html" => $html));
			
			return;
		}
		
	}
	
	public function execute()
	{
		$_transaction_id = $this->input->post("_transaction_id");

		$data = array(
				'status' => 'COMPLETED',
				'update_timestamp' => date("Y-m-d H:i:s")
		);
		$where = array("transaction_id" => $_transaction_id);

		$this->payment_model->update_member_payout($data, $where);

		//add to logs
		/* TODO: CREATE LOGGING FOR PROCESSING */

		$this->return_json("ok", "Update (COMPLETE WITHDRAW REQUEST) Successful. Inserted to Tracking Table");
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
		$where = "CAST(updated_timestamp AS DATE) BETWEEN '$start_date' AND '$end_date' AND status IN ('TRANSFERRING')";

		$pending_count = $this->vouchers_model->get_member_account_vouchers($where);

		if (empty($pending_count))
		{
			$return_html = "<span>No pending requests from <strong>{$proper_start_date}</strong> to <strong>{$proper_end_date}</strong>.</span>";
			$this->return_json("ok",$return_html);
			return;
		} else
		{
			$return_html = "<span>You are to generate Transfer Vouchers Transactions from <strong>{$proper_start_date}</strong> to <strong>{$proper_end_date}</strong>.<br/>Do you want to proceed?</span>";
			$this->return_json("ok",$return_html);
			return;
		}
	}

	public function download_proceed()
	{
		$start_date = trim($this->input->post("start_date"));
		$end_date = trim($this->input->post("end_date"));
		
		$current_timestamp = date('Y-m-d H:i:s');

		//// update all affected records
		//$data = array('status'=>'PREPROCESS', 'user_id' => $this->user->user_id,'update_timestamp'=>$current_timestamp);
		//$where = "status = 'PENDING' AND CAST(insert_timestamp AS DATE) BETWEEN '$start_date' AND '$end_date'";

		//$this->payment_model->update_member_payout($data, $where);

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

			//$where = "CAST(updated_timestamp AS DATE) BETWEEN '$start_date' AND '$end_date' AND status IN ('TRANSFERRING')";
			$where = "CAST(updated_timestamp AS DATE) BETWEEN '$start_date' AND '$end_date'";
			$member_voucher_count = $this->vouchers_model->get_member_account_vouchers_count($where);

			$filename = "transfer_vouchers_" . str_replace("-", "", $start_date) . "_to_" . str_replace("-", "", $end_date) . ".xls";

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
			$worksheet->getStyle('I' . $start_column_num)->getFont()->setBold(true);
			$worksheet->getStyle('J' . $start_column_num)->getFont()->setBold(true);
			$worksheet->getStyle('K' . $start_column_num)->getFont()->setBold(true);
			$worksheet->getStyle('L' . $start_column_num)->getFont()->setBold(true);
			$worksheet->getStyle('M' . $start_column_num)->getFont()->setBold(true);
			
			//center column names
			$worksheet->getStyle('A' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$worksheet->getStyle('B' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$worksheet->getStyle('C' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$worksheet->getStyle('D' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$worksheet->getStyle('E' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$worksheet->getStyle('F' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$worksheet->getStyle('G' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$worksheet->getStyle('H' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$worksheet->getStyle('I' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$worksheet->getStyle('J' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$worksheet->getStyle('K' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$worksheet->getStyle('L' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$worksheet->getStyle('M' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			
			//set column names
			$worksheet->setCellValue('A1', "Transfer Vouchers from {$start_date} to {$end_date}");
			$worksheet->setCellValue('A' . $start_column_num, 'Status');
			$worksheet->setCellValue('B' . $start_column_num, 'Voucher ID');
			$worksheet->setCellValue('C' . $start_column_num, 'Member ID');
			$worksheet->setCellValue('D' . $start_column_num, 'Member Last Name');
			$worksheet->setCellValue('E' . $start_column_num, 'Member First Name');
			$worksheet->setCellValue('F' . $start_column_num, 'Member Middle Name');
			$worksheet->setCellValue('G' . $start_column_num, 'To Member ID');
			$worksheet->setCellValue('H' . $start_column_num, 'To Member Last Name');
			$worksheet->setCellValue('I' . $start_column_num, 'To Member First Name');
			$worksheet->setCellValue('J' . $start_column_num, 'To Member Middle Name');
			$worksheet->setCellValue('K' . $start_column_num, 'Voucher Type');
			$worksheet->setCellValue('L' . $start_column_num, 'Date Created');
			$worksheet->setCellValue('M' . $start_column_num, 'Date Completed');
			

			$row = 4;

			$allowed_rows = 5000;

			for($prow = 0;$prow < ceil($member_voucher_count/$allowed_rows)+1;$prow++)
			{
				$member_vouchers = $this->vouchers_model->get_member_account_vouchers($where, array('rows' => $allowed_rows, 'offset' => $prow*$allowed_rows), 'insert_timestamp ASC');

				foreach ($member_vouchers as $mp)
				{

					// get member details
					$from_member_details = $this->members_model->get_member_by_id($mp->member_id);
					
					$to_member_details = $this->members_model->get_member_by_id($mp->to_member_id);

					$pretty_id = str_pad($mp->voucher_id, 8, "0", STR_PAD_LEFT);
					
					// align right
					$worksheet->getStyle('B' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
					$worksheet->getStyle('G' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
					
					$worksheet->setCellValue('A'. $row, $mp->status);
					$worksheet->setCellValueExplicit('B'. $row, $pretty_id, PHPExcel_Cell_DataType::TYPE_STRING);
					$worksheet->setCellValue('C'. $row, $mp->member_id);
					$worksheet->setCellValue('D'. $row, $from_member_details->last_name);
					$worksheet->setCellValue('E'. $row, $from_member_details->first_name);
					$worksheet->setCellValue('F'. $row, $from_member_details->middle_name);
					$worksheet->setCellValue('G'. $row, $mp->to_member_id);
					$worksheet->setCellValue('H'. $row, $to_member_details->last_name);
					$worksheet->setCellValue('I'. $row, $to_member_details->first_name);
					$worksheet->setCellValue('J'. $row, $to_member_details->middle_name);
					$worksheet->setCellValue('K'. $row, $mp->voucher_type_id);
					$worksheet->setCellValue('L'. $row, $mp->insert_timestamp);
					$worksheet->setCellValue('M'. $row, $mp->updated_timestamp);
					
					
					
					// auto resize columns
					$worksheet->getColumnDimension('A')->setAutoSize(false);
					$worksheet->getColumnDimension('B')->setAutoSize(true);
					$worksheet->getColumnDimension('C')->setAutoSize(true);
					$worksheet->getColumnDimension('D')->setAutoSize(true);
					$worksheet->getColumnDimension('E')->setAutoSize(true);
					$worksheet->getColumnDimension('F')->setAutoSize(true);
					$worksheet->getColumnDimension('G')->setAutoSize(true);
					$worksheet->getColumnDimension('H')->setAutoSize(true);
					$worksheet->getColumnDimension('I')->setAutoSize(true);
					$worksheet->getColumnDimension('J')->setAutoSize(true);
					$worksheet->getColumnDimension('K')->setAutoSize(true);
					$worksheet->getColumnDimension('L')->setAutoSize(true);
					$worksheet->getColumnDimension('M')->setAutoSize(true);
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

}