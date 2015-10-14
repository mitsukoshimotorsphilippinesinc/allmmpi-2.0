<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Genealogy extends Base_Controller {

	public function __construct()
	{
		parent::__construct();
		// load contents model
		$this->load->model("members_model");
		// load pager library
		$this->load->library('pager');
	}
	
	public function index()
	{
		
		//echo $this->load->view('genealogy',array(),true);
		$this->template->view('genealogy');		
	}
	
	public function get_network()
	{
		$account_id = $this->input->get_post('account_id');

		$account = $this->members_model->get_member_account_by_account_id($account_id, "account_id, member_id, sponsor_id, upline_id, account_type_id, account_status_id, node_address, uni_node, ms_monthly_maintenance_ctr, ms_annual_maintenance_ctr, insert_timestamp");

		if (empty($account))
		{
			$this->return_json("error","Invalid Account ID.");
			return;
		}
		
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
		
		foreach ($_accounts as $item)
		{
			array_push($member_ids, $item->member_id);
			array_push($account_ids, $item->account_id);
			array_push($account_ids, $item->sponsor_id);
			array_push($account_ids, $item->upline_id);
			$is_active = ($item->account_status_id == 1 || $item->account_status_id == 3) ? 1 : 0;
			$downline[$item->account_id] = new ArrayClass(array(
				'account_id' => $item->account_id,
				'member_id' => $item->member_id,
				'sponsor_id' => $item->sponsor_id,
				'upline_id' => $item->upline_id,
				'account_type_id' => $item->account_type_id,
				'account_status_id' => $item->account_status_id,
				'uni_node' => $item->uni_node,
				'node_address' => $item->trimmed_node_address,
				'ms_monthly_maintenance_ctr' => $item->ms_monthly_maintenance_ctr,
				'ms_annual_maintenance_ctr' => $item->ms_annual_maintenance_ctr,
				'is_active' => $is_active,
				'insert_timestamp' => $item->insert_timestamp,
			));
		}
		
		$account_ids = __::uniq($account_ids);
		
		$_ref_accounts = $this->members_model->get_member_accounts_by_account_ids($account_ids, null);
		$accounts = array();
		foreach ($_ref_accounts as $item)
		{
			array_push($member_ids, $item->member_id);
			$is_active = ($item->account_status_id == 1 || $item->account_status_id == 3) ? 1 : 0;
			$accounts[$item->account_id] = new ArrayClass(array(
				'account_id' => $item->account_id,
				'member_id' => $item->member_id,
				'sponsor_id' => $item->sponsor_id,
				'upline_id' => $item->upline_id,
				'account_type_id' => $item->account_type_id,
				'account_status_id' => $item->account_status_id,
				'uni_node' => $item->uni_node,
				'node_address' => $item->node_address,
				'ms_monthly_maintenance_ctr' => $item->ms_monthly_maintenance_ctr,
				'ms_annual_maintenance_ctr' => $item->ms_annual_maintenance_ctr,
				'is_active' => $is_active,
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
		
		$account->network = $downline;
		
		$data = array(
			'account' => $account,
			'members' => $members,
			'accounts' => $accounts
		);
		
		$this->return_json("ok","Genealogy.", $data);
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
			$data = array();
			$data['acct_'.$member_account->account_id] = array(
				"member_id" => $member_account->member_id,
				"account_id" => $member_account->account_id,
				"level_away" => abs(strlen($member_account->node_address) - strlen($root_account->node_address)),
				"first_name" => strtoupper($member->first_name),
				"last_name" => strtoupper($member->last_name),
				"middle_name" => strtoupper($member->middle_name),
				"fullname" => strtoupper($member->first_name.' '.substr($member->middle_name,0,1).'. '.$member->last_name),
				"is_account_active" => $is_account_active
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
						$is_account_active = ($mem_act->account_status_id == 1 || $mem_act->account_status_id == 3) ? 1 : 0;
						
						$data['acct_'.$mem_act->account_id] = array(
							"member_id" => $mem_act->member_id,
							"account_id" => $mem_act->account_id,
							"level_away" => abs(strlen($mem_act->node_address) - strlen($root_account->node_address)),
							"first_name" => strtoupper($members[$mem_act->member_id]->first_name),
							"last_name" => strtoupper($members[$mem_act->member_id]->last_name),
							"middle_name" => strtoupper($members[$mem_act->member_id]->middle_name),
							"fullname" => strtoupper($members[$mem_act->member_id]->first_name.' '.substr($members[$mem_act->member_id]->middle_name,0,1).'. '.$members[$mem_act->member_id]->last_name),
							"is_account_active" => $is_account_active
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
		
		// select account_id, node_address from cm_member_accounts where node_address like '1111111122222222%' order by node_address;
		$where = "node_address like '{$account->node_address}%' AND account_type_id != 5 ";
		$total_accounts = $this->members_model->get_member_accounts_count($where);
		
		// set pagination data
		$config = array(
		    'pagination_url' => '/workbench/genealogy/downline',
		    'total_items' => $total_accounts,
		    'per_page' => 20,
		    'uri_segment' => 4,
		);
		$this->pager->set_config($config);

        $limit = array('rows' => $this->pager->per_page, 'offset' => $this->pager->offset);
		
		$fields = "account_id, member_id, sponsor_id, upline_id, account_type_id, account_status_id, node_address, uni_node, insert_timestamp";
		$order = "node_address ASC";
		$_accounts = $this->members_model->get_member_accounts($where, $limit, $order, $fields);
		
		$member_ids = array();
		$account_ids = array();
		$accounts = array();
		
		foreach ($_accounts as $item)
		{
			array_push($member_ids, $item->member_id);
			$is_active = ($item->account_status_id == 1 || $item->account_status_id == 3) ? 1 : 0;
			array_push($accounts, array(
				'account_id' => $item->account_id,
				'member_id' => $item->member_id,
				'sponsor_id' => $item->sponsor_id,
				'upline_id' => $item->upline_id,
				'account_type_id' => $item->account_type_id,
				'account_status_id' => $item->account_status_id,
				'uni_node' => $item->uni_node,
				'side' => substr($item->node_address, -1),
				'is_active' => $is_active,
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
			$accounts[$item->account_id] = array(
				'left' => $this->members_model->get_downline_count_by_node_address($item->node_address, 'l'),
				'right' =>$this->members_model->get_downline_count_by_node_address($item->node_address, 'r')
			);
		}
		
		$this->return_json("ok","Account downlines.", $accounts);
		return;
	}


}