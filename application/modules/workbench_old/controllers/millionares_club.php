<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Millionares_club extends  Base_Controller
{
	
	
	function __construct() 
	{
  		parent::__construct();

		
	}

	public function index() 
	{
		$this->template->view('millionares_club_view');
	}
}
?>