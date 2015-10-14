<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class RFID extends Admin_Controller
{
	public $selected_facility = null;
	
	function __construct() 
	{
  		parent::__construct();
		$this->template->name = 'main-fluid';
	}
	
	public function index() 
	{
		$this->template->view('rfid');
	}
	
}