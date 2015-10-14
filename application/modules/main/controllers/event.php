<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Event extends Base_Controller {

	public function before()
	{
		parent::before();

	}

	public function __construct()
	{
		parent::__construct();

		// set navigation
		$this->set_navigation('events');

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
			'pagination_url' => '/main/event/page/',
		    'total_items' => $this->contents_model->get_news_count(),
		    'per_page' => $rows_per_page,
		    'uri_segment' => 4
		);

		$this->pager->set_config($config);		
		
		//get event
		$limit = array("rows"=>$this->pager->per_page,"offset"=>$this->pager->offset);
		$events = $this->contents_model->get_latest_events($limit);
		$data = array("events"=>$events,"page_title"=>"Events");
		$content = $this->load->view('events/list',$data,TRUE,'main');
		
		$this->display($content);
	}
	
	public function view()
	{
		$news_id = $this->uri->segment(4);
		
		$event = $this->contents_model->get_news_by_id($news_id);
		
		$data = array("event"=>$event);
		$content = $this->load->view('events/view',$data,TRUE,'main');

		$this->display($content);
	}
	
	public function tags() 
	{
		$tag = $this->uri->segment(4);
		
		$rows_per_page = 5;

		// set tag for condition
		$tags = explode("-",$tag);

		$where = "news_type_id = 2";
		$ctr = 0;
		
		if(!empty($tags)) $where .= " AND ";
		
		foreach ($tags as $t) 
		{
			$ctr++;
			if ($ctr==1) $or = '';
			else $or = ' OR ';
			$where .= "{$or}tags like '%" . $t . "%'";	
		}

		
		// initialize pagination class
		// set pagination data
		$config = array(
			'pagination_url' => '/main/event/tags/'. $tag,
		    'total_items' => count($this->contents_model->get_news($where)),
		    'per_page' => $rows_per_page,
		    'uri_segment' => 5
		);

		$this->pager->set_config($config);		
		
		$limit = array("rows"=>$this->pager->per_page,"offset"=>$this->pager->offset);
		$order_by = "news_id DESC";
		$events = $this->contents_model->get_news($where,$limit,$order_by);
		$data = array("events"=>$events,"page_title"=>"Events");
		$content = $this->load->view('events/list',$data,TRUE,'main');

		$this->display($content);
	}
	
	public function display($content)
	{
		$this->template->selected_menu = $this->selected_menu;
		$this->template->content = $content;
		$this->template->view('view');	
		
	}
	
	public function featured()
	{		
		$limit = array("rows"=>3,"offset"=>0);
		$events = $this->contents_model->get_latest_events($limit);
		$data = array("events"=>$events,"page_title"=>"Events");
		echo $this->load->view('events/list',$data,TRUE,'main');
		return;
	}
	
	public function snippets()
	{
		$date = date("Y-m-d");
		$where = "`is_published` = 1 AND `news_type_id` = 2 AND `start_date` >= '{$date}'";
		$limit = array("rows"=>3,"offset"=>0);
		$order_by = "start_date ASC";
		$events = $this->contents_model->get_news($where,$limit);
		$data = array("events"=>$events,"title"=>"Upcoming Events");
		echo $this->load->view('events/widget',$data,TRUE,'main');
		return;
	}

			
}