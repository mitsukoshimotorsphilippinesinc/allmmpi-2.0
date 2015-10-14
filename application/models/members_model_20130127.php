<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Members_model extends Base_model {
	function __construct()
	{
		// call the base model constructor
		parent::__construct();

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
				'member_verification' => 'cm_member_verification',
				'member_rfid_cards' => 'cm_member_rfid_cards',
				'member_account_commissions' => 'cm_member_account_commissions',
				'member_payouts' => 'cm_member_payouts',
				'member_encashments' => 'cm_member_encashments',
				'member_withdraw_funds_transactions' => 'cm_member_withdraw_funds_transactions',	
				'member_withdraw_funds_transactions_view' => 'cm_member_withdraw_funds_transactions_view',
                'member_deductions' => 'cm_member_deductions',
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

	function get_downline_count_by_node_address($node_address, $side = 'both', $dont_include_fs = true, $no_cd_balance = true)
	{
		$side = strtolower($side);
		$side_inset = '';
		if ($side == 'l') $side_inset = '2';
		if ($side == 'r') $side_inset = '1';
		$_where_fs = "";
		if ($dont_include_fs) $_where_fs = " AND account_type_id != 5";
		$_where_cd = "";
		if ($no_cd_balance) $_where_cd = " AND cd_amount = 0";
		$this->db->select("count(1) as cnt");
		$this->db->where("node_address like '{$node_address}{$side_inset}%' {$_where_fs} {$_where_cd}");
		$this->db->from($this->_TABLES['member_accounts']);
		$query = $this->db->get();
		$row = $query->first_row();
		$query->free_result();
		
		return $row->cnt;
		
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

	function credit_earnings($account_id,$card_id,$from_account_id,$amount,$transaction_code,$remarks="") {

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
			"remarks"=>$remarks
		);
		$this->tracking_model->insert_acct_credit_logs($data);

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
	function credit_points($account_id,$card_id,$from_account_id,$type,$position,$points)
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
			"remarks"=>"Credit " . strtoupper($type) . " Points from {$card_id}"
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
				"remarks"=>"No pairing bonus due to flushout from {$card_id}"
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
						"remarks"=>"Credit gift cheque for fifth pair from {$card_id}"
					);
					$this->tracking_model->insert_acct_credit_logs($data);
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
						"remarks"=>$remarks
					);
					$this->tracking_model->insert_acct_credit_logs($data);
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
				"remarks"=>$remarks			
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
				"remarks"=>$remarks			
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
                        "remarks"=>"Credit gift cheque for fifth pair from {$card_id}",
                        "insert_timestamp" => $timestamp,
                    );
                    $this->tracking_model->insert_acct_credit_logs($data);
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
		$result = $this->get_member_account_types(array('account_type_id' => $account_type_id));
		$row = NULL;
		if (count($result) > 0)
		{
			$row = $result[0];
		}
		return $row;
	}

	function get_member_account_type_by_type($account_type)
	{
		$result = $this->get_member_account_types(array('account_type' => $account_type));
		$row = NULL;
		if (count($result) > 0)
		{
			$row = $result[0];
		}
		return $row;
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

        $sql_all_earnings = "SELECT
                    sum(`referral_bonus`) as `referral_bonus`,
                    sum(`pairing_bonus_sp`) as `pairing_bonus_sp`,
                    sum(`gift_cheque_sp`) as `gift_cheque_sp`,
                    sum(`pairing_bonus_vp`) as `pairing_bonus_vp`,
                    sum(`gift_cheque_vp`) as `gift_cheque_vp`,
                    sum(`pairing_bonus_tp`) as `pairing_bonus_tp`,
                    sum(`gift_cheque_tp`) as `gift_cheque_tp`,
                    sum(`pairing_bonus_rs`) as `pairing_bonus_rs`,
                    sum(`gift_cheque_rs`) as `gift_cheque_rs`,
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

        if(empty($all_earnings) || is_null($all_earnings))
        {
            $all_earnings = array();
            $all_earnings = (object)$all_earnings;
            $all_earnings->referral_bonus = 0;
            $all_earnings->pairing_bonus_sp = 0;
            $all_earnings->gift_cheque_sp = 0;
            $all_earnings->pairing_bonus_vp = 0;
            $all_earnings->gift_cheque_vp = 0;
            $all_earnings->pairing_bonus_tp = 0;
            $all_earnings->gift_cheque_tp = 0;
            $all_earnings->pairing_bonus_rs = 0;
            $all_earnings->gift_cheque_rs = 0;
            $all_earnings->unilevel_commission = 0;
        }

        $data['all_earnings'] = $all_earnings;

        $data['cash_earnings'] = 0;
        $data['gc_commissions'] = 0;
        $data['witholding_tax'] = 0;
        $data['unilevel_witholding_tax'] = 0;
        $data['igpsm_earnings'] = 0;
        $data['unilevel_earnings'] = 0;
        $data['cash_earnings'] += $all_earnings->referral_bonus;
        $data['cash_earnings'] += $all_earnings->pairing_bonus_sp;
        $data['cash_earnings'] += $all_earnings->pairing_bonus_vp;
        $data['gc_commissions'] += $all_earnings->gift_cheque_sp;
        $data['gc_commissions'] += $all_earnings->gift_cheque_vp;
        $data['cash_earnings'] += $all_earnings->pairing_bonus_tp;
        $data['gc_commissions'] += $all_earnings->gift_cheque_tp;
        $data['cash_earnings'] += $all_earnings->pairing_bonus_rs;
        $data['gc_commissions'] += $all_earnings->gift_cheque_rs;
        $data['witholding_tax'] = $data['cash_earnings'] * $this->settings->witholding_tax;
        $data['cash_earnings'] = $data['cash_earnings'] * (1 - $this->settings->witholding_tax);
        $data['igpsm_earnings'] = $data['cash_earnings'] + $data['gc_commissions'];
        $data['unilevel_witholding_tax'] = $all_earnings->unilevel_commission * ($this->settings->witholding_tax);
        $data['unilevel_earnings'] = $all_earnings->unilevel_commission * (1 - $this->settings->witholding_tax);
        
        $current_date = date("Y-m-d 00:00:00");
        $current_datetime = date("Y-m-d H:i:s");

        $last_encashment = date("Y-m-d H:i:s",strtotime($this->settings->last_encashment_timestamp));
        $sql_igpsm_earnings = "SELECT
                sum(`amount`) as 'amount'
                FROM `tr_member_acct_credit_logs`
                WHERE `insert_timestamp` > '{$last_encashment}' AND
                    `insert_timestamp` <= NOW() AND
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
        
        $firstday_datetime = date("Y-m-d H:i:s",mktime(0, 0, 0, date('m'), 1, date('Y')));

        $sql_monthly_unilevel = "SELECT
                sum(`amount`) as `amount`
                FROM `tr_member_acct_credit_logs`
                WHERE `insert_timestamp` >= '{$firstday_datetime}' AND
                    `insert_timestamp` <= NOW() AND
                    `transaction_code` IN (105) AND ";

        if($account_id != ""){
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

        return $data;
    }
	
	
	/* -- FOR MEMBER ENCASHMENTS -- */
	function get_member_encashments($where = null, $limit = null, $orderby = null, $fields = null) {
        $query = $this->fetch('member_encashments', $fields, $where, $orderby, $limit);
        $row = $query->result();
        $query->free_result();
        return $row;
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
		
}    

// end of file