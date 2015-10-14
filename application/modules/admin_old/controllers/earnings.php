<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Earnings extends Systems_Controller {

	function __construct()
	{
		parent::__construct();

		$this->load->model('payment_model');
		$this->load->model('members_model');
		$this->load->model('tracking_model');

		// load pager library
		$this->load->library('pager');
		$this->set_navigation('earnings');
	}

	private $_months = array(
			'00' => '-',
			'01' => 'January',
			'02' => 'February',
			'03' => 'March',
			'04' => 'April',
			'05' => 'May',
			'06' => 'June',
			'07' => 'July',
			'08' => 'August',
			'09' => 'September',
			'10' => 'October',
			'11' => 'November',
			'12' => 'December'
	);

	public function index()
	{
		$this->view();
	}

	public function view()
	{
		
		$search_by = trim($this->input->get("search_option"));
		$search_text = trim($this->input->get("search_string"));
		$status = trim($this->input->get("status"));
		
		$search_url = "";
		
		$earnings = array();

		if (($search_text == "") || empty($search_text)) {
			$where = "";			
		} else {
			
			$search_url = "?status=" .$status. "&search_option=" . $search_by . "&search_string=" . urlencode($search_text);
			$period = explode("+",$search_text);
			
			$start_date = $period[0];
			$end_date = $period[1];
			
			$search_status = "'PENDING','PROCESSING','RELEASED','PROCESSED FOR PAYOUT','FORFEITED'";
			
			if(strcmp($status,"pending") == 0 || strcmp($status,"processing") == 0 || strcmp($status,"released") == 0 || strcmp($status,"processed_for_payout") == 0 || strcmp($status,"forfeited") == 0)
			{
				$search_status = "'".strtoupper(str_replace("_"," ",$status))."'";
			}
			
			// get all unprocessed 
			$where = array($start_date . ' 00:00:00', $end_date . ' 23:59:59');	
			
			$sql = "SELECT 
					`account_id`
					FROM `cm_member_earnings` 
					WHERE `status` IN ({$search_status}) AND `insert_timestamp` >= ? AND `insert_timestamp` <= ?
					GROUP BY `account_id`,`period`,`status`";
			
			$query = $this->db->query($sql, $where);
			$rows = $query->result();
			$query->free_result();

			
			// initialize pagination class
			// set pagination data
			$config = array(
					'pagination_url' => "/admin/earnings/index/",
					'total_items' => count($rows),
					'per_page' => 10,
					'uri_segment' => 4,
			);

			$this->pager->set_config($config);
			
			$sql = "SELECT 
					`member_id`,`period`,`account_id`,sum(`referral_bonus`) as 'referral_bonus', sum(`pairing_bonus_sp`) as 'pairing_bonus_sp',sum(`pairing_bonus_vp`) as 'pairing_bonus_vp',sum(`pairing_bonus_tp`) as 'pairing_bonus_tp',sum(`pairing_bonus_rs`) as 'pairing_bonus_rs',sum(`unilevel_commission`) as 'unilevel_commission',`status`,`insert_timestamp`
					FROM `cm_member_earnings` 
					WHERE `status` IN ({$search_status}) AND `insert_timestamp` >= ? AND `insert_timestamp` <= ?
					GROUP BY `account_id`,`period`,`status`
					ORDER BY insert_timestamp DESC
					LIMIT {$this->pager->offset},{$this->pager->per_page}";
			
			$query = $this->db->query($sql, $where);
			$earnings = $query->result();			
			$query->free_result();
			
			foreach($earnings as $e)
			{
				$member = $this->members_model->get_member_by_id($e->member_id);
				$e->first_name = $member->first_name;
				$e->last_name = $member->last_name;
				$e->amount = $e->referral_bonus + $e->pairing_bonus_sp + $e->pairing_bonus_vp + $e->pairing_bonus_tp + $e->pairing_bonus_rs + $e->unilevel_commission;
			}
		}

		$_days = array('00' => '-');
		for ($i = 1; $i <= 31; $i++)
		{	
			$day = str_pad($i, 2, "0", STR_PAD_LEFT);
			//$_days[$day] = $day;
			$_days[$i] = $i;
		}	

		$_years = array('0' => '-');
		$_today = getdate();
		for ($i = $_today['year']; $i >= 2007; $i--)
		{
			$_years[$i] = $i;
		}

		// search vars
		$this->template->status = $status;
		$this->template->search_by = $search_by;
		$this->template->search_text = $search_text;
		$this->template->search_url = $search_url;

		$this->template->months = $this->_months;
		$this->template->days = $_days;
		$this->template->years = $_years;
		$this->template->earnings = $earnings;
		$this->template->view('earnings/list');
	}
	
	
	// diego's process earning
	public function process_earnings()
	{
		$start_date = $this->input->post("start_date");
		$end_date = $this->input->post("end_date");		
		
		// get all pending member earnings for insertion to cm_member_payouts
		$where = array($start_date . ' 00:00:00', $end_date . ' 23:59:59');	
		
		$processing_sql = "UPDATE `cm_member_earnings` SET `status` = 'PROCESSING',`period` = '{$start_date} 00:00:00+{$end_date} 23:59:59' WHERE `status` = 'PENDING' AND `insert_timestamp` >= ? AND `insert_timestamp` <= ? AND (`referral_bonus` + `pairing_bonus_sp` + `pairing_bonus_vp` + `pairing_bonus_tp` + `pairing_bonus_rs` + `unilevel_commission` > 0)";
		$this->db->query($processing_sql, $where);

		$forfeiting_sql = "UPDATE `cm_member_earnings` SET `status` = 'FORFEITED' WHERE `status` = 'PROCESSING' AND `period` = '{$start_date} 00:00:00+{$end_date} 23:59:59' AND `account_id` IN (SELECT `account_id` FROM `cm_member_accounts` WHERE `account_status_id` = 2)";
		$this->db->query($forfeiting_sql);
		
		$drop_tmp_sql = "DROP TABLE IF EXISTS `tmp_member_earnings`";
		$this->db->query($drop_tmp_sql);
		
		$crete_tmp_sql = "CREATE TABLE IF NOT EXISTS `tmp_member_earnings` AS
						SELECT
						`member_id`,`account_id`,`referral_bonus`,`pairing_bonus_sp`,`pairing_bonus_vp`,`pairing_bonus_tp`,`pairing_bonus_rs`,`unilevel_commission`,'0.00' as `funds`,`status`,'' as `type`
						FROM `cm_member_earnings`
						WHERE `status` = 'PROCESSING' AND `period` = '{$start_date} 00:00:00+{$end_date} 23:59:59'";
		$this->db->query($crete_tmp_sql);
		
		$alter_tmp_sql = "ALTER TABLE `tmp_member_earnings` ADD PRIMARY KEY(`member_id`,`account_id`)";
		$this->db->query($alter_tmp_sql);

		$alter_tmp_sql = "ALTER TABLE `tmp_member_earnings` CHANGE `type` `type` VARCHAR( 25 ) NULL DEFAULT ''";
		$this->db->query($alter_tmp_sql);

		$alter_tmp_sql = "ALTER TABLE `tmp_member_earnings` CHANGE `funds` `funds` DECIMAL( 10,2 ) NULL DEFAULT '0.00'";
		$this->db->query($alter_tmp_sql);

		$update_tmp_sql = "UPDATE `tmp_member_earnings` SET `type` = (SELECT CASE WHEN `cm_members`.`is_auto_payout` = 1 THEN 'PAYCARD' ELSE 'FUNDS' END as `type` FROM `cm_members` WHERE `cm_members`.`member_id` = `tmp_member_earnings`.`member_id` )";
		$this->db->query($update_tmp_sql);
		
		$cd_amount_sql = "SELECT `tmp_member_earnings`.`member_id`,`tmp_member_earnings`.`account_id`,`cm_member_accounts`.`member_account_id`,`cm_member_accounts`.`account_type_id`,`cm_member_accounts`.`sponsor_id`,`cm_member_accounts`.`upline_id`,`tmp_member_earnings`.`referral_bonus`,`tmp_member_earnings`.`pairing_bonus_sp`,`tmp_member_earnings`.`pairing_bonus_vp`,`tmp_member_earnings`.`pairing_bonus_tp`,`tmp_member_earnings`.`pairing_bonus_rs`,`tmp_member_earnings`.`unilevel_commission`, `cm_member_accounts`.`cd_amount`,`cm_member_accounts`.`node_address` FROM `tmp_member_earnings` JOIN `cm_member_accounts` ON (`tmp_member_earnings`.`account_id` = `cm_member_accounts`.`account_id`) WHERE `cm_member_accounts`.`cd_amount` > 0";
		$query = $this->db->query($cd_amount_sql);
		$cd_accounts = $query->result();
		$query->free_result();
		
		$insert_sql = "INSERT INTO `cm_member_earnings` (`member_id`,`account_id`,`member_account_id`,`status`)
				(SELECT DISTINCT
					`member_id`,
					`account_id`,
					`member_account_id`,
					'PENDING'
				FROM
					`cm_member_earnings`
				WHERE
					(`cm_member_earnings`.`status` = 'FORFEITED' OR `cm_member_earnings`.`status` = 'PROCESSING') AND `cm_member_earnings`.`account_id` NOT IN (SELECT `account_id` FROM `cm_member_earnings` WHERE `cm_member_earnings`.`status` = 'PENDING'))";
		
		$this->db->query($insert_sql);
		
		foreach($cd_accounts as $cd)
		{
			$cd_amount = $cd->cd_amount;
			foreach($cd as $k => $v)
			{
				if(strcmp($k,'member_id') == 0 || strcmp($k,'account_id') == 0 || strcmp($k,'cd_amount') == 0 || strcmp($k,'account_type_id') == 0 || strcmp($k,'upline_id') == 0 || strcmp($k,'sponsor_id') == 0 || strcmp($k,'node_address') == 0 || strcmp($k,'member_account_id') == 0) continue;
				
				$cd_amount -= $v * (1 - $this->settings->witholding_tax);
				
				$data = array(
					"member_id" => $cd->member_id,
					"member_account_id" => $cd->member_account_id,
					"account_id" => $cd->account_id,
					"remarks" => "DEDUCTED FOR CD AMOUNT"
				);

				//logging
				if(strcmp($k,"referral_bonus")==0) $data["transaction_code"] = 200;
				elseif(strcmp($k,"pairing_bonus_sp")==0) $data["transaction_code"] = 201;
				elseif(strcmp($k,"pairing_bonus_vp")==0) $data["transaction_code"] = 202;
				elseif(strcmp($k,"pairing_bonus_tp")==0) $data["transaction_code"] = 203;
				elseif(strcmp($k,"pairing_bonus_rs")==0) $data["transaction_code"] = 204;
				elseif(strcmp($k,"unilevel_commission")==0) $data["transaction_code"] = 205;
				
				if($cd_amount > 0)
				{
					//cd amount is still greater than 0, earning type (k) was depleted
					$cd->$k = 0;

					$data = array(
						"member_id" => $cd->member_id,
						"member_account_id" => $cd->member_account_id,
						"account_id" => $cd->account_id,
						"amount" => $v,
						"remarks" => "DEDUCTED FOR CD AMOUNT"
					);
					$data["amount"] = $v;

					$this->tracking_model->insert_debit_logs($data);
				}
				else
				{
					$deducted = $cd->$k - abs($cd_amount)/(1 - $this->settings->witholding_tax);
					//cd amount paid, excess is returned to earning type (k)
					$cd->$k = abs($cd_amount)/(1 - $this->settings->witholding_tax);
					$cd_amount = 0;

					$data["amount"] = abs($deducted);

					$this->tracking_model->insert_debit_logs($data);

					Modules::run('jobs/commissions/credit_referral_bonus',array('card_id' => $cd->account_id,'account_id' => $cd->sponsor_id)); //sponsor_id
					
					$position = substr($cd->node_address,-1) == 1 ? "right" : "left";
					
					$data = array(
						'card_id'=>$cd->account_id,
						'account_id' => $cd->upline_id,
						'account_type_id' => $cd->account_type_id,
						'position' => $position,
						'points' => $this->settings->sp_points
					);
					
					Modules::run('jobs/commissions/credit_points',$data); //upline_id
					break;
				}
			}
			//update tmp_member_earnings
			$update_tmp_sql = "UPDATE `tmp_member_earnings` SET `referral_bonus` = {$cd->referral_bonus},`pairing_bonus_sp` = {$cd->pairing_bonus_sp},`pairing_bonus_vp` = {$cd->pairing_bonus_vp},`pairing_bonus_tp` = {$cd->pairing_bonus_tp},`pairing_bonus_rs` = {$cd->pairing_bonus_rs},`unilevel_commission` = {$cd->unilevel_commission} WHERE `account_id` = {$cd->account_id}";
			$this->db->query($update_tmp_sql);
			
			//update cd_amount
			$this->members_model->update_member_accounts(array("cd_amount" => $cd_amount),array("account_id" => $cd->account_id));
		}
		
		//GET FUNDS
		
		$get_funds_sql = "SELECT DISTINCT `tmp_member_earnings`.`member_id`,`cm_members`.`funds` FROM `cm_members` JOIN `tmp_member_earnings` ON (`cm_members`.`member_id` = `tmp_member_earnings`.`member_id`) WHERE `cm_members`.`is_auto_payout` = 1";
		$query = $this->db->query($get_funds_sql);
		$for_debit_funds = $query->result();
		$query->free_result();
		
		foreach($for_debit_funds as $debit)
		{
			$this->members_model->debit_funds($debit->member_id,$debit->funds,"FUNDS","PROCESSED FOR PAYOUT");
			
			$update_tmp_sql = "UPDATE `tmp_member_earnings` SET `funds` = {$debit->funds} WHERE `member_id` = {$debit->member_id}";
			$this->db->query($update_tmp_sql);
		}
		
		$payout_sql = "INSERT INTO `cm_member_payouts` (`member_id`,`gross_amount`,`tax`,`net_amount`,`status`,`type`) 
								(SELECT 
									`member_id`,
									sum(`referral_bonus`)+sum(`pairing_bonus_sp`)+sum(`pairing_bonus_vp`)+sum(`pairing_bonus_tp`)+sum(`pairing_bonus_rs`)+sum(`unilevel_commission`) + `funds`,
									(sum(`referral_bonus`)+sum(`pairing_bonus_sp`)+sum(`pairing_bonus_vp`)+sum(`pairing_bonus_tp`)+sum(`pairing_bonus_rs`)+sum(`unilevel_commission`) + `funds`) * (SELECT CASE WHEN `cm_members`.`is_auto_payout` = 1 THEN {$this->settings->witholding_tax} ELSE 0.00 END FROM `cm_members` WHERE (`cm_members`.`member_id` = `tmp_member_earnings`.`member_id`)),
									(sum(`referral_bonus`)+sum(`pairing_bonus_sp`)+sum(`pairing_bonus_vp`)+sum(`pairing_bonus_tp`)+sum(`pairing_bonus_rs`)+sum(`unilevel_commission`) + `funds`) * (1 - (SELECT CASE WHEN `cm_members`.`is_auto_payout` = 1 THEN {$this->settings->witholding_tax} ELSE 0.00 END FROM `cm_members` WHERE (`cm_members`.`member_id` = `tmp_member_earnings`.`member_id`))),
									'PENDING',
									`type`
								FROM
									`tmp_member_earnings`
								WHERE
									`member_id` != 0
								GROUP BY
									`member_id`)";
		
		$this->db->query($payout_sql);
		
		$processed_sql = "UPDATE `cm_member_earnings` SET `status` = 'PROCESSED FOR PAYOUT' WHERE `status` = 'PROCESSING' AND `period` = '{$start_date} 00:00:00+{$end_date} 23:59:59'";
		$this->db->query($processed_sql);
		
		
		
		$drop_tmp_sql = "DROP TABLE IF EXISTS `tmp_member_earnings`";
		$this->db->query($drop_tmp_sql);
		
		$this->return_json("ok","Earnings from {$start_date} to {$end_date} processed for payout.");
		return;
	}
	
	function print_earnings()
	{
		$this->load->library('PHPExcel');
        $this->load->library('PHPExcel/IOFactory');
		
		try {
			
			// adjusting memory limit to accomodate PHPExcel processing 
			set_time_limit(0); // eliminating the timeout
			ini_set('memory_limit', '512M');

	        $objPHPExcel = new PHPExcel();
	        $objPHPExcel->getProperties()->setTitle("Pending Member Earnings")->setDescription("none");
			$start_column_num = 3;

			$worksheet = $objPHPExcel->setActiveSheetIndex(0);

			$count_sql = "SELECT count(DISTINCT `account_id`) AS `cnt` FROM `cm_member_earnings`";
			$query = $this->db->query($count_sql);
			$member_earnings_count = $query->first_row();
			$query->free_result();

			$filename = "pending_member_earnings_as_of_" . date("Ymd") . ".xlsx";
			
			
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

			//set column names
			$worksheet->setCellValue('A1', "Pending Member Earnings as of " . date("Y-m-d"));
			$worksheet->setCellValue('A' . $start_column_num, 'Account ID');
			$worksheet->setCellValue('B' . $start_column_num, 'First Name');
			$worksheet->setCellValue('C' . $start_column_num, 'Last Name');
			$worksheet->setCellValue('D' . $start_column_num, 'To Paycard?');
			$worksheet->setCellValue('E' . $start_column_num, 'Member Funds');
			$worksheet->setCellValue('F' . $start_column_num, 'Account Status');
			$worksheet->setCellValue('G' . $start_column_num, 'Referral Bonus');
			$worksheet->setCellValue('H' . $start_column_num, 'Pairing Bonus SP');
			$worksheet->setCellValue('I' . $start_column_num, 'Pairing Bonus VP');
			$worksheet->setCellValue('J' . $start_column_num, 'Pairing Bonus TP');
			$worksheet->setCellValue('K' . $start_column_num, 'Pairing Bonus RS');
			$worksheet->setCellValue('L' . $start_column_num, 'Unilevel Commission');

			$row = 4;

			$allowed_rows = 5000;

			for($prow = 0;$prow < ceil($member_payout_count/$allowed_rows)+1;$prow++)
			{
				$member_payouts = $this->payment_model->get_member_payouts($where, array('rows' => $allowed_rows, 'offset' => $prow*$allowed_rows), 'transaction_id ASC',"status,transaction_id,member_id,gross_amount,tax,net_amount,type");

				foreach ($member_payouts as $mp)
				{

					// get member details
					$member = $this->members_model->get_member_by_id($mp->member_id);

					$worksheet->setCellValue('A'. $row, $mp->status);
					$worksheet->setCellValue('B'. $row, $mp->transaction_id);
					$worksheet->setCellValue('C'. $row, $mp->member_id);
					$worksheet->setCellValue('D'. $row, $member->first_name);
					$worksheet->setCellValue('E'. $row, $member->last_name);
					$worksheet->setCellValue('F'. $row, $member->email);
					$worksheet->setCellValue('G'. $row, "{$member->mobile_number}");
					$worksheet->setCellValue('H'. $row, $mp->gross_amount);
					$worksheet->setCellValue('I'. $row, $mp->tax);
					$worksheet->setCellValue('J'. $row, $mp->net_amount);
					$worksheet->setCellValue('K'. $row, 0.00);
					$worksheet->setCellValue('L'. $row, $mp->net_amount);
					
					// auto resize columns
					$worksheet->getColumnDimension('A')->setAutoSize(false);
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
					
					$row++;
				}
			}

			//$member_payouts = $this->payment_model->get_member_payouts($where, null, 'insert_timestamp DESC');

			

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
	
	
}