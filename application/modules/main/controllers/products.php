<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Products extends Base_Controller {

	public function before()
	{
		parent::before();

	}

	public function __construct()
	{
		parent::__construct();

		// load contents model
		$this->load->model("items_model");

		// load pager library
		$this->load->library('pager');

	}
	
	public function index() 
	{
		$this->view();
	}
	
	public function view($product_line_id = 'all')
	{	
		$product_name = $this->input->get_post('search_string');
		
		if(empty($product_name))
		{
			//$where = array('is_visible' => 1);
			$where = "is_visible = 1";
			$pagination_url = '/main/products/view/'.$product_line_id; //default
			if($product_line_id != 0 && $product_line_id != 'all')
			{
				//$where['product_line_id'] = $product_line_id;
				$where .= " AND product_line_id = {$product_line_id}";
				$pagination_url = '/main/products/view/'.$product_line_id.'/';
				$product_line = $this->items_model->get_product_line_by_id($product_line_id);
				$header = $product_line->product_line;
			}elseif ($product_line_id == 0 && $product_line_id != 'all'){
				//if product line id == 0 and product_id IN product_products
				//$where['product_type_id IN'] = 1;
				
				//get is_package = 1
				$packages = $this->items_model->get_product_types("is_package = 1 AND is_visible = 1");
				$package_ids = array();
				foreach($packages as $p)
					array_push($package_ids, $p->product_type_id);
				$package_ids = implode(",", $package_ids);
				$where .= " AND product_type_id IN ({$package_ids})";
				$header = "Packages";
			}else
			{
				$pagination_url = '/main/products/view/'.$product_line_id;
				$header = "All Products	";
			}
			$where .= " AND is_active = 1";
		}
		else
		{
			$where = "product_name LIKE '%" . $product_name . "%' AND is_visible = '1' AND is_active = '1'";
			$pagination_url = '/main/products/view/'.$product_line_id;
		}	
		
		$config = array(
			'pagination_url' => $pagination_url,
			'total_items' => $this->items_model->get_product_count($where),
			'per_page' => 18,
			'uri_segment' => 5,
		);
		$this->pager->set_config($config);	
				
		$products = $this->items_model->get_products($where, array('rows' => $this->pager->per_page, 'offset' => $this->pager->offset));

		$product_lines = $this->items_model->get_product_lines();
	
		$this->template->products = $products;
		$this->template->product_lines = $product_lines;
		$this->template->selected_product_line = $product_line_id;
		$this->template->search_string = $product_name;
		$this->template->search_url = strlen($_SERVER['QUERY_STRING']) > 0 ? '?'.$_SERVER['QUERY_STRING'] : '';
		//$this->template->header = $header;
		$this->template->view('products/view');
	}
	
	public function display($content)
	{
		$this->cache($this->cache_timeout); // implement cache
		
		// set navigation
		$this->set_navigation('products');
		$this->template->selected_menu = $this->selected_menu;
		$this->template->content = $content;
		$this->template->view('view');	
		
	}

	public function view_products() {

		$p_category = trim($this->input->post("p_category"));
        $p_id = abs($this->input->post("p_id"));

		if ($p_category == 'package') {
			//get package
	        $p_details = $this->items_model->get_package_by_id($p_id);
		} else {
        	//get product
        	$p_details = $this->items_model->get_product_by_id($p_id);
		}

        $error_message = "";

        if(empty($p_details)) {
            if($error_message == "") $error_message = "Invalid Product/Package ID";
        }

		if ($p_category == 'package') {
			$p_name = $p_details->package_name;
		} else {
			$p_name = $p_details->item_name;
		}

        $data = array(
			"p_details"=>$p_details,
			"p_category"=>$p_category,
			"error_message" => $error_message
		);

		$json_details = $p_id."|".$p_details->standard_retail_price."|".$p_details->member_price."|".$p_name."|".$p_category;


		$html = $this->load->view('products/view',$data,TRUE);

		echo json_encode(array("status"=>1,"html"=>$html,"title"=>$p_name, "details"=>$json_details));

        return;
    }
}