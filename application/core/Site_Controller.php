<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

class Site_Controller extends Base_Controller {
	
	public $selected_account = NULL;
	
	public function __construct() 
	{
		parent::__construct();

		$this->template->theme = 'site';
		$this->template->name = 'members';
		$this->template->title = $this->config->item('title');

		// check browser we will not support IE version lower than 9
		if ($this->agent->is_browser('MSIE') && (abs($this->agent->version()) < 9))
		{
			$this->template->name = 'basic';
			$this->template->title = $this->config->item('title') . ' - Browser Check!';
			$this->template->view('browser_check', 'main');
			$this->output->_display();
			exit;
		}

		$segment_uri = $this->uri->uri_string();
		$login_uri = 'employee/signin';
		$is_login_action = stripos($segment_uri, $login_uri) !== FALSE;
		
		// configure authenticate
		$auth_config = array(
			'table' => 'sa_user',
			'field_id' => 'id_number',
			'hash_salt' => 'gbs@admin8896',
			'prefix' => 'sa_'
		);
		$this->authenticate->initialize($auth_config);

		if ($this->authenticate->e_is_logged_in()) {

			ci()->user = $this->user = $this->authenticate->get_user();
			
			$this->load->model('human_relations_model');
			
			$employee = $this->human_relations_model->get_employment_information_view_by_id($this->user->id_number);

			//var_dump($employee);	

			//ci()->member = $this->member = $member;
			ci()->employee = $this->employee = $employee;

		} else {
			if (!$is_login_action) 
				redirect('/employee/signin');
		}		
	}
	
	public function before()
	{
		parent::before();

	}
	
	public function after()
	{
		parent::after();
	}
	
}

// end of php file