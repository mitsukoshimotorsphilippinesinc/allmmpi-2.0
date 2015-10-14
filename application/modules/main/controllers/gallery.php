<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Gallery extends Base_Controller {

	public function before()
	{
		parent::before();

	}

	public function __construct()
	{
		parent::__construct();

		// set navigation
		$this->set_navigation('gallery');

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
		$rows_per_page = 5;

		// initialize pagination class
		// set pagination data
		$config = array(
			'pagination_url' => '/main/gallery/page/',
		    'total_items' => $this->contents_model->get_gallery_count(array("is_published" => TRUE)),
		    'per_page' => $rows_per_page,
		    'uri_segment' => 4
		);

		$this->pager->set_config($config);

		//get news
		$limit = array("rows"=>$this->pager->per_page,"offset"=>$this->pager->offset);
		$galleries = $this->contents_model->get_galleries(array("is_published" => TRUE),$limit,"insert_timestamp DESC");
		
		foreach($galleries as $k => $g)
		{	
			$picture_limit = array("rows"=>8,"offset"=>0);
			$gallery_pictures = $this->contents_model->get_gallery_pictures(array('gallery_id' => $g->gallery_id),$picture_limit);
			$g->gallery_pictures = $gallery_pictures;
			$galleries[$k] = $g;
		}
		$data = array("galleries"=>$galleries);
		$contents = $this->load->view('gallery/list',$data,TRUE,'main');

		$this->display($contents,"Galleries");
	}

	public function view()
	{
		$gallery_id = $this->uri->segment(4);

		$gallery = $this->contents_model->get_galleries(array("gallery_id" => $gallery_id, "is_published" => TRUE));
		if(!empty($gallery) && !is_null($gallery)) $gallery = $gallery[0];
		$gallery_pictures = $this->contents_model->get_gallery_pictures_by_gallery_id($gallery_id);
		$data = array("gallery"=>$gallery,"gallery_pictures" => $gallery_pictures);
		$contents = $this->load->view('gallery/view',$data,TRUE,'main');

		$this->display($contents);
	}

	public function display($contents,$title="")
	{
		$this->cache($this->cache_timeout); // implement cache
		
		$this->set_navigation('gallery');
		$this->template->selected_menu = $this->selected_menu;
		$this->template->title = $title;
		$this->template->contents = $contents;
		$this->template->view('view');	

	}


}