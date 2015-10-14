<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Announcement extends Site_Controller 
{
	
	function __construct() 
	{
  		parent::__construct();
		$this->load->model('tracking_model');
		$this->load->model('asset_model');
		$this->load->model('setting_model');

		$this->load->library('pager');
	}
	
	public function index()
	{
		$this->page();
	}
	
	public function page()
	{		
		$from_date = $this->input->get_post('from_date');
		$to_date = $this->input->get_post('to_date');
		
		$from_t = strtotime($from_date);
		$to_t = strtotime($to_date);
			
		$from_dt = $from_date;
		$to_dt = $to_date;
				
		if ($from_t !== false) $from_dt = date('Y-m-d', $from_t); 
		if ($to_t !== false) $to_dt = date('Y-m-d', $to_t); 
		
		//$get_data = "?from_date=" . $from_date . "&to_date=" . $to_date;
		$get_data = "";
	
		$where = "";

		// initialize pagination class
		// set pagination data		
		$config = array(
			'pagination_url' => '/employee/announcement/page',
		   	'total_items' => $this->asset_model->get_announcement_count($where),
		    'per_page' => 2,
		    'uri_segment' => 4,
		);
		
		$this->pager->set_config($config);

		$this->template->announcements = $this->asset_model->get_announcement($where, array('rows' => $this->pager->per_page, 'offset' => $this->pager->offset), 'insert_timestamp DESC');
		
		$this->template->from_date = $from_date;
		$this->template->to_date = $to_date;
		$this->template->get_data = $get_data;

			
		$between_timestamps = $from_date . " - " . $to_date;
		if (trim($between_timestamps) == "-") {
			$between_timestamps = "n/a";
		}
		
		$this->template->between_timestamps = strtoupper($between_timestamps);
		$this->template->current_page = 'announcement';
		$this->template->view('announcement/dashboard');
	}

	
}
