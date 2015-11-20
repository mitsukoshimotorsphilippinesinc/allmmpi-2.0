<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Tracking_model extends Base_model
{
    function __construct() 
    {
        parent::__construct();
        $this->_TABLES = array(            
			'admin_logs'=>'tr_admin_logs',
			'inventory_logs'=>'tr_inventory_logs',			
            'credit_logs' => 'tr_member_funds_credit_logs',
            'debit_logs' => 'tr_member_funds_debit_logs',
            'acct_credit_logs' => 'tr_member_acct_credit_logs',
            'acct_debit_logs' => 'tr_member_acct_debit_logs',
			'credit_points_logs'=>'tr_member_acct_credit_points_logs',
			'debit_points_logs'=>'tr_member_acct_debit_points_logs',
			'payment_gateway_transactions'=>'tr_payment_gateway_transactions',
			'ad_member_logs' => 'tr_ad_member_logs',
			'member_transfers' => 'tr_member_transfers',
			'transaction_codes' => 'rf_transaction_codes',
			'cron_logs' => 'tr_cron_logs',            
        );
    }		

    //
    function insert_logs($type,$data) {	
        if(isset($data['remarks'])) {
            $data['remarks'] .= "\n\n URL:" . $this->uri->uri_string();
        } else {
            $data['remarks'] = "URL:" . $this->uri->uri_string();
        }	

		if ($type=='members')
	        $result = $this->insert('member_logs', $data);
		else if ($type=='admin')
	        $result = $this->insert('admin_logs', $data);			
		else if ($type=='inventory')
	        $result = $this->insert('inventory_logs', $data);

		//return $result;
    }
	
	function get_audit_logs($where = null, $limit = null, $orderby = null, $fields = null, $section = 'admin'){
		if ($section=='members')
	        $table = 'member_logs';
		else if ($section=='admin')
	        $table = 'admin_logs';		
		else if ($section=='inventory')
	        $table = 'inventory_logs';
		
		$query = $this->fetch($table, $fields, $where, $orderby, $limit);
        $row = $query->result();
        $query->free_result();
        return $row;
	}
	
	function get_audit_logs_count($where = null, $section = 'admin')
	{
		if ($section=='members')
	        $table = 'member_logs';
		else if ($section=='admin')
	        $table = 'admin_logs';		
		else if ($section=='inventory')
	        $table = 'inventory_logs';
	
		$query = $this->fetch($table, 'count(1) as cnt', $where);
        $row = $query->first_row();
        $query->free_result();
        return $row->cnt;
	}


	// credit member_logs
    function get_credit_logs($where = null, $limit = null, $orderby = null, $fields = null) 
	{
        $query = $this->fetch('credit_logs', $fields, $where, $orderby, $limit);
        $row = $query->result();
        $query->free_result();
        return $row;
    }
	
	function get_credit_logs_count($where = null) 
	{
        $query = $this->fetch('credit_logs', 'count(1) as cnt', $where);
        $row = $query->first_row();
        $query->free_result();
        return $row->cnt;
    }

    function insert_credit_logs($data) {
        return $this->insert('credit_logs', $data);
    }

	// debit member_logs
	function get_debit_logs($where = null, $limit = null, $orderby = null, $fields = null) 
	{
	    $query = $this->fetch('debit_logs', $fields, $where, $orderby, $limit);
	    $row = $query->result();
	    $query->free_result();
	    return $row;
	}

	function get_debit_logs_count($where = null) 
	{
	    $query = $this->fetch('debit_logs', 'count(1) as cnt', $where);
	    $row = $query->first_row();
	    $query->free_result();
	    return $row->cnt;
	}

	function insert_debit_logs($data) {
	    return $this->insert('debit_logs', $data);
	}

    // credit logs
    function get_acct_credit_logs($where = null, $limit = null, $orderby = null, $fields = null) 
	{
        $query = $this->fetch('acct_credit_logs', $fields, $where, $orderby, $limit);
        $row = $query->result();
        $query->free_result();
        return $row;
    }
	
	function get_acct_credit_logs_count($where = null) 
	{
        $query = $this->fetch('acct_credit_logs', 'count(1) as cnt', $where);
        $row = $query->first_row();
        $query->free_result();
        return $row->cnt;
    }
    
    function insert_acct_credit_logs($data) {
        return $this->insert('acct_credit_logs', $data);
    }

    
    function update_acct_credit_logs($data, $where) {
        return $this->update('acct_credit_logs', $data, $where);
    }

    // debit logs
    function get_acct_debit_logs($where = null, $limit = null, $orderby = null, $fields = null) 
	{
        $query = $this->fetch('acct_debit_logs', $fields, $where, $orderby, $limit);
        $row = $query->result();
        $query->free_result();
        return $row;
    }
	
	function get_acct_debit_logs_count($where = null) 
	{
        $query = $this->fetch('acct_debit_logs', 'count(1) as cnt', $where);
        $row = $query->first_row();
        $query->free_result();
        return $row->cnt;
    }
    
    function insert_acct_debit_logs($data) {
        return $this->insert('acct_debit_logs', $data);
    }


    // credit points logs
    function get_credit_points_logs($where = null, $limit = null, $orderby = null, $fields = null) 
	{
        $query = $this->fetch('credit_points_logs', $fields, $where, $orderby, $limit);
        $row = $query->result();
        $query->free_result();
        return $row;
    }
	
	function get_credit_points_logs_count($where = null) 
	{
        $query = $this->fetch('credit_points_logs', 'count(1) as cnt', $where);
        $row = $query->first_row();
        $query->free_result();
        return $row->cnt;
    }
    
    function insert_credit_points_logs($data) {
        return $this->insert('credit_points_logs', $data);
    }

    // debit logs
    function get_debit_points_logs($where = null, $limit = null, $orderby = null, $fields = null) 
	{
        $query = $this->fetch('debit_points_logs', $fields, $where, $orderby, $limit);
        $row = $query->result();
        $query->free_result();
        return $row;
    }
	
	function get_debit_points_logs_count($where = null) 
	{
        $query = $this->fetch('debit_points_logs', 'count(1) as cnt', $where);
        $row = $query->first_row();
        $query->free_result();
        return $row->cnt;
    }
    
    function insert_debit_points_logs($data) {
        return $this->insert('debit_points_logs', $data);
    }
	
	// payment gateway logs
    function get_payment_gateway_transactions($where = null, $limit = null, $orderby = null, $fields = null) 
	{
        $query = $this->fetch('payment_gateway_transactions', $fields, $where, $orderby, $limit);
        $row = $query->result();
        $query->free_result();
        return $row;
    }
	
	function get_payment_gateway_transactions_count($where = null) 
	{
        $query = $this->fetch('payment_gateway_transactions', 'count(1) as cnt', $where);
        $row = $query->first_row();
        $query->free_result();
        return $row->cnt;
    }
    
    function insert_payment_gateway_transactions($data) {
        return $this->insert('payment_gateway_transactions', $data);
    }

	// ad_member_logs - member concerns
    function get_ad_member_logs($where = null, $limit = null, $orderby = null, $fields = null) 
	{
        $query = $this->fetch('ad_member_logs', $fields, $where, $orderby, $limit);
        $row = $query->result();
        $query->free_result();
        return $row;
    }
	
	function get_ad_member_logs_count($where = null) 
	{
        $query = $this->fetch('ad_member_logs', 'count(1) as cnt', $where);
        $row = $query->first_row();
        $query->free_result();
        return $row->cnt;
    }
    
     function insert_ad_member_logs($data) {
        return $this->insert('ad_member_logs', $data);
    }
    
    function update_ad_member_logs($data, $where) {
        return $this->update('ad_member_logs', $data, $where);
    }
    
    function get_ad_member_logs_by_id($log_id) {
        $result = $this->get_ad_member_logs(array('log_id' => $log_id));
        $row = NULL;
        if (count($result) > 0) {
            $row = $result[0];
        }
        return $row;
    }

	//tr_member_transfers 
    function get_member_transfers($where = null, $limit = null, $orderby = null, $fields = null) 
	{
        $query = $this->fetch('member_transfers', $fields, $where, $orderby, $limit);
        $row = $query->result();
        $query->free_result();
        return $row;
    }
	
	function get_member_transfers_count($where = null) 
	{
        $query = $this->fetch('member_transfers', 'count(1) as cnt', $where);
        $row = $query->first_row();
        $query->free_result();
        return $row->cnt;
    }
    
     function insert_member_transfers($data) {
        return $this->insert('member_transfers', $data);
    }
    
    function update_member_transfers($data, $where) {
        return $this->update('member_transfers', $data, $where);
    }
    
    function get_member_transfers_by_from_member_id($from_member_id) {
        $result = $this->get_member_transfers(array('from_member_id' => $from_member_id));
        $row = NULL;
        if (count($result) > 0) {
            $row = $result[0];
        }
        return $row;
    }
	
	function get_member_transfers_by_to_member_id($to_member_id) {
        $result = $this->get_member_transfers(array('to_member_id' => $to_member_id));
        $row = NULL;
        if (count($result) > 0) {
            $row = $result[0];
        }
        return $row;
    }
	
	function get_member_transfer_by_id($member_transfer_id) {
        $result = $this->get_member_transfers(array('member_transfer_id' => $member_transfer_id));
        $row = NULL;
        if (count($result) > 0) {
            $row = $result[0];
        }
        return $row;
    }
	
	// transaction_codes
	function get_transaction_codes($where = null, $limit = null, $orderby = null, $fields = null)
	{
		$query = $this->fetch('transaction_codes', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
	}

	function insert_transaction_code($data)
	{
		return $this->insert('transaction_codes', $data);
	}

	function update_transaction_code($data, $where)
	{
		return $this->update('transaction_codes', $data, $where);
	}

	function delete_transaction_code($where)
	{
		return $this->delete('transaction_codes', $where);
	}

	function get_transaction_codes_count($where = null)
	{
		// do a sql count instead of row count
		$query = $this->fetch('transaction_codes', 'count(1) as cnt', $where);
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}

	
	function get_transaction_code_by_code($transaction_code)
	{
		$result = $this->get_transaction_codes(array('transaction_code' => $transaction_code));
		$row = NULL;
		if (count($result) > 0)
		{
			$row = $result[0];
		}
		return $row;
	}
	
	
	// cron logging
	function get_cron_logs($where = null, $limit = null, $orderby = null, $fields = null)
	{
		$query = $this->fetch('cron_logs', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
	}

	function insert_cron_logs($data)
	{
		return $this->insert('cron_logs', $data);
	}

	function update_cron_logs($data, $where)
	{
		return $this->update('cron_logs', $data, $where);
	}

	function delete_cron_logs($where)
	{
		return $this->delete('cron_logs', $where);
	}

	function get_cron_logs_count($where = null)
	{
		// do a sql count instead of row count
		$query = $this->fetch('cron_logs', 'count(1) as cnt', $where);
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}

    // card_series_generation_logs
    function get_card_series_generation_logs($where = null, $limit = null, $orderby = null, $fields = null)
    {
        $query = $this->fetch('card_series_generation_logs', $fields, $where, $orderby, $limit);
        $row = $query->result();
        $query->free_result();
        return $row;
    }

    function insert_card_series_generation_logs($data)
    {
        return $this->insert('card_series_generation_logs', $data);
    }

    function update_card_series_generation_logs($data, $where)
    {
        return $this->update('card_series_generation_logs', $data, $where);
    }

    function delete_card_series_generation_logs($where)
    {
        return $this->delete('card_series_generation_logs', $where);
    }

    function get_card_series_generation_count_by_series()
    {
        $sql = "
            SELECT
                card_series_code,
                action,
                SUM(qty) as generated_count
            FROM
                tr_card_series_generation_log
            GROUP BY
                card_series_code
        ";
        $query = $this->db->query($sql);
        $row = $query->result();
        $query->free_result();
        return $row;
    }

    // card_type_allocation_logs
    function get_card_type_allocation_logs($where = null, $limit = null, $orderby = null, $fields = null)
    {
        $query = $this->fetch('card_type_allocation_logs', $fields, $where, $orderby, $limit);
        $row = $query->result();
        $query->free_result();
        return $row;
    }

    function insert_card_type_allocation_logs($data)
    {
        return $this->insert('card_type_allocation_logs', $data);
    }

    function update_card_type_allocation_logs($data, $where)
    {
        return $this->update('card_type_allocation_logs', $data, $where);
    }

    function delete_card_type_allocation_logs($where)
    {
        return $this->delete('card_type_allocation_logs', $where);
    }

	
	// member_voucher_transfer_logs
    function get_member_voucher_transfer_logs($where = null, $limit = null, $orderby = null, $fields = null)
    {
        $query = $this->fetch('member_voucher_transfer_logs', $fields, $where, $orderby, $limit);
        $row = $query->result();
        $query->free_result();
        return $row;
    }

    function insert_member_voucher_transfer_logs($data)
    {
        return $this->insert('member_voucher_transfer_logs', $data);
    }

    function update_member_voucher_transfer_logs($data, $where)
    {
        return $this->update('member_voucher_transfer_logs', $data, $where);
    }

    function delete_member_voucher_transfer_logs($where)
    {
        return $this->delete('member_voucher_transfer_logs', $where);
    }

    function get_member_acct_transfer_earnings_logs($where = null, $limit = null, $orderby = null, $fields = null)
    {
        $query = $this->fetch('member_acct_transfer_earnings_logs', $fields, $where, $orderby, $limit);
        $row = $query->result();
        $query->free_result();
        return $row;
    }

    function insert_member_acct_transfer_earnings_logs($data)
    {
        return $this->insert('member_acct_transfer_earnings_logs', $data);
    }

    function get_member_acct_transfer_earnings_per_type_logs($where = null, $limit = null, $orderby = null, $fields = null)
    {
        $query = $this->fetch('member_acct_transfer_earnings_per_type_logs', $fields, $where, $orderby, $limit);
        $row = $query->result();
        $query->free_result();
        return $row;
    }

    function insert_member_acct_transfer_earnings_per_type_logs($data)
    {
        return $this->insert('member_acct_transfer_earnings_per_type_logs', $data);
    }

    /* auto_crediting_logs */
    function get_auto_crediting_logs($where = null, $limit = null, $orderby = null, $fields = null)
    {
        $query = $this->fetch('auto_crediting_logs', $fields, $where, $orderby, $limit);
        $row = $query->result();
        $query->free_result();
        return $row;
    }

    function insert_auto_crediting_logs($data)
    {
        return $this->insert('auto_crediting_logs', $data);
    }

    function update_auto_crediting_logs($data, $where)
    {
        return $this->update('auto_crediting_logs', $data, $where);
    }

    function delete_auto_crediting_logs($where)
    {
        return $this->delete('auto_crediting_logs', $where);
    }
}


?>
