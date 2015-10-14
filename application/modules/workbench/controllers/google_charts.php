<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Google_charts extends Base_Controller {

	function __construct()
	{
		parent::__construct();

	}

	public function index()
	{
		
		$this->template->view('google_charts_test');
	}
}	