<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Achievers extends  Base_Controller
{
	
	
	function __construct() 
	{
  		parent::__construct();

		
	}

	public function index() 
	{
		$this->template->view('achievers_view');
	}
}
?>