<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Sms_gsm extends Base_Controller {

	function __construct() {
		parent::__construct();
	}

	public function index() {
		echo json_encode(array('status' => 1, 'msg' => 'Crown Lifestyle SMS Handler.'));
	}
	
	public function incoming() {
		
		$data = trim($this->input->get_post('data'));
		
		// dont accept any incoming request
		echo "";
		return;
	}
	
	public function outgoing($identifier = "", $outgoing_id = 0) {
		
		$outgoing_id = abs($outgoing_id);
		
		if (!empty($identifier)) {
			// identifier is present
			$identifier = strtolower($identifier);

			if ($outgoing_id == 0) {
				// fetch outgoing messages
				$sql = "SELECT sms_outgoing_id, identifier, member_id, mobile_number, message, status, insert_timestamp FROM tr_sms_outgoing WHERE identifier = '{$identifier}' AND status = 'pending' ORDER BY insert_timestamp ASC";

				$query = $this->db->query($sql);
				$result = $query->result();
				$sms_messages = "";
				
				foreach ($result as $row) {
					$sms_messages .= $row->sms_outgoing_id . "|";
					$sms_messages .= $row->mobile_number . "|";
					$sms_messages .= $row->message . "\r\n";
					
					$this->db->query("UPDATE tr_sms_outgoing SET status = 'sending' WHERE sms_outgoing_id = {$row->sms_outgoing_id}");
				}
				
				echo $sms_messages;
				return;
				
			} else {
				// update outgoing sms message to sent
				$this->db->query("UPDATE tr_sms_outgoing SET status = 'sent' WHERE sms_outgoing_id = {$outgoing_id}");
				echo "201|{$outgoing_id}|updated.";
				return;
			}
		}
		
		// if everything fails this will be returned;
		echo "";
		return;	
	}

}
