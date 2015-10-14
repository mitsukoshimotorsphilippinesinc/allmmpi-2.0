<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Transactions extends  Base_Controller
{
	
	function __construct() 
	{
  		parent::__construct();
		$this->load->model("raffles_model");
		$this->load->model("items_model");
		$this->load->model('payment_model');
		$this->load->model('members_model');
		$this->load->model('vouchers_model');
	}
	
	public function index() 
	{
		echo "notifications";
		return;
	}
	
	public function credit_transaction($data=array())
	{

		if(empty($data)) return;
		
		$this->load->model("payment_model");
		$this->load->model("members_model");
		
		$transaction = $this->payment_model->get_payment_transaction_by_id($data['transaction_id']);		

		if(empty($transaction)) return;
		
		if($transaction->status=="COMPLETED") return;
		
		if($transaction->transaction_type=="FUNDS" || $transaction->transaction_type=="GIFTCHEQUE" || $transaction->transaction_type=='GCEP' || $transaction->transaction_type == 'CPOINTS'){
			$this->members_model->debit_funds($transaction->member_id, $transaction->total_amount, $transaction->transaction_type);

			//deduct facility items
			$transaction_products_select = "SELECT *, sum(quantity) as total_quantity FROM is_payment_transaction_products WHERE transaction_id = {$transaction->transaction_id} GROUP BY product_id";
			$query = $this->db->query($transaction_products_select);
			$transaction_products = $query->result();
			foreach($transaction_products as $p)
			{
				//get item
				$product = $this->items_model->get_product_by_id($p->product_id);
				
				if($product->item_id != 0)
					$this->facilities_model->add_to_pending($transaction->releasing_facility_id,$product->item_id,$p->total_quantity);
			}
		}
		
		$cart = $this->members_model->get_member_cart(array('payment_transaction_id'=>$data['transaction_id']));
		if(!empty($cart)){
			$this->members_model->update_member_cart(array('status'=>"PAID"), array('cart_id'=>$cart[0]->cart_id));
		}
		
		$data_payment_transaction = array(
			'status' => "COMPLETED"
		);
		$this->payment_model->update_payment_transaction($data_payment_transaction, array('transaction_id'=>$data['transaction_id']));
		
		/*if($transaction->transaction_type=="FUNDS")
			$this->raffles_model->raffle_process('webpoi', 'pre', $data['transaction_id'], "");*/
		
		$member = $this->members_model->get_member_by_id($transaction->member_id);
		
		/*$this->credit_voucher(array(
			'transaction_id' => $data['transaction_id'],
			'cart_id' => $cart[0]->cart_id,
			'first_name' => $member->first_name,
			'middle_name' => $member->middle_name,
			'email' => $member->email,
			'mobile_number' => $member->mobile_number,
			'member_id' => $member->member_id
		));*/
		
		return;
		
	}
	
	public function credit_voucher($data=array())
	{
		
		if(empty($data)) return;
		
		$this->load->model("members_model");
		$this->load->model("vouchers_model");
		$this->load->model("payment_model");
		
		if(!isset($data['transaction_id'])) $data['transaction_id'] = 0;
		if(!isset($data['voucher_type_id'])) $data['voucher_type_id'] = 0;
		if(!isset($data['voucher_product_id'])) $data['voucher_product_id'] = 0;
		if(!isset($data['product_text'])) $data['product_text'] = '';
		if(!isset($data['cart_id'])) $data['cart_id'] = 0;
		if(!isset($data['first_name'])) $data['first_name'] = '';
		if(!isset($data['middle_name'])) $data['middle_name'] = '';
		if(!isset($data['email'])) $data['email'] = '';
		if(!isset($data['mobile_number'])) $data['mobile_number'] = '';
		if(!isset($data['user_id'])) $data['user_id'] = 0;
		if(!isset($data['status'])) $data['status'] = 'ACTIVE';
		if(!isset($data['remarks'])) $data['remarks'] = '';
		if(!isset($data['start_timestamp'])) $data['start_timestamp'] = date("Y-m-d H:i:s");
		if(!isset($data['end_timestamp'])) $data['end_timestamp'] = date('Y-m-d H:i:s',strtotime(date("Y-m-d H:i:s", mktime()) . " + 365 day"));
		if(!isset($data['insert_timestamp'])) $data['insert_timestamp'] = date("Y-m-d H:i:s");
		if(!isset($data['member_id'])) $data['member_id'] = 0;
		
		$transaction = $this->payment_model->get_payment_transaction_by_id($data['transaction_id']);		
		
		if(empty($transaction)) return;
		
		$this->members_model->insert_member_vouchers($data);
		
		$voucher_id = $this->members_model->insert_id();
		
		if($data['voucher_type_id']==0){
			$starting_id = $data['cart_id'];
			$voucher_type_details_code = $this->settings->online_voucher_prefix;
		}else{
			
			$voucher_type_details = $this->vouchers_model->get_is_voucher_type_by_id($data['voucher_type_id']);
			
			$where = "voucher_type_id = ".$data['voucher_type_id'];
			$member_voucher_details = $this->members_model->get_member_vouchers($where, null, "voucher_id DESC");

			if (count($member_voucher_details) == 0) {
				$starting_id = 1;		
			} else {
				$voucher_code_data = explode("-", $member_voucher_details[0]->voucher_code);				
				$str_voucher_code = $voucher_code_data[0];
				$str_voucher_number = abs($voucher_code_data[1]);
				$starting_id = $str_voucher_number + 1;
			}
			
			$voucher_type_details_code = $voucher_type_details->code;
		}
		
		$voucher_code = $voucher_type_details_code . '-' . str_pad($starting_id, 5, "0", STR_PAD_LEFT);
		$confirmation_code = strtoupper(substr(md5($voucher_id . $data['insert_timestamp']), 1, 8));
		$voucher_data = array(
			'voucher_code' => $voucher_code,
			'confirmation_code' => $confirmation_code
		);
		$this->members_model->update_member_vouchers($voucher_data, array('voucher_id' => $voucher_id));
		
		if($data['transaction_id'] > 0){
			$transaction_products = $this->payment_model->get_payment_transaction_products_by_transaction_id($data['transaction_id']);
			if(count($transaction_products) > 0){
				foreach($transaction_products as $tp){
					$this->members_model->insert_member_voucher_products(array(
						'voucher_id' => $voucher_id,
						'product_id' => $tp->product_id,
						'parent_product_id' => $tp->package_product_id,
						'qty' => $tp->quantity
					));
				}
			}
		}
		
		// generate voucher details
		$_html_products = "";
		$voucher_products = $this->members_model->get_member_voucher_products(array('voucher_id'=>$voucher_id,'parent_product_id'=>0));
		if(count($voucher_products) > 0){
			foreach($voucher_products as $vp){
				
				$v_product = $this->items_model->get_product_by_id($vp->product_id);
				
				$_html_products .= "<tr>
										<td>{$vp->qty}</td>
										<td>".$v_product->product_name;
				
				$sub_products = $this->members_model->get_member_voucher_products(array('voucher_id'=>$voucher_id,'parent_product_id'=>$vp->product_id));
				
				if(count($sub_products)){
					$_html_products .= "<br />";
					foreach($sub_products as $sp){
						$s_product = $this->items_model->get_product_by_id($sp->product_id);
						$sp_qty = $sp->qty / $vp->qty;
						$_html_products .= "<br />&nbsp;&nbsp;".$sp_qty." - ".$s_product->product_name;
					}
				}
				
				$_html_products .= "</td></tr>";
				
			}
		}
		
		// get member details
		$member_details = $this->members_model->get_member_by_id($data['member_id']);
		
		$params = array(
			"first_name"=>$member_details->first_name,
			"voucher_code"=>$voucher_code,
			"confirmation_code"=>$confirmation_code,
			"core_url" => $this->config->item('base_url')."/members/vouchers",
			"voucher_products_html"=>$_html_products
		);
		$data = array(
			"email"=>$member_details->email,
			"type"=>"voucher-credited",
			"params"=>$params
		);
		Modules::run('jobs/notifications/send_email',$data);
		
		return;
	}
	
	public function create_mass_transactions()
	{
		for($i = 0; $i < 5000; $i++)
		{
			//insert transaction
			$transaction_data = array(
				'transaction_type' => 'OTC',
				'rate_to_use' => 1,
				'facility_id' => 2,
				'releasing_facility_id' => 2,
				'user_id' => 1,
				'member_id' => '52003',
				'account_id' => '6500001498',
				'status' => 'RELEASED',
				'subtotal_amount' => '375.00',
				'total_amount' => '375.00',
				'tendered_amount' => '375.00',
				'released_by_user_id' => '5',
				'group_name' => 'DAVAO EAGLES',
				'ar_issued_timestamp' => date('Y-m-d H:i:s'),
				'insert_timestamp' => date('Y-m-d H:i:s'),
				'completed_timestamp' => date('Y-m-d H:i:s'),
				'remarks' => "MASS CREATION"
			);
			$this->payment_model->insert_payment_transaction($transaction_data);
			$transaction_id = $this->payment_model->insert_id();
			
			// build an AR number
			preg_match_all("/(\S)\S*/i", strtoupper("ORTIGAS DEPOT"), $arr, PREG_PATTERN_ORDER);
			$ar_number = implode('', $arr[1]).date('Y').'-'.str_pad($transaction_id, 8, "0", STR_PAD_LEFT);

			$transaction_code = strtoupper(substr(md5($transaction_id . date("Y-m-d H:i:s")),0,8)) . str_pad($transaction_id,8,0,STR_PAD_LEFT);
			
			//update transaction with transaction code and AR
			$this->payment_model->update_payment_transaction(
				array(
					'transaction_code' => $transaction_code,
					'ar_number' => $ar_number
				), 
				array('transaction_id' => $transaction_id)
			);
			
			//insert payments
			$transaction_details_data = array(
				'transaction_id' => $transaction_id,
				'payment_method' => "cash",
				'amount' => '375.00',
				'reference_number' => "",
				'reference_detail' => "",
				'first_name' => "EDWIN",
				'last_name' => "SISON",
				'middle_name' => "PAREDES",
			);
			$this->payment_model->insert_payment_transaction_details($transaction_details_data);
			
			//generate voucher
			$this->vouchers_model->generate_account_voucher(2, 52003, 6500001498, 6500001498);
			
			//get voucher
			$latest_voucher = $this->vouchers_model->get_member_account_vouchers(null, null, "voucher_id DESC");
			$latest_voucher = $latest_voucher[0];
			$voucher_id = $latest_voucher->voucher_id;
			$voucher_code = $latest_voucher->voucher_code;
			
			//update voucher
			$voucher_data = array(
				'status' => 'REDEEMED',
				'redeemed_by' => '52003',
				'redeemed_timestamp' => date('Y-m-d H:i:s'),
				'updated_timestamp' => date('Y-m-d H:i:s'),
			);	
			$this->vouchers_model->update_member_account_vouchers($voucher_data, array('voucher_id' => $voucher_id));
				
			//insert voucher products to voucher products
			$voucher_products_data = array(
				'voucher_id' => $voucher_id,
				'product_id' => '48',
				'quantity' => '1'
			);
			$this->vouchers_model->insert_member_account_voucher_products($voucher_products_data);
			
			//insert products
			$transaction_products_data = array(
				'transaction_id' => $transaction_id,
				'product_id' => '48',
				'quantity' => '1',
				'price' => '375.00',
				'voucher_type_id' => '2',
				'voucher_code' => $voucher_code,
			);
			$this->payment_model->insert_payment_transaction_products($transaction_products_data);
		}
	}
	
	public function mass_generate_vouchers()
	{
		$this->vouchers_model->generate_account_voucher(2, 52003, 6500001498, 6500001498);
	}
	
}