<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Navigations extends Systems_Controller {
	private $_validation_rule = array(
			array(
							'field' => 'system_code',
							'label' => 'System',
							'rules' => 'trim|required'
			),
			array(
							'field' => 'code',
							'label' => 'Navigation Code',
							'rules' => 'trim|required'
			),
			array(
							'field' => 'title',
							'label' => 'Name',
							'rules' => 'trim|required'
			),
			array(
							'field' => 'url',
							'label' => 'Link',
							'rules' => 'trim|required'
			),
			array(
							'field' => 'parent_id',
							'label' => 'Section',
							'rules' => 'trim|required'
			),
			array(
							'field' => 'is_active',
							'label' => 'Active',
							'rules' => 'trim|required'
			),
			array(
							'field' => 'type',
							'label' => 'Type',
							'rules' => 'trim|required'
			),
	);

	function __construct()
	{
		parent::__construct();

		$this->load->model('navigations_model');
		$this->load->model('systems_model');
		// load pager library
		$this->load->library('pager');
		$this->set_navigation('navigations');
	}

	public function index($filter = '')
	{
		$this->navigations($filter);
	}

	public function navigations()
	{
		$navigations = $this->navigations_model->get_navigations(null,null,'ordering ASC');
		$inv_max_order = $this->navigations_model->get_system_max_order('inventory');
		$net_max_order = $this->navigations_model->get_system_max_order('network');
		$pos_max_order = $this->navigations_model->get_system_max_order('webpoi');
		$sys_max_order = $this->navigations_model->get_system_max_order('system');
		$site_max_order = $this->navigations_model->get_system_max_order('site');
		$cms_max_order = $this->navigations_model->get_system_max_order('cms');
		$mis_max_order = $this->navigations_model->get_system_max_order('mis');

		$inventory = array();
		$network = array();
		$webpos = array();
		$system = array();
		$site = array();
		$cms = array();
		$mis = array();

		foreach($navigations as $navigation)
		{
			if(strtolower($navigation->system_code) == "inventory")
			{
				array_push($inventory, $navigation);
			}
			elseif(strtolower($navigation->system_code) == "network")
			{
				array_push($network, $navigation);
			}
			elseif(strtolower($navigation->system_code) == "webpoi")
			{
				array_push($webpos, $navigation);
			}
			elseif(strtolower($navigation->system_code) == "system")
			{
				array_push($system, $navigation);
			}
			elseif(strtolower($navigation->system_code) == "site")
			{
				array_push($site, $navigation);
			}
			elseif(strtolower($navigation->system_code) == "cms")
			{
				array_push($cms, $navigation);
			}
			elseif(strtolower($navigation->system_code) == 'mis')
			{
				array_push($mis, $navigation);
			}
		}

		$this->template->inv_max_order = $inv_max_order;
		$this->template->net_max_order = $net_max_order;
		$this->template->pos_max_order = $pos_max_order;
		$this->template->sys_max_order = $sys_max_order;
		$this->template->site_max_order = $site_max_order;
		$this->template->cms_max_order = $cms_max_order;
		$this->template->inventory = $inventory;
		$this->template->network = $network;
		$this->template->webpos = $webpos;
		$this->template->system = $system;
		$this->template->site = $site;
		$this->template->cms = $cms;
		$this->template->mis = $mis;
		$this->template->view('navigations/list');
	}

	public function view($navigation_id)
	{
		/*$navigation = $this->navigations_model->get_navigation_by_id($navigation_id);

		$parent = $this->navigations_model->get_navigation_by_id($navigation->parent_id);
		$parent_title = "";
		if(!empty($parent))
		{
			$parent_title = $parent->title;
		}
		else
		{
			$parent_title = "None";
		}
		$navigation->section = $parent_title;
		$this->template->navigation = $navigation;
		$this->template->view('navigations/view');*/
	}

	public function add()
	{

		/*if ($_POST)
		{

			// post done here
			$this->form_validation->set_rules($this->_validation_rule);

			if ($this->form_validation->run())
			{
				error_log('valid');

				$max_order = $this->navigations_model->get_system_max_order(set_value('system_code'));

				// insert the new navigation
				$data = array(
						'system_code ' => set_value('system_code'),
						'code' => set_value('code'),
						'title' => set_value('title'),
						'url' => set_value('url'),
						'ordering' => $max_order + 1,
						'parent_id' => set_value('parent_id'),
						'is_active' => set_value('is_active'),
						'type' => set_value('type'),
				);
				$this->navigations_model->insert_navigation($data);

				redirect('/admin/navigations#'.set_value('system_code').'/');
				return;
			}
		}
		
		$this->template->systems = $this->systems_model->get_systems();
		$this->template->view('navigations/add');*/
		
		$systems = $this->systems_model->get_systems();

		$data = array(
			'systems' => $systems
		);

		$html = $this->load->view('/navigations/add', $data, TRUE);

		echo json_encode(array("status"=>"1","html"=>$html));
		return;
	}

	public function confirm_add() {
		$_type = $this->input->post('_type');
		$_system_code = $this->input->post('_system_code');
		$_parent_id = $this->input->post('_parent_id');
		$_code = $this->input->post('_code');		
		$_title = $this->input->post('_title');		
		$_url = $this->input->post('_url');		
		$_is_active = $this->input->post('_is_active');		
		
		$_system_code = $_system_code;		
		$_code = $_code;		
		$_title = $_title;
		
		if ($_is_active == 0) {
			$_is_active = "No";
		} else {
			$_is_active = "Yes";
		}
				
		if ($_parent_id == 0) {
			 $navigation_title = $_title;
		} else {	
			// get section/parent_id details
			$navigation_details = $this->navigations_model->get_navigation_by_id($_parent_id);
			if ($navigation_details == NULL) {
				echo json_encode(array("status"=>"0","html"=>"Invalid Navigation ID"));
				return;
			} else {
				$navigation_title = $navigation_details->title;
			}
		}
				
		$html = "<p><label>You are about to add a new Navigation with the following details:</label>	
				<table class='table table-striped table-bordered'>
					<thead>
					</thead>
					<tbody>
						<tr>
							<td style='width:140px;'><label><strong>Type</strong></label></td>
							<td><label class=''>{$_type}</label></td>		
						</tr>
						<tr>
							<td style='width:140px;'><label><strong>System</strong></label></td>
							<td><label class=''>{$_system_code}</label></td>		
						</tr>
						<tr>
							<td style='width:140px;'><label><strong>Section</strong></label></td>
							<td><label class=''>{$navigation_title}</label></td>		
						</tr>	
						<tr>
							<td style='width:140px;'><label><strong>Navigation Code</strong></label></td>
							<td><label class=''>{$_code}</label></td>		
						</tr>	
						<tr>
							<td style='width:140px;'><label><strong>Name</strong></label></td>
							<td><label class=''>{$_title}</label></td>		
						</tr>
						<tr>
							<td style='width:140px;'><label><strong>Link</strong></label></td>
							<td><label class=''>{$_url}</label></td>		
						</tr>
						<tr>
							<td style='width:140px;'><label><strong>Is Active</strong></label></td>
							<td><label class=''>{$_is_active}</label></td>		
						</tr>				
					</tbody>
				</table>				
				<label>Do you want to proceed?</label>
			</p>";
		
		echo json_encode(array("status"=>"1","html"=>$html));
		return;
		
	}
	
	public function add_navigation() {
		$_type = $this->input->post('_type');
		$_system_code = $this->input->post('_system_code');
		$_parent_id = $this->input->post('_parent_id');
		$_code = $this->input->post('_code');		
		$_title = $this->input->post('_title');		
		$_url = $this->input->post('_url');		
		$_is_active = $this->input->post('_is_active');
		 
		$_system_code = $_system_code;		
		$_code = $_code;		
		$_title = $_title;
	
		error_log('valid');

		$max_order = $this->navigations_model->get_system_max_order($_system_code);

		// insert the new navigation
		$data = array(
				'system_code ' => $_system_code,
				'code' => $_code,
				'title' => $_title,
				'url' => $_url,
				'ordering' => $max_order + 1,
				'parent_id' => $_parent_id,
				'is_active' => $_is_active,
				'type' => $_type
		);
		$this->navigations_model->insert_navigation($data);
	
		$insert_id = $this->navigations_model->insert_id();
		
		//logging of action
		$details_after = array('id' => $insert_id, 'details' => $data);
		$details_after = json_encode($details_after);
		$add_navigations_log_data = array(
			'user_id' => $this->user->user_id,
			'module_name' => 'NAVIGATION',
			'table_name' => 'ad_navigations',
			'action' => 'ADD',
			'details_after' => $details_after,
			'remarks' => "",
		);

		$this->tracking_model->insert_logs('admin', $add_navigations_log_data);
		
	
		echo json_encode(array("status"=>"1"));
		return;		
	}



	//public function edit($navigation_id)
	public function edit()
	{
		$_navigation_id = $this->input->post('_navigation_id');	
		$navigation = $this->navigations_model->get_navigation_by_id($_navigation_id);

		/*if ($_POST && !empty($navigation))
		{
			$orig_system_code = $this->input->post('orig_system_code');
			
			// post done here
			$this->form_validation->set_rules($this->_validation_rule);

			if ($this->form_validation->run())
			{
				$post_system_code = set_value('system_code');
				if($orig_system_code == $post_system_code)
				{
					$ordering = $navigation->ordering;
				}
				else
				{
					$max_order = $this->navigations_model->get_system_max_order($post_system_code);

					$ordering = $max_order + 1;

					if($navigation->ordering != $max_order)
					{
						$other_nav = $this->navigations_model->get_navigation_by_system_and_ordering($orig_system_code,$navigation->ordering+1);

						//update the navigations after this entry
						while(!empty($other_nav))
						{
							$data = array(
									'ordering' => $other_nav->ordering - 1,
							);
							$this->navigations_model->update_navigations($data, array('navigation_id' => $other_nav->navigation_id));
							$other_nav = $this->navigations_model->get_navigation_by_system_and_ordering($orig_system_code,$other_nav->ordering+1);
						}
					}
				}

				// insert the new navigation
				$data = array(
						'system_code ' => set_value('system_code'),
						'code' => set_value('code'),
						'title' => set_value('title'),
						'url' => set_value('url'),
						'ordering' => $ordering,
						'parent_id' => set_value('parent_id'),
						'is_active' => set_value('is_active'),
						'type' => set_value('type'),
				);
				$this->navigations_model->update_navigations($data, array('navigation_id' => $navigation_id));

				redirect('/admin/navigations#'.set_value('system_code').'/');
				return;
			}
		}

		$this->template->systems = $this->systems_model->get_systems();
		$this->template->navigation = $navigation;
		$this->template->view('navigations/edit');*/
		
		$systems = $this->systems_model->get_systems();		
		$data = array(
			'systems' => $systems,
			'navigation' => $navigation
		);

		$html = $this->load->view('/navigations/edit', $data, TRUE);

		echo json_encode(array("status"=>"1","html"=>$html));
		return;	
	}

	public function confirm_edit() {
		$_type = $this->input->post('_type');
		$_system_code = $this->input->post('_system_code');
		$_parent_id = $this->input->post('_parent_id');
		$_code = $this->input->post('_code');		
		$_title = $this->input->post('_title');		
		$_url = $this->input->post('_url');		
		$_is_active = $this->input->post('_is_active');		
		$_navigation_id = $this->input->post('_navigation_id');
		$_orig_system_code = $this->input->post('_orig_system_code');	
		
		$_system_code = $_system_code;		
		$_code = $_code;		
		$_title = $_title;
		
		if ($_is_active == 0) {
			$_is_active = "No";
		} else {
			$_is_active = "Yes";
		}
				
		if ($_parent_id == 0) {
			 $navigation_title = $_title;
		} else {	
			// get section/parent_id details
			$navigation_details = $this->navigations_model->get_navigation_by_id($_parent_id);
			if ($navigation_details == NULL) {
				echo json_encode(array("status"=>"0","html"=>"Invalid Navigation ID"));
				return;
			} else {
				$navigation_title = $navigation_details->title;
			}
		}
		
				
		$html = "<p><label>You are about to edit a Navigation with the following details:</label>	
				<table class='table table-striped table-bordered'>
					<thead>
					</thead>
					<tbody>
						<tr>
							<td style='width:140px;'><label><strong>Type</strong></label></td>
							<td><label class=''>{$_type}</label></td>		
						</tr>
						<tr>
							<td style='width:140px;'><label><strong>System</strong></label></td>
							<td><label class=''>{$_system_code}</label></td>		
						</tr>
						<tr>
							<td style='width:140px;'><label><strong>Section</strong></label></td>
							<td><label class=''>{$navigation_title}</label></td>		
						</tr>	
						<tr>
							<td style='width:140px;'><label><strong>Navigation Code</strong></label></td>
							<td><label class=''>{$_code}</label></td>		
						</tr>	
						<tr>
							<td style='width:140px;'><label><strong>Name</strong></label></td>
							<td><label class=''>{$_title}</label></td>		
						</tr>
						<tr>
							<td style='width:140px;'><label><strong>Link</strong></label></td>
							<td><label class=''>{$_url}</label></td>		
						</tr>
						<tr>
							<td style='width:140px;'><label><strong>Is Active</strong></label></td>
							<td><label class=''>{$_is_active}</label></td>		
						</tr>				
					</tbody>
				</table>				
				<label>Do you want to proceed?</label>
			</p>";
		
		echo json_encode(array("status"=>"1","html"=>$html));
		return;
		
	}

	public function update_navigation() {
		$_type = $this->input->post('_type');
		$_system_code = $this->input->post('_system_code');
		$_parent_id = $this->input->post('_parent_id');
		$_code = $this->input->post('_code');		
		$_title = $this->input->post('_title');		
		$_url = $this->input->post('_url');		
		$_is_active = $this->input->post('_is_active');		
		$_navigation_id = $this->input->post('_navigation_id');	
		$orig_system_code = $this->input->post('_orig_system_code');

		$_system_code = $_system_code;		
		$_code = $_code;		
		$_title = $_title;
		
		$navigation = $this->navigations_model->get_navigation_by_id($_navigation_id);
		
		$post_system_code = $_system_code;
		if($orig_system_code == $post_system_code) {
			$ordering = $navigation->ordering;
		}
		else 
		{
			$max_order = $this->navigations_model->get_system_max_order($post_system_code);

			$ordering = $max_order + 1;

			if($navigation->ordering != $max_order)
			{
				$other_nav = $this->navigations_model->get_navigation_by_system_and_ordering($orig_system_code,$navigation->ordering + 1);

				//update the navigations after this entry
				while(!empty($other_nav))
				{
					$data = array(
							'ordering' => $other_nav->ordering - 1,
					);
					$this->navigations_model->update_navigations($data, array('navigation_id' => $other_nav->navigation_id));
					
					$details_before = array('id' => $other_nav->navigation_id, 'details' => array('ordering' => $other_nav->ordering));
					$details_before = json_encode($details_before);

					$details_after = array('id' => $other_nav->navigation_id, 'details' => $data);
					$details_after = json_encode($details_after);
					$update_navigations_log_data = array(
						'user_id' => $this->user->user_id,
						'module_name' => 'NAVIGATION',
						'table_name' => 'ad_navigations',
						'action' => 'UPDATE',
						'details_before' => $details_before,
						'details_after' => $details_after,
						'remarks' => "",
					);

					$this->tracking_model->insert_logs('admin', $update_navigations_log_data);
					
					$other_nav = $this->navigations_model->get_navigation_by_system_and_ordering($orig_system_code,$other_nav->ordering+1);
				}
			}
		}

		// insert the new navigation
		$data = array(
				'system_code ' => $_system_code,
				'code' => $_code,
				'title' => $_title,
				'url' => $_url,
				'ordering' => $ordering,
				'parent_id' => $_parent_id,
				'is_active' => $_is_active,
				'type' => $_type
		);
		$this->navigations_model->update_navigations($data, array('navigation_id' => $_navigation_id));
		
		
		//logging
		$data_before = array(
			'system_code ' => $navigation->system_code,
			'code' => $navigation->code,
			'title' => $navigation->title,
			'url' => $navigation->url,
			'ordering' => $navigation->ordering,
			'parent_id' => $navigation->parent_id,
			'is_active' => $navigation->is_active,
			'type' => $navigation->type
		);
		
		$details_before = array('id' => $_navigation_id, 'details' => $data_before);
		$details_before = json_encode($details_before);

		$details_after = array('id' => $_navigation_id, 'details' => $data);
		$details_after = json_encode($details_after);
		$update_navigations_log_data = array(
			'user_id' => $this->user->user_id,
			'module_name' => 'NAVIGATION',
			'table_name' => 'ad_navigations',
			'action' => 'UPDATE',
			'details_before' => $details_before,
			'details_after' => $details_after,
			'remarks' => "",
		);

		$this->tracking_model->insert_logs('admin', $update_navigations_log_data);
		
		echo json_encode(array("status"=>"1"));
		return;		
	}


	//public function delete($navigation_id)
	public function delete()
	{
		$_navigation_id = $this->input->post('_navigation_id');			
		$navigation = $this->navigations_model->get_navigation_by_id($_navigation_id);

		/*if ($_POST && !empty($navigation))
		{
			$_navigation_id = $this->input->post('navigation_id');
			if (!empty($_navigation_id)) if ($_navigation_id == $navigation_id)
			{
				$system = $navigation->system_code;
				
				$max_order = $this->navigations_model->get_system_max_order($system);
				
				if($navigation->ordering != $max_order)
				{
					$other_nav = $this->navigations_model->get_navigation_by_system_and_ordering($system,$navigation->ordering+1);
				
					//update the navigations after this entry
					while(!empty($other_nav))
					{
						$data = array(
								'ordering' => $other_nav->ordering - 1,
						);
						$this->navigations_model->update_navigations($data, array('navigation_id' => $other_nav->navigation_id));
						$other_nav = $this->navigations_model->get_navigation_by_system_and_ordering($system,$other_nav->ordering+1);
					}
				}

				$this->navigations_model->delete_navigations(array('navigation_id' => $navigation_id));
				redirect('/admin/navigations#	'.$system.'/');
				return;
			}
		}

		$parent = $this->navigations_model->get_navigation_by_id($navigation->parent_id);
		$parent_title = "";
		if(!empty($parent))
		{
			$parent_title = $parent->title;
		}
		else
		{
			$parent_title = "None";
		}
		$navigation->section = $parent_title;
		$this->template->navigation = $navigation;
		$this->template->view('navigations/delete');
		*/
		
		$parent = $this->navigations_model->get_navigation_by_id($navigation->parent_id);
		$parent_title = "";
		if(!empty($parent))
		{
			$parent_title = $parent->title;
		}
		else
		{
			$parent_title = "None";
		}
		$navigation->section = $parent_title;
		
		$data = array(
			'navigation' => $navigation
		);

		$html = $this->load->view('/navigations/delete', $data, TRUE);

		echo json_encode(array("status"=>"1","html"=>$html));
		return;
		
	}

	public function confirm_delete() {
		$_navigation_id = $this->input->post('_navigation_id');	
	
		$html = "<p><label>Are you sure you want to proceed?</label>									
				</p>";
		
		echo json_encode(array("status"=>"1","html"=>$html));
		return;
		
	}
	
	public function delete_navigation() {	
		$_navigation_id = $this->input->post('_navigation_id');	
		$navigation = $this->navigations_model->get_navigation_by_id($_navigation_id);

		if (!empty($_navigation_id)) {		
			$system = $navigation->system_code;
			
			$max_order = $this->navigations_model->get_system_max_order($system);
			
			if($navigation->ordering != $max_order)
			{
				$other_nav = $this->navigations_model->get_navigation_by_system_and_ordering($system,$navigation->ordering + 1);
			
				//update the navigations after this entry
				while(!empty($other_nav))
				{
					$data = array(
							'ordering' => $other_nav->ordering - 1,
					);
					$this->navigations_model->update_navigations($data, array('navigation_id' => $other_nav->navigation_id));
					
					$details_before = array('id' => $other_nav->navigation_id, 'details' => array('ordering' => $other_nav->ordering));
					$details_before = json_encode($details_before);

					$details_after = array('id' => $other_nav->navigation_id, 'details' => $data);
					$details_after = json_encode($details_after);
					$update_navigations_log_data = array(
						'user_id' => $this->user->user_id,
						'module_name' => 'NAVIGATION',
						'table_name' => 'ad_navigations',
						'action' => 'UPDATE',
						'details_before' => $details_before,
						'details_after' => $details_after,
						'remarks' => "",
					);

					$this->tracking_model->insert_logs('admin', $update_navigations_log_data);
					
					$other_nav = $this->navigations_model->get_navigation_by_system_and_ordering($system,$other_nav->ordering + 1);
				}
			}

			$this->navigations_model->delete_navigations(array('navigation_id' => $_navigation_id));
			
			$data_before = array(
				'system_code ' => $navigation->system_code,
				'code' => $navigation->code,
				'title' => $navigation->title,
				'url' => $navigation->url,
				'ordering' => $navigation->ordering,
				'parent_id' => $navigation->parent_id,
				'is_active' => $navigation->is_active,
				'type' => $navigation->type
			);

			$details_before = array('id' => $_navigation_id, 'details' => $data_before);
			$details_before = json_encode($details_before);

			$delete_navigations_log_data = array(
				'user_id' => $this->user->user_id,
				'module_name' => 'NAVIGATION',
				'table_name' => 'ad_navigations',
				'action' => 'DELETE',
				'details_before' => $details_before,
				'remarks' => "",
			);

			$this->tracking_model->insert_logs('admin', $delete_navigations_log_data);
						
		} else {
			echo json_encode(array("status"=>"0", "message" => "Invalid Privilege ID"));
			return;
		}
	
		echo json_encode(array("status"=>"1", 'system'=>strtolower($system)));
		return;
		
	}

	public function order($system,$direction,$navigation_id)
	{
		$direction = strtolower($direction);
		if ($direction == 'up' || $direction == 'down')
		{
			$max_order = $this->navigations_model->get_system_max_order($system);
			$sec = $this->navigations_model->get_navigation_by_id($navigation_id);

			// get switch item with
			$_other_order = 0;
			if ($direction == 'up' && $sec->ordering > 1)
				$_other_order = $sec->ordering - 1;

			if ($direction == 'down' && $sec->ordering < $max_order)
				$_other_order = $sec->ordering + 1;

			if ($_other_order > 0)
			{
				$_other_sec = $this->navigations_model->get_navigation_by_system_and_ordering($system,$_other_order);
				$this->navigations_model->update_navigations(array('ordering' => $_other_sec->ordering), array('navigation_id' => $sec->navigation_id));
				
				//logging
				$details_before = array('id' => $sec->navigation_id, 'details' => array('ordering' => $sec->ordering));
				$details_before = json_encode($details_before);

				$details_after = array('id' => $sec->navigation_id, 'details' => array('ordering' => $_other_sec->ordering));
				$details_after = json_encode($details_after);
				$update_navigations_log_data = array(
					'user_id' => $this->user->user_id,
					'module_name' => 'NAVIGATION',
					'table_name' => 'ad_navigations',
					'action' => 'UPDATE',
					'details_before' => $details_before,
					'details_after' => $details_after,
					'remarks' => "",
				);

				$this->tracking_model->insert_logs('admin', $update_navigations_log_data);
				
				$this->navigations_model->update_navigations(array('ordering' => $sec->ordering), array('navigation_id' => $_other_sec->navigation_id));
				
				//logging
				$details_before = array('id' => $_other_sec->navigation_id, 'details' => array('ordering' => $_other_sec->ordering));
				$details_before = json_encode($details_before);

				$details_after = array('id' => $_other_sec->navigation_id, 'details' => array('ordering' => $sec->ordering));
				$details_after = json_encode($details_after);
				$update_navigations_log_data = array(
					'user_id' => $this->user->user_id,
					'module_name' => 'NAVIGATION',
					'table_name' => 'ad_navigations',
					'action' => 'UPDATE',
					'details_before' => $details_before,
					'details_after' => $details_after,
					'remarks' => "",
				);

				$this->tracking_model->insert_logs('admin', $update_navigations_log_data);
			}

		}

		redirect('/admin/navigations#'.$system.'/');
		return;
	}

	public function get_headers()
	{
		$system_code = $this->input->post('system_code');

		$section_headers = $this->navigations_model->get_navigation_headers_by_system_code($system_code);

		$this->return_json("ok","",$section_headers);
		return;
	}
}