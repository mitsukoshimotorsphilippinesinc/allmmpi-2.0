<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Wall extends  Base_Controller
{
	
	
	function __construct() 
	{
  		parent::__construct();

		$this->load->model("contents_model");
	}

	public function index() 
	{
		$this->cache($this->cache_timeout); // implement cache
		
		$this->template->view('wall_view');
	}

	public function grandmasters() 
	{
		$this->cache($this->cache_timeout); // implement cache
		
		$this->template->grandmasters = $this->contents_model->get_featured_members(array("achievement_id"=>1),null,"`member_name` ASC");
		$this->template->view('wall/grandmasters_view');
	}

	public function presidents() 
	{
		$this->cache($this->cache_timeout); // implement cache
		
		$this->template->presidents = $this->contents_model->get_featured_members(array("achievement_id"=>2),null,"`member_name` ASC");
		$this->template->view('wall/presidents_view');
	}

	public function millionaires() 
	{
		$this->cache($this->cache_timeout); // implement cache
		
		$this->template->millionares = $this->contents_model->get_featured_members(array("achievement_id"=>3),null,"`member_name` ASC");
		$this->template->view('wall/millionares_view');
	}

	public function results() 
	{
		$this->cache($this->cache_timeout); // implement cache
		
		$this->template->featured = $this->contents_model->get_results(array("is_published" => true,"is_featured" => true));
		$this->template->results = $this->contents_model->get_results(array("is_published" => true,"is_featured" => false));
		$this->template->view('wall/results_view');
	}
}
?>