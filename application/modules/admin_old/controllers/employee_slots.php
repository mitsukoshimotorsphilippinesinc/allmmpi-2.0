<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Employee_slots extends Systems_Controller 
{
	private $_validation_rule = array(
		array(
			'field' => 'email',
			'label' => 'Email',
			'rules' => 'trim|required|valid_email'
		),
		array(
			'field' => 'first_name',
			'label' => 'First Name',
			'rules' => 'trim|required'
		),
		array(
			'field' => 'last_name',
			'label' => 'Last Name',
			'rules' => 'trim|required'
		),
		array(
			'field' => 'mobile_number',
			'label' => 'Mobile Number',
			'rules' => 'trim|required'
		),
		array(
			'field' => 'employee_code',
			'label' => 'Employee Code',
			'rules' => 'trim|required'
		),
		array(
			'field' => 'is_active',
			'label' => 'Is Active',
			'rules' => 'trim|required|numeric'
		)
	);
	
	function __construct() 
	{
  		parent::__construct();
		$this->set_navigation('employees');
		// load pager library
		$this->load->library('pager');
		
		$this->load->model('employees_model');
		$this->load->model('items_model');
	}
	
	public function index() 
	{ 
		$this->view();
	}
	
	public function view()
	{
		
		$search_by = trim($this->input->get("search_option"));
		$search_text = trim($this->input->get("search_string"));

		$search_url = "";
		
		if (($search_text == "") || empty($search_text)) {
			$where = "employee_slots != 0";			
		} else {
		
			$where = "{$search_by} LIKE LOWER('%{$search_text}%') AND employee_slots != 0";
			$search_url = "?search_option=" . $search_by . "&search_string=" . $search_text;
		}
		
		$total_items = $this->items_model->get_product_count($where);
		
		// initialize pagination class
		// set pagination data
		$config = array(
		    'pagination_url' => '/admin/employee_slots/view/',
		    'total_items' => $total_items,
		    'per_page' => 10,
		    'uri_segment' => 4,
		);
		
		
		$this->pager->set_config($config);
		
		$employee_slots = $this->items_model->get_products($where, array('rows' => $this->pager->per_page, 'offset' => $this->pager->offset),"product_name ASC");

		// search vars
		$this->template->search_by = $search_by;
		$this->template->search_text = $search_text;
		$this->template->search_url = $search_url;
		
		$this->pager->set_config($config);

		$this->template->employee_slots = $employee_slots;
		$this->template->view('employee_slots/list');
	}
	
	public function add()
	{
		
		$products = $this->items_model->get_products("`employee_slots` = 0",null,"product_name ASC");
		
		if(empty($products))
		{
			$this->return_json("error","All products have been added");
			return;
		}
		
		$html = $this->load->view('/employee_slots/add', array("products" => $products), TRUE);

		$this->return_json("ok",$html);
		return;		
	}
	
	public function confirm_add() {
		$product_id = abs($this->input->post('product_id'));
		$qty = abs($this->input->post('qty'));
		
		// check if product exists
		$product = $this->items_model->get_product_by_id($product_id);
		
		if (!empty($product)) {
						
			$html = "<p><label>You are about to add a new Employee Slot with the following details:</label>	
					<table class='table table-striped table-bordered'>
						<thead>
						</thead>
						<tbody>
							<tr>
								<td style='width:100px;'><label><strong>Product Name</strong></label></td>
								<td><label class=''>{$product->product_name}</label></td>		
							</tr>
							<tr>
								<td style='width:100px;'><label><strong>Quantity</strong></label></td>
								<td><label class=''>{$qty}</label></td>		
							</tr>
						</tbody>
					</table>				
					<label>Do you want to proceed?</label>
				</p>";
		
			$this->return_json("1",$html);
		} else {
			// username already exists
			$this->return_json("0","<p>This product does not exist. Please try another.</p>");
		}
		return;
		
	}
	
	public function add_employee_slot() {
		$action = trim($this->input->post('action'));
		$product_id = abs($this->input->post('product_id'));
		$qty = abs($this->input->post('qty'));
		
		// check if product exists
		$product = $this->items_model->get_product_by_id($product_id);
		
		if (empty($product))
		{
			$this->return_json("0","This product does not exist");
			return;
		}
		
		// insert the new product
		$data = array(
			'employee_slots' => $qty
		);
		$this->items_model->update_product($data,array("product_id" => $product_id));
		
		$details_before = array('id' => $product_id, 'details' => array('employee_slots' => $product->employee_slots));
		$details_before = json_encode($details_before);
		
		//LOGGING OF Employee Slot
		$details_after = array('id' => $product_id, 'details' => $data);
		$details_after = json_encode($details_after);
		$new_employee_slot_data_logs = array(
			'user_id' => $this->user->user_id,
			'module_name' => 'EMPLOYEE SLOTS',
			'table_name' => 'is_products',
			'action' => 'UPDATE',
			'details_before' => $details_before,
			'details_after' => $details_after,
			'remarks' => ""
		);
		$this->tracking_model->insert_logs('admin', $new_employee_slot_data_logs);
		
		if($action == "add")
		{
			//add slots for all employees
			$insert_sql = "INSERT INTO `ad_employee_products` (`employee_id`,`product_id`,`available_qty`) 
					(SELECT `employee_id`,{$product_id},{$qty} FROM `ad_employees`)";

			$this->db->query($insert_sql);
		}
		else
		{
			if($product->employee_slots > $qty)
			{
				$update_sql = "UPDATE `ad_employee_products` SET `available_qty` = {$qty} WHERE `product_id` = {$product_id} AND `available_qty` > {$qty}";
				$this->db->query($update_sql);
			}
			else
			{
				$update_sql = "UPDATE `ad_employee_products` SET `available_qty` = `available_qty` + ".($qty - $product->employee_slots)." WHERE `product_id` = {$product_id}";
				$this->db->query($update_sql);
			}
			
		}
		
		$this->return_json("1");
		return;		
	}
	
	public function edit()
	{
		
		$product_id = abs($this->input->post("product_id"));
		
		if(empty($product_id))
		{
			$this->return_json("0","Invalid ID");
			return;
		}
		
		$product = $this->items_model->get_product_by_id($product_id);
		
		if(empty($product))
		{
			$this->return_json("0","This Product does not exist!");
			return;
		}
		
		$html = $this->load->view('/employee_slots/edit', array("product" => $product), TRUE);

		$this->return_json("1",$html);
		return;
	}
	
	public function confirm_edit() {
		$product_id = abs($this->input->post('orig_product_id'));
		$qty = abs($this->input->post('qty'));
		
		// check if product exists
		$product = $this->items_model->get_product_by_id($product_id);

		if (empty($product))
		{
			$this->return_json("0","<p>Product does not exist.</p>");
			return;
		}
		
		$html = "<p><label>You are about to edit an existing Employee Slot with the following details:</label>	
				<table class='table table-striped table-bordered'>
					<thead>
					</thead>
					<tbody>
						<tr>
							<td style='width:100px;'><label><strong>Product Name</strong></label></td>
							<td><label class=''>{$product->product_name}</label></td>		
						</tr>
						<tr>
							<td style='width:100px;'><label><strong>Quantity</strong></label></td>
							<td><label class=''>{$qty}</label></td>		
						</tr>
					</tbody>
				</table>				
				<label>Do you want to proceed?</label>
			</p>";
	
		$this->return_json("1",$html);
		return;
		
	}
	
	public function delete() {
		$product_id = abs($this->input->post('product_id'));
		
		// check if product exists
		$product = $this->items_model->get_product_by_id($product_id);

		if (empty($product))
		{
			$this->return_json("0","<p>Product does not exist.</p>");
			return;
		}
		
		$html = "<p><label>You are about to delete an existing Employee Slot with the following details:</label>	
				<table class='table table-striped table-bordered'>
					<thead>
					</thead>
					<tbody>
						<tr>
							<td style='width:100px;'><label><strong>Product Name</strong></label></td>
							<td><label class=''>{$product->product_name}</label></td>		
						</tr>
					</tbody>
				</table>				
				<label>Do you want to proceed?</label>
			</p>";
	
		$this->return_json("1",$html);
		return;
		
	}
	
	public function delete_employee_slot() {
		$product_id = abs($this->input->post('product_id'));
		
		// check if product exists
		$product = $this->items_model->get_product_by_id($product_id);
		
		if (empty($product))
		{
			$this->return_json("0","This product does not exist");
			return;
		}
		
		// insert the new product
		$data = array(
			'employee_slots' => 0
		);
		$this->items_model->update_product($data,array("product_id" => $product_id));
		
		$details_before = array('id' => $product_id, 'details' => array('employee_slots' => $product->employee_slots));
		$details_before = json_encode($details_before);
		
		//LOGGING OF Employee Slot
		$details_after = array('id' => $product_id, 'details' => $data);
		$details_after = json_encode($details_after);
		$new_employee_slot_data_logs = array(
			'user_id' => $this->user->user_id,
			'module_name' => 'EMPLOYEE SLOTS',
			'table_name' => 'is_products',
			'action' => 'UPDATE',
			'details_before' => $details_before,
			'details_after' => $details_after,
			'remarks' => ""
		);
		$this->tracking_model->insert_logs('admin', $new_employee_slot_data_logs);
		
		//add slots for all employees
		$insert_sql = "DELETE FROM `ad_employee_products` WHERE `product_id` = {$product_id}";
		$this->db->query($insert_sql);
		
		$this->return_json("1");
		return;		
	}
	
	public function view_change_log() {
		$employee_id = abs($this->input->post('employee_id'));
		
		// check if username is already used
		$employee = $this->employees_model->get_employee_by_id($employee_id);

		if (empty($employee))
		{
			$this->return_json("error","<p>Employee does not exist.</p>");
			return;
		}
		
		$first_name = ucfirst($employee->first_name);
		$last_name = ucfirst($employee->last_name);
		
		$change_log_array = json_decode($employee->change_log);
		$change_log_html = "";
		
		
		//print_r($change_log_array);
		if(empty($change_log_array))
		{
			$change_log_html = "<tr>
				<td colspan='2' style='text-align: center;'><label><strong>No Change Log</strong></label></td>
			</tr>";
		}
		else
		{
			$insert_timestamp_sort = array();
			foreach($change_log_array as $key => $cla)
			{
				$insert_timestamp_sort[$key] = $cla->insert_timestamp;
			}
			
			array_multisort($insert_timestamp_sort, SORT_DESC, $change_log_array);
			
			foreach($change_log_array as $cla)
			{
				$date = date("Y-m-d H:i:s",$cla->insert_timestamp);
				$change_log_html .= "<tr>
					<td style='width:125px;'><label><strong>{$date}</strong></label></td>
					<td><label class=''>{$cla->remark}</label></td>		
				</tr>";
			}
		}
		
		
		$html = "<p><label>Change Log for {$first_name} {$last_name}:</label>
				<div style='max-height: 400px; overflow-y: scroll;'>
					<table class='table table-striped table-bordered' >
						<thead>
						</thead>
						<tbody>
							{$change_log_html}
						</tbody>
					</table>
				</div>
			</p>";
	
		$this->return_json("ok",$html);
		return;
		
	}
	
	public function check_employee_code()
	{
		$employee_code = trim($this->input->post("employee_code"));
		$orig_employee_code = trim($this->input->post("orig_employee_code"));
		
		$employee = $this->employees_model->get_employee_by_code($employee_code);
		
		if(!empty($employee))
		{
			if($orig_employee_code != $employee->employee_code)
			{
				$this->return_json("error","Employee Code already exists.");
				return;
			}
			else
			{
				$this->return_json("ok");
				return;
			}
			
		}
		
		$this->return_json("ok");
		return;
	}
}
