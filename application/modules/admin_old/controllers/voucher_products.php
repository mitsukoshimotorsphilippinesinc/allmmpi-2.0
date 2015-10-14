<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Voucher_products extends Systems_Controller {

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
		$this->voucher_products();
	}

	public function voucher_products()
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
		    'pagination_url' => "/admin/voucher_products/index/",
		    'total_items' => $this->vouchers_model->get_is_voucher_products_count($where),
		    'per_page' => 10,
		    'uri_segment' => 4,
		);

		$this->pager->set_config($config);
		
		$voucher_products = $this->vouchers_model->get_is_voucher_products($where, array('rows' => $this->pager->per_page, 'offset' => $this->pager->offset),'insert_timestamp DESC');
		
		// search vars
		$this->template->search_by = $search_by;
		$this->template->search_text = $search_text;
		$this->template->search_url = $search_url;
		$this->template->searchBy = $searchBy;
	
		$this->template->voucher_products = $voucher_products;
		$this->template->view('voucher_products/list');
	}

	public function add() {
	
		$voucher_types = $this->vouchers_model->get_is_voucher_types(null,null,"code ASC");
	
		$data = array(
			'voucher_types' => $voucher_types
		);

		$html = $this->load->view('/voucher_products/add', $data, TRUE);

		echo json_encode(array("status"=>"1","html"=>$html));
		return;		
	}
	
	public function confirm_add() {
		$_voucher_product_name = trim($this->input->post('_voucher_product_name'));
		$_voucher_type_id = abs($this->input->post('_voucher_type_id'));
	
		$voucher_type_details = $this->vouchers_model->get_is_voucher_type_by_id($_voucher_type_id);		
			
		$_voucher_product_name = strtoupper($_voucher_product_name);		
		$voucher_type_name = strtoupper($voucher_type_details->name);
		
		
		// check if voucher_type_code exists in table
		$voucher_product = $this->vouchers_model->get_is_voucher_product_by_name($_voucher_product_name);
		
		if (count($voucher_product) > 0)  {
			// existing product
			$voucher_type_details = $this->vouchers_model->get_is_voucher_type_by_id($voucher_product->voucher_type_id);
			if (count($voucher_type_details) == 0) {
	
				echo json_encode(array("status"=>"0","html"=>"<p>Voucher Type Code not found.</p>"));				
	
			} else {
				
				$voucher_type_details = $voucher_type_details->code . " - " . $voucher_type_details->name;
				
				$html = "<p>
							<label>Sorry, the Voucher Product already exists. See the details below:</label>
							<table class='table table-striped table-bordered'>
								<thead>
								</thead>
								<tbody>
									<tr>
										<td style='width:100px;'><label style='color:#990000;'><strong>Product Name</strong></label></td>
										<td><label style='color:#990000;'>{$voucher_product->voucher_product_name}</label></td>		
									</tr>
									<tr>
										<td style='width:100px;'><label style='color:#990000;'><strong>Voucher Type</strong></label></td>
										<td><label style='color:#990000;'>{$voucher_type_details}</label></td>		
									</tr>											
								</tbody>
							</table>
						</p>";
					
				echo json_encode(array("status"=>"0","html"=>$html));
			}
		} else {
		
			$html = "<p><label>You are about to add a new Voucher Product with the following details:</label>	
					<table class='table table-striped table-bordered'>
						<thead>
						</thead>
						<tbody>
							<tr>
								<td style='width:120px;'><label><strong>Product Name</strong></label></td>
								<td><label class=''>{$_voucher_product_name}</label></td>		
							</tr>
							<tr>
								<td style='width:120px;'><label><strong>Voucher Type</strong></label></td>
								<td><label class=''>{$voucher_type_name}</label></td>		
							</tr>					
						</tbody>
					</table>				
					<label>Do you want to proceed?</label>
				</p>";
		
			echo json_encode(array("status"=>"1","html"=>$html));
		}
		return;
		
	}
	
	public function add_voucher_product() {
		$_voucher_product_name = $this->input->post('_voucher_product_name');
		$_voucher_type_id = $this->input->post('_voucher_type_id');

		$_voucher_product_name = strtoupper($_voucher_product_name);		
			
		// insert the new product
		$data = array(
			'voucher_product_name' => $_voucher_product_name,
			'voucher_type_id' => $_voucher_type_id
		);
		$this->vouchers_model->insert_is_voucher_products($data);
		
		$insert_id = $this->vouchers_model->insert_id();
				
		//logging of action
		$details_after = array('id' => $insert_id, 'details' => $data);
		$details_after = json_encode($details_after);
		$add_vouchers_log_data = array(
			'user_id' => $this->user->user_id,
			'module_name' => 'MEMBER VOUCHER PRODUCTS',
			'table_name' => 'cm_member_voucher_products',
			'action' => 'ADD',
			'details_after' => $details_after,
			'remarks' => "",
		);

		$this->tracking_model->insert_logs('admin', $add_vouchers_log_data);
		
		echo json_encode(array("status"=>"1"));
		return;		
	}
	

	//public function edit($product_id)
	public function edit()
	
	{
		$_voucher_product_id = $this->input->post('_voucher_product_id');
		
		$voucher_product = $this->vouchers_model->get_is_voucher_product_by_id($_voucher_product_id);

		$voucher_types = $this->vouchers_model->get_is_voucher_types(null,null,"code ASC");

		$data = array(		
			'voucher_types' => $voucher_types,
			'voucher_product' => $voucher_product
		);

		$html = $this->load->view('/voucher_products/edit', $data, TRUE);

		echo json_encode(array("status"=>"1","html"=>$html));
		return;
		
	}

	public function confirm_edit() {
		$_voucher_product_name = $this->input->post('_voucher_product_name');
		$_voucher_type_id = $this->input->post('_voucher_type_id');
		$_voucher_product_id = $this->input->post('_voucher_product_id');
		
		$voucher_type_details = $this->vouchers_model->get_is_voucher_type_by_id($_voucher_type_id);		
			
		$_voucher_product_name = strtoupper($_voucher_product_name);		
		$voucher_type_name = strtoupper($voucher_type_details->name);

		$html = "<p><label>You are about to edit a Voucher Product with the following details:</label>	
				<table class='table table-striped table-bordered'>
					<thead>
					</thead>
					<tbody>
						<tr>
							<td style='width:120px;'><label><strong>Product Name</strong></label></td>
							<td><label class=''>{$_voucher_product_name}</label></td>		
						</tr>
						<tr>
							<td style='width:120px;'><label><strong>Voucher Type</strong></label></td>
							<td><label class=''>{$voucher_type_name}</label></td>		
						</tr>						
					</tbody>
				</table>				
				<label>Do you want to proceed?</label>
			</p>";
		
		echo json_encode(array("status"=>"1","html"=>$html));
		return;
		
	}

	public function update_voucher_product() {
		$_voucher_product_name = $this->input->post('_voucher_product_name');
		$_voucher_type_id = $this->input->post('_voucher_type_id');
		$_voucher_product_id = $this->input->post('_voucher_product_id');
		
		$_voucher_product = $this->vouchers_model->get_is_voucher_product_by_id($_voucher_product_id);
		
		$_voucher_product_name = strtoupper($_voucher_product_name);
	
		// insert the new product
		$data = array(
			'voucher_product_name' => $_voucher_product_name,
			'voucher_type_id' => $_voucher_type_id
		);
		$this->vouchers_model->update_is_voucher_products($data, array('voucher_product_id' => $_voucher_product_id));
				
		//logging of action
		$data_before = array(
			'voucher_product_name' => $_voucher_product->voucher_product_name,
			'voucher_type_id' => $_voucher_product->voucher_type_id
		);
		
		$details_before = array('id' => $_voucher_product_id, 'details' => $data_before);
		$details_before = json_encode($details_before);
		
		$details_after = array('id' => $_voucher_product_id, 'details' => $data);
		$details_after = json_encode($details_after);
		$update_vouchers_log_data = array(
			'user_id' => $this->user->user_id,
			'module_name' => 'MEMBER VOUCHER PRODUCTS',
			'table_name' => 'cm_member_voucher_products',
			'action' => 'UPDATE',
			'details_before' => $details_before,
			'details_after' => $details_after,
			'remarks' => "",
		);

		$this->tracking_model->insert_logs('admin', $update_vouchers_log_data);
	
		echo json_encode(array("status"=>"1"));
		return;		
	}
	

	public function delete()
	{
		$_voucher_product_id = $this->input->post('_voucher_product_id');			
		
		$voucher_product = $this->vouchers_model->get_is_voucher_product_by_id($_voucher_product_id);

		$voucher_type_details = $this->vouchers_model->get_is_voucher_type_by_id($voucher_product->voucher_type_id);

		$voucher_type_text = $voucher_type_details->code . " - " . $voucher_type_details->name;

		$data = array(
			'voucher_product' => $voucher_product,
			'voucher_type_text' => $voucher_type_text
		);		
		$html = $this->load->view('/voucher_products/delete', $data, TRUE);

		echo json_encode(array("status"=>"1","html"=>$html));
		return;
		
	}
	
	public function confirm_delete() {
		$_voucher_product_id = $this->input->post('_voucher_product_id');		
	
		$html = "<p><label>Are you sure you want to proceed?</label>									
				</p>";
		
		echo json_encode(array("status"=>"1","html"=>$html));
		return;
		
	}
	
	public function delete_voucher_product() {	
		$_voucher_product_id = $this->input->post('_voucher_product_id');	
			
		if (!empty($_product_id)) {
			
			$_voucher_product = $this->vouchers_model->get_is_voucher_product_by_id($_voucher_product_id);
			
			$this->vouchers_model->delete_is_voucher_products(array('voucher_product_id' => $_voucher_product_id));

			//logging of action
			$data_before = array(
				'voucher_product_name' => $_voucher_product->voucher_product_name,
				'voucher_type_id' => $_voucher_product->voucher_type_id
			);

			$details_before = array('id' => $_voucher_product_id, 'details' => $data_before);
			$details_before = json_encode($details_before);

			$delete_vouchers_log_data = array(
				'user_id' => $this->user->user_id,
				'module_name' => 'MEMBER VOUCHER PRODUCTS',
				'table_name' => 'cm_member_voucher_products',
				'action' => 'DELETE',
				'details_before' => $details_before,
				'remarks' => "",
			);

			$this->tracking_model->insert_logs('admin', $delete_vouchers_log_data);
			
		} else {
			echo json_encode(array("status"=>"0", "message" => "Invalid Voucher Product ID"));
			return;
		}
	
		echo json_encode(array("status"=>"1"));
		return;
		
	}

	public function create() {	
		$data = "";
		
		$html = $this->load->view('/voucher_products/create_voucher', $data, TRUE);

		echo json_encode(array("status"=>"1","html"=>$html));
		return;		
	}

	public function confirm_create() {
		$_last_name = trim($this->input->post('_last_name'));
		$_first_name = trim($this->input->post('_first_name'));
		$_middle_name =trim($this->input->post('_middle_name'));
		$_mobile_number = trim($this->input->post('_mobile_number'));
		$_email = trim($this->input->post('_email'));
		$_quantity = abs($this->input->post('_quantity'));
		$_voucher_product_id = $this->input->post('_voucher_product_id');
	
	
		$_proper_name = strtoupper($_last_name . ', ' . $_first_name  . ' ' .  $_middle_name);
		
		if (trim($_proper_name) == ",") {
			$_proper_name = "";
		}						
		$_mobile_number = strtoupper($_mobile_number);
		
		
		$voucher_product_details = $this->vouchers_model->get_is_voucher_product_by_id($_voucher_product_id);
		
		
		//var_dump($voucher_product_details);
		//return;
		
		if (count($voucher_product_details) == 0) {
			// non-existing voucher product
			echo json_encode(array("status"=>"0","html"=>"<p>Error: Voucher Product not Found.</p>"));
			return;
		} else {
			// get the voucher_type
			$voucher_type_details = $this->vouchers_model->get_is_voucher_type_by_id($voucher_product_details->voucher_type_id);
			
			if (count($voucher_type_details) == 0) {
				// missing voucher_type_details
				echo json_encode(array("status"=>"0","html"=>"<p>Error: Voucher Type for " . $voucher_product_details->voucher_product_name . " not Found.</p>"));
				return;
			}
			
			$type_details = $voucher_type_details->code . " - " .  $voucher_type_details->name;
		
			$html = "<p><label>You are about to create new Voucher with the following details:</label>	
					<table class='table table-striped table-bordered'>
						<thead>
						</thead>
						<tbody>
							<tr>
								<td style='width:120px;'><label><strong>Voucher Product</strong></label></td>
								<td><label class=''>{$voucher_product_details->voucher_product_name}</label></td>		
							</tr>
							<tr>
								<td style='width:120px;'><label><strong>Voucher Type</strong></label></td>
								<td><label class=''>{$type_details}</label></td>		
							</tr>
							<tr>
								<td style='width:120px;'><label><strong>Name</strong></label></td>
								<td><label class=''>{$_proper_name}</label></td>		
							</tr>
							<tr>
								<td style='width:120px;'><label><strong>Mobile Number</strong></label></td>
								<td><label class=''>{$_mobile_number}</label></td>		
							</tr>	
							<tr>
								<td style='width:120px;'><label><strong>Email</strong></label></td>
								<td><label class=''>{$_email}</label></td>		
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
	}
	
	public function create_voucher() {
		$_last_name = trim($this->input->post('_last_name'));
		$_first_name = trim($this->input->post('_first_name'));
		$_middle_name =trim($this->input->post('_middle_name'));
		$_mobile_number = trim($this->input->post('_mobile_number'));
		$_email = trim($this->input->post('_email'));
		$_quantity = abs($this->input->post('_quantity'));
		$_voucher_product_id = $this->input->post('_voucher_product_id');
		
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
				'last_name' => $_last_name,
				'first_name' => $_first_name,
				'middle_name' => $_middle_name,
				'mobile_number' => $_mobile_number,
				'email' => $_email,
				'voucher_type_id' => $voucher_product_details->voucher_type_id,
				'voucher_product_id' => $voucher_product_details->voucher_product_id,
				'product_text' => $voucher_product_details->voucher_product_name				
			);

			$this->members_model->insert_member_vouchers($data);

			// get id and timestamp
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
			//$details_before = array('id' => $insert_id, 'details' => array('voucher_code' => NULL,'confirmation_code' => NULL));
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

}