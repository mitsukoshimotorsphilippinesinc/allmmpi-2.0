<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

class Admin_Controller extends Base_Controller {
	
	public $selected_facility = null;
	
	public function __construct() 
	{
		parent::__construct();		
		$this->template->theme = 'admin';
		$this->template->name = 'main-fluid';
		$this->template->title = $this->config->item('title').' : Admin';
		
		//$this->load->config('asset_admin');

		$segment_uri = $this->uri->uri_string();
		$login_uri = 'admin/signin';
		$is_login_action = stripos($segment_uri, $login_uri) !== FALSE;

		// configure authenticate
		$auth_config = array(
			'table' => 'sa_user',
			'field_id' => 'user_id',
			'hash_salt' => 'mmpi@admin8896',
			'prefix' => 'sa_'
		);
		$this->authenticate->initialize($auth_config);
		
		// 20150609 - LOGIN
		// load the systems
		$this->load->model(array('human_relations_model', 'warehouse_model', 'spare_parts_model','dpr_model'));

		if ($this->authenticate->is_logged_in()) {

			$systems = $this->human_relations_model->get_department("url IS NOT NULL OR trim(url) <> ''", NULL, "department_name ASC");		
			ci()->systems = $this->systems = $systems;
			//ci()->current_system = $this->current_system = '';
			
			$current_segment_one = $this->uri->segment(1);

			if ($current_segment_one == "admin") {
				$current_system = "N/A";
			} else {	
				$current_system = $this->human_relations_model->get_department_by_url($current_segment_one);
				ci()->current_system = $this->current_system = $current_system->department_name;
			}

			ci()->user = $this->user = $this->authenticate->get_user();

			$has_signin = strpos($this->uri->uri_string, "signin");

			if ($has_signin < 1) { 
				if (ci()->user->id_number <> "1503108") || (ci()->user->id_number <> "0204001") {
					if (!$this->user_model->is_user_allowed($this->uri->uri_string())) 
							redirect('/admin/noaccess/' . $this->uri->uri_string());
				}
			}

			//var_dump($this->uri->uri_string());

		} else {

			if (!$is_login_action) {				
				redirect('/admin/signin');
				//redirect('/employee/signin');
			}
		}
	}
	
	public function set_system($value)
	{
		ci()->current_system = $this->current_system = $value;
	}
	
	
	public function before()
	{
		parent::before();
	}
	
	public function after()
	{
		parent::after();
	}
	
}

// end of php file