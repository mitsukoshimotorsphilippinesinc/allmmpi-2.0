<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Employees extends Systems_Controller 
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
		),
		array(
			'field' => 'employee_type_id',
			'label' => 'Employee Type',
			'rules' => 'trim|required'
		)
	);
	
	function __construct() 
	{
  		parent::__construct();
		$this->set_navigation('employees');
		// load pager library
		$this->load->library('pager');
		
		$this->load->model('employees_model');
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
			$where = NULL;			
		} else {
			if ($search_by == 'name') {				
				$where = "concat(first_name, ' ', last_name) LIKE LOWER('%{$search_text}%')";
			}
			else if($search_by == 'employee_id')
			{
				$where = "`employee_code` LIKE LOWER('%{$search_text}%')";
			} else {			
				$where = "{$search_by} LIKE LOWER('%{$search_text}%')";
			}
			
			$search_url = "?search_option=" . $search_by . "&search_string=" . $search_text;
		}
		
		$total_items = $this->employees_model->get_employee_count($where);
		
		// initialize pagination class
		// set pagination data
		$config = array(
		    'pagination_url' => '/admin/employees/view/',
		    'total_items' => $total_items,
		    'per_page' => 10,
		    'uri_segment' => 4,
		);
		
		
		$this->pager->set_config($config);
		
		$employees = $this->employees_model->get_employees($where, array('rows' => $this->pager->per_page, 'offset' => $this->pager->offset));

		if ($_POST)
		{
			
			$search_validation = array(
				array(
					'field' => 'search',
					'label' => 'Search',
					'rules' => 'trim|required'
				),
				array(
					'field' => 'query',
					'label' => 'Query',
					'rules' => 'trim|required'
				),
			);
			$this->form_validation->set_rules($search_validation);
			if ($this->form_validation->run()) 
			{
				$search = set_value('search');
				$query = set_value('query');
				
				$total_items = $this->employees_model->search();
				$employees = $this->employees_model->search_employees($search,$query,array('rows' => $this->pager->per_page, 'offset' => $this->pager->offset));

			}
		}

		// search vars
		$this->template->search_by = $search_by;
		$this->template->search_text = $search_text;
		$this->template->search_url = $search_url;
		
		$this->pager->set_config($config);

		$this->template->employees = $employees;
		$this->template->view('employees/list');
	}
	
	public function add()
	{
		$employee_types = $this->employees_model->get_employee_types();
		$html = $this->load->view('/employees/add', array('employee_types' => $employee_types), TRUE);

		echo json_encode(array("status"=>"1","html"=>$html));
		return;		
	}
	
	public function confirm_add() {
		$employee_code = strtoupper(trim($this->input->post('employee_code')));
		$email = trim($this->input->post('email'));
		$first_name = trim($this->input->post('first_name'));
		$last_name = trim($this->input->post('last_name'));
		$mobile_number = trim($this->input->post('mobile_number'));
		$is_active = abs($this->input->post('is_active'));
		$employee_type_id = abs($this->input->post('employee_type_id'));
		
		$first_name = ucfirst($first_name);
		$last_name = ucfirst($last_name);
				
		if ($is_active == 0) {
			$is_active = "No";
		} else {
			$is_active = "Yes";
		}
		
		// check if username is already used
		$employee = $this->employees_model->get_employee_by_code($employee_code);
		$employee_type = $this->employees_model->get_employee_type_by_id($employee_type_id);
		if (empty($employee)) {
						
			$html = "<p><label>You are about to add a new Employee with the following details:</label>	
					<table class='table table-striped table-bordered'>
						<thead>
						</thead>
						<tbody>
							<tr>
								<td style='width:100px;'><label><strong>Employee ID</strong></label></td>
								<td><label class=''>{$employee_code}</label></td>		
							</tr>
							<tr>
								<td style='width:100px;'><label><strong>First Name</strong></label></td>
								<td><label class=''>{$first_name}</label></td>		
							</tr>
							<tr>
								<td style='width:100px;'><label><strong>Last Name</strong></label></td>
								<td><label class=''>{$last_name}</label></td>		
							</tr>
							<tr>
								<td style='width:100px;'><label><strong>Email</strong></label></td>
								<td><label class=''>{$email}</label></td>		
							</tr>
							<tr>
								<td style='width:100px;'><label><strong>Mobile Number</strong></label></td>
								<td><label class=''>{$mobile_number}</label></td>		
							</tr>
							<tr>
								<td style='width:100px;'><label><strong>Employee Type</strong></label></td>
								<td><label class=''>{$employee_type->name}</label></td>
							</tr>
							<tr>
								<td style='width:100px;'><label><strong>Active</strong></label></td>
								<td><label class=''>{$is_active}</label></td>		
							</tr>
						</tbody>
					</table>				
					<label>Do you want to proceed?</label>
				</p>";
		
			$this->return_json("1",$html);
		} else {
			// username already exists
			$this->return_json("0","<p>Username already exists. Please try another.</p>");
		}
		return;
		
	}
	
	public function add_employee() {
		$employee_code = strtoupper(trim($this->input->post('employee_code')));
		$email = trim($this->input->post('email'));
		$first_name = trim($this->input->post('first_name'));
		$last_name = trim($this->input->post('last_name'));
		$mobile_number = trim($this->input->post('mobile_number'));
		$is_active = abs($this->input->post('is_active'));
		$employee_type_id = abs($this->input->post('employee_type_id'));
		
		$first_name = ucfirst($first_name);
		$last_name = ucfirst($last_name);
	
		// insert the new product
		$data = array(
			'employee_code' => $employee_code,
			'email' => $email,
			'first_name' => $first_name,
			'last_name' => $last_name,
			'mobile_number' => $mobile_number,
			'employee_type_id' => $employee_type_id,
			'is_active' => $is_active,
		);
		$this->employees_model->insert_employee($data);		
		$employee_id = $this->employees_model->insert_id();
		
		//LOGGING OF employee
		$details_after = array('id' => $employee_id, 'details' => $data);
		$details_after = json_encode($details_after);
		$new_employee_data_logs = array(
			'user_id' => $this->user->user_id,
			'module_name' => 'EMPLOYEES',
			'table_name' => 'ad_employees',
			'action' => 'ADD',
			'details_before' => "",
			'details_after' => $details_after,
			'remarks' => ""
		);
		$this->tracking_model->insert_logs('admin', $new_employee_data_logs);
		
		//add employee slots for new employee
		$insert_sql = "INSERT INTO `ad_employee_products` (`employee_id`,`product_id`,`available_qty`) 
				(SELECT {$employee_id},`product_id`,`employee_slots` FROM `is_products` WHERE `employee_slots` != 0)";
		
		$this->db->query($insert_sql);
		
		$this->return_json("1");
		return;		
	}
	
	public function edit()
	{
		
		$employee_id = $this->input->post("employee_id");
		
		if(empty($employee_id))
		{
			$this->return_json("0","Invalid ID");
			return;
		}
		
		$employee = $this->employees_model->get_employee_by_id($employee_id);
		
		if(empty($employee))
		{
			$this->return_json("0","This Employee does not exist!");
			return;
		}
		$employee_types = $this->employees_model->get_employee_types();
		$html = $this->load->view('/employees/edit', array("employee" => $employee, 'employee_types' => $employee_types), TRUE);

		$this->return_json("1",$html);
		return;
	}
	
	public function confirm_edit() {
		$orig_employee_code = strtoupper(trim($this->input->post('orig_employee_code')));
		$employee_code = strtoupper(trim($this->input->post('employee_code')));
		$email = trim($this->input->post('email'));
		$first_name = trim($this->input->post('first_name'));
		$last_name = trim($this->input->post('last_name'));
		$mobile_number = trim($this->input->post('mobile_number'));
		$is_active = abs($this->input->post('is_active'));
		$employee_type_id = abs($this->input->post('employee_type_id'));
		$change_log = trim($this->input->post('change_log'));
		
		$first_name = ucfirst($first_name);
		$last_name = ucfirst($last_name);
				
		if ($is_active == 0) {
			$is_active = "No";
		} else {
			$is_active = "Yes";
		}
		
		// check if username is already used
		$employee = $this->employees_model->get_employee_by_code($employee_code);
		$employee_type = $this->employees_model->get_employee_type_by_id($employee_type_id);
		if (empty($employee))
		{
			$this->return_json("0","<p>Employee does not exist.</p>");
			return;
		}
		
		if($orig_employee_code != $employee->employee_code)
		{
			$this->return_json("0","<p>Employee codes do not match.</p>");
			return;
		}
		
		$html = "<p><label>You are about to edit an existing Employee with the following details:</label>	
				<table class='table table-striped table-bordered'>
					<thead>
					</thead>
					<tbody>
						<tr>
							<td style='width:100px;'><label><strong>Employee ID</strong></label></td>
							<td><label class=''>{$employee_code}</label></td>		
						</tr>
						<tr>
							<td style='width:100px;'><label><strong>First Name</strong></label></td>
							<td><label class=''>{$first_name}</label></td>		
						</tr>
						<tr>
							<td style='width:100px;'><label><strong>Last Name</strong></label></td>
							<td><label class=''>{$last_name}</label></td>		
						</tr>
						<tr>
							<td style='width:100px;'><label><strong>Email</strong></label></td>
							<td><label class=''>{$email}</label></td>		
						</tr>
						<tr>
							<td style='width:100px;'><label><strong>Mobile Number</strong></label></td>
							<td><label class=''>{$mobile_number}</label></td>		
						</tr>
						<tr>
							<td style='width:100px;'><label><strong>Employee Type</strong></label></td>
							<td><label class=''>{$employee_type->name}</label></td>		
						</tr>
						<tr>
							<td style='width:100px;'><label><strong>Active</strong></label></td>
							<td><label class=''>{$is_active}</label></td>		
						</tr>
						<tr>
							<td style='width:100px;'><label><strong>Editing Remarks</strong></label></td>
							<td><label class=''>{$change_log}</label></td>		
						</tr>
					</tbody>
				</table>				
				<label>Do you want to proceed?</label>
			</p>";
	
		$this->return_json("1",$html);
		return;
		
	}
	
	public function edit_employee() {
		$employee_id = abs($this->input->post('employee_id'));
		$employee_code = strtoupper(trim($this->input->post('employee_code')));
		$email = trim($this->input->post('email'));
		$first_name = trim($this->input->post('first_name'));
		$last_name = trim($this->input->post('last_name'));
		$mobile_number = trim($this->input->post('mobile_number'));
		$employee_type_id = trim($this->input->post('employee_type_id'));
		$is_active = abs($this->input->post('is_active'));
		$change_log = trim($this->input->post('change_log'));
		
		$first_name = ucfirst($first_name);
		$last_name = ucfirst($last_name);
	
		$employee = $this->employees_model->get_employee_by_id($employee_id);
		
		if(empty($employee))
		{
			$this->return_json("0","This employee does not exist");
			return;
		}
		
		$change_log_array = array();
		
		if(!empty($employee->change_log))
		{
			$change_log_array = json_decode($employee->change_log);
		}
		
		array_push($change_log_array,(object) array("insert_timestamp" => strtotime(date("Y-m-d H:i:s")), "remark" => $change_log));
	
		// insert the new product
		$data = array(
			'employee_code' => $employee_code,
			'email' => $email,
			'first_name' => $first_name,
			'last_name' => $last_name,
			'mobile_number' => $mobile_number,
			'employee_type_id' => $employee_type_id,
			'is_active' => $is_active,
			'change_log' => json_encode($change_log_array)
		);
		$this->employees_model->update_employees($data,array("employee_id" => $employee_id));

		$details_before = array('id' => $employee_id, 'details' => $employee);
		$details_before = json_encode($details_before);
		//LOGGING OF employee
		$details_after = array('id' => $employee_id, 'details' => $data);
		$details_after = json_encode($details_after);
		$update_employee_data_logs = array(
			'user_id' => $this->user->user_id,
			'module_name' => 'EMPLOYEES',
			'table_name' => 'ad_employees',
			'action' => 'UPDATE',
			'details_before' => $details_before,
			'details_after' => $details_after,
			'remarks' => ""
		);
		$this->tracking_model->insert_logs('admin', $update_employee_data_logs);
			
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
