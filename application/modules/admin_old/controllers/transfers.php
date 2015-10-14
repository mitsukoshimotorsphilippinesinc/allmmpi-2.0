<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Transfers extends Systems_Controller {

	function __construct()
	{
		parent::__construct();

		$this->load->model('payment_model');
		$this->load->model('members_model');
		$this->load->model('tracking_model');
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

		$from_dt = $this->input->get("from_dt");
		$to_dt = $this->input->get("to_dt");
		$status = trim($this->input->get("status"));
		
		$search_url = "";
		$count_is = 0;
		$transfers = "";
		$additional_where  = "";
		$where_arr = array();

		if ($status == '')
			$status = 'ALL';
				
		if ($status != 'ALL')
			$where_arr[] = "status = '{$status}' ";

		if ($from_dt != '' && $to_dt != '')
			$where_arr[] = "(DATE(insert_timestamp) BETWEEN '{$from_dt}' AND '{$to_dt}') ";
		
		if(count($where_arr))
		{
			$additional_where = implode(' AND ',$where_arr);
		}

		if (($search_text == "") || empty($search_text)) {
			$where = $additional_where;
		}	

		if (($search_text == "") || empty($search_text)) {
			
			$count_is = $this->tracking_model->get_member_transfers_count($where);
				
		} else {
				
			if (($search_by == "last_name") || ($search_by == "first_name")) {
				if($additional_where)
					$additional_where = " AND {$additional_where} ";
				
				$sql = "select count(*) as cnt from (select * from tr_member_transfers where from_member_id in 
						(select member_id from cm_members where lower(first_name) like lower('%{$search_text}%')) {$additional_where}
						UNION
						select * from tr_member_transfers where to_member_id in 
						(select member_id from cm_members where lower(last_name) like lower('%{$search_text}%')) {$additional_where}) a
					";
			
				$query = $this->db->query($sql);				
				$total_results = $query->first_row();											
				$count_is = $total_results->cnt;

			} else {

				if($additional_where)
					$additional_where = " AND {$additional_where} ";
				
				if ($search_by == "member_transfer_id") {
					
					$search_text = ltrim($search_text, "0");
					
					$where = "{$search_by} = '{$search_text}' {$additional_where}";

				} else {				
					$where = "{$search_by} LIKE LOWER('%{$search_text}%') {$additional_where}";
				}								

				$count_is = $this->tracking_model->get_member_transfers_count($where);
				
			}
			
			$search_url = "?search_option=" . $search_by . "&status=" . $status . "&from_dt=" . $from_dt . "&to_dt=" . $to_dt;
			
		}

		// initialize pagination class
		// set pagination data
		$config = array(
				'pagination_url' => "/admin/transfers/index/",
				'total_items' => $count_is,
				'per_page' => 10,
				'uri_segment' => 4,
		);

		$this->pager->set_config($config);

		if (($search_text == "") || empty($search_text)) {
			$transfers = $this->tracking_model->get_member_transfers($where, array('rows' => $this->pager->per_page, 'offset' => $this->pager->offset), "member_transfer_id DESC");	
		
		} else {

			if (($search_by == "last_name") || ($search_by == "first_name")) {

				$limit_val = 10;
				$offset_val = 10 * $this->pager->offset;

				//if($additional_where)
				//	$additional_where = " AND {$additional_where} ";

				$sql = "select * from tr_member_transfers where from_member_id in 
					(select member_id from cm_members where lower({$search_by}) like lower('%{$search_text}%')) {$additional_where}
					UNION
					select * from tr_member_transfers where to_member_id in 
					(select member_id from cm_members where lower({$search_by}) like lower('%{$search_text}%')) {$additional_where}
					ORDER BY member_transfer_id DESC
					LIMIT {$limit_val} OFFSET {$offset_val}
				";

				$query = $this->db->query($sql);
				$result = $query->result();
				$query->free_result();

				$transfers = $result;

			} else {

				/*if($additional_where)
					$additional_where = " AND {$additional_where} ";	

				if ($search_by == "member_transfer_id") {
					
					$search_text = ltrim($search_text, "0");
					
					$where = "{$search_by} = '{$search_text}' {$additional_where}";
				} else {				
					$where = "{$search_by} LIKE LOWER('%{$search_text}%') {$additional_where}";
				}
				*/

				$transfers = $this->tracking_model->get_member_transfers($where, array('rows' => $this->pager->per_page, 'offset' => $this->pager->offset), "member_transfer_id DESC");	

			}			
		}

		foreach ($transfers as $k => $p)
		{
			$member = $this->members_model->get_member_by_id($p->from_member_id);

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

		$this->template->from_dt = $from_dt;
		$this->template->to_dt = $to_dt;
		$this->template->status = $status;
		
		$this->template->view('transfer_funds/dashboard');
	}

	public function resend_code()
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
			

			$proper_member_transfer_id = str_pad($member_transfer_id, 8, "0", STR_PAD_LEFT);
			
			// send email notification to member who requested
			$params = array(
				"first_name"=>ucfirst($from_member_details->first_name),
				"proper_transaction_id"=>$proper_member_transfer_id,
				"proper_to_member_name"=>$proper_name_to_member,
				"proper_amount"=>$transfer_amount,
				"proper_transfer_type"=>strtoupper($transfer->type),
				"proper_member_transfer_id"=>$proper_member_transfer_id,
				"confirmation_code"=>$transfer->confirmation_code					
			);

			
			$data = array(
				"email"=>$from_member_details->email,
				"type"=>"resend_code_via_admin_from_member",
				"params"=>$params
			);

			//send email to user
			Modules::run('jobs/notifications/send_email',$data);

		
			// logging after update
			$details_before = array('id' => $transfer->member_transfer_id, 'details' => $params);
			$details_before = json_encode($details_before);
			$update_member_funds_logs = array(
				'user_id' => $this->user->user_id,
				'module_name' => "TRANSFER FUNDS - RESEND CODE",
				'table_name' => "cm_member_transfers",
				'action' => "RESEND CODE",
				'details_before' => $details_before,
				'details_after' => $details_before
			);
			$this->tracking_model->insert_logs('admin', $update_member_funds_logs);
			
			$html = "The Confirmation Code was sent successfully via email to <strong>{$proper_name_from_member}</strong>. <br/>Transaction ID : <strong>{$proper_member_transfer_id}</strong>";		
			$this->return_json("1","Resend Transfer Code",array("html" => $html));
		}
		
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
	
	public function confirm_cancel() {
	
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
				
			$proper_member_transfer_id = str_pad($member_transfer_id, 8, "0", STR_PAD_LEFT);
			
			$html =	"<h4>Transaction ID: {$proper_member_transfer_id}</h4>
			<p>Are you sure you want to cancel this transaction with an amount of <strong>Php{$transfer_amount}</strong> via <strong>{$transfer->type}</strong> from <strong>{$proper_name_from_member}</strong> to <strong>{$proper_name_to_member}</strong>?
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
	
		$member_transfer_id = $this->input->post("member_transfer_id");
		$remarks = $this->input->post("remarks");
				
		$transfer = $this->tracking_model->get_member_transfer_by_id($member_transfer_id);
	
		if (empty($transfer)) {
		
			$html = "<p>There is something wrong with this transaction [Transfer ID: {$member_transfer_id}].</p>";
			$this->return_json("0","Empty Transfer info",array("html" => $html));
			
		} else {
	
			// change status to CANCELLED
			$current_datetime = date("Y-m-d H:i:s");
			
			
			// add to transfer logs
			//get details before updating
			$member_transfer_details_before = array(
				"member_transfer_id"=>$member_transfer_id, 
				"status"=>$transfer->status, 
				"from_member_id"=>$transfer->from_member_id, 
				"to_member_id"=>$transfer->to_member_id, 
				"amount"=>$transfer->amount, 
				"type"=>$transfer->type,
				"update_timestamp"=>$transfer->update_timestamp
			);
			
			$details_before = array('id' => $member_transfer_id, 'details' => $member_transfer_details_before);
			$details_before = json_encode($details_before);
			
			
			// change the status in tr_member_transfers to CANCELLED			
			$data = array(
				'status' => 'CANCELLED',
				'update_timestamp' => $current_datetime
			);
			
			$where = "member_transfer_id = " . $member_transfer_id;
			$this->tracking_model->update_member_transfers($data, $where);
			
			
			// logging after update
			$details_after = array('id' => $transfer->to_member_id, 'details' => $data);
			$details_after = json_encode($details_after);
			$update_member_funds_logs = array(
				'user_id' => $this->user->user_id,
				'module_name' => "CANCEL - TRANSFER COMMISSION (" . $transfer->type . ")",
				'table_name' => "cm_member_transfers",
				'action' => "CANCEL",
				'details_before' => $details_before,
				'details_after' => $details_after,
				'remarks' => "$remarks"
			);
			$this->tracking_model->insert_logs('admin', $update_member_funds_logs);
			
			
			// add to member logs - change in funds cm_members
			//get details before updating
			$from_member_details = $this->members_model->get_member_by_id($transfer->from_member_id);
			
			if ($transfer->type == "FUNDS") {
				$from_member_details_before = array("funds"=>$from_member_details->funds);
			} else if ($transfer->type == "GCEP") {
				$from_member_details_before = array("gcep"=>$from_member_details->gcep);
			} else {
				$from_member_details_before = array("gift_cheques"=>$from_member_details->gift_cheques);
			}
			
			$details_before = array(
				'id' => $transfer->from_member_id, 
				'details' => $from_member_details_before
			);
			$details_before = json_encode($details_before);
			
			// credit to member =  funds + amount
			if ($transfer->type == "FUNDS") {
				$data = array(
					"funds" => $from_member_details->funds + $transfer->amount
				);
			
			} else if ($transfer->type == "GCEP") {
				$data = array(
					"gcep" => $from_member_details->gcep + $transfer->amount
				);
			} else {
				$data = array(
					"gift_cheques" => $from_member_details->gift_cheques + $transfer->amount
				);
			}
			
			$this->members_model->update_member($data, "member_id = " . $transfer->from_member_id);
			
			// logging after update
			$details_after = array('id' => $transfer->from_member_id, 'details' => $data);
			$details_after = json_encode($details_after);
			$update_member_funds_logs = array(
				'user_id' => $this->user->user_id,
				'module_name' => "CANCEL - TRANSFER COMMISSION (" . $transfer->type . ")",
				'table_name' => "cm_members",
				'action' => "CANCEL",
				'details_before' => $details_before,
				'details_after' => $details_after,
				'remarks' => "$remarks"
			);
			$this->tracking_model->insert_logs('admin', $update_member_funds_logs);
			
			
			//// add to transfer logs
			////get details before updating
			//$member_transfer_details_before = array(
			//	"status"=>$transfer->status, 
			//	"from_member_id"=>$transfer->from_member_id, 
			//	"to_member_id"=>$transfer->to_member_id, 
			//	"amount"=>$transfer->amount, 
			//	"type"=>$transfer->type,
			//	"update_timestamp"=>$transfer->update_timestamp
			//);
			//
			//$details_before = array('id' => $member_transfer_id, 'details' => $member_transfer_details_before);
			//$details_before = json_encode($details_before);
			//
			//
			//$data = array(
			//	'status' => 'COMPLETED',
			//	'update_timestamp' => $current_datetime
			//);
			//
			//$where = "member_transfer_id = " . $member_transfer_id;
			//$this->tracking_model->update_member_transfers($data, $where);
		    //
		    //
			//// logging after update
			//$details_after = array('id' => $member_transfer_id, 'details' => $data);
			//$details_after = json_encode($details_after);
			//$update_member_funds_logs = array(
			//	'user_id' => $this->user->user_id,
			//	'module_name' => "UPDATE MEMBER TRANSFERS - TRANSFER FUNDS",
			//	'table_name' => "cm_member_transfers",
			//	'action' => "UPDATE",
			//	'details_before' => $details_before,
			//	'details_after' => $details_after,
			//	'remarks' => ""
			//);
			//$this->tracking_model->insert_logs('admin', $update_member_funds_logs);
			
			$transfer_amount = number_format($transfer->amount , 2);
						
			// get from_member details			
			$to_member_details = $this->members_model->get_member_by_id($transfer->to_member_id); 
			
			$proper_name_from_member = ucfirst($from_member_details->first_name) . " " . ucfirst($from_member_details->middle_name) . " " . ucfirst($from_member_details->last_name);
			
			$proper_name_to_member = ucfirst($to_member_details->first_name) . " " . ucfirst($to_member_details->middle_name) . " " . ucfirst($to_member_details->last_name);
			
			$proper_member_transfer_id = str_pad($member_transfer_id, 8, "0", STR_PAD_LEFT);			
			
			$html = "You have successfully cancelled the Transfer Funds <strong>Transaction ID: {$proper_member_transfer_id}</strong>.";
			
			// SEND EMAILS
			// send email notification to member who requested
			$params = array(
				"first_name"=>ucfirst($from_member_details->first_name),
				"proper_to_member_name"=>$proper_name_to_member,
				"proper_amount"=>$transfer_amount,
				"proper_transfer_type"=>strtoupper($transfer->type),
				"proper_member_transfer_id"=>$proper_member_transfer_id,	
				"remarks"=>$remarks,					
			);

			
			$data = array(
				"email"=>$from_member_details->email,
				"type"=>"cancel_transfer_funds_via_admin_from_member",
				"params"=>$params
			);

			//send email to user
			Modules::run('jobs/notifications/send_email',$data);
			
			
			// send email notification to recipient
			$params = array(
				"first_name"=>ucfirst($to_member_details->first_name),
				"proper_from_member_name"=>$proper_name_from_member,
				"proper_amount"=>$transfer_amount,
				"proper_transfer_type"=>strtoupper($transfer->type),
				"proper_member_transfer_id"=>$proper_member_transfer_id,
				"remarks"=>$remarks			
			);

			$data = array(
				"email"=>$to_member_details->email,
				"type"=>"cancel_transfer_funds_via_admin_to_member",
				"params"=>$params
			);

			//send email to user
			Modules::run('jobs/notifications/send_email',$data);
			
			
			$this->return_json("1","Successful Cancel Transfer Funds",array("html" => $html));
		
		}
		
		return;
		
	}
	
	
	public function confirm_transfer() {
	
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
				
			$html =	"<p>You are to transfer an amount of <strong>Php{$transfer_amount}</strong> via <strong>{$transfer->type}</strong> from <strong>{$proper_name_from_member}</strong> to <strong>{$proper_name_to_member}</strong>.<br/><br/>Do you want to proceed?
			</p>					
			";
			
			$this->return_json("1","Confirmation before transferring amount to member",array("html" => $html));
		}
		
		return;
	
	}
	
	public function proceed_transfer() {
	
		$member_transfer_id = $this->input->post("member_transfer_id");
				
		$transfer = $this->tracking_model->get_member_transfer_by_id($member_transfer_id);
	
		if (empty($transfer)) {
		
			$html = "<p>There is something wrong with this transaction [Transfer ID: {$member_transfer_id}].</p>";
			$this->return_json("0","Empty Transfer info",array("html" => $html));
			
		} else {
	
			// change status to COMPLETED
			$current_datetime = date("Y-m-d H:i:s");
			
			
			// add to transfer logs
			//get details before updating
			$member_transfer_details_before = array(
				"status"=>$transfer->status, 
				"from_member_id"=>$transfer->from_member_id, 
				"to_member_id"=>$transfer->to_member_id, 
				"amount"=>$transfer->amount, 
				"type"=>$transfer->type,
				"update_timestamp"=>$transfer->update_timestamp
			);
			
			$details_before = array('id' => $member_transfer_id, 'details' => $member_transfer_details_before);
			$details_before = json_encode($details_before);
			
			
			// change the status in tr_member_transfers to PROCESSING			
			$data = array(
				'status' => 'PROCESSING',
				'update_timestamp' => $current_datetime
			);
			
			$where = "member_transfer_id = " . $member_transfer_id;
			$this->tracking_model->update_member_transfers($data, $where);
			
			
			// logging after update
			$details_after = array('id' => $transfer->to_member_id, 'details' => $data);
			$details_after = json_encode($details_after);
			$update_member_funds_logs = array(
				'user_id' => $this->user->user_id,
				'module_name' => "UPDATE " . $transfer->type . " - TRANSFER COMMISSIONS",
				'table_name' => "cm_members",
				'action' => "UPDATE",
				'details_before' => $details_before,
				'details_after' => $details_after,
				'remarks' => ""
			);
			$this->tracking_model->insert_logs('admin', $update_member_funds_logs);
			
			
			// add to member logs - change in funds cm_members
			//get details before updating
			$to_member_details = $this->members_model->get_member_by_id($transfer->to_member_id);			
			$to_member_details_before = array(
				"funds"=>$to_member_details->funds,
				"gift_cheques"=>$to_member_details->gift_cheques,
				"gcep"=>$to_member_details->gcep,
			);
			
			$details_before = array(
				'id' => $transfer->to_member_id, 
				'details' => $to_member_details_before
			);
			$details_before = json_encode($details_before);
			
			// credit to member =  funds + amount			
			if ($transfer->type == 'FUNDS') {
				$data = array(					
					"funds" => $to_member_details->funds + $transfer->amount
				);
			} else if ($transfer->type == 'GCEP') {
				$data = array(					
					"gcep" => $to_member_details->gcep + $transfer->amount
				);
			} else {
				$data = array(					
					"gift_cheques" => $to_member_details->gift_cheques + $transfer->amount
				);
			}
			
			
			$this->members_model->update_member($data, "member_id = " . $transfer->to_member_id);
			
			// logging after update
			$details_after = array('id' => $transfer->to_member_id, 'details' => $data);
			$details_after = json_encode($details_after);
			$update_member_funds_logs = array(
				'user_id' => $this->user->user_id,
				'module_name' => "UPDATE " . $transfer->type . " - TRANSFER COMMISSIONS",
				'table_name' => "cm_members",
				'action' => "UPDATE",
				'details_before' => $details_before,
				'details_after' => $details_after,
				'remarks' => ""
			);
			$this->tracking_model->insert_logs('admin', $update_member_funds_logs);
			
			
			// add to transfer logs
			//get details before updating
			$member_transfer_details_before = array(
				"status"=>$transfer->status, 
				"from_member_id"=>$transfer->from_member_id, 
				"to_member_id"=>$transfer->to_member_id, 
				"amount"=>$transfer->amount, 
				"type"=>$transfer->type,
				"update_timestamp"=>$transfer->update_timestamp
			);
			
			$details_before = array('id' => $member_transfer_id, 'details' => $member_transfer_details_before);
			$details_before = json_encode($details_before);
			
			
			$data = array(
				'status' => 'COMPLETED',
				'update_timestamp' => $current_datetime
			);
			
			$where = "member_transfer_id = " . $member_transfer_id;
			$this->tracking_model->update_member_transfers($data, $where);
		
		
			// logging after update
			$details_after = array('id' => $member_transfer_id, 'details' => $data);
			$details_after = json_encode($details_after);
			$update_member_funds_logs = array(
				'user_id' => $this->user->user_id,
				'module_name' => "UPDATE MEMBER TRANSFERS - TRANSFER " . $transfer->type,
				'table_name' => "cm_member_transfers",
				'action' => "UPDATE",
				'details_before' => $details_before,
				'details_after' => $details_after,
				'remarks' => ""
			);
			$this->tracking_model->insert_logs('admin', $update_member_funds_logs);
			
			$transfer_amount = number_format($transfer->amount , 2);
			
			
			// get from_member details			
			$from_member_details = $this->members_model->get_member_by_id($transfer->from_member_id); 
			
			$proper_name_from_member = ucfirst($from_member_details->first_name) . " " . ucfirst($from_member_details->middle_name) . " " . ucfirst($from_member_details->last_name);
			
			$proper_name_to_member = ucfirst($to_member_details->first_name) . " " . ucfirst($to_member_details->middle_name) . " " . ucfirst($to_member_details->last_name);
			
			
			$proper_member_transfer_id = str_pad($member_transfer_id, 8, "0", STR_PAD_LEFT);
			
			$html = "Congratulations! An amount of <strong>Php{$transfer_amount}</strong> from <strong>{$proper_name_from_member}</strong> was successfully transferred to <strong>{$proper_name_to_member}'s {$transfer->type}</strong>.";
			
			// SEND EMAILS
			// send email notification to member who requested
			$params = array(
				"first_name"=>ucfirst($from_member_details->first_name),
				"proper_amount"=>$transfer_amount,
				"proper_recipient_name"=>$proper_name_to_member,
				"proper_transaction_id"=>$proper_member_transfer_id,
				"proper_transfer_type"=>$transfer->type						
			);

			$data = array(
				"email"=>$from_member_details->email,
				"type"=>"transfer_funds_request_receipt_via_admin",
				"params"=>$params
			);

			//send email to user
			Modules::run('jobs/notifications/send_email',$data);
			
			
			// send email notification to recipient
			$params = array(
				"first_name"=>ucfirst($to_member_details->first_name),
				"proper_amount"=>$transfer_amount,
				"proper_recipient_name"=>$proper_name_from_member,
				"proper_transaction_id"=>$proper_member_transfer_id,
				"proper_transfer_type"=>$transfer->type				
			);

			$data = array(
				"email"=>$to_member_details->email,
				"type"=>"transfer_funds_acceptance_receipt_via_admin",
				"params"=>$params
			);

			//send email to user
			Modules::run('jobs/notifications/send_email',$data);
			
			
			$this->return_json("1","Update Commissions",array("html" => $html));
		
		}
		
		return;
		
	}
	
	public function execute()
	{
		$_transaction_id = $this->input->post("_transaction_id");

		// update cm_member_payouts, set status to COMPLETED
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
		$status = $this->input->post("status");

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

		$status_where = "";
		if($status != "ALL")
			$status_where = " AND status='$status'";

		// check if query will return records to execute
		$where = "CAST(insert_timestamp AS DATE) BETWEEN '$start_date' AND '$end_date' $status_where";

		$pending_count = $this->tracking_model->get_member_transfers($where);

		if (empty($pending_count))
		{
			$return_html = "<span>No pending requests from <strong>{$proper_start_date}</strong> to <strong>{$proper_end_date}</strong>.</span>";
			$this->return_json("ok",$return_html);
			return;
		} else
		{
			$return_html = "<span>You are to generate Transfer Funds Transactions from <strong>{$proper_start_date}</strong> to <strong>{$proper_end_date}</strong>.<br/>Do you want to proceed?</span>";
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

	function export_xls($start_date,$end_date,$status)
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

			$status_where = "";
			if($status != "ALL")
				$status_where = " AND status='$status'";

			$where = "CAST(insert_timestamp AS DATE) BETWEEN '$start_date' AND '$end_date' $status_where";
			$member_payout_count = $this->tracking_model->get_member_transfers_count($where);

			$filename = "transfer_funds_" . str_replace("-", "", $start_date) . "_to_" . str_replace("-", "", $end_date) . ".xls";

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
			$worksheet->getStyle('N' . $start_column_num)->getFont()->setBold(true);
			

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
			$worksheet->getStyle('N' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			
			//set column names
			$worksheet->setCellValue('A1', "Transfer Funds from {$start_date} to {$end_date}");
			$worksheet->setCellValue('A' . $start_column_num, 'Status');
			$worksheet->setCellValue('B' . $start_column_num, 'Transaction ID');
			$worksheet->setCellValue('C' . $start_column_num, 'From Member ID');
			$worksheet->setCellValue('D' . $start_column_num, 'From Member Last Name');
			$worksheet->setCellValue('E' . $start_column_num, 'From Member First Name');
			$worksheet->setCellValue('F' . $start_column_num, 'From Member Middle Name');
			$worksheet->setCellValue('G' . $start_column_num, 'To Member ID');
			$worksheet->setCellValue('H' . $start_column_num, 'To Member Last Name');
			$worksheet->setCellValue('I' . $start_column_num, 'To Member First Name');
			$worksheet->setCellValue('J' . $start_column_num, 'To Member Middle Name');
			$worksheet->setCellValue('K' . $start_column_num, 'Type');
			$worksheet->setCellValue('L' . $start_column_num, 'Amount');
			$worksheet->setCellValue('M' . $start_column_num, 'Date Initiated');
			$worksheet->setCellValue('N' . $start_column_num, 'Date Completed');
			

			$row = 4;

			$allowed_rows = 5000;

			for($prow = 0;$prow < ceil($member_payout_count/$allowed_rows)+1;$prow++)
			{
				$member_payouts = $this->tracking_model->get_member_transfers($where, array('rows' => $allowed_rows, 'offset' => $prow*$allowed_rows), 'insert_timestamp ASC');

				foreach ($member_payouts as $mp)
				{

					// get member details
					$from_member_details = $this->members_model->get_member_by_id($mp->from_member_id);
					
					$to_member_details = $this->members_model->get_member_by_id($mp->to_member_id);

					$pretty_amount = number_format($mp->amount, 2, '.', ',');
					
					$pretty_id = str_pad($mp->member_transfer_id, 8, "0", STR_PAD_LEFT);
					
					// align right
					$worksheet->getStyle('B' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
					$worksheet->getStyle('G' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
					
					$worksheet->setCellValue('A'. $row, $mp->status);
					$worksheet->setCellValueExplicit('B'. $row, $pretty_id, PHPExcel_Cell_DataType::TYPE_STRING);
					$worksheet->setCellValue('C'. $row, $mp->from_member_id);
					$worksheet->setCellValue('D'. $row, $from_member_details->last_name);
					$worksheet->setCellValue('E'. $row, $from_member_details->first_name);
					$worksheet->setCellValue('F'. $row, $from_member_details->middle_name);
					$worksheet->setCellValue('G'. $row, $mp->to_member_id);
					$worksheet->setCellValue('H'. $row, $to_member_details->last_name);
					$worksheet->setCellValue('I'. $row, $to_member_details->first_name);
					$worksheet->setCellValue('J'. $row, $to_member_details->middle_name);
					$worksheet->setCellValue('K'. $row, $mp->type);
					$worksheet->setCellValueExplicit('L'. $row, $pretty_amount, PHPExcel_Cell_DataType::TYPE_STRING);
					$worksheet->setCellValue('M'. $row, $mp->insert_timestamp);
					$worksheet->setCellValue('N'. $row, $mp->update_timestamp);
					
					
					
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
					$worksheet->getColumnDimension('N')->setAutoSize(true);
					$row++;
				}
			}

			//$member_payouts = $this->payment_model->get_member_payouts($where, null, 'insert_timestamp DESC');

			

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