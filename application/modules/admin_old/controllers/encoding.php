<?php

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Encoding extends Systems_Controller {

        function __construct() {
			parent::__construct();		
			
			$this->load->model('cards_model');
			// load pager library
			$this->load->library('pager');
			$this->set_navigation('encoding');	
		}
        
        function index() {
            $data = array();
            //$this->load_view(array('view' => 'voucher_redemption/voucher', 'data' => $data));

			$this->template->view('encoding/dashboard');
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
		
					$html = $this->load->view('admin/encoding/account_details', $data, TRUE);	
				
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
		
					$html = $this->load->view('admin/encoding/card_details', $data, TRUE);
					
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
		
			$html = $this->load->view('admin/encoding/insert_code_modal', $data, TRUE);

			echo json_encode(array("status"=>1,"html"=>$html));
			return;		
		
			}
		}
		
		public function credit_points() {
			$_account_id = trim($this->input->post('_account_id'));
			$_card_id = trim($this->input->post('_card_id'));
			$_member_id_account = trim($this->input->post('_member_id_account'));
			$_member_id_card = trim($this->input->post('_member_id_card'));
			$_is_package = abs($this->input->post('_is_package'));
			$_position = trim($this->input->post('_position'));
			$_card_code = trim($this->input->post('_card_code'));
			
			// check if correct card code
			if ($_is_package == 0) {
				// rs
				$card_master_details = $this->cards_model->get_rs_card_by_card_id($_card_id);
				$card_type = "rs";
				// get points from rf_settings
				$points = $this->settings->rs_points;
			} else {
				// sp
				$card_master_details = $this->cards_model->get_sp_card_by_card_id($_card_id);
				$card_type = "sp";
				$points = $this->settings->sp_points;
			}
			
			if ($card_master_details->card_code == strtoupper($_card_code)) {
				
				// credit points - cards_model
				$data_points = $this->cards_model->credit_points($_account_id, $card_type, $_position, $points, "test");
				
				/*// parse $data points and check for new pairs
				$account_points_pair_data = explode("|", $data_points);				
				$appd_left_points = $account_points_pair_data[0];
				$appd_right_points = $account_points_pair_data[1];
				$appd_pairs = $account_points_pair_data[2];
				$appd_type = $account_points_pair_data[3];*/
				
				//$temp = $this->cards_model->credit_pairs($data_points);
				
				
				
				
				
				$html = "";
				
				
				
				
				
				
				echo json_encode(array("status"=>1,"html"=>$html));
			} else {
				$html = "<p>
							<span>Sorry, the Card Code in our record and the one you entered does not match. Please try again.</span>
						</p>";
				echo json_encode(array("status"=>0,"html"=>$html));
			}
			
			return;
			
		}
		
		public function check_card_encoding() {
			$_card_codes = trim($this->input->post('_card_codes'));
			
			// remove all spaces
			$_card_code_list = str_replace(" ", "", $_card_codes);
			
			// check what card type is the pattern
			
			var_dump($_card_code_list);
			
			$syntax_error = array();
			$syntax_error["card_results"] = $this->_check_pattern($_card_code_list);
			
		}
		
		private function _check_pattern($card_list = "")
		{
			$error = array();

		if(!preg_match("/^$|^([0-9]{10}(-[0-9]{10})?(,[0-9]{10}(-[0-9]{10})?)*)$/", $card_list)) {
			array_push($error,(object) array("card_id" => "","error"=>"There was an error in your syntax. Numbers must be numeric and 10 characters long; and must have no other special characters other than dashes(-) and commas(,)."));
		}
		
			return $error;
		}
}

?>