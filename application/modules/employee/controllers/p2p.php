<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class p2p extends Site_Controller 
{
	
	function __construct() 
	{
  		parent::__construct();
  		$this->load->model('members_model');
  		$this->load->model('items_model');
  		$this->load->model('logs_model');
  		$this->load->model('cards_model');
		$this->load->model('payment_model');
	}
	
	
	public function index() 
	{
		$member_id = $this->member->member_id;

		// get all current member p2p products
		$where = "member_id = '{$member_id}' AND quantity > 0 ";
		$products = $this->members_model->get_member_inventory($where);
		foreach($products as $product)
		{
			$prod_info = $this->items_model->get_product_by_id($product->product_id);
			$product->name = $prod_info->product_name;
		}

		// get all current member p2p cards
		$tmp_cards = $this->logs_model->get_cards_logging(array(
			'member_id' => $member_id,
			'type' => 'P2P'
		));

		$cards = array();
		foreach($tmp_cards as $tmp_card)
		{
			$account_count = $this->members_model->get_member_accounts_count(array('account_id' => $tmp_card->card_id));
			// do not include encoded cards
			if($account_count == 0)
			{
				$card = $this->cards_model->get_sp_card_by_card_id($tmp_card->card_id, null, null, array('card_id', 'status'));
				$assigned_products = $this->cards_model->get_card_product_selections(array('card_id' => $tmp_card->card_id));
				foreach($assigned_products as $assigned_product)
				{
					$prod_info = $this->items_model->get_product_by_id($assigned_product->product_id);
					$assigned_product->name = $prod_info->product_name;
				}
				if($card->status == 'USED')
				{
					$upgrade_details = $this->members_model->get_member_account_upgrades(array('upgrade_account_id'=>$tmp_card->card_id));
					$base_account = $this->members_model->get_member_account_by_account_id($upgrade_details[0]->base_account_id);
					$member_details = $this->members_model->get_members(array('member_id'=>$base_account->member_id));
					$card->status .= " BY: ".$member_details[0]->first_name." ".$member_details[0]->last_name."<br />ACCOUNT ID: ".$base_account->account_id;
				}

				$card->products = $assigned_products;
				$transaction = $this->payment_model->get_payment_transaction_by_id($tmp_card->transaction_id);
				$card->transaction_code = $transaction->transaction_code;
				$card->date_ordered = date('M d, Y H:i:s', strtotime($transaction->insert_timestamp));
				$cards[] = $card;
			}
		}
		
		//get all of member's p2p transactions
		$transactions = $this->members_model->get_member_p2p_product_cards_tally(array('member_id' => $member_id), null, "insert_timestamp DESC");

		$this->template->cards = $cards;
		$this->template->products = $products;
		$this->template->p2p_transactions = $transactions;
		$this->template->view('p2p');
    }

    public function get_card_selection_products()
    {
    	$member_id = $this->member->member_id;
    	$where = "member_id = '{$member_id}' AND quantity > 0 ";
    	$products = $this->members_model->get_member_inventory($where);
		foreach($products as $product)
		{
			$prod_info = $this->items_model->get_product_by_id($product->product_id);
			$product->name = $prod_info->product_name;
			$product->srp = $prod_info->standard_retail_price;
		}

		$this->return_json(1, 'ok', array('products' => $products));
    }

    public function get_card_selection_products_member_encode()
    {
    	$card_id = $this->input->post('card_id');
    	if(empty($card_id))
		{
			$this->return_json(0, 'Invalid Card');
			return;
		}
		// get card's member id from tr_cards_logging
		$card_log_data = $this->logs_model->get_cards_logging(array('card_id' => $card_id));
		if(sizeof($card_log_data) == 0)
		{
			$this->return_json(0, 'Card not Found');
			return;
		}

    	$member_id = $card_log_data[0]->member_id;
    	$where = "member_id = '{$member_id}' AND quantity > 0 ";
    	$products = $this->members_model->get_member_inventory($where);
		foreach($products as $product)
		{
			$prod_info = $this->items_model->get_product_by_id($product->product_id);
			$product->name = $prod_info->product_name;
			$product->srp = $prod_info->standard_retail_price;
		}

		$equipped = false;

		$selected_products = $this->cards_model->get_card_product_selections(array('card_id' => $card_id));
		if(sizeof($selected_products) > 0) $equipped = true;
		$this->return_json(1, 'ok', array('products' => $products, 'equipped' => $equipped));
    }

    public function assign_products()
    {
    	$card_id = $this->input->post('card_id');
    	$products = $this->input->post('products');

    	if(empty($card_id) || empty($products))
		{
			$this->return_json(0, 'Invalid Request');
			return;
		}

		$existing = $this->cards_model->get_sp_card(array('card_id' => $card_id));
		if(sizeof($existing) == 0)
		{
			$this->return_json(0, 'Card does not exist');
			return;	
		}

		$existing = $this->cards_model->get_card_product_selections(array('card_id' => $card_id));
		if(sizeof($existing) > 0)
		{
			$this->return_json(0, 'Card already has assigned products');
			return;	
		}

		if(count($products) == 1)
		{
			if($products[0]['qty'] == 1)
			{
				$this->return_json(0, 'Total selected quantity must be 2');
				return;
			}
		}


		foreach($products as $product)
		{
			// reduct qty on cm_member_p2p_inventory
			$member_inventory_product = $this->members_model->get_member_inventory(array(
				'member_id' => $this->member->member_id,
				'product_id' => $product['product_id']
			));
			$member_inventory_product = $member_inventory_product[0];
			$this->members_model->update_member_inventory(array(
				'quantity' => $member_inventory_product->quantity - $product['qty']
			), array(
				'member_id' => $this->member->member_id,
				'product_id' => $product['product_id']
			));

			// insert to tr_card_product_selections
			$this->cards_model->insert_card_product_selections(array(
				'card_id' => $card_id,
				'product_id' => $product['product_id'],
				'qty' => $product['qty'],
				'original_member_id' => $this->member->member_id,
			));
		}

		$this->return_json(1, 'Success');
    }

    public function assign_products_member_encode()
    {
    	$card_id = $this->input->post('card_id');
    	$products = $this->input->post('products');

    	if(empty($card_id) || empty($products))
		{
			$this->return_json(0, 'Invalid Request');
			return;
		}

		$existing = $this->cards_model->get_sp_card(array('card_id' => $card_id));
		if(sizeof($existing) == 0)
		{
			$this->return_json(0, 'Card does not exist');
			return;	
		}

		$existing = $this->cards_model->get_card_product_selections(array('card_id' => $card_id));
		if(sizeof($existing) > 0)
		{
			$this->return_json(0, 'Card already has assigned products');
			return;	
		}

		$card_log_data = $this->logs_model->get_cards_logging(array('card_id' => $card_id));
		if(sizeof($card_log_data) == 0)
		{
			$this->return_json(0, 'Card log data was not found');
			return;	
		}

		$card_log_data = $card_log_data[0];

		foreach($products as $product)
		{
			// reduce qty on cm_member_p2p_inventory
			$member_inventory_product = $this->members_model->get_member_inventory(array(
				'member_id' => $card_log_data->member_id,
				'product_id' => $product['product_id']
			));
			$member_inventory_product = $member_inventory_product[0];
			$this->members_model->update_member_inventory(array(
				'quantity' => $member_inventory_product->quantity - $product['qty']
			), array(
				'member_id' => $card_log_data->member_id,
				'product_id' => $product['product_id']
			));

			// insert to tr_card_product_selections
			$this->cards_model->insert_card_product_selections(array(
				'card_id' => $card_id,
				'product_id' => $product['product_id'],
				'qty' => $product['qty'],
				'original_member_id' => $card_log_data->member_id,
			));
		}

		$this->return_json(1, 'Success');
    }
	
}
