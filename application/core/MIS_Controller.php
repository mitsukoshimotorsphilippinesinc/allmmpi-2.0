<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

class MIS_Controller extends Admin_Controller {
	
	public function __construct()
	{
		parent::__construct();
		$this->set_system('mis');
		//$this->template->name = 'admin';
		$this->template->name = 'main-fluid';
		
		$this->load->model(array('systems_model', 'users_model', 'facilities_model','logs_model','members_model','tracking_model', 'items_model'));
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