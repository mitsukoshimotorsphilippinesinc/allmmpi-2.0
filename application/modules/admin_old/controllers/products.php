<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Products extends Systems_Controller {
	private $_validation_rule = array(
			array(
							'field' => 'product_type_id',
							'label' => 'Product Type',
							'rules' => 'trim|required|numeric'
			),
			array(
							'field' => 'standard_retail_price',
							'label' => 'Standard Retail Price',
							'rules' => 'trim|required|numeric'
			),
			array(
							'field' => 'igpsm_points',
							'label' => 'IGPSM Points',
							'rules' => 'trim|required|numeric'
			),
			array(
							'field' => 'is_active',
							'label' => 'Active',
							'rules' => 'trim|required|numeric'
			),
			array(
							'field' => 'is_display',
							'label' => 'Display',
							'rules' => 'trim|required|numeric'
			),
			array(
							'field' => 'is_visible',
							'label' => 'Visible',
							'rules' => 'trim|required|numeric'
			),
			array(
							'field' => 'is_vatable',
							'label' => 'Vatable',
							'rules' => 'trim|required|numeric'
			),
			array(
							'field' => 'is_gc_buyable',
							'label' => 'GC Buyable',
							'rules' => 'trim|required|numeric'
			),
			array(
							'field' => 'is_gc_exclusive',
							'label' => 'GC Exclusive',
							'rules' => 'trim|required|numeric'
			),
			array(
							'field' => 'cpoints_value',
							'label' => 'C Points Value',
							'rules' => 'trim|numeric'
			),
			array(
							'field' => 'is_cpoints_buyable',
							'label' => 'C Points Buyable',
							'rules' => 'trim|numeric'
			)
	);

	function __construct()
	{
		parent::__construct();

		$this->load->model('items_model');
		// load pager library
		$this->load->library('pager');
		$this->set_navigation('products');
	}

	public function index()
	{
		$this->products();
	}

	public function products()
	{
		
		$search_by = trim($this->input->get("search_option"));
		$search_text = trim($this->input->get("search_string"));
		$product_line = trim($this->input->get('product_line'));
		$product_type = trim($this->input->get("product_type"));
		
		$search_url = "";
		
		if (($search_text == "") || empty($search_text)) {
			if($search_by == 'product_type')
			{
				$product_type = trim($this->input->get("product_type"));
				$where = "product_type_id = {$product_type}";
				$product_type_data = $this->items_model->get_product_type_by_id($product_type);
				$search_text = $product_type_data->name;
				$search_url = "?search_option=" . $search_by . "&product_type=" . $product_type;
			}
			elseif($search_by == 'product_line')
			{
				$product_line = trim($this->input->get('product_line'));
				$where = "product_line_id = {$product_line}";
				$product_line_data = $this->items_model->get_product_line_by_id($product_line);
				$search_text = $product_line_data->product_line;
				$search_url = "?search_option=" . $search_by . "&product_line=" . $product_line;
			}
			else
				$where = NULL;					
		} else {
			if($search_by == 'product_type')
			{
				$product_type = trim($this->input->get("product_type"));
				$where = "product_type_id = {$product_type}";
				$product_type_data = $this->items_model->get_product_type_by_id($product_type);
				var_dump($product_type);
				$search_text = $product_type_data->name;
				$search_url = "?search_option=" . $search_by . "&product_type=" . $product_type;
			}
			elseif($search_by == 'product_line')
			{
				$product_line = trim($this->input->get('product_line'));
				$where = "product_line_id = {$product_line}";
				$product_line_data = $this->items_model->get_product_line_by_id($product_line);
				$search_text = $product_type_data->product_line;
				$search_url = "?search_option=" . $search_by . "&product_line=" . $product_line;
			}
			else
			{
				$where = "{$search_by} LIKE LOWER('%{$search_text}%')";
				if($search_by == 'item_name') $where .= " OR product_name LIKE LOWER('%{$search_text}%')";
				$search_url = "?search_option=" . $search_by . "&search_string=" . $search_text;
			}
		}
		
				
		// initialize pagination class
		// set pagination data
		$config = array(
		    'pagination_url' => "/admin/products/index/",
		    'total_items' => $this->items_model->get_product_count($where),
		    'per_page' => 10,
		    'uri_segment' => 4,
		);

		$this->pager->set_config($config);
		
		//$products = $this->items_model->get_products(NULL, array('rows' => $this->pager->per_page, 'offset' => $this->pager->offset),'item_name');
		$products = $this->items_model->get_products($where, array('rows' => $this->pager->per_page, 'offset' => $this->pager->offset),'item_name');

		// search vars
		$this->template->search_by = $search_by;
		$this->template->search_text = $search_text;
		$this->template->search_url = $search_url;
		//$this->template->type = $type;
		
		$product_types = $this->items_model->get_product_type_array();
		$product_lines = $this->items_model->get_product_lines();
		$product_types_list = $this->items_model->get_product_types();
		
		$this->template->product_types = json_encode($product_types);
		$this->template->product_types_list = $product_types_list;
		$this->template->product_lines = $product_lines;
		$this->template->products = $products;
		$this->template->product_type = $product_type;
		$this->template->product_line = $product_line;
		$this->template->search_by = $search_by;
		$this->template->view('products/list');
	}

	public function view($navigation_id)
	{
	}

	public function add($type = "product", $product_type_id = "1")
	{
		$product_type = $this->items_model->get_product_type_by_id($product_type_id);
		
		if($type == "product")
		{
			$this->_validation_rule[] = array(
					'field' => 'single_product_name',
					'label' => 'Product Name',
					'rules' => 'trim'
			);
			$this->_validation_rule[] = array(
					'field' => 'item_id',
					'label' => 'Item',
					'rules' => 'trim|required|callback__check_item'
			);
			$this->_validation_rule[] = array(
					'field' => 'product_line_id',
					'label' => 'Product Line',
					'rules' => 'trim|callback__modified_required'
			);
			$this->_validation_rule[] = array(
					'field' => 'member_price',
					'label' => 'Member\'s Price',
					'rules' => 'trim|required|numeric'
			);
			$this->_validation_rule[] = array(
					'field' => 'employee_price',
					'label' => 'Employee\'s Price',
					'rules' => 'trim|required|numeric'
			);
			$this->_validation_rule[] = array(
					'field' => 'giftcheque_standard_retail_price',
					'label' => 'GC Standard Retail Price',
					'rules' => 'trim|required|numeric'
			);
			$this->_validation_rule[] = array(
					'field' => 'giftcheque_member_price',
					'label' => 'GC Member\'s Price',
					'rules' => 'trim|required|numeric'
			);
			$this->_validation_rule[] = array(
					'field' => 'giftcheque_employee_price',
					'label' => 'GC Employee\'s Price',
					'rules' => 'trim|required|numeric'
			);
			$this->_validation_rule[] = array(
					'field' => 'is_product_rebate',
					'label' => 'Product Rebate',
					'rules' => 'trim|required|numeric'
			);
			
			if($product_type_id == 13)
			{
				$this->_validation_rule[] = array(
						'field' => 'product_name',
						'label' => 'Product Name',
						'rules' => 'trim|callback__product_name_required'
				);
				$this->_validation_rule[] = array(
						'field' => 'is_variable_price',
						'label' => 'Variable Price',
						'rules' => 'trim|required|numeric'
				);
			}
			
			if($product_type->is_cpoints == 1)
			{
				$this->_validation_rule[] = array(
						'field' => 'product_name',
						'label' => 'Product Name',
						'rules' => 'trim|callback__product_name_required'
				);
			}
			
			$this->template->items = $this->items_model->get_available_items();
			$this->template->product_lines = $this->items_model->get_product_lines();
			$this->template->types = $this->items_model->get_product_type_array(array("is_visible" => true));
		}
		else if($type == "package")
		{
			$swap_group = "[]";

			$this->_validation_rule[] = array(
					'field' => 'product_code',
					'label' => 'Product Code',
					'rules' => 'trim|required|callback__check_product_code'
			);
			$this->_validation_rule[] = array(
					'field' => 'product_description',
					'label' => 'Product Description',
					'rules' => 'trim|required'
			);
			$this->_validation_rule[] = array(
					'field' => 'product_name_input',
					'label' => 'Product Input',
					'rules' => 'trim|callback__check_product'
			);
			$this->_validation_rule[] = array(
					'field' => 'package_name',
					'label' => 'Package Name',
					'rules' => 'trim|required|callback__check_package'
			);
			$this->template->package_types = $this->items_model->get_product_type_array(array("product_type_id !=" => 1,"is_visible" => true));
			
			//get non-package product types
			$get_product_types = $this->items_model->get_product_types("is_package != 1");
			$valid_product_types = array();
			foreach($get_product_types as $p)
				array_push($valid_product_types, $p->product_type_id);
			
			$valid_product_types = implode(',', $valid_product_types);
			
			$where = "product_type_id IN ({$valid_product_types})";
			$this->template->products = $this->items_model->get_products($where,null,"product_name ASC");
		}
		else
		{
			redirect("/admin/products");
			return;
		}
		
		if ($_POST)
		{
			if($type == "package")
			{
				$i = 0;

				$product_name = $this->input->post('product_name');
				$is_swappable = $this->input->post('is_swappable');
				$quantity = $this->input->post('quantity');
				$group = $this->input->post('group');
				$group_qty = $this->input->post('group_qty');

				$swap_group_array = array();

				if(!is_array($group)) $group = array();

				//set swap group for js variable
				foreach($group as $k => $g)
				{
					if($g == 0) continue;
					$swap_group_array[$g] = $group_qty[$k];
				}

				if(empty($swap_group_array)) $swap_group = '[]';
				else $swap_group = '[,'.implode(",",$swap_group_array).']';


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
			}

			// post done here
			$this->form_validation->set_rules($this->_validation_rule);
			if ($this->form_validation->run())
			{

				//product type == ITEM
				if($type == "product")
				{
					// insert the new product
					$data = array(
						'product_name' => set_value('single_product_name'),
						'item_id' => set_value('item_id'),
						'product_type_id' => set_value('product_type_id'),
						'product_line_id' => set_value('product_line_id'),
						'standard_retail_price' => set_value('standard_retail_price'),
						'member_price' => set_value('member_price'),
						'employee_price' => set_value('employee_price'),
						'giftcheque_standard_retail_price' => set_value('giftcheque_standard_retail_price'),
						'giftcheque_member_price' => set_value('giftcheque_member_price'),
						'giftcheque_employee_price' => set_value('giftcheque_employee_price'),
						'is_cpoints_buyable' => set_value('is_cpoints_buyable'),
						'cpoints_value' => set_value('cpoints_value'),
						'igpsm_points' => set_value('igpsm_points'),
						'image_filename' => json_encode(array()),
						'is_vatable' => set_value('is_vatable'),
						'is_visible' => set_value('is_visible'),
						'is_display' => set_value('is_display'),
						'is_active' => set_value('is_active'),
						'is_gc_buyable' => set_value('is_gc_buyable'),
						'is_gc_exclusive' => set_value('is_gc_exclusive'),
						'is_product_rebate' => set_value('is_product_rebate')
					);

					if($product_type_id == 13)
					{
						$data['is_variable_price'] = set_value('is_variable_price');
						$data["product_name"] = set_value('product_name');
					}
					
					if($product_type->is_cpoints == 1)
					{
						if(set_value('single_product_name') == 'NONE')
							$data["product_name"] = set_value('product_name');
					}

					$this->items_model->insert_product($data);

					$table_id = $this->items_model->insert_id();

					$details_after = array('id' => 0, 'details' => $data);
					$details_after = json_encode($details_after);
					$new_product_data_logs = array(
						'user_id' => $this->user->user_id,
						'module_name' => 'PRODUCTS',
						'table_name' => 'is_products',
						'action' => 'ADD',
						'details_before' => "",
						'details_after' => $details_after,
						'remarks' => ""
					);
					$this->tracking_model->insert_logs('admin', $new_product_data_logs);
					
				}//product type == PACKAGE
				elseif($type == "package")
				{
					// insert the new package
					$data = array(
						'product_name' => set_value('package_name'),
						'product_code' => set_value('product_code'),
						'product_description' => set_value('product_description'),
						'product_type_id' => set_value('product_type_id'),
						'standard_retail_price' => set_value('standard_retail_price'),
						'member_price' => set_value('standard_retail_price'),
						'employee_price' => set_value('standard_retail_price'),
						'giftcheque_standard_retail_price' => set_value('standard_retail_price'),
						'giftcheque_member_price' => set_value('standard_retail_price'),
						'giftcheque_employee_price' => set_value('standard_retail_price'),
						'cpoints_value' => set_value('cpoints_value'),
						'is_cpoints_buyable' => set_value('is_cpoints_buyable'),
						'igpsm_points' => set_value('igpsm_points'),
						'image_filename' => json_encode(array()),
						'is_vatable' => set_value('is_vatable'),
						'is_visible' => set_value('is_visible'),
						'is_display' => set_value('is_display'),
						'is_active' => set_value('is_active'),
						'is_gc_buyable' => set_value('is_gc_buyable'),
						'is_gc_exclusive' => set_value('is_gc_exclusive')
					);
					$this->items_model->insert_product($data);

					$package_id = $this->items_model->insert_id();

					$details_after = array('id' => 0, 'details' => $data);
					$details_after = json_encode($details_after);
					$new_product_data_logs = array(
						'user_id' => $this->user->user_id,
						'module_name' => 'PRODUCTS',
						'table_name' => 'is_products',
						'action' => 'ADD',
						'details_before' => "",
						'details_after' => $details_after,
						'remarks' => ""
					);
					$this->tracking_model->insert_logs('admin', $new_product_data_logs);

					foreach($product_name as $k => $v)
					{

						$data = array(
							'product_id' => $package_id,
							'child_product_id' => $v,
							'quantity' => $quantity[$k],
							'is_swappable' => $is_swappable[$k],
							'group' => $group[$k],
							'group_quantity' => $group_qty[$k]
						);
						$this->items_model->insert_product_product($data);

						$details_after = array('id' => 0, 'details' => $data);
						$details_after = json_encode($details_after);
						$new_product_data_logs = array(
							'user_id' => $this->user->user_id,
							'module_name' => 'PRODUCTS',
							'table_name' => 'is_product_products',
							'action' => 'ADD',
							'details_before' => "",
							'details_after' => $details_after,
							'remarks' => ""
						);
						$this->tracking_model->insert_logs('admin', $new_product_data_logs);
					}
				}

				redirect("/admin/products");
				return;
			}
		}

		if($type == "package") $this->template->swap_group = $swap_group;
		

		//reference for P2P Packages
		$p2p_package_type = $this->items_model->get_product_types(array('name' => 'P2P Packages'));
		$p2p_package_type = $p2p_package_type[0];

		//cpoints switch
		$cpoints_switch = $this->settings_model->get_setting_by_slug('switch_to_cpoints');
		if(empty($cpoints_switch)) $cpoints_switch = 0;
		
		//get product type
		$product_type = $this->items_model->get_product_type_by_id($product_type_id);

		$this->template->cpoints_switch = $cpoints_switch->value;
		$this->template->p2p_package_type_id = $p2p_package_type->product_type_id;
		$this->template->product_type_id = $product_type_id;
		$this->template->is_cpoints = $product_type->is_cpoints;
		$this->template->view("{$type}s/add");
		
	}
	
	public function confirm_add() {
		$_item_id = $this->input->post('_item_id');
		$_product_line_id = $this->input->post('_product_line_id');
		$_standard_retail_price = $this->input->post('_standard_retail_price');
		$_cpoints_value = $this->input->post('_cpoints_value');
		$_member_price = $this->input->post('_member_price');
		$_employee_price = $this->input->post('_employee_price');
		$_igpsm_points = $this->input->post('_igpsm_points');
		
		$item_details = $this->items_model->get_item_by_id($_item_id);		
		$product_line_details = $this->items_model->get_product_line_by_id($_product_line_id);
		
		$item_name = strtoupper($item_details->item_name);		
		$product_line = strtoupper($product_line_details->product_line);
		
				
		$html = "<p><label>You are about to add a new Product with the following details:</label>	
				<table class='table table-striped table-bordered'>
					<thead>
					</thead>
					<tbody>
						<tr>
							<td style='width:140px;'><label><strong>Item Name</strong></label></td>
							<td><label class=''>{$item_name}</label></td>		
						</tr>
						<tr>
							<td style='width:140px;'><label><strong>Product Line</strong></label></td>
							<td><label class=''>{$product_line}</label></td>		
						</tr>
						<tr>
							<td style='width:140px;'><label><strong>Standard Retail Price</strong></label></td>
							<td><label class=''>PHP {$_standard_retail_price}</label></td>		
						</tr>	
						<tr>
							<td style='width:140px;'><label><strong>Member's Price</strong></label></td>
							<td><label class=''>PHP {$_member_price}</label></td>		
						</tr>
						<tr>
							<td style='width:140px;'><label><strong>Employee's Price</strong></label></td>
							<td><label class=''>PHP {$_employee_price}</label></td>		
						</tr>
						<tr>
							<td style='width:140px;'><label><strong>IGPSM Points</strong></label></td>
							<td><label class=''>{$_igpsm_points}</label></td>		
						</tr>
						<tr>
							<td style='width:140px;'><label><strong>C Points Value</strong></label></td>
							<td><label class=''>{$_cpoints_value}</label></td>
						</tr>
					</tbody>
				</table>				
				<label>Do you want to proceed?</label>
			</p>";
		
		echo json_encode(array("status"=>"1","html"=>$html));
		return;
		
	}
	
	public function add_product() {
		$_item_id = $this->input->post('_item_id');
		$_product_line_id = $this->input->post('_product_line_id');
		$_standard_retail_price = $this->input->post('_standard_retail_price');
		$_member_price = $this->input->post('_member_price');
		$_employee_price = $this->input->post('_employee_price');
		$_igpsm_points = $this->input->post('_igpsm_points');
		$_cpoints_value = $this->input->post('_cpoints_value');
		
		// insert the new product
		$data = array(
			'item_id' => $_item_id,
			'product_line_id' => $_product_line_id,
			'standard_retail_price' => $_standard_retail_price,
			'member_price' => $_member_price,
			'employee_price' => $_employee_price,
			'igpsm_points' => $_igpsm_points,
			'cpoints_value' => $_cpoints_value,
			'image_filename' => json_encode(array("image_count" => 0,"images" => array()))
		);
		$this->items_model->insert_product($data);
		
		$table_id = $this->items_model->insert_id();
		
		$details_after = array('id' => 0, 'details' => $data);
		$details_after = json_encode($details_after);
		$new_product_data_logs = array(
			'user_id' => $this->user->user_id,
			'module_name' => 'PRODUCTS',
			'table_name' => 'is_products',
			'action' => 'ADD',
			'details_before' => "",
			'details_after' => $details_after,
			'remarks' => ""
		);
		$this->tracking_model->insert_logs('admin', $new_product_data_logs);
	
		echo json_encode(array("status"=>"1"));
		return;		
	}
	
	public function edit($product_id = 0)
	{
		$type = "product";
		
		$for_edit = $this->items_model->get_product_by_id($product_id);
		$product_type_edit = $this->items_model->get_product_type_by_id($for_edit->product_type_id);
		if(!empty($product_type_edit))
		{
			if($product_type_edit->is_package == 0)
				$type = "product";
			else
				$type = "package";
		}
		else
		{
			$type = "product";
		}
		/*if($for_edit->item_id > 0 || $for_edit->product_type_id == 13)
		{
			$type = "product";
		}
		elseif($for_edit->item_id == 0)
		{
			$type = "package";
		}*/
		
		
		if($type == "product")
		{
			$this->_validation_rule[] = array(
					'field' => 'product_code',
					'label' => 'Product Code',
					'rules' => 'trim|required|callback__check_product_code'
			);
			$this->_validation_rule[] = array(
					'field' => 'product_line_id',
					'label' => 'Product Line',
					'rules' => 'trim|required'
			);
			$this->_validation_rule[] = array(
					'field' => 'member_price',
					'label' => 'Member\'s Price',
					'rules' => 'trim|required|numeric'
			);
			$this->_validation_rule[] = array(
					'field' => 'employee_price',
					'label' => 'Employee\'s Price',
					'rules' => 'trim|required|numeric'
			);
			$this->_validation_rule[] = array(
					'field' => 'giftcheque_standard_retail_price',
					'label' => 'GC Standard Retail Price',
					'rules' => 'trim|required|numeric'
			);
			$this->_validation_rule[] = array(
					'field' => 'giftcheque_member_price',
					'label' => 'GC Member\'s Price',
					'rules' => 'trim|required|numeric'
			);
			$this->_validation_rule[] = array(
					'field' => 'giftcheque_employee_price',
					'label' => 'GC Employee\'s Price',
					'rules' => 'trim|required|numeric'
			);
			$this->_validation_rule[] = array(
					'field' => 'is_product_rebate',
					'label' => 'Product Rebate',
					'rules' => 'trim|required|numeric'
			);
			$this->_validation_rule[] = array(
					'field' => 'is_cpoints_buyable',
					'label' => 'C Points Buyable',
					'rules' => 'trim|numeric',
			);

			if($for_edit->product_type_id == 13)
			{
				$this->_validation_rule[] = array(
						'field' => 'product_name',
						'label' => 'Product Name',
						'rules' => 'trim|callback__product_name_required'
				);
				$this->_validation_rule[] = array(
						'field' => 'is_variable_price',
						'label' => 'Variable Price',
						'rules' => 'trim|required|numeric'
				);
			}
			
			$product = $this->items_model->get_products(array("product_id" => $product_id));

			if(!empty($product)) $product = $product[0];
			else $product = array();

			$this->template->product = $product;
			$this->template->product_lines = $this->items_model->get_product_lines();
			$this->template->types = $this->items_model->get_product_type_array(array("is_visible" => true));
		}
		else if($type == "package")
		{
			$swap_group = "[]";

			$this->_validation_rule[] = array(
					'field' => 'product_code',
					'label' => 'Product Code',
					'rules' => 'trim|required|callback__check_product_code'
			);
			$this->_validation_rule[] = array(
					'field' => 'product_description',
					'label' => 'Product Description',
					'rules' => 'trim|required'
			);
			$this->_validation_rule[] = array(
					'field' => 'product_name_input',
					'label' => 'Product Input',
					'rules' => 'trim|callback__check_product'
			);
			$this->_validation_rule[] = array(
					'field' => 'package_name',
					'label' => 'Package Name',
					'rules' => 'trim|required|callback__check_package'
			);

			$package = $this->items_model->get_products(array("product_id" => $product_id));

			if(!empty($package)) $package = $package[0];
			else $package = array();

			$this->template->package = $package;

			$this->template->package_types = $this->items_model->get_product_type_array(array("product_type_id !=" => 1,"is_visible" => true));
			
			//get non-package product types
			$get_product_types = $this->items_model->get_product_types("is_package != 1");
			$valid_product_types = array();
			foreach($get_product_types as $p)
				array_push($valid_product_types, $p->product_type_id);
			
			$valid_product_types = implode(',', $valid_product_types);
			
			$where = "product_type_id IN ({$valid_product_types})";
			
			$products_list = $this->items_model->get_products($where,null,"product_name ASC");
			foreach($products_list as $p)
			{
				if($p->item_id == 0 && $p->item_name == "")
					$p->item_name = $p->product_name;
			}
			$this->template->products = $products_list;
			$swap_group_array = array();
			$package_products = array();
			if(!empty($package))
			{
				$package_products = $this->items_model->get_product_products(array("product_id"=>$package->product_id),null,"group ASC");

				//set swap group for js variable
				foreach($package_products as $k => $pp)
				{
					if($pp->group == 0) continue;
					$swap_group_array[$pp->group] = $pp->group_quantity;
				}
			}


			if(empty($swap_group_array)) $swap_group = '[]';
			else $swap_group = '[,'.implode(",",$swap_group_array).']';
		}
		else
		{
			redirect("/admin/products");
			return;
		}

		if ($_POST)
		{
			if($type == "package")
			{
				$i = 0;
				$package_products = '';
				
				$product_name = $this->input->post('product_name');
				$is_swappable = $this->input->post('is_swappable');
				$quantity = $this->input->post('quantity');
				$group = $this->input->post('group');
				$group_qty = $this->input->post('group_qty');

				$swap_group_array = array();

				if(!is_array($group)) $group = array();

				//set swap group for js variable
				foreach($group as $k => $g)
				{
					if($g == 0) continue;
					$swap_group_array[$g] = $group_qty[$k];
				}

				if(empty($swap_group_array)) $swap_group = '[]';
				else $swap_group = '[,'.implode(",",$swap_group_array).']';


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
			}

			// post done here
			$this->form_validation->set_rules($this->_validation_rule);
			if ($this->form_validation->run())
			{

				$product_type_id = set_value('product_type_id');
				$product_type = $this->items_model->get_product_type_by_id($product_type_id);
				
				//product type == ITEM
				if($type == "product")
				{
					// insert the new product
					$data = array(
						'product_code' => set_value('product_code'),
						'item_id' => $product->item_id,
						'product_type_id' => set_value('product_type_id'),
						'product_line_id' => set_value('product_line_id'),
						'standard_retail_price' => set_value('standard_retail_price'),
						'member_price' => set_value('member_price'),
						'employee_price' => set_value('employee_price'),
						'giftcheque_standard_retail_price' => set_value('giftcheque_standard_retail_price'),
						'giftcheque_member_price' => set_value('giftcheque_member_price'),
						'giftcheque_employee_price' => set_value('giftcheque_employee_price'),
						'cpoints_value' => set_value('cpoints_value'),
						'is_cpoints_buyable' => set_value('is_cpoints_buyable'),
						'igpsm_points' => set_value('igpsm_points'),
						'is_vatable' => set_value('is_vatable'),
						'is_visible' => set_value('is_visible'),
						'is_display' => set_value('is_display'),
						'is_active' => set_value('is_active'),
						'is_gc_buyable' => set_value('is_gc_buyable'),
						'is_gc_exclusive' => set_value('is_gc_exclusive'),
						'is_product_rebate' => set_value('is_product_rebate')
					);
					
					if($product_type_id == 13)
					{
						$data['is_variable_price'] = set_value('is_variable_price');
						$data["product_name"] = set_value('product_name');
					}
					if($product_type->is_cpoints == 1)
					{
						$name =  $this->input->post('product_name');
						$data["product_name"] = $name;
					}
					
					$old_product_details = $this->items_model->get_product_by_id($product->product_id);
					$details_before = array('id' => $product->product_id, 'details' => $old_product_details);
					$details_before = json_encode($details_before);
					
					$this->items_model->update_product($data,array('product_id' => $product->product_id));

					$details_after = array('id' => $product->product_id, 'details' => $data);
					$details_after = json_encode($details_after);
					$update_product_data_logs = array(
						'user_id' => $this->user->user_id,
						'module_name' => 'PRODUCTS',
						'table_name' => 'is_products',
						'action' => 'UPDATE',
						'details_before' => $details_before,
						'details_after' => $details_after,
						'remarks' => ""
					);
					$this->tracking_model->insert_logs('admin', $update_product_data_logs);
				}//product type == PACKAGE
				elseif($type == "package")
				{
					$package = $this->items_model->get_product_by_id($product_id);
					
					// insert the new package
					$data = array(
						'product_name' => set_value('package_name'),
						'product_code' => set_value('product_code'),
						'product_description' => set_value('product_description'),
						'product_type_id' => set_value('product_type_id'),
						'standard_retail_price' => set_value('standard_retail_price'),
						'member_price' => set_value('standard_retail_price'),
						'employee_price' => set_value('standard_retail_price'),
						'giftcheque_standard_retail_price' => set_value('standard_retail_price'),
						'giftcheque_member_price' => set_value('standard_retail_price'),
						'giftcheque_employee_price' => set_value('standard_retail_price'),
						'cpoints_value' => set_value('cpoints_value'),
						'is_cpoints_buyable' => set_value('is_cpoints_buyable'),
						'igpsm_points' => set_value('igpsm_points'),
						'is_vatable' => set_value('is_vatable'),
						'is_visible' => set_value('is_visible'),
						'is_display' => set_value('is_display'),
						'is_active' => set_value('is_active'),
						'is_gc_buyable' => set_value('is_gc_buyable'),
						'is_gc_exclusive' => set_value('is_gc_exclusive')
					);
					$old_product_details = $this->items_model->get_product_by_id($package->product_id);
					$details_before = array('id' => $package->product_id, 'details' => $old_product_details);
					$details_before = json_encode($details_before);
					
					$this->items_model->update_product($data,array('product_id' => $package->product_id));
					error_log($this->db->last_query());
					$details_after = array('id' => $package->product_id, 'details' => $data);
					$details_after = json_encode($details_after);
					$update_product_data_logs = array(
						'user_id' => $this->user->user_id,
						'module_name' => 'PRODUCTS',
						'table_name' => 'is_products',
						'action' => 'UPDATE',
						'details_before' => $details_before,
						'details_after' => $details_after,
						'remarks' => ""
					);
					$this->tracking_model->insert_logs('admin', $update_product_data_logs);
					
					
					$old_product_products = $this->items_model->get_product_product_by_product_id($package->product_id);
					$this->items_model->delete_product_product(array('product_id' => $package->product_id));
					
					if(!empty($old_product_products))
					{
						//LOGGING OF DELETE
						foreach($old_product_products as $key => $opp)
						{
							$details_before = array('id' => array('product_id' => $opp->product_id, 'child_product_id' => $opp->child_product_id), 'details' => $opp);
							$details_before = json_encode($details_before);
							$delete_product_product_data_logs = array(
								'user_id' => $this->user->user_id,
								'module_name' => 'PRODUCTS',
								'table_name' => 'is_product_products',
								'action' => 'DELETE',
								'details_before' => $details_before,
								'details_after' => "",
								'remarks' => ""
							);
							$this->tracking_model->insert_logs('admin', $delete_product_product_data_logs);
						}
					}
					

					foreach($product_name as $k => $v)
					{
						$data = array(
							'product_id' => $package->product_id,
							'child_product_id' => $v,
							'quantity' => $quantity[$k],
							'is_swappable' => $is_swappable[$k],
							'group' => $group[$k],
							'group_quantity' => $group_qty[$k]
						);
						$this->items_model->insert_product_product($data);

						$details_after = array('id' => 0, 'details' => $data);
						$details_after = json_encode($details_after);
						$new_product_product_data_logs = array(
							'user_id' => $this->user->user_id,
							'module_name' => 'PRODUCTS',
							'table_name' => 'is_product_products',
							'action' => 'ADD',
							'details_before' => "",
							'details_after' => $details_after,
							'remarks' => ""
						);
						$this->tracking_model->insert_logs('admin', $new_product_product_data_logs);
					}
				}

				redirect("/admin/products");
				return;
			}
		}

		if($type == "package") 
		{
			$this->template->swap_group = $swap_group;
			$this->template->package_products = $package_products;
		}
		//cpoints switch
		$cpoints_switch = $this->settings_model->get_setting_by_slug('switch_to_cpoints');
		if(empty($cpoints_switch)) $cpoints_switch = 0;
		
		$this->template->cpoints_switch = $cpoints_switch->value;

		$this->template->view("{$type}s/edit");
	}

	public function confirm_edit() {
		$_item_id = $this->input->post('_item_id');
		$_product_line_id = $this->input->post('_product_line_id');
		$_standard_retail_price = $this->input->post('_standard_retail_price');
		$_member_price = $this->input->post('_member_price');
		$_employee_price = $this->input->post('_employee_price');
		$_igpsm_points = $this->input->post('_igpsm_points');
		$_product_id = $this->input->post('_product_id');
		$_cpoints_value = $this->input->post('_cpoints_value');
	
		$item_details = $this->items_model->get_item_by_id($_item_id);		
		$product_line_details = $this->items_model->get_product_line_by_id($_product_line_id);

		$item_name = strtoupper($item_details->item_name);		
		$product_line = strtoupper($product_line_details->product_line);

		$html = "<p><label>You are about to edit a Product with the following details:</label>	
				<table class='table table-striped table-bordered'>
					<thead>
					</thead>
					<tbody>
						<tr>
							<td style='width:140px;'><label><strong>Item Name</strong></label></td>
							<td><label class=''>{$item_name}</label></td>		
						</tr>
						<tr>
							<td style='width:140px;'><label><strong>Product Line</strong></label></td>
							<td><label class=''>{$product_line}</label></td>		
						</tr>
						<tr>
							<td style='width:140px;'><label><strong>Standard Retail Price</strong></label></td>
							<td><label class=''>PHP {$_standard_retail_price}</label></td>		
						</tr>	
						<tr>
							<td style='width:140px;'><label><strong>Member's Price</strong></label></td>
							<td><label class=''>PHP {$_member_price}</label></td>		
						</tr>
						<tr>
							<td style='width:140px;'><label><strong>Employee's Price</strong></label></td>
							<td><label class=''>PHP {$_employee_price}</label></td>		
						</tr>
						<tr>
							<td style='width:140px;'><label><strong>IGPSM Points</strong></label></td>
							<td><label class=''>{$_igpsm_points}</label></td>		
						</tr>
						<tr>
							<td style='width:140px;'><label><strong>C Points Value</strong></label></td>
							<td><label class=''>{$_cpoints_value}</label></td>
						</tr>
					</tbody>
				</table>				
				<label>Do you want to proceed?</label>
			</p>";
		
		echo json_encode(array("status"=>"1","html"=>$html));
		return;
		
	}

	public function update_product() {
		$_item_id = $this->input->post('_item_id');
		$_product_line_id = $this->input->post('_product_line_id');
		$_standard_retail_price = $this->input->post('_standard_retail_price');
		$_member_price = $this->input->post('_member_price');
		$_employee_price = $this->input->post('_employee_price');
		$_igpsm_points = $this->input->post('_igpsm_points');
		$_product_id = $this->input->post('_product_id');
		$_cpoints_value = $this->input->post('_cpoints_value');
	
		$_product_id = $this->input->post('_product_id');		
		$product = $this->items_model->get_product_by_id($_product_id);
	
		// insert the new product
		$data = array(
			'item_id' => $_item_id,
			'product_line_id' => $_product_line_id,
			'standard_retail_price' => $_standard_retail_price,
			'member_price' => $_member_price,
			'employee_price' => $_employee_price,
			'igpsm_points' => $_igpsm_points,
			'cpoints_value' => $_cpoints_value,
		);
		$previous_product_data = $this->items_model->get_product_by_id($_product_id);
		
		$this->items_model->update_product($data, array('product_id' => $_product_id));
				
		$details_before = array('id' => $_product_id, 'details' => $previous_product_data);
		$details_before = json_encode($details_before);
		$details_after = array('id' => $_product_id, 'details' => $data);
		$details_after = json_encode($details_after);
		$update_product_data_logs = array(
			'user_id' => $this->user->user_id,
			'module_name' => 'PRODUCTS',
			'table_name' => 'is_products',
			'action' => 'UPDATE',
			'details_before' => $details_before,
			'details_after' => $details_after,
			'remarks' => ""
		);
		$this->tracking_model->insert_logs('admin', $update_product_data_logs);
		
		echo json_encode(array("status"=>"1"));
		return;		
	}
	
	//public function delete($product_id)
	public function delete()
	{
		$_product_id = $this->input->post('_product_id');
		$_product_type_id = $this->input->post('_product_type_id');

		$product = $this->items_model->get_product_by_id($_product_id);

		$html = "";
		$title = "";
		if($_product_type_id == 1)
		{
			$title = "Delete Product";
			$data = array(
				'product' => $product
			);
			$html = $this->load->view('/products/delete', $data, TRUE);
		}
		elseif($_product_type_id > 1)
		{
			$product_type = $this->items_model->get_product_type_array(array("product_type_id" => $_product_type_id));
			$title = "Delete Package";
			$data = array(
				'package' => $product,
				'product_type_id' => $_product_type_id,
				'product_type' => $product_type
			);
			$html = $this->load->view('/packages/delete', $data, TRUE);
		}
		

		$this->return_json("ok","",array("title"=>$title,"html"=>$html));
		return;
		
	}
	
	public function confirm_delete() {
		$_product_id = $this->input->post('_product_id');
		$_product_type_id = $this->input->post('_product_type_id');
	
		$html = "<p><label>Are you sure you want to proceed?</label>									
				</p>";

		$title = "";
		if($_product_type_id)
		{
			$title = "Delete Product";
		}
		elseif($_product_type_id)
		{
			$title = "Delete Package";
		}

		$this->return_json("ok","",array("title"=>$title,"html"=>$html));
		return;
		
	}
	
	public function delete_product() {	
		$_product_id = $this->input->post('_product_id');
		$_product_type_id = $this->input->post('_product_type_id');
		$product = $this->items_model->get_product_by_id($_product_id);	
		$title = "Delete Product";
		if (!empty($_product_id)) {
			$this->items_model->delete_product(array('product_id' => $_product_id));
			//$this->items_model->update_product(array('is_active' => 0), array('product_id' => $_product_id));

			//DELETE PRODUCT LOGGING
			$details_before = array('id' => $_product_id, 'details' => $product);
			$details_before = json_encode($details_before);
			$delete_child_product_data_logs = array(
				'user_id' => $this->user->user_id,
				'module_name' => 'PRODUCTS',
				'table_name' => 'is_products',
				'action' => 'DELETE',
				'details_before' => $details_before,
				'details_after' => "",
				'remarks' => ""
			);
			$this->tracking_model->insert_logs('admin', $delete_child_product_data_logs);
			
			if($_product_type_id == 1)
			{
				$old_child_product_product = $this->items_model->get_product_product_by_child_product_id($product->product_id);

				$this->items_model->delete_product_product(array('child_product_id' => $product->product_id));

				if(!empty($old_child_product_product))
				{
					foreach($old_child_product_product as $key => $opp)
					{
						//DELETE CHILD PRODUCT LOGGING
						$details_before = array('id' => array('product_id' => $opp->product_id, 'child_product_id' => $opp->child_product_id), 'details' => $opp);
						$details_before = json_encode($details_before);
						$delete_child_product_data_logs = array(
							'user_id' => $this->user->user_id,
							'module_name' => 'PRODUCTS',
							'table_name' => 'is_product_products',
							'action' => 'DELETE',
							'details_before' => $details_before,
							'details_after' => "",
							'remarks' => ""
						);
						$this->tracking_model->insert_logs('admin', $delete_child_product_data_logs);
					}
				}
			}
			elseif($_product_type_id > 1)
			{
				$title = "Delete Package";
				$old_product_products = $this->items_model->get_product_product_by_product_id($product->product_id);

				$this->items_model->delete_product_product(array('product_id' => $product->product_id));

				if(!empty($old_product_products))
				{
					foreach($old_product_products as $key => $opp)
					{
						//DELETE PACKAGE PRODUCT LOGGING
						$details_before = array('id' => array('product_id' => $opp->product_id, 'child_product_id' => $opp->child_product_id), 'details' => $opp);
						$details_before = json_encode($details_before);
						$delete_product_product_data_logs = array(
							'user_id' => $this->user->user_id,
							'module_name' => 'PRODUCTS',
							'table_name' => 'is_product_products',
							'action' => 'DELETE',
							'details_before' => $details_before,
							'details_after' => "",
							'remarks' => ""
						);
						$this->tracking_model->insert_logs('admin', $delete_product_product_data_logs);
					}
				}
				
			}
		} else {
			$this->return_json("error","Invalid Product ID");
			return;
		}
	
		$this->return_json("ok","",array("title" => $title));
		return;
		
	}
	
	public function gallery()
	{
		$_product_id = $this->input->post('_product_id');
		$_product_type_id = $this->input->post('_product_type_id');
		
		$product = $this->items_model->get_product_by_id($_product_id);
		
		if(empty($product))
		{
			$this->return_json("error","This product does not exist!");
			return;
		}

		$data = array(
			'product' => $product
		);

		$html = $this->load->view('/products/gallery', $data, TRUE);
		
		
		$this->return_json("ok","",array("html" => $html));
		return;
	}

	public function get_headers()
	{
		$system_code = $this->input->post('system_code');

		$section_headers = $this->navigations_model->get_navigation_headers_by_system_code($system_code);

		$this->return_json("ok","",$section_headers);
		return;
	}

	public function get_items()
	{
		$item_name = $this->input->post('item_name');

		$items = $this->items_model->item_search($item_name);

		$this->return_json("ok","",$items);
	}

	public function _check_item($item_id)
	{
		$orig_item_id = trim($this->input->post('orig_item_id'));
		$item = $this->items_model->get_item_by_id($item_id);
		
		if($item_id != 0)
		{
			if (!empty($item))
			{
				$product = $this->items_model->get_product_by_item_id($item->item_id);
				if(!empty($product))
				{
					if($item_id == $orig_item_id)
					{
						return TRUE;
					}
					else
					{
						$this->form_validation->set_message('_check_item', 'A product for this item already exists.');
						return FALSE;
					}
				}
				else
				{
					return TRUE;
				}
			}
			else
			{
				$this->form_validation->set_message('_check_item', 'This item is not in the database.');
				return FALSE;
			}
		}
		else
		{
			return TRUE;
		}
	}
	
	public function set_primary(){
		$product_id = abs($this->input->post('product_id'));
		$filename = trim($this->input->post('filename'));
		
		$product = $this->items_model->get_product_by_id($product_id);
		
		if(empty($product))
		{
			$this->return_json("error","This Product does not exist!");
			return;
		}
		
		$images = json_decode($product->image_filename);
		
		
		foreach($images as $i)
		{
			if($i->url == $filename) $i->is_default = true;
			else  $i->is_default = false;
		}
		
		
		$data = array("image_filename" => json_encode($images));
		
		$this->items_model->update_product($data,array("product_id" => $product_id));
		
		$details_before = array('id' => $product_id, 'details' => array('image_filename' => $product->image_filename));
		$details_before = json_encode($details_before);
		$details_after = array('id' => $product_id, 'details' => $data);
		$details_after = json_encode($details_after);
		$update_product_image_logs = array(
			'user_id' => $this->user->user_id,
			'module_name' => 'PRODUCTS',
			'table_name' => 'is_products',
			'action' => 'UPDATE',
			'details_before' => $details_before,
			'details_after' => $details_after,
			'remarks' => ""
		);
		$this->tracking_model->insert_logs('admin', $update_product_image_logs);

		$file = $filename;

		$file = substr($file, strrpos($file,"/")+1);

		$this->return_json("ok","{$file} set as primary picture.",array("filename" => $filename));
		return;
		
	}

	public function _check_package($package_name)
	{
		$package_name = trim($package_name);
		$orig_package_name = trim($this->input->post('orig_package_name'));
		$package = $this->items_model->get_product_by_product_name($package_name);

		if (!empty($package))
		{
			if(strtolower($package_name) == strtolower($orig_package_name))
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

	public function _check_product_code($product_code)
	{
		$product_code = trim($product_code);
		$orig_product_code = trim($this->input->post('orig_product_code'));
		$package = $this->items_model->get_product_by_product_code($product_code);

		if (!empty($package))
		{
			if($product_code == $orig_product_code)
			{
				return TRUE;
			}
			else
			{
				$this->form_validation->set_message('_check_product_code', 'This product code is already in use.');
				return FALSE;
			}
		}
		else
		{
			return TRUE;
		}
	}

	public function _modified_required($product_line_id)
	{
		$item_id = $this->input->post('item_id');
		if($item_id != 0 || strcmp($item_id,"") == 0)
		{
			if($product_line_id == "")
			{	
				$this->form_validation->set_message('_modified_required', 'The Product Line field is required.');
				return FALSE;
			}
			else
			{
				return TRUE;
			}
		}
		else
		{
			 return TRUE;
		}
	}

	public function _product_name_required($product_name)
	{
		$item_id = $this->input->post('item_id');
		if($item_id == 0)
		{
			if($product_name == "")
			{
				$this->form_validation->set_message('_product_name_required', 'The Product Name field is required.');
				return FALSE;
			}
			else
			{
				return TRUE;
			}
		}
		else
		{
			 return TRUE;
		}
	}

	public function delete_image()
	{
		$product_id = $this->input->post('product_id');
		$filename = $this->input->post('filename');

		$product = $this->items_model->get_product_by_id($product_id);

		if(empty($product))
		{
			$this->return_json("error","This Product does not exist!");
			return;
		}

		$images = json_decode($product->image_filename);
		$new_images = array();

		foreach($images as $k => $i)
		{
			if($i->url == $filename)
			{
				if($i->is_default && count($images) > 1 && $k == 0)
				{
					$new_default = $images[1];
					$new_default->is_default = true;
					$images[1] = $new_default;
				}
				elseif($i->is_default && count($images) > 1 && $k != 0)
				{
					$new_default = $images[0];
					$new_default->is_default = true;
					$images[0] = $new_default;
				}
				continue;
			}
			else
			{
				array_push($new_images, $i);
			}
		}


		$data = array("image_filename" => json_encode($new_images));

		$this->items_model->update_product($data,array("product_id" => $product_id));
		
		$details_before = array('id' => $product_id, 'details' => array('image_filename' => $product->image_filename));
		$details_before = json_encode($details_before);
		$details_after = array('id' => $product_id, 'details' => $data);
		$details_after = json_encode($details_after);
		$update_product_image_logs = array(
			'user_id' => $this->user->user_id,
			'module_name' => 'PRODUCTS',
			'table_name' => 'is_products',
			'action' => 'UPDATE',
			'details_before' => $details_before,
			'details_after' => $details_after,
			'remarks' => ""
		);
		$this->tracking_model->insert_logs('admin', $update_product_image_logs);

		$product->image_filename = json_encode($new_images);

		$html_data = array(
			'product' => $product
		);

		$html = $this->load->view('/products/gallery', $html_data, TRUE);

		$this->return_json("ok","Image deleted sucessfully!",array("html" => $html));
		return;
	}
	
	public function view_cards($product_id)
	{
		$product = $this->items_model->get_product_by_id($product_id);
		$product_cards = $this->items_model->get_product_cards(array('product_id' => $product_id));
		
		$group_products = "";
		
		$products = $this->items_model->get_products("product_id NOT IN ({$product_id})");

		$this->template->products = $products;
		$this->template->product_cards = $product_cards;
		$this->template->group_products = $group_products;
		$this->template->product_id = $product_id;
		$this->template->product_name = $product->product_name;
		$this->template->view('products/rs_cards');
	}
	
	public function edit_product_cards()
	{
		$product_id = trim($this->input->post('product_id'));
		$qty_needed = trim($this->input->post('quantity_needed'));
		$qty_counted = trim($this->input->post('quantity_issued'));
		$type = trim($this->input->post('type'));
		$group_products_array = $this->input->post('group_products_array');
		
		sort($group_products_array);
		$group_product_ids = "";
		if(!empty($group_products_array)) $group_product_ids = implode(",", $group_products_array);
		else $group_product_ids = 0;
		
		//check if combination already exists
		$product_card_where = array(
			'product_id' => $product_id,
			'group_product_ids' => $group_product_ids
		);
		$existing_entry = $this->items_model->get_product_cards($product_card_where);
		
		if(!empty($existing_entry))
		{
			$this->return_json('error', 'Product combination already exists.');
			return;
		}
		
		foreach($group_products_array as $g)
		{
			$data = array(
				'product_id' => $g,
				'group_product_ids' => $group_product_ids,
				'qty_needed' => $qty_needed,
				'qty_counted' => $qty_counted
			);
			$this->items_model->insert_product_cards($data);
			
			$product_data = array(
				'is_igpsm' => 1
			);
			$this->items_model->update_product($product_data, array('product_id' => $g));
		}
		
		$this->return_json('ok', 'Product successfully tagged as releasing cards.');
		return;
	}
	
	public function remove_product_cards()
	{
		$product_id = trim($this->input->post('product_id'));
		$group_products_array = $this->input->post('group_products_array');
		
		$where = array(
			'product_id' => $product_id,
			'group_product_ids' => $group_products_array
		);
		$product_card = $this->items_model->get_product_cards($where);
		if(empty($product_card))
		{
			$this->return_json('error', 'Product Card not found.');
			return;
		}
		
		$this->items_model->delete_product_cards($where);
		
		$this->return_json('ok', 'Product Card successfully removed.');
		return;
	}
	
	public function export_excel()
	{	
		$search_by = trim($this->input->post("search_option"));
		$search_text = trim($this->input->post("search_string"));
		
		$search_url = "";
		
		if (($search_text == "") || empty($search_text)) {
			$where = NULL;					
		} else {
			$where = "{$search_by} LIKE LOWER('%{$search_text}%')";
		}
		
		$this->load->library('PHPExcel');
		$this->load->library('PHPExcel/IOFactory');
		$objPHPExcel = new PHPExcel();
		
		$title = 'Products List';
		
		$objPHPExcel->getProperties()->setTitle($title)->setDescription("Exported List of Products");
		$start_column_num = 4;

		$objPHPExcel->setActiveSheetIndex(0);
		$objPHPExcel->getActiveSheet()->setTitle($title);
		
		// auto resize columns
		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
		
		// set column header to bold		
		$objPHPExcel->getActiveSheet()->getStyle('A' . $start_column_num)->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('B' . $start_column_num)->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('C' . $start_column_num)->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('D' . $start_column_num)->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('E' . $start_column_num)->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('F' . $start_column_num)->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('G' . $start_column_num)->getFont()->setBold(true);
			
		//center column names
		$objPHPExcel->getActiveSheet()->getStyle('A' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('B' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('C' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('D' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('E' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('F' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('G' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		
		$header = "VITAL C HEALTH PRODUCTS, INC.";
		$header2 = "List of Products";

		$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->mergeCells('A1:G1');
		$objPHPExcel->getActiveSheet()->setCellValue('A1', $header);
		
		$objPHPExcel->getActiveSheet()->getStyle('A2')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->mergeCells('A2:G2');
		$objPHPExcel->getActiveSheet()->setCellValue('A2', $header2);
		
		//set column names
		$objPHPExcel->getActiveSheet()->setCellValue('A' . $start_column_num, 'NAME');
		$objPHPExcel->getActiveSheet()->setCellValue('B' . $start_column_num, 'PRODUCT CODE');
		$objPHPExcel->getActiveSheet()->setCellValue('C' . $start_column_num, 'PRODUCT TYPE');
		$objPHPExcel->getActiveSheet()->setCellValue('D' . $start_column_num, 'STANDARD RETAIL PRICE');
		$objPHPExcel->getActiveSheet()->setCellValue('E' . $start_column_num, 'MEMBER PRICE'); 
		$objPHPExcel->getActiveSheet()->setCellValue('F' . $start_column_num, 'EMPLOYEE PRICE');
		$objPHPExcel->getActiveSheet()->setCellValue('G' . $start_column_num, 'IGPSM POINTS');

		$row = $start_column_num + 1;
		
		$objPHPExcel->getActiveSheet()->freezePane('A' . $row);
		
		/* --------------------- */
		
		$offset = 0;
		$rows_per_page = 1000;
		$total_records = $this->items_model->get_product_count($where);
		$pages = $total_records / $rows_per_page;
		$cnt = floor($pages);
		if ($pages - $cnt > 0) $cnt+=1;
		
		for ($page = 0; $page < $cnt; $page++)
		{
			$limit = array('rows' => 1000, 'offset' => $page * $rows_per_page);

			$products = $this->items_model->get_products($where, $limit,'item_name');

			//=======================
			//loop thru products list
			//=======================
			foreach($products as $p)
			{
				//set product name
				if($p->item_id == 0)
					$name = $p->product_name;
				elseif($p->item_id > 0)
					$name = $p->item_name;
				$objPHPExcel->getActiveSheet()->setCellValue('A'. $row, html_entity_decode($name));
			
				//set product type
				$product_types = $this->items_model->get_product_type_array();
				
				//set product code
				$objPHPExcel->getActiveSheet()->setCellValue('B'. $row, $p->product_code);
				
				$product_type_id = $p->product_type_id;
				if(isset($product_types[$product_type_id]))
					$prod_type = $product_types[$product_type_id];
				else
					$prod_type->name = "Product Type Not Found.";
				$objPHPExcel->getActiveSheet()->setCellValue('C'. $row, $prod_type->name);				
				
				//set prices
				$objPHPExcel->getActiveSheet()->setCellValue('D'. $row, $p->standard_retail_price);				
				$objPHPExcel->getActiveSheet()->getStyle('D' . $row)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
				
				$objPHPExcel->getActiveSheet()->setCellValue('E'. $row, $p->member_price);				
				$objPHPExcel->getActiveSheet()->getStyle('E' . $row)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
				
				$objPHPExcel->getActiveSheet()->setCellValue('F'. $row, $p->employee_price);				
				$objPHPExcel->getActiveSheet()->getStyle('F' . $row)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
				
				$objPHPExcel->getActiveSheet()->setCellValue('G'. $row, $p->igpsm_points);				
				$objPHPExcel->getActiveSheet()->getStyle('G' . $row)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
			
				$row++;
			}
		}
		
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="VitalC_list_of_products.xls"');
		header('Cache-Control: max-age=0');		
		$objWriter = IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
	}
	
	public function get_raffle_products() 
	{
		$product_id = $this->input->post('product_id');
		
		$product = $this->items_model->get_product_by_id($product_id);
		
		if(empty($product))
		{
			$this->return_json('error', "Product not found.");
			return;
		}
		
		$raffle = $this->items_model->get_raffle_product_by_product_id($product_id);
		
		$data = array();
		if(empty($raffle))
		{
			$qty_generated = 0;
			$qty_needed = 0;
		}
		else
		{	
			$qty_generated = $raffle->qty_generated;
			$qty_needed = $raffle->qty_needed;
		}
		
		$data = array(
			'qty_needed' => $qty_needed,
			'qty_generated' => $qty_generated,
			'is_active' => $product->is_raffle
		);
		
		$this->return_json('ok', 'Success', $data);
		return;
	}
	
	public function tag_raffle_products()
	{
		$product_id = $this->input->post('product_id');
		$is_active = $this->input->post('is_active');
		$qty_needed = $this->input->post('qty_needed');
		$qty_generated = $this->input->post('qty_generated');
		
		$data = array(
			'is_raffle' => $is_active
		);
		$this->items_model->update_product($data, array('product_id' => $product_id));
		
		$msg = "Product successfully tagged as not generated raffle entries.";
		if($is_active == 1)
		{
			$raffle = $this->items_model->get_raffle_product_by_product_id($product_id);
			
			if(empty($raffle))
			{
				$raffle_product_data = array(
					'product_id' => $product_id,
					'qty_needed' => $qty_needed,
					'qty_generated' => $qty_generated
				);
				$this->items_model->insert_raffle_products($raffle_product_data);
			}
			else
			{
				$raffle_product_data = array(
					'qty_needed' => $qty_needed,
					'qty_generated' => $qty_generated
				);
				$this->items_model->update_raffle_products($raffle_product_data, array('product_id' => $product_id));
			}
			$msg = "Product successfully tagged as generating raffle entries.";
		}
		
		$this->return_json('ok', $msg);
		return;
	}
}