<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class SignIn extends Base_Controller 
{
	
	function __construct() 
	{
  		parent::__construct();
		$this->load->model('human_relations_model');
		$this->load->model('setting_model');
		$this->load->helper('systems');
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

				//logging of action
				$details_before = "";
				
				$data = array(
					"username" => $username,
					"password" => $password,
					);
				
				$details_after = array('id_number' => $user_details[0]->id_number, 'details' => $data);

				log_to_db("user", $user_details[0]->id_number, "SIGNIN", "sa_user", "LOGIN", $details_before, $details_after);

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
				
		
		//$this->user_model->insert_log('user', $add_result_log_data);

		// assign data to be sent to view
		$this->template->invalid_login = $invalid_login;
		$this->template->active_member = $active_member;
		
		$this->template->view('signin');
		
	}

	public function proceed_password_change1()
    {

    	$new_password = $this->input->post("new_password");
    	$id_number = $this->input->post("id_number");

    	$hash = md5($id_number.time()); 

    	$hash = hash_hmac('md5',$id_number.time(),$this->config->item('encryption_key'));
					
		$user_verification = $this->user_model->get_user_verification_by_id_number($id_number);

		if(empty($user_verification)){
			$data = array(
	            'id_number' => $id_number,
	            'change_password_code' => $hash
	        );
			$this->user_model->insert_user_verification($data);			
		} else {
			$this->user_model->update_user_verification(array('change_password_code'=>$hash), array('id_number'=>$id_number));
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

	
	public function proceed_password_change(){
		if ($this->authenticate->e_is_logged_in())
		{
			redirect('/employee');
			return;
		}
		
		$new_password = $this->input->get_post('new_password');
		$id_number = $this->input->get_post('id_number');
		$user_not_found = false;
		$email_sent = false;
		$send_notification = false;

		if($_POST){
			
			$this->form_validation->set_rules('new_password', 'new_password', 'trim|required');
			$this->form_validation->set_error_delimiters('<span>', '</span>');

			if ($this->form_validation->run()) 
			{				
				$this->load->model('user_model');
				$this->load->model('human_relations_model');

				$user = $this->human_relations_model->get_employment_information_view_by_id($id_number);
				if(count($user)==0){
					$user_not_found = true;
				}else{
					
					$hash = md5($user->id_number.time());
					
					$hash = hash_hmac('md5',$user->id_number.time(),$this->config->item('encryption_key'));
					
					$user_verification = $this->user_model->get_user_verification(array('id_number'=>$user->id_number));
					
					if(count($user_verification) > 0){
						$this->user_model->update_user_verification(array('change_password_code'=>$hash, 'change_password_original' => $new_password),array('id_number'=>$user->id_number));
					}else{
						$data = array(
				            'id_number' => $user->id_number,
				            'change_password_code' => $hash,
				            'change_password_original' => $new_password
				        );
						$this->user_model->insert_user_verification($data);
					}
					
					$url = $this->config->item('base_url') . '/employee/signin/change_password/'.$user->id_number.'/'.$hash;
					
					// send email
					$email_params = array(
						"first_name"=>$user->first_name,
						"url"=>$url
					);
					
					$email_data = array(
						"email"=>$user->company_email_address,
						"type"=>"change-password-request-email",
						"params"=>$email_params
					);

					$send_notification = true;
					$email_sent = true;
				}
				
			}
		}
		
		
		$this->template->user_not_found = $user_not_found;
		$this->template->email_sent = $email_sent;

		$this->template->view('change_password_notify');
		
		if($send_notification){
			Modules::run('jobs/notifications/send_email',$email_data);
			//Modules::run('jobs/notifications/send_sms',$sms_data);
		}
		
	}
	
	public function change_password(){
		$id_number = abs($this->uri->segment(4));
		$hash = trim($this->uri->segment(5));
		
		$error_found = false;
		$error_message = "";
		$send_notification = false;
		
		if($id_number == 0){
			$error_found = true;
			$error_message = "Error Code 001: Invalid url.";
		}elseif(strlen($hash)==0){
			$error_found = true;
			$error_message = "Error Code 002: Invalid url.";
		}else{
			$this->load->model('user_model');
			$user_verification = $this->user_model->get_user_verification_by_id_number($id_number);
			
			if(empty($user_verification)){
				$error_found = true;
				$error_message = "Error Code 003: Invalid url.";
			}else{
				
				if($hash != $user_verification->change_password_code){
					$error_found = true;
					$error_message = "Error Code 005: Invalid url.";
				}else{
					// update password
					
					$data_user = array(
						'password' => md5(strtoupper($user_verification->change_password_original)),
						'original_password' => $user_verification->change_password_original,
						'is_password_changed' => 1,

					);
					$this->user_model->update_user($data_user, array('id_number'=>$id_number));
					
					// update member_verification
					$this->user_model->update_user_verification(array('change_password_code'=> "", 'change_password_original'=> ""), array('id_number' => $id_number));
					
					$this->load->model('human_relations_model');
					$employment_info_details = $this->human_relations_model->get_employment_information_view_by_id($id_number);
					
					// send email
					$email_params = array(
						"first_name" => $employment_info_details->first_name,
						"password" => $user_verification->change_password_original
					);
					
					$email_data = array(
						"email" => $employment_info_details->company_email_address,
						"type" => "change-password-success-email",
						"params" => $email_params
					);

					$this->template->email = $employment_info_details->company_email_address;
					$send_notification = true; 

				}
				
			}
		}
		
		$this->template->error_found = $error_found;
		$this->template->error_message = $error_message;
		
		$this->template->view('reset_password');
		
		if($send_notification){
			Modules::run('jobs/notifications/send_email',$email_data);
			//Modules::run('jobs/notifications/send_sms',$sms_data);
		}
		
	}
}
