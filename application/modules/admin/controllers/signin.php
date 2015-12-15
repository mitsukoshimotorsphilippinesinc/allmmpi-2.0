<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class SignIn extends Admin_Controller 
{
	
	function __construct() 
	{
  		parent::__construct();
		$this->template->name = 'basic';
		$this->load->model('human_relations_model');		
		$this->load->model('user_model');		
		$this->load->helper('systems');
	}
	
	public function index() 
	{ 
		
		$this->template->view('signin');
	}
	
	public function authenticate()
	{	
		
		if ($this->authenticate->is_logged_in())
		{			
			$user = $this->user_model->get_user_by_id($this->user->user_id);
			$redirect_url = $user->default_page;
			redirect($redirect_url);							
			return;
		}
		
		$username = $this->input->post('username');
		$password = $this->input->post('password');
		
		$this->form_validation->set_rules('username', 'Username', 'trim|required');
		$this->form_validation->set_rules('password', 'Password', 'trim|required');
		$this->form_validation->set_error_delimiters('<span>', '</span>');
		
		$invalid_login = false;
		$active_member = 1;

		// get member status
		$employee_account_details = $this->user_model->get_user_by_username($username);

		if (empty($employee_account_details)) {
			$active_member == 0;
			$invalid_login = true;
		} else {
			
			$active_member = $employee_account_details->is_active;
		
			if ($this->form_validation->run()) 
			{
				
				if ($this->authenticate->login($username, $password))
				{	
					if ($active_member == 1) {

						// check the default user page					
						$user_details = $this->user_model->get_user("username = '{$username}'");

						//logging of action
						$details_before = "";
						
						$data = array(
							"username" => $username,
							"password" => $password,
							);
						
						$details_after = array('id_number' => $user_details[0]->id_number, 'details' => $data);
						$id_number = $user_details[0]->id_number;
						
						log_to_db("default", $id_number, "SIGNIN", "sa_user", "LOGIN", $details_before, $details_after, "ADMIN");

						$redirect_url = $user_details[0]->default_page;
									
						redirect($redirect_url);				
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
		
		// assign data to be sent to view
		$this->template->invalid_login = $invalid_login;
		$this->template->active_member = $active_member;
		$this->template->view('signin');
		
	}
	
	public function signout() 
	{
		ci()->session->unset_userdata("selected_facility");
		$this->authenticate->logout();
		redirect('/admin/signin');
		return;
	}

}
