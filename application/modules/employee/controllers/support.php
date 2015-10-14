<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Support extends Site_Controller {
	
	function __construct() {
		parent::__construct();		
		
		$this->load->model('tracking_model');
		$this->load->model('settings_model');
		
		// load pager library
		$this->load->library('pager');
	}
	
	public function index()
	{
		$this->view();
	}
	
	public function view()
	{
		$member_id = $this->member->member_id;
		$member = $this->members_model->get_member_by_id($member_id);
		$ticket_id = $this->input->get_post('ticket_id');
		
		$where = "member_id = {$member_id}";
		if(!empty($ticket_id))
		{
			$trimmed_ticket_id = ltrim($ticket_id, '0');
			$where .= " AND log_id = {$trimmed_ticket_id}";
		}
		
		$total_items = $this->tracking_model->get_ad_member_logs_count($where);
		
		// initialize pagination class
		// set pagination data
		$config = array(
		    'pagination_url' => '/members/support/view',
		    'total_items' => $total_items,
		    'per_page' => 10,
		    'uri_segment' => 4,
		);
		$this->pager->set_config($config);
		$limit = array('rows' => $this->pager->per_page, 'offset' => $this->pager->offset);
		
		$member_concerns = $this->tracking_model->get_ad_member_logs($where,$limit,"insert_timestamp DESC, update_timestamp DESC");		
		$support_types = $this->settings_model->get_setting_by_slug('support_types');
		$support_type_value = json_decode($support_types->value);
	
		if(!empty($support_types))
		{
			$support_type_options = "<select id='inquiry_type' name='inquiry_type'>";
			foreach($support_type_value as $k => $s)
			{
				$class = "";
				if($k == 'others')
				{
					$class = "class='other_inquiry'";
				}
				$support_type_options .= "<option {$class} value='{$s}'>{$s}</option>";
			}
			$support_type_options .= "</select>";
		}else
		{
			$support_type_options = "";
		}
		$open_string_inquiry_type = "<span id='others_inquiry' style='display:none;margin-left:20px'><input type='text' id='variable_inquiry' name='variable_inquiry' placeholder='Input Inquiry Here' validation='required' value='' /></span>";
		$inquiry_options = "<div style='display:inline;'>" . $support_type_options . $open_string_inquiry_type . "</div>";
	
		$this->template->inquiry_options = $inquiry_options;
		$this->template->member_concerns = $member_concerns;
		$this->template->member = $member;
		$this->template->member_id = $member_id;
		$this->template->view('support/list');
	}
	
	public function add_concern()
	{
		$member_id = $this->input->post('member_id');
		$details = $this->input->post('details');
		$inquiry_type = $this->input->post('inquiry_type');
		$others_inquiry = $this->input->post('others_inquiry');
		
		if(strtoupper($inquiry_type) == 'OTHERS') $selected_inquiry = $others_inquiry;
		else $selected_inquiry = $inquiry_type;
		
		$data = array(
			'member_id' => $member_id,
			'details' => $details,
			'inquiry_type' => $selected_inquiry,
			'status' => "OPEN"
		);
		$this->tracking_model->insert_ad_member_logs($data); 
		$log_id = $this->db->insert_id();
		
		$generated_code = str_pad($log_id, 6, "0", STR_PAD_LEFT);
		
		$params = array(
            "first_name"=>ucfirst($this->member->first_name),
            "generated_code"=>$generated_code,
			"inquiry_type" => $selected_inquiry
        );

		$data = array(
            "email"=>$this->member->email,
            "type"=> "support-concern",
            "params"=>$params
        );
		//send email to user
        Modules::run('jobs/notifications/send_email',$data);
		
		$this->return_json('ok', 'Your concern was successfully sent. A message containing your Ticket ID was sent to your email.');
		return;
	}
}
