<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Signout extends Site_Controller 
{
	
	function __construct() 
	{
  		parent::__construct();
	}
	
	public function index() 
	{
		$this->authenticate->logout();
		redirect('/employee/signin');
		return;
	}

}
