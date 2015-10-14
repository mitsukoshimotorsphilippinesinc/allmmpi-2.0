<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

class Network_Controller extends Admin_Controller 
{
	public function __construct()
	{
		parent::__construct();
		$this->set_system('network');
		$this->template->name = 'main-fluid';
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