<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Raffles_model extends Base_model {
	
	function __construct()
	{
		// call the base model constructor
		parent::__construct();

		// assign the table for this model
		$this->_TABLES = array(				
				'raffles' => 'rm_raffles',
				'raffle_process_points' => 'rm_raffle_process_points',
				'raffle_groups' => 'rm_raffle_groups',
				'raffle_entries' => 'rm_raffle_entries',
				'raffle_winners' => 'rm_raffle_winners',
		);
		
		// load models
		$this->load->model('members_model');
		$this->load->model('payment_model');
		$this->load->model('items_model');
		$this->load->model('cards_model');
	}
	
	/* -- RAFFLES -- */	
	function get_raffles($where = null, $limit = null, $orderby = null, $fields = null) 
	{
        $query = $this->fetch('raffles', $fields, $where, $orderby, $limit);
        $row = $query->result();
        $query->free_result();
        return $row;
    }
	
	 function get_raffles_count($where = null) 
	{
        // do a sql count instead of row count
        $query = $this->fetch('raffles', 'count(1) as cnt', $where);
        $row = $query->first_row();
        $query->free_result();
        return $row->cnt;
    }
	
	function get_raffle_by_id($raffle_id) 
	{
        $result = $this->get_raffles(array('raffle_id' => $raffle_id));
        $row = NULL;
        if (count($result) > 0) {
            $row = $result[0];
        }
        return $row;
    }

	function get_raffle_by_code($raffle_code) 
	{
        $result = $this->get_raffles(array('raffle_code' => $raffle_code));
        $row = NULL;
        if (count($result) > 0) {
            $row = $result[0];
        }
        return $row;
    }

	function get_raffle_by_ids($raffle_ids, $limit = null) {
		
		$this->db->select("*");
		$this->db->where_in('raffle_id', $raffle_ids);
		
		if (is_array($limit))
			if (isset($limit['offset']) && isset($limit['rows']))
				$this->db->limit($limit['rows'],$limit['offset']);
				
		$this->db->from($this->_TABLES['raffles']);
		$query = $this->db->get();
		$rows = $query->result();
		$query->free_result();
		
		return $rows;
    }

	function insert_raffles($data) 
    {
        return $this->insert('raffles', $data);
    }
	
	function update_raffles($data, $where) 
    {
        return $this->update('raffles', $data, $where);
    }

	function delete_raffles($where) 
    {
        return $this->delete('raffles', $where);
    }

	/* -- RAFFLE PROCESS POINTS -- */	
	function get_raffle_process_points($where = null, $limit = null, $orderby = null, $fields = null) 
	{
	    $query = $this->fetch('raffle_process_points', $fields, $where, $orderby, $limit);
	    $row = $query->result();
	    $query->free_result();
	    return $row;
	}

	 function get_raffle_process_points_count($where = null) 
	{
	    // do a sql count instead of row count
	    $query = $this->fetch('raffle_process_points', 'count(1) as cnt', $where);
	    $row = $query->first_row();
	    $query->free_result();
	    return $row->cnt;
	}

	function get_raffle_process_point_by_id($raffle_process_point_id) 
	{
	    $result = $this->get_raffle_process_points(array('raffle_process_point_id' => $raffle_process_point_id));
	    $row = NULL;
	    if (count($result) > 0) {
	        $row = $result[0];
	    }
	    return $row;
	}

	function get_raffle_process_points_by_raffle_id($raffle_id) 
	{
	    return $this->get_raffle_process_points(array('raffle_id' => $raffle_id));
	}
	
	function get_raffle_process_points_by_process_point($process_point) 
	{
	    return $this->get_raffle_process_points(array('process_point' => $process_point));
	}
	
	function get_raffle_process_points_by_process_point_and_type($process_point, $type = 'default') 
	{
		if (empty($type)) $type = 'default';
	    return $this->get_raffle_process_points(array('process_point' => $process_point, 'process_type' => $type));
	}

	function insert_raffle_process_points($data) 
	{
	    return $this->insert('raffle_process_points', $data);
	}

	function update_raffle_process_points($data, $where) 
	{
	    return $this->update('raffle_process_points', $data, $where);
	}

	function delete_raffle_process_points($where) 
	{
	    return $this->delete('raffle_process_points', $where);
	}
	
	/* -- RAFFLE GROUPS -- */	
	function get_raffle_groups($where = null, $limit = null, $orderby = null, $fields = null) 
	{
	    $query = $this->fetch('raffle_groups', $fields, $where, $orderby, $limit);
	    $row = $query->result();
	    $query->free_result();
	    return $row;
	}

	 function get_raffle_groups_count($where = null) 
	{
	    // do a sql count instead of row count
	    $query = $this->fetch('raffle_groups', 'count(1) as cnt', $where);
	    $row = $query->first_row();
	    $query->free_result();
	    return $row->cnt;
	}

	function get_raffle_group_by_id($raffle_group_id) 
	{
	    $result = $this->get_raffle_groups(array('raffle_group_id' => $raffle_group_id));
	    $row = NULL;
	    if (count($result) > 0) {
	        $row = $result[0];
	    }
	    return $row;
	}
	
	function get_raffle_groups_by_parent_id($parent_id) 
	{
	    return $this->get_raffle_groups(array('parent_group_id' => $parent_id));
	}

	function get_raffle_groups_by_raffle_id($raffle_id) 
	{
	    return $this->get_raffle_groups(array('raffle_id' => $raffle_id));
	}
	
	function get_raffle_groups_by_group($group) 
	{
	    return $this->get_raffle_groups(array('group' => $group));
	}
	
	function get_raffle_groups_by_parent_group($parent_group) 
	{
	    return $this->get_raffle_groups(array('parent_group' => $parent_group));
	}

	function insert_raffle_groups($data) 
	{
	    return $this->insert('raffle_groups', $data);
	}

	function update_raffle_groups($data, $where) 
	{
	    return $this->update('raffle_groups', $data, $where);
	}

	function delete_raffle_groups($where) 
	{
	    return $this->delete('raffle_groups', $where);
	}
	
	/* -- RAFFLE ENTRIES -- */	
	function get_raffle_entries($where = null, $limit = null, $orderby = null, $fields = null) 
	{
	    $query = $this->fetch('raffle_entries', $fields, $where, $orderby, $limit);
	    $row = $query->result();
	    $query->free_result();
	    return $row;
	}

	 function get_raffle_entries_count($where = null) 
	{
	    // do a sql count instead of row count
	    $query = $this->fetch('raffle_entries', 'count(1) as cnt', $where);
	    $row = $query->first_row();
	    $query->free_result();
	    return $row->cnt;
	}

	function get_raffle_entry_by_id($raffle_entry_id) 
	{
	    $result = $this->get_raffle_entries(array('raffle_entry_id' => $raffle_entry_id));
	    $row = NULL;
	    if (count($result) > 0) {
	        $row = $result[0];
	    }
	    return $row;
	}
	
	function get_raffle_entry_by_raffle_number($raffle_number) 
	{
	    $result = $this->get_raffle_entries(array('raffle_number' => $raffle_number));
	    $row = NULL;
	    if (count($result) > 0) {
	        $row = $result[0];
	    }
	    return $row;
	}
	
	function get_raffle_entries_by_member_id($member_id) 
	{
	    return $this->get_raffle_entries(array('member_id' => $member_id));
	}
	
	function get_raffle_entries_by_account_id($account_id) 
	{
	    return $this->get_raffle_entries(array('account_id' => $account_id));
	}

	function get_raffle_entries_by_group($group) 
	{
	    return $this->get_raffle_entries(array('group' => $group));
	}
	
	function get_raffle_entries_by_sub_group($sub_group) 
	{
	    return $this->get_raffle_entries(array('sub_group' => $sub_group));
	}
	
	function get_raffle_entries_by_groups($group, $sub_group = '') 
	{
		$where = array();
		if ($group != "") $where['group'] = $group;
		if ($sub_group != "") $where['sub_group'] = $sub_group;
		 
	    return $this->get_raffle_entries($where);
	}

	function insert_raffle_entries($data) 
	{
	    return $this->insert('raffle_entries', $data);
	}

	function update_raffle_entries($data, $where) 
	{
	    return $this->update('raffle_entries', $data, $where);
	}

	function delete_raffle_entries($where) 
	{
	    return $this->delete('raffle_entries', $where);
	}
	
	/* -- RAFFLE WINNERS -- */	
	function get_raffle_winners($where = null, $limit = null, $orderby = null, $fields = null) 
	{
	    $query = $this->fetch('raffle_winners', $fields, $where, $orderby, $limit);
	    $row = $query->result();
	    $query->free_result();
	    return $row;
	}

	 function get_raffle_winners_count($where = null) 
	{
	    // do a sql count instead of row count
	    $query = $this->fetch('raffle_winners', 'count(1) as cnt', $where);
	    $row = $query->first_row();
	    $query->free_result();
	    return $row->cnt;
	}

	function get_raffle_winners_by_raffle_id($raffle_id) 
	{
	    return $this->get_raffle_winners(array('raffle_id' => $raffle_id));
	}

	function get_raffle_winner_by_raffle_entry_id($raffle_entry_id) 
	{
	    $result = $this->get_raffle_winners(array('raffle_entry_id' => $raffle_entry_id));
	    $row = NULL;
	    if (count($result) > 0) {
	        $row = $result[0];
	    }
	    return $row;
	}
	
	function get_raffle_winner_by_raffle_number($raffle_number) 
	{
	    $result = $this->get_raffle_winners(array('raffle_number' => $raffle_number));
	    $row = NULL;
	    if (count($result) > 0) {
	        $row = $result[0];
	    }
	    return $row;
	}
	
	function get_raffle_winners_by_group($group) 
	{
	    return $this->get_raffle_winners(array('group' => $group));
	}
	
	function get_raffle_winners_by_sub_group($sub_group) 
	{
	    return $this->get_raffle_winners(array('sub_group' => $sub_group));
	}
	
	function get_raffle_winners_by_groups($group, $sub_group = '') 
	{
		$where = array();
		if ($group != "") $where['group'] = $group;
		if ($sub_group != "") $where['sub_group'] = $sub_group;
		
	    return $this->get_raffle_winners(array('sub_group' => $sub_group));
	}

	function insert_raffle_winners($data) 
	{
	    return $this->insert('raffle_winners', $data);
	}

	function update_raffle_winners($data, $where) 
	{
	    return $this->update('raffle_winners', $data, $where);
	}

	function delete_raffle_winners($where) 
	{
	    return $this->delete('raffle_winners', $where);
	}
	
	/******************************************************
	 * Raffle check process
	 */
	
	function raffle_process()
	{
		/* --------------------------------
		 NOTE: first argument should be process_point, second will be raffle id
		 */
		// get arguments/parameters
		$args = func_get_args();
		$num_args = count($args);
		
		if ($num_args > 0)
		{
			$point = $args[0];
			$type = $args[1];
			$new_args = array_slice($args, 2);
			// get process point
			$process_points = $this->get_raffle_process_points_by_process_point_and_type($point, $type);
			
			if (count($process_points) > 0)
			{
				$raffle_ids = array();
				foreach ($process_points as $item)
					array_push($raffle_ids, $item->raffle_id);

				$raffles = $this->get_raffle_by_ids($raffle_ids);
				foreach ($raffles as $item)
				{
					$func_name = '';
					$func_params = '';
					if ($type == 'pre')
					{
						$func_name = $item->pre_process_method;
						$func_params = $item->pre_process_params;
					}
					else if ($type == 'post')
					{
						$func_name = $item->post_process_method;
						$func_params = $item->post_process_params;
					}
					else if ($type == 'update')
					{
						$func_name = $item->update_process_method;
						$func_params = $item->update_process_params;
					}
					else if ($type == 'indiv')
					{
						$func_name = 'raffle_individual_products';
						$func_params = "";
					}
					else if ($type == 'default')
					{
						$func_name = $item->process_method;
						$func_params = $item->process_params;
					}
					
					if (!empty($func_name))
						if (method_exists($this, $func_name))
						{
							$this->{$func_name}(array_merge(array($item->raffle_id), $new_args), $func_params);
						}
				}
					
			}
			
			return true;
		}
		
		return false;
	}

	/******************************************************
	 * Raffle entry method
	 */

	/* --- ULTIMATE PACK PROMO ---*/
	/* --- SHARED WITH ULTIMATE PACK MONTHLY PROMO ---*/
	function entry_ultimate_pack_promo($args, $extra_params = '')
	{
		if (count($args) == 0) return false;
		if (count($args) < 3) return false;
		$raffle_id = $args[0];
		
		$raffle = $this->get_raffle_by_id($raffle_id);
		
		if (!empty($raffle))
		{
			$card_id = $args[1];
			// check if its prefixed 88 and 89
			if ((substr($card_id, 0, 2) == "88") || (substr($card_id, 0, 2) == "89"))
			{
				// get account info
				$account_id = $args[2];
				$account = $this->members_model->get_member_account_by_account_id($account_id);
				
				if (!empty($account))
				{
					$sp_card = $this->cards_model->get_sp_card_by_card_id($card_id);

					if (!empty($sp_card))
					{
						
						// get primary account
						$primary_account = $this->members_model->get_member_primary_account_by_timestamp($account->member_id);

						// get sponsor 
						$sponsor = $this->members_model->get_member_account_by_account_id($primary_account->sponsor_id);
						$sponsor_member_id = 0;
						$sponsor_account_id = 0;
						if (!empty($sponsor))
						{
							$sponsor_member_id = $sponsor->member_id;
							$sponsor_account_id = $sponsor->account_id;
						}

						// get upline
						$upline = $this->members_model->get_member_account_by_account_id($primary_account->upline_id);
						$upline_member_id = 0;
						$upline_account_id = 0;
						if (!empty($upline))
						{
							$upline_member_id = $upline->member_id;
							$upline_account_id = $upline->account_id;
						}
						
						// insert an entry
						$data = array(
							'raffle_id' => $raffle->raffle_id,
							'raffle_number' => $card_id,
							'is_active' => 1,
							'value' => $account->account_id,
							'member_id' => $primary_account->member_id,
							'account_id' => $primary_account->account_id,
							'sponsor_member_id' => $sponsor_member_id,
							'sponsor_account_id' => $sponsor_account_id,
							'upline_member_id' => $upline_member_id,
							'upline_account_id' => $upline_account_id
						);
						
						$this->insert_raffle_entries($data);
						return true;
					}
				}
			}
		}
		return false;
	}
	
	/* --- REPEAT SALES PROMO ---*/
	function entry_repeat_sales_promo($args, $extra_params = '')
	{
		if (count($args) == 0) return false;
		$raffle_id = $args[0];
		
		$raffle = $this->get_raffle_by_id($raffle_id);
		
		if (!empty($raffle))
		{
			$raffle_number = $args[1];
			
			$raffle_entry = $this->get_raffle_entry_by_raffle_number($raffle_number);
			
			if (!empty($raffle_entry))
				if ($raffle_entry->is_active == 0)
				{
					// get account info
					$account_id = $args[2];
					$account = $this->members_model->get_member_account_by_account_id($account_id);

					if (!empty($account))
					{
						// get primary account
						$primary_account = $this->members_model->get_member_primary_account_by_timestamp($account->member_id);

						// get sponsor 
						$sponsor = $this->members_model->get_member_account_by_account_id($primary_account->sponsor_id);
						$sponsor_member_id = 0;
						$sponsor_account_id = 0;
						if (!empty($sponsor))
						{
							$sponsor_member_id = $sponsor->member_id;
							$sponsor_account_id = $sponsor->account_id;
						}

						// get upline
						$upline = $this->members_model->get_member_account_by_account_id($primary_account->upline_id);
						$upline_member_id = 0;
						$upline_account_id = 0;
						if (!empty($upline))
						{
							$upline_member_id = $upline->member_id;
							$upline_account_id = $upline->account_id;
						}

						// insert an entry
						$data = array(
							'is_active' => 1,
							'value' => $account->account_id,
							'member_id' => $primary_account->member_id,
							'account_id' => $primary_account->account_id,
							'sponsor_member_id' => $sponsor_member_id,
							'sponsor_account_id' => $sponsor_account_id,
							'upline_member_id' => $upline_member_id,
							'upline_account_id' => $upline_account_id
						);

						$this->update_raffle_entries($data, array('raffle_number' => $raffle_number));
						return true;
					}
				}
			
		}
		return false;
	}
	
	function _is_account_in_group_account($group_account_id, $account_id)
	{
		$group_account = $this->members_model->get_member_account_by_account_id($group_account_id);
		
		if (!empty($group_account))
		{
			$node_address = $group_account->node_address;

			$where = "SUBSTRING(node_address,1, LENGTH('{$node_address}'))  = '{$node_address}' and account_id = '{$account_id}'";
			$this->db->select("COUNT(1) as cnt");
			$this->db->where($where);
			$this->db->order_by("LENGTH(node_address) ASC");

			$this->db->from('cm_member_accounts');
			$query = $this->db->get();
			$row = $query->first_row();
	        $query->free_result();
	        return new ArrayClass(array('node_address' => $node_address, 'match' => $row->cnt > 0));
		}
		
		return false;
	}
	
	/* --- BATTLE OF THE GRANDMASTER PROMO ---*/
	function entry_grandmaster_promo($args, $extra_params = '')
	{
		if (count($args) == 0) return false;
		$raffle_id = $args[0];
		
		$raffle = $this->get_raffle_by_id($raffle_id);
		
		if (!empty($raffle))
		{
			$card_id = $args[1];
			// check if its prefixed 88 and 89
			if ((substr($card_id, 0, 2) == "88") || (substr($card_id, 0, 2) == "89"))
			{
				// get account info
				$account_id = $args[2];
				$account = $this->members_model->get_member_account_by_account_id($account_id);
				
				if (!empty($account))
				{
					$sp_card = $this->cards_model->get_sp_card_by_card_id($card_id);

					if (!empty($sp_card))
					{
						
						// get which group
						// group information are account originators 
						$_groups = $this->get_raffle_groups_by_raffle_id($raffle->raffle_id);
						$groups = array();
						$found_groups = array();
						foreach ($_groups as $item)
						{
							$groups[$item->raffle_id.'_'.$item->group] = __::clon($item);
							$is_found = $this->_is_account_in_group_account($item->group, $account_id);
							if ($is_found !== false)
							{
								$groups[$item->raffle_id.'_'.$item->group]->node_address = $is_found->node_address;
								if ($is_found->match) array_push($found_groups, $item->raffle_id.'_'.$item->group);
							}
						}
						
						$selected_group = false;
						if (count($found_groups) > 0)
						{
							$len = 0;
							foreach ($found_groups as $group_idx)
							{
								$_len = strlen($groups[$group_idx]->node_address);
								if ($_len > $len) $selected_group = __::clon($groups[$group_idx]);
								$len = $_len;
							}
						}
						
						if ($selected_group !== false)
						{
							// get primary account
							$primary_account = $this->members_model->get_member_primary_account_by_timestamp($account->member_id);

							// get sponsor 
							$sponsor = $this->members_model->get_member_account_by_account_id($primary_account->sponsor_id);
							$sponsor_member_id = 0;
							$sponsor_account_id = 0;
							if (!empty($sponsor))
							{
								$sponsor_member_id = $sponsor->member_id;
								$sponsor_account_id = $sponsor->account_id;
							}

							// get upline
							$upline = $this->members_model->get_member_account_by_account_id($primary_account->upline_id);
							$upline_member_id = 0;
							$upline_account_id = 0;
							if (!empty($upline))
							{
								$upline_member_id = $upline->member_id;
								$upline_account_id = $upline->account_id;
							}
							
							
							$entry_group = $selected_group->group;
							$entry_sub_group = '';
							
							if (!empty($selected_group->parent_group))
							{
								$entry_group = $selected_group->parent_group;
								$entry_sub_group = $selected_group->group;
							}
							
							// insert an entry
							$data = array(
								'raffle_id' => $raffle->raffle_id,
								'raffle_number' => $card_id,
								'is_active' => 1,
								'value' => $account->account_id,
								'member_id' => $primary_account->member_id,
								'account_id' => $primary_account->account_id,
								'sponsor_member_id' => $sponsor_member_id,
								'sponsor_account_id' => $sponsor_account_id,
								'upline_member_id' => $upline_member_id,
								'upline_account_id' => $upline_account_id,
								'group' => $entry_group,
								'sub_group' => $entry_sub_group
							);

							$this->insert_raffle_entries($data);
							return true;
						}
					}
				}
			}
		}
		
		return false;
	}
	
	/******************************************************
	 * PRE - PROCESS METHODS
	 */
	
	function pre_repeat_sales_promo($args, $extra_params = '')
	{
		/* -------------------------------------
		 NOTE: every 2000 worth of RS (repeat sales) is equivalent to 1 raffle entry
			- Will generate an raffle number and will have an inserted record to rm_raffle_entreis as inactive
			- Vital-C (product_id : 48, item_id: 197, product_code : FP000197, item_code : FP000197) product will not be included
			- VITAL C POWER UP WITH IPOH WHITE COFFFEE SACHET (product_id : 264, item_id: 344, product_code: FP000344) also not included
			- All packages are not included
			- TO-DO: module to allow user to set blacklisted products/products included in 2000 raffle
		 */
		if (count($args) == 0) return false;
		if (count($args) < 3) return false;
		$raffle_id = $args[0];
		$payment_trans_id = $args[1];
		$ar_number = $args[2];
		
		$amount_per_entry = 2000;
		$total_rs_sales = 0;
		$total_entries = 0;
		$blacklist_products = array(48, 264);
		$raffle = $this->get_raffle_by_id($raffle_id);

		if (!empty($raffle))
		{
			$trans = $this->payment_model->get_payment_transaction_by_id($payment_trans_id);
			if (!empty($trans))
			{
				$trans_products = $this->payment_model->get_payment_transaction_products(array(
																							'transaction_id' => $trans->transaction_id, 
																							'package_product_id' => 0,
																							'is_product_rebate' => 0,
																							'voucher_type_id' => 0));
				$prod_ids = array();
				$prods = array();
				foreach ($trans_products as $prod_item)
				{
					array_push($prod_ids, $prod_item->product_id);
					$prods[$prod_item->product_id] = __::clon($prod_item);
				}
				
				if(empty($prod_ids)) return;	
				
				$products = $this->items_model->get_product_by_ids($prod_ids);
				
				foreach ($products as $item)
				{
					if ($item->product_type_id == 1 && $item->item_type_id == 2) // ITEM AND FINISHED PRODUCTS
					{
						if (!in_array($item->product_id, $blacklist_products))
						{
							$_amount = 0;
							if ($trans->rate_to_use == 1) // retail
								$_amount = floatVal($item->standard_retail_price);
							else if ($trans->rate_to_use == 2) // member
								$_amount = floatVal($item->member_price);
							else if ($trans->rate_to_use == 3) // employee
								$_amount = floatVal($item->employee_price);
							
							$total_rs_sales += ($_amount * intVal($prods[$item->product_id]->quantity));
						}
					}
				}

				// start processing
				if ($total_rs_sales > 0)
				{
					$total_entries = floor($total_rs_sales / $amount_per_entry);
					
					for ($idx = 0; $idx < $total_entries; $idx++)
					{
						
						$data = array(
							'raffle_id' => $raffle->raffle_id,
							'raffle_number' => '',
							'reference_info' => $trans->transaction_id,
							'value' => $ar_number,
							'is_active' => 0,
						);
						$this->insert_raffle_entries($data);
						
						$raffle_entry_id = $this->insert_id();
						
						$_key = $raffle->raffle_id.".".$raffle->raffle_code.".".$trans->transaction_id."-".$raffle_entry_id.".".$idx."-".time();
						$_salt = sha1(md5($_key).'-'.time());
						$_hash = strtoupper(md5($_key."-".$raffle_entry_id."-".$idx.$_salt.time()));
						$_hash = substr($_hash, 0, 8);
						
						$this->update_raffle_entries(array('raffle_number' => $_hash), array('raffle_entry_id' => $raffle_entry_id));
					}
					
				}
				
			}
		}
		
	}
	
	function raffle_individual_products($args, $extra_params = ''){
		if (count($args) == 0) return false;
		if (count($args) < 3) return false;
		$raffle_id = $args[0];
		$payment_trans_id = $args[1];
		$ar_number = $args[2];
		
		$raffle = $this->get_raffle_by_id($raffle_id);

		if (!empty($raffle))
		{
			$trans = $this->payment_model->get_payment_transaction_by_id($payment_trans_id);
			if (!empty($trans))
			{
				$trans_products = $this->payment_model->get_payment_transaction_products(array('transaction_id' => $trans->transaction_id, 'package_product_id' => 0, 'is_product_rebate' => 0, 'voucher_type_id' => 0));
				$idx = 0;
				foreach ($trans_products as $prod_item)
				{
					$product = $this->items_model->get_product_by_id($prod_item->product_id);
					
					if($product->is_raffle == 1)
					{
						$raffle_product = $this->items_model->get_raffle_product_by_product_id($prod_item->product_id);
						if(!empty($raffle_product))
						{
							$num_of_entries = floor($prod_item->quantity / $raffle_product->qty_needed) * $raffle_product->qty_generated;
							for($i = 0; $i < $num_of_entries; $i++)
							{
								//loop through number of entries
								$data = array(
									'raffle_id' => $raffle->raffle_id,
									'raffle_number' => '',
									'reference_info' => $trans->transaction_id,
									'value' => $ar_number,
									'is_active' => 0,
								);
								$this->insert_raffle_entries($data);
							
								$raffle_entry_id = $this->insert_id();
							
								$_key = $raffle->raffle_id.".".$raffle->raffle_code.".".$trans->transaction_id."-".$raffle_entry_id.".".$idx."-".time();
								$_salt = sha1(md5($_key).'-'.time());
								$_hash = strtoupper(md5($_key."-".$raffle_entry_id."-".$idx.$_salt.time()));
								$_hash = substr($_hash, 0, 8);

								$this->update_raffle_entries(array('raffle_number' => $_hash), array('raffle_entry_id' => $raffle_entry_id));
								$idx++;
							}
						}
					}
				}				
			}
		}
	}
	
	/******************************************************
	 * UPDATE - PROCESS METHODS
	 */
	
	/* ----------------------------------
	 * this will return the total entries that are active
	 */
	function update_repeat_sales_promo($args, $extra_params = '')
	{
		if (is_array($args))
		{
			if (count($args) == 0) return 0;
			$raffle_id = $args[0];
		}
		else
		{
			$raffle_id = $args;
		}
		
		
		$total_entries = 0;
		$raffle = $this->get_raffle_by_id($raffle_id);
		if (!empty($raffle))
		{
			$total_entries = $this->get_raffle_entries_count(array('raffle_id' => $raffle->raffle_id, 'is_active' => 1));
		}
		
		return $total_entries;
	}
	
	function update_grandmaster_promo($args, $extra_params = '')
	{
		if (is_array($args))
		{
			if (count($args) == 0) return 0;
			$raffle_id = $args[0];
		}
		else
		{
			$raffle_id = $args;
		}
		
		$this->db->select("group, COUNT(1) as qty");
		$this->db->where(array('raffle_id' => $raffle_id, 'is_active' => 1));
		$this->db->group_by("group");
				
		$this->db->from($this->_TABLES['raffle_entries']);
		$query = $this->db->get();
		$rows = $query->result();
		$query->free_result();
		
		$total_entries = array();
		foreach ($rows as $item)
			$total_entries[$item->group] = __::clon($item);
		
		return $total_entries;
	}
	
	function update_ultimate_pack_promo($args, $extra_params = '')
	{
		if (is_array($args))
		{
			if (count($args) == 0) return 0;
			$raffle_id = $args[0];
		}
		else
		{
			$raffle_id = $args;
		}
		
		$total_entries = 0;
		$raffle = $this->get_raffle_by_id($raffle_id);
		if (!empty($raffle))
		{
			$total_entries = $this->get_raffle_entries_count(array('raffle_id' => $raffle->raffle_id, 'is_active' => 1));
		}
		
		return $total_entries;
	}
	
	function update_ultimate_pack_monthly_promo($args, $extra_params = '')
	{
		if (is_array($args))
		{
			if (count($args) == 0) return 0;
			$raffle_id = $args[0];
		}
		else
		{
			$raffle_id = $args;
		}
		
		$total_entries = 0;
		$raffle = $this->get_raffle_by_id($raffle_id);
		if (!empty($raffle))
		{
			$total_entries = $this->get_raffle_entries_count(array('raffle_id' => $raffle->raffle_id, 'is_active' => 1));
		}
		
		return $total_entries;
	}
}    

// end of file