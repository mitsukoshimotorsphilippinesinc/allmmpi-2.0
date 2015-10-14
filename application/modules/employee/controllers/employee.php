<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Employee extends Site_Controller 
{
	
	function __construct() 
	{
  		parent::__construct();
		$this->load->model('tracking_model');
		$this->load->model('asset_model');
		$this->load->model('setting_model');
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
		//// TODO : must be dependent to members session id
		$is_notify = 0;
        $error_tag = "";
		
		$this->template->current_page = 'dashboard'; 
        $this->template->view('dashboard');
    }
	

	
	public function get_html()
	{
		$page = $this->input->post("page");
		$member_id = $this->input->post("member_id");
		
		$member = $this->members_model->get_member_by_id($member_id);
		
		if(empty($member) || is_null($member))
		{
			$this->return_json("error","This member does not exist!",array("html" => ""));
			return;
		}

		
		$accounts = $this->members_model->get_member_accounts("member_id = ".$member_id);
		
		if($page == "profile")
		{
			$user_account = $this->members_model->get_member_user_account_by_member_id($member_id);
			$data = array(
				"member" => $member,
				"accounts" => $accounts,
				"user_account" => $user_account
			);
		}
		elseif($page == "accounts")
		{
			$data = array(
				"accounts" => $accounts
			);
		}
		elseif($page == "vouchers")
		{
			$data = array();
		}
		elseif($page == "earnings")
		{
			
		}
		elseif($page == "encoding")
		{			
			$page = "/encoding/dashboard";
			
			// get first account of member			
			$where = "member_id = {$member_id}";
			
			$account_details = $this->members_model->get_member_accounts($where,null,"account_id");
			
			$data = array(
				"account_details" => $account_details
			);
		}
		
		
		if(empty($data))
		{
			$this->return_json("error","This is not a valid page!",array("html" => ""));
			return;
		}
		
		$html = "";
		$html = $this->load->view($page,$data,true);
		
		$this->return_json("ok","HTML retrieved",array("html" => $html));
		return;
	}
	
	public function get_account_html()
	{
		$member_id = $this->input->post("member_id");
		$account_id = $this->input->post("account_id");
		
		$member = $this->members_model->get_member_by_id($member_id);
		
		if(empty($member) || is_null($member))
		{
			$this->return_json("error","This member does not exist!",array("html" => ""));
			return;
		}
		
		$account = $this->members_model->get_member_account_by_account_id($account_id);
		
		if(empty($account) || is_null($account))
		{
			$this->return_json("error","This account does not exist!",array("html" => ""));
			return;
		}
		
		$data = array();
		$_days = array('0' => '-');
		for ($i = 1; $i <= 31; $i++)
			$_days[$i] = $i;

		$_years = array('0' => '-');
		$_today = getdate();
		for ($i = $_today['year'] + 5; $i >= $_today['year'] - 60; $i--)
			$_years[$i] = $i;
		
		$account_type = $this->members_model->get_member_account_type_by_id($account->account_type_id);
		$account_status = $this->members_model->get_member_account_status_by_id($account->account_status_id);
		
		$total_referrals = $this->members_model->get_member_accounts(array('sponsor_id' => $account_id));
		
		$upline_account = $this->members_model->get_member_account_by_account_id($account->upline_id);
		$upline = $this->members_model->get_member_by_id($upline_account->member_id);
		$sponsor_account = $this->members_model->get_member_account_by_account_id($account->sponsor_id);
		$sponsor = $this->members_model->get_member_by_id($sponsor_account->member_id);
		
		
		$data = array(
			"member_id" => $member_id,
			"account_id" => $account_id,
			"account" => $account,
			"upline" => $upline,
			"sponsor" => $sponsor,
			"months" => $this->_months,
			"days" => $_days,
			"years" => $_years,
			"status" => $account_status->account_status,
			"account_type" => $account_type->description,
			"total_referrals" => count($total_referrals)
		);
		
		$html = "";
		$html = $this->load->view("earnings",$data,true);


		$this->return_json("ok","HTML retrieved",array("html" => $html));
		return;
		
	}
	
	public function check_password()
	{
		$member_id = $this->input->post("member_id");
		$password = $this->input->post("password");
		$password = strtoupper($password);
		
		if($this->member->member_id != $member_id){
			$this->return_json("error","You are currently logged in as {$this->member->first_name}.  Refresh page and try again.");
			return;
		}
		
		$member_account = $this->members_model->get_member_user_account_by_member_id($member_id);
		
		$md5_password = md5($password);
		
		if($member_account->password != $md5_password)
		{
			$this->return_json("error","Password does not match!");
			return;
		}
		
		$this->return_json("ok","Password matched!");
		return;
	}
	
	public function change_password()
	{
		$member_id = $this->input->post("member_id");
		$new_password = $this->input->post("new_password");
		
		if($this->member->member_id != $member_id){
			$this->return_json("error","You are currently logged in as {$this->member->first_name}.  Refresh page and try again.");
			return;
		}
		
		$previous_data = $this->members_model->get_member_user_account_by_member_id($member_id);
		$details_before = array('id' => $member_id, 'details' => array('password' => $previous_data->password));
		$details_before = json_encode($details_before);
		
		$this->members_model->update_member_user_accounts(array(
			"password" => md5(strtoupper($new_password)),
			"orig_password" => $new_password
			),array("member_id"=>$member_id));
		
		$details_after = array('id' => $member_id, 'details' => array("password" => md5($new_password)));
		$details_after = json_encode($details_after);
		$change_password_logs = array(
			'member_id' => $member_id,
			'module_name' => 'MY PROFILE',
			'table_name' => 'cm_member_user_accounts',
			'action' => 'UPDATE',
			'details_before' => $details_before,
			'details_after' => $details_after,
			'remarks' => "",
			
		);
		$this->tracking_model->insert_logs('members', $change_password_logs);
		
		$this->return_json("ok","Password Change Successful");
		return;
		
	}
		
	
	
	
	public function verify_proceed() {
		$member_id = abs($this->input->post("_member_id"));	
		$code = trim($this->input->post("_code"));	
		$code_type = $this->input->post("_code_type");
		$number = $this->input->post("_number");	
		
		if($this->member->member_id != $member_id){
			$this->return_json("error","You are currently logged in as {$this->member->first_name}.  Refresh page and try again.");
			return;
		}
		
		$verification_details = $this->members_model->get_member_verification_by_id($member_id);
		
		if (empty($verification_details)) {
			
			if ($code_type == "mobile_number") 
				$code_type = "mobile number";  

			$code_type = ucwords($code_type);
	
			$data = array(
				"html"=>"<p>Member Verification Code not found</p>",
				"code_type_title"=>$code_type
			);
			
			$this->return_json("0","Member Verification Code not found",$data);
			return;
		}
		
		// get date
		$update_timestamp = date("Y-m-d H:i:s");
			
		// check code against db
		if ($code_type == "email") {
			$code_from_table = $verification_details->email_code;
			$data_member_verification_before = array(
				"email_verification_timestamp"=>$verification_details->email_verification_timestamp
			);
			$data_member_verification = array(
				"email_verification_timestamp"=>$update_timestamp
			);
			$data_member_before = array(
				"is_email_verified"=>$this->member->is_email_verified
			);
			$data_member = array(
				"is_email_verified"=>1
			);
        } else if ($code_type == "rfid")  {
            $code_from_table = $verification_details->rf_id_code;
			$data_member_verification_before = array(
				"rf_id_verification_timestamp"=>$verification_details->rf_id_verification_timestamp
			);
            $data_member_verification = array(
                "rf_id_verification_timestamp"=>$update_timestamp
            );
			$data_member_before = array(
				"is_rf_id_verified"=>$this->member->is_rf_id_verified
			);
			$data_member = array(
				"is_rf_id_verified"=>1
			);
        } else if ($code_type == "paycard")  {
            $code_from_table = $verification_details->paycard_code;
			$data_member_verification_before = array(
				"paycard_verification_timestamp"=>$verification_details->paycard_verification_timestamp
			);
            $data_member_verification = array(
                "paycard_verification_timestamp"=>$update_timestamp
            );
			$data_member_before = array(
				"is_paycard_verified"=>$this->member->is_paycard_verified
			);
			$data_member = array(
				"is_paycard_verified"=>1
			);
        } else {
			$code_from_table = $verification_details->mobile_code;
			$data_member_verification_before = array(
				"mobile_verification_timestamp"=>$verification_details->mobile_verification_timestamp
			);
			$data_member_verification = array(
				"mobile_verification_timestamp"=>$update_timestamp
			);
			$data_member_before = array(
				"is_mobile_verified"=>$this->member->is_mobile_verified
			);
			$data_member = array(
				"is_mobile_verified"=>1
			);			
		}
		
		if ($code == trim($code_from_table)) {
			// correct code
			// check if rfid or paycard has not been tagged to another member yet
			if($code_type == "rfid" || $code_type == "paycard")
			{
				$column = "rfid_card_number";

				if($code_type == "paycard") $column = "paycard_number";

				$rfid_paycard_tag = $this->members_model->get_member_rfid_cards(array($column => $number));
				
				if(empty($rfid_paycard_tag))
				{
					$code_type = ucwords($code_type);
					$data = array(
						"html"=>"<p>{$code_type} Number not found</p>",
						"code_type_title"=>$code_type
					);

					$this->return_json("0","Number not found",$data);
					return;
				}
				$rfid_paycard_tag = $rfid_paycard_tag[0];

				if($rfid_paycard_tag->{$code_type."_member_id"} != 0 && $rfid_paycard_tag->{$code_type."_member_id"} != $this->member->member_id)
				{
					$code_type = ucwords($code_type);
					$data = array(
						"html"=>"<p>{$code_type} Number has already been tagged to another member.</p>",
						"code_type_title"=>$code_type
					);

					$this->return_json("0","Number already tagged to someone else",$data);
					return;
				}
				else
				{
					$data = array("{$code_type}_member_id"=>$this->member->member_id);
					$this->members_model->update_member_rfid($data,array($column => $number));
					
					$details_before = array('id' => $rfid_paycard_tag->rfid_card_id, 'details' => array("{$code_type}_member_id" => $rfid_paycard_tag->{$code_type."_member_id"}));
					$details_before = json_encode($details_before);

					// -------------------------------
					// tracking
					// -------------------------------
					$details_after = array('id' => $rfid_paycard_tag->rfid_card_id, 'details' => $data);
					$details_after = json_encode($details_after);
					$update_rfid_paycard_details = array(
						'member_id' => $this->member->member_id,
						'module_name' => strtoupper($code_type)." VERIFICATION",
						'table_name' =>  'cm_member_rfid_cards',
						'action' => "UPDATE",
						'details_before' => $details_before,
						'details_after' => $details_after,
						'remarks' => ""
					);
					$this->tracking_model->insert_logs('members', $update_rfid_paycard_details);
					
				}
			}
			
			// update verification_timestamp
			$this->members_model->update_member_verification($data_member_verification, "member_id = {$member_id}");
			
			$details_before = array('id' => $member_id, 'details' => $data_member_verification_before);
			$details_before = json_encode($details_before);

			// -------------------------------
			// tracking
			// -------------------------------
			$details_after = array('id' => $member_id, 'details' => $data_member_verification);
			$details_after = json_encode($details_after);
			$update_rfid_paycard_details = array(
				'member_id' => $this->member->member_id,
				'module_name' => strtoupper($code_type)." VERIFICATION",
				'table_name' =>  'cm_member_verification',
				'action' => "UPDATE",
				'details_before' => $details_before,
				'details_after' => $details_after,
				'remarks' => ""
			);
			$this->tracking_model->insert_logs('members', $update_rfid_paycard_details);
			
			
			$this->members_model->update_member($data_member, "member_id = {$member_id}");
			$details_before = array('id' => $member_id, 'details' => $data_member_before);
			$details_before = json_encode($details_before);

			// -------------------------------
			// tracking
			// -------------------------------
			$details_after = array('id' => $member_id, 'details' => $data_member);
			$details_after = json_encode($details_after);
			$update_rfid_paycard_details = array(
				'member_id' => $this->member->member_id,
				'module_name' => strtoupper($code_type)." VERIFICATION",
				'table_name' =>  'cm_members',
				'action' => "UPDATE",
				'details_before' => $details_before,
				'details_after' => $details_after,
				'remarks' => ""
			);
			$this->tracking_model->insert_logs('members', $update_rfid_paycard_details);		
			
			$html = "<p>
						You have successfully verified your {$code_type}.
					</p>";

			if ($code_type == "mobile_number") 
				$code_type = "mobile number";  

			$code_type = ucwords($code_type);
				
			$data = array(
				"html"=>$html,
				"code_type_title"=>$code_type
			);
			$this->return_json("1","Successful Verify Code", $data);
			return;
			
		} else {
			if ($code_type == "mobile_number") 
				$code_type = "mobile number";  

			$code_type = ucwords($code_type);
			
			// incorrect code
			$data = array(
				"html"=>"<p>Incorrect ". $code_type ." Code.</p>",
				"code_type_title"=>$code_type
			);
			$this->return_json("0","Incorrect ". ucwords($code_type) ." Verification Code", $data);
			return;
		}		
	}

    public function resend_email_verification() {
        $member_id = abs($this->input->post("_member_id"));
        $code_type = $this->input->post("_code_type");
		
		if($this->member->member_id != $member_id){
			$this->return_json("error","You are currently logged in as {$this->member->first_name}.  Refresh page and try again.");
			return;
		}
		
        $verification_details = $this->members_model->get_member_verification_by_id($member_id);

        if (empty($verification_details)) {
			if ($code_type == "mobile_number") 
				$code_type = "mobile number";  

			$code_type = ucwords($code_type);
			
            $data = array(
                "html"=>"<p>Member Verification Code not found</p>",
                "code_type_title"=>$code_type
            );
            $this->return_json("0","Member Verification Code not found",$data);
            return;
        }

        $member_details = $this->members_model->get_member_by_id($member_id);

        // get date
        $timestamp = date("Y-m-d H:i:s");

        // check code against db
        if ($code_type == "email") {
            // generate email code
            $generated_code = md5($member_id . $timestamp);


            $data = array(
                'member_id' => $member_id,
                'email_code' => $generated_code,
                'insert_timestamp' => $timestamp,
                'email_verification_timestamp' => '0000-00-00 00:00:00'
            );
            $detail = $member_details->email;

        } else if ($code_type == "rfid") {
            // generate rf_id code
            $generated_code = substr(md5($this->member->rf_id . $timestamp),1,16);


            $data = array(
                'member_id' => $member_id,
                'rf_id_code' => $generated_code,
                'insert_timestamp' => $timestamp,
                'rf_id_verification_timestamp' => '0000-00-00 00:00:00'
            );
            $detail = $member_details->rf_id;

        } else if ($code_type == "paycard") {
            // generate paycard code
            $generated_code = substr(md5($this->member->metrobank_paycard_number . $timestamp),1,16);

            $data = array(
                'member_id' => $member_id,
                'paycard_code' => $generated_code,
                'insert_timestamp' => $timestamp,
                'paycard_verification_timestamp' => '0000-00-00 00:00:00'
            );
            $detail = $member_details->metrobank_paycard_number;

        } else {
            // mobile number
            $generated_code = substr(md5($this->member->mobile_number . $timestamp),1,6);

            $data = array(
                'member_id' => $member_id,
                'mobile_code' => $generated_code,
                'insert_timestamp' => $timestamp,
                'mobile_verification_timestamp' => '0000-00-00 00:00:00'
            );
            $detail = $member_details->mobile_number;

        }

        // check if member got a existing email verificaiton
        $result = $this->members_model->get_member_verification(array('member_id' => $this->member->member_id));
        if (empty($result)) {
            $this->members_model->insert_member_verification($data);
        } else {
			$this->members_model->update_member_verification($data, array('member_id' => $this->member->member_id));           
        }

        $base_url = $this->config->item('base_url') . "/members/verification/index/{$code_type}/{$generated_code}";

        // send email
        //set params
        $params = array(
            "first_name"=>ucfirst($this->member->first_name),
            "link"=>$base_url,
            "generated_code"=>$generated_code,
            "detail"=>$detail
        );

        $data = array(
            "email"=>$this->member->email,
            "type"=> $code_type . "_verification_update",
            "params"=>$params
        );

        //send email to user
        Modules::run('jobs/notifications/send_email',$data);


        if ($code_type == "mobile_number") 
			$code_type = "mobile number";  

		$code_type = ucwords($code_type);
		
        $html = "<p>
                    {$code_type} Verification Code sent.
                </p>";

        $data = array(
            "html"=>$html,
            "code_type_title"=>$code_type
        );
        $this->return_json("1","Successful Verify Code", $data);
        return;
    }
	
	
}
