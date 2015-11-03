<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Password extends Base_Controller 
{
	
	function __construct() 
	{
  		parent::__construct();
		$this->load->model('tracking_model');
		$this->load->model('asset_model');
		$this->load->model('setting_model');
	}
	
	public function index($test) 
	{ 		
		var_dump($test);
		$this->change();		
    }

    public function change() 
    {
    	$this->template->current_page = 'change_password'; 
    	$this->template->view('dashboard');	    
    }
}    