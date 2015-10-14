<?php

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Redemption extends Admin_Controller {

        function __construct() {
			parent::__construct();
			// if(!$this->admin_authenticate->is_allowed("voucher_redemption")){
			// redirect('/admin');
			// }
		}
        
        function index() {
            $data = array();
            //$this->load_view(array('view' => 'voucher_redemption/voucher', 'data' => $data));

			$this->template->view('redemption/voucher');

        }
    
		public function check()
		{
			$voucher_code = trim($this->input->post("voucher_code"));

			$voucher = $this->members_model->get_member_voucher_by_code($voucher_code);

			if (is_null($voucher)) 
			{
				echo json_encode(array("status"=>0,"html"=>"Voucher does not exist."));
				return;
			}

			$voucher->status = strtoupper($voucher->status);
			$voucher_details = "<label style='margin-bottom:0; margin-top:5px;'>Validity Period: </label><span>".date("F j, Y",strtotime($voucher->start_timestamp))." to ".date("F j, Y",strtotime($voucher->end_timestamp))."</span>";

			//$voucher_remarks = str_replace('\n', '<br/>',$voucher->remarks);

			$voucher_remarks = $voucher->remarks;
			
			$voucher_description = "";

			$validate_button = "";
			
			$owner_id = $voucher->member_id;
			$owner_name = $voucher->first_name . " " . $voucher->middle_name . " " . $voucher->last_name;
			$owner_name = trim($owner_name);
			$owner_email = $voucher->email;
			$owner_mobile_number = $voucher->mobile_number;

			if ($owner_name=='' && $this->users_model->is_user_allowed("admin/redemption/update_owner_details")) 
				$update_owner_details_button = "<button id='update-owner-details' class='btn btn-large btn-primary'>Update Owner Details</button>";	
			else
				$update_owner_details_button = "";
			
			if ($voucher->status!='REDEEMED' && $this->users_model->is_user_allowed("admin/redemption/update_status"))
				$update_status_button = "<button id='update-status' class='btn btn-large btn-primary'>Update Status</button>";				
			else
				$update_status_button = "";

			$html = "
				<div style='background:#CCC;min-height:350px;padding:20px;'>
				<div class='row-fluid'>
					<div class='span6'>
						<strong>Voucher Code:</strong><br/>
						<h2 style='margin-bottom:10px;'><span id='voucher-code'>{$voucher->voucher_code}</span></h2>
						<strong>Voucher Details:</strong> <br/>
						<h3 style='margin-bottom:10px;'>{$voucher->product_text}</h3>
					</div>
					<div class='span4'>
						<h2 style='font-size:36px; line-height: 50px; text-align:center; color: #991111; background-color:#CDEFAB'><span id='voucher-status'>{$voucher->status}</span></h2>
					</div>
				</div>
				<div class='row-fluid'>
					<div class='span6'>
						<br/>
						<br/>
						<strong>Owner Details:</strong><br/><br/>
						Full Name: <h3 style='margin-bottom:10px;'>{$owner_name}</h3>
						Email: <h3 style='margin-bottom:10px;'>{$owner_email}</h3>
						Mobile Number: <h3 style='margin-bottom:10px;'>{$owner_mobile_number}</h3>
					</div>
					<div class='span4'>
					<strong>Remarks:</strong> <br/><br/>		
					<textarea rows='7' style='width:300px;' readonly='readonly'>{$voucher_remarks}</textarea>
					</div>
				</div>
				<div class='row-fluid'>
						<center>
							{$update_owner_details_button}
							{$update_status_button}
						</center>
				</div>
				</div>";

			echo json_encode(array("status"=>1,"html"=>$html));
			return;
		}
		
		public function update_status()
		{
			$voucher_code = trim($this->input->post("voucher_code"));
			$status = trim($this->input->post("status"));
			$remarks = trim($this->input->post("remarks"));
			
			$voucher = $this->members_model->get_member_voucher_by_code($voucher_code);

			if (is_null($voucher)) 
			{
				echo json_encode(array("status"=>0,"html"=>"Voucher does not exist."));
				return;
			}
			
			if ($voucher->status==$status)
			{
				echo json_encode(array("status"=>0,"html"=>"Invalid update."));
				return;
			}
			
			$old_status = $voucher->status;
			
			$new_remarks = date("Y-m-d H:i:s") . " - " . $status . " by [" . $this->user->username. "] " . $remarks . "\n" . $voucher->remarks;
			
			// create redemption timestamp 
			
			$redemption_code = substr(strtoupper(md5($voucher_code . date("Y-m-d H:i:s"))),1,8);
			
			// change status
			$data = array(
				"status"=>$status,
				"redemption_code"=>$redemption_code,
				"remarks"=>$new_remarks,
				"user_id"=>$this->user->user_id,
				"update_timestamp"=>date("Y-m-d H:i:s")
			);
			$this->members_model->update_member_vouchers($data,"voucher_id = {$voucher->voucher_id}");
			
			//logging of action
			$data_before = array(
				"status"=>$voucher->status,
				"redemption_code"=>$voucher->redemption_code,
				"remarks"=>$voucher->remarks,
				"user_id"=>$voucher->user_id,
				"update_timestamp"=>$voucher->update_timestamp
			);
			
			$details_before = array('id' => $voucher->voucher_id, 'details' => $data_before);
			$details_before = json_encode($details_before);
			
			$details_after = array('id' => $voucher->voucher_id, 'details' => $data);
			$details_after = json_encode($details_after);
			$update_vouchers_log_data = array(
				'user_id' => $this->user->user_id,
				'module_name' => 'MEMBER VOUCHERS',
				'table_name' => 'cm_member_vouchers',
				'action' => 'UPDATE',
				'details_before' => $details_before,
				'details_after' => $details_after,
				'remarks' => "",
			);

			$this->tracking_model->insert_logs('admin', $update_vouchers_log_data);
			
			echo json_encode(array("status"=>1,"html"=>"Voucher status successfully updated from {$old_status} to {$status}"));
			return;
		}
		
		public function update_owner_details()
		{
			$voucher_code = trim($this->input->post("voucher_code"));	
			$last_name = strtoupper(trim($this->input->post("last_name")));
			$first_name = strtoupper(trim($this->input->post("first_name")));
			$middle_name = strtoupper(trim($this->input->post("middle_name")));
			$email = strtoupper(trim($this->input->post("email")));
			$mobile_number = strtoupper(trim($this->input->post("mobile_number")));
			
			// check voucher code
			
			$voucher = $this->members_model->get_member_voucher_by_code($voucher_code);
			
			if (is_null($voucher)) 
			{
				echo json_encode(array("status"=>0,"html"=>"Voucher does not exist."));
				return;
			}			
			
			$data = array(
				"last_name"=>$last_name,
				"first_name"=>$first_name,
				"middle_name"=>$middle_name,
				"email"=>$email,
				"mobile_number"=>$mobile_number
			);
			
			$this->members_model->update_member_vouchers($data,"voucher_id = {$voucher->voucher_id}");			
			
			//logging of action
			$data_before = array(
				"last_name"=>$voucher->last_name,
				"first_name"=>$voucher->first_name,
				"middle_name"=>$voucher->middle_name,
				"email"=>$voucher->email,
				"mobile_number"=>$voucher->mobile_number
			);
			
			$details_before = array('id' => $voucher->voucher_id, 'details' => $data_before);
			$details_before = json_encode($details_before);
			
			$details_after = array('id' => $voucher->voucher_id, 'details' => $data);
			$details_after = json_encode($details_after);
			$update_vouchers_log_data = array(
				'user_id' => $this->user->user_id,
				'module_name' => 'MEMBER VOUCHERS',
				'table_name' => 'cm_member_vouchers',
				'action' => 'UPDATE',
				'details_before' => $details_before,
				'details_after' => $details_after,
				'remarks' => "",
			);

			$this->tracking_model->insert_logs('admin', $update_vouchers_log_data);
			
			echo json_encode(array("status"=>1,"html"=>"Voucher owner details successfully updated."));
			return;									
		}

}

?>