<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Pages extends Base_Controller {

	function __construct() {
		parent::__construct();
		$this->load->model("contents_model");

	}
    
    function index($slug = '', $site = 0) {
	
		$this->cache($this->cache_timeout);
	
		$body = "<div style='min-height:300px;'><p style='font-size:18px;'>Oops! You have encountered a page that does not exist! </p></div>";
		$title = "Page Not Found!";
		$not_found = true;
		
		if (!empty($slug)) {
			
			if (method_exists($this, $slug)) {
				$this->$slug($site);
				return;
			}
			
			if ($site == 1) {
				//$content = $this->site_model->get_site_conent_by_slug($slug);
				// find if there img src start with /uploads and replace it with /site/uploads
				//$content->body = str_replace("'/uploads", "'/site/uploads", $content->body);
				//$content->body = str_replace("\"/uploads", "\"/site/uploads", $content->body);
			} else {
				$content = $this->contents_model->get_content_by_slug($slug);
			}
			
			if ($content->is_active == 1) {

				if (!empty($content)) {
					$title = $content->title;
					$body = $content->body;
					$not_found = false;
				}

			} 	
		} 
		
		$data = array(
			'body' => $body,
			'title' => $title
		);
		
		if ($not_found) {
			$this->output->set_status_header('404');
		}

		$this->template->title = $title;
		$this->template->body = $body;
        $this->template->view('page_with_tweeter'); //load_view(array('view' => 'page_view', 'data' => $data, 'title' => $title));
		
    }


}

/* End of file */