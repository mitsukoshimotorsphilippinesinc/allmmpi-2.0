<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Credit_earnings_fix extends Base_Controller
{
	
	
	function __construct() 
	{
  		parent::__construct();
		$this->load->model("members_model");
		$this->load->model("settings_model");
		$this->load->model("cards_model");
	}

	public function index() 
	{
		echo "credit earnings fix";
	}

	public function process()
	{
		
		// GET LIST OF TRANSACTIONS FROM TABLE
		$sql = "SELECT * FROM tmp_credit_earnings_fix_20140205 ORDER BY credit_earning_fix_id";
		$query = $this->db->query($sql);
		$result = $query->result();
		
		
		if (empty($result)) {
			
			echo "ERROR";
			
		} else {
		
			foreach($result as $r) {
				
				$params = array(
					'member_id' => $r->member_id,
					'account_id' => $r->account_id,
					'card_id' => $r->card_id,
					'position' => $r->position,
					'maintenance_period' => $r->maintenance_period,
					'card_code' => $r->card_code,
					'type' => $r->type,
					'points' => $r->points
				);
				
				$this->credit_repeat_sales_commission($params);			
			}	
		}
		
	}

	public function credit_repeat_sales_commission($params=array())
	{
		$maintenance_period = $params['maintenance_period'];
		if($maintenance_period != "raffle")
		{
			$account_id = $params["account_id"];
			$card_id = $params["card_id"];
			
			// CREDIT REPEAT SALES COMMISSION
			$transaction_code = 105;		
			
			// get setting values
			$no_of_uplines_with_commission = $this->settings->rs_no_of_uplines_with_commission;				
			//$repeat_sales_commission = $this->settings->rs_commission;

			// get card type
			$card_data = $this->cards_model->get_rs_card_by_card_id($card_id);
			$card_type_data = $this->cards_model->get_card_types(array('code' => $card_data->type));

			// assuming that cascade will only give funds (create dynamic if needed)
			$cascade_bonus = $this->cards_model->get_card_type_bonuses(array(
				'card_type_id' => $card_type_data[0]->card_type_id,
				'commission_type' => 'CASCADE',
				'bonus_type' => 'FUNDS'
			));
			//$repeat_sales_commission = $cascade_bonus[0]->qty_amount;
			$repeat_sales_commission = 10;
			
			
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
					//counter for unilevel commission max counter is 10
					$counter++;
					// only give commission to active accounts	
					$remarks = "Credit repeat sales unilevel commission from {$card_id}.";
					$this->credit_earnings($ua->account_id,$card_id,$card_type_data[0]->card_type_id,$account_id,$repeat_sales_commission,$transaction_code,$remarks,$counter);				
				}elseif($ua->account_id == $member_account->account_id){
					//counter for unilevel commission max counter is 10
					$counter++;
					// only give commission to active accounts	
					$remarks = "Credit repeat sales unilevel commission from {$card_id}.";
					$this->credit_earnings($ua->account_id,$card_id,$card_type_data[0]->card_type_id,$account_id,$repeat_sales_commission,$transaction_code,$remarks,$counter);
				}else{
					// log missed commissions due to inactivity 
					$remarks = "No repeat sales unilevel commission due to inactivity from {$card_id}.";
					$commission = 0;
					$this->credit_earnings($ua->account_id,$card_id,$card_type_data[0]->card_type_id,$account_id,$commission,$transaction_code,$remarks);								
				}

				if ($counter==$no_of_uplines_with_commission) break;				
			}
		}
		
		echo "1";
	}
		

	function credit_earnings($account_id,$card_id,$card_type_id,$from_account_id,$amount,$transaction_code,$remarks="",$counter=NULL,$timestamp="") {
        
        //get member_account
      $member_account = $this->members_model->get_member_account_by_account_id($account_id);               

        // get pending earnings
        $condition = "account_id = '{$account_id}'";  
        $order_by = NULL;
        $member_earnings = $this->members_model->get_member_earnings($condition,NULL,$order_by);
        if(sizeof($member_earnings) == 0)
        {
            $this->members_model->insert_member_earnings(array(
                'member_id' => $member_account->member_id,
                'account_id' => $account_id
            ));
            $member_earnings = $this->members_model->get_member_earnings($condition,NULL,$order_by);
        }
        $member_earnings = $member_earnings[0];
        
        $data = array();

        if ($transaction_code == '100') // referral
            $data['referral_bonus'] = $member_earnings->referral_bonus + $amount; 
        else if ($transaction_code == '101') // starter pack pairing bonus
            $data['pairing_bonus_sp'] = $member_earnings->pairing_bonus_sp + $amount;     
        else if ($transaction_code == '102') // value pack pairing bonus
            $data['pairing_bonus_vp'] = $member_earnings->pairing_bonus_vp + $amount;         
        else if ($transaction_code == '103') // trial pack pairing bonus
            $data['pairing_bonus_tp'] = $member_earnings->pairing_bonus_tp + $amount;         
        else if ($transaction_code == '104') // repeat sales pairing bonus
            $data['pairing_bonus_rs'] = $member_earnings->pairing_bonus_rs + $amount;         
        else if ($transaction_code == '105') // repeat sales unilevel commission
            $data['unilevel_commission'] = $member_earnings->unilevel_commission + $amount;         
        else if ($transaction_code == '106') // gift cheque sp commission
          $data['gift_cheque_sp'] = $member_earnings->gift_cheque_sp + $amount;         
        else if ($transaction_code == '107') // gift cheque vp commission
          $data['gift_cheque_vp'] = $member_earnings->gift_cheque_vp + $amount;         
        else if ($transaction_code == '108') // gift cheque tp commission
          $data['gift_cheque_tp'] = $member_earnings->gift_cheque_tp + $amount;         
        else if ($transaction_code == '109') // gift cheque rs commission
          $data['gift_cheque_rs'] = $member_earnings->gift_cheque_rs + $amount;

        $this->members_model->update_member_earnings($data,"earning_id = {$member_earnings->earning_id}");
        
        $data = array();

        // insert remarks
        $this->load->model('tracking_model');
		
        $data = array(
          "member_id"=>$member_account->member_id,
          "account_id"=>$member_account->account_id,
    	  "account_status_id"=>$member_account->account_status_id,
          "card_id"=>$card_id,
          "from_account_id"=>$from_account_id,
          "transaction_code"=>$transaction_code,
          "type"=>"FUNDS",
          "amount"=>$amount,
          "level" => $counter,
          "remarks"=>$remarks

        );
		
        if($timestamp!="") $data["insert_timestamp"] = $timestamp;
		
		if ($member_account->sms_notification == 1) 
			$data["sms_sent"] = 1;
			
        $this->tracking_model->insert_acct_credit_logs($data);
		
		$member_details = $this->members_model->get_member_by_id($member_account->member_id);
	    
		// get card_type_bonus
		$card_type_bonus_details = $this->cards_model->get_card_type_bonuses(array(
                        'card_type_id' => $card_type_id,
                        'commission_type' => 'REFERRAL'
						));
        
		$card_type_bonus_count = $this->cards_model->get_card_type_bonuses_count(array(
                        'card_type_id' => $card_type_id,
                        'commission_type' => 'REFERRAL'
						));				
						
        $card_type_data = $this->cards_model->get_card_type_by_id($card_type_id);
		
		$d = 0;
		$remarks_message = "";
		foreach($card_type_bonus_details as $card_type_bonus_detail) {
			
			// compose remarks message
			$remarks_message .= $card_type_bonus_detail->qty_amount . " " . $card_type_bonus_detail->bonus_type;
			
			if ($d < $card_type_bonus_count - 1) {
				if ($d == $card_type_bonus_count - 2) {
					$remarks_message .= " and ";
				} else {
					$remarks_message .= ", ";
				}	
			}
			
			$d++;
		}		
		
		if (($card_type_bonus_count > 0) && ($member_account->sms_notification == 1)) {
			
			$data_ctbd =  array(
				"remarks" => "You earned {$card_type_data->code} referral bonus of {$remarks_message} from {$card_id}."
			);
		
			$sms_data = array(
				"member_id"=>$member_account->member_id,
				"account_id"=>$account_id,
				"mobile_number"=>$member_details->mobile_number,
				"type"=>"text-blast-generic-message",
				"is_charged"=>1,
				"tariff"=>$this->settings->notification_cost_sms,
				"params"=>$data_ctbd
			);
        
			//send sms to user
			//Modules::run('jobs/notifications/send_tb_sms',$sms_data);
		}
		
        return $data;
    }

}