<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Bdo_Payment extends  Base_Controller
{
	
	
	function __construct() 
	{
  		parent::__construct();
	}

	public function index() 
	{	
		if($_POST)
		{
			print_r($_POST);
		}
		echo $this->get_ipay_form_by_order_id();
	}

	public function return_url()
	{
		print_r($_POST);
	}


	private function get_ipay_form_by_order_id($transaction_id = 0)
	{
	    $ci = ci();

	    //$settings = $ci->settings_model->get_all_settings();
	    $merchant_account = 'PH00103';
	    $bdo_sandbox = 1;

	    if ($bdo_sandbox == 1) {
	        $form_start = '<FORM method="post" name="ePayment" id="bdo_form" action="https://sandbox.ipay88.com.ph/epayment/entry.asp">';
	    } else {
	        $form_start = '<FORM method="post" name="ePayment" id="bdo_form" action="https://payment.ipay88.com.ph/epayment/entry.asp">';
	    }

	    //$transaction = $ci->payment_model->get_payment_transaction_by_id($transaction_id);
	    //$transaction_detail = $ci->payment_model->get_payment_transaction_details_by_transaction_id($transaction_id);
	    //if(sizeof($transaction_detail)==0)
	    //{
	     //     $bdo_form = "ERROR: No Order found!".$transaction_id.json_encode($transaction);
	    //      return $bdo_form;
	    //}
	    //$member = $ci->members_model->get_member_by_id($transaction->member_id);

	    //$total_amount = $transaction_detail[0]->amount;
	    $total_amount = '15.00';
	    //$transaction_code = $transaction->transaction_code;
	    $transaction_id = '0001';
	    $transaction_code = '0001';
	    //$username = $transaction_detail[0]->first_name.' '.$transaction_detail[0]->last_name;
	    $username = 'TEST USERNAME';
	    //$email = $member->email;
	    $email = 'dante.pangan@mitsukoshimotors.com';
	    //$contact = $member->mobile_number;
	    $contact = '0999999999';
	    $before_sig = 'te03ualLig'.$merchant_account.$transaction_id.str_replace('.','',$total_amount).'PHP';
	    $signature = base64_encode($this->_hex2bin(sha1($before_sig)));

	    $bdo_form = "{$form_start}
	        <INPUT type='text' name='MerchantCode' value='{$merchant_account}'> <br />
	        <INPUT type='text' name='PaymentId' value='1'> <br />
	        <INPUT type='text' name='RefNo'  value='{$transaction_id}'><br />
	        <INPUT type='text' name='Amount' value='{$total_amount}'><br />
	        <INPUT type='text' name='Currency' value='PHP'> <br />
	        <INPUT type='text' name='ProdDesc' value='Payment for {$transaction_code}'><br />
	        <INPUT type='text' name='UserName' value='{$username}'><br />
	        <INPUT type='text' name='UserEmail' value='{$email}'><br />
	        <INPUT type='text' name='UserContact' value='{$contact}'><br />
	        <INPUT type='text' name='Remark' value=''><br />
	        <INPUT type='text' name='Lang' value='ISO-8859-1'><br />
	        <INPUT type='text' name='Signature' value='{$signature}'><br />
	        <INPUT type='text' name='ResponseURL' value='{$ci->config->item("base_url")}/workbench/bdo_payment/return_url/'><br />
	        <INPUT type='text' name='BackendURL' value='{$ci->config->item("base_url")}/workbench/bdo_payment/return_url/'><br />
	        <INPUT type='submit' value='Proceed with Payment' name='Submit'>
	        </form>".$before_sig;

	    return $bdo_form;


	}


	private function _hex2bin($source) {
        $bin = '';
        for ($i = 0; $i < strlen($source); $i += 2) {
            $bin .= chr(hexdec(substr($source, $i, 2)));
        }
        return $bin;
    }

}
?>