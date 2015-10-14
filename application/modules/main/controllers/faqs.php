<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Faqs extends Base_Controller {

	public function before()
	{
		parent::before();

	}

	public function __construct()
	{
		parent::__construct();

		// load contents model
		$this->load->model("contents_model");
		
		// load pager library
		$this->load->library('pager');

	}
	
	public function index() 
	{
		$this->page();
	}
	
	public function page()
	{		
		$rows_per_page = 10;
		
		// initialize pagination class
		// set pagination data
		$config = array(
			'pagination_url' => '/main/faqs/page/',
		    'total_items' => $this->contents_model->get_faqs_count("is_published = 1"),
		    'per_page' => $rows_per_page,
		    'uri_segment' => 4
		);

		$this->pager->set_config($config);		
		
		//get faq
		$limit = array("rows"=>$this->pager->per_page,"offset"=>$this->pager->offset);
		$faqs = $this->contents_model->get_faqs("is_published = 1",$limit,"ordering ASC");
		$data = array("faqs"=>$faqs,"page_title"=>"Frequently Asked Questions");
		$contents = $this->load->view('faqs/list',$data,TRUE,'main');
		
		$this->display($contents);
	}
	
	public function display($contents)
	{
		$this->cache($this->cache_timeout); // implement cache
		$this->template->title = null;
		$this->template->contents = $contents;
		$this->template->view('view');	
		
	}		
}