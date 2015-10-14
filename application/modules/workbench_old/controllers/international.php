<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class International extends  Base_Controller
{
	
	
	function __construct() 
	{
  		parent::__construct();

		
	}

	public function index() 
	{
		$this->template->view('international_view');
	}
}
?>