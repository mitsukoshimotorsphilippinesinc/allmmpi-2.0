<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Account_points_fix extends  Base_Controller
{
	
	public $member_accounts = "cm_member_accounts";
	public $member_acct_credit_points_logs = "tr_member_acct_credit_points_logs";
	
	function __construct() 
	{
  		parent::__construct();
		$this->load->model('members_model');
	}

	public function index() 
	{
		echo "Account Points Fix Index.";
	}
	
	public function remove_credited_points()
	{
		$logs = $this->db->query("SELECT * FROM {$this->member_acct_credit_points_logs} ORDER BY member_id")->result();
		
		foreach($logs as $l){
			echo json_encode($l)."<br />";
			
			$this->db->query("UPDATE {$this->member_accounts} SET ".strtolower($l->side)."_".strtolower($l->type)." = ".strtolower($l->side)."_".strtolower($l->type)." - ".$l->amount." WHERE account_id = '{$l->account_id}'");
			
			$revert_log = $this->db->query("SELECT * FROM temp_member_acct_points_revert_logs WHERE account_id = '{$l->account_id}' AND type = '{$l->type}' AND side = '{$l->side}' AND card_id = '$l->card_id'")->result();
			
			if(count($revert_log)){
				$this->db->query("UPDATE temp_member_acct_points_revert_logs SET cnt_deducted = cnt_deducted + 1, amounts = CONCAT(amounts, '|', '{$l->amount}') WHERE account_id = '{$l->account_id}' AND type = '{$l->type}' AND side = '{$l->side}' AND card_id = '{$l->card_id}'");
			}else{
				$this->db->query("INSERT INTO `temp_member_acct_points_revert_logs` (`log_id`, `account_id`, `card_id`, `type`, `side`, `cnt_deducted`, `amounts`)
				VALUES
					(null, '{$l->account_id}', '{$l->card_id}', '{$l->type}', '{$l->side}', 1, '{$l->amount}');
				");
			}
			
			
			
		}
	}
	
	public function re_encode()
	{		
		$tmp_log = $this->db->query("SELECT *, DATE_FORMAT(used_timestamp, '%Y-%m-%d') AS curr_date FROM a_tmp_card_used_from_credit_points_logs ORDER BY used_timestamp ASC")->result();
		$current_date = '';
		
		foreach($tmp_log as $l)
		{
			$l->type = strtolower($l->type);
			$l->side = strtolower($l->side);

			if ($current_date != $l->curr_date)
			{
				$this->_reset_pairs();
				$current_date = $l->curr_date;
			}

			$point_setting = strtolower($l->type)."_points";
			
			$points = $this->settings->$point_setting;
			
			// cedit points to account				
			$params = array(
				"card_id"=>$l->card_id,
				"account_id"=>$l->account_id,
				"type"=>strtolower($l->type),
				"position"=>strtolower($l->side),
				"points"=>$points,
				"timestamp"=>$l->used_timestamp				
			);

			$this->_credit_points($params);	
			
			if (strtolower($l->type) == 'rs')
			{
				// credit rs commission
				$params = array("card_id"=>$l->card_id,"account_id"=>$l->account_id, "timestamp" => $l->used_timestamp);	
				$this->_credit_repeat_sales_commission($params);			
			}
			else if (strtolower($l->type) == 'sp' || strtolower($l->type) == 'vp' || strtolower($l->type) == 'tp')
			{
				// credit referral
				$member = $this->members_model->get_member_account_by_account_id($l->account_id);
				$params = array("card_id"=>$l->account_id,"account_id"=>$member->sponsor_id,"type" => strtolower($l->type), "timestamp" => $l->used_timestamp);
				$this->_credit_referral_bonus($params);
			}
			
			// card status
			$this->_update_card_status($l->type, $l->card_id, $l->used_timestamp);			
		}
	}
	
	private function _update_card_status($type,$card_id,$timestamp)
	{
		$this->load->model("cards_model");
				
		$data = array("status"=>"ACTIVE","used_timestamp"=>$timestamp);
		
		if ($type == "RS")
			$this->cards_model->update_rs_card($data,"card_id = {$card_id}");
		else
			$this->cards_model->update_sp_card($data,"card_id = {$card_id}");
	}

	public function fix_dates()
	{
		$clean = $this->db->query("SELECT * FROM temp_tr_member_acct_credit_points_logs_clean WHERE account_id = from_account_id")->result();

		foreach($clean as $r){
			$this->db->query("UPDATE tr_member_acct_credit_points_logs SET insert_timestamp = '{$r->insert_timestamp}' WHERE card_id = '{$r->card_id}' AND account_id = {$r->account_id}");
			$this->db->query("UPDATE tr_member_acct_credit_points_logs SET insert_timestamp = '{$r->insert_timestamp}' WHERE card_id = '{$r->card_id}' AND from_account_id = {$r->account_id}");
		}

		echo "Done!!!";
	}

	public function fix_new_members()
	{
		$new_members = $this->db->query("SELECT * FROM cm_member_accounts_new_registrations ORDER BY insert_timestamp")->result();

		foreach($new_members as $m){
			// position
			$address = substr($m->node_address,strlen($m->node_address)-1,1);
			$position = "left";
			if($address=="2") $position = "right";
			// card type
			$card_details = $this->cards_model->get_sp_card_by_card_id($m->account_id);
			if(empty($card_details)) $card_details = $this->cards_model->get_rs_card_by_card_id($m->account_id);
			$type = strtolower($card_details->type);
			$settings_points - $type . "_points";
			$params = array(
				"card_id"=>$m->account_id,
				"account_id"=>$m->upline_id,
				"type"=>"sp",
				"position"=>$position,
				"points"=>$this->settings->$settings_points,
			);		
			Modules::run('jobs/commissions/credit_points',$params);	
		}
	}

	public function fix_encoding()
	{
		
	}


	public function _credit_points($params=array())
	{
		$account_id = $params["account_id"];
		$card_id = $params["card_id"];
		$type = strtolower($params["type"]);
		$position = strtolower($params["position"]);
		$points = $params["points"];
		$timestamp = $params["timestamp"];
		
		// get member_account
		$member_account = $this->members_model->get_member_account_by_account_id($account_id);
		
		$address = $member_account->node_address;		
		
		$address_length_count = strlen($address);
		
		$addresses = array();

		// get addresses of all uplines
		for ($i=1;$i<=$address_length_count;$i++) 
			$addresses[] = "'" . substr($address,0,$i) . "'";
		
		$new_addresses = implode(",",$addresses);
		
		// get all uplines
		$accounts = $this->members_model->get_member_accounts("node_address in ({$new_addresses})",NULL,"LENGTH(node_address) DESC","account_id,node_address");

		// credit points and check for pairing
		foreach($accounts as $a)
		{
			// credit points and check for pairings
			// $this->members_model->credit_points($a->account_id,$card_id,$account_id,$type,$position,$points);			
			$this->members_model->_credit_points_2($a->account_id,$card_id,$account_id,$type,$position,$points,$timestamp);			
			$position = substr($a->node_address,-1) == 1 ? "right" : "left";
		}
		
		return;		
	}

	public function _credit_referral_bonus($params = array())
	{
		// CREDIT REFERRAL BONUS
		$transaction_code = 100;
		
		// get parameters
		$account_id = $params["account_id"];
		$card_id = $params["card_id"];
		$type = $params['type'];
		$timestamp = $params["timestamp"];

		$referral_bonus_type = "referral_bonus_{$type}";
		
		$referral_bonus = 300;
		
		$referral_bonus = $this->settings->$referral_bonus_type;
	
		
		$remarks = "Credited Referral Bonus from {$card_id}";
				
		// credit referral fee to members funds
		$this->members_model->_credit_earnings_2($account_id, $card_id, $card_id, $referral_bonus, $transaction_code, $remarks, $timestamp);		
	}

	public function _reset_pairs()
	{
		
		if ($this->settings->auto_reset_pairing_counters)
		{
			// get the 1000 accounts and iterate until all accounts pairing counters have been reset to 0
			$max_no_of_accounts_per_loop = 10000;

			$where = "pairs_sp > 0  OR gc_sp > 0  OR flushout_sp > 0  OR pairs_vp > 0  OR gc_vp > 0  OR flushout_vp > 0  OR pairs_rs > 0  OR gc_rs > 0  OR flushout_rs > 0";
			$limit = array("rows"=>$max_no_of_accounts_per_loop,"offset"=>0);
			$fields = "account_id";

			do 
			{
				$member_accounts = $this->members_model->get_member_accounts($where,$limit,NULL,$fields);						
				$count = count($member_accounts);
				$accounts = array();		

				if($count>0) 
				{
					foreach ($member_accounts as $a) $accounts[] = "'{$a->account_id}'";

					$_accounts = implode(",",$accounts);

					$condition = "account_id IN ({$_accounts})";

					$data = array(
						"pairs_sp"=>0,
						"gc_sp"=>0,
						"flushout_sp"=>0,
						"pairs_vp"=>0,
						"gc_vp"=>0,
						"flushout_vp"=>0,
						"pairs_rs"=>0,
						"gc_rs"=>0,
						"flushout_rs"=>0
					);

					$this->members_model->update_member_accounts($data,$condition);
				}
			} while ($count>0);			
		}		
	}

	public function _credit_repeat_sales_commission($params=array())
	{
		$account_id = $params["account_id"];
		$card_id = $params["card_id"];
		$timestamp = $params["timestamp"];
		
		// CREDIT REPEAT SALES COMMISSION
		$transaction_code = 105;		
		
		// get setting values
		$no_of_uplines_with_commission = $this->settings->rs_no_of_uplines_with_commission;				
		$repeat_sales_commission = $this->settings->rs_commission;
		
		// get member_account
		$member_account = $this->members_model->get_member_account_by_account_id($account_id);
		
		// address of the one who encoded the rs card
		$uni_node_address = $member_account->uni_node;
				
		$_parsed = explode('.',$uni_node_address);

		$address_count = count($_parsed);
		
		$upline_addresses = array();
		
		// get all addresses including the one who encoded the sales
		for($i=$address_count - 1;$i>=0;$i--)
		{
			$new_address = array();
			for($j=0;$j<=$i;$j++) $new_address[] = $_parsed[$j];
			$upline_address = implode(".",$new_address);			
			$upline_addresses[] = "'{$upline_address}'";			
		}
		
		// get upline_accounts
		$upline_addresses = implode(",",$upline_addresses);
		
		// get all uplines
		$upline_accounts = $this->members_model->get_member_accounts("uni_node in ({$upline_addresses})",NULL,"LENGTH(uni_node) DESC","account_id,account_status_id");		
		
		// credit repeat sales commission to active upline addresses
		$counter = 0;			

		foreach($upline_accounts as $ua)
		{
			if ($ua->account_status_id<>2) {
				// only give commission to active accounts	
				$remarks = "Credit repeat sales unilevel commission from {$card_id}.";
				$this->members_model->_credit_earnings_2($ua->account_id,$card_id,$account_id,$repeat_sales_commission,$transaction_code,$remarks, $timestamp);				

				$counter++;
			}elseif($ua->account_id == $member_account->account_id){
				// only give commission to active accounts	
				$remarks = "Credit repeat sales unilevel commission from {$card_id}.";
				$this->members_model->_credit_earnings_2($ua->account_id,$card_id,$account_id,$repeat_sales_commission,$transaction_code,$remarks, $timestamp);				

				$counter++;
			}else{
				// log missed commissions due to inactivity 
				$remarks = "No repeat sales unilevel commission due to inactivity from {$card_id}.";
				$commission = 0;
				$this->members_model->_credit_earnings_2($ua->account_id,$card_id,$account_id,$commission,$transaction_code,$remarks, $timestamp);								
			}

			if ($counter==$no_of_uplines_with_commission) break;				
		}
	}

	public function fix_missing_referral_bonus()
	{
		
		$sql = "SELECT * FROM tmp_no_referral_bonus_20121208_to_20121217 WHERE transaction_code IS NULL;";
		
		$query = $this->db->query($sql);
		
		foreach ($query->result() as $r)
		{
			// credit referral
			$params = array("card_id"=>$r->card_id,"account_id"=>$r->sponsor_id,"type" => strtolower($r->type), "timestamp" => $r->insert_timestamp);
			$this->_credit_referral_bonus($params);			
		}
	}
	
}
