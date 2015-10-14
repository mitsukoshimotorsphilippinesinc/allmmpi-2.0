<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Sales extends Site_Controller 
{	
	function __construct() 
	{
  		parent::__construct();
	}
	
	public function index() 
	{ 
            redirect('/members/sales/encode');
            return;
	}
        public function encode()
        {
            $this->template->view('dashboard');
        }        
}