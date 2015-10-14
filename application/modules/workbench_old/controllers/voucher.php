<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Voucher extends  Base_Controller
{
	
	
	function __construct() 
	{
  		parent::__construct();

		
	}

	public function index() 
	{
		$this->template->view('voucher_view');
	}
}
?>