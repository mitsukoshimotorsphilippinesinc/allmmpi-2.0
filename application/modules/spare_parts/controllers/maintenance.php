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
		$this->load->model('warehouse_model');
		$this->load->library('pager');	
		$this->load->helper("spare_parts_helper");	
		$this->load->helper("systems_helper");	
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
		$this->template->agents = $this->spare_parts_model->get_agent($where, array('rows' => $this->pager->per_page, 'offset' => $this->pager->offset), 'complete_name');
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
				
				//logging of action
				$details_after = array('id' => $insert_id, 'details' => $data);
				$details_after = json_encode($details_after);
				$add_result_log_data = array(
					'id_number' => $this->user->id_number,
					'module_name' => 'SPARE PARTS',
					'table_name' => 'rf_agent',
					'action' => 'ADD',
					'details_after' => $details_after,
					'remarks' => "",
				);

				$this->spare_parts_model->insert_log('admin', $add_result_log_data);
				
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
				// insert the new results
				$data = array(					
					'complete_name' => strtoupper(set_value('complete_name')),
					'complete_address' => strtoupper(set_value('complete_address')),
					'contact_number' => strtoupper(set_value('contact_number')),
					'is_active' => set_value('is_active'),
				);

				$this->spare_parts_model->update_agent($data, array('agent_id' => $agent_id));
				
				//logging of action
				$details_before = array('id' => $agent_id, 'details' => array());
				$details_after = array('id' => $agent_id, 'details' => array());
				
				foreach($data as $k => $v)
				{
					if($agent_details->$k != $v)
					{
						$details_before['details'][$k] = $agent_details->$k;
						$details_after['details'][$k] = $v;
					}
				}

				$details_before = json_encode($details_before);
				$details_after = json_encode($details_after);
				$update_result_log_data = array(
					'id_number' => $this->user->id_number,
					'module_name' => 'SPARE PARTS',
					'table_name' => 'rf_agent',
					'action' => 'UPDATE',
					'details_before' => $details_before,
					'details_after' => $details_after,
					'remarks' => "",
				);

				$this->spare_parts_model->insert_logs('admin', $update_result_log_data);
				
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
				
				$data = array(
					"is_deleted" => 1
				);

				//$this->spare_parts_model->update_agent($data, "agent_id = {$agent_id}");

				$this->spare_parts_model->delete_agent(array('agent_id' => $agent_id));
				
				//logging of action
				$details_before = array('id' => $agent_id, 'details' => $agent_details);
				$details_before = json_encode($details_before);
				
				$delete_result_log_data = array(
					'id_number' => $this->user->id_number,
					'module_name' => 'SPARE PARTS',
					'table_name' => 'rf_agent',
					'action' => 'DELETE',
					'details_before' => $details_before,
					'remarks' => "",
				);

				$this->spare_parts_model->insert_logs('admin', $delete_result_log_data);
				
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
		$id = $this->input->post('_id');
		$maintenance_name = $this->input->post('maintenance_name');

		$data = array("image_filename"=>$filename);


		if ($maintenance_name == 'agent') {
			$this->spare_parts_model->update_agent($data, "agent_id = {$id}");
		} else {
			$this->spare_parts_model->update_dealer($data, "dealer_id = {$id}");
		}	
		
		//logging of action
		$details_before = array('id' => $result_id, 'details' => array("image_filename"=>$filename));
		$details_after = array('id' => $result_id, 'details' => array("image_filename"=>$filename));
		
		log_to_db("spare_parts", $this->user->id_number, "MAINTENANCE-DEALERS", "rf_dealer", "UPDATE", $details_before, $details_after);
		
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
			'field' => 'agent_id',
			'label' => 'Agent Name',
			'rules' => 'required'
		),
		array(
			'field' => 'max_discount',
			'label' => 'Max Discount',
			'rules' => 'required'
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
		$this->template->dealers = $this->spare_parts_model->get_dealer($where, array('rows' => $this->pager->per_page, 'offset' => $this->pager->offset), 'complete_name');
		$this->template->view('maintenance/dealers/list');
	}

	public function add_dealer()
	{
		if ($_POST)
		{
			// post done here
			$this->form_validation->set_rules($this->_dealers_validation_rule);
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
				
				//logging of action
				$details_after = array('id' => $insert_id, 'details' => $data);
				$details_after = json_encode($details_after);
				$add_result_log_data = array(
					'id_number' => $this->user->id_number,
					'module_name' => 'SPARE PARTS',
					'table_name' => 'rf_dealer',
					'action' => 'ADD',
					'details_after' => $details_after,
					'remarks' => "",
				);

				$this->spare_parts_model->insert_log('admin', $add_result_log_data);
				
				redirect('/spare_parts/maintenance/dealers');
				return;
			}
		}
		$this->template->view('spare_parts/maintenance/dealers/add');
	}

	public function edit_dealer($dealer_id = 0)
	{
		$dealer_details = $this->spare_parts_model->get_dealer_by_id($dealer_id);

		if ($_POST and !empty($dealer_details))
		{
			// post done here
			$this->form_validation->set_rules($this->_dealers_validation_rule);

			if ($this->form_validation->run())
			{				
				// insert the new results
				$data = array(					
					'complete_name' => strtoupper(set_value('complete_name')),
					'complete_address' => strtoupper(set_value('complete_address')),
					'contact_number' => strtoupper(set_value('contact_number')),
					'agent_id' => set_value('agent_id'),
					'max_discount' => (set_value('max_discount') / 100),
					'is_active' => set_value('is_active'),
				);

				$this->spare_parts_model->update_dealer($data, array('dealer_id' => $dealer_id));
				
				//logging of action
				$details_before = array('id' => $dealer_id, 'details' => array());
				$details_after = array('id' => $dealer_id, 'details' => array());
				
				foreach($data as $k => $v)
				{
					if($dealer_details->$k != $v)
					{
						$details_before['details'][$k] = $dealer_details->$k;
						$details_after['details'][$k] = $v;
					}
				}

				log_to_db("spare_parts", $this->user->id_number, "MAINTENANCE-DEALERS", "rf_dealer", "UPDATE", $details_before, $details_after);
				
				redirect('/spare_parts/maintenance/dealers');
				return;
			}
		}

		$this->template->dealer_details = $dealer_details;
		$this->template->view('spare_parts/maintenance/dealers/edit');
	}

	public function delete_dealer($dealer_id = 0)
	{
		$dealer_details = $this->spare_parts_model->get_dealer_by_id($dealer_id);

		if ($_POST and !empty($dealer_details))
		{
			$_dealer_id = $this->input->post('dealer_id');
			if (!empty($_dealer_id)) if ($_dealer_id == $dealer_id)
			{
				
				//$data = array(
				//	"is_deleted" => 1
				//);

				//$this->spare_parts_model->update_agent($data, "agent_id = {$agent_id}");

				$this->spare_parts_model->delete_dealer(array('dealer_id' => $dealer_id));
				
				//logging of action
				$details_before = array('id' => $dealer_id, 'details' => $dealer_details);
				$details_after = "";
				
				log_to_db("spare_parts", $this->user->id_number, "MAINTENANCE-DEALERS", "rf_dealer", "DELETE", $details_before, $details_after);

				redirect('/spare_parts/maintenance/dealers');
				return;
			}
		}

		$this->template->dealer_details = $dealer_details;
		$this->template->view('spare_parts/maintenance/dealers/delete');

	}

	public function view_dealer($dealer_id = 0)
	{
		$dealer_details = $this->spare_parts_model->get_dealer_by_id($dealer_id);

		$this->template->dealer_details = $dealer_details;
		$this->template->view('spare_parts/maintenance/dealers/view');

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

	public function inventory2()
	{
		$search_by = trim($this->input->get("search_option"));
		$search_text = trim($this->input->get("search_string"));


		$search_url = "";

		if (($search_text == "") || empty($search_text)) {
			$where = NULL;			
		} else {
			$where = "WHERE {$search_by} LIKE LOWER('%{$search_text}%')";
			$search_url = "?search_option=" . $search_by . "&search_string=" . $search_text;
		}	

		$sql = "SELECT 
					sku, model_name, brand_name, description, SUM(good_quantity) AS good_quantity, SUM(bad_quantity) AS bad_quantity, srp, stock_limit
				FROM 
				 	is_item_view {$where}
				GROUP BY
					sku, model_name, brand_name, description				
				ORDER BY 
				 	sku";

		$query = $this->db_spare_parts->query($sql);
		$item_details = $query->result();			
		$query->free_result();

		// set pagination data
		$config = array(
		    'pagination_url' => '/spare_parts/maintenance/inventory/',
		    'total_items' => count($item_details),
		    'per_page' => 30,
		    'uri_segment' => 4,
		);

		$current_offset = $this->pager->per_page * $this->pager->offset;

		$sql = "SELECT 
					item_id, sku, model_name, brand_name, description, SUM(good_quantity) AS good_quantity, SUM(bad_quantity) AS bad_quantity, srp, stock_limit
				FROM 
				 	is_item_view {$where}
				GROUP BY
					sku, model_name, brand_name, description				
				ORDER BY 
				 	sku
				LIMIT 
					{$this->pager->per_page} 
				OFFSET 
					{$current_offset}";

		$query = $this->db_spare_parts->query($sql);
		$items = $query->result();			
		$query->free_result();			

		// search vars
		$this->template->search_by = $search_by;
		$this->template->search_text = $search_text;
		$this->template->search_url = $search_url;
		
		$this->pager->set_config($config);
		//$this->template->items = $this->spare_parts_model->get_item_view($where, array('rows' => $this->pager->per_page, 'offset' => $this->pager->offset), 'model_name');
		$this->template->items = $items;
		$this->template->view('maintenance/inventory/list');
	}


	public function inventory()
	{
		$search_by = trim($this->input->get("search_option"));
		$search_text = trim($this->input->get("search_string"));


		$search_url = "";

		if (($search_text == "") || empty($search_text)) {
			$where = NULL;			
		} else {
			$where = "WHERE {$search_by} LIKE LOWER('%{$search_text}%')";
			$search_url = "?search_option=" . $search_by . "&search_string=" . $search_text;
		}	

		$sql = "SELECT 
					item_id, sku, model_name, brand_name, description, SUM(good_quantity) AS good_quantity, SUM(bad_quantity) AS bad_quantity, srp, stock_limit
				FROM 
				 	is_item_view {$where}
				GROUP BY
					sku, model_name, brand_name, description				
				ORDER BY 
				 	sku";

		$query = $this->db_spare_parts->query($sql);
		$item_details = $query->result();			
		$query->free_result();

		// set pagination data
		$config = array(
		    'pagination_url' => '/spare_parts/maintenance/inventory/',
		    'total_items' => count($item_details),
		    'per_page' => 30,
		    'uri_segment' => 4,
		);

		// search vars
		$this->template->search_by = $search_by;
		$this->template->search_text = $search_text;
		$this->template->search_url = $search_url;
		
		//var_dump($this->pager->per_page . '|' . $this->pager->offset);

		$this->pager->set_config($config);

		$current_offset = $this->pager->per_page * $this->pager->offset;

		$sql = "SELECT 
					item_id, sku, model_name, brand_name, description, SUM(good_quantity) AS good_quantity, SUM(bad_quantity) AS bad_quantity, srp, stock_limit
				FROM 
				 	is_item_view {$where}
				GROUP BY
					sku, model_name, brand_name, description				
				ORDER BY 
				 	sku
				LIMIT 
					{$this->pager->per_page} 
				OFFSET 
					{$current_offset}";

		$query = $this->db_spare_parts->query($sql);
		$items = $query->result();			
		$query->free_result();		

		$this->template->items = $items;
		$this->template->view('maintenance/inventory/list');
	}


	// SPARE PART
	// ------------------------------------------
	private $_spare_parts_validation_rule = array(
		array(
			'field' => 'sku',
			'label' => 'SKU',
			'rules' => 'trim|required'
		),
		array(
			'field' => 'description',
			'label' => 'Description',
			'rules' => 'trim|required'
		),		
		array(
			'field' => 'is_active',
			'label' => 'Is Active',
			'rules' => 'trim|required'
		),
		array(
			'field' => 'srp',
			'label' => 'SRP',
			'rules' => 'trim|required'
		),
		array(
			'field' => 'stock_limit',
			'label' => 'Stock Limit',
			'rules' => 'trim|required'
		),
		array(
			'field' => 'motorcycle_brand_id',
			'label' => 'Motorcycle Brand',
			'rules' => 'required'
		),
		array(
			'field' => 'motorcycle_brand_model_id',
			'label' => 'Motorcycle Model',
			'rules' => 'required'
		),
		array(
			'field' => 'part_number',
			'label' => 'Part Number',
			'rules' => 'trim'
		)
	);

	public function spare_parts()
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
		    'pagination_url' => '/spare_parts/maintenance/spare_parts/',
		    'total_items' => $this->spare_parts_model->get_spare_part_count($where),
		    'per_page' => 30,
		    'uri_segment' => 4,
		);

		// search vars
		$this->template->search_by = $search_by;
		$this->template->search_text = $search_text;
		$this->template->search_url = $search_url;
		
		$this->pager->set_config($config);
		$this->template->spare_parts = $this->spare_parts_model->get_spare_part($where, array('rows' => $this->pager->per_page, 'offset' => $this->pager->offset), 'length(sku)');
		$this->template->view('maintenance/spare_parts/list');
	}

	public function add_spare_part()
	{
		if ($_POST)
		{
			// post done here
			$this->form_validation->set_rules($this->_spare_parts_validation_rule);

			if ($this->form_validation->run())
			{
				//$this->spare_parts_model->update_spare_part(array('is_active' => 0),array());
				$spare_part_details = $this->spare_parts_model->get_spare_part_by_sku(strtoupper(set_value('sku')));

				if (empty($spare_part_details)) {	
				
					$brand_details = $this->warehouse_model->get_motorcycle_brand_by_id(set_value('motorcycle_brand_id'));

					$brand_model_details = $this->warehouse_model->get_motorcycle_brand_model_class_view_by_id(set_value('motorcycle_brand_model_id'));

					// insert the new results
					$data = array(
						'sku' => strtoupper(set_value('sku')),
						'description' => strtoupper(set_value('description')),
						'part_number' => strtoupper(set_value('part_number')),					
						'motorcycle_brand_id' => abs(set_value('motorcycle_brand_id')),
						'brand_name' => strtoupper($brand_details->brand_name),
						'motorcycle_model_id' => abs(set_value('motorcycle_brand_model_id')),
						'model_name' => strtoupper($brand_model_details->model_name),
						'stock_limit' => abs(set_value('stock_limit')),
						'srp' => set_value('srp'),
						'is_active' => set_value('is_active'),
						'remarks' => set_value('remarks'),
					);
					$this->spare_parts_model->insert_spare_part($data);
					
					$insert_id = $this->spare_parts_model->insert_id();
					
					/*//logging of action				
					// TODO...
					*/

					redirect('/spare_parts/maintenance/spare_parts');
					return;
				} else {
					echo "ERROR";
				}
			}
		}
		$this->template->view('spare_parts/maintenance/spare_parts/add');
	}

	public function edit_spare_part($spare_part_id = 0)
	{
		$spare_part_details = $this->spare_parts_model->get_spare_part_by_id($spare_part_id);

		if ($_POST and !empty($spare_part_details))
		{
			// post done here
			$this->form_validation->set_rules($this->_spare_parts_validation_rule);

			if ($this->form_validation->run())
			{
				//$brand_details = $this->warehouse_model->get_motorcycle_brand_by_id(set_value('motorcycle_brand_id'));

				//$brand_model_details = $this->warehouse_model->get_motorcycle_brand_model_class_view_by_id(set_value('motorcycle_brand_model_id'));

				//$this->spare_parts_model->update_agent(array('is_active' => 0),array());				
				$data = array(
						'sku' => strtoupper(set_value('sku')),
						'description' => strtoupper(set_value('description')),
						'part_number' => strtoupper(set_value('part_number')),					
						'motorcycle_brand_id' => abs(set_value('motorcycle_brand_id')),
						//'brand_name' => strtoupper($brand_details->brand_name),
						'motorcycle_model_id' => abs(set_value('motorcycle_brand_model_id')),
						//'model_name' => strtoupper($brand_model_details->model_name),
						'stock_limit' => abs(set_value('stock_limit')),
						'srp' => set_value('srp'),
						'is_active' => set_value('is_active'),
						'remarks' => set_value('remarks'),
					);

				$this->spare_parts_model->update_spare_part($data, array('spare_part_id' => $spare_part_id));
				
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

				$this->tracking_model->insert_logs('admin', $update_result_log_data);
				*/
				
				redirect('/spare_parts/maintenance/spare_parts');
				return;
			}
		}

		//$brand_name_details = $this->warehouse_model->get_motorcycle_brand();
		
		$this->template->spare_part_details = $spare_part_details;
		//$this->template->brand_name_details = $brand_name_details;
		$this->template->view('spare_parts/maintenance/spare_parts/edit');

	}

	public function delete_spare_part($spare_part_id = 0)
	{
		$spare_part_details = $this->spare_parts_model->get_spare_part_by_id($spare_part_id);

		if ($_POST and !empty($spare_part_details))
		{
			
			$this->spare_parts_model->delete_spare_part(array('spare_part_id' => $spare_part_id));
			
			/*//logging of action
			*/
			
			redirect('/spare_parts/maintenance/spare_parts');
			return;
		
		}

		$this->template->spare_part_details = $spare_part_details;
		$this->template->view('spare_parts/maintenance/spare_parts/delete');

	}

	public function view_spare_part($spare_part_id = 0)
	{
		$spare_part_details = $this->spare_parts_model->get_spare_part_by_id($spare_part_id);

		$this->template->spare_part_details = $spare_part_details;
		$this->template->view('spare_parts/maintenance/spare_parts/view');

	}

	// RUNNERS
	// ------------------------------------------
	private $_runners_validation_rule = array(
		array(
			'field' => 'runner_name',
			'label' => 'Runner Name',
			'rules' => 'trim|required'
		),
		array(
			'field' => 'warehouse_name',
			'label' => 'Warehouse Name',
			'rules' => 'trim|required'
		),		
		array(
			'field' => 'is_active',
			'label' => 'Is Active',
			'rules' => 'trim|required'
		)
	);

	public function runners()
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
		    'pagination_url' => '/spare_parts/maintenance/runners/',
		    'total_items' => $this->spare_parts_model->get_runner_view_count($where),
		    'per_page' => 5,
		    'uri_segment' => 4,
		);

		// search vars
		$this->template->search_by = $search_by;
		$this->template->search_text = $search_text;
		$this->template->search_url = $search_url;
		
		$this->pager->set_config($config);
		$this->template->runners = $this->spare_parts_model->get_runner_view($where, array('rows' => $this->pager->per_page, 'offset' => $this->pager->offset), 'last_name');
		$this->template->view('maintenance/runners/list');
	}

	public function add_runner()
	{
		if ($_POST)
		{
			// post done here
			$this->form_validation->set_rules($this->_runners_validation_rule);
			if ($this->form_validation->run())
			{
				//$this->spare_parts_model->update_runner(array('is_active' => 0),array());

				// insert the new results
				$data = array(
					'id_number' => strtoupper(set_value('runner_name')),
					'warehouse_id' => strtoupper(set_value('warehouse_name')),					
					'is_active' => strtoupper(set_value('is_active'))
				);
				$this->spare_parts_model->insert_runner($data);
				
				$insert_id = $this->spare_parts_model->insert_id();
				
				/*//logging of action
				*/
				
				redirect('/spare_parts/maintenance/runners');
				return;
			}
		}
		
		$this->template->view('spare_parts/maintenance/runners/add');
	}

	public function edit_runner($runner_id = 0)
	{
		$runner_details = $this->spare_parts_model->get_runner_by_id($runner_id);


		if ($_POST and !empty($runner_details))
		{
			// post done here
			$this->form_validation->set_rules($this->_runners_validation_rule);

			if ($this->form_validation->run())
			{				
				$data = array(					
					'warehouse_id' => strtoupper(set_value('warehouse_name')),					
					'is_active' => strtoupper(set_value('is_active'))
				);				

				$this->spare_parts_model->update_runner($data, array('runner_id' => $runner_id));				
				
				//logging of actionv here
				
				
				redirect('/spare_parts/maintenance/runners');
				return;
			}
		}

		$this->template->runner_details = $runner_details;
		$this->template->view('spare_parts/maintenance/runners/edit');
	}

	public function delete_runner($runner_id = 0)
	{
		$runner_details = $this->spare_parts_model->get_runner_by_id($runner_id);

		if ($_POST and !empty($runner_details))
		{
			$_runner_id = $this->input->post('runner_id');
			if (!empty($_runner_id)) if ($_runner_id == $runner_id)
			{
				
				$this->spare_parts_model->delete_runner(array('runner_id' => $runner_id));
				
				/*//logging of action
				
				*/
				
				redirect('/spare_parts/maintenance/runners');
				return;
			}
		}

		$this->template->runner_details = $runner_details;
		$this->template->view('spare_parts/maintenance/runners/delete');

	}

	public function view_runner($runner_id = 0)
	{
		$runner_details = $this->spare_parts_model->get_agent_by_id($runner_id);

		$this->template->runner_details = $runner_details;
		$this->template->view('spare_parts/maintenance/runner/view');

	}

	public function get_runner_image()
	{
		$id_number = $this->input->post("id_number");

		$employment_view_details = $this->human_relations_model->get_employment_information_view_by_id($id_number);

		if ((empty($employment_view_details->image_filename)) || ($employment_view_details->image_filename == NULL) || (trim($employment_view_details->image_filename) == "")) {
			$image_display = "ni_". strtolower($employment_view_details->gender) .".png";
		} else {
			$image_display = $employment_view_details->image_filename;
		}

		$html = "<img id='agent_image' src='/assets/media/employees/{$image_display}' alt='' style='width:180px; height:180px;border:dashed 1px gray;'>";

		$this->return_json("1","OK", array("html" => $html));
		return;
	}


	// WAREHOUSE
	// ------------------------------------------
	private $_warehouse_validation_rule = array(
		array(
			'field' => 'warehouse_name',
			'label' => 'Warehouse Name',
			'rules' => 'trim|required'
		),
		array(
			'field' => 'description',
			'label' => 'Description',
			'rules' => 'trim|required'
		),
		array(
			'field' => 'warehouse_head',
			'label' => 'Warehouse Head',
			'rules' => 'trim|required'
		),		
		array(
			'field' => 'is_active',
			'label' => 'Is Active',
			'rules' => 'trim|required'
		)
	);

	public function warehouse()
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
		    'pagination_url' => '/spare_parts/maintenance/warehouse/',
		    'total_items' => $this->spare_parts_model->get_warehouse_view_count($where),
		    'per_page' => 5,
		    'uri_segment' => 4,
		);

		// search vars
		$this->template->search_by = $search_by;
		$this->template->search_text = $search_text;
		$this->template->search_url = $search_url;
		
		$this->pager->set_config($config);
		$this->template->warehouse = $this->spare_parts_model->get_warehouse_view($where, array('rows' => $this->pager->per_page, 'offset' => $this->pager->offset), 'warehouse_name');
		$this->template->view('maintenance/warehouse/list');
	}

	public function add_warehouse()
	{
		if ($_POST)
		{
			
			// post done here
			$this->form_validation->set_rules($this->_warehouse_validation_rule);
			if ($this->form_validation->run())
			{
				
				//$this->spare_parts_model->update_runner(array('is_active' => 0),array());

				// insert the new results
				$data = array(
					'warehouse_name' => strtoupper(set_value('warehouse_name')),
					'description' => strtoupper(set_value('description')),
					'warehouse_head' => strtoupper(set_value('warehouse_head')),
					'is_active' => set_value('is_active')
				);

				$this->spare_parts_model->insert_warehouse($data);
				
				$insert_id = $this->spare_parts_model->insert_id();
								
				/*//logging of action
				*/
				
				redirect('/spare_parts/maintenance/warehouse');
				return;
			}
		}
		
		$this->template->view('spare_parts/maintenance/warehouse/add');
	}

	public function edit_warehouse($warehouse_id = 0)
	{
		$warehouse_details = $this->spare_parts_model->get_warehouse_by_id($warehouse_id);


		if ($_POST and !empty($warehouse_details))
		{
			
			// post done here
			$this->form_validation->set_rules($this->_warehouse_validation_rule);

			if ($this->form_validation->run())
			{				
				$data = array(					
					'warehouse_name' => strtoupper(set_value('warehouse_name')),
					'description' => strtoupper(set_value('description')),
					'warehouse_head' => strtoupper(set_value('warehouse_head')),
					'is_active' => set_value('is_active')
				);				

				$this->spare_parts_model->update_warehouse($data, array('warehouse_id' => $warehouse_id));				
				
				//logging of actionv here
				
				
				redirect('/spare_parts/maintenance/warehouse');
				return;
			}
		}

		$this->template->warehouse_details = $warehouse_details;
		$this->template->view('spare_parts/maintenance/warehouse/edit');
	}

	public function delete_warehouse($warehouse_id = 0)
	{
		$warehouse_details = $this->spare_parts_model->get_warehouse_by_id($warehouse_id);

		if ($_POST and !empty($warehouse_details))
		{
			$_warehouse_id = $this->input->post('warehouse_id');
			if (!empty($_warehouse_id)) if ($_warehouse_id == $warehouse_id)
			{
				
				$this->spare_parts_model->delete_warehouse(array('warehouse_id' => $warehouse_id));
				
				/*//logging of action
				
				*/
				
				redirect('/spare_parts/maintenance/warehouse');
				return;
			}
		}

		$this->template->warehouse_details = $warehouse_details;
		$this->template->view('spare_parts/maintenance/warehouse/delete');

	}

	public function view_warehouse($warehouse_id = 0)
	{
		$warehouse_details = $this->spare_parts_model->get_warehouse_by_id($warehouse_id);

		$this->template->warehouse_details = $warehouse_details;
		$this->template->view('spare_parts/maintenance/warehouse/view');

	}

	private $_inventory_validation_rule = array(
		array(
			'field' => 'item_details',
			'label' => 'Item Details',
			'rules' => 'trim|required'
		),
		array(
			'field' => 'warehouse_id',
			'label' => 'Warehouse Name',
			'rules' => 'trim|required'
		),
		array(
			'field' => 'sku',
			'label' => 'SKU',
			'rules' => 'trim'
		),		
		array(
			'field' => 'good_quantity',
			'label' => 'Good Quantity',
			'rules' => 'required'
		),
		array(
			'field' => 'bad_quantity',
			'label' => 'Bad Quantity',
			'rules' => 'required'
		),		
		array(
			'field' => 'rack_location',
			'label' => 'Rack Location',
			'rules' => 'trim|required'
		)
	);


	public function add_inventory($spare_part_id = 0)	
	{

		$spare_part_details = $this->spare_parts_model->get_spare_part_by_id($spare_part_id);


		$sql = "SELECT 
					rack_location
				FROM
					is_item
				GROUP BY
					rack_location DESC	
				";

		$query = $this->db_spare_parts->query($sql);
		$rack_location_details = $query->result();			
		$query->free_result();			

		$where = "is_active = 1";
		$warehouse_details = $this->spare_parts_model->get_warehouse($where);


		if ($_POST)
		{
			// post done here
			$this->form_validation->set_rules($this->_inventory_validation_rule);

			if ($this->form_validation->run())
			{				
				$brand_details = $this->warehouse_model->get_motorcycle_brand_by_id(set_value('motorcycle_brand_id'));

				$brand_model_details = $this->warehouse_model->get_motorcycle_brand_model_class_view_by_id(set_value('motorcycle_brand_model_id'));

				// insert the new results
				$data = array(
					'warehouse_id' => abs(set_value('warehouse_id')),
					'sku' => strtoupper(set_value('sku')),
					'good_quantity' => set_value('good_quantity'),
					'bad_quantity' => set_value('bad_quantity'),
					'rack_location' => strtoupper(set_value('rack_location'))
				);
				$this->spare_parts_model->insert_item($data);
				
				$insert_id = $this->spare_parts_model->insert_id();
				
				/*//logging of action				
				// TODO...
				*/

				redirect('/spare_parts/maintenance/inventory');
				return;		
			}
		}
		
		$this->template->warehouse_details = $warehouse_details;
		$this->template->rack_location_details = $rack_location_details;
		$this->template->spare_part_details = $spare_part_details;
		$this->template->view('spare_parts/maintenance/inventory/add');
	}

	public function edit_inventory($item_id = 0)
	{
		
		$item_details = $this->spare_parts_model->get_item_by_id($item_id);

		//$spare_part_details = $this->spare_parts_model->get_spare_part_by_id($spare_part_id);


		$sql = "SELECT 
					rack_location
				FROM
					is_item
				GROUP BY
					rack_location DESC	
				";

		$query = $this->db_spare_parts->query($sql);
		$rack_location_details = $query->result();			
		$query->free_result();			

		$where = "is_active = 1";
		$warehouse_details = $this->spare_parts_model->get_warehouse($where);



		if ($_POST and !empty($item_details))
		{
			
			// post done here
			$this->form_validation->set_rules($this->_warehouse_validation_rule);

			if ($this->form_validation->run())
			{				
				$data = array(
					'warehouse_id' => abs(set_value('warehouse_id')),
					'sku' => strtoupper(set_value('sku')),
					'good_quantity' => set_value('good_quantity'),
					'bad_quantity' => set_value('bad_quantity'),
					'rack_location' => strtoupper(set_value('rack_location'))
				);		

				$this->spare_parts_model->update_item($data, array('item_id' => $item_id));				
				
				//logging of actionv here
				
				
				redirect('/spare_parts/maintenance/inventory');
				return;
			}
		}

		$this->template->item_details = $item_details;
		$this->template->warehouse_details = $warehouse_details;
		$this->template->rack_location_details = $rack_location_details;
		//$this->template->spare_part_details = $spare_part_details;
		$this->template->view('spare_parts/maintenance/inventory/edit');
	}

	public function delete_inventory($item_id = 0)
	{
		/*$warehouse_details = $this->spare_parts_model->get_warehouse_by_id($warehouse_id);

		if ($_POST and !empty($warehouse_details))
		{
			$_warehouse_id = $this->input->post('warehouse_id');
			if (!empty($_warehouse_id)) if ($_warehouse_id == $warehouse_id)
			{
				
				$this->spare_parts_model->delete_warehouse(array('warehouse_id' => $warehouse_id));
		*/		
				/*//logging of action
				
				*/
				
				redirect('/spare_parts/maintenance/warehouse');
				return;
			}
		}

		$this->template->warehouse_details = $warehouse_details;
		$this->template->view('spare_parts/maintenance/warehouse/delete');

	}



}