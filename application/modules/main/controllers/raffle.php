<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Raffle extends Base_Controller {

	public function before()
	{
		parent::before();

	}

	public function __construct()
	{
		parent::__construct();

		// load contents model
		$this->load->model("raffles_model");
		$this->load->model("members_model");
		$this->load->model('cards_model');
	}
	
	public function index() 
	{
		$this->page();
	}
	
	public function page()
	{
		$data = array();
		$html = $this->load->view('raffle/details',$data,TRUE);
		
		$this->template->html = $html;
		$this->template->view('raffle/dashboard');
		//$contents = $this->load->view('raffle/dashboard',$data,TRUE,'main');
		
		//$this->display($contents);
	}
	
	public function encode_rs()
	{
		$card_id = $this->input->post('card_id');
		$card_code = $this->input->post('card_code');
		$account_id = $this->input->post('account_id');
		$last_name = $this->input->post('last_name');
		$first_name = $this->input->post('first_name');
		$middle_name = $this->input->post('middle_name');
		$address = $this->input->post('address');
		$contact_number = $this->input->post('contact_number');
		$email = $this->input->post('email');
		$referror = $this->input->post('referror');
		$type = $this->input->post('type');
		
		//check first two chars
		$card_type = substr($card_id, 0, 2);
		if($card_type != "74")
		{
			$this->return_json('error', 'Card cannot be used for raffle.');
			return;
		}
		
		//check card id
		$card_exists = $this->cards_model->get_rs_card_by_card_id($card_id);
		if(empty($card_exists))
		{
			$this->return_json('error', 'Invalid Card ID.');
			return;
		}
		else
		{
			if($card_exists->status == 'USED')
			{
				$this->return_json('error', 'Card has already been used.');
				return;
			}
			elseif($card_exists->status == 'INACTIVE')
			{
				$this->return_json('error', 'Card has not yet been issued.');
				return;
			}
			elseif($card_exists->status == 'INACTIVE')
			{
				$this->return_json('error', 'Card is inactive.');
				return;
			}
		}
		
		$card_code_exists = $this->cards_model->get_rs_card(array('card_code' => $card_code));
		if(empty($card_code_exists))
		{
			$this->return_json('error', 'Invalid Card Code.');
			return;
		}
		else
		{
			$card_details = $card_code_exists[0];
			if($card_details->card_id != $card_id)
			{
				$this->return_json('error', 'Card ID and Card Code do not match in the database.');
				return;
			}
		}
		
		if($type == 'non_member')
		{
			//check for duplicate
			$duplicate = array(
				'last_name' => $last_name,
				'first_name' => $first_name,
				'middle_name' => $middle_name,
				'email' => $email,
			);
			$duplicate_non_member = $this->members_model->get_non_members($duplicate);
			if(!empty($duplicate_non_member))
			{
				$this->return_json('error', 'An entry for your record already exists. Please enter your Account ID instead');
				return;
			}
			
			//insert into table
			$data = array(
				'last_name' => $last_name,
				'first_name' => $first_name,
				'middle_name' => $middle_name,
				'address' => $address,
				'contact_number' => $contact_number,
				'email' => $email,
				'referror' => $referror
			);
			$this->members_model->insert_non_member($data);
			$insert_id = $this->members_model->insert_id();
			
			$temp_account_id = "NVC" . str_pad($insert_id, 7, "0", STR_PAD_LEFT);
			$this->members_model->update_non_member(array('temp_account_id' => $temp_account_id), array('non_member_id' => $insert_id));
			
			$member_id = 0;
			$sponsor_member_id = 0;
			$sponsor_account_id = 0;
			$upline_account_id = 0;
			$upline_member_id = 0;

			// send email
			//set params needed for notification
	        $params = array(
	            "first_name"=>ucfirst($first_name),
				"middle_name"=>ucfirst($middle_name),
	            "last_name"=>ucfirst($last_name),
	            "temp_account_id"=>$temp_account_id,
				"card_id"=>$card_id
	        );

	        $data = array(
	            "email"=>$email,
	            "type"=>"temp-account-id",
	            "params"=>$params
	        );

	        //send email to user
	        Modules::run('jobs/notifications/send_email',$data);
			$account_id = $temp_account_id;
		}
		else
		{
			//search member
			$member_account = $this->members_model->get_member_accounts(array('account_id' => $account_id));
			if(!empty($member_account))
			{
				$member_account = $member_account[0];
				$member_id = $member_account->member_id;
				$upline_account_id = $member_account->upline_id;
				$sponsor_account_id = $member_account->sponsor_id;
			}
			else
			{
				//check in non-members
				
				$non_member = $this->members_model->get_non_member_by_account_id($account_id);
				if(!empty($non_member))
				{
					$member_id = 0;
					$sponsor_member_id = 0;
					$sponsor_account_id = 0;
					$upline_account_id = 0;
					$upline_member_id = 0;
				}
				else
				{
					$this->return_json('error', 'Account ID Not Found.');
					return;
				}
			}
			
		}
		
		$data = array(
			'raffle_number' => $card_id,
			'raffle_id' => 2,
			'is_active' => 1,
			'group' => 0,
			'sub_group' => 0,
			'member_id' => $member_id,
			'account_id' => $account_id,
			'sponsor_member_id' => $sponsor_member_id,
			'sponsor_account_id' => $sponsor_account_id,
			'upline_member_id' => $upline_member_id,
			'upline_account_id' => $upline_account_id,
		);
		$this->raffles_model->insert_raffle_entries($data);
		
		//update card
		$card_data = array(
			"status"=>"USED",
			"member_id"=>$member_id,
			"account_id"=>$account_id,
			"use_type"=>"raffle",
			"used_timestamp"=>date("Y-m-d H:i:s")
		);
		$this->cards_model->update_rs_card($card_data, array('card_id' => $card_id));
		$non_member_info = "";
		if($type == "non_member")
		{
			$non_member_info = "<p>This is your Temporary Account ID <strong>{$account_id}</strong>. You should use this Account ID for future raffle entries.</p><p>An email was sent to <strong>{$email}</strong> containing your temporary account information.</p>";
		}
		
		$this->return_json('ok', "<p>Raffle Entry successfully encoded.</p>{$non_member_info}");
		return;
	}
	
	public function check_account_id()
	{
		$account_id = $this->input->post('account_id');
		
		//check in cm_member_accounts
		$member_account = $this->members_model->get_member_accounts(array('account_id' => $account_id));
		if(!empty($member_account))
		{
			$member_account = $member_account[0];
			$member = $this->members_model->get_member_by_id($member_account->member_id);
			$member_name = $member->last_name . ', ' . $member->first_name . ' ' . $member->middle_name;
			$address = (!empty($member->home_address) ? $member->home_address : "None");
			$email = (!empty($member->email) ? $member->email : "None");
			$mobile_number = (!empty($member->mobile_number) ? $member->mobile_number : "None");
			$member_info = "<div style='width: 350px;'>
				<div><div style='display:inline;'><span><label class='label'>Name: </label></span></div><div style='display:inline;float:right;'>{$member_name}</div></div>
				<div><span><label class='label'>Address: </label></span><span style='float:right'>{$address}</span></div>
				<span><label class='label'>Email: </label></span><span style='float:right'>{$email}</span> <br />
				<span><label class='label'>Contact Number: </label></span><span style='float:right'>{$mobile_number}</span> <br />
				<span><label class='label'>Type: </label></span><span style='float:right'>MEMBER</span> <br />
			</div>";
			
			$this->return_json('ok', $member_info);
			return;
		}
		else
		{
			//check in non-members
			
			$non_member = $this->members_model->get_non_member_by_account_id($account_id);
			if(!empty($non_member))
			{
				$non_member_name = $non_member->last_name . ', ' . $non_member->first_name . ' ' . $non_member->middle_name;
				$address = (!empty($non_member->address) ? $non_member->address : "None");
				$email = (!empty($non_member->email) ? $non_member->email : "None");
				$contact_number = (!empty($non_member->contact_number) ? $non_member->contact_number : "None");
				$member_info = "<div>
					<span><label class='label'>Name: </label></span><span style='float:right'>{$non_member_name}</span> <br />
					<span><label class='label'>Address: </label></span><span style='float:right'>{$address}</span> <br />
					<span><label class='label'>Email: </label></span><span style='float:right'>{$email}</span> <br />
					<span><label class='label'>Contact Number: </label></span><span style='float:right'>{$contact_number}</span> <br />
					<span><label class='label'>Type: </label></span><span style='float:right'>NON-MEMBER</span> <br />
				</div>";

				$this->return_json('ok', $member_info);
				return;
			}	
			else
			{
				$this->return_json('error', 'Account ID Not Found.');
				return;
			}
		}
	}
	
	
	
	public function check_card_id()
	{
		$card_id = $this->input->post('card_id');
		
		// check if card series is 74
		
		if (substr($card_id, 0, 2) == "74") {
			// check if length = 10
			if (strlen($card_id) != 10) {
				$this->return_json('error', 'Invalid Control Code.');
				return;
			} else {
				$this->return_json('ok', 'Valid Control Code Series.');
				return;
			}
		
			
		} else {
			$this->return_json('error', 'Control Code not is not allowed.');
			return;
		}
		
	}
	
	
	public function display_raffle_entries() {
		$account_id = $this->input->post("account_id");
		
		if (empty($account_id) || trim($account_id == "")) {
		
			$this->return_json('error', 'Please enter an Account ID.');
			return;
		}
		
		
		// check if temporary
		if (substr($account_id, 0, 3) == "NVC") {
			// non-member account			
			$_details = $this->members_model->get_non_member_by_account_id($account_id);					
		} else {
			// member account
			$_details = $this->members_model->get_member_account_by_account_id($account_id);			
		}
		
		if (empty($_details)) {
			$this->return_json('error', 'Account ID not found.');
			return;
		} else {
		
			// get all raffle entries
			$raffles_details = $this->raffles_model->get_raffle_entries_by_account_id($account_id);
		
			$data = array(
				"raffles_details" => $raffles_details
			);
			
			$html = $this->load->view('raffle/view',$data,TRUE);
		
			$this->return_json('ok', 'Account ID found.', array("html" => $html));
			return;
		}		
	}
	
	public function load_details_form() {
		
		$data = array();
			
		$html = $this->load->view('raffle/details',$data,TRUE);
		
		$this->return_json('ok', 'Load Details', array("html" => $html));
		return;		
	}
	
}