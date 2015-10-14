<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Transfers extends Site_Controller {
	
	function __construct() {
		parent::__construct();		
		
		$this->load->model('facilities_model');
		$this->load->model('members_model');
		$this->load->model('tracking_model');
		$this->set_navigation('webpoi');
		$this->load->model('settings_model');
		
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
		$date_filter = $this->input->get_post('date_filter');			
		$transfer_type = $this->input->get_post('transfer_type');
		$status_type = $this->input->get_post('status_type');		
		
		$from_t = strtotime($from_date);
		$to_t = strtotime($to_date);
		
		$where = "(from_member_id = " . $member_id . " OR to_member_id = " . $member_id .")";
		
		if(!empty($transfer_type) && $transfer_type != 'ALL')	$where .= " AND type = '" . strtoupper(trim($transfer_type)) . "'";
		if(!empty($status_type) && $status_type != 'ALL')	$where .= " AND status = '" . strtoupper(trim($status_type)) . "'";		
		
		
		$from_dt = $from_date;
		$to_dt = $to_date;
				
		if ($from_t !== false) $from_dt = date('Y-m-d', $from_t); 
		if ($to_t !== false) $to_dt = date('Y-m-d', $to_t); 
		
		if ($from_t !== false && $to_t !== false)
			$where .= " AND (date_format(insert_timestamp, '%Y-%m-%d') BETWEEN '{$from_dt}' AND '{$to_dt}') ";
		else if ($from_t !== false && $to_t === false)
			$where .= " AND insert_timestamp >= '{$from_dt}'";
		else if ($from_t === false && $to_t !== false)
			$where .= " AND insert_timestamp <= '{$to_dt}'";
			
		$get_data = "?type=" . $transfer_type . "&status=" . $status_type . "&from_date=" . $from_date . "&to_date=" . $to_date;
	
		// initialize pagination class
		// set pagination data		
		$config = array(
			'pagination_url' => '/members/transfers/page/',
		   	'total_items' => $this->tracking_model->get_member_transfers_count($where),
		    'per_page' => 20,
		    'uri_segment' => 4,
		);

		$this->pager->set_config($config);
			
		$this->template->transactions = $this->tracking_model->get_member_transfers($where, array('rows' => $this->pager->per_page, 'offset' => $this->pager->offset), 'insert_timestamp DESC');
		$this->template->member_id = $member_id;				
		$this->template->from_date = $from_date;
		$this->template->to_date = $to_date;
		$this->template->get_data = $get_data;
		
		if (empty($status_type)) 
			$status_type = "all";
			
		if (empty($transfer_type)) 
			$transfer_type = "all";	
			
		$between_timestamps = $from_date . " - " . $to_date;
		if (trim($between_timestamps) == "-") {
			$between_timestamps = "n/a";
		}
		
		$this->template->status_type = strtoupper($status_type);
		$this->template->transfer_type = strtoupper($transfer_type);
		$this->template->between_timestamps = strtoupper($between_timestamps);
		$this->template->current_page = 'transfers';
		
		// get settings if need to disable transfer page
		$disable_transfer_page_details = $this->settings_model->get_setting_by_slug("disable_transfer_page");
		
		if ($disable_transfer_page_details->value == 1) {
			$this->template->title_page = 'My Transfers';
			$this->template->view('main/dashboard_disabled');
		} else {			
			$this->template->view('transfers/list');
		}	
	}
	
	public function check() {
	
		// check if member has pending transfer transaction
		$count = $this->tracking_model->get_member_transfers_count("from_member_id = ".$this->member->member_id." AND status = 'PENDING'");
        
        if($count > 0){
            
            $transfer = $this->tracking_model->get_member_transfers("from_member_id = ".$this->member->member_id." AND status = 'PENDING'");
            $transfer = $transfer[0];
            
			// get to_member details			
			$to_member_details = $this->members_model->get_member_by_id($transfer->to_member_id); 
			
			$proper_name_to_member = ucfirst($to_member_details->first_name) . " " . ucfirst($to_member_details->middle_name) . " " . ucfirst($to_member_details->last_name);
			
		    $transfer_amount = number_format($transfer->amount, 2);
            
            $html = "<p>You have a pending {$transfer->type} Transfer that needs confirmation.</p>
						
						<fieldset >		
							<div>
								<label class='control-label' for='to_member_name'><strong>To Member Name</strong></label>								
								<input type='text' class='span5' disabled='disabled' placeholder='' name='to_member_name' id='to_member_name' value='{$proper_name_to_member}'/>								
							</div>
							
							<div>
								<label class='control-label' for='transfer_amount'><strong>Amount</strong></label>								
								<input type='text' class='span3' disabled='disabled' placeholder='' name='transfer_amount' id='transfer_amount' value='{$transfer_amount}'/>								
							</div>
							
							<div>
								<label class='control-label' for='transaction_type'><strong>Transaction Type</strong></label>								
								<input type='text' class='span3' disabled='disabled' placeholder='' name='transaction_type' id='transaction_type' value='{$transfer->type}'/>								
							</div>
							
							<div>
								<label class='control-label' for='transaction_date'><strong>Transaction Date</strong></label>								
								<input type='text' class='span4' disabled='disabled' placeholder='' name='transaction_date' id='transaction_date' value='{$transfer->insert_timestamp}'/>								
							</div>					
						</fieldset>
					";
            
            $this->return_json("0","With pending transfer request",array("html" => $html));
			return;
            
        } else {
            if ($transfer->type == 'FUNDS') {
				// check if member has funds  
				if($this->member->funds > 0) {
					$data = "";
					$html = $this->load->view('members/transfers/transfer_modal', $data, TRUE);
					
					$this->return_json("1","Ok",array("html" => $html));
					return;		
				
					
				} else {
					// no fund or gc to transfer
					$this->return_json("-1","Error",array("html" => "Sorry, you don't have any credit to transfer."));
					return;
				}	
			} else if ($transfer->type == 'GCEP') {
				// GCEP
				// check if member has funds  
				if($this->member->gcep > 0) {
					$data = "";
					$html = $this->load->view('members/transfers/transfer_modal', $data, TRUE);
					
					$this->return_json("1","Ok",array("html" => $html));
					return;		
				
					
				} else {
					// no fund or gc to transfer
					$this->return_json("-1","Error",array("html" => "Sorry, you don't have any credit to transfer."));
					return;
				}	
				
			} else {
				// GC
				// check if member has funds  
				if($this->member->gift_cheques > 0) {
					$data = "";
					$html = $this->load->view('members/transfers/transfer_modal', $data, TRUE);
					
					$this->return_json("1","Ok",array("html" => $html));
					return;		
				
					
				} else {
					// no fund or gc to transfer
					$this->return_json("-1","Error",array("html" => "Sorry, you don't have any credit to transfer."));
					return;
				}	
			}
		}			
	}
	
	public function search() {
		// check if member has funds or gc
		$member_to_search = $this->input->get_post('member_to_search');
		
		$no_space_search_string = str_replace(' ','',$member_to_search);
		// find in cm_members
		$where = "last_name like '%". $member_to_search . "%' OR first_name LIKE '%" . $member_to_search ."%' OR concat(first_name, last_name) LIKE '%" . $no_space_search_string ."%' OR concat(last_name, first_name) LIKE '%" . $no_space_search_string ."%' OR concat(first_name, middle_name ,last_name) LIKE '%" . $no_space_search_string ."%' ORDER BY first_name LIMIT 10";
		
		$members_select = "SELECT member_id, last_name, first_name, middle_name, email, mobile_number FROM `cm_members` WHERE {$where}";
		$query = $this->db->query($members_select);
		$member_details = $query->result();
		
		$html = "
			<table class='table table-striped table-condensed assign-customer-list'>
				<thead>
					<tr>
						<td style='width: 280px;'>Name</td>
						<td>Email</td>
						<td>Action</td>
					</tr>
				</thead>
				<tbody id='assign-customer-listing'> 
		";
	
		foreach ($member_details as $member_detail) {
		
			$html .= 	"<tr>
							<td>{$member_detail->first_name} {$member_detail->middle_name} {$member_detail->last_name}</td>
							<td>{$member_detail->email}</td>
							<td><a href='#'><button class='btn-select-member btn btn-small btn-info' data='$member_detail->member_id'>Select</button></a></td>
						</tr>";
		}
	
		$html .= "</tbody></table>";
		
		
		
		$this->return_json("1","Ok",array("html" => $html));
		return;
	}
	
	
	public function transfer_form() {		
		$member_id = $this->input->get_post('member_id');
		
		// get member details
		$member_details = $this->members_model->get_member_by_id($member_id);
		
		if (empty($member_details)) {
			$this->return_json("0","Member details not found.",array("html" => "Member not found. Please select another."));
			return;
		}
		
		if (trim($member_details->email == "")) {
			$this->return_json("0","Member email not found.",array("html" => "Email not found. This is required to later confirm the transfer. Please select another member."));
			return;
		}
		
		$proper_name = ucfirst($member_details->first_name) . " " . ucfirst($member_details->middle_name) . " " . ucfirst($member_details->last_name); 
		
		$minimum_allowable_transfer = $this->settings_model->get_setting_by_slug("minimum_allowable_transfer");
		$maximum_allowable_transfer = $this->settings_model->get_setting_by_slug("maximum_allowable_transfer");
						
		$minimum_transfer_amount_display = number_format(($minimum_allowable_transfer->value), 2);
		
		if ($maximum_allowable_transfer->value == 0) {
			$active_member_funds_display = number_format(($this->member->funds), 2);
			$maximum_allowable_transfer_display = $active_member_funds_display;
		} else {
			$maximum_allowable_transfer_display = number_format(($maximum_allowable_transfer->value), 2);
		}
		
		$html = "
					<fieldset>	
					<div>
						<label class='control-label' for='transfer_to'><strong>Transfer To</strong></label>
						<input type='text' class='span5' disabled='disabled' placeholder='' name='transfer_to' id='transfer_to' value='{$proper_name}'/>												
					</div>
					
					<div>
						<label class='control-label' for='amount'><strong>Type</strong></label>						
						<select id='transfer_modal_type'>
							<option value='FUNDS'>FUNDS</option>
						</select>						
					</div>	
					
					<div>
						<label class='control-label' for='amount'><strong>Amount</strong></label>						
						<input type='text' class='span3' placeholder='{$minimum_transfer_amount_display} - {$maximum_allowable_transfer_display}' name='amount' id='amount' value=''/>						
						<span class='label label-important' id='amount_error' style='display:none;'></span>
					</div>					
					</fieldset>
				
			";
		
		$this->return_json("1","Ok",array("html" => $html));
		return;
	}
	

	public function check_amount() {		
		$to_member_id = $this->input->get_post('_to_member_id');
		$transfer_type = $this->input->get_post('_transfer_modal_type');
		$amount = $this->input->get_post('_amount');
		
		// get member details
		$to_member_details = $this->members_model->get_member_by_id($to_member_id);
		
		if (empty($to_member_details)) {
			$this->return_json("0","Member details not found.",array("html" => "Member not found. Please select another."));
			return;
		}
		
		// check if current member has credits to transfer
		// FUNDS
		$minimum_allowable_transfer = $this->settings_model->get_setting_by_slug("minimum_allowable_transfer");
		 
		if ($amount < $minimum_allowable_transfer->value) {
			$this->return_json("-1","Invalid Amount",array("html" => "Invalid amount. Amount must be  Funds"));		
			return;
		}
		
		if ($this->member->funds < $amount) {
			$this->return_json("-1","Insufficient Funds",array("html" => "Insufficient Funds"));
			return;	
		} 
		if ($amount)
		$this->return_json("1","Proceed with Transfer",array("html" => "Proceed with Transfer"));
		return;			
	}
	
	
	public function proceed_transfer() {
	
        $to_member_id = trim($this->input->post("to_member_id"));
        $amount = abs($this->input->post("amount"));

        $hash = $this->input->post("hash");
		$transfer_type = $this->input->post("transfer_type");

		$hash_check = hash_hmac('md5',$this->member->session_id,$this->config->item('encryption_key'));

		// perform routine check before proceeding to transfer
        // check hash
        if ($hash!=$hash_check) {
            $this->return_json("-1","Invalid Hash",array("html" => "Invalid Hash"));
			return;	            
        }        
		
		// check again if member exists
		// get member details
		$member_details = $this->members_model->get_member_by_id($to_member_id);
		
		if (empty($member_details)) {
			$this->return_json("0","Member details not found.",array("html" => "Member not found. Please select another."));
			return;
		}
		
		 // check if from owner = to owner
        if ($to_member_id == $this->member->member_id) {
            $this->return_json("0","Cannot transfer to same member.",array("html" => "Cannot transfer to same member."));
			return;          
        }

        // check FUNDS
        if ($this->member->funds <= 0) {
			$this->return_json("0","No funds to transfer.",array("html" => "No funds to transfer."));
			return;                     
        }
        
        // check if from_node has enough funds to transfer
        if ($this->member->funds < $amount) {
			$this->return_json("0","No enough funds.",array("html" => "You do not have enough funds."));
			return;                   
        }
				
        $pretty_amount = number_format($amount, 2);
               
		$image_filename = "male.jpg";
		if (empty($member_details->image_filename) || ($member_details->image_filename == NULL) || (trim($member_details->image_filename) == "")) {
			// check gender of member
			if (trim($member_details->sex) == "F") {
				$image_filename = "female.jpg";
			} else {
				$image_filename = "male.jpg";
			}
		} else {
			$image_filename = $member_details->image_filename;
		}
			
		$proper_to_name = ucfirst($member_details->first_name)." ".ucfirst($member_details->middle_name)." ".ucfirst($member_details->last_name);	
		$transfer_type = strtoupper($transfer_type);
		
		$html = "<div class='row-fluid'>
					<label>You are about to transfer an amount of <strong>Php {$pretty_amount}</strong> from <strong>{$transfer_type}</strong> to:</label>	
					<div class='span3'>
						<div class='img-wrap'><img id='to_member_image' style='width:100px;height:auto;' alt='' src='/assets/media/members/{$image_filename}'></div>
					</div>				
					<div class='span8'>
						<table class='table table-striped table-bordered'>
							<thead>
							</thead>
							<tbody>
								<tr>
									<td style='width:100px;'><label><strong>First Name</strong></label></td>
									<td><label class=''>{$member_details->first_name}</label></td>		
								</tr>
								<tr>
									<td style='width:100px;'><label><strong>Middle Name</strong></label></td>
									<td><label class=''>{$member_details->middle_name}</label></td>		
								</tr>
								<tr>
									<td style='width:100px;'><label><strong>Last Name</strong></label></td>
									<td><label class=''>{$member_details->last_name}</label></td>
								</tr>	
								<tr>
									<td style='width:100px;'><label><strong>Email</strong></label></td>
									<td><label class=''>{$member_details->email}</label></td>
								</tr>									
							</tbody>
						</table>	
					</div>
				</div>";
		
		
        $this->return_json("1","Confirmation",array("html" => $html));
		return;   
    }  
	  
	public function execute_transfer() {
	
        $to_member_id = trim($this->input->post("to_member_id"));
        $amount = abs($this->input->post("amount"));
        $transfer_type = $this->input->post("transfer_type");

		$current_datetime = date("Y-m-d H:i:s");
		// insert request to tr_member_transfers, status as pending
		$confirmation_code = md5($this->member->member_id."_".$to_member_id."_".$current_datetime);
        $confirmation_code = strtoupper(substr($confirmation_code, 0, 8));
			
        $data = array(
            'from_member_id' => $this->member->member_id,
            'to_member_id' => $to_member_id,
            'amount' => $amount,
			'type' => strtoupper($transfer_type),
            'confirmation_code' => $confirmation_code,
            'status' => 'PENDING',
            'insert_timestamp' => $current_datetime
        );
        $this->tracking_model->insert_member_transfers($data);
		
		// get to member details
		$to_member_details = $this->members_model->get_member_by_id($to_member_id);
		
		$proper_name = $to_member_details->first_name . " " . $to_member_details->last_name;
			
		// deduct amount to member's credit
		$data = array(
			"funds" => ($this->member->funds) - $amount
		);
		
		$this->members_model->update_member($data, "member_id = " . $this->member->member_id);
		
		/*// member logs 
		$details_after = array('id' => 0, 'details' => $data);
		$details_after = json_encode($details_after);
		$new_member_log_data = array(
			'member_id' => 0, //no member id given yet	
			'module_name' => 'MEMBER REGISTRATION',
			'table_name' => 'cm_members',
			'action' => 'ADD',
			'details_before' => "",
			'details_after' => $details_after,
			'remarks' => "",
		);
		$this->tracking_model->insert_logs('members', $new_member_log_data);
		*/
		
		$pretty_amount = number_format(($amount), 2);
		//$base_url = $this->config->item('base_url') . "/members/transfers/index/email/{$confirmation_code}";
		$base_url = $this->config->item('base_url') . "/members/transfers";
		
		// send email notification to recipient
        $params = array(
            "to_first_name"=>ucfirst($to_member_details->first_name),
            "to_last_name"=>ucfirst($to_member_details->last_name),
            "link"=>$base_url,
			"proper_amount"=>$pretty_amount,
			"proper_transfer_type"=>$transfer_type,
            "confirmation_code"=>$confirmation_code,
			"from_first_name"=>ucfirst($this->member->first_name),
            "from_last_name"=>ucfirst($this->member->last_name)			
        );

        $data = array(
            "email"=>$to_member_details->email,
            "type"=>"transfer_email",
            "params"=>$params
        );

        //send email to user
        Modules::run('jobs/notifications/send_email',$data);
		 
		
		
		// transfer message
        
		$html ="<p>A Transfer request has been queued with an amount of <strong>Php{$pretty_amount}</strong> via <strong>{$transfer_type}</strong> to <strong>{$proper_name}</strong>.<br/><br/>
				Recipient must acknowledge via email to check the Transfer.</p>
				";
		
        $this->return_json("1","Confirmation",array("html" => $html));
		return;   
	  
    }
	
	public function enter_code() {
		$member_transfer_id = trim($this->input->post("member_transfer_id"));
		 
		$transfer = $this->tracking_model->get_member_transfer_by_id($member_transfer_id);
	
		// get from_member details			
		$from_member_details = $this->members_model->get_member_by_id($transfer->from_member_id); 
		
		$proper_name_from_member = ucfirst($from_member_details->first_name) . " " . ucfirst($from_member_details->middle_name) . " " . ucfirst($from_member_details->last_name);
		
		$transfer_amount = number_format($transfer->amount , 2);
		
		$html = "<p>Pending {$transfer->type} Transfer details: </p>
					
					<fieldset >		
						<div>
							<label class='control-label' for='to_member_name'><strong>From Member Name</strong></label>								
							<input type='text' class='span5' disabled='disabled' placeholder='' name='from_member_name' id='to_member_name' value='{$proper_name_from_member}'/>								
						</div>
						
						<div>
							<label class='control-label' for='transfer_amount'><strong>Amount</strong></label>								
							<input type='text' class='span3' disabled='disabled' placeholder='' name='transfer_amount' id='transfer_amount' value='{$transfer_amount}'/>								
						</div>
						
						<div>
							<label class='control-label' for='transaction_type'><strong>Transaction Type</strong></label>								
							<input type='text' class='span3' disabled='disabled' placeholder='' name='transaction_type' id='transaction_type' value='{$transfer->type}'/>								
						</div>
						
						<div>
							<label class='control-label' for='transaction_date'><strong>Transaction Date</strong></label>								
							<input type='text' class='span4' disabled='disabled' placeholder='' name='transaction_date' id='transaction_date' value='{$transfer->insert_timestamp}'/>								
						</div>
						
						<div>
							<label class='control-label' for='transfer_confirmation_code'><strong>Enter Confirmation Code:</strong></label>								
							<input type='text' class='span4' placeholder='' name='transfer_confirmation_code' id='transfer_confirmation_code' value=''/>								
						</div>
						<span id='confirmation_code_error' style='display:none;' class='label label-important' ></span> 							
					</fieldset>
				";
            
        $this->return_json("1","With pending transfer request",array("html" => $html));
		return;
       				
	}
	
	public function check_confirmation() {
		$confirmation_code = trim($this->input->post("confirmation_code"));
		$member_transfer_id = trim($this->input->post("member_transfer_id"));
	
		$transfer = $this->tracking_model->get_member_transfer_by_id($member_transfer_id);
	
		// check if encoded confirmation code
		if ($transfer->confirmation_code == $confirmation_code) {
			
			$html = "";			
			$this->return_json("1","Correct Code",array("html" => $html));
		
		} else {
		
			$html = "<p>Sorry, you have entered a wrong code.</p>";
			$this->return_json("0","Wrong Code",array("html" => $html));
		
		}		
		
		return;
	
	}
	
	
	public function commit_transfer_to_member() {
		$member_transfer_id = trim($this->input->post("member_transfer_id"));
	
		$transfer = $this->tracking_model->get_member_transfer_by_id($member_transfer_id);
	
		// change status to COMPLETED
		$current_datetime = date("Y-m-d H:i:s");
		
		// add to member logs - change in funds cm_members
		//get details before updating
		$to_member_details = $this->members_model->get_member_by_id($transfer->to_member_id);
		if ($transfer->type == "FUNDS") {
			$to_member_details_before = array("funds"=>$to_member_details->funds);
		} else if ($transfer->type == "GCEP") {
			$to_member_details_before = array("gcep"=>$to_member_details->gcep);
		} else {		
			$to_member_details_before = array("gift_cheques"=>$to_member_details->gift_cheques);
		}
		$details_before = array(
			'id' => $member_transfer_id, 
			'details' => $to_member_details_before
		);
		$details_before = json_encode($details_before);
		
		
		if ($transfer->type == "FUNDS") {
			// credit to member =  funds + amount
			$data = array(
				"funds" => $to_member_details->funds + $transfer->amount
			);
			
		} else if ($transfer->type == "GCEP") {
			// credit to member =  funds + amount
			$data = array(
				"gcep" => $to_member_details->gcep + $transfer->amount
			);
		
		} else {
			$data = array(
				"gift_cheques" => $to_member_details->gift_cheques + $transfer->amount
			);
		}
		
		$this->members_model->update_member($data, "member_id = " . $to_member_details->member_id);
		
	
		// logging after update
		$details_after = array('id' => $member_transfer_id, 'details' => $data);
		$details_after = json_encode($details_after);
		$update_member_funds_logs = array(
			'member_id' => $to_member_details->member_id,
			'module_name' => "UPDATE FUNDS - TRANSFER " . $transfer->type,
			'table_name' => "cm_members",
			'action' => "UPDATE",
			'details_before' => $details_before,
			'details_after' => $details_after,
			'remarks' => ""
		);
		$this->tracking_model->insert_logs('members', $update_member_funds_logs);
		
		
		// add to transfer logs
		$member_transfer_details_before = array(
			"status"=>$transfer->status, 
			"from_member_id"=>$transfer->from_member_id, 
			"to_member_id"=>$transfer->to_member_id, 
			"amount"=>$transfer->amount, 
			"type"=>$transfer->type,
			"update_timestamp"=>$transfer->update_timestamp
		);
		
		$details_before = array('id' => $member_transfer_id, 'details' => $member_transfer_details_before);
		$details_before = json_encode($details_before);
		
		
		$data = array(
            'status' => 'COMPLETED',
            'update_timestamp' => $current_datetime
        );
		
		$where = "member_transfer_id = " . $member_transfer_id;
        $this->tracking_model->update_member_transfers($data, $where);
	
	
		// logging after update
		$details_after = array('id' => $member_transfer_id, 'details' => $data);
		$details_after = json_encode($details_after);
		$update_member_funds_logs = array(
			'member_id' => $to_member_details->member_id,
			'module_name' => "UPDATE MEMBER TRANSFERS - TRANSFER " . $transfer->type,
			'table_name' => "cm_member_transfers",
			'action' => "UPDATE",
			'details_before' => $details_before,
			'details_after' => $details_after,
			'remarks' => ""
		);
		$this->tracking_model->insert_logs('members', $update_member_funds_logs);
		
		$transfer_amount = number_format($transfer->amount , 2);
		
		// get from_member details			
		$from_member_details = $this->members_model->get_member_by_id($transfer->from_member_id); 
		
		$proper_name_from_member = ucfirst($from_member_details->first_name) . " " . ucfirst($from_member_details->middle_name) . " " . ucfirst($from_member_details->last_name);
		
		// get from_member details			
		$to_member_details = $this->members_model->get_member_by_id($transfer->to_member_id); 
		
		$proper_name_to_member = ucfirst($to_member_details->first_name) . " " . ucfirst($to_member_details->middle_name) . " " . ucfirst($to_member_details->last_name);
		
		$transfer_type = strtoupper($transfer->type);
		
		$html = "Congratulations! An amount of <strong>Php{$transfer_amount}</strong> was successfully transferred to the <strong>{$transfer_type}</strong> of  <strong>{$proper_name_to_member}</strong>.";
		
		$proper_member_transfer_id = str_pad($member_transfer_id, 8, "0", STR_PAD_LEFT);
		
		// send email as a receipt to recipient
        $params = array(
            "first_name"=>ucfirst($to_member_details->first_name),            
			"proper_amount"=>$transfer_amount,
			"proper_sender_name"=>$proper_name_from_member,
            "proper_transaction_id"=>$proper_member_transfer_id,
			"proper_transfer_type"=>$transfer_type
        );

        $data = array(
            "email"=>$to_member_details->email,
            "type"=>"transfer_funds_acceptance_receipt",
            "params"=>$params
        );

        //send email to user
        Modules::run('jobs/notifications/send_email',$data);
		
		$proper_name_to_member = ucfirst($to_member_details->first_name) . " " . ucfirst($to_member_details->middle_name) . " " . ucfirst($to_member_details->last_name);		
			
		// send email as a receipt to sender
        $params = array(
            "first_name"=>ucfirst($from_member_details->first_name),            
			"proper_amount"=>$transfer_amount,
			"proper_recipient_name"=>$proper_name_to_member,
            "proper_transaction_id"=>$proper_member_transfer_id,
			"proper_transfer_type"=>$transfer_type
        );

        $data = array(
            "email"=>$from_member_details->email,
            "type"=>"transfer_funds_notification_receipt",
            "params"=>$params
        );

        //send email to user
        Modules::run('jobs/notifications/send_email',$data);
		 
		
		$this->return_json("1","Update Funds",array("html" => $html));
		
		return;
	
	}
	
	
	public function cancel_transfer() {
		$member_transfer_id = trim($this->input->post("member_transfer_id"));
		 
		$transfer = $this->tracking_model->get_member_transfer_by_id($member_transfer_id);
	
		// get from_member details			
		$from_member_details = $this->members_model->get_member_by_id($transfer->from_member_id); 
		
		$proper_name_from_member = ucfirst($from_member_details->first_name) . " " . ucfirst($from_member_details->middle_name) . " " . ucfirst($from_member_details->last_name);
		
		$transfer_amount = number_format($transfer->amount , 2);
		
		$html = "<p>You are to CANCEL the {$transfer->type} Transfer with the following details:</p>
					
					<fieldset >		
						<div>
							<label class='control-label' for='to_member_name'><strong>From Member Name</strong></label>								
							<input type='text' class='span5' disabled='disabled' placeholder='' name='from_member_name' id='to_member_name' value='{$proper_name_from_member}'/>								
						</div>
						
						<div>
							<label class='control-label' for='transfer_amount'><strong>Amount</strong></label>								
							<input type='text' class='span3' disabled='disabled' placeholder='' name='transfer_amount' id='transfer_amount' value='{$transfer_amount}'/>								
						</div>
						
						<div>
							<label class='control-label' for='transaction_type'><strong>Transaction Type</strong></label>								
							<input type='text' class='span3' disabled='disabled' placeholder='' name='transaction_type' id='transaction_type' value='{$transfer->type}'/>								
						</div>
						
						<div>
							<label class='control-label' for='transaction_date'><strong>Transaction Date</strong></label>								
							<input type='text' class='span4' disabled='disabled' placeholder='' name='transaction_date' id='transaction_date' value='{$transfer->insert_timestamp}'/>								
						</div>											
					</fieldset>
					<p>Do you want to continue?</p>
				";
            
        $this->return_json("1","Cancel pending transfer request",array("html" => $html));
		return;
       				
	}
	
	public function proceed_cancel_transfer() {
		$member_transfer_id = trim($this->input->post("member_transfer_id"));
	
		$transfer = $this->tracking_model->get_member_transfer_by_id($member_transfer_id);
	
		// change status to COMPLETED
		$current_datetime = date("Y-m-d H:i:s");
		
		// add to member logs - change in funds cm_members
		//get details before updating
		$to_member_details = $this->members_model->get_member_by_id($transfer->to_member_id);
		if ($transfer->type == "FUNDS") {
			$from_member_details_before = array("funds"=>$this->member->funds);
		} else if ($transfer->type == "GCEP") {
			$from_member_details_before = array("gcep"=>$this->member->gcep);
		} else {		
			$from_member_details_before = array("gift_cheques"=>$this->member->gift_cheques);
		}
		$details_before = array(
			'id' => $member_transfer_id, 
			'details' => $from_member_details_before
		);
		$details_before = json_encode($details_before);
		
		
		if ($transfer->type == "FUNDS") {
			// credit to member =  funds + amount
			$data = array(
				"funds" => $this->member->funds + $transfer->amount
			);
			
		} else if ($transfer->type == "GCEP") {
			// credit to member =  funds + amount
			$data = array(
				"gcep" => $this->member->gcep + $transfer->amount
			);
		
		} else {
			$data = array(
				"gift_cheques" => $this->member->gift_cheques + $transfer->amount
			);
		}
		
		$this->members_model->update_member($data, "member_id = " . $this->member->member_id);
	
		// logging after update
		$details_after = array('id' => $member_transfer_id, 'details' => $data);
		$details_after = json_encode($details_after);
		$update_member_funds_logs = array(
			'member_id' => $this->member->member_id,
			'module_name' => "CANCEL FUNDS TRANSFER " . $transfer->type,
			'table_name' => "cm_members",
			'action' => "UPDATE",
			'details_before' => $details_before,
			'details_after' => $details_after,
			'remarks' => ""
		);
		$this->tracking_model->insert_logs('members', $update_member_funds_logs);
		
		// add to transfer logs
		$member_transfer_details_before = array(
			"from_member_id"=>$transfer->from_member_id, 
			"to_member_id"=>$transfer->to_member_id, 
			"amount"=>$transfer->amount, 
			"type"=>$transfer->type,
			"update_timestamp"=>$transfer->update_timestamp
		);
		
		$details_before = array('id' => $member_transfer_id, 'details' => $member_transfer_details_before);
		$details_before = json_encode($details_before);
		
		
		$data = array(
            'status' => 'CANCELLED',
            'update_timestamp' => $current_datetime
        );
		
		$where = "member_transfer_id = " . $member_transfer_id;
        $this->tracking_model->update_member_transfers($data, $where);
	
	
		// logging after update
		$details_after = array('id' => $member_transfer_id, 'details' => $data);
		$details_after = json_encode($details_after);
		$update_member_funds_logs = array(
			'member_id' => $to_member_details->member_id,
			'module_name' => "UPDATE MEMBER TRANSFERS - CANCEL " . $transfer->type,
			'table_name' => "tr_member_transfers",
			'action' => "UPDATE",
			'details_before' => $details_before,
			'details_after' => $details_after,
			'remarks' => ""
		);
		$this->tracking_model->insert_logs('members', $update_member_funds_logs);
		
		$transfer_amount = number_format($transfer->amount , 2);
		
		// get from_member details			
		$from_member_details = $this->members_model->get_member_by_id($transfer->from_member_id); 
		
		$proper_name_from_member = ucfirst($from_member_details->first_name) . " " . ucfirst($from_member_details->middle_name) . " " . ucfirst($from_member_details->last_name);
		
		$proper_name_to_member = ucfirst($to_member_details->first_name) . " " . ucfirst($to_member_details->middle_name) . " " . ucfirst($to_member_details->last_name);
		
		$transfer_type = strtoupper($transfer->type);
		
		$html = "Commission Transfer of <strong>Php{$transfer_amount}</strong> to <strong>{$proper_name_to_member}</strong> was successfully cancelled and returned to your <strong>{$transfer_type}</strong>.";
		
		$proper_member_transfer_id = str_pad($member_transfer_id, 8, "0", STR_PAD_LEFT);
				
		$proper_name_to_member = ucfirst($to_member_details->first_name) . " " . ucfirst($to_member_details->middle_name) . " " . ucfirst($to_member_details->last_name);		
			
		// send email as a receipt to sender
        $params = array(
            "first_name"=>ucfirst($from_member_details->first_name),            
			"proper_amount"=>$transfer_amount,
			"proper_recipient_name"=>$proper_name_to_member,
            "proper_transaction_id"=>$proper_member_transfer_id,
			"proper_transfer_type"=>$transfer_type
        );

        $data = array(
            "email"=>$from_member_details->email,
            "type"=>"cancel_transfer_funds_notification_receipt",
            "params"=>$params
        );

        //send email to user
        Modules::run('jobs/notifications/send_email',$data);
		
		$this->return_json("1","Cancel Transfer",array("html" => $html));
		
		return;
	
	}
	
	
	public function resend_code() {
		$member_transfer_id = $this->input->post("member_transfer_id");
	
		$transfer = $this->tracking_model->get_member_transfer_by_id($member_transfer_id);
	
		if (empty($transfer)) {
		
			$html = "<p>There is something wrong with this transaction [Transfer ID: {$member_transfer_id}].</p>";
			$this->return_json("0","Empty Transfer info",array("html" => $html));
			
		} else {
			// get from_member details			
			$from_member_details = $this->members_model->get_member_by_id($transfer->from_member_id); 
			
			$proper_name_from_member = ucfirst($from_member_details->first_name) . " " . ucfirst($from_member_details->middle_name) . " " . ucfirst($from_member_details->last_name);
			
			$to_member_details = $this->members_model->get_member_by_id($transfer->to_member_id); 
			
			$proper_name_to_member = ucfirst($to_member_details->first_name) . " " . ucfirst($to_member_details->middle_name) . " " . ucfirst($to_member_details->last_name);
			
			$transfer_amount = number_format($transfer->amount , 2);
			

			$proper_member_transfer_id = str_pad($member_transfer_id, 8, "0", STR_PAD_LEFT);
			
			// send email notification to member who requested
			$params = array(
				"first_name"=>ucfirst($from_member_details->first_name),
				"proper_transaction_id"=>$proper_member_transfer_id,
				"proper_to_member_name"=>$proper_name_to_member,
				"proper_amount"=>$transfer_amount,
				"proper_transfer_type"=>strtoupper($transfer->type),
				"proper_member_transfer_id"=>$proper_member_transfer_id,
				"confirmation_code"=>$transfer->confirmation_code					
			);

			
			$data = array(
				"email"=>$from_member_details->email,
				"type"=>"resend_code_to_member",
				"params"=>$params
			);

			//send email to user
			Modules::run('jobs/notifications/send_email',$data);

		
			// logging after update
			$details_before = array('id' => $transfer->member_transfer_id, 'details' => $params);
			$details_before = json_encode($details_before);
			$update_member_funds_logs = array(
				'member_id' => $this->member->member_id,
				'module_name' => "TRANSFER FUNDS - RESEND CODE",
				'table_name' => "tr_member_transfers",
				'action' => "RESEND CODE",
				'details_before' => $details_before,
				'details_after' => $details_before
			);
			$this->tracking_model->insert_logs('member', $update_member_funds_logs);
			
			$html = "The Confirmation Code was sent successfully to your email: <strong>{$from_member_details->email}</strong>. <br/>Transaction ID : <strong>{$proper_member_transfer_id}</strong>";		
			$this->return_json("1","Resend Transfer Code",array("html" => $html));
		}
		
		return;
	}
	
	public function view_details() {
		$member_transfer_id = trim($this->input->post("member_transfer_id"));
		 
		$transfer = $this->tracking_model->get_member_transfer_by_id($member_transfer_id);
	
		if (empty($transfer)) {
					
			$html = "<p>There is something wrong with this transaction [Transfer ID: {$member_transfer_id}].</p>";
			$this->return_json("1","With pending transfer request",array("html" => $html));
		
		} else {
		
			// get from_member details			
			$from_member_details = $this->members_model->get_member_by_id($transfer->from_member_id); 
			
			$proper_name_from_member = ucfirst($from_member_details->first_name) . " " . ucfirst($from_member_details->middle_name) . " " . ucfirst($from_member_details->last_name);
			
			// get to_member details			
			$to_member_details = $this->members_model->get_member_by_id($transfer->to_member_id); 
			
			$proper_name_to_member = ucfirst($to_member_details->first_name) . " " . ucfirst($to_member_details->middle_name) . " " . ucfirst($to_member_details->last_name);
			
			
			
			$transfer_amount = number_format($transfer->amount, 2);
			
			$html = "<p>{$transfer->type} Transfer details: </p>
						
						<fieldset >		
							<div>
								<label class='control-label' for='from_member_name'><strong>From Member Name</strong></label>								
								<input type='text' class='span5' disabled='disabled' placeholder='' name='from_member_name' id='from_member_name' value='{$proper_name_from_member}'/>								
							</div>
							
							<div>
								<label class='control-label' for='transfer_amount'><strong>Amount</strong></label>								
								<input type='text' class='span3' disabled='disabled' placeholder='' name='transfer_amount' id='transfer_amount' value='{$transfer_amount}'/>								
							</div>
							
							<div>
								<label class='control-label' for='to_member_name'><strong>To Member Name</strong></label>								
								<input type='text' class='span5' disabled='disabled' placeholder='' name='to_member_name' id='to_member_name' value='{$proper_name_to_member}'/>								
							</div>
							
							
							<div>
								<label class='control-label' for='transaction_type'><strong>Transaction Type</strong></label>								
								<input type='text' class='span3' disabled='disabled' placeholder='' name='transaction_type' id='transaction_type' value='{$transfer->type}'/>								
							</div>
							
							<div>
								<label class='control-label' for='transaction_date'><strong>Transaction Date</strong></label>								
								<input type='text' class='span4' disabled='disabled' placeholder='' name='transaction_date' id='transaction_date' value='{$transfer->insert_timestamp}'/>								
							</div>
							<div align='center'>
								<span><strong> - {$transfer->status} - </strong></span>
							</div>				
						</fieldset>
					";
				
			$this->return_json("1","With pending transfer request",array("html" => $html));
	
		}
		
       	return;			
	}
	
}
