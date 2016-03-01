<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Verification extends Base_Controller {
   
    function __construct() {
		parent::__construct();
     	
		$this->load->model("members_model");
		$this->load->model("tracking_model");
	}
    
    public function index() {

        $_type = trim($this->uri->segment(4));
    	$_code = trim($this->uri->segment(5));
		//var_dump($_type . $_code);		

        if ($_type == 'email') {
            $validation_details = $this->members_model->get_member_verification_by_email_code($_code);
        } else if ($_type == 'rfid') {
            $validation_details = $this->members_model->get_member_verification_by_rf_id_code($_code);
        } else if ($_type == 'paycard') {
            $validation_details = $this->members_model->get_member_verification_by_paycard_code($_code);
        } else {
            $validation_details = $this->members_model->get_member_verification_by_mobile_code($_code);
        }

		if (empty($validation_details)) {
			$html = "Sorry, there's something wrong with your verification details. Kindly contact MMPI Admin. Thank you.";
		} else {
			// get member_details 
			$member_details = $this->members_model->get_member_by_id($validation_details->member_id);
			
			if (empty($member_details)) {
				// check if is_email_verified
				$html = "Sorry, there's something wrong with your membership details. Kindly contact MMPI Admin. Thank you.";
			} else {
				$continue_validation = true;
				// get date
				$update_timestamp = date("Y-m-d H:i:s");
                // email
                if ($_type == 'email') {
                    if ($member_details->is_email_verified == 1) {
                        $html = "Your E-mail has been already verified.";
						$continue_validation = false;
                    } else {
                        $html = "Thank you for verifying your E-mail. You have successfully completed E-mail verification process for MMPI. Congratulations!";

                        $data_member_verification_before = array(
							"email_verification_timestamp"=>$validation_details->email_verification_timestamp
						);
						$data_member_verification = array(
							"email_verification_timestamp"=>$update_timestamp
						);
						$data_member_before = array(
							"is_email_verified"=>$member_details->is_email_verified
						);
						$data_member = array(
							"is_email_verified"=>1
						);

                    }
                }
				
				if($continue_validation)
				{
					$finish_validation = true;
					if($_type == "rfid" || $_type == "paycard")
					{
						$column = "rfid_card_number";
						$number = "rf_id";
						if($_type == "paycard")
						{
							$column = "paycard_number";
							$number = "metrobank_paycard_number";
						}

						$rfid_paycard_tag = $this->members_model->get_member_rfid_cards(array($column => $member_details->$number));

						if(empty($rfid_paycard_tag))
						{
							$_type = ucwords($_type);
							$html = "Sorry, the {$_type} you used does not exist in our database. Kindly contact MMPI Admin. Thank you.";
							$finish_validation = false;
						}
						else
						{
							$rfid_paycard_tag = $rfid_paycard_tag[0];

							if($rfid_paycard_tag->{$_type."_member_id"} != 0 && $rfid_paycard_tag->{$_type."_member_id"} != $member_details->member_id)
							{
								$code_type = ucwords($code_type);
								$html = "Sorry, the {$_type} you used has already been used by another member. Kindly contact MMPI Admin. Thank you.";
								$finish_validation = false;
							}
							else
							{
								$data = array("{$_type}_member_id"=>$member_details->member_id);
								$this->members_model->update_member_rfid($data,array($column => $member_details->$number));

								$details_before = array('id' => $rfid_paycard_tag->rfid_card_id, 'details' => array("{$_type}_member_id" => $rfid_paycard_tag->{$_type."_member_id"}));
								$details_before = json_encode($details_before);

								// -------------------------------
								// tracking
								// -------------------------------
								$details_after = array('id' => $rfid_paycard_tag->rfid_card_id, 'details' => $data);
								$details_after = json_encode($details_after);
								$update_rfid_paycard_details = array(
									'member_id' => $member_details->member_id,
									'module_name' => strtoupper($_type)." VERIFICATION",
									'table_name' =>  'cm_member_rfid_cards',
									'action' => "UPDATE",
									'details_before' => $details_before,
									'details_after' => $details_after,
									'remarks' => ""
								);
								$this->tracking_model->insert_logs('members', $update_rfid_paycard_details);

							}
						}
					}

					if($finish_validation)
					{
						// update verification_timestamp
						$this->members_model->update_member_verification($data_member_verification, "member_id = {$member_details->member_id}");

						$details_before = array('id' => $member_details->member_id, 'details' => $data_member_verification_before);
						$details_before = json_encode($details_before);

						// -------------------------------
						// tracking
						// -------------------------------
						$details_after = array('id' => $member_details->member_id, 'details' => $data_member_verification);
						$details_after = json_encode($details_after);
						$update_rfid_paycard_details = array(
							'member_id' => $member_details->member_id,
							'module_name' => strtoupper($_type)." VERIFICATION",
							'table_name' =>  'cm_member_verification',
							'action' => "UPDATE",
							'details_before' => $details_before,
							'details_after' => $details_after,
							'remarks' => ""
						);
						$this->tracking_model->insert_logs('members', $update_rfid_paycard_details);


						$this->members_model->update_member($data_member,"member_id = {$member_details->member_id}");
						$details_before = array('id' => $member_details->member_id, 'details' => $data_member_before);
						$details_before = json_encode($details_before);

						// -------------------------------
						// tracking
						// -------------------------------
						$details_after = array('id' => $member_details->member_id, 'details' => $data_member);
						$details_after = json_encode($details_after);
						$update_rfid_paycard_details = array(
							'member_id' => $member_details->member_id,
							'module_name' => strtoupper($_type)." VERIFICATION",
							'table_name' =>  'cm_members',
							'action' => "UPDATE",
							'details_before' => $details_before,
							'details_after' => $details_after,
							'remarks' => ""
						);
						$this->tracking_model->insert_logs('members', $update_rfid_paycard_details);
					}
				}
			}
		}
		
		//var_dump($validation_details);
		$this->template->html = $html;
		$this->template->view('verification');	
    }
}