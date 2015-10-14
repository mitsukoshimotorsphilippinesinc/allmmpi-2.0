<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class News extends Base_Controller {

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
	
	public function view()
	{
		$news_id = $this->uri->segment(4);
		
		$news = $this->contents_model->get_featured_by_id($news_id);
		
		$data = array("news"=>$news);
		$contents = $this->load->view('news/view',$data,TRUE,'main');

		$this->display($contents);
	}
	
	public function page()
	{		
		$rows_per_page = 5;
		$where = "(type = 'news' OR type = 'event') AND is_published = 1";
		// initialize pagination class
		// set pagination data
		$config = array(
			'pagination_url' => '/main/news/page/',
		    'total_items' => $this->contents_model->get_featured_count($where),
		    'per_page' => $rows_per_page,
		    'uri_segment' => 4
		);

		$this->pager->set_config($config);		
		
		//get news
		$limit = array("rows"=>$this->pager->per_page,"offset"=>$this->pager->offset);
		//$news = $this->contents_model->get_latest_news($limit);
		$news = $this->contents_model->get_featured($where,$limit, 'insert_timestamp DESC');
		$data = array("news"=>$news);
		$contents = $this->load->view('news/list',$data,TRUE,'main');
		
		$this->display($contents,"News and Updates");
	}
	
	public function tags() 
	{
		$tag = $this->uri->segment(4);
		
		$rows_per_page = 5;

		// set tag for condition
		$tags = explode("-",$tag);

		$where = "(type = 'news' OR type = 'event') AND is_published = 1";
		$ctr = 0;
		foreach ($tags as $t) 
		{
			$ctr++;
			//if ($ctr==1) $or = ' ';
			//else 
			$or = ' OR ';
			$where .= " {$or}tags like '%" . $t . "%'";	
		}

		
		// initialize pagination class
		// set pagination data
		$config = array(
			'pagination_url' => '/main/news/tags/'. $tag,
		    'total_items' => $this->contents_model->get_featured_count($where),
		    'per_page' => $rows_per_page,
		    'uri_segment' => 5
		);

		$this->pager->set_config($config);		
		
		$limit = array("rows"=>$this->pager->per_page,"offset"=>$this->pager->offset);
		$order_by = "featured_id DESC";
		$news = $this->contents_model->get_featured($where,$limit,$order_by);
		$data = array("news"=>$news);
		$contents = $this->load->view('news/list',$data,TRUE,'main');

		$this->display($contents,"News and Updates");
	}
	
	public function display($contents,$title="")
	{
		$this->cache($this->cache_timeout); // implement cache
		
		$this->set_navigation('news');
		$this->template->selected_menu = $this->selected_menu;
		$this->template->title = $title;
		$this->template->contents = $contents;
		$this->template->view('view');	
		
	}
	
	public function featured()
	{		
		$limit = array("rows"=>3,"offset"=>0);
		$news = $this->contents_model->get_latest_news($limit);
		$data = array("news"=>$news);
		echo $this->load->view('news/list',$data,TRUE,'main');
		return;
	}

	public function snippets()
	{		
		$limit = array("rows"=>3,"offset"=>0);
		$news = $this->contents_model->get_latest_news($limit);
		$data = array("news"=>$news,"title"=>"News and Updates");
		echo $this->load->view('news/widget',$data,TRUE,'main');
		return;
	}
	
	public function calendar() 
	{
		$date = $this->uri->segment(4);

		$where = "(type = 'news' OR type = 'event') AND is_published = 1 AND (DATE(`start_date`) = '{$date}' OR (DATE(`start_date`) <= '{$date}' AND DATE(`end_date`) >= '{$date}'))";

		// initialize pagination class
		// set pagination data
		$config = array(
			'pagination_url' => '/main/news/tags/'. $date,
		    'total_items' => $this->contents_model->get_featured_count($where),
		    'per_page' => 5,
		    'uri_segment' => 5
		);

		$this->pager->set_config($config);		
		
		$limit = array("rows"=>$this->pager->per_page,"offset"=>$this->pager->offset);
		$news = $this->contents_model->get_featured($where,$limit);
		
		//set priority for display
		$starts_today = array(); //highest
		$continues_today = array(); //mid
		$ends_today = array(); //lowest
		foreach($news as $n)
		{
			if(strtotime(date("Y-m-d",strtotime($n->start_date))) == strtotime($date))
			{
				array_push($starts_today,$n);
			}
			elseif(strtotime(date("Y-m-d",strtotime($n->end_date))) == strtotime($date))
			{
				array_push($ends_today,$n);
			}
			else
			{
				array_push($continues_today,$n);
			}
		}
		
		$start_date = array();
		$end_date = array();
		foreach($starts_today as $k => $v)
		{
			$start_date[$k] = $v->start_date;
			$end_date[$k] = $v->end_date;
		}
		array_multisort($start_date, SORT_ASC, $end_date, SORT_ASC, $starts_today);
		
		$start_date = array();
		$end_date = array();
		foreach($continues_today as $k => $v)
		{
			$start_date[$k] = $v->start_date;
			$end_date[$k] = $v->end_date;
		}
		array_multisort($start_date, SORT_ASC, $end_date, SORT_ASC, $continues_today);
		
		$start_date = array();
		$end_date = array();
		foreach($ends_today as $k => $v)
		{
			$start_date[$k] = $v->start_date;
			$end_date[$k] = $v->end_date;
		}
		array_multisort($start_date, SORT_ASC, $end_date, SORT_ASC, $ends_today);
		$sorted_news = array();
		array_splice($sorted_news,count($sorted_news),0,$starts_today);
		array_splice($sorted_news,count($sorted_news),0,$continues_today);
		array_splice($sorted_news,count($sorted_news),0,$ends_today);
		$data = array("news"=>$sorted_news);
		$contents = $this->load->view('news/list',$data,TRUE,'main');

		$this->display($contents,"News and Updates");
	}
			
}