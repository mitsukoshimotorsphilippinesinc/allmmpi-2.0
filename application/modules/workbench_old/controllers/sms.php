<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Sms extends Base_Controller {

	function __construct() {
		parent::__construct();
	}

	public function index() {
		
		$sms_qued = false;
		
		if($_POST){
			
			$mobile = trim($this->input->post('mobile'));
			$message = trim($this->input->post('message'));
			
			$this->form_validation->set_rules('mobile', 'Mobile Number', 'trim|required');
			$this->form_validation->set_error_delimiters('<span>', '</span>');

			if ($this->form_validation->run()) 
			{
				$sms_params = array(
					'message' => $message
				);
				$sms_data = array(
					"member_id"=>0,
					"mobile_number"=>$mobile,
					"type"=>"test-sms",
					"params"=>$sms_params
				);
				
				$sms_qued = true;
				
			}
			
		}
		
		$this->template->sms_qued = $sms_qued;
		
		$this->template->view('sms');
		error_log(123123);
		if($sms_qued)
			Modules::run('jobs/notifications/send_sms',$sms_data);
		
	}
	
	public function send_sms()
	{
		// use sun api
		$access = json_decode($this->settings->sun_api_access_credential);
		
		var_dump($this->settings);
		
		var_dump($this->settings->sun_api_access_credential);
		var_dump($this->settings->sun_api_originator1);
		var_dump($this->settings->sun_api_login_url);
		var_dump($this->settings->sun_api_send_url);
		
		$originator = $this->settings->sun_api_originator1;
		$login_url = $this->settings->sun_api_login_url;
		$send_url = $this->settings->sun_api_send_url;
		// $to = '0' . $area_code . $number;

		$ret = send_sunapi_sms("09278649105" , "11111", $login_url, $send_url, $originator, $access->user, $access->pass);
		
		var_dump($ret);
		
	}
	
	public function notification()
	{
		$this->load->model('members_model');
		$member_id = $this->input->get_post('member_id');
		$member = $this->members_model->get_member_by_id($member_id);
		
		if (empty($member))
		{
			echo "Invalid Request";
			return;
		}
		
		$code_type = 'mobile_number';
		$generated_code = '123456-09876';
		$detail = $member->mobile_number;
		
		$base_url = $this->config->item('base_url') . "/members/verification/index/{$code_type}/{$generated_code}";

		$params = array(
            "first_name"=>ucfirst($member->first_name),
            "link"=>$base_url,
            "generated_code" => $generated_code,
            "detail"=>$detail
        );
		
		$data = array(
			"member_id" => $member_id,
			"mobile_number" => '09278649105',
			"type" => "sms_" . $code_type . "_verification_update",
			"params" => $params
		);
		// send mobile to user
		Modules::run('jobs/notifications/send_sms',$data);


		$data = array(
            "email"=> 'brian.caballero1204@gmail.com',
            "type"=> $code_type . "_verification_update",
            "params"=>$params
        );
		//send email to user
        Modules::run('jobs/notifications/send_email',$data);

		echo "Ok.";
	}
	
}
