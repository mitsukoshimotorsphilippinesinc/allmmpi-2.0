<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Privileges extends Systems_Controller 
{	
	private $_validation_rule = array(
		array(					
			'field' => 'privilege_code',
			'label' => 'Code',
			'rules' => 'trim|required|min_length[4]'
		),
		array(					
			'field' => 'privilege_description',
			'label' => 'Description',
			'rules' => 'trim|required'
		),
		array(					
			'field' => 'privilege_uri',
			'label' => 'URI',
			'rules' => 'trim|required'
		),
		array(
			'field' => 'system_code',
			'label' => 'System',
			'rules' => 'trim|required'
		)
	);
	
	
	function __construct() 
	{
  		parent::__construct();

		$this->set_navigation('privileges');
		
		$this->load->model('systems_model');
		$this->load->model('users_model');

		// load pager library
		$this->load->library('pager');
	}
	
	public function index() 
	{ 
		$this->view();
	}
	
	public function view()
	{		
		$where = NULL;
		$total_items = $this->systems_model->get_privilege_count($where);
		
		// initialize pagination class
		// set pagination data
		$config = array(
		    'pagination_url' => '/admin/privileges/view/',
		    'total_items' => $total_items,
		    'per_page' => 10,
		    'uri_segment' => 4,
		);
		$this->pager->set_config($config);
		$limit = array('rows' => $this->pager->per_page, 'offset' => $this->pager->offset);
		$cms = $this->systems_model->get_privileges(array('system_code' => 'cms'), $limit);
		$inventory = $this->systems_model->get_privileges(array('system_code' => 'inventory'), $limit);
		$network = $this->systems_model->get_privileges(array('system_code' => 'network'), $limit);
		$site = $this->systems_model->get_privileges(array('system_code' => 'site'), $limit);
		$system = $this->systems_model->get_privileges(array('system_code' => 'system'), $limit);
		$webpoi = $this->systems_model->get_privileges(array('system_code' => 'webpoi'), $limit);
		$mis = $this->systems_model->get_privileges(array('system_code' => 'mis'), $limit);
				
		$this->template->cms = $cms;
		$this->template->inventory = $inventory;
		$this->template->network = $network;
		$this->template->site = $site;
		$this->template->system = $system;
		$this->template->webpoi = $webpoi;
		$this->template->mis = $mis;
		$this->template->view('privileges/list');
	}
	
	public function add()
	{
		/*if ($_POST)
		{
			// post done here
			$this->form_validation->set_rules($this->_validation_rule);
			if ($this->form_validation->run()) 
			{				
				// json encode privilege uri
				$uris = set_value('privilege_uri');
				$uris = explode(',',$uris);
				$t = array();
				foreach ($uris as $u) $t[] = trim($u);
				
				// insert the new user
				$data = array(
					'privilege_code' => set_value('privilege_code'),
					'privilege_description' => set_value('privilege_description'),
					'privilege_uri' => json_encode($t),
					'system_code' => set_value('system_code')
				);
				$this->systems_model->insert_privilege($data);
				
				redirect('/admin/privileges');
				return;
			}
		}
		$this->template->systems = $this->systems_model->get_systems();
		$this->template->view('privileges/add');*/
		$systems = $this->systems_model->get_systems();
		
		$data = array(
			'systems' => $systems
		);

		$html = $this->load->view('/privileges/add', $data, TRUE);

		echo json_encode(array("status"=>"1","html"=>$html));
		return;
				
	}
	
	public function confirm_add() {
		$_system_code = $this->input->post('_system_code');
		$_privilege_code = $this->input->post('_privilege_code');
		$_privilege_description = $this->input->post('_privilege_description');
		$_privilege_uri = $this->input->post('_privilege_uri');		
		
		//$item_details = $this->items_model->get_item_by_id($_item_id);		
		//$product_line_details = $this->items_model->get_product_line_by_id($_product_line_id);
		
		$_system_code = strtoupper($_system_code);		
		$_privilege_description = strtoupper($_privilege_description);		
		$_privilege_code = strtoupper($_privilege_code);
		
				
		$html = "<p><label>You are about to add a new Privilege with the following details:</label>	
				<table class='table table-striped table-bordered'>
					<thead>
					</thead>
					<tbody>
						<tr>
							<td style='width:140px;'><label><strong>System</strong></label></td>
							<td><label class=''>{$_system_code}</label></td>		
						</tr>
						<tr>
							<td style='width:140px;'><label><strong>Code</strong></label></td>
							<td><label class=''>{$_privilege_code}</label></td>		
						</tr>
						<tr>
							<td style='width:140px;'><label><strong>Description</strong></label></td>
							<td><label class=''>{$_privilege_description}</label></td>		
						</tr>	
						<tr>
							<td style='width:140px;'><label><strong>URI</strong></label></td>
							<td><label class=''>{$_privilege_uri}</label></td>		
						</tr>					
					</tbody>
				</table>				
				<label>Do you want to proceed?</label>
			</p>";
		
		echo json_encode(array("status"=>"1","html"=>$html));
		return;
		
	}
	
	public function add_privilege() {
		$_system_code = $this->input->post('_system_code');
		$_privilege_code = $this->input->post('_privilege_code');
		$_privilege_description = $this->input->post('_privilege_description');
		$_privilege_uri = $this->input->post('_privilege_uri');		
	
		$_system_code = strtoupper($_system_code);		
		$_privilege_description = strtoupper($_privilege_description);		
		$_privilege_code = strtoupper($_privilege_code);
	
		// json encode privilege uri
		$uris = $_privilege_uri;
		$uris = explode(',',$uris);
		$t = array();
		foreach ($uris as $u) $t[] = trim($u);
		
		// insert the new user
		$data = array(
			'privilege_code' => $_privilege_code,
			'privilege_description' => $_privilege_description,
			'privilege_uri' => json_encode($t),
			'system_code' => $_system_code
		);
		$this->systems_model->insert_privilege($data);
		
		$insert_id = $this->systems_model->insert_id();
		
		//logging of action
		$details_after = array('id' => $insert_id, 'details' => $data);
		$details_after = json_encode($details_after);
		$add_privileges_log_data = array(
			'user_id' => $this->user->user_id,
			'module_name' => 'PRIVILEGES',
			'table_name' => 'ad_privileges',
			'action' => 'ADD',
			'details_after' => $details_after,
			'remarks' => "",
		);

		$this->tracking_model->insert_logs('admin', $add_privileges_log_data);
	
		echo json_encode(array("status"=>"1"));
		return;		
	}
	
	
	
	//public function edit($privilege_id = 0)
	public function edit()
	{
		$_privilege_id = $this->input->post('_privilege_id');
		$privilege = $this->systems_model->get_privilege_by_id($_privilege_id);
		
		/*if ($_POST and !empty($privilege))
		{
			// post done here
			$this->form_validation->set_rules($this->_validation_rule);
			
			if ($this->form_validation->run()) 
			{
				// json encode privilege uri
				$uris = set_value('privilege_uri');
				$uris = explode(',',$uris);
				$t = array();
				foreach ($uris as $u) $t[] = trim($u);
				
				// insert the new user
				$data = array(
					'privilege_code' => set_value('privilege_code'),
					'privilege_description' => set_value('privilege_description'),
					'privilege_uri' => json_encode($t),
					'system_code' => set_value('system_code')
				);
				
				$this->systems_model->update_privileges($data, array('privilege_id' => $privilege_id));
				
				redirect('/admin/privileges');
				return;
			}
		}

		$this->template->systems = $this->systems_model->get_systems();
		$this->template->privilege = $privilege;
		$this->template->view('privileges/edit');*/
		
		$systems = $this->systems_model->get_systems();		
		$data = array(
			'systems' => $systems,
			'privilege' => $privilege
		);

		$html = $this->load->view('/privileges/edit', $data, TRUE);

		echo json_encode(array("status"=>"1","html"=>$html));
		return;
		
	}
	
	public function confirm_edit() {
		$_system_code = $this->input->post('_system_code');
		$_privilege_code = $this->input->post('_privilege_code');
		$_privilege_description = $this->input->post('_privilege_description');
		$_privilege_uri = $this->input->post('_privilege_uri');
		$_privilege_id = $this->input->post('_privilege_id');
	
		$_system_code = strtoupper($_system_code);		
		$_privilege_description = strtoupper($_privilege_description);		
		$_privilege_code = strtoupper($_privilege_code);
		
				
		$html = "<p><label>You are about to add a new Privilege with the following details:</label>	
				<table class='table table-striped table-bordered'>
					<thead>
					</thead>
					<tbody>
						<tr>
							<td style='width:140px;'><label><strong>System</strong></label></td>
							<td><label class=''>{$_system_code}</label></td>		
						</tr>
						<tr>
							<td style='width:140px;'><label><strong>Code</strong></label></td>
							<td><label class=''>{$_privilege_code}</label></td>		
						</tr>
						<tr>
							<td style='width:140px;'><label><strong>Description</strong></label></td>
							<td><label class=''>{$_privilege_description}</label></td>		
						</tr>	
						<tr>
							<td style='width:140px;'><label><strong>URI</strong></label></td>
							<td><label class=''>{$_privilege_uri}</label></td>		
						</tr>					
					</tbody>
				</table>				
				<label>Do you want to proceed?</label>
			</p>";
		
		echo json_encode(array("status"=>"1","html"=>$html));
		return;
		
	}

	public function update_privilege() {
		$_system_code = $this->input->post('_system_code');
		$_privilege_code = $this->input->post('_privilege_code');
		$_privilege_description = $this->input->post('_privilege_description');
		$_privilege_uri = $this->input->post('_privilege_uri');
		$_privilege_id = $this->input->post('_privilege_id');

		$privilege = $this->systems_model->get_privilege_by_id($_privilege_id);
	
		$_system_code = strtoupper($_system_code);		
		$_privilege_description = strtoupper($_privilege_description);		
		$_privilege_code = strtoupper($_privilege_code);
		
		$uris = $_privilege_uri;
		$uris = explode(',',$uris);
		$t = array();
		foreach ($uris as $u) $t[] = trim($u);
		
		// insert the new user
		$data = array(
			'privilege_code' => $_privilege_code,
			'privilege_description' => $_privilege_description,
			'privilege_uri' => json_encode($t),
			'system_code' => $_system_code
		);
		
		$this->systems_model->update_privileges($data, array('privilege_id' => $_privilege_id));
	
		//logging of action
		$data_before = array(
			'privilege_code' => $privilege->privilege_code,
			'privilege_description' => $privilege->privilege_description,
			'privilege_uri' => $privilege->privilege_uri,
			'system_code' => $privilege->system_code
		);
		
		$details_before = array('id' => $_privilege_id, 'details' => $data_before);
		$details_before = json_encode($details_before);
		
		$details_after = array('id' => $_privilege_id, 'details' => $data);
		$details_after = json_encode($details_after);
		$update_privileges_log_data = array(
			'user_id' => $this->user->user_id,
			'module_name' => 'PRIVILEGES',
			'table_name' => 'ad_privileges',
			'action' => 'UPDATE',
			'details_before' => $details_before,
			'details_after' => $details_after,
			'remarks' => "",
		);

		$this->tracking_model->insert_logs('admin', $update_privileges_log_data);
		
		echo json_encode(array("status"=>"1"));
		return;		
	}
	
	
	//public function delete($privilege_id = 0)
	public function delete()
	{
		$_privilege_id = $this->input->post('_privilege_id');
		$privilege = $this->systems_model->get_privilege_by_id($_privilege_id);
		
		/*if ($_POST and !empty($privilege))
		{
			$_privilege_id = $this->input->post('privilege_id');
			if (!empty($_privilege_id)) if ($_privilege_id == $privilege_id)
			{
				$this->systems_model->delete_privileges(array('privilege_id' => $privilege_id));
				redirect('/admin/privileges');
				return;
			}
		}
		
		$this->template->privilege = $privilege;
		$this->template->view('privileges/delete');
		*/	
		$data = array(
			'privilege' => $privilege
		);

		$html = $this->load->view('/privileges/delete', $data, TRUE);

		echo json_encode(array("status"=>"1","html"=>$html));
		return;
	}
	
	public function confirm_delete() {
		$_privilege_id = $this->input->post('_privilege_id');
	
		$html = "<p><label>Are you sure you want to proceed?</label>									
				</p>";
		
		echo json_encode(array("status"=>"1","html"=>$html));
		return;
		
	}
	
	public function delete_privilege() {	
		$_privilege_id = $this->input->post('_privilege_id');
		$privilege = $this->systems_model->get_privilege_by_id($_privilege_id);
		$user_privileges = $this->users_model->get_user_privileges(array('privilege_id' => $_privilege_id));
		
		if (!empty($_privilege_id)) {		
			$this->systems_model->delete_privileges(array('privilege_id' => $_privilege_id));
			
			//logging of action
			$data_before = array(
				'privilege_code' => $privilege->privilege_code,
				'privilege_description' => $privilege->privilege_description,
				'privilege_uri' => $privilege->privilege_uri,
				'system_code' => $privilege->system_code
			);

			$details_before = array('id' => $_privilege_id, 'details' => $data_before);
			$details_before = json_encode($details_before);

			$delete_privileges_log_data = array(
				'user_id' => $this->user->user_id,
				'module_name' => 'PRIVILEGES',
				'table_name' => 'ad_privileges',
				'action' => 'DELETE',
				'data_before' => $data_before,
				'remarks' => "",
			);

			$this->tracking_model->insert_logs('admin', $delete_privileges_log_data);
			
			$this->users_model->delete_user_privileges(array('privilege_id' => $_privilege_id));
			
			foreach($user_privileges as $up)
			{
				//logging of action
				$data_before = array(
					'user_id' => $up->user_id,
					'privilege_id' => $up->privilege_id
				);

				$details_before = array('id' => $up->user_id, 'details' => $data_before);
				$details_before = json_encode($details_before);

				$delete_privileges_log_data = array(
					'user_id' => $this->user->user_id,
					'module_name' => 'USER PRIVILEGES',
					'table_name' => 'ad_user_privileges',
					'action' => 'DELETE',
					'data_before' => $data_before,
					'remarks' => "",
				);

				$this->tracking_model->insert_logs('admin', $delete_privileges_log_data);
			}
						
		} else {
			echo json_encode(array("status"=>"0", "message" => "Invalid Privilege ID"));
			return;
		}
	
		echo json_encode(array("status"=>"1"));
		return;
		
	}

}
