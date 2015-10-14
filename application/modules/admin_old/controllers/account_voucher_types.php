<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Account_Voucher_Types extends Systems_Controller {


	function __construct()
	{
		parent::__construct();

		$this->load->model('vouchers_model');
		$this->set_navigation('account_voucher_types');
	}

	function index()
	{
		$this->account_voucher_types();
	}

	function account_voucher_types()
	{
		$account_voucher_types = $this->vouchers_model->get_account_voucher_types();
		$this->template->account_voucher_types = $account_voucher_types;
		$this->template->view('account_voucher_types/account_voucher_types.php');
	}

	function update_allow_inactive()
	{
		$action = $this->input->post('action');
		$account_voucher_type_id = $this->input->post('account_voucher_type_id');

		if(!$this->users_model->is_user_allowed_by_privilege_code($this->user->user_id,'SUPER ADMIN'))
		{
			$this->return_json(0, 'You have no permission to update account voucher types');
			return;
		}	

		$this->vouchers_model->update_account_voucher_types(array('allow_inactive'=>$action) , array('voucher_type_id' => $account_voucher_type_id ));
		$this->return_json('ok', 'Account voucher type successfully updated.');
		return;
	}

}