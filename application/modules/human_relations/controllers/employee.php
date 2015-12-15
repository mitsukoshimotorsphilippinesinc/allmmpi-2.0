<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Employee extends Admin_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->model('spare_parts_model');
		$this->load->library('pager');	
		$this->load->helper("spare_parts_helper");	
		$this->load->helper("breadcrumb_helper");

	}

	public function index()
	{
		
		$this->template->view('employee/list');
	}
}