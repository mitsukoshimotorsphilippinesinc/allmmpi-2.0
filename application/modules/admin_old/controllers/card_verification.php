<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Card_verification extends Systems_Controller {

	function __construct()
	{
		parent::__construct();

		$this->load->model('cards_model');
	}

	public function index()
	{
		$this->template->view('card_verification/verification');
	}
	
	public function search()
	{
		$card_id = trim($this->input->post('card_id'));
		
		if(empty($card_id)){
			$this->return_json(0,"Control Code is required.");
			return;
		}
		
		$card = $this->cards_model->get_rs_card(array("card_id"=>$card_id));
		
		if(empty($card)){
			$card = $this->cards_model->get_sp_card(array("card_id"=>$card_id));
		}
		
		if(empty($card)){
			$this->return_json(0,"Control Code not found.");
			return;
		}
		
		$card = $card[0];
		
		$member_owner = "None";
		$_account = "None";
		$member_account = "";
		
		$owner = $this->members_model->get_member_by_id($card->member_id);
		if(!empty($owner)){
			$member_owner = strtoupper($owner->first_name) . " " . strtoupper($owner->last_name);
			
			// get account used from tr_member_acct_credit_points_logs
			$_select = "SELECT account_id FROM tr_member_acct_credit_points_logs WHERE member_id = '". $card->member_id ."' AND card_id = '". $card_id ."' AND account_id = from_account_id";
			$query = $this->db->query($_select);
			$_result = $query->result();
					
			if (!empty($_result)) {				
				$member_account = $_result[0];
				$_account = $member_account->account_id;
			}
			
		}
		
		$member_issued = "None";
		$issued = $this->members_model->get_member_by_id($card->issued_to);
		if(!empty($issued)){
			$member_issued = strtoupper($issued->first_name) . " " . strtoupper($issued->last_name);
		}
		
		$member_released = "None";
		$released = $this->members_model->get_member_by_id($card->released_to);
		if(!empty($released)){
			$member_released = strtoupper($released->first_name) . " " . strtoupper($released->last_name);
		}
		
		$card->other_info = new ArrayClass(array("member_owner"=>$member_owner, "member_issued"=>$member_issued, "member_released"=>$member_released, "member_account"=>$_account));
		
		$this->return_json(1,"Card found.",$card);
		return;
		
	}
	
	public function change_status() {
		$card_id = trim($this->input->post('card_id'));
		
		if(empty($card_id)){		
			$html = "Error! Control Code is required.";
			$title = "Change Card Status :: Error";		
			$this->return_json(0, "Control Code error.",array("html"=>$html, "title"=>$title));
			return;			
		}
		
		$card = $this->cards_model->get_rs_card(array("card_id"=>$card_id));
	
		$card_type = "";
		
		if (!(empty($card))) {
			$card_type = "RS";
		} else {
		//if(empty($card)){
			$card = $this->cards_model->get_sp_card(array("card_id"=>$card_id));
		}
		
		if (!(empty($card))) {
			$card_type = "SP";
		} else {			
			$html = "Error! Control Code not found.";
			$title = "Change Card Status :: Error";		
			$this->return_json(0, "Control Code error.",array("html"=>$html, "title"=>$title));
			return;			
		}
		
		$card = $card[0];
		$data = array(
			"card" => $card
		);
		
		$html = $this->load->view('/card_verification/option_modal', $data, TRUE);
		$title = "Change Card Status :: Confirm";		
		$this->return_json(1,"Card found.",array("html"=>$html, "card_type"=>$card_type, "status_from"=>$card->status, "title"=>$title));
		return;	
	}
	
	public function proceed_change_status() {
		$card_type = trim($this->input->post('card_type'));
		$card_id = trim($this->input->post('card_id'));
		$status_from = trim($this->input->post('status_from'));
		$status_to = trim($this->input->post('status_to'));
		$status_remarks = trim($this->input->post('status_remarks'));
		
		if ($card_type == "SP") {
			$card_details = $this->cards_model->get_sp_card(array("card_id"=>$card_id));
		
			// get current details 
			$details_before = array(
				'sp_card_id' => $card_details[0]->sp_card_id, 
				'details' => array('card_details' => $card_details)
			);			
		} else {
			// RS
			$card_details = $this->cards_model->get_rs_card(array("card_id"=>$card_id));
			
			// get current details 
			$details_before = array(
				'rs_card_id' => $card_details[0]->rs_card_id, 
				'details' => array('card_details' => $card_details)
			);			
		}
		
		$details_before = json_encode($details_before);
		
		$used_timestamp = "0000-00-00 00:00:00";
		$current_timestamp = date('Y-m-d H:i:s');
		
		if ($status_to == "USED") {
			$used_timestamp = $current_timestamp;
		}
		
		// update card status
		if ($card_type == "SP") {
			$data = array(
				'status' => $status_to,
				'member_id' => 0,
				'used_timestamp' => $used_timestamp,
				'activate_timestamp' => $current_timestamp
			);

			$this->cards_model->update_sp_card($data, array('card_id' => $card_id));
			
		} else {		
			$data = array(
				'status' => $status_to,
				'member_id' => 0,
				'account_id' => 0,
				'used_timestamp' =>  $used_timestamp,
				'activate_timestamp' => $current_timestamp
			);

			$this->cards_model->update_rs_card($data, array('card_id' => $card_id));
			
		}
		
		// admin logs
		$details_after = array('card_id' => $card_id, 'details' => $data);
		$details_after = json_encode($details_after);
		$update_card_details_data = array(
			'user_id' => $this->user->user_id,
			'module_name' => 'CARD VERIFICATION - CHANGE STATUS',
			'table_name' => 'is_' . strtolower($card_type) . '_cards',
			'action' => 'UPDATE',
			'details_before' => $details_before,
			'details_after' => $details_after,
			'remarks' => $status_remarks,
		);
		$this->tracking_model->insert_logs('admin', $update_card_details_data);
		
		// response html
		$html = "You have successfully changed the status of {$card_type} card {$card_id} from <strong>{$status_from}</strong> to <strong>{$status_to}</strong>.";
		$title = "Change Card Status :: Success";		
		$this->return_json(1,"Changed Card Status.",array("html"=>$html, "title"=>$title));
		return;	
		
	}
	
}