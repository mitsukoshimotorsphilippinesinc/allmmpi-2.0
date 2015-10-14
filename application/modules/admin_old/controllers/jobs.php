<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Jobs extends Systems_Controller
{

	function __construct()
	{
  		parent::__construct();

		$this->load->model('contents_model');
		// load pager library
		$this->load->library('pager');
		$this->set_navigation('tools');
	}

	public function index()
	{
		echo "notifications";
		return;
	}
	
	public function send_email($data=array())
	{
		// load contents_model
		$this->load->model("contents_model");
		
		$content = $this->contents_model->get_template($data['type'],$data['params']);	

		var_dump($content);	
		
		// set params
		$params = array(
			"from"=>$this->settings->email_from,
			"to"=>$data['email'],
			"subject"=>$content->title,
			"message"=>$content->body
		);

		$body = urldecode(trim($this->input->get('body')));
		$body_text = strip_tags($body);

		// load contents_model
		$this->load->helper("notifications_helper");

		/*
		 * override to use elasicemail 
		 * for some reason, checking and conditioning of settings is failing to use smtp or send_email routine.
		 */

		$username = 'odie.miranda@gmail.com';
		$api_key = '5e75d891-37be-415d-985f-c1eda6b147c7';

		return send_elastic_email($username,
									$api_key,
									$data['email'],
									$content->title,
									strip_tags($content->body),
									$content->body,
									$this->settings->email_from,
									$this->settings->email_from);
		/*
		if(isset($this->settings->email_api)){
			if($this->settings->email_api=="elastic_email"){
				// set default
				$username = 'odie.miranda@gmail.com';
				$api_key = '5e75d891-37be-415d-985f-c1eda6b147c7';

				// set any override
				if (isset($this->settings->email_username)) 
					if (!empty($this->settings->email_username)) 
						$username = $this->settings->email_username;

				// set any override
				if (isset($this->settings->email_api_key)) 
					if (!empty($this->settings->email_api_key)) 
						$username = $this->settings->email_api_key;

				return send_elastic_email($username,
											$api_key,
											$data['email'],
											$content->title,
											strip_tags($content->body),
											$content->body,
											$this->settings->email_from,
											$this->settings->email_from);

			}else{
				return send_email($params);
			}
		}else{
			return send_email($params);
		}
		*/
		
	}

	public function send_sms($data=array())
	{
		$this->load->model("contents_model");
		$this->load->model("sms_model");		
		
		$content = $this->contents_model->get_template($data['type'],$data['params']);		
		
		// check identifier
		$identifier = "smart";
		$area_code = substr($data['mobile_number'], -10, 3);
		$number = substr($data['mobile_number'], -7);
		$prefix = $this->sms_model->get_telco_prefix(array('prefix'=>$area_code));
		if(!empty($prefix)){
			$identifier = $prefix[0]->identifier;
		}
		
		$insert_data = array(
			"identifier"=>$identifier,
			"member_id"=>$data["member_id"],
			"mobile_number"=>$data["mobile_number"],
			"message"=>$content->body
		);
		
		if($this->settings->sms_use_sun_api){
			$insert_data['identifier'] = "sun_api";
			$insert_data['status'] = "sent";
			
			// use sun api
			$access = json_decode($this->settings->sun_api_access_credential);
			
			$originator = $this->settings->sun_api_originator1;
			$login_url = $this->settings->sun_api_login_url;
			$send_url = $this->settings->sun_api_send_url;
			$to = '0' . $area_code . $number;
			$ret = send_sunapi_sms($to , $content->body, $login_url, $send_url, $originator, $access->user, $access->pass);

			if(!$ret) $insert_data['status'] = "failed";
			
		}
		
		$this->sms_model->insert_sms_outgoing($insert_data);	
		
	}
	
	public function send_tb_sms($data=array())
	{
		$this->load->model("contents_model");
		$this->load->model("sms_model");		
		
		$content = $this->contents_model->get_template($data['type'],$data['params']);		
		
		// check identifier
		$identifier = "smart";
		$area_code = substr($data['mobile_number'], -10, 3);
		$number = substr($data['mobile_number'], -7);
		$prefix = $this->sms_model->get_telco_prefix(array('prefix'=>$area_code));
		if(!empty($prefix)){
			$identifier = $prefix[0]->identifier;
		}
		
		$insert_data = array(
			"identifier"=>$identifier,
			"member_id"=>$data["member_id"],
			"account_id"=>$data["account_id"],
			"mobile_number"=>$data["mobile_number"],
			"message"=>$content->body,
			"is_charged"=>$data["is_charged"],
			"tariff"=>$data["tariff"],
		);
		
		if($this->settings->sms_use_sun_api){
			$insert_data['identifier'] = "sun_api";
			$insert_data['status'] = "sent";
			
			// use sun api
			$access = json_decode($this->settings->sun_api_access_credential);
			
			$originator = $this->settings->sun_api_originator1;
			$login_url = $this->settings->sun_api_login_url;
			$send_url = $this->settings->sun_api_send_url;
			$to = '0' . $area_code . $number;
			$ret = send_sunapi_sms($to , $content->body, $login_url, $send_url, $originator, $access->user, $access->pass);

			if(!$ret) $insert_data['status'] = "failed";
			
		}
		
		$this->sms_model->insert_sms_outgoing($insert_data);	
		
	}
	
	
	public function send_sms_batch($data=array())
	{
		$this->load->model("contents_model");
		$this->load->model("sms_model");		
		
		$content = $this->contents_model->get_template($data['type'],$data['params']);		
		
		$batch_mobile_numbers = $data['mobile_number']; // semicolon delimited
		
		$member_ids_and_mobile_numbers = explode("|", $batch_mobile_numbers);
		
		foreach($member_ids_and_mobile_numbers as $mn) {
			
			// parse
			$parsed_mid_mobnum = explode(":", $mn);
			
			$member_id = $parsed_mid_mobnum[0];
			$mobile_number = $parsed_mid_mobnum[1];
			
			// check identifier
			$identifier = "smart";
			$area_code = substr($mobile_number, -10, 3);
			$number = substr($mobile_number, -7);
			$prefix = $this->sms_model->get_telco_prefix(array('prefix'=>$area_code));
			if(!empty($prefix)){
				$identifier = $prefix[0]->identifier;
			}
			
			$insert_data = array(
				"identifier"=>$identifier,
				"is_charged"=>$data['is_charged'],
				"tariff"=>$data['tariff'],
				"member_id"=>$member_id,
				"mobile_number"=>"09328561112",
				"message"=>$content->body
			);		
			
			print_r($insert_data);
			
			//if($this->settings->sms_use_sun_api){
			//	$insert_data['identifier'] = "sun_api";
			//	$insert_data['status'] = "sent";
			//	
			//	// use sun api
			//	$access = json_decode($this->settings->sun_api_access_credential);
			//	
			//	$originator = $this->settings->sun_api_originator1;
			//	$login_url = $this->settings->sun_api_login_url;
			//	$send_url = $this->settings->sun_api_send_url;
			//	$to = '0' . $area_code . $number;
			//	$ret = send_sunapi_sms($to , $content->body, $login_url, $send_url, $originator, $access->user, $access->pass);
            //
			//	if(!$ret) $insert_data['status'] = "failed";
			//	
			//}
			
			$this->sms_model->insert_sms_outgoing($insert_data);	
			
		}
		
	}
	
}