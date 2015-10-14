<?php

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Account extends Site_Controller {

	function __construct() {
		parent::__construct();		
		
		$this->load->model('cards_model');
		$this->load->model('raffles_model');
		$this->load->model('jobs_model');
		$this->load->model('items_model');
		$this->load->helper('utils_helper');
	}
       
	public function check_sp() {
		$_card_code = strtoupper(trim($this->input->post('_card_code')));
		$_account_id = trim($this->input->post('_account_id'));
		
		// get card series (first 2 chars of account_id)
		$account_card_series = substr($_account_id, 0, 2);
		
		$card_type_details = $this->cards_model->get_card_types_view_by_series($account_card_series);
		
		if (count($card_type_details) == 0) {
			// not a valid card_series
			$message = "Not a valid Account ID. Unknown Card Series.";
			
			echo json_encode(array("status"=>0, "message"=>$message));
			return;
			
		} else {
			// check if pack
			if ($card_type_details->is_package == 1) {
				
				// find in is_sp_cards
				$where = array(
					"card_id"=>$_account_id,
					"status"=>"ACTIVE"
				);
				$card_details = $this->cards_model->get_sp_card($where, null, null);
								
				// check if correct card code
				if (count($card_details) == 0) {
					// not present in is_sp_cards
					$message = "Account ID not found.";

					echo json_encode(array("status"=>0, "message"=>$message));
					return;
				} else {
					// check if code is the same as in DB
					$card_details = $card_details[0];
					if ($card_details->card_code == $_card_code) {
						$message="";
						echo json_encode(array("status"=>1, "message"=>$message));
						return;
						
					} else {
						// not present in is_sp_cards
						$message = "Card Code did not match.";

						echo json_encode(array("status"=>0, "message"=>$message));
						return;
					}			
				}					
			} else {
				// not a pack
				$message = "Not a valid Account ID. Not a Package Card.";

				echo json_encode(array("status"=>0, "message"=>$message));
				return;
			}
		}			
	}
	
	public function check_id() {
		$_id = trim($this->input->post('_id'));
		$_check_by = trim($this->input->post('_check_by'));
		
		$id_type = ucfirst($_check_by);
	
		if($_id == "") {      
			echo json_encode(array('status'=>0,'html'=>"{$id_type} ID is required"));
            return;
        }

		$account_to_check = $this->members_model->get_member_account_by_account_id($_id);

		if (empty($account_to_check))
		{
			echo json_encode(array('status'=>0,'html'=>"Invalid {$id_type} ID"));
		    return;
		}

		// get members account sort by node_address asc
		$where = array(
			"member_id"=>$this->member->member_id			
		);				
		$member_base_account_details = $this->members_model->get_member_accounts($where,null,"LENGTH(node_address) ASC");
		
		if (count($member_base_account_details) == 0) {
			echo json_encode(array('status'=>0,'html'=>"There seems to be a problem with your account. Please contact the administrator."));
            return;	
		} else {
			$member_base_account_details = $member_base_account_details[0];
			$tmp_node_address = $account_to_check->node_address;
			if(strlen($account_to_check->node_address) > strlen($member_base_account_details->node_address)) {
				$account_to_check->node_address = substr($account_to_check->node_address, 0, strlen($member_base_account_details->node_address)-1);
			}
		
			// check if upline is same line with member
			$pos = strpos($member_base_account_details->node_address, $account_to_check->node_address);
			$account_to_check->node_address = $tmp_node_address;
			
			//var_dump($pos ."|". $account_to_check->node_address . "|" .  $member_base_account_details->node_address);
			
			if (($pos === false) || ($pos < 0)) {
				// invalid upline
				echo json_encode(array('status'=>0,'html'=>"Invalid {$id_type} ID. Not inline with your account."));
			    return;
			} else {
				
				if ($_check_by == "upline") { 
					
					// inline with account, check if still with available slot
					$upline_left_node_address = $account_to_check->node_address . "2"; 
					$upline_right_node_address = $account_to_check->node_address . "1"; 

					// check if left and right nodes are available
					$upline_left_node_count = $this->members_model->get_member_accounts_count("node_address = '{$upline_left_node_address}'");
					$upline_right_node_count = $this->members_model->get_member_accounts_count("node_address = '{$upline_right_node_address}'");

					$available_side = "";
				
					if (($upline_left_node_count > 0) && ($upline_right_node_count > 0)) {
						$available_side = "0";
					} else if (($upline_left_node_count <= 0) && ($upline_right_node_count > 0)) {
						$available_side = "2";
					} else if (($upline_left_node_count > 0) && ($upline_right_node_count <= 0)) {
						$available_side = "1";
					} else {
						$available_side = "3";
					}
								
					echo json_encode(array('status'=>1,'html'=>"Valid {$id_type} ID.",'available_side'=>$available_side));
			    	return;
			
				} else {
					
					echo json_encode(array('status'=>1,'html'=>"Valid {$id_type} ID.",'is_valid_sponsor'=>"true"));
			    	return;
				}
			}			
		}				
	}
	
	public function confirm_add() {
		$_account_id = trim($this->input->post('_account_id'));
		$_card_code = trim($this->input->post('_card_code'));
		$_upline_id = trim($this->input->post('_upline_id'));		
		$_sponsor_id = trim($this->input->post('_sponsor_id'));
		$_position = strtoupper(trim($this->input->post('_position')));
				
		$html = "<p><label>You are about to Add an Account with the following details:</label>	
				<table class='table table-striped table-bordered'>
					<thead>
					</thead>
					<tbody>
						<tr>
							<td style='width:120px;'><label><strong>Account/Card ID</strong></label></td>
							<td><label class=''>{$_account_id}</label></td>		
						</tr>
						<tr>
							<td style='width:120px;'><label><strong>Upline ID</strong></label></td>
							<td><label class=''>{$_upline_id}</label></td>		
						</tr>
						<tr>
							<td style='width:120px;'><label><strong>Sponsor ID</strong></label></td>
							<td><label class=''>{$_sponsor_id}</label></td>		
						</tr>	
						<tr>
							<td style='width:120px;'><label><strong>Position</strong></label></td>
							<td><label class=''>{$_position}</label></td>		
						</tr>					
					</tbody>
				</table>				
				<label>Do you want to proceed?</label>
			</p>";
		
		echo json_encode(array("status"=>"1","html"=>$html));
		return;
		
	}
	
		
	public function add_account() {
		$account_id = trim($this->input->post('_account_id'));
		$account_code = trim($this->input->post('_card_code'));
		$upline_id = trim($this->input->post('_upline_id'));		
		$sponsor_id = trim($this->input->post('_sponsor_id'));
		$position = trim($this->input->post('_position'));
		
		// check card
		$card = $this->cards_model->get_sp_card("card_id = '{$account_id}' AND card_code='{$account_code}' AND status='ACTIVE'");
		
		if (empty($card)) 
		{			
		 	echo json_encode(array("status"=>0,"html"=>"Invalid Account ID"));
			return;
		}
		
		$card = $card[0];

		// check if card is an upgrade card
		$modifiers = $this->cards_model->get_modifiers_by_card_type($card->type);
		if(in_array("UPGRADE", $modifiers))
		{
			echo json_encode(array("status"=>0,"html"=>"Cannot use an Upgrade Card"));
			return;
		}
		
		$current_timestamp = date("Y-m-d H:i:s");
						
		// insert into cm_member_accounts
		// get first the user_account_id
		$member_user_account = $this->members_model->get_member_user_accounts("member_id = {$this->member->member_id}", null, "member_user_account_id");		
		
		if (empty($member_user_account)) {
			echo json_encode(array('status'=>0,'html'=>"Invalid member id."));
	    	return;
		} else {
			// get first user's account
			$member_user_account = $member_user_account[0];			
		}

		// check upline
		$upline = $this->members_model->get_member_account_by_account_id($upline_id);
		
		if (empty($upline)) {
			echo json_encode(array('status'=>0,'html'=>"Invalid upline account id."));
	    	return;
		}
		
		// check sponsor
		$sponsor = $this->members_model->get_member_account_by_account_id($sponsor_id);
		
		if (empty($sponsor)) {
			echo json_encode(array('status'=>0,'html'=>"Invalid Sponsor Account Details"));
	    	return;
		}

		// INSERT JOB
		$this->load->model('jobs_model');
		$params = array(
			'account_id' => $account_id,
			'account_code' => $account_code,
			'upline_id' => $upline_id,
			'sponsor_id' => $sponsor_id,
			'position' => $position,
			'member_id' => $this->member->member_id
		);
		$job_data = array(
			'job_type_id' => 5, // add account
			'parameters' => json_encode($params)
		);
		$this->jobs_model->insert_job($job_data);
		$job_id = $this->jobs_model->insert_id();
		
		// RUN JOB
		job_exec($job_id);

		echo json_encode(array('status'=>1,'html'=>"Add New Account Successful."));
    	return;

		/* ---------------- OLD ---------------- */		
		
		// update sp card, set to USED
		$data = array(
			"status"=>"USED",
			"member_id"=>$this->member->member_id,
			"used_timestamp"=>$current_timestamp
		);
		$this->cards_model->update_sp_card($data,"card_id = '{$account_id}'");
		

		// create new account then credit referral and points if not CD or FS
		$params = array(
			"account_id"=>$account_id,
			"account_code"=>$account_code,
			"sponsor_id"=>$sponsor_id,
			"upline_id"=>$upline_id,
			"member_id"=>$this->member->member_id,
			"member_user_account_id"=>$member_user_account->member_user_account_id,
			"position"=>$position,
			"card_type"=>$card->type			
		);		
		Modules::run('main/registration/create_member_account',$params);	

        //set params
        $params = array(
            "first_name"=>ucfirst($this->member->first_name),
            "last_name"=>ucfirst($this->member->last_name),
            "account_id"=>$account_id,
            "upline_id"=>$upline_id,
            "sponsor_id"=>$sponsor_id,
            "position"=>strtoupper($position)
        );
		
		// process entry for raffle/promo
		$this->raffles_model->raffle_process('sp_encoding', 'default', $account_id, $account_id);

        $data = array(
            "email"=>$this->member->email,
            "type"=>"new_account",
            "params"=>$params
        );

        //send email to user
        Modules::run('jobs/notifications/send_email',$data);

		if ($this->member->mobile_number!='' && !is_null($this->member->mobile_number))
		{
	        $data = array(
	            "member_id"=>$this->member->member_id,
	            "mobile_number"=>$this->member->mobile_number,
	            "type"=>"add-new-account-sms",
	            "params"=>$params
	        );

	        //send sms to user
	        Modules::run('jobs/notifications/send_sms',$data);			
		}




		//TODO: add to log table		
		
		echo json_encode(array('status'=>1,'html'=>"Add New Account Successful."));
    	return;		
	}

	public function upgrade_account()
	{
		$account_id = $this->input->post('account_id');
		$card_number = $this->input->post('card_number');
		$card_code = $this->input->post('card_code');


		if(empty($account_id) || empty($card_number) || empty($card_code))
		{
			$this->return_json(0, 'Invalid Request');
			return;
		}

		$account = $this->members_model->get_member_accounts(array(
			'account_id' => $account_id
		));

		if(sizeof($account) == 0)
		{
			$this->return_json(0, 'Account not Found');
			return;
		}

		$upgrade_card = $this->cards_model->get_sp_card(array(
			'card_id' => $card_number,
			'card_code' => $card_code,
			'status' => 'ACTIVE',
		));

		if(sizeof($upgrade_card) == 0)
		{
			$this->return_json(0, 'Upgrade Card not Found');
			return;
		}
		$upgrade_card = $upgrade_card[0];

		$account_upgrades = $this->members_model->get_member_account_upgrades(array(
			'base_account_id' => $account_id,
			'upgrade_type' => $upgrade_card->type
		));

		if(sizeof($account_upgrades) > 0)
		{
			$this->return_json(0, 'Account is already upgraded using this type');
			return;
		}

		// create job (upgrade referral and pairing)
		$params = array(
			'account_id' => $account_id,
			'card_number' => $card_number,
			'member_id' =>$account[0]->member_id
		);
		$job_data = array(
			'job_type_id' => 9, // upgrade account
			'parameters' => json_encode($params)
		);
		$this->jobs_model->insert_job($job_data);
		$job_id = $this->jobs_model->insert_id();
		
		// execute job
		job_exec($job_id);

		$this->return_json(1, 'Ok');
		return;
	}

	public function get_upgrade_card_details()
	{
		$card_number = $this->input->post('card_number');
		$card_code = $this->input->post('card_code');
		$base_account_id = $this->input->post('base_account_id');

		if(empty($card_number) || empty($base_account_id))
		{
			$this->return_json(0, 'Invalid Request');
			return;
		}

		$upgrade_card = $this->cards_model->get_sp_card(array(
			'card_id' => $card_number,
			'card_code' => $card_code,
			'status' => 'ACTIVE'
		));

		if(sizeof($upgrade_card) == 0)
		{
			$this->return_json(0, 'Upgrade Card not found or Invalid card details');
			return;
		}
		$upgrade_card = $upgrade_card[0];

		// check if card has upgrade modifier
		$modifiers = $this->cards_model->get_modifiers_by_card_type($upgrade_card->type);
		if(!in_array("UPGRADE", $modifiers))
		{
			$this->return_json(0, 'Card encoded is not an upgrade card.');
			return;
		}

		$card_type = $this->cards_model->get_card_types(array(
			'code' => $upgrade_card->type
		));
		$card_type = $card_type[0];

		// check upgrade mapping
		$base_account_type = $this->cards_model->get_sp_card(array('card_id' => $base_account_id), null, null, array('type'));
		$base_account_type_data = $this->cards_model->get_card_types(array('code' => $base_account_type[0]->type));

		$mapping_data = $this->cards_model->get_upgrade_card_mapping(array(
			'base_card_type_id' => $base_account_type_data[0]->card_type_id,
			'upgrade_card_type_id' => $card_type->card_type_id
		));

		if(sizeof($mapping_data) == 0)
		{
			$this->return_json(0, 'This upgrade card is not compatible with the account');
			return;
		}

		$upgrade_card->type_name = $card_type->name;

		// if P2P get stored items
		$upgrade_card->stored_items = array();
		if($upgrade_card->type_name == 'P2P') $upgrade_card->stored_items = $this->cards_model->get_card_product_selections(array('card_id' => $card_number));
		// if($upgrade_card->type_name == 'P2P' && sizeof($upgrade_card->stored_items) == 0)
		// {
		// 	$this->return_json(0, 'There is no stored items on this P2P Card', array('p2p_item' => true));
		// 	return;
		// }
		foreach($upgrade_card->stored_items as $stored_item) {
			$prod_name = $this->items_model->get_product_by_id($stored_item->product_id);
			$stored_item->product_name = $prod_name->item_name;
		}

		$this->return_json(1, 'Ok', array(
			'card_details' => $upgrade_card
		));
		return;
	}
}

?>