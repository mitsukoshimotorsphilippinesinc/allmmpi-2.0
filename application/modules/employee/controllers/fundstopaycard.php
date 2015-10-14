<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Fundstopaycard extends Site_Controller 
{	
	
	function __construct() 
	{
  		parent::__construct();

			
		$this->load->model('vouchers_model');
		$this->load->model('contents_model');
		$this->load->model('items_model');
		$this->load->model('raffles_model');
		
		// load pager library
		$this->load->library('pager');	
	}
	
	public function index() 
	{ 

		$this->page();
	}
	
	public function page()
	{
	
		$member_id = $this->member->member_id;
		$from_date = $this->input->get_post('from_date');
		$to_date = $this->input->get_post('to_date');
		$status = $this->input->get_post('status');
			
		$where = "member_id = " . $member_id;
		
		if(!empty($status) && $status != 'ALL')	$where .= " AND status = '" . strtoupper($status) . "'";
		
		$from_dt = $from_date;
		$to_dt = $to_date;
			
		if ((!($from_date == "")) && (!($to_date == "")))
			$where .= " AND (date(insert_timestamp) BETWEEN '{$from_date}' AND '{$to_date}') ";
		else if ((!($from_date == "")) && ($to_date == ""))
			$where .= " AND insert_timestamp >= '{$from_date}'";
		else if ((!($to_date == "")) && ($from_date == ""))
			$where .= " AND insert_timestamp <= '{$to_date}'";
				
		// search filters
		$get_data = "?status=" . $status . "&from_date=" . $from_date . "&to_date=" . $to_date;
		
		$total_items = $this->payout_model->get_funds_to_paycard_count($where);

		// set pagination
		$config = array(
			'pagination_url' => '/members/fundstopaycard/page',
			'total_items' => $total_items,
			'per_page' => 20,
			'uri_segment' => 4,
		);
	
		$this->pager->set_config($config);

		$limit = array('rows' => $this->pager->per_page, 'offset' => $this->pager->offset);
			
		$member_f2p = $this->payout_model->get_funds_to_paycard($where, $limit,"insert_timestamp DESC");	
	
		$between_timestamps = $from_date . " - " . $to_date;
		if (trim($between_timestamps) == "-") 
			$between_timestamps = "n/a";
				
		$this->template->between_timestamps = strtoupper($between_timestamps);	
		$this->template->member_f2p = $member_f2p;
		$this->template->member_id = $member_id;
		$this->template->from_date = $from_date;
		$this->template->to_date = $to_date;
		$this->template->status = $status;
		$this->template->get_data = $get_data;
		
		$this->template->current_page = 'fundstopaycard';
		$this->template->view('fundstopaycard/dashboard');
	}
	
	public function request_transfer() {
		
		$member_details = $this->members_model->get_member_by_id($this->member->member_id);
		
		$witholding_tax = $this->settings->witholding_tax * 100;
		$minimum_f2p_amount = $this->settings->minimum_f2p_amount;
		$maximum_f2p_amount = $this->settings->maximum_f2p_amount;
		
		// payout period
		$where = "now() BETWEEN start_date AND end_date";
		$payout_period_details = $this->payout_model->get_payout_periods($where);
		
		if (empty($payout_period_details)) {
			$start_date = date("Y-m-d 00:00:00");
			$end_date = date("Y-m-d 23:59:59");
		} else {		
			$start_date = $payout_period_details[0]->start_date;
			$end_date = $payout_period_details[0]->end_date;
		}
		
		// check if already have a pending request for this cutoff
		$where = "member_id = {$this->member->member_id} AND status = 'PENDING' AND insert_timestamp BETWEEN '{$start_date}' AND '{$end_date}'";		
		$existing_f2p_count = $this->payout_model->get_funds_to_paycard_count($where);
		
		if ($existing_f2p_count > 0) {
			$html = "Sorry, you already have a pending Funds To Paycard request. Only one request is allowed per cut-off. Please cancel the pending transaction if you wish to change the amount.";
			$this->return_json("0","Existing Funds to Paycard request.",array("html" => $html));
			return;	
		}
		
		
		$html = "<p>Please specify the amount you want to transfer: </p>
					
					<fieldset >		
						<div>
							<label class='control-label' for='member_name'><strong>Current Funds</strong></label>								
							<input type='text' class='span3' disabled='disabled' placeholder='' name='member_funds' id='member_funds' value='{$member_details->funds}'/>								
						</div>
						
						<div>
							<label class='control-label' for='f2p_amount'><strong>Desired Amount:</strong></label>								
							<input type='text' maxlength='8' class='span3' placeholder='{$member_details->funds}' name='f2p_amount' id='f2p_amount' value=''/>								
						</div>
						<span id='f2p_amount_error' style='display:none;' class='label label-important' ></span> 							
					</fieldset>
				 		
				  <p><strong>NOTE:</strong> A {$witholding_tax}% Witholding Tax will be deducted to your requested amount.</p>	
				";
            
        $this->return_json("1","Transfer request",array("html" => $html, "member_funds" => $member_details->funds, "minimum_f2p_amount" => $minimum_f2p_amount, "maximum_f2p_amount" => $maximum_f2p_amount));
		return;
	}
	
	public function proceed_request_f2p() {
	
        $f2p_amount = $this->input->post("f2p_amount");        
		
		// get member details
		$member_details = $this->members_model->get_member_by_id($this->member->member_id);
				
		if (empty($member_details)) {
			$html = "Error: Member Details not found.";
			$this->return_json("0","Member Details Not Found",array("html" => $html));
			return;	
		}
	
		$proper_name = $member_details->first_name . " " . $member_details->last_name;		
		$current_datetime = date("Y-m-d H:i:s");
		
		// compute 
		$witholding_tax = $this->settings->witholding_tax;
		$minimum_f2p_amount = $this->settings->minimum_f2p_amount;
		$maximum_f2p_amount = $this->settings->maximum_f2p_amount;
		
		// GCEP
		$gcep = 0;
		$net_of_gcep = $f2p_amount - $gcep;
		
		// TAX
		$wtax = $f2p_amount * $witholding_tax;		
		$net_of_wtax = $net_of_gcep - $wtax;
		
		// FINAL
		$total_deductions = 0;
		$final_commission = $net_of_wtax - $total_deductions;
		
		$where = "now() BETWEEN start_date AND end_date";
		$payout_period_details = $this->payout_model->get_payout_periods($where);
		
		if (empty($payout_period_details)) {
			$proper_start_date = date("Y-m-d");
			$proper_end_date = $proper_start_date;			
		} else {		
			$proper_start_date = date("Y-m-d", strtotime($payout_period_details[0]->start_date));
			$proper_end_date = date("Y-m-d", strtotime($payout_period_details[0]->end_date));
		}
		
		//insert request to ph_funds_to_paycard
		$data = array(            
            'member_id' => $this->member->member_id,
			'last_name' => $member_details->last_name,
			'first_name' => $member_details->first_name,
			'middle_name' => $member_details->middle_name,
			'gross' => $f2p_amount,
			'gcep' => $gcep,
			'net_of_gcep' => $net_of_gcep,
			'wtax' => $wtax,
			'net_of_wtax' => $net_of_wtax,
            'final_commission' => $final_commission,
            'status' => 'PENDING',
			'type' => 'IGPSM',
			'start_date' => $proper_start_date,
			'end_date' => $proper_end_date,
			'cash_card' => $member_details->metrobank_paycard_number,
        );
        $this->payout_model->insert_funds_to_paycard($data);
		
		$funds_to_paycard_id = $this->payout_model->insert_id();
		
		$details_before = '';
		$details_after = array('funds_to_paycard_id' =>  $funds_to_paycard_id, 'details' => $data);
		$details_after = json_encode($details_after);
		
		// log to tr_member_logs
		$insert_member_logs = array(
			'member_id' => $this->member->member_id,
			'module_name' => "FUNDS TO PAYCARD :: REQUEST",
			'table_name' => "ph_funds_to_paycard",
			'action' => "INSERT",
			'details_before' => $details_before,
			'details_after' => $details_after,
			'remarks' => $funds_to_paycard_id
		);
		$this->tracking_model->insert_logs('members', $insert_member_logs);
		
		// deduct f2p amount to member Funds		
		$funds_after_f2p = $member_details->funds - $f2p_amount;
		
		// update cm_members
		$data = array(
			'funds' => $funds_after_f2p
		);
		$this->members_model->update_member($data, "member_id = " . $this->member->member_id);
		
		// insert to tr_member_logs		
		$details_before == array('member_id' => $this->member->member_id, 'details' => array('funds' => $member_details->funds));
		$details_before = json_encode($details_before);
		
		$details_after = array('member_id' =>  $this->member->member_id, 'details' => $data);
		$details_after = json_encode($details_after);
		
		// log to tr_member_logs
		$insert_member_logs = array(
			'member_id' => $this->member->member_id,
			'module_name' => "FUNDS TO PAYCARD :: REQUEST",
			'table_name' => "cm_members",
			'action' => "UPDATE FUNDS",
			'details_before' => $details_before,
			'details_after' => $details_after,
			'remarks' => $funds_to_paycard_id
		);
		$this->tracking_model->insert_logs('members', $insert_member_logs);
		
		$proper_funds_to_paycard_id = str_pad($funds_to_paycard_id, 8, "0", STR_PAD_LEFT);
		
		$html_data = "<table>
						<thead> 
							<tr>
								<td>Title</td>
								<td><strong>Details</strong></td>								
							</tr>
						</thead>
						<tbody>
							<tr>
								<td><strong>Gross</strong></td>
								<td>{$f2p_amount}</label></td>
							</tr>					
							<tr>
								<td><strong>GCEP</strong></td>
								<td>{$gcep}</label></td>
							</tr>
							<tr>
								<td><strong>Witholding Tax</strong></td>
								<td>{$wtax}</label></td>
							</tr>
							<tr>
								<td><strong>Deductions</strong></td>
								<td>{$total_deductions}</label></td>
							</tr>
							<tr>
								<td><strong>Net Commission</strong></td>
								<td>{$final_commission}</label></td>
							</tr>	
							<tr>
								<td><strong>Cash Card</strong></td>
								<td>{$member_details->metrobank_paycard_number}</label></td>
							</tr>	
						</tbody>
					</table>";
		
		
		// send email notification to recipient
        $params = array(
			 "first_name"=>ucfirst($member_details->first_name),
			"html_data"=>strtoupper($html_data), 					
            "proper_funds_to_paycard_id"=>$proper_funds_to_paycard_id	
        );
        
        $data = array(
            "email"=>$this->member->email,
            "type"=>"request_funds_to_paycard_notification_receipt",
            "params"=>$params
        );
        
        //send email to user
        Modules::run('jobs/notifications/send_email',$data);
		
		$html ="<p>Your Funds To Paycard Request has been queued.<br/><br/>
				</p>
				";
		
        $this->return_json("1","Request Successful",array("html" => $html));
		return;   	  
    }
	
	public function cancel_transfer() {
		$funds_to_paycard_id = abs($this->input->post("funds_to_paycard_id"));
		 
		$f2p = $this->payout_model->get_funds_to_paycard_by_id($funds_to_paycard_id);	
		$proper_name_from_member = ucfirst($f2p->first_name) . " " . ucfirst($f2p->middle_name) . " " . ucfirst($f2p->last_name);
		
		$total_deductions = $f2p->deduction1 + $f2p->deduction2 + $f2p->fee;
		
		$html = "<p>You are to cancel your pending Funds To Paycard request with the following details: </p>
					
					<fieldset >		
						
						<div class='span4'>
							<label class='control-label' for='member_name'><strong>Gross Amount</strong></label>								
							<input type='text' class='span4' disabled='disabled' placeholder='' name='gross' id='gross' value='{$f2p->gross}'/>								
						</div>
						
						<div class='span2'>
							<label class='control-label' for='witholding_tax'><strong>Witholding Tax</strong></label>								
							<input type='text' class='span2' disabled='disabled' placeholder='' name='witholding_tax' id='witholding_tax' value='{$f2p->wtax}'/>								
						</div>
						
						<div class='span2'>
							<label class='control-label' for='total_deductions'><strong>Total Deductions</strong></label>								
							<input type='text' class='span2' disabled='disabled' placeholder='' name='total_deductions' id='total_deductions' value='{$total_deductions}'/>								
						</div>
						
						<div  class='span4'>
							<label class='control-label' for='final_commission'><strong>Net Commission</strong></label>								
							<input type='text' class='span4' disabled='disabled' placeholder='' name='final_commission' id='final_commission' value='{$f2p->final_commission}'/>								
						</div>
						
						<div class='span4'>
							<label class='control-label' for='insert_timestamp'><strong>Date Requested</strong></label>								
							<input type='text' class='span4' disabled='disabled' placeholder='' name='insert_timestamp' id='insert_timestamp' value='{$f2p->insert_timestamp}'/>								
						</div>
						
					</fieldset>
					
					<p>The Gross amount will be returned to your Funds once completed. Do you want to proceed?</p>
				";
            
        $this->return_json("1","Cancel Funds To Paycard request",array("html" => $html));
		return;      				
	}
	
	public function proceed_cancel_f2p() {
		$funds_to_paycard_id = abs($this->input->post("funds_to_paycard_id"));
	
		$f2p = $this->payout_model->get_funds_to_paycard_by_id($funds_to_paycard_id);	
	
		$current_datetime = date("Y-m-d H:i:s");		
	
		$member_details = $this->members_model->get_member_by_id($f2p->member_id);
	
		// get details before update	
		$details_before = $f2p;	
		$details_before = json_encode($details_before);	
		
		// update member_id = to_member_id
		 $data = array(            		            
            'status' => 'CANCELLED',
			'update_timestamp' => $current_datetime
        );
        $this->payout_model->update_funds_to_paycard($data, "funds_to_paycard_id = " . $funds_to_paycard_id);
		
		// logs 
		$details_after = array('funds_to_paycard_id' =>  $funds_to_paycard_id, 'details' => $data);
		$details_after = json_encode($details_after);
		
		// log to tr_member_logs
		$update_member_logs = array(
			'member_id' => $this->member->member_id,
			'module_name' => "CANCEL FUNDS TO PAYCARD :: SUCCESSFUL",
			'table_name' => "ph_funds_to_paycard",
			'action' => "CANCEL",
			'details_before' => $details_before,
			'details_after' => $details_after,
			'remarks' => $funds_to_paycard_id
		);
		$this->tracking_model->insert_logs('members', $update_member_logs);		
		
		// return f2p amount to member Funds		
		$funds_before_f2p = $member_details->funds + $f2p->gross;
		
		// update cm_members
		$data = array(
			'funds' => $funds_before_f2p
		);
		$this->members_model->update_member($data, "member_id = " . $this->member->member_id);
		
		// insert to tr_member_logs		
		$details_before == array('member_id' => $this->member->member_id, 'details' => array('funds' => $member_details->funds));
		$details_before = json_encode($details_before);
		
		$details_after = array('member_id' =>  $this->member->member_id, 'details' => $data);
		$details_after = json_encode($details_after);
		
		// log to tr_member_logs
		$insert_member_logs = array(
			'member_id' => $this->member->member_id,
			'module_name' => "FUNDS TO PAYCARD :: CANCEL",
			'table_name' => "cm_members",
			'action' => "UPDATE FUNDS",
			'details_before' => $details_before,
			'details_after' => $details_after,
			'remarks' => $funds_to_paycard_id
		);
		$this->tracking_model->insert_logs('members', $insert_member_logs);
		
		
		$html = "You have successfully cancelled your Funds To Paycard request.";
		
		$proper_funds_to_paycard_id = str_pad($funds_to_paycard_id, 8, "0", STR_PAD_LEFT);
		$total_deductions = $f2p->deduction1 + $f2p->deduction2 + $f2p->fee;
		
		$html_data = "<table>
						<thead> 
							<tr>
								<td>Title</td>
								<td><strong>Details</strong></td>								
							</tr>
						</thead>
					<tbody>
						<tr>
							<td><strong>Gross</strong></td>
							<td>{$f2p->gross}</label></td>
						</tr>					
						<tr>
							<td><strong>GCEP</strong></td>
							<td>{$f2p->gcep}</label></td>
						</tr>
						<tr>
							<td><strong>Witholding Tax</strong></td>
							<td>{$f2p->wtax}</label></td>
						</tr>
						<tr>
							<td><strong>Deductions</strong></td>
							<td>{$total_deductions}</label></td>
						</tr>
						<tr>
							<td><strong>Net Commission</strong></td>
							<td>{$f2p->final_commission}</label></td>
						</tr>	
						<tr>
							<td><strong>Cash Card</strong></td>
							<td>{$f2p->cash_card}</label></td>
						</tr>	
					</tbody>
				</table>";
		
		// send email as a receipt to sender
        $params = array(
            "first_name"=>ucfirst($member_details->first_name),
			"html_data"=>strtoupper($html_data), 					
            "proper_funds_to_paycard_id"=>$proper_funds_to_paycard_id			
        );
    
        $data = array(
            "email"=>$member_details->email,
            "type"=>"cancel_funds_to_paycard_notification_receipt",
            "params"=>$params
        );
    
        //send email to user
        Modules::run('jobs/notifications/send_email',$data);
		 
		
		$this->return_json("1","Update F2P",array("html" => $html));
		
		return;
	
	}
	
}

