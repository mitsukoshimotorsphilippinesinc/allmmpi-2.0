<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Operations extends Admin_Controller {

	function __construct()
	{
		parent::__construct();
	
		$this->db_operations = $this->load->database('operations', TRUE);	
	}

	public function index()
	{
		
		$this->template->view('dashboard');
	}

}