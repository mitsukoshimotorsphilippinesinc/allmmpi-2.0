<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cod extends Site_Controller 
{
	
	function __construct() 
	{
  		parent::__construct();
		$this->load->model('tracking_model');
		$this->load->model('asset_model');
		$this->load->model('setting_model');

		$this->load->library('pager2');
	}
	
	public function index()
	{
		$this->page();
	}
	
	public function page()
	{							
		$this->template->current_page = 'cod';
		$this->template->view('cod/dashboard');
	}

}
