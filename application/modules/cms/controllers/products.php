<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Products extends CMS_Controller
{
	function __construct()
	{
  		parent::__construct();

		$this->load->model('contents_model');
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
		$products = $this->contents_model->get_featured_products("`feature_type_id` = 1",null,"order_id ASC");

		$sql = "SELECT * FROM `is_products_view` WHERE `item_id` > 0 AND `is_visible` = 1 AND `product_id` NOT IN(SELECT `product_id` FROM `sm_featured_products`) ORDER BY `product_name` ASC";
		$query = $this->db->query($sql);

		$this->template->available_products = $query->result();
		$this->template->max_order = $this->contents_model->get_featured_products_count("`feature_type_id` = 1");
		$this->template->products = $products;
		$this->template->view('products/list');
	}

	public function add()
	{
		$product_id = $this->input->post("product_id");
		if ($_POST)
		{
			$max_order = $this->contents_model->get_featured_products_count("`feature_type_id` = 1");
			// insert the new news
			$data = array(
				'product_id' => $product_id,
				'feature_type_id' => 1,
				'title' => 'Featured',
				'user_id' => $this->user->user_id,
				'order_id' => $max_order + 1
			);
			$this->contents_model->insert_featured_products($data);
			
			$insert_id = $this->contents_model->insert_id();
			
			//logging of action
			$details_after = array('id' => $insert_id, 'details' => $data);
			$details_after = json_encode($details_after);
			$add_product_log_data = array(
				'user_id' => $this->user->user_id,
				'module_name' => 'PRODUCTS',
				'table_name' => 'sm_featured_products',
				'action' => 'ADD',
				'details_after' => $details_after,
				'remarks' => "",
			);

			$this->tracking_model->insert_logs('admin', $add_product_log_data);
			
			$this->return_json("ok");
			return;
		}

	}

	public function delete($product_id)
	{
		$product = $this->contents_model->get_featured_product_by_product_id($product_id);

		if (!empty($product_id))
		{

			$max_order = $this->contents_model->get_featured_products_count("`feature_type_id` = 1");

			if($product->order_id != $max_order)
			{
				$other_nav = $this->contents_model->get_featured_products(array("feature_type_id" => 1, "order_id" => $product->order_id + 1));
				
				//update the navigations after this entry
				while(!empty($other_nav))
				{
					$other_nav = $other_nav[0];
					$data = array(
						'order_id' => $other_nav->order_id - 1,
					);
					$this->contents_model->update_featured_products($data, array('product_id' => $other_nav->product_id));
					
					$details_before = array('id' => $other_nav->product_id, 'details' => array('order_id' => $other_nav->order_id));
					$details_before = json_encode($details_before);

					$details_after = array('id' => $other_nav->product_id, 'details' => $data);
					$details_after = json_encode($details_after);
					$update_product_log_data = array(
						'user_id' => $this->user->user_id,
						'module_name' => 'PRODUCTS',
						'table_name' => 'sm_featured_products',
						'action' => 'UPDATE',
						'details_before' => $details_before,
						'details_after' => $details_after,
						'remarks' => "",
					);

					$this->tracking_model->insert_logs('admin', $update_product_log_data);
					
					$other_nav = $this->contents_model->get_featured_products(array("feature_type_id" => 1, "order_id" => $other_nav->order_id + 1));

				}
			}

			$this->contents_model->delete_featured_products(array('product_id' => $product_id));
			
			//logging of action
			$details_before = array('id' => $product_id, 'details' => $product);
			$details_before = json_encode($details_before);
			
			$delete_product_log_data = array(
				'user_id' => $this->user->user_id,
				'module_name' => 'PRODUCTS',
				'table_name' => 'sm_featured_products',
				'action' => 'DELETE',
				'details_before' => $details_before,
				'remarks' => "",
			);

			$this->tracking_model->insert_logs('admin', $delete_product_log_data);
			
		}

		redirect('/cms/products');
		return;
	}

	public function view_product() {

		$product_id = $this->input->post("product_id");

		$product = $this->items_model->get_product_by_id($product_id);

		if(empty($product))
		{
			$this->return_json("error","This product does not exist!");
			return;
		}

		$feature_html = $this->_feature_html($product);

		$feature_details = $feature_html["feature_details"];
		$options = $feature_html["options"];

		$product_description_html = "<span id='product_description_text'>{$product->item_description}</span>&nbsp;&nbsp;&nbsp;<a id='edit_product_description' class='btn btn-small btn-primary' data='{$product_id}'>Edit</a>";

		$image = "http://placehold.it/200x200";

		$product_image = json_decode($product->image_filename);

		if(!empty($product_image))
		{
			foreach($product_image as $pi)
				if($pi->is_default) $image = "{$this->config->item('base_url')}/{$pi->url}?v=".floor(rand(0, 999999));
		}
		
		$data = array(
			"product_picture" => "<img src='{$image}'>",
			"product_id" => $product->product_id,
			"product_name" => $product->item_name,
			"product_description" => $product_description_html,
			"available_features" => $options,
			"feature_details" => $feature_details
		);

		$this->return_json("ok", "Show Product", $data);
		return;
	}

	public function product_features()
	{

		$product_id = $this->input->post("product_id");
		$feature_type_id = $this->input->post("feature_type_id");
		$action = $this->input->post("action");

		$product = $this->items_model->get_product_by_id($product_id);

		if(empty($product))
		{
			$this->return_json("error","This product does not exist!");
			return;
		}

		$feature = $this->contents_model->get_product_feature_types_by_id($feature_type_id);

		if(empty($feature))
		{
			$this->return_json("error","This feature does not exist!");
			return;
		}

		$data = array(
			"product_id" => $product_id,
			"feature_type_id" => $feature_type_id
		);

		if($action == "add")
		{
			$data["title"] = $feature->title;
			$data["user_id"] = $this->user->user_id;
			$this->contents_model->insert_featured_products($data);
		}
		elseif($action == "remove")
		{
			$this->contents_model->delete_featured_products($data);
		}
		else
		{
			$this->return_json("error","This action does not exist.");
			return;
		}

		$feature_html = $this->_feature_html($product);

		$this->return_json("ok","Feature successfully added.",array("available_features" => $feature_html["options"],"feature_details" => $feature_html["feature_details"]));
		return;
	}

	public function _feature_html($product)
	{
		$features = $this->contents_model->get_featured_product_by_product_id($product->product_id);

		$feature_type_ids = array();
		$feature_details = "";
		if(!empty($features))
		{
			foreach($features as $f)
			{
				array_push($feature_type_ids,$f->feature_type_id);
				$feature_details .= "<tr><td>{$f->title}</td><td>{$f->insert_timestamp}</td><td><a data='$product->product_id|{$f->feature_type_id}' class='btn btn-danger btn-small btn-remove-feature' title='remove'><i class='icon-remove icon-white'></i></a></td></tr>";
			}
		}
		else
		{
			$feature_details = "<tr><td colspan='3'>No Features Found</td></tr>";
		}

		$available_features = $this->contents_model->get_available_product_feature_types($feature_type_ids);

		$options = "";
		if(!empty($available_features))
		{
			foreach($available_features as $available)
			{
				$options .= "<option value='{$available->feature_type_id}'>{$available->title}</option>";
			}
		}


		return array("feature_details" =>$feature_details, "options" => $options);
	}

	public function edit_product_description()
	{
		$product_id = $this->input->post("product_id");
		$product_description = $this->input->post("product_description");

		$product = $this->items_model->get_product_by_id($product_id);

		if(empty($product))
		{
			$this->return_json("error","This product does not exist!");
			return;
		}

		$item_id = $product->item_id;

		$data = array("item_description" => $product_description);

		$this->items_model->update_item($data,array("item_id" => $item_id));

		$this->return_json("ok","Item Description successfully updated.");
		return;
		
	}
	
	public function update_image()
	{
		$filename = $this->input->post('filename');
		$product_id = $this->input->post('product_id');
		
		$data = array("image_filename"=>$filename);
		
		$this->items_model->update_product($data,"product_id = {$product_id}");
		
		$this->return_json('ok','');
	}

	public function gallery()
	{
		$_product_id = $this->input->post('_product_id');

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
		
		
		
		$file = $filename;

		$file = substr($file, strrpos($file,"/")+1);

		$this->return_json("ok","{$file} set as primary picture.",array("filename" => $filename));
		return;

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

		$product->image_filename = json_encode($new_images);

		$html_data = array(
			'product' => $product
		);

		$html = $this->load->view('/products/gallery', $html_data, TRUE);

		$this->return_json("ok","Image deleted sucessfully!",array("html" => $html));
		return;
	}

	public function order($direction,$product_id)
	{
		$direction = strtolower($direction);
		if ($direction == 'up' || $direction == 'down')
		{
			$this->template->max_order = $this->contents_model->get_featured_products_count("`feature_type_id` = 1");
			$sec = $this->contents_model->get_featured_product_by_product_id($product_id);

			// get switch item with
			$_other_order = 0;
			if ($direction == 'up' && $sec->order_id > 1)
				$_other_order = $sec->order_id - 1;

			if ($direction == 'down' && $sec->order_id < $max_order)
				$_other_order = $sec->order_id + 1;

			if ($_other_order > 0)
			{
				$_other_sec = $this->contents_model->get_featured_products(array("feature_type_id" => 1, "order_id" => $_other_order));
				$_other_sec = $_other_sec[0];
				$this->contents_model->update_featured_products(array('order_id' => $_other_sec->order_id), array('product_id' => $sec->product_id));
				
				//logging of action
				$details_before = array('id' => $sec->product_id, 'details' => array("order_id" => $sec->order_id));
				$details_after = array('id' => $sec->product_id, 'details' => array("order_id" => $_other_sec->order_id));

				$details_before = json_encode($details_before);
				$details_after = json_encode($details_after);
				$update_product_log_data = array(
					'user_id' => $this->user->user_id,
					'module_name' => 'PRODUCTS',
					'table_name' => 'sm_featured_products',
					'action' => 'UPDATE',
					'details_before' => $details_before,
					'details_after' => $details_after,
					'remarks' => "",
				);

				$this->tracking_model->insert_logs('admin', $update_product_log_data);
				
				$this->contents_model->update_featured_products(array('order_id' => $sec->order_id), array('product_id' => $_other_sec->product_id));
				
				//logging of action
				$details_before = array('id' => $_other_sec->product_id, 'details' => array("order_id" => $_other_sec->order_id));
				$details_after = array('id' => $_other_sec->product_id, 'details' => array("order_id" => $sec->order_id));

				$details_before = json_encode($details_before);
				$details_after = json_encode($details_after);
				$update_product_log_data = array(
					'user_id' => $this->user->user_id,
					'module_name' => 'PRODUCTS',
					'table_name' => 'sm_featured_products',
					'action' => 'UPDATE',
					'details_before' => $details_before,
					'details_after' => $details_after,
					'remarks' => "",
				);

				$this->tracking_model->insert_logs('admin', $update_product_log_data);
			}
		}

		redirect('/cms/products');
		return;
	}
}