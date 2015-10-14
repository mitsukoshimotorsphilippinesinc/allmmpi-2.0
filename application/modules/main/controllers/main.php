<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Main extends Base_Controller {

	public function before()
	{
		parent::before();
	}

	public function __construct()
	{
		parent::__construct();
		// load contents model
		$this->load->model("contents_model");
	}
	
	public function index()
	{
		//$this->cache($this->cache_timeout); // implement cache
		
		$this->set_navigation('home');
		$this->template->view('dashboard');		
	}
}