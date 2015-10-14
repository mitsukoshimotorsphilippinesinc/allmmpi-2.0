<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	 // if (!defined('CRON')) exit('This script is accessed thru CRON script only');

class Dpr extends  Base_Controller
{
	
	function __construct() 
	{
  		parent::__construct();
  		$this->load->model('dpr_model'); 
  		$this->load->model('setting_model');
	}
	
	public function index() 
	{
		echo "Start Booklet Processes...";
	}

	public function generate_booklet($params=array()) {
		$request_detail_id = $params['request_detail_id'];
		$id_number = $params['id_number'];
		$request_code = $params['request_code'];		

		var_dump($request_details_id);

		//$request_detail_id = 83;
		//$id_number = 1;
		//$request_code = "TRD082615-063";		

		// get request details
		$request_detail_details = $this->dpr_model->get_request_detail_by_id($request_detail_id);

		$padded_form_id = str_pad($request_detail_details->form_type_id, 2, "0", STR_PAD_LEFT);
		
		if (($request_detail_details->branch_id == NULL) || ($request_detail_details->branch_id == 0) || (trim($request_detail_details->branch_id) == "")) {
			$branch_id = 0;	
		} else {
			$branch_id = $request_detail_details->branch_id;	
		}
		
		$padded_branch_id = str_pad($branch_id, 3, "0", STR_PAD_LEFT);

		$booklet_number_code = $padded_form_id . $padded_branch_id;
		$series_per_booklet = $this->setting->series_per_booklet;
		$starting_series = abs($request_detail_details->last_serial_number);

		for ($i = 1; $i <= $request_detail_details->quantity; $i++) {
			
			$ending_series = $starting_series + $series_per_booklet;	
			$booklet_number = $booklet_number_code . "-" . $i;

			$data_insert = array(
					'request_detail_id' => $request_detail_id,
					'branch_id' => $branch_id,
					'booklet_code' => $booklet_number_code,
					'booklet_series' => $i,		
					'booklet_number' => $booklet_number,
					'series_from' => $starting_series + 1, 
					'series_to' => $ending_series, 
					'receive_timestamp' => $request_detail_details->date_delivered, 
					'receive_remarks' => $request_detail_details->remarks, 
				);

			$this->dpr_model->insert_booklet($data_insert);

			$starting_series = $ending_series;
		}

	}































	public function card_generation($params=array())
	{
		$is_package = $params['is_package'];
		$starting_index = $params['starting_index'];
		$qty = $params['qty'];
		$card_type_code = $params['card_type_code'];
		$series_number = $params['series_number'];
		$card_type_id = $params['card_type_id'];
		$series_id = $params['series_id'];

		// generate actual cards
		$insert_method = ((boolean)$is_package)?"insert_sp_card":"insert_rs_card";

		// consider multi processing instead :)
		for($i = $starting_index; $i < ($starting_index+$qty); $i++)
		{
			$cnt = '';
			for($j = strlen($i); $j < 10 ; $j++)	
				$cnt .= '0';
			
			// 20140109: changed default status from ACTIVE to INACTIVE
			$this->cards_model->$insert_method(array(
				'card_id' => $cnt.$i,
				'card_code' => $this->generate_card_code(),
				'status' => 'INACTIVE',
				'type' => $card_type_code
			));
		}


		echo "SUCCESS";
		return;
	}

	private function generate_card_code()
	{
		$len = 10;
		$code = "";
		for($i = 0; $i < $len; $i++) $code .= rand(0, 9);
		return $code;
	}

	public function create_series($params=array())
	{
		$quantity = abs($params['quantity']);
		$card_series_id = abs($params['card_series_id']);
		$released_to = trim($params['released_to']);
		$rn_prefix = trim($params['rn_prefix']);

		$last_card_number = $this->_get_last_card_number($card_series_id);

		$card_series_details = $this->cards_model->get_card_series_by_id($card_series_id);
		$series_number = $card_series_details->series_number;

		$card_type_details = $this->cards_model->get_card_type_by_id($card_series_details->card_type_id);			

		// get the record from rs_master with the same series
		$where = "card_id LIKE '{$card_series_details->series_number}%'";
		
		if ($card_type_details->is_package == 0) {		
			$master_table = $this->cards_model->get_rs_card($where, null, "card_id DESC");
		} else {
			$master_table = $this->cards_model->get_sp_card($where, null, "card_id DESC");
		}
		
		// get the card_id number without the prefix		
		if (count($master_table) == 0) {
			$starting_id = 1;
		} else {
			$card_num_wo_series = substr($last_card_number, 2);					
			$starting_card_number = $last_card_number + 1;
			$starting_id = abs($card_num_wo_series) + 1;			
		}
		
		$i = 1;
		
		$released_timestamp = date("Y-m-d H:i:s");
		
		while ($i <= $quantity) {			
			
			// create card_code		
			$_card_id = $card_series_details->series_number . str_pad($starting_id, 8, "0", STR_PAD_LEFT);	
						
			$data = array(
				'card_id' => $_card_id,
				'status' => 'ACTIVE',
				'user_id' => 1,
				'type' => 'RS',
				'released_to' => $released_to,
				'released_timestamp' => $released_timestamp	
			);

			if ($card_type_details->is_package == 0) {
				$this->cards_model->insert_rs_card($data);
			} else {
				$this->cards_model->insert_sp_card($data);
			}

			// get id and timestamp
			$insert_id = $this->cards_model->insert_id();
			$insert_timestamp = date("Y-m-d H:i:s");

			//logging of action
			$details_after = array('id' => $insert_id, 'details' => $data);
			$details_after = json_encode($details_after);
			
			$module_name = "";
			$table_name = "";
			
			if ($card_type_details->is_package == 0) {
				$module_name = "RS CARDS";
				$table_name = "is_rs_cards";
			} else {
				$module_name = "SP CARDS";
				$table_name = "is_sp_cards";
			}
			
			$add_card_log_data = array(
				'user_id' => $this->user->user_id,
				'module_name' => $module_name,
				'table_name' => $table_name,
				'action' => 'ADD',
				'details_after' => $details_after,
				'remarks' => "",
			);

			$this->tracking_model->insert_logs('admin', $add_card_log_data);
			
			//// generate confirmation_code
			if (($rn_prefix == "") || ($rn_prefix == NULL)) {
				$generated_card_code = strtoupper(substr(md5($card_series_details->series_number . $insert_id . $insert_timestamp), 1, 10));
			} else {
				$generated_card_code = strtoupper($rn_prefix . substr(md5($card_series_details->series_number . $insert_id . $insert_timestamp), 1, 7));
			}
			
			// insert the card_code
			$data = array(
				'card_code' => $generated_card_code				
			);
			
			if ($card_type_details->is_package == 0) {
				$this->cards_model->update_rs_card($data, array('rs_card_id' => $insert_id));
			} else {
				$this->cards_model->update_sp_card($data, array('sp_card_id' => $insert_id));
			}
		
			// --------------------------------------------
			// TO-DO: send information via email and/or sms
			// --------------------------------------------
			
			$details_before = array('id' => $insert_id, 'details' => array('card_code' => NULL));
			$details_before = json_encode($details_before);
			
			//logging of action
			$details_after = array('id' => $insert_id, 'details' => $data);
			$details_after = json_encode($details_after);
			
			$module_name = "";
			$table_name = "";
			
			if ($card_type_details->is_package == 0) {
				$module_name = "RS CARDS";
				$table_name = "is_rs_cards";
			} else {
				$module_name = "SP CARDS";
				$table_name = "is_sp_cards";
			}
			
			$update_card_log_data = array(
				'user_id' => $this->user->user_id,
				'module_name' => $module_name,
				'table_name' => $table_name,
				'action' => 'UPDATE',
				'details_before' => $details_before,
				'details_after' => $details_after,
				'remarks' => "",
			);

			$this->tracking_model->insert_logs('admin', $update_card_log_data);
		
			if ($i == 1) {
				$start_id = $_card_id;
			}
		
			if ($i == $quantity) {
				$end_id = $_card_id;
			}
			
			$starting_id++;
			$i++;
		}
		
		$type_details = $card_type_details->code . " - " .  $card_type_details->name;
		$generated_ids_description = $start_id ." - ". $end_id;

		echo "SUCCESS";
		return;
		
	}

	private function _get_last_card_number($card_series_id=0) {
		$last_card_count = "";
		// get last card number from series	
		$card_series_data = $this->cards_model->get_card_series_by_id($card_series_id);
		
		$card_type_details = $this->cards_model->get_card_type_by_id($card_series_data->card_type_id);
				
		// get the last record from master table with the same series
		$where = "card_id LIKE '{$card_series_data->series_number}%'";

		if ($card_type_details->is_package == 0) {		
			$master_table = $this->cards_model->get_rs_card($where, null, "card_id DESC");
		} else {
			$master_table = $this->cards_model->get_sp_card($where, null, "card_id DESC");
		}
		
		if(count($master_table) > 0) {
			$last_card_count = $master_table[0]->card_id;
		}
		
		return $last_card_count;
	}

	public function give_mpv_72($tbl = "")
	{
		// tmp_members_used_mpv_20140129_1537

		/* -- TABLE TEMPLATE
		CREATE TABLE tmp_members_used_mpv_20140129_1537
		SELECT
			a.member_id,
			a.voucher_code,
			b.transaction_id
		FROM
			cm_member_account_vouchers a
		LEFT JOIN
			is_payment_transaction_products b ON a.voucher_code = b.voucher_code
		WHERE
			a.status = 'REDEEMED'
		AND
			a.voucher_type_id = 2;
		*/

		$mpv_rs_series_number = '72';
		$mpv_rs_card_type = 'RS72';

		if($tbl == "") return;
		$sql = "SELECT * FROM {$tbl}";
		$query = $this->db->query($sql);
		$members = $query->result();

		// group vouchers to distinct member_id (so we will send a single email for multiple cards)
		$tmp = array();
		foreach($members as $mem){ $tmp[$mem->member_id][] = $mem; }
		$members = $tmp;

		// get last count
		$sql = "SELECT COUNT(1) AS cnt FROM is_rs_cards WHERE LEFT(card_id, 2) = '{$mpv_rs_series_number}' ";
		$query = $this->db->query($sql);
		$rs_count = $query->result();
		$rs_count = $rs_count->cnt;
		
		// give 72 series
		foreach($members as $mem_id => $mem_data)
		{
			print_r("\nnew member\n");
			// get member email
			$member_data = $this->members_model->get_member_by_id($mem_id);

			// group by transaction_id
			$trans = array();
			foreach($mem_data as $tran)
			{
				$tmp = new stdClass;
				$tmp->voucher_code = $tran->voucher_code;
				$trans[$tran->transaction_id][] = $tmp;
			}

			// generate cards
			foreach($trans as $tran_id => $tran_obj)
			{
				$card_ids = array();
				foreach($tran_obj as $ent)
				{
					$rs_count++;
					$card_id = $mpv_rs_series_number . str_pad($rs_count, 8, "0", STR_PAD_LEFT);
					$card_code = $this->generate_card_code();

					// add to is_rs_cards
					$this->cards_model->insert_rs_card(array(
						'card_id' => $card_id,
						'card_code' => $card_code,
						'status' => 'ACTIVE',
						'type' => $mpv_rs_card_type
					));

					$card_ids[] = $card_id;
					$ent->card_id = $card_id;
					$ent->card_code = $card_code;
				}

				// add to tr_cards_logging
				$this->logs_model->insert_cards_log($tran_id, array(
					"SP" => array(),
					"RS" => $card_ids,
					"RF" => array(),
					"METROBANK" => array()
				));
			}

			// email cards
			$entries = "";
			foreach($trans as $tran_id => $tran_obj)
			{
				foreach($tran_obj as $ent)
				{
					$entries .= "
					<tr>
						<td>" . $ent->voucher_code . "</td>
						<td>" . $ent->card_id . "</td>
						<td>" . $ent->card_code . "</td>
					</tr>
					";
				}
			}

			$detail_msg = "
			<table>
				<thead>
					<tr>
						<th>MPV Encoded</th>
						<th>Control Code</th>
						<th>RSRN</th>
					</tr>
				</thead>
				<tbody>" . $entries . "</tbody>
			</table>
			";

			$params = array(
				"detail_msg" => $detail_msg
			);

			// $member_data->email
			$email_data = array(
				"email" => $member_data->email,
				"type" => "mpv_rs_card",
				"params" => $params
			);

			//print_r($email_data);

			Modules::run('jobs/notifications/send_email',$email_data);

			print_r("\n-----\n\n");
		}

		//print_r($members);

		return;
	}

	// one time use only - DO NOT RERUN
	public function email_72_corrections()
	{
		$sql = "SELECT * FROM tmp_rs_72_emailer_corrections";
		$query = $this->db->query($sql);
		$corrections = $query->result();

		$tmp = array();
		foreach($corrections as $correction) $tmp[$correction->member_id][] = $correction;
		$corrections = $tmp;

		// email cards
		foreach($corrections as $member_id => $correction_arr)
		{
			$email = $correction_arr[0]->email;
			$entries = "";
			foreach($correction_arr as $correction)
			{
				$entries .= "
				<tr>
					<td>" . $correction->card_id . "</td>
					<td>" . $correction->rep_card_id . "</td>
					<td>" . $correction->card_code . "</td>
				</tr>
				";
			}

			$detail_msg = "
			<table border='1'>
				<thead>
					<tr>
						<th>Old Card Numbers</th>
						<th>Corrected Card Numbers</th>
						<th>RSRN</th>
					</tr>
				</thead>
				<tbody>" . $entries . "</tbody>
			</table>
			";

			$params = array(
				"detail_msg" => $detail_msg
			);

			// $member_data->email
			$email_data = array(
				"email" => $email,
				"type" => "mpv_rs_card_corrections",
				"params" => $params
			);
			print_r($email_data);
			print_r("\n");

			//print_r($email_data);

			Modules::run('jobs/notifications/send_email',$email_data);
		}
	}
}