<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Account_Vouchers extends Systems_Controller {
	private $_validation_rule = array(
			array(
							'field' => 'product_type_id',
							'label' => 'Product Type',
							'rules' => 'trim|required|numeric'
			),
	);

	function __construct()
	{
		parent::__construct();

		$this->load->model('vouchers_model');
		$this->load->model('items_model');
		// load pager library
		$this->load->library('pager');
		$this->set_navigation('account_vouchers');
	}

	public function index()
	{
		$this->voucher_products();
	}

	public function voucher_products()
	{
		$voucher_type_id = trim($this->input->get("voucher_type_id_filter"));
		
		$search_url = "";
		
		$where = "";
		
		if(!empty($voucher_type_id) && $voucher_type_id != 'all') 
			$where = "voucher_type_id = {$voucher_type_id}";
		else 
			$where = "";
		
		// initialize pagination class
		// set pagination data
		$config = array(
		    'pagination_url' => "/admin/account_vouchers/index/",
		    //'total_items' => $this->vouchers_model->get_account_voucher_type_count($where),
			'total_items' => $this->vouchers_model->get_account_voucher_product_count($where),
		    'per_page' => 10,
		    'uri_segment' => 4,
		);

		$this->pager->set_config($config);
		
		$voucher_products = $this->vouchers_model->get_account_voucher_products($where, array('rows' => $this->pager->per_page, 'offset' => $this->pager->offset),'voucher_product_id DESC');
		
		if(!empty($voucher_products))
		{
			foreach($voucher_products as $k => $vp)
			{
				$voucher_type = $this->vouchers_model->get_account_voucher_type_by_id($vp->voucher_type_id);
				$vp->voucher_type = $voucher_type->name;
				
				$array_items = json_decode($vp->json_products);

				foreach($array_items as $array_items_key => $array_item)
				{
					$product = $this->items_model->get_product_by_id($array_item->product_id);
					if(!empty($product))
						$array_item->product = $product->product_name;
					else
						$array_item->product = "Product not found.";
					$array_items[$array_items_key] = $array_item;
				}
				$vp->array_items = $array_items;
				$vp->json_array_items = json_encode($array_items);
				$voucher_products[$k] = $vp;
			}
		}

		$voucher_types = $this->vouchers_model->get_account_voucher_types("voucher_type_id != 3");
		$get_product_types = $this->items_model->get_product_types("is_package != 1");
		$valid_product_types = array();
		foreach($get_product_types as $p)
			array_push($valid_product_types, $p->product_type_id);
		
		$valid_product_types = implode(',', $valid_product_types);
		
		$product_where = "product_type_id IN ({$valid_product_types})";
		$products = $this->items_model->get_products("{$product_where} AND is_active = 1", "", "product_name");

		$this->template->products = $products;
		$this->template->voucher_types = $voucher_types;
		$this->template->voucher_type_id = $voucher_type_id;
		$this->template->voucher_products = $voucher_products;
		$this->template->search_url = strlen($_SERVER['QUERY_STRING']) > 0 ? '?'.$_SERVER['QUERY_STRING'] : '';
		$this->template->view('account_vouchers/list');
	}
	
	public function add_voucher_product()
	{
		$voucher_type_id = $this->input->post('voucher_type_id');
		$voucher_products = $this->input->post('voucher_products');
		$voucher_product_name = $this->input->post('voucher_product_name');
		$voucher_products_array = array();
		
		if(empty($voucher_type_id) || $voucher_type_id == 0)
		{
			$this->return_json('error', 'Voucher Type not found.');
			return;
		}
		
		if(empty($voucher_products))
		{
			$this->return_json('error', 'Voucher Products not found.');
			return;
		}
		
		foreach($voucher_products as $vp)
		{
			array_push($voucher_products_array, $vp);
		}
		$products_json = json_encode($voucher_products_array);
		
		$data = array(
			'voucher_type_id' => $voucher_type_id,
			'json_products' => $products_json,
			'voucher_product_name' => $voucher_product_name,
			'is_active' => 1,
		);
		$this->vouchers_model->insert_account_voucher_products($data);
		
		$this->return_json('ok', 'Voucher products successfully added.');
		return;
	}
	
	public function edit_voucher_product()
	{
		$voucher_product_id = $this->input->post('voucher_product_id');
		$voucher_products = $this->input->post('voucher_products');
		$voucher_type_id = $this->input->post('voucher_type_id');
		$voucher_product_name = $this->input->post('voucher_product_name');
		$voucher_products_array = array();
		
		if(empty($voucher_product_id) || $voucher_product_id == 0)
		{
			$this->return_json('error', 'Entry not found.');
			return;
		}
		
		if(empty($voucher_type_id) || $voucher_type_id == 0)
		{
			$this->return_json('error', 'Voucher Type not found.');
			return;
		}
		
		if(empty($voucher_products))
		{
			$this->return_json('error', 'Voucher Products not found.');
			return;
		}
		
		foreach($voucher_products as $vp)
		{
			array_push($voucher_products_array, $vp);
		}
		$products_json = json_encode($voucher_products_array);
		
		$data = array(
			'voucher_type_id' => $voucher_type_id,
			'json_products' => $products_json,
			'voucher_product_name' => $voucher_product_name,
		);
		$where = array(
			'voucher_product_id' => $voucher_product_id
		);
		$this->vouchers_model->update_account_voucher_products($data, $where);
		
		$this->return_json('ok', 'Voucher product successfully updated.');
		return;
		
	}
	
	public function deactivate_voucher_product()
	{
		$voucher_product_id = $this->input->post('voucher_product_id');
		
		if(empty($voucher_product_id) || $voucher_product_id == 0)
		{
			$this->return_json('error', 'Voucher Product not found.');
			return;
		}
		
		$data = array(
			'is_active' => 0,
		);
		
		$where = array(
			'voucher_product_id' => $voucher_product_id
		);
		$this->vouchers_model->update_account_voucher_products($data, $where);
		
		$this->return_json('ok', 'Voucher product successfully updated.');
		return;
	}
	
	public function reactivate_voucher_product()
	{
		$voucher_product_id = $this->input->post('voucher_product_id');
		
		if(empty($voucher_product_id) || $voucher_product_id == 0)
		{
			$this->return_json('error', 'Voucher Product not found.');
			return;
		}
		
		$data = array(
			'is_active' => 1,
		);
		
		$where = array(
			'voucher_product_id' => $voucher_product_id
		);
		$this->vouchers_model->update_account_voucher_products($data, $where);
		
		$this->return_json('ok', 'Voucher product successfully updated.');
		return;
	}

}