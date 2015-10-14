<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Vouchers extends Site_Controller 
{	
	
	function __construct() 
	{
  		parent::__construct();

			
		$this->load->model('vouchers_model');
		$this->load->model('contents_model');
		$this->load->model('items_model');
		$this->load->model('raffles_model');
		
		// load pager library
		$this->load->library('pager');
	}
	
	public function index() 
	{ 
		$this->view();
	}
	
	public function view()
	{		
		
		$where = "member_id = {$this->member->member_id}";
		//$where ="";
		$total_items = $this->members_model->get_member_vouchers_count($where);
		
		// initialize pagination class
		// set pagination data
		$config = array(
		    'pagination_url' => '/members/vouchers/view/',
		    'total_items' => $total_items,
		    'per_page' => 10,
		    'uri_segment' => 4,
		);
		$this->pager->set_config($config);
		$limit = array('rows' => $this->pager->per_page, 'offset' => $this->pager->offset);
		
		
		$data= array(
			'member_id'=>$this->member->member_id			
		);
		
		$member_vouchers = $this->members_model->get_member_vouchers($data,$limit,"insert_timestamp DESC");		
		
		$voucher_raffle_entries = array();
		foreach($member_vouchers as $mv){
			$raffle_entries = $this->raffles_model->get_raffle_entries(array("reference_info"=>$mv->transaction_id));
			if(count($raffle_entries) >0)
				$voucher_raffle_entries[$mv->voucher_id] = $raffle_entries;
		}
		
		$this->template->voucher_raffle_entries = $voucher_raffle_entries;
		$this->template->member_vouchers = $member_vouchers;
		$this->template->current_page = 'vouchers';
		$this->template->view('vouchers/list');
	}
	
	public function display($voucher_code = 0) {

        $this->template->voucher_code = $voucher_code;
        $this->template->view('members/vouchers/display');
    }

    public function display_pdf($voucher_code = 0) {
        $this->load->library('mpdf');

		$terms_and_conditions = $this->contents_model->get_content_by_slug("voucher_terms_and_conditions");
		$footer = $this->contents_model->get_content_by_slug("voucher_footer");

		// get voucher_details
		$voucher_details = $this->members_model->get_member_voucher_by_code($voucher_code);
	
		if (empty($voucher_details)) {
			// missing voucher details
			// TODO :: what to do if missing voucher_details (just in case)
			
		} else {
			// check if last_name or first_name is null
			if (($voucher_details->last_name == NULL) || (trim($voucher_details->last_name) == "") || ($voucher_details->first_name == NULL) || (trim($voucher_details->first_name) == "")) {
				// get member_details using member_id
				$member_details = $this->members_model->get_member_by_id($voucher_details->member_id);
				
				$proper_owner_name = $member_details->last_name . ", " . $member_details->first_name . " " . $member_details->middle_name;				
			} else {
				$proper_owner_name = $voucher_details->last_name . ", " . $voucher_details->first_name . " " . $voucher_details->middle_name;
			}
			
			$proper_member_since = "";
			// member details if voucher is from member passed to another person
			if (($voucher_details->member_id == 0) || ($voucher_details->member_id == NULL) || (trim($voucher_details->member_id) == "")) {
				$proper_member_name = "N/A";
			} else {
				$member_details = $this->members_model->get_member_by_id($voucher_details->member_id);
				$proper_member_name = $member_details->last_name . ", " . $member_details->first_name . " " . $member_details->middle_name;	
				$proper_member_since = date("F j, Y", strtotime($member_details->insert_timestamp));
			}			
		}
		
		$raffle_entries = $this->raffles_model->get_raffle_entries(array("reference_info"=>$voucher_details->transaction_id));
		
		$data = array (
            "voucher_code" => $voucher_code,
			"proper_owner_name" => $proper_owner_name,
			"proper_member_name" => $proper_member_name,
			"proper_member_since" => $proper_member_since,
			"voucher_details"=> $voucher_details,
			"voucher_terms_and_conditions" => $terms_and_conditions->body,
			"raffle_entries" => $raffle_entries
        );
 		
		// for testing purposes
        
        $this->mpdf->WriteHTML($this->load->view('members/vouchers/display',$data,TRUE));
		$footer = "<hr/><div align='center' style='font-size:10px;'>{$footer->body}</div>";
		$this->mpdf->SetHTMLFooter($footer);
		
        $this->mpdf->Output('voucher_' . $voucher_code, 'I');
		
	}
}
