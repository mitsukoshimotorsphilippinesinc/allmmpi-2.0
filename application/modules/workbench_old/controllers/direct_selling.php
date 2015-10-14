<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Direct_selling extends  Base_Controller
{
	
	
	function __construct() 
	{
  		parent::__construct();

		
	}

	public function index() 
	{
		$this->template->view('direct_selling_view');
	}
}
?>