<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	  // if (!defined('CRON')) exit('This script is accessed thru CRON script only');

class Encoding extends MX_Controller {
	
	function __construct() {
		parent::__construct();	
		$this->load->model('users_model');
		$this->load->model('cards_model');
		$this->load->model('members_model');
		$this->load->model('tracking_model');

		$this->load->library('PHPExcel');
        $this->load->library('PHPExcel/IOFactory');
	}
	 
	public function index() {
		echo "Back-End Process:: Encoding";
	}

	public function generate_ess_start($user_id, $json_selected_series, $start_date, $end_date)
	{
		$selected_series = json_decode(urldecode($json_selected_series));

		// DROP tmp_encoded_sales		
		$sql = "DROP TABLE IF EXISTS tmp_encoded_sales;";
		$this->db->query($sql);
		
		// CREATE tmp_encoded_sales		
		$sql = "
		CREATE TABLE tmp_encoded_sales (
			member_id INT,
			account_id VARCHAR(64),
			node_address TEXT,
			type VARCHAR(2),
			card_count INT,
			KEY (member_id),
			KEY (account_id)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";		
		$this->db->query($sql);

		// GET LIST OF MEMBERS FOR ENCODED SALES SUMMARY
		$sql = "SELECT * FROM rf_member_list_for_encoded_sales_summary WHERE is_active = 1";
		$query = $this->db->query($sql);
		$result = $query->result();

		$sp_cards = $selected_series[1];	
		$rs_cards = $selected_series[0];
		$all_cards = array();

		// FOR SP CARDS
		foreach ($sp_cards as $t)
		{
			$all_cards[] = $t;
			print_r("processing {$t} series..\n");
			foreach ($result as $r)
			{
				$member_id = $r->member_id;
				$account_id = $r->account_id;
				$type = $t;
				$address = $r->node;
				
				$sql = "
					INSERT INTO tmp_encoded_sales (member_id,account_id,type,node_address,card_count)
					(SELECT {$member_id},'{$account_id}','{$type}','{$address}',count(card_id) FROM is_sp_cards WHERE date(used_timestamp) between '{$start_date}' and '{$end_date}' and substring(card_id,1,2)='{$type}' and card_id in (SELECT account_id FROM cm_member_accounts WHERE node_address LIKE '{$address}%'))";
				$this->db->query($sql);
			}
			print_r("done..\n");
		}
		
		// FOR RS CARDS		
		foreach($rs_cards as $t)
		{
			$all_cards[] = $t;
			print_r("processing {$t} series..\n");
			foreach ($result as $r)
			{
				$member_id = $r->member_id;
				$account_id = $r->account_id;
				$type = $t;
				$address = $r->node;
				
				$sql = "
					INSERT INTO tmp_encoded_sales (member_id,account_id,type,node_address,card_count)
					(SELECT {$member_id},'{$account_id}','{$type}','{$address}',count(card_id) FROM is_rs_cards WHERE date(used_timestamp) between '{$start_date}' and '{$end_date}' and substring(card_id,1,2)='{$type}' and account_id in (SELECT account_id FROM cm_member_accounts WHERE node_address LIKE '{$address}%'))";
				$this->db->query($sql);
			}
			print_r("done..\n");
		}

		// generate query
		print_r('generate query\n');
		$selections = array();
		foreach($all_cards as $card)
		{
			$selections[] = "(SELECT card_count FROM tmp_encoded_sales b WHERE b.type='{$card}' AND a.account_id = b.account_id ) AS 'series_{$card}'";
		}

		$selections = implode(", ", $selections);

		$report_sql = "
		SELECT
			a.member_id,
		    a.name,
		    a.node,
		    {$selections}
		FROM
		    rf_member_list_for_encoded_sales_summary a
		";

		$pretty_start_date = str_replace("-","",$start_date);
		$pretty_end_date = str_replace("-","",$end_date);

		$filename = "encoded_sales_summary_{$pretty_start_date}_to_{$pretty_end_date}.xlsx";

		// generating report file
		print_r("generating report file\n");
		try {
			$title = "Encoded Sales Summary {$start_date} to {$end_date}";

			$objPHPExcel = new PHPExcel();
	        $objPHPExcel->getProperties()->setTitle($title)->setDescription("none");

	        $title = "Encode Sales Summary";
			$worksheet = $objPHPExcel->setActiveSheetIndex(0);
			$objPHPExcel->getActiveSheet()->setTitle($title);			

			$title = "Encode Sales Summary for {$start_date} to {$end_date}";

			$start_column_num = 3;
			$starting_series_column = 'D';

			//set width of first column
			$worksheet->getColumnDimension('A')->setWidth(12.00);
			$worksheet->mergeCells('A1:E1');

			// set column header to bold
			$worksheet->getStyle('A1')->getFont()->setBold(true);
			$worksheet->getStyle('A' . $start_column_num)->getFont()->setBold(true);
			$worksheet->getStyle('B' . $start_column_num)->getFont()->setBold(true);
			$worksheet->getStyle('C' . $start_column_num)->getFont()->setBold(true);
			$series_column = $starting_series_column;
			foreach($all_cards as $card)
			{
				$worksheet->getStyle($series_column . $start_column_num)->getFont()->setBold(true);
				++$series_column;
			}
			$worksheet->getStyle($series_column . $start_column_num)->getFont()->setBold(true);

			//center column names
			$worksheet->getStyle('A' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$worksheet->getStyle('B' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$worksheet->getStyle('C' . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$series_column = $starting_series_column;
			foreach($all_cards as $card)
			{
				$worksheet->getStyle($series_column . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				++$series_column;
			}
			$worksheet->getStyle($series_column . $start_column_num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

			//set column names
			$worksheet->setCellValue('A1', $title);
			$worksheet->setCellValue('A' . $start_column_num, 'Member ID');
			$worksheet->setCellValue('B' . $start_column_num, 'Name');
			$worksheet->setCellValue('C' . $start_column_num, 'Node');
			$series_column = $starting_series_column;
			foreach($all_cards as $card)
			{
				$worksheet->setCellValue($series_column . $start_column_num, $card);
				++$series_column;
			}
			$worksheet->setCellValue($series_column . $start_column_num, 'Total');

			$query = $this->db->query($report_sql);
			$result = $query->result();
			
			// $members_payout = $this->payout_model->get_member_commissions_report(array(
			// 	'payout_id' => $payout_id,
			// 	'start_date' => $start_date,
			// 	'end_date' => $end_date
			// ));
			
			$row = 4;
			
			foreach ($result as $r)
			{	
				$worksheet->setCellValue('A'. $row, $r->member_id);
				$worksheet->setCellValue('B'. $row, $r->name);
				$worksheet->setCellValueExplicit('C'. $row, $r->node, PHPExcel_Cell_DataType::TYPE_STRING);
				$series_column = $starting_series_column;
				$last_col = $series_column;
				print_r($r);
				foreach($all_cards as $card)
				{
					$col_name = "series_" . $card;
					$worksheet->setCellValue($series_column . $row, $r->$col_name);
					$last_col = $series_column;
					++$series_column;
				}
				$worksheet->setCellValue($series_column . $row, '=SUM(' . $starting_series_column . $row . ':' . $last_col . $row . ')');

				// auto resize columns
				$worksheet->getColumnDimension('A')->setAutoSize(true);
				$worksheet->getColumnDimension('B')->setAutoSize(true);
				$worksheet->getColumnDimension('C')->setAutoSize(true);
				$series_column = $starting_series_column;
				foreach($all_cards as $card)
				{
					$worksheet->getColumnDimension($series_column)->setAutoSize(true);
					++$series_column;
				}
				$worksheet->getColumnDimension($series_column)->setAutoSize(true);
				
				$row++;
			}

			$objWriter = IOFactory::createWriter($objPHPExcel, 'Excel2007');
			if(file_exists(FCPATH . "assets/media/tmp/" . $filename)){
				unlink(FCPATH . "assets/media/tmp/" . $filename);
			}
			$objWriter->save(FCPATH . "assets/media/tmp/" . $filename);
		} catch (Exception $e) {
			//exit($e->getMessage());
		}
		print_r("file generated..\n");

		// email the file
		print_r("generating email\n");
		$base_url = 'http://cp.vital-c.net';
		$params = array(
			"excel_path" => $base_url . '/assets/media/tmp/' . $filename
		);

		$user_data = $this->users_model->get_user_by_id($user_id);

		$data = array(
			"email" => $user_data->email,
			"type" => "ess_email",
			"params" => $params
		);

		Modules::run('jobs/notifications/send_email',$data);
	}

	public function process($params=array())
	{

		$process_timestamp = $params['process_timestamp'];

		$member_id = $params['member_id'];
		$account_id = $params['account_id'];
		$maintenance_period = $params['maintenance_period'];
		$card_id = $params['card_id'];
		$position = $params['position'];

		$member_account = $this->members_model->get_member_account_by_account_id($account_id);

		if ((substr($card_id,0,2)!="74") || (substr($card_id,0,2)!="72"))
		{

			// 20130201 new maintenance card 73 series
			$ms_monthly_counter = $member_account->ms_monthly_maintenance_ctr;			
			$ms_annual_counter = $member_account->ms_annual_maintenance_ctr;
			
			$monthly_counter = $member_account->monthly_maintenance_ctr;
			$annual_counter = $member_account->annual_maintenance_ctr;
			//$monthly_counter = ($member_account->monthly_maintenance_ctr + $member_account->ms_monthly_maintenance_ctr);			
			//$annual_counter = $member_account->annual_maintenance_ctr + $member_account->ms_annual_maintenance_ctr;
		
			$main_monthly_ctr = $member_account->monthly_maintenance_ctr + $member_account->ms_monthly_maintenance_ctr;
			$main_annual_ctr = $member_account->annual_maintenance_ctr + $member_account->ms_annual_maintenance_ctr;
		
			if ($maintenance_period == "monthly") {
				// check series
				if(substr($card_id,0,2)=="73"){ // new maintenance series
					$ms_monthly_counter++;
					$data = array("ms_monthly_maintenance_ctr"=>$ms_monthly_counter);
				} else {			
					$monthly_counter++;
					$data = array("monthly_maintenance_ctr"=>$monthly_counter);
				}
				$main_monthly_ctr++;				
			} else {
				// annual
				// check series
				if(substr($card_id,0,2)=="73"){ // new maintenance series
					$ms_annual_counter++;
					$data = array("ms_annual_maintenance_ctr"=>$ms_annual_counter);
				} else {	
					$annual_counter++;
					$data = array("annual_maintenance_ctr"=>$annual_counter);
				}
				$main_annual_ctr++;
			}
			
			// check if maintenance is enough to change the account status from inactve (2) to active (1)			
			// if ($member_account->account_status_id == 2 && $monthly_counter >= $this->settings->monthly_maintenance && $annual_counter >= $this->settings->annual_maintenance)
			//if ($member_account->account_status_id == 2 && (($monthly_counter >= $this->settings->monthly_maintenance) || ($ms_monthly_counter >= $this->settings->ms_monthly_maintenance)))
			if (($member_account->account_status_id == 2) && ($main_monthly_ctr >= $this->settings->monthly_maintenance))
			
			{
				// activate member account
				$data["account_status_id"] = 1; // ACTIVE = 1, INACTIVE = 2, COMPANY ACCOUNT = 3;
			}

			$this->members_model->update_member_accounts($data, "account_id = '{$account_id}'");
			
		}

		$details_before = $this->cards_model->get_rs_card(array('card_id'=>$card_id));
		$details_before = json_encode($details_before[0]);

		$data = array(
			"status"=>"USED",
			"member_id"=>$member_id,
			"account_id"=>$account_id,
			"use_type"=>$maintenance_period,
			"used_timestamp"=>$process_timestamp
		);			
		$this->cards_model->update_rs_card($data, array('card_id'=>$card_id));

		$details_after = $this->cards_model->get_rs_card(array('card_id'=>$card_id));
		$details_after = json_encode($details_after[0]);

		// log
		$log_data = array(
			'member_id' => $member_id,
			'module_name' => 'RS CARD ENCODING',
			'table_name' => 'is_rs_cards',
			'action' => 'UPDATE',
			'details_before' => $details_before,
			'details_after' => $details_after,
			'remarks' => 'updated rs_card status'
		);
		$this->tracking_model->insert_logs('members',$log_data);

		/*
		if($maintenance_period == "raffle" || substr($card_id,0,2) != "74")
		{
			// insert raffle entry
			$sponsor_member_account = $this->members_model->get_member_account_by_account_id($member_account->sponsor_id);
			$upline_member_account = $this->members_model->get_member_account_by_account_id($member_account->upline_id);

			$this->load->model('raffles_model');
			$data = array(
				'raffle_id' => 2,
				'raffle_number' => $card_id,
				'reference_info' => 'RS CARD',
				'value' => $card_id,
				'is_active' => 1,
				'member_id' => $member_id,
				'account_id' => $account_id,
				'sponsor_member_id' => $sponsor_member_account->member_id,
				'sponsor_account_id' => $member_account->sponsor_id,
				'upline_member_id' => $upline_member_account->member_id,
				'upline_account_id' => $member_account->upline_id
			);
			$this->raffles_model->insert_raffle_entries($data);

			// log
			$log_data = array(
				'member_id' => $member_id,
				'module_name' => 'RAFFLE ENTRY',
				'table_name' => 'rm_raffle_entries',
				'action' => 'ADD',
				'details_before' => '',
				'details_after' => json_encode($data),
				'remarks' => 'added raffle entry for maintenance period = raffle'
			);
			$this->tracking_model->insert_logs('members',$log_data);

		}
		*/

		echo "SUCCESS";

		return;
	}

	public function process_modifiers($params=array())
	{
		$process_timestamp = $params['process_timestamp'];
		$member_id = $params['member_id'];
		$account_id = $params['account_id'];
		$maintenance_period = $params['maintenance_period'];
		$card_type = $params['type'];
		$card_id = $params['card_id'];

		// get modifiers [expected = RAFFLE, MAINTENANCE, PAIRING, CASCADE]
		$card_modifiers = $this->cards_model->get_modifiers_by_card_type($card_type);

		// check modifier condition
		$card_type_data = $this->cards_model->get_card_types(array('code' => $card_type));
		$card_type_id = $card_type_data[0]->card_type_id;
		$card_type_modifiers = $this->cards_model->get_card_modifiers(array('card_type_id' => $card_type_id));
		foreach($card_type_modifiers as $card_type_modifier) {
			if($card_type_modifier->condition != "NONE" && strtoupper($maintenance_period) != $card_type_modifier->condition) {
				$modifier_data = $this->cards_model->get_modifiers(array('modifier_id' => $card_type_modifier->modifier_id));
				unset($card_modifiers[array_search($modifier_data[0]->modifier_name, $card_modifiers)]);
			}
		}

		// // series 74 special case
		// if(substr($card_id,0,2)=="74"){
		// 	// remove igpsm(pairing and cascade) if raffle is present and vice versa
		// 	if($maintenance_period == "raffle")
		// 	{
		// 		unset($card_modifiers[array_search('PAIRING', $card_modifiers)]);
		// 		unset($card_modifiers[array_search('CASCADE', $card_modifiers)]);
		// 	}
		// 	elseif($maintenance_period == "igpsm")
		// 	{
		// 		unset($card_modifiers[array_search('RAFFLE', $card_modifiers)]);
		// 	}
		// }

		// // series 72 special case - 20140106
		// if(substr($card_id,0,2)=="72"){
		// 	// remove igpsm(pairing and cascade) if raffle is present and vice versa
		// 	if($maintenance_period == "raffle")
		// 	{
		// 		unset($card_modifiers[array_search('CASCADE', $card_modifiers)]);
		// 	}
		// 	elseif($maintenance_period == "igpsm")
		// 	{
		// 		unset($card_modifiers[array_search('RAFFLE', $card_modifiers)]);
		// 	}
		// }

		// run modifiers
		foreach($card_modifiers as $mods)
		{
			$method = "process_modifier_" . str_replace(" ", "_", strtolower($mods));
			if(method_exists($this, $method)) $this->$method($params);
		}

		// update card
		$details_before = $this->cards_model->get_rs_card(array('card_id'=>$card_id));
		$details_before = json_encode($details_before[0]);

		$data = array(
			"status"=>"USED",
			"member_id"=>$member_id,
			"account_id"=>$account_id,
			"use_type"=>$maintenance_period,
			"used_timestamp"=>$process_timestamp
		);			
		$this->cards_model->update_rs_card($data, array('card_id'=>$card_id));

		$details_after = $this->cards_model->get_rs_card(array('card_id'=>$card_id));
		$details_after = json_encode($details_after[0]);

		// log
		$log_data = array(
			'member_id' => $member_id,
			'module_name' => 'RS CARD ENCODING',
			'table_name' => 'is_rs_cards',
			'action' => 'UPDATE',
			'details_before' => $details_before,
			'details_after' => $details_after,
			'remarks' => 'updated rs_card status'
		);
		$this->tracking_model->insert_logs('members',$log_data);
	}

	public function process_modifier_maintenance($params=array())
	{
		echo "maintenance mod\n";
		$maintenance_period = $params['maintenance_period']; // [expected = annual, monthly]
		$account_id = $params['account_id'];
		$card_id = $params['card_id'];

		$member_account = $this->members_model->get_member_account_by_account_id($account_id);
		
		$ms_monthly_counter = $member_account->ms_monthly_maintenance_ctr;			
		$ms_annual_counter = $member_account->ms_annual_maintenance_ctr;
			
		$monthly_counter = $member_account->monthly_maintenance_ctr;
		$annual_counter = $member_account->annual_maintenance_ctr;
		
		$main_monthly_ctr = $member_account->monthly_maintenance_ctr + $member_account->ms_monthly_maintenance_ctr;
		$main_annual_ctr = $member_account->annual_maintenance_ctr + $member_account->ms_annual_maintenance_ctr;
		
		if ($maintenance_period == "monthly") {
			// check series
			if(substr($card_id,0,2)=="73"){ // new maintenance series
				$ms_monthly_counter++;
				$data = array("ms_monthly_maintenance_ctr"=>$ms_monthly_counter);
			} else {			
				$monthly_counter++;
				$data = array("monthly_maintenance_ctr"=>$monthly_counter);
			}
			$main_monthly_ctr++;				
		} else {
			// annual
			// check series
			if(substr($card_id,0,2)=="73"){ // new maintenance series
				$ms_annual_counter++;
				$data = array("ms_annual_maintenance_ctr"=>$ms_annual_counter);
			} else {	
				$annual_counter++;
				$data = array("annual_maintenance_ctr"=>$annual_counter);
			}
			$main_annual_ctr++;
		}
			
		// check if maintenance is enough to change the account status from inactve (2) to active (1)			
		if (($member_account->account_status_id == 2) && ($main_monthly_ctr >= $this->settings->monthly_maintenance))
		{
			// activate member account
			$data["account_status_id"] = 1; // ACTIVE = 1, INACTIVE = 2, COMPANY ACCOUNT = 3;
		}

		$this->members_model->update_member_accounts($data, "account_id = '{$account_id}'");

		echo "SUCCESS";
		return;
	}

	public function process_modifier_raffle($params=array())
	{
		echo "raffle mod\n";
		$s = "jobs/raffle/process";
		$output = Modules::run($s,$params);
	}

	public function process_modifier_pairing($params=array())
	{
		echo "pairing mod\n";
		$s = "jobs/commissions/credit_points";
		$output = Modules::run($s,$params);
	}

	public function process_modifier_cascade($params=array())
	{
		echo "cascade mod\n";
		$s = "jobs/commissions/credit_repeat_sales_commission";
		$output = Modules::run($s,$params);
	}
	
	public function process_modifier_unilevel($params=array())
	{
		echo "cascade mod\n";
		$s = "jobs/commissions/credit_repeat_sales_commission";
		$output = Modules::run($s,$params);
	}
	
}