<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Funds_to_paycard extends Base_Controller {
	
	function __construct() 
	{
  		parent::__construct();
		$this->load->model("members_model");
		$this->load->model("payout_model");
		$this->load->model("tracking_model");
	}
	
	function index()
	{
		echo "Funds to paycard execution";
	}
	
	function view()
	{
		$this->template->view("funds_to_paycard/dashboard");
	}
	
	function get_groups()
	{
		$type = $this->input->get_post("type");
		$adjustment_type = $this->input->get_post("adjustment_type");
				
		
		$where ="status = 'PENDING'";
		if ($type != 'ALL') {
			$where .=" AND type = '{$type}'";
		}
		
		
		if (($adjustment_type != 'DEDUCTIONS') && ($adjustment_type != 'DEDUCTIONS-GCEP')) {
		
		
			if ($adjustment_type == 'FUNDSTOPAYCARD') {
				// FUNDS TO PAYCARD
				$funds_to_paycard_details = $this->payout_model->get_funds_to_paycard($where);
				
			} else if ($adjustment_type == 'ADJUSTMENTS') {
				// ADJUSTMENTS
				$where .=" AND cash_card LIKE '%FUNDS%'";
				$funds_to_paycard_details = $this->payout_model->get_member_adjustments($where);
			} 
			
			if (empty($funds_to_paycard_details)) {
				$html = "Error. No Pending Transaction to process.";
				$this->return_json("error","success", array("html" => $html));
				return;		
			}
			
			foreach($funds_to_paycard_details as $ftpd) {
				
				// get member details from cm_members
				$member_id = $ftpd->member_id;
				
				$member_details = $this->members_model->get_member_by_id($member_id);
				
				// record 
				$data = array('funds_before' => $member_details->funds, 'status' => 'PROCESSING');
				
				if ($adjustment_type == 'FUNDSTOPAYCARD') {
					$where = "funds_to_paycard_id = {$ftpd->funds_to_paycard_id}";
					$this->payout_model->update_funds_to_paycard($data, $where);
				} else {
					$where = "member_adjustment_id = {$ftpd->member_adjustment_id}";
					$this->payout_model->update_member_adjustments($data, $where);
				}
						
				if ($type == 'IGPSM') {					
					$amount_to_process = $ftpd->net_of_gcep;					
				} else {
					$amount_to_process = $ftpd->gross;
				}
				
				if ($adjustment_type == 'FUNDSTOPAYCARD') {
				
					// check if funds is enough
					if ($member_details->funds >= $amount_to_process) {
						
						// deduct
						$new_funds = $member_details->funds - $amount_to_process;
						$data = array('funds' => $new_funds);
						$where = "member_id = {$member_id}";
						$this->members_model->update_member($data, $where);
						
						$deducted_amount = $amount_to_process;
						
						// log to debit funds logs
						$data = array(
							"member_id" => $member_id,
							"amount" => $amount_to_process,
							"remarks" => "DEDUCTED FOR FUNDS TO PAYCARD"
						);

						$this->tracking_model->insert_debit_logs($data);
					
					} else {
						// set funds to zero
						$new_funds = 0;
						$data = array('funds' => $new_funds);
						$where = "member_id = {$member_id}";
						$this->members_model->update_member($data, $where);
						
						$deducted_amount = $member_details->funds;
						
						// log to debit funds logs
						$data = array(
							"member_id" => $member_id,
							"amount" => $member_details->funds,
							"remarks" => "DEDUCTED FOR FUNDS TO PAYCARD - funds_to_paycard_id[{$ftpd->funds_to_paycard_id}]"
						);

						$this->tracking_model->insert_debit_logs($data);			
					
					}
								
					// record new funds			
					$current_datetime = date("Y-m-d H:i:s");	
					$data = array(
						'funds_after' => $new_funds, 
						'update_timestamp' => $current_datetime, 
						'status' => 'PROCESSED',
						'deducted_amount' => $deducted_amount
					);
					$where = "funds_to_paycard_id = {$ftpd->funds_to_paycard_id}";
					$this->payout_model->update_funds_to_paycard($data, $where);
				
				} else 	{
					// ADJUSTMENTS
					
					// add to funds
					$new_funds = $member_details->funds + $amount_to_process;
					$data = array('funds' => $new_funds);
					$where = "member_id = {$member_id}";
					$this->members_model->update_member($data, $where);
					
					$added_amount = $amount_to_process;
					
					// log to debit funds logs
					$data = array(
						"member_id" => $member_id,
						"amount" => $amount_to_process,
						"type" => "FUNDS",
						"remarks" => "ADDED FOR ADJUSTMENTS - member_adjustment_id[{$ftpd->member_adjustment_id}]"
					);

					$this->tracking_model->insert_credit_logs($data);
					
					// record new funds			
					$current_datetime = date("Y-m-d H:i:s");	
					$data = array(
						'funds_after' => $new_funds, 
						'update_timestamp' => $current_datetime, 
						'status' => 'PROCESSED',
						'added_amount' => $added_amount
					);
					$where = "member_adjustment_id = {$ftpd->member_adjustment_id}";
					$this->payout_model->update_member_adjustments($data, $where);
					
				}				
			}
			
		} else {
			//// ==============
			//// DEDUCTIONS ===
			//// ==============
			
			if ($adjustment_type == 'DEDUCTIONS') {						
				$funds_to_paycard_details = $this->payout_model->get_member_deductions($where);
			} else {
				// deductions-gcep
				$funds_to_paycard_details = $this->payout_model->get_member_gcep_deductions($where);
			}
				
			if (empty($funds_to_paycard_details)) {
				$html = "Error. No Pending {$adjustment_type} to process.";
				$this->return_json("error","success", array("html" => $html));
				return;
			
			}
			
			foreach($funds_to_paycard_details as $ftpd) {
			
				// get member details from cm_members
				$member_id = $ftpd->member_id;				
				$member_details = $this->members_model->get_member_by_id($member_id);
				
				// record 
				if ($adjustment_type == 'DEDUCTIONS') {	
					$data = array('funds_before' => $member_details->funds, 'status' => 'PROCESSING');
					$where = "member_deduction_id = {$ftpd->member_deduction_id}";
					$this->payout_model->update_member_deductions($data, $where);
					
					$amount_to_process = 0;
					$amount_to_process = $ftpd->deduction1 + $ftpd->deduction2 + $ftpd->card_fee;
				} else {
					// gcep
					$data = array('gcep_before' => $member_details->gcep, 'status' => 'PROCESSING');
					$where = "member_gcep_deduction_id = {$ftpd->member_gcep_deduction_id}";
					$this->payout_model->update_member_gcep_deductions($data, $where);
					
					$amount_to_process = 0;
					$amount_to_process = $ftpd->gross * 0.10;
				}
				
				$proper_payout_period = $ftpd->start_date . " to " . $ftpd->end_date . " (" . $ftpd->type . ")"; 
			
				if ($adjustment_type == 'DEDUCTIONS') {	
					
					// check if funds is enough
					if ($member_details->funds >= $amount_to_process) {
						
						// deduct
						$new_funds = $member_details->funds - $amount_to_process;
						$data = array('funds' => $new_funds);
						$where = "member_id = {$member_id}";
						$this->members_model->update_member($data, $where);
						
						$deducted_amount = $amount_to_process;

						
						// log to debit funds logs
						$data = array(
							"member_id" => $member_id,
							"type" => "FUNDS",
							"amount" => $amount_to_process,
							"remarks" => "DEDUCTED FOR " . $proper_payout_period . " DEDUCTIONS"
						);
					
						$this->tracking_model->insert_debit_logs($data);
					
					} else {
						// set funds to zero
						$new_funds = 0;
						$data = array('funds' => $new_funds);
						$where = "member_id = {$member_id}";
						$this->members_model->update_member($data, $where);
						
						$deducted_amount = $member_details->funds;
						
						// log to debit funds logs
						$data = array(
							"member_id" => $member_id,
							"type" => "FUNDS",
							"amount" => $member_details->funds,
							"remarks" => "DEDUCTED FOR " . $proper_payout_period . " DEDUCTIONS"
						);
				
						$this->tracking_model->insert_debit_logs($data);							
					}
					
				} else {
					// GCEP					
					// check if gcep is enough
					if ($member_details->gcep >= $amount_to_process) {
						
						// deduct
						$new_funds = $member_details->gcep - $amount_to_process;
						$data = array('gcep' => $new_funds);
						$where = "member_id = {$member_id}";
						$this->members_model->update_member($data, $where);
						
						$deducted_amount = $amount_to_process;
						
						// log to debit funds logs
						$data = array(
							"member_id" => $member_id,
							"type" => "GCEP",
							"amount" => $amount_to_process,
							"remarks" => "DEDUCTED FOR " . $proper_payout_period . " DEDUCTIONS-GCEP"
						);
					
						//$this->tracking_model->insert_debit_logs($data);
					
					} else {
						// set funds to zero
						$new_funds = 0;
						$data = array('gcep' => $new_funds);
						$where = "member_id = {$member_id}";
						$this->members_model->update_member($data, $where);
						
						$deducted_amount = $member_details->gcep;
						
						// log to debit funds logs
						$data = array(
							"member_id" => $member_id,
							"type" => "GCEP",
							"amount" => $member_details->gcep,
							"remarks" => "DEDUCTED FOR " . $proper_payout_period . " DEDUCTIONS-GCEP"
						);
				
						//$this->tracking_model->insert_debit_logs($data);			
					
					}	
				}
				
				if ($adjustment_type == 'DEDUCTIONS') {	
					// record new funds			
					$current_datetime = date("Y-m-d H:i:s");	
					$data = array(
						'funds_after' => $new_funds, 
						'update_timestamp' => $current_datetime, 
						'status' => 'PROCESSED',
						'deducted_amount' => $deducted_amount
					);
					
					$where = "member_deduction_id = {$ftpd->member_deduction_id}";
					$this->payout_model->update_member_deductions($data, $where);								
					
				} else {
				
					// record new funds			
					$current_datetime = date("Y-m-d H:i:s");	
					$data = array(
						'gcep_after' => $new_funds, 
						'update_timestamp' => $current_datetime, 
						'status' => 'PROCESSED',
						'deducted_amount' => $deducted_amount
					);
					
					$where = "member_gcep_deduction_id = {$ftpd->member_gcep_deduction_id}";
					$this->payout_model->update_member_gcep_deductions($data, $where);								
					
				}
		
			}
				
		}
		
		$this->return_json("ok","success");
		return;		
		
	}
}