<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

//gets ip address of user browsing the site
if ( ! function_exists('get_ip')){
	function get_ip() { 
	    $ip; 
	    if (getenv("HTTP_CLIENT_IP")) 
	    $ip = getenv("HTTP_CLIENT_IP"); 
	    else if(getenv("HTTP_X_FORWARDED_FOR")) 
	    $ip = getenv("HTTP_X_FORWARDED_FOR"); 
	    else if(getenv("REMOTE_ADDR")) 
	    $ip = getenv("REMOTE_ADDR"); 
	    else 
	    $ip = "UNKNOWN";
	    return $ip; 
	
	}
}


function get_paypal_form_by_order_id($transaction_id, $epc_applied) {

    $ci = ci();
    
    $settings = $ci->settings_model->get_all_settings();
    
    $paypal_email = $settings["paypal_email"];

    $ip_address = get_ip();

	$hash = hash_hmac('md5',$ci->member->member_id,$ci->config->item('encryption_key'));
    $transaction = $ci->payment_model->get_payment_transaction_by_id($transaction_id);
    
    if ($settings["is_paypal_sandbox"] == 1) {
            $form_start = "<form id='paypal_form' action='https:www.sandbox.paypal.com/cgi-bin/webscr' method='post'>";
    } else {
            $form_start = "<form id='paypal_form' action='https://www.paypal.com/cgi-bin/webscr' method='post'>";
    }

    $paypal_form =
    "{$form_start}
    <input type='hidden' value='_cart' name='cmd'>
    <input type='hidden' value='1' name='upload'>
    <input type='hidden' value='{$paypal_email}' name='business'>
    <input type='hidden' value='PH' name='lc'>
    <input type='hidden' value='0' name='no_note'>
    <input id='currency_code' type='hidden' value='PHP' name='currency_code'>
    <input type='hidden' name='bn' value='PP-ShopCartBF'>
    <input id='custom' type='hidden' name='custom' value='{$ci->member->member_id}|{$ip_address}|$hash|$transaction_id'>
    <input type='hidden' name='notify_url' value='{$ci->config->item("base_url")}/payment/paypal/paypal_ipn'>
    <input type='hidden' name='return' value='{$ci->config->item("base_url")}/members/orders/?transaction_code={$transaction->transaction_code}'>";
		
	// get products
	$data = array(
			"transaction_id"=>$transaction_id,
			"package_product_id"=>0
		);
	
	$transaction_products = $ci->payment_model->get_payment_transaction_products($data,NULL,"product_id");
	$ctr = 0;
    $total_amount = 0;

	$epc_discount = 0.1;
	
	foreach($transaction_products as $tp) {
		
		$product_details = $ci->items_model->get_products("product_id = {$tp->product_id}", null, null);
		$pd = $product_details[0];

		if ($epc_applied == 1) {
			// apply epc discount for every product
			$epc_discount_amount = 	$pd->member_price * $epc_discount;				
			$product_amount_after_epc = $pd->member_price - $epc_discount_amount;
		}else
		{
			$product_amount_after_epc = $pd->member_price;
		}

        $product_name = $pd->product_name;
        if($tp->voucher_type_id > 0)
        {    
            $product_amount_after_epc = $tp->price;
            $product_name = "[".$tp->voucher_code."] ".$product_name;
        }   
	   
        $ctr++;
        $paypal_form .= "
        <input id='item_name' type='hidden' value='{$product_name}' name='item_name_{$ctr}'>
        <input id='item_number' type='hidden' value='{$pd->product_id}' name='item_number_{$ctr}'>
        <input id='amount' type='hidden' name='amount_{$ctr}' value='{$product_amount_after_epc}'>
        <input id='quantity' type='hidden' name='quantity_{$ctr}' value='{$tp->quantity}'>";
        $total_amount += $pd->member_price * $tp->quantity;
    }

    $paypal_percentage = $total_amount * $settings["paypal_transaction_cost"];

    $paypal_form .= "</form>";
	
	
    return $paypal_form;

} // end function get paypal form


function get_bdo_form_by_order_id($order_id) {
    
    $ci = ci();
    
    $settings = $ci->settings_model->get_all_settings();
    
    $merchant_account = $settings['bdo_merchant_account'];

    if (abs($settings['is_bdo_sandbox']) == 1) {
            $form_start = "<form id='bdo_form' name='payFormCcard' action='https://test.paydollar.com/ECN/eng/payment/payForm.jsp' method='post'>";
    } else {
            $form_start = "<form id='bdo_form' name='payFormCcard' action='https://www.paydollar.com/ECN/eng/payment/payForm.jsp' method='post'>";
    }
    
    $ip_address = get_ip();
    
    $order = $ci->members_model->get_member_orders("order_id = {$order_id}");
    
    if (!empty($order)) {
        $order = $order[0];
        $total_amount = $order->total_amount;
        $bdo_form = "{$form_start}
        <input type='hidden' name='merchantId' value='{$merchant_account}'>
        <input type='hidden' name='orderRef' value='{$order_id}'>
        <input type='hidden' name='currCode' value='608'>
        <input type='hidden' name='successUrl' value='{$ci->urls->base_url}orders'>
        <input type='hidden' name='failUrl' value='{$ci->urls->base_url}orders'>
        <input type='hidden' name='cancelUrl' value='{$ci->urls->base_url}orders'>
        <input type='hidden' name='payType' value='N'>
        <input type='hidden' name='lang' value='E'>
        <input type='hidden' name='amount' value='{$total_amount}'>
        <input type='hidden' name='sourceIp' value='{$ip_address}'>
        </form>";            
    } else {
        $bdo_form = "ERROR: No Order found!";
    } 
    
    return $bdo_form;
    
}

function get_ipay_form_by_order_id($transaction_id)
{
    $ci = ci();

    $settings = $ci->settings_model->get_all_settings();
    $merchant_account = $settings['ipay_merchant_code'];
    $merchant_key = $settings['ipay_merchant_key'];

    if (abs($settings['is_bdo_sandbox']) == 1) {
        $form_start = '<FORM method="post" name="ePayment" id="bdo_form" action="https://sandbox.ipay88.com.ph/epayment/entry.asp">';
    } else {
        $form_start = '<FORM method="post" name="ePayment" id="bdo_form" action="https://payment.ipay88.com.ph/epayment/entry.asp">';
    }

    $transaction = $ci->payment_model->get_payment_transaction_by_id($transaction_id);
    $transaction_detail = $ci->payment_model->get_payment_transaction_details_by_transaction_id($transaction_id);
    if(sizeof($transaction_detail)==0)
    {
          $bdo_form = "ERROR: No Order found!".$transaction_id.json_encode($transaction);
          return $bdo_form;
    }
    $member = $ci->members_model->get_member_by_id($transaction->member_id);

    $total_amount = $transaction_detail[0]->amount;
    //$total_amount = '15.00';
    $transaction_code = $transaction->transaction_code;
    $username = $transaction_detail[0]->first_name.' '.$transaction_detail[0]->last_name;
    $email = $member->email;
    $contact = $member->mobile_number;

    $before_sig = $merchant_key.$merchant_account.$transaction_code.str_replace('.','',$total_amount).'PHP';
    $signature = base64_encode(_hex2bin(sha1($before_sig)));

    $bdo_form = "{$form_start}
        <INPUT type='hidden' name='MerchantCode' value='{$merchant_account}'> 
        <INPUT type='hidden' name='PaymentId' value='1'> 
        <INPUT type='hidden' name='RefNo'  value='{$transaction_code}'>
        <INPUT type='hidden' name='Amount' value='{$total_amount}'>
        <INPUT type='hidden' name='Currency' value='PHP'> 
        <INPUT type='hidden' name='ProdDesc' value='Payment for {$transaction_code}'>
        <INPUT type='hidden' name='UserName' value='{$username}'>
        <INPUT type='hidden' name='UserEmail' value='{$email}'>
        <INPUT type='hidden' name='UserContact' value='{$contact}'>
        <INPUT type='hidden' name='Remark' value='{$transaction_id}'>
        <INPUT type='hidden' name='Lang' value='ISO-8859-1'>
        <INPUT type='hidden' name='Signature' value='{$signature}'>
        <INPUT type='hidden' name='ResponseURL' value='{$ci->config->item("base_url")}/members/orders/?transaction_code={$transaction->transaction_code}'>
        <INPUT type='hidden' name='BackendURL' value='{$ci->config->item("base_url")}/payment/bdo/backend/'>
        <INPUT type='submit' value='Proceed with Payment' name='Submit'>
        </form>";

    return $bdo_form;


}

    function _hex2bin($source) 
    {
        $bin = '';
        for ($i = 0; $i < strlen($source); $i += 2) {
            $bin .= chr(hexdec(substr($source, $i, 2)));
        }
        return $bin;
    }

    
