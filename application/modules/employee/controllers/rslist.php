<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Rslist extends Site_Controller {
	
	function __construct() {
		parent::__construct();		
		
		$this->load->model('facilities_model');
		$this->load->model('members_model');
		$this->load->model('cards_model');
		
		// load pager library
		$this->load->library('pager');
	}
	
	public function index()
	{
		$this->page();
	}
	
	public function page()
	{
		$member_id = $this->member->member_id;
		$from_date = $this->input->get_post('from_date');
		$to_date = $this->input->get_post('to_date');
		$date_filter = $this->input->get_post('date_filter');			
		$account_id = $this->input->get_post('account_id');		
		
		$from_t = strtotime($from_date);
		$to_t = strtotime($to_date);
		
		$where = "member_id = " . $member_id;
		
		if(!empty($account_id) && $account_id != 'all')	$where .= " AND account_id = '" . trim($account_id) . "'";		
			
		$from_dt = $from_date;
		$to_dt = $to_date;
				
		if ($from_t !== false) $from_dt = date('Y-m-d', $from_t); 
		if ($to_t !== false) $to_dt = date('Y-m-d', $to_t); 
		
		if ($from_t !== false && $to_t !== false)
			$where .= " AND (DATE(used_timestamp) BETWEEN '{$from_dt}' AND '{$to_dt}') ";
		else if ($from_t !== false && $to_t === false)
			$where .= " AND used_timestamp >= '{$from_dt}'";
		else if ($from_t === false && $to_t !== false)
			$where .= " AND used_timestamp <= '{$to_dt}'";
		
		// get all account id of member		
		$member_account_details = $this->members_model->get_member_accounts("member_id = {$member_id}");
		
		$get_data = "?account_id=" . $account_id . "&from_date=" . $from_date . "&to_date=" . $to_date;
	
		// initialize pagination class
		// set pagination data		
		$config = array(
			'pagination_url' => '/members/rslist/page/',
		   	'total_items' => $this->cards_model->get_rs_card_count($where),
		    'per_page' => 20,
		    'uri_segment' => 4,
		);

		$this->pager->set_config($config);
			
		$this->template->transactions = $this->cards_model->get_rs_card($where, array('rows' => $this->pager->per_page, 'offset' => $this->pager->offset), 'used_timestamp DESC');
		$this->template->member_id = $member_id;
		$this->template->member_account_details = $member_account_details;
		$this->template->from_date = $from_date;
		$this->template->to_date = $to_date;
		$this->template->get_data = $get_data;

		if (empty($account_id)) 
			$account_id = "all";
			
		$between_timestamps = $from_date . " - " . $to_date;
		if (trim($between_timestamps) == "-") {
			$between_timestamps = "n/a";
		}
		
		$this->template->account_id = strtoupper($account_id);
		$this->template->between_timestamps = strtoupper($between_timestamps);
		$this->template->current_page = 'rslist';
		$this->template->view('rslist/list');
	}
	
}
