<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Myvouchers extends Site_Controller 
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
		$this->load->library('pager2');	
	}
	
	public $cln_test = 1;
	public $vitalc_test = 1;
	
	public function index() 
	{ 

		$this->page();
	}
	
	public function page()
	{
	
		if (($this->uri->segment(4) == NULL) || trim($this->uri->segment(4) == '')) {
			$segment = "both";
		} else {
			$segment = trim($this->uri->segment(4));
		}
	
		

			$member_id = $this->member->member_id;
			$from_date = $this->input->get_post('from_date');
			$to_date = $this->input->get_post('to_date');
			$status = $this->input->get_post('status');
			$voucher_type = $this->input->get_post('voucher_type');
			
			//$export = $this->input->get_post('export');
			
			$where = "member_id = " . $member_id;
			
			if(!empty($status) && $status != 'ALL')	$where .= " AND status = '" . strtoupper($status) . "'";
			if(!empty($voucher_type) && $voucher_type != 'ALL') {
				// get voucher_type_id
				$voucher_type_details = $this->vouchers_model->get_account_voucher_type_by_code($voucher_type);
				$where .= " AND voucher_type_id = '" . $voucher_type_details->voucher_type_id . "'";
			}
			$from_dt = $from_date;
			$to_dt = $to_date;
			
			if ((!($from_date == "")) && (!($to_date == "")))
				$where .= " AND (date(insert_timestamp) BETWEEN '{$from_date}' AND '{$to_date}') ";
			else if ((!($from_date == "")) && ($to_date == ""))
				$where .= " AND insert_timestamp >= '{$from_date}'";
			else if ((!($to_date == "")) && ($from_date == ""))
				$where .= " AND insert_timestamp <= '{$to_date}'";
					
			// search filters
			$get_data = "?status=" . $status . "&voucher_type=" . $voucher_type . "&from_date=" . $from_date . "&to_date=" . $to_date;
			
			$total_items = $this->vouchers_model->get_member_account_vouchers_count($where);

			// set pagination
			$config = array(
				'pagination_url' => '/members/myvouchers/page/vitalc',
				'total_items' => $total_items,
				'per_page' => 40,
				'uri_segment' => 5,
			);
		
			$this->pager->set_config($config);

			// display paged transaction
			//if(($this->pager->per_page != null) && ($this->pager->offset != null))
			//{
				$limit = array('rows' => $this->pager->per_page, 'offset' => $this->pager->offset);
			//}
				
			$member_vouchers = $this->vouchers_model->get_member_account_vouchers($where, $limit,"insert_timestamp DESC");	
		
			$between_timestamps = $from_date . " - " . $to_date;
			if (trim($between_timestamps) == "-") 
				$between_timestamps = "n/a";
				
		$this->template->between_timestamps = strtoupper($between_timestamps);	
		$this->template->member_vouchers = $member_vouchers;
		$this->template->member_id = $member_id;
		$this->template->from_date = $from_date;
		$this->template->to_date = $to_date;
		$this->template->voucher_type = $voucher_type;
		$this->template->status = $status;
		$this->template->get_data = $get_data;
				
	
		//// CLN
		//	
		$from_date_cln = $this->input->get_post('from_date');
		$to_date_cln = $this->input->get_post('to_date');
		$status_cln = $this->input->get_post('status');
		$voucher_type_cln = $this->input->get_post('voucher_type');
		//
		$where = "member_id = " . $member_id;
		//
		////if(!empty($status) && $status != 'ALL')	$where .= " AND status = '" . strtoupper($status) . "'";
		////if(!empty($voucher_type) && $voucher_type != 'ALL') {
		////	// get voucher_type_id
		////	$voucher_type_details = $this->vouchers_model->get_account_voucher_type_by_code($voucher_type);
		////	$where .= " AND voucher_type_id = '" . $voucher_type_details->voucher_type_id . "'";
		////}
		////$from_dt = $from_date;
		////$to_dt = $to_date;
		////	
		////if ((!($from_date == "")) && (!($to_date == "")))
		////	$where .= " AND (date(insert_timestamp) BETWEEN '{$from_date}' AND '{$to_date}') ";
		////else if ((!($from_date == "")) && ($to_date == ""))
		////	$where .= " AND insert_timestamp >= '{$from_date}'";
		////else if ((!($to_date == "")) && ($from_date == ""))
		////	$where .= " AND insert_timestamp <= '{$to_date}'";
		//			
		// search filters
		$get_data_cln = "?status=" . $status_cln . "&voucher_type=" . $voucher_type_cln . "&from_date=" . $from_date_cln . "&to_date=" . $to_date_cln;
		//
		$total_items = $this->members_model->get_member_vouchers_count($where);
        //
		//// set pagination
		$config2 = array(
			'pagination_url' => '/members/myvouchers/page/cln',
			'total_items' => $total_items,
			'per_page' => 40,
			'uri_segment' => 5,
		);
	    //
		$this->pager2->set_config($config2);
        //
		//// display paged transaction
		////if(($this->pager->per_page != null) && ($this->pager->offset != null))
		////{
			$limit = array('rows' => $this->pager2->per_page, 'offset' => $this->pager2->offset);
		////}
		//	
		$member_vouchers = $this->members_model->get_member_vouchers($where,$limit,"insert_timestamp DESC");		
	    //
		$between_timestamps_cln = $from_date_cln . " - " . $to_date_cln;
		if (trim($between_timestamps_cln) == "-") 
			$between_timestamps_cln = "n/a";
				
		$this->template->between_timestamps_cln = strtoupper($between_timestamps_cln);	
		$this->template->member_cln_vouchers = $member_vouchers;
	    
		$this->template->from_date_cln = $from_date_cln;
		$this->template->to_date_cln = $to_date_cln;
		$this->template->voucher_type_cln = $voucher_type_cln;
		$this->template->status_cln = $status_cln;
		$this->template->get_data_cln = $get_data_cln;	
	
		//My Vital-C Voucher History
		//$where = "to_member_id = '34937' OR from_member_id ='34937'";

		//get cpv id
		$cpv_id = 0;
		$cpv = $this->vouchers_model->get_account_voucher_type_by_code('CPV');
		if(sizeof($cpv) >0)
		{
			$cpv_id = $cpv->voucher_type_id;
		}
		$this->template->cpv_id = $cpv_id;


		$my_voucher_history = $this->get_member_voucher_history($member_id);
		$this->template->my_voucher_history = $my_voucher_history;
		
		$this->template->current_page = 'myvouchers';
		$this->template->view('vouchers/dashboard');

	}
	
	
	public function show_vitalc(){
		$page = $this->input->post('page');
		
		$where = "member_id = {$this->member->member_id}";
		//$where ="";
		$total_items = $this->vouchers_model->get_member_account_vouchers_count($where);
		
		// initialize pagination class
		// set pagination data
		$config = array(
		    'pagination_url' => '/members/myvouchers/view/vitalc/',
		    'total_items' => $total_items,
		    'per_page' => 2,
		    'uri_segment' => 5,
		);
		$this->pager->set_config($config);
		$this->pager->current_page = $page;
		$limit = array('rows' => $this->pager->per_page, 'offset' => $this->pager->per_page * ($page - 1));
			
		$data= array(
			'member_id'=>$this->member->member_id			
		);
		
		$member_vouchers = $this->vouchers_model->get_member_account_vouchers($data,$limit,"insert_timestamp DESC");		
		//$search_url = "?search_option=" . $search_by . "&search_string=" . $search_text;	
		
		$this->vitalc_test = $page;
		
		$data = array(
			'member_vouchers' =>$member_vouchers,
		);
			
        $view = $this->load->view('members/vouchers/vitalc_vouchers', $data, TRUE);
        
        echo json_encode(array('status'=>1,'html'=>$view, 'vitalc_current_page'=>$this->vitalc_test, 'cln_current_page'=>$this->cln_test));
        return;
    }	
	
	
	public function show_cln(){
		$page = $this->input->post('page');
		
		$where = "member_id = {$this->member->member_id}";
	
		$total_items = $this->members_model->get_member_vouchers_count($where);
		
		// initialize pagination class
		// set pagination data
		$config = array(
		    'pagination_url' => '/members/myvouchers/view/cln/',
		    'total_items' => $total_items,
		    'per_page' => 40,
		    'uri_segment' => 5,
		);
		$this->pager->set_config($config);
		$this->pager->current_page = $page;
		$limit = array('rows' => $this->pager->per_page, 'offset' => $this->pager->per_page * ($page - 1));
		
		$data= array(
			'member_id'=>$this->member->member_id			
		);
		
		$member_vouchers = $this->members_model->get_member_vouchers($data,$limit,"insert_timestamp DESC");		
		//
		//$voucher_raffle_entries = array();
		//foreach($member_vouchers as $mv){
		//	$raffle_entries = $this->raffles_model->get_raffle_entries(array("reference_info"=>$mv->transaction_id));
		//	if(count($raffle_entries) >0)
		//		$voucher_raffle_entries[$mv->voucher_id] = $raffle_entries;
		//}
		//
		//$this->template->voucher_raffle_entries = $voucher_raffle_entries;
		$this->template->member_vouchers = $member_vouchers;
		
		$this->cln_test = $page;
		
		$data = array(
			'member_vouchers' =>$member_vouchers,
		);
			
        $view = $this->load->view('members/vouchers/cln_vouchers', $data, TRUE);
        
        echo json_encode(array('status'=>1,'html'=>$view, 'cln_current_page'=>$this->cln_test, 'vitalc_current_page'=>$this->vitalc_test));
        return;
    }	
	
	
	public function display($voucher_code = 0) {

        $this->template->voucher_code = $voucher_code;
        $this->template->view('members/vouchers/display');
    }

    public function display_pdf($voucher_code = 0) {
        $this->load->library('mpdf');

		$terms_and_conditions = $this->contents_model->get_content_by_slug("voucher_terms_and_conditions");
		$footer = $this->contents_model->get_content_by_slug("voucher_footer");

		// get voucher_details
		$voucher_details = $this->members_model->get_member_voucher_by_code($voucher_code);
	
		if (empty($voucher_details)) {
			// missing voucher details
			// TODO :: what to do if missing voucher_details (just in case)
			
		} else {
			// check if last_name or first_name is null
			if (($voucher_details->last_name == NULL) || (trim($voucher_details->last_name) == "") || ($voucher_details->first_name == NULL) || (trim($voucher_details->first_name) == "")) {
				// get member_details using member_id
				$member_details = $this->members_model->get_member_by_id($voucher_details->member_id);
				
				$proper_owner_name = $member_details->last_name . ", " . $member_details->first_name . " " . $member_details->middle_name;				
			} else {
				$proper_owner_name = $voucher_details->last_name . ", " . $voucher_details->first_name . " " . $voucher_details->middle_name;
			}
			
			$proper_member_since = "";
			// member details if voucher is from member passed to another person
			if (($voucher_details->member_id == 0) || ($voucher_details->member_id == NULL) || (trim($voucher_details->member_id) == "")) {
				$proper_member_name = "N/A";
			} else {
				$member_details = $this->members_model->get_member_by_id($voucher_details->member_id);
				$proper_member_name = $member_details->last_name . ", " . $member_details->first_name . " " . $member_details->middle_name;	
				$proper_member_since = date("F j, Y", strtotime($member_details->insert_timestamp));
			}			
		}
		
		$raffle_entries = $this->raffles_model->get_raffle_entries(array("reference_info"=>$voucher_details->transaction_id));
		
		$data = array (
            "voucher_code" => $voucher_code,
			"proper_owner_name" => $proper_owner_name,
			"proper_member_name" => $proper_member_name,
			"proper_member_since" => $proper_member_since,
			"voucher_details"=> $voucher_details,
			"voucher_terms_and_conditions" => $terms_and_conditions->body,
			"raffle_entries" => $raffle_entries
        );
 		
		// for testing purposes
        
        $this->mpdf->WriteHTML($this->load->view('members/myvouchers/display',$data,TRUE));
		$footer = "<hr/><div align='center' style='font-size:10px;'>{$footer->body}</div>";
		$this->mpdf->SetHTMLFooter($footer);
		
        $this->mpdf->Output('voucher_' . $voucher_code, 'I');
		
	}
	
	public function check_account(){
  	
   		$account_id = trim($this->input->post('account_id'));
		
		if (($account_id == '') || ($account_id == '0') || ($account_id == NULL)) {
			$html = "Empty Account ID!";
			$this->return_json("0","Empty Account ID ",array("html" => $html));
			return;
		}
		
		// get account details
		$member_account_details = $this->members_model->get_member_account_by_account_id($account_id);
		
		if ($member_account_details->member_id == '1162') {
			$html = "<p><strong>NOTE:</strong> You are not allowed to transfer a voucher to Account ID <strong>{$account_id}</strong> - (Company Account).</p>";
					
			$this->return_json("0","Company Account ",array("html" => $html));
			return;
		}
		
		// check if same member
		if ($member_account_details->member_id == $this->member->member_id) {
			$html = "<p><strong>NOTE:</strong> Not allowed - Same sender and recipient.</p>";
					
			$this->return_json("0","Same Member Error.",array("html" => $html));
			return;
		}
		
		if (empty($member_account_details)) {
			$html = "Account ID not found!";
			$this->return_json("0","Invalid Account ID ",array("html" => $html));
		} else {
		
			// get member details
			$member_details = $this->members_model->get_member_by_id($member_account_details->member_id);
		
			$proper_name = strtoupper($member_details->last_name) . ", " . strtoupper($member_details->first_name) . " " . strtoupper($member_details->middle_name);
			
			$html = "<p><label>Account Summary:</label>	
						<table class='table table-striped table-bordered'>
							<thead>
							</thead>
							<tbody>
								<tr>
									<td style='width:120px;'><label><strong>Member Id</strong></label></td>
									<td><label class=''>{$member_account_details->member_id}</label></td>		
								</tr>
								<tr>
									<td style='width:120px;'><label><strong>Name</strong></label></td>
									<td><label class=''>{$proper_name}</label></td>		
								</tr>	
								<tr>
									<td style='width:120px;'><label><strong>Email</strong></label></td>
									<td><label class=''>{$member_details->email}</label></td>		
								</tr>				
							</tbody>
						</table>				
						<label>Do you want to proceed?</label>
					</p>";
					
					
			$this->return_json("1","Valid Account ID ",array("html" => $html));
		}
		return;			
	}
	
	public function execute_transfer() {
	
        $account_id = trim($this->input->post("account_id"));
        $voucher_id = abs($this->input->post("voucher_id"));
		
		// get voucher details
		$voucher_details = $this->vouchers_model->get_member_account_voucher_by_id($voucher_id);
		if (empty($voucher_details)) {
			$html = "Error: Voucher not found.";
			$this->return_json("0","Voucher Not Found",array("html" => $html));
			return;	
		}
		
		// get member_account details
		$to_member_account_details = $this->members_model->get_member_account_by_account_id($account_id);
				
		if (empty($to_member_account_details)) {
			$html = "Error: Account Details not found.";
			$this->return_json("0","Account ID Not Found",array("html" => $html));
			return;	
		}
		
		// get member details
		$to_member_details = $this->members_model->get_member_by_id($to_member_account_details->member_id);
		$proper_name = $to_member_details->first_name . " " . $to_member_details->last_name;
		
		$current_datetime = date("Y-m-d H:i:s");
		// insert request to tr_member_transfers, status as pending
		$confirmation_code = md5($this->member->member_id."_".$to_member_details->member_id."_".$current_datetime);
        $confirmation_code = strtoupper(substr($confirmation_code, 0, 8));
		
		// get details before update	
		$details_before = $voucher_details;	
		$details_before = json_encode($details_before);	
		
        $data = array(            
            'to_member_id' => $to_member_account_details->member_id,			
            'confirmation_code' => $confirmation_code,
            'status' => 'TRANSFERRING',
			'updated_timestamp' => $current_datetime
        );
        $this->vouchers_model->update_member_account_vouchers($data, "voucher_id = " . $voucher_id);
		
		
		// logs 
		$details_after = array('voucher_id' =>  $voucher_id, 'details' => $data);
		$details_after = json_encode($details_after);
		$log_data = array(
			'voucher_id' => $voucher_id,
			'from_member_id' => $voucher_details->member_id,
			'to_member_id' =>  $to_member_account_details->member_id,				
			'details_before' => $details_before,
			'details_after' => $details_after
		);
		$this->tracking_model->insert_member_voucher_transfer_logs($log_data);
		
			// log to tr_member_logs
		$update_member_logs = array(
			'member_id' => $to_member_details->member_id,
			'module_name' => "UPDATE VOUCHER TRANSFER :: TRANSFERRING",
			'table_name' => "cm_member_account_vouchers",
			'action' => "UPDATE",
			'details_before' => $details_before,
			'details_after' => $details_after,
			'remarks' => ""
		);
		$this->tracking_model->insert_logs('members', $update_member_logs);
		
	
		$base_url = $this->config->item('base_url') . "/members/myvouchers";
		
		// send email notification to recipient
        $params = array(
			"voucher_code"=>strtoupper($voucher_details->voucher_code),
            "link"=>$base_url,						
            "confirmation_code"=>$confirmation_code,
			"from_first_name"=>ucfirst($this->member->first_name),
            "from_last_name"=>ucfirst($this->member->last_name),
			"proper_name_to_member"=>$proper_name				
        );

        $data = array(
            "email"=>$this->member->email,
            "type"=>"transfer_voucher_email",
            "params"=>$params
        );

        //send email to user
        Modules::run('jobs/notifications/send_email',$data);
		
		
		// transfer message
        
		$html ="<p>Your Transfer Request has been queued.<br/><br/>
				You must enter the Confirmation Code sent to you via Email to complete the transaction.</p>
				";
		
        $this->return_json("1","Confirmation",array("html" => $html));
		return;   
	  
    }
	
	public function enter_code() {
		$voucher_id = trim($this->input->post("voucher_id"));
		 
		$transfer = $this->vouchers_model->get_member_account_voucher_by_id($voucher_id);
	
		// get current owner details			
		$current_member_details = $this->members_model->get_member_by_id($transfer->member_id); 
		
		$proper_name_from_member = ucfirst($current_member_details->first_name) . " " . ucfirst($current_member_details->middle_name) . " " . ucfirst($current_member_details->last_name);
		
		// get current owner details			
		$to_member_details = $this->members_model->get_member_by_id($transfer->to_member_id); 
		
		$proper_name_to_member = ucfirst($to_member_details->first_name) . " " . ucfirst($to_member_details->middle_name) . " " . ucfirst($to_member_details->last_name);
		
		$voucher_type_details = $this->vouchers_model->get_account_voucher_type_by_id($transfer->voucher_type_id);
		
		$html = "<p>Pending Voucher Transfer details: </p>
					
					<fieldset >		
						<div>
							<label class='control-label' for='member_name'><strong>From Member Name</strong></label>								
							<input type='text' class='span5' disabled='disabled' placeholder='' name='from_member_name' id='to_member_name' value='{$proper_name_from_member}'/>								
						</div>
						
						<div>
							<label class='control-label' for='voucher_code'><strong>Voucher Code</strong></label>								
							<input type='text' class='span3' disabled='disabled' placeholder='' name='voucher_code' id='voucher_code' value='{$transfer->voucher_code}'/>								
						</div>
						
						<div>
							<label class='control-label' for='voucher_type'><strong>Voucher Type</strong></label>								
							<input type='text' class='span3' disabled='disabled' placeholder='' name='voucher_type' id='voucher_type' value='{$voucher_type_details->code}'/>								
						</div>
						
						<div>
							<label class='control-label' for='to_member_name'><strong>To Member Name</strong></label>								
							<input type='text' class='span4' disabled='disabled' placeholder='' name='to_member_name' id='to_member_name' value='{$proper_name_to_member}'/>								
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
		$voucher_id = trim($this->input->post("voucher_id"));
	
		$transfer = $this->vouchers_model->get_member_account_voucher_by_id($voucher_id);
	
		// check if encoded confirmation code
		if ($transfer->confirmation_code == strtoupper($confirmation_code)) {
			
			$html = "";			
			$this->return_json("1","Correct Code",array("html" => $html));
		
		} else {
		
			$html = "<p>Sorry, you have entered a wrong code.</p>";
			$this->return_json("0","Wrong Code",array("html" => $html));
		
		}		
		
		return;
	
	}
	
	
	public function commit_transfer_to_member() {
		$voucher_id = trim($this->input->post("voucher_id"));
	
		$transfer = $this->vouchers_model->get_member_account_voucher_by_id($voucher_id);
	
		// change status to TRANSFERRING
		$current_datetime = date("Y-m-d H:i:s");		
		
		// get from_member details			
		$from_member_details = $this->members_model->get_member_by_id($transfer->member_id); 
		
		$proper_name_from_member = ucfirst($from_member_details->first_name) . " " . ucfirst($from_member_details->middle_name) . " " . ucfirst($from_member_details->last_name);
		
		// get from_member details			
		$to_member_details = $this->members_model->get_member_by_id($transfer->to_member_id); 
		
		$proper_name_to_member = ucfirst($to_member_details->first_name) . " " . ucfirst($to_member_details->middle_name) . " " . ucfirst($to_member_details->last_name);
		
		// =======
		// LOGGING
		// get details before update	
		$details_before = $transfer;	
		$details_before = json_encode($details_before);	
		
		// update member_id = to_member_id
		 $data = array(            
			'member_id' => $transfer->to_member_id,			
            'to_member_id' => 0,			            
            'status' => 'ACTIVE',
			'transfer_count' => $transfer->transfer_count++,
			'updated_timestamp' => $current_datetime
        );
        $this->vouchers_model->update_member_account_vouchers($data, "voucher_id = " . $voucher_id);
		
		// logs 
		$details_after = array('voucher_id' =>  $voucher_id, 'details' => $data);
		$details_after = json_encode($details_after);
		$log_data = array(
			'voucher_id' => $voucher_id,
			'from_member_id' => $transfer->member_id,
			'to_member_id' =>  $transfer->to_member_id,				
			'details_before' => $details_before,
			'details_after' => $details_after
		);
		$this->tracking_model->insert_member_voucher_transfer_logs($log_data);
		
		
		// log to tr_member_logs
		$update_member_logs = array(
			'member_id' => $to_member_details->member_id,
			'module_name' => "UPDATE VOUCHER TRANSFER :: SUCCESSFUL",
			'table_name' => "cm_member_account_vouchers",
			'action' => "UPDATE",
			'details_before' => $details_before,
			'details_after' => $details_after,
			'remarks' => ""
		);
		$this->tracking_model->insert_logs('members', $update_member_logs);		
		// END LOGGING
		// ===========
		
		$html = "Congratulations! Voucher {$transfer->voucher_code} was successfully transferred to <strong>{$proper_name_to_member}</strong>.";
		
		$proper_member_voucher_id = str_pad($voucher_id, 8, "0", STR_PAD_LEFT);
		
		// send email as a receipt to recipient
        $params = array(
            "first_name"=>ucfirst($to_member_details->first_name),     
			"voucher_code"=>strtoupper($transfer->voucher_code),     			
			"proper_sender_name"=>$proper_name_from_member,
            "proper_voucher_id"=>$proper_member_voucher_id
        );
    
        $data = array(
            "email"=>$to_member_details->email,
            "type"=>"transfer_voucher_acceptance_receipt",
            "params"=>$params
        );
    
        //send email to user
        Modules::run('jobs/notifications/send_email',$data);
			
		// send email as a receipt to sender
        $params = array(
            "first_name"=>ucfirst($from_member_details->first_name),
			"voucher_code"=>strtoupper($transfer->voucher_code), 		
			"proper_recipient_name"=>$proper_name_to_member,
            "proper_voucher_id"=>$proper_member_voucher_id			
        );
    
        $data = array(
            "email"=>$from_member_details->email,
            "type"=>"transfer_voucher_notification_receipt",
            "params"=>$params
        );
    
        //send email to user
        Modules::run('jobs/notifications/send_email',$data);
		 
		
		$this->return_json("1","Update Voucher",array("html" => $html));
		
		return;
	
	}
	
	public function cancel_transfer() {
		$voucher_id = trim($this->input->post("voucher_id"));
		 
		$transfer = $this->vouchers_model->get_member_account_voucher_by_id($voucher_id);
	
		// get current owner details			
		$current_member_details = $this->members_model->get_member_by_id($transfer->member_id); 
		
		$proper_name_from_member = ucfirst($current_member_details->first_name) . " " . ucfirst($current_member_details->middle_name) . " " . ucfirst($current_member_details->last_name);
		
		// get current owner details			
		$to_member_details = $this->members_model->get_member_by_id($transfer->to_member_id); 
		
		$proper_name_to_member = ucfirst($to_member_details->first_name) . " " . ucfirst($to_member_details->middle_name) . " " . ucfirst($to_member_details->last_name);
		
		$voucher_type_details = $this->vouchers_model->get_account_voucher_type_by_id($transfer->voucher_type_id);
		
		$html = "<p>You are to cancel the Voucher Transfer with the following details: </p>
					
					<fieldset >		
						<div>
							<label class='control-label' for='member_name'><strong>From Member Name</strong></label>								
							<input type='text' class='span5' disabled='disabled' placeholder='' name='from_member_name' id='to_member_name' value='{$proper_name_from_member}'/>								
						</div>
						
						<div>
							<label class='control-label' for='voucher_code'><strong>Voucher Code</strong></label>								
							<input type='text' class='span3' disabled='disabled' placeholder='' name='voucher_code' id='voucher_code' value='{$transfer->voucher_code}'/>								
						</div>
						
						<div>
							<label class='control-label' for='voucher_type'><strong>Voucher Type</strong></label>								
							<input type='text' class='span3' disabled='disabled' placeholder='' name='voucher_type' id='voucher_type' value='{$voucher_type_details->code}'/>								
						</div>
						
						<div>
							<label class='control-label' for='to_member_name'><strong>To Member Name</strong></label>								
							<input type='text' class='span4' disabled='disabled' placeholder='' name='to_member_name' id='to_member_name' value='{$proper_name_to_member}'/>								
						</div>						
					</fieldset>
					
					<p>Do you want to proceed?</p>
				";
            
        $this->return_json("1","Cancel transfer request",array("html" => $html));
		return;      				
	}
	
	public function proceed_cancel_transfer() {
		$voucher_id = trim($this->input->post("voucher_id"));
	
		$transfer = $this->vouchers_model->get_member_account_voucher_by_id($voucher_id);
	
		// change status to ACTIVE
		$current_datetime = date("Y-m-d H:i:s");		
		
		// get from_member details			
		$from_member_details = $this->members_model->get_member_by_id($transfer->member_id); 
		
		$proper_name_from_member = ucfirst($from_member_details->first_name) . " " . ucfirst($from_member_details->middle_name) . " " . ucfirst($from_member_details->last_name);
		
		// get from_member details			
		$to_member_details = $this->members_model->get_member_by_id($transfer->to_member_id); 
		
		$proper_name_to_member = ucfirst($to_member_details->first_name) . " " . ucfirst($to_member_details->middle_name) . " " . ucfirst($to_member_details->last_name);
		
		// =======
		// LOGGING
		// get details before update	
		$details_before = $transfer;	
		$details_before = json_encode($details_before);	
		
		// update member_id = to_member_id
		 $data = array(            
			'member_id' => $transfer->member_id,			
            'to_member_id' => 0,			            
            'status' => 'ACTIVE',
			'confirmation_code' => '',
			'updated_timestamp' => $current_datetime
        );
        $this->vouchers_model->update_member_account_vouchers($data, "voucher_id = " . $voucher_id);
		
		// logs 
		$details_after = array('voucher_id' =>  $voucher_id, 'details' => $data);
		$details_after = json_encode($details_after);
		$log_data = array(
			'voucher_id' => $voucher_id,
			'from_member_id' => $transfer->member_id,
			'to_member_id' =>  $transfer->to_member_id,				
			'details_before' => $details_before,
			'details_after' => $details_after
		);
		$this->tracking_model->insert_member_voucher_transfer_logs($log_data);
		
		
		// log to tr_member_logs
		$update_member_logs = array(
			'member_id' => $transfer->member_id,
			'module_name' => "CANCEL VOUCHER TRANSFER :: SUCCESSFUL",
			'table_name' => "cm_member_account_vouchers",
			'action' => "CANCEL",
			'details_before' => $details_before,
			'details_after' => $details_after,
			'remarks' => ""
		);
		$this->tracking_model->insert_logs('members', $update_member_logs);		
		// END LOGGING
		// ===========
		
		$html = "You have successfully cancelled the Voucher Transfer to <strong>{$proper_name_to_member}</strong> with code </strong>{$transfer->voucher_code}</strong>.";
		
		$proper_member_voucher_id = str_pad($voucher_id, 8, "0", STR_PAD_LEFT);
		
		//// send email as a receipt to recipient
        //$params = array(
        //    "first_name"=>ucfirst($to_member_details->first_name),     
		//	"voucher_code"=>strtoupper($transfer->voucher_code),     			
		//	"proper_sender_name"=>$proper_name_from_member,
        //    "proper_voucher_id"=>$proper_member_voucher_id
        //);
    
        //$data = array(
        //    "email"=>$to_member_details->email,
        //    "type"=>"cancel_transfer_voucher_acceptance_receipt",
        //    "params"=>$params
        //);
        //
        ////send email to user
        //Modules::run('jobs/notifications/send_email',$data);
			
		// send email as a receipt to sender
        $params = array(
            "first_name"=>ucfirst($from_member_details->first_name),
			"voucher_code"=>strtoupper($transfer->voucher_code), 		
			"proper_recipient_name"=>$proper_name_to_member,
            "proper_voucher_id"=>$proper_member_voucher_id			
        );
    
        $data = array(
            "email"=>$from_member_details->email,
            "type"=>"cancel_transfer_voucher_notification_receipt",
            "params"=>$params
        );
    
        //send email to user
        Modules::run('jobs/notifications/send_email',$data);
		 
		
		$this->return_json("1","Update Voucher",array("html" => $html));
		
		return;
	
	}

	private function get_member_voucher_history($member_id)
	{
		//echo $member_id;
		$where = "from_member_id = '$member_id' OR to_member_id = '$member_id'";
		//$where = "from_member_id = '$member_id'";
		$voucher_transfers = $this->tracking_model->get_member_voucher_transfer_logs($where);
		$vouchers = array();
		$voucher_id = '';

		if( sizeof($voucher_transfers) > 0)
		{
			foreach($voucher_transfers as $voucher_transfer)
			{
				$details_before = json_decode($voucher_transfer->details_before);
				$details_after = json_decode($voucher_transfer->details_after);
				
				$tmp = array();
				$tmp = (object)$tmp;

				// get voucher details
				$voucher = $this->vouchers_model->get_member_account_vouchers(array('voucher_id'=>$voucher_transfer->voucher_id));
				$tmp->voucher_code = $voucher[0]->voucher_code;

				// get voucher type
				$voucher_type = $this->vouchers_model->get_account_voucher_type_by_id($voucher[0]->voucher_type_id);
				$tmp->voucher_type = $voucher_type->code;

				// transfer_date 
				$tmp->transfer_timestamp = $voucher_transfer->insert_timestamp;

				//check if receiving
				$transfer_to = $this->members_model->get_member_by_id($voucher_transfer->to_member_id);
				if($voucher_transfer->to_member_id == $member_id )
				{
					if($details_after->details->status == 'ACTIVE' && $details_after->details->member_id == $member_id)
					{	
						$tmp->transfer_status = "RECEIVED";

						// get_transfer_account
						$received_from = $this->members_model->get_member_by_id($voucher_transfer->from_member_id);
						$tmp->received_from = $transfer_to->first_name.' '.$transfer_to->last_name;
						$tmp->transfer_to = '';
						$vouchers[] = $tmp;
					}
					continue;
				}

				// get_transfer_account
				$tmp->transfer_to = $transfer_to->first_name.' '.$transfer_to->last_name;
				$tmp->received_from = '';
				// transfer_status
				if($details_after->details->status == 'ACTIVE' && $member_id != $details_after->details->member_id)
					$tmp->transfer_status = "TRANSFER SUCCESSFUL";
				elseif($details_after->details->status == 'ACTIVE' && $member_id == $details_after->details->member_id)
					$tmp->transfer_status = "TRANSFER CANCELLED";
				else
					$tmp->transfer_status = $details_after->details->status;
					

				$vouchers[] = $tmp;
			}
			return $vouchers;
		}
		else
			return;
		
	}

	public function get_voucher_history()
	{
		$voucher_id = $this->input->post('voucher_id');

		$member_id = $this->member->member_id;
		//get voucher info
		$voucher = $this->vouchers_model->get_member_account_vouchers(array('voucher_id'=>$voucher_id));
		if(sizeof($voucher) > 0)
		{
			$voucher = $voucher[0];

			//get voucher logs for the voucher id
			$voucher_logs = array();
			$voucher_logs_raw = $this->tracking_model->get_member_voucher_transfer_logs(array('voucher_id'=>$voucher_id));

			if(sizeof($voucher_logs_raw) > 0)
			{
				foreach($voucher_logs_raw as $voucher_log_raw)
				{
					$details_after = json_decode($voucher_log_raw->details_after);
					
					if(!isset($details_after->details->member_id))
						$details_after->details->member_id = $voucher_log_raw->from_member_id;


					if($voucher_log_raw->from_member_id != $details_after->details->member_id && $details_after->details->status == 'ACTIVE')
					{	
						$tmp = array();
						$tmp = (object)$tmp;

						//get from member
						$voucher_from = $this->members_model->get_member_by_id($voucher_log_raw->from_member_id);
						$tmp->from = $voucher_from->first_name.' '.$voucher_from->last_name;

						//get to member
						$voucher_to = $this->members_model->get_member_by_id($details_after->details->member_id);
						$tmp->to = $voucher_to->first_name.' '.$voucher_to->last_name;

						$tmp->insert_timestamp = $voucher_log_raw->insert_timestamp;
						
						$voucher_logs[] = $tmp;
					}
				}
			}

			if  (($voucher->status == 'PENDING') || ($voucher->status == 'TRANSFERRING'))  {
				$status =  "<span class='status label label-warning'>{$voucher->status}</span>";
			} else if (($voucher->status == 'INACTIVE') || ($voucher->status == 'VOID')) {
				$status =  "<span class='status label label-important'>{$voucher->status}</span>";
			} else {
				$status =  "<span class='status label label-success'>{$voucher->status}</span>";
			}


			$this->return_json(1,'Ok',array('voucher_logs'=>$voucher_logs,'status'=>$status));
		}
		else
		{
			$this->return_json(1,'Invalid Voucher ID');
		}
		return;
	}

	public function get_voucher_product()
	{
		$voucher_id = $this->input->post('voucher_id');
		$product_name = '';

		$voucher_product = $this->vouchers_model->get_member_account_voucher_products(array('voucher_id'=>$voucher_id));
		
		if(sizeof($voucher_product)>0)
		{
			$product_details = $this->items_model->get_products(array('product_id'=>$voucher_product[0]->product_id));
			if(sizeof($product_details) >0 )
			{
				$product_name = $product_details[0]->product_name;
			}
		}
		else
		{
			$this->return_json(1,'No product found');
			return;
		}

		$this->return_json(1,$product_name);
		return;

	}
}

