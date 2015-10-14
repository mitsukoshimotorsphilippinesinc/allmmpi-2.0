<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Content extends Base_Controller {

	public function __construct()
	{
		parent::__construct();

		// load contents model
		$this->load->model("contents_model");
	}
	
	public function view($slug)
	{
		$content = $this->contents_model->get_content_by_slug($slug);						
		$content = "<h2>{$content->title}</h2>{$content->body}";
		return $content;
	}
}