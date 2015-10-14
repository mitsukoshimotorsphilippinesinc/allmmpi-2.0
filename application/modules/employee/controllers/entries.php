<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Entries extends Site_Controller {
	
	function __construct() {
		parent::__construct();		
		
		$this->load->model('members_model');
		$this->load->model('raffles_model');
		
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
		$promo_type = $this->input->get_post('promo_type');
			
		if ($from_date == "") $from_date = "2012-12-08";
		if ($to_date == "") $to_date = date("Y-m-d");
		
		
		;
		
		$from_t = strtotime($from_date);
		$to_t = strtotime($to_date);
		
		// get promo list
		$rpd_where = "is_active = 1";
		$raffle_promo_details = $this->raffles_model->get_raffles($rpd_where, NULL, NULL);
		
		$where = "member_id = " . $member_id . " OR sponsor_member_id = " . $member_id;
		
		if(!empty($promo_type) && $promo_type != 'all')	$where .= " AND raffle_id = '" . strtoupper(trim($promo_type)) . "'";		
			
		$from_dt = $from_date;
		$to_dt = $to_date;
				
		if ($from_t !== false) $from_dt = date('Y-m-d H:i:s', $from_t); 
		if ($to_t !== false) $to_dt = date('Y-m-d H:i:s', $to_t); 
		
		if ($from_t !== false && $to_t !== false)
			$where .= " AND (date(insert_timestamp) BETWEEN '{$from_dt}' AND '{$to_dt}') ";
		else if ($from_t !== false && $to_t === false)
			$where .= " AND DATE(insert_timestamp) >= '{$from_dt}'";
		else if ($from_t === false && $to_t !== false)
			$where .= " AND DATE(insert_timestamp) <= '{$to_dt}'";
		
		//// get all account id of member		
		//$member_account_details = $this->members_model->get_member_accounts("member_id = {$member_id}");
		
		$get_data = "?promo_type=" . $promo_type . "&from_date=" . $from_date . "&to_date=" . $to_date;
	
		// initialize pagination class
		// set pagination data		
		$config = array(
			'pagination_url' => '/members/entries/page/',
		   	'total_items' => $this->raffles_model->get_raffle_entries_count($where),
		   	'per_page' => 20,
		    'uri_segment' => 4,
		);

		$this->pager->set_config($config);
	
		$this->template->transactions = $this->raffles_model->get_raffle_entries($where, array('rows' => $this->pager->per_page, 'offset' => $this->pager->offset), 'insert_timestamp DESC');	
		$this->template->member_id = $member_id;				
		$this->template->from_date = $from_date;
		$this->template->to_date = $to_date;
		$this->template->get_data = $get_data;
	
		if (empty($promo_type)) 
			$promo_type = "all";	
			
		$between_timestamps = $from_date . " - " . $to_date;
		if (trim($between_timestamps) == "-") {
			$between_timestamps = "n/a";
		}
		
		$this->template->raffle_promo_details = $raffle_promo_details; 	
		$this->template->promo_type = strtoupper($promo_type);
		$this->template->between_timestamps = strtoupper($between_timestamps);
		$this->template->current_page = 'entries';
		$this->template->view('entries/list');
	}
	
}
