<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mobile extends Base_Controller {

	function __construct() {
		parent::__construct();
		$this->load->model('members_model');
		$this->load->model('contents_model');
        $this->load->model('cards_model');	
    }

	private $_err_codes = array(
		'1' => 'Invalid login',
		'2' => 'Member not found',
		'3' => 'Invalid token',
		'4' => 'Invalid Session',
        '5' => 'Account not found',
        '6' => 'Upline account not found',
        '7' => 'Sponsor account not found',
        '8' => 'Pairing details not found',
        '9' => 'Card type not found',
        '10' => 'No RS cards found',
        '11' => 'No Orders found',
        '12' => 'Invalid voucher type',
        '13' => 'No voucher found',
        '14' => 'No payout period found',
        '15' => 'No account found',
        '16' => 'Head account not found',
        '17' => 'No Commission transfer found',
        '18' => 'Account type data not found',
	);

	private function _return_err_code($code)
	{
		$this->return_json(0, $this->_err_codes[$code], array('err_code' => $code));
	}

	private function _check_token_validity($token)
	{
		$now = date('Y-m-d h:i:s');
		$where = "session_token = '$token' AND session_token_validity > '$now' ";
		$user_account = $this->members_model->get_member_user_accounts($where);
		return (sizeof($user_account) > 0) ? $user_account[0] : false;
	}

	public function login($user, $pass)
	{

		if(empty($user) || empty($pass))
		{
			$this->_return_err_code(1);
			return;
		}

		$member = $this->members_model->get_member_user_accounts(array(
			'username' => $user,
			'password' => $pass
		));
        

		if(sizeof($member) == 0)
		{
			$this->_return_err_code(2);
			return;
		}

		// generated session token
		$session_token = sha1($member[0]->member_id . date("Y-m-d h:i:s"));
		$session_expire = date("Y-m-d h:i:s", mktime(date("h"), date("i"), date("s"), date("m"), date("d")+1, date("Y"))); // 1 day expire

		$this->members_model->update_member_user_accounts(array(
			'session_token' => $session_token,
			'session_token_validity' => $session_expire
		), array(
			'member_id' => $member[0]->member_id
		));
		
		$this->return_json(1, 'Success', array('token' => $session_token));
		return;
	}

	public function get_announcements($token, $count)
	{
		if(empty($token))
		{
			$this->_return_err_code(3);
			return;
		}

		$user_account = $this->_check_token_validity($token);
		if(!$user_account)
		{
			$this->_return_err_code(4);
			return;
		}

		$announcements = $this->contents_model->get_announcements(array("is_published" => 1), array('rows' => 2, 'offset' => 0), "insert_timestamp DESC", array("announcement_id", "title", "body"));
		foreach($announcements as $announce)
		{
			$announce->title = strip_tags($announce->title);
			$announce->body = strip_tags($announce->body, '<p><img>');
		}
		
		$this->return_json(1, 'Success', array('announcements' => $announcements));
		return;
	}

	public function get_server_settings($token)
	{
		if(empty($token))
		{
			$this->_return_err_code(3);
			return;
		}

		$user_account = $this->_check_token_validity($token);
		if(!$user_account)
		{
			$this->_return_err_code(4);
			return;
		}

		$slugs = array('annual_maintenance', 'monthly_maintenance');
		$where = "slug IN ('" . implode("','", $slugs) . "')";
		$_settings = $this->settings_model->get_settings($where, null, null, array('slug', 'value'));
		$settings = array();
		foreach($_settings as $set) $settings[] = array('slug' => $set->slug, 'value' => $set->value);

		$this->return_json(1, 'Success', array('settings' => $settings));
		return;
	}

	public function get_member_details($token, $member_id = null)
	{
		if(empty($token))
		{
			$this->_return_err_code(3);
			return;
		}

		$user_account = $this->_check_token_validity($token);
		if(!$user_account)
		{
			$this->_return_err_code(4);
			return;
		}

		$mem_id = (empty($member_id) || is_null($member_id))?$user_account->member_id:$member_id;

		// get member details
		$member_details = $this->members_model->get_members(array('member_id' => $mem_id), null, null, array('member_id', 'first_name', 'last_name'));
		if(sizeof($member_details) == 0)
		{
			$this->_return_err_code(2);
			return;
		}

		// get member achievement
		$achievement = $this->contents_model->get_featured_members(array('member_id' => $user_account->member_id));
		$title = 'REGULAR';
		if(sizeof($achievement) > 0)
		{
			$ach = $this->contents_model->get_member_achievements(array('member_achievement_id' => $achievement[0]->achievement_id));
			$title = $ach[0]->achievement_name;
		}
		$member_details[0]->title = $title;

		$this->return_json(1, 'Success', array('member_details' => $member_details[0]));
		return;
	}

	public function get_member_funds($token)
	{
		if(empty($token))
		{
			$this->_return_err_code(3);
			return;
		}

		$user_account = $this->_check_token_validity($token);
		if(!$user_account)
		{
			$this->_return_err_code(4);
			return;
		}

		// get member details
		$member_details = $this->members_model->get_members(array('member_id' => $user_account->member_id), null, null, array('funds', 'gift_cheques', 'gcep', 'cpoints'));
		if(sizeof($member_details) == 0)
		{
			$this->_return_err_code(2);
			return;
		}

		$this->return_json(1, 'Success', array('member_details' => $member_details[0]));
		return;	
	}

	public function get_accounts($token)
	{
		if(empty($token))
		{
			$this->_return_err_code(3);
			return;
		}

		$user_account = $this->_check_token_validity($token);
		if(!$user_account)
		{
			$this->_return_err_code(4);
			return;
		}

		$accounts = $this->members_model->get_member_accounts(array('member_id' => $user_account->member_id), null, null, array('account_id', 'account_status_id', 'sms_notification'));
		if(sizeof($accounts) == 0)
		{
			$this->_return_err_code(15);
			return;
		}

		$this->return_json(1, 'Success', array('accounts' => $accounts));
		return;	
	}
    
    public function get_account_details($token, $account_id)
	{
		if(empty($token))
		{
			$this->_return_err_code(3);
			return;
		}

		$user_account = $this->_check_token_validity($token);
		if(!$user_account)
		{
			$this->_return_err_code(4);
			return;
		}

		// get member details
		$member_details = $this->members_model->get_members(array('member_id' => $user_account->member_id), null, null, array('first_name', 'last_name'));
		if(sizeof($member_details) == 0)
		{
			$this->_return_err_code(2);
			return;
		}

        // get account details
        $data = array(
            'member_id' => $user_account->member_id,
            'account_id' => $account_id
        );
        
        $member_account_details = $this->members_model->get_member_accounts($data, null, null, array('member_id', 'account_id', 'sponsor_id', 'upline_id', 'account_type_id', 'account_status_id', 'node_address', 'uni_node', 'monthly_maintenance_ctr', 'annual_maintenance_ctr', 'ms_monthly_maintenance_ctr', 'ms_annual_maintenance_ctr', 'sms_notification', 'insert_timestamp'));
        
        if(sizeof($member_account_details) == 0)
        {
            $this->_return_err_code(5);
            return;
        }

		// get upline account details
		$upline_account_details = $this->members_model->get_member_accounts(array('upline_id' => $member_account_details[0]->upline_id), null, null, array('member_id'));
		
        if(sizeof($upline_account_details) == 0)
        {
            $this->_return_err_code(6);
            return;
        }
        
        // get upline member details
        $upline_member_details = $this->members_model->get_members(array('member_id' => $upline_account_details[0]->member_id), null, null, array('first_name', 'last_name'));
		if(sizeof($upline_member_details) == 0)
		{
			$this->_return_err_code(2);
			return;
		}
        
        
        // get sponsor account details
		$sponsor_account_details = $this->members_model->get_member_accounts(array('upline_id' => $member_account_details[0]->sponsor_id), null, null, array('member_id'));
		
        if(sizeof($sponsor_account_details) == 0)
        {
            $this->_return_err_code(7);
            return;
        }
        
        // get upline member details
        $sponsor_member_details = $this->members_model->get_members(array('member_id' => $sponsor_account_details[0]->member_id), null, null, array('first_name', 'last_name'));
		if(sizeof($sponsor_member_details) == 0)
		{
			$this->_return_err_code(2);
			return;
		}
        
        // get account position
        $account_position = 'R';
        
        if (substr($member_account_details[0]->node_address, -1) == '2')
            $account_position = 'L';
        
        $member_account_details[0]->upline_name = ucfirst($upline_member_details[0]->first_name) . ' ' . ucfirst($upline_member_details[0]->last_name);
        $member_account_details[0]->sponsor_name = ucfirst($sponsor_member_details[0]->first_name) . ' ' . ucfirst($sponsor_member_details[0]->last_name);      
        $member_account_details[0]->position = $account_position;

        // get account type code and name
        $account_type_data = $this->cards_model->get_card_types(array('card_type_id' => $member_account_details[0]->account_type_id), null, null, array('code', 'name', 'color'));
        if(sizeof($account_type_data) == 0)
		{
			$this->_return_err_code(18);
			return;
		}
		$member_account_details[0]->account_type_code = $account_type_data[0]->code;
		$member_account_details[0]->account_type_name = $account_type_data[0]->name;

        // get account type base color
        $member_account_details[0]->account_base_color = strtolower($this->get_format_color_hex($account_type_data[0]->color));

        // get account type up1 color
        $up1_data = $this->members_model->get_member_account_upgrades(array('base_account_id' => $account_id, 'upgrade_type' => 'UP1'));
        $up1_type_data = $this->cards_model->get_card_types(array('code' => 'UP1'), null, null, array('code', 'name', 'color'));
        if(sizeof($up1_data) == 0)
			$up1_color = strtolower($this->get_format_color_hex(""));
		else
			$up1_color = strtolower($this->get_format_color_hex($up1_type_data[0]->color));

        $member_account_details[0]->account_up1_color = $up1_color;

        // get account type p2p color
        $p2p_data = $this->members_model->get_member_account_upgrades(array('base_account_id' => $account_id, 'upgrade_type' => 'P2P'));
        $p2p_type_data = $this->cards_model->get_card_types(array('code' => 'P2P'), null, null, array('code', 'name', 'color'));
        if(sizeof($p2p_data) == 0)
			$p2p_color = strtolower($this->get_format_color_hex(""));
		else
			$p2p_color = strtolower($this->get_format_color_hex($p2p_type_data[0]->color));

        $member_account_details[0]->account_p2p_color = $p2p_color;

        // get account type dollar color
        $member_account_details[0]->account_dollar_color = strtolower($this->get_format_color_hex(""));

        // get count per count
		$left = $this->get_downline_count_expanded_by_node_address_obj($member_account_details[0]->node_address, 'l');
		$right = $this->get_downline_count_expanded_by_node_address_obj($member_account_details[0]->node_address, 'r');

		$pairing_data = array();
		$pairing_data[] = array(
			'card_type_code' => 'cd',
			'left_count' => $left['cd'],
			'right_count' => $right['cd']
		);
		$pairing_data[] = array(
			'card_type_code' => 'erhm',
			'left_count' => $left['erhm'],
			'right_count' => $right['erhm']
		);
		$pairing_data[] = array(
			'card_type_code' => 'up1',
			'left_count' => $left['up1'],
			'right_count' => $right['up1']
		);
		$pairing_data[] = array(
			'card_type_code' => 'p2p',
			'left_count' => $left['p2p'],
			'right_count' => $right['p2p']
		);

        $member_account_details[0]->account_pairing = $pairing_data;
        
		$this->return_json(1, 'Success', array('member_account_details' => $member_account_details[0]));
		return;
	}

	private function get_downline_count_expanded_by_node_address_obj($node_address, $side = 'both')
	{
		$side = strtolower($side);
        $side_inset = '';
        if ($side == 'l') $side_inset = '2';
        if ($side == 'r') $side_inset = '1';


        $p2p_where = "AND upgrade_type = 'P2P'";

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

        //erhm
        $this->db->select("count(1) as cnt");
        $this->db->where("node_address like '{$node_address}{$side_inset}%' {$erhm_where}");
        $this->db->from("cm_member_accounts");
        $query = $this->db->get();
        $row = $query->first_row();
        if(sizeof($row)>0)
            $erhm_count = $row->cnt;
        $query->free_result();

        //count completed CD as ERHM
        $this->db->select("count(1) as cnt");
        $this->db->where("node_address like '{$node_address}{$side_inset}%' {$cd_where} AND credit_flag = '1'");
        $this->db->from("cm_member_accounts");
        $this->db->join('trg_commission_deduct_completion','trg_commission_deduct_completion.account_id = cm_member_accounts.account_id','left');
        $query = $this->db->get();
        $row = $query->first_row();
        if(sizeof($row)>0)
            $erhm_count += $row->cnt;
        $query->free_result();

        //cd
        $this->db->select("count(1) as cnt");
        $this->db->where("node_address like '{$node_address}{$side_inset}%' {$cd_where}");
        $this->db->from("cm_member_accounts");
        $query = $this->db->get();
        $row = $query->first_row();
        if(sizeof($row)>0)
            $cd_count = $row->cnt;
        $query->free_result();

        //up1
        $this->db->select("count(1) as cnt");
        $this->db->where("node_address like '{$node_address}{$side_inset}%' {$up1_where}");
        $this->db->from("cm_member_accounts");
        $this->db->join('cm_member_account_upgrades','account_id = base_account_id','left');
        $query = $this->db->get();
        $row = $query->first_row();
        if(sizeof($row)>0)
            $up1_count = $row->cnt;
        $query->free_result();

        //p2p
        $this->db->select("count(1) as cnt");
        $this->db->where("node_address like '{$node_address}{$side_inset}%' {$p2p_where}");
        $this->db->from('cm_member_accounts');
        $this->db->join('cm_member_account_upgrades','account_id = base_account_id','left');
        $query = $this->db->get();
        $row = $query->first_row();
        if(sizeof($row)>0)
            $p2p_count = $row->cnt;
        $query->free_result();
        
        $position_count = array(
        	'cd' => $cd_count,
        	'erhm' => $erhm_count,
        	'up1' => $up1_count,
        	'p2p' => $p2p_count
        );

        return $position_count;
	}

	private function get_format_color_hex($color) {
		if(substr($color, 0, 1) == "#") {
			return substr($color, 1, 6);
		} elseif(strtolower($color) == "red") {
			return "FF0000";
		} elseif(strtolower($color) == "gray") {
			return "808080";
		} elseif(strtolower($color) == "violet") {
			return "8D38C9";
		} elseif(strtolower($color) == "orange") {
			return "FFA500";
		} elseif(strtolower($color) == "blue") {
			return "0000FF";
		} elseif(strtolower($color) == "darkgreen") {
			return "008000";
		} else {
			return "808080";
		}
	}

    public function get_member_account_earnings($token, $account_id)
    {
        if(empty($token))
		{
			$this->_return_err_code(3);
			return;
		}

		$user_account = $this->_check_token_validity($token);
		if(!$user_account)
		{
			$this->_return_err_code(4);
			return;
		}

		// get member details
		$member_details = $this->members_model->get_members(array('member_id' => $user_account->member_id), null, null, array('first_name', 'last_name'));
		if(sizeof($member_details) == 0)
		{
			$this->_return_err_code(2);
			return;
		}
        
        if (strtolower($account_id) == 'all') {
            $member_account_earnings = $this->members_model->get_all_member_earnings($user_account->member_id);
        } else {
            $member_account_earnings = $this->members_model->get_all_member_earnings($user_account->member_id, $account_id);
        }    
        
        $this->return_json(1, 'Success', array('member_account_earnings' => $member_account_earnings));
		return;
    }

    public function get_account_pairing_per_type($token, $account_id, $card_type)
    {
        
        if(empty($token))
		{
			$this->_return_err_code(3);
			return;
		}

		$user_account = $this->_check_token_validity($token);
		if(!$user_account)
		{
			$this->_return_err_code(4);
			return;
		}

        // get card type details
        $card_type_details = $this->cards_model->get_card_type_by_code($card_type);
        if(!$card_type_details)
		{
			$this->_return_err_code(9);
			return;
		}
        
        $where = array(
            'member_id' => $user_account->member_id,
            'card_type_id' => $card_type_details->card_type_id
            );
        
        if (strtolower($account_id) != 'all') 
            $where['account_id'] = $account_id;
            
        $member_account_pairing_details = $this->members_model->get_member_account_pairing($where, null, null, array('member_id', 'account_id', 'left_count','right_count','total_left', 'total_right', 'pair_count', 'flushout', 'gc_count', 'gc_pair'));
        
        if (sizeof($member_account_pairing_details) == 0) {
       	    $this->_return_err_code(8);
			return;
        }
        
        $member_account_pairing_details[0]->card_type_code = $card_type_details->code;
        $member_account_pairing_details[0]->card_type_name = $card_type_details->name;
        
        $this->return_json(1, 'Success', array('member_account_pairing_details' => $member_account_pairing_details));
		return;
    }
    
    public function get_rs_history($token, $account_id, $start_date = NULL, $end_date = NULL, $limit)
    {
        if(empty($token))
		{
			$this->_return_err_code(3);
			return;
		}

		$user_account = $this->_check_token_validity($token);
		if(!$user_account)
		{
			$this->_return_err_code(4);
			return;
		}
        
        // get rs
        $where = "member_id = {$user_account->member_id}";
        
        if (!($start_date == 'NULL') && !($start_date == 'NULL')) {
            $where .= " AND (DATE(used_timestamp) BETWEEN '{$start_date}' AND '{$end_date}') ";
        } else if (($start_date == NULL) && ($start_date != NULL)) {
            $where .= " AND (DATE(used_timestamp) <= '{$end_date}') ";
        } else if (($start_date != NULL) && ($start_date == NULL)) {
            $where .= " AND (DATE(used_timestamp) >= '{$start_date}') ";
        }
        
        if (strtolower($account_id) != 'all')
            $where .= " AND account_id = '{$account_id}'";
        
        $rs_card_list = $this->cards_model->get_rs_card($where, array('rows' => $limit, 'offset' => 0), 'used_timestamp DESC', array('card_id', 'card_code', 'status', 'type', 'member_id', 'account_id', 'use_type', 'used_timestamp'));
        
         if (sizeof($rs_card_list) == 0) {
       	    $this->_return_err_code(10);
			return;
        }
        
        $this->return_json(1, 'Success', array('rs_card_list' => $rs_card_list));
		return;        
    }

    public function get_member_orders($token, $start_date = NULL, $end_date = NULL, $limit, $status, $payment_type)
    {
        if(empty($token))
		{
			$this->_return_err_code(3);
			return;
		}

		$user_account = $this->_check_token_validity($token);
		if(!$user_account)
		{
			$this->_return_err_code(4);
			return;
		}
        
        // get orders
        $where = "member_id = {$user_account->member_id}";
        
        if (!($start_date == 'NULL') && !($start_date == 'NULL')) {
            $where .= " AND (DATE(a.insert_timestamp) BETWEEN '{$start_date}' AND '{$end_date}') ";
        } else if (($start_date == NULL) && ($start_date != NULL)) {
            $where .= " AND (DATE(a.insert_timestamp) <= '{$end_date}') ";
        } else if (($start_date != NULL) && ($start_date == NULL)) {
            $where .= " AND (DATE(a.insert_timestamp) >= '{$start_date}') ";
        }
        
        if (strtolower($status) != 'all')
            $where .= " AND a.status = UPPER('{$status}')";

        if (strtolower($payment_type) != 'all')
            $where .= " AND LOWER(transaction_type) = '{$payment_type}'";
        
        
    	$sql = "SELECT
					a.transaction_id,
					d.transaction_code,
					a.payment_method as transaction_type,
					a.amount as total_amount,
					d.status,
					d.facility_id,
					d.releasing_facility_id,
					a.insert_timestamp,
					a.remarks,
					d.ar_number
					from is_payment_transaction_details a
					left join is_payment_transactions d on (a.transaction_id = d.transaction_id)
				WHERE
					{$where}
				ORDER BY
					d.insert_timestamp DESC
				LIMIT
                    {$limit}";	
		
		$query = $this->db->query($sql);
        $member_orders = $query->result();
        
        if (sizeof($member_orders) == 0) {
       	    $this->_return_err_code(11);
			return;
        }
        
        $this->return_json(1, 'Success', array('member_orders' => $member_orders));
		return;        
    }
    
    
    public function get_member_order_details($token, $transaction_id, $payment_type)
    {
        if(empty($token))
		{
			$this->_return_err_code(3);
			return;
		}

		$user_account = $this->_check_token_validity($token);
		
        var_dump($user_account);
        
        if(!$user_account)
		{
			$this->_return_err_code(4);
			return;
		}
        
        // get orders
        $where = "member_id = {$user_account->member_id} AND a.transaction_id = {$transaction_id} AND LOWER(a.payment_method) = '{$payment_type}'";
        
        
    	$sql = "SELECT
					a.transaction_id,
					d.transaction_code,
					a.payment_method as transaction_type,
					a.amount as total_amount,
					d.status,
					d.facility_id,
					d.releasing_facility_id,
					a.insert_timestamp,
					a.remarks,
					d.ar_number
					from is_payment_transaction_details a
					left join is_payment_transactions d on (a.transaction_id = d.transaction_id)
				WHERE
					{$where}
                ";	
		
		$query = $this->db->query($sql);
        $member_order_details = $query->result();
        
        if (sizeof($member_order_details) == 0) {
       	    $this->_return_err_code(11);
			return;
        }
        
        $this->return_json(1, 'Success', array('member_order_details' => $member_order_details));
		return;        
    }
    
    public function get_member_vouchers($token, $start_date = NULL, $end_date = NULL, $limit, $status, $voucher_type)
    {
        if(empty($token))
		{
			$this->_return_err_code(3);
			return;
		}

		$user_account = $this->_check_token_validity($token);
		if(!$user_account)
		{
			$this->_return_err_code(4);
			return;
		}
        
        // get orders 
        $where = "member_id = {$user_account->member_id}";
        
        if (!($start_date == 'NULL') && !($start_date == 'NULL')) {
            $where .= " AND (DATE(a.insert_timestamp) BETWEEN '{$start_date}' AND '{$end_date}') ";
        } else if (($start_date == NULL) && ($start_date != NULL)) {
            $where .= " AND (DATE(a.insert_timestamp) <= '{$end_date}') ";
        } else if (($start_date != NULL) && ($start_date == NULL)) {
            $where .= " AND (DATE(a.insert_timestamp) >= '{$start_date}') ";
        }
        
        if (strtolower($status) != 'all')
            $where .= " AND a.status = UPPER('{$status}')";
            
        // get voucher type id
        $voucher_type_details = $this->vouchers_model->get_account_voucher_type_by_code($voucher_type);
         if (sizeof($voucher_type_details) == 0) {
       	    $this->_return_err_code(12);
			return;
        }
            

        if (strtolower($voucher_type) != 'all')
            $where .= " AND voucher_type_id = {$voucher_type_details[0]->voucher_type_id}";
        
        $limit = array('rows' => $limit, 'offset' => 0);
        $member_vouchers = $this->vouchers_model->get_member_account_vouchers($where, $limit,"insert_timestamp DESC");
        
        if (sizeof($member_vouchers) == 0) {
       	    $this->_return_err_code(13);
			return;
        }
        
        $this->return_json(1, 'Success', array('member_orders' => $member_vouchers));
		return;        
    }
    
    
     public function get_gross_per_cutoff($token, $account_id, $start_date, $end_date)
    {
        if(empty($token))
		{
			$this->_return_err_code(3);
			return;
		}

		$user_account = $this->_check_token_validity($token);
		if(!$user_account)
		{
			$this->_return_err_code(4);
			return;
		}
        
     	$is_current_payout_period = 0;

        if (($start_date != 'NULL') && ($end_date != 'NULL')) {
        	// is start date and end date the current payout period?         
        	$sql = "SELECT 
						payout_period_id, start_date, end_date, is_official, payout_type, status
					FROM 
						po_payout_periods			
					WHERE 
						'{$start_date}' BETWEEN DATE(start_date) AND DATE(end_date) 
        			AND 
        				'{$end_date}' BETWEEN DATE(start_date) AND DATE(end_date) 
        			AND 
        				is_official = 1 
        			AND 
        				payout_type = 'IGPSM'";

			$query = $this->db->query($sql);
			$payout_period_details = $query->result();			
			$query->free_result();

	        if (sizeof($payout_period_details) == 0) {
	       	    $this->_return_err_code(14); // no payout period found
				return;
	        }

	        if ($payout_period_details[0]->status == 'ACTIVE') {
	        	// current IGPSM payout period	
	        	$is_current_payout_period = 1;
	        } 

	    } else {
	    	// current IGPSM payout period
	    	$is_current_payout_period = 1;
	    }    	


	    if ($is_current_payout_period == 1) {

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

			if ((empty($payout_period_result)) ||  ($payout_period_result[0]->payout_period_id == NULL)) {
				// set values to N/A
				$data['gross_igpsm'] = "n/a";
				$data['$gross_gc'] = "n/a";
				$data['$gross_unilevel'] = "n/a";
				$data['$gross_total'] = "n/a";			

			} else {	
				
				$payout_id = $payout_period_result[0]->payout_period_id;
				$start_date = $payout_period_result[0]->start_date;
				$end_date = $payout_period_result[0]->end_date;	

				$table_to_query = 'tr_member_acct_credit_logs';
			}  
		
	    } else {
	    	// check startdate and enddate
	    	$payout_id = $payout_period_details[0]->payout_period_id;
	    	$start_date = $payout_period_details[0]->start_date;	
	    	$end_date = $payout_period_details[0]->end_date;	

	    	// build trh table name based on dates
	    	$table_to_query = 'trh_member_acct_credit_logs_' .  date('Ymd', strtotime($start_date)) . '_' . date('Ymd', strtotime($end_date));

	    }

	    //var_dump($is_current_payout_period .'|'. $start_date .'|'. $end_date .'|'. $table_to_query);

	    // IGPSM
		$sql = "SELECT 
					SUM(amount) as amount
				FROM 
				 	{$table_to_query}
				WHERE 
				 	insert_timestamp BETWEEN '{$start_date}' AND '{$end_date}'
				AND
				 	transaction_code < 105
				AND 
				 	member_id = {$user_account->member_id}
				";

		if (strtolower($account_id) != 'all')
			$sql .= " AND account_id = '{$account_id}'";

		$query = $this->db->query($sql);
		$gross_igpsm_qresult = $query->result();			
		$query->free_result();
	
	
		if ((empty($gross_igpsm_qresult)) ||  ($gross_igpsm_qresult[0]->amount == NULL)) {
			$data['gross_igpsm'] = "0.00";
		} else {	
			//var_dump($gross_igpsm_qresult[0]->amount);
			$data['gross_igpsm'] = number_format($gross_igpsm_qresult[0]->amount, 2);
		}
	
		// GC
		$sql = "SELECT 
				 	SUM(amount) as amount
				FROM 
				 	{$table_to_query}
				WHERE 
				 	insert_timestamp BETWEEN '{$start_date}' AND '{$end_date}'
				AND
				 	transaction_code > 105
				AND 
				 	member_id = {$this->member->member_id}
				AND 
					type = 'GC'";

		if (strtolower($account_id) != 'all')
				$sql .= " AND account_id = '{$account_id}'";			

		$query = $this->db->query($sql);
		$gross_gc_qresult = $query->result();			
		$query->free_result();
	
	
		if ((empty($gross_gc_qresult)) ||  ($gross_gc_qresult[0]->amount == NULL)) {
			$data['gross_gc'] = "0.00";
		} else {				
			$data['gross_gc'] = number_format($gross_gc_qresult[0]->amount, 2);
		}
		
		// Unilevel
		$sql = "SELECT 
				 	SUM(amount) as amount
				FROM 
				 	{$table_to_query}
				WHERE 
				 	insert_timestamp BETWEEN '{$start_date}' AND '{$end_date}'
				AND
				 	transaction_code = 105
				AND 
				 	member_id = {$this->member->member_id}
				";

		if (strtolower($account_id) != 'all')
				$sql .= " AND account_id = '{$account_id}'";		

		$query = $this->db->query($sql);
		$gross_unilevel_qresult = $query->result();			
		$query->free_result();
	
	
		if ((empty($gross_unilevel_qresult)) ||  ($gross_unilevel_qresult[0]->amount == NULL)) {
			$data['gross_unilevel'] = "0.00";
		} else {				
			$data['gross_unilevel'] = number_format($gross_unilevel_qresult[0]->amount, 2);
		}
		
		// TOTAL
		$gross_total = $data['gross_igpsm'] + $data['gross_gc'] + $data['gross_unilevel'];
		$gross_total = number_format($gross_total, 2);
		$data['gross_total'] = $gross_total;	


		$this->return_json(1, 'Success', array('gross_per_cutoff' => $data));
		return;  
    }

    public function get_payout_history($token, $start_date, $end_date)
    {

    	if(empty($token))
		{
			$this->_return_err_code(3);
			return;
		}

		$user_account = $this->_check_token_validity($token);
		if(!$user_account)
		{
			$this->_return_err_code(4);
			return;
		}

    }
    
     public function get_account_voucher_details($token, $voucher_code)
    {

    	if(empty($token))
		{
			$this->_return_err_code(3);
			return;
		}

		$user_account = $this->_check_token_validity($token);
		if(!$user_account)
		{
			$this->_return_err_code(4);
			return;
		}		
    }

    public function get_member_profile($token)
    {

    	if(empty($token))
		{
			$this->_return_err_code(3);
			return;
		}

		$user_account = $this->_check_token_validity($token);
		if(!$user_account)
		{
			$this->_return_err_code(4);
			return;
		}

		$member_details = $this->members_model->get_member_by_id($user_account->member_id);

		if (sizeof($member_details) == 0) {
			$this->_return_err_code(2);
			return;
		}

		$this->return_json(1, 'Success', array('member_details' => $member_details));
		return;	

    }

    public function get_commission_transfer_history($token, $start_date, $end_date, $transfer_type, $status_type)
    {

    	if(empty($token))
		{
			$this->_return_err_code(3);
			return;
		}

		$user_account = $this->_check_token_validity($token);
		if(!$user_account)
		{
			$this->_return_err_code(4);
			return;
		}

		$from_t = strtotime($start_date);
		$to_t = strtotime($end_date);
		
		$where = "(from_member_id = " . $user_account->member_id . " OR to_member_id = " . $user_account->member_id .")";
		
		if(!empty($transfer_type) && strtolower($transfer_type) != 'all')	$where .= " AND type = '" . strtoupper(trim($transfer_type)) . "'";
		if(!empty($status_type) && strtolower($status_type) != 'all')	$where .= " AND status = '" . strtoupper(trim($status_type)) . "'";		
		
		$from_dt = $start_date;
		$to_dt = $end_date;
				
		if ($from_t !== false) $from_dt = date('Y-m-d', $from_t); 
		if ($to_t !== false) $to_dt = date('Y-m-d', $to_t); 
		
		if ($from_t !== false && $to_t !== false)
			$where .= " AND (date_format(insert_timestamp, '%Y-%m-%d') BETWEEN '{$from_dt}' AND '{$to_dt}') ";
		else if ($from_t !== false && $to_t === false)
			$where .= " AND insert_timestamp >= '{$from_dt}'";
		else if ($from_t === false && $to_t !== false)
			$where .= " AND insert_timestamp <= '{$to_dt}'";

		$transfer_history = $this->tracking_model->get_member_transfers($where, '', 'insert_timestamp DESC');

		if (sizeof($transfer_history) == 0) {
			$this->_return_err_code(17);
			return;
		}

		$this->return_json(1, 'Success', array('transfer_history' => $transfer_history));
		return;


    }

    public function get_genealogy_tree($token, $head_account, $levels) {
    	if(empty($token) || empty($head_account) || empty($levels))
		{
			$this->_return_err_code(3);
			return;
		}

		$user_account = $this->_check_token_validity($token);
		if(!$user_account)
		{
			$this->_return_err_code(4);
			return;
		}

		$h_acct = $this->members_model->get_member_accounts(array('account_id' => $head_account, 'member_id' => $user_account->member_id), null, null, array('account_id', 'node_address'));
		if(sizeof($h_acct) == 0)
		{
			$this->_return_err_code(16);
			return;
		}
		$h_acct = $h_acct[0];

		$head_len = strlen($h_acct->node_address);
		$level_depth = strlen($h_acct->node_address) + $levels;

		$nodes = $this->get_downlines($h_acct->node_address, $head_len, $level_depth);
		$this->return_json(1, 'Success', array('nodes' => $nodes));
		return;
    }

    private function get_downlines($node_address, $head_len, $level_depth)
    {
    	$fields = "account_id, member_id, node_address, sms_notification, account_status_id, LENGTH(node_address) - {$head_len} AS level";
    	$where = "node_address LIKE '{$node_address}%' AND LENGTH(node_address) < {$level_depth}";
    	$order = "LENGTH(node_address), node_address ASC";
    	$ret_arr = array();
    	$accts = $this->members_model->get_member_accounts($where, null, $order, $fields);
    	foreach($accts as $acct)
    	{
    		$member_detail = $this->members_model->get_members(array("member_id" => $acct->member_id), null, null, array('last_name', 'first_name'));
    		$acct->last_name = $member_detail[0]->last_name;
    		$acct->first_name = $member_detail[0]->first_name;
    		$ret_arr[$acct->node_address] = $acct;
    	}
    	return $ret_arr;
    }

    public function get_sms_notification($token, $account_id) {
    	if(empty($token))
		{
			$this->_return_err_code(3);
			return;
		}

		$user_account = $this->_check_token_validity($token);
		if(!$user_account)
		{
			$this->_return_err_code(4);
			return;
		}

		if (strtolower($account_id) == 'all') {
			$where = array(
					'member_id' => $user_account->member_id
				);
		} else {
			$where = array(
					'member_id' => $user_account->member_id,
					'account_id' => $account_id
				);
		}

		$account_sms_notification_details = $this->members_model->get_member_accounts($where,'', 'account_id', array('account_id', 'sms_notification'));


		$this->return_json(1, 'Success', array('account_sms_notification_details' => $account_sms_notification_details));
		return;
    }


    public function search_result($searchString = false){

    	$where_in = array();
  
    	    (strlen($searchString)>2)? $result_account_ids = $this->members_model->get_search_result_by_account_id($searchString) : die("error");
    		foreach($result_account_ids as $data){
    			array_push($where_in,$data->member_id);
    		}
    		$result = $this->members_model->get_search_result_by_name($searchString,$where_in);
    		$final = array();
    		foreach($result as $data){
    			
    			$account_id = $this->members_model->get_search_final_result($data->member_id);
    			if($account_id != NULL){
    			$final[] = array('fullname' => $data->fullname,
    					         'member_id' => $data->member_id,
    					         'account_id' => $account_id);
    			}
    		}

    		//echo json_encode($final);
			
			$this->return_json(1, 'Success', array('result' => $final));
			return;


    		



    }

}