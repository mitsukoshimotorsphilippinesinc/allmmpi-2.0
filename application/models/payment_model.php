<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Payment_model extends Base_model {
	function __construct()
	{
		// call the base model constructor
		parent::__construct();

		$this->load->model('tracking_model');

		// assign the table for this model
		$this->_TABLES = array(
				//'member_payouts' => 'cm_member_payouts',
				'payment_transactions' => 'is_payment_transactions',
				'payment_transaction_details' => 'is_payment_transaction_details',
				'payment_transaction_products' => 'is_payment_transaction_products',
				'payment_transaction_discounts' => 'is_payment_transaction_discounts',
				'paypal_log' => 'tr_paypal_log',
				'bdo_log' => 'tr_bdo_log',
				'cpoints_transaction_products' => 'tr_cpoints_transactions',
				'ipay_log' => 'tr_ipay_log'
		);
	}

	/*function get_member_payouts($where = null, $limit = null, $orderby = null, $fields = null)
	{
		$query = $this->fetch('member_payouts', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
	}

	function insert_member_payout($data)
	{
		return $this->insert('member_payouts', $data);
	}

	function update_member_payout($data, $where)
	{
		return $this->update('member_payouts', $data, $where);
	}

	function delete_member_payout($where)
	{
		return $this->delete('member_payouts', $where);
	}

	function get_member_payout_count($where = null)
	{
		// do a sql count instead of row count
		$query = $this->fetch('member_payouts', 'count(1) as cnt', $where);
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}

	function get_member_payout_by_id($transaction_id)
	{
		$result = $this->get_member_payouts(array('transaction_id' => $transaction_id));
		$row = NULL;
		if (count($result) > 0)
		{
			$row = $result[0];
		}
		return $row;
	}	
	*/
	
	//payment transactions
	function get_payment_transactions($where = null, $limit = null, $orderby = null, $fields = null)
	{
		$query = $this->fetch('payment_transactions', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
	}

	function insert_payment_transaction($data)
	{
		return $this->insert('payment_transactions', $data);
	}

	function update_payment_transaction($data, $where)
	{
		return $this->update('payment_transactions', $data, $where);
	}

	function delete_payment_transaction($where)
	{
		return $this->delete('payment_transactions', $where);
	}

	function get_payment_transaction_count($where = null)
	{
		// do a sql count instead of row count
		$query = $this->fetch('payment_transactions', 'count(1) as cnt', $where);
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}

	function get_payment_transaction_by_id($transaction_id)
	{
		$result = $this->get_payment_transactions(array('transaction_id' => $transaction_id));
		$row = NULL;
		if (count($result) > 0)
		{
			$row = $result[0];
		}
		return $row;
	}	
	
	//payment transaction details
	function get_payment_transaction_details($where = null, $limit = null, $orderby = null, $fields = null)
	{
		$query = $this->fetch('payment_transaction_details', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
	}

	function insert_payment_transaction_details($data)
	{
		return $this->insert('payment_transaction_details', $data);
	}

	function update_payment_transaction_details($data, $where)
	{
		return $this->update('payment_transaction_details', $data, $where);
	}

	function delete_payment_transaction_details($where)
	{
		return $this->delete('payment_transaction_details', $where);
	}

	function get_payment_transaction_details_count($where = null)
	{
		// do a sql count instead of row count
		$query = $this->fetch('payment_transaction_details', 'count(1) as cnt', $where);
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}

	function get_payment_transaction_details_by_transaction_id($transaction_id)
	{
		$result = $this->get_payment_transaction_details(array('transaction_id' => $transaction_id));
		$row = array();
		if (count($result) > 0)
		{
			$row = $result;
		}
		return $row;
	}	
	
	//payment transaction products
	function get_payment_transaction_products($where = null, $limit = null, $orderby = null, $fields = null)
	{
		$query = $this->fetch('payment_transaction_products', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
	}

	function insert_payment_transaction_products($data)
	{
		return $this->insert('payment_transaction_products', $data);
	}

	function update_payment_transaction_products($data, $where)
	{
		return $this->update('payment_transaction_products', $data, $where);
	}

	function delete_payment_transaction_products($where)
	{
		return $this->delete('payment_transaction_products', $where);
	}

	function get_payment_transaction_products_count($where = null)
	{
		// do a sql count instead of row count
		$query = $this->fetch('payment_transaction_products', 'count(1) as cnt', $where);
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}

	function get_payment_transaction_products_by_transaction_id($transaction_id)
	{
		$result = $this->get_payment_transaction_products(array('transaction_id' => $transaction_id), null, "package_product_id, product_id");
		$rows = array();
		if (count($result) > 0)
		{
			$rows = $result;
		}
		return $rows;
	}
	
	function get_payment_transaction_product_by_product_id($product_id)
	{
		$result = $this->get_payment_transaction_products(array('product_id' => $product_id));
		$row = NULL;
		if (count($result) > 0)
		{
			$row = $result[0];
		}
		return $row;
	}	
	
	
	//payment transaction discounts
	function get_payment_transaction_discounts($where = null, $limit = null, $orderby = null, $fields = null)
	{
		$query = $this->fetch('payment_transaction_discounts', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
	}

	function insert_payment_transaction_discounts($data)
	{
		return $this->insert('payment_transaction_discounts', $data);
	}

	function update_payment_transaction_discounts($data, $where)
	{
		return $this->update('payment_transaction_discounts', $data, $where);
	}

	function delete_payment_transaction_discounts($where)
	{
		return $this->delete('payment_transaction_discounts', $where);
	}

	function get_payment_transaction_discounts_count($where = null)
	{
		// do a sql count instead of row count
		$query = $this->fetch('payment_transaction_discounts', 'count(1) as cnt', $where);
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}
	
	function get_payment_transaction_discounts_by_transaction_id($transaction_id)
	{
		$result = $this->get_payment_transaction_discounts(array('transaction_id' => $transaction_id));
		$row = array();
		if (count($result) > 0)
		{
			$row = $result;
		}
		return $row;
	}
	
	function get_paypal_log($where = null, $limit = null, $orderby = null, $fields = null) {
        $query = $this->fetch('paypal_log', $fields, $where, $orderby, $limit);
        $row = $query->result();
        $query->free_result();
        return $row;
 	}

 	function insert_paypal_log($data) {
        return $this->insert('paypal_log', $data);
 	}



  	function get_bdo_log($where = null, $limit = null, $orderby = null, $fields = null) {
        $query = $this->fetch('bdo_log', $fields, $where, $orderby, $limit);
        $row = $query->result();
        $query->free_result();
        return $row;
 	}

 	function insert_bdo_log($data) {
        return $this->insert('bdo_log', $data);
 	}
	
	function get_cpoints_transaction_products($where = null, $limit = null, $orderby = null, $fields = null)
	{
		$query = $this->fetch('cpoints_transaction_products', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
	}

	function insert_cpoints_transaction_products($data)
	{
		return $this->insert('cpoints_transaction_products', $data);
	}

	function update_cpoints_transaction_products($data, $where)
	{
		return $this->update('cpoints_transaction_products', $data, $where);
	}

	function delete_cpoints_transaction_products($where)
	{
		return $this->delete('cpoints_transaction_products', $where);
	}

	function get_cpoints_transaction_products_count($where = null)
	{
		// do a sql count instead of row count
		$query = $this->fetch('cpoints_transaction_products', 'count(1) as cnt', $where);
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}

	function get_ipay_log($where = null, $limit = null, $orderby = null, $fields = null) {
        $query = $this->fetch('ipay_log', $fields, $where, $orderby, $limit);
        $row = $query->result();
        $query->free_result();
        return $row;
 	}

 	function insert_ipay_log($data) {
        return $this->insert('ipay_log', $data);
 	}
	
	function process_ipay($from,$post)
	{
		$data['log'] = json_encode($post);
		$data['transaction_id'] = $this->input->post('Remark');
		$data['txn_id'] = $this->input->post('TransId');
		$data['status'] = $this->input->post('Status');
		$data['from'] = $from;

		$transaction_code = $this->input->post('RefNo');
		$amount = $this->input->post('Amount');

		$merchant_account = $this->settings->ipay_merchant_code;

		$transaction = $this->get_payment_transaction_by_id($data['transaction_id']);

		if($transaction->status != "COMPLETED")
		{	
			$requery = $this->_ipay_requery($merchant_account,$transaction_code,$amount);
			if($requery == '00')
				$this->_credit_transaction_to_member($transaction);				
			else
				$this->update_payment_transaction(array("status"=>"FAILED"),array("transaction_id"=>$data['transaction_id']));
		}

		$this->insert_ipay_log($data);
	}


	private function _credit_transaction_to_member($transaction) {


	    $insert_timestamp = date("Y-m-d H:i:s");
		
	    Modules::run('jobs/transactions/credit_transaction',array('transaction_id' => $transaction->transaction_id));
		
		$completed_timestamp = date("Y-m-d H:i:s");
		
	    // insert to tr_payment_gateway_transactions
	    $data = array(
	        "transaction_id"=>$transaction->transaction_id,
	        "member_id"=>$transaction->member_id,
	        "total_amount"=>$transaction->total_amount,
	        "payment_gateway"=>$transaction->transaction_type,
	        "ip_address"=>get_ip(),
	        "completed_timestamp"=>$completed_timestamp,
	        "insert_timestamp"=>$insert_timestamp
	    );
	    $this->tracking_model->insert_payment_gateway_transactions($data);                

	} //end function

	private function _ipay_requery($MerchantCode, $RefNo, $Amount)
    {
        if ($this->settings->is_bdo_sandbox == 1) {
            $query = "https://sandbox.ipay88.com.ph/epayment/enquiry.asp?MerchantCode=";
        } else {
            $query = "https://payment.ipay88.com.ph/epayment/enquiry.asp?MerchantCode=";
        }
        
        $query.= $MerchantCode . "&RefNo=" . $RefNo . "&Amount=" . $Amount;
        $url = parse_url($query);
        $host = $url["host"];
        $path = $url["path"] . "?" . $url["query"];
        $timeout = 1;
        $fp = fsockopen ($host, 80, $errno, $errstr, $timeout);
        if ($fp) {
            $buf = '';
            fputs ($fp, "GET $path HTTP/1.0\nHost: " . $host . "\n\n");
            while (!feof($fp)) {
                $buf .= fgets($fp, 128);
            }
            $lines = explode("\n", $buf);
            $Result = $lines[count($lines)-1];
            fclose($fp);
        } else {
                     # enter error handing code here
        }
        return $Result;

    }
}    

// end of file