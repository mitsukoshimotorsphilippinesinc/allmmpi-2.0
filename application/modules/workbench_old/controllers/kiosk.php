<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Kiosk extends  Kiosk_Controller
{

	function __construct() 
	{
  		parent::__construct();

	}
	
	public function index() 
	{
		$this->template->view('kiosk/home');
	}
	
	public function login()
	{
		$this->template->name = 'basic';
		$this->template->view('kiosk/login');
	}
	public function login_rfid_modal() 
	{
		$this->template->view('kiosk/login_rfid_modal');
	}
	public function products_list() 
	{
		$this->template->view('kiosk/products_list');
	}
	public function product_detail() 
	{
		$this->template->view('kiosk/product_detail');
	}
	public function product_order_modal() 
	{
		$this->template->view('kiosk/product_order_modal');
	}	
	public function orders() 
	{
		$this->template->view('kiosk/orders');
	}
	public function search_results() 
	{
		$this->template->view('kiosk/search_results');
	}
}