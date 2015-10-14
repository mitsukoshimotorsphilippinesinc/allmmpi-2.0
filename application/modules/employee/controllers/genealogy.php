<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Genealogy extends Base_Controller {

	public function __construct()
	{
		parent::__construct();
		// load contents model
		$this->load->model("members_model");
		$this->load->model("cards_model");
		$this->load->model("settings_model");
		$this->load->model("items_model");
		// load pager library
		$this->load->library('pager');
	}
	
	public function index()
	{
	}
	
	public function get_network()
	{
		$account_id = $this->input->get_post('account_id');
		
		$account = $this->members_model->get_member_account_by_account_id($account_id, "account_id, member_id, sponsor_id, upline_id, account_type_id, account_status_id, node_address, ms_monthly_maintenance_ctr, ms_annual_maintenance_ctr, monthly_maintenance_ctr, annual_maintenance_ctr, uni_node");

		if (empty($account))
		{
			$this->return_json("error","Invalid Account ID.");
			return;
		}

		$card_type_details = $this->cards_model->get_card_type_by_id($account->account_type_id);
		$account->account_type_code = $card_type_details->code;
			
		
		$is_active = ($account->account_status_id == 1 || $account->account_status_id == 3) ? 1 : 0;
		$account->is_active = $is_active;
		
		$_accounts = $this->members_model->get_member_accounts_downline_by_level($account->node_address);
		
		$member_ids = array();
		$account_ids = array();
		$downline = array();
	
		array_push($member_ids, $account->member_id);
		array_push($account_ids, $account->account_id);
		array_push($account_ids, $account->sponsor_id);
		array_push($account_ids, $account->upline_id);
		
		// 20131125=====
		// get all UPGRADE rf_modifiers
		$modifier = $this->cards_model->get_modifiers(array(
			'modifier_name' => "UPGRADE"
		));
		$modifier = $modifier[0];
		
		// get card modifiers
		$card_modifiers = $this->cards_model->get_card_modifiers(array(
			'modifier_id' => $modifier->modifier_id
		));
		// =============	
		// 20131125
		// ----------
		$main_account_upgrades = array();
		foreach ($card_modifiers as $cmod) {
			$card_type_details = $this->cards_model->get_card_type_by_id($cmod->card_type_id);
			
			//get count per card_modifier
			$where = "base_account_id = '{$account_id}' AND card_type_id = '{$cmod->card_type_id}' AND upgrade_type = '{$card_type_details->code}'";
				
			$upgrade_count = $this->members_model->get_member_account_upgrades_count($where);
			
			array_push($main_account_upgrades, $upgrade_count);		
		}

		//20131201
		// ---------- get card_type
		$account_card_type_details = $this->cards_model->get_card_type_by_id($account->account_type_id);
		if (empty($account_card_type_details)) {
			$account_type_code = 'ERHM';
		} else {
			$account_type_code = $account_card_type_details->code;
		}	

		$is_erhm = $this->settings->switch_to_erhm;
		
		$account_main = new ArrayClass(array(
			//'erhm_upgrade' => $main_account_upgrades[0],
			'up1_upgrade' => $main_account_upgrades[0],
			'p2p_upgrade' => $main_account_upgrades[1],
			'account_type_code' => $account_type_code
		));
			
		foreach ($_accounts as $item)
		{
			array_push($member_ids, $item->member_id);
			array_push($account_ids, $item->account_id);
			array_push($account_ids, $item->sponsor_id);
			array_push($account_ids, $item->upline_id);
			$is_active = ($item->account_status_id == 1 || $item->account_status_id == 3) ? 1 : 0;
			
			$upgrade_counts = array();
			
			// 20131125
			// ----------
			foreach ($card_modifiers as $cmod) {
				$card_type_details = $this->cards_model->get_card_type_by_id($cmod->card_type_id);
				
				//get count per card_modifier
				$where = "base_account_id = '{$item->account_id}' AND card_type_id = '{$cmod->card_type_id}' AND upgrade_type = '{$card_type_details->code}'";
					
				$upgrade_count = $this->members_model->get_member_account_upgrades_count($where);
				
				array_push($upgrade_counts, $upgrade_count);
				
			}	
			
			//20131201
			// ---------- get card_type
			$account_card_type_details = $this->cards_model->get_card_type_by_id($item->account_type_id);
			if (empty($account_card_type_details)) {
				$account_type_code = 'ERHM';
			} else {
				$account_type_code = $account_card_type_details->code;
			}
			
			$downline[$item->account_id] = new ArrayClass(array(
				'account_id' => $item->account_id,
				'member_id' => $item->member_id,
				'sponsor_id' => $item->sponsor_id,
				'upline_id' => $item->upline_id,
				'account_type_id' => $item->account_type_id,
				'account_type_code' => $account_type_code,
				'account_status_id' => $item->account_status_id,
				'uni_node' => $item->uni_node,
				'node_address' => $item->trimmed_node_address,
				'ms_monthly_maintenance_ctr' => $item->ms_monthly_maintenance_ctr,
				'ms_annual_maintenance_ctr' => $item->ms_annual_maintenance_ctr,
				'monthly_maintenance_ctr' => $item->monthly_maintenance_ctr,
				'annual_maintenance_ctr' => $item->annual_maintenance_ctr,
				'is_active' => $is_active,
				'insert_timestamp' => $item->insert_timestamp,
				//'erhm_upgrade' => $upgrade_counts[0],
				'up1_upgrade' => $upgrade_counts[0],
				'p2p_upgrade' => $upgrade_counts[1],
				
			));
		}
		
		$account_ids = __::uniq($account_ids);
		
		$_ref_accounts = $this->members_model->get_member_accounts_by_account_ids($account_ids, null);
		$accounts = array();
		
		foreach ($_ref_accounts as $item)
		{
		
			// 20131125
			// ----------
			$upgrade_counts = array();
			$p2p_products = array();
			foreach ($card_modifiers as $cmod) {
				$card_type_details = $this->cards_model->get_card_type_by_id($cmod->card_type_id);

				//get count per card_modifier
				$where = "base_account_id = '{$item->account_id}' AND card_type_id = '{$cmod->card_type_id}' AND upgrade_type = '{$card_type_details->code}'";
				
				$upgrade_count = $this->members_model->get_member_account_upgrades($where);

				if(sizeof($upgrade_count)>0)
				{
					if($upgrade_count[0]->upgrade_type == 'P2P')
					{	
						$upgrade_products = $this->cards_model->get_card_product_selections(array('card_id'=>$upgrade_count[0]->upgrade_account_id));
						foreach($upgrade_products as $upgrade_product)
						{
							$product = $this->items_model->get_product_by_id($upgrade_product->product_id);
							$p2p_products[] = array('product_name'=>$product->product_name, 'qty'=>$upgrade_product->qty);
						}
					}
				}
				array_push($upgrade_counts, $upgrade_count);
	
			}	
			// ---------- get card_type
			$account_card_type_details = $this->cards_model->get_card_type_by_id($item->account_type_id);
			if (empty($account_card_type_details)) {
				$account_type_code = 'ERHM';
			} else {
				$account_type_code = $account_card_type_details->code;
			}
			
			$member_details = $this->members_model->get_member_by_id($item->member_id);
			$image_filename = "male.jpg";
			if (empty($member_details->image_filename) || ($member_details->image_filename == NULL) || (trim($member_details->image_filename) == "")) {
				// check gender of member
				if (trim($member_details->sex) == "F") {
					$image_filename = "female.jpg";
				} else {
					$image_filename = "male.jpg";
				}
			} else {
				$image_filename = $member_details->image_filename;
			}
			$image_filename = "<img src='".image_url('/assets/media/members/'.$image_filename)."' style='max-width: 40px; padding:3px 5px 5px 0px; float:left;' />";
			
			array_push($member_ids, $item->member_id);
			$is_active = ($item->account_status_id == 1 || $item->account_status_id == 3) ? 1 : 0;
			$accounts[$item->account_id] = new ArrayClass(array(
				'account_id' => $item->account_id,
				'member_id' => $item->member_id,
				'sponsor_id' => $item->sponsor_id,
				'upline_id' => $item->upline_id,
				'account_type_id' => $item->account_type_id,
				'account_type_code' => $account_type_code,
				'account_status_id' => $item->account_status_id,
				'uni_node' => $item->uni_node,
				'node_address' => $item->node_address,
				'ms_monthly_maintenance_ctr' => $item->ms_monthly_maintenance_ctr,
				'ms_annual_maintenance_ctr' => $item->ms_annual_maintenance_ctr,
				'monthly_maintenance_ctr' => $item->monthly_maintenance_ctr,
				'annual_maintenance_ctr' => $item->annual_maintenance_ctr,
				'is_active' => $is_active,
				'insert_timestamp' => $item->insert_timestamp,
				//'erhm_upgrade' => $upgrade_counts[0],
				'up1_upgrade' => $upgrade_counts[0],
				'p2p_upgrade' => $upgrade_counts[1],
				'p2p_products' => $p2p_products,
				'image_filename' =>$image_filename
			));
			
		}
		
		$member_ids = __::uniq($member_ids);
		
		$_members = $this->members_model->get_member_by_member_ids($member_ids, null);

		$members = array();
		foreach ($_members as $item)
		{
			$members[$item->member_id] = new ArrayClass(array(
				'member_id' => $item->member_id,
				'last_name' => $item->last_name,
				'first_name' => $item->first_name,
				'middle_name' => $item->middle_name,
				'email' => $item->email,
				'secondary_email' => $item->secondary_email,
				'image_filename' => $item->image_filename,
				'sex' => $item->sex,
				'birthdate' => $item->birthdate,
			));
		}
		
		$account->network = $downline;
		
		$data = array(
			'account' => $account,
			'account_upgrades' => $account_main,
			'members' => $members,
			'accounts' => $accounts,
			'is_erhm_mode' => $is_erhm
		);
		
		$this->return_json("ok","Genealogy.", $data);
		return;
	}
	
	public function get_unilevel()
	{
		$account_id = $this->input->get_post('account_id');
		
		$member_account = $this->members_model->get_member_account_by_account_id($account_id, "account_id, member_id, sponsor_id, upline_id, account_type_id, account_status_id, uni_node");
		
		if (empty($member_account))
		{
			$this->return_json("error","Invalid Account ID.");
			return;
		}
		
		
		$is_active = ($member_account->account_status_id == 1 || $member_account->account_status_id == 3) ? 1 : 0;
		$member_account->is_active = $is_active;
		
		// get all unilevel downlines
		$where = "uni_node like '{$member_account->uni_node}.%'";
		
		$member_accounts_count = $this->members_model->get_member_accounts_count($where);

		// set pagination data
		$config = array(
		    'pagination_url' => '/members/genealogy/get_unilevel',
		    'total_items' => $member_accounts_count,
		    'per_page' => 20,
		    'uri_segment' => 4,
		);
		$this->pager->set_config($config);

        $limit = array('rows' => $this->pager->per_page, 'offset' => $this->pager->offset);

		$fields = "account_id,member_id,sponsor_id,upline_id,account_type_id,account_status_id,uni_node,insert_timestamp";
		$order = "uni_node ASC";
		$member_accounts = $this->members_model->get_member_accounts($where,$limit,$order,$fields);

		$member_ids = array();
		$account_ids = array();
		$sponsor_ids = array();

		foreach ($member_accounts as $ma)
		{
			$account_ids[] = "'{$ma->account_id}'";			
			$sponsor_ids[] = "'{$ma->sponsor_id}'";			
			$member_ids[] = $ma->member_id;
		}

		$_sponsor_ids = implode(',',$sponsor_ids);		
		$where = "account_id IN ({$_sponsor_ids})";
		$limit = NULL;
		$order = NULL;
		$fields = "account_id,member_id,sponsor_id,upline_id,account_type_id,account_status_id,uni_node,insert_timestamp";
		$sponsor_accounts = $this->members_model->get_member_accounts($where,$limit,$order,$fields);		
		
		foreach ($sponsor_accounts as $sa)
		{
			$member_ids[] = $ma->member_id;			
		}
		
		$member_ids = array_unique($member_ids);
		
		$_member_ids = implode(",",$member_ids);
		
		$where = "member_id IN ({$_member_ids})";
		$limit = NULL;
		$order = NULL;
		$fields = "member_id,last_name,first_name,middle_name";
		$members = $this->members_model->get_members($where,$limit,$order,$fields);

		$member_details = array();
		
		foreach ($members as $m)
		{
			$member_details[$m->member_id] = new ArrayClass(array("name"=>$m->last_name . ", " . $m->first_name . " " . $m->middle_name));
		}

        $data_var = array(
            "member_accounts" => $member_accounts,
            "member_details" => $member_details
        );

        $html = $this->load->view('members/genealogy/unilevel', $data_var, TRUE);

        $pagination = $this->pager->create_links(); 

        $data['html'] = $html;
        $data['status'] = 'ok';
        $data['pagination'] = $pagination;

		$this->return_json("ok","Unilevel Genealogy.", $data);
		return;
	}
	
	public function search()
	{
		$root_account_id = $this->input->get_post('root_account_id');
		$search_key = trim($this->input->get_post('search_key'));
		
		if (empty($search_key)) 
		{
			$this->return_json("error","Search key is empty.");
			return;
		}
		
		if (strlen($search_key) < 4)
		{
			$this->return_json("error","Search key should have minimum of 4 characters.");
			return;
		}
		
		// get root account
		$root_account = $this->members_model->get_member_account_by_account_id($root_account_id);
		
		if (empty($root_account)) 
		{
			$this->return_json("error","Invalid root account.");
			return;
		}
		
		
		// check if its a valid account id
		$where = "node_address like '{$root_account->node_address}%' AND LENGTH(node_address) > LENGTH('{$root_account->node_address}') AND account_id = '{$search_key}'";
		$tmp_members = $this->members_model->get_member_accounts($where, array('offset' => 0, 'rows' => 50));

		if (count($tmp_members) > 0)
		{
			$member_account = $tmp_members[0];
			
			// found and its an account id
			$is_account_active = ($member_account->account_status_id == 1 || $member_account->account_status_id == 3) ? 1 : 0; 			 
			
			$member = $this->members_model->get_member_by_id($member_account->member_id);
			$member_type_results = $this->members_model->get_member_type_by_member_id($member_id);
			if(!empty($member_type_results))
			{
				$member_type = $this->members_model->get_rf_member_type_by_member_type_id($member_type_results->member_type_id);
				$member_type = $member_type->member_type;
			}
			else
				$member_type = "Member";
			$member_type = $this->members_model->get_rf_member_type_by_member_type_id($member_type_results->member_type_id);
			$data = array();
			$data['acct_'.$member_account->account_id] = array(
				"member_id" => $member_account->member_id,
				"account_id" => $member_account->account_id,
				"level_away" => abs(strlen($member_account->node_address) - strlen($root_account->node_address)),
				"first_name" => strtoupper($member->first_name),
				"last_name" => strtoupper($member->last_name),
				"middle_name" => strtoupper($member->middle_name),
				"fullname" => strtoupper($member->first_name.' '.substr($member->middle_name,0,1).'. '.$member->last_name),
				"is_account_active" => $is_account_active,
				"member_type" => strtoupper($member_type),
			);
			
			$this->return_json("ok","Ok.", $data);
			return;
		} else {
			
			// check if its a string name or part of a name
			$escaped_search_key = $this->members_model->escape('%'.$search_key.'%');
			$where = "first_name like {$escaped_search_key} OR last_name like {$escaped_search_key} ";
			$tmp_members = $this->members_model->get_members($where, null, "last_name ASC, first_name ASC");
			
			if (count($tmp_members) == 0)
			{
				// if these is reached then nothing are found.
				$this->return_json("error","Not found.");
				return;
			}
			
			$members = array();
			$member_ids = array();
			foreach ($tmp_members as $mem)
			{
				array_push($member_ids, $mem->member_id);
				$members[$mem->member_id] = $mem;
			}
			
			// get accounts based on member ids
			$where = "node_address like '{$root_account->node_address}%' AND LENGTH(node_address) > LENGTH('{$root_account->node_address}') AND member_id in ('".implode("', '",$member_ids)."')";
			$member_accounts = $this->members_model->get_member_accounts($where,array('offset' => 0, 'rows' => 50), "(LENGTH(node_address) - LENGTH('{$root_account->node_address}')) ASC");
			
			if (!empty($member_accounts))
			{
				if (count($member_accounts) > 0)
				{
					$data = array();
					foreach ($member_accounts as $mem_act)
					{
						$member_type_results = $this->members_model->get_member_type_by_member_id($mem_act->member_id);
						if(!empty($member_type_results))
						{
							$member_type = $this->members_model->get_rf_member_type_by_member_type_id($member_type_results->member_type_id);
							$member_type = $member_type->member_type;
						}
						else
							$member_type = "Member";
					
						$is_account_active = ($mem_act->account_status_id == 1 || $mem_act->account_status_id == 3) ? 1 : 0;
						
						$data['acct_'.$mem_act->account_id] = array(
							"member_id" => $mem_act->member_id,
							"account_id" => $mem_act->account_id,
							"level_away" => abs(strlen($mem_act->node_address) - strlen($root_account->node_address)),
							"first_name" => strtoupper($members[$mem_act->member_id]->first_name),
							"last_name" => strtoupper($members[$mem_act->member_id]->last_name),
							"middle_name" => strtoupper($members[$mem_act->member_id]->middle_name),
							"fullname" => strtoupper($members[$mem_act->member_id]->first_name.' '.substr($members[$mem_act->member_id]->middle_name,0,1).'. '.$members[$mem_act->member_id]->last_name). "A",
							"is_account_active" => $is_account_active,
							'member_type' => strtoupper($member_type),
						);
					}
					
					$this->return_json("ok","Ok.", $data);
					return;
				}
			}
				
			// if these is reached then nothing are found.
			$this->return_json("error","Not found.");
			return;
		}
		
	}
	
	function downline()
	{
		$account_id = $this->input->get_post('account_id');
		$page = abs($this->input->get_post('page'));
		if (empty($page)) $page = 1;
		if ($page < 1) $page = 1;
 		
		if (empty($account_id)) 
		{
			$this->return_json("error","Invalid request.");
			return;
		}
		
		$account = $this->members_model->get_member_account_by_account_id($account_id, "account_id, member_id, sponsor_id, upline_id, account_type_id, account_status_id, node_address, uni_node, insert_timestamp");
		
		if (empty($account))
		{
			$this->return_json("error","Invalid Account ID.");
			return;
		}
		
		$where = "node_address like '{$account->node_address}%'";
		$total_accounts = $this->members_model->get_member_accounts_count($where);
		
		// set pagination data
		$config = array(
		    'pagination_url' => '/members/genealogy/downline',
		    'total_items' => $total_accounts,
		    'per_page' => 20,
		    'uri_segment' => 4,
		);
		$this->pager->set_config($config);

        $limit = array('rows' => $this->pager->per_page, 'offset' => $this->pager->offset);
		
		$fields = "account_id, member_id, sponsor_id, upline_id, account_type_id, account_status_id, node_address, uni_node, sp_downline_count_left, sp_downline_count_right, insert_timestamp";
		$order = "node_address ASC";
		$_accounts = $this->members_model->get_member_accounts($where, $limit, $order, $fields);
		
		$member_ids = array();
		$account_ids = array();
		$accounts = array();
		
		foreach ($_accounts as $item)
		{
			$card_type = $this->cards_model->get_card_types(array(
				'card_type_id' => $item->account_type_id
			));
			$card_type = $card_type[0];

			$card_html = '<label class="label" style="background-color:'.$card_type->color.';">'.$card_type->code.'</label><br />';

			$card_upgrades = $this->members_model->get_member_account_upgrades(array('base_account_id'=>$item->account_id));
			if(sizeof($card_upgrades) > 0)
			{	
				foreach($card_upgrades as $card_upgrade)
				{
					$card_type = $this->cards_model->get_card_types(array(
						'card_type_id' => $card_upgrade->card_type_id
					));
					$card_type = $card_type[0];

					$card_html .= '<label class="label" style="background-color:'.$card_type->color.'">'.$card_type->code.'</label><br />';
				}
			}

			array_push($member_ids, $item->member_id);
			$is_active = ($item->account_status_id == 1 || $item->account_status_id == 3) ? 1 : 0;
			array_push($accounts, array(
				'account_id' => $item->account_id,
				'member_id' => $item->member_id,
				'sponsor_id' => $item->sponsor_id,
				'upline_id' => $item->upline_id,
				'account_type_id' => $item->account_type_id,
				'account_type_code' => $card_html,
				'account_status_id' => $item->account_status_id,
				'uni_node' => $item->uni_node,
				'side' => substr($item->node_address, -1),
				'is_active' => $is_active,
				'count_left' => $item->sp_downline_count_left,
				'count_right' => $item->sp_downline_count_right,
				'insert_timestamp' => $item->insert_timestamp,
			));
		}
		
		$member_ids = __::uniq($member_ids);
		
		$_members = $this->members_model->get_member_by_member_ids($member_ids, null);

		$members = array();
		foreach ($_members as $item)
		{
			$members[$item->member_id] = new ArrayClass(array(
				'member_id' => $item->member_id,
				'last_name' => $item->last_name,
				'first_name' => $item->first_name,
				'middle_name' => $item->middle_name,
				'email' => $item->email,
				'secondary_email' => $item->secondary_email,
				'image_filename' => $item->image_filename,
				'sex' => $item->sex,
				'birthdate' => $item->birthdate,
			));
		}
		
		$data = array(
			'accounts' => $accounts,
			'members' => $members,
			'pager' => sanitize_html($this->pager->create_links())
		);
		
		$this->return_json("ok","Account downlines.", $data);
		return;
		
	}
	
	function downline_count()
	{
		$account_ids = $this->input->get_post('account_ids');
		
		$_accounts = $this->members_model->get_member_accounts_by_account_ids($account_ids, null);
		$accounts = array();
		foreach ($_accounts as $item)
		{
		
			$left_value = number_format($this->members_model->get_downline_count_by_node_address($item->node_address, 'l') + $this->members_model->get_downline_count_by_node_address($item->node_address, 'l', false, true, false, true), 1);
			
			if (strpos($left_value, ".0")) {
				// whole number
				$left_value = substr($left_value, 0, -2);
			}
			
			$right_value =  number_format($this->members_model->get_downline_count_by_node_address($item->node_address, 'r') + $this->members_model->get_downline_count_by_node_address($item->node_address, 'r', false, true, false, true), 1);
			
			if (strpos($right_value, ".0")) {
				// whole number
				$right_value = substr($right_value, 0, -2);
			}
		
			$accounts[$item->account_id] = array(
				//'left' => $this->members_model->get_downline_count_by_node_address($item->node_address, 'l'),
				//'right' =>$this->members_model->get_downline_count_by_node_address($item->node_address, 'r')
				'left' =>  $left_value,
				'right' => $right_value
			
			);
		}
		
		$this->return_json("ok","Account downlines.", $accounts);
		return;
	}

	function downline_count_expanded()
	{
		$account_ids = $this->input->get_post('account_ids');
		
		$_accounts = $this->members_model->get_member_accounts_by_account_ids($account_ids, null);
		$accounts = array();
		foreach ($_accounts as $item)
		{
			$left_value = $this->members_model->get_downline_count_expanded_by_node_address($item->node_address,'l');
			$right_value = $this->members_model->get_downline_count_expanded_by_node_address($item->node_address,'r');

			$accounts[$item->account_id] = array(
				//'left' => $this->members_model->get_downline_count_by_node_address($item->node_address, 'l'),
				//'right' =>$this->members_model->get_downline_count_by_node_address($item->node_address, 'r')
				'left' =>  $left_value,
				'right' => $right_value
			
			);
		}
		
		$this->return_json("ok","Account downlines.", $accounts);
		return;
	}

}