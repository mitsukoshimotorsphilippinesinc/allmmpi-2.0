<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Fileserver_test extends Base_Controller {

	function __construct()
	{
		parent::__construct();
	
		$this->db = $this->load->database('default', TRUE);
	}

	public function index()
	{
		
		$this->template->view('fileservertest');
	}

	
}