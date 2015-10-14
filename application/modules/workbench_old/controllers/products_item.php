<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Products_item extends  Base_Controller
{
	
	
	function __construct() 
	{
  		parent::__construct();
		$this->load->model('items_model');
		
	}

	public function index() 
	{
		$products = $this->items_model->get_products();
	
		$this->template->products = $products;
		$this->template->view('products_item_view');
	}
}
?>