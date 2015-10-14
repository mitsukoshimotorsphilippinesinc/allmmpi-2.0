<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Grandmaster_club extends  Base_Controller
{
	
	
	function __construct() 
	{
  		parent::__construct();

		
	}

	public function index() 
	{
		$this->template->view('grandmaster_club_view');
	}
}
?>