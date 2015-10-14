<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Maintenance extends Admin_Controller {

	private $_agents_validation_rule = array(
		array(
			'field' => 'complete_name',
			'label' => 'Complete Name',
			'rules' => 'trim|required'
		),
		array(
			'field' => 'complete_address',
			'label' => 'Complete Address',
			'rules' => 'trim|required'
		),
		array(
			'field' => 'contact_number',
			'label' => 'Contact Number',
			'rules' => 'trim|required'
		),
		array(
			'field' => 'is_active',
			'label' => 'Is Active',
			'rules' => 'trim|required'
		)
	);

	function __construct()
	{
		parent::__construct();		
		$this->load->model('spare_parts_model');
		$this->load->library('pager');	
		$this->load->helper("spare_parts_helper");	
		$this->load->helper("breadcrumb_helper");	

		$this->db_spare_parts = $this->load->database('spare_parts', TRUE);

	}

	public $segment_name = "maintenance";

	public function index()
	{
		$this->template->view('maintenance/dashboard');
	}

	// AGENTS
	// ------------------------------------------
	public function agents()
	{
		$search_by = trim($this->input->get("search_option"));
		$search_text = trim($this->input->get("search_string"));


		$search_url = "";

		if (($search_text == "") || empty($search_text)) {
			$where = NULL;			
		} else {
			$where = "{$search_by} LIKE LOWER('%{$search_text}%')";
			$search_url = "?search_option=" . $search_by . "&search_string=" . $search_text;
		}	


		// set pagination data
		$config = array(
		    'pagination_url' => '/spare_parts/maintenance/agents/',
		    'total_items' => $this->spare_parts_model->get_agent_count($where),
		    'per_page' => 5,
		    'uri_segment' => 4,
		);

		// search vars
		$this->template->search_by = $search_by;
		$this->template->search_text = $search_text;
		$this->template->search_url = $search_url;
		

		$this->pager->set_config($config);
		$this->template->agents = $this->spare_parts_model->get_agent(null, array('rows' => $this->pager->per_page, 'offset' => $this->pager->offset), 'complete_name');
		$this->template->view('maintenance/agents/list');
	}

	public function add_agent()
	{
		if ($_POST)
		{
			// post done here
			$this->form_validation->set_rules($this->_agents_validation_rule);
			if ($this->form_validation->run())
			{
				$this->spare_parts_model->update_agent(array('is_active' => 0),array());

				// insert the new results
				$data = array(
					'complete_name' => strtoupper(set_value('complete_name')),
					'complete_address' => strtoupper(set_value('complete_address')),					
					'contact_number' => strtoupper(set_value('contact_number'))
				);
				$this->spare_parts_model->insert_agent($data);
				
				$insert_id = $this->spare_parts_model->insert_id();
				
				/*//logging of action
				$details_after = array('id' => $insert_id, 'details' => $data);
				$details_after = json_encode($details_after);
				$add_result_log_data = array(
					'user_id' => $this->user->user_id,
					'module_name' => 'RESULTS',
					'table_name' => 'sm_results',
					'action' => 'ADD',
					'details_after' => $details_after,
					'remarks' => "",
				);

				$this->tracking_model->insert_logs('admin', $add_result_log_data);
				*/
				redirect('/spare_parts/maintenance/agents');
				return;
			}
		}
		$this->template->view('spare_parts/maintenance/agents/add');
	}

	public function edit_agent($agent_id = 0)
	{
		$agent_details = $this->spare_parts_model->get_agent_by_id($agent_id);


		if ($_POST and !empty($agent_details))
		{
			// post done here
			$this->form_validation->set_rules($this->_agents_validation_rule);

			if ($this->form_validation->run())
			{
				//$this->spare_parts_model->update_agent(array('is_active' => 0),array());
				// insert the new results
				$data = array(					
					'complete_name' => strtoupper(set_value('complete_name')),
					'complete_address' => strtoupper(set_value('complete_address')),
					'contact_number' => strtoupper(set_value('contact_number')),
					'is_active' => set_value('is_active'),
				);

				$this->spare_parts_model->update_agent($data, array('agent_id' => $agent_id));
				
				/*//logging of action
				$details_before = array('id' => $result_id, 'details' => array());
				$details_after = array('id' => $result_id, 'details' => array());
				
				foreach($data as $k => $v)
				{
					if($result->$k != $v)
					{
						$details_before['details'][$k] = $result->$k;
						$details_after['details'][$k] = $v;
					}
				}
				$details_before = json_encode($details_before);
				$details_after = json_encode($details_after);
				$update_result_log_data = array(
					'user_id' => $this->user->user_id,
					'module_name' => 'RESULTS',
					'table_name' => 'sm_results',
					'action' => 'UPDATE',
					'details_before' => $details_before,
					'details_after' => $details_after,
					'remarks' => "",
				);

				$this->tracking_model->insert_logs('admin', $update_result_log_data);*/
				
				redirect('/spare_parts/maintenance/agents');
				return;
			}
		}

		$this->template->agent_details = $agent_details;
		$this->template->view('spare_parts/maintenance/agents/edit');

	}

	public function delete_agent($agent_id = 0)
	{
		$agent_details = $this->spare_parts_model->get_agent_by_id($agent_id);

		if ($_POST and !empty($agent_details))
		{
			$_agent_id = $this->input->post('agent_id');
			if (!empty($_agent_id)) if ($_agent_id == $agent_id)
			{
				$this->spare_parts_model->delete_agent(array('agent_id' => $agent_id));
				
				/*//logging of action
				$details_before = array('id' => $agent_id, 'details' => $agent_details);
				$details_before = json_encode($details_before);
				
				$delete_announcement_log_data = array(
					'user_id' => $this->user->user_id,
					'module_name' => 'RESULTS',
					'table_name' => 'sm_results',
					'action' => 'DELETE',
					'details_before' => $details_before,
					'remarks' => "",
				);

				$this->tracking_model->insert_logs('admin', $delete_announcement_log_data);*/
				
				redirect('/spare_parts/maintenance/agents');
				return;
			}
		}

		$this->template->agent_details = $agent_details;
		$this->template->view('spare_parts/maintenance/agents/delete');

	}

	public function view_agent($agent_id = 0)
	{
		$agent_details = $this->spare_parts_model->get_agent_by_id($agent_id);

		$this->template->agent_details = $agent_details;
		$this->template->view('spare_parts/maintenance/agents/view');

	}


	public function update_image()
	{
		$filename = $this->input->post('filename');
		$agent_id = $this->input->post('agent_id');

		$data = array("image_filename"=>$filename);

		$this->spare_parts_model->update_agent($data, "agent_id = {$agent_id}");
		
		/*//logging of action
		$details_before = array('id' => $result_id, 'details' => array("image_filename"=>$filename));
		$details_after = array('id' => $result_id, 'details' => array("image_filename"=>$filename));
		
		$details_before = json_encode($details_before);
		$details_after = json_encode($details_after);
		$update_result_log_data = array(
			'user_id' => $this->user->user_id,
			'module_name' => 'RESULTS',
			'table_name' => 'sm_results',
			'action' => 'UPDATE',
			'details_before' => $details_before,
			'details_after' => $details_after,
			'remarks' => "",
		);

		$this->tracking_model->insert_logs('admin', $update_result_log_data);*/
		
		$this->return_json('ok','');
	}

	// DEALERS
	// ------------------------------------------

	private $_dealers_validation_rule = array(
		array(
			'field' => 'complete_name',
			'label' => 'Complete Name',
			'rules' => 'trim|required'
		),
		array(
			'field' => 'complete_address',
			'label' => 'Complete Address',
			'rules' => 'trim|required'
		),
		array(
			'field' => 'contact_number',
			'label' => 'Contact Number',
			'rules' => 'trim|required'
		),
		array(
			'field' => 'is_active',
			'label' => 'Is Active',
			'rules' => 'trim|required'
		)
	);

	// ------------------------------------------
	public function dealers()
	{
		$search_by = trim($this->input->get("search_option"));
		$search_text = trim($this->input->get("search_string"));


		$search_url = "";

		if (($search_text == "") || empty($search_text)) {
			$where = NULL;			
		} else {
			$where = "{$search_by} LIKE LOWER('%{$search_text}%')";
			$search_url = "?search_option=" . $search_by . "&search_string=" . $search_text;
		}	


		// set pagination data
		$config = array(
		    'pagination_url' => '/spare_parts/maintenance/dealers/',
		    'total_items' => $this->spare_parts_model->get_dealer_count($where),
		    'per_page' => 20,
		    'uri_segment' => 4,
		);

		// search vars
		$this->template->search_by = $search_by;
		$this->template->search_text = $search_text;
		$this->template->search_url = $search_url;
		

		$this->pager->set_config($config);
		$this->template->dealers = $this->spare_parts_model->get_dealer(null, array('rows' => $this->pager->per_page, 'offset' => $this->pager->offset), 'complete_name');
		$this->template->view('maintenance/dealers/list');
	}

	public function add_dealer()
	{
		if ($_POST)
		{
			// post done here
			$this->form_validation->set_rules($this->_agents_validation_rule);
			if ($this->form_validation->run())
			{
				$this->spare_parts_model->update_dealer(array('is_active' => 0),array());

				// insert the new results
				$data = array(
					'complete_name' => strtoupper(set_value('complete_name')),
					'complete_address' => strtoupper(set_value('complete_address')),					
					'contact_number' => strtoupper(set_value('contact_number'))
				);
				$this->spare_parts_model->insert_dealer($data);
				
				$insert_id = $this->spare_parts_model->insert_id();
				
				/*//logging of action
				$details_after = array('id' => $insert_id, 'details' => $data);
				$details_after = json_encode($details_after);
				$add_result_log_data = array(
					'user_id' => $this->user->user_id,
					'module_name' => 'RESULTS',
					'table_name' => 'sm_results',
					'action' => 'ADD',
					'details_after' => $details_after,
					'remarks' => "",
				);

				$this->tracking_model->insert_logs('admin', $add_result_log_data);
				*/
				redirect('/spare_parts/maintenance/dealers');
				return;
			}
		}
		$this->template->view('spare_parts/maintenance/dealers/add');
	}

	public function privileges() 
	{
		
		$search_by = trim($this->input->get("search_option"));
		$search_text = trim($this->input->get("search_string"));

		$search_url = "";
		
		// get department_id
		$system = $this->uri->segment(1);

		$department_details = $this->human_relations_model->get_department_by_url($system);


		$where = "department_id = '{$department_details->department_id}' AND is_employed = 1";

		if (($search_text == "") || empty($search_text)) {
			$where .= "";			
		} else {
			if ($search_by == 'name') {				
				$where .= " AND concat(first_name, ' ', last_name) LIKE LOWER('%{$search_text}%')";
			} else {			
				$where .= " AND {$search_by} LIKE LOWER('%{$search_text}%')";
			}
			
			$search_url = "?search_option=" . $search_by . "&search_string=" . $search_text;
		}
		
		$total_items = $this->human_relations_model->get_employment_information_view_count($where);
		
		// initialize pagination class
		// set pagination data
		$config = array(
		    'pagination_url' => '/spare_parts/maintenance/privileges',
		    'total_items' => $total_items,
		    'per_page' => 10,
		    'uri_segment' => 4,
		);
		
		
		$this->pager->set_config($config);
		
		$users = $this->human_relations_model->get_employment_information_view($where, array('rows' => $this->pager->per_page, 'offset' => $this->pager->offset), "last_name, first_name");
		
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
				
				$total_items = $this->user_model->search();
				$users = $this->user_model->search_user($search,$query,array('rows' => $this->pager->per_page, 'offset' => $this->pager->offset));

			}
		}

		// search vars
		$this->template->search_by = $search_by;
		$this->template->search_text = $search_text;
		$this->template->search_url = $search_url;
		
		$this->pager->set_config($config);

		$this->template->users = $users;
		$this->template->view('maintenance/privileges/list');
	
	}


	public function edit_privilege($user_id = 0)
	{
		if ($_POST)
		{
			$_user_id = $this->input->post('user_id');
			$_privileges = $this->input->post('privileges');
			
			// remove all user privileges
			$this->user_model->delete_user_privilege("user_id = {$_user_id}");
			// add new privileges
			
			foreach($_privileges as $p)
			{
				$data = array("user_id"=>$_user_id,"privilege_id"=>$p);
				$this->user_model->insert_user_privilege($data);
			}
			
			echo json_encode(array("status"=>1));
			return;
		}
				
		$user = $this->user_model->get_user_by_id($user_id);
		$employment_information_details = $this->human_relations_model->get_employment_information_view("id_number = {$user->id_number}");
		$employment_information_details = $employment_information_details[0];

		$user_privileges = $this->user_model->get_user_privilege("user_id = {$user_id}");		
			
		$where = "system_code = '{$this->uri->segment(1)}'";

		if (!empty($user_privileges))
		{
			$up_array = array();
			foreach ($user_privileges as $up) $up_array[] = $up->privilege_id;			
			$where .= " AND privilege_id NOT IN (".implode(",",$up_array).")";
		}
		else
			$where .= null;
		
		$privileges = $this->user_model->get_privilege($where);

		$this->template->user = $user;
		$this->template->employment_information_details = $employment_information_details;
		$this->template->user_privileges = $user_privileges;
		$this->template->privileges = $privileges;
		$this->template->view('maintenance/privileges/privilege');
	}


	private $_validation_rule = array(
		array(
			'field' => 'is_active',
			'label' => 'Is Active',
			'rules' => 'trim|required'
		),
		array(
			'field' => 'default_page',
			'label' => 'Default Page',
			'rules' => 'trim|required'
		)
	);

	public function edit_user($user_id = 0)
	{
		$user = $this->user_model->get_user_by_id($user_id);
		
		if ($_POST and !empty($user))
		{

			$this->form_validation->set_rules($this->_validation_rule);

			if ($this->form_validation->run()) 
			{

				// insert the new user
				$data = array(					
					'is_active' => set_value('is_active'),
					'default_page' => set_value('default_page')
				);
				
				$this->user_model->update_user($data, array('user_id' => $user_id));
				
				/*//LOGGING FOR EDIT USER
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
				*/

				redirect('/spare_parts/maintenance/privileges');
				return;
			}
		}

		$employment_info_details = $this->human_relations_model->get_employment_information_view("id_number = '{$user->id_number}'");
		$employment_info_details = $employment_info_details[0];

		$this->template->user = $user;		
		$this->template->employment_info_details = $employment_info_details;
		$this->template->view('spare_parts/maintenance/privileges/edit_user');
	}
}