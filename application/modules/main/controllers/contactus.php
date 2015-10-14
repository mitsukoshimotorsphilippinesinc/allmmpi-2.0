<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Contactus extends Base_Controller {

	public function before()
	{
		parent::before();

	}

	public function __construct()
	{
		parent::__construct();

		// load contents model
		$this->load->model("contents_model");

	}
	
	private $_validation_rule = array(
		array(
			'field' => 'name',
			'label' => 'Name',
			'rules' => 'trim|required'
		),
		array(
			'field' => 'email',
			'label' => 'Email',
			'rules' => 'trim|required'
		),
		array(
			'field' => 'subject',
			'label' => 'Subject',
			'rules' => 'trim|required'
		),
		array(
			'field' => 'message',
			'label' => 'Message',
			'rules' => 'trim|required'
		),
	);
	
	public function index() 
	{
		$this->page();
	}
	
	public function page()
	{
		$contact_details = $this->contents_model->get_content_by_slug("contact-us");
		
		$data = array("contact_details" => $contact_details, "title" => "Contact Us");
		$contents = $this->load->view('contactus/page',$data,TRUE,'main');
		
		$this->display($contents);
	}
	
	public function display($contents)
	{	
		$this->cache($this->cache_timeout); // implement cache
		$this->template->title = null;
		$this->template->contents = $contents;
		$this->template->view('view');	
	}
	
	public function email()
	{
		if($_POST)
		{
			$this->form_validation->set_rules($this->_validation_rule);
			if ($this->form_validation->run())
			{
				$message = "MESSAGE FROM CONTACT US PAGE: " . "<br />" . $this->input->post("message");
			
				//set params
				$params = array(
					"name"=>$this->input->post("name"),
					"email"=>$this->input->post("email"),
					"subject" => $this->input->post("subject"),
					"message" => $this->input->post("message")
				);

				$data = array(
					"email"=>$this->settings->contact_us_recipient,
					"type"=>"contact-us-form",
					"params"=>$params
				);

				//send email to user
				Modules::run('jobs/notifications/send_email',$data);
				
				//redirect('/main/contactus');
				$this->return_json('1', "Message successfully sent.");
				return;
			}
		}
		
		$this->page();
	}
}