<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Grow extends  Base_Controller
{
	
	
	function __construct() 
	{
  		parent::__construct();

		
	}

	public function index() 
	{
		$this->template->view('packages_view');
	}

}
?>