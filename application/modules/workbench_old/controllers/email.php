<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Email extends Base_Controller {

	function __construct() {
		parent::__construct();
	}

	public function index() {
		
		echo "Workbench :: Send Email";
		
	}
	
	public function send()
	{

		$email = $this->input->get_post('email');
		$type = $this->input->get_post('type');

		$email_params = array();
		$email_data = array(
			"email"=>$email,
			"type"=>$type,
			"params"=>$email_params
		);

		$result = Modules::run('jobs/notifications/send_email',$email_data);

		var_dump($result);

	}

	public function resend_transfer_fund_email_form()
	{
		$this->template->view("resend_transfer_fund_email_form");
	}

	public function resend_transfer_fund_email()
	{
		$fund_transfer_id = $this->input->get_post('fund_transfer_id');

		$sql = "
			SELECT * FROM tr_member_transfers
			WHERE member_transfer_id = '" . $fund_transfer_id . "'
			AND status = 'PENDING'
		";

		$query = $this->db->query($sql);
		$transfers = $query->result();

		if(count($transfers) > 0)
		{
			$transfers = $transfers[0];
			$this->load->model("members_model");

			// TO MEMBER DETAILS
			$to_member = $this->members_model->get_member_by_id($transfers->to_member_id);
			if(count($to_member) == 0)
			{
				$this->return_json(0,"Invalid Transfer Details");
				return;
			}

			// FROM MEMBER DETAILS
			$from_member = $this->members_model->get_member_by_id($transfers->from_member_id);
			if(count($from_member) == 0)
			{
				$this->return_json(0,"Invalid Transfer Details");
				return;
			}			

			$base_url = $this->config->item('base_url') . "/members/transfers";
			$pretty_amount = number_format(($transfers->amount), 2);
			$proper_name = $to_member->first_name . " " . $to_member->last_name;

			// send email notification to recipient
	        $params = array(
	            "to_first_name"=>ucfirst($to_member->first_name),
	            "to_last_name"=>ucfirst($to_member->last_name),
	            "link"=>$base_url,
				"proper_amount"=>$pretty_amount,
				"proper_transfer_type"=>$transfers->type,
	            "confirmation_code"=>$transfers->confirmation_code,
				"from_first_name"=>ucfirst($from_member->first_name),
	            "from_last_name"=>ucfirst($from_member->last_name),
				"proper_name_to_member"=>$proper_name				
	        );

	        $data = array(
	            "email"=>$from_member->email,
	            "type"=>"transfer_email",
	            "params"=>$params
	        );

	        //send email to user
	        Modules::run('jobs/notifications/send_email',$data);
	        $this->return_json(1,"SUCCESS");
		}
		else
		{
			$this->return_json(0,"Invalid Transfer ID");
		}
		return;
	}
	
}
