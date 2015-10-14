<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Packages extends Systems_Controller {
	private $_validation_rule = array(
			array(
							'field' => 'product_name_input',
							'label' => 'Product Input',
							'rules' => 'trim|callback__check_product'
			),
			array(
							'field' => 'package_name',
							'label' => 'Package Name',
							'rules' => 'trim|required|callback__check_package'
			),
			array(
							'field' => 'package_description',
							'label' => 'Description',
							'rules' => 'trim'
			),
			array(
							'field' => 'package_type_id',
							'label' => 'Type',
							'rules' => 'trim|required'
			),
			array(
							'field' => 'srp_price',
							'label' => 'Standard Retail Price',
							'rules' => 'trim|required|numeric'
			),
			array(
							'field' => 'cpoints_value',
							'label' => 'C Points Value',
							'rules' => 'trim|numeric'
			),
			array(
							'field' => 'igpsm_points',
							'label' => 'IGPSM Points',
							'rules' => 'trim|required|numeric'
			),
	);

	function __construct()
	{
		parent::__construct();

		$this->load->model('items_model');
		// load pager library
		$this->load->library('pager');
		$this->set_navigation('packages');
	}

	public function index()
	{
		$this->packages();
	}

	public function packages()
	{

		$search_by = trim($this->input->get("search_option"));
		$search_text = trim($this->input->get("search_string"));

		$search_url = "";

		if (($search_text == "") || empty($search_text)) {
			$where = NULL;					
		} else {
			$where = "{$search_by} LIKE LOWER('%{$search_text}%')";
			$search_url = "?search_option=" . $search_by . "&search_string=" . $search_text;
		}
		

		// initialize pagination class
		// set pagination data
		$config = array(
		    'pagination_url' => "/admin/packages/index/",
		    'total_items' => $this->items_model->get_package_count($where),
		    'per_page' => 10,
		    'uri_segment' => 4,
		);
		$this->pager->set_config($config);
		//$packages = $this->items_model->get_packages(NULL, array('rows' => $this->pager->per_page, 'offset' => $this->pager->offset), 'package_name');
		$packages = $this->items_model->get_packages($where, array('rows' => $this->pager->per_page, 'offset' => $this->pager->offset), 'package_name');

		foreach($packages as $k => $v)
		{
			$products = $this->items_model->get_package_product_by_package_id($v->package_id);
			foreach($products as $pr_k => $pr_v)
			{
				$prod = $this->items_model->get_product_by_id($pr_v->product_id);
				$item = $this->items_model->get_item_by_id($prod->item_id);
				$pr_v->product_name = $item->item_name;
				
				$products[$pr_k] = $pr_v;
			}
			$v->products = $products;
			
			$type = $this->items_model->get_package_type_by_id($v->package_type_id);
			$v->package_type = $type->package_type;

			$packages[$k] = $v;

		}
		
		// search vars
		$this->template->search_by = $search_by;
		$this->template->search_text = $search_text;
		$this->template->search_url = $search_url;
		//$this->template->type = $type;
		
		$this->template->packages = $packages;
		$this->template->view('packages/list');
	}

	public function view($navigation_id)
	{
		/*$navigation = $this->navigations_model->get_navigation_by_id($navigation_id);

		$parent = $this->navigations_model->get_navigation_by_id($navigation->parent_id);
		$parent_title = "";
		if(!empty($parent))
		{
			$parent_title = $parent->title;
		}
		else
		{
			$parent_title = "None";
		}
		$navigation->section = $parent_title;
		$this->template->navigation = $navigation;
		$this->template->view('navigations/view');*/
	}

	public function add()
	{
		
		$swap_group = "[]";
		
		if ($_POST)
		{
			$i = 0;


			$product_name = $this->input->post('product_name');
			$is_swappable = $this->input->post('is_swappable');
			$quantity = $this->input->post('quantity');
			$group = $this->input->post('group');
			$group_qty = $this->input->post('group_qty');
			
			$swap_group_array = array();
			
			//set swap group for js variable
			foreach($group as $k => $g)
			{
				if($g == 0) continue;
				$swap_group_array[$g] = $group_qty[$k];
			}
			
			$swap_group = '[,'.implode(",",$swap_group_array).']';
			
			while(isset($product_name[$i]) && isset($is_swappable[$i]))
			{
				$this->_validation_rule[] = array(
						'field' => 'product_name['.$i.']',
						'label' => 'Product',
						'rules' => 'trim|required'
				);
				$this->_validation_rule[] = array(
						'field' => 'is_swappable['.$i.']',
						'label' => 'Swappable',
						'rules' => 'trim|required'
				);
				$this->_validation_rule[] = array(
						'field' => 'quantity['.$i.']',
						'label' => 'Quantity',
						'rules' => 'trim|required'
				);
				$this->_validation_rule[] = array(
						'field' => 'group['.$i.']',
						'label' => 'Group',
						'rules' => 'trim|required'
				);
				$this->_validation_rule[] = array(
						'field' => 'group_qty['.$i.']',
						'label' => 'Group Qty',
						'rules' => 'trim|required'
				);
				
				$i = ++$i;
			}

			// post done here
			$this->form_validation->set_rules($this->_validation_rule);

			if ($this->form_validation->run())
			{
				error_log('valid');

				// insert the new package
				$data = array(
					'package_name' => set_value('package_name'),
					'package_description' => set_value('package_description'),
					'package_type_id' => set_value('package_type_id'),
					'standard_retail_price' => set_value('srp_price'),
					'cpoints_value' => set_value('cpoints_value'),
					'igpsm_points' => set_value('igpsm_points'),
					'image_filename' => json_encode(array("image_count" => 0,"images" => array()))
				);
				$this->items_model->insert_package($data);

				$package_id = $this->items_model->insert_id();

				foreach($product_name as $k => $v)
				{
					
					$data = array(
						'package_id' => $package_id,
						'product_id' => $v,
						'quantity' => $quantity[$k],
						'is_swappable' => $is_swappable[$k],
						'group' => $group[$k],
						'group_qty' => $group_qty[$k]
					);
					$this->items_model->insert_package_product($data);
					
				}

				redirect('/admin/packages');
				return;
			}
		}
		$products = $this->items_model->get_products(null,null,"item_name ASC");

		foreach($products as $k => $v)
		{
			$item = $this->items_model->get_item_by_id($v->item_id);
			$v->product_name = $item->item_name;
			$products[$k] = $v;
		}
		
		$this->template->swap_group = $swap_group;
		$this->template->package_types = $this->items_model->get_package_types();
		$this->template->products = $products;
		$this->template->view('packages/add');
	}

	public function edit($package_id)
	{
		$package = $this->items_model->get_package_by_id($package_id);
		$package_products = $this->items_model->get_package_product_by_package_id($package_id);
		$swap_group = "[]";
		
		if ($_POST && !empty($package))
		{
			$package_products = '';

			$i = 0;


			$product_name = $this->input->post('product_name');
			$is_swappable = $this->input->post('is_swappable');
			$quantity = $this->input->post('quantity');
			$group = $this->input->post('group');
			$group_qty = $this->input->post('group_qty');
			
			$swap_group_array = array();
			
			//set swap group for js variable
			foreach($group as $k => $g)
			{
				if($g == 0) continue;
				$swap_group_array[$g] = $group_qty[$k];
			}
			
			$swap_group = '[,'.implode(",",$swap_group_array).']';
			
			while(isset($product_name[$i]) && isset($is_swappable[$i]))
			{
				$this->_validation_rule[] = array(
						'field' => 'product_name['.$i.']',
						'label' => 'Product',
						'rules' => 'trim|required'
				);
				$this->_validation_rule[] = array(
						'field' => 'is_swappable['.$i.']',
						'label' => 'Swappable',
						'rules' => 'trim|required'
				);
				$this->_validation_rule[] = array(
						'field' => 'quantity['.$i.']',
						'label' => 'Quantity',
						'rules' => 'trim|required'
				);
				$this->_validation_rule[] = array(
						'field' => 'group['.$i.']',
						'label' => 'Group',
						'rules' => 'trim|required'
				);
				$this->_validation_rule[] = array(
						'field' => 'group_qty['.$i.']',
						'label' => 'Group Qty',
						'rules' => 'trim|required'
				);
				$i = ++$i;
			}

			// post done here
			$this->form_validation->set_rules($this->_validation_rule);

			if ($this->form_validation->run())
			{
				// insert the new package
				$data = array(
					'package_name' => set_value('package_name'),
					'package_description' => set_value('package_description'),
					'package_type_id' => set_value('package_type_id'),
					'standard_retail_price' => set_value('srp_price'),
					'cpoints_value' => set_value('cpoints_value'),
					'igpsm_points' => set_value('igpsm_points')
				);
				$this->items_model->update_package($data, array('package_id' => $package_id));

				$this->items_model->delete_package_product(array('package_id' => $package_id));

				foreach($product_name as $k => $v)
				{
					$data = array(
						'package_id' => $package_id,
						'product_id' => $v,
						'quantity' => $quantity[$k],
						'is_swappable' => $is_swappable[$k],
						'group' => $group[$k],
						'group_qty' => $group_qty[$k]
					);
					$this->items_model->insert_package_product($data);
				}

				redirect('/admin/packages');
				return;
			}
		}

		$products = $this->items_model->get_products(null,null,"item_name ASC");

		foreach($products as $k => $v)
		{
			$item = $this->items_model->get_item_by_id($v->item_id);
			$v->product_name = $item->item_name;
			$products[$k] = $v;
		}
		$this->template->swap_group = $swap_group;
		$this->template->package_types = $this->items_model->get_package_types();
		$this->template->package_products = $package_products;
		$this->template->products = $products;
		$this->template->package = $package;
		$this->template->view('packages/edit');
	}

	//public function delete($package_id)
	public function delete()
	{
		$_package_id = $this->input->post('_package_id');	
		$package = $this->items_model->get_package_by_id($_package_id);

		$package_type_details = $this->items_model->get_package_type_by_id($package->package_type_id);

		/*if ($_POST && !empty($package))
		{
			$_package_id = $this->input->post('package_id');
			if (!empty($_package_id)) if ($_package_id == $package_id)
			{
				$this->items_model->delete_package(array('package_id' => $package_id));
				redirect('/admin/packages');
				return;
			}
		}

		$this->template->package = $package;
		$this->template->view('packages/delete');*/
		
		$data = array(
			'package' => $package,
			'package_type_details' => $package_type_details
		);		
		$html = $this->load->view('/packages/delete', $data, TRUE);

		echo json_encode(array("status"=>"1","html"=>$html));
		return;
	}
	
	public function confirm_delete() {
		$_package_id = $this->input->post('_package_id');
	
		$html = "<p><label>Are you sure you want to proceed?</label>									
				</p>";
		
		echo json_encode(array("status"=>"1","html"=>$html));
		return;
		
	}
	
	public function delete_package() {	
		$_package_id = $this->input->post('_package_id');
		$package = $this->items_model->get_package_by_id($_package_id);
		
		if (!empty($_package_id)) {
			$this->items_model->delete_package(array('package_id' => $_package_id));

			foreach($package as $k => $v) {
				$log_data = array(
					'table_name' => 'is_items',
					'table_id' => $package->package_id,
					'field_name' => $k,
					'old_value' => $v,
					'user_id' => $this->user->user_id,
				);

				$this->logs_model->insert_inventory_log($log_data);
			}		
		} else {
			echo json_encode(array("status"=>"0", "message" => "Invalid Package ID"));
			return;
		}
	
		echo json_encode(array("status"=>"1"));
		return;
		
	}

	public function items($package_id)
	{
		$products = $this->items_model->get_products();

		foreach($products as $k => $v)
		{
			$item = $this->items_model->get_item_by_id($v->item_id);
			$v->product_name = $item->item_name;
			$products[$k] = $v;
		}

		$this->template->products = $products;
		$this->template->view('packages/items');
	}

	public function get_items()
	{
		$item_name = $this->input->post('item_name');

		$items = $this->items_model->item_search($item_name);

		$this->return_json("ok","",$items);
	}

	public function _check_package($package_name)
	{
		$package_name = trim($package_name);
		$orig_package_name = trim($this->input->post('orig_package_name'));
		$package = $this->items_model->get_package_by_name($package_name);

		if (!empty($package))
		{
			if($package_name == $orig_package_name)
			{
				return TRUE;
			}
			else
			{
				$this->form_validation->set_message('_check_package', 'This package already exists.');
				return FALSE;
			}
		}
		else
		{
			return TRUE;
		}
	}

	public function _check_product()
	{
		$products = $this->input->post('product_name');
		if(empty($products))
		{
			$this->form_validation->set_message('_check_product', 'Please enter a product.');
			return FALSE;
		}
		else
		{
			return TRUE;
		}
	}
	
	public function gallery()
	{
		$_package_id = $this->input->post('_package_id');
		
		$package = $this->items_model->get_package_by_id($_package_id);
		
		if(empty($package))
		{
			$this->return_json("error","This package does not exist!");
			return;
		}

		$data = array(
			'package' => $package
		);

		$html = $this->load->view('/packages/gallery', $data, TRUE);
		
		$this->return_json("ok","",array("html" => $html));
		return;
	}
	
	public function set_primary(){
		$package_id = abs($this->input->post('package_id'));
		$filename = trim($this->input->post('filename'));
		
		$package = $this->items_model->get_package_by_id($package_id);
		
		if(empty($package))
		{
			$this->return_json("error","This package does not exist!");
			return;
		}
		
		$image_filename = json_decode($package->image_filename);
		$images = $image_filename->images;
		
		foreach($images as $i)
		{
			if($i->filename == $filename) $i->is_main = true;
			else  $i->is_main = false;
		}
		
		$image_filename->images = $images;
		
		$data = array("image_filename" => json_encode($image_filename));
		
		$this->items_model->update_package($data,array("package_id" => $package_id));
		
		$this->return_json("ok","{$filename} set as primary picture.",array("filename" => $filename));
		return;
		
	}
}