<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Notifications extends  Base_Controller
{
	
	function __construct() 
	{
  		parent::__construct();
	}
	
	public function index() 
	{
		echo "notifications";
		return;
	}
	
	public function send_email($data=array())
	{
		// load setting_model
		$this->load->model("setting_model");
		
		$notification_content = $this->setting_model->get_content_template($data['type'],$data['params']);	

		//var_dump($notification_content);

		// set params
		$params = array(
			"from"=>$this->setting->email_from,
			"to"=>$data['email'],
			"subject"=>$notification_content->title,
			"message"=>$notification_content->body
		);

		$body = urldecode(trim($this->input->get('body')));
		$body_text = strip_tags($body);

		// load contents_model
		$this->load->helper("notifications_helper");

		return send_email($params);		
	}


}