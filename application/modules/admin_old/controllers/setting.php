<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Setting extends Systems_Controller 
{

	private $_validation_rule = array(
		array(
			'field' => 'slug',
			'label' => 'Slug',
			'rules' => 'trim|required|alpha_dot_dash|min_length[4]|max_length[32]|callback__check_slug'
		),
		array(
			'field' => 'value',
			'label' => 'Value',
			'rules' => 'trim|required'
		),
		array(
			'field' => 'default',
			'label' => 'Default',
			'rules' => 'trim|required'
		),
		array(
			'field' => 'system_code',
			'label' => 'System',
			'rules' => 'trim|required'
		),
		
	);
	
	public function __construct()
	{
		parent::__construct();
		$this->set_navigation('settings');
		// load pager library
		$this->load->library('pager');
	}

	public function index()
	{
		$this->setting();	
	}
	public function setting() 
	{ 
		
		$search_by = trim($this->input->get("search_option"));
		$search_text = trim($this->input->get("search_string"));


		$search_url = "";
		
		if (($search_text == "") || empty($search_text)) {
			$where = "";			
		} elseif($search_by == "name") {
			$where = "`slug` LIKE LOWER('%{$search_text}%')";
			$search_url = "?search_option=" . $search_by . "&search_string=" . $search_text;
		}
		
		
		// initialize pagination class
		// set pagination data
		$config = array(
				'pagination_url' => "/admin/setting/index/",
				'total_items' => $this->settings_model->get_setting_count($where),
				'per_page' => 10,
				'uri_segment' => 4,
		);

		$this->pager->set_config($config);
		
		// search vars
		$this->template->search_by = $search_by;
		$this->template->search_text = $search_text;
		$this->template->search_url = $search_url;
		
		$this->template->settings = $this->settings_model->get_settings($where, array('rows' => $this->pager->per_page, 'offset' => $this->pager->offset));
		$this->template->view('settings/list');
	}
	
	public function add()
	{
		if ($_POST)
		{
			// post done here
			
			$this->form_validation->set_rules($this->_validation_rule);
			
			if ($this->form_validation->run()) 
			{

				// insert the new setting
				$data = array(
					'slug' => set_value('slug'),
					'value' => $this->input->post('value'),
					'default' => $this->input->post('default'),
					'system_code' => set_value('system_code'),
					
				);
				$this->settings_model->insert_setting($data);
				
				$insert_id = $this->settings_model->insert_id();
				
				//logging of action
				$details_after = array('id' => $insert_id, 'details' => $data);
				$details_after = json_encode($details_after);
				$add_settings_log_data = array(
					'user_id' => $this->user->user_id,
					'module_name' => 'SETTINGS',
					'table_name' => 'rf_settings',
					'action' => 'ADD',
					'details_after' => $details_after,
					'remarks' => "",
				);

				$this->tracking_model->insert_logs('admin', $add_settings_log_data);
				
				redirect('/admin/setting');
				return;
			}
		}
		$this->template->view('settings/add');
		
	}
	
	public function edit($slug = 0)
	{
		$setting = $this->settings_model->get_setting_by_slug($slug);
		
		if ($_POST and !empty($setting))
		{
			// post done here
			$this->form_validation->set_rules($this->_validation_rule);
			
			if ($this->form_validation->run()) 
			{

				// insert the new setting
				$data = array(
					'slug' => set_value('slug'),
					'value' => $this->input->post('value'),
					'default' => $this->input->post('default'),
					'system_code' => set_value('system_code'),
				);
				
				$this->settings_model->update_settings($data, array('slug' => $slug));
				
				//logging of action
				$data_before = array(
					'slug' => $setting->slug,
					'value' => $setting->value,
					'default' => $setting->default,
					'system_code' => $setting->system_code,
				);
				
				$details_before = array('id' => $slug, 'details' => $data_before);
				$details_before = json_encode($details_before);
				
				$details_after = array('id' => $slug, 'details' => $data);
				$details_after = json_encode($details_after);
				$update_settings_log_data = array(
					'user_id' => $this->user->user_id,
					'module_name' => 'SETTINGS',
					'table_name' => 'rf_settings',
					'action' => 'UPDATE',
					'details_before' => $details_before,
					'details_after' => $details_after,
					'remarks' => "",
				);

				$this->tracking_model->insert_logs('admin', $update_settings_log_data);
				
				redirect('/admin/setting');
				return;
			}
		}

		$this->template->setting = $setting;
		$this->template->view('settings/edit');
		
	}
	
	public function delete($slug = 0)
	{
		$setting = $this->settings_model->get_setting_by_slug($slug);
		
		if ($_POST and !empty($setting))
		{
			$_slug = $this->input->post('slug');
			if (!empty($_slug)) if ($_slug == $slug)
			{
				$this->settings_model->delete_settings(array('slug' => $slug));
				
				//logging of action
				$data_before = array(
					'slug' => $setting->slug,
					'value' => $setting->value,
					'default' => $setting->default,
					'system_code' => $setting->system_code,
				);
				
				$details_before = array('id' => $slug, 'details' => $data_before);
				$details_before = json_encode($details_before);
				
				$update_settings_log_data = array(
					'user_id' => $this->user->user_id,
					'module_name' => 'SETTINGS',
					'table_name' => 'rf_settings',
					'action' => 'UPDATE',
					'details_before' => $details_before,
					'remarks' => "",
				);

				$this->tracking_model->insert_logs('admin', $update_settings_log_data);
				
				redirect('/admin/setting');
				return;
			}
		}
		
		$this->template->setting = $setting;
		$this->template->view('settings/delete');
		
	}
	
	public function _check_slug($slug)
	{
		
		$item = $this->settings_model->get_setting_by_slug($slug);

		if (!empty($item)) 
		{
			// get the orig slug
			$orig_slug = $this->input->post('orig_slug');
			if (!empty($orig_slug)) {
				if ($orig_slug == $slug) {
					return TRUE;
				}
			} 
			
			$this->form_validation->set_message('_check_slug', 'Slug "'.$slug.'" already exist!');
			return FALSE;
		} 
		else
		{
			return TRUE;
		}
	}

}
