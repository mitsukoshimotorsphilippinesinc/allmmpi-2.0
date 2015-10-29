<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Pdfjs_test extends Base_Controller {

	function __construct()
	{
		parent::__construct();
	
		$this->db = $this->load->database('default', TRUE);
	}

	public function index()
	{
		
		$this->template->view('pdfjs_test_view');
	}


}