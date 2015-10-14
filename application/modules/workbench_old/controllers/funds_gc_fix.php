<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Funds_gc_fix extends  Base_Controller
{
	
	public $member_earnings = "cm_member_earnings";
	public $member_acct_credit_logs = "tr_member_acct_credit_logs";
	
	function __construct() 
	{
  		parent::__construct();
	}

	public function index() 
	{
		echo "Account Points Fix Index.";
	}
	
	public function remove_credited_earnings()
	{
		$logs = $this->db->query("select * from {$this->member_acct_credit_logs} where credit_log_id > 8009489")->result();
		
		foreach($logs as $l){
			echo json_encode($l)."<br />";
			
			if ($l->transaction_code == '100') // referral
			    $field = 'referral_bonus';
			else if ($l->transaction_code == '101') // starter pack pairing bonus
			    $field = 'pairing_bonus_sp';
			else if ($l->transaction_code == '102') // value pack pairing bonus
			    $field = 'pairing_bonus_vp';
			else if ($l->transaction_code == '103') // trial pack pairing bonus
			    $field = 'pairing_bonus_tp';
			else if ($l->transaction_code == '104') // repeat sales pairing bonus
			    $field = 'pairing_bonus_rs';
			else if ($l->transaction_code == '105') // repeat sales unilevel commission
			    $field = 'unilevel_commission';
			else if ($l->transaction_code == '106') // gift cheque sp commission
				$field = 'gift_cheque_sp';
			else if ($l->transaction_code == '107') // gift cheque vp commission
				$field = 'gift_cheque_vp';
			else if ($l->transaction_code == '108') // gift cheque tp commission
				$field = 'gift_cheque_tp';
			else if ($l->transaction_code == '109') // gift cheque rs commission
				$field = 'gift_cheque_rs';
			
			$this->db->query("UPDATE {$this->member_earnings} SET {$field} = {$field} - {$l->amount} WHERE account_id = '{$l->account_id}'");
			
		}
	}
	
}
