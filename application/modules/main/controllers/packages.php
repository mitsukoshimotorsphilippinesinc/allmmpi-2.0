<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Packages extends Base_Controller {

	public function before()
	{
		parent::before();

	}

	public function __construct()
	{
		parent::__construct();
		
		// set navigation
		$this->set_navigation('products');
		
		// load contents model
		$this->load->model("items_model");
		$this->load->library("pager");

	}
	
	public function index() 
	{
		$this->view();
	}
	
	public function view($product_type_id = 0)
	{	
		/*$packages_list = array();
		$package_types_list = array();
		
		$packages = $this->items_model->get_packages();
		$package_types = $this->items_model->get_package_types("is_visible = 1");
		
		if(!is_null($package_types) && !empty($package_types))
		{
			foreach($package_types as $pt)
			{
				$package_types_list[$pt->package_type_id] = $pt->package_type;
			}
		}
		
		if(!is_null($packages) && !empty($packages))
		{
			foreach($packages as $p)
			{
				if(!array_key_exists($p->package_type_id, $packages_list)) $packages_list[$p->package_type_id] = array();

				$swappable_count = $this->items_model->get_package_products_count("package_id = ". $p->package_id . " AND is_swappable = 1");
				$p->has_swappable = 0;
				if ($swappable_count > 0) {
					$p->has_swappable = 1;
				}
				array_push($packages_list[$p->package_type_id], $p);
			}
		}
		
		$data = array("packages_list"=>$packages_list,"page_title"=>"Packages","package_types" => $package_types_list);
		$content = $this->load->view('packages/list',$data,TRUE,'main');
		
		$this->display($content);*/
		
		$where = array('is_visible' => 1);
		$pagination_url = '/main/packages/view/'.$product_type_id; //default
		if($product_type_id != 0 && $product_type_id != 1)
		{
			$where['product_type_id'] = $product_type_id;
			$pagination_url = '/main/packages/'.$product_line_id.'/';
		}elseif ($product_type_id == 0){
			//if product line id == 0 and product_id IN product_products
			$where['product_type_id >'] = 1	;
		}else
		{
			$pagination_url = '/main/packages/view/'.$product_line_id;
		}
		
		$config = array(
			'pagination_url' => $pagination_url,
			'total_items' => $this->items_model->get_product_count($where),
			'per_page' => 18,
			'uri_segment' => 5,
		);
		$this->pager->set_config($config);
				
		$products = $this->items_model->get_products($where, array('rows' => $this->pager->per_page, 'offset' => $this->pager->offset));
		//$products = $this->items_model->get_products($where);
	
		$this->template->products = $products;
		$this->template->view('packages/list');
	}
	
	public function packages($package_id = 0)
	{

		$package = $this->items_model->get_package_by_id($package_id);
		
		$data = array("package"=>$package);
		$content = $this->load->view('packages/view',$data,TRUE,'main');

		$this->display($content);
	}
	
	public function display($content)
	{
		$this->cache($this->cache_timeout); // implement cache
		
		$this->template->selected_menu = $this->selected_menu;
		$this->template->content = $content;
		$this->template->view('view');	
		
	}
}