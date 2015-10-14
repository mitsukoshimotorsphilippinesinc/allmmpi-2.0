<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class It extends Admin_Controller {

	function __construct()
	{
		parent::__construct();
	
		$this->db_spare_parts = $this->load->database('spare_parts', TRUE);	
	}

	public function index()
	{
		
		$this->template->view('dashboard');
	}

}