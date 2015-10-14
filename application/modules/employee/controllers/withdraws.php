<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Withdraws extends Site_Controller {
	
	function __construct() {
		parent::__construct();		
		
		$this->load->model('facilities_model');
		$this->load->model('members_model');
		$this->load->model('tracking_model');
		
		// load pager library
		$this->load->library('pager');
	}
	
	public function index()
	{
		$this->page();
	}
	
	public function page()
	{
		$member_id = $this->member->member_id;
		$from_date = $this->input->get_post('from_date');
		$to_date = $this->input->get_post('to_date');
		$date_filter = $this->input->get_post('date_filter');			
		$preferred_payout = $this->input->get_post('preferred_payout');
		$status_type = $this->input->get_post('status_type');		
		
		$from_t = strtotime($from_date);
		$to_t = strtotime($to_date);
		
		$where = "member_id = " . $member_id;
		
		if(!empty($preferred_payout) && $preferred_payout != 'all')	$where .= " AND preferred_payout = '" . strtoupper(trim($preferred_payout)) . "'";
		if(!empty($status_type) && $status_type != 'all')	$where .= " AND status = '" . strtoupper(trim($status_type)) . "'";		
			
		$from_dt = $from_date;
		$to_dt = $to_date;
				
		if ($from_t !== false) $from_dt = date('Y-m-d H:i:s', $from_t); 
		if ($to_t !== false) $to_dt = date('Y-m-d H:i:s', $to_t); 
		
		if ($from_t !== false && $to_t !== false)
			$where .= " AND (date_format(insert_timestamp, '%Y-%c-%d %H:%i:00') BETWEEN '{$from_dt}' AND '{$to_dt}') ";
		else if ($from_t !== false && $to_t === false)
			$where .= " AND insert_timestamp >= '{$from_dt}'";
		else if ($from_t === false && $to_t !== false)
			$where .= " AND insert_timestamp <= '{$to_dt}'";
		
		//// get all account id of member		
		//$member_account_details = $this->members_model->get_member_accounts("member_id = {$member_id}");
		
		$get_data = "?preferred_payout=" . $preferred_payout . "&status_type=" . $status_type . "&from_date=" . $from_date . "&to_date=" . $to_date;
	
		// initialize pagination class
		// set pagination data		
		$config = array(
			'pagination_url' => '/members/withdraws/page/',
		   	'total_items' => $this->members_model->get_member_withdraw_funds_transactions_count($where),
		    'per_page' => 10,
		    'uri_segment' => 4,
		);

		$this->pager->set_config($config);
			
		$this->template->transactions = $this->members_model->get_member_withdraw_funds_transactions($where, array('rows' => $this->pager->per_page, 'offset' => $this->pager->offset), 'insert_timestamp DESC');
		$this->template->member_id = $member_id;				
		$this->template->from_date = $from_date;
		$this->template->to_date = $to_date;
		$this->template->get_data = $get_data;
		
		if (empty($status_type)) 
			$status_type = "all";
			
		if (empty($preferred_payout)) 
			$preferred_payout = "all";	
			
		$between_timestamps = $from_date . " - " . $to_date;
		if (trim($between_timestamps) == "-") {
			$between_timestamps = "n/a";
		}
		
		$this->template->status_type = strtoupper($status_type);
		$this->template->preferred_payout = strtoupper($preferred_payout);
		$this->template->between_timestamps = strtoupper($between_timestamps);
		$this->template->current_page = 'withdraws';
		$this->template->view('withdraws/list');
	}
	
	 public function pending_withdraw() {
    	$hash = trim($this->input->post('hash'));
        
        
        $_hash = hash_hmac('md5',$this->member->session_id,$this->config->item('encryption_key'));
        if($hash != $_hash){
			$this->return_json("0","Invalid hash",array("html" => "Invalid Hash."));
			return;           
        }
        
        // check if user has pending withdraw request
		$withdraw_request = $this->members_model->get_member_withdraw_funds_transactions_count("member_id = {$this->member->member_id} and status = 'PENDING'");
	
		if ($withdraw_request > 0) {		
			// there is and existing request
			
			$html = "You still have a pending request.";					

			$this->return_json("0","With pending request",array("html" => $html));
			return;
			
		} else {
			$html = "No pending request.";					

			$this->return_json("1","No pending request",array("html" => $html));
			return;
		}    	
    }
	
	public function preprocess_withdraw() {    	    	    	
    	$withdraw_amount = abs(trim($this->input->post('withdraw_amount')));
    	$hash = $this->input->post('hash');
    	$process = $this->input->post('process');
        $payout_option = trim($this->input->post('payout_option'));

		// DEFAULT to PAYCARD
		$payout_option = "PAYCARD";
    	
    	$hash_check = hash_hmac('md5',$this->member->session_id,$this->config->item('encryption_key'));
    	
    	// check hash
        if ($hash!=$hash_check) {
			$this->return_json("0","Invalid Hash",array("html" => "Invalid Hash."));
			return;                        
        } 
    	 
    	// check if empty 
    	if ((empty($withdraw_amount)) || ($withdraw_amount <= 0)) {
    		$this->return_json("0","No amount encoded",array("html" => "Please enter an amount."));
			return; 			         
    	}
        
		// PAYCARD checking
		
		
		if 	(($this->member->metrobank_paycard_number == NULL) || empty($this->member->metrobank_paycard_number) || $this->member->is_paycard_verified == 0) {
			$this->return_json("0","No amount encoded",array("html" => "You need to have a Verified Metrobank Paycard in order to withdraw via Metrobank Paycard Payout. Please kindly update and verify your Paycard details. For more info, contact Vital-C IT Department."));
			return; 	
			
		}
		        
    	// check if the amount is in range    	
    	$withdrawal_limit_details = $this->settings_model->get_setting_by_slug("withdrawal_limit");	
		$withdrawal_limit = json_decode($withdrawal_limit_details->value);
	
		//$withdrawal_limit = json_decode($this->settings['withdrawal_limit']);
    	    	     
    	if (($withdraw_amount < abs($withdrawal_limit->minimum))  || ($withdraw_amount > abs($withdrawal_limit->maximum))) {
			$this->return_json("0","Request is beyond withdrawal limit",array("html" => "You are only allowed to withdraw an amount between <b>PHP" . $withdrawal_limit->minimum . "- PHP" . $withdrawal_limit->maximum ."</b>"));
			return;     		            
    	}
    	
    	//check if funds is sufficient          
        //if ($member_funds < $withdraw_amount) {
		if ($this->member->funds < $withdraw_amount) {
			$this->return_json("0","Insufficient funds",array("html" => "Insufficient Funds."));
			return;
			
        } else {
        	
			$initial_funds = $this->member->funds;
			
        	if ($process == 'queue') {	
				$data = array (
					'funds' => $this->member->funds - $withdraw_amount
				);
				
				// update funds minus the requested amount
				$this->members_model->update_member($data, "member_id = {$this->member->member_id}");
				
				// INSERT TO DEBIT TRACKING TABLES
				$data = array(
					'member_id' => $this->member->member_id,
					'type' => "FUNDS",
					'amount' => $withdraw_amount,
					'remarks' => "Deducted " . $withdraw_amount . " as Withdraw Request from funds with initial amount of " . $initial_funds 
				);
				$this->tracking_model->insert_debit_logs($data);
									
           	}
        	        	
        	$actual_withdrawable_amount = 0;
        	
			$withdrawal_tax_details = $this->settings_model->get_setting_by_slug("withdrawal_tax");	
			
            $actual_withdrawable_amount_tax = ($withdraw_amount * $withdrawal_tax_details->value);
        	$actual_withdrawable_amount = number_format($withdraw_amount - $actual_withdrawable_amount_tax, 2, '.', '');
        	$actual_withdrawable_amount_text = "PHP" . number_format($actual_withdrawable_amount, 2, '.', ',');
            $actual_withdrawable_amount_tax_text = "PHP" . number_format($actual_withdrawable_amount_tax, 2, '.', ',');
            $withdraw_amount_text = "PHP" . number_format($withdraw_amount, 2, '.', ',');
            
        	// if a queue process
        	if ($process == 'queue') {
                
                $completed_timestamp = "0000-00-00 00:00:00";
				$data = array('member_id'=>$this->member->member_id,
					'amount'=>$withdraw_amount,
					'amount_after_tax'=>$actual_withdrawable_amount,
					'status'=>"PENDING",
                    'preferred_payout'=>"PAYCARD",
					'tax'=>$withdrawal_tax_details->value,
					'completed_timestamp'=>$completed_timestamp
				);									
									
				$this->members_model->insert_member_withdraw_funds_transactions($data);

				// TODO : log to tr_member_logs
			
			 
				$this->return_json("1","Sufficient funds :: Queue",array("html"=>"Inserted to withdraw table."));
				return;
		     	//echo json_encode(array("status"=>1,"message"=>"Inserted to withdraw table."));
				//return;
				
			} else {				
				$this->return_json("1","Sufficient funds :: Check",array("html"=>"Sufficient Funds :: Check", "withdraw_amount" => $withdraw_amount_text, "actual_withdrawable_amount_tax_text" => $actual_withdrawable_amount_tax_text, "payout_option" => $payout_option));
				return;
				//echo json_encode(array("status"=>1,"html"=>"Sufficient Funds.", "withdraw_amount" => $withdraw_amount_text, "actual_withdrawable_amount_tax_text" => $actual_withdrawable_amount_tax_text, "payout_option" => $payout_option));
				//return;
			}
        }    	
    }
	
}
