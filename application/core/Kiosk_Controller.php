<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

class Kiosk_Controller extends Base_Controller 
{
	public function __construct()
	{
		parent::__construct();
		$this->template->theme = 'kiosk';
		$this->template->name = 'main';
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