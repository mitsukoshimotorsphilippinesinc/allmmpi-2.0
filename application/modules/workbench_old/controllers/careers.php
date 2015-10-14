<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Careers extends  Base_Controller
{
	
	
	function __construct() 
	{
  		parent::__construct();

		
	}

	public function index() 
	{
		$this->template->view('careers_view');
	}
}
?>