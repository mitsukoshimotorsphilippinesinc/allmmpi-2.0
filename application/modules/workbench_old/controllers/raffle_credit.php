<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Raffle_Credit extends  Base_Controller
{

	function __construct() 
	{
  		parent::__construct();
		$this->load->model('users_model');
		$this->load->model('members_model');
		$this->load->model('payment_model');
		$this->load->model('raffles_model');

	}
	
	public function index() 
	{
		$this->template->view('raffle/raffle_box');
	}
	
	
	public function sp()
	{
		$card_id = $this->input->get_post('card_id');
		$account_id = $this->input->get_post('account_id');
		
		if (empty($card_id) || empty($account_id))
		{
			echo "Invalid request.";
			return;
		}
		
		// process entry for raffle/promo
		$ret = $this->raffles_model->raffle_process('sp_encoding', 'default', $card_id, $account_id);
		
		if ($ret)
			echo "Crediting Ok.";
		else
			echo "Crediting Failed.";
		
	}
	
	
}