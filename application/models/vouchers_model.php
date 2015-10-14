<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Vouchers_model extends Base_model {
	function __construct()
	{
		// call the base model constructor
		parent::__construct();

		// assign the table for this model
		$this->_TABLES = array(				
			'is_voucher_products' => 'is_voucher_products',
			'is_voucher_types' => 'is_voucher_types',
			'account_voucher_types' => 'rf_account_voucher_types',
			'account_voucher_products' => 'rf_account_voucher_products',
			'member_account_vouchers' => 'cm_member_account_vouchers',
			'member_account_voucher_products' => 'cm_member_account_voucher_products',
			'voucher_redemption_logs' => 'tr_voucher_redemption_logs'
		);
	}
	
	/* -- FOR MEMBER VOUCHERS -- */
	function get_member_account_vouchers($where = null, $limit = null, $orderby = null, $fields = null)
	{
		$query = $this->fetch('member_account_vouchers', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
	}
	
	function insert_member_account_vouchers($data) 
	{
		return $this->insert('member_account_vouchers', $data);
	}

	function update_member_account_vouchers($data, $where) 
	{
		return $this->update('member_account_vouchers', $data, $where);
	}

	function delete_member_account_vouchers($where) 
	{
		return $this->delete('member_account_vouchers', $where);
	}
	
	function get_member_account_vouchers_count($where = null) {
		// do a sql count instead of row count
		$query = $this->fetch('member_account_vouchers', 'count(1) as cnt', $where);
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}
	
	function get_member_account_voucher_by_id($voucher_id)
	{
		$result = $this->get_member_account_vouchers(array('voucher_id' => $voucher_id));
		$row = NULL;
		if (count($result) > 0) {
			$row = $result[0];
		}
		return $row;
	}
	
	function get_member_account_voucher_by_code($voucher_code)
	{
		$result = $this->get_member_account_vouchers(array('voucher_code' => $voucher_code));
		$row = NULL;
		if (count($result) > 0) {
			$row = $result[0];
		}
		return $row;
	}

    function generate_account_voucher($type_id, $member_id, $account_id, $from_account_id)
    {
        $voucher_type = $this->get_account_voucher_type_by_id($type_id);

        $generate_length = 6;
        $voucher_code = "";
        do
        {
            $voucher_code = substr(md5(time().rand(0,100)),-$generate_length);
			$where = "voucher_code LIKE '%{$voucher_code}%' ";
			$existing = $this->get_member_account_vouchers_count($where);
        } while($existing > 0);
        $voucher_code = $voucher_type->code . "-" . $voucher_code;
        $voucher_code = strtoupper($voucher_code);

        $generate_length = 8;
        $confirmation_code = substr(md5(rand(0,100).time()),$generate_length);
        $confirmation_code = strtoupper($confirmation_code);

		// check sms_notfication status
		$where = array(
			'member_id' => $member_id,
			'account_id' => $account_id
		);
		$member_account_details =  $this->members_model->get_member_accounts($where);
		$member_account_details =  $member_account_details[0];
				
		$is_sms_sent = $member_account_details->sms_notification;
		
        $this->insert_member_account_vouchers(array(
            'voucher_code' => $voucher_code,
            'confirmation_code' => $confirmation_code,
            'voucher_type_id' => $type_id,
            'member_id' => $member_id,
            'original_member_id' => $member_id,
            'original_account_id' => $account_id,
            'status' => 'ACTIVE',
			'sms_sent' => $is_sms_sent
        ));

        $voucher_id = $this->db->insert_id();
        $voucher_details = $this->get_member_account_vouchers(array('voucher_id' => $voucher_id));
        $details_after = json_encode($voucher_details);
        $log_data = array(
            'member_id' => $member_id,
            'module_name' => "NEW MEMBER ACCOUNT VOUCHER",
            'table_name' => "cm_member_account_vouchers",
            'action' => "ADD",
            'details_before' => '',
            'details_after' => $details_after,
            'remarks' => "generate new account voucher"
        );
        $this->tracking_model->insert_logs('members', $log_data);

		//// load members model
		//$this->load->model('members_model');
		//
		//// send sms
		//$member_details = $this->members_model->get_member_by_id($member_id);
		//$where = array(
		//	'member_id' => $member_id,
		//	'account_id' => $account_id
		//);
		//$member_account_details =  $this->members_model->get_member_accounts($where);
		//$member_account_details =  $member_account_details[0];
		//
		//if ($member_account_details->sms_notification == 1) {
        //
		//	$data = array(
		//		'voucher_code' => $voucher_code,
		//		'confirmation_code' => $confirmation_code,
		//		'voucher_type_code' => $voucher_type->code,
		//		'member_id' => $member_id,				
		//		'account_id' => $account_id,
		//		'remarks' => "New {$voucher_type->code} Voucher with Voucher Code {$voucher_code} has been credited to {$account_id}."
		//	);
		//
		//	$sms_data = array(
		//		"member_id"=>$member_id,
		//		"mobile_number"=>$member_details->mobile_number,
		//		"type"=>"text-blast-generic-message",
		//		"params"=>$data
		//	);
        //
		//	//send sms to user
		//	Modules::run('jobs/notifications/send_sms',$sms_data);
		//}

		//if ($type_id == 3) {
		//	// load members model
		//	$this->load->model('members_model');
		//	
		//	// send sms
		//	$member_details = $this->members_model->get_member_by_id($member_id);
		//	$where = array(
		//		'member_id' => $member_id,
		//		'account_id' => $account_id
		//	);
		//	$member_account_details =  $this->members_model->get_member_accounts($where);
		//	$member_account_details =  $member_account_details[0];
		//	
		//	if ($member_account_details->sms_notification == 1) {
		//	
		//		$data = array(
		//			//'voucher_code' => $voucher_code,
		//			//'confirmation_code' => $confirmation_code,
		//			//'voucher_type_code' => $voucher_type->code,
		//			//'member_id' => $member_id,				
		//			//'account_id' => $account_id,
		//			'remarks' => "One (1) {$voucher_type->code} Voucher with Voucher Code {$voucher_code} has been credited to {$account_id}."
		//		);
		//	
		//		$sms_data = array(
		//			"member_id"=>$member_id,
		//			"mobile_number"=>$member_details->mobile_number,
		//			"type"=>"text-blast-generic-message",
		//			"params"=>$data
		//		);
		//	
		//		//send sms to user
		//		Modules::run('jobs/notifications/send_sms',$sms_data);
		//	}
		//}

		return $voucher_id;
    }
	
	/* -- FOR MEMBER VOUCHER PRODUCTS -- */
	function get_member_account_voucher_products($where = null, $limit = null, $orderby = null, $fields = null)
	{
		$query = $this->fetch('member_account_voucher_products', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
	}
	
	function insert_member_account_voucher_products($data) 
	{
		return $this->insert('member_account_voucher_products', $data);
	}

	function update_member_account_voucher_products($data, $where) 
	{
		return $this->update('member_account_voucher_products', $data, $where);
	}

	function delete_member_account_voucher_products($where) 
	{
		return $this->delete('member_account_voucher_products', $where);
	}
	
	function get_member_account_voucher_products_count($where = null) {
		// do a sql count instead of row count
		$query = $this->fetch('member_account_voucher_products', 'count(1) as cnt', $where);
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}
	
	/* -- FOR ACCOUNT VOUCHERS -- */
	function get_account_voucher_types($where = null, $limit = null, $orderby = null, $fields = null) 
	{
		$query = $this->fetch('account_voucher_types', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
    }

	function insert_account_voucher_types($data) 
	{
		return $this->insert('account_voucher_types', $data);
	}

	function update_account_voucher_types($data, $where) 
	{
		return $this->update('account_voucher_types', $data, $where);
	}

	function delete_account_voucher_types($where) 
	{
		return $this->delete('account_voucher_types', $where);
	}
	
	function get_account_voucher_type_by_id($voucher_type_id) 
	{
		$result = $this->get_account_voucher_types(array('voucher_type_id' => $voucher_type_id));
		$row = NULL;
		if (count($result) > 0) {
			$row = $result[0];
		}
		return $row;
	}
	
	function get_account_voucher_type_by_code($voucher_type_code) 
	{
		$result = $this->get_account_voucher_types(array('code' => $voucher_type_code));
		$row = NULL;
		if (count($result) > 0) {
			$row = $result[0];
		}
		return $row;
	}
	
	function get_account_voucher_type_count($where = null) {
		// do a sql count instead of row count
		$query = $this->fetch('account_voucher_types', 'count(1) as cnt', $where);
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}
	
	/* -- FOR ACCOUNT VOUCHER PRODUCTS -- */
	
	function get_account_voucher_products($where = null, $limit = null, $orderby = null, $fields = null) 
	{
		$query = $this->fetch('account_voucher_products', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
    }

	function insert_account_voucher_products($data) 
	{
		return $this->insert('account_voucher_products', $data);
	}

	function update_account_voucher_products($data, $where) 
	{
		return $this->update('account_voucher_products', $data, $where);
	}

	function delete_account_voucher_products($where) 
	{
		return $this->delete('account_voucher_products', $where);
	}
		
	function get_account_voucher_product_count($where = null) {
		// do a sql count instead of row count
		$query = $this->fetch('account_voucher_products', 'count(1) as cnt', $where);
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}
	
	/* -- FOR VOUCHER PRODUCTS MODULE -- */	
	function get_is_voucher_products($where = null, $limit = null, $orderby = null, $fields = null) {
        $query = $this->fetch('is_voucher_products', $fields, $where, $orderby, $limit);
        $row = $query->result();
        $query->free_result();
        return $row;
    }
	
	 function get_is_voucher_products_count($where = null) {
        // do a sql count instead of row count
        $query = $this->fetch('is_voucher_products', 'count(1) as cnt', $where);
        $row = $query->first_row();
        $query->free_result();
        return $row->cnt;
    }
	
	function get_is_voucher_product_by_id($voucher_product_id) {
        $result = $this->get_is_voucher_products(array('voucher_product_id' => $voucher_product_id));
        $row = NULL;
        if (count($result) > 0) {
            $row = $result[0];
        }
        return $row;
    }

	function get_is_voucher_product_by_name($voucher_product_name) {
        $result = $this->get_is_voucher_products(array('voucher_product_name' => $voucher_product_name));
        $row = NULL;
        if (count($result) > 0) {
            $row = $result[0];
        }
        return $row;
    }

	function insert_is_voucher_products($data) 
    {
        return $this->insert('is_voucher_products', $data);
    }
	
	function update_is_voucher_products($data, $where) 
    {
        return $this->update('is_voucher_products', $data, $where);
    }

	function delete_is_voucher_products($where) 
    {
        return $this->delete('is_voucher_products', $where);
    }
	
	/* -- FOR VOUCHER TYPES MODULE -- */	
	function get_is_voucher_types($where = null, $limit = null, $orderby = null, $fields = null) {
        $query = $this->fetch('is_voucher_types', $fields, $where, $orderby, $limit);
        $row = $query->result();
        $query->free_result();
        return $row;
    }
	
	 function get_is_voucher_types_count($where = null) {
        // do a sql count instead of row count
        $query = $this->fetch('is_voucher_types', 'count(1) as cnt', $where);
        $row = $query->first_row();
        $query->free_result();
        return $row->cnt;
    }
	
	function get_is_voucher_type_by_id($voucher_type_id) {
        $result = $this->get_is_voucher_types(array('voucher_type_id' => $voucher_type_id));
        $row = NULL;
        if (count($result) > 0) {
            $row = $result[0];
        }
        return $row;
    }

	function get_is_voucher_type_by_code($code) {
        $result = $this->get_is_voucher_types(array('code' => $code));
        $row = NULL;
        if (count($result) > 0) {
            $row = $result[0];
        }
        return $row;
    }

	function insert_is_voucher_types($data) 
    {
        return $this->insert('is_voucher_types', $data);
    }
	
	function update_is_voucher_types($data, $where) 
    {
        return $this->update('is_voucher_types', $data, $where);
    }	

	function delete_is_voucher_types($where) 
    {
        return $this->delete('is_voucher_types', $where);
    }
	
	/* -- FOR VOUCHER REDEMPTION LOGS -- */
	function get_voucher_redemption_logs($where = null, $limit = null, $orderby = null, $fields = null)
	{
		$query = $this->fetch('voucher_redemption_logs', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
	}
	
	function insert_voucher_redemption_logs($data) 
	{
		return $this->insert('voucher_redemption_logs', $data);
	}

	function update_voucher_redemption_logs($data, $where) 
	{
		return $this->update('voucher_redemption_logs', $data, $where);
	}

	function delete_voucher_redemption_logs($where) 
	{
		return $this->delete('voucher_redemption_logs', $where);
	}
	
	function get_voucher_redemption_logs_count($where = null) {
		// do a sql count instead of row count
		$query = $this->fetch('voucher_redemption_logs', 'count(1) as cnt', $where);
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}
	
	function get_voucher_redemption_log_by_id($voucher_redemption_id)
	{
		$result = $this->get_voucher_redemption_logs(array('voucher_redemption_id' => $voucher_redemption_id));
		$row = NULL;
		if (count($result) > 0) {
			$row = $result[0];
		}
		return $row;
	}
	
}    

// end of file