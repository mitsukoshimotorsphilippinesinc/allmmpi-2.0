<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Systems extends Systems_Controller 
{
	function __construct()
	{
		parent::__construct();

		// load pager library
		$this->load->library('pager');

		$this->load->model('systems_model');
	}
		
	public function index() 
	{
		$this->set_navigation('dashboard');
		$this->template->view('systems/dashboard');
	}

	public function noaccess() 
	{
		$this->set_navigation('dashboard');
		$this->template->view('systems/noaccess');
	}

}