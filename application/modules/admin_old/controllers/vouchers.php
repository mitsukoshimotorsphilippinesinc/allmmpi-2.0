<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Vouchers extends Systems_Controller {
	
	function __construct()
	{
		parent::__construct();

		$this->load->model('vouchers_model');
		// load pager library
		$this->load->library('pager');
		$this->set_navigation('voucher_products');
	}

	public function index()
	{
		$this->vouchers();
	}

	public function vouchers()
	{
		
		$search_by = trim($this->input->get("search_option"));
		$search_text = trim($this->input->get("search_string"));
		
		$search_url = "";
		$searchBy = "product name";
			
		if (($search_text == "") || empty($search_text)) {
			$where = NULL;					
		} else {	
						
			if ($search_by == "voucher_type") {
				$searchBy = "type";
				$where = "code LIKE LOWER('%{$search_text}%') OR name LIKE LOWER('%{$search_text}%')";
				$voucher_type_details = $this->vouchers_model->get_is_voucher_types($where);

				if (count($voucher_type_details) == 0) {
					$search_by = "voucher_type_id";
					//$search_text = "0";				
				} else {
					$voucher_type_details = $voucher_type_details[0];

					$type_id = $voucher_type_details->voucher_type_id;
					$search_by = "voucher_type_id";
					//$search_text = $type_id;
				}
			}
			
					
			$where = "{$search_by} LIKE LOWER('%{$search_text}%')";
			$search_url = "?search_option=" . $search_by . "&search_string=" . $search_text;
		}
	
				
		// initialize pagination class
		// set pagination data
		$config = array(
		    'pagination_url' => "/admin/vouchers/index/",
		    'total_items' => $this->members_model->get_member_vouchers_count($where),
		    'per_page' => 10,
		    'uri_segment' => 4,
		);

		$this->pager->set_config($config);
		
		$vouchers = $this->members_model->get_member_vouchers($where, array('rows' => $this->pager->per_page, 'offset' => $this->pager->offset),'voucher_id DESC');
		
		// search vars
		$this->template->search_by = $search_by;
		$this->template->search_text = $search_text;
		$this->template->search_url = $search_url;
		$this->template->searchBy = $searchBy;
	
		$this->template->vouchers = $vouchers;
		$this->template->view('vouchers/list');
	}

	public function create_new() {
	
		$voucher_types = $this->vouchers_model->get_is_voucher_types(null,null,"code ASC");	
		$voucher_products = $this->vouchers_model->get_is_voucher_products(null,null,"voucher_product_id DESC");
	
		$data = array(
			'voucher_types' => $voucher_types,
			'voucher_products' => $voucher_products			
		);

		$html = $this->load->view('/vouchers/create_new', $data, TRUE);

		echo json_encode(array("status"=>"1","html"=>$html));
		return;		
	}
	
	public function confirm_create() {
		$_voucher_product_id = $this->input->post('_voucher_product_id');
		$_quantity = $this->input->post('_quantity');
					
		$voucher_product_details = $this->vouchers_model->get_is_voucher_product_by_id($_voucher_product_id);
		$voucher_product_name = strtoupper($voucher_product_details->voucher_product_name);
		
		$voucher_type_details = $this->vouchers_model->get_is_voucher_type_by_id($voucher_product_details->voucher_type_id);			
		$voucher_type_name = strtoupper($voucher_type_details->code . ' - ' . $voucher_type_details->name);
				
		$html = "<p><label>You are about to Create New Voucher/s with the following details:</label>	
				<table class='table table-striped table-bordered'>
					<thead>
					</thead>
					<tbody>
						<tr>
							<td style='width:120px;'><label><strong>Product Name</strong></label></td>
							<td><label class=''>{$voucher_product_name}</label></td>		
						</tr>
						<tr>
							<td style='width:120px;'><label><strong>Voucher Type</strong></label></td>
							<td><label class=''>{$voucher_type_name}</label></td>		
						</tr>
						<tr>
							<td style='width:120px;'><label><strong>Quantity</strong></label></td>
							<td><label class=''>{$_quantity}</label></td>		
						</tr>					
					</tbody>
				</table>				
				<label>Do you want to proceed?</label>
			</p>";
		
		echo json_encode(array("status"=>"1","html"=>$html));
		return;
		
	}
	
	public function create_voucher() {
		$_voucher_product_id = abs($this->input->post('_voucher_product_id'));
		$_quantity = abs($this->input->post('_quantity'));
		
		$voucher_product_details = $this->vouchers_model->get_is_voucher_product_by_id($_voucher_product_id);
		$voucher_product_name = strtoupper($voucher_product_details->voucher_product_name);

		$voucher_type_details = $this->vouchers_model->get_is_voucher_type_by_id($voucher_product_details->voucher_type_id);			
		//$voucher_type_name = strtoupper($voucher_type_details->code . ' - ' . $voucher_type_details->name);
		
		// check the last entry in cm_member_vouchers where voucher code is the same as the vouchers to be created		
		$where = "voucher_type_id = {$voucher_product_details->voucher_type_id}";
		
		$member_voucher_details = $this->members_model->get_member_vouchers($where, null, "voucher_id DESC");
		
		if (count($member_voucher_details) == 0) {
			$starting_id = 1;		
		} else {
			$voucher_code_data = explode("-", $member_voucher_details[0]->voucher_code);				
			$str_voucher_code = $voucher_code_data[0];
			$str_voucher_number = abs($voucher_code_data[1]);
					
			$starting_id = $str_voucher_number + 1;			
		}
		
		$i = 1;
		$generated_voucher_code_list = "";
		
		while ($i <= $_quantity) {			
			// insert name, email, mobile_number, confirmation_code	
			$data = array(
				'voucher_type_id' => $voucher_product_details->voucher_type_id,
				'voucher_product_id' => $voucher_product_details->voucher_product_id,
				'product_text' => $voucher_product_details->voucher_product_name				
			);

			$this->members_model->insert_member_vouchers($data);

			//// get id and timestamp
			$insert_id = $this->vouchers_model->insert_id();
			
			//logging of action
			$details_after = array('id' => $insert_id, 'details' => $data);
			$details_after = json_encode($details_after);
			$add_vouchers_log_data = array(
				'user_id' => $this->user->user_id,
				'module_name' => 'MEMBER VOUCHERS',
				'table_name' => 'cm_member_vouchers',
				'action' => 'ADD',
				'details_after' => $details_after,
				'remarks' => "",
			);

			$this->tracking_model->insert_logs('admin', $add_vouchers_log_data);
			
			$insert_timestamp = date("Y-m-d H:i:s");

			// create voucher_code
			//$_voucher_code = $voucher_type_details->code . '-' . str_pad($insert_id, 10, "0", STR_PAD_LEFT);			
			$_voucher_code = $voucher_type_details->code . '-' . str_pad($starting_id, 5, "0", STR_PAD_LEFT);
			
			$generated_voucher_code_list = $generated_voucher_code_list . ', ' . $_voucher_code;
						
			//// generate confirmation_code
			$generated_confirmation_code = strtoupper(substr(md5($insert_id . $insert_timestamp), 1, 8));
			
			// insert the voucher and confirmation code
			$data = array(
				'voucher_code' => $_voucher_code,
				'confirmation_code' => $generated_confirmation_code
			);
			$this->members_model->update_member_vouchers($data, array('voucher_id' => $insert_id));
		
			// --------------------------------------------
			// TO-DO: send information via email and/or sms
			// --------------------------------------------

			//logging of action
			//$details_before = array('id' => $insert_id, 'details' => array('voucher_code' => NULL,'confirmation_code' => NULL));
			//$details_before = json_encode($details_before);
			$details_before = "";
			
			$details_after = array('id' => $insert_id, 'details' => $data);
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
		
			$starting_id++;
			$i++;
		}
	    
	 	echo json_encode(array("status"=>"1"));
		return;		
	}

	public function view_details() {	
		$_voucher_id = $this->input->post('_voucher_id');
		
		$html = "	
				<table class='table table-striped table-bordered'>
					<thead>
					<div style='height:15px' class='alert alert-info'><h4>Voucher Details</h4></div>	
					</thead>
					<tbody>
						<tr>
							<td style='width:120px;'><label><strong>Voucher Code</strong></label></td>
							<td><label class=''></label></td>		
						</tr>
						<tr>
							<td style='width:120px;'><label><strong>Confirmation Code</strong></label></td>
							<td><label class=''></label></td>		
						</tr>
						<tr>
							<td style='width:120px;'><label><strong>Voucher Type</strong></label></td>
							<td><label class=''></label></td>		
						</tr>
						<tr>
							<td style='width:120px;'><label><strong>Product Text</strong></label></td>
							<td><label class=''></label></td>		
						</tr>															
					</tbody>
				</table>	
				
				
				<table  class='table table-bordered'>	
					<thead>
					<div style='height:15px' class='alert alert-info'><h4>Owner Details</h4></div>				
					</thead>
					<tbody>						
							<td style='width:120px;'><label><strong>Name</strong></label></td>
							<td><label class=''></label></td>		
						</tr>
						<tr>
							<td style='width:120px;'><label><strong>Email</strong></label></td>
							<td><label class=''></label></td>		
						</tr>	
						<tr>
							<td style='width:120px;'><label><strong>Mobile Number</strong></label></td>
							<td><label class=''></label></td>		
						</tr>	
						<tr>
							<td style='width:120px;'><label><strong>Remarks</strong></label></td>
							<td><label class=''></label></td>		
						</tr>				
					</tbody>
				</table>					
				";
		
		echo json_encode(array("status"=>"1","html"=>$html));
		return;
		
	}	
	
	public function get_voucher_product() {
		$_voucher_product_id = $this->input->post('_voucher_product_id');
		
		if (($_voucher_product_id == NULL) || trim($_voucher_product_id == "")) {
			echo json_encode(array("status"=>"0","html"=>""));
			return;
		} else {
			$voucher_product_details = $this->vouchers_model->get_is_voucher_product_by_id($_voucher_product_id);
			$voucher_product_name = strtoupper($voucher_product_details->voucher_product_name);
			echo json_encode(array("status"=>"1","html"=>$voucher_product_name));
			return;
		}				
	}
	
}