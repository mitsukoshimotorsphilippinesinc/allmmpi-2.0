<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Transfers_on_going extends Site_Controller {
	
	function __construct() {
		parent::__construct();				
	}
	
	public function index()
	{	
		$this->template->view('transfers/dashboard_on_going');
	}
	
	
	
}
