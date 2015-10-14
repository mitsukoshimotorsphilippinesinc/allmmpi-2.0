<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Noaccess extends Admin_Controller {

	public function before()
	{
		parent::before();
	}

	public function index()
	{
		$this->template->view('dashboard');
	}
	
	
}
