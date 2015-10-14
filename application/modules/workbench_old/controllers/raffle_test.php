<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Raffle_Test extends  Base_Controller
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
	
	
	public function rs_generate_entry()
	{
		
		$this->raffles_model->pre_repeat_sales_promo(2, 22);
		
		echo "<div>RS Generate Entry.</div>";
		return false;
	}
	
	
	public function process()
	{
		$raffle_number = abs($this->uri->segment(4));	   
			
		$raffle_id = 10;
		$this->raffles_model->raffle_process('webpoi', 'pre', $raffle_number , '123');
		echo "<div>Process test.</div>";
		return false;
	}
	
	public function test_entry_power_888()
	{
		$card_id = $this->input->get_post('card_id');
		$account_id = $this->input->get_post('account_id');
		
		if (empty($card_id) || empty($account_id))
		{
			echo "Invalid request.";
			return;
		}
		
		$this->raffles_model->raffle_process('rs_encoding', 'default', $card_id, $account_id);
		echo "Ok.";
		return;
		
	}
	
	public function test_entry_repeat_sales()
	{
		$raffle_number = $this->input->get_post('raffle_number');
		$account_id = $this->input->get_post('account_id');
		
		if (empty($raffle_number) || empty($account_id))
		{
			echo "Invalid request.";
			return;
		}
		
		$this->raffles_model->raffle_process('raffle_num_encoding', 'default', $raffle_number, $account_id);
		echo "Ok.";
		return;
	}
	
	public function test_entry_grandmaster()
	{
		
	}
}