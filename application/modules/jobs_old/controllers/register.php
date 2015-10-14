<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	 // if (!defined('CRON')) exit('This script is accessed thru CRON script only');

class Register extends  Base_Controller
{
	
	function __construct() 
	{
  		parent::__construct();
  		$this->load->model("cards_model");
  		$this->load->model("tracking_model");
  		$this->load->model("members_model");
  		$this->load->model("raffles_model");
  		$this->load->model("payment_model");
  		$this->load->model("items_model");
	}
	
	public function index() 
	{
		echo "Process Register...";
	}

	public function add_account($params=array())
	{
		$account_id = trim($params['account_id']);
		$account_code = trim($params['account_code']);
		$upline_id = trim($params['upline_id']);
		$sponsor_id = trim($params['sponsor_id']);
		$position = trim($params['position']);
		$member_id = trim($params['member_id']);
		$current_timestamp = $params['process_timestamp'];

		$member = $this->members_model->get_member_by_id($member_id);		

		// get primary account
		$sql = "
			SELECT
				node_type
			FROM
				cm_member_accounts
			WHERE
				member_id = '" . $member_id . "'
			ORDER BY
				insert_timestamp ASC
			LIMIT 1
		";
		$query = $this->db->query($sql);
		$prim_account = $query->result();

		if(sizeof($prim_account)>0)
		{
			$node_type = $prim_account[0]->node_type;
		}
		else
		{
			$member = $this->members_model->get_member_by_id($member_id);
			$node_type = ($member->registration_type == "INDIVIDUAL")?"REGULAR":"CORPO";
		}

		$current_timestamp = date("Y-m-d H:i:s");
		$member_user_account = $this->members_model->get_member_user_accounts("member_id = {$member_id}", null, "member_user_account_id");
		$member_user_account = $member_user_account[0];
		$card = $this->cards_model->get_sp_card("card_id = '{$account_id}' AND card_code='{$account_code}' AND status='ACTIVE'");
		$card = $card[0];

		// update sp card, set to USED
		$data = array(
			"status"=>"USED",
			"member_id"=>$member_id,
			"used_timestamp"=>$current_timestamp
		);
		$this->cards_model->update_sp_card($data,"card_id = '{$account_id}'");

		// create new account then credit referral and points if not CD or FS
		$params = array(
			"account_id"=>$account_id,
			"account_code"=>$account_code,
			"sponsor_id"=>$sponsor_id,
			"upline_id"=>$upline_id,
			"member_id"=>$member_id,
			"member_user_account_id"=>$member_user_account->member_user_account_id,
			"position"=>$position,
			"card_type"=>$card->type,
			"node_type"=>$node_type,
			"current_timestamp"=>$current_timestamp			
		);

		$this->create_member_account($params);		
		
		//set params
        $params = array(
            "first_name"=>ucfirst($member->first_name),
            "last_name"=>ucfirst($member->last_name),
            "account_id"=>$account_id,
            "upline_id"=>$upline_id,
            "sponsor_id"=>$sponsor_id,
            "position"=>strtoupper($position)
        );

        // process entry for raffle/promo
		$this->raffles_model->raffle_process('sp_encoding', 'default', $account_id, $account_id);

        $data = array(
            "email"=>$member->email,
            "type"=>"new_account",
            "params"=>$params
        );

        //send email to user
        Modules::run('jobs/notifications/send_email',$data);

		if ($member->mobile_number!='' && !is_null($member->mobile_number))
		{
	        $data = array(
	            "member_id"=>$member->member_id,
	            "mobile_number"=>$member->mobile_number,
	            "type"=>"add-new-account-sms",
	            "params"=>$params
	        );

	        //send sms to user
	        Modules::run('jobs/notifications/send_sms',$data);			
		}

		echo "SUCCESS";
        return;
	}

	public function process ($params=array()) 
	{
		$account_id = trim($params['account_id']);
		$account_code = trim($params['account_code']);
		
		$card = $this->cards_model->get_sp_card("card_id = '{$account_id}' AND card_code='{$account_code}' AND status='ACTIVE'");
		$card = $card[0];
		
		$service_depot_id = abs($params['service_depot_id']);	
		
		if ($service_depot_id==2) // ortigas depot in user facilities
			$service_depot_id = 1;
		else if ($service_depot_id==4) // cebu depot in user facilities
			$service_depot_id = 2;
		else if ($service_depot_id==5) // davao depot in user facilities
			$service_depot_id = 3;
		else
			$service_depot_id = 1;

		$first_name = strtoupper(trim($params['first_name']));
		$middle_name = strtoupper(trim($params['middle_name']));
		$last_name = strtoupper(trim($params['last_name']));
		$primary_email = trim($params['primary_email']);
			
		$proper_name = $last_name . ", " . $first_name . " " . $middle_name;

		$username = trim($params['username']);
        $rf_id = trim($params['rf_id']);
        $group_name = trim($params['group_name']);
		
		$nationality = strtoupper(trim($params['nationality']));
		$marital_status = strtoupper(trim($params['marital_status']));
		$birthday = trim($params['birthday']);
		$gender = strtoupper(trim($params['gender']));
		$password1 = $params['password1'];
		
		$country_code = $params['country_code'];
		$area_code = trim($params['area_code']);
		$mobile_number = trim($params['mobile_number']);           	            
		$address = strtoupper(trim($params['address']));
		$city = strtoupper(trim($params['city']));
		$state_province = strtoupper(trim($params['state_province']));
		$zip_postalcode = strtoupper(trim($params['zip_postalcode']));
		$country = strtoupper(trim($params['country']));	           
		$tin_number = trim($params['tin_number']); //FIND
		$registration_type = strtoupper(trim($params['registration_type']));
		$sponsor_id = trim($params['sponsor_id']);
		$upline_id = trim($params['upline_id']);
		$position = $params['position'];
		$beneficiary1 = strtoupper(trim($params['beneficiary1']));
		$beneficiary2 = strtoupper(trim($params['beneficiary2']));
		
		$complete_address = $address . ' ' . $city . ' ' . $state_province . ' ' . $country . ' ' . $zip_postalcode;
		$mobile_number = $country_code . $area_code . $mobile_number;

		$current_timestamp = $params['process_timestamp'];

        // get group_name and id based on upline_id
        $upline = $this->members_model->get_member_account_by_account_id($upline_id);
        $upline_details = $this->members_model->get_member_by_id($upline->member_id);
		
        // insert details to cm_members
		$data = array(		
           	'first_name' => $first_name,
           	'middle_name' => $middle_name,
           	'last_name' => $last_name,
			'email' => $primary_email,	
			'mobile_number' => $mobile_number,    
			'home_address' => $complete_address,       	            
			'home_address_street' => $address,
           	'home_address_city' => $city,
           	'home_address_province' => $state_province,          	
           	'home_address_country' => $country,
			'home_address_zip_code' => $zip_postalcode,
			'birthdate' => $birthday,
			'sex' => $gender,
			'nationality' => $nationality,
          	'marital_status' => substr($marital_status,0,1),
			'tin' => $tin_number,
            'service_depot' => $service_depot_id,
            'rf_id' => $rf_id,
            'group_name' => $upline_details->group_name,
            'group_id' => $upline_details->group_id,
			'beneficiary1' => $beneficiary1,
			'beneficiary2' => $beneficiary2,
			'service_depot'=>$service_depot_id,
			'completed_timestamp'=>$current_timestamp,
			'registration_type'=>$registration_type,
			'insert_timestamp'=>$current_timestamp
		);

		$this->members_model->insert_member($data);
		
		$new_member_id = $this->members_model->insert_id();

		$details_after = array('id' => 0, 'details' => $data);
		$details_after = json_encode($details_after);
		$new_member_log_data = array(
			'member_id' => 0, //no member id given yet	
			'module_name' => 'MEMBER REGISTRATION',
			'table_name' => 'cm_members',
			'action' => 'ADD',
			'details_before' => "",
			'details_after' => $details_after,
			'remarks' => "",
		);
		$this->tracking_model->insert_logs('members', $new_member_log_data);
		
		// update card
		//$data = array("status"=>"USED","member_id"=>$new_member_id,"used_timestamp"=>$current_timestamp);
		//$this->cards_model->update_sp_card($data,"card_id = '{$account_id}'");

		// insert details to cm_member_user_accounts
		$data = array(
			'member_id' => $new_member_id,
           	'username' => $username,
			'password' => md5(strtoupper($password1)),
			'orig_password' => $password1,
			'primary_account_id' =>  $account_id,
			'email' => $primary_email
		);
		$this->members_model->insert_member_user_accounts($data);
		
		$new_member_user_account_id = $this->members_model->insert_id();
		
		//MEMBER USER ACCOUNT LOGGING
		$details_after = array('id' => 0, 'details' => $data);
		$details_after = json_encode($details_after);
		$new_member_log_data = array(
			'member_id' => $new_member_id,
			'module_name' => 'MEMBER REGISTRATION',
			'table_name' => 'cm_member_user_accounts',
			'action' => 'ADD',
			'details_before' => "",
			'details_after' => $details_after,
			'remarks' => "",
			'insert_timestamp' => $current_timestamp
		);
		$this->tracking_model->insert_logs('members', $new_member_log_data);
		//END LOGGING
		
		//create member_account and credit referrals and points if not CD or FS
		$node_type = ($registration_type == "INDIVIDUAL")?"REGULAR":"CORPO";
		$params = array(
			"account_id"=>$account_id,
			"account_code"=>$account_code,
			"sponsor_id"=>$sponsor_id,
			"upline_id"=>$upline_id,
			"member_id"=>$new_member_id,
			"member_user_account_id"=>$new_member_user_account_id,
			"position"=>$position,
			"card_type"=>$card->type,			
			"node_type"=>$node_type,
			"current_timestamp"=>$current_timestamp
		);
		$this->create_member_account($params);

        $timestamp = $current_timestamp;
        $generated_email_code = md5($new_member_id . $timestamp);

        $data = array(
            'member_id' => $new_member_id,
            'mobile_code' => $generated_email_code,
            'email_code' => $generated_email_code,
            'insert_timestamp' => $timestamp,
            'mobile_verification_timestamp' => '0000-00-00 00:00:00'
        );
        
        // check if member got a existing email verificaiton
        $result = $this->members_model->get_member_verification(array('member_id' => $new_member_id));
        if (count($result) > 0) {
            $this->members_model->update_member_verification($data, array('member_id' => $new_member_id));
        } else {
            $this->members_model->insert_member_verification($data);
        }

        $base_url = $this->config->item('base_url') . "/members/verification/index/email/{$generated_email_code}";

        // process entry for raffle/promo
		$this->raffles_model->raffle_process('sp_encoding', 'default', $account_id, $account_id);

        // send email
		//set params needed for notification
        $params = array(
            "first_name"=>ucfirst($first_name),
            "last_name"=>ucfirst($last_name),
            "link"=>$base_url,
            "generated_email_code"=>$generated_email_code,
            "account_id"=>$account_id,
            "sponsor_id"=>$sponsor_id,
            "upline_id"=>$upline_id,
            "position"=>strtoupper($position)
        );
		$data = array(
            "email"=>$primary_email,
            "type"=>"welcome_email",
            "params"=>$params
        );
        //send email to user
        Modules::run('jobs/notifications/send_email',$data);

        $data = array(
            "member_id"=>$new_member_id,
            "mobile_number"=>$mobile_number,
            "type"=>"welcome-message-sms",
            "params"=>$params
        );

        //send sms to user
        Modules::run('jobs/notifications/send_sms',$data);
        
        echo "SUCCESS";
        return;	
	}

	public function create_member_account($params=array())
	{
		// reusable function to create new account
		$account_id = $params["account_id"];
		$account_code = $params["account_code"];
		$sponsor_id = $params["sponsor_id"];
		$upline_id = $params["upline_id"];
		$member_id = $params["member_id"];
		$member_user_account_id = $params["member_user_account_id"];
		$position = $params["position"];
		$card_type = $params["card_type"];
		$node_type = $params["node_type"];
		$params['card_number'] = $account_id;
		
		// current_timestamp
		$current_timestamp = $params["current_timestamp"];
		
		$this->log_sp_card_update($params,"CREATE MEMBER ACCOUNT");
		
		// check upline and side and create the accounts node_address
		$side = $position == "right" ? 1 : 2;
		$upline = $this->members_model->get_member_account_by_account_id($upline_id);		
		$node_address = $upline->node_address . $side;
		
		// check sponsor and no of downlines and create the uni_node address
		$sponsor = $this->members_model->get_member_account_by_account_id($sponsor_id);
		$no_of_unilevel_downlines = $this->members_model->get_member_accounts_count("sponsor_id = {$sponsor_id}");
		$my_uni_node_level = $no_of_unilevel_downlines + 1;		
		$uni_node = $sponsor->uni_node . "." . $my_uni_node_level;
		
		// get account type
		$account_type = $this->members_model->get_member_account_type_by_type($card_type);

		// insert details to cm_member_accounts
		$data = array(
			"member_id"=>$member_id,
			"member_user_account_id"=>$member_user_account_id,
			"account_id"=>$account_id,
			"sponsor_id"=>$sponsor_id,
			"upline_id"=>$upline_id,
			"account_type_id"=>$account_type->account_type_id,
			"account_status_id"=>1,
			"node_address"=>$node_address,
			"uni_node"=>$uni_node,
			"node_type"=>$node_type
		);		
		$this->members_model->insert_member_accounts($data);
		
		$insert_id = $this->members_model->insert_id();

		// cascade downline counter
		$this->members_model->member_account_add_downline_count($account_id);
		
		//MEMBER ACCOUNT LOGGING
		$details_after = array('id' => 0, 'details' => $data);
		$details_after = json_encode($details_after);
		$new_member_account_log_data = array(
			'member_id' => $member_id,
			'module_name' => 'ADD NEW ACCOUNT',
			'table_name' => 'cm_member_accounts',
			'action' => 'ADD',
			'details_before' => "",
			'details_after' => $details_after,
			'remarks' => "",
		);								
		$this->tracking_model->insert_logs('members', $new_member_account_log_data);
		//END LOGGING
		
		// insert details to cm_member_earnings
		$data = array(
			"member_id"=>$member_id,
			"account_id"=>$account_id
		);		
		$this->members_model->insert_member_earnings($data);
		
		//MEMBER ACCOUNT EARNINGS LOGGING
		$details_after = array('id' => 0, 'details' => $data);
		$details_after = json_encode($details_after);
		$new_member_account_earnings_log_data = array(
			'member_id' => $member_id,
			'module_name' => 'ADD NEW ACCOUNT MEMBER EARNINGS',
			'table_name' => 'cm_member_earnings',
			'action' => 'ADD',
			'details_before' => "",
			'details_after' => $details_after,
			'remarks' => "",
		);

		$this->tracking_model->insert_logs('members', $new_member_account_earnings_log_data);
		//END LOGGING

		// get card type modifiers
		$sp_card_details = $this->cards_model->get_sp_card_by_card_id($account_id);
		$modifiers = $this->cards_model->get_modifiers_by_card_type($sp_card_details->type); 

		$card_type = $this->cards_model->get_card_types(array(
			'code' => $sp_card_details->type
		));
		$card_type = $card_type[0];

		if(in_array("REFERRAL", $modifiers))
		{
			// credit referral
			$params = array(
				"card_id" => $account_id,
				"account_id" => $sponsor_id,
				"from_account_id" => $account_id
			);
			if($sp_card_details->type != 'CD-ERHM')
				Modules::run('jobs/commissions/credit_referral_bonus',$params);
		}

		// sp special case
		if($sp_card_details->type == 'SP')
		{
			// get ERHM card type
			$erhm_card_type = $this->cards_model->get_card_types(array('code' => 'ERHM'), null, null, array('card_type_id'));
			$erhm_card_type_id = $erhm_card_type[0]->card_type_id;

			// update account type from SP to ERHM
			$this->members_model->update_member_accounts(array(
				'account_type_id' => $erhm_card_type_id
			), array(
				'account_id' => $account_id
			));

			// update sp_card_details and card_type to current (ERHM)
			$sp_card_details->type = 'ERHM';
			$card_type = $this->cards_model->get_card_types(array(
				'code' => $sp_card_details->type
			));
			$card_type = $card_type[0];
		}

		if(in_array("PAIRING", $modifiers))
		{
			$params = array(
				"card_id" => $account_id,
				"account_id" => $upline_id,
				"type" => $sp_card_details->type,
				"position" => $position,
				"points" => $card_type->points,
			);

			if($sp_card_details->type != 'CD-ERHM')
				Modules::run('jobs/commissions/credit_points', $params);
		}
		
		// //check new account, if account_type_id = FS and CD do nothing, no points will be generated
		
		// if ($card_type=='FS' || $card_type=='CD')
		// {
		// 	// do nothing, no points will be generated
		// 	return;
		// } 
		
		// // otherwise credit referral bonus and credit points starting from upline				
		
		// // credit points and check for pairs
		// $sps = array("RG","CD","FS","SP","UP","PP");  //SP, CD, FS, TP - starter packs
		// $vps = array("MF","VP"); //VP - value packs
		
		// if (in_array($card_type,$sps))
		// {
		// 	$_type = "sp";
		// 	$points = $this->settings->sp_points;
		// }
		// else if (in_array($card_type,$vps))
		// {
		// 	$_type = "vp";			
		// 	$points = $this->settings->vp_points;
		// }
		// else if ($card_type=='TP')
		// {
		// 	$_type = "tp";
		// 	$points = $this->settings->tp_points;
		// }
		// else
		// {
		// 	$_type = "sp";
		// 	$points = $this->settings->sp_points;
		// }
		
		// //credit referral
		// $params = array("card_id"=>$account_id,"account_id"=>$sponsor_id,"type" => $_type);
		// Modules::run('jobs/commissions/credit_referral_bonus',$params);

		// $params = array(
		// 	"card_id"=>$account_id,
		// 	"account_id"=>$upline_id,
		// 	"type"=>$_type,
		// 	"position"=>$position,
		// 	"points"=>$points,
		// );		
		// Modules::run('jobs/commissions/credit_points',$params);
		// return;
	}

	public function upgrade_account($params=array())
	{
		$account_id = trim($params['account_id']);
		$card_number = trim($params['card_number']);

		$this->log_sp_card_update($params,"UPGRADE ACCOUNT");

		// mark card as used
		$this->cards_model->update_sp_card(array(
			'status' => 'USED'
		), array(
			'card_id' => $card_number
		));

		$upgrade_card = $this->cards_model->get_sp_card(array(
			'card_id' => $card_number
		));
		$upgrade_card = $upgrade_card[0];

		// insert to member account upgrades
		$data = array(
			'base_account_id' => $account_id,
			'upgrade_account_id' => $upgrade_card->card_id,
			'upgrade_type' => $upgrade_card->type
		);

		$card_type = $this->cards_model->get_card_type_by_code($upgrade_card->type);

		$this->members_model->insert_member_account_upgrades(array(
			'base_account_id' => $account_id,
			'upgrade_account_id' => $upgrade_card->card_id,
			'upgrade_type' => $upgrade_card->type,
			'card_type_id' => $card_type->card_type_id
		));

		$base_account = $this->members_model->get_member_accounts(array(
			'account_id' => $account_id
		));
		$base_account = $base_account[0];

		$logdata = array(
			'member_id' => $base_account->member_id,
			'module_name' => 'UPGRADE ACCOUNT',
			'table_name' => 'cm_member_account_upgrades',
			'action' => 'ADD',
			'details_before' => '',
			'details_after' => json_encode($data),
			'remarks' => 'Upgrade account of member_id = '. $base_account->member_id .' to ' . $upgrade_card->type
		);

		$this->tracking_model->insert_logs('members', $logdata);
		// check if sponsor has same upgrade type
		$check_sponsor = $this->members_model->get_member_account_upgrades(array(
			'base_account_id' => $base_account->sponsor_id,
			'upgrade_type' => $upgrade_card->type
		));

		if(sizeof($check_sponsor) > 0)
		{
			// credit referral
			$params = array(
				"card_id" => $card_number,
				"account_id" => $base_account->sponsor_id,
				"from_account_id" => $account_id
			);
			//echo "credit_referral_bonus";
			Modules::run('jobs/commissions/credit_referral_bonus',$params);
		}
		else
		{
			//echo "TODO";
			// TODO: log no upgrade referral bonus(es)
		}

		//credit points
		$member_account = $this->members_model->get_member_account_by_account_id($base_account->upline_id);
		$address = $member_account->node_address;
		$address_length_count = strlen($address);
		$addresses = array();

		// get addresses of all uplines
		for ($i=1; $i <= $address_length_count; $i++) $addresses[] = "'" . substr($address, 0, $i) . "'";
		$new_addresses = implode(",",$addresses);
		
		// get all uplines
		$accounts = $this->members_model->get_member_accounts("node_address in ({$new_addresses})",NULL,"LENGTH(node_address) DESC","account_id,node_address");

		$card_type = $this->cards_model->get_card_types(array(
			'code' => $upgrade_card->type
		));
		$card_type = $card_type[0];

		// add to cm_member_account_product_transaction
		// $product_transaction = $this->payment_model->get_payment_transaction_products(array('card_id' => $card_number, 'package_product_id' => 0));
		// if(sizeof($product_transaction) > 0)
  //       {
  //           $product_transaction = $product_transaction[0];

  //           $ar_number = $this->payment_model->get_payment_transactions(array('transaction_id' => $product_transaction->transaction_id), null, null, array('ar_number'));
  //           $ar_number = $ar_number[0]->ar_number;

  //           $node_address = $this->members_model->get_member_accounts(array('account_id' => $account_id), null, null, array('node_address'));
  //           $node_address = $node_address[0]->node_address;

  //           $this->members_model->insert_member_account_product_transaction(array(
  //               'transaction_id' => $product_transaction->transaction_id,
  //               'ar_number' => $ar_number,
  //               'card_id' => $card_number,
  //               'account_id' => $account_id,
  //               'node_address' => $node_address,
  //               'product_id' => $product_transaction->lowest_product
  //           ));
  //       }

		print_r("\nadding to product to cm_member_account_product_transaction\n");
		$card_product_selection = $this->cards_model->get_card_product_selections(array('card_id' => $card_number));
		print_r($card_product_selection);

		if(sizeof($card_product_selection) > 0)
		{
			$node_address = $this->members_model->get_member_accounts(array('account_id' => $account_id), null, null, array('node_address'));
            $node_address = $node_address[0]->node_address;

            $lowest_product_id = $card_product_selection[0]->product_id;

            if($card_product_selection[0]->qty == 2)
            {
            	$lowest_product_id = $card_product_selection[0]->product_id;
            }
            else
            {
            	$lowest_product_id = $this->items_model->get_lower_product_from_ids($card_product_selection[0]->product_id, $card_product_selection[1]->product_id);
            }

			$this->members_model->insert_member_account_product_transaction(array(
                'card_id' => $card_number,
                'account_id' => $account_id,
                'node_address' => $node_address,
                'product_id' => $lowest_product_id
            ));
		}

		// credit points and check for pairing
		$position = substr($base_account->node_address,-1) == 1 ? "right" : "left";
		foreach($accounts as $a)
		{
			// check if account has same upgrade type
			$check_account = $this->members_model->get_member_account_upgrades(array(
				'base_account_id' => $a->account_id,
				'upgrade_type' => $upgrade_card->type
			));

			if(sizeof($check_account) > 0)
			{
				// credit points and check for pairings
				$this->members_model->credit_points($a->account_id, $card_number, $account_id, $card_type->card_type_id, $position, $card_type->points);
			}
			else
			{
				// TODO: log no upgrade pairing bonus(es)
			}
			
			$position = substr($a->node_address,-1) == 1 ? "right" : "left";
		}
		echo "SUCCESS";
	}
 

	private function log_sp_card_update($params = array(),$action = "")
	{
		$account_id = $params["account_id"];
	    $member_id = $params["member_id"];
	    $card_number = trim($params['card_number']);
	    //$member_user_account_id = $params["member_user_account_id"];
	    //$current_timestamp = $params["current_timestamp"];
	    
	    //get details before updating
	    $sp_card_details = $this->cards_model->get_sp_card_by_card_id($account_id);
	    $sp_card_details_before = array("status"=>$sp_card_details->status,"member_id"=>$sp_card_details->member_id,"used_timestamp"=>$sp_card_details->used_timestamp);
	    $details_before = array('id' => $account_id, 'details' => $sp_card_details_before);
	    $details_before = json_encode($details_before);
	    
	    $used_timestamp = date("Y-m-d H:i:s");

	    // update card
	    $data = array(
	    			"status"=>"USED",
	    			"member_id"=>$member_id,
	    			"used_timestamp"=>$used_timestamp
	    		);
	    $this->cards_model->update_sp_card($data,"card_id = '{$card_number}'");
	    
	    //================================
	    //LOGGING FOR UPDATING SP CARD
	    //================================
	    $details_after = array('id' => $account_id, 'details' => $data, );
	    $details_after = json_encode($details_after);
	    $update_sp_card_logs = array(
			'member_id' => $member_id,
			'module_name' => $action,
			'table_name' => "is_sp_cards",
			'action' => "UPDATE",
			'details_before' => $details_before,
			'details_after' => $details_after,
			'remarks' => ""
	    );
	    $this->tracking_model->insert_logs('members', $update_sp_card_logs);
	}

}