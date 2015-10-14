<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Featured extends Base_Controller {

	public function before()
	{
		parent::before();

	}

	public function __construct()
	{
		parent::__construct();

		
		// load contents model
		$this->load->model("contents_model");

	}

	public function index()
	{
		$this->promos();
	}

	public function promos($slug = "")
	{
		$promo = $this->contents_model->get_featured_promo_by_slug($slug);
		
		
		if(!is_null($promo) && !empty($promo))
		{
			$this->_display('home',$promo->promo_description,$promo->promo_title);
		}
		else
		{
			$this->_display('home',"<strong>Promo does not exist.</strong>");
		}
		
	}

	public function _display($menu,$content,$title="")
	{
		$this->cache($this->cache_timeout); // implement cache
		
		// set navigation
		$this->set_navigation($menu);
		$this->template->selected_menu = $this->selected_menu;
		$this->template->title = $title;
		$this->template->content = $content;
		$this->template->view('view');	

	}


}