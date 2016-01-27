<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Profile extends Site_Controller 
{
	
	function __construct() 
	{
  		parent::__construct();
		$this->load->library('Email');
		$this->load->model('human_relations_model');
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
	
		$personal_information = $this->human_relations_model->get_personal_information_by_id($this->employee->personal_information_id);
		$employment_information = $this->human_relations_model->get_employment_information_by_id($this->employee->id_number);		

		$this->template->personal_information = $personal_information;
		$this->template->employment_information = $employment_information;
		$this->template->current_page = 'profile';
		$this->template->view('profile');
    }
	
	public function get_html()
	{
		$page = $this->input->post("page");
		$member_id = $this->input->post("member_id");
		
		
		if(empty($member) || is_null($member))
		{
			$this->return_json("error","This member does not exist!",array("html" => ""));
			return;
		}
		
		
		
		if($page == "profile")
		{
			
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
		
		$data = array(
			"member_id" => $member_id,
			"account_id" => $account_id,
			"account" => $account,
			"months" => $this->_months,
			"days" => $_days,
			"years" => $_years,
			"status" => $account_status->account_status,
			"account_type" => $account_type->description
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
		
		$this->members_model->update_member_user_accounts(array("password" => md5($new_password)),array("member_id"=>$member_id));
		
		$this->return_json("ok","Password Change Successful");
		return;
		
	}
	
	
	public function check_rfid()
	{
		$member_id = $this->input->post("member_id");
		$rf_id = trim($this->input->post("rf_id"));
		
		if($this->member->member_id != $member_id){
			$this->return_json("error","You are currently logged in as {$this->member->first_name}.  Refresh page and try again.");
			return;
		}
		
		$member = $this->members_model->get_member_by_id($member_id);
			
		if(empty($member))
		{
			$this->return_json("error","This member does not exist.");
			return;
		}
		
		$rfid_cards = $this->members_model->get_member_rfid_cards(array("rfid_card_number" => $rf_id));

		if(empty($rfid_cards))
		{
			$this->return_json("error","This RFID does not exist in our system. Please enter another.");
			return;
		}
		
		$rfid_cards = $rfid_cards[0];
		if($rfid_cards->rfid_member_id != 0 && $rfid_cards->rfid_member_id != $this->member->member_id)
		{
			$this->return_json("error","This RFID is already taken. Please enter another.");
			return;
		}
		// check if rfid exists in cm_members
		$where = array(
			'rf_id' => $rf_id
		);
		$members_with_same_rfid = $this->members_model->get_members($where);
		
		if (empty($members_with_same_rfid)) {
			// available rfid
			$this->return_json("ok","RFID verified as available.");
			return;
		} else {
			// check if rfid is member's current rfid
			foreach ($members_with_same_rfid as $mwse) {
				if (($this->member->member_id == $mwse->member_id) && ($this->member->rf_id == $mwse->rf_id)) {
					$this->return_json("same","RFID verified as same with current rfid.");
					return;
				} 								
			}
			
			$this->return_json("error","RFID is already taken. Please enter another.");
			return;		
		}
				
	}
	
	public function edit_rfid()
	{
		$member_id = $this->input->post("member_id");
		$rfid = $this->input->post("rfid");
		
		if($this->member->member_id != $member_id){
			$this->return_json("error","You are currently logged in as {$this->member->first_name}.  Refresh page and try again.");
			return;
		}
		
		$member = $this->members_model->get_member_by_id($member_id);
			
		if(empty($member))
		{
			$this->return_json("error","This member does not exist.");
			return;
		}
				
		$this->members_model->update_member(array("rf_id" => $rfid),array("member_id"=>$member_id));
		
		$details_before = array('id' => $member_id, 'details' => array("rf_id" => $member->rf_id));
		$details_before = json_encode($details_before);

		// -------------------------------
		// tracking
		// -------------------------------
		$details_after = array('id' => $member_id, 'details' => array("rf_id" => $rfid));
		$details_after = json_encode($details_after);
		$update_rfid_details = array(
			'member_id' => $this->member->member_id,
			'module_name' => "RFID EDITING",
			'table_name' =>  'cm_members',
			'action' => "UPDATE",
			'details_before' => $details_before,
			'details_after' => $details_after,
			'remarks' => ""
		);
		$this->tracking_model->insert_logs('members', $update_rfid_details);
		$this->return_json("ok","RF ID change successful.");
		return;
	}
	
	public function check_paycard()
	{
		$member_id = $this->input->post("member_id");
		$paycard = trim($this->input->post("paycard"));
		$is_paycard_corpo = trim($this->input->post("is_paycard_corpo"));
		
		if($this->member->member_id != $member_id){
			$this->return_json("error","You are currently logged in as {$this->member->first_name}.  Refresh page and try again.");
			return;
		}
		
		$member = $this->members_model->get_member_by_id($member_id);
			
		if(empty($member))
		{
			$this->return_json("error","This member does not exist.");
			return;
		}
		
		if($is_paycard_corpo == 1)
		{
			$this->return_json("ok","Metrobank Paycard will be set as CORPO.");
			return;
		}
		
		$rfid_cards = $this->members_model->get_member_rfid_cards(array("paycard_number" => $paycard));

		if(empty($rfid_cards))
		{
			$this->return_json("error","This Paycard does not exist in our system. Please enter another.");
			return;
		}
		
		$rfid_cards = $rfid_cards[0];
		if($rfid_cards->paycard_member_id != 0 && $rfid_cards->paycard_member_id != $this->member->member_id)
		{
			$this->return_json("error","This Paycard is already taken. Please enter another.");
			return;
		}
		
		// check if paycard exists in cm_members
		$where = array(
			'metrobank_paycard_number' => $paycard
		);
		
		
		$members_with_same_paycard = $this->members_model->get_members($where);
		
		if (empty($members_with_same_paycard)) {
			// available paycard
			$this->return_json("ok","Metrobank Paycard verified as available.");
			return;
		} else {
			// check if paycard is member's current paycard
			foreach ($members_with_same_paycard as $mwse) {
				if (($this->member->member_id == $mwse->member_id) && ($this->member->metrobank_paycard_number == $mwse->metrobank_paycard_number)) {
					$this->return_json("same","Metrobank Paycard verified as same with current paycard.");
					return;
				} 								
			}
			
			$this->return_json("error","Metrobank Paycard verified as taken.");
			return;		
		}
				
	}
	
	public function edit_paycard()
	{
		$member_id = $this->input->post("member_id");
		$paycard = $this->input->post("paycard");
		$is_paycard_corpo = $this->input->post("is_paycard_corpo");
		
		if($this->member->member_id != $member_id){
			$this->return_json("error","You are currently logged in as {$this->member->first_name}.  Refresh page and try again.");
			return;
		}
		
		$member = $this->members_model->get_member_by_id($member_id);
			
		if(empty($member))
		{
			$this->return_json("error","This member does not exist.");
			return;
		}
		
		$data_member = array("is_paycard_corpo" => $is_paycard_corpo);
		
		if($is_paycard_corpo == 0)
		{
			$data_member["metrobank_paycard_number"] = $paycard;
		}
		
		$this->members_model->update_member($data_member,array("member_id"=>$member_id));
		$details_before = array('id' => $member_id, 'details' => array("is_paycard_corpo" => $member->is_paycard_corpo,"metrobank_paycard_number" => $member->metrobank_paycard_number));
		$details_before = json_encode($details_before);

		// -------------------------------
		// tracking
		// -------------------------------
		$details_after = array('id' => $member_id, 'details' => $data_member);
		$details_after = json_encode($details_after);
		$update_paycard_details = array(
			'member_id' => $this->member->member_id,
			'module_name' => "PAYCARD EDITING",
			'table_name' =>  'cm_members',
			'action' => "UPDATE",
			'details_before' => $details_before,
			'details_after' => $details_after,
			'remarks' => ""
		);
		
		
		$member_accounts = $this->members_model->get_member_accounts(array("member_id" => $member_id));
		if(!empty($member_accounts))
		{
			if($is_paycard_corpo == 1)
			{
				$data = array("node_type" => "CORPO");
			}
			else
			{
				$data = array("node_type" => "REGULAR");
			}
			$this->members_model->update_member_accounts($data,array("member_id" => $member_id));

			foreach($member_accounts as $ma)
			{
				$details_before = array('id' => $ma->member_account_id, 'details' => array("node_type" => $ma->node_type));
				$details_before = json_encode($details_before);

				// -------------------------------
				// tracking
				// -------------------------------
				$details_after = array('id' => $ma->member_account_id, 'details' => $data);
				$details_after = json_encode($details_after);
				$update_member_account_details = array(
					'member_id' => $this->member->member_id,
					'module_name' => "PAYCARD EDITING",
					'table_name' =>  'cm_member_accounts',
					'action' => "UPDATE",
					'details_before' => $details_before,
					'details_after' => $details_after,
					'remarks' => ""
				);
				$this->tracking_model->insert_logs('members', $update_member_account_details);
			}
		}
		

		$this->return_json("ok","Metrobank Paycard Number change successful.");
		return;
	}
	
	
	public function check_email()
	{
		$member_id = $this->input->post("member_id");
		$email = trim($this->input->post("email"));
		
		
		if($this->member->member_id != $member_id){
			$this->return_json("error","You are currently logged in as {$this->member->first_name}.  Refresh page and try again.");
			return;
		}
		
		$member = $this->members_model->get_member_by_id($member_id);
			
		if(empty($member))
		{
			$this->return_json("error","This member does not exist.");
			return;
		}
		
		
		//if ($this->email->valid_email($email)) {
			
			// check if email exists in cm_members
			$where = array(
				'email' => $email
			);
			
			
			$members_with_same_email = $this->members_model->get_members($where);
			
			if (empty($members_with_same_email)) {
				// available email
				$this->return_json("ok","Email verified as available.");
				return;
			} else {
				// check if email is member's current email
				foreach ($members_with_same_email as $mwse) {
					if (($this->member->member_id == $mwse->member_id) && ($this->member->email == $mwse->email)) {
						$this->return_json("same","Email verified as same with current email.");
						return;
					} 								
				}
				
				$this->return_json("not ok","Email is already taken. Please try another.");
				return;		
			}
		//} else {
		//	$this->return_json("not ok","Invalid Email. Please try again.");
		//	return;
		//}
				
	}
	
	public function edit_email()
	{
		$member_id = $this->input->post("member_id");
		$email = $this->input->post("email");
		
		if($this->member->member_id != $member_id){
			$this->return_json("error","You are currently logged in as {$this->member->first_name}.  Refresh page and try again.");
			return;
		}
		
		$member = $this->members_model->get_member_by_id($member_id);
			
		if(empty($member))
		{
			$this->return_json("error","This member does not exist.");
			return;
		}

		$timestamp = date('Y-m-d H:i:s');
        $generated_email_code = md5($member->member_id . $timestamp);

        $data = array(
            'member_id' => $member->member_id,
            'mobile_code' => $generated_email_code,
            'email_code' => $generated_email_code,
            'insert_timestamp' => $timestamp
        );
        
        // check if member got a existing email verificaiton
        $result = $this->members_model->get_member_verification(array('member_id' => $member->member_id));
        if (count($result) > 0) {
            $this->members_model->update_member_verification($data, array('member_id' => $member->member_id));
        } else {
            $this->members_model->insert_member_verification($data);
        }

        $base_url = $this->config->item('base_url') . "/members/verification/index/email/{$generated_email_code}";

		// send email
		//set params needed for notification
        $params = array(
            "first_name"=>ucfirst($member->first_name),
            "detail"=>$email,
            "link"=>$base_url,
            "generated_code"=>$generated_email_code
        );

        //$data = array(
        //    "email"=>$email,
        //    "type"=>"email_verification_update",
        //    "params"=>$params
        //);

		$data = array(
            "email"=>$member->email,
            "type"=>"email_verification_update",
            "params"=>$params
        );
		
        //send email to user
        Modules::run('jobs/notifications/send_email',$data);
		

		$this->members_model->update_member(array("email" => $email),array("member_id"=>$member_id));
		$this->return_json("ok","Email change successful.");
		return;
	}
	
	
	public function check_mobile()
	{
		$member_id = $this->input->post("member_id");
		$mobile = trim($this->input->post("mobile"));
		
		if($this->member->member_id != $member_id){
			$this->return_json("error","You are currently logged in as {$this->member->first_name}.  Refresh page and try again.");
			return;
		}
		
		$member = $this->members_model->get_member_by_id($member_id);
			
		if(empty($member))
		{
			$this->return_json("error","This member does not exist.");
			return;
		}
		
		// check if mobile number exists in cm_members
		$where = array(
			'mobile_number' => $mobile
		);
		
		$members_with_same_mobile = $this->members_model->get_members($where);
		
		if (empty($members_with_same_mobile)) {
			// available email
			$this->return_json("ok","Mobile verified as available.");
			return;
		} else {
			// check if mobile is member's current mobile
			foreach ($members_with_same_mobile as $mwse) {
				if (($this->member->member_id == $mwse->member_id) && ($this->member->mobile_number == $mwse->mobile_number)) {
					$this->return_json("same","Mobile verified as same with current mobile.");
					return;
				} 								
			}
			
			$this->return_json("not ok","Mobile verified as taken.");
			return;		
		}
				
	}
	
	public function edit_mobile()
	{
		$member_id = $this->input->post("member_id");
		$mobile = $this->input->post("mobile");
		
		if($this->member->member_id != $member_id){
			$this->return_json("error","You are currently logged in as {$this->member->first_name}.  Refresh page and try again.");
			return;
		}
		
		$member = $this->members_model->get_member_by_id($member_id);
			
		if(empty($member))
		{
			$this->return_json("error","This member does not exist.");
			return;
		}
		
		$this->members_model->update_member(array("mobile_number" => $mobile),array("member_id"=>$member_id));
		$this->return_json("ok","Email change successful.");
		return;
	}

	public function edit_profile_information()
	{
		$type = $this->input->post('type');
		$member_id = $this->input->post('member_id');
		$member = $this->members_model->get_member_by_id($member_id);
		
		if($this->member->member_id != $member_id){
			$this->return_json("error","You are currently logged in as {$this->member->first_name}.  Refresh page and try again.");
			return;
		}
		
		if($type == 'personal')
		{
			$sex = $this->input->post('gender');
			$marital_status = $this->input->post('marital_status');
			$nationality = $this->input->post('nationality');
			$tin = $this->input->post('tin');
			$birth_month = $this->input->post('birth_month');
			$birth_year = $this->input->post('birth_year');
			$birth_day = $this->input->post('birth_day');
			$date = $birth_year . '-' . $birth_month . '-' . $birth_day;
			$birthdate = date('Y-m-d', strtotime($date)); 
		
			$data = array(
				'sex' => $sex,
				'marital_status' => $marital_status,
				'nationality' => $nationality,
				'tin' => $tin,
				'birthdate' => $birthdate
			);

			$old = array(
				'sex' => $member->sex,
				'marital_status' => $member->marital_status,
				'nationality' => $member->nationality,
				'tin' => $member->tin,
				'birthdate' => $member->birthdate
			);
		}
		elseif($type == 'address')
		{
			$home_address_street = $this->input->post('home_address_street');
			$home_address_city =  $this->input->post('home_address_city');
			$home_address_province = $this->input->post('home_address_province');
			$home_address_country = $this->input->post('home_address_country');
			$home_zip_postalcode = $this->input->post('home_zip_postalcode');
			
			$data = array(
				'home_address_street' => $home_address_street,
				'home_address_city' => $home_address_city,
				'home_address_province' => $home_address_province,
				'home_address_country' => $home_address_country,
				'home_address_zip_code' => $home_zip_postalcode 
			);

			$old = array(
				'home_address_street' => $member->home_address_street,
				'home_address_city' => $member->home_address_city,
				'home_address_province' => $member->home_address_province,
				'home_address_country' => $member->home_address_country,
				'home_address_zip_code' => $member->home_zip_postalcode 
			);
		}
		elseif($type == 'account')
		{
			$beneficiary1 = $this->input->post('beneficiary1');
			$beneficiary2 = $this->input->post('beneficiary2');
			$group_name = trim($this->input->post('group_name'));
			$group_name = strtoupper($group_name);
			if($group_name != "")
			{
				$is_group_exists = $this->members_model->get_member_group_by_group_name($group_name);
				if(empty($is_group_exists))
				{
					$data = array('group_name' => $group_name);
					$this->members_model->insert_member_group($data);
				}
			}
			
			$data = array(
				'beneficiary1' => $beneficiary1,
				'beneficiary2' => $beneficiary2,
				'group_name' => $group_name
			);

			$old = array(
				'beneficiary1' => $member->beneficiary1,
				'beneficiary2' => $member->beneficiary2,
				'group_name' => $member->group_name
			);
 		}
		$where = array('member_id' => $member_id);
		$this->members_model->update_member($data, $where);

		// send email to user
		$old_details = "";
		foreach ($old as $key => $value) {
			$field = ucwords(str_replace("_", " ", $key));
			$old_details .= "<li>" . $field . " : <strong>" . $value . "</strong></li>";
		}
		$old_details = "<ul>" . $old_details . "</ul>";

		$new_details = "";
		foreach ($data as $key => $value) {
			$field = ucwords(str_replace("_", " ", $key));
			$new_details .= "<li>" . $field . " : <strong>" . $value . "</strong></li>";
		}
		$new_details = "<ul>" . $new_details . "</ul>";

		$params = array(
            "first_name"=>ucfirst($member->first_name),
            "last_name"=>ucfirst($member->last_name),
            "old_details"=>$old_details,
            "new_details"=>$new_details
        );

		if(!empty($member->email))
		{
			$email_data = array(
	            "email"=>$member->email,
	            "type"=>"member-profile-update",
	            "params"=>$params
	        );

	        Modules::run('jobs/notifications/send_email',$email_data);
		}        
		
		$this->return_json('1', 'success');
	}

	public function upload_profile_picture()
	{
		$target_path = FCPATH . "assets/media/tmp/";
		$allowedExts = array();
		$maxFileSize = 0;
	
		$headers = get_fu_headers();

		$width = abs($this->input->get('width'));
		$height = abs($this->input->get('height'));
		
		$upload_type = trim($this->input->get('type'));
		
		if ($width<=0) $width = 200;
		if ($height<=0) $height =200;
		
		$location = $this->input->get('location');
		
		if ($location=="") $location = "/assets/media/uploads"; 
		else $location = urldecode($location);
		
		$gallery_id = 0;
		
		if(strtolower($upload_type) == "gallery")
		{
			$gallery_id = abs($this->input->get('gallery_id'));
		}
		elseif(strtolower($upload_type) == "general")
		{
			$gallery_id = "general";
		}

		if ($headers['X-Requested-With']=='XMLHttpRequest') { 
			$fileName = $headers['X-File-Name'];
			$fileSize = $headers['X-File-Size'];
			$ext = substr($fileName, strrpos($fileName, '.') + 1);
			if (in_array($ext,$allowedExts) or empty($allowedExts)) {
				if ($fileSize<$maxFileSize or empty($maxFileSize)) {
				$content = file_get_contents("php://input");
				file_put_contents($target_path.$fileName,$content);

				$filename = $this->input->get('filename');
				if ($filename == "") $filename = str_replace(".".$ext,'',$fileName);
                
				echo $this->_saveImage($filename, $fileName, $target_path.$fileName, $location, $width, $height,$gallery_id, false);

			} else { echo('{"success":false, "details": "Maximum file size: '.get_byte_size($maxFileSize).'."}'); };
			} else {
				echo('{"success":false, "details": "File type '.$ext.' not allowed."}');
			}
				
		} else {
			if ($_FILES['file']['name']!='') {
			$fileName= $_FILES['file']['name'];
			$fileSize = $_FILES['file']['size'];
			$ext = substr($fileName, strrpos($fileName, '.') + 1);
			if (in_array($ext,$allowedExts) or empty($allowedExts)) {
				if ($fileSize<$maxFileSize or empty($maxFileSize)) {
			
			$filename = $this->input->get('filename');
			if ($filename == "") $filename = str_replace(".".$ext,'',$fileName);
			
            echo $this->_saveImage($filename, $_FILES['file']['name'], $_FILES['file']['tmp_name'],$location,$width,$height,$gallery_id);
			
			} else { echo('{"success":false, "details": "Maximum file size: '.get_byte_size($maxFileSize).'."}'); };
			} else echo('{"success":false, "details": "File type '.$ext.' not allowed."}');
			} else echo '{"success":false, "details": "No file received."}';
		}
	}

	public function update_image()
	{
		$filename = $this->input->post('filename');
		$member_id = $this->input->post('member_id');
		$member = $this->members_model->get_member_by_id($member_id);
		
		$data = array(
			'image_filename' => $filename
		);
		$this->members_model->update_member($data, array('member_id' => $member_id));
		
		$previous_data = array(
			'image_filename' => $member->image_filename
		);
		
		$details_before = array('id' => $member_id, 'details' => $previous_data);
		$details_before = json_encode($details_before);
		
       //admin logs
		$details_after = array('id' => $member_id, 'details' => $data);
		$details_after = json_encode($details_after);
		$update_member_details_data = array(
			'member_id' => $member_id,
			'module_name' => 'MEMBER ADMIN - CONCERNS',
			'table_name' => 'tr_ad_member_logs',
			'action' => 'UPDATE',
			'details_before' => $details_before,
			'details_after' => $details_after,
			'remarks' => ""
		);

		// send email notification
		$old_details = "";
		foreach ($previous_data as $key => $value) {
			$field = ucwords(str_replace("_", " ", $key));
			$old_details .= "<li>" . $field . " : <strong>" . $value . "</strong></li>";
		}
		$old_details = "<ul>" . $old_details . "</ul>";

		$new_details = "";
		foreach ($data as $key => $value) {
			$field = ucwords(str_replace("_", " ", $key));
			$new_details .= "<li>" . $field . " : <strong>" . $value . "</strong></li>";
		}
		$new_details = "<ul>" . $new_details . "</ul>";

		$params = array(
            "first_name"=>ucfirst($member->first_name),
            "last_name"=>ucfirst($member->last_name),
            "old_details"=>$old_details,
            "new_details"=>$new_details
        );

		if(!empty($member->email))
		{
			$email_data = array(
	            "email"=>$member->email,
	            "type"=>"member-profile-update",
	            "params"=>$params
	        );

	        Modules::run('jobs/notifications/send_email',$email_data);
		}

		$this->tracking_model->insert_logs('members', $update_member_details_data);
		
	}

	private function _saveImage($filename, $uploading_filename, $tmp_filename, $location, $width, $height, $gallery_id = 0, $using_file = true) {
		
		$format = 'invalid';
		
		$_uploading_filename = $uploading_filename; //$_FILES['Filedata']['name'];
		
		//check if gif
	    if(stristr(strtolower($_uploading_filename),'.gif')) $format = 'gif';
	    //check if jpg
	    elseif(stristr(strtolower($_uploading_filename),'.jpg') || stristr(strtolower($_uploading_filename),'.jpeg')) $format = 'jpg';
	    //check if png
	    elseif(stristr(strtolower($_uploading_filename),'.png')) $format = 'png';
		
		$temp_file = $tmp_filename; //$_FILES['file']['tmp_name'];
		
		$_hash = substr(md5(date('Y-m-d H:i:s')),0,8);
		
		$filename = $filename . "." . $format;
		$target_filename = $filename;
		
		$location = substr($location,1,strlen($location));
		
		$target_fullpath = FCPATH . $location;
		$target_thumb_fullpath = FCPATH . $location. "/thumbnail";
		$fullpath = FCPATH . $location . "/". $filename;
		
		$_ret = true;
		
		if ($using_file) {
			$_ret = move_uploaded_file($temp_file, $fullpath);
		} else {
			$_ret = copy($temp_file, $fullpath);
			if ($_ret) unlink($temp_file);
		}
		
		if(!$_ret) {
			return json_encode(array('success' => false, 'details' => 'move_uploaded_file failed'));
		} else {

			// resize
			$_width = $width;
			$_height = $height;		
			
			if ($format != 'invalid') {
				// Load image
		        $image = null;
		        switch($format) {
		            case 'gif':
		                $image = ImageCreateFromGif($fullpath);
		                break;
		            case 'jpg':
		                $image = ImageCreateFromJpeg($fullpath);
		                break;
		            case 'png':
		                $image = ImageCreateFromPng($fullpath);
		                break;
		        }

		        if ($image === null) {
		            echo 'Unable to open image';
					exit;
		        }

				// Get original width and height
		        list($width,$height)=getimagesize($fullpath);

				
				
				// serve as default image
		        $image_resized = $image;
				$image_thumb = null;
				if(strcmp($gallery_id, "general") == 0)
				{
					
					$image_thumb = $image;
					
					// New width with aspect ratio
					$newWidth = 100;
					$newHeight = $height * ($newWidth / $width);

					$pad_x = 0;
					$pad_y = 0;
					

					$image_thumb = imagecreatetruecolor($newWidth,$newHeight);
					if ($format == 'png') // png we can actually preserve transparency
					{
						imagecolortransparent($image_thumb, imagecolorallocatealpha($image_thumb, 0, 0, 0, 127));
						imagealphablending($image_thumb, FALSE);
						imagesavealpha($image_thumb, TRUE);
					}	
					imagecopyresampled($image_thumb, $image, 0, 0, $pad_x, $pad_y, $newWidth, $newHeight, $width, $height);
					
					$target_fullpath = $target_fullpath;
					
					$target_thumb_fullpath = $target_thumb_fullpath;
					
				}
				elseif($gallery_id == 0)
				{
					
					// New width with aspect ratio
					$newWidth= $_width;
					$newHeight = $_height;
					$pad_x = 0;
					$pad_y = 0;

					//old
					/*if ($width > $height) {
						$newWidth=($width/$height)* $_height;
						//$pad_x = ($width - $height) / 2;
					} else {
						$newHeight=($height/$width)* $_width;
						//$pad_y = ($height - $width) / 2;
					}*/
					
					if ($width > $height) {
						$newHeight=($height/$width)* $_width;
					} else {
						$newWidth=($width/$height)* $_height;
					}

					$image_resized = imagecreatetruecolor($newWidth,$newHeight);
					if ($format == 'png') // png we can actually preserve transparency
					{
						imagecolortransparent($image_resized, imagecolorallocatealpha($image_resized, 0, 0, 0, 127));
						imagealphablending($image_resized, FALSE);
						imagesavealpha($image_resized, TRUE);
					}					
					imagecopyresampled($image_resized, $image, 0, 0, $pad_x, $pad_y, $newWidth, $newHeight, $width, $height);
				}
				else
				{
					
					$image_thumb = $image;
					
					// New width with aspect ratio
					$newWidth = 100;
					$newHeight = $height * ($newWidth / $width);

					$pad_x = 0;
					$pad_y = 0;
					
					// New width with aspect ratio
					$newWidth= $_width;
					$newHeight = $_height;
					$pad_x = 0;
					$pad_y = 0;


					if ($width > $height) {
						$newHeight=($height/$width)* 200;
					} else {
						$newWidth=($width/$height)* 200;
					}
					
					$image_thumb = imagecreatetruecolor($newWidth,$newHeight);
					
					if ($format == 'png') // png we can actually preserve transparency
					{
						imagecolortransparent($image_thumb, imagecolorallocatealpha($image_thumb, 0, 0, 0, 127));
						imagealphablending($image_thumb, FALSE);
						imagesavealpha($image_thumb, TRUE);
					}
					
					imagecopyresampled($image_thumb, $image, 0, 0, $pad_x, $pad_y, $newWidth, $newHeight, $width, $height);
					
					$target_fullpath = $target_fullpath . "/gallery_" . $gallery_id;
					
					if(!is_dir($target_fullpath))
						mkdir($target_fullpath);
					
					$target_thumb_fullpath = $target_thumb_fullpath . "/gallery_" . $gallery_id;
					
					if(!is_dir($target_thumb_fullpath))
						mkdir($target_thumb_fullpath);
					
				}
				
		        $target_fullpath = $target_fullpath . "/". $target_filename;
				$target_thumb_fullpath = $target_thumb_fullpath . "/". $target_filename;
				
				// Display resized image
		        $thumb_filename = "";
				unlink($fullpath);
				//imagejpeg($image_resized, $target_fullpath);
				
				switch($format) {
		            case 'gif':
		                imagegif($image_resized, $target_fullpath);
		                break;
		            case 'jpg':
		                imagejpeg($image_resized, $target_fullpath);
		                break;
		            case 'png':
		                imagepng($image_resized, $target_fullpath);
		                break;
		        }
				
				
				if(!is_null($image_thumb) && !empty($image_thumb)) 
				{
					//imagejpeg($image_thumb, $target_thumb_fullpath);
					
					switch($format) {
		            case 'gif':
		                imagegif($image_thumb, $target_thumb_fullpath);
		                break;
		            case 'jpg':
		                imagejpeg($image_thumb, $target_thumb_fullpath);
		                break;
		            case 'png':
		                imagepng($image_thumb, $target_thumb_fullpath);
		                break;
		        	}
				}
				
				if(strcmp($gallery_id, "general") == 0)
				{
					$this->load->model("contents_model");
					$data = array(
						"image_filename" => $filename,
						"user_id" => $this->user->user_id
					);
					$this->contents_model->insert_image_uploads($data);
					
					$insert_id = $this->contents_model->insert_id();
					
					//logging of action
					$details_after = array('id' => $insert_id, 'details' => $data);
					$details_after = json_encode($details_after);
					$add_upload_log_data = array(
						'user_id' => $this->user->user_id,
						'module_name' => 'IMAGE UPLOADS',
						'table_name' => 'sm_image_uploads',
						'action' => 'ADD',
						'details_after' => $details_after,
						'remarks' => "",
					);

					$this->tracking_model->insert_logs('admin', $add_upload_log_data);
				}
				elseif($gallery_id != 0)
				{
					$this->load->model("contents_model");
					$data = array(
						"gallery_id" => $gallery_id,
						"image_filename" => $filename,
						"user_id" => $this->user->user_id
					);
					$this->contents_model->insert_gallery_pictures($data);
					
					$insert_id = $this->contents_model->insert_id();
					
					//logging of action
					$details_after = array('id' => $insert_id, 'details' => $data);
					$details_after = json_encode($details_after);
					$add_gallery_log_data = array(
						'user_id' => $this->user->user_id,
						'module_name' => 'GALLERY PICTURES',
						'table_name' => 'sm_gallery_pictures',
						'action' => 'ADD',
						'details_after' => $details_after,
						'remarks' => "",
					);

					$this->tracking_model->insert_logs('admin', $add_gallery_log_data);
				}
				return json_encode(array('success' => true, 'file' => $filename));
			}
		
		}
		
	}


	public function job_description()
	{							
		$this->template->current_page = 'job_description';

		// get parent position_id
		$position_details = $this->human_relations_model->get_position_by_id($this->employee->position_id);

		$position_id = 0;

		if (empty($position_details)) {
			// error
		} else {
			$position_id = ($position_details->parent_position_id == 0) ? $position_id = $position_details->position_id : $position_id = $position_details->parent_position_id;

			$job_description_asset_details = $this->human_relations_model->get_job_description_asset(array("position_id" => $position_id, "is_active" => 1));
			$this->template->position_details = $position_details;
			
			if (!empty($job_description_asset_details)) {
				$this->template->job_description_asset_details = $job_description_asset_details[0];
			} else {
				$this->template->job_description_asset_details = "";
			}
		}
			
		$this->template->view('job_description/dashboard');
	}
}
