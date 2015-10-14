<?php

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Encoding extends Site_Controller {

        function __construct() {
			parent::__construct();		
			
			$this->load->model('cards_model');
			$this->load->model('tracking_model');
			$this->load->model('members_model');
			$this->load->model('raffles_model');
			$this->load->model('settings_model');
		}
        
        function index() {
	
			// get first account of member			
			$where = "member_id = {$this->member->member_id}";
	
            $account_details = $this->members_model->get_member_accounts($where,null,"account_id");
	
			$this->template->account_details = $account_details;	
			$this->template->current_page = 'encoding';
			
			$disable_encoding = $this->settings->disable_rs_encoding;
			
			//var_dump($disable_encoding);
			
			if ($disable_encoding == 0) {			
				$this->template->view('members/encoding/dashboard');
			} else {
				$this->template->view('members/encoding/dashboard_on_going');
			}
        }
		
		public function check_rs() {
			$_card_code = strtoupper(trim($this->input->post('_card_code')));
			$_card_id = trim($this->input->post('_card_id'));

			$where = array(
				"card_id"=>$_card_id,
			);
			$card_details = $this->cards_model->get_rs_card($where, null, null);

			// check if correct card code
			if (empty($card_details)) {
				// not present in is_sp_cards
				$message = "Repeat Sales Card not found.";

				echo json_encode(array("status"=>0, "message"=>$message, "error_in"=>"card"));
				return;
			} 
				
			//card found
			// check if code is the same as in DB
			$card_details = $card_details[0];
			if($card_details->status == 'USED')
			{
				$message = "Card has already been USED.";

				echo json_encode(array("status"=>0, "message"=>$message, "error_in"=>"card"));
				return;
			}
			elseif($card_details->status == 'INACTIVE')
			{
				$message = "Card is INACTIVE.";

				echo json_encode(array("status"=>0, "message"=>$message, "error_in"=>"card"));
				return;
			}
			elseif($card_details->status == 'INVALID')
			{
				$message = "Card is INVALID.";

				echo json_encode(array("status"=>0, "message"=>$message, "error_in"=>"card"));
				return;
			}
			elseif($card_details->status == 'VOIDED')
			{
				$message = "Card is VOIDED.";
				
				echo json_encode(array("status" => 0, "message"=>$message, "error_in"=>"card"));
				return;
			}
			elseif($card_details->status == 'ACTIVE')
			{
				if ($card_details->card_code == $_card_code) {
					$message="Card is ACTIVE";
					echo json_encode(array("status"=>1, "message"=>$message));
					return;

				} else {
					// not present in is_rs_cards
					$message = "Card Code did not match.";

					echo json_encode(array("status"=>0, "message"=>$message, "error_in"=>"code"));
					return;
				}
			}
															
		}
		
		
		public function check_account() {
			$_account_id = trim($this->input->post('_account_id'));
			
			// check if valid account id
			$account_details = $this->members_model->get_member_account_by_account_id($_account_id);
			
			if (count($account_details) == 0) {
								
				$html = "<p>
							<span>Account <strong>{$_account_id}</strong> not found.</span>
						</p>";	
			
				echo json_encode(array("status"=>0, "html"=>$html));
				return;
				
			} else {
				if ($account_details->member_id != 0) {
					$member_details = $this->members_model->get_member_by_id($account_details->member_id);
					
					// get account status
					$account_status_details = $this->members_model->get_member_account_status_by_id($account_details->account_status_id);
					
					
					// get account type
					$account_type_details = $this->members_model->get_member_account_type_by_id($account_details->account_type_id);					
				
					$data = array(
						'account_details' => $account_details,
						'member_details' => $member_details,
						'account_status_details' => $account_status_details,
						'account_type_details' => $account_type_details
					);
		
					$html = $this->load->view('members/encoding/account_details', $data, TRUE);	
				
					echo json_encode(array("status"=>1, "html"=>$html));
					return;	
				} else {					
					$html = "<p>
								<span><Member ID for account <strong>{$_account_id}</strong> not found.</span>
							</p>";	

					echo json_encode(array("status"=>0, "html"=>$html));
					return;
				}
			}				
		}	
	
		public function get_account_points() {
			$_account_id = trim($this->input->post('_account_id'));
			
			if (($_account_id == 0) || ($_account_id == "")) {
				$html = "<p>Please select an account below. <br/> Note: Only Active Accounts are listed. </p>";
				echo json_encode(array("status"=>0, "html"=>$html));
				return;
			}
			
			$account_details = $this->members_model->get_member_account_by_account_id($_account_id);


			//// get account left and right rs points
			//$sales_cards = $this->cards_model->get_card_types(array(
			//	'is_package' => 0
			//), null, null, array('card_type_id'));
			//$sales_card_ids = array();
			//foreach($sales_cards as $item) $sales_card_ids[] = $item->card_type_id;
			//$sales_card_ids = implode(",", $sales_card_ids);
			//$where = "account_id = '" . $_account_id . "' AND card_type_id IN (" . $sales_card_ids . ")";
            //
			//$fields = "SUM(left_count) AS left_rs, SUM(right_count) AS right_rs, SUM(pair_count) AS pairs_rs";
			//$left_right_pair = $this->members_model->get_member_account_pairing($where, null, null, $fields);
			//$left_right_pair = $left_right_pair[0];
            //
			//$account_details->left_rs = $left_right_pair->left_rs;
			//$account_details->right_rs = $left_right_pair->right_rs;
			//$account_details->pairs_rs = $left_right_pair->pairs_rs;

			//get rs card id
			$rs_type_code = $this->cards_model->get_card_type_by_code('RS');

			$rs_card_points = $this->members_model->get_member_account_pairing(array(
				'member_id' => $account_details->member_id,
				'account_id' => $account_details->account_id,
				'card_type_id' => $rs_type_code->card_type_id
			), null, null, array('left_count', 'right_count', 'pair_count'));
			$rs_card_points = $rs_card_points[0];

			$account_details->left_rs = $rs_card_points->left_count;
			$account_details->right_rs = $rs_card_points->right_count;
			$account_details->pairs_rs = $rs_card_points->pair_count;

			$data = array(		
				'account_details' => $account_details
			);

			$html = $this->load->view('members/encoding/account_details', $data, TRUE);

			echo json_encode(array("status"=>"1","html"=>$html));
			return;
						
		}
	


    	public function check_card() {
			$_card_id = trim($this->input->post('card_id'));
			
			//check where to verify card id
			// get prefix
			$user_card_prefix = substr($_card_id, 0, 2);
			
			$where = "series_number = '{$user_card_prefix}'";
			
			$card_details = $this->cards_model->get_card_types_view($where, null,null);
			
			if (count($card_details) == 0) {
				// series not yet available on list
				$html = "<p>Card Series <strong>[{$user_card_prefix}]</strong> is not yet defined.</p>";
				echo json_encode(array("status"=>0,"html"=>$html));
			} else {
				$card_details = $card_details[0];
					
				if ($card_details->is_package == '0') {
					// rs card
					$card_master_details = $this->cards_model->get_rs_card_by_card_id($_card_id);					
				} else {
					// sp card
					$card_master_details = $this->cards_model->get_sp_card_by_card_id($_card_id);
				}
				
				if (count($card_master_details) == 0) {
					// no record found
					
					$html = "<p>
								<span><Card ID <strong>{$_card_id}</strong> not found.</span>
							</p>";	

					echo json_encode(array("status"=>0, "html"=>$html));
				
				} else {
					if ($card_master_details->member_id != 0) {
						$member_details = $this->members_model->get_member_by_id($card_master_details->member_id);					
					} else {
						$member_details = new ArrayClass(array(
							'member_id' => 'N/A',							
							'last_name' => 'N/A',
							'first_name' => 'N/A',
							'middle_name' => 'N/A',
							'email' => 'N/A',
							'mobile_number' => 'N/A'
						));
					}
					
					$data = array(
						'_card_id' => $_card_id,
						'member_details' => $member_details,
						'card_master_details' => $card_master_details
					);
		
					$html = $this->load->view('members/encoding/card_details', $data, TRUE);
					
					echo json_encode(array("status"=>1, "html"=>$html,"is_package"=>$card_details->is_package));
					
				}
			
				
			}				
			return;			
		}
		
		public function submit_check() {
			$_account_id = trim($this->input->post('_account_id'));
			$_card_id = trim($this->input->post('_card_id'));
			$_member_id_account = trim($this->input->post('_member_id_account'));
			$_member_id_card = trim($this->input->post('_member_id_card'));
			$_is_package = abs($this->input->post('_is_package'));
			
			$html_error = "<span>Error/s found while checking the required data for verification:</span><br/>";
			$hasError = 0;
			
			// check first if no empty values
			if (empty($_account_id) || ($_account_id == "") || ($_account_id == NULL)) {
				$html_error .= "<span>Empty Account ID.</span><br/>";
				$hasError = 1;			
			}
			
			if (empty($_card_id) || ($_card_id == "") || ($_card_id == NULL)) {
				$html_error .= "<span>Empty Card ID.</span><br/>";
				$hasError = 1;
			}
			
			if (empty($_member_id_account) || ($_member_id_account == "") || ($_member_id_account == NULL)) {
				$html_error .= "<span>Empty Member ID from Account Details.</span><br/>";
				$hasError = 1;
			}
			
			if (empty($_member_id_card) || ($_member_id_card == "") || ($_member_id_card == NULL)) {
				$html_error .= "<span>Empty Member ID from Card Details.</span><br/>";
				$hasError = 1;
			}
			
			if ($hasError) {				
				echo json_encode(array("status"=>0,'html'=>$html_error));
				return;
			}
			
			// check status of card
			if ($_is_package == 1) {
				// sp
				$master_details = $this->cards_model->get_sp_card_by_card_id($_card_id);			
			} else {
				// rs
				$master_details = $this->cards_model->get_rs_card_by_card_id($_card_id);
			}
			
			//if (($master_details->status == 'INACTIVE') || ($master_details->status == 'USED')) {
			if (($master_details->status == 'USED')) {
				$html_error= "Card is {$master_details->status}";
			
				echo json_encode(array("status"=>0,'html'=>$html_error));
				return;
			} else {
				
			
				/*// must be member_id_account = member_id_card
				if (!($_member_id_account == $_member_id_card)) {
					$html_error = "<span>The Card <strong>[{$_card_id}]</strong> you want to encode must belong to the selected member.</span><br/>";
					echo json_encode(array("status"=>0,'html'=>$html_error));
					return;
				} else {
					// proceed with points
					
				}*/
			
			
			$data = array(
				'is_package' => $_is_package,
				'master_details' => $master_details,
				'account_id' => $_account_id
			);
		
			$html = $this->load->view('members/encoding/insert_code_modal', $data, TRUE);

			echo json_encode(array("status"=>1,"html"=>$html));
			return;		
		
			}
		}
		
		public function confirm_credit() {
			$_account_id = trim($this->input->post('_account_id'));
			$_card_id = trim($this->input->post('_card_id'));				
			$_position = strtoupper(trim($this->input->post('_position')));
			$_maintenance_period = strtoupper(trim($this->input->post('_maintenance_period')));
			$_card_code = trim($this->input->post('_card_code'));

			$position = "";
			if(substr($_card_id, 0, 2) != "74")
			{
				$position = "
				<tr>
					<td style='width:120px;'><label><strong>Position</strong></label></td>
					<td><label class=''>{$_position}</label></td>		
				</tr>	
				";
			}
			
			//if (($_account_id == "") || ($_card_id == "") || ($_card_code == "")) {
			//	$html = "<p>Something's wrong with the fields needed to process the action.</p>";
			//	echo json_encode(array("status"=>0,"html"=>$html));
			//} else {
				$html = "<p><label>You are about to encode a Repeat Sales Card with the following details:</label>	
						<table class='table table-striped table-bordered'>
							<thead>
							</thead>
							<tbody>
								<tr>
									<td style='width:120px;'><label><strong>Card ID</strong></label></td>
									<td><label class=''>{$_card_id}</label></td>		
								</tr>
								<tr>
									<td style='width:120px;'><label><strong>Account ID</strong></label></td>
									<td><label class=''>{$_account_id}</label></td>		
								</tr>
								{$position}
								<tr>
									<td style='width:120px;'><label><strong>Maintenance</strong></label></td>
									<td><label class=''>{$_maintenance_period}</label></td>		
								</tr>					
							</tbody>
						</table>				
						<label>Do you want to proceed?</label>
					</p>";
					
				echo json_encode(array("status"=>1,"html"=>$html));		
			//}
			return;			
		}	
				
	public function credit_points() {
		// ENCODING ATTEMPT LOG
		$details_after = array(
			'member_id' => $this->input->post('_member_id'),
			'account_id' => $this->input->post('_account_id'),
			'card_id' => $this->input->post('_card_id'),
			'position' => $this->input->post('_position'),
			'maintenance_period' => $this->input->post('_maintenance_period'),
			'card_code' => $this->input->post('_card_code')
		);
		$log_data = array(
			'member_id' => $this->member->member_id,
			'module_name' => 'CARD ENCODING ATTEMPT',
			'action' => 'ADD',
			'details_after' => json_encode($details_after),
			'remarks' => 'CARD ENCODING ATTEMPT'
		);
		$this->tracking_model->insert_logs('members',$log_data);

		$_member_id = trim($this->input->post('_member_id'));
		$_account_id = trim($this->input->post('_account_id'));
		$_card_id = trim($this->input->post('_card_id'));				
		$_position = trim($this->input->post('_position'));
		$_maintenance_period = trim($this->input->post('_maintenance_period'));
		$_card_code = trim($this->input->post('_card_code'));
	
		// all cards are sales
		$card = $this->cards_model->get_rs_card_by_card_id($_card_id);			
		$rs_points = $this->settings->rs_points;
		
		if ($card->status=='USED')
		{
			echo json_encode(array("status"=>0,"html"=>"Repeat Sales Card has already been used."));
			return;				
		} 

		if ($card->status=='INACTIVE')
		{
			echo json_encode(array("status"=>0,"html"=>"Repeat Sales Card is inactive."));
			return;				
		} 

		if ($card->status=='INVALID')
		{
			echo json_encode(array("status"=>0,"html"=>"Repeat Sales Card is invalid."));
			return;				
		}
	
		// check card code if the same
		if ($card->card_code != strtoupper($_card_code)) {
			echo json_encode(array("status"=>0,"html"=>"Sorry, the Card Code you entered did not match. Please try again."));
			return;
		}

		$job_type_id = 1; // encode_rs
		// if(in_array(substr($_card_id,0,2), array(73,74,75,76))) {
		// // if (substr($_card_id,0,2)=="74") { 
		// 	$job_type_id = 8; // encode_rs_only
		// 	if (strtolower($_maintenance_period) == 'raffle') {
		// 		$job_type_id = 7; // encode_rs_raffle  
		// 	}
		// }
		$card = $this->cards_model->get_rs_card(array(
			'card_id' => $_card_id
		));
		$card = $card[0];

		$card_type = $this->cards_model->get_card_types(array(
			'code' => $card->type
		));
		$card_type = $card_type[0];

		$this->load->model('jobs_model');
		// $params = array(
		// 		'member_id' => $_member_id,
		// 		'account_id' => $_account_id,
		// 		'card_id' => $_card_id,
		// 		'position' => $_position,
		// 		'maintenance_period' => $_maintenance_period,
		// 		'card_code' => $_card_code,
		// 		'type' => "rs",
		// 		'points' => $this->settings->rs_points
		// 	);
		$params = array(
				'member_id' => $_member_id,
				'account_id' => $_account_id,
				'card_id' => $_card_id,
				'position' => $_position,
				'maintenance_period' => $_maintenance_period,
				'card_code' => $_card_code,
				'type' => $card->type,
				'points' => $card_type->points
			);
		$job_data = array(
				'job_type_id' => $job_type_id, // encode_rs
				'parameters' => json_encode($params)
			);
		$this->jobs_model->insert_job($job_data);

		$job_id = $this->jobs_model->insert_id();
		
		job_exec($job_id);

		$html = "<p>Your encoded Repeat Sales Card ID: <strong>{$_card_id}</strong> is now being processed.</p>";
						
		echo json_encode(array("status"=>1,"html"=>$html));
		return;			
	}
		
	function rs_promo_register()
	{
		$reg_num = $this->input->get_post('reg_num');
		$account_id = $this->input->get_post('account_id');
		
		// check if reg num is already claimed
		$raffle_entry = $this->raffles_model->get_raffle_entry_by_raffle_number($reg_num);
		if (empty($raffle_entry))
		{
			$this->return_json("error","Invalid registration number.");
			return;
		}
		
		if($raffle_entry->is_valid == 0)
		{
			$this->return_json("error", "The registration number <strong>".strtoupper($reg_num)."</strong> is invalid.");
			return;
		}
		
		if ($raffle_entry->is_active == 1)
		{
			$this->return_json("error","The registration number <strong>".strtoupper($reg_num)."</strong> is already registered by someone else.");
			return;
		}
		
		
		$member_account = $this->members_model->get_member_account_by_account_id($account_id);
		if (empty($member_account))
		{
			$this->return_json("error","Invalid Account ID.");
			return;
		}
		
		$this->raffles_model->raffle_process('raffle_num_encoding', 'default', $reg_num, $account_id);
		
		$this->return_json("ok","Registration number <strong>".strtoupper($reg_num)."</strong> successfully registered.");
		return;				
	}
	
	function check_limit_73() {
		$_card_id = trim($this->input->post('_card_id'));
		$_maintenance_period = trim($this->input->post('_maintenance_period'));
		$_account_id = trim($this->input->post('_account_id'));
		
		if (substr($_card_id, 0,2)  == "73") {
		
			// get account details
			$account_details = $this->members_model->get_member_account_by_account_id($_account_id);
			
			if ($_maintenance_period == "monthly") {
				if ($account_details->ms_monthly_maintenance_ctr >= $this->settings->monthly_maintenance) {
					// reached limit
					$this->return_json("1","Maximum number of maintenance card reached. You are only allowed to encode two (2) Series 73 Monthly Maintenance cards.");
					return;				
				}
			} else {
				if ($account_details->ms_annual_maintenance_ctr >= $this->settings->annual_maintenance) {
					// reached limit
					$this->return_json("1","Maximum number of maintenance card reached. You are only allowed to encode four (4) Series 73 Annual Maintenance cards.");
					return;				
				}
			}
		}
		
		$this->return_json("0","Not Reached");
		return;				
		
		
	}
}