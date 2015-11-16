<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Pages extends Base_Controller {

	function __construct() {
		parent::__construct();
		$this->load->model("setting_model");

	}
    
    function index($slug = '', $site = 0) {
	
		$this->cache($this->cache_timeout);
	
		$body = "<div style='min-height:300px;'><p style='font-size:18px;'>Oops! Page that does not exist! </p></div>";
		$title = "Page Not Found!";
		$not_found = true;
		
		if (!empty($slug)) {
			
			if (method_exists($this, $slug)) {
				$this->$slug($site);
				return;
			}
			
			if ($site == 1) {
				// TODO				
			} else {
				$content = $this->setting_model->get_page_content_by_slug($slug);
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
        //$this->template->view('page_with_tweeter'); 
        $this->template->view('page_view'); 
		
    }


}

/* End of file */