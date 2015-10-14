<?php


class Encode_sales extends Systems_Controller {

        function __construct() {
			parent::__construct();		
			
			$this->load->model('cards_model');
			$this->load->model('tracking_model');
			$this->load->model('members_model');
			$this->load->model('raffles_model');
		}
        
        function index() {
	
			// get first account of member			
			$where = "member_id = {$this->member->member_id}";
	
            $account_details = $this->members_model->get_member_accounts($where,null,"account_id");
	
			$this->template->account_details = $account_details;	
			$this->template->current_page = 'encoding';
			$this->template->view('admin/encode_sales/dashboard');
        }
		
		public function check_rs() {
			$_card_code = strtoupper(trim($this->input->post('_card_code')));
			$_card_id = trim($this->input->post('_card_id'));

			// get card series (first 2 chars of account_id)
			$sales_card_series = substr($_card_id, 0, 2);

			$card_type_details = $this->cards_model->get_card_types_view_by_series($sales_card_series);

			// find in is_rs_cards
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


		public function check_account_id() {
			$_account_id = trim($this->input->post('account_id'));
			
			// check if valid account id
			$account_details = $this->members_model->get_member_account_by_account_id($_account_id);
			
			if (count($account_details) == 0) {
								
				$html = "<p>
							<span>Account <strong>{$_account_id}</strong> not found.</span>
						</p>";	
			
				echo json_encode(array("status"=>0, "html"=>$html, "message" => "Account ID not found."));
				return;
				
			} else {
				
				//// check if account id is inactive
				//if ($account_details->account_status_id == 2) {
				//	$html = "<p>
				//			<span>Account <strong>{$_account_id}</strong> is INACTIVE.</span>
				//		</p>";	
			    //
				//	echo json_encode(array("status"=>0, "html"=>$html, "message" => "Account ID is INACTIVE."));
				//	return;
				//
				//} else {
				
					$html ="";
					$message = "Account ID is valid.";
					
					echo json_encode(array("status"=>1, "html"=>$html, "message" => $message, "account_status_id" => $account_details->account_status_id));
					return;
				//}

			}				
		}		
	
		public function get_account_points() {
			$_account_id = trim($this->input->post('_account_id'));
			
			//if (($_account_id == 0) || ($_account_id == "")) {
			//	$html = "<p>Please enter anaccount below. <br/> Note: Only Active Accounts are listed. </p>";
			//	echo json_encode(array("status"=>0, "html"=>$html));
			//	return;
			//}
			
			$account_details = $this->members_model->get_member_account_by_account_id($_account_id);
			
			if ((strlen($_account_id) != 10) && (trim($_account_id) != '')) {
				$html = "<p>Invalid Account ID.";
				echo json_encode(array("status"=>0, "html"=>$html));
				return;
			}
			
			if (empty($account_details)) {
				
				
				$data = array(		
					'account_details' => $account_details,
					'reset' => 1,
					'proper_name' => "",
					'proper_email' => ""
				);
				$html = $this->load->view('admin/encode_sales/account_details', $data, TRUE);
			
				$message = "<p>Account ID not found.</p>";				
				echo json_encode(array("status"=>0, "html"=>$html, "message"=>$message));
				return;
			}			
			
			// get member data
			
			$member_details = $this->members_model->get_member_by_id($account_details->member_id);
			
			$proper_name = $member_details->last_name . ", " . $member_details->first_name . " " . $member_details->middle_name;
			
			$data = array(		
				'account_details' => $account_details,
				'reset' => 0,
				'proper_name' => $proper_name,
				'proper_email' => $member_details->email
			);

			$html = $this->load->view('admin/encode_sales/account_details', $data, TRUE);

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
								<tr>
									<td style='width:120px;'><label><strong>Position</strong></label></td>
									<td><label class=''>{$_position}</label></td>		
								</tr>	
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
			
			// insert maintenance period counter to cm_member_accounts
			$member_account = $this->members_model->get_member_account_by_account_id($_account_id);
			$monthly_counter = $member_account->monthly_maintenance_ctr;			
			$annual_counter = $member_account->annual_maintenance_ctr;

			if ($_maintenance_period == "monthly") {
				$monthly_counter++;
				$data = array("monthly_maintenance_ctr"=>$monthly_counter);
			} else {
				// annual
				$annual_counter++;
				$data = array("annual_maintenance_ctr"=>$annual_counter);
			}
			
			// check if maintenance is enough to change the account status from inactve (2) to active (1)			
			// if ($member_account->account_status_id == 2 && $monthly_counter >= $this->settings->monthly_maintenance && $annual_counter >= $this->settings->annual_maintenance)
			if ($member_account->account_status_id == 2 && $monthly_counter >= $this->settings->monthly_maintenance)
			{
				// activate member account
				$data["account_status_id"] = 1; // ACTIVE = 1, INACTIVE = 2, COMPANY ACCOUNT = 3;
			}

			$this->members_model->update_member_accounts($data, "account_id = '{$_account_id}'");

			// update is_rs_cards status to USED
			$data = array(
				"status"=>"USED",
				"member_id"=>$_member_id,
				"account_id"=>$_account_id,
				"use_type"=>$_maintenance_period,
				"used_timestamp"=>date("Y-m-d H:i:s")
			);			
								
			$this->cards_model->update_rs_card($data, "card_id = '{$_card_id}'");
			

			// cedit points to account				
			$params = array(
				"card_id"=>$_card_id,
				"account_id"=>$_account_id,
				"type"=>"rs",
				"position"=>$_position,
				"points"=>$rs_points,
			);
			Modules::run('jobs/commissions/credit_points',$params);
			
			// credit unilevel commission
			$params = array("card_id"=>$_card_id,"account_id"=>$_account_id);
			Modules::run('jobs/commissions/credit_repeat_sales_commission',$params);

			//get details before update
			$rs_card_details = $this->cards_model->get_rs_card("card_id = {$_card_id}");			
			$details_before = array('id' => $_card_id, 'details' => array('status' => $rs_card_details[0]->status));
			$details_before = json_encode($details_before);
			
			//// update is_rs_cards status to USED
			//$data = array(
			//	"status"=>"USED",
			//	"account_id"=>$_account_id
			//	);			
			//$this->cards_model->update_rs_card($data, "card_id = '{$_card_id}'");
						
			$html = "<p>You have successfully encoded Repeat Sales Card ID: <strong>{$_card_id}</strong></p>";
			
			// process entry for raffle/promo
			$this->raffles_model->raffle_process('rs_encoding', 'default', $_card_id, $_account_id);
			
			//LOGGING FOR UPDATE RS CARDS
			$details_after = array('id' => $_card_id, 'details' => $data);
			$details_after = json_encode($details_after);
			$update_rs_card_logs = array(
				'member_id' => "",
				'module_name' => "ENCODING",
				'table_name' => "is_rs_cards",
				'action' => "UPDATE",
				'details_before' => $details_before,
				'details_after' => $details_after,
				'remarks' => ""
			);
			$this->tracking_model->insert_logs('member', $update_rs_card_logs);
			//END LOGGING
			
			// get member details
			$member_details = $this->members_model->get_member_by_id($member_account->member_id);
			
			$proper_name = $member_details->last_name . ", " . $member_details->first_name . " " . $member_details->middle_name;
			$proper_name = strtoupper($proper_name);
			
			$encoded_as = "MAINTENANCE";
			// override for series 74 - raffle RS
			if (substr($_card_id, 0, 2) == '74') {
				$encoded_as = "RAFFLE";
			} 
			
			// 20130205 email to member
			$params = array(
				"first_name"=>ucfirst($member_details->first_name),
				"proper_name"=>$proper_name,
				"control_code"=>$_card_id,
				"position"=>strtoupper($_position),
				"encoded_as"=>$encoded_as,
				"maintenance_period"=>$_maintenance_period,
				"current_timestamp"=>date("Y-m-d H:i:s")
			);

			$data = array(
				"email"=>$member_details->email,
				"type"=>"admin_encode_sales_email",
				"params"=>$params
			);

			//send email to user
			Modules::run('jobs/notifications/send_email',$data);
				
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
}