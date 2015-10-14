<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mytransfers extends Site_Controller {
	
	function __construct() {
		parent::__construct();		
		
		$this->load->model('facilities_model');
		$this->load->model('members_model');
		$this->load->model('tracking_model');
		$this->load->model('settings_model');
		
		
		// load pager library
		$this->load->library('pager');
	}
	
	public function index()
	{
	
		// get settings minimum_allowable transfer
		$minimum_transfer_amount_details = $this->settings_model->get_setting_by_slug("minimum_allowable_transfer");
	
		if (empty($minimum_transfer_amount_details)) {
			$minimum_transfer_amount = 1;
		} else {
			$minimum_transfer_amount = $minimum_transfer_amount_details->value;
		}
		
		// check max amount
		$maximum_transfer_amount_details = $this->settings_model->get_setting_by_slug("maximum_allowable_transfer");
	
		if (empty($maximum_transfer_amount_details)) {
			// set maximum = member's max funds
			$maximum_transfer_amount = $this->member->funds;
		} else {		
			$maximum_transfer_amount = $maximum_transfer_amount_details->value;
			
			if ($maximum_transfer_amount == 0) {
				// set maximum = member's max funds
				$maximum_transfer_amount = $this->member->funds;
			}
		}
		
		$min_max_placeholder = number_format($minimum_transfer_amount, 2, ".", ",") ." - ". number_format($maximum_transfer_amount, 2, ".", ",");
		
		
		// get all pending transactions where member is involved
		$where = "(from_member_id = " . $this->member->member_id . " OR to_member_id = " . $this->member->member_id .") AND status = 'PENDING'";
		
		$limit_rows_details = $this->settings_model->get_setting_by_slug('transfers_display_pending_transactions');
		
		if (empty($limit_rows_details) || ($limit_rows_details->value == 0)) {
			$limit_rows = 5;
		} else {
			$limit_rows = $limit_rows_details->value;
		}
		
		$limit_offset = 0;
		$pending_transactions = $this->tracking_model->get_member_transfers($where, array('rows' => $limit_rows, 'offset' => $limit_offset), 'insert_timestamp DESC');
		$total_pending_count = $this->tracking_model->get_member_transfers_count($where);
		
		$this->template->total_pending_count = $total_pending_count;		
		$this->template->min_max_placeholder = $min_max_placeholder;
		$this->template->pending_transactions = $pending_transactions;		
		$this->template->current_page = 'mytransfers';
		
		// get settings if need to disable transfer page
		$disable_transfer_page_details = $this->settings_model->get_setting_by_slug("disable_transfer_page");
		
		if ($disable_transfer_page_details->value == 1) {
			$this->template->title_page = 'Transfer Commissions';
			$this->template->view('main/dashboard_disabled');					
		} else {
			$setting_details = $this->settings_model->get_setting_by_slug("disable_corpo_transfer_funds");
			
			if (abs($setting_details->value) == 1) {		
			
			//if (abs($settings['disable_corpo_transfer_funds']) == 1) {		
				if ($this->member->is_paycard_corpo == 1) {
					$this->template->view('members/transfers/corpo_not_allowed');		
				} else {
					$this->template->view('members/transfers/dashboard');
					//$this->template->view('members/transfers/dashboard_on_going');
				}
			} else {
				$this->template->view('members/transfers/dashboard');
				//$this->template->view('members/transfers/dashboard_on_going');
			}		
		}	
	}
	
	
	public function check_account(){
  	
   		$_to_account_id = trim($this->input->post('_to_account_id'));
		$from_submit = trim($this->input->post('from_submit'));
  
		if($_to_account_id == "")
		{   
			$html = "Account ID is required";
    		$this->return_json("0","Account ID is required",array("html" => $html));
			return;
		} 
		
		$to_account_id_details = $this->members_model->get_member_account_by_account_id($_to_account_id);
		
		
		if (empty($to_account_id_details))
		{
			$html = "Invalid Account ID";
    		$this->return_json("0","Invalid Account ID ",array("html" => $html));
			return;
		} 
		
		// check if account_id is owned by the current member
		if ($to_account_id_details->member_id == $this->member->member_id) {
			$html = "Transfer to own account is not allowed.";
    		$this->return_json("0","Same owner",array("html" => $html));
			return; 
		} 
				
				
		// get details of account
		$to_member_details = $this->members_model->get_member_by_id($to_account_id_details->member_id);
		
		// Check Account if ON-HOLD
		if ($to_member_details->is_on_hold == 1) {
			$html = "Member is currently On-Hold.";
    		$this->return_json("0","On-hold",array("html" => $html));
			return; 
		} 
		
		$proper_name_to_member = $to_member_details->last_name . ", " . $to_member_details->first_name . " " . $to_member_details->middle_name; 
		
		$image_filename = "male.jpg";
		if (empty($to_member_details->image_filename) || ($to_member_details->image_filename == NULL) || (trim($to_member_details->image_filename) == "")) {
			// check gender of member
			if (trim($to_member_details->sex) == "F") {
				$image_filename = "female.jpg";
			} else {
				$image_filename = "male.jpg";
			}
		} else {
			$image_filename = $to_member_details->image_filename;
		}
	
		// get all account_ids
		$all_account_ids = $this->members_model->get_member_accounts("member_id = ". $to_member_details->member_id);
		
		$ctr = 0;
		$all_accounts = "";
		
		foreach($all_account_ids as $aci):
			
			if ($ctr == 0) {
				$all_accounts .= $aci->account_id; 	
			} else {
				$all_accounts .=  ", ". $aci->account_id; 	
			}
			$ctr++;
					
		endforeach;
		
		$html = "<div class='row-fluid'>
					<div class='span4'>
						<div class='img-wrap'><img id='member_image' style='width:150px;height:150px;' alt='' src='/assets/media/members/{$image_filename}'></div>
					</div>				
					<div class='span8'>
						<table class='table table-striped table-bordered'>
							<thead>
							</thead>
							<tbody>
								<tr>
									<td style='width:80px;'><label><strong>Account ID:</strong></label></td>
									<td><label class=''>{$all_accounts}</label></td>		
								</tr>
								<tr>
									<td style='width:80px;'><label><strong>Name</strong></label></td>
									<td><label class=''>{$proper_name_to_member}</label></td>		
								</tr>
								<tr>
									<td style='width:80px;'><label><strong>Group Name</strong></label></td>
									<td><label class=''>{$to_member_details->group_name}</label></td>
								</tr>						
							</tbody>
						</table>	
					</div>
				</div>";
			
		$is_valid = 1;
				
		$this->return_json("1","Valid Account ID ",array("html" => $html,"valid_sponsor"=>$is_valid, "proper_name"=>$proper_name_to_member));
		
		return;	
		
	}
	
	
	public function check_details() {		
		$to_account_id = $this->input->get_post('_to_account_id');
		$amount = $this->input->get_post('_amount');
		$transfer_type = $this->input->get_post('_transfer_type');
		
		
		if ($this->member->is_email_verified == 0) {
			// error: verify email
			$this->return_json("0","Member has no verified email",array("html" => "<strong>Error: Email not verified.</strong> <br/>This feature requires your email. You must validate your email address before you can proceed. Go to <a href='/members/profile'>Profile</a> tab, then enter and verify your email.", "title" => "Transfer Error :: Unverified Email"));
			return;
		} else {
			if (($this->member->email == NULL) || empty($this->member->email) || (trim($this->member->email) == '')) {
				// error
				$this->return_json("0","Member has no verified email",array("html" => "<strong>Error: Missing Email Address.</strong> <br/>This feature requires your email. You must validate your email address before you can proceed. Go to <a href='/members/profile'>Profile tab</a> tab, then enter and verify your email.", "title" => "Transfer Error :: Unverified Email"));
				return;
			}
		}
		
		// get maximum_transfer_transactions
		$maximum_transfer_details = $this->settings_model->get_setting_by_slug("maximum_transfer_transactions");
		
		if (empty($maximum_transfer_details)) {
			$maximum_transactions = 0;
		} else {
			$maximum_transactions = $maximum_transfer_details->value;
		}
		
		
		$count = $this->tracking_model->get_member_transfers_count("from_member_id = ".$this->member->member_id." AND status = 'PENDING'");
        
		if (($count >= $maximum_transactions) && ($maximum_transactions != 0)) {
			$this->return_json("0","Maximum allowed transactions reached.",array("html" => "Error: You have reached the maximum transactions allowed (The system allows only {$maximum_transactions} PENDING transactions).", "title" => "Transfer Error :: Number of Transactions"));
			return;
		}
		
		// get member_account details
		$to_member_account_details = $this->members_model->get_member_account_by_account_id($to_account_id);
			
		if (empty($to_member_account_details)) {
			$html = "Error: Account Details not found.";
			$this->return_json("0","Account ID Not Found",array("html" => $html, "title" => "Transfer Error :: Account Verification."));
		return;	
		}
		
		// get member details
		$to_member_details = $this->members_model->get_member_by_id($to_member_account_details->member_id);
		
		if (empty($to_member_details)) {
			$this->return_json("0","Member details not found.",array("html" => "Error: Member Details not found.", "title" => "Transfer Error :: Account Verification"));
			return;
		}
	
		
		// check if current member has credits to transfer
		$minimum_allowable_transfer = $this->settings_model->get_setting_by_slug("minimum_allowable_transfer");
		$proper_minimum_value = number_format(($minimum_allowable_transfer->value), 2);	
		
		if ($amount < $minimum_allowable_transfer->value) {
			$this->return_json("0","Invalid Amount",array("html" => "Invalid amount. Amount must be greater than or equal to {$proper_minimum_value}", "title" => "Transfer Error :: Invalid Amount"));		
			return;
		}
		
		$member_credit_type_amount = $this->member->funds; 
		$member_variance_type_amount = $this->member->funds_variance; 
		
		if ($transfer_type == 'GIFT CHEQUES') {
			$member_credit_type_amount = $this->member->gift_cheques; 
			$member_variance_type_amount = $this->member->gift_cheques_variance; 
		}
		
		if ($transfer_type == 'GCEP') {
			$member_credit_type_amount = $this->member->gcep; 
			$member_variance_type_amount = $this->member->gcep_variance; 
		}
		
		if ($member_credit_type_amount < $amount) {
			$proper_member_credit_type_amount = number_format(($member_credit_type_amount), 2);	
			$title_transfer_type = ucwords(strtolower($transfer_type));
			$this->return_json("0","Insufficient {$title_transfer_type}",array("html" => "Insufficient <strong>{$title_transfer_type}</strong> to proceed with the transfer. You only have Php{$proper_member_credit_type_amount} worth of {$title_transfer_type}.", "title" => "Transfer Error :: Insufficient {$title_transfer_type}"));
			return;	
		} 
		
		// 20130716 - simplify funds_variance condition				
		$enable_commission_variance_checking = $this->settings_model->get_setting_by_slug("enable_commission_variance_checking");
		
		if ($enable_commission_variance_checking->value) {
			if ($member_variance_type_amount < 0) {				
				$proper_member_credit_type_amount = number_format(($member_credit_type_amount), 2);	
				$title_transfer_type = ucwords(strtolower($transfer_type));
				$this->return_json("0","Transfer {$title_transfer_type} not allowed.",array("html" => "You are not allowed to proceed with the transfer of <strong>{$title_transfer_type}</strong> - {$title_transfer_type} Variance. Please contact Edwin Sison of the IT Department at 631-1899 or 0917-5439586. You can also send your concerns via email to edwin.sison@vital-c.com.", "title" => "Transfer Error :: {$title_transfer_type} On-Hold"));
				return;			
			}			
		}
		
		// 20130817
		$enable_commission_on_hold_checking = $this->settings_model->get_setting_by_slug("enable_commission_on_hold_checking");
		
		if ($transfer_type == 'FUNDS') {
			if (($member_credit_type_amount - $amount) < abs($this->member->on_hold_funds)) {
				// transaction exceeds on hold funds value
				$proper_member_credit_type_amount = number_format(($member_credit_type_amount), 2);	
				$title_transfer_type = ucwords(strtolower($transfer_type));
				$this->return_json("0","Transfer {$title_transfer_type} not allowed.",array("html" => "You are not allowed to proceed with the transfer of <strong>{$title_transfer_type}</strong> - Will exceed On-Hold {$title_transfer_type}. Please contact Edwin Sison of the IT Department at 631-1899 or 0917-5439586. You can also send your concerns via email to edwin.sison@vital-c.com.", "title" => "Transfer Error :: {$title_transfer_type} On-Hold"));
				return;				
			}
		}
		
		
		if ($amount)
		$this->return_json("1","Proceed with Transfer",array("html" => "Proceed with Transfer"));
		return;			
	}
	
	public function execute_transfer() {
	
        $to_account_id = trim($this->input->post("to_account_id"));
        $amount = abs($this->input->post("amount"));
		$transfer_type = trim($this->input->post("transfer_type"));
        
		// get member_account details
		$to_member_account_details = $this->members_model->get_member_account_by_account_id($to_account_id);
				
		if (empty($to_member_account_details)) {
			$html = "Error: Account Details not found.";
			$this->return_json("0","Account ID Not Found",array("html" => $html));
		return;	
		}
		
		// 20130716 - simplify funds_variance condition				
		$member_credit_type_amount = $this->member->funds; 
		$member_variance_type_amount = $this->member->funds_variance; 
		
		if ($transfer_type == 'GIFT CHEQUES') {
			$member_credit_type_amount = $this->member->gift_cheques; 
			$member_variance_type_amount = $this->member->gift_cheques_variance; 
		}
		
		if ($transfer_type == 'GCEP') {
			$member_credit_type_amount = $this->member->gcep; 
			$member_variance_type_amount = $this->member->gcep_variance; 
		}
		
		$enable_commission_variance_checking = $this->settings_model->get_setting_by_slug("enable_commission_variance_checking");
		
		if ($enable_commission_variance_checking->value) {
			if ($member_variance_type_amount < 0) {				
				$proper_member_credit_type_amount = number_format(($member_credit_type_amount), 2);	
				$title_transfer_type = ucwords(strtolower($transfer_type));
				$this->return_json("0","Transfer {$title_transfer_type} not allowed.",array("html" => "You are not allowed to proceed with the transfer of <strong>{$title_transfer_type}</strong> - {$title_transfer_type} Variance. Please contact Edwin Sison of the IT Department at 631-1899 or 0917-5439586. You can also send your concerns via email to edwin.sison@vital-c.com.", "title" => "Transfer Error :: {$title_transfer_type} On-Hold"));
				return;			
			}
		}
		
		// 20130817
		$enable_commission_on_hold_checking = $this->settings_model->get_setting_by_slug("enable_commission_on_hold_checking");
		
		if ($transfer_type == 'FUNDS') {
			if (($member_credit_type_amount - $amount) < abs($this->member->on_hold_funds)) {
				// transaction exceeds on hold funds value
				$proper_member_credit_type_amount = number_format(($member_credit_type_amount), 2);	
				$title_transfer_type = ucwords(strtolower($transfer_type));
				$this->return_json("0","Transfer {$title_transfer_type} not allowed.",array("html" => "You are not allowed to proceed with the transfer of <strong>{$title_transfer_type}</strong> - Will exceed On-Hold {$title_transfer_type}. Please contact Edwin Sison of the IT Department at 631-1899 or 0917-5439586. You can also send your concerns via email to edwin.sison@vital-c.com.", "title" => "Transfer Error :: {$title_transfer_type} On-Hold"));
				return;				
			}
		}
		
		// get member details
		$to_member_details = $this->members_model->get_member_by_id($to_member_account_details->member_id);
		
		$current_datetime = date("Y-m-d H:i:s");
		// insert request to tr_member_transfers, status as pending
		$confirmation_code = md5($this->member->member_id."_".$to_member_details->member_id."_".$current_datetime);
        $confirmation_code = strtoupper(substr($confirmation_code, 0, 8));
			
        $data = array(
            'from_member_id' => $this->member->member_id,
            'to_member_id' => $to_member_details->member_id,
            'amount' => $amount,
			'type' => $transfer_type,
            'confirmation_code' => $confirmation_code,
            'status' => 'PENDING',
            'insert_timestamp' => $current_datetime
        );
        $this->tracking_model->insert_member_transfers($data);
		
		$insert_id = $this->tracking_model->insert_id();
		
		$details_before = array('id' => $insert_id, 'details' => $data);
		$details_before = json_encode($details_before);
		
		$proper_name = $to_member_details->first_name . " " . $to_member_details->last_name;
			
		// deduct amount to member's credit
		if ($transfer_type == 'FUNDS') {
			$data = array(
				"funds" => ($this->member->funds) - $amount
			);
		} else if ($transfer_type == 'GCEP') {		
			$data = array(
				"gcep" => ($this->member->gcep) - $amount
			);
		} else {
			$data = array(
				"gift_cheques" => ($this->member->gift_cheques) - $amount
			);
		}		
		
		$this->members_model->update_member($data, "member_id = " . $this->member->member_id);
		
		// member logs 
		$details_after = array('id' =>  $insert_id, 'details' => $data);
		$details_after = json_encode($details_after);
		$new_member_log_data = array(
			'member_id' => $this->member->member_id,
			'module_name' => 'MEMBER TRANSFER ' . $transfer_type,
			'table_name' => 'cm_members_transfers',
			'action' => 'REQUEST',
			'details_before' => $details_before,
			'details_after' => $details_after,
			'remarks' => "",
		);
		$this->tracking_model->insert_logs('members', $new_member_log_data);
		
		
		$pretty_amount = number_format(($amount), 2);
		//$base_url = $this->config->item('base_url') . "/members/transfers/index/email/{$confirmation_code}";
		$base_url = $this->config->item('base_url') . "/members/transfers";
		
		// send email notification to recipient
        $params = array(
            "to_first_name"=>ucfirst($to_member_details->first_name),
            "to_last_name"=>ucfirst($to_member_details->last_name),
            "link"=>$base_url,
			"proper_amount"=>$pretty_amount,
			"proper_transfer_type"=>$transfer_type,
            "confirmation_code"=>$confirmation_code,
			"from_first_name"=>ucfirst($this->member->first_name),
            "from_last_name"=>ucfirst($this->member->last_name),
			"proper_name_to_member"=>$proper_name				
        );

		
		
        $data = array(
            "email"=>$this->member->email,
            "type"=>"transfer_email",
            "params"=>$params
        );

        //send email to user
        Modules::run('jobs/notifications/send_email',$data);
		 
		
		// transfer message
        
		$html ="<p>Your Transfer Request has been queued with an amount of <strong>Php{$pretty_amount}</strong> via <strong>{$transfer_type}</strong> to <strong>{$proper_name}</strong>.<br/><br/>
				You must enter the Confirmation Code sent to you via Email to complete the transaction.</p>
				";
		
        $this->return_json("1","Confirmation",array("html" => $html));
		return;   
	  
    }
	
	
	
}
