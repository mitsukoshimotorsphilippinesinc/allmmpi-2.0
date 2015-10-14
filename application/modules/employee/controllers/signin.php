<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class SignIn extends Base_Controller 
{
	
	function __construct() 
	{
  		parent::__construct();
		$this->load->model('human_relations_model');
		$this->load->model('setting_model');
    }
	
	public function index() 
	{
		if ($this->authenticate->e_is_logged_in())
		{
			redirect('/employee');
			return;
		}

		$this->template->view('signin');
	}
	
	public function authenticate()
	{
		
		if ($this->authenticate->e_is_logged_in())
		{
			redirect('/employee');
			return;
		}
		
		$username = $this->input->post('username');
		$password = $this->input->post('password');
				
		$this->form_validation->set_rules('username', 'Username', 'trim|required');
		$this->form_validation->set_rules('password', 'Password', 'trim|required');
		$this->form_validation->set_error_delimiters('<span>', '</span>');

		$invalid_login = false;
		$active_member = 1;
		
		if ($this->form_validation->run()) 
		{		
			if ($this->authenticate->e_login($username, $password))
			{					
				// check the default user page
				//$user_details = $this->user_model->get_user("username = '{$username}' OR email = '{$username}'");
				$user_details = $this->user_model->get_user("username = '{$username}'");
				
				$redirect_url = $user_details[0]->default_page;
							
				redirect('/employee');
				return;
			}
			else
			{
				$invalid_login = true;
			}
		}
		
		// assign data to be sent to view
		$this->template->invalid_login = $invalid_login;
		$this->template->view('signin');
		

		var_dump($invalid_login);

		/*
		// get member status
		$member_user_account_details = $this->members_model->get_member_user_account_by_username($username);
		
		if (empty($member_user_account_details)) {
			$active_member == 0;
			$invalid_login = true;
		} else {						
			$member_details = $this->members_model->get_member_by_id($member_user_account_details->member_id);
			
			if (empty($member_details)) {
				$active_member == 0;
			} else {				
				$active_member = $member_details->is_active;
			}
			
			if ($this->form_validation->run()) 
			{
				
				if ($this->authenticate->login($username, $password))
				{
					if ($active_member == 1) {
						redirect('/members');
						return;
					}
					else
					{
						$this->authenticate->logout();
						$invalid_login = true;
					}
				}
				else
				{
					$invalid_login = true;
				}
			}
		}
		*/

		// assign data to be sent to view
		$this->template->invalid_login = $invalid_login;
		$this->template->active_member = $active_member;
		
		$this->template->view('signin');
		
	}
	
	public function forgot_password(){
		if ($this->authenticate->e_is_logged_in())
		{
			redirect('/members');
			return;
		}
		
		$email = $this->input->get_post('email');
		$user_not_found = false;
		$email_sent = false;
		$send_notification = false;
		
		if($_POST){
			
			$this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');
			$this->form_validation->set_error_delimiters('<span>', '</span>');

			if ($this->form_validation->run()) 
			{				
				$this->load->model('members_model');
				$member = $this->members_model->get_members(array('email'=>$email));
				if(count($member)==0){
					$user_not_found = true;
				}else{
					$member = $member[0];
					
					$hash = md5($member->member_id.time());
					
					$hash = hash_hmac('md5',$member->member_id.time(),$this->config->item('encryption_key'));
					
					$member_verification = $this->members_model->get_member_verification(array('member_id'=>$member->member_id));
					
					if(count($member_verification) > 0){
						$this->members_model->update_member_verification(array('forgot_password_code'=>$hash),array('member_id'=>$member->member_id));
					}else{
						$data = array(
				            'member_id' => $member->member_id,
				            'forgot_password_code' => $hash
				        );
						$this->members_model->insert_member_verification($data);
					}
					
					$url = $this->config->item('base_url') . '/members/signin/reset_password/'.$member->member_id.'/'.$hash;
					
					// send email
					$email_params = array(
						"first_name"=>$member->first_name,
						"url"=>$url
					);
					
					$email_data = array(
						"email"=>$email,
						"type"=>"forgot-password-request-email",
						"params"=>$email_params
					);

					// send sms
					$sms_params = array(
						"first_name"=>$member ->first_name,
						"url"=>$url
					);
					$sms_data = array(
						"member_id"=>$member->member_id,
						"mobile_number"=>$member->mobile_number,
						"type"=>"forgot-password-request-sms",
						"params"=>$sms_params
					);
					
					$send_notification = true;
					$email_sent = true;
				}
				
			}
		}
		
		
		$this->template->user_not_found = $user_not_found;
		$this->template->email_sent = $email_sent;

		$this->template->view('forgot_password');
		// run here
		if($send_notification){
			Modules::run('jobs/notifications/send_email',$email_data);
			Modules::run('jobs/notifications/send_sms',$sms_data);
		}
		
	}
	
	public function reset_password(){
		$member_id = abs($this->uri->segment(4));
		$hash = trim($this->uri->segment(5));
		
		$error_found = false;
		$error_message = "";
		$send_notification = false;
		
		if($member_id == 0){
			$error_found = true;
			$error_message = "Error Code 001: Invalid url.";
		}elseif(strlen($hash)==0){
			$error_found = true;
			$error_message = "Error Code 002: Invalid url.";
		}else{
			$this->load->model('members_model');
			$member_verification = $this->members_model->get_member_verification_by_id($member_id);
			if(empty($member_verification)){
				$error_found = true;
				$error_message = "Error Code 003: Invalid url.";
			}else{
				
				if($member_verification->forgot_password_code==""){
					$error_found = true;
					$error_message = "Error Code 004: Password already reset.";
				}else{
					$member = $this->members_model->get_member_by_id($member_id);
					$member_user_account = $this->members_model->get_member_user_account_by_member_id($member_id);

					if($hash != $member_verification->forgot_password_code){
						$error_found = true;
						$error_message = "Error Code 005: Invalid url.";
					}else{
						// update password
						$new_password = "";
				        for($i=0; $i<6; $i++){
				            $new_password .= chr(rand(65,90));
				        }
						$data_member_user_account = array(
							'password' => md5(strtoupper($new_password)),
							'orig_password' => $new_password
						);
						$this->members_model->update_member_user_accounts($data_member_user_account, array('member_id'=>$member_id));
						
						// update member_verification
						$this->members_model->update_member_verification(array('forgot_password_code'=>""),array('member_id'=>$member_id));
						
						// send email
						$email_params = array(
							"first_name"=>$member->first_name,
							"password"=>$new_password
						);
						$email_data = array(
							"email"=>$member->email,
							"type"=>"forgot-password-success-email",
							"params"=>$email_params
						);

						$this->template->email = $member->email;

						// send sms
						$sms_params = array(
							"first_name"=>$member->first_name,
							"password"=>$new_password
						);
						$sms_data = array(
							"member_id"=>$member->member_id,
							"mobile_number"=>$member->mobile_number,
							"type"=>"forgot-password-success-sms",
							"params"=>$sms_params
						);
						
						$send_notification = true;

					}
				}
				
			}
		}
		
		$this->template->error_found = $error_found;
		$this->template->error_message = $error_message;
		
		$this->template->view('reset_password');
		// run here
		if($send_notification){
			Modules::run('jobs/notifications/send_email',$email_data);
			Modules::run('jobs/notifications/send_sms',$sms_data);
		}
		
	}
}
