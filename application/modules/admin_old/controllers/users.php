<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Users extends Systems_Controller 
{
	private $_validation_rule = array(
		array(
			'field' => 'username',
			'label' => 'Username',
			'rules' => 'trim|required|min_length[3]|max_length[16]'
		),
		array(
			'field' => 'email',
			'label' => 'Email',
			'rules' => 'trim|required|valid_email'
		),
		array(
			'field' => 'first_name',
			'label' => 'First Name',
			'rules' => 'trim'
		),
		array(
			'field' => 'last_name',
			'label' => 'Last Name',
			'rules' => 'trim'
		),
		array(
			'field' => 'mobile_number',
			'label' => 'Mobile Number',
			'rules' => 'trim'
		),
		array(
			'field' => 'user_facility',
			'label' => 'Facility',
			'rules' => 'trim|required'
		),
		array(
			'field' => 'role_id',
			'label' => 'Role',
			'rules' => 'trim|numeric'
		),
		array(
			'field' => 'is_active',
			'label' => 'Is Active',
			'rules' => 'trim|numeric'
		),
		array(
			'field' => 'default_page',
			'label' => 'Default Page',
			'rules' => 'trim|required'
		),
	);
	
	function __construct() 
	{
  		parent::__construct();
		$this->set_navigation('users');
		// load pager library
		$this->load->library('pager');
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
			} else {			
				$where = "{$search_by} LIKE LOWER('%{$search_text}%')";
			}
			
			$search_url = "?search_option=" . $search_by . "&search_string=" . $search_text;
		}
		
		$total_items = $this->users_model->get_user_count($where);
		
		// initialize pagination class
		// set pagination data
		$config = array(
		    'pagination_url' => '/admin/users/view/',
		    'total_items' => $total_items,
		    'per_page' => 10,
		    'uri_segment' => 4,
		);
		
		
		$this->pager->set_config($config);
		
		$users = $this->users_model->get_users($where, array('rows' => $this->pager->per_page, 'offset' => $this->pager->offset));
		
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
				
				$total_items = $this->users_model->search();
				$users = $this->users_model->search_users($search,$query,array('rows' => $this->pager->per_page, 'offset' => $this->pager->offset));

			}
		}

		// search vars
		$this->template->search_by = $search_by;
		$this->template->search_text = $search_text;
		$this->template->search_url = $search_url;
		
		$this->pager->set_config($config);

		$this->template->users = $users;
		$this->template->view('users/list');
	}
	
	public function add()
	{
		
		$facilities = $this->facilities_model->get_facilities();
	

		$data = array(
			'facilities' => $facilities
		);

		$html = $this->load->view('/users/add', $data, TRUE);

		echo json_encode(array("status"=>"1","html"=>$html));
		return;		
	}
	
	public function confirm_add() {
		$_username = trim($this->input->post('_username'));
		$_email = trim($this->input->post('_email'));
		$_password = $this->input->post('_password');
		$_first_name = trim($this->input->post('_first_name'));
		$_last_name = trim($this->input->post('_last_name'));
		$_mobile_number = trim($this->input->post('_mobile_number'));
		$_user_facility = $this->input->post('_user_facility');
		$_is_active = abs($this->input->post('_is_active'));
		$_default_page = trim($this->input->post('_default_page'));
		
		$_first_name = ucfirst($_first_name);
		$_last_name = ucfirst($_last_name);
		
		$user_facility_details = $this->facilities_model->get_facility_by_id($_user_facility);
		
		if ($_is_active == 0) {
			$_is_active = "No";
		} else {
			$_is_active = "Yes";
		}
		
		// check if username is already used
		$user_details = $this->users_model->get_user_by_username($_username);
		
		if (count($user_details) == 0) {
						
			$html = "<p><label>You are about to add a new User with the following details:</label>	
					<table class='table table-striped table-bordered'>
						<thead>
						</thead>
						<tbody>
							<tr>
								<td style='width:100px;'><label><strong>Username</strong></label></td>
								<td><label class=''>{$_username}</label></td>		
							</tr>
							<tr>
								<td style='width:100px;'><label><strong>Email</strong></label></td>
								<td><label class=''>{$_email}</label></td>		
							</tr>
							<tr>
								<td style='width:100px;'><label><strong>Password</strong></label></td>
								<td><input type='password' disabled='disabled' value='{$_password}' /></td>		
							</tr>	
							<tr>
								<td style='width:100px;'><label><strong>First Name</strong></label></td>
								<td><label class=''>{$_first_name}</label></td>		
							</tr>
							<tr>
								<td style='width:100px;'><label><strong>Last Name</strong></label></td>
								<td><label class=''>{$_last_name}</label></td>		
							</tr>
							<tr>
								<td style='width:100px;'><label><strong>Mobile Number</strong></label></td>
								<td><label class=''>{$_mobile_number}</label></td>		
							</tr>
							<tr>
								<td style='width:100px;'><label><strong>User Facility</strong></label></td>
								<td><label class=''>{$user_facility_details->facility_name}</label></td>		
							</tr>
							<tr>
								<td style='width:100px;'><label><strong>Active</strong></label></td>
								<td><label class=''>{$_is_active}</label></td>		
							</tr>
								<tr>
									<td style='width:100px;'><label><strong>Default Page</strong></label></td>
									<td><label class=''>{$_default_page}</label></td>		
								</tr>
						</tbody>
					</table>				
					<label>Do you want to proceed?</label>
				</p>";
		
			echo json_encode(array("status"=>"1","html"=>$html));
		} else {
			// username already exists
			echo json_encode(array("status"=>"0","html"=>"<p>Username already exists. Please try another.</p>"));
		}
		return;
		
	}
	
	public function add_user() {
		$_username = trim($this->input->post('_username'));
		$_email = trim($this->input->post('_email'));
		$_password = $this->input->post('_password');
		$_first_name = trim($this->input->post('_first_name'));
		$_last_name = trim($this->input->post('_last_name'));
		$_mobile_number = trim($this->input->post('_mobile_number'));
		$_user_facility = $this->input->post('_user_facility');
		$_is_active = abs($this->input->post('_is_active'));
		$_default_page = trim($this->input->post('_default_page'));
		
		$_first_name = ucfirst($_first_name);
		$_last_name = ucfirst($_last_name);
	
		// insert the new product
		$data = array(
			'username' => $_username,
			'email' => $_email,
			'password' => md5(strtoupper($_password)),
			'first_name' => $_first_name,
			'last_name' => $_last_name,
			'mobile_number' => $_mobile_number,
			'is_active' => $_is_active,
			'default_page' => $_default_page
		);
		$this->users_model->insert_user($data);		
		$user_id = $this->users_model->insert_id();
		
		//LOGGING OF USER
		$details_after = array('id' => 0, 'details' => $data);
		$details_after = json_encode($details_after);
		$new_user_data_logs = array(
			'user_id' => $this->user->user_id,
			'module_name' => 'USERS',
			'table_name' => 'ad_users',
			'action' => 'ADD',
			'details_before' => "",
			'details_after' => $details_after,
			'remarks' => ""
		);
		$this->tracking_model->insert_logs('admin', $new_user_data_logs);
		
		$data = array(
			"user_id" => $user_id,
			"facility_id" => $_user_facility,
			"is_default" => 1
		);
		
		$this->users_model->insert_user_facilities($data);
		
		//LOGGING OF USER FACILITY
		$details_after = array('id' => 0, 'details' => $data);
		$details_after = json_encode($details_after);
		$new_product_data_logs = array(
			'user_id' => $this->user->user_id,
			'module_name' => 'USERS',
			'table_name' => 'ad_user_facilities',
			'action' => 'ADD',
			'details_before' => "",
			'details_after' => $details_after,
			'remarks' => ""
		);
		$this->tracking_model->insert_logs('admin', $new_product_data_logs);
	
		echo json_encode(array("status"=>"1"));
		return;		
	}


	public function edit($user_id = 0)
	{
		$user = $this->users_model->get_user_by_id($user_id);
		
		if ($_POST and !empty($user))
		{
			// post done here
			// add password validation rule
			$this->_validation_rule[] = array(
				'field' => 'password',
				'label' => 'Password',
				'rules' => 'trim|min_length[4]|callback__verify_password'
			);
			$this->_validation_rule[] = array(
				'field' => 'new_password',
				'label' => 'New Password',
				'rules' => 'trim|min_length[4]|callback__verify_new_password'
			);
			$this->_validation_rule[] = array(
				'field' => 'reenter_password',
				'label' => 'Re-enter Password',
				'rules' => 'trim|min_length[4]|callback__verify_reenter_password'
			);
			$this->form_validation->set_rules($this->_validation_rule);
			
			//$test = set_value('username');
			//var_dump("1");
			//return;
			
			if ($this->form_validation->run()) 
			{

				// insert the new user
				$data = array(
					'username' => set_value('username'),
					'email' => set_value('email'),
					'first_name' => set_value('first_name'),
					'last_name' => set_value('last_name'),
					'mobile_number' => set_value('mobile_number'),
					'role_id' => set_value('role_id'),
					'is_active' => set_value('is_active'),
					'default_page' => set_value('default_page')
				);
				$password = $this->input->post('orig_password');
				$new_password = set_value('new_password');
				//save password as strtoupper
				if (!empty($new_password))
				{
					$password = md5(strtoupper($new_password));
				}
				$data['password'] = $password;
				
				$this->users_model->update_users($data, array('user_id' => $user_id));
				
				//LOGGING FOR EDIT USER
				$details_before = array('id' => $user_id, 'details' => $user);
				$details_before = json_encode($details_before);
				$details_after = array('id' => $user_id, 'details' => $data);
				$details_after = json_encode($details_after);
				$update_user_data_logs = array(
					'user_id' => $this->user->user_id,
					'module_name' => 'USERS',
					'table_name' => 'ad_users',
					'action' => 'UPDATE',
					'details_before' => $details_before,
					'details_after' => $details_after,
					'remarks' => ""
				);
				$this->tracking_model->insert_logs('admin', $update_user_data_logs);
				

				$new_facility = set_value('user_facility');
				$orig_facility = $this->input->post('orig_facility');


				if($orig_facility != $new_facility)
				{

					$where = array(
						"user_id" => $user_id,
						"facility_id" => $orig_facility
					);
					$previous_data = $this->users_model->get_user_facilities($where);
					$previous_data = $previous_data[0];
					//remove default on old facility
					$this->users_model->update_user_facilities(array("is_default" => 0),$where);
	
					$details_before = array('id' => array('user_id' => $previous_data->user_id, 'facility_id' => $previous_data->facility_id), 'details' => array("is_default" => $previous_data->is_default));
					$details_before = json_encode($details_before);
					$details_after = array('id' => array('user_id' => $previous_data->user_id, 'facility_id' => $previous_data->facility_id), 'details' => array("is_default" => 1));
					$details_after = json_encode($details_after);
					$update_user_data_logs = array(
						'user_id' => $this->user->user_id,
						'module_name' => 'USERS',
						'table_name' => 'ad_user_facilities',
						'action' => 'UPDATE',
						'details_before' => $details_before,
						'details_after' => $details_after,
						'remarks' => ""
					);
					$this->tracking_model->insert_logs('admin', $update_user_data_logs);
						
					//check if facility is already in the user's list
					$where = array(
						"user_id" => $user_id,
						"facility_id" => $new_facility
					);

					$facility = $this->users_model->get_user_facilities($where);

					if(!is_null($facility) && !empty($facility))
					{
						$previous_data = $this->users_model->get_user_facilities($where);
						$previous_data = $previous_data[0];
					
						$this->users_model->update_user_facilities(array("is_default" => 1), $where);
						
						$details_before = array('id' => array('user_id' => $previous_data->user_id, 'facility_id' => $previous_data->facility_id), 'details' => array("is_default" => $previous_data->is_default));
						$details_before = json_encode($details_before);
						$details_after = array('id' => array('user_id' => $previous_data->user_id, 'facility_id' => $previous_data->facility_id), 'details' => array("is_default" => 1));
						$details_after = json_encode($details_after);
						$update_user_data_logs = array(
							'user_id' => $this->user->user_id,
							'module_name' => 'USERS',
							'table_name' => 'ad_user_facilities',
							'action' => 'UPDATE',
							'details_before' => $details_before,
							'details_after' => $details_after,
							'remarks' => ""
						);
						$this->tracking_model->insert_logs('admin', $update_user_data_logs);
					}
					else
					{
						$data = array(
							"user_id" => $user_id,
							"facility_id" => $new_facility,
							"is_default" => 1
						);
						
						$this->users_model->insert_user_facilities($data);
						
						$details_after = array('id' => 0, 'details' => $data);
						$details_after = json_encode($details_after);
						$insert_user_data_logs = array(
							'user_id' => $this->user->user_id,
							'module_name' => 'USERS',
							'table_name' => 'ad_user_facilities',
							'action' => 'ADD',
							'details_before' => "",
							'details_after' => $details_after,
							'remarks' => ""
						);
						$this->tracking_model->insert_logs('admin', $insert_user_data_logs);
					}
				}

				redirect('/admin/users');
				return;
				
				
			}
		}

		$user_facility = $this->users_model->get_user_facilities(array("user_id" => $user_id,"is_default" => 1));
		$user_facility_id = "";
		if(!empty($user_facility))
		{
			$user_facility = $user_facility[0];
			$user_facility_id = $user_facility->facility_id;
		}
		$this->template->user = $user;
		$this->template->facilities = $this->facilities_model->get_facilities();
		$this->template->user_facility_id = $user_facility_id;
		$this->template->view('users/edit');
		
	}
	
	public function delete($user_id = 0)
	{
		$_user_id = $this->input->post('_user_id');		
		$user = $this->users_model->get_user_by_id($_user_id);
		
		/*if ($_POST and !empty($user))
		{
			$_user_id = $this->input->post('user_id');
			if (!empty($_user_id)) if ($_user_id == $user_id)
			{
				$this->users_model->delete_users(array('user_id' => $user_id));
				redirect('/admin/users');
				return;
			}
		}
		
		$this->template->user = $user;
		$this->template->view('users/delete');*/
		$data = array(
			'user_id' => $_user_id,
			'is_default' => 1
		);
		
		$user_facility_details = $this->users_model->get_user_facilities($data);		
		$user_facility_details = $user_facility_details[0];
		
		
		$data = array(
			'user' => $user,
			'user_facility_details' => $user_facility_details			
		);		
		$html = $this->load->view('/users/delete', $data, TRUE);

		echo json_encode(array("status"=>"1","html"=>$html));
		return;
		
	}

	public function confirm_delete() {
		$_user_id = $this->input->post('_user_id');
	
		$html = "<p><label>Are you sure you want to proceed?</label>									
				</p>";
		
		echo json_encode(array("status"=>"1","html"=>$html));
		return;
		
	}
	
	public function delete_user() {	
		$_user_id = $this->input->post('_user_id');
		$user = $this->users_model->get_user_by_id($_user_id);
		
		if (!empty($_user_id)) {
			// delete to ad_users		
			$this->users_model->delete_users(array('user_id' => $_user_id));
			// delete to ad_user_facilities
			$this->users_model->delete_user_facilities(array('user_id' => $_user_id));		
			
			//LOGGING
			$details_before = array('id' => $_user_id, 'details' => $user);
			$details_before = json_encode($details_before);
			$delete_user_data_logs = array(
				'user_id' => $this->user->user_id,
				'module_name' => 'USERS',
				'table_name' => 'ad_users',
				'action' => 'DELETE',
				'details_before' => $details_before,
				'details_after' => "",
				'remarks' => ""
			);
			$this->tracking_model->insert_logs('admin', $delete_user_data_logs);
								
		} else {
			echo json_encode(array("status"=>"0", "message" => "Invalid User ID"));
			return;
		}
	
		echo json_encode(array("status"=>"1"));
		return;
		
	}


	public function privileges($user_id = 0)
	{
		if ($_POST)
		{
			$_user_id = $this->input->post('user_id');
			$_privileges = $this->input->post('privileges');
			
			// remove all user privileges
			$this->users_model->delete_user_privileges("user_id = {$_user_id}");
			// add new privileges
			
			foreach($_privileges as $p)
			{
				$data = array("user_id"=>$_user_id,"privilege_id"=>$p);
				$this->users_model->insert_user_privilege($data);
			}
			
			echo json_encode(array("status"=>1));
			return;
		}
				
		$user = $this->users_model->get_user_by_id($user_id);				
		$user_privileges = $this->users_model->get_user_privileges("user_id = {$user_id}");		
		
		if (!empty($user_privileges))
		{
			$up_array = array();
			foreach ($user_privileges as $up) $up_array[] = $up->privilege_id;			
			$where = "privilege_id NOT IN (".implode(",",$up_array).")";
		}
		else
			$where = null;
		
		$privileges = $this->systems_model->get_privileges($where);

		$this->template->user = $user;
		$this->template->user_privileges = $user_privileges;
		$this->template->privileges = $privileges;
		$this->template->view('users/privileges');
		
	}

	public function facilities($user_id = 0)
	{

		if ($_POST)
		{
			$_user_id = $this->input->post('user_id');
			$_facilities = $this->input->post('facilities');
			$default = abs($this->input->post('default'));
			
			// remove all user facilities
			$this->users_model->delete_user_facilities("user_id = {$_user_id}");
			

			// add new facilities			
			foreach($_facilities as $f)
			{
				if ($default==0) $default = $f;

				$is_default = $default == $f ? $is_default=1 : $is_default = 0;
				$data = array("user_id"=>$_user_id,"facility_id"=>$f,"is_default"=>$is_default);
				$this->users_model->insert_user_facilities($data);
			}
			
			echo json_encode(array("status"=>1));
			return;
		}
				
		$user = $this->users_model->get_user_by_id($user_id);				
		$user_facilities = $this->users_model->get_user_facilities("user_id = {$user_id}");		
		
		if (!empty($user_facilities))
		{
			$uf_array = array();
			foreach ($user_facilities as $uf) $uf_array[] = $uf->facility_id;			
			$where = "facility_id NOT IN (".implode(",",$uf_array).")";
		}
		else
			$where = null;
		
		$facilities = $this->facilities_model->get_facilities($where);

		$this->template->user = $user;
		$this->template->user_facilities = $user_facilities;
		$this->template->facilities = $facilities;
		$this->template->view('users/facilities');
		
	}

	public function _verify_password($password)
	{
		$orig_password = trim($this->input->post('orig_password'));

		if (!empty($password))
		{
			if($orig_password == md5(strtoupper($password)))
			{
				return TRUE;
			}
			else
			{
				$this->form_validation->set_message('_verify_password', 'The password you entered does not match your current password.');
				return FALSE;
			}
		}
		else
		{
			$new_password = trim($this->input->post('new_password'));
			if(!empty($new_password))
			{
				$this->form_validation->set_message('_verify_password', 'Please enter your current password.');
				return FALSE;
			}
			else{
				return TRUE;
			}
		}
	}

	public function _verify_new_password($password)
	{
		$old_password = trim($this->input->post('password'));

		if (!empty($old_password))
		{
			if(!empty($password))
			{
				return TRUE;
			}
			else
			{
				$this->form_validation->set_message('_verify_new_password', 'Please enter your new password.');
				return FALSE;
			}
		}
		else
		{
			return TRUE;
		}
	}

	public function _verify_reenter_password($password)
	{
		$new_password = trim($this->input->post('new_password'));
		$old_password = trim($this->input->post('password'));

		if (!empty($old_password))
		{

			if(!empty($new_password))
			{
				if($new_password == $password)
				{
					return TRUE;
				}
				else
				{
					$this->form_validation->set_message('_verify_reenter_password', 'The password you entered does not match your new password.');
					return FALSE;
				}
			}
		}
		else
		{
			return TRUE;
		}
	}
	
	public function check_username()
	{
		$username = trim($this->input->post("username"));
		
		$user = $this->users_model->get_user_by_username($username);
		
		if(!empty($user))
		{
			$this->return_json("error","Username already exists.");
			return;
		}
		
		$this->return_json("ok");
		return;
	}
}
