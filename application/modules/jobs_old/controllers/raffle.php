<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	 // if (!defined('CRON')) exit('This script is accessed thru CRON script only');

class Raffle extends  Base_Controller
{
	
	function __construct() 
	{
  		parent::__construct();
  		$this->load->model("raffles_model");
  		$this->load->model("cards_model");
  		$this->load->model("members_model");
  		$this->load->model("tracking_model");
	}
	
	public function index() 
	{
		echo "Process Raffle Entry...";
	}

	public function process($params=array())
	{
		$card_id = $params['card_id'];
		$account_id = $params['account_id'];
		$member_id = $params['member_id'];
		// get card info
		$card = $this->cards_model->get_rs_card_by_card_id($card_id);

		if (strtoupper($card->status) == 'USED') {
			echo "Card ID {$card_id} is already used.";
			return false;
		}

		// get member of account id
		$account = $this->members_model->get_member_account_by_account_id($account_id);

		// sponsor data
		$sponsor = $this->members_model->get_member_account_by_account_id($account->sponsor_id);

		// upline data
		$upline = $this->members_model->get_member_account_by_account_id($account->upline_id);

		// check if card_id is in raffle entry
		$raffle_entry = $this->raffles_model->get_raffle_entry_by_raffle_number($card_id);

		if (empty($raffle_entry)) 
		{
			$data = array(
				'raffle_id' => 2,
				'raffle_number' => $card_id,
				'reference_info' => 'RS CARD',
				'value' => $card_id,
				'is_active' => 1,
				'member_id' => $member_id,
				'account_id' => $account_id,
				'sponsor_member_id' => $sponsor->member_id,
				'sponsor_account_id' => $account->sponsor_id,
				'upline_member_id' => $upline->member_id,
				'upline_account_id' => $account->upline_id
			);
			$this->raffles_model->insert_raffle_entries($data);
		}

		$this->raffles_model->raffle_process('rs_encoding', 'default', $card_id, $account_id);

		// condition series 74, assigned as used
		if (substr($card_id,0,2)=="74") {
			$data = array(
				"status"=>"USED",
				"member_id"=>$account->member_id,
				"account_id"=>$account_id,
				"use_type"=>'raffle',
				"used_timestamp"=> date('Y-m-d H:i:s')
			);			
			$this->cards_model->update_rs_card($data, array('card_id'=>$card_id));
		}


		echo "SUCCESS";
		return true;        
	}

}