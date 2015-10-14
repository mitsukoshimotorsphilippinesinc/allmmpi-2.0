<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Accounts_position_count extends Admin_Controller 
{
	
	
	function __construct() 
	{
  		parent::__construct();
		$this->load->model("members_model");
		$this->load->library('pager');
	}
	
	public function index() 
	{
		$this->view();
	}

	public function view()
	{
		$where = '';

		$search_account_id = $this->input->get('account_id');
		if(!empty($search_account_id)) $where = "account_id = '$search_account_id' ";

		$total_accounts = $this->members_model->get_member_accounts_count($where);

		// set pagination data
		$config = array(
		    'pagination_url' => '/admin/accounts_position_count/view',
		    'total_items' => $total_accounts,
		    'per_page' => 10,
		    'uri_segment' => 4,
		);
		$this->pager->set_config($config);
		
		$accounts = $this->members_model->get_member_accounts($where, array('rows' => $this->pager->per_page, 'offset' => $this->pager->offset), 'insert_timestamp ASC');

		foreach($accounts as $account)
		{
			// add member full name
			$member = $this->members_model->get_member_by_id($account->member_id);
			$account->fullname = $member->first_name . " " . $member->middle_name . " " . $member->last_name;

			// add position
			$last_digit = substr($account->node_address, -1);
			$account->position = ($last_digit == '1')?'R':'L';

			// add account type
			$account_type = $this->members_model->get_member_account_type_by_id($account->account_type_id);
			$account->type_name = $account_type->account_type;
		}

		$this->template->accounts = $accounts;
		$this->template->view('account_position_count/view');
	}

}
