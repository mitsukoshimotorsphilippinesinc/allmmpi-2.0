<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Rss extends Base_Controller {

	public function before()
	{
		parent::before();
	}

	public function __construct()
	{
		parent::__construct();
		// load contents model
		$this->load->model("contents_model");
		// load users model
		$this->load->model("users_model");
		// load xml helper
		$this->load->helper('xml');
	}
	
	public function index()
	{	
		$data['encoding'] = 'utf-8';
        $data['feed_name'] = 'Vital C Health Products, Inc';
        $data['feed_url'] = $this->config->item('base_url');
        $data['page_description'] = 'Official Website of Vital C Health Products, Inc.';
        $data['page_language'] = 'en';
        //$data['creator_email'] = 'Derek Allard is at derek at derekallard dot com';

        // get content
	    $limit = array("rows"=>10,"offset"=>0);
		//$news = $this->contents_model->get_latest_news($limit);
		$news = $this->contents_model->get_news("",$limit, 'insert_timestamp DESC');

        $data['posts'] = $news;
        header("Content-Type: text/xml");
        $this->load->view('feed/rss', $data);
	}
}