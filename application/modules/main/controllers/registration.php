<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Registration extends Base_Controller {

	function __construct() {
		// call the base model constructor
		parent::__construct();
		$this->load->model("members_model");
		$this->load->model("items_model");
		$this->load->model("settings_model");
		$this->load->model("facilities_model");
		$this->load->model("tracking_model");
		$this->load->model('raffles_model');
		$this->load->library('Email');
	}

	public function index() {
		
        $upline_id = $this->input->get("upline_id");
        $position = $this->input->get("position");

        //var_dump($upline_id . "|" . $position);

		$this->template->upline_id = $upline_id;
		$this->template->position = $position;		
		$this->template->view('registration/registration');
	}
	
	public function register () 
	{
		// registration attempt log
	    $details_after = array(
	      'account_id' => $this->input->post('account_id'),
	      'account_code' => $this->input->post('account_code'),
	      'sponsor_id' => $this->input->post('sponsor_id'),
	      'upline_id' => $this->input->post('upline_id'),
	      'group_name' => $this->input->post('group_name'),
	      'position' => $this->input->post('position'),
	      'username' => $this->input->post('username'),
	      'password1' => md5($this->input->post('password1')),
	      'password2' => md5($this->input->post('password2')),
	      'service_depot' => $this->input->post('service_depot'),
	      'rf_id' => $this->input->post('rf_id'),
	      'registration_type' => $this->input->post('registration_type'),
	      'fname' => $this->input->post('fname'),
	      'mname' => $this->input->post('mname'),
	      'lname' => $this->input->post('lname'),
	      'birthday' => $this->input->post('birthday'),
	      'gender' => $this->input->post('gender'),
	      'marital_status' => $this->input->post('marital_status'),
	      'nationality' => $this->input->post('nationality'),
	      'tin_number' => $this->input->post('tin_number'),
	      'beneficiary1' => $this->input->post('beneficiary1'),
	      'beneficiary2' => $this->input->post('beneficiary2'),
	      'email' => $this->input->post('email'),
	      'country_code' => $this->input->post('country_code'),
	      'area_code' => $this->input->post('area_code'),
	      'mobile_number' => $this->input->post('mobile_number'),
	      'address' => $this->input->post('address'),
	      'country' => $this->input->post('country'),
	      'state_province' => $this->input->post('state_province'),
	      'city' => $this->input->post('city'),
	      'zip_postalcode' => $this->input->post('zip_postalcode')
	    );
	    $registration_attempt_log_data = array(
	      'member_id' => 0, //no member id given yet  
	      'module_name' => 'MEMBER REGISTRATION',
	      'table_name' => 'cm_members',
	      'action' => 'ADD',
	      'details_before' => "",
	      'details_after' => json_encode($details_after),
	      'remarks' => "REGISTRATION ATTEMPT"
	    );
	    $this->tracking_model->insert_logs('members', $registration_attempt_log_data);
		
		// PREPARING PARAMETERS
		$account_id = trim($this->input->post('account_id'));
		$account_code = trim($this->input->post('account_code'));

		// check card
		$card = $this->cards_model->get_sp_card("card_id = '{$account_id}' AND card_code='{$account_code}' AND status='ACTIVE'");

		if (empty($card)) 
		{			
		 	echo json_encode(array("status"=>0,"message"=>"Invalid Account ID"));
			return;
		}

		$service_depot_id = abs($this->input->post('service_depot'));

		$first_name = strtoupper(trim($this->input->post('fname')));
		$middle_name = strtoupper(trim($this->input->post('mname')));
		$last_name = strtoupper(trim($this->input->post('lname')));
		$primary_email = trim($this->input->post('email'));

		$username = trim($this->input->post('username'));
        $rf_id = trim($this->input->post('rf_id'));
        $group_name = trim($this->input->post('group_name'));
		$nationality = strtoupper(trim($this->input->post('nationality')));
		$marital_status = strtoupper(trim($this->input->post('marital_status')));
		$birthday = trim($this->input->post('birthday'));
		$gender = strtoupper(trim($this->input->post('gender')));
		$password1 = $this->input->post('password1');
		$country_code = $this->input->post('country_code');
		$area_code = trim($this->input->post('area_code'));
		$mobile_number = trim($this->input->post('mobile_number'));           	            
		$address = strtoupper(trim($this->input->post('address')));
		$city = strtoupper(trim($this->input->post('city')));
		$state_province = strtoupper(trim($this->input->post('state_province')));
		$zip_postalcode = strtoupper(trim($this->input->post('zip_postalcode')));
		$country = strtoupper(trim($this->input->post('country')));	           
		$tin_number = trim($this->input->post('tin_number')); //FIND
		$registration_type = strtoupper(trim($this->input->post('registration_type')));
		$sponsor_id = trim($this->input->post('sponsor_id'));	  
		$upline_id = trim($this->input->post('upline_id')); 
		$position = $this->input->post('position');
		$beneficiary1 = strtoupper(trim($this->input->post('beneficiary1')));
		$beneficiary2 = strtoupper(trim($this->input->post('beneficiary2')));

		// INSERT JOB
		$this->load->model('jobs_model');
		$params = array(
			'account_id' => $account_id,
			'account_code' => $account_code,
			'service_depot_id' => $service_depot_id,
			'first_name' => $first_name,
			'middle_name' => $middle_name,
			'last_name' => $last_name,
			'primary_email' => $primary_email,
			'username' => $username,
			'rf_id' => $rf_id,
			'group_name' => $group_name,
			'nationality' => $nationality,
			'marital_status' => $marital_status,
			'birthday' => $birthday,
			'gender' => $gender,
			'password1' => $password1,
			'country_code' => $country_code,
			'area_code' => $area_code,
			'mobile_number' => $mobile_number,
			'address' => $address,
			'city' => $city,
			'state_province' => $state_province,
			'zip_postalcode' => $zip_postalcode,
			'country' => $country,
			'tin_number' => $tin_number,
			'registration_type' => $registration_type,
			'sponsor_id' => $sponsor_id,
			'upline_id' => $upline_id,
			'position' => $position,
			'beneficiary1' => $beneficiary1,
			'beneficiary2' => $beneficiary2
		);
		$job_data = array(
			'job_type_id' => 3, // registration
			'parameters' => json_encode($params)
		);
		$this->jobs_model->insert_job($job_data);
		$job_id = $this->jobs_model->insert_id();
		
		// RUN JOB
		job_exec($job_id);

		echo json_encode(array("status"=>1,"message"=>"Successful Registration"));
        return;

        /* ----------- OLD ----------- */
		
		$this->load->model("cards_model");
		
		$account_id = trim($this->input->post('account_id'));
		$account_code = trim($this->input->post('account_code'));
		
		// check card
		$card = $this->cards_model->get_sp_card("card_id = '{$account_id}' AND card_code='{$account_code}' AND status='ACTIVE'");
		
		if (empty($card)) 
		{			
		 	echo json_encode(array("status"=>0,"message"=>"Invalid Account ID"));
			return;
		}

		$card = $card[0];
		
		$service_depot_id = abs($this->input->post('service_depot'));
		
		if ($service_depot_id==2) // ortigas depot in user facilities
			$service_depot_id = 1;
		else if ($service_depot_id==4) // cebu depot in user facilities
			$service_depot_id = 2;
		else if ($service_depot_id==5) // davao depot in user facilities
			$service_depot_id = 3;
		else
			$service_depot_id = 1;

		$first_name = strtoupper(trim($this->input->post('fname')));
		$middle_name = strtoupper(trim($this->input->post('mname')));
		$last_name = strtoupper(trim($this->input->post('lname')));
		$primary_email = trim($this->input->post('email'));
			
		$proper_name = $last_name . ", " . $first_name . " " . $middle_name;

		$username = trim($this->input->post('username'));
        $rf_id = trim($this->input->post('rf_id'));
        $group_name = trim($this->input->post('group_name'));
		//$tin_number = trim($this->input->post('tin_number'));
		$nationality = strtoupper(trim($this->input->post('nationality')));
		$marital_status = strtoupper(trim($this->input->post('marital_status')));
		$birthday = trim($this->input->post('birthday'));
		$gender = strtoupper(trim($this->input->post('gender')));
		$password1 = $this->input->post('password1');
		//$password2 = $this->input->post('password2');
		$country_code = $this->input->post('country_code');
		$area_code = trim($this->input->post('area_code'));
		$mobile_number = trim($this->input->post('mobile_number'));           	            
		$address = strtoupper(trim($this->input->post('address')));
		$city = strtoupper(trim($this->input->post('city')));
		$state_province = strtoupper(trim($this->input->post('state_province')));
		$zip_postalcode = strtoupper(trim($this->input->post('zip_postalcode')));
		$country = strtoupper(trim($this->input->post('country')));	           
		$tin_number = trim($this->input->post('tin_number')); //FIND
		$registration_type = strtoupper(trim($this->input->post('registration_type')));
		$sponsor_id = trim($this->input->post('sponsor_id'));	  
		$upline_id = trim($this->input->post('upline_id')); 
		$position = $this->input->post('position');
		$beneficiary1 = strtoupper(trim($this->input->post('beneficiary1')));
		$beneficiary2 = strtoupper(trim($this->input->post('beneficiary2')));
		
		$complete_address = $address . ' ' . $city . ' ' . $state_province . ' ' . $country . ' ' . $zip_postalcode;
		$mobile_number = $country_code . $area_code . $mobile_number;

		// set current timestamp
		$current_timestamp = date("Y-m-d H:i:s");		

        // get group_name and id based on upline_id
        $upline = $this->members_model->get_member_account_by_account_id($upline_id);
        $upline_details = $this->members_model->get_member_by_id($upline->member_id);
		

		// insert details to cm_members
		$data = array(		
           	'first_name' => $first_name,
           	'middle_name' => $middle_name,
           	'last_name' => $last_name,
			'email' => $primary_email,	
			'mobile_number' => $mobile_number,    
			'home_address' => $complete_address,       	            
			'home_address_street' => $address,
           	'home_address_city' => $city,
           	'home_address_province' => $state_province,          	
           	'home_address_country' => $country,
			'home_address_zip_code' => $zip_postalcode,
			'birthdate' => $birthday,
			'sex' => $gender,
			'nationality' => $nationality,
          	'marital_status' => substr($marital_status,0,1),
			'tin' => $tin_number,
            'service_depot' => $service_depot_id,
            'rf_id' => $rf_id,
            'group_name' => $upline_details->group_name,
            'group_id' => $upline_details->group_id,
			'beneficiary1' => $beneficiary1,
			'beneficiary2' => $beneficiary2,
			'service_depot'=>$service_depot_id,
			'completed_timestamp'=>$current_timestamp,
			'registration_type'=>$registration_type
		);

		$this->members_model->insert_member($data);
		
		// get generated_member_id
		$new_member_id = $this->members_model->insert_id();

		//MEMBER DETAILS LOGGING
		$details_after = array('id' => 0, 'details' => $data);
		$details_after = json_encode($details_after);
		$new_member_log_data = array(
			'member_id' => 0, //no member id given yet	
			'module_name' => 'MEMBER REGISTRATION',
			'table_name' => 'cm_members',
			'action' => 'ADD',
			'details_before' => "",
			'details_after' => $details_after,
			'remarks' => "",
		);
		$this->tracking_model->insert_logs('members', $new_member_log_data);
		//END LOGGING
		
		// update card
		//$data = array("status"=>"USED","member_id"=>$new_member_id,"used_timestamp"=>$current_timestamp);
		//$this->cards_model->update_sp_card($data,"card_id = '{$account_id}'");

		// insert details to cm_member_user_accounts
		$data = array(
			'member_id' => $new_member_id,
           	'username' => $username,
			'password' => md5(strtoupper($password1)),
			'orig_password' => $password1,
			'primary_account_id' =>  $account_id,
			'email' => $primary_email
		);
		$this->members_model->insert_member_user_accounts($data);
		
		$new_member_user_account_id = $this->members_model->insert_id();
		
		//MEMBER USER ACCOUNT LOGGING
		$details_after = array('id' => 0, 'details' => $data);
		$details_after = json_encode($details_after);
		$new_member_log_data = array(
			'member_id' => $new_member_id,
			'module_name' => 'MEMBER REGISTRATION',
			'table_name' => 'cm_member_user_accounts',
			'action' => 'ADD',
			'details_before' => "",
			'details_after' => $details_after,
			'remarks' => "",
		);
		$this->tracking_model->insert_logs('members', $new_member_log_data);
		//END LOGGING
		
		//create member_account and credit referrals and points if not CD or FS
		$node_type = ($registration_type == "INDIVIDUAL")?"REGULAR":"CORPO";
		$params = array(
			"account_id"=>$account_id,
			"account_code"=>$account_code,
			"sponsor_id"=>$sponsor_id,
			"upline_id"=>$upline_id,
			"member_id"=>$new_member_id,
			"member_user_account_id"=>$new_member_user_account_id,
			"position"=>$position,
			"card_type"=>$card->type,			
			"node_type"=>$node_type
		);
		$this->create_member_account($params);

        $timestamp = date('Y-m-d H:i:s');
        $generated_email_code = md5($new_member_id . $timestamp);

        $data = array(
            'member_id' => $new_member_id,
            'mobile_code' => $generated_email_code,
            'email_code' => $generated_email_code,
            'insert_timestamp' => $timestamp,
            'mobile_verification_timestamp' => '0000-00-00 00:00:00'
        );
        
        // check if member got a existing email verificaiton
        $result = $this->members_model->get_member_verification(array('member_id' => $new_member_id));
        if (count($result) > 0) {
            $this->members_model->update_member_verification($data, array('member_id' => $new_member_id));
        } else {
            $this->members_model->insert_member_verification($data);
        }

        $base_url = $this->config->item('base_url') . "/members/verification/index/email/{$generated_email_code}";


        // send email
		//set params needed for notification
        $params = array(
            "first_name"=>ucfirst($first_name),
            "last_name"=>ucfirst($last_name),
            "link"=>$base_url,
            "generated_email_code"=>$generated_email_code,
            "account_id"=>$account_id,
            "sponsor_id"=>$sponsor_id,
            "upline_id"=>$upline_id,
            "position"=>strtoupper($position)
        );

		// process entry for raffle/promo
		$this->raffles_model->raffle_process('sp_encoding', 'default', $account_id, $account_id);

        $data = array(
            "email"=>$primary_email,
            "type"=>"welcome_email",
            "params"=>$params
        );

        //send email to user
        Modules::run('jobs/notifications/send_email',$data);

        $data = array(
            "member_id"=>$new_member_id,
            "mobile_number"=>$mobile_number,
            "type"=>"welcome-message-sms",
            "params"=>$params
        );

        //send sms to user
        Modules::run('jobs/notifications/send_sms',$data);

        echo json_encode(array("status"=>1,"message"=>"Successful Registration"));
        return;	
	}
	
	public function check_reg_details() {
		$data['message'] = "Fields are valid.";
	    $data['status'] = 1;
		
		$username = trim($this->input->post('username'));
	
        $data['message'] = "Fields are valid.";
        $data['status'] = 1;

        // =================
		// name checking
		// =================
		$fname = trim($this->input->post('fname'));
		$lname = trim($this->input->post('lname'));
		$mname = trim($this->input->post('mname'));
		$concat_name = strtoupper($lname . $fname . $mname);
		$members = $this->members_model->get_members('CONCAT(last_name,first_name,middle_name) = "' . $concat_name . '"');
		if(count($members) > 0)
		{
			$data['message'] = "Name <strong>'" . strtoupper($fname) . " " . strtoupper($mname) . " " . strtoupper($lname) . "'</strong> is already used.";
            $data['status'] = 0;
            echo json_encode($data);
            return;
		}
        
        // =================
		// username checking
		// =================
        if($username==""){
            $data['message'] = "Username is required.";
            $data['status'] = 0;
            echo json_encode($data);
            return;
        }
        $is_username_valid = check_username($username);

		
		if (!$is_username_valid) {
            $data['message'] = "Username '<b>{$username}</b>' has invalid characters. Username should only consist of alphanumeric characters.";
            $data['status'] = 0;
            echo json_encode($data);
            return;
		}
		
		// reserved for reserved username checking
        //$pipe_delimited_username = $this->settings["username_reserved_words"];
        //$array_username = explode("|",$pipe_delimited_username);        
        //if ($is_username_valid) {
        //    //checking of restricted usernames
        //    foreach ($array_username as $au) {
        //        if ($username == $au) {
        //            $data['message'] = "You cannot use that username. Please choose another.";
        //            $data['status'] = 0;
        //            break;
        //        }
        //    }
        //    if ($data['status'] == 0) {
        //        echo json_encode($data);
        //        return;                    
        //    }
    	//}

        if ($is_username_valid) {
   	    	// username has no invalid characters
	        $count_users = 0;
	       
	        $count_users = $this->members_model->get_member_user_accounts_count("username = '{$username}'");
	        // if (count($a_user) > 0) {
	        if ($count_users > 0) {
				$data['message'] = "That username is already in use.";
	               $data['status'] = 0;
	               echo json_encode($data);
	               return;                                    
			}
       	}        
        
		// ==============
		// email checking
		// ==============	
	    $email = trim($this->input->post('email'));
        if($email==""){
            $data['message'] = "Email is required.";
            $data['status'] = 0;
            echo json_encode($data);
            return;
        }

		// 20121221 - check if valid email format using ci validation
		/*
		if ($this->email->valid_email($email)) {
			 $data['message'] = "Invalid Email.";
            $data['status'] = 0;
            echo json_encode($data);
            return;
		}
		*/
		
        $count_users = $this->members_model->get_members_count("email = '{$email}'");
        
		if ($count_users > 0) {
            $data['message'] = "That email is already in use.";
            $data['status'] = 0;
            echo json_encode($data);
            return;
        }
      
		// ======================
		// mobile number checking
		// ======================
        $country_code = trim($this->input->post('country_code'));
        $area_code = trim($this->input->post('area_code'));
        $mobile_number = trim($this->input->post('mobile_number'));
        $new_mobile_number = json_encode(array('country_code' => $country_code, 'area_code' => $area_code, 'number' => $mobile_number));
        $count_user = $this->members_model->get_members_count("mobile_number = '{$new_mobile_number}'");
       	
		// added checking 
		$user_mobilenum_63 = $country_code . $area_code . $mobile_number; 
		$user_mobilenum_0 = "0" . $area_code . $mobile_number; 		
		$count_user_reg = $this->members_model->get_members_count("mobile_number IN ('{$user_mobilenum_63}', {$user_mobilenum_0})");
		

        if (($count_users > 0) || ($count_user_reg > 0)) {
            $data['message'] = "That mobile number is already in use.";
            $data['status'] = 0;
            echo json_encode($data);
            return;
        }

		// ===================
		// sponsor id checking
		// ===================
		$sponsor_id = trim($this->input->post('sponsor_id'));

		if($sponsor_id == ""){           
			$data = array(
				'message' => "Sponsor ID is required.",
				'status' => 0
			);

    		echo json_encode($data);
            return;

		} else {
			$account_details = $this->members_model->get_member_account_by_account_id($sponsor_id);

			if (count($account_details) <= 0) {
				$data['message'] = "Sponsor ID not found.";
	            $data['status'] = 0;
	            echo json_encode($data);
	            return;
			}
		}
		
		// ===============================
		// upline_id and position checking
		// ===============================
		$upline_id = trim($this->input->post('upline_id'));
		$position = trim($this->input->post('position'));

		if($upline_id==""){
            $data['message'] = "Upline ID is required.";
            $data['status'] = 0;
            echo json_encode($data);
            return;
        } else {
			$account_details = $this->members_model->get_member_account_by_account_id($upline_id);

			if (count($account_details) > 0) {				
				// check what side is available for the declared upline
				$upline_node_address_length = strlen($account_details->node_address);
				
				//if ($position == "L") {
                if ($position == "left") {
					$desired_upline_node_address = $account_details->node_address . "2"; 
				} else {
					// right position
					$desired_upline_node_address = $account_details->node_address . "1"; 
				}

				// check if position is available
				$desired_upline_node_count = $this->members_model->get_member_accounts_count("node_address = '{$desired_upline_node_address}'");
			
				if ($desired_upline_node_count > 0) {
					// position not available
					$data['message'] = "Position/Side already taken.";
			        $data['status'] = 0;
			        echo json_encode($data);
			        return;
				}
			
			} else {
				// upline id not found
				$data['message'] = "Upline ID not found.";
		        $data['status'] = 0;
		        echo json_encode($data);
		        return;
			}
		}


        $data['html'] = $return_html =  $this->load->view("main/registration/confirm", null, true);
        	
        echo json_encode($data);
        return;
    }


	public function check_username()
	{    
        $username = trim($this->input->post('username'));
        if($username==""){
            echo json_encode(array("status"=>0,"message"=>"Username is required"));
            return;
        }

        $is_username_valid = check_username($username);
		if (!$is_username_valid) {
            $message = "Username invalid";
            echo json_encode(array("status"=>0,"message"=>$message));
            return;
		}
		
		// reserved for reserved usernames
        //$pipe_delimited_username = $this->settings["username_reserved_words"];
        //$array_username = explode("|",$pipe_delimited_username);        
        //if ($is_username_valid) {
        //    //checking of restricted usernames
        //    foreach ($array_username as $au) {
        //        if ($username == $au) {
        //            $data['message'] = "You cannot use that username. Please choose another.";
        //            $data['status'] = 0;
        //            break;
        //        }
        //    }
        //    if ($data['status'] == 0) {
        //        echo json_encode($data);
        //        return;                    
        //    }
    	//}

        if ($is_username_valid) {
            	// username has no invalid characters
                $count_users = 0;
                $count_users = $this->members_model->get_member_user_accounts_count("username = '{$username}'");

                if ($count_users > 0) {
                    echo json_encode(array("status"=>0,"message"=>"Username not available"));
                    return;                                    
				}
       	}        

        echo json_encode(array("status"=>1,"message"=>"Success"));
        return;
    }

	
	public function check_sponsor_id(){
        
   		$sponsor_id = trim($this->input->post('sponsor_id'));
		$upline_id = trim($this->input->post('upline_id'));
		$from_submit = trim($this->input->post('from_submit'));
        
		if($sponsor_id == "")
		{           
    		echo json_encode(array('status'=>0,'html'=>"Sponsor is required"));
            return;
        } 
		
		$sponsor = $this->members_model->get_member_account_by_account_id($sponsor_id);
		
		
		if (empty($sponsor))
		{
			echo json_encode(array('status'=>0,'html'=>"Invalid Sponsor ID"));
			return;
		} 
		
		// check if upline_id is the same line with upline
		$upline_details = $this->members_model->get_member_account_by_account_id($upline_id);
		
		// get details of account
		$member_details = $this->members_model->get_member_by_id($sponsor->member_id);
		
		$proper_name = $member_details->last_name . ", " . $member_details->first_name . " " . $member_details->middle_name; 
		
		$image_filename = "male.jpg";
		if (empty($member_details->image_filename) || ($member_details->image_filename == NULL) || (trim($member_details->image_filename) == "")) {
			// check gender of member
			if (trim($member_details->sex) == "F") {
				$image_filename = "female.jpg";
			} else {
				$image_filename = "male.jpg";
			}
		} else {
			$image_filename = $member_details->image_filename;
		}
	
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
									<td><label class=''>{$sponsor_id}</label></td>		
								</tr>
								<tr>
									<td style='width:80px;'><label><strong>Name</strong></label></td>
									<td><label class=''>{$proper_name}</label></td>		
								</tr>
								<tr>
									<td style='width:80px;'><label><strong>Group Name</strong></label></td>
									<td><label class=''>{$member_details->group_name}</label></td>
								</tr>						
							</tbody>
						</table>	
					</div>
				</div>";
			
		$is_valid = 1;
				
		echo json_encode(array('status'=>1,'html'=>$html,'valid_sponsor'=>$is_valid));
		
		return;
    }
	
	public function check_upline_id(){
        
   		$upline_id = trim($this->input->post('upline_id'));
   		$check_from = ($this->input->post('check_from'))?trim($this->input->post('check_from')):"";
        
		if($upline_id == "")
		{      
			echo json_encode(array('status'=>0,'html'=>"Upline ID is required", "is_valid_upline"=>0));
            return;
        }

		$upline = $this->members_model->get_member_account_by_account_id($upline_id);

		if (empty($upline))
		{
			echo json_encode(array('status'=>0,'html'=>"Invalid Upline ID", "is_valid_upline"=>0));
		    return;
		}
		
		if($check_from == "members-account") {
			// get members account sort by node_address asc
			$where = array(
				"member_id"=>$this->member->member_id			
			);				
			$member_base_account_details = $this->members_model->get_member_accounts($where,null,"node_address ASC");
			$node_address_length = array();
			foreach($member_base_account_details as $key => $m)
			{
				$node_address_length[$key] = strlen($m->node_address);
			}
			array_multisort($node_address_length,SORT_ASC,$member_base_account_details);
			$member_base_account_details = $member_base_account_details[0];
			

			$tmp_node_address = $upline->node_address;
			if(strlen($upline->node_address) > strlen($member_base_account_details->node_address)) {
				$upline->node_address = substr($upline->node_address, 0, strlen($member_base_account_details->node_address)-1);
			}
			$pos = strpos($member_base_account_details->node_address, $upline->node_address);

			if (($pos === false) || ($pos < 0)) {
				echo json_encode(array('status'=>0,'html'=>"Upline ID is not inline with your account", "is_valid_upline"=>0));
		    	return;
			}
			$upline->node_address = $tmp_node_address;
		}
		
		// get details of account
		$member_details = $this->members_model->get_member_by_id($upline->member_id);
		
		// upline name
		$proper_name = $member_details->last_name . ", " . $member_details->first_name . " " . $member_details->middle_name; 
		
		// check what side is available for the declared upline
		$upline_node_address_length = strlen($upline->node_address);
		
		$upline_left_node_address = $upline->node_address . "2"; 
		$upline_right_node_address = $upline->node_address . "1"; 
		
		// check if left and right nodes are available
		$upline_left_node_count = $this->members_model->get_member_accounts_count("node_address = '{$upline_left_node_address}'");
		$upline_right_node_count = $this->members_model->get_member_accounts_count("node_address = '{$upline_right_node_address}'");
						
		if (($upline_left_node_count > 0) && ($upline_right_node_count > 0)) 
		{
			$side_value = "n/a";
		} 
		else if (($upline_left_node_count <= 0) && ($upline_right_node_count > 0)) 
		{
			$side_value = "left";
		} 
		else if (($upline_left_node_count > 0) && ($upline_right_node_count <= 0)) 
		{
			$side_value = "right";
		} 
		else 
		{
			$side_value = "both";
		}

		// email
		if (empty($member_details->email) || ($member_details->email == "")) 
		{
			$member_email = "N/A";
		} 
		else 
		{
			$member_email = $member_details->email;
		}
		
		
		$image_filename = "male.jpg";
		if (empty($member_details->image_filename) || ($member_details->image_filename == NULL) || (trim($member_details->image_filename) == "")) {
			// check gender of member
			if (trim($member_details->sex) == "F") {
				$image_filename = "female.jpg";
			} else {
				$image_filename = "male.jpg";
			}
		} else {
			$image_filename = $member_details->image_filename;
		}
		
		
		if ($side_value == "n/a") 
		{
			$position_html = "<label>N/A</label>";
		} 
		else if (($side_value == "left") || ($side_value == "right")) 
		{
			$position_html = form_dropdown('position',array('left' => 'Left', 'right' => 'Right'),$side_value,"id='available_position' disabled='disabled'");
		} 
		else 
		{
			$position_html = form_dropdown('position',array('left' => 'Left', 'right' => 'Right'),$side_value,"id='available_position'");
		}

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
									<td><label class=''>{$upline_id}</label></td>		
								</tr>
								<tr>
									<td style='width:80px;'><label><strong>Name</strong></label></td>
									<td><label class=''>{$proper_name}</label></td>		
								</tr>
								<tr>
									<td style='width:80px;'><label><strong>Group Name</strong></label></td>
									<td><label id='upline_id_group_name'>{$member_details->group_name}</label></td>
								</tr>		
								<tr>
									<td style='width:80px;'><label><strong>Avaliable Position/s</strong></label></td>
									<td>{$position_html}</td>																
								</tr>				
							</tbody>
						</table>	
					</div>
				</div>";

		echo json_encode(array('status'=>1,'html'=>$html, 'side_value'=>$side_value, "is_valid_upline"=>1, 'group_name'=>$member_details->group_name));
		return;
				
    }

	public function check_account_id()
	{
		$account_id = trim($this->input->post("account_id"));
		$account_code = trim($this->input->post("account_code"));
		
		//cards model
		$this->load->model("cards_model");
		
		// check account from cards 
		$card = $this->cards_model->get_sp_card_by_card_id($account_id);
		
		//empty
		if (empty($card))
		{
			echo json_encode(array('status'=>0,'html'=>"Invalid Account ID"));
			return;			
		} 

		// check if card has UPGRADE modifier
		$modifiers = $this->cards_model->get_modifiers_by_card_type($card->type);
		if(in_array("UPGRADE", $modifiers))
		{
			echo json_encode(array('status'=>0,'html'=>"Upgrade Cards cannot be used here."));
			return;
		}

		// check if card is active
		if ($card->status!='ACTIVE') {
			echo json_encode(array('status'=>0,'html'=>"Invalid Account ID"));
			return;			
		} else {
			// valid account_id, check if code is correct
			if ($account_code!==$card->card_code) {
				echo json_encode(array('status'=>-1,'html'=>"Invalid Account Code"));
				return;						
			}
		} 
	
		
		echo json_encode(array('status'=>1,'html'=>"Valid"));
		return;		
	}
	
	
	public function create_member_account($params=array())
	{
		// reusable function to create new account
		$account_id = $params["account_id"];
		$account_code = $params["account_code"];
		$sponsor_id = $params["sponsor_id"];
		$upline_id = $params["upline_id"];
		$member_id = $params["member_id"];
		$member_user_account_id = $params["member_user_account_id"];
		$position = $params["position"];
		$card_type = $params["card_type"];
		$node_type = $params["node_type"];
		
		// current_timestamp
		$current_timestamp = date("Y-m-d H:i:s");
		
		$this->load->model('cards_model');
		
		//get details before updating
		$sp_card_details = $this->cards_model->get_sp_card_by_card_id($account_id);
		$sp_card_details_before = array("status"=>$sp_card_details->status,"member_id"=>$sp_card_details->member_id,"used_timestamp"=>$sp_card_details->used_timestamp);
		$details_before = array('id' => $account_id, 'details' => $sp_card_details_before);
		$details_before = json_encode($details_before);
		
		// update card
		$data = array("status"=>"USED","member_id"=>$member_id,"used_timestamp"=>$current_timestamp);
		$this->cards_model->update_sp_card($data,"card_id = '{$account_id}'");
		
		//================================
		//LOGGING FOR UPDATING SP CARD
		//================================
		$details_after = array('id' => $account_id, 'details' => $data);
		$details_after = json_encode($details_after);
		$update_sp_card_logs = array(
			'member_id' => $member_id,
			'module_name' => "CREATE MEMBER ACCOUNT",
			'table_name' => "is_sp_cards",
			'action' => "UPDATE",
			'details_before' => $details_before,
			'details_after' => $details_after,
			'remarks' => ""
		);
		$this->tracking_model->insert_logs('members', $update_sp_card_logs);
		//END LOGGING
		
		// check upline and side and create the accounts node_address
		$side = $position == "right" ? 1 : 2;
		$upline = $this->members_model->get_member_account_by_account_id($upline_id);		
		$node_address = $upline->node_address . $side;
		
		// check sponsor and no of downlines and create the uni_node address
		$sponsor = $this->members_model->get_member_account_by_account_id($sponsor_id);
		$no_of_unilevel_downlines = $this->members_model->get_member_accounts_count("sponsor_id = {$sponsor_id}");
		$my_uni_node_level = $no_of_unilevel_downlines + 1;		
		$uni_node = $sponsor->uni_node . "." . $my_uni_node_level;
		
		// get account type
		$account_type = $this->members_model->get_member_account_type_by_type($card_type);

		// insert details to cm_member_accounts
		$data = array(
			"member_id"=>$member_id,
			"member_user_account_id"=>$member_user_account_id,
			"account_id"=>$account_id,
			"sponsor_id"=>$sponsor_id,
			"upline_id"=>$upline_id,
			"account_type_id"=>$account_type->account_type_id,
			"account_status_id"=>1,
			"node_address"=>$node_address,
			"uni_node"=>$uni_node,
			"node_type"=>$node_type
		);		
		$this->members_model->insert_member_accounts($data);
		
		$insert_id = $this->members_model->insert_id();
		
		//MEMBER ACCOUNT LOGGING
		$details_after = array('id' => 0, 'details' => $data);
		$details_after = json_encode($details_after);
		$new_member_account_log_data = array(
			'member_id' => $member_id,
			'module_name' => 'ADD NEW ACCOUNT',
			'table_name' => 'cm_member_accounts',
			'action' => 'ADD',
			'details_before' => "",
			'details_after' => $details_after,
			'remarks' => "",
		);
										
		$this->tracking_model->insert_logs('members', $new_member_account_log_data);
		//END LOGGING
		
		// insert details to cm_member_earnings
		$data = array(
			"member_id"=>$member_id,
			"account_id"=>$account_id
		);		
		$this->members_model->insert_member_earnings($data);
		
		//MEMBER ACCOUNT EARNINGS LOGGING
		$details_after = array('id' => 0, 'details' => $data);
		$details_after = json_encode($details_after);
		$new_member_account_earnings_log_data = array(
			'member_id' => $member_id,
			'module_name' => 'ADD NEW ACCOUNT MEMBER EARNINGS',
			'table_name' => 'cm_member_earnings',
			'action' => 'ADD',
			'details_before' => "",
			'details_after' => $details_after,
			'remarks' => "",
		);
										
		$this->tracking_model->insert_logs('members', $new_member_account_earnings_log_data);
		//END LOGGING
		
		//check new account, if account_type_id = FS and CD do nothing, no points will be generated
		
		if ($card_type=='FS' || $card_type=='CD')
		{
			// do nothing, no points will be generated
			return;
		} 
		
		// otherwise credit referral bonus and credit points starting from upline
		
				
		
		// credit points and check for pairs
		$sps = array("RG","CD","FS","SP","UP","PP");  //SP, CD, FS, TP - starter packs
		$vps = array("MF","VP"); //VP - value packs
		
		if (in_array($card_type,$sps))
		{
			$_type = "sp";
			$points = $this->settings->sp_points;
		}
		else if (in_array($card_type,$vps))
		{
			$_type = "vp";			
			$points = $this->settings->vp_points;
		}
		else if ($card_type=='TP')
		{
			$_type = "tp";
			$points = $this->settings->tp_points;
		}
		else
		{
			$_type = "sp";
			$points = $this->settings->sp_points;
		}
		
		//credit referral
		$params = array("card_id"=>$account_id,"account_id"=>$sponsor_id,"type" => $_type);
		Modules::run('jobs/commissions/credit_referral_bonus',$params);

		$params = array(
			"card_id"=>$account_id,
			"account_id"=>$upline_id,
			"type"=>$_type,
			"position"=>$position,
			"points"=>$points,
		);		
		Modules::run('jobs/commissions/credit_points',$params);			
		return;
	}
	
	function finish() {		
	    $confirmation_code = trim($this->uri->segment(4));	   

        $html_message = "You are now a part of the Vital-C Family. Thank you!";

        $this->template->html_message = $html_message;
		$this->template->confirmation_code = $confirmation_code;
		$this->template->view('registration/finish');
	
	}	
	
}

