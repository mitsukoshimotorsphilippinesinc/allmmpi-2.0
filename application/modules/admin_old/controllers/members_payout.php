<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Members_payout extends  Systems_Controller
{
	
	public $start_date;
	public $end_date;
	public $type;
	
	function __construct() 
	{
  		parent::__construct();
  		$this->load->model("payout_model");
  		$this->load->model("settings_model");
  		$this->load->model("members_model");
  		$this->load->model("tracking_model");
  		$this->load->model("facilities_model");
  		$this->access_log();
  	}
	
	public function index()
	{
		$this->set_navigation('payout_header');
		$this->template->view('payout/dashboard');
	}

	public function check_payout()
	{
		$start_date = $this->input->post('start_date');
		$end_date = $this->input->post('end_date');

		$start = strtotime($start_date);
		$end = strtotime($end_date);

		if($start >= $end){
			$this->return_json(0,"Invalid Dates.");
			return;
		}

		$payout_id = 0;

		$member_payouts = $this->payout_model->get_member_payouts(array(
				'start_date' => $start_date,
				'end_date' => $end_date
			));

		if(count($member_payouts) > 0){
			$payout_id = $member_payouts[0]->payout_id;
			if($member_payouts[0]->status == "PROCESSING"){
				$this->return_json(1,"Processing Payout",array("new_payout"=>true,"member_payout"=>$member_payouts[0]));
				return;
			}else{
				$this->return_json(1,"Selected Date for payout is already completed.",array("new_payout"=>false,"member_payout"=>$member_payouts[0]));
				return;
			}

		}
		
		$this->return_json(1,"New Payout",array("new_payout"=>true,"payout_id"=>$payout_id));
		return;

	}

	public function process_commissions()
	{
		$start_date = $this->input->post('start_date');
		$end_date = $this->input->post('end_date');
		$type = $this->input->post('type');

		$start = strtotime($start_date);
		$end = strtotime($end_date);

		if($start >= $end){
			$this->return_json(0,"Invalid Dates.");
			return;
		}
		
		$member_payouts = $this->payout_model->get_member_payouts(array(
			'start_date' => $start_date, 
			'end_date' => $end_date
		));

		$psf_type = $this->input->post('psf_type');
		$psf_value = $this->input->post('psf_value');
		$psf_limit = $this->input->post('psf_limit');

		if(count($member_payouts) == 0){

			$this->payout_model->insert_member_payout(array(
				'type' => $type,
				'start_date' => $start_date,
				'end_date' => $end_date,
				'psf_type' => $psf_type,
				'psf_value' => $psf_value,
				'psf_limit' => $psf_limit
			));
			$payout_id = $this->payout_model->insert_id();

		}else{
			$this->payout_model->update_member_payouts(array(
					'psf_type' => $psf_type,
					'psf_value' => $psf_value,
					'psf_limit' => $psf_limit
				), array('payout_id' => $member_payouts[0]->payout_id));
			$payout_id = $member_payouts[0]->payout_id;
		}
		
		// additional condition
		if ($type=='IGPSM')
		{
			//$condition = " AND a.transaction_code <> 105 ";
			$condition = " AND a.transaction_code NOT IN (105, 0) ";
		}
		else
		{
			$condition = " AND a.transaction_code = 105 ";
		}
			
		// make sure that there will be no duplication of commissions
		$this->db->query("DELETE FROM po_member_commissions WHERE start_date = '{$start_date}' AND end_date = '{$end_date}'");

		$sql = "INSERT INTO po_member_commissions (member_id,account_id,account_status_id,transaction_code,amount,payout_id,start_date,end_date)
			(
			SELECT
				a.member_id,
				a.account_id,
				b.account_status_id,
				a.transaction_code,
				SUM(a.amount) as amount,
				{$payout_id} as payout_id,
				'{$start_date}' as start_date,
				'{$end_date}' as end_date
			FROM tr_member_acct_credit_logs a
			LEFT JOIN cm_member_accounts b ON a.account_id = b.account_id
			WHERE DATE(a.insert_timestamp) BETWEEN '{$start_date}' AND '{$end_date}' AND a.amount > 0 {$condition}
			GROUP BY a.member_id,a.account_id,a.transaction_code,a.account_status_id
			)";		
		$this->db->query($sql);

		// create po_member_commissions history table
		$ph_po_member_commissions = "ph_member_commissions_" . $start_date . "_" . $end_date;
		$ph_po_member_commissions = str_replace('-', '', $ph_po_member_commissions);
		$sql = "DROP TABLE IF EXISTS " . $ph_po_member_commissions;
		$this->db->query($sql);
		$sql = "
			CREATE TABLE " . $ph_po_member_commissions . "
			SELECT
				*
			FROM
				po_member_commissions
		";
		$this->db->query($sql);
		$sql = "ALTER TABLE " . $ph_po_member_commissions . " ADD PRIMARY KEY (  `commission_id` ), ADD KEY `member_id` ( `member_id` ), ADD KEY `account_id` ( `account_id` ), ADD KEY `start_date` (`start_date`,`end_date`)";
		$this->db->query($sql);

		// make sure that there will be no duplication of commissions
		$this->db->query("DELETE FROM po_member_account_commissions WHERE start_date = '{$start_date}' AND end_date = '{$end_date}'");

		if ($type=='IGPSM')
		{
			// IGPSM insert
			$sql = "INSERT INTO `po_member_account_commissions`
					(
					`payout_id`,
					`payout_type`,
					`member_id`,
					`account_id`,
					`gross`,
					`witholding_tax`,
					`net_of_tax`,
					`total`,
					`cash_card`,
					`account_status`,
					`commission_status`,
					`start_date`,
					`end_date`
					)

					(
						SELECT 
						a.payout_id,
						'IGPSM' AS payout_type,
						a.member_id,
						a.account_id,
						SUM(a.amount),
						b.value AS witholding_tax,
						(SUM(a.amount)-(b.value*SUM(a.amount))) AS net_of_tax,
						(SUM(a.amount)-(b.value*SUM(a.amount))) AS total,
						CASE
							WHEN c.is_auto_payout = 0 THEN 'TO FUNDS'
							ELSE (
								CASE
									WHEN c.metrobank_paycard_number IS NOT NULL THEN (
										CASE
											WHEN c.metrobank_paycard_number <> '' THEN REPLACE(REPLACE(c.metrobank_paycard_number, '-', ''), ' ', '')
											ELSE 'TO FUNDS - Blank Paycard' 
										END
									)										
									ELSE 'TO FUNDS - Blank Paycard'
								END
							)
						END AS cash_card,
						CASE 
							WHEN d.account_status_id = 1 THEN 'ACTIVE' 
							WHEN d.account_status_id = 2 THEN 'INACTIVE' 
							WHEN d.account_status_id = 3 THEN 'COMPANY' 
						END AS account_status,
						f.status AS commission_status,
						a.start_date,
						a.end_date
						FROM 
							po_member_commissions a
						LEFT JOIN
							rf_settings b ON b.slug = 'witholding_tax'
						LEFT JOIN
							cm_members c ON a.member_id = c.member_id
						LEFT JOIN
							cm_member_accounts d ON d.account_id = a.account_id
						LEFT JOIN
							po_member_payouts f ON a.payout_id = f.payout_id AND a.start_date = f.start_date AND a.end_date = f.end_date
						WHERE
							a.transaction_code IN (100,101,102,103,104)
						GROUP BY
							a.member_id, a.account_id, a.account_status_id
					)
			";
			$this->db->query($sql);
		}
		else
		{
			// UNILEVEL insert
			$sql = "INSERT INTO `po_member_account_commissions`
					(
					`payout_id`,
					`payout_type`,
					`member_id`,
					`account_id`,
					`gross`,
					`witholding_tax`,
					`net_of_tax`,
					`total`,
					`cash_card`,
					`account_status`,
					`commission_status`,
					`start_date`,
					`end_date`
					)

					(
						SELECT 
						a.payout_id,
						'UNILEVEL' AS payout_type,
						a.member_id,
						a.account_id,
						SUM(a.amount),
						b.value AS witholding_tax,
						(SUM(a.amount)-(b.value*SUM(a.amount))) AS net_of_tax,
						(SUM(a.amount)-(b.value*SUM(a.amount))) AS total,
						CASE
							WHEN c.is_auto_payout = 0 THEN 'TO FUNDS'
							ELSE (
								CASE
									WHEN c.metrobank_paycard_number IS NOT NULL THEN (
										CASE
											WHEN c.metrobank_paycard_number <> '' THEN REPLACE(REPLACE(c.metrobank_paycard_number, '-', ''), ' ', '')
											ELSE 'TO FUNDS - Blank Paycard' 
										END
									)										
									ELSE 'TO FUNDS - Blank Paycard'
								END
							)
						END AS cash_card,
						CASE 
							WHEN d.account_status_id = 1 THEN 'ACTIVE' 
							WHEN d.account_status_id = 2 THEN 'INACTIVE' 
							WHEN d.account_status_id = 3 THEN 'COMPANY' 
						END AS account_status,
						f.status AS commission_status,
						a.start_date,
						a.end_date
						FROM 
							po_member_commissions a
						LEFT JOIN
							rf_settings b ON b.slug = 'witholding_tax'
						LEFT JOIN
							cm_members c ON a.member_id = c.member_id
						LEFT JOIN
							cm_member_accounts d ON d.account_id = a.account_id
						LEFT JOIN
							po_member_payouts f ON a.payout_id = f.payout_id AND a.start_date = f.start_date AND a.end_date = f.end_date
						WHERE
							a.transaction_code = 105
						GROUP BY
							a.member_id, a.account_id, a.account_status_id
					)
			";
			$this->db->query($sql);
		}

		// create po_member_account_commissions history table
		$ph_po_member_account_commissions = "ph_member_account_commissions_" . $start_date . "_" . $end_date;
		$ph_po_member_account_commissions = str_replace('-', '', $ph_po_member_account_commissions);
		$sql = "DROP TABLE IF EXISTS " . $ph_po_member_account_commissions;
		$this->db->query($sql);
		$sql = "
			CREATE TABLE " . $ph_po_member_account_commissions . "
			SELECT
				a.*,
				b.account_status_id AS current_account_status_id
			FROM
				po_member_account_commissions a
			LEFT JOIN
				cm_member_accounts b ON a.account_id = b.account_id
		";
		$this->db->query($sql);
		$sql = "ALTER TABLE " . $ph_po_member_account_commissions . " ADD PRIMARY KEY (  `commission_id` ), ADD KEY `member_id` (  `member_id` ), ADD KEY `account_id` ( `account_id` ), ADD KEY `start_date` ( `start_date` ), ADD KEY `end_date` ( `end_date` ), ADD KEY `start_end_date` (`start_date`,`end_date`)";
		$this->db->query($sql);
		
		$this->return_json(1,"Done Processing Commissions",array("payout_id"=>$payout_id));
		return;
	}

	public function commission_per_account_edit_account_status()
	{
		$start_date = $this->input->post('start_date');
		$end_date = $this->input->post('end_date');
		$type = $this->input->get_post("type");
		$account_id = $this->input->get_post("account_id");
		$new_status = $this->input->get_post("new_status");

		// get payout id
		$payout = $this->payout_model->get_member_payouts(array(
			'start_date' => $start_date,
			'end_date' => $end_date,
			'type' => $type,
			'status' => 'PROCESSING'
		));

		if(count($payout))
		{
			$payout = $payout[0];

			// get account commission
			$account_commission = $this->payout_model->get_member_account_commissions(array(
				'start_date' => $start_date,
				'end_date' => $end_date,
				'payout_type' => $type,
				'payout_id' => $payout->payout_id,
				'account_id' => $account_id
			));

			if(count($account_commission))
			{
				// update account status
				$this->payout_model->update_member_account_commissions(array(
					'account_status' => $new_status
				),array(
					'start_date' => $start_date,
					'end_date' => $end_date,
					'payout_type' => $type,
					'payout_id' => $payout->payout_id,
					'account_id' => $account_id
				));

				$details_before = json_encode($account_commission);
				$details_after = $this->payout_model->get_member_account_commissions(array(
					'start_date' => $start_date,
					'end_date' => $end_date,
					'payout_type' => $type,
					'payout_id' => $payout->payout_id,
					'account_id' => $account_id
				));
				$details_after = json_encode($details_after);

				// log update
				$log_data = array(
					'user_id' => $this->user->user_id,
					'module_name' => 'PAYOUT - ACCOUNT COMMISSION',
					'table_name' => 'po_member_account_commissions',
					'action' => 'UPDATE',
					'details_before' => $details_before,
					'details_after' => $details_after,
					'remarks' => 'update account status'
				);
				$this->tracking_model->insert_logs('admin',$log_data);

				// get new account status id
				$account_status = $this->members_model->get_member_account_statuses(array('account_status'=>$new_status));
				$new_account_status_id = $account_status[0]->account_status_id;

				$details_before = $this->members_model->get_member_accounts(array('account_id' => $account_id));
				$details_before = json_encode($details_before[0]);

				// update cm_member_accounts account status
				$this->members_model->update_member_accounts(array(
					'account_status_id' => $new_account_status_id
				),array(
					'account_id' => $account_id
				));

				$details_after = $this->members_model->get_member_accounts(array('account_id' => $account_id));
				$details_after = json_encode($details_after[0]);

				// log update
				$log_data = array(
					'user_id' => $this->user->user_id,
					'module_name' => 'PAYOUT - MEMBER ACCOUNT',
					'table_name' => 'cm_member_accounts',
					'action' => 'UPDATE',
					'details_before' => $details_before,
					'details_after' => $details_after,
					'remarks' => 'update account status id'
				);
				$this->tracking_model->insert_logs('admin',$log_data);

				$data = array(
					'new_status_name' => $account_status[0]->account_status
				);
				$this->return_json(1,"Success", $data);
			}
			else
			{
				$this->return_json(0,"Failed", array("msg"=>'account commission not found'));
			}
		}
		else
		{
			$this->return_json(0,"Failed", array("msg"=>'payout not found'));
		}
		return;
	}

	public function get_commission_per_account()
	{
		$start_date = $this->input->post('start_date');
		$end_date = $this->input->post('end_date');
		$type = $this->input->get_post("type");
		$filter = $this->input->post("filter");
		if(empty($filter)) $filter = "all";

		$commissions = $this->payout_model->get_member_account_commissions(array(
			'start_date' => $start_date,
			'end_date' => $end_date,
			'payout_type' => $type
		));
		
		$html = "";
		
		$witholding_tax = $this->settings->witholding_tax;
		
		foreach($commissions as $c)
		{
			$c->cd_amount = $c->balance;
			$c->amount = $c->gross;

			$member = $this->members_model->get_member_by_id($c->member_id);
			$c->last_name = $member->last_name;
			$c->first_name = $member->first_name;
			$c->middle_name = $member->middle_name;

			$account = $this->members_model->get_member_accounts(array('account_id' => $c->account_id));
			$account = $account[0];
			$c->account_insert_timestamp = $account->insert_timestamp;

			if($filter=="new_accounts"){
				$date_registered_time = date(strtotime($c->account_insert_timestamp));
				$curr_time = date(time());

				$difference = $curr_time - $date_registered_time;
				$months = floor($difference / 86400 / 30 );

				if($months > 3) continue;
			}

			$member_name = "{$c->last_name}, {$c->first_name} {$c->middle_name}";
			
			$commission_dates = "{$c->start_date} - {$c->end_date}";

			if ($type=="IGPSM")
				$balance = $c->cd_amount;				
			else
				$balance = 0;
			
			// computed values
			/*
			if ($c->cash_card == 'TO FUNDS')
				$tax = 0;
			else {
				$tax = $c->amount * $witholding_tax;
			}
			*/				
			$tax = $c->amount * $witholding_tax;
			
			$net = $c->amount - $tax;
			$total = $net - $balance;

			$pretty_amount = number_format($c->amount,2);
			$pretty_balance = number_format($c->cd_amount,2);
			$pretty_tax = number_format($tax,2);
			$pretty_net = number_format($net,2);
			$pretty_total = number_format($total,2);

			$account_statuses = $this->members_model->get_member_account_statuses();
			$opts = "";
			foreach($account_statuses as $acct_status)
			{
				$selected = ($c->account_status == $acct_status->account_status)?"selected='selected'":'';
				$opts .= "<option value='{$acct_status->account_status}' {$selected}>{$acct_status->account_status}</option>";
			}

			$html .= "
			<tr>
				<td>{$c->last_name}</td>
				<td>{$c->first_name}</td>
				<td>{$c->middle_name}</td>
				<td>{$c->account_id}</td>
				<td style='text-align: right;'>{$pretty_amount}</td>
				<td style='text-align: right;'>{$pretty_tax}</td>
				<td style='text-align: right;'>{$pretty_net}</td>
				<td style='text-align: right;'>{$pretty_balance}</td>
				<td style='text-align: right;'>{$pretty_total}</td>
				<td>{$c->cash_card}</td>
				<td>
					<span style='cursor: pointer;' class='commission-per-account-edit-account-status-view' data-account_id='{$c->account_id}'>{$c->account_status}</span>
					<select style='cursor: pointer;' class='commission-per-account-edit-account-status span10 hide' data-old_value='{$c->account_status}' data-account_id='{$c->account_id}'>
						{$opts}
					</select>
					<button class='btn btn-danger commission-per-account-edit-account-status-cancel hide' data-account_id='{$c->account_id}'>Cancel <i class='icon-ban-circle icon-white'></i></button>
				</td>
				<td>{$c->account_insert_timestamp}</td>
				<td>{$commission_dates}</td>
			</tr>";
		}

		$this->return_json(1,"Success!!!", array("html"=>$html));
		return;
	}

	public function get_commission_per_member()
	{
		$start_date = $this->input->post('start_date');
		$end_date = $this->input->post('end_date');
		$type = $this->input->get_post("type");

		$commissions = $this->payout_model->get_member_account_commissions(array(
			'start_date' => $start_date,
			'end_date' => $end_date,
			'payout_type' => $type
		));
		
		$html = "";
		
		$witholding_tax = $this->settings->witholding_tax;

		$members_commissions = array();
		
		foreach($commissions as $c)
		{
			$c->cd_amount = $c->balance;
			$c->amount = $c->gross;

			$member = $this->members_model->get_member_by_id($c->member_id);
			$c->last_name = $member->last_name;
			$c->first_name = $member->first_name;
			$c->middle_name = $member->middle_name;

			$account = $this->members_model->get_member_accounts(array('account_id' => $c->account_id));
			$account = $account[0];
			$c->account_insert_timestamp = $account->insert_timestamp;

			$date_registered_time = date(strtotime($c->account_insert_timestamp));
			$curr_time = date(time());

			$difference = $curr_time - $date_registered_time;
			$months = floor($difference / 86400 / 30 );

			if($months > 3) continue;

			if(isset($members_commissions[$c->member_id])) {
				$new_amount = $c->amount;
				if ($c->cash_card == 'TO FUNDS')
					$new_tax = 0;
				else
					$new_tax = $c->amount * $witholding_tax;
				$new_net = $new_amount - $new_tax;
				if ($type=="IGPSM")
					$new_balance = $c->cd_amount;
				else
					$new_balance = 0;
				$new_total = $new_net - $new_balance;

				$members_commissions[$c->member_id]->amount += $new_amount;
				$members_commissions[$c->member_id]->tax += $new_tax;
				$members_commissions[$c->member_id]->net += $new_net;
				$members_commissions[$c->member_id]->balance += $new_balance;
				$members_commissions[$c->member_id]->total += $new_total;
			} else {
				$members_commissions[$c->member_id] = new stdClass;
				$members_commissions[$c->member_id]->last_name = $c->last_name;
				$members_commissions[$c->member_id]->first_name = $c->first_name;
				$members_commissions[$c->member_id]->middle_name = $c->middle_name;
				$members_commissions[$c->member_id]->amount = $c->amount;
				if ($c->cash_card == 'TO FUNDS')
					$members_commissions[$c->member_id]->tax = 0;
				else
					$members_commissions[$c->member_id]->tax = $c->amount * $witholding_tax;
				$members_commissions[$c->member_id]->net = $c->amount - $members_commissions[$c->member_id]->tax;
				if ($type=="IGPSM")
					$members_commissions[$c->member_id]->balance = $c->cd_amount;
				else
					$members_commissions[$c->member_id]->balance = 0;
				$members_commissions[$c->member_id]->total = $members_commissions[$c->member_id]->net - $members_commissions[$c->member_id]->balance;
				$members_commissions[$c->member_id]->cash_card = $c->cash_card;
				$members_commissions[$c->member_id]->commission_dates = "{$c->start_date} - {$c->end_date}";
			}
		}

		foreach($members_commissions as $c) {
			$pretty_amount = number_format($c->amount,2);
			$pretty_balance = number_format($c->balance,2);
			$pretty_tax = number_format($c->tax,2);
			$pretty_net = number_format($c->net,2);
			$pretty_total = number_format($c->total,2);

			$html .= "
			<tr>
				<td>{$c->last_name}</td>
				<td>{$c->first_name}</td>
				<td>{$c->middle_name}</td>
				<td style='text-align: right;'>{$pretty_amount}</td>
				<td style='text-align: right;'>{$pretty_tax}</td>
				<td style='text-align: right;'>{$pretty_net}</td>
				<td style='text-align: right;'>{$pretty_balance}</td>
				<td style='text-align: right;'>{$pretty_total}</td>
				<td>{$c->cash_card}</td>
				<td>{$c->commission_dates}</td>
			</tr>";
		}

		$this->return_json(1,"Success!!!", array("html"=>$html));
		return;
	}

	public function get_gc_per_account()
	{
		$start_date = $this->input->get_post("start_date");
		$end_date = $this->input->get_post("end_date");
		
		$query = $this->_get_gc_per_account_query($start_date,$end_date);
		$html = "";

		foreach ($query->result() as $r)
		{
			$member_name = $r->first_name . ' ' . $r->middle_name . ' ' . $r->last_name;
			$gc_amount = number_format($r->amount);
			$html .= "
			<tr>
				<td>{$r->last_name}</td>
				<td>{$r->first_name}</td>
				<td>{$r->middle_name}</td>
				<td>{$r->account_id}</td>
				<td>{$r->type}</td>
				<td style='text-align: right;'>{$gc_amount}</td>
				<td>{$r->account_status}</td>
			</tr>";
		}
		$this->return_json(1,"Success!!!", array("html"=>$html));
		return;
	}

	public function update_last_encashment_timestamp()
	{
		$end_date = $this->input->get_post("end_date");
		$data = array(
			'value' => $end_date . ' 23:59:59'
		);
		$this->settings_model->update_settings($data, array('slug'=>'last_encashment_timestamp'));
		$this->return_json(1,'ok',$data);
		return;
	}

	public function get_deducted_conflicts()
	{
		$start_date = $this->input->get_post("start_date");
		$end_date = $this->input->get_post("end_date");
		$payout_id = $this->input->get_post("payout_id");

		$deducted_conflicts = $this->payout_model->get_member_deduction_conflicts(array(
			'payout_id' => $payout_id,
			'start_date' => $start_date,
			'end_date' => $end_date
		));

		$html = "<div style='max-height: 400px; overflow: auto;'><table class='table table-striped table-bordered table-condensed'>";
		$html .= "<thead><tr>";
		$html .= "<th>Member ID</th>";
		$html .= "<th>Name</th>";
		$html .= "<th>Deduction Amount</th>";
		$html .= "<th>Amount Total</th>";
		$html .= "</tr></thead>";
		$html .= "<tbody>";
		foreach($deducted_conflicts as $conflicts)
		{
			$member = $this->members_model->get_member_by_id($conflicts->member_id);

			$html .= "<tr>";
			$html .= "<td>" . $conflicts->member_id . "</td>";
			$html .= "<td>" . $member->first_name . " " . $member->middle_name . " " . $member->last_name . "</td>";
			$html .= "<td>" . $conflicts->deduction_amount . "</td>";
			$html .= "<td>" . $conflicts->amount_total . "</td>";
			$html .= "</tr>";
		}
		if(count($deducted_conflicts) == 0)
		{
			$html .= "<tr><td colspan='4' style='text-align: center;'>No Deduction Conflicts Found</td></tr>";
		}
		$html .= "</tbody></table></div>";

		$this->return_json(1,'ok',$html);
	}

	public function get_deduction_conflicts()
	{
		$start_date = $this->input->get_post("start_date");
		$end_date = $this->input->get_post("end_date");
		$payout_id = $this->input->get_post("payout_id");

		$conflicts = $this->payout_model->get_member_deduction_conflicts(array(
			'payout_id' => $payout_id,
			'start_date' => $start_date,
			'end_date' => $end_date
		));

		$conflict_list = array();
		foreach($conflicts as $mem_conflict)
		{
			if(!in_array($mem_conflict->member_id, $conflict_list))
			{
				$conflict_list[] = $mem_conflict->member_id;
			}
		}

		$data = array(
			'deduction_conflict_count' => count($conflicts),
			'deduction_conflict_list' => $conflict_list
		);

		$this->return_json(1,'ok',$data);
		return;
	}

	public function apply_deductions()
	{
		$start_date = $this->input->get_post("start_date");
		$end_date = $this->input->get_post("end_date");
		$payout_id = $this->input->get_post("payout_id");
		$deductions = $this->input->get_post("deductions");
		$conflicts = $this->input->get_post("conflicts");

		// process deductions
		foreach($deductions as $deduction_id)
		{
			$cm_deduction = $this->members_model->get_member_deductions(array(
				'deduction_id' => $deduction_id
			));

			if(count($cm_deduction) > 0)
			{
				$cm_deduction = $cm_deduction[0];
				$member_id = $cm_deduction->member_id;
				$current_amount_due = $cm_deduction->amount_due;
				$old_lapsed_amount = $cm_deduction->lapsed_balance;
				$deduction_per_payout = $cm_deduction->deduction_per_payout;

				if($cm_deduction->is_to_all == 0)
				{
					$report = $this->payout_model->get_member_commissions_report(array(
						'payout_id' => $payout_id,
						'start_date' => $start_date,
						'end_date' => $end_date,
						'member_id' => $member_id
					));

					$report = $report[0];
					$balance = $report->balance;
					$balance += $deduction_per_payout;
					$old_total = $report->total_amount;
					$new_total = $old_total - ($deduction_per_payout + $old_lapsed_amount);

					$amount_lapsed = ($new_total < 0)?abs($new_total):0;

					$details_before = $cm_deduction;
					$details_before = json_encode($details_before);

					// update amount_due on cm_member_deductions
					$current_amount_due -= $deduction_per_payout;
					
					//$total_amount_lapsed = $old_lapsed_amount + $amount_lapsed;
					
					if($current_amount_due < 0) $current_amount_due = 0;
					$data = array(
						'amount_due' => $current_amount_due,
						'lapsed_balance' => $amount_lapsed
					);
					$this->members_model->update_member_deductions($data, array(
						'deduction_id' => $deduction_id
					));

					$details_after = $this->members_model->get_member_deductions(array(
						'deduction_id' => $deduction_id
					));
					$details_after = json_encode($details_after[0]);

					// log update
					$log_data = array(
						'user_id' => $this->user->user_id,
						'module_name' => 'PAYOUT - DEDUCTION',
						'table_name' => 'cm_member_deductions',
						'action' => 'UPDATE',
						'details_before' => $details_before,
						'details_after' => $details_after,
						'remarks' => 'update amount due'
					);
					$this->tracking_model->insert_logs('admin',$log_data);

					// insert entry to po_member_deductions
					$data = array(
						'deduction_id' => $deduction_id,
						'payout_id' => $payout_id,
						'member_id' => $member_id,
						'start_date' => $start_date,
						'end_date' => $end_date,
						'deducted_amount' => $deduction_per_payout,
						'amount_due' => $current_amount_due,
						'amount_lapsed' => $amount_lapsed - $old_lapsed_amount
					);
					$this->payout_model->insert_member_deductions($data);

					// log insert
					$log_data = array(
						'user_id' => $this->user->user_id,
						'module_name' => 'PAYOUT - DEDUCTION',
						'table_name' => 'po_member_deductions',
						'action' => 'ADD',
						'details_after' => json_encode($data),
						'remarks' => 'added new deduction'
					);
					$this->tracking_model->insert_logs('admin',$log_data);

					// add deduction to balance of po_member_commissions_report
					$data = array(
						'balance' => $balance,
						'total_amount' => $new_total
					);
					$this->payout_model->update_member_commissions_report($data, array(
						'payout_id' => $payout_id,
						'start_date' => $start_date,
						'end_date' => $end_date,
						'member_id' => $member_id
					));

					// log report update
					$details_before = json_encode($report);
					$details_after = $this->payout_model->get_member_commissions_report(array(
						'payout_id' => $payout_id,
						'start_date' => $start_date,
						'end_date' => $end_date,
						'member_id' => $member_id
					));
					$details_after = json_encode($details_after[0]);

					$log_data = array(
						'user_id' => $this->user->user_id,
						'module_name' => 'PAYOUT - COMMISSION REPORT',
						'table_name' => 'po_member_commissions_report',
						'action' => 'UPDATE',
						'details_before' => $details_before,
						'details_after' => $details_after,
						'remarks' => 'update balance'
					);
					$this->tracking_model->insert_logs('admin',$log_data);
				}
				else
				{
					$report = $this->payout_model->get_member_commissions_report(array(
						'payout_id' => $payout_id,
						'start_date' => $start_date,
						'end_date' => $end_date
					));

					// check for po deduction made
					foreach($report as $mem_report)
					{
						$data = array(
							'member_id' => $mem_report->member_id,
							'deduction_id' => $deduction_id,
							'payout_id' => $payout_id,
							'start_date' => $start_date,
							'end_date' => $end_date
						);
						$po_deduction = $this->payout_model->get_member_deductions($data);

						if(count($po_deduction) == 0)
						{
							$balance = $mem_report->balance;
							$balance += $deduction_per_payout;
							$old_total = $mem_report->total_amount;
							$new_total = $old_total - ($deduction_per_payout + $old_lapsed_amount);

							$amount_lapsed = ($new_total < 0)?abs($new_total):0;

							// insert entry to po_member_deductions
							$data = array(
								'deduction_id' => $deduction_id,
								'payout_id' => $payout_id,
								'member_id' => $mem_report->member_id,
								'start_date' => $start_date,
								'end_date' => $end_date,
								'deducted_amount' => $deduction_per_payout,
								'amount_due' => $current_amount_due,
								'amount_lapsed' => $amount_lapsed - $old_lapsed_amount
							);
							$this->payout_model->insert_member_deductions($data);

							// log insert
							$log_data = array(
								'user_id' => $this->user->user_id,
								'module_name' => 'PAYOUT - DEDUCTION',
								'table_name' => 'po_member_deductions',
								'action' => 'ADD',
								'details_after' => json_encode($data),
								'remarks' => 'added new deduction'
							);
							$this->tracking_model->insert_logs('admin',$log_data);

							// add deduction to balance of po_member_commissions_report
							$data = array(
								'balance' => $balance,
								'total_amount' => $new_total
							);
							$this->payout_model->update_member_commissions_report($data, array(
								'payout_id' => $payout_id,
								'start_date' => $start_date,
								'end_date' => $end_date,
								'member_id' => $mem_report->member_id
							));

							// log report update
							$details_before = json_encode($report);
							$details_after = $this->payout_model->get_member_commissions_report(array(
								'payout_id' => $payout_id,
								'start_date' => $start_date,
								'end_date' => $end_date,
								'member_id' => $mem_report->member_id
							));
							$details_after = json_encode($details_after[0]);

							$log_data = array(
								'user_id' => $this->user->user_id,
								'module_name' => 'PAYOUT - COMMISSION REPORT',
								'table_name' => 'po_member_commissions_report',
								'action' => 'UPDATE',
								'details_before' => $details_before,
								'details_after' => $details_after,
								'remarks' => 'update balance'
							);
							$this->tracking_model->insert_logs('admin',$log_data);
						}
					}
				}				
			}
		}

		// process conflicts
		if($conflicts)
		{
			foreach($conflicts as $deduction_id)
			{
				$cm_deduction = $this->members_model->get_member_deductions(array(
					'deduction_id' => $deduction_id
				));

				if(count($cm_deduction) > 0)
				{
					$cm_deduction = $cm_deduction[0];
					$member_id = $cm_deduction->member_id;

					$report = $this->payout_model->get_member_commissions_report(array(
						'payout_id' => $payout_id,
						'start_date' => $start_date,
						'end_date' => $end_date,
						'member_id' => $member_id
					));
					$report = $report[0];

					$po_deduction = $this->payout_model->get_member_deductions(array(
						'deduction_id' => $deduction_id,
						'payout_id' => $payout_id,
						'member_id' => $member_id,
						'start_date' => $start_date,
						'end_date' => $end_date
					));
					$po_deduction = $po_deduction[0];

					// insert entry to po_member_deduction_conflicts
					$data = array(
						'deduction_id' => $deduction_id,
						'payout_id' => $payout_id,
						'member_id' => $member_id,
						'start_date' => $start_date,
						'end_date' => $end_date,
						'amount_total' => ($po_deduction->amount_lapsed*-1),
						'deduction_amount' => $cm_deduction->deduction_per_payout
					);
					$this->payout_model->insert_member_deduction_conflicts($data);

					// log insert
					$log_data = array(
						'user_id' => $this->user->user_id,
						'module_name' => 'PAYOUT - DEDUCTION',
						'table_name' => 'po_member_deduction_conflicts',
						'action' => 'ADD',
						'details_after' => json_encode($data),
						'remarks' => 'added new deduction conflict'
					);
					$this->tracking_model->insert_logs('admin',$log_data);
				}
			}
		}

		$this->return_json(1,'ok');
		return;
	}

	public function get_deductions()
	{
		$start_date = $this->input->get_post("start_date");
		$end_date = $this->input->get_post("end_date");
		$payout_id = $this->input->get_post("payout_id");

		$error_msg = "";

		if(!empty($start_date) && !empty($end_date) && !empty($payout_id))
		{
			$commission_reports = $this->payout_model->get_member_commissions_report(array(
				'payout_id' => $payout_id,
				'end_date' => $end_date,
				'start_date' => $start_date
			));
			if(count($commission_reports) > 0)
			{
				$deductions = array();
				foreach($commission_reports as $report)
				{
					$member_id = $report->member_id;
					$member_deductions = $this->members_model->get_member_deductions(array('member_id'=>$member_id));

					// get member deductions
					if(count($member_deductions) > 0)
					{

						$member_info = $this->members_model->get_member_by_id($member_id);
						$member = new stdClass;
						$member->member_id = $member_id;
						$member->member_name = $member_info->first_name . ' ' . $member_info->middle_name . ' ' . $member_info->last_name;
						$member->deductions = array();
						foreach($member_deductions as $mem_deduct)
						{
							$deducted_amount = $this->payout_model->get_member_deductions(array(
								'deduction_id' => $mem_deduct->deduction_id,
								'payout_id' => $payout_id,
								'member_id' => $member_id,
								'start_date' => $start_date,
								'end_date' => $end_date
							));

							if(count($deducted_amount) == 0)
							{
								if($mem_deduct->amount_due > 0)
								{
									$member->deductions[] = array(
										'deduction_id' => $mem_deduct->deduction_id,
										'deduction_per_payout' => $mem_deduct->deduction_per_payout
									);
								}
							}
						}
						$member->amount_total = $report->total_amount;
						if(count($member->deductions) > 0)
						{
							$deductions[] = $member;
						}
					}

					// get deduction to all
					$deduct_to_all = $this->members_model->get_member_deductions(array('is_to_all' => 1));
					$member_info = $this->members_model->get_member_by_id($member_id);
					$member = new stdClass;
					$member->member_id = $member_id;
					$member->member_name = $member_info->first_name . ' ' . $member_info->middle_name . ' ' . $member_info->last_name;
					$member->deductions = array();
					foreach($deduct_to_all as $to_all)
					{
						$data = array(
							'deduction_id' => $to_all->deduction_id,
							'member_id' => $member_id,
							'start_date' => $start_date,
							'end_date' => $end_date,
							'payout_id' => $payout_id
						);
						$po_deduction = $this->payout_model->get_member_deductions($data);
						if(count($po_deduction) == 0)
						{
							// no payment yet
							$member->deductions[] = array(
								'deduction_id' => $to_all->deduction_id,
								'deduction_per_payout' => $to_all->deduction_per_payout
							);
						}
						else
						{
							// check if fully paid
							$sum = 0;
							foreach($po_deduction as $po_deduct)
							{
								$sum += $po_deduct->deducted_amount;
							}

							if($sum < $to_all->total_amount)
							{
								$member->deductions[] = array(
									'deduction_id' => $to_all->deduction_id,
									'deduction_per_payout' => $to_all->deduction_per_payout
								);
							}
						}
					}

					$member->amount_total = $report->total_amount;
					if(count($member->deductions) > 0)
					{
						$deductions[] = $member;
					}
				}

				$html = "<div style='max-height: 400px; overflow: auto;'><table class='table table-striped table-bordered table-condensed'>";
				$html .= "<thead><tr>";
				$html .= "<th>Member ID</th>";
				$html .= "<th>Name</th>";
				$html .= "<th>Total Net Commissions</th>";
				$html .= "<th>Deduction</th>";
				$html .= "</tr></thead>";
				$html .= "<tbody>";
				foreach($deductions as $deduct)
				{
					$html .= "<tr>";
					$html .= "<td>" . $deduct->member_id . "</td>";
					$html .= "<td>" . $deduct->member_name . "</td>";
					$html .= "<td style='text-align: right;'>" . number_format($deduct->amount_total,2) . "</td>";
					$html .= "<td style='text-align: right;'>";
					$current_amount_total = $deduct->amount_total;
					foreach($deduct->deductions as $tr)
					{
						$style = "";
						if($current_amount_total <= $tr['deduction_per_payout'])
						{
							$tag = "<i class='icon-warning-sign deduction-conflicts' data-deduction_id='" . $tr['deduction_id'] . "' ></i>";
							$style = "style='color: red;'";
						}
						else 
						{
							$tag = "<i class='icon-check deduction-deductions' data-deduction_id='" . $tr['deduction_id'] . "' ></i>";
							$current_amount_total -= $tr['deduction_per_payout'];
						}
						$html .= "<div " . $style . ">" . number_format($tr['deduction_per_payout'],2) . " " . $tag . "</div>";
					}
					$html .= "</td>";
					$html .= "</tr>";
				}
				if(count($deductions) == 0)
				{
					$html .= "<tr><td colspan='4' style='text-align: center;'>No Deductions Found</td></tr>";
				}
				$html .= "</tbody>";
				$html .= "</table></div>";
				$this->return_json(1,'ok', $html);
				return;
			}
			else
			{
				$error_msg = "No Reports Found";
			}
		}
		else
		{
			$error_msg = "Invalid Input";
		}

		$this->return_json(0,$error_msg);
		return;
	}

	public function apply_gcep()
	{
		$gcep_limit = $this->input->get_post("gcep_limit");
		$gcep_type = $this->input->get_post("gcep_type");
		$gcep_value = $this->input->get_post("gcep_value");
		$save_queue = $this->input->get_post("save_queue");
		$start_date = $this->input->get_post("start_date");
		$end_date = $this->input->get_post("end_date");
		$payout_id = $this->input->get_post("payout_id");

		// process save_queue
		foreach($save_queue as $save)
		{
			$account_id = $save[0];
			$member_id = $save[1];
			$member_commission_report = $this->payout_model->get_member_commissions_report(array(
				'payout_id' => $payout_id,
				'start_date' => $start_date,
				'end_date' => $end_date,
				'member_id' => $member_id
			));

			if(count($member_commission_report) > 0)
			{
				$member_commission_report = $member_commission_report[0];
				if($member_commission_report->gross >= $gcep_limit) // recompute for new gcep
					$gcep = ($gcep_type == "percent")?($member_commission_report->gross*($gcep_value/100)):$gcep_value;
				else // reset gcep to zero
					$gcep = 0;

				$net_gross = $member_commission_report->gross - $gcep;
				$tax = $member_commission_report->tax;
				$net_tax = $net_gross - $tax;
				$balance = $member_commission_report->balance;
				$payout_amount = $net_tax - $balance;

				$this->payout_model->update_member_commissions_report(array(
					'psf' => $gcep,
					'net_gross' => $net_gross,
					'tax' => $tax,
					'net_tax' => $net_tax,
					'balance' => $balance,
					'total_amount' => $payout_amount
				),array(
					'payout_id' => $payout_id,
					'start_date' => $start_date,
					'end_date' => $end_date,
					'member_id' => $member_id
				));
			}
		}
		$this->return_json(1,'ok');
		return;
	}

	public function reset_gcep()
	{
		$reset_queue = $this->input->get_post("reset_queue");
		$start_date = $this->input->get_post("start_date");
		$end_date = $this->input->get_post("end_date");
		$payout_id = $this->input->get_post("payout_id");

		// process reset_queue
		foreach($reset_queue as $save)
		{
			$account_id = $save[0];
			$member_id = $save[1];
			$member_commission_report = $this->payout_model->get_member_commissions_report(array(
				'payout_id' => $payout_id,
				'start_date' => $start_date,
				'end_date' => $end_date,
				'member_id' => $member_id
			));

			if(count($member_commission_report) > 0)
			{
				$member_commission_report = $member_commission_report[0];
				$gcep = 0;

				$net_gross = $member_commission_report->gross - $gcep;
				$tax = $member_commission_report->tax;
				$net_tax = $net_gross - $tax;
				$balance = $member_commission_report->balance;
				$payout_amount = $net_tax - $balance;

				$this->payout_model->update_member_commissions_report(array(
					'psf' => $gcep,
					'net_gross' => $net_gross,
					'tax' => $tax,
					'net_tax' => $net_tax,
					'balance' => $balance,
					'total_amount' => $payout_amount
				),array(
					'payout_id' => $payout_id,
					'start_date' => $start_date,
					'end_date' => $end_date,
					'member_id' => $member_id
				));
			}
		}
		$this->return_json(1,'ok');
		return;
	}

	public function set_member_commissions_report()
	{
		$psf = $this->input->get_post("psf");
		$psf = str_replace(',', '', $psf);
		$tax = $this->input->get_post("tax");
		$tax = str_replace(',', '', $tax);
		$balance = $this->input->get_post("balance");
		$balance = str_replace(',', '', $balance);
		$cash_card = $this->input->get_post("cash_card");
		$start_date = $this->input->get_post("start_date");
		$end_date = $this->input->get_post("end_date");
		$payout_id = $this->input->get_post("payout_id");
		$member_id = $this->input->get_post("member_id");

		$member_commission_report = $this->payout_model->get_member_commissions_report(array(
			'payout_id' => $payout_id,
			'start_date' => $start_date,
			'end_date' => $end_date,
			'member_id' => $member_id
		));

		if(count($member_commission_report) > 0){
			$member_commission_report = $member_commission_report[0];
			$net_gross = $member_commission_report->gross - $psf;
			$net_tax = $net_gross - $tax;
			$payout_amount = $net_tax-$balance;

			$this->payout_model->update_member_commissions_report(array(
				'psf' => $psf,
				'net_gross' => $net_gross,
				'tax' => $tax,
				'net_tax' => $net_tax,
				'balance' => $balance,
				'total_amount' => $payout_amount,
				'cash_card' => $cash_card
			),array(
				'payout_id' => $payout_id,
				'start_date' => $start_date,
				'end_date' => $end_date,
				'member_id' => $member_id
			));

			$psf = number_format($psf, 2);
			$tax = number_format($tax, 2);
			$balance = number_format($balance, 2);
			$net_gross = number_format($net_gross,2);
			$net_tax = number_format($net_tax,2);
			$payout_amount = number_format($payout_amount,2);

			$data = array(
				'psf' => $psf,
				'tax' => $tax,
				'balance' => $balance,
				'cash_card' => $cash_card,
				'net_gross' => $net_gross,
				'net_tax' => $net_tax,
				'payout_amount' => $payout_amount
			);
			$this->return_json(1,'ok',$data);
			return;
		} else {
			$this->return_json(0,"err","No Member Commission Report Found");
			return;
		}
	}

	public function get_member_payout()
	{
		$type = $this->input->get_post("type");
		$start_date = $this->input->get_post("start_date");
		$end_date = $this->input->get_post("end_date");
		$payout_id = $this->input->get_post("payout_id");

		$query = $this->_get_member_payout_query($type,$start_date,$end_date);

		$psf_limit = $this->input->post("psf_limit");
		$psf_type = $this->input->post("psf_type");
		$psf_value = $this->input->post("psf_value");

		$witholding_tax = $this->settings->witholding_tax;
		
		$html = "";
		foreach ($query->result() as $r)
		{
			$rfid_paycard_number = '';
			$rfid_account_number = '';

			// metrobank paycard account number
			if($r->cash_card != "TO FUNDS" && $r->cash_card != "TO FUNDS - Blank Paycard"){
				$metrobank_paycard = $this->members_model->get_member_rfid_cards(array(
					'paycard_number' => $r->cash_card
				));
				if(count($metrobank_paycard) > 0){
					//$r->cash_card = $metrobank_paycard[0]->account_number;
					$rfid_paycard_number = $metrobank_paycard[0]->paycard_number;
					$rfid_account_number = $metrobank_paycard[0]->account_number;
				}
			}
			
			$psf = 0;
			if($r->amount >= $psf_limit){
				$psf = $r->amount * ($psf_value / 100);
				if($psf_type!="percent"){
					$psf = $psf_value;
				}
			}

			$net_gross = $r->amount - $psf;
			$tax = ($r->cash_card == "TO FUNDS" || $r->cash_card == "TO FUNDS - Blank Paycard")?0:($net_gross * $witholding_tax);

			$net_tax = $net_gross - $tax;
			$payout_amount = $net_tax;

			$member_commission_report = $this->payout_model->get_member_commissions_report(array(
				'payout_id' => $payout_id,
				'start_date' => $start_date,
				'end_date' => $end_date,
				'member_id' => $r->member_id
			));
			if(count($member_commission_report) == 0){
				$balance = 0.00;

				// insert to po_member_commissions_report
				$data = array(
					'payout_id' => $payout_id,
					'start_date' => $start_date,
					'end_date' => $end_date,
					'member_id' => $r->member_id,
					'gross' => $r->amount,
					'psf' => $psf,
					'net_gross' => $net_gross,
					'tax' => $tax,
					'net_tax' => $net_tax,
					'balance' => $balance,
					'total_amount' => $payout_amount,
					'cash_card' => $r->cash_card
				);
				$this->payout_model->insert_member_commissions_report($data);

				// po_member_commissions_report log
				$log_data = array(
					'user_id' => $this->user->user_id,
					'module_name' => 'PAYOUT - MEMBER COMMISSION REPORT',
					'table_name' => 'po_member_commissions_report',
					'action' => 'ADD',
					'details_after' => json_encode($data)
				);
				$this->tracking_model->insert_logs('admin',$log_data);

			} else {

				// recompute data
				$psf = $member_commission_report[0]->psf;
				$gross = $r->amount;
				$net_gross = $gross - $psf;
				$tax = ($member_commission_report[0]->cash_card == "TO FUNDS" || $member_commission_report[0]->cash_card == "TO FUNDS - Blank Paycard")?0:$member_commission_report[0]->tax;
				$net_tax = $net_gross - $tax;
				$balance = $member_commission_report[0]->balance;
				$payout_amount = $net_tax - $balance;
				$cash_card = $member_commission_report[0]->cash_card;

				// update po_member_commissions_report
				$data_before = $this->payout_model->get_member_commissions_report(array(
					'payout_id' => $payout_id,
					'start_date' => $start_date,
					'end_date' => $end_date,
					'member_id' => $r->member_id
				));
				$this->payout_model->update_member_commissions_report(array(
					'gross' => $gross,
					'psf' => $psf,
					'net_gross' => $net_gross,
					'tax' => $tax,
					'net_tax' => $net_tax,
					'balance' => $balance,
					'total_amount' => $payout_amount,
					'cash_card' => $cash_card
				),array(
					'payout_id' => $payout_id,
					'start_date' => $start_date,
					'end_date' => $end_date,
					'member_id' => $r->member_id
				));
				$data_after = $this->payout_model->get_member_commissions_report(array(
					'payout_id' => $payout_id,
					'start_date' => $start_date,
					'end_date' => $end_date,
					'member_id' => $r->member_id
				));

				// po_member_commissions_report log
				$log_data = array(
					'user_id' => $this->user->user_id,
					'module_name' => 'PAYOUT - MEMBER COMMISSION REPORT',
					'table_name' => 'po_member_commissions_report',
					'action' => 'UPDATE',
					'details_before' => json_encode($data_before),
					'details_after' => json_encode($data_after)
				);
				$this->tracking_model->insert_logs('admin',$log_data);

				$r->cash_card = $cash_card;
			}
			
			$member_name = $r->first_name . ' ' . $r->middle_name . ' ' . $r->last_name;
			$gross_amount = number_format($r->amount, 2);
			$psf = number_format($psf, 2);
			$net_gross = number_format($net_gross, 2);
			$tax = number_format($tax, 2);
			$net_tax = number_format($net_tax, 2);
			$balance = number_format($balance, 2);
			$payout_amount = number_format($payout_amount, 2);
			$cash_card_opts = ($r->cash_card == "TO FUNDS" || $r->cash_card == "TO FUNDS - Blank Paycard")?"<option value='TO FUNDS' selected='selected'>TO FUNDS</option>":"<option value='TO FUNDS'>TO FUNDS</option>";
			if(!is_null($r->metrobank_paycard_number)) {
				$cash_card_opts .= ($r->cash_card == str_replace(' ', '', str_replace('-', '', $r->metrobank_paycard_number)))?"<option value='{$r->metrobank_paycard_number}' selected='selected'>{$r->metrobank_paycard_number}</option>":"<option value='{$r->metrobank_paycard_number}'>{$r->metrobank_paycard_number}</option>";
			}
			$psf_class = ($type == "UNILEVEL")?"class='muted'":"";
			$psf_muted = ($type == "UNILEVEL")?"muted":"";
			$psf_disable = ($type == "UNILEVEL")?"disabled='disabled'":"";
			$html .= "
			<tr>
				<td>{$r->last_name}</td>
				<td>{$r->first_name}</td>
				<td>{$r->middle_name}</td>
				<td style='text-align: right;' class='payout_gross_value' data-member_id='{$r->member_id}' data-gross_amount='{$r->amount}'>{$gross_amount}</td>
				<td style='max-width: 80px;' {$psf_class} >
					<div style='text-align: right;' class='{$psf_muted} payout-control-view payout_psf_value' data-member_id='{$r->member_id}'>{$psf}</div>
					<input {$psf_disable} class='payout-control-input payout_psf_input span10 hide' data-member_id='{$r->member_id}' data-field_type='psf' value='{$psf}'/>
				</td>
				<td>
					<div style='text-align: right;' class='payout_net_gross_value' data-member_id='{$r->member_id}'>{$net_gross}</div>
				</td>
				<td style='max-width: 80px;'>
					<div style='text-align: right;' class='payout-control-view payout_tax_value' data-member_id='{$r->member_id}'>{$tax}</div>
					<input class='payout-control-input payout_tax_input span10 hide' data-old_value='{$tax}' data-member_id='{$r->member_id}' data-field_type='tax' value='{$tax}'/>
				</td>
				<td>
					<div style='text-align: right;' class='payout_net_tax_value' data-member_id='{$r->member_id}'>{$net_tax}</div>
				</td>
				<td style='max-width: 80px;' >
					<div style='text-align: right;' class='payout-control-view payout_balance_value' data-member_id='{$r->member_id}'>{$balance}</div>
					<input class='payout-control-input payout_balance_input span10 hide' data-member_id='{$r->member_id}' data-field_type='balance' value='{$balance}'/>
				</td>
				<td id='payout_total_{$r->member_id}' >
					<div style='text-align: right;' class='payout_payout_amount_value' data-member_id='{$r->member_id}'>{$payout_amount}</div>
				</td>
				<td>
					<div class='payout-control-view payout_cash_card_value' data-member_id='{$r->member_id}'>{$r->cash_card}</div>
					<select class='payout-control-input payout_cash_card_input span10 hide' data-old_value='{$r->cash_card}' data-member_id='{$r->member_id}' data-field_type='cash_card'>
					{$cash_card_opts}
					</select>
				</td>
				<td>{$rfid_account_number}</td>
				<td>{$r->depot_name}</td>
				<td>{$r->group_name}</td>
				<td style='max-width: 130px;'>
					<div class='hide btn-group btn-group-vertical btn-save-cancel' data-member_id='{$r->member_id}'>
						<button confirm='1' class='btn btn-success member-payout-save-row' data-member_id='{$r->member_id}' title='Save'><i class='icon-ok icon-white'></i></button>
						<button class='btn btn-danger member-payout-cancel-row' data-member_id='{$r->member_id}' title='Cancel'><i class='icon-remove icon-white'></i></button>
					</div>
					<div class='btn-group btn-group-vertical btn-edit-reset' data-member_id='{$r->member_id}'>
						<button class='btn btn-primary member-payout-edit-row' data-member_id='{$r->member_id}' title='Edit'><i class='icon-pencil icon-white'></i></button>
						<button confirm='1' class='btn btn-primary member-payout-reset-row' data-member_id='{$r->member_id}' title='Reset'><i class='icon-repeat icon-white'></i></button>
					</div>
				</td>
			</tr>";

		}

		$this->return_json(1,"Success!!!", array("html"=>$html));
		return;

	}

	public function get_edited_member_payout()
	{
		$start_date = $this->input->get_post("start_date");
		$end_date = $this->input->get_post("end_date");
		$payout_id = $this->input->get_post("payout_id");

		$members_payout = $this->payout_model->get_member_commissions_report(array(
			'payout_id' => $payout_id,
			'start_date' => $start_date,
			'end_date' => $end_date,
		));
		
		$html = "";
		foreach ($members_payout as $r)
		{
			$rfid_paycard_number = '';
			$rfid_account_number = '';
			if($r->cash_card != "TO FUNDS"){
				$metrobank_paycard = $this->members_model->get_member_rfid_cards(array(
						'paycard_number' => $r->cash_card
					));
				if(count($metrobank_paycard) > 0){
					$r->cash_card = $metrobank_paycard[0]->account_number;
					$rfid_paycard_number = $metrobank_paycard[0]->paycard_number;
					$rfid_account_number = $metrobank_paycard[0]->account_number;
				}
			}
			
			$gross_amount = number_format($r->gross, 2);
			$psf = number_format($r->psf, 2);
			$net_gross = number_format($r->net_gross, 2);
			$tax = number_format($r->tax, 2);
			$net_tax = number_format($r->net_tax, 2);
			$balance = number_format($r->balance, 2);
			$payout_amount = number_format($r->total_amount, 2);

			$member = $this->members_model->get_member_by_id($r->member_id);
			$service_depot = $this->facilities_model->get_service_depots(array(
				'service_depot_id' => $member->service_depot
			));
			$service_depot = $service_depot[0];
			$html .= "
			<tr>
				<td>{$member->last_name}</td>
				<td>{$member->first_name}</td>
				<td>{$member->middle_name}</td>
				<td align='right'>{$gross_amount}</td>
				<td align='right'>{$psf}</td>
				<td align='right'>{$net_gross}</td>
				<td align='right'>{$tax}</td>
				<td align='right'>{$net_tax}</td>
				<td align='right' width='80px' >
					<span id='payout_balance_{$r->member_id}_value' class='payout_balance_value' >{$balance}</span>
					<input id='payout_balance_{$r->member_id}' name='payout_balance_{$r->member_id}' value='{$balance}' style='width:80px;display:none;' class='payout_balance_input' data-member_id='{$r->member_id}' />
				</td>
				<td align='right' id='payout_total_{$r->member_id}' >{$payout_amount}</td>
				<td>{$r->cash_card}</td>
				<td>{$rfid_paycard_number}</td>
				<td>{$service_depot->depot_name}</td>
				<td>{$member->group_name}</td>
				<td></td>
			</tr>";

		}

		$this->return_json(1,"Success!!!", array("html"=>$html));
		return;

	}

	public function get_transaction_logs()
	{
		$type = $this->input->get_post("type");
		$start_date = $this->input->get_post("start_date");
		$end_date = $this->input->get_post("end_date");
		$member_id = abs($this->input->get_post("member_id"));

		if($member_id == 0){ // get first member_id from tmp_cm_member_commissions_igpsm_view
			//$sql = "SELECT DISTINCT(member_id) as member_id FROM po_member_commissions_igpsm_view WHERE start_date = '{$start_date}' AND end_date = '{$end_date}'";
			$sql = "SELECT DISTINCT(member_id) as member_id FROM po_member_account_commissions WHERE start_date = '{$start_date}' AND end_date = '{$end_date}' AND payout_type = '{$type}'";
			$query = $this->db->query($sql);
			$result = $query->first_row();
			
			if(!isset($result->member_id))
			{
				$html_member_option = "";
				$html = "
				<tr>
					<td colspan='6'>No Transaction Logs Found</td>
				</tr>";
				$this->return_json(1,"Success!!!", array("html"=>$html,"html_member_option"=>$html_member_option));
				return;
			}
			$member_id = $result->member_id;

			$query->free_result();
		}

		// get all members with commisions
		//$sql = "SELECT DISTINCT(member_id) as member_id, first_name, last_name, middle_name FROM po_member_commissions_igpsm_view WHERE start_date = '{$start_date}' AND end_date = '{$end_date}'";
		$sql = "SELECT DISTINCT(member_id) as member_id FROM po_member_account_commissions WHERE start_date = '{$start_date}' AND end_date = '{$end_date}' AND payout_type = '{$type}' ";
		$query = $this->db->query($sql);
		$result = $query->result();
		$ctr = 1;
		$html_member_option = "";
		foreach($result as $r){
			$member = $this->members_model->get_member_by_id($r->member_id);
			if($ctr==1 && $member_id==0) $member_id = $r->member_id;
			$selected = "";
			if($member_id == $r->member_id) $selected = "selected";
			$html_member_option .= "<option value='{$r->member_id}' {$selected}>{$member->last_name}, {$member->first_name} {$member->middle_name}</option>";
			$ctr++;
		}

		$count = $this->_get_transaction_logs_query_count($type,$member_id,$start_date,$end_date);
		$count = $count[0]->cnt;
		$count_limit = 0;
		$query_limit = 5000;

		while($count_limit < $count)
		{
			$count_limit += $query_limit;
		}
		$count_limit += $query_limit;
		$loops = $count_limit/$query_limit;
		
		$html = "";

		for($i = 0; $i <= $loops; $i++)
		{
			$offset = $i * $query_limit;
			$query = $this->_get_transaction_logs_query($type,$member_id,$start_date,$end_date,$query_limit,$offset);
			foreach ($query->result() as $r)
			{
				if($r->amount > 0) {
					$payout_amount = number_format($r->amount, 2);
					$html .= "
					<tr>
						<td>{$r->account_id}</td>
						<td>{$r->remarks}</td>
						<td>{$r->type}</td>
						<td>{$r->level}</td>
						<td style='text-align: right;'>{$payout_amount}</td>
						<td>{$r->insert_timestamp}</td>
					</tr>";
				}
				
			}
		}

		
		

		$this->return_json(1,"Success!!!", array("html"=>$html,"html_member_option"=>$html_member_option));
		return;

	}

	public function verify_admin_login()
	{
		$username = $this->input->get_post("username");
		$password = $this->input->get_post("password");

		if(!empty($username) && !empty($password))
		{
			if ($this->authenticate->login($username, $password))
			{
				$this->return_json(1,"SUCCESS");
			}
			else
			{
				$this->return_json(0,"Invalid Login",array($username, $password));
			}
		}
		else
		{
			$this->return_json(0,"Invalid Login");
		}
		return;
	}

	public function get_admin_login()
	{
		$html = '
		<fieldset>
			<div id="username_control" class="control-group" style="width: 220px; margin: 0 auto;">
				<label class="control-label" for="username">Username</label>
				<div class="controls">
					<input type="text" placeholder="" name="username" class="payout-confirm-username" value=""> 
				</div>
			</div>
			<div id="password_control" class="control-group" style="width: 220px; margin: 0 auto;">
				<label class="control-label" for="password">Password</label>
				<div class="controls">
					<input type="password" placeholder="" name="password" class="payout-confirm-password" value=""> 
				</div>
			</div>
		</fieldset>
		';
		$this->return_json(1,"SUCCESS",array('html'=>$html));
		return;
	}

	public function save_balance()
	{
		$member_commissions_report = $this->payout_model->get_member_commissions_report();

		foreach($member_commissions_report as $c){

			$balance = abs($this->input->post("payout_balance_".$c->member_id));

			if($balance != $c->balance){

				$total_amount = $c->net_tax - $balance;

				$this->payout_model->update_member_commissions_report(array(
						'balance' => $balance,
						'total_amount' => $total_amount
					), array("member_id"=>$c->member_id));
			}

		}

		$this->return_json(1,"Payout Balance Updated.");
		return;

	}

	public function credit_payout()
	{
		//$this->return_json(0,"Payout Credited."); // disable temporarily
		//return;

		$type = $this->input->get_post("type");
		$start_date = $this->input->get_post("start_date");
		$end_date = $this->input->get_post("end_date");

		$payout = $this->payout_model->get_member_payouts(array(
			'start_date' => $start_date,
			'end_date' => $end_date
		));

		if(count($payout) > 0 && $payout[0]->status == "PROCESSING")
		{
			$details_before = $this->payout_model->get_member_payouts(array(
				'start_date' => $start_date,
				'end_date' => $end_date
			));
			$details_before = json_encode($details_before[0]);

			/* LOCK Payout */
			$this->payout_model->update_member_payouts(array(
				'status' => "COMPLETED"
			), array(
				'start_date' => $start_date,
				'end_date' => $end_date
			));

			$details_after = $this->payout_model->get_member_payouts(array(
				'start_date' => $start_date,
				'end_date' => $end_date
			));
			$details_after = json_encode($details_before[0]);

			// log update
			$log_data = array(
				'user_id' => $this->user->user_id,
				'module_name' => 'PAYOUT - COMMISSION UPDATE',
				'table_name' => 'po_member_payouts',
				'action' => 'UPDATE',
				'details_before' => $details_before,
				'details_after' => $details_after,
				'remarks' => 'update commission status'
			);
			$this->tracking_model->insert_logs('admin',$log_data);

			$commissions = $this->payout_model->get_member_account_commissions(array(
				'start_date' => $start_date,
				'end_date' => $end_date,
				'payout_type' => $type
			));
			foreach($commissions as $c){
				$data = array(
					'member_id' => $c->member_id,
					'account_id' => $c->account_id,
					'type' => $c->cash_card,
					'amount' => $c->total,
					'status' => "PROCESSED",
					'start_date' => $c->start_date,
					'end_date' => $c->end_date
				);
				$this->members_model->insert_member_encashment($data);

				// log insert
				$log_data = array(
					'user_id' => $this->user->user_id,
					'module_name' => 'PAYOUT - MEMBER ENCASHMENT',
					'table_name' => 'cm_member_encashment',
					'action' => 'ADD',
					'details_before' => '',
					'details_after' => json_encode($data),
					'remarks' => 'added member encashment'
				);
				$this->tracking_model->insert_logs('admin',$log_data);
			}

			/* CREDIT FUNDS */
			$member_commissions_report = $this->payout_model->get_member_commissions_report(array(
				'cash_card' => "TO FUNDS",
				'start_date' => $start_date,
				'end_date' => $end_date
			));
			foreach($member_commissions_report as $c){
				if($c->total_amount > 0){
					$this->members_model->credit_funds($c->member_id,$c->total_amount,"FUNDS","Payout ({$start_date} to {$end_date})");
				}
			}
			$this->return_json(1,"Payout Credited.");
		}
		else
		{
			$this->return_json(0,"Error: Payout does not exist or is already credited.");
		}		
		return;
	}

	public function download_deductions()
	{
		$start_date = $this->input->get_post("start_date");
		$end_date = $this->input->get_post("end_date");
		$payout_id = $this->input->get_post("payout_id");

		$this->load->library('PHPExcel');
        $this->load->library('PHPExcel/IOFactory');

		$pretty_start_date = str_replace("-","",$start_date);
		$pretty_end_date = str_replace("-","",$end_date);

		$filename = "member_deductions_{$pretty_start_date}_to_{$pretty_end_date}.xlsx";

		try {
			$title = "Member Deductions for {$this->start_date} to {$this->end_date}";

	        $objPHPExcel = new PHPExcel();
	        $objPHPExcel->getProperties()->setTitle($title)->setDescription("none");

	        $title = "Member Deductions";
			$worksheet = $objPHPExcel->setActiveSheetIndex(0);
			$objPHPExcel->getActiveSheet()->setTitle($title);			
			$this->_get_member_deduction_worksheet($worksheet,$start_date,$end_date,$payout_id);

			$objPHPExcel->setActiveSheetIndex(0);

			header('Content-Type: application/vnd.ms-excel');
			header('Content-Disposition: attachment;filename='.$filename.'');
			header('Cache-Control: max-age=0');

			$objWriter = IOFactory::createWriter($objPHPExcel, 'Excel2007');
			$objWriter->save('php://output');
			exit(0);
		} catch (Exception $e) {
			exit($e->getMessage());
		}
	}

	public function download()
	{
		$start_date = $this->input->get_post("start_date");
		$end_date = $this->input->get_post("end_date");
		$type = $this->input->get_post("type");
		$payout_id = $this->input->get_post("payout_id");

		$this->load->library('PHPExcel');
        $this->load->library('PHPExcel/IOFactory');

		$pretty_start_date = str_replace("-","",$start_date);
		$pretty_end_date = str_replace("-","",$end_date);

		$filename = "{$type}_commission_payout_{$pretty_start_date}_to_{$pretty_end_date}.xlsx";

		try {

			$title = "{$this->type} Commission Payout for {$this->start_date} to {$this->end_date}";

	        $objPHPExcel = new PHPExcel();
	        $objPHPExcel->getProperties()->setTitle($title)->setDescription("none");

			$title = "Member Payout";
			$worksheet = $objPHPExcel->setActiveSheetIndex(0);
			$objPHPExcel->getActiveSheet()->setTitle($title);			
			$this->_get_member_payout_worksheet($worksheet,$start_date,$end_date,$payout_id);

			$objPHPExcel->createSheet();

			$title = "{$type} Per Account";
			$worksheet = $objPHPExcel->setActiveSheetIndex(1);
			$objPHPExcel->getActiveSheet()->setTitle($title);			
			$this->_get_commissions_per_account_worksheet($worksheet,$start_date,$end_date,$type);

			$objPHPExcel->createSheet();

			$title = "3 Month Old Per Account";
			$worksheet = $objPHPExcel->setActiveSheetIndex(2);
			$objPHPExcel->getActiveSheet()->setTitle($title);			
			$this->_get_commissions_per_account_worksheet($worksheet,$start_date,$end_date,$type,"new_accounts");

			$objPHPExcel->createSheet();
			
			$title = "3 Month Old Per Member";
			$worksheet = $objPHPExcel->setActiveSheetIndex(3);
			$objPHPExcel->getActiveSheet()->setTitle($title);			
			$this->_get_commissions_per_member_worksheet($worksheet,$start_date,$end_date,$type);			

			$objPHPExcel->createSheet();
			
			$title = "Gift Cheques Per Account";
			$worksheet = $objPHPExcel->setActiveSheetIndex(4);
			$objPHPExcel->getActiveSheet()->setTitle($title);			
			$this->_get_gc_per_account_worksheet($worksheet,$start_date,$end_date);

			$objPHPExcel->createSheet();

			$title = "Transferred Funds";
			$worksheet = $objPHPExcel->setActiveSheetIndex(5);
			$objPHPExcel->getActiveSheet()->setTitle($title);			
			$this->_get_transferred_per_member($worksheet,$start_date,$end_date,'FUNDS');

			$objPHPExcel->createSheet();

			$title = "Transferred Gift Checques";
			$worksheet = $objPHPExcel->setActiveSheetIndex(6);
			$objPHPExcel->getActiveSheet()->setTitle($title);			
			$this->_get_transferred_per_member($worksheet,$start_date,$end_date,'GIFT CHECQUES');

			/*
			$objPHPExcel->createSheet();
			
			$title = "Transaction Logs";
			$worksheet = $objPHPExcel->setActiveSheetIndex(5);
			$objPHPExcel->getActiveSheet()->setTitle($title);			
			$this->_get_transaction_logs_worksheet($worksheet,$start_date,$end_date,$type);		

			$objPHPExcel->setActiveSheetIndex(0);
			*/

			$objPHPExcel->setActiveSheetIndex(0);

			header('Content-Type: application/vnd.ms-excel');
			header('Content-Disposition: attachment;filename='.$filename.'');
			header('Cache-Control: max-age=0');

			$objWriter = IOFactory::createWriter($objPHPExcel, 'Excel2007');
			$objWriter->save('php://output');
			exit(0);

		} catch (Exception $e) {
			exit($e->getMessage());
		}		
	}

	public function merge_download_segmented_excel()
	{
		$type = $this->input->get_post("type");
		$start_date = $this->input->get_post("start_date");
		$end_date = $this->input->get_post("end_date");
		$generate_again = $this->input->get_post("generate_again");
		$generate_again = ($generate_again === 'true');

		$pretty_start_date = str_replace("-","",$start_date);
		$pretty_end_date = str_replace("-","",$end_date);

		$processed_sheet = $this->payout_model->get_download_sheet_processing(array(
			'type' => $type,
			'start_date' => $start_date,
			'end_date' => $end_date
		));

		if(count($processed_sheet) == 0)
		{
			$this->return_json(0,"FAIL");
		}
		else
		{
			if($generate_again)
			{
				$this->payout_model->update_download_sheet_processing(array(
					'status' => 'processing'
				), array(
					'type' => $type,
					'start_date' => $start_date,
					'end_date' => $end_date
				));

				$processed_sheet = $this->payout_model->get_download_sheet_processing(array(
					'type' => $type,
					'start_date' => $start_date,
					'end_date' => $end_date
				));
			}

			$processed_sheet = $processed_sheet[0];
			$generate_date = str_replace(" ", "_", $processed_sheet->insert_timestamp);
			$generate_date = str_replace("-", "", $generate_date);
			$generate_date = str_replace(":", "", $generate_date);

			if($processed_sheet->status == "completed")
			{
				$merged_filename = $type . '_commission_payout_' . $pretty_start_date . '_to_' . $pretty_end_date . '_generated_' . $generate_date . '.xlsx';
			}
			else
			{
				$this->payout_model->update_download_sheet_processing(array(
					'status' => 'processing',
					'insert_timestamp' => date('Y-m-d h:i:s',time())
				), array(
					'type' => $type,
					'start_date' => $start_date,
					'end_date' => $end_date
				));

				$processed_sheet = $this->payout_model->get_download_sheet_processing(array(
					'type' => $type,
					'start_date' => $start_date,
					'end_date' => $end_date
				));

				$processed_sheet = $processed_sheet[0];
				$generate_date = str_replace(" ", "_", $processed_sheet->insert_timestamp);
				$generate_date = str_replace("-", "", $generate_date);
				$generate_date = str_replace(":", "", $generate_date);

				$pretty_start_date = str_replace("-","",$start_date);
				$pretty_end_date = str_replace("-","",$end_date);

				$filenames = array();
				$sheets = $this->payout_model->get_payout_download_sheets();
				foreach($sheets as $sheet)
				{
					$filenames[] = FCPATH . "assets/media/tmp/" . $type . $sheet->file_name . $pretty_start_date . "_to_" . $pretty_end_date . ".xlsx";
				}

				$bigExcel = new PHPExcel();
				$bigExcel->removeSheetByIndex(0);

				$reader = new PHPExcel_Reader_Excel2007();

				foreach ($filenames as $filename) {
				    $excel = $reader->load($filename);
				    
				    foreach ($excel->getAllSheets() as $sheet) {
				        $bigExcel->addExternalSheet($sheet);
				        break;
				    }

				    unlink($filename);
				}

				if(!is_dir(FCPATH . "assets/media/payout"))
				{
					mkdir(FCPATH . "assets/media/payout/", 0775);
				}

				$writer = new PHPExcel_Writer_Excel2007($bigExcel);
				$merged_filename = $type . '_commission_payout_' . $pretty_start_date . '_to_' . $pretty_end_date . '_generated_' . $generate_date . '.xlsx';
				$writer->save(FCPATH . "assets/media/payout/" . $merged_filename);
			}

			$this->payout_model->update_download_sheet_processing(array(
				'status' => 'completed'
			), array(
				'type' => $type,
				'start_date' => $start_date,
				'end_date' => $end_date
			));

			$this->return_json(1,"SUCCESS",array('filename'=>$merged_filename));
		}
		return;
	}

	public function check_job_status()
	{
		$job_id = $this->input->get_post("job_id");

		$this->load->model('jobs_model');
		$job = $this->jobs_model->get_jobs(array(
			'job_id' => $job_id
		));

		if(count($job) > 0)
		{
			$job = $job[0];
			if($job->status == "completed")
			{
				$this->return_json(1,"SUCCESS", array('date_generated'=>$job->insert_timestamp));
			}
			else
			{
				$this->return_json(0,"FAIL");
			}
		}
		else
		{
			$this->return_json(0,"FAIL");
		}

		return;
	}

	public function start_download_job()
	{
		$sheet_id = $this->input->get_post("sheet_id");
		$type = $this->input->get_post("type");
		$start_date = $this->input->get_post("start_date");
		$end_date = $this->input->get_post("end_date");
		$payout_id = $this->input->get_post("payout_id");

		$this->load->model('jobs_model');
		$params = array(
			'sheet_id' => $sheet_id,
			'type' => $type,
			'start_date' => $start_date,
			'end_date' => $end_date,
			'payout_id' => $payout_id
		);
		$job_data = array(
			'job_type_id' => 4, // payout
			'parameters' => json_encode($params)
		);
		$this->jobs_model->insert_job($job_data);
		$job_id = $this->jobs_model->insert_id();

		job_exec($job_id);

		$this->return_json(1,"SUCCESS",array('job_id'=>$job_id));
		return;
	}

	public function get_download_form()
	{
		$type = $this->input->get_post("type");
		$start_date = $this->input->get_post("start_date");
		$end_date = $this->input->get_post("end_date");

		$processed_sheets = $this->payout_model->get_download_sheet_processing(array(
			'type' => $type,
			'start_date' => $start_date,
			'end_date' => $end_date,
			'status' => 'completed'
		));

		if(count($processed_sheets) > 0)
			$hasExisting = true;
		else
		{
			$hasExisting = false;
			$this->payout_model->insert_download_sheet_processing(array(
				'type' => $type,
				'start_date' => $start_date,
				'end_date' => $end_date,
				'status' => 'processing'
			));

			$processed_sheets = $this->payout_model->get_download_sheet_processing(array(
				'type' => $type,
				'start_date' => $start_date,
				'end_date' => $end_date
			));
		}

		$html = "<div>";
		$html .= "
			<div class='row-fluid'>
				<div class='span12' style='text-align: center; font-weight: bold;'>Please wait. Generation process may take awhile.</div>
			</div>
		";
		$sheets = $this->payout_model->get_payout_download_sheets();

		$processed_sheets = $processed_sheets[0];
		foreach($sheets as $sheet)
		{
			if(!$hasExisting)
			{
				$html .= "
				<div class='row-fluid'>
					<div class='span4'>{$sheet->sheet_name}</div>
					<div class='span8'>
						<div class='all-sheets dl-sheets' data-dl='{$sheet->sheet_id}'>
							<div class='label' style='display: inline;'>Pending</div>
						</div>
					</div>
				</div>
				";
			}
			else
			{
				//$sheet_processing = $sheet_processing[0];
				$html .= "
				<div class='row-fluid'>
					<div class='span4'>{$sheet->sheet_name}</div>
					<div class='span8'>
						<div class='all-sheets' data-dl='{$sheet->sheet_id}'>
							<div class='label label-success' style='display: inline;'>Completed: {$processed_sheets->insert_timestamp}</div>
						</div>
					</div>
				</div>
				";
			}
		}
		$html .= "</div>";
		
		$this->return_json(1,"SUCCESS",array('html'=>$html, 'hasExisting'=>$hasExisting));
		return;
	}

	private function _get_commissions_per_account_worksheet($worksheet, $start_date, $end_date, $type, $filter = "all")
	{	
		$title = "{$type} Commissions per Account for {$start_date} to {$end_date}";

		$start_column_num = 3;

		//set width of first column
		$worksheet->getColumnDimension('A')->setWidth(12.00);

		// set column header to bold
		$worksheet->getStyle('A1')->getFont()->setBold(true);
		$worksheet->getStyle('A' . $start_column_num)->getFont()->setBold(true);
		$worksheet->getStyle('B' . $start_column_num)->getFont()->setBold(true);
		$worksheet->getStyle('C' . $start_column_num)->getFont()->setBold(true);
		$worksheet->getStyle('D' . $start_column_num)->getFont()->setBold(true);
		$worksheet->getStyle('E' . $start_column_num)->getFont()->setBold(true);
		$worksheet->getStyle('F' . $start_column_num)->getFont()->setBold(true);
		$worksheet->getStyle('G' . $start_column_num)->getFont()->setBold(true);
		$worksheet->getStyle('H' . $start_column_num)->getFont()->setBold(true);
		$worksheet->getStyle('I' . $start_column_num)->getFont()->setBold(true);
		$worksheet->getStyle('J' . $start_column_num)->getFont()->setBold(true);
		$worksheet->getStyle('K' . $start_column_num)->getFont()->setBold(true);
		$worksheet->getStyle('L' . $start_column_num)->getFont()->setBold(true);
		$worksheet->getStyle('M' . $start_column_num)->getFont()->setBold(true);

		//center column names
		$worksheet->getStyle('A' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$worksheet->getStyle('B' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$worksheet->getStyle('C' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$worksheet->getStyle('D' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$worksheet->getStyle('E' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$worksheet->getStyle('F' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$worksheet->getStyle('G' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$worksheet->getStyle('H' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$worksheet->getStyle('I' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$worksheet->getStyle('J' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$worksheet->getStyle('K' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$worksheet->getStyle('L' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$worksheet->getStyle('M' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

		//set column names
		$worksheet->setCellValue('A1', $title);
		$worksheet->setCellValue('A' . $start_column_num, 'Last Name');
		$worksheet->setCellValue('B' . $start_column_num, 'First Name');
		$worksheet->setCellValue('C' . $start_column_num, 'Middle Name');
		$worksheet->setCellValue('D' . $start_column_num, 'Account ID');
		$worksheet->setCellValue('E' . $start_column_num, 'Gross');
		$worksheet->setCellValue('F' . $start_column_num, 'Tax');
		$worksheet->setCellValue('G' . $start_column_num, 'Net');
		$worksheet->setCellValue('H' . $start_column_num, 'Balance');
		$worksheet->setCellValue('I' . $start_column_num, 'Total');
		$worksheet->setCellValue('J' . $start_column_num, 'Cash Card');
		$worksheet->setCellValue('K' . $start_column_num, 'Account Status');
		$worksheet->setCellValue('L' . $start_column_num, 'Date Registered');
		$worksheet->setCellValue('M' . $start_column_num, 'Commission Date');

		$commissions = $this->payout_model->get_member_account_commissions(array(
			'start_date' => $start_date,
			'end_date' => $end_date,
			'payout_type' => $type
		));
		
		$witholding_tax = $this->settings->witholding_tax;
		$row = 4;
		
		foreach($commissions as $c)
		{
			$c->cd_amount = $c->balance;
			$c->amount = $c->gross;

			$member = $this->members_model->get_member_by_id($c->member_id);
			$c->last_name = $member->last_name;
			$c->first_name = $member->first_name;
			$c->middle_name = $member->middle_name;

			$account = $this->members_model->get_member_accounts(array('account_id' => $c->account_id));
			$account = $account[0];
			$c->account_insert_timestamp = $account->insert_timestamp;

			if($filter=="new_accounts"){
				$date_registered_time = date(strtotime($c->account_insert_timestamp));
				$curr_time = date(time());

				$difference = $curr_time - $date_registered_time;
				$months = floor($difference / 86400 / 30 );

				if($months > 3) continue;
			}

			if ($type=="IGPSM")
				$balance = $c->cd_amount;
			else
				$balance = 0;
			
			// computed values
			/*
			if ($c->cash_card == 'TO FUNDS')
				$tax = 0;
			else
				$tax = $c->amount * $witholding_tax;
			*/
			$tax = $c->amount * $witholding_tax;
			
			$net = $c->amount - $tax;
			$total = $net - $balance;
			$commission_date = $c->start_date . ' - ' . $c->end_date;

			$worksheet->setCellValue('A'. $row, $c->last_name);
			$worksheet->setCellValue('B'. $row, $c->first_name);
			$worksheet->setCellValue('C'. $row, $c->middle_name);
			$worksheet->setCellValue('D'. $row, $c->account_id);
			$worksheet->setCellValue('E'. $row, $c->amount);
			$worksheet->setCellValue('F'. $row, $tax);
			$worksheet->setCellValue('G'. $row, "=E{$row} - F{$row}");
			$worksheet->setCellValue('H'. $row, $balance);
			$worksheet->setCellValue('I'. $row, "=G{$row} - H{$row}");
			$worksheet->setCellValueExplicit('J'. $row, "{$c->cash_card}",PHPExcel_Cell_DataType::TYPE_STRING);
			$worksheet->setCellValue('K'. $row, $c->account_status);
			$worksheet->setCellValue('L'. $row, $c->insert_timestamp);
			$worksheet->setCellValue('M'. $row, $commission_date);

			// auto resize columns
			$worksheet->getColumnDimension('A')->setAutoSize(true);
			$worksheet->getColumnDimension('B')->setAutoSize(true);
			$worksheet->getColumnDimension('C')->setAutoSize(true);
			$worksheet->getColumnDimension('D')->setAutoSize(true);
			$worksheet->getColumnDimension('E')->setAutoSize(true);
			$worksheet->getColumnDimension('F')->setAutoSize(true);
			$worksheet->getColumnDimension('G')->setAutoSize(true);
			$worksheet->getColumnDimension('H')->setAutoSize(true);
			$worksheet->getColumnDimension('I')->setAutoSize(true);
			$worksheet->getColumnDimension('J')->setAutoSize(true);
			$worksheet->getColumnDimension('K')->setAutoSize(true);
			$worksheet->getColumnDimension('L')->setAutoSize(true);
			$worksheet->getColumnDimension('M')->setAutoSize(true);

			$row++;			
		}
		
	}

	private function _get_commissions_per_member_worksheet($worksheet, $start_date, $end_date, $type, $filter = "all")
	{	
		$title = "{$type} Commissions per Member for {$start_date} to {$end_date}";

		$start_column_num = 3;

		//set width of first column
		$worksheet->getColumnDimension('A')->setWidth(12.00);

		// set column header to bold
		$worksheet->getStyle('A1')->getFont()->setBold(true);
		$worksheet->getStyle('A' . $start_column_num)->getFont()->setBold(true);
		$worksheet->getStyle('B' . $start_column_num)->getFont()->setBold(true);
		$worksheet->getStyle('C' . $start_column_num)->getFont()->setBold(true);
		$worksheet->getStyle('D' . $start_column_num)->getFont()->setBold(true);
		$worksheet->getStyle('E' . $start_column_num)->getFont()->setBold(true);
		$worksheet->getStyle('F' . $start_column_num)->getFont()->setBold(true);
		$worksheet->getStyle('G' . $start_column_num)->getFont()->setBold(true);
		$worksheet->getStyle('H' . $start_column_num)->getFont()->setBold(true);
		$worksheet->getStyle('I' . $start_column_num)->getFont()->setBold(true);
		$worksheet->getStyle('J' . $start_column_num)->getFont()->setBold(true);

		//center column names
		$worksheet->getStyle('A' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$worksheet->getStyle('B' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$worksheet->getStyle('C' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$worksheet->getStyle('D' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$worksheet->getStyle('E' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$worksheet->getStyle('F' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$worksheet->getStyle('G' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$worksheet->getStyle('H' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$worksheet->getStyle('I' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$worksheet->getStyle('J' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

		//set column names
		$worksheet->setCellValue('A1', $title);
		$worksheet->setCellValue('A' . $start_column_num, 'Last Name');
		$worksheet->setCellValue('B' . $start_column_num, 'First Name');
		$worksheet->setCellValue('C' . $start_column_num, 'Middle Name');
		$worksheet->setCellValue('D' . $start_column_num, 'Gross');
		$worksheet->setCellValue('E' . $start_column_num, 'Tax');
		$worksheet->setCellValue('F' . $start_column_num, 'Net');
		$worksheet->setCellValue('G' . $start_column_num, 'Balance');
		$worksheet->setCellValue('H' . $start_column_num, 'Total');
		$worksheet->setCellValue('I' . $start_column_num, 'Cash Card');
		$worksheet->setCellValue('J' . $start_column_num, 'Commission Date');

		$commissions = $this->payout_model->get_member_account_commissions(array(
			'start_date' => $start_date,
			'end_date' => $end_date,
			'payout_type' => $type
		));
		
		$witholding_tax = $this->settings->witholding_tax;
		$members_commissions = array();
		$row = 4;
		
		foreach($commissions as $c)
		{
			$c->cd_amount = $c->balance;
			$c->amount = $c->gross;

			$member = $this->members_model->get_member_by_id($c->member_id);
			$c->last_name = $member->last_name;
			$c->first_name = $member->first_name;
			$c->middle_name = $member->middle_name;

			$account = $this->members_model->get_member_accounts(array('account_id' => $c->account_id));
			$account = $account[0];
			$c->account_insert_timestamp = $account->insert_timestamp;

			$date_registered_time = date(strtotime($c->account_insert_timestamp));
			$curr_time = date(time());

			$difference = $curr_time - $date_registered_time;
			$months = floor($difference / 86400 / 30 );

			if($months > 3) continue;

			$member_obj = new stdClass;
			$member_obj->last_name = $c->last_name;
			$member_obj->first_name = $c->first_name;
			$member_obj->middle_name = $c->middle_name;
			$member_obj->amount = $c->amount;
			if ($c->cash_card == 'TO FUNDS')
				$member_obj->tax = 0;
			else
				$member_obj->tax = $c->amount * $witholding_tax;
			if ($type=="IGPSM")
				$member_obj->balance = $c->cd_amount;				
			else
				$member_obj->balance = 0;
			$member_obj->cash_card = $c->cash_card;
			$member_obj->commission_dates = $c->start_date . ' - ' . $c->end_date;

			if(isset($members_commissions[$c->member_id]))
			{
				$members_commissions[$c->member_id]->amount += $member_obj->amount;
				$members_commissions[$c->member_id]->tax += $member_obj->tax;
				$members_commissions[$c->member_id]->balance += $member_obj->balance;
			}
			else
			{
				$members_commissions[$c->member_id] = $member_obj;
			}	
		}

		foreach($members_commissions as $c)
		{
			$worksheet->setCellValue('A'. $row, $c->last_name);
			$worksheet->setCellValue('B'. $row, $c->first_name);
			$worksheet->setCellValue('C'. $row, $c->middle_name);
			$worksheet->setCellValue('D'. $row, $c->amount);
			$worksheet->setCellValue('E'. $row, $c->tax);
			$worksheet->setCellValue('F'. $row, "=D{$row} - E{$row}");
			$worksheet->setCellValue('G'. $row, $c->balance);
			$worksheet->setCellValue('H'. $row, "=F{$row} - G{$row}");
			$worksheet->setCellValueExplicit('I'. $row, "{$c->cash_card}",PHPExcel_Cell_DataType::TYPE_STRING);
			$worksheet->setCellValue('J'. $row, $c->commission_dates);

			// auto resize columns
			$worksheet->getColumnDimension('A')->setAutoSize(true);
			$worksheet->getColumnDimension('B')->setAutoSize(true);
			$worksheet->getColumnDimension('C')->setAutoSize(true);
			$worksheet->getColumnDimension('D')->setAutoSize(true);
			$worksheet->getColumnDimension('E')->setAutoSize(true);
			$worksheet->getColumnDimension('F')->setAutoSize(true);
			$worksheet->getColumnDimension('G')->setAutoSize(true);
			$worksheet->getColumnDimension('H')->setAutoSize(true);
			$worksheet->getColumnDimension('I')->setAutoSize(true);
			$worksheet->getColumnDimension('J')->setAutoSize(true);

			$row++;
		}
	}

	private function _get_gc_per_account_worksheet($worksheet, $start_date, $end_date)
	{
		$title = "Gift Cheques per Account for {$start_date} to {$end_date}";

		//set width of first column
		$worksheet->getColumnDimension('A')->setWidth(12.00);

		// set column header to bold
		$worksheet->getStyle('A1')->getFont()->setBold(true);

		//set column names
		$worksheet->setCellValue('A1', $title);
		
		$query = $this->_get_gc_per_account_query($start_date,$end_date);
		
		$sortedRows = array();
		foreach ($query->result() as $r)
		{
			$gcType = explode(' ', $r->type);
			$sortedRows[$gcType[0]][] = $r;
		}

		$row = 3;
		$sortedRowsKeys = array_keys($sortedRows);
		foreach($sortedRowsKeys as $k => $v)
		{
			// group title
			$groupTitle = strtoupper($v) . " GC";
			$worksheet->getStyle('A' . $row)->getFont()->setBold(true);
			$worksheet->getStyle('A' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$worksheet->setCellValue('A' . $row, $groupTitle);
			$row++;

			// group fields
			$worksheet->getStyle('A' . $row)->getFont()->setBold(true);
			$worksheet->getStyle('B' . $row)->getFont()->setBold(true);
			$worksheet->getStyle('C' . $row)->getFont()->setBold(true);
			$worksheet->getStyle('D' . $row)->getFont()->setBold(true);
			$worksheet->getStyle('E' . $row)->getFont()->setBold(true);
			$worksheet->getStyle('F' . $row)->getFont()->setBold(true);

			$worksheet->getStyle('A' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$worksheet->getStyle('B' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$worksheet->getStyle('C' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$worksheet->getStyle('D' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$worksheet->getStyle('E' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$worksheet->getStyle('F' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

			$worksheet->setCellValue('A' . $row, 'Last Name');
			$worksheet->setCellValue('B' . $row, 'First Name');
			$worksheet->setCellValue('C' . $row, 'Middle Name');
			$worksheet->setCellValue('D' . $row, 'Account ID');
			$worksheet->setCellValue('E' . $row, 'Gift Cheques');
			$worksheet->setCellValue('F' . $row, 'Account Status');

			$row++;

			// display group values
			for($i = 0; $i < sizeof($sortedRows[$v]); $i++) {
				$worksheet->setCellValue('A'. $row, $sortedRows[$v][$i]->last_name);
				$worksheet->setCellValue('B'. $row, $sortedRows[$v][$i]->first_name);
				$worksheet->setCellValue('C'. $row, $sortedRows[$v][$i]->middle_name);
				$worksheet->setCellValue('D'. $row, $sortedRows[$v][$i]->account_id);
				$worksheet->setCellValue('E'. $row, $sortedRows[$v][$i]->amount);
				$worksheet->setCellValue('F'. $row, $sortedRows[$v][$i]->account_status);

				// auto resize columns
				$worksheet->getColumnDimension('A')->setAutoSize(true);
				$worksheet->getColumnDimension('B')->setAutoSize(true);
				$worksheet->getColumnDimension('C')->setAutoSize(true);
				$worksheet->getColumnDimension('D')->setAutoSize(true);
				$worksheet->getColumnDimension('E')->setAutoSize(true);
				$worksheet->getColumnDimension('F')->setAutoSize(true);

				$row++;
			}

			$row++;
		}		
	}

	private function _get_member_deduction_worksheet($worksheet, $start_date, $end_date, $payout_id)
	{
		$title = "Member Deduction for {$start_date} to {$end_date}";

		$start_column_num = 3;

		// set width of first column
		$worksheet->getColumnDimension('A')->setWidth(12.00);

		// set column header to bold
		$worksheet->getStyle('A1')->getFont()->setBold(true);
		$worksheet->getStyle('A' . $start_column_num)->getFont()->setBold(true);
		$worksheet->getStyle('B' . $start_column_num)->getFont()->setBold(true);
		$worksheet->getStyle('C' . $start_column_num)->getFont()->setBold(true);
		$worksheet->getStyle('D' . $start_column_num)->getFont()->setBold(true);
		$worksheet->getStyle('E' . $start_column_num)->getFont()->setBold(true);
		$worksheet->getStyle('F' . $start_column_num)->getFont()->setBold(true);

		//center column names
		$worksheet->getStyle('A' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$worksheet->getStyle('B' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$worksheet->getStyle('C' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$worksheet->getStyle('D' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$worksheet->getStyle('E' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$worksheet->getStyle('F' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

		//set column names
		$worksheet->setCellValue('A1', $title);
		$worksheet->setCellValue('A' . $start_column_num, 'Last Name');
		$worksheet->setCellValue('B' . $start_column_num, 'First Name');
		$worksheet->setCellValue('C' . $start_column_num, 'Middle Name');
		$worksheet->setCellValue('D' . $start_column_num, 'Total Net Commissions');
		$worksheet->setCellValue('E' . $start_column_num, 'Deduction');
		$worksheet->setCellValue('F' . $start_column_num, 'Remarks');

		$members_payout = $this->payout_model->get_member_commissions_report(array(
			'payout_id' => $payout_id,
			'start_date' => $start_date,
			'end_date' => $end_date,
		));

		$row = 4;
		
		foreach ($members_payout as $r)
		{
			$member = $this->members_model->get_member_by_id($r->member_id);
			$deduction = $this->members_model->get_member_deductions(array('member_id' => $member->member_id));
			$deduction_to_all = $this->members_model->get_member_deductions(array('is_to_all' => 1));
			// add deduction_to_all to deduction list
			foreach($deduction_to_all as $to_all)
			{
				$deduction[] = $to_all;
			}

			foreach($deduction as $cm_d)
			{
				// check if it already exist on po deductions
				$po_deduction = $this->payout_model->get_member_deductions(array(
					'deduction_id' => $cm_d->deduction_id,
					'payout_id' => $payout_id,
					'member_id' => $r->member_id,
					'start_date' => $start_date,
					'end_date' => $end_date
				));

				if(count($po_deduction) == 0)
				{
					$worksheet->setCellValue('A'. $row, $member->last_name);
					$worksheet->setCellValue('B'. $row, $member->first_name);
					$worksheet->setCellValue('C'. $row, $member->middle_name);
					$worksheet->setCellValue('D'. $row, $r->total_amount);
					$worksheet->setCellValue('E'. $row, $cm_d->deduction_per_payout);
					$worksheet->setCellValue('F'. $row, $cm_d->remarks);

					// auto resize columns
					$worksheet->getColumnDimension('A')->setAutoSize(true);
					$worksheet->getColumnDimension('B')->setAutoSize(true);
					$worksheet->getColumnDimension('C')->setAutoSize(true);
					$worksheet->getColumnDimension('D')->setAutoSize(true);
					$worksheet->getColumnDimension('E')->setAutoSize(true);
					$worksheet->getColumnDimension('F')->setAutoSize(true);

					// format total amount if negative
					$worksheet->getStyle('D' . $row)->getNumberFormat()->setFormatCode("[Black]#,##0;[Red](-#,##0)");
					$worksheet->getStyle('E' . $row)->getNumberFormat()->setFormatCode("[Black]#,##0;[Red](-#,##0)");
					
					$row++;
				}
			}
		}
	}

	private function _get_member_payout_worksheet($worksheet, $start_date, $end_date, $payout_id)
	{				
		$title = "Member Payout for {$start_date} to {$end_date}";

		$start_column_num = 3;

		//set width of first column
		$worksheet->getColumnDimension('A')->setWidth(12.00);

		// set column header to bold
		$worksheet->getStyle('A1')->getFont()->setBold(true);
		$worksheet->getStyle('A' . $start_column_num)->getFont()->setBold(true);
		$worksheet->getStyle('B' . $start_column_num)->getFont()->setBold(true);
		$worksheet->getStyle('C' . $start_column_num)->getFont()->setBold(true);
		$worksheet->getStyle('D' . $start_column_num)->getFont()->setBold(true);
		$worksheet->getStyle('E' . $start_column_num)->getFont()->setBold(true);
		$worksheet->getStyle('F' . $start_column_num)->getFont()->setBold(true);
		$worksheet->getStyle('G' . $start_column_num)->getFont()->setBold(true);
		$worksheet->getStyle('H' . $start_column_num)->getFont()->setBold(true);
		$worksheet->getStyle('I' . $start_column_num)->getFont()->setBold(true);
		$worksheet->getStyle('J' . $start_column_num)->getFont()->setBold(true);
		$worksheet->getStyle('K' . $start_column_num)->getFont()->setBold(true);
		$worksheet->getStyle('L' . $start_column_num)->getFont()->setBold(true);
		$worksheet->getStyle('M' . $start_column_num)->getFont()->setBold(true);
		$worksheet->getStyle('N' . $start_column_num)->getFont()->setBold(true);

		//center column names
		$worksheet->getStyle('A' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$worksheet->getStyle('B' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$worksheet->getStyle('C' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$worksheet->getStyle('D' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$worksheet->getStyle('E' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$worksheet->getStyle('F' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$worksheet->getStyle('G' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$worksheet->getStyle('H' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$worksheet->getStyle('I' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$worksheet->getStyle('J' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$worksheet->getStyle('K' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$worksheet->getStyle('L' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$worksheet->getStyle('M' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$worksheet->getStyle('N' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

		//set column names
		$worksheet->setCellValue('A1', $title);
		$worksheet->setCellValue('A' . $start_column_num, 'Last Name');
		$worksheet->setCellValue('B' . $start_column_num, 'First Name');
		$worksheet->setCellValue('C' . $start_column_num, 'Middle Name');
		$worksheet->setCellValue('D' . $start_column_num, 'Gross');
		$worksheet->setCellValue('E' . $start_column_num, 'Less 10%');
		$worksheet->setCellValue('F' . $start_column_num, 'Net Gross');
		$worksheet->setCellValue('G' . $start_column_num, 'Tax');
		$worksheet->setCellValue('H' . $start_column_num, 'Net of Tax');
		$worksheet->setCellValue('I' . $start_column_num, 'Balance');
		$worksheet->setCellValue('J' . $start_column_num, 'Total');
		$worksheet->setCellValue('K' . $start_column_num, 'Cash Card');
		$worksheet->setCellValue('L' . $start_column_num, 'Account Number');
		$worksheet->setCellValue('M' . $start_column_num, 'Service Depot');
		$worksheet->setCellValue('N' . $start_column_num, 'Group Name');
		
		$members_payout = $this->payout_model->get_member_commissions_report(array(
				'payout_id' => $payout_id,
				'start_date' => $start_date,
				'end_date' => $end_date,
			));
		
		$row = 4;
		
		foreach ($members_payout as $r)
		{
			$member = $this->members_model->get_member_by_id($r->member_id);

			$rfid_paycard_number = '';
			$rfid_account_number = '';
			$rfid = $this->members_model->get_member_rfid_cards(array('paycard_number'=>$r->cash_card));
			if(sizeof($rfid) > 0) {
				$rfid_paycard_number = $rfid[0]->paycard_number;
				$rfid_account_number = $rfid[0]->account_number;
			}

			$service_depot = $this->facilities_model->get_service_depots(array(
				'service_depot_id' => $member->service_depot
			));

			$service_depot = (count($service_depot) > 0)?$service_depot[0]->depot_name:'';
			
			$worksheet->setCellValue('A'. $row, $member->last_name);
			$worksheet->setCellValue('B'. $row, $member->first_name);
			$worksheet->setCellValue('C'. $row, $member->middle_name);
			$worksheet->setCellValue('D'. $row, $r->gross);
			$worksheet->setCellValue('E'. $row, $r->psf);			
			$worksheet->setCellValue('F'. $row, "=D{$row} - E{$row}");			
			$worksheet->setCellValue('G'. $row, $r->tax);
			$worksheet->setCellValue('H'. $row, "=F{$row} - G{$row}");
			$worksheet->setCellValue('I'. $row, $r->balance);
			$worksheet->setCellValue('J'. $row, "=H{$row} - I{$row}");
			$worksheet->setCellValueExplicit('K'. $row, "{$r->cash_card}",PHPExcel_Cell_DataType::TYPE_STRING);
			$worksheet->setCellValueExplicit('L'. $row, "{$rfid_account_number}",PHPExcel_Cell_DataType::TYPE_STRING);
			$worksheet->setCellValue('M'. $row, $service_depot);
			$worksheet->setCellValue('N'. $row, $member->group_name);

			// auto resize columns
			$worksheet->getColumnDimension('A')->setAutoSize(true);
			$worksheet->getColumnDimension('B')->setAutoSize(true);
			$worksheet->getColumnDimension('C')->setAutoSize(true);
			$worksheet->getColumnDimension('D')->setAutoSize(true);
			$worksheet->getColumnDimension('E')->setAutoSize(true);
			$worksheet->getColumnDimension('F')->setAutoSize(true);
			$worksheet->getColumnDimension('G')->setAutoSize(true);
			$worksheet->getColumnDimension('H')->setAutoSize(true);
			$worksheet->getColumnDimension('I')->setAutoSize(true);
			$worksheet->getColumnDimension('J')->setAutoSize(true);
			$worksheet->getColumnDimension('K')->setAutoSize(true);
			$worksheet->getColumnDimension('L')->setAutoSize(true);
			$worksheet->getColumnDimension('M')->setAutoSize(true);
			$worksheet->getColumnDimension('N')->setAutoSize(true);

			// format total amount if negative
			$worksheet->getStyle('J' . $row)->getNumberFormat()->setFormatCode("[Black]#,##0;[Red](-#,##0)");
			
			$row++;
		}
	}

	private function _get_transferred_per_member($worksheet, $start_date, $end_date, $type)
	{
		$title = "Member Transferred {$type} for {$start_date} to {$end_date}";

		$start_column_num = 3;

		$worksheet->mergeCells('A1:E1');

		// set column header to bold
		$worksheet->getStyle('A1')->getFont()->setBold(true);
		$worksheet->getStyle('A' . $start_column_num)->getFont()->setBold(true);
		$worksheet->getStyle('D' . $start_column_num)->getFont()->setBold(true);

		//center column names
		$worksheet->getStyle('A' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$worksheet->getStyle('B' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$worksheet->getStyle('C' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$worksheet->getStyle('D' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$worksheet->getStyle('E' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$worksheet->getStyle('F' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$worksheet->getStyle('G' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$worksheet->getStyle('H' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$worksheet->getStyle('I' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

		//merge cells for FROM and TO
		$worksheet->mergeCells('A'. $start_column_num .':C'.$start_column_num);
		$worksheet->mergeCells('D'. $start_column_num .':F'.$start_column_num);
		$worksheet->setCellValue('A' . $start_column_num, 'FROM');
		$worksheet->setCellValue('D' . $start_column_num, 'TO');

		$start_column_num++;

		//set column names
		$worksheet->setCellValue('A1', $title);
		$worksheet->setCellValue('A' . $start_column_num, 'Last Name');
		$worksheet->setCellValue('B' . $start_column_num, 'First Name');
		$worksheet->setCellValue('C' . $start_column_num, 'Middle Name');
		$worksheet->setCellValue('D' . $start_column_num, 'Last Name');
		$worksheet->setCellValue('E' . $start_column_num, 'First Name');
		$worksheet->setCellValue('F' . $start_column_num, 'Middle Name');
		$worksheet->setCellValue('G' . $start_column_num, 'Amount');
		$worksheet->setCellValue('H' . $start_column_num, 'Status');
		$worksheet->setCellValue('I' . $start_column_num, 'Date Time');

		$worksheet->getStyle('A' . $start_column_num)->getFont()->setBold(true);
		$worksheet->getStyle('B' . $start_column_num)->getFont()->setBold(true);
		$worksheet->getStyle('C' . $start_column_num)->getFont()->setBold(true);
		$worksheet->getStyle('D' . $start_column_num)->getFont()->setBold(true);
		$worksheet->getStyle('E' . $start_column_num)->getFont()->setBold(true);
		$worksheet->getStyle('F' . $start_column_num)->getFont()->setBold(true);
		$worksheet->getStyle('G' . $start_column_num)->getFont()->setBold(true);
		$worksheet->getStyle('H' . $start_column_num)->getFont()->setBold(true);
		$worksheet->getStyle('I' . $start_column_num)->getFont()->setBold(true);

		$sql = "
			SELECT *
			FROM
				tr_member_transfers
			WHERE
				`type` = '{$type}'
			AND
				`insert_timestamp` BETWEEN '{$start_date}' AND '{$end_date}'
			ORDER BY
				status, from_member_id, to_member_id";
		$query = $this->db->query($sql);
		$transfers = $query->result();

		$row = 5;
		foreach($transfers as $trans)
		{
			$from_member = $this->members_model->get_member_by_id($trans->from_member_id);
			$to_member = $this->members_model->get_member_by_id($trans->to_member_id);

			$worksheet->setCellValue('A'. $row, $from_member->last_name);
			$worksheet->setCellValue('B'. $row, $from_member->first_name);
			$worksheet->setCellValue('C'. $row, $from_member->middle_name);
			$worksheet->setCellValue('D'. $row, $to_member->last_name);
			$worksheet->setCellValue('E'. $row, $to_member->first_name);
			$worksheet->setCellValue('F'. $row, $to_member->middle_name);
			$worksheet->setCellValue('G'. $row, $trans->amount);
			$worksheet->setCellValue('H'. $row, $trans->status);
			$worksheet->setCellValue('I'. $row, $trans->insert_timestamp);

			// format total amount if negative
			$worksheet->getStyle('G' . $row)->getNumberFormat()->setFormatCode("[Black]#,##0;[Red](-#,##0)");

			// auto resize columns
			$worksheet->getColumnDimension('A')->setAutoSize(true);
			$worksheet->getColumnDimension('B')->setAutoSize(true);
			$worksheet->getColumnDimension('C')->setAutoSize(true);
			$worksheet->getColumnDimension('D')->setAutoSize(true);
			$worksheet->getColumnDimension('E')->setAutoSize(true);
			$worksheet->getColumnDimension('F')->setAutoSize(true);
			$worksheet->getColumnDimension('G')->setAutoSize(true);
			$worksheet->getColumnDimension('H')->setAutoSize(true);
			$worksheet->getColumnDimension('I')->setAutoSize(true);

			$row++;
		}
	}

	private function _get_transaction_logs_worksheet($worksheet, $start_date, $end_date, $type)
	{
		$title = "Member Transaction Logs for {$start_date} to {$end_date}";

		$start_column_num = 3;

		//set width of first column
		$worksheet->getColumnDimension('A')->setWidth(12.00);

		// set column header to bold
		$worksheet->getStyle('A1')->getFont()->setBold(true);
		$worksheet->getStyle('A' . $start_column_num)->getFont()->setBold(true);
		$worksheet->getStyle('B' . $start_column_num)->getFont()->setBold(true);
		$worksheet->getStyle('C' . $start_column_num)->getFont()->setBold(true);
		$worksheet->getStyle('D' . $start_column_num)->getFont()->setBold(true);
		$worksheet->getStyle('E' . $start_column_num)->getFont()->setBold(true);
		$worksheet->getStyle('F' . $start_column_num)->getFont()->setBold(true);
		$worksheet->getStyle('G' . $start_column_num)->getFont()->setBold(true);
		$worksheet->getStyle('H' . $start_column_num)->getFont()->setBold(true);
		$worksheet->getStyle('I' . $start_column_num)->getFont()->setBold(true);

		//center column names
		$worksheet->getStyle('A' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$worksheet->getStyle('B' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$worksheet->getStyle('C' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$worksheet->getStyle('D' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$worksheet->getStyle('E' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$worksheet->getStyle('F' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$worksheet->getStyle('G' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$worksheet->getStyle('H' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$worksheet->getStyle('I' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

		//set column names
		$worksheet->setCellValue('A1', $title);
		$worksheet->setCellValue('A' . $start_column_num, 'Last Name');
		$worksheet->setCellValue('B' . $start_column_num, 'First Name');
		$worksheet->setCellValue('C' . $start_column_num, 'Middle Name');
		$worksheet->setCellValue('D' . $start_column_num, 'Account ID');
		$worksheet->setCellValue('E' . $start_column_num, 'Details');
		$worksheet->setCellValue('F' . $start_column_num, 'Type');
		$worksheet->setCellValue('G' . $start_column_num, 'Level');
		$worksheet->setCellValue('H' . $start_column_num, 'Amount');
		$worksheet->setCellValue('I' . $start_column_num, 'Date Time');
		
		$member_id = 0;
		$row = 4;

		$count = $this->_get_transaction_logs_query_count($type,$member_id,$start_date,$end_date);
		$count = $count[0]->cnt;
		$count_limit = 0;
		$query_limit = 5000;

		while($count_limit < $count)
		{
			$count_limit += $query_limit;
		}
		$count_limit += $query_limit;
		$loops = $count_limit/$query_limit;

		for($i = 0; $i <= $loops; $i++)
		{
			$offset = $i * $query_limit;
			$query = $this->_get_transaction_logs_query($type,$member_id,$start_date,$end_date,$query_limit,$offset);
			foreach ($query->result() as $r)
			{
				if($r->amount != 0)
				{
					$worksheet->setCellValue('A'. $row, $r->last_name);
					$worksheet->setCellValue('B'. $row, $r->first_name);
					$worksheet->setCellValue('C'. $row, $r->middle_name);
					$worksheet->setCellValue('D'. $row, $r->account_id);
					$worksheet->setCellValue('E'. $row, $r->remarks);
					$worksheet->setCellValue('F'. $row, $r->type);
					$worksheet->setCellValue('G'. $row, $r->level);
					$worksheet->setCellValue('H'. $row, $r->amount);
					$worksheet->setCellValue('I'. $row, $r->insert_timestamp);

					// auto resize columns
					$worksheet->getColumnDimension('A')->setAutoSize(true);
					$worksheet->getColumnDimension('B')->setAutoSize(true);
					$worksheet->getColumnDimension('C')->setAutoSize(true);
					$worksheet->getColumnDimension('D')->setAutoSize(true);
					$worksheet->getColumnDimension('E')->setAutoSize(true);
					$worksheet->getColumnDimension('F')->setAutoSize(true);
					$worksheet->getColumnDimension('G')->setAutoSize(true);
					$worksheet->getColumnDimension('H')->setAutoSize(true);
					$worksheet->getColumnDimension('I')->setAutoSize(true);

					$row++;
				}
			}
		}

		
	}

	private function _get_gc_per_account_query($start_date,$end_date)
	{
		$sql = "
			SELECT
		    	a.member_id,				
				a.account_id,
				b.last_name,
				b.first_name,
				b.middle_name,
				d.transaction_description as `type`,
				SUM(a.amount) AS amount,
				CASE 
					WHEN c.account_status_id = 1 THEN 'ACTIVE' 
					WHEN c.account_status_id = 2 THEN 'INACTIVE' 
					WHEN c.account_status_id = 3 THEN 'COMPANY' 
				END AS account_status
			FROM
				po_member_commissions a
			LEFT JOIN
				cm_members b ON a.member_id = b.member_id
			LEFT JOIN
				cm_member_accounts c ON a.account_id = c.account_id
			LEFT JOIN
				`rf_transaction_codes` d ON a.transaction_code = d.transaction_code
			WHERE
				a.transaction_code >= 106 AND a.transaction_code <= 109
			AND
				a.start_date = '{$start_date}'
			AND
				a.end_date = '{$end_date}'	
			GROUP BY
				a.transaction_code, a.account_id
			ORDER BY
				b.last_name,b.first_name,b.middle_name, c.account_id, a.transaction_code";
		$query = $this->db->query($sql);
		return $query;
	}

	private function _get_member_payout_query($type,$start_date,$end_date)
	{
		$sql = "
			SELECT
				a.member_id,
				b.last_name,
				b.first_name,
				b.middle_name,
				b.metrobank_paycard_number,
				SUM(a.gross) AS amount,
				a.cash_card AS cash_card,
				c.depot_name,
				b.group_name
			FROM
				po_member_account_commissions a
			LEFT JOIN
				cm_members b ON a.member_id = b.member_id
			LEFT JOIN 
				rf_service_depots c ON c.service_depot_id = b.service_depot
			WHERE 
				(a.account_status = 'ACTIVE' OR a.account_status='COMPANY')
			AND
				a.start_date = '{$start_date}'
			AND
				a.end_date = '{$end_date}'
			AND
				a.payout_type = '{$type}'
			GROUP BY
				a.member_id
			ORDER BY
				b.last_name,b.first_name,b.middle_name
		";

		$query = $this->db->query($sql);
		return $query;
	}

	private function _get_transaction_logs_query_count($type,$member_id,$start_date,$end_date)
	{
		if($type=="IGPSM"){
			$where_transaction_codes = "transaction_code IN (100,101,102,103,104)";
		}else{
			$where_transaction_codes = "transaction_code = 105";
		}
		$where_member = "";
		if($member_id != 0) $where_member = "AND a.member_id = {$member_id}";
		$sql = "
			SELECT
			    COUNT(*) as cnt
			FROM
				tr_member_acct_credit_logs a
			LEFT JOIN
				cm_members b ON a.member_id = b.member_id
			WHERE
				{$where_transaction_codes} {$where_member}
			AND
				(a.insert_timestamp BETWEEN '{$start_date} 00:00:00' AND '{$end_date} 23:59:59')
			ORDER BY
				a.insert_timestamp DESC";

		$query = $this->db->query($sql);
		return $query->result();
	}

	private function _get_transaction_logs_query($type,$member_id,$start_date,$end_date,$limit,$offset)
	{
		if($type=="IGPSM"){
			$where_transaction_codes = "transaction_code IN (100,101,102,103,104)";
		}else{
			$where_transaction_codes = "transaction_code = 105";
		}
		$where_member = "";
		if($member_id != 0) $where_member = "AND a.member_id = {$member_id}";
		$sql = "
			SELECT
			    a.*,
				b.first_name,
				b.last_name,
				b.middle_name
			FROM
				tr_member_acct_credit_logs a
			LEFT JOIN
				cm_members b ON a.member_id = b.member_id
			WHERE
				{$where_transaction_codes} {$where_member}
			AND
				(a.insert_timestamp BETWEEN '{$start_date} 00:00:00' AND '{$end_date} 23:59:59')
			ORDER BY
				a.insert_timestamp DESC
			LIMIT {$limit} OFFSET {$offset}
			";

		$query = $this->db->query($sql);
		return $query;
	}

	private function access_log()
	{
		// access log
		$log_data = array(
			'user_id' => $this->user->user_id,
			'module_name' => 'ACCESS LOG',
			'table_name' => '',
			'action' => 'ACCESS',
			'details_before' => '',
			'details_after' => '',
			'remarks' => ''
		);
		$this->tracking_model->insert_logs('admin',$log_data);
	}
}