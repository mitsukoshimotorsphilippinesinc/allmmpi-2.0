<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Roles extends Systems_Controller 
{
	private $_validation_rule = array(
		array(
			'field' => 'user_role',
			'label' => 'User Role',
			'rules' => 'trim|required'
		),
		array(
			'field' => 'default_url',
			'label' => 'Default URL',
			'rules' => 'trim|required'
		)
	);
	
	function __construct() 
	{
  		parent::__construct();
		$this->set_navigation('roles');
		// load pager library
		$this->load->library('pager');
	}
	
	public function index() 
	{ 
		$this->roles();
	}
	
	public function roles()
	{
		
		$where = NULL;
		$total_items = $this->users_model->get_user_role_count($where);
		
		// initialize pagination class
		// set pagination data
		$config = array(
		    'pagination_url' => '/admin/roles/index/',
		    'total_items' => $total_items,
		    'per_page' => 10,
		    'uri_segment' => 4,
		);
		
		$this->pager->set_config($config);
		
		$this->template->user_roles = $this->users_model->get_user_roles($where, array('rows' => $this->pager->per_page, 'offset' => $this->pager->offset));
		$this->template->view('roles/list');
	}
	
	public function add()
	{
		if ($_POST)
		{
			$this->form_validation->set_rules($this->_validation_rule);
			if ($this->form_validation->run()) 
			{
				error_log('valid');
				// insert the new user
				$data = array(
					'user_role' => set_value('user_role'),
					'default_url' => set_value('default_url')
				);
				$this->users_model->insert_user_role($data);
				
				redirect('/admin/roles');
				return;
			}
		}
		$this->template->facilities = $this->facilities_model->get_facilities();
		$this->template->view('roles/add');
		
	}
	
	public function edit($role_id = 0)
	{
		$user_role = $this->users_model->get_user_role_by_id($role_id);
		
		if ($_POST and !empty($user_role))
		{
			// post done here
			$this->form_validation->set_rules($this->_validation_rule);
			
			if ($this->form_validation->run()) 
			{

				// insert the new user
				$data = array(
					'user_role' => set_value('user_role'),
					'default_url' => set_value('default_url')
				);
				$this->users_model->update_user_roles($data, array('role_id' => $role_id));
				
				redirect('/admin/roles');
				return;
			}
		}
		
		$this->template->user_role = $user_role;
		$this->template->view('roles/edit');
		
	}
	
	public function delete($role_id = 0)
	{
		$user_role = $this->users_model->get_user_role_by_id($role_id);
		
		if ($_POST and !empty($user_role))
		{
			$_role_id = $this->input->post('role_id');
			if (!empty($_role_id)) if ($_role_id == $role_id)
			{
				$this->users_model->delete_user_roles(array('role_id' => $role_id));
				redirect('/admin/roles');
				return;
			}
		}
		
		$this->template->user_role = $user_role;
		$this->template->view('roles/delete');
		
	}

	public function privileges($role_id = 0)
	{

		if ($_POST)
		{
			$_role_id = $this->input->post('role_id');
			$_privileges = $this->input->post('privileges');
			
			// remove all user role privileges
			$this->users_model->delete_user_role_privileges("role_id = {$_role_id}");
			// add new privileges
			
			foreach($_privileges as $p)
			{
				$data = array("role_id"=>$_role_id,"privilege_id"=>$p);
				$this->users_model->insert_user_role_privilege($data);
			}
			
			echo json_encode(array("status"=>1));
			return;
		}
				
		$user_role = $this->users_model->get_user_role_by_id($role_id);				
		$user_role_privileges = $this->users_model->get_user_role_privileges("role_id = {$role_id}");
		
		if (!empty($user_role_privileges))
		{
			$up_array = array();
			foreach ($user_role_privileges as $up) $up_array[] = $up->privilege_id;			
			$where = "privilege_id NOT IN (".implode(",",$up_array).")";
		}
		else
			$where = null;
		
		$privileges = $this->systems_model->get_privileges($where);

		$this->template->user_role = $user_role;
		$this->template->user_role_privileges = $user_role_privileges;
		$this->template->privileges = $privileges;
		$this->template->view('roles/privileges');
		
	}

}
