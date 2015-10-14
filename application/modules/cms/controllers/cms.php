<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cms extends CMS_Controller 
{	
	public function __construct()
	{
		parent::__construct();
	}
	
	public function index() 
	{
		$this->set_navigation('dashboard');
		$this->template->view('dashboard');
	}
}
