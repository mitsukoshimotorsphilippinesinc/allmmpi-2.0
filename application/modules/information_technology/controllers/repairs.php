<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Repairs extends Admin_Controller {

	function __construct()
	{
		parent::__construct();
		
		$this->load->library('pager');
		$this->load->model('asset_model');
		$this->load->helper("breadcrumb_helper");

	}

	public $segment_name = "systems";

	public function index()
	{		
		$this->template->view('repairs/dashboard');
	}


	public function navigation()
	{
		$filter_by = trim($this->input->get("filter_option"));
		$search_by = trim($this->input->get("search_option"));
		$search_text = trim($this->input->get("search_string"));


		$search_url = "";

		if (($search_text == "") || empty($search_text)) {
			$where = NULL;			
		} else {
			$where = "{$search_by} LIKE LOWER('%{$search_text}%')";
			$search_url = "?search_option=" . $search_by . "&search_string=" . $search_text;
		}	


		// set pagination data
		$config = array(
		    'pagination_url' => '/information_technology/systems/navigation',
		    'total_items' => $this->navigation_model->get_navigation_count($where),
		    'per_page' => 5,
		    'uri_segment' => 4,
		);

		// search vars
		$this->template->search_by = $search_by;
		$this->template->search_text = $search_text;
		$this->template->search_url = $search_url;
		

		$this->pager->set_config($config);
		$this->template->navigations = $this->navigation_model->get_navigation(null, array('rows' => $this->pager->per_page, 'offset' => $this->pager->offset), 'title, priority_order');
		$this->template->view('information_technology/navigation/list');
	}



	public function alert_message()
	{
		$filter_by = trim($this->input->get("filter_option"));
		$search_by = trim($this->input->get("search_option"));
		$search_text = trim($this->input->get("search_string"));


		$search_url = "";

		if (($search_text == "") || empty($search_text)) {
			$where = NULL;			
		} else {
			$where = "{$search_by} LIKE LOWER('%{$search_text}%')";
			$search_url = "?search_option=" . $search_by . "&search_string=" . $search_text;
		}	


		// set pagination data
		$config = array(
		    'pagination_url' => '/information_technology/systems/navigation',
		    'total_items' => $this->asset_model->get_alert_message_count($where),
		    'per_page' => 5,
		    'uri_segment' => 4,
		);

		// search vars
		$this->template->search_by = $search_by;
		$this->template->search_text = $search_text;
		$this->template->search_url = $search_url;
		

		$this->pager->set_config($config);
		$this->template->alert_messages = $this->asset_model->get_alert_message(null, array('rows' => $this->pager->per_page, 'offset' => $this->pager->offset), 'insert_timestamp DESC');
		$this->template->view('information_technology/alert_message/list');
	}


}
