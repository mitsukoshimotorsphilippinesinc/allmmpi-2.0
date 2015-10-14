<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Commissions extends  Base_Controller
{
	
	function __construct() 
	{
  		parent::__construct();

		$this->load->model("members_model");
		$this->load->model("cards_model");
		$this->load->model("vouchers_model");
		$this->load->model("payment_model");
		$this->load->model("items_model");
		$this->load->model('tracking_model');
	}
	
	public function index() 
	{
	}
	
	// public function credit_referral_bonus($params = array())
	// {
	// 	// CREDIT REFERRAL BONUS
	// 	$transaction_code = 100;
		
	// 	// get parameters
	// 	$account_id = $params["account_id"];
	// 	$card_id = $params["card_id"];
	// 	$type = $params['type'];

	// 	$referral_bonus_type = "referral_bonus_{$type}";
		
	// 	$referral_bonus = 300;
		
	// 	$referral_bonus = $this->settings->$referral_bonus_type;
	
	// 	if(substr($card_id,0,2)=="89"){ // ultimate starter pack override
	// 		$referral_bonus = 600;
	// 	}
    
	// 	$remarks = "Credited Referral Bonus from {$card_id}";
				
	// 	// credit referral fee to members funds
	// 	$this->members_model->credit_earnings($account_id,$card_id,$card_id,$referral_bonus,$transaction_code,$remarks);
	// }

	private function credit_bonus_funds($params = array())
	{
		// CREDIT REFERRAL BONUS
		$transaction_code = 100;
		$from_account_id = $params["from_account_id"];
		$account_id = $params["account_id"];
		$referral_bonus = $params["qty_amount"];
		$card_id = $params["card_id"];
		$card_type = $params["card_type"];
		$card_type_id = $params["card_type_id"];

		$remarks = "Credited {$card_type} Referral Bonus from {$card_id}";
		// credit referral fee to members funds
		$this->members_model->credit_earnings($account_id,$card_id,$card_type_id,$from_account_id,$referral_bonus,$transaction_code,$remarks);
	}

	private function credit_bonus_fpv($params = array())
	{
		$params['voucher_code'] = "FPV";
		$this->credit_bonus_voucher($params);
	}

	private function credit_bonus_mpv($params = array())
	{
		$params['voucher_code'] = "MPV";
		$this->credit_bonus_voucher($params);
	}

	private function credit_bonus_voucher($params = array())
	{
		$from_account_id = $params["from_account_id"];
		$account_id = $params["account_id"];
		$voucher_count = $params["qty_amount"];
		$card_id = $params["card_id"];
		$voucher_code = $params["voucher_code"];

		// check if company account id
		$company_accounts = $this->members_model->get_company_account_ids();
		if(in_array($account_id, $company_accounts)) return; // end method if company account // no vouchers given

		$voucher_type = $this->vouchers_model->get_account_voucher_types(array(
			'code' => $voucher_code
		));
		$voucher_type = $voucher_type[0];

		$account = $this->members_model->get_member_accounts(array(
			'account_id' => $account_id
		));
		$account = $account[0];

		for($i = 0; $i < $voucher_count; $i++) $this->vouchers_model->generate_account_voucher($voucher_type->voucher_type_id, $account->member_id, $account->account_id, $from_account_id);
	}

	private function credit_bonus_product($params = array())
	{
		$from_account_id = $params['from_account_id'];
		$account_id = $params['account_id'];
		$qty_amount = $params['qty_amount'];
		$card_id = $params['card_id'];

		$lowest_product_id = '';
		$lowest_product_price = '';
		$lowest_product_name = '';

		$products_ids = $this->cards_model->get_card_product_selections(array('card_id' => $card_id));

		$products = array();
		$product_arr = array();
		if(sizeof($products_ids)>0)
		{	
			foreach($products_ids as $product_id)
				$product_arr[] = $product_id->product_id; 
			
			$products = $this->items_model->get_product_by_ids($product_arr);
			
			$lowest_product_id = $products[0]->product_id;
			$lowest_product_price = $products[0]->standard_retail_price;
			$lowest_product_name = $products[0]->product_name;
			if(count($products) > 1)
			{
				if($products[0]->standard_retail_price > $products[1]->standard_retail_price)
				{
					$lowest_product_id = $products[1]->product_id;
					$lowest_product_price = $products[1]->standard_retail_price;
					$lowest_product_name = $products[1]->product_name;
				}
			}
			
		}

		$account_data = $this->members_model->get_member_accounts(array('account_id' => $account_id));
		$member_id = $account_data[0]->member_id;

		// generate voucher
		$voucher_id = $this->vouchers_model->generate_account_voucher(3, $member_id, $account_id, $from_account_id);
		// assign product to voucher
		$this->vouchers_model->insert_member_account_voucher_products(array(
			'voucher_id' => $voucher_id,
			'product_id' => $lowest_product_id,
			'quantity' => 1
		));

		/***** insert earnings *****/
        $referrals = 0; 
        //get_card_type_id 
        
        $earnings_where = array('member_id'=>$member_id, 'account_id'=>$account_id);
        $current_earnings = $this->members_model->get_member_earnings($earnings_where);
        if(sizeof($current_earnings)>0)
        {
            $current_earning = $current_earnings[0];
            $referrals = $current_earning->referral_bonus;
        }

        $referrals += $lowest_product_price;
        
        $details_before = json_encode($current_earnings);

        $this->members_model->update_member_earnings(array('referral_bonus'=>$referrals), $earnings_where);

        $member_earning = $this->members_model->get_member_earnings($earnings_where); 
        $details_after = json_encode($member_earning);

        $log_data = array(
                'member_id' => $member_id,
                'module_name' => "REFERRALS",
                'table_name' => "cm_member_earnings",
                'action' => "UPDATE",
                'details_before' => $details_before,
                'details_after' => $details_after,
                'remarks' => "Update member eearnings REFERRAL of member_id (".$member_id.") with with lowest price of product(".$lowest_product_id."-".$lowest_product_name.")"
        );
        $this->tracking_model->insert_logs('members', $log_data);
        
        $data = array(
          "member_id"=>$member_id,
          "account_id"=>$account_id,
    	  "account_status_id"=>$account_data[0]->account_status_id,
          "card_id"=>$card_id,
          "from_account_id"=>$from_account_id,
          "transaction_code"=>'100',
          "type"=>"PRODUCT",
          "amount"=>$lowest_product_price,
          "level" => $counter,
          "remarks"=> "Your account {$account_id} earned a (P-P)2 referral bonus with SRP of {$lowest_product_price} from {$card_id}"
        );
        
         if ($account_data[0]->sms_notification == 1) {
			$data["sms_sent"] = 1;
            $data["tariff"] = $this->settings->notification_cost_sms;
		}
		
        $this->tracking_model->insert_acct_credit_logs($data);
		
        // get credit log id
        $credit_log_id = $this->tracking_model->insert_id();

		$member_details = $this->members_model->get_member_by_id($member_id);
		
		if ($account_data[0]->sms_notification == 1) {
			
				$data_ctbd =  array(
					"remarks" => "Your acct {$account_id} earned (P-P)2 referral bonus of 1 CPV from {$card_id}."
				);
			
				$sms_data = array(
					"member_id"=>$member_id,
					"account_id"=>$account_id,
					"mobile_number"=>$member_details->mobile_number,
					"type"=>"text-blast-generic-message",
					"is_charged"=>1,
                    "log_id"=>$credit_log_id,
					"tariff"=>$this->settings->notification_cost_sms,
					"params"=>$data_ctbd
				);

				//send sms to user
				Modules::run('jobs/notifications/send_tb_sms',$sms_data);
			}	
	}

	public function credit_referral_bonus($params = array())
	{
		echo "credit_referral_bonus\n";
		// get parameters
		$from_account_id = $params["from_account_id"];
		$sponsor_account_id = $params["account_id"];
		$card_id = $params["card_id"];
		$type = "";

		if(isset($params['type']))
			$type = $params['type'];

		// only sp has referral
		$card_data = $this->cards_model->get_sp_card(array(
			'card_id' => $card_id
		));

		if(sizeof($card_data) > 0)
		{
			$card_data = $card_data[0];
			
			if($type)
				$card_data->type = $type;
			
			$card_type = $this->cards_model->get_card_types(array(
				'code' => $card_data->type
			));

			if(sizeof($card_type) > 0)
			{
				$card_type = $card_type[0];
				$referral_bonuses = $this->cards_model->get_card_type_bonuses(array(
					'card_type_id' => $card_type->card_type_id,
					'commission_type' => 'REFERRAL'
				));
				echo "bonuses\n";
				print_r($referral_bonuses);
				echo "\n--\n";

				foreach($referral_bonuses as $bonus)
				{
					$method = "credit_bonus_" . strtolower($bonus->bonus_type);
					
					if(method_exists($this, $method))
					{
						$this->$method(array(
							'from_account_id' => $from_account_id,
							'account_id' => $sponsor_account_id,
							'qty_amount' => $bonus->qty_amount,
							'card_id' => $card_id,
							'card_type' => $card_type->code,
							'card_type_id' => $card_type->card_type_id,
						));
					}
					else
					{
						echo "ERROR: METHOD DOES NOT EXIST\n";
					}
				}
			}
			else
			{
				echo "ERROR: CARD TYPE DOES NOT EXIST\n";
			}
		}
		else
		{
			echo "ERROR: CARD DATA DOES NOT EXIST\n";
		}
	}
	
	
	public function credit_points($params=array())
	{ 
		//$maintenance_period = $params['maintenance_period'];
		//if($maintenance_period != "raffle")
		//{
			$account_id = $params["account_id"];
			$card_id = $params["card_id"];
			$type = $params["type"];
			$position = $params["position"];
			$points = $params["points"];

			$card_type = $this->cards_model->get_card_types(array(
				'code' => $type
			));
			$card_type = $card_type[0];

			// switch card type to merge type id
			$card_type->card_type_id = $card_type->merge_to_point_type;

			// check is its a sales card / change to default rs type if sales card
			// if(!(boolean)$card_type->is_package)
			// {
			// 	// get RS card type id
			// 	$rs_card_type = $this->cards_model->get_card_types(array(
			// 		'code' => 'RS'
			// 	));
			// 	$rs_card_type = $rs_card_type[0];
			// 	$card_type->card_type_id = $rs_card_type->card_type_id;
			// }
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
				$this->members_model->credit_points($a->account_id,$card_id,$account_id,$card_type->card_type_id,$position,$points);			
				$position = substr($a->node_address,-1) == 1 ? "right" : "left";
			}
		//}
		
		echo "SUCCESS";
		return;		
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
			$repeat_sales_commission = $cascade_bonus[0]->qty_amount;
			
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
					$this->members_model->credit_earnings($ua->account_id,$card_id,$card_type_data[0]->card_type_id,$account_id,$repeat_sales_commission,$transaction_code,$remarks,$counter);				
				}elseif($ua->account_id == $member_account->account_id){
					//counter for unilevel commission max counter is 10
					$counter++;
					// only give commission to active accounts	
					$remarks = "Credit repeat sales unilevel commission from {$card_id}.";
					$this->members_model->credit_earnings($ua->account_id,$card_id,$card_type_data[0]->card_type_id,$account_id,$repeat_sales_commission,$transaction_code,$remarks,$counter);
				}else{
					// log missed commissions due to inactivity 
					$remarks = "No repeat sales unilevel commission due to inactivity from {$card_id}.";
					$commission = 0;
					$this->members_model->credit_earnings($ua->account_id,$card_id,$card_type_data[0]->card_type_id,$account_id,$commission,$transaction_code,$remarks);								
				}

				if ($counter==$no_of_uplines_with_commission) break;				
			}
		}
		
		echo "SUCCESS";
	}
}