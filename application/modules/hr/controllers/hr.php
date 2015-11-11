<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Hr extends Admin_Controller {

	function __construct()
	{
		parent::__construct();
	

	}

	public function index()
	{
		
		$this->template->view('dashboard');
	}
}