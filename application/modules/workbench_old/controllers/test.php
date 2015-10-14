<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Test extends  Base_Controller
{
	
	public $time_start;
	public $time_end;
	
	function __construct() 
	{
  		parent::__construct();

		$this->load->model("members_model");
	}
	
	public function index() 
	{
	}
	
	public function before()
	{
		$this->time_start = microtime(true); //place this before any script you want to calculate time
	}

	public function after()
	{
		$time_end = microtime(true);
		$execution_time = ($time_end - $this->time_start); //dividing with 60 will give the execution time in minutes other wise seconds
		echo '<b>Total Execution Time:</b> '.$execution_time.' Seconds'; //execution time of the script		
	}
	
	public function test_credit_funds()
	{
		$this->before();
		
		$member_id = 1;
		
		$amount = 1000;

		$type ='FUNDS';
		
		$this->members_model->credit_funds($member_id,$amount,$type,'Test Credit Funds');				

		$type ='GC';

		$this->members_model->credit_funds($member_id,$amount,$type,'Test Credit GC');				

		$this->after();
	}	


	public function test_debit_funds()
	{		
		$this->before();
		
		$member_id = 1;
		
		$amount = 1000;

		$type ='FUNDS';
		
		$this->members_model->debit_funds($member_id,$amount,$type,'Test Credit Funds');				

		$type ='GC';

		$this->members_model->debit_funds($member_id,$amount,$type,'Test Credit GC');				

		$this->after();				
	}
	
	public function test_credit_referral_bonus()
	{		
		$this->before();

		$account_id = $this->input->get('account_id');

		$card_id = "00testcard";
	
		$params = array("card_id"=>$card_id,"account_id"=>$account_id);
		Modules::run('jobs/commissions/credit_referral_bonus',$params);			
		
		$this->after();
	}
	
	public function test_credit_repeat_sales_points_and_commission()
	{
		$this->before();		

		$account_id = $this->input->get('account_id');
		
		$card_id = "00testcard";
		
		// cedit points to account				
		$params = array(
			"card_id"=>$card_id,
			"account_id"=>$account_id,
			"type"=>"rs",
			"position"=>"left",
			"points"=>"25",
		);
		Modules::run('jobs/commissions/credit_points',$params);
		
		// credit unilevel commission
		$params = array("card_id"=>$card_id,"account_id"=>$account_id);
		Modules::run('jobs/commissions/credit_repeat_sales_commission',$params);		
		$this->after();
	}

	public function test_credit_repeat_sales_commission()
	{
		$this->before();
		$account_id = $this->input->get('account_id');

		$card_id = "00testcard";

		// credit unilevel commission
		$params = array("card_id"=>$card_id,"account_id"=>$account_id);
		Modules::run('jobs/commissions/credit_repeat_sales_commission',$params);				
		$this->after();
	}
		
	public function test_credit_points()
	{
		$this->before();

		$account_id = $this->input->get('account_id');

		$card_id = "00testcard";

		$params = array(
			"card_id"=>$card_id,
			"account_id"=>$account_id,
			"type"=>"sp",
			"position"=>"right",
			"points"=>"700"
		);
		
		$result = Modules::run('jobs/commissions/credit_points',$params);
		
		echo $result;

		$this->after();
	}
	
	
	public function test_nodes()
	{
		// address of the one who encoded the rs card
		$uni_node_address = "1.8.2.2.13.1.1.1.1.1.1.1.1.1.1.1.1.2.2.1.1.1.1.1.1.1.1.1.1.1.1.1.1.1.1.1.1.1.1.1.1.1.1.1.1.1.1.1.1.1.1.1.1.1.1.1.1.2.1.1.1.1.1.3.1.1.1.3.1.1.1.1.1.1.2.1.1.2.1.2.1.1.1.1.2.2.1.2.1.1.2.1.1.2.1.1.1.1.1.1.1.1.2.1.1.1.1.1.1.1.1.3.1.1.1.1.1.1.1.1.1.1.1.1.1.1.1.1.1";
				
		$_parsed = explode('.',$uni_node_address);

		$address_count = count($_parsed);
		
		$upline_addresses = array();
		
		// get all addresses including the one who encoded the sales
		for($i=$address_count - 1;$i>=0;$i--)
		{
			$new_address = array();
			for($j=0;$j<=$i;$j++) $new_address[] = $_parsed[$j];
			$upline_address = implode(".",$new_address);			
			//$upline_addresses[] = "'{$upline_address}'";			
			echo "'{$upline_address}'<br/>";			
		}		
	}
	
	public function test_reset_pairs()
	{
		$this->before();
		
		$result = Modules::run('jobs/accounts/reset_pairs');
		
		$this->after();
	}
	
	public function test_create_member_account()
	{
		$this->before();
		
		$params = array(
			"account_id"=>'7800129399',
			"account_code"=>'SPC7870487',
			"sponsor_id"=>'6500001498',
			"upline_id"=>'9900004907',
			"member_id"=>50527,
			"member_user_account_id"=>16083,
			"position"=>'right',
			"card_type"=>'SP',
		);

		$account_id = $params["account_id"];
		$account_code = $params["account_code"];
		$sponsor_id = $params["sponsor_id"];
		$upline_id = $params["upline_id"];
		$member_id = $params["member_id"];
		$member_user_account_id = $params["member_user_account_id"];
		$position = $params["position"];
		$card_type = $params["card_type"];

		$result = Modules::run('main/registration/create_member_account',$params);

		$this->after();
		
		echo "<br/>". $result;
		return;
	}
	
	public function test_backup_member_accounts()
	{
		$this->before();		
		
		Modules::run('jobs/accounts/backup_member_accounts');

		$this->after();		
	}
	
	
	public function test_process_earnings()
	{
		$this->before();		

		/*
		$type = $this->input->post("type"); //IGPSM or UNILEVEL
		$start_date = $this->input->post("start_date");
		$end_date = $this->input->post("end_date");				
		*/

		$type = 'IGPSM';
		$start_date = '2012-10-26';
		$end_date = '2012-10-31';

		// only get accounts with earnings > 0
		if ($type=="IGPSM")
			$condition = "referral_bonus > 0 OR pairing_bonus_sp > 0 OR gift_cheque_sp > 0 OR pairing_bonus_vp > 0 OR gift_cheque_vp > 0 OR pairing_bonus_tp > 0 OR gift_cheque_tp > 0 OR pairing_bonus_rs > 0 OR gift_cheque_rs > 0";
		else
			$condition = "unilevel_commission > 0";
		
		$limit = NULL;
		$fields = NULL;
		$order = NULL;

		$earnings = $this->members_model->get_member_earnings($condition,$limit,$order,$fields);
		
		foreach($earnings as $e)
		{

			// get members funds from the cm_members.funds for withdrawal
			$member = $this->members_model->get_member_by_id($e->member_id);
			
			if (!empty($member))
			{
				if ($member->is_auto_payout)
				{
					// get member funds
					$member_funds = $member->funds;

					if ($member_funds>0)
					{
						// debit funds from cm_member_funds
						$this->members_model->debit_funds($member_id,$member_funds,"FUNDS","Debited funds for payout");					
					}
				}
				else
				{
					// no auto payout, do not get funds from members account 
					$member_funds = 0;
				}


				// insert values to cm_member_processed_earnings
				$sql = "
				INSERT INTO cm_member_earnings_processed 	
				(
					type,
					member_id, 
					account_id, 
					referral_bonus, 
					pairing_bonus_sp, 
					gift_cheque_sp,
					pairing_bonus_vp, 
					gift_cheque_vp, 
					pairing_bonus_tp, 
					gift_cheque_tp, 
					pairing_bonus_rs, 
					gift_cheque_rs, 
					unilevel_commission, 
					funds,
					status,
					period_start,
					period_end
				) 
				VALUES 
				(
					'{$type}',
					{$e->member_id},
					'{$e->account_id}',
					{$e->referral_bonus},
					{$e->pairing_bonus_sp},
					{$e->gift_cheque_sp},
					{$e->pairing_bonus_vp},
					{$e->gift_cheque_vp},
					{$e->pairing_bonus_tp},
					{$e->gift_cheque_tp},
					{$e->pairing_bonus_rs},
					{$e->gift_cheque_rs},
					{$e->unilevel_commission},
					{$member_funds},
					'PENDING',
					'{$start_date}',
					'{$end_date}'
				)";
				$this->db->query($sql);
				
				// if IGPSM remove pairing bonuses and gift cheques
				// if UNILEVEL remove unilevel commission
			} 
			
			
			// create excel sheet for commission per accounts
			if ($type=='IGPSM') 
				$summative_fields = "";
			else
				$summative_fields = "";
			
			$sql = "
			SELECT
				type,
				member_id,
				account_id,
				AS amount
			FROM 
				cm_member_earnings_processed 
			WHERE 
				status='PENDING' 
			AND 
				type='{$type}'";
			
			$query = $this->db->query($sql);
			
		}

		$this->after();		
	}	
	
	public function test_send_sms()
	{
		
		 $params = array(
	            "first_name"=>"Ronald",
	            "last_name"=>"Gonzales",
	            "account_id"=>"00001",
	            "upline_id"=>"00002",
	            "sponsor_id"=>"00003",
	            "position"=>"Center"
	        );
		
		
        $data = array(
            "member_id"=>0,
            "mobile_number"=>"09209452875",
            "type"=>"add-new-account-sms",
            "params"=>$params
        );

        //send sms to user
        Modules::run('jobs/notifications/send_sms',$data);					
	}
	
	public function update_rs_counter()
	{

		$sql = "select card_id from tr_member_acct_credit_logs where card_id = '7500000098';";
		
		//$sql = "select card_id from tmp_rs_cards_acct_credit_logs_20121201_to_20121231;";
		
		$query = $this->db->query($sql);
		
		$result = $query->result();
		
		foreach ($result as $row)
		{
			$s = "select credit_log_id from tr_member_acct_credit_logs where card_id = '{$row->card_id}'";
			
			$q = $this->db->query($s);
			
			$res = $q->result();
			
			$credit_log_ids = implode(',',$res);			
		}
		
		return $credit_log_ids;
	} 	
}