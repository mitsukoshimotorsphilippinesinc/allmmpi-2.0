<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Members_model extends Base_model {
	function __construct()
	{
		// call the base model constructor
		parent::__construct();
		$this->load->model('payout_model');
        $this->load->model('tracking_model');
        $this->load->model('vouchers_model');
		$this->load->model('contents_model');
        $this->load->model('cards_model');
        $this->load->model('payment_model');
        $this->load->model('items_model');
		//$this->load->model('settings_model');


		// assign the table for this model
		$this->_TABLES = array(
				'members' => 'cm_members',
				'member_orders'=>'cm_member_orders',
	            'member_order_products'=>'cm_member_order_products',
				'member_cart'=>'cm_member_cart',
				'member_cart_products'=>'cm_member_cart_products',
				'member_order_products_view'=>'cm_member_order_products_view',
				'member_vouchers' => 'cm_member_vouchers',
				'member_voucher_products' => 'cm_member_voucher_products',
	            'member_vouchers_view' => 'cm_member_vouchers_view',
				'member_accounts' => 'cm_member_accounts',
				'member_user_accounts' => 'cm_member_user_accounts',
				'member_accounts_view' => 'tr_member_accounts_logs_view',
				'member_types' => 'cm_member_types',
				'discounts' => 'rf_discounts',
				'rf_member_types' => 'rf_member_types',
				'member_order_reports' => 'rt_member_orders',
				'member_order_reports_view' => 'rt_member_orders_view',
				'member_account_types' => 'rf_member_account_types',
				'member_account_status' => 'rf_member_account_status',
				'groups' => 'cm_groups',
				'member_groups' => 'rf_member_groups',
				'transaction_codes' => 'rf_transaction_codes',
				'member_earnings' => 'cm_member_earnings',
                'member_earnings_per_type' => 'cm_member_earnings_per_type',
				'member_verification' => 'cm_member_verification',
				'member_rfid_cards' => 'cm_member_rfid_cards',
				'member_account_commissions' => 'cm_member_account_commissions',
				'member_payouts' => 'cm_member_payouts',
				'member_encashments' => 'cm_member_encashments',
				'member_withdraw_funds_transactions' => 'cm_member_withdraw_funds_transactions',	
				'member_withdraw_funds_transactions_view' => 'cm_member_withdraw_funds_transactions_view',
                'member_deductions' => 'cm_member_deductions',
				'non_members' => 'cm_non_members',
                'member_account_upgrades' => 'cm_member_account_upgrades',
                'member_account_pairing' => 'cm_member_account_pairing',
                'member_account_credited_transaction' => 'cm_member_account_credited_transaction',
                'member_account_product_transaction' => 'cm_member_account_product_transaction',
                'member_inventory' => 'cm_member_p2p_inventory',
                'cpoint_ratio' => 'rf_cpoint_ratio',
                'member_p2p_mismatch' => 'cm_member_p2p_mismatch',
				'member_products_cards' => 'cm_member_p2p_products_cards_tally',
                'auto_crediting' => 'cm_auto_crediting',
                'member_payouts_backups' => 'cm_member_payouts_backups',
		);
	}

	function get_members($where = null, $limit = null, $orderby = null, $fields = null)
	{
		$query = $this->fetch('members', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
	}

	function insert_member($data)
	{
		return $this->insert('members', $data);
	}

	function update_member($data, $where)
	{
		return $this->update('members', $data, $where);
	}

	function delete_member($where)
	{
		return $this->delete('members', $where);
	}

	function get_members_count($where = null)
	{
		// do a sql count instead of row count
		$query = $this->fetch('members', 'count(1) as cnt', $where);
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}

	function get_member_by_id($member_id)
	{
		$result = $this->get_members(array('member_id' => $member_id));
		$row = NULL;
		if (count($result) > 0)
		{
			$row = $result[0];
		}
		return $row;
	}
	
	function get_member_by_member_ids($member_ids, $limit = array('offset' => 0, 'rows' => 50)) {
		
		$this->db->select("*");
		$this->db->where_in('member_id', $member_ids);
		
		if (is_array($limit))
			if (isset($limit['offset']) && isset($limit['rows']))
				$this->db->limit($limit['rows'],$limit['offset']);
				
		$this->db->from($this->_TABLES['members']);
		$query = $this->db->get();
		$rows = $query->result();
		$query->free_result();
		
		return $rows;
    }

	function get_member_by_username($slug)
	{
		$result = $this->get_members(array('slug' => $slug));
		$row = NULL;
		if (count($result) > 0)
		{
			$row = $result[0];
		}
		return $row;
	}
	
    function member_account_add_downline_count($account_id)
    {
        $account = $this->get_member_accounts_by_account_ids(array($account_id));
        if(count($account) > 0) {
            $account = $account[0];
            $node_address = $account->node_address;

            while(strlen($node_address) > 1) {
                $last_position = substr($node_address, -1);
                $node_address = substr($node_address, 0, -1);

                $where = "node_address = '{$node_address}' AND account_type_id != 5 AND cd_amount = 0 AND account_type_id != 3";
                $upline_account = $this->get_member_accounts($where);
                if(count($upline_account) == 0) continue;

                $upline_account = $upline_account[0];
                $left = $upline_account->sp_downline_count_left + ($last_position == '2')?1:0;
                $right = $upline_account->sp_downline_count_right + ($last_position == '1')?1:0;
                $this->update_member_accounts(array(
                    'sp_downline_count_left' => $left,
                    'sp_downline_count_right' => $right
                ), $where);
            }
            return true;
        }
        return false;
    }
	
	// member orders
    function get_member_orders($where = null, $limit = null, $orderby = null, $fields = null) 
    {
        $query = $this->fetch('member_orders', $fields, $where, $orderby, $limit);
        $row = $query->result();
        $query->free_result();
        return $row;
    }

    function get_member_order_by_id($order_id) 
    {
        $result = $this->get_member_orders(array('order_id' => $order_id));
        $row = NULL;
        if (count($result) > 0) {
            $row = $result[0];
        }
        return $row;
    }

    function get_member_orders_count($where = null) {
        // do a sql count instead of row count
        $query = $this->fetch('member_orders', 'count(1) as cnt', $where);
        $row = $query->first_row();
        $query->free_result();
        return $row->cnt;
    }


    function insert_member_order($data) {
        return $this->insert('member_orders', $data);
    }    

    function update_member_order($data, $where) 
    {
        return $this->update('member_orders', $data, $where);
    }

    function delete_member_order($where) 
    {
        return $this->delete('member_orders', $where);
    }    
    
    // end of member orders

	function get_member_types($where = null, $limit = null, $typeby = null, $fields = null) 
	{
		$query = $this->fetch('member_types', $fields, $where, $typeby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
	}

	function get_member_type_by_member_id($member_id) 
	{
		$result = $this->get_member_types(array('member_id' => $member_id));
		$row = NULL;
		if (count($result) > 0) {
			$row = $result[0];
		}
		return $row;
	}
	
	function get_member_types_count($where = null) {
        // do a sql count instead of row count
        $query = $this->fetch('member_types', 'count(1) as cnt', $where);
        $row = $query->first_row();
        $query->free_result();
        return $row->cnt;
    }
	
    function insert_member_type($data) {
        return $this->insert('member_types', $data);
    }    

    function update_member_type($data, $where) 
    {
        return $this->update('member_types', $data, $where);
    }

    function delete_member_type($where) 
    {
        return $this->delete('member_types', $where);
    }

	function get_rf_member_types($where = null, $limit = null, $typeby = null, $fields = null)
	{
		$query = $this->fetch('rf_member_types', $fields, $where, $typeby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
	}


	function get_rf_member_type_by_member_type_id($member_type_id)
	{
		$result = $this->get_rf_member_types(array('member_type_id' => $member_type_id));
		$row = NULL;
		if (count($result) > 0) {
			$row = $result[0];
		}
		return $row;
	}

    // member order products
    function get_member_order_products($where = null, $limit = null, $orderby = null, $fields = null) 
    {
        $query = $this->fetch('member_order_products', $fields, $where, $orderby, $limit);
        $row = $query->result();
        $query->free_result();
        return $row;
    }
    
    function get_member_order_product_by_id($order_id) {
        $result = $this->get_member_order_products(array('order_id' => $order_id));
        $row = NULL;
        if (count($result) > 0) {
            $row = $result;
        }
        return $row;
    }

    function insert_member_order_product($data) {
        return $this->insert('member_order_products', $data);
    }    

    function update_member_order_product($data, $where) 
    {
        return $this->update('member_order_products', $data, $where);
    }

    function delete_member_order_product($where) 
    {
        return $this->delete('member_order_products', $where);
    } 
    // end or member order products
	
	
	// member cart
    function get_member_cart($where = null, $limit = null, $orderby = null, $fields = null) 
    {
        $query = $this->fetch('member_cart', $fields, $where, $orderby, $limit);
        $row = $query->result();
        $query->free_result();
        return $row;
    }

	function get_recent_member_cart_by_member_id($member_id)
	{
		$result = $this->get_member_cart(array('member_id' => $member_id, 'status' => "SHOPPING"), null,"insert_timestamp DESC");
        $row = NULL;
        if (count($result) > 0) {
            $row = $result[0];
        }
        return $row;
	}

    function get_member_cart_by_id($cart_id) 
    {
        $result = $this->get_member_cart(array('cart_id' => $cart_id));
        $row = NULL;
        if (count($result) > 0) {
            $row = $result[0];
        }
        return $row;
    }

    function insert_member_cart($data) {
        return $this->insert('member_cart', $data);
    }    

    function update_member_cart($data, $where) 
    {
        return $this->update('member_cart', $data, $where);
    }

    function delete_member_cart($where) 
    {
        return $this->delete('member_cart', $where);
    }        
    // end of member cart

	// member cart products
    function get_member_cart_products($where = null, $limit = null, $orderby = null, $fields = null) 
    {
        $query = $this->fetch('member_cart_products', $fields, $where, $orderby, $limit);
        $row = $query->result();
        $query->free_result();
        return $row;
    }
	
	function get_member_cart_products_by_cart_id($cart_id) 
    {
        return $this->get_member_cart_products(array('cart_id'=>$cart_id));
    }
	
	function get_member_cart_product_by_id($cart_product_id) 
    {
        $result = $this->get_member_cart_products(array('cart_product_id' => $cart_product_id));
        $row = NULL;
        if (count($result) > 0) {
            $row = $result[0];
        }
        return $row;
    }
	
	function get_member_cart_product_by_cart_id_and_product_id($cart_id, $product_id) 
    {
        $result = $this->get_member_cart_products(array('cart_id' => $cart_id, 'product_id'=> $product_id));
        $row = NULL;
        if (count($result) > 0) {
            $row = $result[0];
        }
        return $row;
    }

    function insert_member_cart_product($data) {
        return $this->insert('member_cart_products', $data);
    }    

    function update_member_cart_products($data, $where) 
    {
        return $this->update('member_cart_products', $data, $where);
    }

    function delete_member_cart_products($where) 
    {
        return $this->delete('member_cart_products', $where);
    }
	
	
	/* -- FOR VOUCHER MODULE -- */
	function get_member_vouchers($where = null, $limit = null, $orderby = null, $fields = null) {
        $query = $this->fetch('member_vouchers_view', $fields, $where, $orderby, $limit);
        $row = $query->result();
        $query->free_result();
        return $row;
    }

	function get_member_vouchers_count($where = null) {
        // do a sql count instead of row count
        $query = $this->fetch('member_vouchers', 'count(1) as cnt', $where);
        $row = $query->first_row();
        $query->free_result();
        return $row->cnt;
    }
	
	// get voucher details using voucher_code
	function get_member_voucher_by_code($voucher_code) {
        $result = $this->get_member_vouchers(array('voucher_code' => $voucher_code));
        $row = NULL;
        if (count($result) > 0) {
            $row = $result[0];
        }
        return $row;
    }
	
	function get_member_voucher_by_id($voucher_id) {
        $result = $this->get_member_vouchers(array('voucher_id' => $voucher_id));
        $row = NULL;
        if (count($result) > 0) {
            $row = $result[0];
        }
        return $row;
    }
	
	function get_member_voucher_by_transaction_id($transaction_id) {
        $result = $this->get_member_vouchers(array('transaction_id' => $transaction_id));
        $row = NULL;
        if (count($result) > 0) {
            $row = $result[0];
        }
        return $row;
    }

	function insert_member_vouchers($data) 
    {
        return $this->insert('member_vouchers', $data);
    }
	
	function update_member_vouchers($data, $where) 
    {
        return $this->update('member_vouchers', $data, $where);
    }

	// member_voucher_products
	
	function get_member_voucher_products($where = null, $limit = null, $orderby = null, $fields = null) {
        $query = $this->fetch('member_voucher_products', $fields, $where, $orderby, $limit);
        $row = $query->result();
        $query->free_result();
        return $row;
    }

	function get_member_voucher_products_count($where = null) {
        // do a sql count instead of row count
        $query = $this->fetch('member_voucher_products', 'count(1) as cnt', $where);
        $row = $query->first_row();
        $query->free_result();
        return $row->cnt;
    }
	
	function get_member_voucher_products_by_id($voucher_id) {
        $result = $this->get_member_voucher_products(array('voucher_id' => $voucher_id));
        $row = array();
        if (count($result) > 0) {
            $row = $result;
        }
        return $row;
    }

	function insert_member_voucher_products($data) 
    {
        return $this->insert('member_voucher_products', $data);
    }
	
	function update_member_voucher_products($data, $where) 
    {
        return $this->update('member_voucher_products', $data, $where);
    }
	
	
	
	
	
	function get_member_account_earnings($where = null, $limit = null, $orderby = null, $fields = null) 
    {
        $query = $this->fetch('member_accounts_view', $fields, $where, $orderby, $limit);
        $row = $query->result();
        $query->free_result();
        return $row;
    }
	
	// purchase orders
    function get_purchase_orders($where = null, $limit = null, $orderby = null, $fields = null) 
    {
        $query = $this->fetch('purchase_orders', $fields, $where, $orderby, $limit);
        $row = $query->result();
        $query->free_result();
        return $row;
    }

    function get_purchase_order_by_id($order_id) 
    {
        $result = $this->get_purchase_orders(array('purchase_order_id' => $order_id));
        $row = NULL;
        if (count($result) > 0) {
            $row = $result[0];
        }
        return $row;
    }

    function get_purchase_orders_count($where = null) {
        // do a sql count instead of row count
        $query = $this->fetch('purchase_orders', 'count(1) as cnt', $where);
        $row = $query->first_row();
        $query->free_result();
        return $row->cnt;
    }


    function insert_purchase_order($data) {
        return $this->insert('purchase_orders', $data);
    }    

    function update_purchase_order($data, $where) 
    {
        return $this->update('purchase_orders', $data, $where);
    }

    function delete_purchase_order($where) 
    {
        return $this->delete('purchase_orders', $where);
    }    
    
    // end of purchase orders


    // purchase order products
    function get_purchase_order_products($where = null, $limit = null, $orderby = null, $fields = null) 
    {
        $query = $this->fetch('purchase_order_products', $fields, $where, $orderby, $limit);
        $row = $query->result();
        $query->free_result();
        return $row;
    }
    
    function get_purchase_order_product_by_id($purchase_order_id) {
        $result = $this->get_purchase_order_products(array('purchase_order_id' => $purchase_order_id));
        $rows = NULL;
        if (count($result) > 0) {
            $rows = $result;
        }
        return $rows;
    }

    function insert_purchase_order_product($data) {
        return $this->insert('purchase_order_products', $data);
    }    

    function update_purchase_order_product($data, $where) 
    {
        return $this->update('purchase_order_products', $data, $where);
    }

    function delete_purchase_order_product($where) 
    {
        return $this->delete('purchase_order_products', $where);
    } 
    // end or member order products
	

	
	// member user accounts
    function get_member_user_accounts($where = null, $limit = null, $orderby = null, $fields = null) 
    {
        $query = $this->fetch('member_user_accounts', $fields, $where, $orderby, $limit);
        $row = $query->result();
        $query->free_result();
        return $row;
    }
    
    function get_member_user_account_by_id($member_user_account_id) {
        $result = $this->get_member_user_accounts(array('member_user_account_id' => $member_user_account_id));
        $row = NULL;
        if (count($result) > 0) {
            $row = $result[0];
        }
        return $row;
    }

	function get_member_user_account_by_member_id($member_id) {
        $result = $this->get_member_user_accounts(array('member_id' => $member_id));
        $row = NULL;
        if (count($result) > 0) {
            $row = $result[0];
        }
        return $row;
    }

	function get_member_user_account_by_username($username) {
        $result = $this->get_member_user_accounts(array('username' => $username));
        $row = NULL;
        if (count($result) > 0) {
            $row = $result[0];
        }
       	return $row;
	}
	
    function insert_member_user_accounts($data) {
        return $this->insert('member_user_accounts', $data);
    }    

    function update_member_user_accounts($data, $where) 
    {
        return $this->update('member_user_accounts', $data, $where);
    }

    function delete_member_user_accounts($where) 
    {
        return $this->delete('member_user_accounts', $where);
    } 

	function get_member_user_accounts_count($where = null) {
        // do a sql count instead of row count
        $query = $this->fetch('member_user_accounts', 'count(1) as cnt', $where);
        $row = $query->first_row();
        $query->free_result();
        return $row->cnt;
    }
    // end or member user accounts
	
	
	// member accounts
	function get_member_accounts($where = null, $limit = null, $orderby = null, $fields = null)
	{
		$query = $this->fetch('member_accounts', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
	}

	function insert_member_accounts($data)
	{
		return $this->insert('member_accounts', $data);
	}

	function update_member_accounts($data, $where)
	{
		return $this->update('member_accounts', $data, $where);
	}

	function delete_member_accounts($where)
	{
		return $this->delete('member_accounts', $where);
	}

    function get_company_account_ids()
    {
        $accounts = $this->get_member_accounts(array('member_id' => '1162'), null, null, array('account_id'));
        $acct_ids = array();
        foreach($accounts as $item) $acct_ids[] = $item->account_id;
        return $acct_ids;
    }

	function get_member_accounts_count($where = null)
	{
		// do a sql count instead of row count
		$query = $this->fetch('member_accounts', 'count(1) as cnt', $where);
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}
	
	function get_member_accounts_by_member_ids($member_ids, $limit = array('offset' => 0, 'rows' => 50)) {
		
		$this->db->select("*");
		$this->db->where_in('member_id', $member_ids);
		
		if (is_array($limit))
			if (isset($limit['offset']) && isset($limit['rows']))
				$this->db->limit($limit['rows'],$limit['offset']);
				
		$this->db->from($this->_TABLES['member_accounts']);
		$query = $this->db->get();
		$rows = $query->result();
		$query->free_result();
		
		return $rows;
    }

	function get_member_accounts_by_account_ids($account_ids, $limit = array('offset' => 0, 'rows' => 50)) {
		
		$this->db->select("*");
		$this->db->where_in('account_id', $account_ids);
		
		if (is_array($limit))
			if (isset($limit['offset']) && isset($limit['rows']))
				$this->db->limit($limit['rows'],$limit['offset']);
				
		$this->db->from($this->_TABLES['member_accounts']);
		$query = $this->db->get();
		$rows = $query->result();
		$query->free_result();
		
		return $rows;
    }

	function get_member_accounts_downline_by_level($node_address, $level = 4, $fields = null) {
		
		$where = "node_address like '{$node_address}%' AND node_address != '{$node_address}' AND LENGTH(node_address) <= (LENGTH('{$node_address}') + {$level})";
		$this->db->select( (empty($fields) ? "*" : $fields) . ", SUBSTRING(node_address, LENGTH('{$node_address}')+1) as trimmed_node_address");
		$this->db->where($where);
		$this->db->order_by("LENGTH(node_address) ASC");
				
		$this->db->from($this->_TABLES['member_accounts']);
		$query = $this->db->get();
		$rows = $query->result();
		$query->free_result();
		
		return $rows;

    }

	function get_downline_count_by_node_address($node_address, $side = 'both', $dont_include_fs = true, $no_cd_balance = true, $dont_include_tp = true, $trial_pack_is_half = false)
	{
		$side = strtolower($side);
		$side_inset = '';
		if ($side == 'l') $side_inset = '2';
		if ($side == 'r') $side_inset = '1';
		$_where_fs = "";
		if ($dont_include_fs) $_where_fs = " AND account_type_id != 2";
		$_where_cd = "";
		if ($no_cd_balance) $_where_cd = " AND cd_amount = 0";
		$_where_tp = "";
		if ($dont_include_tp) {
			$_where_tp = " AND account_type_id != 11";
		} else {
			$_where_tp = " AND account_type_id = 11";
		};
		
		
		if ($trial_pack_is_half) {		
			$this->db->select("count(1) / 2 as cnt");
		} else {
			$this->db->select("count(1) as cnt");
		}

        $_where_vp = " AND account_type_id != 5";
		
		$this->db->where("node_address like '{$node_address}{$side_inset}%' {$_where_vp} {$_where_fs} {$_where_cd} {$_where_tp}");
		$this->db->from($this->_TABLES['member_accounts']);
		$query = $this->db->get();
		$row = $query->first_row();
		$query->free_result();
		
		return $row->cnt;
		
	}


    function get_downline_count_expanded_by_node_address($node_address,$side = 'both')
    {
        $side = strtolower($side);
        $side_inset = '';
        if ($side == 'l') $side_inset = '2';
        if ($side == 'r') $side_inset = '1';


        //$p2p_card = $this->cards_model->get_card_type_by_code('P2P');
        //$p2p_card_id = $p2p_card->card_type_id;
        $p2p_where = "AND upgrade_type = 'P2P'";

        //$up1_card = $this->cards_model->get_card_type_by_code('UP1');
        //$up1_card_id = $up1_card->card_type_id;
        $up1_where = "AND upgrade_type = 'UP1'";

        $erhm_card = $this->cards_model->get_card_type_by_code('ERHM');
        $erhm_card_id = $erhm_card->card_type_id;
        $sp_card = $this->cards_model->get_card_type_by_code('SP');
        $sp_card_id = $sp_card->card_type_id;
        $erhm_where = "AND account_type_id IN ('{$sp_card_id}','{$erhm_card_id}')";
       
        $cd_card = $this->cards_model->get_card_type_by_code('CD');
        $cd_card_id = $cd_card->card_type_id;
        $cd_erhm_card = $this->cards_model->get_card_type_by_code('CD-ERHM');
        $cd_erhm_card_id = $cd_erhm_card->card_type_id;
        $cd_where = "AND account_type_id IN ('{$cd_card_id}','{$cd_erhm_card_id}')";
        
        $erhm_count = 0;
        $cd_count = 0;
        $up1_count = 0;
        $p2p_count = 0;

        $this->db->select("count(1) as cnt");
        //erhm
        $this->db->where("node_address like '{$node_address}{$side_inset}%' {$erhm_where}");
        $this->db->from($this->_TABLES['member_accounts']);
        $query = $this->db->get();
        $row = $query->first_row();
        if(sizeof($row)>0)
            $erhm_count = $row->cnt;
        $query->free_result();

        //count completed CD as ERHM
        $this->db->select("count(1) as cnt");
        $this->db->where("node_address like '{$node_address}{$side_inset}%' {$cd_where} AND credit_flag = '1'");
        $this->db->from($this->_TABLES['member_accounts']);
        $this->db->join('trg_commission_deduct_completion','trg_commission_deduct_completion.account_id = '.$this->_TABLES['member_accounts'].'.account_id','left');
        //$this->db->join('trg_commission_deduct_completion','account_id = account_id','left');
        $query = $this->db->get();
        $row = $query->first_row();
        if(sizeof($row)>0)
            $erhm_count += $row->cnt;
        $query->free_result();

        //cd
        $this->db->select("count(1) as cnt");
        $this->db->where("node_address like '{$node_address}{$side_inset}%' {$cd_where}");
        $this->db->from($this->_TABLES['member_accounts']);
        $query = $this->db->get();
        $row = $query->first_row();
        if(sizeof($row)>0)
            $cd_count = $row->cnt;
        $query->free_result();

        //up1
        $this->db->select("count(1) as cnt");
        $this->db->where("node_address like '{$node_address}{$side_inset}%' {$up1_where}");
        $this->db->from($this->_TABLES['member_accounts']);
        $this->db->join($this->_TABLES['member_account_upgrades'],'account_id = base_account_id','left');
        $query = $this->db->get();
        $row = $query->first_row();
        if(sizeof($row)>0)
            $up1_count = $row->cnt;
        $query->free_result();

        //p2p
        $this->db->select("count(1) as cnt");
        $this->db->where("node_address like '{$node_address}{$side_inset}%' {$p2p_where}");
        $this->db->from($this->_TABLES['member_accounts']);
        $this->db->join($this->_TABLES['member_account_upgrades'],'account_id = base_account_id','left');
        $query = $this->db->get();
        $row = $query->first_row();
        if(sizeof($row)>0)
            $p2p_count = $row->cnt;
        $query->free_result();
        

        $tbl_return  = '';
        $tbl_return .= '<table>';
        $tbl_return .= '<tr><td class="count-label">ERHM</td><td>'.$erhm_count.'</td></tr>';
        $tbl_return .= '<tr style="display:none;"><td class="count-label">CD</td><td>'.$cd_count.'</td></tr>';
        $tbl_return .= '<tr><td class="count-label">UP1</td><td>'.$up1_count.'</td></tr>';
        $tbl_return .= '<tr><td class="count-label">(P-P)<sup>3</sup></td><td>'.$p2p_count.'</td></tr>';
        
        $tbl_return .= '</table>';

        return $tbl_return;
    }

	function get_member_account_by_id($member_account_id)
	{
		$result = $this->get_member_accounts(array('member_account_id' => $member_account_id));
		$row = NULL;
		if (count($result) > 0)
		{
			$row = $result[0];
		}
		return $row;
	}
	
	function get_member_primary_account_by_timestamp($member_id)
	{
		$result = $this->get_member_accounts(array('member_id' => $member_id), array('rows' => 1, 'offset' => 0), "insert_timestamp ASC");
		$row = NULL;
		if (count($result) > 0)
		{
			$row = $result[0];
		}
		return $row;
	}
	
	function get_member_primary_account_by_node($member_id)
	{
		$result = $this->get_member_accounts(array('member_id' => $member_id), array('rows' => 1, 'offset' => 0), "LENGTH(node_address) ASC");
		$row = NULL;
		if (count($result) > 0)
		{
			$row = $result[0];
		}
		return $row;
	}

	function get_member_account_by_account_id($account_id)
	{
		$result = $this->get_member_accounts(array('account_id' => $account_id));
		$row = NULL;
		if (count($result) > 0)
		{
			$row = $result[0];
		}
		return $row;
	}

	function get_member_account_by_uni_node_address($uni_node_address)
	{
		$result = $this->get_member_accounts(array('uni_node' => $uni_node_address));
		$row = NULL;
		if (count($result) > 0)
		{
			$row = $result[0];
		}
		return $row;
	}
	
	// Add to Member Accounts Funds
    function credit_funds($member_id,$amount,$type,$remarks="") {
        
        $member = $this->get_member_by_id($member_id);               

		if ($type=="GC") 
		{
	        $new_gcs = $member->gift_cheques + $amount;
	        $data = array("gift_cheques"=>$new_gcs);
		}
		elseif($type == 'GCEP')
		{
			$new_gcep = $member->gcep + $amount;
			$data = array("gcep" => $new_gcep);
		}
		elseif($type == 'C POINTS')
		{
			$new_cpoints = $member->cpoints + $amount;
			$data = array("cpoints" => $new_cpoints);
		}
		else
		{
	        $new_funds = $member->funds + $amount;
	        $data = array("funds"=>$new_funds);
		}
        $this->update_member($data,"member_id = {$member_id}");						

		// insert remarks
		$this->load->model('tracking_model');
		
		$data = array(
			"member_id"=>$member_id,
			"amount"=>$amount,
			"type"=>$type,
			"remarks"=>$remarks
		);
		$this->tracking_model->insert_credit_logs($data);
		
        return $data;
    }

	// Subtract from Member Accounts Funds
    function debit_funds($member_id,$amount,$type,$remarks="") {
        
        $member = $this->get_member_by_id($member_id);            
		
		$type = strtoupper($type);
		
		if ($type=="GIFTCHEQUE") 
		{
	        $new_gcs = $member->gift_cheques - $amount;
	        $data = array("gift_cheques"=>max($new_gcs,0));
		}
		elseif($type == 'GCEP')
		{
			$new_gcep = $member->gcep - $amount;
			$data = array('gcep' => max($new_gcep, 0));
		}
		elseif($type == 'CPOINTS')
		{
			$new_cpoints = $member->cpoints - $amount;
			$data = array('cpoints' => max($new_cpoints, 0));
		}
		else
		{
	        $new_funds = $member->funds - $amount;
	        $data = array("funds"=>max($new_funds,0));
		}
        $this->update_member($data,"member_id = {$member_id}");						

		// insert remarks
		$this->load->model('tracking_model');
		
		$data = array(
			"member_id"=>$member_id,
			"amount"=>$amount,
			"type"=>$type,
			"remarks"=>$remarks
		);
		$this->tracking_model->insert_debit_logs($data);

        return $data;
    }

	function credit_earnings($account_id,$card_id,$card_type_id,$from_account_id,$amount,$transaction_code,$remarks="",$counter=NULL,$timestamp="") {
        
        //get member_account
      $member_account = $this->get_member_account_by_account_id($account_id);               

        // get pending earnings
        $condition = "account_id = '{$account_id}'";  
        $order_by = NULL;
        $member_earnings = $this->get_member_earnings($condition,NULL,$order_by);
        if(sizeof($member_earnings) == 0)
        {
            $this->insert_member_earnings(array(
                'member_id' => $member_account->member_id,
                'account_id' => $account_id
            ));
            $member_earnings = $this->get_member_earnings($condition,NULL,$order_by);
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

        $this->update_member_earnings($data,"earning_id = {$member_earnings->earning_id}");

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
		
		/*if ($member_account->sms_notification == 1) {
            if ($transaction_code != 105) {
                $data["sms_sent"] = 1;
                $data["tariff"] = $this->settings->notification_cost_sms;
            } else {
                if ($this->settings->enable_unilevel_text_blast == 1) {
                    $data["sms_sent"] = 1;
                    $data["tariff"] = $this->settings->notification_cost_sms;
                }
            }      	
        }
        */

        $this->tracking_model->insert_acct_credit_logs($data);

        // get credit log id
        $credit_log_id = $this->tracking_model->insert_id();
		
        // get credit log id
        $credit_log_id = $this->tracking_model->insert_id();
        
		$member_details = $this->get_member_by_id($member_account->member_id);
	    
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
			
			if (($transaction_code != 105)) {          
                // IGPSM
                
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
                    "log_id"=>$credit_log_id,
                    "params"=>$data_ctbd
                );
            
                //send sms to user
                Modules::run('jobs/notifications/send_tb_sms',$sms_data);
            
            } else {
                // UNILEVEL
                
                if ($this->settings->enable_unilevel_text_blast == 1) {
                    $data_ctbd =  array(
                        "remarks" => $remarks
                    );
                
                    $sms_data = array(
                        "member_id"=>$member_account->member_id,
                        "account_id"=>$account_id,
                        "mobile_number"=>$member_details->mobile_number,
                        "type"=>"text-blast-generic-message",
                        "is_charged"=>1,
                        "tariff"=>$this->settings->notification_cost_sms,
                        "log_id"=>$credit_log_id,
                        "params"=>$data_ctbd
                    );
                
                    //send sms to user
                    Modules::run('jobs/notifications/send_tb_sms',$sms_data);
                }
            } 
		}

        return $data;
    }

    function _credit_earnings_2($account_id,$card_id,$from_account_id,$amount,$transaction_code,$remarks="", $timestamp) {

        //get member_account
        $member_account = $this->get_member_account_by_account_id($account_id);               

        // get pending earnings
        $condition = "account_id = '{$account_id}'";    
        $order_by = NULL;
        $member_earnings = $this->get_member_earnings($condition,NULL,$order_by);               
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
        

        $this->update_member_earnings($data,"earning_id = {$member_earnings->earning_id}");

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
            "remarks"=>$remarks,
            "insert_timestamp" => $timestamp
        );
        $this->tracking_model->insert_acct_credit_logs($data);
		
		//$member_details = $this->get_member_by_id($member_account->member_id);
	    //
		//if ($member_account->sms_notification == 1) {
	    //
		//	$sms_data = array(
		//		"member_id"=>$member_account->member_id,
		//		"mobile_number"=>$member_details->mobile_number,
		//		"type"=>"text-blast-generic-message",
		//		"params"=>$data
		//	);
        //
		//	//send sms to user
		//	Modules::run('jobs/notifications/send_sms',$sms_data);
		//}

        return $data;
    }


	function debit_earnings($account_id,$amount,$transaction_code,$remarks="") {

		//get member_account
	    $member_account = $this->get_member_account_by_account_id($account_id);               

		// insert remarks
		$this->load->model('tracking_model');
		
		$type = $transaction_code == 200 ? 'FUNDS' : 'GC';
		
		$data = array(
			"member_id"=>$member_account->member_id,
			"account_id"=>$member_account->account_id,
			"account_status_id"=>$member_account->account_status_id,
			"transaction_code"=>$transaction_code,
			"type"=>$type,
			"amount"=>$amount,
			"remarks"=>$remarks
		);
		$this->tracking_model->insert_acct_debit_logs($data);

	    return $data;
	}


	// Add to Member Accounts Points
	// function credit_points($account_id,$card_id,$from_account_id,$type,$position,$points,$timestamp="")
	// {
		
 //        $this->load->model('tracking_model');

	// 	$member_account = $this->get_member_account_by_account_id($account_id);

	// 	// check account type, if tp give only half pairing bonus and half pairing points

	// 	if ($type=="sp" && $member_account->account_type_id==3) // if TP member
	// 	{
	// 		$is_trial_pack_account = 1;
	// 	}
	// 	else
	// 	{
	// 		$is_trial_pack_account = 0;			
	// 	}		

	// 	$pairs_type = "pairs_{$type}";
	// 	$left_type = "left_{$type}";
	// 	$right_type = "right_{$type}";
	// 	$gc_pair_ctr_type = "gc_pair_ctr_{$type}";
	// 	$gc_type = "gc_{$type}";
	// 	$flushout_type = "flushout_{$type}";

	// 	if ($is_trial_pack_account) 
	// 	{
	// 		$points_to_pair_type = "tp_points_to_pair";
	// 		$pairing_bonus_type = "tp_pairing_bonus";		
	// 		$bonus_type = "pairing_bonus_tp";
	// 		$gift_cheque_type = "gift_cheque_tp";
				
	// 	}
	// 	else
	// 	{
	// 		$points_to_pair_type = "{$type}_points_to_pair";
	// 		$pairing_bonus_type = "{$type}_pairing_bonus";			
	// 		$bonus_type = "pairing_bonus_{$type}";
	// 		$gift_cheque_type = "gift_cheque_{$type}";		
	// 	}
		

	// 	if($type == "tp") // credit points to sp when type is tp
	// 	{
	// 		$pairs_type = "pairs_sp";
	// 		$left_type = "left_sp";
	// 		$right_type = "right_sp";
	// 		$gc_type = "gc_sp";
	// 		$flushout_type = "flushout_sp";
	// 		$gc_pair_ctr_type = "gc_pair_ctr_sp";
	// 	}
		
	// 	$where = "account_id = '{$account_id}'";
	// 	$limit = array("rows"=>1,"offset"=>0);
	// 	$order = NULL;
	// 	$member_earnings = $this->get_member_earnings($where,$limit,$order);
	// 	$member_earnings = $member_earnings[0];

	// 	// get current gift cheques and funds account
	// 	$gift_cheques = $member_earnings->$gift_cheque_type;
	// 	$bonus = $member_earnings->$bonus_type;
				
	// 	$pairs =  $member_account->$pairs_type;
	// 	$gc = $member_account->$gc_type;
	// 	$flushout = $member_account->$flushout_type;		
	// 	$gc_pair_ctr = $member_account->$gc_pair_ctr_type;		
		
	// 	if ($position=='left') 
	// 	{
	// 		$left = $member_account->$left_type + $points;
	// 		$right = $member_account->$right_type;			
	// 	}
	// 	else
	// 	{			
	// 		$left = $member_account->$left_type;
	// 		$right = $member_account->$right_type + $points;			
	// 	}
		

	// 	// get settings value
	// 	$points_to_pair = $this->settings->$points_to_pair_type;
	// 	$max_pairs = $this->settings->max_pairs;
	// 	$pairs_ctr_to_gc = $this->settings->pairs_ctr_to_gc;
	// 	$pairing_bonus = $this->settings->$pairing_bonus_type;
	// 	$gift_cheque_bonus = $type=='vp' ? ($this->settings->gift_cheque * 2) : $this->settings->gift_cheque; // double the gc if value pack

	//	// 20131112
	//	// new (from rf_members_achivements) / check first if member is included in the sm_featured_members table
	//	$featured_member_details = $this->contents_model->get_featured_member_by_member_id($member_account->member_id);
	//	
	//	if (!empty($featured_member_details)) {				
	//		$member_achievement_details = $this->contents_model->get_member_achievement_by_id($featured_member_details[0]->achievement_id);
	//		
	//		if (!empty($member_achievement_details)) {
	//			// get flushout/max pairs
	//			$max_pairs = $member_achievement_details->max-pairs;
	//		}
	//	}
	//	
	//	$pairs_ctr_to_gc = $this->settings->pairs_ctr_to_gc;
	//	$pairing_bonus = $this->settings->$pairing_bonus_type;
	//	$gift_cheque_bonus = $type=='vp' ? ($this->settings->gift_cheque * 2) : $this->settings->gift_cheque; // double the gc if value pack
	//	
	// 	// get commission type based on transaction_code		
	// 	if ($type=='sp') $transaction_code = 101;
	// 	elseif ($type=='vp') $transaction_code = 102;
	// 	elseif ($type=='tp') $transaction_code = 103;
	// 	elseif ($type=='rs') $transaction_code = 104;
			
	// 	// insert credited points to logs
	// 	$data = array(
	// 		"member_id"=>$member_account->member_id,
	// 		"account_id"=>$member_account->account_id,
	// 		"account_status_id"=>$member_account->account_status_id,
	// 		"card_id"=>$card_id,
	// 		"from_account_id"=>$from_account_id,
	// 		"type"=>strtoupper($type),
	// 		"side"=>strtoupper($position),
	// 		"amount"=>$points,
	// 		"remarks"=>"Credit " . strtoupper($type) . " Points from {$card_id}"
	// 	);
 //        if($timestamp!="") $data["insert_timestamp"] = $timestamp;
		
	// 	$this->tracking_model->insert_credit_points_logs($data);
				
	// 	// check for pairings		
	// 	$_left = floor($left / $points_to_pair);
	// 	$_right = floor($right / $points_to_pair);
		
	// 	if ($_left<=$_right) $new_pairs = $_left; 
	// 	elseif ($_right<$_left) $new_pairs = $_right; 
		
	// 	// get points to deduct		
	// 	if ($new_pairs>0) 
	// 		$points_to_deduct = $points_to_pair * $new_pairs;
	// 	else
	// 		$points_to_deduct = 0;
			
	// 	if (($is_trial_pack_account || $type == "tp") && $new_pairs>0)
	// 	{
	// 		$new_pairs = $new_pairs / 2;
	// 		$new_pairs = number_format($new_pairs,2);
	// 	} 
		
	// 	// set new pairs, new left and new right
	// 	$pairs = $pairs + $new_pairs;
	// 	$left = $left - $points_to_deduct;
	// 	$right = $right - $points_to_deduct;
				
	// 	// process pairs
	// 	if ($pairs > $max_pairs) // flushout
	// 	{
	// 		$flushout++; // do nothing

	// 		// insert to credit logs
	// 		$data = array(
	// 			"member_id"=>$member_account->member_id,
	// 			"account_id"=>$member_account->account_id,
	// 			"account_status_id"=>$member_account->account_status_id,
	// 			"card_id"=>$card_id,
	// 			"from_account_id"=>$from_account_id,
	// 			"transaction_code"=>$transaction_code,
	// 			"type"=>"FUNDS",
	// 			"amount"=>0,
	// 			"remarks"=>"No pairing bonus due to flushout from {$card_id}"
	// 		);
 //            if($timestamp!="") $data["insert_timestamp"] = $timestamp;
	// 		$this->tracking_model->insert_acct_credit_logs($data);
	// 	} 
	// 	else // credit gc or pairing bonus
	// 	{
	// 		if ($new_pairs>0) { // only add if there are new pairs
				
	// 			$gc_pair_ctr += $new_pairs; // add to gc pair ctr;	

	// 			if ($gc_pair_ctr>=$pairs_ctr_to_gc) // check if 5th pair based on gc_pair_ctr
	// 			{
	// 				$gc++; 
	// 				$gc_pair_ctr = 0; // reset gc_pair_ctr;

	// 				$gift_cheques += $gift_cheque_bonus;					
					
	// 				if ($type=='sp') $transaction_code = 106;
	// 				elseif ($type=='vp') $transaction_code = 107;
	// 				elseif ($type=='tp') $transaction_code = 108;
	// 				elseif ($type=='rs') $transaction_code = 109;					

	// 				// insert to credit logs
	// 				$data = array(
	// 					"member_id"=>$member_account->member_id,
	// 					"account_id"=>$member_account->account_id,
	// 					"account_status_id"=>$member_account->account_status_id,
	// 					"card_id"=>$card_id,
	// 					"from_account_id"=>$from_account_id,
	// 					"transaction_code"=>$transaction_code,
	// 					"type"=>"GC",
	// 					"amount"=>$gift_cheque_bonus,
	// 					"remarks"=>"Credit gift cheque for fifth pair from {$card_id}"
	// 				);
 //                    if($timestamp!="") $data["insert_timestamp"] = $timestamp;
	// 				$this->tracking_model->insert_acct_credit_logs($data);

 //                    // credit gc to cm_members and cm_member_accounts
 //                    // $this->credit_gc_to_member($member_account->member_id, $member_account->account_id, $gift_cheque_bonus, $type);
	// 			} 
	// 			else
	// 			{				
	// 				// set remarks
	// 				$remarks = "Credit ". strtoupper($type) ." pairing bonus from {$card_id}";

	// 				$bonus += $pairing_bonus;

	// 				// insert to credit logs
	// 				$data = array(
	// 					"member_id"=>$member_account->member_id,
	// 					"account_id"=>$member_account->account_id,
	// 					"account_status_id"=>$member_account->account_status_id,
	// 					"card_id"=>$card_id,
	// 					"from_account_id"=>$from_account_id,
	// 					"transaction_code"=>$transaction_code,
	// 					"type"=>"FUNDS",
	// 					"amount"=>$pairing_bonus,
	// 					"remarks"=>$remarks
	// 				);
 //                    if($timestamp!="") $data["insert_timestamp"] = $timestamp;
	// 				$this->tracking_model->insert_acct_credit_logs($data);
	// 			}			
	// 		}			
	// 	}
		
	// 	if ($points_to_deduct>0) 
	// 	{
	// 		$remarks = "Debit " . strtoupper($type) . " pairing points from {$card_id}";
			
	// 		// insert debited left points to logs
	// 		$data = array(
	// 			"member_id"=>$member_account->member_id,
	// 			"account_id"=>$member_account->account_id,
	// 			"account_status_id"=>$member_account->account_status_id,
	// 			"card_id"=>$card_id,
	// 			"type"=>strtoupper($type),
	// 			"side"=>"LEFT",
	// 			"amount"=>$points,
	// 			"remarks"=>$remarks		
	// 		);
 //            if($timestamp!="") $data["insert_timestamp"] = $timestamp;
	// 		$this->tracking_model->insert_debit_points_logs($data);

	// 		// insert debited right points to logs
	// 		$data = array(
	// 			"member_id"=>$member_account->member_id,
	// 			"account_id"=>$member_account->account_id,
	// 			"account_status_id"=>$member_account->account_status_id,
	// 			"card_id"=>$card_id,
	// 			"type"=>strtoupper($type),
	// 			"side"=>"RIGHT",
	// 			"amount"=>$points,
	// 			"remarks"=>$remarks
	// 		);
 //            if($timestamp!="") $data["insert_timestamp"] = $timestamp;
	// 		$this->tracking_model->insert_debit_points_logs($data);			
	// 	}

	// 	// update member account with final values
	// 	$data = array(
	// 		$pairs_type=>$pairs,
	// 		$left_type=>$left,
	// 		$right_type=>$right,
	// 		$gc_type=>$gc,
	// 		$flushout_type=>$flushout,
	// 		$gc_pair_ctr_type=>$gc_pair_ctr
	// 	);
		
	// 	$this->update_member_accounts($data,"account_id = '{$account_id}'");
	// 	// update member earnings with final values
	// 	$data = array(
	// 		$bonus_type=>$bonus,
	// 		$gift_cheque_type=>$gift_cheques,
	// 	);
	// 	$this->update_member_earnings($data,"earning_id = {$member_earnings->earning_id}");
	
	// }

    function credit_points($account_id, $card_id, $from_account_id, $card_type_id, $position, $points)
    {
        print_r("account_id = " . $account_id . "\n\n");
        $account_data = $this->get_member_accounts(array(
            'account_id' => $account_id
        ));
        $account_data = $account_data[0];

        // get member earnings
        $account_earnings_type = $this->get_member_earnings_per_type(array(
            'member_id' => $account_data->member_id,
            'account_id' => $account_id,
            'type_id' => $card_type_id
        ));

        // create new if non existing
        if(sizeof($account_earnings_type) == 0)
        {
            $account_earning_data = array(
                'member_id' => $account_data->member_id,
                'account_id' => $account_id,
                'type_id' => $card_type_id
            );

            $this->insert_member_earnings_per_type($account_earning_data);

            $account_earnings_type = $this->get_member_earnings_per_type($account_earning_data);
            
            //add log
            $details_after = json_encode($account_earnings_type);
            $log_data = array(
                'member_id' => $account_data->member_id,
                'module_name' => "COMMISIONS",
                'table_name' => "cm_member_earnings_per_type",
                'action' => "ADD",
                'details_before' => '',
                'details_after' => $details_after,
                'remarks' => "create new member earnings per type"
            );
            $this->tracking_model->insert_logs('members', $log_data);
        }
        $account_earnings_type = $account_earnings_type[0];

        // get current gift cheques and funds account
        $gift_cheques = $account_earnings_type->gift_cheque;
        $bonus = $account_earnings_type->pairing_bonus;
        $temp_bonus = $bonus;

        // get member account pairing
        $account_pairing = $this->get_member_account_pairing(array(
            'member_id' => $account_data->member_id,
            'account_id' => $account_id,
            'card_type_id' => $card_type_id
        ));

        // create new if non existing
        if(sizeof($account_pairing) == 0)
        {
            $account_pairing_data = array(
                'member_id' => $account_data->member_id,
                'account_id' => $account_id,
                'card_type_id' => $card_type_id
            );

            $this->insert_member_account_pairing($account_pairing_data);

            $account_pairing = $this->get_member_account_pairing($account_pairing_data);

            //add log
            $details_after = json_encode($account_pairing);
            $log_data = array(
                'member_id' => $account_data->member_id,
                'module_name' => "COMMISIONS",
                'table_name' => "cm_member_account_pairing",
                'action' => "ADD",
                'details_before' => '',
                'details_after' => $details_after,
                'remarks' => "create new member account pairing"
            );
            $this->tracking_model->insert_logs('members', $log_data);
        }
        $account_pairing = $account_pairing[0];
        
        $pairs = $account_pairing->pair_count;
        $gc = $account_pairing->gc_count;
        $flushout = $account_pairing->flushout;
        $gc_pair_ctr = $account_pairing->gc_pair;

        $left = ($position == 'left')?($account_pairing->left_count+$points):$account_pairing->left_count;
        $right = ($position == 'left')?$account_pairing->right_count:($account_pairing->right_count+$points);

        $this->load->model('cards_model');
        $card_type = $this->cards_model->get_card_types(array(
            'card_type_id' => $card_type_id
        ));
        $card_type = $card_type[0];

        // get settings value
        $points_to_pair = $card_type->points_to_pair;
        $max_pairs = $this->settings->max_pairs;
        $pairs_ctr_to_gc = $this->settings->pairs_ctr_to_gc;
        $gift_cheque_bonus = strtolower($card_type->code)=='vp' ? ($this->settings->gift_cheque * 2) : $this->settings->gift_cheque; // double the gc if value pack

		//	// 20131112
		// new (from rf_members_achivements) / check first if member is included in the sm_featured_members table
		$featured_member_details = $this->contents_model->get_featured_member_by_member_id($account_data->member_id);
		
		if (!empty($featured_member_details)) {				
			$member_achievement_details = $this->contents_model->get_member_achievement_by_id($featured_member_details[0]->achievement_id);
			
			if (!empty($member_achievement_details)) {
				// get flushout/max pairs
				$max_pairs = $member_achievement_details->max_pairs;
			}
		}
		
         // get commission type based on transaction_code        
        $type = strtolower($card_type->code);
        if ($type=='sp') $transaction_code = 101;
        elseif ($type=='vp') $transaction_code = 102;
        elseif ($type=='tp') $transaction_code = 103;
        elseif ($type=='rs') $transaction_code = 104;
        else $transaction_code = 101; // default for new types unless changed?

        // insert credited points to logs
        $data = array(
            "member_id" => $account_data->member_id,
            "account_id" => $account_id,
            "account_status_id" => $account_data->account_status_id,
            "card_id" => $card_id,
            "from_account_id" => $from_account_id,
            "type" => strtoupper($card_type->code),
            "side" => strtoupper($position),
            "amount" => $points,
            "remarks" => "Credit " . strtoupper($card_type->code) . " Points from {$card_id}"
        );
        $this->tracking_model->insert_credit_points_logs($data);

        // check for pairings       
        $_left = floor($left / $points_to_pair);
        $_right = floor($right / $points_to_pair);

        if ($_left <= $_right) $new_pairs = $_left; 
        elseif ($_right < $_left) $new_pairs = $_right; 

        // get points to deduct     
        if ($new_pairs > 0) $points_to_deduct = $points_to_pair * $new_pairs;
        else $points_to_deduct = 0;

        // check account type, if tp give only half pairing bonus and half pairing points
        if (strtolower($card_type->code)=="sp" && $account_data->account_type_id==3) // if TP member
        {
            $is_trial_pack_account = 1;
        }
        else
        {
            $is_trial_pack_account = 0;         
        }       

        if (($is_trial_pack_account || strtolower($card_type->code) == "tp") && $new_pairs > 0)
        {
            $new_pairs = $new_pairs / 2;
            $new_pairs = number_format($new_pairs,2);
        } 

        // set new pairs, new left and new right
        if($points_to_deduct > 700) // hard checking for ceiling deduction
        {
            $log_data = array(
                    'member_id' => $account_data->member_id,
                    'module_name' => "COMMISIONS",
                    'table_name' => "cm_member_account_pairing",
                    'action' => "STOP",
                    'remarks' => "Stopped deducting points because deduction to points is too high " . $points_to_deduct
            );
            $this->tracking_model->insert_logs('members', $log_data);
        }

        $pairs = $pairs + $new_pairs;
        $left = $left - $points_to_deduct;
        $right = $right - $points_to_deduct;

        // process pairs
        //print_r($pairs . " > " . $max_pairs);
        if ($pairs > $max_pairs) // flushout
        {
            print_r("\nflushout\n");
            if($new_pairs > 0) $flushout++;

            // update points and counts
            $data = array(
                'pair_count' => $pairs,
                'left_count' => $left,
                'right_count' => $right,
                'gc_count' => $gc,
                'flushout' => $flushout,
                'gc_pair' => $gc_pair_ctr
            ); 

            $where = array(
                'member_id' => $account_data->member_id,
                'account_id' => $account_id,
                'card_type_id' => $card_type_id
            );

            $member_pairing = $this->get_member_account_pairing($where);
            $details_before = json_encode($member_pairing);

            // update member account pairings with final values
            $this->update_member_account_pairing($data, $where);

            $member_pairing = $this->get_member_account_pairing($where);
            $details_after = json_encode($member_pairing);

            $log_data = array(
                    'member_id' => $account_data->member_id,
                    'module_name' => "COMMISIONS",
                    'table_name' => "cm_member_account_pairing",
                    'action' => "UPDATE",
                    'details_before' => $details_before,
                    'details_after' => $details_after,
                    'remarks' => "Update member account pairing of member_id (".$account_data->member_id.") with final values"
            );
            $this->tracking_model->insert_logs('members', $log_data);

            // insert to credit logs
            $data = array(
                "member_id" => $account_data->member_id,
                "account_id" => $account_id,
                "account_status_id" => $account_data->account_status_id,
                "card_id" => $card_id,
                "from_account_id" => $from_account_id,
                "transaction_code" => $transaction_code,
                "type" => "FUNDS",
                "amount" => 0,
                "remarks" => "No pairing bonus due to flushout from {$card_id}"
            );
            $this->tracking_model->insert_acct_credit_logs($data);
        } 
        else // credit gc or pairing bonus
        {
            print_r("\nno flushout\n");

            if ($new_pairs>0) { // only add if there are new pairs
                print_r("\nnew pairs detected\n");
                $gc_pair_ctr += $new_pairs; // add to gc pair ctr;  

                if ($gc_pair_ctr>=$pairs_ctr_to_gc) // check if 5th pair based on gc_pair_ctr
                {
                    print_r("\ngc pair - $gc_pair_ctr = {$gc_pair_ctr} | $pairs_ctr_to_gc = {$pairs_ctr_to_gc}\n");
                    $gc++; 
                    $gc_pair_ctr = 0; // reset gc_pair_ctr;

                    $gift_cheques += $gift_cheque_bonus;                    

                    if ($type=='sp') $transaction_code = 106;
                    elseif ($type=='vp') $transaction_code = 107;
                    elseif ($type=='tp') $transaction_code = 108;
                    elseif ($type=='rs') $transaction_code = 109;
                    else $transaction_code = 106;

                    // insert to credit logs
                    $data = array(
                        "member_id" => $account_data->member_id,
                        "account_id"=> $account_id,
                        "account_status_id" => $account_data->account_status_id,
                        "card_id" => $card_id,
                        "from_account_id" => $from_account_id,
                        "transaction_code" => $transaction_code,
                        "type" => "GC",
                        "amount" => $gift_cheque_bonus,
                        "remarks" => "Credit " . strtoupper($card_type->code) . " gift cheque for fifth pair from {$card_id} to account {$account_id}"
                    );
					
					/*if ($account_data->sms_notification == 1) {
						$data["sms_sent"] = 1;
                        $data["tariff"] = $this->settings->notification_cost_sms;
                    }*/
					
                    $this->tracking_model->insert_acct_credit_logs($data);

                     // get credit log id
                    $credit_log_id = $this->tracking_model->insert_id();
					
					$member_details = $this->get_member_by_id($account_data->member_id);
					
					if ($account_data->sms_notification == 1) {
    
                        $sms_data = array(
                            "member_id"=>$account_data->member_id,
                            "account_id"=>$account_id,
                            "mobile_number"=>$member_details->mobile_number,
                            "type"=>"text-blast-generic-message",
                            "params"=>$data,
                            "is_charged"=>1,
                            "log_id"=>$credit_log_id,
                            "tariff"=>$this->settings->notification_cost_sms,
                        );

                        //send sms to user
                        Modules::run('jobs/notifications/send_tb_sms',$sms_data);
                        
                        // check cm_member_earnings_per_type - get current total
                        $earnings_where = array(
                            "member_id" => $account_data->member_id,
                            "account_id" => $account_id,
                            "type_id" => $card_type_id
                        );
                        
                        $member_earnings_per_type_details = $this->get_member_earnings_per_type($earnings_where);
                                        
                    }
					
                } 
                else
                {
                    print_r("\nnon gc pairing\n");
                    // get card type pairing bonuses
                    $card_type_bonuses = $this->cards_model->get_card_type_bonuses(array(
                        'card_type_id' => $card_type_id,
                        'commission_type' => 'PAIRING'
                    ));

                    foreach($card_type_bonuses as $card_type_bonus)
                    {
                        $method = "credit_pairing_bonus_" . strtolower($card_type_bonus->bonus_type);
                        print_r("\nrunning {$method} \n");

                        if(method_exists($this, $method))
                        {
                            // call pairing bonus type
                            $this->$method(array(
                                'from_account_id' => $from_account_id,
                                'account_id' => $account_id,
                                'qty_amount' => $card_type_bonus->qty_amount,
                                'card_id' => $card_id,
                                'card_code' => $card_type->code,
                                'transaction_code' => $transaction_code,
								'card_type_id' => $card_type_id
                            ));

                            if($card_type_bonus->bonus_type == "PRODUCT")
                            {
                                $product_price = $this->get_member_earnings_per_type(array('member_id' => $account_data->member_id, 'account_id' => $account_id, 'type_id' => $card_type_id));
                                if(sizeof($product_price))
                                {
                                    $bonus += $product_price[0]->pairing_bonus;
                                    $bonus -= $temp_bonus;
                                }
                            }
                        }

                        if(strtoupper($card_type_bonus->bonus_type) == "FUNDS") $bonus += $card_type_bonus->qty_amount;
                    }
                }
            }
       
			if ($points_to_deduct > 0)
			{
				$remarks = "Debit " . strtoupper($type) . " pairing points from {$card_id}";

				// insert debited left points to logs
				$data = array(
					"member_id" => $account_data->member_id,
					"account_id" => $account_id,
					"account_status_id" => $account_data->account_status_id,
					"card_id" => $card_id,
					"type" => strtoupper($card_type->code),
					"side" => "LEFT",
					"amount" => $points,
					"remarks" => $remarks
				);
				$this->tracking_model->insert_debit_points_logs($data);

				// insert debited right points to logs
				$data = array(
					"member_id" => $account_data->member_id,
					"account_id" => $account_id,
					"account_status_id" => $account_data->account_status_id,
					"card_id" => $card_id,
					"type" => strtoupper($card_type->code),
					"side" => "RIGHT",
					"amount" => $points,
					"remarks" => $remarks
				);
				$this->tracking_model->insert_debit_points_logs($data);         
			}

            // flushout override - zero out member's points
            //if($flushout > 0)
            // if ($pairs >= $max_pairs)
            // {
            //     $left = 0;
            //     $right = 0;
            // }

            $data = array(
                'pair_count' => $pairs,
                'left_count' => $left,
                'right_count' => $right,
                'gc_count' => $gc,
                'flushout' => $flushout,
                'gc_pair' => $gc_pair_ctr
            ); 

            $where = array(
                'member_id' => $account_data->member_id,
                'account_id' => $account_id,
                'card_type_id' => $card_type_id
            );

			$member_pairing = $this->get_member_account_pairing($where);
			$details_before = json_encode($member_pairing);

			// update member account pairings with final values
			$this->update_member_account_pairing($data, $where);

			$member_pairing = $this->get_member_account_pairing($where);
			$details_after = json_encode($member_pairing);

			$log_data = array(
					'member_id' => $account_data->member_id,
					'module_name' => "COMMISIONS",
					'table_name' => "cm_member_account_pairing",
					'action' => "UPDATE",
					'details_before' => $details_before,
					'details_after' => $details_after,
					'remarks' => "Update member account pairing of member_id (".$account_data->member_id.") with final values"
			);
			$this->tracking_model->insert_logs('members', $log_data);

			// update member earnings with final values
			$data = array(
				'pairing_bonus' => $bonus,
				'gift_cheque' => $gift_cheques
			);

			$earnings_where = array(
				'member_id' => $account_data->member_id,
				'account_id' => $account_id,
				'type_id' => $card_type_id
			);

			$member_earning = $this->get_member_earnings_per_type($earnings_where);    
			$details_before = json_encode($member_earning);

			$this->update_member_earnings_per_type($data, $earnings_where);

			$member_earning = $this->get_member_earnings_per_type($earnings_where);    
			$details_after = json_encode($member_earning);

			$log_data = array(
					'member_id' => $account_data->member_id,
					'module_name' => "COMMISIONS",
					'table_name' => "cm_member_earnings_per_type",
					'action' => "UPDATE",
					'details_before' => $details_before,
					'details_after' => $details_after,
					'remarks' => "Update member earning per type of member_id (".$account_data->member_id.") with final values"
			);
			$this->tracking_model->insert_logs('members', $log_data);
		}
	}

    function credit_pairing_bonus_funds($params)
    {
        $from_account_id = $params['from_account_id'];
        $account_id = $params['account_id'];
        $qty_amount = $params['qty_amount'];
        $card_id = $params['card_id'];
        $card_code = $params['card_code'];
        $transaction_code = $params['transaction_code'];
		$card_type_id = $params['card_type_id'];

        $account_data = $this->get_member_accounts(array(
            'account_id' => $account_id
        ));
        $account_data = $account_data[0];

        // set remarks
        $remarks = "Credit " . strtoupper($card_code) . " pairing bonus from {$card_id}";

        // insert to credit logs
        $data = array(
            "member_id" => $account_data->member_id,
            "account_id" => $account_id,
            "account_status_id" => $account_data->account_status_id,
            "card_id" => $card_id,
            "from_account_id" => $from_account_id,
            "transaction_code" => $transaction_code,
            "type" => "FUNDS",
            "amount" => $qty_amount,
            "remarks" => $remarks
        );
		
		/*if ($account_data->sms_notification == 1) {
			$data["sms_sent"] = 1;
            $data["tariff"] = $this->settings->notification_cost_sms;
        }*/
		
        $this->tracking_model->insert_acct_credit_logs($data);

        // get credit log id
        $credit_log_id = $this->tracking_model->insert_id();
		
		// get card type bonuses where bonus_type <> FUNDS using card_type_id
		$card_type_bonus_details = $this->cards_model->get_card_type_bonuses(array(
                        'card_type_id' => $card_type_id,
                        'commission_type' => 'PAIRING'
						));
		
		$card_type_bonus_count = $this->cards_model->get_card_type_bonuses_count(array(
                        'card_type_id' => $card_type_id,
                        'commission_type' => 'PAIRING'
						));				
						
		$member_details = $this->get_member_by_id($account_data->member_id);
		
		$d = 0;
		$remarks_message = "";

		// get qantity and bonus type  
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
			
		if ($account_data->sms_notification == 1) {
			
			//if ($card_code == 'P2P') {
			//	$data_ctbd =  array(
			//		"remarks" => "You earned a {$card_code} pairing bonus of {$card_type_bonus_detail->qty_amount} {$card_type_bonus_detail->bonus_type} (CPV) from {$card_id}."
			//	);	
			//} else {
			//	$data_ctbd =  array(
			//		"remarks" => "You earned a {$card_code} pairing bonus of {$card_type_bonus_detail->qty_amount} {$card_type_bonus_detail->bonus_type} from {$card_id}."
			//	);
			//}
			
			$data_ctbd =  array(
					"remarks" => "Your acct {$account_id} earned a {$card_code} pairing bonus of {$remarks_message} from {$card_id}."
				);
			
			$sms_data = array(
                "member_id"=>$account_data->member_id,
                "account_id"=>$account_id,
                "mobile_number"=>$member_details->mobile_number,
                "type"=>"text-blast-generic-message",
                "params"=>$data_ctbd,
                "is_charged"=>1,
                "log_id"=>$credit_log_id,
                "tariff"=>$this->settings->notification_cost_sms,
            );

			//send sms to user
			Modules::run('jobs/notifications/send_tb_sms',$sms_data);
			
			// check cm_member_earnings_per_type - get current total
			$earnings_where = array(
				"member_id" => $account_data->member_id,
				"account_id" => $account_id,
				"type_id" => $card_type_id
			);
			
			$member_earnings_per_type_details = $this->get_member_earnings_per_type($earnings_where);
			
			if (empty($member_earnings_per_type_details)) {
				// create record
				$data = array(
					"member_id" => $account_data->member_id,
					"account_id" => $account_id,
					"type_id" => $card_type_id,
					"pairing_bonus" => 0,
					"gift_cheque" => 0,
					"sms_pairing_total" => 1,
					"sms_referral_total" =>	0			
				);
				
				// insert to cm_member_earnings_per_type
				$this->insert_member_earnings_per_type($data);	
				
			} else {
			
				$sms_pairing_total =$member_earnings_per_type_details[0]->sms_pairing_total;
			
				$data = array(
					"sms_pairing_total" => $sms_pairing_total++
				);
				// update cm_member_earnings_per_type - sms_pairing_total		
				$this->update_member_earnings_per_type($data, $earnings_where);		
			}
			
		}
		
    }

    function credit_pairing_bonus_fpv($params = array())
    {
        $params['voucher_code'] = "FPV";
        $this->credit_bonus_voucher($params);
    }

    function credit_pairing_bonus_mpv($params = array())
    {
        $params['voucher_code'] = "MPV";
        $this->credit_bonus_voucher($params);
    }

    function credit_pairing_bonus_product($params = array())
    {
        $lowest_product_id = 0;

        $from_account_id = $params['from_account_id'];
        $account_id = $params['account_id'];
        $qty_amount = $params['qty_amount'];
        $card_id = $params['card_id'];
        $card_code = $params['card_code'];
        $transaction_code = $params['transaction_code'];

        $from_account_data = $this->get_member_accounts(array('account_id' => $from_account_id), null, null, array('member_id','node_address'));
        $from_account_data = $from_account_data[0];

        $account_data = $this->get_member_accounts(array('account_id' => $account_id));
        $account_data = $account_data[0];

        // get current node product id
        $current_node_product_id = 0;

        // generate product transaction from is_payment_transaction_products
        // changed 20140102
        //OLD $product_transaction = $this->payment_model->get_payment_transaction_products(array('card_id' => $card_id, 'package_product_id' => 0));
        //NEW
        $product_transaction = $this->cards_model->get_card_product_selections(array('card_id' => $card_id));
        if(sizeof($product_transaction) > 0)
        {
            print_r("\nfound card product selection\n");
            print_r($product_transaction);
            //$product_transaction = $product_transaction[0];
            //$current_node_product_id = $product_transaction->lowest_product;
            if($product_transaction[0]->qty == 1)
                $current_node_product_id = $this->items_model->get_lower_product_from_ids($product_transaction[0]->product_id, $product_transaction[1]->product_id, 'standard_retail');
            else
                $current_node_product_id = $product_transaction[0]->product_id;
        }
        else
        {
            print_r("\nno assigned products found on {$card_id}\n");
            // log
            $data = array(
                'member_id' => $account_data->member_id,
                'module_name' => "PRODUCT COMMISSION",
                'table_name' => 'cm_member_account_vouchers',
                'action' => 'INSERT',
                'details_before' => '',
                'details_after' => '',
                'remarks' => "Error on product commission for {$account_id} from {$from_account_id} on card id {$card_id}"
            );
            $this->tracking_model->insert_logs('members',$data);
            return;
        }

        // get partner node product id
        $partner_node_product_id = 0;

        //$partner_position
        $head_len = strlen($account_data->node_address);
        $trigger_position = substr($from_account_data->node_address, $head_len, 1);
        $partner_position = ($trigger_position==1)?2:1;
        $partner_search_node = $account_data->node_address . $partner_position;
        $partner_node_account_id = ''; 

        // check member_account_credited_transaction // remove credited transaction limiter
        // $credited_products = $this->get_member_account_credited_transaction(array('account_id' => $account_id));
        // if(sizeof($credited_products) > 0)
        // {
        //     print_r("\nfound credited transaction\n");
        //     print_r($credited_products);
        //     // get lowest price
        //     $partner_node_product_id = $credited_products[0]->product_id;
        //     $lowest_price = $this->items_model->get_product_by_id($partner_node_product_id);
        //     $lowest_price = $lowest_price->standard_retail_price;
        //     foreach($credited_products as $credited_product)
        //     {
        //         $tmp_price = $this->items_model->get_product_by_id($credited_product->product_id);
        //         $tmp_price = $tmp_price->standard_retail_price;
        //         if($tmp_price < $lowest_price)
        //         {
        //             $partner_node_product_id = $credited_product->product_id;
        //             $lowest_price = $tmp_price;
        //         }
        //     }
        // }
        // else
        // {
            //$where = "node_address LIKE '{$partner_search_node}%' AND status = 'UNUSED' ";
            $where = "node_address LIKE '{$partner_search_node}%' "; // remove USED/UNUSED limiter
            print_r("\ntrying uncredited products {$where}\n");
            $order = "insert_timestamp DESC";
            $uncredited_products = $this->get_member_account_product_transaction($where, null, $order);

            print_r("\ncurrent uncredited products found.\n");
            print_r($uncredited_products);
            print_r("\nremoving credited if any..\n");

            // remove credited (if any) as replacement of being "USED" and "UNUSED"
            $tmp = array();
            foreach($uncredited_products as $uncredited_product)
            {
                $credited_where = "account_id = '{$account_id}' AND (left_account_id = '{$uncredited_product->account_id}' OR right_account_id = '{$uncredited_product->account_id}') ";
                print_r("\n" . $credited_where . "\n");
                $credited_product_data = $this->get_member_account_credited_transaction($credited_where);
                if(sizeof($credited_product_data) == 0)
                {
                    $tmp[] = $uncredited_product;
                }
                else
                {
                    print_r("\nfound credited.. removing from pool..\n");
                }
            }
            $uncredited_products = $tmp;
            print_r("\nfiltered uncredited products.\n");
            print_r($uncredited_products);
            print_r("\n");

            if(sizeof($uncredited_products) > 0)
            {
                print_r("\nuncredited product found\n");
                print_r($uncredited_products);
                $partner_node_product_id = $uncredited_products[0]->product_id;
                $partner_node_account_id = $uncredited_products[0]->account_id;
                $used_uncredited_product = $uncredited_products[0]->product_transaction_id;
                print_r("\ninitial $used_uncredited_product = {$used_uncredited_product}\n");
                $lowest_price = $this->items_model->get_product_by_id($partner_node_product_id);
                $lowest_price = $lowest_price->standard_retail_price;
                
                foreach($uncredited_products as $uncredited_product)
                {
                    $tmp_price = $this->items_model->get_product_by_id($uncredited_product->product_id);
                    $tmp_price = $tmp_price->standard_retail_price;
                    if($tmp_price < $lowest_price)
                    {
                        $partner_node_product_id = $uncredited_product->product_id;
                        $partner_node_account_id = $uncredited_product->account_id;
                        $used_uncredited_product = $uncredited_product->product_transaction_id;
                        $lowest_price = $tmp_price;
                        print_r("\nchanged $used_uncredited_product = {$used_uncredited_product}\n");
                    }
                }

                print_r("\nfinal $used_uncredited_product = {$used_uncredited_product}\n");

                // tag product transaction as used
                $this->update_member_account_product_transaction(array(
                    'status' => 'USED'
                ), array(
                    'product_transaction_id' => $used_uncredited_product
                ));

                print_r($this->db->last_query());
                print_r("\nthen mark ok trigger node...\n");

                // tag product transaction as used from trigger node
                $this->update_member_account_product_transaction(array(
                    'status' => 'USED'
                ), array(
                    'card_id' => $card_id,
                    'account_id' => $from_account_id
                ));

                print_r($this->db->last_query());
                print_r("\n");
            }
            else
            {
                print_r("\nno uncredited product found. check mismatch log\n");
                // log
                $tracking_arr = array(
                    'trigger_card_id' => $card_id,
                    'trigger_account_id' => $account_id,
                    'missing_position' => ($partner_position==2)?'LEFT':'RIGHT',
                    'waiting_account_id' => $from_account_id,
                    'waiting_product_id' => $current_node_product_id
                );
                $data = array(
                    'member_id' => $account_data->member_id,
                    'module_name' => "P2P MISMATCH",
                    'table_name' => 'cm_member_account_credited_transaction',
                    'action' => 'INSERT',
                    'details_before' => '',
                    'details_after' => json_encode($tracking_arr),
                    'remarks' => "Mismatch Error. Pair Partner was not found for account id {$account_id} on card id {$card_id}"
                );
                $this->tracking_model->insert_logs('members',$data);

                // add to mismatch table
                $log_id = $this->insert_id();
                $data = array(
                    "log_id" => $log_id,
                    "status" => "PENDING"
                );

                $this->insert_member_p2p_mismatch($data);

                return;
            }
        // }

        // insert credited transaction
        print_r("\n$current_node_product_id: {$current_node_product_id}\n");
        print_r("\n$partner_node_product_id: {$partner_node_product_id}\n");
        $lowest_product_id = $this->items_model->get_lower_product_from_ids($current_node_product_id, $partner_node_product_id, 'standard_retail');
        $lowest_product = $this->items_model->get_product_by_id($lowest_product_id);
        $lowest_price = $lowest_product->standard_retail_price; 
        $this->insert_member_account_credited_transaction(array(
            'account_id' => $account_id,
            'left_account_id' => ($partner_position==2)?$partner_node_account_id:$from_account_id,
            'right_account_id' => ($partner_position==1)?$partner_node_account_id:$from_account_id,
            'product_id' => $lowest_product_id
        ));

        // generate account voucher
        $voucher_id = $this->vouchers_model->generate_account_voucher(3, $account_data->member_id, $account_id, $from_account_id);
        $this->vouchers_model->insert_member_account_voucher_products(array(
            'voucher_id' => $voucher_id,
            'product_id' => $lowest_product_id,
            'quantity' => 1
        ));

        /***** insert earnings *****/
        $earning = 0; 
        //get_card_type_id 
        
        $card_type = $this->cards_model->get_card_type_by_code( $card_code);
        if(sizeof($card_type)>0)
        {   
            $card_type_id = $card_type->card_type_id;

            
            //get_current_earning
            $earnings_where = array('member_id'=>$account_data->member_id, 'account_id'=>$account_id, 'type_id'=>$card_type_id);
            $current_earnings = $this->get_member_earnings_per_type($earnings_where);
            if(sizeof($current_earnings)>0)
            {
                $current_earning = $current_earnings[0];
                $earning = $current_earning->pairing_bonus;
            }

            $earning += $lowest_price;
            
            $details_before = json_encode($current_earnings);

            $this->update_member_earnings_per_type(array('pairing_bonus'=>$earning), $earnings_where);

            //$member_earning = $this->get_current_earning_per_type($earnings_where); 
            $member_earning = $this->get_member_earnings_per_type($earnings_where); 
            $details_after = json_encode($member_earning);

            $log_data = array(
                    'member_id' => $account_data->member_id,
                    'module_name' => "COMMISIONS",
                    'table_name' => "cm_member_earnings_per_type",
                    'action' => "UPDATE",
                    'details_before' => $details_before,
                    'details_after' => $details_after,
                    'remarks' => "Update member earning per type of member_id (".$account_data->member_id.") with with lowest price of product(".$lowest_product_id."-".$lowest_product->product_name.")"
            );
            $this->tracking_model->insert_logs('members', $log_data);

            $data = array(
              "member_id"=>$account_data->member_id,
              "account_id"=>$account_id,
              "account_status_id"=>$account_data->account_status_id,
              "card_id"=>$card_id,
              "from_account_id"=>$from_account_id,
              "transaction_code"=>$transaction_code,
              "type"=>"PRODUCT",
              "amount"=>$lowest_price,
              // "level" => $counter, // counter not defined and not needed for P2P pairing (20140202)
			  //"sms_sent" =>$account_data->sms_notification,
              //"tariff" =>$this->settings->notification_cost_sms,
              "remarks"=> "You earned a P2P pairing bonus product - ".$lowest_product->product_name." with SRP of {$lowest_price} from {$card_id}"
            );
            
            if ($account_data->sms_notification == 1) {
    			$data["sms_sent"] = 1;
                $data["tariff"] = $this->settings->notification_cost_sms;
    		}
            
            $this->tracking_model->insert_acct_credit_logs($data);
            
            // get credit log id
            $credit_log_id = $this->tracking_model->insert_id();

            // get credit log id
            $credit_log_id = $this->tracking_model->insert_id();
            
			$member_details = $this->get_member_by_id($account_data->member_id);
			
			if ($account_data->sms_notification == 1) {
			
				$data_ctbd =  array(
					"remarks" => "Your acct {$account_id} earned (P-P)2 pairing bonus of 1 CPV from {$card_id}."
				);
			
				$sms_data = array(
                    "member_id"=>$account_data->member_id,
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

    }

    function credit_bonus_voucher($params = array())
    {
        $from_account_id = $params["from_account_id"];
        $account_id = $params["account_id"];
        $voucher_count = $params["qty_amount"];
        $card_id = $params["card_id"];
        $voucher_code = $params["voucher_code"];

        // check if company account id
        $company_accounts = $this->get_company_account_ids();
        if(in_array($account_id, $company_accounts)) return; // end method if company account // no vouchers given

        $voucher_type = $this->vouchers_model->get_account_voucher_types(array(
            'code' => $voucher_code
        ));
        $voucher_type = $voucher_type[0];

        $account = $this->get_member_accounts(array(
            'account_id' => $account_id
        ));
        $account = $account[0];

        for($i = 0; $i < $voucher_count; $i++) $this->vouchers_model->generate_account_voucher($voucher_type->voucher_type_id, $account->member_id, $account->account_id, $from_account_id);
    }

    function credit_gc_to_member($member_id, $account_id, $gift_cheque_bonus, $type)
    {
        // update gc to cm_members
        $member = $this->get_member_by_id($member_id);
        $details_before = json_encode($member);

        $new_member_gc = $member->gift_cheques + $gift_cheque_bonus;
        $this->update_member(array(
            'gift_cheques' => $new_member_gc
        ),array(
            'member_id' => $member_id
        ));

        $member = $this->get_member_by_id($member_id);
        $details_after = json_encode($member);

        // log
        $data = array(
            'member_id' => $member_id,
            'module_name' => strtoupper($type) . " CREDIT CM MEMBER GC",
            'table_name' => 'cm_members',
            'action' => 'UPDATE',
            'details_before' => $details_before,
            'details_after' => $details_after,
            'remarks' => "Credit " . strtoupper($type) . " gift cheque for fifth pair from {$card_id} on members"
        );
        $this->tracking_model->insert_logs('members',$data);

        // update gc to cm_member_accounts
        $member_account = $this->get_member_account_by_account_id($account_id);
        $details_before = json_encode($member_account);

        $new_gc_member_account = $member_account->gift_cheques + $gift_cheque_bonus;
        $this->update_member_accounts(array(
            'gift_cheques' => $new_gc_member_account
        ),array(
            'account_id' => $account_id
        ));

        $member_account = $this->get_member_account_by_account_id($account_id);
        $details_after = json_encode($member_account);

        // log
        $data = array(
            'member_id' => $member_id,
            'module_name' => strtoupper($type) . " CREDIT CM MEMBER ACCOUNTS GC",
            'table_name' => 'cm_member_accounts',
            'action' => 'UPDATE',
            'details_before' => $details_before,
            'details_after' => $details_after,
            'remarks' => "Credit " . strtoupper($type) . " gift cheque for fifth pair from {$card_id} on member accounts"
        );
        $this->tracking_model->insert_logs('members',$data);
    }

    // Add to Member Accounts Points
    function _credit_points_2($account_id,$card_id,$from_account_id,$type,$position,$points, $timestamp)
    {
        $this->load->model('tracking_model');

        $member_account = $this->get_member_account_by_account_id($account_id);

        // check account type, if tp give only half pairing bonus and half pairing points

        if ($type=="sp" && $member_account->account_type_id==3) // if TP member
        {
            $is_trial_pack_account = 1;
        }
        else
        {
            $is_trial_pack_account = 0;         
        }       

        $pairs_type = "pairs_{$type}";
        $left_type = "left_{$type}";
        $right_type = "right_{$type}";
        $gc_pair_ctr_type = "gc_pair_ctr_{$type}";
        $gc_type = "gc_{$type}";
        $flushout_type = "flushout_{$type}";

        if ($is_trial_pack_account) 
        {
            $points_to_pair_type = "tp_points_to_pair";
            $pairing_bonus_type = "tp_pairing_bonus";       
            $bonus_type = "pairing_bonus_tp";
            $gift_cheque_type = "gift_cheque_tp";
                
        }
        else
        {
            $points_to_pair_type = "{$type}_points_to_pair";
            $pairing_bonus_type = "{$type}_pairing_bonus";          
            $bonus_type = "pairing_bonus_{$type}";
            $gift_cheque_type = "gift_cheque_{$type}";      
        }
        

        if($type == "tp") // credit points to sp when type is tp
        {
            $pairs_type = "pairs_sp";
            $left_type = "left_sp";
            $right_type = "right_sp";
            $gc_type = "gc_sp";
            $flushout_type = "flushout_sp";
            $gc_pair_ctr_type = "gc_pair_ctr_sp";
        }
        
        $where = "account_id = '{$account_id}'";
        $limit = array("rows"=>1,"offset"=>0);
        $order = NULL;
        $member_earnings = $this->get_member_earnings($where,$limit,$order);
        $member_earnings = $member_earnings[0];

        // get current gift cheques and funds account
        $gift_cheques = $member_earnings->$gift_cheque_type;
        $bonus = $member_earnings->$bonus_type;     
                
        $pairs =  $member_account->$pairs_type;
        $gc = $member_account->$gc_type;
        $flushout = $member_account->$flushout_type;        
        $gc_pair_ctr = $member_account->$gc_pair_ctr_type;      
        
        if ($position=='left') 
        {
            $left = $member_account->$left_type + $points;
            $right = $member_account->$right_type;          
        }
        else
        {           
            $left = $member_account->$left_type;
            $right = $member_account->$right_type + $points;            
        }
        
        // get settings value
        $points_to_pair = $this->settings->$points_to_pair_type;
        $max_pairs = $this->settings->max_pairs;
        $pairs_ctr_to_gc = $this->settings->pairs_ctr_to_gc;
        $pairing_bonus = $this->settings->$pairing_bonus_type;
        $gift_cheque_bonus = $type=='vp' ? ($this->settings->gift_cheque * 2) : $this->settings->gift_cheque; // double the gc if value pack

        
        // get commission type based on transaction_code        
        if ($type=='sp') $transaction_code = 101;
        elseif ($type=='vp') $transaction_code = 102;
        elseif ($type=='tp') $transaction_code = 103;
        elseif ($type=='rs') $transaction_code = 104;
            
        // insert credited points to logs
        $data = array(
            "member_id"=>$member_account->member_id,
            "account_id"=>$member_account->account_id,
			"account_status_id"=>$member_account->account_status_id,
            "card_id"=>$card_id,
            "from_account_id"=>$from_account_id,
            "type"=>strtoupper($type),
            "side"=>strtoupper($position),
            "amount"=>$points,
            "remarks"=>"Credit " . strtoupper($type) . " Points from {$card_id}",
            "insert_timestamp" => $timestamp,
        );
        
        $this->tracking_model->insert_credit_points_logs($data);
                
        // check for pairings       
        $_left = floor($left / $points_to_pair);
        $_right = floor($right / $points_to_pair);
        
        if ($_left<=$_right) $new_pairs = $_left; 
        elseif ($_right<$_left) $new_pairs = $_right; 
        
        // get points to deduct     
        if ($new_pairs>0) 
            $points_to_deduct = $points_to_pair * $new_pairs;
        else
            $points_to_deduct = 0;
            
        if (($is_trial_pack_account || $type == "tp") && $new_pairs>0)
        {
            $new_pairs = $new_pairs / 2;
            $new_pairs = number_format($new_pairs,2);
        } 
        
        // set new pairs, new left and new right
        $pairs = $pairs + $new_pairs;
        $left = $left - $points_to_deduct;
        $right = $right - $points_to_deduct;
                
        // process pairs
        if ($pairs > $max_pairs) // flushout
        {
            $flushout++; // do nothing

            // insert to credit logs
            $data = array(
                "member_id"=>$member_account->member_id,
                "account_id"=>$member_account->account_id,
				"account_status_id"=>$member_account->account_status_id,
                "card_id"=>$card_id,
                "from_account_id"=>$from_account_id,
                "transaction_code"=>$transaction_code,
                "type"=>"FUNDS",
                "amount"=>0,
                "remarks"=>"No pairing bonus due to flushout from {$card_id}",
                "insert_timestamp" => $timestamp,
            );
            $this->tracking_model->insert_acct_credit_logs($data);
            
        } 
        else // credit gc or pairing bonus
        {
            if ($new_pairs>0) { // only add if there are new pairs
                
                $gc_pair_ctr += $new_pairs; // add to gc pair ctr;  

                if ($gc_pair_ctr>=$pairs_ctr_to_gc) // check if 5th pair based on gc_pair_ctr
                {
                    $gc++; 
                    $gc_pair_ctr = 0; // reset gc_pair_ctr;

                    $gift_cheques += $gift_cheque_bonus;                    
                    
                    if ($type=='sp') $transaction_code = 106;
                    elseif ($type=='vp') $transaction_code = 107;
                    elseif ($type=='tp') $transaction_code = 108;
                    elseif ($type=='rs') $transaction_code = 109;                   
					
                    // insert to credit logs
                    $data = array(
                        "member_id"=>$member_account->member_id,
                        "account_id"=>$member_account->account_id,
						"account_status_id"=>$member_account->account_status_id,
                        "card_id"=>$card_id,
                        "from_account_id"=>$from_account_id,
                        "transaction_code"=>$transaction_code,
                        "type"=>"GC",
                        "amount"=>$gift_cheque_bonus,
                        "remarks"=>"Credit " . strtoupper($type) . " gift cheque for fifth pair from {$card_id}",
                        "insert_timestamp" => $timestamp,
                    );
                    $this->tracking_model->insert_acct_credit_logs($data);
					
					//$member_details = $this->get_member_by_id($member_account->member_id);
					//
					//if ($member_account->sms_notification == 1) {
	                //
					//	$sms_data = array(
					//		"member_id"=>$member_account->member_id,
					//		"mobile_number"=>$member_details->mobile_number,
					//		"type"=>"text-blast-generic-message",
					//		"params"=>$data
					//	);
                    //
					//	//send sms to user
					//	Modules::run('jobs/notifications/send_sms',$sms_data);
					//}
					
                } 
                else
                {               
                    // set remarks
                    $remarks = "Credit ". strtoupper($type) ." pairing bonus from {$card_id}";

                    $bonus += $pairing_bonus;

                    // insert to credit logs
                    $data = array(
                        "member_id"=>$member_account->member_id,
                        "account_id"=>$member_account->account_id,
						"account_status_id"=>$member_account->account_status_id,
                        "card_id"=>$card_id,
                        "from_account_id"=>$from_account_id,
                        "transaction_code"=>$transaction_code,
                        "type"=>"FUNDS",
                        "amount"=>$pairing_bonus,
                        "remarks"=>$remarks,
                        "insert_timestamp" => $timestamp,
                    );
                    $this->tracking_model->insert_acct_credit_logs($data);
					
					//$member_details = $this->get_member_by_id($member_account->member_id);
					//
					//if ($member_account->sms_notification == 1) {
	                //
					//	$sms_data = array(
					//		"member_id"=>$member_account->member_id,
					//		"mobile_number"=>$member_details->mobile_number,
					//		"type"=>"text-blast-generic-message",
					//		"params"=>$data
					//	);
                    //
					//	//send sms to user
					//	Modules::run('jobs/notifications/send_sms',$sms_data);
					//}
					
                }           
            }           
        }
        
        if ($points_to_deduct>0) 
        {
            $remarks = "Debit " . strtoupper($type) . " pairing points from {$card_id}";
            
            // insert debited left points to logs
            $data = array(
                "member_id"=>$member_account->member_id,
                "account_id"=>$member_account->account_id,
				"account_status_id"=>$member_account->account_status_id,
                "card_id"=>$card_id,
                "type"=>strtoupper($type),
                "side"=>"LEFT",
                "amount"=>$points,
                "remarks"=>$remarks,
                "insert_timestamp" => $timestamp,
            );

            $this->tracking_model->insert_debit_points_logs($data);

            // insert debited right points to logs
            $data = array(
                "member_id"=>$member_account->member_id,
                "account_id"=>$member_account->account_id,
				"account_status_id"=>$member_account->account_status_id,
                "card_id"=>$card_id,
                "type"=>strtoupper($type),
                "side"=>"RIGHT",
                "amount"=>$points,
                "remarks"=>$remarks,
                "insert_timestamp" => $timestamp,
            );

            $this->tracking_model->insert_debit_points_logs($data);         
        }

        // update member account with final values
        $data = array(
            $pairs_type=>$pairs,
            $left_type=>$left,
            $right_type=>$right,
            $gc_type=>$gc,
            $flushout_type=>$flushout,
            $gc_pair_ctr_type=>$gc_pair_ctr
        );
        
        $this->update_member_accounts($data,"account_id = '{$account_id}'");
        // update member earnings with final values
        $data = array(
            $bonus_type=>$bonus,
            $gift_cheque_type=>$gift_cheques,
        );
        $this->update_member_earnings($data,"earning_id = {$member_earnings->earning_id}");
        
    }

	
	// unregistered members
    function get_unregistered_members($where = null, $limit = null, $orderby = null, $fields = null) {
        $query = $this->fetch('unregistered_members', $fields, $where, $orderby, $limit);
        $row = $query->result();
        $query->free_result();
        return $row;
    }
    
	function insert_unregistered_members ($data) {
        return $this->insert('unregistered_members', $data);
    }
    
    function update_unregistered_members ($data, $where) {
        return $this->update('unregistered_members', $data, $where);
    }
	    
    function delete_unregistered_members($where) {
        return $this->delete('unregistered_members', $where);
    }    
    function get_unregistered_members_count($where = null) {
        // do a sql count instead of row count
        $query = $this->fetch('unregistered_members', 'count(1) as cnt', $where);
        $row = $query->first_row();
        $query->free_result();
        return $row->cnt;
    }
	
	function get_discounts($where = null, $limit = null, $orderby = null, $fields = null)
	{
		$query = $this->fetch('discounts', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
	}
			
	function get_member_order_reports($where = null, $limit = null, $orderby = null, $fields = null)
	{
		$query = $this->fetch('member_order_reports_view', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
	}
	
	function get_member_account_types($where = null, $limit = null, $orderby = null, $fields = null)
	{
		$query = $this->fetch('member_account_types', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
	}
	
	function get_member_account_type_by_id($account_type_id)
	{
		// $result = $this->get_member_account_types(array('account_type_id' => $account_type_id));
		// $row = NULL;
		// if (count($result) > 0)
		// {
		// 	$row = $result[0];
		// }
		// return $row;
        $card_type = $this->cards_model->get_card_types(array('card_type_id' => $account_type_id));
		//var_dump($account_type_id . '|' . $card_type);
		
        $card_type = $card_type[0];
        $ret = new stdClass;
        $ret->account_type_id = $card_type->card_type_id;
        $ret->account_type = $card_type->code;
        $ret->description = $card_type->name;
        return $ret;
	}

	function get_member_account_type_by_type($account_type)
	{
		// $result = $this->get_member_account_types(array('account_type' => $account_type));
		// $row = NULL;
		// if (count($result) > 0)
		// {
		// 	$row = $result[0];
		// }
        // return $row;
        $card_type = $this->cards_model->get_card_types(array('code' => $account_type));
        $card_type = $card_type[0];
        $ret = new stdClass;
        $ret->account_type_id = $card_type->card_type_id;
        $ret->account_type = $card_type->code;
        $ret->description = $card_type->name;
        return $ret;
	}

	
	function get_member_account_statuses($where = null, $limit = null, $orderby = null, $fields = null)
	{
		$query = $this->fetch('member_account_status', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
	}
	
	function get_member_account_status_by_id($account_status_id)
	{
		$result = $this->get_member_account_statuses(array('account_status_id' => $account_status_id));
		$row = NULL;
		if (count($result) > 0)
		{
			$row = $result[0];
		}
		return $row;
	}
	
	//groups
	function get_groups($where = null, $limit = null, $orderby = null, $fields = null)
	{
		$query = $this->fetch('groups', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
	}
	
	function insert_group($data)
	{
		return $this->insert('groups', $data);
	}

	function update_group($data, $where)
	{
		return $this->update('groups', $data, $where);
	}

	function delete_groups($where)
	{
		return $this->delete('groups', $where);
	}

	function get_groups_count($where = null)
	{
		// do a sql count instead of row count
		$query = $this->fetch('groups', 'count(1) as cnt', $where);
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}

	function get_groups_by_id($group_id)
	{
		$result = $this->get_groups(array('group_id' => $group_id));
		$row = NULL;
		if (count($result) > 0)
		{
			$row = $result[0];
		}
		return $row;
	}

	function get_group_by_group_name($group_name)
	{
		$result = $this->get_groups(array('group_name' => $group_name));
		$row = NULL;
		if (count($result) > 0)
		{
			$row = $result[0];
		}
		return $row;	
	}
	
	function get_group_by_leader_member_id($group_leader_member_id)
	{
		$result = $this->get_groups(array('group_leader_member_id' => $group_leader_member_id));
		$row = NULL;
		if (count($result) > 0)
		{
			$row = $result[0];
		}
		return $row;	
	}
	
	//member groups
	function get_member_groups($where = null, $limit = null, $orderby = null, $fields = null)
	{
		$query = $this->fetch('member_groups', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
	}
	
	function insert_member_group($data)
	{
		return $this->insert('member_groups', $data);
	}

	function update_member_group($data, $where)
	{
		return $this->update('member_groups', $data, $where);
	}

	function delete_member_groups($where)
	{
		return $this->delete('member_groups', $where);
	}

	function get_member_groups_count($where = null)
	{
		// do a sql count instead of row count
		$query = $this->fetch('member_groups', 'count(1) as cnt', $where);
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}

    function get_member_group_by_group_id($group_id) {
        $result = $this->get_member_groups(array('group_id' => $group_id));
        $row = NULL;
        if (count($result) > 0) {
            $row = $result[0];
        }
        return $row;
    }

    function get_member_group_by_group_name($group_name) {
        $result = $this->get_member_groups(array('group_name' => $group_name));
        $row = NULL;
        if (count($result) > 0) {
            $row = $result[0];
        }
        return $row;
    }


	function get_transaction_codes($where = null, $limit = null, $orderby = null, $fields = null)
	{
		$query = $this->fetch('transaction_codes', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
	}

	//member earnings
	function get_member_earnings($where = null, $limit = null, $orderby = null, $fields = null)
	{
		$query = $this->fetch('member_earnings', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
	}

	function get_member_earnings_count($where = null) {
        // do a sql count instead of row count
        $query = $this->fetch('member_earnings', 'count(1) as cnt', $where);
        $row = $query->first_row();
        $query->free_result();
        return $row->cnt;
    }

	function insert_member_earnings($data)
	{
		return $this->insert('member_earnings', $data);
	}

	function update_member_earnings($data, $where)
	{
		return $this->update('member_earnings', $data, $where);
	}

	function delete_member_earnings($where)
	{
		return $this->delete('member_earnings', $where);
	}

    //member_earnings_per_type
    function get_member_earnings_per_type($where = null, $limit = null, $orderby = null, $fields = null)
    {
        $query = $this->fetch('member_earnings_per_type', $fields, $where, $orderby, $limit);
        $row = $query->result();
        $query->free_result();
        return $row;
    }

    function insert_member_earnings_per_type($data)
    {
        return $this->insert('member_earnings_per_type', $data);
    }

    function update_member_earnings_per_type($data, $where)
    {
        return $this->update('member_earnings_per_type', $data, $where);
    }

    function delete_member_earnings_per_type($where)
    {
        return $this->delete('member_earnings_per_type', $where);
    }
	
	
	/* -- FOR MEMBER VERIFICATION -- */
	function get_member_verification($where = null, $limit = null, $orderby = null, $fields = null) {
        $query = $this->fetch('member_verification', $fields, $where, $orderby, $limit);
        $row = $query->result();
        $query->free_result();
        return $row;
    }

	function get_member_verification_count($where = null) {
        // do a sql count instead of row count
        $query = $this->fetch('member_verification', 'count(1) as cnt', $where);
        $row = $query->first_row();
        $query->free_result();
        return $row->cnt;
    }
	
	function get_member_verification_by_id($member_id) {
        $result = $this->get_member_verification(array('member_id' => $member_id));
        $row = NULL;
        if (count($result) > 0) {
            $row = $result[0];
        }
        return $row;
    }

	function get_member_verification_by_email_code($code) {
        $result = $this->get_member_verification(array('email_code' => $code));
        $row = NULL;
        if (count($result) > 0) {
            $row = $result[0];
        }
        return $row;
    }

    function get_member_verification_by_rf_id_code($code) {
        $result = $this->get_member_verification(array('rf_id_code' => $code));
        $row = NULL;
        if (count($result) > 0) {
            $row = $result[0];
        }
        return $row;
    }

    function get_member_verification_by_paycard_code($code) {
        $result = $this->get_member_verification(array('paycard_code' => $code));
        $row = NULL;
        if (count($result) > 0) {
            $row = $result[0];
        }
        return $row;
    }

    function get_member_verification_by_mobile_code($code) {
        $result = $this->get_member_verification(array('mobile_code' => $code));
        $row = NULL;
        if (count($result) > 0) {
            $row = $result[0];
        }
        return $row;
    }

	function insert_member_verification($data) 
    {
        return $this->insert('member_verification', $data);
    }
	
	function update_member_verification($data, $where) 
    {
        return $this->update('member_verification', $data, $where);
    }

	function delete_member_verification($where) 
    {
        return $this->update('member_verification', $where);
    }

    // get_all_member_earnings
    function get_all_member_earnings($member_id, $account_id = "")
    {
        $data = array();

        //$sql_all_earnings = "SELECT
        //            sum(`referral_bonus`) as `referral_bonus`,
        //            sum(`pairing_bonus_sp`) as `pairing_bonus_sp`,
        //            sum(`gift_cheque_sp`) as `gift_cheque_sp`,
        //            sum(`pairing_bonus_vp`) as `pairing_bonus_vp`,
        //            sum(`gift_cheque_vp`) as `gift_cheque_vp`,
        //            sum(`pairing_bonus_tp`) as `pairing_bonus_tp`,
        //            sum(`gift_cheque_tp`) as `gift_cheque_tp`,
        //            sum(`pairing_bonus_rs`) as `pairing_bonus_rs`,
        //            sum(`gift_cheque_rs`) as `gift_cheque_rs`,
        //            sum(`unilevel_commission`) as `unilevel_commission`
        //        FROM `cm_member_earnings` ";
		
		$sql_all_earnings = "SELECT
                    sum(`referral_bonus`) as `referral_bonus`,
                    sum(`unilevel_commission`) as `unilevel_commission`
                FROM `cm_member_earnings` ";
		
        if($account_id != ""){
            $sql_all_earnings .= "WHERE `member_id` = {$member_id} AND `account_id` = '{$account_id}'
                GROUP BY `account_id`";
        }else{
            $sql_all_earnings .= "WHERE `member_id` = {$member_id}
                GROUP BY `member_id`";
        }
        
        $query = $this->db->query($sql_all_earnings);
        $all_earnings = $query->row();
        $query->free_result();

        $all_earnings = $this->get_current_earning($member_id,$account_id);

        //20140324
        //get pairing and gc of all cards that display on dashboard 
        $card_types_where = array('display_on_dashboard'=>'1');
        $card_types = $this->cards_model->get_card_types($card_types_where);
        foreach($card_types as $card_type)
        {
            $earnings_tag = "pairing_and_gc_" . strtolower($card_type->code);
            $temp_card_type_earnings = $this->get_current_earning_per_type($member_id,$account_id,$card_type->card_type_id);
            $data[$earnings_tag] = $temp_card_type_earnings;
        }

		// 20131120
		// SP pairing and gc
        $sp_card = $this->cards_model->get_card_type_by_code('SP');
        $sp_card_id = $sp_card->card_type_id;
		/*$sql_pairing_and_gc_sp = "SELECT 
									`type_id`, 
									SUM(`pairing_bonus`) AS pairing_bonus, 
									SUM(`gift_cheque`) AS `gift_cheque` 
								FROM
									`cm_member_earnings_per_type`
								WHERE  type_id = '$sp_card_id' ";
								
		if($account_id != ""){
            $sql_pairing_and_gc_sp .= "AND `member_id` = {$member_id} AND `account_id` = '{$account_id}'
                GROUP BY `type_id`";
        } else {
            $sql_pairing_and_gc_sp .= "AND `member_id` = {$member_id}
                GROUP BY `type_id`";
        }	
        
		$query = $this->db->query($sql_pairing_and_gc_sp);
        $pairing_and_gc_sp = $query->row();
        $query->free_result();	
		
		if(empty($pairing_and_gc_sp) || is_null($pairing_and_gc_sp)) {
			$pairing_and_gc_sp = array();
			$pairing_and_gc_sp = (object)$pairing_and_gc_sp;
			
			$pairing_and_gc_sp->pairing_bonus = 0;
            $pairing_and_gc_sp->gift_cheque = 0;
		}*/

        $pairing_and_gc_sp = $this->get_current_earning_per_type($member_id,$account_id,$sp_card_id);
		
		//var_dump($pairing_and_gc_sp);
		
		// VP pairing and gc
        $vp_card = $this->cards_model->get_card_type_by_code('VP');
        $vp_card_id = $vp_card->card_type_id;
		/*$sql_pairing_and_gc_vp = "SELECT 
									`type_id`, 
									SUM(`pairing_bonus`) AS pairing_bonus, 
									SUM(`gift_cheque`) AS `gift_cheque` 
								FROM
									`cm_member_earnings_per_type`
								WHERE  type_id = '$vp_card_id' ";
								
		if($account_id != ""){
            $sql_pairing_and_gc_vp .= "AND `member_id` = {$member_id} AND `account_id` = '{$account_id}'
                GROUP BY `type_id`";
        } else {
            $sql_pairing_and_gc_vp .= "AND `member_id` = {$member_id}
                GROUP BY `type_id`";
        }	
        
		$query = $this->db->query($sql_pairing_and_gc_vp);
        $pairing_and_gc_vp = $query->row();
        $query->free_result();	
		
		if(empty($pairing_and_gc_vp) || is_null($pairing_and_gc_vp)) {
			$pairing_and_gc_vp = array();
			$pairing_and_gc_vp = (object)$pairing_and_gc_vp;
			
			$pairing_and_gc_vp->pairing_bonus = 0;
            $pairing_and_gc_vp->gift_cheque = 0;            
		}*/

        $pairing_and_gc_vp = $this->get_current_earning_per_type($member_id,$account_id,$vp_card_id);
        			
		// RS pairing and gc
        $rs_card = $this->cards_model->get_card_type_by_code('RS');
        $rs_card_id = $rs_card->card_type_id;
		/*$sql_pairing_and_gc_rs = "SELECT 
									`type_id`, 
									SUM(`pairing_bonus`) AS pairing_bonus, 
									SUM(`gift_cheque`) AS `gift_cheque` 
								FROM
									`cm_member_earnings_per_type`
								WHERE  type_id = '$rs_card_id' ";
								
		if($account_id != ""){
            $sql_pairing_and_gc_rs .= "AND `member_id` = {$member_id} AND `account_id` = '{$account_id}'
                GROUP BY `type_id`";
        } else {
            $sql_pairing_and_gc_rs .= "AND `member_id` = {$member_id}
                GROUP BY `type_id`";
        }	
        
		$query = $this->db->query($sql_pairing_and_gc_rs);
        $pairing_and_gc_rs = $query->row();
        $query->free_result();	
		
		if(empty($pairing_and_gc_rs) || is_null($pairing_and_gc_rs)) {
			$pairing_and_gc_rs = array();
			$pairing_and_gc_rs = (object)$pairing_and_gc_rs;
			
			$pairing_and_gc_rs->pairing_bonus = 0;
            $pairing_and_gc_rs->gift_cheque = 0;            
		}*/

        $pairing_and_gc_rs = $this->get_current_earning_per_type($member_id,$account_id,$rs_card_id);
		
		// TP pairing and gc
        $tp_card = $this->cards_model->get_card_type_by_code('TP');
        $tp_card_id = $tp_card->card_type_id;
		/*$sql_pairing_and_gc_tp = "SELECT 
									`type_id`, 
									SUM(`pairing_bonus`) AS pairing_bonus, 
									SUM(`gift_cheque`) AS `gift_cheque` 
								FROM
									`cm_member_earnings_per_type`
								WHERE  type_id = '$tp_card_id' ";
								
		if($account_id != ""){
            $sql_pairing_and_gc_tp .= "AND `member_id` = {$member_id} AND `account_id` = '{$account_id}'
                GROUP BY `type_id`";
        } else {
            $sql_pairing_and_gc_tp .= "AND `member_id` = {$member_id}
                GROUP BY `type_id`";
        }	
        
		$query = $this->db->query($sql_pairing_and_gc_tp);
        $pairing_and_gc_tp = $query->row();
        $query->free_result();	
		
		if(empty($pairing_and_gc_tp) || is_null($pairing_and_gc_tp)) {
			$pairing_and_gc_tp = array();
			$pairing_and_gc_tp = (object)$pairing_and_gc_tp;
			
			$pairing_and_gc_tp->pairing_bonus = 0;
            $pairing_and_gc_tp->gift_cheque = 0;            
		}*/
        $pairing_and_gc_tp = $this->get_current_earning_per_type($member_id,$account_id,$tp_card_id);
        
		
        // ERHM pairing and gc
        $erhm_card = $this->cards_model->get_card_type_by_code('ERHM');
        $erhm_card_id = $erhm_card->card_type_id;
        /*$sql_pairing_and_gc_erhm = "SELECT 
                                    `type_id`, 
                                    SUM(`pairing_bonus`) AS pairing_bonus, 
                                    SUM(`gift_cheque`) AS `gift_cheque` 
                                FROM
                                    `cm_member_earnings_per_type`
                                WHERE  type_id = '$erhm_card_id' ";
                                
        if($account_id != ""){
            $sql_pairing_and_gc_erhm .= "AND `member_id` = {$member_id} AND `account_id` = '{$account_id}'
                GROUP BY `type_id`";
        } else {
            $sql_pairing_and_gc_erhm .= "AND `member_id` = {$member_id}
                GROUP BY `type_id`";
        }   
        
        $query = $this->db->query($sql_pairing_and_gc_erhm);
        $pairing_and_gc_erhm = $query->row();
        $query->free_result(); 

        if(empty($pairing_and_gc_erhm) || is_null($pairing_and_gc_erhm)) {
            $pairing_and_gc_erhm = array();
            $pairing_and_gc_erhm = (object)$pairing_and_gc_erhm;
            
            $pairing_and_gc_erhm->pairing_bonus = 0;
            $pairing_and_gc_erhm->gift_cheque = 0;            
        }*/

        $pairing_and_gc_erhm = $this->get_current_earning_per_type($member_id,$account_id,$erhm_card_id);

        // UP1 pairing and gc
        $up1_card = $this->cards_model->get_card_type_by_code('UP1');
        $up1_card_id = $up1_card->card_type_id;
        /*$sql_pairing_and_gc_up1 = "SELECT 
                                    `type_id`, 
                                    SUM(`pairing_bonus`) AS pairing_bonus, 
                                    SUM(`gift_cheque`) AS `gift_cheque` 
                                FROM
                                    `cm_member_earnings_per_type`
                                WHERE  type_id = '$up1_card_id' ";
                                
        if($account_id != ""){
            $sql_pairing_and_gc_up1 .= "AND `member_id` = {$member_id} AND `account_id` = '{$account_id}'
                GROUP BY `type_id`";
        } else {
            $sql_pairing_and_gc_up1 .= "AND `member_id` = {$member_id}
                GROUP BY `type_id`";
        }   
        
        $query = $this->db->query($sql_pairing_and_gc_up1);
        $pairing_and_gc_up1 = $query->row();
        $query->free_result();  

        if(empty($pairing_and_gc_up1) || is_null($pairing_and_gc_up1)) {
            $pairing_and_gc_up1 = array();
            $pairing_and_gc_up1 = (object)$pairing_and_gc_up1;
            
            $pairing_and_gc_up1->pairing_bonus = 0;
            $pairing_and_gc_up1->gift_cheque = 0;            
        }*/
        $pairing_and_gc_up1 = $this->get_current_earning_per_type($member_id,$account_id,$up1_card_id);


        // P2P pairing and gc
        $p2p_card = $this->cards_model->get_card_type_by_code('P2P');
        $p2p_card_id = $p2p_card->card_type_id;
        /*$sql_pairing_and_gc_p2p = "SELECT 
                                    `type_id`, 
                                    SUM(`pairing_bonus`) AS pairing_bonus, 
                                    SUM(`gift_cheque`) AS `gift_cheque` 
                                FROM
                                    `cm_member_earnings_per_type`
                                WHERE  type_id = '$p2p_card_id' ";
                                
        if($account_id != ""){
            $sql_pairing_and_gc_p2p .= "AND `member_id` = {$member_id} AND `account_id` = '{$account_id}'
                GROUP BY `type_id`";
        } else {
            $sql_pairing_and_gc_p2p .= "AND `member_id` = {$member_id}
                GROUP BY `type_id`";
        }   
        
        $query = $this->db->query($sql_pairing_and_gc_p2p);
        $pairing_and_gc_p2p = $query->row();
        $query->free_result(); 
        
        if(empty($pairing_and_gc_p2p) || is_null($pairing_and_gc_p2p)) {
            $pairing_and_gc_p2p = array();
            $pairing_and_gc_p2p = (object)$pairing_and_gc_p2p;
            
            $pairing_and_gc_p2p->pairing_bonus = 0;
            $pairing_and_gc_p2p->gift_cheque = 0;            
        }*/
        $pairing_and_gc_p2p = $this->get_current_earning_per_type($member_id,$account_id,$p2p_card_id);
        


        if(empty($all_earnings) || is_null($all_earnings))
        {
            $all_earnings = array();
            $all_earnings = (object)$all_earnings;
            $all_earnings->referral_bonus = 0;
            //$all_earnings->pairing_bonus_sp = 0;
            //$all_earnings->gift_cheque_sp = 0;
            //$all_earnings->pairing_bonus_vp = 0;
            //$all_earnings->gift_cheque_vp = 0;
            //$all_earnings->pairing_bonus_tp = 0;
            //$all_earnings->gift_cheque_tp = 0;
            //$all_earnings->pairing_bonus_rs = 0;
            //$all_earnings->gift_cheque_rs = 0;
            $all_earnings->unilevel_commission = 0;
        }
		
        $data['all_earnings'] = $all_earnings;
		$data['pairing_and_gc_sp'] = $pairing_and_gc_sp;
		$data['pairing_and_gc_vp'] = $pairing_and_gc_vp;
		$data['pairing_and_gc_rs'] = $pairing_and_gc_rs;
		$data['pairing_and_gc_tp'] = $pairing_and_gc_tp;
        $data['pairing_and_gc_erhm'] = $pairing_and_gc_erhm;
        $data['pairing_and_gc_up1'] = $pairing_and_gc_up1;
		$data['pairing_and_gc_p2p'] = $pairing_and_gc_p2p;

		// OLD	
        //$data['cash_earnings'] = 0;
        //$data['gc_commissions'] = 0;
        //$data['witholding_tax'] = 0;
        //$data['unilevel_witholding_tax'] = 0;
        //$data['igpsm_earnings'] = 0;
        //$data['unilevel_earnings'] = 0;
        //$data['cash_earnings'] += $all_earnings->referral_bonus;
        //$data['cash_earnings'] += $all_earnings->pairing_bonus_sp;
        //$data['cash_earnings'] += $all_earnings->pairing_bonus_vp;
        //$data['gc_commissions'] += $all_earnings->gift_cheque_sp;
        //$data['gc_commissions'] += $all_earnings->gift_cheque_vp;
        //$data['cash_earnings'] += $all_earnings->pairing_bonus_tp;
        //$data['gc_commissions'] += $all_earnings->gift_cheque_tp;
        //$data['cash_earnings'] += $all_earnings->pairing_bonus_rs;
        //$data['gc_commissions'] += $all_earnings->gift_cheque_rs;
        //$data['witholding_tax'] = $data['cash_earnings'] * $this->settings->witholding_tax;
        //$data['cash_earnings'] = $data['cash_earnings'] * (1 - $this->settings->witholding_tax);
        //$data['igpsm_earnings'] = $data['cash_earnings'] + $data['gc_commissions'];
        //$data['unilevel_witholding_tax'] = $all_earnings->unilevel_commission * ($this->settings->witholding_tax);
        //$data['unilevel_earnings'] = $all_earnings->unilevel_commission * (1 - $this->settings->witholding_tax);   
		
		// NEW
		$data['cash_earnings'] = 0;
        $data['gc_commissions'] = 0;
        $data['witholding_tax'] = 0;
        $data['unilevel_witholding_tax'] = 0;
        $data['igpsm_earnings'] = 0;
        $data['unilevel_earnings'] = 0;
        $data['cash_earnings'] += $all_earnings->referral_bonus;
        $data['cash_earnings'] += $pairing_and_gc_sp->pairing_bonus; 
        $data['cash_earnings'] += $pairing_and_gc_vp->pairing_bonus;
        $data['cash_earnings'] += $pairing_and_gc_tp->pairing_bonus;                                
        $data['cash_earnings'] += $pairing_and_gc_rs->pairing_bonus;
        $data['cash_earnings'] += $pairing_and_gc_erhm->pairing_bonus;
        $data['cash_earnings'] += $pairing_and_gc_up1->pairing_bonus;
        $data['cash_earnings'] += $pairing_and_gc_p2p->pairing_bonus;
                                        
        $data['gc_commissions'] += $pairing_and_gc_sp->gift_cheque; 
        $data['gc_commissions'] += $pairing_and_gc_vp->gift_cheque;        
        $data['gc_commissions'] += $pairing_and_gc_tp->gift_cheque;        
        $data['gc_commissions'] += $pairing_and_gc_rs->gift_cheque;        
        $data['gc_commissions'] += $pairing_and_gc_erhm->gift_cheque;        
        $data['gc_commissions'] += $pairing_and_gc_up1->gift_cheque;        
        $data['gc_commissions'] += $pairing_and_gc_p2p->gift_cheque;
                                        
        $data['witholding_tax'] = $data['cash_earnings'] * $this->settings->witholding_tax;
        $data['cash_earnings'] = $data['cash_earnings'] * (1 - $this->settings->witholding_tax);
        $data['igpsm_earnings'] = $data['cash_earnings'] + $data['gc_commissions'];
        $data['unilevel_witholding_tax'] = $all_earnings->unilevel_commission * ($this->settings->witholding_tax);
        $data['unilevel_earnings'] = $all_earnings->unilevel_commission * (1 - $this->settings->witholding_tax);
		
        $current_date = date("Y-m-d 00:00:00");
        $current_datetime = date("Y-m-d H:i:s");		
		
		$where = "status = 'COMPLETED' AND payout_type = 'IGPSM' AND is_official = 1";		
		$payout_period_details = $this->payout_model->get_payout_periods($where,'','end_date DESC');
		
		$last_encashment = date("Y-m-d H:i:s",strtotime($this->settings->last_encashment_timestamp));
				
		// get current payout period
		$sql = "SELECT 
				payout_period_id, start_date, end_date
				FROM 
				 po_payout_periods			
				WHERE 
				 now() BETWEEN start_date AND end_date
				AND
				 status = 'ACTIVE'
				AND 
				 is_official = 1 
				AND 
				 payout_type = 'IGPSM'
				ORDER BY 
				 payout_period_id DESC";

		$query = $this->db->query($sql);
		$payout_period_result = $query->result();			
		$query->free_result();
		
		$payout_start_date = "";
		$payout_end_date = "";
		$payout_id = "";
		
		if ((empty($payout_period_result)) ||  ($payout_period_result[0]->payout_period_id == NULL)) {
			// set values to N/A
			 $data['weekly_igpsm_amount'] = 0;
			 
		} else {	
			$payout_id = $payout_period_result[0]->payout_period_id;
			$payout_start_date = $payout_period_result[0]->start_date;
			$payout_end_date = $payout_period_result[0]->end_date;
		
			$sql_igpsm_earnings = "SELECT
					sum(`amount`) as 'amount'
					FROM `tr_member_acct_credit_logs`
					WHERE 
						insert_timestamp BETWEEN '{$payout_start_date}' AND '{$payout_end_date}' 
					AND 
                        LOWER(type) <> 'products' 
                    AND
						`transaction_code` IN (100,101,102,103,104) AND ";

			if($account_id != ""){
				$sql_igpsm_earnings .= "`member_id` = {$member_id} AND `account_id` = '{$account_id}'
					GROUP BY `account_id`";
			}else{
				$sql_igpsm_earnings .= "`member_id` = {$member_id}
					GROUP BY `member_id`";
			}

			$query = $this->db->query($sql_igpsm_earnings);
			$weekly_igpsm = $query->first_row();
			$query->free_result();
			
			$data['weekly_igpsm_amount'] = 0;
			if(!empty($weekly_igpsm)) $data['weekly_igpsm_amount'] = $weekly_igpsm->amount;
        }
		
        $firstday_datetime = date("Y-m-d H:i:s",mktime(0, 0, 0, date('m'), 1, date('Y')));

		// monthly unilevel	
        $sql_monthly_unilevel = "SELECT
                sum(`amount`) as `amount`
                FROM `tr_member_acct_credit_logs`
                WHERE 
                    `insert_timestamp` >= '{$firstday_datetime}' 
                AND 
                    `insert_timestamp` <= NOW() 
                AND 
                        LOWER(type) <> 'products' 
                AND 
                    `transaction_code` IN (105) AND ";

        if (($account_id != "") || ($account_id != "ALL")) {
            $sql_monthly_unilevel .= "`member_id` = {$member_id} AND `account_id` = '{$account_id}'
                GROUP BY `account_id`";
        }else{
            $sql_monthly_unilevel .= "`member_id` = {$member_id}
                GROUP BY `member_id`";
        }

        $query = $this->db->query($sql_monthly_unilevel);
        $monthly_unilevel = $query->first_row();
        $query->free_result();
                        
        $data['monthly_unilevel_amount'] = 0;
        if(!empty($monthly_unilevel)) $data['monthly_unilevel_amount'] = $monthly_unilevel->amount;

		// monthly igpsm	
        $sql_monthly_igpsm = "SELECT
                sum(`amount`) as `amount`
                FROM `tr_member_acct_credit_logs`
                WHERE `insert_timestamp` >= '{$firstday_datetime}' AND
                    `insert_timestamp` <= NOW() AND
                    `transaction_code` NOT IN (105) AND ";

        if(($account_id != "") || ($account_id != "ALL")){
            $sql_monthly_igpsm .= "`member_id` = {$member_id} AND `account_id` = '{$account_id}'
                GROUP BY `account_id`";
        }else{
            $sql_monthly_igpsm .= "`member_id` = {$member_id}
                GROUP BY `member_id`";
        }

        $query = $this->db->query($sql_monthly_igpsm);
        $monthly_igpsm = $query->first_row();
        $query->free_result();
        
        $data['monthly_igpsm_amount'] = 0;
        if(!empty($monthly_igpsm)) $data['monthly_igpsm_amount'] = $monthly_igpsm->amount;
		
        return $data;
    }
	
	
	/* -- FOR MEMBER ENCASHMENTS -- */
	function get_member_encashments($where = null, $limit = null, $orderby = null, $fields = null) {
        $query = $this->fetch('member_encashments', $fields, $where, $orderby, $limit);
        $row = $query->result();
        $query->free_result();
        return $row;
    }

    function insert_member_encashment($data) 
    {
        return $this->insert('member_encashments', $data);
    }

	function get_member_encashments_count($where = null) {
        // do a sql count instead of row count
        $query = $this->fetch('member_encashments', 'count(1) as cnt', $where);
        $row = $query->first_row();
        $query->free_result();
        return $row->cnt;
    }
	
	function get_member_encashment_by_member_id($member_id) {
        $result = $this->get_member_encashments(array('member_id' => $member_id));
        $row = NULL;
        if (count($result) > 0) {
            $row = $result[0];
        }
        return $row;
    }

	// cm_member_rfid_cards
	function get_member_rfid_cards($where = null, $limit = null, $orderby = null, $fields = null) {
        $query = $this->fetch('member_rfid_cards', $fields, $where, $orderby, $limit);
		$row = $query->result();
        $query->free_result();
        return $row;
    }
			
   	function get_member_rfid_cards_count($where = null, $limit = null, $orderby = null, $fields = null) {
        // do a sql count instead of row count
        $query = $this->fetch('member_rfid_cards', 'count(1) as cnt', $where);
		$row = $query->first_row();
        $query->free_result();
        return $row->cnt;
	}
	
	function get_member_rfid_by_member_id($member_id) {
        $result = $this->get_member_rfid_cards(array('member_id' => $member_id));
		 $row = NULL;
        if (count($result) > 0) {
            $row = $result[0];
        }
        return $row;
    }
		
	function insert_member_rfid($data) 
    {
        return $this->insert('member_rfid_cards', $data);
    }
	
	function update_member_rfid($data, $where)
	{
		return $this->update('member_rfid_cards', $data, $where);
	}	
		
	/* -- FOR MEMBER WITHDRAW FUNDS -- */
	function get_member_withdraw_funds_transactions($where = null, $limit = null, $orderby = null, $fields = null) {
        $query = $this->fetch('member_withdraw_funds_transactions_view', $fields, $where, $orderby, $limit);
        $row = $query->result();
        $query->free_result();
        return $row;
    }
		
	function get_member_withdraw_funds_transactions_count($where = null) {
        // do a sql count instead of row count
        $query = $this->fetch('member_withdraw_funds_transactions_view', 'count(1) as cnt', $where);
        $row = $query->first_row();
        $query->free_result();
        return $row->cnt;
    }
		
	function get_member_withdraw_funds_transactions_by_member_id($member_id) {
        $result = $this->get_member_withdraw_funds_transactions(array('member_id' => $member_id));
        $row = NULL;
        if (count($result) > 0) {
            $row = $result[0];
        }
        return $row;
    }

	function get_member_withdraw_funds_transactions_by_transaction_id($transaction_id) {
        $result = $this->get_member_withdraw_funds_transactions(array('transaction_id' => $transaction_id));
        $row = NULL;
        if (count($result) > 0) {
            $row = $result[0];
        }
        return $row;
    }
	
	function insert_member_withdraw_funds_transactions($data) 
    {
        return $this->insert('member_withdraw_funds_transactions', $data);
    }
	
	function update_member_withdraw_funds_transactions($data, $where) 
    {
        return $this->update('member_withdraw_funds_transactions', $data, $where);
    }
	
	
	/* -- FOR MEMBER PAYOUTS -- */
	function get_member_payouts($where = null, $limit = null, $orderby = null, $fields = null) {
        $query = $this->fetch('member_payouts', $fields, $where, $orderby, $limit);
        $row = $query->result();
        $query->free_result();
        return $row;
    }

	function get_member_payouts_count($where = null) {
        // do a sql count instead of row count
        $query = $this->fetch('member_payouts', 'count(1) as cnt', $where);
        $row = $query->first_row();
        $query->free_result();
        return $row->cnt;
    }
	
	function get_member_payouts_by_member_id($member_id) {
        $result = $this->get_member_payouts(array('member_id' => $member_id));
		 $row = NULL;
        if (count($result) > 0) {
            $row = $result[0];
        }
        return $row;
    }
	
    function insert_member_payouts($data) 
    {
        return $this->insert('member_payouts', $data);
    }
    
	/* -- FOR MEMBER ACCOUNT COMMISSIONS-- */
	function get_member_account_commissions($where = null, $limit = null, $orderby = null, $fields = null) {
        $query = $this->fetch('member_account_commissions', $fields, $where, $orderby, $limit);
        $row = $query->result();
        $query->free_result();
        return $row;
    }

	function get_member_account_commissions_count($where = null) {
        // do a sql count instead of row count
        $query = $this->fetch('member_account_commissions', 'count(1) as cnt', $where);
        $row = $query->first_row();
        $query->free_result();
        return $row->cnt;
    }
	
	function get_member_account_commissions_by_member_id($member_id) {
        $result = $this->get_member_account_commissions(array('member_id' => $member_id));
        $row = NULL;
        if (count($result) > 0) {
            $row = $result[0];
        }
        return $row;
    }

    function insert_member_account_commissions($data) 
    {
        return $this->insert('member_account_commissions', $data);
    }

    /* -- MEMBER DEDUCTIONS -- */
    function get_member_deductions($where = null, $limit = null, $orderby = null, $fields = null)
    {
        $query = $this->fetch('member_deductions', $fields, $where, $orderby, $limit);
        $row = $query->result();
        $query->free_result();
        return $row;
    }

    function insert_member_deductions($data)
    {
        return $this->insert('member_deductions', $data);
    }

    function update_member_deductions($data, $where)
    {
        return $this->update('member_deductions', $data, $where);
    }

    function delete_member_deductions($where)
    {
        return $this->delete('member_deductions', $where);
    }


	function get_non_members($where = null, $limit = null, $orderby = null, $fields = null)
	{
		$query = $this->fetch('non_members', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
	}

	function insert_non_member($data)
	{
		return $this->insert('non_members', $data);
	}

	function update_non_member($data, $where)
	{
		return $this->update('non_members', $data, $where);
	}

	function delete_non_member($where)
	{
		return $this->delete('non_members', $where);
	}

	function get_non_members_count($where = null)
	{
		// do a sql count instead of row count
		$query = $this->fetch('non_members', 'count(1) as cnt', $where);
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}

	function get_non_member_by_id($non_member_id)
	{
		$result = $this->get_non_members(array('non_member_id' => $non_member_id));
		$row = NULL;
		if (count($result) > 0)
		{
			$row = $result[0];
		}
		return $row;
	}
	
	function get_non_member_by_account_id($temp_account_id)
	{
		$result = $this->get_non_members(array('temp_account_id' => $temp_account_id));
		$row = NULL;
		if (count($result) > 0)
		{
			$row = $result[0];
		}
		return $row;
	}
	
    /* member_account_upgrades */	
    function get_member_account_upgrades($where = null, $limit = null, $orderby = null, $fields = null)
    {
        $query = $this->fetch('member_account_upgrades', $fields, $where, $orderby, $limit);
        $row = $query->result();
        $query->free_result();
        return $row;
    }

    function insert_member_account_upgrades($data)
    {
        return $this->insert('member_account_upgrades', $data);
    }

    function update_member_account_upgrades($data, $where)
    {
        return $this->update('member_account_upgrades', $data, $where);
    }

    function delete_member_account_upgrades($where)
    {
        return $this->delete('member_account_upgrades', $where);
    }

	function get_member_account_upgrades_by_account_id($account_id) {
        $result = $this->get_member_account_upgrades(array('base_account_id' => $account_id));
		 $row = NULL;
        if (count($result) > 0) {
            $row = $result[0];
        }
        return $row;
    }
	
	function get_member_account_upgrades_by_card_type_id($card_type_id) {
        $result = $this->get_member_account_upgrades(array('card_type_id' => $card_type_id));
		 $row = NULL;
        if (count($result) > 0) {
            $row = $result[0];
        }
        return $row;
    }
	
	function get_member_account_upgrades_count($where = null)
	{
		// do a sql count instead of row count
		$query = $this->fetch('member_account_upgrades', 'count(1) as cnt', $where);
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}
	
	//function get_all_upgrades_count($account_id, $modifier_name)
    //{
	//	$this->load->model('cards_model');
    //	// get modifier with UPGRADE
	//	$modifier = $this->cards_model->get_modifiers(array(
	//		'modifier_name' => $modifier_name
	//	));
	//	$modifier = $modifier[0];
	//	
	//	// get card modifiers
	//	$card_modifiers = $this->cards_model->get_card_modifiers(array(
	//		'card_type_id' => $modifier->modifier_id
	//	));
	//	$ret = array();
	//	foreach($card_modifiers as $mod)
	//	{
	//		// get count
	//		$where ="card_type_id = '{$mod->card_type_id}'";
	//		$upgrade_count = $this->get_member_account_upgrades_count($where);
	//	
	//		$ret[] =  $upgrade_count;
	//	}
	//	return $ret;
    //}
	
    /* member_account_pairing */   
    function get_member_account_pairing($where = null, $limit = null, $orderby = null, $fields = null)
    {
        $query = $this->fetch('member_account_pairing', $fields, $where, $orderby, $limit);
        $row = $query->result();
        $query->free_result();
        return $row;
    }

    function insert_member_account_pairing($data)
    {
        return $this->insert('member_account_pairing', $data);
    }

    function update_member_account_pairing($data, $where)
    {
        return $this->update('member_account_pairing', $data, $where);
    }

    function delete_member_account_pairing($where)
    {
        return $this->delete('member_account_pairing', $where);
    }

    /* member_account_credited_transaction */   
    function get_member_account_credited_transaction($where = null, $limit = null, $orderby = null, $fields = null)
    {
        $query = $this->fetch('member_account_credited_transaction', $fields, $where, $orderby, $limit);
        $row = $query->result();
        $query->free_result();
        return $row;
    }

    function insert_member_account_credited_transaction($data)
    {
        return $this->insert('member_account_credited_transaction', $data);
    }

    function update_member_account_credited_transaction($data, $where)
    {
        return $this->update('member_account_credited_transaction', $data, $where);
    }

    function delete_member_account_credited_transaction($where)
    {
        return $this->delete('member_account_credited_transaction', $where);
    }

    /* member_account_product_transaction */   
    function get_member_account_product_transaction($where = null, $limit = null, $orderby = null, $fields = null)
    {
        $query = $this->fetch('member_account_product_transaction', $fields, $where, $orderby, $limit);
        $row = $query->result();
        $query->free_result();
        return $row;
    }

    function insert_member_account_product_transaction($data)
    {
        return $this->insert('member_account_product_transaction', $data);
    }

    function update_member_account_product_transaction($data, $where)
    {
        return $this->update('member_account_product_transaction', $data, $where);
    }

    function delete_member_account_product_transaction($where)
    {
        return $this->delete('member_account_product_transaction', $where);
    }

    function get_current_earning($member_id,$account_id = '')
    {
        $current_earning = array();
        $current_earning = (object)$current_earning;

        $current_earning->referral_bonus = 0;
        $current_earning->pairing_bonus_sp = 0;
        $current_earning->gift_cheque_sp = 0;
        $current_earning->pairing_bonus_vp = 0;
        $current_earning->gift_cheque_vp = 0;
        $current_earning->pairing_bonus_tp = 0;
        $current_earning->gift_cheque_tp = 0;
        $current_earning->pairing_bonus_rs = 0;
        $current_earning->gift_cheque_rs = 0;
        $current_earning->unilevel_commission = 0;
        
        if($account_id)
            $where = array('member_id' => $member_id , 'account_id' => $account_id);
        else
            $where = array('member_id' => $member_id);

        $earnings = $this->get_member_earnings($where);
        if(sizeof($earnings) > 0)
        {
            $earning = $earnings[0];
            $current_earning->referral_bonus = $earning->referral_bonus;
            $current_earning->pairing_bonus_sp = $earning->pairing_bonus_sp;
            $current_earning->gift_cheque_sp = $earning->gift_cheque_sp;
            $current_earning->pairing_bonus_vp = $earning->pairing_bonus_vp;
            $current_earning->gift_cheque_vp = $earning->gift_cheque_vp;
            $current_earning->pairing_bonus_tp = $earning->pairing_bonus_tp;
            $current_earning->gift_cheque_tp = $earning->gift_cheque_tp;
            $current_earning->pairing_bonus_rs = $earning->pairing_bonus_rs;
            $current_earning->gift_cheque_rs = $earning->gift_cheque_rs;
            $current_earning->unilevel_commission = $earning->unilevel_commission;
        }

        //check if this account has been transferred with earnings, will be deducted
        if($account_id)
            $where = array('from_member_id' => $member_id , 'account_id' => $account_id, 'with_earnings' => '1');
        else
            $where = array('from_member_id' => $member_id , 'with_earnings' => '1');

        $limit = array('rows'=>1, 'offset'=>0);
        $order_by = 'insert_timestamp DESC';

        $transfered_earnings = $this->tracking_model->get_member_acct_transfer_earnings_logs($where,$limit,$order_by);

        if(sizeof($transfered_earnings) > 0)
        {
            $transfered_earning = $transfered_earnings[0];
            
            $current_earning->referral_bonus -= $transfered_earning->referral_bonus;
            $current_earning->pairing_bonus_sp -= $transfered_earning->pairing_bonus_sp;
            $current_earning->gift_cheque_sp -= $transfered_earning->gift_cheque_sp;
            $current_earning->pairing_bonus_vp -= $transfered_earning->pairing_bonus_vp;
            $current_earning->gift_cheque_vp -= $transfered_earning->gift_cheque_vp;
            $current_earning->pairing_bonus_tp -= $transfered_earning->pairing_bonus_tp;
            $current_earning->gift_cheque_tp -= $transfered_earning->gift_cheque_tp;
            $current_earning->pairing_bonus_rs -= $transfered_earning->pairing_bonus_rs;
            $current_earning->gift_cheque_rs -= $transfered_earning->gift_cheque_rs;
            $current_earning->unilevel_commission -= $transfered_earning->unilevel_commission;
        }

        if($account_id)
            $where = array('to_member_id' => $member_id, 'account_id'=> $account_id); 
        else
            $where = array('to_member_id' => $member_id); 

        $transfered_to = $this->tracking_model->get_member_acct_transfer_earnings_logs($where,$limit,$order_by);

        if(sizeof($transfered_to) > 0)
        {
            $transfered_to = $transfered_to[0];

            if($transfered_to->with_earnings == '1')
            {
                $current_earning->referral_bonus += $transfered_to->transfer_referral_bonus;
                $current_earning->pairing_bonus_sp += $transfered_to->transfer_pairing_bonus_sp;
                $current_earning->gift_cheque_sp += $transfered_to->transfer_gift_cheque_sp;
                $current_earning->pairing_bonus_vp += $transfered_to->transfer_pairing_bonus_vp;
                $current_earning->gift_cheque_vp += $transfered_to->transfer_gift_cheque_vp;
                $current_earning->pairing_bonus_tp += $transfered_to->transfer_pairing_bonus_tp;
                $current_earning->gift_cheque_tp += $transfered_to->transfer_gift_cheque_tp;
                $current_earning->pairing_bonus_rs += $transfered_to->transfer_pairing_bonus_rs;
                $current_earning->gift_cheque_rs += $transfered_to->transfer_gift_cheque_rs;
                $current_earning->unilevel_commission += $transfered_to->transfer_unilevel_commission;
            }
        }

        return $current_earning;

    }

    function get_current_earning_per_type($member_id,$account_id = '',$type_id)
    {
        $current_earning = array();
        $current_earning = (object)$current_earning;

        $current_earning->pairing_bonus = 0;
        $current_earning->gift_cheque = 0;
        
        if($account_id)
            $where = array('member_id' => $member_id , 'account_id' => $account_id, 'type_id'=> $type_id);
        else
            $where = array('member_id' => $member_id , 'type_id'=> $type_id);
            
        $earnings = $this->get_member_earnings_per_type($where);
        if(sizeof($earnings) > 0) {   
			
			foreach($earnings as $earning) {			
				//$earning = $earnings[0];
				$current_earning->pairing_bonus = $current_earning->pairing_bonus + $earning->pairing_bonus;
				$current_earning->gift_cheque = $current_earning->gift_cheque + $earning->gift_cheque;
			}
        }

        //check if this account has been transferred with earnings, will be deducted

        if($account_id)
            $where = array('from_member_id' => $member_id , 'account_id' => $account_id, 'type_id'=> $type_id, 'with_earnings' => '1');
        else
            $where = array('from_member_id' => $member_id , 'type_id'=> $type_id, 'with_earnings' => '1');


        $limit = array('rows'=>1, 'offset'=>0);
        $order_by = 'insert_timestamp DESC';

        $transfered_earnings = $this->tracking_model->get_member_acct_transfer_earnings_per_type_logs($where,$limit,$order_by);

        if(sizeof($transfered_earnings) > 0)
        {
			foreach($transfered_earnings as $transfered_earning) {	
				//$transfered_earning = $transfered_earnings[0];
            
				$current_earning->pairing_bonus -= $transfered_earning->pairing_bonus;
				$current_earning->gift_cheque -=  $transfered_earning->gift_cheque;
			}
        }


        if($account_id)
            $where = array('to_member_id' => $member_id, 'account_id'=> $account_id, 'type_id'=> $type_id); 
        else
            $where = array('to_member_id' => $member_id, 'type_id'=> $type_id); 

        $transfered_to = $this->tracking_model->get_member_acct_transfer_earnings_per_type_logs($where,$limit,$order_by);

        if(sizeof($transfered_to) > 0)
        {
			foreach($transfered_to as $transfered_t) {	
            //$transfered_to = $transfered_to[0];

				if($transfered_t->with_earnings == '1')
				{
					$current_earning->pairing_bonus += $transfered_t->transfer_pairing_bonus;
					$current_earning->gift_cheque += $transfered_t->transfer_gift_cheque;
				}
			}	
        }

        return $current_earning;
    }

    /* member_inventory */
    function get_member_inventory($where = null, $limit =  null, $orderby = null, $fields = null)
    {
        $query = $this->fetch('member_inventory', $fields, $where, $orderby, $limit);
        $row = $query->result();
        $query->free_result();
        return $row;
    }
    
    function insert_member_inventory($data)
    {
        return $this->insert('member_inventory', $data);
    }

    function update_member_inventory($data, $where)
    {
        return $this->update('member_inventory', $data, $where);
    }

    function delete_member_inventory($where)
    {
        return $this->delete('member_inventory', $where);
    }

    /* cpoint_ratio */
    function get_cpoint_ratio($where = null, $limit = null, $orderby = null, $fields = null)
    {
        $query = $this->fetch('cpoint_ratio', $fields, $where, $orderby, $limit);
        $row = $query->result();
        $query->free_result();
        return $row;
    }

    function insert_cpoint_ratio($data)
    {
        return $this->insert('cpoint_ratio', $data);
    }

    function update_cpoint_ratio($data, $where)
    {
        return $this->update('cpoint_ratio', $data, $where);
    }

    function delete_cpoint_ratio($where)
    {
        return $this->delete('cpoint_ratio', $where);
    }

    /* member_p2p_mismatch */
    function get_member_p2p_mismatch($where = null, $limit = null, $orderby = null, $fields = null)
    {
        $query = $this->fetch('member_p2p_mismatch', $fields, $where, $orderby, $limit);
        $row = $query->result();
        $query->free_result();
        return $row;
    }

    function insert_member_p2p_mismatch($data)
    {
        return $this->insert('member_p2p_mismatch', $data);
    }

    function update_member_p2p_mismatch($data, $where)
    {
        return $this->update('member_p2p_mismatch', $data, $where);
    }

    function delete_member_p2p_mismatch($where)
    {
        return $this->delete('member_p2p_mismatch', $where);
    }

	/* member p2p products cards tally */
	function get_member_p2p_product_cards_tally($where = null, $limit = null, $orderby = null, $fields = null)
    {
        $query = $this->fetch('member_products_cards', $fields, $where, $orderby, $limit);
        $row = $query->result();
        $query->free_result();
        return $row;
    }

    function insert_member_p2p_product_cards_tally($data)
    {
        return $this->insert('member_products_cards', $data);
    }

    function update_member_p2p_product_cards_tally($data, $where)
    {
        return $this->update('member_products_cards', $data, $where);
    }

    function delete_member_p2p_product_cards_tally($where)
    {
        return $this->delete('member_products_cards', $where);
    }

     /* auto_crediting */
    function get_auto_crediting($where = null, $limit = null, $orderby = null, $fields = null)
    {
        $query = $this->fetch('auto_crediting', $fields, $where, $orderby, $limit);
        $row = $query->result();
        $query->free_result();
        return $row;
    }

    function insert_auto_crediting($data)
    {
        return $this->insert('auto_crediting', $data);
    }

    function update_auto_crediting($data, $where)
    {
        return $this->update('auto_crediting', $data, $where);
    }

    function delete_auto_crediting($where)
    {
        return $this->delete('auto_crediting', $where);
    }

    function get_auto_crediting_by_id($crediting_id)
    {
        $result = $this->get_auto_crediting(array('crediting_id' => $crediting_id));
        $row = NULL;
        if (count($result) > 0)
        {
            $row = $result[0];
        }
        return $row;
    }

    /* -- FOR MEMBER PAYOUTS BACKUPS -- */
    function get_member_payouts_backups($where = null, $limit = null, $orderby = null, $fields = null) {
        $query = $this->fetch('member_payouts_backups', $fields, $where, $orderby, $limit);
        $row = $query->result();
        $query->free_result();
        return $row;
    }

    function get_member_payouts_backups_count($where = null) {
        // do a sql count instead of row count
        $query = $this->fetch('member_payouts_backups', 'count(1) as cnt', $where);
        $row = $query->first_row();
        $query->free_result();
        return $row->cnt;
    }
    
    function get_member_payouts_backups_by_member_id($member_id) {
        $result = $this->get_member_payouts_backups(array('member_id' => $member_id));
         $row = NULL;
        if (count($result) > 0) {
            $row = $result[0];
        }
        return $row;
    }
    
    function insert_member_payouts_backups($data) 
    {
        return $this->insert('member_payouts_backups', $data);
    }

    function get_search_result_by_account_id($searchString){
        $query = $this->db->query("SELECT primary_account_id,member_id FROM cm_member_user_accounts 
                                    WHERE primary_account_id 
                                    LIKE '%{$searchString}%'");
        return $query->result();
    }
    function get_search_result_by_name($searchString = false,$where_in = false){
       if(count($where_in) != 0 ){
            $member_ids = implode(",",$where_in);
            $query = $this->db->query("SELECT CONCAT(first_name,' ',middle_name,' ',last_name) as fullname,member_id
                                       FROM cm_members
                                       WHERE member_id IN({$member_ids})");
            $result_1 =  $query->result();//Result 1
            $query = $this->db->query("SELECT CONCAT(first_name,' ',middle_name,' ',last_name) as fullname,
                                              member_id
                                       FROM cm_members
                                       WHERE first_name LIKE '%{$searchString}%'
                                       AND member_id NOT IN({$member_ids})");
           $result_2 =  $query->result();//Result 2
           return array_merge($result_1,$result_2);

       }
       else{
            $query = $this->db->query("SELECT CONCAT(first_name,' ',middle_name,' ',last_name) as fullname,
                                              member_id
                                       FROM cm_members
                                       WHERE first_name LIKE '%{$searchString}%'
                           ");
            return  $query->result();
            
       }
    }

    function get_search_final_result($member_id = false){
            $query = $this->db->query("SELECT primary_account_id 
                                        FROM cm_member_user_accounts
                                        WHERE member_id = '{$member_id}'
                                        AND primary_account_id <> ''
                                        
                                    ");
            $count = $query->num_rows();
          
            if($count > 0){
                return $query->row()->primary_account_id;
            }else{
            return;
            }
           
            
    }



}    

// end of file