<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class SignIn extends Admin_Controller 
{
	
	function __construct() 
	{
  		parent::__construct();
		$this->template->name = 'basic';
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
		
		if ($this->form_validation->run()) 
		{
			
			if ($this->authenticate->login($username, $password))
			{	
				// check the default user page
				//$user_details = $this->user_model->get_user("username = '{$username}' OR email = '{$username}'");
				$user_details = $this->user_model->get_user("username = '{$username}'");
				
				$redirect_url = $user_details[0]->default_page;
							
				redirect($redirect_url);				
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
		
	}
	
	public function signout() 
	{
		ci()->session->unset_userdata("selected_facility");
		$this->authenticate->logout();
		redirect('/admin/signin');
		return;
	}

}
