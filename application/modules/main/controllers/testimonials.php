<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Testimonials extends Base_Controller {

	public function before()
	{
		parent::before();

	}

	public function __construct()
	{
		parent::__construct();

		// set navigation
		$this->set_navigation('news');

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
		$rows_per_page = 7;
		
		// initialize pagination class
		// set pagination data
		$config = array(
			'pagination_url' => '/main/testimonials/page/',
		    'total_items' => $this->contents_model->get_testimonial_count(),
		    'per_page' => $rows_per_page,
		    'uri_segment' => 4
		);

		$this->pager->set_config($config);		
		
		//get testimonials
		$where = NULL;
		$limit = array("rows"=>$this->pager->per_page,"offset"=>$this->pager->offset);
		$testimonials = $this->contents_model->get_testimonials($where,$limit);
		$data = array("testimonials"=>$testimonials,"page_title"=>"Testimonials");
		$content = $this->load->view('testimonials/list',$data,TRUE,'main');
		
		$this->display($content);
	}
	
	public function display($content)
	{
		$this->cache($this->cache_timeout); // implement cache
		$this->template->title = null;
		$this->set_navigation('testimonials');
		$this->template->selected_menu = $this->selected_menu;
		//$this->template->content = $content;
		$this->template->contents = $content;
		$this->template->view('view');		
		
	}
	
	public function featured()
	{		
		$limit = array("rows"=>3,"offset"=>0);
		$testimonial = $this->contents_model->get_featured_testimonial();
		$data = array("testimonial"=>$testimonial,"title"=>"Featured Testimonial");
		echo $this->load->view('testimonials/featured',$data,TRUE,'main');
		return;
	}
			
}