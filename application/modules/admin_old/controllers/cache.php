<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cache extends Systems_Controller 
{
	function __construct()
	{
		parent::__construct();
		$this->set_navigation('cache');
	}
		
	public function index() 
	{
		
		$this->template->view('systems/cache');
	}
	
	public function clear()
	{
		// clear all cached data
		$this->memcachedlib->flush();
		
		$this->return_json("ok","Cache cleared.");
		return;
	}

}