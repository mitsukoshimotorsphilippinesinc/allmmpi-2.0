<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Business_join extends  Base_Controller
{
	
	
	function __construct() 
	{
  		parent::__construct();

		
	}

	public function index() 
	{
		$this->template->view('business_join_view');
	}
}
?>